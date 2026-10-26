@extends('layouts.app', ['title' => 'Portal Staff Toko - HijabSociety'])

@section('content')
<div class="max-w-md mx-auto my-8">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 sm:p-8">
        
        <!-- Header -->
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h1 class="text-xl font-bold text-gray-900">
                Portal Staff Operasional
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Akses kasir transaksi penjualan (Stock-Out) dan pencatatan penerimaan stok (Stock-In).
            </p>
        </div>

        <!-- Form Login Staff -->
        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Hidden Role -->
            <input type="hidden" name="portal_role" value="staff">

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email Staff <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" required autofocus
                    value="{{ old('email') }}"
                    placeholder="staff@hijabsociety.com"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-emerald-600 focus:border-emerald-600">
            </div>

            <!-- Password Input -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Kata Sandi <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password" id="password" required
                    placeholder="Masukkan kata sandi"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-emerald-600 focus:border-emerald-600">
            </div>

            <!-- Remember Me -->
            <div class="flex items-center text-sm">
                <input type="checkbox" name="remember" id="remember" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                <label for="remember" class="ml-2 text-gray-600 select-none">Ingat saya di perangkat ini</label>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit"
                    class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-md text-sm transition-colors shadow-xs">
                    Masuk Portal Staff
                </button>
            </div>
        </form>
    </div>

    <!-- Switch Portal Link -->
    <div class="mt-6 text-center text-xs text-gray-400 space-x-3">
        <span>Bukan Staff Operasional?</span>
        <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-700 underline">Portal Pelanggan</a>
        <span>&bull;</span>
        <a href="{{ route('login.admin') }}" class="text-gray-500 hover:text-gray-700 underline">Portal Administrator</a>
    </div>
</div>
@endsection
