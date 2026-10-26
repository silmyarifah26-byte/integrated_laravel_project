@extends('layouts.app', ['title' => 'Dashboard Utama'])

@section('content')
<div class="space-y-8 py-2">

    <!-- Top Greeting Banner -->
    <div class="bg-gradient-to-r from-plush-600 via-rose-500 to-pink-500 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-plush-500/10 relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 text-white/10 text-9xl pointer-events-none">
            <i class="fa-solid fa-paw"></i>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                @if($user->isAdmin() || $user->isStaff())
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-white/20 backdrop-blur border border-white/30 text-white mb-2">
                        @if($user->isAdmin())
                            <i class="fa-solid fa-shield-halved mr-1.5"></i> ROLE: ADMINISTRATOR / OWNER
                        @else
                            <i class="fa-solid fa-user-gear mr-1.5"></i> ROLE: STAFF KASIR & INVENTORI
                        @endif
                    </div>
                @endif
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                    Halo, {{ $user->name }}! 👋
                </h1>
                <p class="text-white/90 text-sm mt-1 max-w-2xl">
                    @if($user->isAdmin())
                        Pantau performa penjualan harian, omzet bulanan, pengadaan stok supplier, serta amankan stok boneka melalui alert low-stock.
                    @elseif($user->isStaff())
                        Kelola alur transaksi Stock-In dari supplier, catat transaksi kasir toko harian, dan pantau ketersediaan stok boneka secara real-time.
                    @else
                        Selamat datang di Diza's Plushie! Temukan beragam boneka lucu, teddy bear klasik, dan plushie impor berkualitas premium untuk kado maupun koleksi terbaik Anda.
                    @endif
                </p>
            </div>

            <!-- Date & Quick Actions -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-3.5 py-2 rounded-2xl bg-white/15 backdrop-blur text-xs font-bold border border-white/20 text-white">
                    <i class="fa-regular fa-calendar-check mr-1.5"></i> {{ now()->translatedFormat('l, d F Y') }}
                </span>
                @if($user->isCustomer())
                    <a href="{{ route('sales.create') }}" class="px-4 py-2 rounded-2xl bg-white text-plush-700 hover:bg-rose-50 text-xs font-extrabold shadow-sm transition-all flex items-center">
                        <i class="fa-solid fa-cart-plus mr-1.5"></i> Buat Pesanan Baru
                    </a>
                @else
                    <a href="{{ route('sales.create') }}" class="px-4 py-2 rounded-2xl bg-white text-slate-800 hover:bg-slate-50 text-xs font-extrabold shadow-sm transition-all flex items-center">
                        <i class="fa-solid fa-cash-register mr-1.5 text-emerald-600"></i> Kasir (Stock-Out)
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- 1. ADMIN & STAFF DASHBOARD VIEW -->
    <!-- ===================================================================== -->
    @if($user->isAdmin() || $user->isStaff())

        <!-- Aggregate Stat Cards Grid (Slide 16 / Realtime Business Metrics) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">

            <!-- Card 1: Today's Sales -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Penjualan Hari Ini</span>
                    <span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-calendar-day"></i>
                    </span>
                </div>
                <div class="mt-3">
                    <p class="text-xl font-black text-slate-900 tracking-tight">Rp {{ number_format($todaySalesTotal, 0, ',', '.') }}</p>
                    <p class="text-xs text-emerald-600 font-bold mt-1 flex items-center">
                        <i class="fa-solid fa-receipt mr-1"></i> {{ $todaySalesCount }} Transaksi Hari Ini
                    </p>
                </div>
            </div>

            <!-- Card 2: This Month's Sales -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Penjualan Bulan Ini</span>
                    <span class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-wallet"></i>
                    </span>
                </div>
                <div class="mt-3">
                    <p class="text-xl font-black text-indigo-600 tracking-tight">Rp {{ number_format($monthSalesTotal, 0, ',', '.') }}</p>
                    <p class="text-xs text-indigo-500 font-bold mt-1 flex items-center">
                        <i class="fa-solid fa-chart-line mr-1"></i> {{ $monthSalesCount }} Transaksi Bulan Ini
                    </p>
                </div>
            </div>

            <!-- Card 3: Stock-In Purchases This Month -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pembelian Bulan Ini</span>
                    <span class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-cart-flatbed"></i>
                    </span>
                </div>
                <div class="mt-3">
                    <p class="text-xl font-black text-blue-600 tracking-tight">Rp {{ number_format($monthPurchasesTotal, 0, ',', '.') }}</p>
                    <p class="text-xs text-blue-500 font-semibold mt-1 flex items-center">
                        <i class="fa-solid fa-boxes-packing mr-1"></i> Stock-In Supplier
                    </p>
                </div>
            </div>

            <!-- Card 4: Low Stock Alert -->
            <div class="bg-white p-5 rounded-2xl border {{ $totalLowStock > 0 ? 'border-rose-300 bg-rose-50/20' : 'border-slate-200' }} shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-rose-500">Alert Stok Rendah</span>
                    <span class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-sm {{ $totalLowStock > 0 ? 'animate-pulse' : '' }}">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </span>
                </div>
                <div class="mt-3">
                    <p class="text-xl font-black text-rose-600 tracking-tight">{{ $totalLowStock }} <span class="text-xs font-semibold text-rose-400">Boneka</span></p>
                    <p class="text-xs text-rose-600 font-semibold mt-1">Stok &le; Batas Minimum</p>
                </div>
            </div>

            <!-- Card 5: Total Products -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Boneka</span>
                    <span class="w-9 h-9 rounded-xl bg-plush-100 text-plush-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </span>
                </div>
                <div class="mt-3">
                    <p class="text-xl font-black text-slate-900 tracking-tight">{{ $totalProducts }} <span class="text-xs font-semibold text-slate-500">Item</span></p>
                    <p class="text-xs text-slate-500 font-semibold mt-1">{{ $totalCategories }} Kategori Boneka</p>
                </div>
            </div>

            <!-- Card 6: Total Suppliers & Master Data -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Mitra Pemasok</span>
                    <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-industry"></i>
                    </span>
                </div>
                <div class="mt-3">
                    <p class="text-xl font-black text-slate-900 tracking-tight">{{ $totalSuppliers }} <span class="text-xs font-semibold text-slate-500">Supplier</span></p>
                    <p class="text-xs text-slate-500 font-semibold mt-1">
                        @if($totalUsers)
                            {{ $totalUsers }} Pengguna Sistem
                        @else
                            Pabrik & Distributor
                        @endif
                    </p>
                </div>
            </div>

        </div>

        <!-- ================================================================= -->
        <!-- Low-Stock Alert Table (Daftar Boneka Stok Rendah) -->
        <!-- ================================================================= -->
        <div class="bg-white rounded-3xl border {{ $lowStockProducts->count() > 0 ? 'border-rose-200' : 'border-slate-200' }} shadow-sm overflow-hidden">
            <div class="p-5 {{ $lowStockProducts->count() > 0 ? 'bg-rose-50/70 border-b border-rose-200' : 'bg-slate-50 border-b border-slate-200' }} flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center space-x-3">
                    <span class="w-10 h-10 rounded-2xl {{ $lowStockProducts->count() > 0 ? 'bg-rose-500 text-white' : 'bg-emerald-500 text-white' }} flex items-center justify-center text-base shadow-sm">
                        <i class="fa-solid {{ $lowStockProducts->count() > 0 ? 'fa-bell' : 'fa-circle-check' }}"></i>
                    </span>
                    <div>
                        <h2 class="text-base font-black text-slate-900">
                            Monitoring Peringatan Stok Rendah (Low-Stock Alert)
                        </h2>
                        <p class="text-xs {{ $lowStockProducts->count() > 0 ? 'text-rose-600' : 'text-slate-500' }} font-medium">
                            Daftar boneka dengan sisa stok fisik di bawah atau sama dengan batas minimum toko (stock &le; minimum_stock).
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold {{ $lowStockProducts->count() > 0 ? 'bg-rose-100 text-rose-700 border border-rose-300' : 'bg-emerald-100 text-emerald-800 border border-emerald-300' }}">
                        {{ $lowStockProducts->count() }} Boneka Perlu Restock
                    </span>
                    <a href="{{ route('purchases.create') }}" class="px-3.5 py-1.5 rounded-xl bg-plush-600 hover:bg-plush-700 text-white text-xs font-bold shadow-sm transition-all inline-flex items-center">
                        <i class="fa-solid fa-truck-ramp-box mr-1.5"></i> Form Pembelian (Stock-In)
                    </a>
                </div>
            </div>

            @if($lowStockProducts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm text-slate-600">
                        <thead class="bg-slate-50 text-[11px] uppercase font-extrabold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-3.5 w-12 text-center">No</th>
                                <th class="px-6 py-3.5">SKU & Nama Boneka</th>
                                <th class="px-6 py-3.5">Kategori</th>
                                <th class="px-6 py-3.5 text-center">Stok Saat Ini</th>
                                <th class="px-6 py-3.5 text-center">Batas Minimum</th>
                                <th class="px-6 py-3.5 text-right">Harga Jual</th>
                                <th class="px-6 py-3.5 text-center">Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($lowStockProducts as $index => $prod)
                                <tr class="hover:bg-rose-50/40 transition-colors">
                                    <td class="px-6 py-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-extrabold text-slate-900">{{ $prod->name }}</div>
                                        <div class="text-xs text-slate-400 font-mono">{{ $prod->sku }} &bull; {{ $prod->size ?? 'Ukuran Standar' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200/60">
                                            {{ $prod->category->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-700 border border-rose-300">
                                            <i class="fa-solid fa-triangle-exclamation mr-1 text-[10px]"></i> {{ $prod->stock }} pcs
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-slate-500">
                                        {{ $prod->minimum_stock }} pcs
                                    </td>
                                    <td class="px-6 py-4 text-right font-black text-slate-800">
                                        Rp {{ number_format($prod->selling_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('purchases.create', ['product_id' => $prod->id]) }}" class="inline-flex items-center px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs border border-blue-200 shadow-sm transition-all" title="Buka form Stock-In untuk restock boneka ini">
                                            <i class="fa-solid fa-cart-plus mr-1.5 text-blue-600"></i> Restock / Buat Purchase
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-slate-400 space-y-2">
                    <span class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 inline-flex items-center justify-center text-xl">
                        <i class="fa-solid fa-check-double"></i>
                    </span>
                    <p class="text-sm font-bold text-slate-700">Semua Stok Boneka Aman!</p>
                    <p class="text-xs text-slate-400">Seluruh produk boneka di gudang saat ini memiliki sisa stok di atas batas minimum stok.</p>
                </div>
            @endif
        </div>

        <!-- ================================================================= -->
        <!-- Recent Transactions Grid (5 Sales & 5 Purchases) -->
        <!-- ================================================================= -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- 5 Transaksi Penjualan Terakhir -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center space-x-2.5">
                            <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-receipt"></i>
                            </span>
                            <h3 class="font-extrabold text-slate-900 text-base">5 Transaksi Penjualan Terakhir</h3>
                        </div>
                        <a href="{{ route('sales.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 inline-flex items-center">
                            Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse($recentSales as $sale)
                            <div class="p-4 hover:bg-slate-50 transition-colors flex items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('sales.show', $sale) }}" class="text-sm font-extrabold text-slate-900 hover:text-emerald-600 font-mono transition-colors">
                                            {{ $sale->sale_number }}
                                        </a>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $sale->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                            {{ $sale->status }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1">
                                        Pembeli: <span class="font-semibold text-slate-700">{{ $sale->customer_name ?? ($sale->customer->name ?? 'Pelanggan Umum') }}</span> &bull;
                                        {{ \Carbon\Carbon::parse($sale->sold_at)->translatedFormat('d M, H:i') }}
                                    </p>
                                </div>
                                <div class="text-right flex flex-col items-end">
                                    <span class="text-sm font-black text-emerald-600">Rp {{ number_format($sale->total, 0, ',', '.') }}</span>
                                    <a href="{{ route('sales.show', $sale) }}" class="text-[11px] font-bold text-slate-400 hover:text-emerald-600 inline-flex items-center mt-1">
                                        Rincian Nota <i class="fa-solid fa-angle-right ml-1 text-[9px]"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-sm text-slate-400">Belum ada transaksi penjualan yang dicatat.</div>
                        @endforelse
                    </div>
                </div>

                <div class="p-3 bg-slate-50 border-t border-slate-100 text-center">
                    <a href="{{ route('sales.create') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 inline-flex items-center">
                        <i class="fa-solid fa-plus-circle mr-1.5"></i> Catat Transaksi Penjualan Baru (POS)
                    </a>
                </div>
            </div>

            <!-- 5 Transaksi Pembelian Terakhir -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center space-x-2.5">
                            <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-sm">
                                <i class="fa-solid fa-truck-ramp-box"></i>
                            </span>
                            <h3 class="font-extrabold text-slate-900 text-base">5 Transaksi Pembelian Terakhir</h3>
                        </div>
                        <a href="{{ route('purchases.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 inline-flex items-center">
                            Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse($recentPurchases as $purchase)
                            <div class="p-4 hover:bg-slate-50 transition-colors flex items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('purchases.show', $purchase) }}" class="text-sm font-extrabold text-slate-900 hover:text-blue-600 font-mono transition-colors">
                                            {{ $purchase->purchase_number }}
                                        </a>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $purchase->status === 'completed' ? 'bg-blue-100 text-blue-700' : 'bg-rose-100 text-rose-700' }}">
                                            {{ $purchase->status }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1">
                                        Supplier: <span class="font-semibold text-slate-700">{{ $purchase->supplier->name ?? '-' }}</span> &bull;
                                        {{ \Carbon\Carbon::parse($purchase->purchased_at)->translatedFormat('d M, H:i') }}
                                    </p>
                                </div>
                                <div class="text-right flex flex-col items-end">
                                    <span class="text-sm font-black text-blue-600">Rp {{ number_format($purchase->total, 0, ',', '.') }}</span>
                                    <a href="{{ route('purchases.show', $purchase) }}" class="text-[11px] font-bold text-slate-400 hover:text-blue-600 inline-flex items-center mt-1">
                                        Rincian PO <i class="fa-solid fa-angle-right ml-1 text-[9px]"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-sm text-slate-400">Belum ada transaksi pembelian pengadaan boneka.</div>
                        @endforelse
                    </div>
                </div>

                <div class="p-3 bg-slate-50 border-t border-slate-100 text-center">
                    <a href="{{ route('purchases.create') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 inline-flex items-center">
                        <i class="fa-solid fa-plus-circle mr-1.5"></i> Buat Pengadaan Pembelian Baru (Stock-In)
                    </a>
                </div>
            </div>

        </div>

    <!-- ===================================================================== -->
    <!-- 2. CUSTOMER DASHBOARD VIEW -->
    <!-- ===================================================================== -->
    @else

        <!-- Customer Summary Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <!-- Total Completed Orders -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-4">
                <div class="w-14 h-14 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-2xl shadow-sm">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pesanan Selesai</span>
                    <p class="text-2xl font-black text-slate-900 mt-0.5">{{ $myOrdersCount }} <span class="text-sm font-semibold text-slate-500">Pesanan</span></p>
                    <p class="text-xs text-rose-600 font-medium">Transaksi berhasil diproses</p>
                </div>
            </div>

            <!-- Total Spending -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-4">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl shadow-sm">
                    <i class="fa-solid fa-coins"></i>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Belanja Saya</span>
                    <p class="text-2xl font-black text-emerald-600 mt-0.5">Rp {{ number_format($myTotalSpent, 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-500 font-medium">Akumulasi pesanan lunas</p>
                </div>
            </div>

            <!-- Quick Action: Order Doll -->
            <div class="bg-gradient-to-br from-plush-50 to-pink-50 p-6 rounded-2xl border border-rose-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-rose-600">Ingin Boneka Baru?</span>
                    <h3 class="text-base font-extrabold text-slate-900 mt-0.5">Pesan Langsung di Sini</h3>
                    <p class="text-xs text-slate-500">Pilih boneka favorit Anda sekarang.</p>
                </div>
                <a href="{{ route('sales.create') }}" class="px-4 py-2.5 rounded-xl bg-plush-600 hover:bg-plush-700 text-white font-extrabold text-xs shadow-md shadow-plush-500/20 transition-all flex items-center">
                    <i class="fa-solid fa-cart-plus mr-1.5"></i> Buat Pesanan
                </a>
            </div>
        </div>

        <!-- Customer Recent Orders -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900 flex items-center">
                        <i class="fa-solid fa-clock-rotate-left mr-2 text-rose-500"></i> 5 Riwayat Pesanan Terbaru Saya
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar boneka yang baru saja Anda pesan di Diza's Plushie.</p>
                </div>
                <a href="{{ route('sales.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 inline-flex items-center">
                    Lihat Seluruh Riwayat Pesanan <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($myRecentOrders as $order)
                    <div class="p-5 hover:bg-slate-50 transition-colors">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('sales.show', $order) }}" class="font-extrabold text-slate-900 hover:text-rose-600 font-mono transition-colors">
                                    {{ $order->sale_number }}
                                </a>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $order->status }}
                                </span>
                            </div>
                            <span class="text-xs text-slate-400 font-medium">
                                <i class="fa-regular fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($order->sold_at)->translatedFormat('d F Y, H:i') }} WIB
                            </span>
                        </div>

                        <!-- Item Badges -->
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            @foreach($order->items as $it)
                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-700 font-medium border border-slate-200">
                                    {{ $it->product->name ?? 'Boneka' }} &times; <strong>{{ $it->quantity }}</strong>
                                </span>
                            @endforeach
                        </div>

                        <div class="mt-3 flex items-center justify-between pt-2 border-t border-slate-100">
                            <a href="{{ route('sales.show', $order) }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 inline-flex items-center">
                                <i class="fa-solid fa-receipt mr-1"></i> Lihat Faktur Nota
                            </a>
                            <div>
                                <span class="text-xs text-slate-400">Total Belanja:</span>
                                <span class="text-base font-black text-rose-600 ml-1">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400">
                        <i class="fa-solid fa-basket-shopping text-4xl mb-3 text-slate-300"></i>
                        <p class="text-sm font-semibold">Anda belum memiliki riwayat pesanan.</p>
                        <p class="text-xs text-slate-400 mt-1">Jelajahi koleksi boneka kami dan buat pesanan pertama Anda!</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Featured Products / Catalog Preview for Customer -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900 flex items-center">
                        <i class="fa-solid fa-heart mr-2 text-pink-500"></i> Rekomendasi Boneka Tersedia &bull; Diza's Plushie
                    </h2>
                    <p class="text-xs text-slate-500">Pilihan boneka lucu dan empuk berstandar kualitas terbaik yang siap dipesan.</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('products.catalog') }}" class="text-xs font-bold text-pink-600 hover:text-pink-700 inline-flex items-center">
                        Buka Katalog Lengkap <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredProducts as $item)
                    <div class="bg-white rounded-3xl border border-pink-100 p-4 sm:p-5 shadow-sm hover:shadow-md hover:border-pink-200 transition-all flex flex-col justify-between group">
                        <div>
                            <!-- Real Plushie Photo -->
                            <div class="w-full h-52 rounded-2xl overflow-hidden mb-3 relative group-hover:scale-[1.02] transition-transform duration-200 bg-slate-100 border border-pink-100">
                                <img src="{{ asset($item->image_url ?? 'images/plushies/teddy-bear-pita.jpg') }}" 
                                     alt="{{ $item->name }}" 
                                     class="object-cover object-center w-full h-52 rounded-2xl">
                                
                                <div class="absolute top-2 left-2">
                                    <span class="text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full bg-white/90 text-pink-600 border border-pink-200 shadow-xs">
                                        {{ $item->category->name ?? 'Boneka' }}
                                    </span>
                                </div>
                                <div class="absolute top-2 right-2">
                                    <span class="text-[10px] font-bold text-slate-400 font-mono bg-white/80 px-2 py-0.5 rounded-full border border-slate-100">
                                        {{ $item->sku }}
                                    </span>
                                </div>
                            </div>

                            <h3 class="font-extrabold text-slate-900 text-base line-clamp-1 group-hover:text-pink-600 transition-colors">{{ $item->name }}</h3>
                            <p class="text-xs text-slate-500 mt-1">Ukuran: <strong class="text-slate-700">{{ $item->size ?? 'Standar' }}</strong></p>
                            <p class="text-xs text-slate-400 line-clamp-1">Bahan: {{ $item->material ?? 'Kain Plush Halus' }}</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-pink-50 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">Harga</span>
                                <span class="text-base font-black text-pink-600">Rp {{ number_format($item->selling_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full border border-emerald-200">
                                    Stok: {{ $item->stock }}
                                </span>
                                <a href="{{ route('sales.create', ['product_id' => $item->id]) }}" class="px-3 py-1.5 rounded-xl bg-pink-500 hover:bg-pink-600 text-white text-xs font-bold shadow-sm transition-all flex items-center" title="Pesan boneka ini">
                                    <i class="fa-solid fa-cart-shopping mr-1"></i> Pesan
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    @endif

</div>
@endsection
