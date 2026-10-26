@extends('layouts.app', ['title' => 'Detail Produk Hijab'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('products.index') }}" class="text-xs font-medium text-gray-500 hover:text-gray-700 inline-flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Master Produk
        </a>

        @if(auth()->user()->isAdmin())
            <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-3 py-1.5 rounded-md bg-yellow-600 hover:bg-yellow-700 text-white font-medium text-xs transition-colors">
                <i class="fa-solid fa-pen-to-square mr-1.5"></i> Edit Produk
            </a>
        @endif
    </div>

    <!-- Product Card Header -->
    <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-5">
                <div class="w-36 h-36 sm:w-44 sm:h-44 rounded-md overflow-hidden border border-gray-200 flex-shrink-0 bg-gray-100">
                    <img src="{{ asset($product->image_path) }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-cover object-center">
                </div>
                <div>
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                            {{ $product->category->name ?? 'Kategori Hijab' }}
                        </span>
                        <span class="text-xs font-mono text-gray-400">{{ $product->sku }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                    <p class="text-xs text-gray-500 mt-1">Koleksi Hijab HijabSociety</p>
                </div>
            </div>

            <!-- Stock Status Pill -->
            <div class="p-3.5 rounded-md {{ $product->stock <= $product->minimum_stock ? 'bg-red-50 border border-red-200 text-red-800' : 'bg-green-50 border border-green-200 text-green-800' }} text-center sm:text-right min-w-[140px]">
                <span class="text-[10px] uppercase font-semibold tracking-wider block text-gray-500">Saldo Stok</span>
                <span class="text-2xl font-bold">{{ $product->stock }} pcs</span>
                <span class="text-xs block mt-0.5">
                    @if($product->stock <= $product->minimum_stock)
                        Low Stock (Min: {{ $product->minimum_stock }})
                    @else
                        Aman (Min: {{ $product->minimum_stock }})
                    @endif
                </span>
            </div>
        </div>

        <!-- Specifications & Pricing Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6 pt-6 border-t border-gray-100">
            <!-- Specifications -->
            <div class="space-y-3">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Spesifikasi Hijab</h2>
                <div class="bg-gray-50 p-4 rounded-md border border-gray-200 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Ukuran:</span>
                        <span class="font-medium text-gray-800">{{ $product->size ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Material / Bahan:</span>
                        <span class="font-medium text-gray-800">{{ $product->material ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Batas Minimum Stok:</span>
                        <span class="font-medium text-gray-800">{{ $product->minimum_stock }} pcs</span>
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="space-y-3">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Struktur Harga</h2>
                <div class="bg-gray-50 p-4 rounded-md border border-gray-200 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Harga Beli Modal:</span>
                        <span class="font-medium text-gray-700">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Harga Jual Konsumen:</span>
                        <span class="font-bold text-rose-600">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between border-t border-gray-200 pt-1 text-xs">
                        <span class="text-gray-500">Estimasi Margin Keuntungan:</span>
                        <span class="font-semibold text-green-700">
                            Rp {{ number_format($product->selling_price - $product->purchase_price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Restock Action -->
        <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
            <a href="{{ route('purchases.create', ['product_id' => $product->id]) }}" class="inline-flex items-center px-3.5 py-1.5 rounded-md bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs transition-colors">
                <i class="fa-solid fa-truck-ramp-box mr-1.5"></i> Restock Pengadaan (Stock-In)
            </a>
            <a href="{{ route('sales.create', ['product_id' => $product->id]) }}" class="inline-flex items-center px-3.5 py-1.5 rounded-md bg-gray-900 hover:bg-gray-800 text-white font-medium text-xs transition-colors">
                <i class="fa-solid fa-cash-register mr-1.5"></i> Buat Penjualan (Stock-Out)
            </a>
        </div>
    </div>
</div>
@endsection
