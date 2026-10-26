@extends('layouts.app', ['title' => "Portal Administrator - Diza's Plushie"])

@section('content')
<div class="max-w-md mx-auto py-8 sm:py-12 px-4">

    <!-- Main Admin Login Card Container -->
    <div class="bg-white rounded-3xl border border-purple-100 shadow-xl shadow-purple-100/50 overflow-hidden transition-all duration-300">

        <!-- Soft Lavender / Rose Gold Header Banner -->
        <div class="p-6 sm:p-8 border-b border-purple-100/60 bg-gradient-to-br from-purple-50 via-indigo-50/40 to-white text-slate-800">
            <!-- Title & Subtitle -->
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                Portal Administrator & Owner
            </h1>

            <p class="text-xs sm:text-sm mt-1.5 text-slate-500 font-medium">
                Akses manajemen master data, laporan keuangan, stok opname, dan otorisasi sistem.
            </p>
        </div>

        <!-- Login Form Body -->
        <div class="p-6 sm:p-8">
            <form action="{{ route('login') }}" method="POST" class="space-y-5" id="adminLoginForm">
                @csrf

                <!-- Hidden Admin Portal Role -->
                <input type="hidden" name="portal_role" value="admin">

                <!-- Email Input (Required) -->
                <div>
                    <label for="email" class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1.5">
                        Alamat Email Administrator <span class="text-purple-600">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-purple-500">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <input type="email" name="email" id="email" required autofocus
                            value="{{ old('email') }}"
                            placeholder="admin@dizasplushie.com"
                            class="w-full pl-11 pr-4 py-3 rounded-2xl border border-purple-200 text-sm transition-all bg-purple-50/30 hover:bg-white focus:bg-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 font-medium">
                    </div>
                </div>

                <!-- Password Input (Required) -->
                <div>
                    <label for="password" class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1.5">
                        Kata Sandi (Password) <span class="text-purple-600">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-key"></i>
                        </span>
                        <input type="password" name="password" id="password" required
                            placeholder="Masukkan kata sandi administrator"
                            class="w-full pl-11 pr-4 py-3 rounded-2xl border border-purple-200 text-sm transition-all bg-slate-50/50 hover:bg-white focus:bg-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 font-medium">
                    </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center justify-between text-xs sm:text-sm">
                    <label class="flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-purple-600 focus:ring-purple-500 border-purple-200">
                        <span class="ml-2 text-slate-600 font-bold">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                        class="w-full py-3.5 px-4 rounded-2xl font-black text-sm text-white shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center space-x-2 bg-gradient-to-r from-purple-600 to-indigo-700 hover:from-purple-700 hover:to-indigo-800 shadow-purple-300/30">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Masuk Panel Administrator 👑</span>
                    </button>
                </div>

                <!-- Authorization Notice -->
                <div class="pt-4 border-t border-purple-100 text-center text-xs text-slate-500 flex items-center justify-center space-x-2 font-medium">
                    <i class="fa-solid fa-lock text-purple-500 text-sm"></i>
                    <span>Akses terbatas Pemilik & Administrator Diza's Plushie dengan otorisasi penuh server-side.</span>
                </div>
            </form>
        </div>

    </div>

    <!-- Security Note & Discreet Portal Links for Evaluator / Testing -->
    <div class="mt-8 text-center text-xs text-slate-400 font-medium">
        <p>👑 Portal Manajemen Administrator &bull; Diza's Plushie Store</p>
        <div class="mt-3 text-[11px] text-slate-400/50 flex items-center justify-center space-x-3">
            <span>Akses Pengujian Dosen:</span>
            <a href="{{ route('login') }}" class="text-slate-400/70 hover:text-rose-500 transition-colors">Portal Pelanggan</a>
            <span>&bull;</span>
            <a href="{{ route('login.staff') }}" class="text-slate-400/70 hover:text-emerald-600 transition-colors">Portal Staff Toko</a>
        </div>
    </div>

</div>
@endsection
