@extends('layouts.app', ['title' => "Portal Staff Toko - Diza's Plushie"])

@section('content')
<div class="max-w-md mx-auto py-8 sm:py-12 px-4">

    <!-- Main Staff Login Card Container -->
    <div class="bg-white rounded-3xl border border-emerald-100 shadow-xl shadow-emerald-100/50 overflow-hidden transition-all duration-300">

        <!-- Soft Mint / Sage Green Header Banner -->
        <div class="p-6 sm:p-8 border-b border-emerald-100/60 bg-gradient-to-br from-emerald-50 via-teal-50/40 to-white text-slate-800">
            <!-- Title & Subtitle -->
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                Portal Staff Operasional
            </h1>

            <p class="text-xs sm:text-sm mt-1.5 text-slate-500 font-medium">
                Masuk khusus petugas kasir penjualan (Stock-Out) dan penerimaan stok gudang (Stock-In).
            </p>
        </div>

        <!-- Login Form Body -->
        <div class="p-6 sm:p-8">
            <form action="{{ route('login') }}" method="POST" class="space-y-5" id="staffLoginForm">
                @csrf

                <!-- Hidden Staff Portal Role -->
                <input type="hidden" name="portal_role" value="staff">

                <!-- Email Input (Required) -->
                <div>
                    <label for="email" class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1.5">
                        Alamat Email Akun Staf <span class="text-emerald-600">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-emerald-500">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <input type="email" name="email" id="email" required autofocus
                            value="{{ old('email') }}"
                            placeholder="staff@dizasplushie.com"
                            class="w-full pl-11 pr-4 py-3 rounded-2xl border border-emerald-200 text-sm transition-all bg-emerald-50/30 hover:bg-white focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                    </div>
                </div>

                <!-- Password Input (Required) -->
                <div>
                    <label for="password" class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1.5">
                        Kata Sandi (Password) <span class="text-emerald-600">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-key"></i>
                        </span>
                        <input type="password" name="password" id="password" required
                            placeholder="Masukkan kata sandi staf"
                            class="w-full pl-11 pr-4 py-3 rounded-2xl border border-emerald-200 text-sm transition-all bg-slate-50/50 hover:bg-white focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                    </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center justify-between text-xs sm:text-sm">
                    <label class="flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-emerald-200">
                        <span class="ml-2 text-slate-600 font-bold">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                        class="w-full py-3.5 px-4 rounded-2xl font-black text-sm text-white shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center space-x-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 shadow-teal-300/30">
                        <i class="fa-solid fa-cash-register"></i>
                        <span>Masuk Portal Staff 🌿</span>
                    </button>
                </div>

                <!-- Internal Staff Notice -->
                <div class="pt-4 border-t border-emerald-100 text-center text-xs text-slate-500 flex items-center justify-center space-x-2 font-medium">
                    <i class="fa-solid fa-circle-info text-emerald-500 text-sm"></i>
                    <span>Akses internal staf operasional. Akun staf didaftarkan oleh Administrator.</span>
                </div>
            </form>
        </div>

    </div>

    <!-- Security Note & Discreet Portal Links for Evaluator / Testing -->
    <div class="mt-8 text-center text-xs text-slate-400 font-medium">
        <p>🌿 Portal Internal Staf &bull; Diza's Plushie Store</p>
        <div class="mt-3 text-[11px] text-slate-400/50 flex items-center justify-center space-x-3">
            <span>Akses Pengujian Dosen:</span>
            <a href="{{ route('login') }}" class="text-slate-400/70 hover:text-rose-500 transition-colors">Portal Pelanggan</a>
            <span>&bull;</span>
            <a href="{{ route('login.admin') }}" class="text-slate-400/70 hover:text-purple-600 transition-colors">Portal Administrator</a>
        </div>
    </div>

</div>
@endsection
