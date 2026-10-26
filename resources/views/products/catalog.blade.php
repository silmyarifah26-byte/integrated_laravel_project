@extends('layouts.app', ['title' => 'Katalog Hijab & Aksesoris Muslimah'])

@section('content')
<div class="space-y-6">

    <!-- Header Section Sederhana -->
    <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Katalog Hijab & Aksesoris Muslimah</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                Pilihan koleksi pashmina, segi empat voal, dan hijab instan berkualitas di HijabSociety.
            </p>
        </div>
        <div class="flex items-center space-x-3 text-xs">
            <span class="text-gray-500">
                Total: <span class="font-semibold text-gray-800">{{ $products->total() }}</span> Produk
            </span>
            <a href="{{ route('cart.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-md bg-rose-50 border border-rose-200 text-rose-700 font-semibold hover:bg-rose-100 transition-colors">
                <i class="fa-solid fa-cart-shopping mr-1.5"></i> Lihat Keranjang
                @if(count(session('cart', [])) > 0)
                    <span class="ml-1.5 px-1.5 py-0.2 rounded-full text-[10px] bg-rose-600 text-white font-bold">
                        {{ count(session('cart', [])) }}
                    </span>
                @endif
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar Standar -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
        <form action="{{ route('products.catalog') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-7">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama hijab, bahan voal/ceruty, atau ukuran..."
                    class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500">
            </div>

            <div class="sm:col-span-3">
                <select name="category_id" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center space-x-2">
                <button type="submit" class="w-full py-2 px-3 rounded-md bg-gray-900 hover:bg-gray-800 text-white font-medium text-xs transition-colors">
                    Filter
                </button>
                @if($search || $categoryId)
                    <a href="{{ route('products.catalog') }}" class="py-2 px-3 rounded-md border border-gray-300 text-gray-600 hover:bg-gray-50 text-xs font-medium transition-colors" title="Reset filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Product Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @forelse($products as $item)
            <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm hover:border-gray-300 transition-colors flex flex-col justify-between">
                <div>
                    <!-- Foto Hijab -->
                    <div class="w-full h-48 rounded-md overflow-hidden mb-3 relative bg-gray-100 border border-gray-200">
                        <img src="{{ asset($item->image_path) }}" 
                             alt="{{ $item->name }}" 
                             class="object-cover object-center w-full h-48">
                        
                        <div class="absolute top-2 left-2">
                            <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-white/95 text-gray-700 border border-gray-200">
                                {{ $item->category->name ?? 'Hijab' }}
                            </span>
                        </div>
                        <div class="absolute top-2 right-2">
                            <span class="text-[10px] font-mono text-gray-500 bg-white/90 px-1.5 py-0.5 rounded border border-gray-200">
                                {{ $item->sku }}
                            </span>
                        </div>
                    </div>

                    <!-- Nama Hijab -->
                    <h2 class="font-semibold text-gray-900 text-sm line-clamp-2">
                        {{ $item->name }}
                    </h2>

                    <!-- Spesifikasi Bahan -->
                    <div class="mt-1 text-xs text-gray-500">
                        <span class="line-clamp-1">Bahan: <span class="text-gray-700 font-medium">{{ $item->material ?: '-' }}</span></span>
                    </div>

                    <!-- Harga dan Status Stok -->
                    <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-100">
                        <div>
                            <span class="text-[10px] uppercase text-gray-400 block">Harga</span>
                            <span class="text-base font-bold text-rose-600">
                                Rp {{ number_format($item->selling_price, 0, ',', '.') }}
                            </span>
                        </div>

                        <div>
                            @if($item->stock > 0)
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                    Stok: {{ $item->stock }}
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-red-50 text-red-700 border border-red-200">
                                    Habis
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Form Pemilihan Variasi & Add to Cart -->
                <form action="{{ route('cart.add') }}" method="POST" class="mt-3 pt-3 border-t border-gray-100 space-y-2">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $item->id }}">

                    <!-- Pilihan Variasi Warna -->
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-700 mb-0.5">
                            Variasi Warna:
                        </label>
                        <select name="color" class="w-full text-xs px-2.5 py-1.5 border border-gray-300 rounded-md bg-white text-gray-800 focus:outline-none focus:ring-1 focus:ring-rose-500">
                            @foreach($item->available_colors as $color)
                                <option value="{{ $color }}">{{ $color }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pilihan Variasi Ukuran -->
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-700 mb-0.5">
                            Variasi Ukuran:
                        </label>
                        <select name="size" class="w-full text-xs px-2.5 py-1.5 border border-gray-300 rounded-md bg-white text-gray-800 focus:outline-none focus:ring-1 focus:ring-rose-500">
                            @foreach($item->available_sizes as $sizeOption)
                                <option value="{{ $sizeOption }}">{{ $sizeOption }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kuantitas & Tombol Masukkan ke Keranjang -->
                    <div class="flex items-center space-x-2 pt-1">
                        <div class="w-16">
                            <input type="number" 
                                   name="quantity" 
                                   value="1" 
                                   min="1" 
                                   max="{{ $item->stock > 0 ? $item->stock : 1 }}" 
                                   {{ $item->stock < 1 ? 'disabled' : '' }}
                                   class="w-full text-xs px-2 py-1.5 border border-gray-300 rounded-md text-center font-medium focus:outline-none focus:ring-1 focus:ring-rose-500">
                        </div>
                        <button type="submit" 
                                {{ $item->stock < 1 ? 'disabled' : '' }}
                                class="flex-1 py-1.5 px-3 rounded-md {{ $item->stock > 0 ? 'bg-rose-600 hover:bg-rose-700 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed' }} font-medium text-xs transition-colors flex items-center justify-center">
                            <i class="fa-solid fa-cart-plus mr-1.5"></i>
                            {{ $item->stock > 0 ? 'Masukkan ke Keranjang' : 'Stok Habis' }}
                        </button>
                    </div>
                </form>
            </div>
        @empty
            <div class="col-span-full bg-white p-8 rounded-lg border border-gray-200 text-center text-gray-500">
                <i class="fa-solid fa-boxes-stacked text-3xl mb-2 text-gray-300"></i>
                <p class="font-semibold text-gray-700 text-sm">Tidak ada produk hijab yang ditemukan.</p>
                <p class="text-xs text-gray-400 mt-0.5">Silakan gunakan kata kunci lain atau pilih semua kategori.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
        <div class="bg-white p-3 rounded-lg border border-gray-200">
            {{ $products->links() }}
        </div>
    @endif

</div>
@endsection
