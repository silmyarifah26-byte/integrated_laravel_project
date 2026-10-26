@extends('layouts.app', ['title' => 'Dashboard Utama'])

@section('content')
<div class="space-y-6">

    <!-- Header Sambutan Standar -->
    <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            @if($user->isAdmin())
                <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200 mb-1">
                    ROLE: ADMINISTRATOR / OWNER
                </span>
            @elseif($user->isStaff())
                <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200 mb-1">
                    ROLE: STAFF KASIR
                </span>
            @endif

            <h1 class="text-xl font-bold text-gray-900">
                Halo, {{ $user->name }}
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                @if($user->isAdmin())
                    Pantau ringkasan penjualan harian, pembelian stok dari supplier, dan status inventori toko.
                @elseif($user->isStaff())
                    Kelola transaksi kasir toko dan catat penerimaan stok masuk (Stock-In) dari supplier.
                @else
                    Selamat datang di toko HijabSociety. Jelajahi katalog pashmina, voal, dan khimar pilihan Anda.
                @endif
            </p>
        </div>

        <div class="flex items-center space-x-2 text-xs">
            <span class="px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-md text-gray-600 font-medium">
                <i class="fa-regular fa-calendar mr-1 text-gray-400"></i> {{ now()->translatedFormat('l, d F Y') }}
            </span>
            @if($user->isCustomer())
                <a href="{{ route('sales.create') }}" class="px-3.5 py-1.5 rounded-md bg-rose-600 hover:bg-rose-700 text-white font-medium transition-colors flex items-center">
                    <i class="fa-solid fa-cart-plus mr-1"></i> Buat Pesanan
                </a>
            @else
                <a href="{{ route('sales.create') }}" class="px-3.5 py-1.5 rounded-md bg-gray-900 hover:bg-gray-800 text-white font-medium transition-colors flex items-center">
                    <i class="fa-solid fa-cash-register mr-1"></i> Kasir (Stock-Out)
                </a>
            @endif
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- 1. ADMIN & STAFF DASHBOARD -->
    <!-- ===================================================================== -->
    @if($user->isAdmin() || $user->isStaff())

        <!-- Ringkasan Statistik Bisnis -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">

            <!-- Card 1: Penjualan Hari Ini -->
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                <span class="text-xs font-medium uppercase tracking-wider text-gray-500">Penjualan Hari Ini</span>
                <p class="text-lg font-bold text-gray-900 mt-1">Rp {{ number_format($todaySalesTotal, 0, ',', '.') }}</p>
                <p class="text-xs text-green-600 font-medium mt-1">
                    <i class="fa-solid fa-check mr-1"></i> {{ $todaySalesCount }} Transaksi Hari Ini
                </p>
            </div>

            <!-- Card 2: Penjualan Bulan Ini -->
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                <span class="text-xs font-medium uppercase tracking-wider text-gray-500">Penjualan Bulan Ini</span>
                <p class="text-lg font-bold text-gray-900 mt-1">Rp {{ number_format($monthSalesTotal, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">
                    {{ $monthSalesCount }} Transaksi Bulan Ini
                </p>
            </div>

            <!-- Card 3: Pembelian Bulan Ini -->
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                <span class="text-xs font-medium uppercase tracking-wider text-gray-500">Pembelian Bulan Ini</span>
                <p class="text-lg font-bold text-gray-900 mt-1">Rp {{ number_format($monthPurchasesTotal, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">
                    Stock-In Supplier
                </p>
            </div>

            <!-- Card 4: Alert Stok Rendah -->
            <div class="bg-white p-4 rounded-lg border {{ $totalLowStock > 0 ? 'border-red-300 bg-red-50/20' : 'border-gray-200' }} shadow-sm">
                <span class="text-xs font-medium uppercase tracking-wider {{ $totalLowStock > 0 ? 'text-red-600' : 'text-gray-500' }}">Alert Stok Rendah</span>
                <p class="text-lg font-bold {{ $totalLowStock > 0 ? 'text-red-600' : 'text-gray-900' }} mt-1">{{ $totalLowStock }} <span class="text-xs font-normal text-gray-500">Item</span></p>
                <p class="text-xs {{ $totalLowStock > 0 ? 'text-red-500' : 'text-gray-400' }} font-medium mt-1">Stok &le; Batas Minimum</p>
            </div>

            <!-- Card 5: Total Produk Aktif -->
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                <span class="text-xs font-medium uppercase tracking-wider text-gray-500">Total Produk Aktif</span>
                <p class="text-lg font-bold text-gray-900 mt-1">{{ $totalProducts }} <span class="text-xs font-normal text-gray-500">Item</span></p>
                <p class="text-xs text-gray-500 font-medium mt-1">{{ $totalCategories }} Kategori Hijab</p>
            </div>

            <!-- Card 6: Total Suppliers -->
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                <span class="text-xs font-medium uppercase tracking-wider text-gray-500">Mitra Pemasok</span>
                <p class="text-lg font-bold text-gray-900 mt-1">{{ $totalSuppliers }} <span class="text-xs font-normal text-gray-500">Supplier</span></p>
                <p class="text-xs text-gray-500 font-medium mt-1">
                    @if($totalUsers)
                        {{ $totalUsers }} User Sistem
                    @else
                        Pemasok Terdaftar
                    @endif
                </p>
            </div>

        </div>

        <!-- Tabel Monitoring Stok Rendah -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 bg-gray-50 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold text-gray-900">
                        Monitoring Peringatan Stok Rendah
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Daftar produk dengan jumlah fisik kurang dari atau sama dengan batas minimum stok toko.
                    </p>
                </div>

                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-1 rounded text-xs font-semibold {{ $lowStockProducts->count() > 0 ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-green-100 text-green-800 border border-green-200' }}">
                        {{ $lowStockProducts->count() }} Item Rendah
                    </span>
                    <a href="{{ route('purchases.create') }}" class="px-3 py-1 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium transition-colors">
                        <i class="fa-solid fa-plus mr-1"></i> Restock / Buat Purchase
                    </a>
                </div>
            </div>

            @if($lowStockProducts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm text-gray-700">
                        <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-600 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 w-12 text-center">No</th>
                                <th class="px-4 py-3">SKU & Nama Hijab</th>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3 text-center">Stok Saat Ini</th>
                                <th class="px-4 py-3 text-center">Batas Minimum</th>
                                <th class="px-4 py-3 text-right">Harga Jual</th>
                                <th class="px-4 py-3 text-center">Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($lowStockProducts as $index => $prod)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-center font-medium text-gray-400">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-gray-900">{{ $prod->name }}</div>
                                        <div class="text-xs text-gray-500 font-mono">{{ $prod->sku }} &bull; {{ $prod->size ?? 'Standar' }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $prod->category->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-800">
                                            {{ $prod->stock }} pcs
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-medium text-gray-600">
                                        {{ $prod->minimum_stock }} pcs
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-900">
                                        Rp {{ number_format($prod->selling_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('purchases.create', ['product_id' => $prod->id]) }}" class="inline-flex items-center px-2.5 py-1 rounded-md bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-medium border border-blue-200 transition-colors">
                                            <i class="fa-solid fa-cart-plus mr-1"></i> Restock / Buat Purchase
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-6 text-center text-gray-500 text-sm">
                    <i class="fa-solid fa-circle-check text-green-500 text-xl mb-1"></i>
                    <p class="font-medium text-gray-700">Semua Stok Hijab Aman</p>
                    <p class="text-xs text-gray-400 mt-0.5">Seluruh stok fisik produk berada di atas batas minimum toko.</p>
                </div>
            @endif
        </div>

        <!-- Tabel Riwayat 5 Transaksi Terakhir (Sales & Purchases) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- 5 Transaksi Penjualan Terakhir -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900">
                        5 Transaksi Penjualan Terakhir
                    </h3>
                    <a href="{{ route('sales.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-gray-700">
                        <thead class="bg-gray-50 uppercase text-[11px] font-semibold text-gray-500 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-2.5">No Faktur</th>
                                <th class="px-4 py-2.5">Pelanggan</th>
                                <th class="px-4 py-2.5 text-right">Total</th>
                                <th class="px-4 py-2.5 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentSales as $sale)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-mono font-medium">
                                        <a href="{{ route('sales.show', $sale) }}" class="text-blue-600 hover:underline">
                                            {{ $sale->sale_number }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3">{{ $sale->customer_name }}</td>
                                    <td class="px-4 py-3 text-right font-medium">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $sale->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $sale->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-gray-400">Belum ada transaksi penjualan tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 5 Transaksi Pembelian Terakhir -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900">
                        5 Transaksi Pembelian Terakhir
                    </h3>
                    <a href="{{ route('purchases.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-gray-700">
                        <thead class="bg-gray-50 uppercase text-[11px] font-semibold text-gray-500 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-2.5">No PO</th>
                                <th class="px-4 py-2.5">Supplier</th>
                                <th class="px-4 py-2.5 text-right">Total Biaya</th>
                                <th class="px-4 py-2.5 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentPurchases as $purchase)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-mono font-medium">
                                        <a href="{{ route('purchases.show', $purchase) }}" class="text-blue-600 hover:underline">
                                            {{ $purchase->purchase_number }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3">{{ $purchase->supplier->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-right font-medium">Rp {{ number_format($purchase->total, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $purchase->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $purchase->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-gray-400">Belum ada transaksi pembelian tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    <!-- ===================================================================== -->
    <!-- 2. CUSTOMER DASHBOARD VIEW -->
    <!-- ===================================================================== -->
    @else

        <!-- Kartu Ringkasan Akun Customer -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            
            <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Pesanan Selesai</span>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $myOrdersCount }} <span class="text-xs font-normal text-gray-500">Pesanan</span></p>
                <p class="text-xs text-gray-500 mt-1">Total riwayat pesanan Anda</p>
            </div>

            <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Belanja Saya</span>
                <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($myTotalSpent, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 mt-1">Akumulasi transaksi berhasil</p>
            </div>

            <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Pemesanan Online</span>
                    <p class="text-sm font-semibold text-gray-900 mt-1">Ingin menambah koleksi hijab?</p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('sales.create') }}" class="inline-flex items-center px-3.5 py-1.5 rounded-md bg-rose-600 hover:bg-rose-700 text-white text-xs font-medium transition-colors">
                        <i class="fa-solid fa-cart-plus mr-1.5"></i> Buat Pesanan
                    </a>
                </div>
            </div>

        </div>

        <!-- 5 Riwayat Pesanan Terbaru Saya -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-gray-900">
                        5 Riwayat Pesanan Terbaru Saya
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">Daftar pesanan hijab Anda yang tercatat di sistem.</p>
                </div>
                <a href="{{ route('sales.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                    Lihat Semua Riwayat &rarr;
                </a>
            </div>

            <div class="divide-y divide-gray-200">
                @forelse($myRecentOrders as $order)
                    <div class="p-4 hover:bg-gray-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('sales.show', $order) }}" class="font-mono font-semibold text-blue-600 hover:underline text-sm">
                                    {{ $order->sale_number }}
                                </a>
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $order->status }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ \Carbon\Carbon::parse($order->sold_at)->translatedFormat('d F Y, H:i') }} WIB &bull;
                                @foreach($order->items as $it)
                                    <span>{{ $it->product->name ?? 'Hijab' }} ({{ $it->quantity }}x)</span>@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <span class="text-sm font-bold text-gray-900">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                            <a href="{{ route('sales.show', $order) }}" class="px-2.5 py-1 text-xs border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 transition-colors">
                                Faktur
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-400 text-sm">
                        Anda belum memiliki riwayat pesanan.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Rekomendasi Hijab Pilihan -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900">
                        Rekomendasi Hijab Pilihan
                    </h2>
                    <p class="text-xs text-gray-500">Koleksi hijab favorit dengan stok siap kirim.</p>
                </div>
                <a href="{{ route('products.catalog') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                    Lihat Seluruh Katalog &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($featuredProducts as $item)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm flex flex-col justify-between">
                        <div>
                            <!-- Foto Produk -->
                            <div class="w-full h-48 rounded-md overflow-hidden bg-gray-100 border border-gray-200 mb-3">
                                <img src="{{ asset($item->image_path) }}" 
                                     alt="{{ $item->name }}" 
                                     class="object-cover object-center w-full h-48">
                            </div>

                            <span class="text-[11px] font-medium text-gray-500 uppercase">{{ $item->category->name ?? 'Hijab' }}</span>
                            <h3 class="font-semibold text-gray-900 text-sm mt-0.5 line-clamp-1">{{ $item->name }}</h3>
                            <p class="text-xs text-gray-500 mt-1">Bahan: {{ $item->material ?? 'Premium' }}</p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <span class="text-sm font-bold text-rose-600">Rp {{ number_format($item->selling_price, 0, ',', '.') }}</span>
                                </div>
                                <span class="text-[11px] px-2 py-0.5 rounded font-medium {{ $item->stock > 0 ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700' }}">
                                    Stok: {{ $item->stock }}
                                </span>
                            </div>

                            <!-- Form Pemilihan Variasi & Add to Cart -->
                            <form action="{{ route('cart.add') }}" method="POST" class="space-y-2">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item->id }}">

                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[10px] font-semibold text-gray-600 mb-0.5">Warna:</label>
                                        <select name="color" class="w-full text-xs px-2 py-1 border border-gray-300 rounded-md bg-white text-gray-800 focus:outline-none focus:ring-1 focus:ring-rose-500">
                                            @foreach($item->available_colors as $color)
                                                <option value="{{ $color }}">{{ $color }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-gray-600 mb-0.5">Ukuran:</label>
                                        <select name="size" class="w-full text-xs px-2 py-1 border border-gray-300 rounded-md bg-white text-gray-800 focus:outline-none focus:ring-1 focus:ring-rose-500">
                                            @foreach($item->available_sizes as $sizeOption)
                                                <option value="{{ $sizeOption }}">{{ $sizeOption }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2 pt-1">
                                    <input type="number" 
                                           name="quantity" 
                                           value="1" 
                                           min="1" 
                                           max="{{ $item->stock > 0 ? $item->stock : 1 }}" 
                                           {{ $item->stock < 1 ? 'disabled' : '' }}
                                           class="w-14 text-xs px-2 py-1.5 border border-gray-300 rounded-md text-center font-medium focus:outline-none focus:ring-1 focus:ring-rose-500">
                                    <button type="submit" 
                                            {{ $item->stock < 1 ? 'disabled' : '' }}
                                            class="flex-1 py-1.5 px-3 rounded-md {{ $item->stock > 0 ? 'bg-rose-600 hover:bg-rose-700 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed' }} text-xs font-medium transition-colors flex items-center justify-center">
                                        <i class="fa-solid fa-cart-plus mr-1.5"></i>
                                        {{ $item->stock > 0 ? 'Masukkan ke Keranjang' : 'Habis' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    @endif

</div>
@endsection
