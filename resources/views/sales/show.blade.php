@extends('layouts.app', ['title' => 'Faktur Penjualan ' . $sale->sale_number])

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <div class="flex items-center justify-between">
        <a href="{{ route('sales.index') }}" class="text-xs font-medium text-gray-500 hover:text-gray-700 inline-flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Riwayat Penjualan
        </a>

        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="inline-flex items-center px-3 py-1.5 rounded-md bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 text-xs font-medium transition-colors">
                <i class="fa-solid fa-print mr-1.5 text-gray-500"></i> Cetak Struk
            </button>

            @if(auth()->user()->isAdmin() && $sale->status === 'completed')
                <form action="{{ route('sales.cancel', $sale) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan transaksi penjualan {{ $sale->sale_number }}? Stok produk akan dikembalikan.');">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-md bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-xs font-medium transition-colors">
                        <i class="fa-solid fa-ban mr-1.5"></i> Batalkan Penjualan Ini
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Store Invoice Card -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 space-y-6 print:p-0 print:border-none print:shadow-none">

        <!-- Invoice Header -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 border-b border-gray-100 pb-5">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Faktur Penjualan & Pembelian</span>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 font-mono mt-0.5">{{ $sale->sale_number }}</h1>
                <p class="text-xs text-gray-500 mt-1">Waktu: {{ \Carbon\Carbon::parse($sale->sold_at)->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>

            <!-- Status Badge -->
            <div class="text-left sm:text-right">
                @if($sale->status === 'completed')
                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                        Status: Completed
                    </span>
                    <p class="text-[11px] text-gray-400 mt-1">Stok telah terpotong dari gudang</p>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                        Status: Cancelled
                    </span>
                    <p class="text-[11px] text-red-600 font-medium mt-1">Stok telah dikembalikan ke inventori</p>
                @endif
            </div>
        </div>

        <!-- Customer, Cashier & Payment Info Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div class="bg-gray-50 p-4 rounded-md border border-gray-200 space-y-1">
                <span class="text-[10px] uppercase font-semibold text-gray-400 block">Identitas Pembeli</span>
                <p class="text-sm font-bold text-gray-900">
                    {{ $sale->customer_name ?? ($sale->customer->name ?? 'Pelanggan Umum') }}
                </p>
                @if($sale->customer)
                    <p class="text-gray-600">Email: {{ $sale->customer->email }}</p>
                    <p class="text-gray-600">Telepon: {{ $sale->customer->phone ?: '-' }}</p>
                    <p class="text-gray-500">Alamat: {{ $sale->customer->address ?: '-' }}</p>
                @else
                    <span class="inline-block px-1.5 py-0.5 rounded text-[11px] bg-gray-200 text-gray-700">
                        Pelanggan Umum (Guest)
                    </span>
                @endif
            </div>

            <div class="bg-gray-50 p-4 rounded-md border border-gray-200 space-y-1">
                <span class="text-[10px] uppercase font-semibold text-gray-400 block">Pembayaran</span>
                <p class="text-sm font-bold text-gray-900">{{ $sale->payment_method ?? 'Cash' }}</p>
                <div class="pt-1">
                    <span class="text-gray-500">Status Pembayaran:</span>
                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold bg-green-100 text-green-800 ml-1">
                        {{ strtoupper($sale->payment_status ?? 'PAID') }}
                    </span>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-md border border-gray-200 space-y-1">
                <span class="text-[10px] uppercase font-semibold text-gray-400 block">Petugas Kasir</span>
                <p class="text-sm font-bold text-gray-900">{{ $sale->user->name ?? '-' }}</p>
                <p class="text-gray-500">Role: {{ strtoupper($sale->user->role ?? '-') }}</p>
                @if($sale->notes)
                    <p class="text-gray-500 pt-1 border-t border-gray-200">Catatan: {{ $sale->notes }}</p>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <div class="border border-gray-200 rounded-md overflow-hidden">
            <table class="w-full text-left text-xs sm:text-sm text-gray-700">
                <thead class="bg-gray-50 uppercase text-xs font-semibold text-gray-600 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-2.5 w-12 text-center">No</th>
                        <th class="px-4 py-2.5">Produk Hijab</th>
                        <th class="px-4 py-2.5">Kategori & Ukuran</th>
                        <th class="px-4 py-2.5 text-center">Jumlah</th>
                        <th class="px-4 py-2.5 text-right">Harga Satuan</th>
                        <th class="px-4 py-2.5 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($sale->items as $index => $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-center text-gray-400">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ asset($item->product->image_path ?? 'images/hijab/pashmina-ceruty.jpg') }}" 
                                         alt="{{ $item->product->name ?? 'Hijab' }}" 
                                         class="w-10 h-10 object-cover rounded border border-gray-200 flex-shrink-0">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $item->product->name ?? 'Produk Dihapus' }}</div>
                                        <div class="text-xs text-gray-400 font-mono">{{ $item->product->sku ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                <div>{{ $item->product->category->name ?? '-' }}</div>
                                <div class="text-gray-400">{{ $item->product->size ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-center font-medium">{{ $item->quantity }} pcs</td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200 font-bold text-gray-900">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right uppercase text-xs text-gray-600">Total Kuantitas:</td>
                        <td class="px-4 py-3 text-center font-bold">{{ $sale->items->sum('quantity') }} pcs</td>
                        <td class="px-4 py-3 text-right uppercase text-xs text-gray-600">Total Tagihan:</td>
                        <td class="px-4 py-3 text-right text-base text-rose-600 font-bold">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="text-center pt-3 text-xs text-gray-400 border-t border-gray-100">
            Terima kasih telah berbelanja di HijabSociety.
        </div>
    </div>
</div>
@endsection
