@extends('layouts.app', ['title' => "Masuk Pelanggan - Diza's Plushie"])

@section('content')
<div class="max-w-md mx-auto py-8 sm:py-12 px-4">

    <!-- Main Login Card Container -->
    <div class="bg-white rounded-3xl border border-pink-100 shadow-xl shadow-pink-100/50 overflow-hidden transition-all duration-300">

        <!-- Soft Pink Pastel Header Banner -->
        <div class="p-6 sm:p-8 border-b border-pink-100/60 bg-gradient-to-br from-pink-50 via-rose-50/40 to-white text-slate-800">
            <!-- Title & Subtitle -->
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                Masuk Pelanggan Diza's Plushie
            </h1>

            <p class="text-xs sm:text-sm mt-1.5 text-slate-500 font-medium">
                Belanja boneka favoritmu dan pantau pesanan dengan mudah tanpa perlu mengingat kata sandi.
            </p>
        </div>

        <!-- Login Form Body -->
        <div class="p-6 sm:p-8">
            <form action="{{ route('login') }}" method="POST" class="space-y-5" id="customerLoginForm">
                @csrf

                <!-- Hidden Customer Portal Role -->
                <input type="hidden" name="portal_role" value="customer">

                <!-- Name Input (Required) -->
                <div>
                    <label for="name" class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1.5">
                        Nama Panggilan / Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-rose-300">
                            <i class="fa-solid fa-user-tag"></i>
                        </span>
                        <input type="text" name="name" id="name" required
                            value="{{ old('name') }}"
                            placeholder="Contoh: Zahra"
                            class="w-full pl-11 pr-4 py-3 rounded-2xl border border-pink-200 text-sm transition-all bg-pink-50/30 hover:bg-white focus:bg-white focus:ring-2 focus:ring-rose-400 focus:border-rose-400 font-medium">
                    </div>
                </div>

                <!-- Email Input (Required) -->
                <div>
                    <label for="email" class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1.5">
                        Alamat Email Akun <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <input type="email" name="email" id="email" required autofocus
                            value="{{ old('email') }}"
                            placeholder="nama@email.com"
                            class="w-full pl-11 pr-4 py-3 rounded-2xl border border-pink-200 text-sm transition-all bg-slate-50/50 hover:bg-white focus:bg-white focus:ring-2 focus:ring-rose-400 focus:border-rose-400 font-medium">
                    </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center justify-between text-xs sm:text-sm">
                    <label class="flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-rose-500 focus:ring-rose-400 border-pink-200">
                        <span class="ml-2 text-slate-600 font-bold">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                        class="w-full py-3.5 px-4 rounded-2xl font-black text-sm text-white shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center space-x-2 bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 shadow-pink-300/30">
                        <i class="fa-solid fa-heart"></i>
                        <span>Masuk & Mulai Belanja</span>
                    </button>
                </div>

                <!-- Registration Link -->
                <div class="pt-4 border-t border-pink-100 text-center text-xs sm:text-sm text-slate-500 font-semibold">
                    Masuk otomatis tanpa password! Atau ingin daftar profil lengkap?
                    <a href="{{ route('register') }}" class="font-black text-rose-500 hover:text-rose-600 hover:underline ml-1">
                        Daftar Akun Customer Sekarang
                    </a>
                </div>
            </form>
        </div>

    </div>

    <!-- Security Note & Discreet Portal Links for Evaluator / Testing -->
    <div class="mt-8 text-center text-xs text-slate-400 font-medium">
        <p>🌸 Diza's Plushie Store &bull; Sistem Inventori & Kasir Boneka Cantik</p>
        <div class="mt-3 text-[11px] text-slate-400/50 flex items-center justify-center space-x-3">
            <span>Akses Pengujian Dosen:</span>
            <a href="{{ route('login.staff') }}" class="text-slate-400/70 hover:text-emerald-600 transition-colors">Portal Staff Toko</a>
            <span>&bull;</span>
            <a href="{{ route('login.admin') }}" class="text-slate-400/70 hover:text-purple-600 transition-colors">Portal Administrator</a>
        </div>
    </div>

</div>
@endsection
