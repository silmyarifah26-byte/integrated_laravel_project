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
        <div class="text-xs text-gray-500">
            Total: <span class="font-semibold text-gray-800">{{ $products->total() }}</span> Produk
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

                    <!-- Spesifikasi Ukuran & Bahan -->
                    <div class="mt-2 text-xs text-gray-500 space-y-1">
                        <div>Ukuran: <span class="text-gray-700 font-medium">{{ $item->size ?: '-' }}</span></div>
                        <div class="line-clamp-1">Bahan: <span class="text-gray-700 font-medium">{{ $item->material ?: '-' }}</span></div>
                    </div>
                </div>

                <!-- Harga, Stok, dan Tombol Pesan -->
                <div class="mt-4 pt-3 border-t border-gray-100">
                    <div class="flex items-center justify-between mb-3">
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

                    <a href="{{ route('sales.create', ['product_id' => $item->id]) }}" class="w-full inline-flex items-center justify-center py-2 px-3 rounded-md bg-rose-600 hover:bg-rose-700 text-white font-medium text-xs transition-colors">
                        <i class="fa-solid fa-cart-plus mr-1.5"></i> Pesan Hijab Ini
                    </a>
                </div>
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
