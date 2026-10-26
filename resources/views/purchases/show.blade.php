@extends('layouts.app', ['title' => 'Detail Transaksi Pembelian ' . $purchase->purchase_number])

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <div class="flex items-center justify-between">
        <a href="{{ route('purchases.index') }}" class="text-xs font-medium text-gray-500 hover:text-gray-700 inline-flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Riwayat Pembelian
        </a>

        @if(auth()->user()->isAdmin() && $purchase->status === 'completed')
            <form action="{{ route('purchases.cancel', $purchase) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan transaksi {{ $purchase->purchase_number }}? Stok produk akan dikurangi kembali.');">
                @csrf
                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-md bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-medium text-xs transition-colors">
                    <i class="fa-solid fa-ban mr-1.5"></i> Batalkan Transaksi Ini
                </button>
            </form>
        @endif
    </div>

    <!-- PO Invoice Card -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 space-y-6">

        <!-- Invoice Header -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 border-b border-gray-100 pb-5">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Faktur Pengadaan (Stock-In)</span>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 font-mono mt-0.5">{{ $purchase->purchase_number }}</h1>
                <p class="text-xs text-gray-500 mt-1">Tanggal: {{ \Carbon\Carbon::parse($purchase->purchased_at)->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>

            <!-- Status Badge -->
            <div class="text-left sm:text-right">
                @if($purchase->status === 'completed')
                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                        Status: Completed
                    </span>
                    <p class="text-[11px] text-gray-400 mt-1">Stok telah ditambahkan ke toko</p>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                        Status: Cancelled
                    </span>
                    <p class="text-[11px] text-red-600 font-medium mt-1">Transaksi dibatalkan & stok dikembalikan</p>
                @endif
            </div>
        </div>

        <!-- Supplier & User Info Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="bg-gray-50 p-4 rounded-md border border-gray-200 space-y-1">
                <span class="text-[10px] uppercase font-semibold text-gray-400 block">Pemasok</span>
                <p class="text-sm font-bold text-gray-900">{{ $purchase->supplier->name ?? '-' }}</p>
                <p class="text-gray-600">Telepon: {{ $purchase->supplier->phone ?? '-' }}</p>
                @if($purchase->supplier->email)
                    <p class="text-gray-600">Email: {{ $purchase->supplier->email }}</p>
                @endif
                <p class="text-gray-500">Alamat: {{ $purchase->supplier->address ?: '-' }}</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-md border border-gray-200 space-y-1">
                <span class="text-[10px] uppercase font-semibold text-gray-400 block">Petugas Penerima</span>
                <p class="text-sm font-bold text-gray-900">{{ $purchase->user->name ?? '-' }}</p>
                <p class="text-gray-600">Role: {{ strtoupper($purchase->user->role ?? '-') }}</p>
                <p class="text-gray-600">Email: {{ $purchase->user->email ?? '-' }}</p>
                @if($purchase->notes)
                    <p class="text-gray-500 pt-1 border-t border-gray-200">Catatan: {{ $purchase->notes }}</p>
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
                        <th class="px-4 py-2.5 text-center">Jumlah Masuk</th>
                        <th class="px-4 py-2.5 text-right">Harga Beli Satuan</th>
                        <th class="px-4 py-2.5 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($purchase->items as $idx => $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-center text-gray-400">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-3">
                                    @if($item->product)
                                        <img src="{{ asset($item->product->image_path) }}" 
                                             alt="{{ $item->product->name }}" 
                                             class="w-10 h-10 object-cover rounded border border-gray-200 flex-shrink-0">
                                    @endif
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $item->product->name ?? 'Produk Dihapus' }}</div>
                                        <div class="text-xs text-gray-400 font-mono">{{ $item->product->sku ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center font-medium">{{ $item->quantity }} pcs</td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($item->unit_cost, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200 font-bold text-gray-900">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right uppercase text-xs">Total Biaya Pengadaan:</td>
                        <td class="px-4 py-3 text-right text-base text-blue-700">Rp {{ number_format($purchase->total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
