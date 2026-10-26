@extends('layouts.app', ['title' => 'Daftar Akun Customer'])

@section('content')
<div class="max-w-xl mx-auto my-8">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 sm:p-8">
        
        <!-- Header -->
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h1 class="text-xl font-bold text-gray-900">
                Daftar Akun Pembeli HijabSociety
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Lengkapi formulir di bawah ini untuk mendaftar akun pelanggan baru.
            </p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Name Input -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" required autofocus
                    value="{{ old('name') }}"
                    placeholder="Contoh: Nadia Putri"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500">
            </div>

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Alamat Email <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" required
                    value="{{ old('email') }}"
                    placeholder="nama@email.com"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500">
            </div>

            <!-- Phone & Password Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                        Nomor Telepon / WhatsApp
                    </label>
                    <input type="text" name="phone" id="phone"
                        value="{{ old('phone') }}"
                        placeholder="08123456789"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Kata Sandi <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password" id="password" required
                        placeholder="Min. 6 karakter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500">
                </div>
            </div>

            <!-- Password Confirmation -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                    Konfirmasi Kata Sandi <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    placeholder="Ulangi kata sandi"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500">
            </div>

            <!-- Address Input -->
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                    Alamat Lengkap Pengiriman
                </label>
                <textarea name="address" id="address" rows="3"
                    placeholder="Alamat jalan, kelurahan, kecamatan, kota..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500">{{ old('address') }}</textarea>
            </div>

            <!-- Role info -->
            <div class="p-3 rounded-md bg-gray-50 border border-gray-200 text-xs text-gray-600">
                Akun yang didaftarkan pada form ini secara default memiliki hak akses sebagai <strong>Customer</strong>.
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit"
                    class="w-full py-2.5 px-4 bg-rose-600 hover:bg-rose-700 text-white font-medium rounded-md text-sm transition-colors shadow-xs">
                    Selesaikan Pendaftaran
                </button>
            </div>

            <!-- Login Link -->
            <div class="pt-3 text-center text-xs text-gray-500 border-t border-gray-100">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" class="text-rose-600 hover:text-rose-700 font-semibold ml-1">
                    Masuk ke Akun Anda
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
