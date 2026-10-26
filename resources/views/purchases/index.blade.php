@extends('layouts.app', ['title' => 'Daftar Pembelian (Stock-In)'])

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </span>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Transaksi Pembelian (Stock-In)</h1>
            </div>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">Pencatatan pengadaan stok boneka dari pabrik / supplier resmi ke inventori toko.</p>
        </div>

        <div>
            <a href="{{ route('purchases.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-sm transition-all duration-200">
                <i class="fa-solid fa-plus mr-1.5"></i> Catat Pembelian Baru
            </a>
        </div>
    </div>

    <!-- Multi-Filter & Search Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('purchases.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <!-- Search Keyword -->
            <div class="sm:col-span-3">
                <label class="block text-xs font-bold text-slate-500 mb-1">Cari No PO / Supplier</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="No PO atau nama..."
                        class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-slate-50 focus:bg-white transition-all">
                </div>
            </div>

            <!-- Supplier Filter -->
            <div class="sm:col-span-3">
                <label class="block text-xs font-bold text-slate-500 mb-1">Pemasok / Pabrik</label>
                <select name="supplier_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-slate-50 focus:bg-white transition-all">
                    <option value="">-- Semua Supplier --</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ $supplierId == $sup->id ? 'selected' : '' }}>
                            {{ $sup->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-500 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-slate-50 focus:bg-white transition-all">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Date Range Filter -->
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-500 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="w-full px-2.5 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-slate-50 focus:bg-white transition-all">
            </div>

            <div class="sm:col-span-2 flex items-center space-x-1.5">
                <div class="w-full">
                    <label class="block text-xs font-bold text-slate-500 mb-1">Sampai</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="w-full px-2.5 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-slate-50 focus:bg-white transition-all">
                </div>
                <button type="submit" class="mt-5 px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition-colors" title="Terapkan Filter">
                    <i class="fa-solid fa-filter"></i>
                </button>
                @if($search || $supplierId || $status || $startDate || $endDate)
                    <a href="{{ route('purchases.index') }}" class="mt-5 px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-600 text-xs font-bold transition-colors" title="Reset filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>

        <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Ditemukan: <strong>{{ $purchases->total() }}</strong> transaksi pengadaan</span>
        </div>
    </div>

    <!-- Purchases Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-extrabold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 w-12 text-center">No</th>
                        <th class="px-6 py-4">Nomor PO</th>
                        <th class="px-6 py-4">Pemasok Pabrik</th>
                        <th class="px-6 py-4">Tanggal Pengadaan</th>
                        <th class="px-6 py-4">Dicatat Oleh</th>
                        <th class="px-6 py-4 text-center">Total Item</th>
                        <th class="px-6 py-4 text-right">Total Biaya</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($purchases as $index => $po)
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $po->status === 'cancelled' ? 'bg-slate-50/60 opacity-75' : '' }}">
                            <td class="px-6 py-4 text-center font-bold text-slate-400">
                                {{ $purchases->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('purchases.show', $po) }}" class="font-extrabold text-blue-600 hover:underline font-mono text-sm block">
                                    {{ $po->purchase_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800">
                                {{ $po->supplier->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ \Carbon\Carbon::parse($po->purchased_at)->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                <span class="font-bold text-slate-800">{{ $po->user->name ?? '-' }}</span>
                                <span class="block text-[10px] text-slate-400">({{ strtoupper($po->user->role ?? '-') }})</span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-slate-700">
                                {{ $po->items->sum('quantity') }} pcs
                            </td>
                            <td class="px-6 py-4 text-right font-black text-slate-900 text-sm">
                                Rp {{ number_format($po->total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($po->status === 'completed')
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
                                    <a href="{{ route('purchases.show', $po) }}" class="p-2 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 transition-colors" title="Lihat Faktur / Detail">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>

                                    @if(auth()->user()->isAdmin() && $po->status === 'completed')
                                        <form action="{{ route('purchases.cancel', $po) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin MEMBATALKAN transaksi {{ $po->purchase_number }}? Seluruh kuantitas stok akan dikembalikan (dikurangi) secara otomatis.');">
                                            @csrf
                                            <button type="submit" class="p-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition-colors" title="Batalkan Transaksi">
                                                <i class="fa-solid fa-ban text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-truck-ramp-box text-4xl mb-2 text-slate-300"></i>
                                <p class="font-semibold">Tidak ada transaksi pembelian yang ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        @if($purchases->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
