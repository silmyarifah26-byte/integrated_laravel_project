<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    /**
     * Display a listing of purchases with search and multi-criteria filters.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $supplierId = $request->query('supplier_id');
        $status = $request->query('status');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $suppliers = Supplier::orderBy('name', 'asc')->get(['id', 'name']);

        $purchases = Purchase::with(['supplier', 'user', 'items.product'])
            ->when($search, function ($query, $search) {
                $query->where('purchase_number', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->when($supplierId, function ($query, $supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($startDate, function ($query, $startDate) {
                $query->whereDate('purchased_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                $query->whereDate('purchased_at', '<=', $endDate);
            })
            ->latest('purchased_at')
            ->paginate(10)
            ->withQueryString();

        return view('purchases.index', compact('purchases', 'suppliers', 'search', 'supplierId', 'status', 'startDate', 'endDate'));
    }

    /**
     * Show the form for creating a new purchase (Stock-In).
     */
    public function create(Request $request): View
    {
        $suppliers = Supplier::orderBy('name', 'asc')->get();
        $products = Product::orderBy('name', 'asc')->get(['id', 'sku', 'name', 'size', 'purchase_price', 'stock', 'minimum_stock']);
        $selectedProductId = $request->query('product_id');
        $selectedProduct = $selectedProductId ? Product::find($selectedProductId) : null;

        return view('purchases.create', compact('suppliers', 'products', 'selectedProduct', 'selectedProductId'));
    }

    /**
     * Store a newly created purchase transaction with atomic stock increments.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchased_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ], [
            'supplier_id.required' => 'Pemasok / pabrik boneka wajib dipilih.',
            'supplier_id.exists' => 'Pemasok yang dipilih tidak valid.',
            'purchased_at.required' => 'Tanggal pembelian wajib diisi.',
            'items.required' => 'Minimal tambahkan 1 baris boneka yang dibeli.',
            'items.min' => 'Minimal tambahkan 1 baris boneka yang dibeli.',
            'items.*.product_id.required' => 'Produk boneka pada setiap baris wajib dipilih.',
            'items.*.product_id.distinct' => 'Tidak boleh memilih boneka yang sama pada lebih dari satu baris.',
            'items.*.quantity.required' => 'Kuantitas wajib diisi.',
            'items.*.quantity.min' => 'Kuantitas minimal adalah 1 pcs.',
            'items.*.unit_cost.required' => 'Harga beli satuan wajib diisi.',
            'items.*.unit_cost.min' => 'Harga beli satuan tidak boleh bernilai negatif.',
        ]);

        $purchase = DB::transaction(function () use ($validated, $request) {
            // Generate purchase number format: PO-YYYYMMDD-XXXX
            $datePart = Carbon::parse($validated['purchased_at'])->format('Ymd');
            $prefix = "PO-{$datePart}-";

            $latestPurchase = Purchase::where('purchase_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            $sequence = 1;
            if ($latestPurchase && preg_match('/-(\d+)$/', $latestPurchase->purchase_number, $matches)) {
                $sequence = ((int) $matches[1]) + 1;
            }

            $purchaseNumber = sprintf("PO-%s-%04d", $datePart, $sequence);

            // Create Purchase Header
            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'user_id' => Auth::id(),
                'purchase_number' => $purchaseNumber,
                'total' => 0,
                'status' => 'completed',
                'notes' => $validated['notes'] ?? null,
                'purchased_at' => $validated['purchased_at'],
            ]);

            $calculatedTotal = 0;

            // Process Each Item
            foreach ($validated['items'] as $itemData) {
                /** @var Product $product */
                $product = Product::lockForUpdate()->findOrFail($itemData['product_id']);

                $qty = (int) $itemData['quantity'];
                $unitCost = (float) $itemData['unit_cost'];
                $subtotal = $qty * $unitCost;

                // Save Purchase Item
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_cost' => $unitCost,
                    'subtotal' => $subtotal,
                ]);

                // Increment Product Stock and update latest purchase price
                $product->increment('stock', $qty);
                $product->update(['purchase_price' => $unitCost]);

                $calculatedTotal += $subtotal;
            }

            // Update Total on Header
            $purchase->update(['total' => $calculatedTotal]);

            return $purchase;
        });

        return redirect()->route('purchases.show', $purchase)
            ->with('success', "Transaksi pengadaan boneka '{$purchase->purchase_number}' berhasil dicatat dan stok fisik produk telah bertambah.");
    }

    /**
     * Display the specified purchase transaction detail.
     */
    public function show(Purchase $purchase): View
    {
        $purchase->load(['supplier', 'user', 'items.product.category']);
        return view('purchases.show', compact('purchase'));
    }

    /**
     * Cancel the purchase transaction (Admin only) with stock validation check.
     */
    public function cancel(Request $request, Purchase $purchase): RedirectResponse
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Administrator yang berhak membatalkan transaksi.');
        }

        if ($purchase->status !== 'completed') {
            return back()->with('error', "Transaksi '{$purchase->purchase_number}' tidak dapat dibatalkan karena berstatus " . strtoupper($purchase->status) . '.');
        }

        try {
            DB::transaction(function () use ($purchase) {
                // Check stock sufficiency for all items first
                foreach ($purchase->items as $item) {
                    /** @var Product $product */
                    $product = Product::lockForUpdate()->findOrFail($item->product_id);

                    if ($product->stock < $item->quantity) {
                        throw ValidationException::withMessages([
                            'error' => "Pembatalan gagal! Sisa stok produk '{$product->name}' saat ini ({$product->stock} pcs) kurang dari kuantitas yang dibeli pada transaksi ini ({$item->quantity} pcs) karena sebagian sudah terjual ke pelanggan. Lakukan penyesuaian stok atau batalkan transaksi penjualan terkait terlebih dahulu agar stok tidak menjadi negatif."
                        ]);
                    }

                    // Deduct stock back
                    $product->decrement('stock', $item->quantity);
                }

                // Update purchase status
                $purchase->update(['status' => 'cancelled']);
            });

            return redirect()->route('purchases.show', $purchase)
                ->with('success', "Transaksi pembelian '{$purchase->purchase_number}' berhasil dibatalkan dan stok produk telah dikurangi kembali secara otomatis.");
        } catch (ValidationException $e) {
            $msg = $e->validator->errors()->first() ?? $e->getMessage();
            return back()->with('error', $msg);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}
