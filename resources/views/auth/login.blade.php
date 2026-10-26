@extends('layouts.app', ['title' => 'Masuk Pelanggan - HijabSociety'])

@section('content')
<div class="max-w-md mx-auto my-6">
    <div class="bg-white border border-gray-200 rounded-md shadow-xs p-6">
        
        <!-- Header -->
        <div class="mb-5 pb-3 border-b border-gray-200">
            <h1 class="text-lg font-bold text-gray-900">
                Masuk Pelanggan HijabSociety
            </h1>
            <p class="text-xs text-gray-500 mt-1">
                Silakan masukkan nama dan alamat email Anda untuk mulai berbelanja hijab.
            </p>
        </div>

        <!-- Form Login -->
        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="portal_role" value="customer">

            <!-- Name Input -->
            <div>
                <label for="name" class="block text-xs font-semibold text-gray-700 mb-1">
                    Nama Lengkap / Panggilan <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" required
                    value="{{ old('name') }}"
                    placeholder="Masukkan nama lengkap atau panggilan Anda"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500">
            </div>

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">
                    Alamat Email <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" required autofocus
                    value="{{ old('email') }}"
                    placeholder="nama@email.com"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500">
            </div>

            <!-- Remember Me -->
            <div class="flex items-center text-xs">
                <input type="checkbox" name="remember" id="remember" class="h-4 w-4 text-rose-600 focus:ring-rose-500 border-gray-300 rounded">
                <label for="remember" class="ml-2 text-gray-600 select-none">Ingat saya di perangkat ini</label>
            </div>

            <!-- Submit Button -->
            <div class="pt-1">
                <button type="submit"
                    class="w-full py-2 px-4 bg-rose-600 hover:bg-rose-700 text-white font-medium rounded-md text-sm transition-colors">
                    Masuk
                </button>
            </div>

            <!-- Register Link -->
            <div class="pt-3 text-center text-xs text-gray-500 border-t border-gray-100">
                Belum memiliki akun?
                <a href="{{ route('register') }}" class="text-rose-600 hover:text-rose-700 font-semibold ml-1">
                    Daftar Akun Customer Sekarang
                </a>
            </div>
        </form>
    </div>

    <!-- Switch Portal Link -->
    <div class="mt-4 text-center text-xs text-gray-500 space-x-2">
        <span>Portal Lain:</span>
        <a href="{{ route('login.staff') }}" class="text-blue-600 hover:underline">Portal Staff Toko</a>
        <span>&bull;</span>
        <a href="{{ route('login.admin') }}" class="text-blue-600 hover:underline">Portal Administrator</a>
    </div>
</div>
@endsection
