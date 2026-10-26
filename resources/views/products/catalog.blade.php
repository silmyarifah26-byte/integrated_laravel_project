@extends('layouts.app', ['title' => 'Katalog Boneka & Plushie'])

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-pink-400 via-rose-300 to-purple-300 rounded-3xl p-6 sm:p-8 text-white shadow-md relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 text-white/15 text-9xl pointer-events-none">
            <i class="fa-solid fa-heart"></i>
        </div>
        <div class="relative z-10 max-w-2xl">
            <span class="px-3 py-1 rounded-full text-xs font-black bg-white/25 backdrop-blur border border-white/40 text-white mb-2 inline-block">
                🧸 Koleksi Lengkap Diza's Plushie
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Katalog Boneka & Plushie</h1>
            <p class="text-white/95 text-sm mt-1">
                Pilih boneka favorit Anda dari berbagai kategori seperti Teddy Bear, Karakter Hewan, Anime Plushie, Buket Wisuda, hingga Bantal Boneka empuk dengan bahan super lembut.
            </p>
        </div>
    </div>

    <!-- Search & Category Filter -->
    <div class="bg-white p-4 rounded-3xl border border-pink-100 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form action="{{ route('products.catalog') }}" method="GET" class="flex-1 grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-8 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama boneka, karakter, atau ukuran..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-pink-400 focus:border-pink-400 bg-pink-50/20 focus:bg-white transition-all">
            </div>

            <div class="sm:col-span-4 flex items-center space-x-2">
                <select name="category_id" class="w-full px-3 py-2.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-pink-400 focus:border-pink-400 bg-pink-50/20 focus:bg-white transition-all">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-pink-500 hover:bg-pink-600 text-white font-bold text-xs shadow-sm transition-colors">
                    Filter
                </button>
            </div>
        </form>

        <span class="text-xs font-semibold text-slate-500 whitespace-nowrap">
            Ditemukan: <strong>{{ $products->total() }}</strong> Boneka
        </span>
    </div>

    <!-- Product Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($products as $item)
            <div class="bg-white rounded-3xl border border-pink-100 p-4 sm:p-5 shadow-sm hover:shadow-md hover:border-pink-200 transition-all duration-200 flex flex-col justify-between group">
                <div>
                    <!-- Real Plushie Photo -->
                    <div class="w-full h-52 rounded-2xl overflow-hidden mb-3 relative group-hover:scale-[1.02] transition-transform duration-200 bg-slate-100 border border-pink-100">
                        <img src="{{ asset($item->image_url ?? 'images/plushies/teddy-bear-pita.jpg') }}" 
                             alt="{{ $item->name }}" 
                             class="object-cover object-center w-full h-52 rounded-2xl">
                        
                        <div class="absolute top-2.5 left-2.5">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-white/90 backdrop-blur text-pink-600 border border-pink-200 shadow-xs">
                                {{ $item->category->name ?? 'Plushie' }}
                            </span>
                        </div>
                        <div class="absolute top-2.5 right-2.5">
                            <span class="text-[10px] font-bold text-slate-400 font-mono bg-white/80 px-2 py-0.5 rounded-full border border-slate-100">
                                {{ $item->sku }}
                            </span>
                        </div>
                    </div>

                    <!-- Doll Name -->
                    <h2 class="font-extrabold text-slate-800 text-base group-hover:text-pink-600 transition-colors line-clamp-2">
                        {{ $item->name }}
                    </h2>

                    <!-- Specs -->
                    <div class="mt-3 space-y-1 text-xs text-slate-600 bg-pink-50/30 p-2.5 rounded-2xl border border-pink-100/50">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Ukuran:</span>
                            <span class="font-bold text-slate-700">{{ $item->size ?: '-' }}</span>
                        </div>
                        <div class="flex items-start justify-between gap-1">
                            <span class="text-slate-400">Bahan:</span>
                            <span class="font-medium text-slate-600 text-right line-clamp-1" title="{{ $item->material }}">{{ $item->material ?: '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Bottom Pricing & Stock -->
                <div class="mt-4 pt-3 border-t border-pink-50 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Harga</span>
                        <span class="text-lg font-black text-pink-600">
                            Rp {{ number_format($item->selling_price, 0, ',', '.') }}
                        </span>
                    </div>

                    @if($item->stock > 0)
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Stok: {{ $item->stock }}
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                            Habis
                        </span>
                    @endif
                </div>

                <!-- Quick Action Button -->
                <div class="mt-3">
                    <a href="{{ route('sales.create', ['product_id' => $item->id]) }}" class="w-full inline-flex items-center justify-center py-2 px-3 rounded-2xl bg-pink-50 hover:bg-pink-100 text-pink-700 font-bold text-xs border border-pink-200 transition-colors">
                        <i class="fa-solid fa-basket-shopping mr-1.5 text-pink-500"></i> Pesan Boneka Ini
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-12 rounded-3xl border border-slate-200 text-center text-slate-400">
                <i class="fa-solid fa-basket-shopping text-5xl mb-3 text-slate-300"></i>
                <p class="font-bold text-base text-slate-700">Tidak ada boneka yang sesuai dengan filter pencarian.</p>
                <p class="text-xs text-slate-400 mt-1">Silakan coba pilih kategori lain atau bersihkan kata kunci.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination Bar -->
    @if($products->hasPages())
        <div class="bg-white p-4 rounded-2xl border border-slate-200">
            {{ $products->links() }}
        </div>
    @endif

</div>
@endsection
