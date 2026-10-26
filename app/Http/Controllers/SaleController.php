<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleController extends Controller
{
    /**
     * Display a listing of sales transactions.
     * Admin & Staff view all sales; Customers view only their own sales.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        $search = $request->query('search');
        $status = $request->query('status');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $sales = Sale::with(['user', 'customer', 'items.product'])
            ->when($user->isCustomer(), function ($query) use ($user) {
                // Strictly filter to own customer sales matching logged-in customer identity
                $query->where(function ($userQuery) use ($user) {
                    $userQuery->where('customer_id', $user->id)
                              ->orWhere('user_id', $user->id);
                })
                ->where(function ($nameQuery) use ($user) {
                    $nameQuery->where('customer_name', $user->name)
                              ->orWhere('customer_name', 'like', $user->name . ' (%');
                });
            })
            ->when($search, function ($query, $search) use ($user) {
                if ($user->isCustomer()) {
                    $query->where('sale_number', 'like', "%{$search}%");
                } else {
                    $query->where(function ($q) use ($search) {
                        $q->where('sale_number', 'like', "%{$search}%")
                            ->orWhere('customer_name', 'like', "%{$search}%")
                            ->orWhereHas('customer', function ($sub) use ($search) {
                                $sub->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            });
                    });
                }
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($startDate, function ($query, $startDate) {
                $query->whereDate('sold_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                $query->whereDate('sold_at', '<=', $endDate);
            })
            ->latest('sold_at')
            ->paginate(10)
            ->withQueryString();

        return view('sales.index', compact('sales', 'search', 'status', 'startDate', 'endDate', 'user'));
    }

    /**
     * Show the form for creating a new sales transaction / customer order.
     */
    public function create(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Load doll products with stock > 0
        $products = Product::with('category')
            ->where('stock', '>', 0)
            ->orderBy('name', 'asc')
            ->get(['id', 'sku', 'name', 'size', 'selling_price', 'stock', 'category_id', 'image_url']);

        // Load customers for staff / admin dropdown
        $customers = [];
        if ($user->isAdmin() || $user->isStaff()) {
            $customers = User::where('role', 'customer')
                ->orderBy('name', 'asc')
                ->get(['id', 'name', 'email', 'phone']);
        }

        $selectedProductId = $request->query('product_id');

        return view('sales.create', compact('products', 'customers', 'user', 'selectedProductId'));
    }

    /**
     * Store a newly created sales transaction (Following Slide 16 lecturer specs).
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'customer_id' => ['nullable', 'exists:users,id'],
            'customer_name' => ['nullable', 'string', 'max:150'],
            'payment_method' => ['nullable', 'string', 'in:QRIS,Transfer Bank,E-Wallet,Tunai / COD'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'items.required' => 'Pilih minimal 1 jenis boneka untuk ditransaksikan.',
            'items.min' => 'Pilih minimal 1 jenis boneka untuk ditransaksikan.',
            'items.*.product_id.required' => 'Produk boneka wajib dipilih.',
            'items.*.product_id.distinct' => 'Tidak boleh memilih boneka yang sama pada lebih dari satu baris.',
            'items.*.quantity.required' => 'Jumlah kuantitas pembelian wajib diisi.',
            'items.*.quantity.min' => 'Kuantitas minimal adalah 1 pcs.',
            'payment_method.in' => 'Metode pembayaran yang dipilih tidak valid.',
        ]);

        // Determine Customer ID & Customer Name
        if ($user->isCustomer()) {
            $customerId = $user->id;
            $customerName = $user->name;
        } else {
            $customerId = $request->customer_id;
            if ($customerId) {
                $registeredCustomer = User::find($customerId);
                $customerName = $registeredCustomer ? $registeredCustomer->name : $request->customer_name;
            } else {
                $customerName = $request->customer_name ?: 'Pelanggan Umum (Guest)';
            }
        }

        try {
            $sale = DB::transaction(function () use ($request, $customerId, $customerName) {
                // Generate Invoice Number format: INV-YYYYMMDD-XXXX
                $datePart = now()->format('Ymd');
                $prefix = "INV-{$datePart}-";

                $latestSale = Sale::where('sale_number', 'like', "{$prefix}%")
                    ->lockForUpdate()
                    ->orderByDesc('id')
                    ->first();

                $sequence = 1;
                if ($latestSale && preg_match('/-(\d+)$/', $latestSale->sale_number, $matches)) {
                    $sequence = ((int) $matches[1]) + 1;
                }

                $saleNumber = sprintf("INV-%s-%04d", $datePart, $sequence);

                // Create Sale Header
                $sale = Sale::create([
                    'user_id' => Auth::id(),
                    'customer_id' => $customerId,
                    'customer_name' => $customerName,
                    'sale_number' => $saleNumber,
                    'total' => 0,
                    'status' => 'completed',
                    'payment_method' => $request->payment_method ?: 'QRIS',
                    'payment_status' => 'paid',
                    'notes' => $request->notes,
                    'sold_at' => now(),
                ]);

                $total = 0;

                // Process Each Item (Strictly matching Slide 16 logic)
                foreach ($request->items as $item) {
                    /** @var Product $product */
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                    $qty = (int) $item['quantity'];

                    if ($qty < 1 || $product->stock < $qty) {
                        throw ValidationException::withMessages([
                            'items' => "Stok boneka '{$product->name}' tidak mencukupi (Stok tersedia: {$product->stock}, diminta: {$qty}). Transaksi dibatalkan secara otomatis.",
                        ]);
                    }

                    $subtotal = $product->selling_price * $qty;

                    $sale->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_price' => $product->selling_price,
                        'subtotal' => $subtotal,
                    ]);

                    $product->decrement('stock', $qty);
                    $total += $subtotal;
                }

                // Update total header
                $sale->update(['total' => $total]);

                return $sale;
            });

            return redirect()->route('sales.show', $sale)
                ->with('success', "Transaksi penjualan / pesanan '{$sale->sale_number}' berhasil diproses dan stok fisik boneka telah berkurang.");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses transaksi penjualan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified sales transaction / order receipt.
     */
    public function show(Sale $sale): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Customer can only view their own sale
        if ($user->isCustomer() && $sale->customer_id !== $user->id) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat nota transaksi milik pengguna lain.');
        }

        $sale->load(['user', 'customer', 'items.product.category']);

        return view('sales.show', compact('sale', 'user'));
    }

    /**
     * Cancel the sales transaction (Admin only) with atomic stock increment.
     */
    public function cancel(Request $request, Sale $sale): RedirectResponse
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Administrator yang berhak membatalkan transaksi penjualan.');
        }

        if ($sale->status !== 'completed') {
            return back()->with('error', "Transaksi '{$sale->sale_number}' tidak dapat dibatalkan karena berstatus " . strtoupper($sale->status) . '.');
        }

        DB::transaction(function () use ($sale) {
            // Restore product stocks
            foreach ($sale->items as $item) {
                /** @var Product $product */
                $product = Product::lockForUpdate()->findOrFail($item->product_id);
                $product->increment('stock', $item->quantity);
            }

            // Update status to cancelled
            $sale->update(['status' => 'cancelled']);
        });

        return redirect()->route('sales.show', $sale)
            ->with('success', "Transaksi penjualan '{$sale->sale_number}' berhasil dibatalkan dan seluruh stok boneka telah dikembalikan ke inventori toko.");
    }
}
