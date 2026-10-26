@extends('layouts.app', ['title' => 'Manajemen Data Produk Boneka'])

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="w-8 h-8 rounded-lg bg-plush-100 text-plush-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </span>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Master Produk Boneka</h1>
            </div>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">Data inventori boneka, penentuan harga beli (modal), harga jual, dan kontrol stok minimum.</p>
        </div>

        <div class="flex items-center space-x-2">
            @if(auth()->user()->isAdmin())
                <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-plush-600 hover:bg-plush-700 text-white font-bold text-sm shadow-sm transition-all duration-200">
                    <i class="fa-solid fa-plus mr-1.5"></i> Tambah Boneka Baru
                </a>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                    <i class="fa-solid fa-eye mr-1.5"></i> Mode Akses: Hanya Lihat (Staff)
                </span>
            @endif
        </div>
    </div>

    <!-- Search & Multi-Filter Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('products.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- Search Keyword -->
            <div class="sm:col-span-5 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, SKU, ukuran, bahan..."
                    class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-plush-500 focus:border-plush-500 bg-slate-50 focus:bg-white transition-all">
            </div>

            <!-- Filter Category -->
            <div class="sm:col-span-3">
                <select name="category_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-plush-500 focus:border-plush-500 bg-slate-50 focus:bg-white transition-all">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Low Stock Checkbox -->
            <div class="sm:col-span-2 flex items-center">
                <label class="flex items-center space-x-2 cursor-pointer select-none text-xs font-bold text-slate-700 bg-rose-50/60 hover:bg-rose-100/60 p-2 rounded-xl border border-rose-200/60 transition-colors w-full">
                    <input type="checkbox" name="low_stock" value="1" {{ $lowStock === '1' ? 'checked' : '' }}
                        class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500 border-rose-300">
                    <span class="text-rose-700">Hanya Low Stock</span>
                </label>
            </div>

            <!-- Filter Action Buttons -->
            <div class="sm:col-span-2 flex items-center space-x-2">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition-colors">
                    Terapkan
                </button>
                @if($search || $categoryId || $lowStock)
                    <a href="{{ route('products.index') }}" class="py-2 px-3 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-600 text-xs font-bold transition-colors" title="Reset filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>

        <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Menampilkan <strong>{{ $products->count() }}</strong> dari total <strong>{{ $products->total() }}</strong> produk boneka</span>
            @if($lowStock === '1')
                <span class="text-rose-600 font-bold bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                    <i class="fa-solid fa-filter mr-1"></i> Mode Filter Low Stock Aktif
                </span>
            @endif
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-extrabold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 w-12 text-center">No</th>
                        <th class="px-6 py-4">SKU & Boneka</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Spesifikasi</th>
                        <th class="px-6 py-4 text-right">Harga Beli</th>
                        <th class="px-6 py-4 text-right">Harga Jual</th>
                        <th class="px-6 py-4 text-center">Stok / Min</th>
                        <th class="px-6 py-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $index => $prod)
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $prod->stock <= $prod->minimum_stock ? 'bg-rose-50/20' : '' }}">
                            <td class="px-6 py-4 text-center font-bold text-slate-400">
                                {{ $products->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ asset($prod->image_url ?? 'images/plushies/teddy-bear-pita.jpg') }}" 
                                         alt="{{ $prod->name }}" 
                                         class="w-14 h-14 object-cover object-center rounded-2xl border border-pink-100 flex-shrink-0 shadow-xs">
                                    <div>
                                        <a href="{{ route('products.show', $prod) }}" class="font-extrabold text-slate-900 hover:text-pink-600 transition-colors block">
                                            {{ $prod->name }}
                                        </a>
                                        <span class="text-xs text-slate-400 font-mono">{{ $prod->sku }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-pink-50 text-pink-700 border border-pink-100">
                                    {{ $prod->category->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                <div><strong class="text-slate-800">Ukuran:</strong> {{ $prod->size ?? '-' }}</div>
                                <div class="text-slate-400 truncate max-w-xs" title="{{ $prod->material }}">
                                    <strong class="text-slate-600">Bahan:</strong> {{ $prod->material ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-slate-600">
                                Rp {{ number_format($prod->purchase_price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-black text-slate-900">
                                Rp {{ number_format($prod->selling_price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($prod->stock <= $prod->minimum_stock)
                                    <div class="inline-flex flex-col items-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-rose-100 text-rose-700 border border-rose-300">
                                            {{ $prod->stock }} pcs
                                        </span>
                                        <span class="text-[10px] text-rose-500 font-bold mt-0.5">Min: {{ $prod->minimum_stock }} (Kritis)</span>
                                        <a href="{{ route('purchases.create', ['product_id' => $prod->id]) }}" 
                                           class="mt-1 px-2 py-0.5 rounded-lg text-[10px] font-black bg-blue-100 hover:bg-blue-200 text-blue-800 border border-blue-300 inline-flex items-center transition-colors shadow-2xs" 
                                           title="Restock Boneka Ini">
                                            <i class="fa-solid fa-cart-plus mr-1"></i> Restock
                                        </a>
                                    </div>
                                @else
                                    <div class="inline-flex flex-col items-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            {{ $prod->stock }} pcs
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-medium mt-0.5">Min: {{ $prod->minimum_stock }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center space-x-1.5">
                                    <a href="{{ route('products.show', $prod) }}" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors" title="Lihat Detail">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('purchases.create', ['product_id' => $prod->id]) }}" class="p-2 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 transition-colors" title="Restock / Buat Purchase">
                                        <i class="fa-solid fa-truck-ramp-box text-xs"></i>
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('products.edit', $prod) }}" class="p-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 transition-colors" title="Ubah Produk">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </a>
                                        <form action="{{ route('products.destroy', $prod) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk {{ $prod->name }}? Jika sudah memiliki riwayat transaksi, produk akan dinonaktifkan secara aman.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition-colors" title="Hapus Produk">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-boxes-stacked text-4xl mb-2 text-slate-300"></i>
                                <p class="font-semibold">Tidak ada produk boneka yang sesuai dengan kriteria.</p>
                                @if($search || $categoryId || $lowStock)
                                    <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter atau bersihkan kotak pencarian.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        @if($products->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
