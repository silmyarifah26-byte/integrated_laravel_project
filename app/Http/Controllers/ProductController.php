<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of products with search, category filter, and low-stock filter.
     */
    public function index(Request $request): View
    {
        // If logged-in user is a customer, redirect or render catalog view directly
        if (Auth::check() && Auth::user()->isCustomer()) {
            return $this->catalog($request);
        }

        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $lowStock = $request->query('low_stock');

        $categories = Category::orderBy('name', 'asc')->get();

        $products = Product::with('category')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('size', 'like', "%{$search}%")
                        ->orWhere('material', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($lowStock === '1', function ($query) {
                $query->whereColumn('stock', '<=', 'minimum_stock');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('products.index', compact('products', 'categories', 'search', 'categoryId', 'lowStock'));
    }

    /**
     * Display customer-facing doll catalog (Never exposes purchase_price).
     */
    public function catalog(Request $request): View
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');

        $categories = Category::orderBy('name', 'asc')->get();

        $products = Product::with('category')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('size', 'like', "%{$search}%")
                        ->orWhere('material', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('products.catalog', compact('products', 'categories', 'search', 'categoryId'));
    }

    /**
     * Show the form for creating a new product (Admin only).
     */
    public function create(): View
    {
        $categories = Category::orderBy('name', 'asc')->get();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created product (Admin only).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'size' => ['nullable', 'string', 'max:100'],
            'material' => ['nullable', 'string', 'max:100'],
            'image_url' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
        ], [
            'sku.required' => 'SKU boneka wajib diisi.',
            'sku.unique' => 'SKU ini sudah terdaftar.',
            'name.required' => 'Nama produk boneka wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'purchase_price.required' => 'Harga beli/modal wajib diisi.',
            'selling_price.required' => 'Harga jual wajib diisi.',
            'minimum_stock.required' => 'Batas stok minimum wajib diisi.',
            'image_file.image' => 'File harus berupa foto gambar yang valid.',
            'image_file.max' => 'Ukuran foto maksimal 5MB.',
        ]);

        if ($request->hasFile('image_file') && $request->file('image_file')->isValid()) {
            $file = $request->file('image_file');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/plushies'), $filename);
            $validated['image_url'] = '/images/plushies/' . $filename;
        }

        $validated['stock'] = $validated['stock'] ?? 0;
        if (empty($validated['image_url'])) {
            $validated['image_url'] = '/images/plushies/teddy-bear-pita.jpg';
        }

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', "Produk boneka '{$validated['name']}' ({$validated['sku']}) berhasil ditambahkan.");
    }

    /**
     * Display the specified product detail.
     */
    public function show(Product $product): View
    {
        $product->load(['category', 'purchaseItems.purchase.supplier', 'saleItems.sale.customer']);
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the product (Admin only).
     */
    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name', 'asc')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product (Admin only).
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'sku')->ignore($product->id),
            ],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'size' => ['nullable', 'string', 'max:100'],
            'material' => ['nullable', 'string', 'max:100'],
            'image_url' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
        ], [
            'sku.required' => 'SKU boneka wajib diisi.',
            'sku.unique' => 'SKU ini sudah digunakan produk lain.',
            'name.required' => 'Nama produk boneka wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'purchase_price.required' => 'Harga beli wajib diisi.',
            'selling_price.required' => 'Harga jual wajib diisi.',
            'minimum_stock.required' => 'Batas minimum stok wajib diisi.',
            'image_file.image' => 'File harus berupa foto gambar yang valid.',
            'image_file.max' => 'Ukuran foto maksimal 5MB.',
        ]);

        if ($request->hasFile('image_file') && $request->file('image_file')->isValid()) {
            $file = $request->file('image_file');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/plushies'), $filename);
            $validated['image_url'] = '/images/plushies/' . $filename;
        }

        if (empty($validated['image_url'])) {
            $validated['image_url'] = $product->image_url ?: '/images/plushies/teddy-bear-pita.jpg';
        }

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', "Data produk boneka '{$product->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified product (Admin only).
     * Enforces Soft Delete with transaction history integrity rule.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $hasTransactions = $product->purchaseItems()->exists() || $product->saleItems()->exists();
        $name = $product->name;

        // Perform Soft Delete
        $product->delete();

        if ($hasTransactions) {
            return redirect()->route('products.index')
                ->with('success', "Produk boneka '{$name}' telah dinonaktifkan (soft delete) karena memiliki riwayat transaksi, sehingga integritas data laporan keuangan tetap terjaga.");
        }

        return redirect()->route('products.index')
            ->with('success', "Produk boneka '{$name}' berhasil dihapus.");
    }
}
