<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name', 'HijabSociety') }}</title>

    <!-- Google Font: Inter (Standard, clean, neutral) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Inter"', 'system-ui', '-apple-system', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="flex flex-col min-h-screen text-gray-800 bg-gray-50 font-sans">

    <!-- Navbar Standar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand Logo -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 text-rose-600 font-bold text-lg">
                        <i class="fa-solid fa-store text-xl"></i>
                        <span class="text-gray-900 font-bold">Hijab<span class="text-rose-600">Society</span></span>
                    </a>
                </div>

                <!-- Desktop Navigation Menu -->
                @auth
                <nav class="hidden md:flex items-center space-x-1 lg:space-x-2">
                    <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        <i class="fa-solid fa-chart-pie mr-1 text-xs text-gray-400"></i> Dashboard
                    </a>

                    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                        <!-- Admin & Staff Navigation -->
                        <a href="{{ route('products.index') }}" class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('products.*') ? 'bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            <i class="fa-solid fa-boxes-stacked mr-1 text-xs text-gray-400"></i> Koleksi Hijab
                        </a>
                        <a href="{{ route('categories.index') }}" class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('categories.*') ? 'bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            <i class="fa-solid fa-tags mr-1 text-xs text-gray-400"></i> Kategori
                        </a>
                        <a href="{{ route('suppliers.index') }}" class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('suppliers.*') ? 'bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            <i class="fa-solid fa-industry mr-1 text-xs text-gray-400"></i> Pemasok
                        </a>
                        <a href="{{ route('purchases.index') }}" class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('purchases.*') ? 'bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            <i class="fa-solid fa-truck-ramp-box mr-1 text-xs text-gray-400"></i> Stock-In
                        </a>
                        <a href="{{ route('sales.index') }}" class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('sales.*') ? 'bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            <i class="fa-solid fa-cash-register mr-1 text-xs text-gray-400"></i> Penjualan (POS)
                        </a>

                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('users.index') }}" class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('users.*') ? 'bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                <i class="fa-solid fa-users-gear mr-1 text-xs text-gray-400"></i> Kelola User
                            </a>
                        @endif
                    @else
                        <!-- Customer Navigation -->
                        <a href="{{ route('products.catalog') }}" class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('products.catalog') ? 'bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            <i class="fa-solid fa-store mr-1 text-xs text-gray-400"></i> Katalog Hijab
                        </a>
                        <a href="{{ route('sales.index') }}" class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('sales.*') ? 'bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            <i class="fa-solid fa-bag-shopping mr-1 text-xs text-gray-400"></i> Riwayat Pesanan Saya
                        </a>
                        <a href="{{ route('cart.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium {{ request()->routeIs('cart.*') ? 'bg-rose-700 text-white font-semibold' : 'bg-rose-600 hover:bg-rose-700 text-white' }} transition-colors">
                            <i class="fa-solid fa-cart-shopping mr-1.5 text-xs"></i> Keranjang
                            @if(count(session('cart', [])) > 0)
                                <span class="ml-1.5 px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-white text-rose-600 leading-none">
                                    {{ count(session('cart', [])) }}
                                </span>
                            @endif
                        </a>
                    @endif
                </nav>
                @endauth

                <!-- User Profile & Action -->
                <div class="flex items-center space-x-3">
                    @auth
                        <!-- Role Badge Standar -->
                        @if(auth()->user()->isAdmin())
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                                ADMIN
                            </span>
                        @elseif(auth()->user()->isStaff())
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                STAFF
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                CUSTOMER
                            </span>
                        @endif

                        <!-- User Info -->
                        <div class="hidden sm:flex flex-col text-right">
                            <span class="text-sm font-semibold text-gray-900 leading-tight">{{ auth()->user()->name }}</span>
                            <span class="text-xs text-gray-500">{{ auth()->user()->email }}</span>
                        </div>

                        <!-- Logout Button -->
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" title="Keluar" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 hover:text-red-600 transition-colors" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                                <i class="fa-solid fa-arrow-right-from-bracket mr-1"></i> Keluar
                            </button>
                        </form>
                    @else
                        @if(!request()->is('login*') && !request()->is('*/login') && !request()->is('register*'))
                            <a href="{{ route('login') }}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-md bg-rose-600 text-white hover:bg-rose-700 transition-colors">
                                <i class="fa-solid fa-arrow-right-to-bracket mr-1.5"></i> Masuk
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <!-- Flash Messages (Standar Bootstrap-like Alerts) -->
        @if(session('success'))
            <div class="mb-5 flex items-start p-3.5 rounded-md bg-green-50 border border-green-200 text-green-800 text-sm" role="alert">
                <i class="fa-solid fa-circle-check text-green-600 text-base mr-2 mt-0.5"></i>
                <div>
                    <span class="font-semibold">Berhasil:</span> {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-5 flex items-start p-3.5 rounded-md bg-red-50 border border-red-200 text-red-800 text-sm" role="alert">
                <i class="fa-solid fa-triangle-exclamation text-red-600 text-base mr-2 mt-0.5"></i>
                <div>
                    <span class="font-semibold">Perhatian:</span> {{ session('error') }}
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 p-3.5 rounded-md bg-red-50 border border-red-200 text-red-800 text-sm" role="alert">
                <div class="flex items-center mb-1 font-semibold">
                    <i class="fa-solid fa-circle-xmark text-red-600 mr-2"></i>
                    <span>Terdapat beberapa kesalahan pengisian form:</span>
                </div>
                <ul class="list-disc list-inside text-xs text-red-700 space-y-0.5 ml-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Yield View Content -->
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Minimalist Footer -->
    <footer class="mt-auto border-t border-gray-200 bg-white py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-gray-500">
            <div>
                <span class="font-semibold text-gray-700">HijabSociety</span> &mdash; Sistem Informasi Penjualan & Inventaris Hijab
            </div>
            <div>
                <span>Tugas UTS Web Programming &copy; {{ date('Y') }}</span>
            </div>
        </div>
    </footer>

</body>
</html>
