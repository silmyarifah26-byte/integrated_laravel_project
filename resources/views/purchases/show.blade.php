@extends('layouts.app', ['title' => 'Detail Transaksi Pembelian ' . $purchase->purchase_number])

@section('content')
<div class="max-w-4xl mx-auto py-4 space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('purchases.index') }}" class="text-xs font-bold text-slate-500 hover:text-blue-600 inline-flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Daftar Pembelian
        </a>

        <!-- Cancel Transaction Button (Admin Only & Completed Only) -->
        @if(auth()->user()->isAdmin() && $purchase->status === 'completed')
            <form action="{{ route('purchases.cancel', $purchase) }}" method="POST" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin membatalkan transaksi {{ $purchase->purchase_number }}? Seluruh kuantitas stok produk pada transaksi ini akan dikurangi kembali dari inventori toko.');">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs shadow-sm transition-colors">
                    <i class="fa-solid fa-ban mr-1.5"></i> Batalkan Transaksi Ini
                </button>
            </form>
        @endif
    </div>

    <!-- PO Invoice Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-6 sm:p-8 space-y-6">

        <!-- Invoice Header -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 border-b border-slate-100 pb-6">
            <div>
                <div class="flex items-center space-x-2 mb-1.5">
                    <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-truck-ramp-box"></i>
                    </span>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Faktur Pengadaan (Stock-In)</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-mono tracking-tight">{{ $purchase->purchase_number }}</h1>
                <p class="text-xs text-slate-400 mt-1">Dicatat pada: {{ \Carbon\Carbon::parse($purchase->purchased_at)->translatedFormat('d F Y, H:i') }}</p>
            </div>

            <!-- Status Badge -->
            <div class="text-left sm:text-right">
                @if($purchase->status === 'completed')
                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                        <i class="fa-solid fa-circle-check mr-1.5 text-sm"></i> STATUS: COMPLETED
                    </span>
                    <p class="text-[11px] text-slate-400 mt-1">Stok telah ditambahkan ke inventori</p>
                @else
                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-black bg-rose-100 text-rose-700 border border-rose-200">
                        <i class="fa-solid fa-ban mr-1.5 text-sm"></i> STATUS: CANCELLED
                    </span>
                    <p class="text-[11px] text-rose-500 font-semibold mt-1">Transaksi dibatalkan & stok dikembalikan</p>
                @endif
            </div>
        </div>

        <!-- Supplier & Cashier Info Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <!-- Supplier Info -->
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1.5">
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Informasi Pemasok / Pabrik</span>
                <p class="text-sm font-extrabold text-slate-900">{{ $purchase->supplier->name ?? '-' }}</p>
                <p class="text-slate-600"><strong class="text-slate-700">Telepon:</strong> {{ $purchase->supplier->phone ?? '-' }}</p>
                @if($purchase->supplier->email)
                    <p class="text-slate-600"><strong class="text-slate-700">Email:</strong> {{ $purchase->supplier->email }}</p>
                @endif
                <p class="text-slate-500 text-[11px]"><strong class="text-slate-700">Alamat:</strong> {{ $purchase->supplier->address ?? '-' }}</p>
            </div>

            <!-- Officer & Note Info -->
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1.5">
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Petugas Pencatat & Catatan</span>
                <p class="text-sm font-extrabold text-slate-900">{{ $purchase->user->name ?? '-' }} ({{ strtoupper($purchase->user->role ?? '-') }})</p>
                <p class="text-slate-600"><strong class="text-slate-700">Email:</strong> {{ $purchase->user->email ?? '-' }}</p>
                <div class="pt-1 border-t border-slate-200/60 mt-1">
                    <span class="text-slate-400 block text-[10px]">Catatan:</span>
                    <p class="text-slate-700 italic">{{ $purchase->notes ?: 'Tidak ada catatan khusus.' }}</p>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="space-y-3">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Rincian Boneka yang Dipesan</h2>
            <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full text-left text-xs sm:text-sm text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase font-extrabold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3 w-12 text-center">No</th>
                            <th class="px-5 py-3">Produk Boneka</th>
                            <th class="px-5 py-3">Kategori & Ukuran</th>
                            <th class="px-5 py-3 text-center">Kuantitas Masuk</th>
                            <th class="px-5 py-3 text-right">Harga Beli Satuan</th>
                            <th class="px-5 py-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($purchase->items as $index => $item)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-3.5 text-center font-bold text-slate-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="font-extrabold text-slate-900">{{ $item->product->name ?? 'Produk Dihapus' }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $item->product->sku ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-xs">
                                    <span class="font-semibold text-slate-700">{{ $item->product->category->name ?? '-' }}</span>
                                    <div class="text-slate-400 text-[11px]">{{ $item->product->size ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-center font-black text-blue-600">
                                    +{{ $item->quantity }} pcs
                                </td>
                                <td class="px-5 py-3.5 text-right font-medium text-slate-700">
                                    Rp {{ number_format($item->unit_cost, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3.5 text-right font-black text-slate-900">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 font-black text-slate-900 border-t border-slate-200">
                        <tr>
                            <td colspan="3" class="px-5 py-3 text-right text-xs uppercase tracking-wider text-slate-500">
                                Total Kuantitas:
                            </td>
                            <td class="px-5 py-3 text-center text-sm font-black text-blue-700">
                                {{ $purchase->items->sum('quantity') }} pcs
                            </td>
                            <td class="px-5 py-3 text-right text-xs uppercase tracking-wider text-slate-500">
                                Total Pembayaran:
                            </td>
                            <td class="px-5 py-3 text-right text-base font-black text-blue-700">
                                Rp {{ number_format($purchase->total, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
