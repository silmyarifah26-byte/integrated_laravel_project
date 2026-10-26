@extends('layouts.app', ['title' => 'Detail Produk Boneka'])

@section('content')
<div class="max-w-4xl mx-auto py-4 space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('products.index') }}" class="text-xs font-bold text-slate-500 hover:text-plush-600 inline-flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Master Produk
        </a>

        @if(auth()->user()->isAdmin())
            <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-sm transition-colors">
                <i class="fa-solid fa-pen-to-square mr-1.5"></i> Ubah Data Boneka
            </a>
        @endif
    </div>

    <!-- Product Card Header -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-pink-100 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-5">
                <div class="w-36 h-36 sm:w-48 sm:h-48 rounded-3xl overflow-hidden border border-pink-100 flex-shrink-0 shadow-sm bg-slate-100">
                    <img src="{{ asset($product->image_url ?? 'images/plushies/teddy-bear-pita.jpg') }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-cover object-center">
                </div>
                <div>
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase bg-pink-100 text-pink-700 border border-pink-200">
                            {{ $product->category->name ?? 'Kategori Boneka' }}
                        </span>
                        <span class="text-xs font-mono font-bold text-slate-400">{{ $product->sku }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900">{{ $product->name }}</h1>
                    <p class="text-xs text-slate-400 mt-1">Koleksi Asli Diza's Plushie Store</p>
                </div>
            </div>

            <!-- Stock Status Pill -->
            <div class="p-4 rounded-3xl {{ $product->stock <= $product->minimum_stock ? 'bg-rose-50 border border-rose-200 text-rose-700' : 'bg-emerald-50 border border-emerald-200 text-emerald-800' }} text-center sm:text-right min-w-[150px]">
                <span class="text-[10px] uppercase font-bold tracking-wider block">Saldo Stok Fisik</span>
                <span class="text-2xl font-black">{{ $product->stock }} pcs</span>
                <span class="text-[11px] block font-semibold mt-0.5">
                    @if($product->stock <= $product->minimum_stock)
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> Low Stock (Min: {{ $product->minimum_stock }})
                    @else
                        Aman (Min: {{ $product->minimum_stock }})
                    @endif
                </span>
            </div>
        </div>

        <!-- Specifications & Pricing Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6 pt-6 border-t border-slate-100">
            <!-- Specifications -->
            <div class="space-y-3">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Spesifikasi Boneka</h2>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Ukuran / Dimensi:</span>
                        <span class="font-bold text-slate-800">{{ $product->size ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Material & Isian:</span>
                        <span class="font-bold text-slate-800">{{ $product->material ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tanggal Dibuat:</span>
                        <span class="font-semibold text-slate-700">{{ $product->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Pricing Details -->
            <div class="space-y-3">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Analisis Finansial & Harga</h2>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2 text-sm">
                    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                        <div class="flex justify-between">
                            <span class="text-slate-500">Harga Beli (Modal Pabrik):</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-slate-500">Harga Jual ke Pelanggan:</span>
                        <span class="font-black text-rose-600 text-base">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                    </div>
                    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                        @php
                            $margin = $product->selling_price - $product->purchase_price;
                            $marginPct = $product->purchase_price > 0 ? ($margin / $product->purchase_price) * 100 : 0;
                        @endphp
                        <div class="flex justify-between pt-1 border-t border-slate-200">
                            <span class="text-slate-500">Estimasi Margin Laba:</span>
                            <span class="font-black text-emerald-600">
                                Rp {{ number_format($margin, 0, ',', '.') }} ({{ number_format($marginPct, 1) }}%)
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Movement History (Purchases & Sales) -->
    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Purchase History -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                <h2 class="font-extrabold text-slate-900 text-sm mb-3 flex items-center">
                    <i class="fa-solid fa-truck-ramp-box mr-2 text-blue-500"></i> Riwayat Pengadaan (Stock-In)
                </h2>
                <div class="divide-y divide-slate-100 text-xs">
                    @forelse($product->purchaseItems as $pi)
                        <div class="py-2.5 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-slate-800">{{ $pi->purchase->purchase_number ?? '-' }}</span>
                                <p class="text-slate-400 text-[11px]">{{ $pi->purchase->supplier->name ?? '-' }} &bull; {{ \Carbon\Carbon::parse($pi->purchase->purchased_at)->format('d M Y') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-blue-600">+{{ $pi->quantity }} pcs</span>
                                <p class="text-slate-500">@ Rp {{ number_format($pi->unit_cost, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="py-4 text-center text-slate-400">Belum ada riwayat stock-in pembelian.</p>
                    @endforelse
                </div>
            </div>

            <!-- Sale History -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                <h2 class="font-extrabold text-slate-900 text-sm mb-3 flex items-center">
                    <i class="fa-solid fa-cart-shopping mr-2 text-emerald-500"></i> Riwayat Penjualan (Stock-Out)
                </h2>
                <div class="divide-y divide-slate-100 text-xs">
                    @forelse($product->saleItems as $si)
                        <div class="py-2.5 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-slate-800">{{ $si->sale->sale_number ?? '-' }}</span>
                                <p class="text-slate-400 text-[11px]">{{ $si->sale->customer_name ?? 'Pelanggan' }} &bull; {{ \Carbon\Carbon::parse($si->sale->sold_at)->format('d M Y') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-emerald-600">-{{ $si->quantity }} pcs</span>
                                <p class="text-slate-500">@ Rp {{ number_format($si->unit_price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="py-4 text-center text-slate-400">Belum ada riwayat stock-out penjualan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
