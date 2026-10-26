<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the shopping cart contents and summary.
     */
    public function index(): View
    {
        $cart = session()->get('cart', []);
        
        $totalQuantity = 0;
        $totalPrice = 0;

        // Synchronize and verify current stock and price with database
        foreach ($cart as $key => &$item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $item['current_stock'] = $product->stock;
                $item['price'] = (float) $product->selling_price;
                $item['subtotal'] = $item['price'] * $item['quantity'];
                $item['image_path'] = $product->image_path;

                $totalQuantity += $item['quantity'];
                $totalPrice += $item['subtotal'];
            }
        }
        unset($item);

        return view('cart.index', compact('cart', 'totalQuantity', 'totalPrice'));
    }

    /**
     * Add a product with selected variation (color & size) to the cart.
     */
    public function add(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'color' => ['nullable', 'string', 'max:100'],
            'size' => ['nullable', 'string', 'max:100'],
        ]);

        /** @var Product $product */
        $product = Product::findOrFail($request->product_id);

        if ($product->stock < 1) {
            return back()->with('error', "Maaf, stok hijab '{$product->name}' sedang habis.");
        }

        $qty = (int) ($request->quantity ?: 1);
        $color = trim((string) $request->color);
        if (empty($color)) {
            $availableColors = $product->available_colors;
            $color = !empty($availableColors) ? $availableColors[0] : 'Standar';
        }

        $size = trim((string) $request->size);
        if (empty($size)) {
            $availableSizes = $product->available_sizes;
            $size = !empty($availableSizes) ? $availableSizes[0] : ($product->size ?: 'Standar');
        }

        $variation = "Warna: {$color} | Ukuran: {$size}";
        $cartKey = $product->id . '_' . md5($color . '_' . $size);

        $cart = session()->get('cart', []);
        $existingQty = isset($cart[$cartKey]) ? (int) $cart[$cartKey]['quantity'] : 0;
        $newQty = $existingQty + $qty;

        if ($newQty > $product->stock) {
            return back()->with('error', "Jumlah pesanan melebihi stok tersedia. Stok fisik: {$product->stock} pcs, di keranjang: {$existingQty} pcs.");
        }

        $cart[$cartKey] = [
            'key' => $cartKey,
            'product_id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'category_name' => $product->category->name ?? 'Hijab',
            'color' => $color,
            'size' => $size,
            'variation' => $variation,
            'price' => (float) $product->selling_price,
            'quantity' => $newQty,
            'subtotal' => (float) $product->selling_price * $newQty,
            'image_path' => $product->image_path,
            'current_stock' => $product->stock,
        ];

        session()->put('cart', $cart);

        return back()->with('success', "'{$product->name}' ({$color} - {$size}) berhasil ditambahkan ke keranjang belanja!");
    }

    /**
     * Update the quantity of a specific cart item.
     */
    public function update(Request $request, string $key): RedirectResponse
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = session()->get('cart', []);

        if (!isset($cart[$key])) {
            return back()->with('error', 'Item keranjang tidak ditemukan.');
        }

        $product = Product::find($cart[$key]['product_id']);
        if (!$product) {
            unset($cart[$key]);
            session()->put('cart', $cart);
            return back()->with('error', 'Produk tidak lagi tersedia.');
        }

        $qty = (int) $request->quantity;
        if ($qty > $product->stock) {
            return back()->with('error', "Kuantitas melebihi batas stok tersedia ({$product->stock} pcs).");
        }

        $cart[$key]['quantity'] = $qty;
        $cart[$key]['subtotal'] = (float) $product->selling_price * $qty;
        session()->put('cart', $cart);

        return back()->with('success', 'Kuantitas keranjang berhasil diperbarui.');
    }

    /**
     * Remove an item from the shopping cart.
     */
    public function remove(string $key): RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$key])) {
            $name = $cart[$key]['name'] ?? 'Item';
            unset($cart[$key]);
            session()->put('cart', $cart);
            return back()->with('success', "Item '{$name}' telah dihapus dari keranjang belanja.");
        }

        return back()->with('error', 'Item keranjang tidak ditemukan.');
    }

    /**
     * Clear all items from the shopping cart.
     */
    public function clear(): RedirectResponse
    {
        session()->forget('cart');

        return redirect()->route('cart.index')->with('success', 'Keranjang belanja berhasil dikosongkan.');
    }

    /**
     * Process checkout from the shopping cart atomically.
     */
    public function checkout(Request $request): RedirectResponse
    {
        $request->validate([
            'payment_method' => ['nullable', 'string', 'in:QRIS,Transfer Bank,E-Wallet,Tunai / COD'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda masih kosong. Silakan pilih hijab terlebih dahulu.');
        }

        /** @var User $user */
        $user = Auth::user();
        $customerId = $user->isCustomer() ? $user->id : null;
        $customerName = $user->name;

        try {
            $sale = DB::transaction(function () use ($request, $user, $customerId, $customerName, $cart) {
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
                    'user_id' => $user->id,
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

                // Process each item from cart with lockForUpdate to prevent overselling
                foreach ($cart as $item) {
                    /** @var Product $product */
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                    $qty = (int) $item['quantity'];

                    if ($qty < 1 || $product->stock < $qty) {
                        throw ValidationException::withMessages([
                            'cart' => "Stok hijab '{$product->name}' tidak mencukupi (Tersedia: {$product->stock}, diminta: {$qty}). Transaksi checkout dibatalkan.",
                        ]);
                    }

                    $unitPrice = $product->selling_price;
                    $subtotal = $unitPrice * $qty;

                    $sale->items()->create([
                        'product_id' => $product->id,
                        'variation' => $item['variation'] ?? "Warna: " . ($item['color'] ?? '-') . " | Ukuran: " . ($item['size'] ?? '-'),
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                    ]);

                    $product->decrement('stock', $qty);
                    $total += $subtotal;
                }

                $sale->update(['total' => $total]);

                // Clear cart from session
                session()->forget('cart');

                return $sale;
            });

            return redirect()->route('sales.show', $sale)
                ->with('success', "Checkout pesanan '{$sale->sale_number}' berhasil diproses! Terima kasih telah berbelanja di HijabSociety.");
        } catch (ValidationException $e) {
            return redirect()->route('cart.index')->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', 'Gagal memproses checkout pesanan: ' . $e->getMessage());
        }
    }
}
