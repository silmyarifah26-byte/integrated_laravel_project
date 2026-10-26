@extends('layouts.app', ['title' => 'Daftar Akun Customer'])

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-xl shadow-slate-100">
        <div class="mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 mb-4 shadow-sm">
                <i class="fa-solid fa-user-plus text-xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Daftar Akun Pembeli Diza's Plushie</h1>
            <p class="text-slate-500 text-sm mt-1">Buat akun customer untuk mulai memesan boneka dan melihat riwayat belanja Anda.</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Name Input -->
            <div>
                <label for="name" class="block text-sm font-bold text-slate-700 mb-1.5">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        <i class="fa-regular fa-user"></i>
                    </span>
                    <input type="text" name="name" id="name" required autofocus
                        value="{{ old('name') }}"
                        placeholder="Contoh: Nadia Putri"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-plush-500 focus:border-plush-500 text-sm transition-all bg-slate-50/50 hover:bg-white focus:bg-white">
                </div>
            </div>

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-sm font-bold text-slate-700 mb-1.5">
                    Alamat Email <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        <i class="fa-regular fa-envelope"></i>
                    </span>
                    <input type="email" name="email" id="email" required
                        value="{{ old('email') }}"
                        placeholder="nama@email.com"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-plush-500 focus:border-plush-500 text-sm transition-all bg-slate-50/50 hover:bg-white focus:bg-white">
                </div>
            </div>

            <!-- Grid for Phone & Password -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Nomor Telepon / WhatsApp
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-phone"></i>
                        </span>
                        <input type="text" name="phone" id="phone"
                            value="{{ old('phone') }}"
                            placeholder="08123456789"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-plush-500 focus:border-plush-500 text-sm transition-all bg-slate-50/50 hover:bg-white focus:bg-white">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-key"></i>
                        </span>
                        <input type="password" name="password" id="password" required
                            placeholder="Min. 6 karakter"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-plush-500 focus:border-plush-500 text-sm transition-all bg-slate-50/50 hover:bg-white focus:bg-white">
                    </div>
                </div>
            </div>

            <!-- Password Confirmation -->
            <div>
                <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-1.5">
                    Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        placeholder="Ulangi kata sandi Anda"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-plush-500 focus:border-plush-500 text-sm transition-all bg-slate-50/50 hover:bg-white focus:bg-white">
                </div>
            </div>

            <!-- Address Input -->
            <div>
                <label for="address" class="block text-sm font-bold text-slate-700 mb-1.5">
                    Alamat Lengkap Pengiriman
                </label>
                <textarea name="address" id="address" rows="3"
                    placeholder="Alamat rumah atau alamat pengiriman boneka..."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-plush-500 focus:border-plush-500 text-sm transition-all bg-slate-50/50 hover:bg-white focus:bg-white">{{ old('address') }}</textarea>
            </div>

            <!-- Notice on Role -->
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-500 flex items-center space-x-2">
                <i class="fa-solid fa-circle-info text-plush-500 text-sm"></i>
                <span>Akun yang didaftarkan melalui form publik ini otomatis memiliki peran <strong>Customer</strong> demi keamanan.</span>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-plush-600 hover:bg-plush-700 active:bg-plush-800 text-white font-bold text-sm shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-user-check"></i>
                    <span>Selesaikan Pendaftaran</span>
                </button>
            </div>

            <!-- Login Link -->
            <div class="pt-4 border-t border-slate-100 text-center text-sm text-slate-500">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" class="font-bold text-plush-600 hover:text-plush-700 hover:underline">
                    Masuk ke Akun Anda
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
