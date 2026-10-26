@extends('layouts.app', ['title' => $user->isCustomer() ? 'Riwayat Pesanan Saya' : 'Transaksi Penjualan (Stock-Out)'])

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">
                {{ $user->isCustomer() ? 'Riwayat Pesanan Saya' : 'Transaksi Penjualan (Stock-Out)' }}
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                {{ $user->isCustomer() ? 'Daftar transaksi belanja hijab Anda beserta status dan rincian faktur.' : 'Pencatatan kasir toko dan riwayat pengurangan stok produk.' }}
            </p>
        </div>

        @if(!$user->isCustomer())
            <div>
                <a href="{{ route('sales.create') }}" class="inline-flex items-center px-3.5 py-2 rounded-md bg-rose-600 hover:bg-rose-700 text-white font-medium text-xs transition-colors">
                    <i class="fa-solid fa-plus mr-1.5"></i> Buat Penjualan Baru
                </a>
            </div>
        @endif
    </div>

    <!-- Multi-Filter & Search Bar -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
        <form action="{{ route('sales.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <!-- Search Keyword -->
            <div class="sm:col-span-4">
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    {{ $user->isCustomer() ? 'Cari Nomor Nota' : 'Cari No. Nota / Pembeli' }}
                </label>
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="{{ $user->isCustomer() ? 'Contoh: INV-202610...' : 'No INV atau nama pembeli...' }}"
                    class="w-full px-3 py-2 rounded-md border border-gray-300 text-xs text-gray-900 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500">
            </div>

            <!-- Status Filter -->
            <div class="sm:col-span-3">
                <label class="block text-xs font-medium text-gray-500 mb-1">Status Transaksi</label>
                <select name="status" class="w-full px-3 py-2 rounded-md border border-gray-300 text-xs text-gray-900 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Date Range Filter -->
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="w-full px-2.5 py-2 rounded-md border border-gray-300 text-xs text-gray-900 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500">
            </div>

            <div class="sm:col-span-3 flex items-center space-x-1.5">
                <div class="w-full">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Sampai</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="w-full px-2.5 py-2 rounded-md border border-gray-300 text-xs text-gray-900 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500">
                </div>
                <button type="submit" class="mt-5 px-3 py-2 rounded-md bg-gray-900 hover:bg-gray-800 text-white text-xs font-medium transition-colors" title="Terapkan Filter">
                    Filter
                </button>
                @if($search || $status || $startDate || $endDate)
                    <a href="{{ route('sales.index') }}" class="mt-5 px-3 py-2 rounded-md border border-gray-300 hover:bg-gray-50 text-gray-600 text-xs font-medium transition-colors" title="Reset filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
            <span>Ditemukan: <strong>{{ $sales->total() }}</strong> transaksi penjualan</span>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-600 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 w-12 text-center">No</th>
                        <th class="px-4 py-3">Nomor Nota</th>
                        <th class="px-4 py-3">Nama Pelanggan</th>
                        <th class="px-4 py-3">Waktu Transaksi</th>
                        @if(!$user->isCustomer())
                            <th class="px-4 py-3">Kasir</th>
                        @endif
                        <th class="px-4 py-3 text-center">Metode Bayar</th>
                        <th class="px-4 py-3 text-center">Jumlah Item</th>
                        <th class="px-4 py-3 text-right">Total Belanja</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($sales as $index => $sale)
                        <tr class="hover:bg-gray-50 {{ $sale->status === 'cancelled' ? 'bg-gray-50/60 opacity-75' : '' }}">
                            <td class="px-4 py-3 text-center font-medium text-gray-400">
                                {{ $sales->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-3 font-mono font-medium">
                                <a href="{{ route('sales.show', $sale) }}" class="text-blue-600 hover:underline">
                                    {{ $sale->sale_number }}
                                </a>
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ $sale->customer_name ?? ($sale->customer->name ?? 'Pelanggan Umum') }}
                                @if($sale->customer_id)
                                    <span class="inline-block px-1.5 py-0.2 rounded text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200 ml-1">
                                        Member
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($sale->sold_at)->format('d M Y, H:i') }}
                            </td>
                            @if(!$user->isCustomer())
                                <td class="px-4 py-3 text-xs text-gray-600">
                                    <div>{{ $sale->user->name ?? '-' }}</div>
                                </td>
                            @endif
                            <td class="px-4 py-3 text-center text-xs">
                                <span class="px-2 py-0.5 rounded border border-gray-200 bg-gray-50 font-medium text-gray-700">
                                    {{ $sale->payment_method ?? 'Cash' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-medium text-gray-700">
                                {{ $sale->items->sum('quantity') }} pcs
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900 text-sm">
                                Rp {{ number_format($sale->total, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($sale->status === 'completed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800">
                                        Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-800">
                                        Cancelled
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('sales.show', $sale) }}" class="px-2.5 py-1 text-xs border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                                    Faktur
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ !$user->isCustomer() ? 10 : 9 }}" class="px-4 py-8 text-center text-gray-400 text-sm">
                                Tidak ada data transaksi penjualan yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($sales->hasPages())
            <div class="p-3 border-t border-gray-200 bg-gray-50">
                {{ $sales->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
