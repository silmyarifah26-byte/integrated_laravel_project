@extends('layouts.app', ['title' => 'Keranjang Belanja'])

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-cart-shopping text-rose-600 text-lg"></i>
                <h1 class="text-xl font-bold text-gray-900">Keranjang Belanja</h1>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                Periksa kembali pilihan variasi warna, ukuran, dan jumlah hijab sebelum melakukan konfirmasi checkout.
            </p>
        </div>

        <div>
            <a href="{{ route('products.catalog') }}" class="inline-flex items-center px-3 py-1.5 rounded-md border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-xs font-medium transition-colors">
                <i class="fa-solid fa-arrow-left mr-1.5 text-gray-400"></i> Lanjut Belanja di Katalog
            </a>
        </div>
    </div>

    @if(empty($cart))
        <!-- Keranjang Kosong State -->
        <div class="bg-white border border-gray-200 rounded-lg p-10 shadow-sm text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-rose-50 rounded-full flex items-center justify-center text-rose-500">
                <i class="fa-solid fa-basket-shopping text-2xl"></i>
            </div>
            <h2 class="text-base font-bold text-gray-900">Keranjang Belanja Anda Masih Kosong</h2>
            <p class="text-xs text-gray-500 max-w-md mx-auto mt-1 mb-5">
                Anda belum memilih produk hijab atau variasi apa pun. Silakan kunjungi katalog untuk menemukan pashmina ceruty, segi empat voal, dan khimar favorit Anda.
            </p>
            <a href="{{ route('products.catalog') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-rose-600 hover:bg-rose-700 text-white text-xs font-medium transition-colors">
                <i class="fa-solid fa-store mr-1.5"></i> Buka Katalog Hijab Sekarang
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Daftar Item Keranjang (Col 8) -->
            <div class="lg:col-span-8 space-y-4">
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-700">
                            Daftar Pilihan Hijab ({{ count($cart) }} Baris Item)
                        </span>

                        <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengosongkan seluruh keranjang belanja?');">
                            @csrf
                            <button type="submit" class="text-xs text-red-600 hover:text-red-700 font-medium inline-flex items-center">
                                <i class="fa-solid fa-trash-can mr-1 text-[11px]"></i> Kosongkan Keranjang
                            </button>
                        </form>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @foreach($cart as $key => $item)
                            <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-gray-50/50 transition-colors">
                                
                                <!-- Foto dan Detail Hijab -->
                                <div class="flex items-start space-x-3">
                                    <div class="w-16 h-16 rounded-md overflow-hidden bg-gray-100 border border-gray-200 flex-shrink-0">
                                        <img src="{{ asset($item['image_path'] ?? 'images/hijab/pashmina-ceruty.jpg') }}" 
                                             alt="{{ $item['name'] }}" 
                                             class="w-full h-full object-cover">
                                    </div>

                                    <div>
                                        <span class="text-[10px] font-semibold text-rose-600 uppercase">{{ $item['category_name'] ?? 'Hijab' }}</span>
                                        <h2 class="text-sm font-semibold text-gray-900 leading-snug">{{ $item['name'] }}</h2>
                                        <span class="text-[11px] font-mono text-gray-400 block">{{ $item['sku'] }}</span>

                                        <!-- Badge Pilihan Variasi -->
                                        <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                                <i class="fa-solid fa-palette text-[10px] mr-1"></i> {{ $item['color'] ?? '-' }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                                <i class="fa-solid fa-ruler-combined text-[10px] mr-1"></i> {{ $item['size'] ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kontrol Kuantitas, Subtotal, dan Hapus -->
                                <div class="flex items-center justify-between sm:justify-end sm:space-x-6 pt-3 sm:pt-0 border-t sm:border-t-0 border-gray-100">
                                    <!-- Form Edit Kuantitas -->
                                    <div>
                                        <span class="text-[10px] uppercase text-gray-400 block mb-1">Jumlah</span>
                                        <form action="{{ route('cart.update', $key) }}" method="POST" class="flex items-center space-x-1">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" 
                                                   name="quantity" 
                                                   value="{{ $item['quantity'] }}" 
                                                   min="1" 
                                                   max="{{ $item['current_stock'] ?? 999 }}" 
                                                   class="w-16 px-2 py-1 text-xs border border-gray-300 rounded-md text-center font-medium focus:ring-1 focus:ring-rose-500 focus:outline-none">
                                            <button type="submit" 
                                                    title="Perbarui kuantitas" 
                                                    class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs rounded-md border border-gray-300 transition-colors">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                        <span class="text-[10px] text-gray-400 block mt-0.5">Stok: {{ $item['current_stock'] ?? '-' }}</span>
                                    </div>

                                    <!-- Subtotal -->
                                    <div class="text-right">
                                        <span class="text-[10px] uppercase text-gray-400 block mb-0.5">Subtotal</span>
                                        <div class="text-sm font-bold text-gray-900">
                                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                        </div>
                                        <span class="text-[10px] text-gray-400">@ Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                    </div>

                                    <!-- Tombol Hapus -->
                                    <div>
                                        <form action="{{ route('cart.remove', $key) }}" method="POST" onsubmit="return confirm('Hapus hijab variasi ini dari keranjang belanja?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    title="Hapus dari keranjang" 
                                                    class="text-gray-400 hover:text-red-600 p-1.5 transition-colors">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Ringkasan Pesanan & Checkout Form (Col 4) -->
            <div class="lg:col-span-4 space-y-4">
                <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold text-gray-900 pb-2 border-b border-gray-100">
                        Ringkasan Pesanan
                    </h2>

                    <div class="space-y-2 text-xs text-gray-600">
                        <div class="flex items-center justify-between">
                            <span>Total Kuantitas Hijab:</span>
                            <span class="font-semibold text-gray-900">{{ $totalQuantity }} pcs</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Subtotal Belanja:</span>
                            <span class="font-semibold text-gray-900">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Ongkos Kirim / Pickup:</span>
                            <span class="font-semibold text-green-600">GRATIS</span>
                        </div>
                        <div class="pt-2 border-t border-gray-200 flex items-center justify-between text-sm font-bold text-gray-900">
                            <span>Total Tagihan:</span>
                            <span class="text-base text-rose-600">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Form Konfirmasi Checkout -->
                    <form action="{{ route('cart.checkout') }}" method="POST" class="pt-3 border-t border-gray-100 space-y-3">
                        @csrf

                        <!-- Metode Pembayaran -->
                        <div>
                            <label for="payment_method" class="block text-xs font-semibold text-gray-700 mb-1">
                                Metode Pembayaran <span class="text-red-500">*</span>
                            </label>
                            <select name="payment_method" id="payment_method" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-xs text-gray-900 focus:outline-none focus:ring-1 focus:ring-rose-500">
                                <option value="QRIS" selected>QRIS (BCA, Mandiri, Gopay, OVO, ShopeePay)</option>
                                <option value="Transfer Bank">Transfer Bank Virtual Account</option>
                                <option value="E-Wallet">E-Wallet (Dana / LinkAja)</option>
                                <option value="Tunai / COD">Tunai / COD di Toko</option>
                            </select>
                        </div>

                        <!-- Catatan Pesanan -->
                        <div>
                            <label for="notes" class="block text-xs font-semibold text-gray-700 mb-1">
                                Catatan Pesanan (Opsional)
                            </label>
                            <textarea name="notes" id="notes" rows="2" placeholder="Contoh: Tolong packing rapi dengan box kado..." class="w-full px-3 py-2 border border-gray-300 rounded-md text-xs text-gray-900 focus:outline-none focus:ring-1 focus:ring-rose-500"></textarea>
                        </div>

                        <!-- Tombol Checkout -->
                        <button type="submit" class="w-full py-2.5 px-4 rounded-md bg-rose-600 hover:bg-rose-700 text-white font-medium text-xs transition-colors flex items-center justify-center">
                            <i class="fa-solid fa-lock mr-1.5"></i> Proses Checkout Pesanan
                        </button>
                    </form>

                    <div class="text-[11px] text-gray-400 text-center pt-2">
                        <i class="fa-solid fa-shield-halved mr-1 text-green-500"></i> Transaksi terenkripsi aman & stok otomatis terpotong saat pesanan dikonfirmasi.
                    </div>
                </div>
            </div>

        </div>
    @endif

</div>
@endsection
