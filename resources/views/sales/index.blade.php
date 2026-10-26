@extends('layouts.app', ['title' => $user->isCustomer() ? 'Riwayat Pesanan Saya' : 'Transaksi Penjualan (Stock-Out)'])

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-cash-register"></i>
                </span>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                    {{ $user->isCustomer() ? 'Riwayat Pesanan Saya' : 'Transaksi Penjualan (Stock-Out)' }}
                </h1>
            </div>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                {{ $user->isCustomer() ? "Daftar transaksi pembelian boneka Anda di Diza's Plushie beserta status pembayaran dan faktur belanja." : 'Pencatatan kasir toko dan alur stock-out pengurangan stok fisik boneka.' }}
            </p>
        </div>

        @if(!$user->isCustomer())
            <div>
                <a href="{{ route('sales.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-pink-500 hover:bg-pink-600 text-white font-bold text-sm shadow-sm transition-all duration-200">
                    <i class="fa-solid fa-plus mr-1.5"></i> Buat Penjualan Baru
                </a>
            </div>
        @endif
    </div>

    <!-- Multi-Filter & Search Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('sales.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <!-- Search Keyword -->
            <div class="sm:col-span-4">
                <label class="block text-xs font-bold text-slate-500 mb-1">
                    {{ $user->isCustomer() ? 'Cari Nomor Nota' : 'Cari No. Nota / Pembeli' }}
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="{{ $user->isCustomer() ? 'Contoh: INV-202610...' : 'No INV atau nama...' }}"
                        class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-pink-500 focus:border-pink-500 bg-slate-50 focus:bg-white transition-all">
                </div>
            </div>

            <!-- Status Filter -->
            <div class="sm:col-span-3">
                <label class="block text-xs font-bold text-slate-500 mb-1">Status Transaksi</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-pink-500 focus:border-pink-500 bg-slate-50 focus:bg-white transition-all">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Date Range Filter -->
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-500 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="w-full px-2.5 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-pink-500 focus:border-pink-500 bg-slate-50 focus:bg-white transition-all">
            </div>

            <div class="sm:col-span-3 flex items-center space-x-1.5">
                <div class="w-full">
                    <label class="block text-xs font-bold text-slate-500 mb-1">Sampai</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="w-full px-2.5 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-pink-500 focus:border-pink-500 bg-slate-50 focus:bg-white transition-all">
                </div>
                <button type="submit" class="mt-5 px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition-colors" title="Terapkan Filter">
                    <i class="fa-solid fa-filter"></i>
                </button>
                @if($search || $status || $startDate || $endDate)
                    <a href="{{ route('sales.index') }}" class="mt-5 px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-600 text-xs font-bold transition-colors" title="Reset filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>

        <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Ditemukan: <strong>{{ $sales->total() }}</strong> transaksi penjualan</span>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-extrabold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 w-12 text-center">No</th>
                        <th class="px-6 py-4">Nomor Nota</th>
                        <th class="px-6 py-4">Nama Pelanggan</th>
                        <th class="px-6 py-4">Waktu Transaksi</th>
                        @if(!$user->isCustomer())
                            <th class="px-6 py-4">Kasir / Pembuat</th>
                        @endif
                        <th class="px-6 py-4 text-center">Metode Bayar</th>
                        <th class="px-6 py-4 text-center">Jumlah Item</th>
                        <th class="px-6 py-4 text-right">Total Belanja</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sales as $index => $sale)
                        <tr class="hover:bg-pink-50/30 transition-colors {{ $sale->status === 'cancelled' ? 'bg-slate-50/60 opacity-75' : '' }}">
                            <td class="px-6 py-4 text-center font-bold text-slate-400">
                                {{ $sales->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('sales.show', $sale) }}" class="font-extrabold text-pink-600 hover:text-pink-700 hover:underline font-mono text-sm block">
                                    {{ $sale->sale_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800">
                                {{ $sale->customer_name ?? ($sale->customer->name ?? 'Pelanggan Umum') }}
                                @if($sale->customer_id)
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-extrabold bg-pink-100 text-pink-700 border border-pink-200 ml-1">
                                        MEMBER
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ \Carbon\Carbon::parse($sale->sold_at)->format('d M Y, H:i') }}
                            </td>
                            @if(!$user->isCustomer())
                                <td class="px-6 py-4 text-xs text-slate-600">
                                    <span class="font-bold text-slate-800">{{ $sale->user->name ?? '-' }}</span>
                                    <span class="block text-[10px] text-slate-400">({{ strtoupper($sale->user->role ?? '-') }})</span>
                                </td>
                            @endif
                            <td class="px-6 py-4 text-center">
                                @php
                                    $pm = $sale->payment_method ?? 'QRIS';
                                    $badgeClass = match($pm) {
                                        'QRIS' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'Transfer Bank' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'E-Wallet' => 'bg-pink-50 text-pink-700 border-pink-200',
                                        default => 'bg-amber-50 text-amber-700 border-amber-200'
                                    };
                                    $iconClass = match($pm) {
                                        'QRIS' => 'fa-qrcode',
                                        'Transfer Bank' => 'fa-building-columns',
                                        'E-Wallet' => 'fa-wallet',
                                        default => 'fa-money-bill-wave'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $badgeClass }}">
                                    <i class="fa-solid {{ $iconClass }} mr-1 text-[10px]"></i> {{ $pm }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-slate-700">
                                {{ $sale->items->sum('quantity') }} pcs
                            </td>
                            <td class="px-6 py-4 text-right font-black text-slate-900 text-sm">
                                Rp {{ number_format($sale->total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($sale->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-check-circle mr-1 text-[10px]"></i> COMPLETED
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-700">
                                        <i class="fa-solid fa-ban mr-1 text-[10px]"></i> CANCELLED
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center space-x-1.5">
                                    <a href="{{ route('sales.show', $sale) }}" class="p-2 rounded-lg bg-pink-50 hover:bg-pink-100 text-pink-700 border border-pink-200 transition-colors" title="Lihat Faktur / Struk">
                                        <i class="fa-solid fa-receipt text-xs"></i>
                                    </a>

                                    @if(auth()->user()->isAdmin() && $sale->status === 'completed')
                                        <form action="{{ route('sales.cancel', $sale) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin MEMBATALKAN penjualan {{ $sale->sale_number }}? Stok boneka akan dikembalikan ke inventori secara otomatis.');">
                                            @csrf
                                            <button type="submit" class="p-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition-colors" title="Batalkan Penjualan">
                                                <i class="fa-solid fa-ban text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $user->isCustomer() ? 9 : 10 }}" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-cash-register text-4xl mb-2 text-slate-300"></i>
                                <p class="font-semibold">Belum ada transaksi penjualan yang tercatat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        @if($sales->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $sales->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
