<!DOCTYPE html>
<html lang="id" class="h-full bg-[#FFF9FB]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name', "Diza's Plushie") }}</title>

    <!-- Google Fonts: Plus Jakarta Sans & Quicksand -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Quicksand:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Quicksand"', '"Plus Jakarta Sans"', 'sans-serif'],
                        heading: ['"Quicksand"', 'sans-serif'],
                    },
                    colors: {
                        plush: {
                            50: '#fff5f7',
                            100: '#ffe4e8',
                            200: '#fecdd6',
                            300: '#fda4b5',
                            400: '#fb718d',
                            500: '#f43f68',
                            600: '#e11d48',
                            700: '#be123c',
                            800: '#9f1239',
                            900: '#881337',
                        },
                        pastel: {
                            pink: '#FBCFE8',
                            rose: '#F472B6',
                            lavender: '#E9D5FF',
                            mint: '#D1FAE5',
                            peach: '#FFEDD5',
                            cream: '#FFF9FB',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Quicksand', 'Plus Jakarta Sans', sans-serif;
            background-color: #FFF9FB;
        }
    </style>
</head>
<body class="flex flex-col min-h-full text-slate-800 bg-[#FFF9FB]">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-pink-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand Logo -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 text-rose-500 font-extrabold text-xl tracking-tight group">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-pink-100 text-rose-500 group-hover:scale-105 transition-transform duration-200 shadow-sm">
                            <i class="fa-solid fa-paw text-xl"></i>
                        </span>
                        <span class="font-black">Diza's <span class="text-slate-800">Plushie</span></span>
                    </a>
                </div>

                <!-- Desktop Navigation Menu -->
                @auth
                <nav class="hidden md:flex items-center space-x-1 lg:space-x-2">
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-xl text-sm font-bold transition-colors {{ request()->routeIs('dashboard') ? 'bg-pink-100/70 text-rose-700' : 'text-slate-600 hover:text-rose-600 hover:bg-pink-50/50' }}">
                        <i class="fa-solid fa-chart-pie mr-1.5 text-xs"></i> Dashboard
                    </a>

                    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                        <!-- Admin & Staff Navigation -->
                        <a href="{{ route('products.index') }}" class="px-3 py-2 rounded-xl text-sm font-bold transition-colors {{ request()->routeIs('products.*') ? 'bg-pink-100/70 text-rose-700' : 'text-slate-600 hover:text-rose-600 hover:bg-pink-50/50' }}">
                            <i class="fa-solid fa-boxes-stacked mr-1.5 text-xs"></i> Koleksi Boneka
                        </a>
                        <a href="{{ route('categories.index') }}" class="px-3 py-2 rounded-xl text-sm font-bold transition-colors {{ request()->routeIs('categories.*') ? 'bg-pink-100/70 text-rose-700' : 'text-slate-600 hover:text-rose-600 hover:bg-pink-50/50' }}">
                            <i class="fa-solid fa-tags mr-1.5 text-xs"></i> Kategori
                        </a>
                        <a href="{{ route('suppliers.index') }}" class="px-3 py-2 rounded-xl text-sm font-bold transition-colors {{ request()->routeIs('suppliers.*') ? 'bg-amber-100/70 text-amber-800' : 'text-slate-600 hover:text-amber-700 hover:bg-amber-50/50' }}">
                            <i class="fa-solid fa-industry mr-1.5 text-xs"></i> Pemasok
                        </a>
                        <a href="{{ route('purchases.index') }}" class="px-3 py-2 rounded-xl text-sm font-bold transition-colors {{ request()->routeIs('purchases.*') ? 'bg-emerald-100/70 text-emerald-800' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/50' }}">
                            <i class="fa-solid fa-truck-ramp-box mr-1.5 text-xs"></i> Stock-In
                        </a>
                        <a href="{{ route('sales.index') }}" class="px-3 py-2 rounded-xl text-sm font-bold transition-colors {{ request()->routeIs('sales.*') ? 'bg-rose-100/70 text-rose-800' : 'text-slate-600 hover:text-rose-700 hover:bg-pink-50/50' }}">
                            <i class="fa-solid fa-cash-register mr-1.5 text-xs"></i> Penjualan (POS)
                        </a>

                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('users.index') }}" class="px-3 py-2 rounded-xl text-sm font-bold transition-colors {{ request()->routeIs('users.*') ? 'bg-purple-100/70 text-purple-800' : 'text-slate-600 hover:text-purple-700 hover:bg-purple-50/50' }}">
                                <i class="fa-solid fa-users-gear mr-1.5 text-xs"></i> Kelola User
                            </a>
                        @endif
                    @else
                        <!-- Customer Navigation -->
                        <a href="{{ route('products.catalog') }}" class="px-3.5 py-2 rounded-xl text-sm font-bold transition-colors {{ request()->routeIs('products.catalog') ? 'bg-pink-100/80 text-rose-700' : 'text-slate-600 hover:text-rose-600 hover:bg-pink-50/50' }}">
                            <i class="fa-solid fa-store mr-1.5 text-xs"></i> Katalog Boneka
                        </a>
                        <a href="{{ route('sales.index') }}" class="px-3.5 py-2 rounded-xl text-sm font-bold transition-colors {{ request()->routeIs('sales.*') ? 'bg-emerald-100/80 text-emerald-800' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/50' }}">
                            <i class="fa-solid fa-bag-shopping mr-1.5 text-xs"></i> Riwayat Pesanan Saya
                        </a>
                        <a href="{{ route('sales.create') }}" class="px-3.5 py-2 rounded-xl text-sm font-bold bg-rose-500 hover:bg-rose-600 text-white shadow-sm transition-all">
                            <i class="fa-solid fa-cart-plus mr-1.5 text-xs"></i> Pesan Boneka
                        </a>
                    @endif
                </nav>
                @endauth

                <!-- User Profile & Action -->
                <div class="flex items-center space-x-3">
                    @auth
                        <!-- Role Badge -->
                        @if(auth()->user()->isAdmin())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-purple-100 text-purple-800 border border-purple-200 shadow-sm">
                                <i class="fa-solid fa-crown mr-1.5 text-purple-600"></i> ADMIN
                            </span>
                        @elseif(auth()->user()->isStaff())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-800 border border-amber-200 shadow-sm">
                                <i class="fa-solid fa-user-gear mr-1.5 text-amber-600"></i> STAFF
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-pink-100 text-rose-700 border border-pink-200 shadow-sm">
                                <i class="fa-solid fa-heart mr-1.5 text-rose-500"></i> CUSTOMER
                            </span>
                        @endif

                        <!-- User Info -->
                        <div class="hidden sm:flex flex-col text-right">
                            <span class="text-sm font-black text-slate-800 leading-tight">{{ auth()->user()->name }}</span>
                            <span class="text-xs text-slate-400">{{ auth()->user()->email }}</span>
                        </div>

                        <!-- Logout Button -->
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" title="Logout" class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-white text-slate-500 hover:bg-rose-50 hover:text-rose-600 border border-pink-200 shadow-sm transition-all" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            </button>
                        </form>
                    @else
                        @if(!request()->is('login*') && !request()->is('*/login') && !request()->is('register*'))
                            <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 text-sm font-extrabold rounded-2xl bg-rose-500 text-white hover:bg-rose-600 shadow-sm transition-colors">
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

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 flex items-start p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 shadow-sm" role="alert">
                <div class="flex-shrink-0 w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mr-3 mt-0.5">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-emerald-900">Operasi Berhasil</p>
                    <p class="text-sm text-emerald-700 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 flex items-start p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm" role="alert">
                <div class="flex-shrink-0 w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center mr-3 mt-0.5">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-rose-900">Peringatan / Error</p>
                    <p class="text-sm text-rose-700 mt-0.5">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 shadow-sm" role="alert">
                <div class="flex items-center mb-2">
                    <i class="fa-solid fa-circle-xmark text-amber-600 mr-2"></i>
                    <p class="font-bold">Terdapat beberapa kesalahan input data:</p>
                </div>
                <ul class="list-disc list-inside text-sm text-amber-800 space-y-1 ml-2">
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

    <!-- Footer -->
    <footer class="mt-auto border-t border-pink-100 bg-white py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-slate-500">
            <div class="flex items-center space-x-2">
                <span class="font-black text-rose-500">Diza's Plushie</span>
                <span>&bull;</span>
                <span>Sistem Manajemen Inventori & Penjualan Boneka Pastel</span>
            </div>
            <div>
                <span>Tugas UTS Web Programming 2 &copy; {{ date('Y') }}</span>
            </div>
        </div>
    </footer>

</body>
</html>
