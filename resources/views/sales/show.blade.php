@extends('layouts.app', ['title' => 'Faktur Penjualan ' . $sale->sale_number])

@section('content')
<div class="max-w-4xl mx-auto py-4 space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('sales.index') }}" class="text-xs font-bold text-slate-500 hover:text-pink-600 inline-flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Riwayat Penjualan
        </a>

        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-bold text-xs shadow-sm transition-colors">
                <i class="fa-solid fa-print mr-1.5 text-pink-500"></i> Cetak Struk
            </button>

            <!-- Cancel Transaction Button (Admin Only & Completed Only) -->
            @if(auth()->user()->isAdmin() && $sale->status === 'completed')
                <form action="{{ route('sales.cancel', $sale) }}" method="POST" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin membatalkan transaksi penjualan {{ $sale->sale_number }}? Seluruh kuantitas boneka pada nota ini akan dikembalikan ke stok inventori toko secara otomatis.');">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs shadow-sm transition-colors">
                        <i class="fa-solid fa-ban mr-1.5"></i> Batalkan Penjualan Ini
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Store Invoice Card -->
    <div class="bg-white rounded-3xl border border-pink-100 shadow-sm overflow-hidden p-6 sm:p-8 space-y-6 print:p-2 print:shadow-none print:border-none">

        <!-- Invoice Header -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 border-b border-pink-100/60 pb-6">
            <div>
                <div class="flex items-center space-x-2 mb-1.5">
                    <span class="w-8 h-8 rounded-xl bg-pink-100 text-pink-600 flex items-center justify-center text-sm shadow-sm">
                        <i class="fa-solid fa-heart"></i>
                    </span>
                    <div>
                        <span class="text-xs font-black uppercase tracking-wider text-pink-500 block">Diza's Plushie Store</span>
                        <span class="text-[10px] text-slate-400">Official Cute Plushie & Doll Receipt</span>
                    </div>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-mono tracking-tight">{{ $sale->sale_number }}</h1>
                <p class="text-xs text-slate-400 mt-1">Waktu Transaksi: {{ \Carbon\Carbon::parse($sale->sold_at)->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>

            <!-- Status Badge -->
            <div class="text-left sm:text-right space-y-1">
                @if($sale->status === 'completed')
                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                        <i class="fa-solid fa-circle-check mr-1.5 text-sm"></i> SUKSES / COMPLETED
                    </span>
                    <p class="text-[11px] text-slate-400">Stok telah terpotong dari gudang</p>
                @else
                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-black bg-rose-100 text-rose-700 border border-rose-200">
                        <i class="fa-solid fa-ban mr-1.5 text-sm"></i> DIBATALKAN / CANCELLED
                    </span>
                    <p class="text-[11px] text-rose-500 font-semibold">Stok telah dikembalikan ke inventori</p>
                @endif
            </div>
        </div>

        <!-- Customer, Cashier & Payment Info Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <!-- Customer Info -->
            <div class="bg-pink-50/40 p-4 rounded-2xl border border-pink-100/70 space-y-1.5">
                <span class="text-[10px] uppercase font-bold text-pink-600 block tracking-wider">
                    <i class="fa-solid fa-user-heart mr-1"></i> Identitas Pembeli
                </span>
                <p class="text-sm font-extrabold text-slate-900">
                    {{ $sale->customer_name ?? ($sale->customer->name ?? 'Pelanggan Umum') }}
                </p>
                @if($sale->customer)
                    <p class="text-slate-600"><strong class="text-slate-700">Akun Member:</strong> {{ $sale->customer->email }}</p>
                    <p class="text-slate-600"><strong class="text-slate-700">No. Kontak:</strong> {{ $sale->customer->phone ?: '-' }}</p>
                    <p class="text-slate-500 text-[11px]"><strong class="text-slate-700">Alamat Kirim:</strong> {{ $sale->customer->address ?: '-' }}</p>
                @else
                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700">
                        Pelanggan Umum (Guest)
                    </span>
                @endif
            </div>

            <!-- Payment Info -->
            <div class="bg-purple-50/40 p-4 rounded-2xl border border-purple-100/70 space-y-1.5">
                <span class="text-[10px] uppercase font-bold text-purple-600 block tracking-wider">
                    <i class="fa-solid fa-credit-card mr-1"></i> Pembayaran
                </span>
                @php
                    $pm = $sale->payment_method ?? 'QRIS';
                    $iconClass = match($pm) {
                        'QRIS' => 'fa-qrcode text-emerald-600',
                        'Transfer Bank' => 'fa-building-columns text-indigo-600',
                        'E-Wallet' => 'fa-wallet text-pink-600',
                        default => 'fa-money-bill-wave text-amber-600'
                    };
                @endphp
                <div class="flex items-center space-x-2 pt-0.5">
                    <i class="fa-solid {{ $iconClass }} text-base"></i>
                    <p class="text-sm font-extrabold text-slate-900">{{ $pm }}</p>
                </div>
                <div class="pt-1 flex items-center space-x-1.5">
                    <span class="text-[11px] text-slate-500">Status Pembayaran:</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800">
                        {{ strtoupper($sale->payment_status ?? 'PAID') }}
                    </span>
                </div>
                <p class="text-[11px] text-slate-400">Pembayaran terverifikasi aman & instan</p>
            </div>

            <!-- Cashier & Note Info -->
            <div class="bg-amber-50/40 p-4 rounded-2xl border border-amber-100/70 space-y-1.5">
                <span class="text-[10px] uppercase font-bold text-amber-700 block tracking-wider">
                    <i class="fa-solid fa-receipt mr-1"></i> Kasir & Catatan
                </span>
                <p class="text-sm font-extrabold text-slate-900">{{ $sale->user->name ?? '-' }} ({{ strtoupper($sale->user->role ?? '-') }})</p>
                <div class="pt-1 border-t border-amber-100 mt-1">
                    <span class="text-slate-400 block text-[10px]">Catatan Pesanan:</span>
                    <p class="text-slate-700 italic">{{ $sale->notes ?: 'Tidak ada catatan khusus.' }}</p>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="space-y-3">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Rincian Boneka yang Dibeli</h2>
            <div class="border border-pink-100 rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full text-left text-xs sm:text-sm text-slate-600">
                    <thead class="bg-pink-50/50 text-[11px] uppercase font-extrabold text-slate-500 border-b border-pink-100">
                        <tr>
                            <th class="px-5 py-3 w-12 text-center">No</th>
                            <th class="px-5 py-3">Produk Boneka</th>
                            <th class="px-5 py-3">Kategori & Ukuran</th>
                            <th class="px-5 py-3 text-center">Kuantitas</th>
                            <th class="px-5 py-3 text-right">Harga Satuan</th>
                            <th class="px-5 py-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-pink-50">
                        @foreach($sale->items as $index => $item)
                            <tr class="hover:bg-pink-50/30 transition-colors">
                                <td class="px-5 py-3.5 text-center font-bold text-slate-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center space-x-3">
                                        <img src="{{ asset($item->product->image_url ?? 'images/plushies/teddy-bear-pita.jpg') }}" 
                                             alt="{{ $item->product->name ?? 'Boneka' }}" 
                                             class="w-12 h-12 object-cover rounded-xl border border-pink-100 flex-shrink-0 shadow-xs">
                                        <div>
                                            <div class="font-extrabold text-slate-900">{{ $item->product->name ?? 'Produk Dihapus' }}</div>
                                            <div class="text-[11px] text-slate-400 font-mono">{{ $item->product->sku ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-xs">
                                    <span class="font-semibold text-slate-700">{{ $item->product->category->name ?? '-' }}</span>
                                    <div class="text-slate-400 text-[11px]">{{ $item->product->size ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-center font-black text-pink-600">
                                    {{ $item->quantity }} pcs
                                </td>
                                <td class="px-5 py-3.5 text-right font-medium text-slate-700">
                                    Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3.5 text-right font-black text-slate-900">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-pink-50/60 font-black text-slate-900 border-t border-pink-100">
                        <tr>
                            <td colspan="3" class="px-5 py-3 text-right text-xs uppercase tracking-wider text-slate-500">
                                Total Kuantitas:
                            </td>
                            <td class="px-5 py-3 text-center text-sm font-black text-pink-600">
                                {{ $sale->items->sum('quantity') }} pcs
                            </td>
                            <td class="px-5 py-3 text-right text-xs uppercase tracking-wider text-slate-500">
                                Total Tagihan:
                            </td>
                            <td class="px-5 py-3 text-right text-base font-black text-pink-600">
                                Rp {{ number_format($sale->total, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="text-center pt-4 border-t border-dashed border-pink-200 text-xs text-slate-400 space-y-1">
            <p class="font-bold text-pink-600">✨ Terima kasih telah berbelanja di Diza's Plushie! ✨</p>
            <p>Boneka lucu yang dipeluk penuh kehangatan, siap menemani harimu.</p>
        </div>

    </div>
</div>
@endsection
