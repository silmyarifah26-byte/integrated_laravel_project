@extends('layouts.app', ['title' => 'Portal Staff Toko - HijabSociety'])

@section('content')
<div class="max-w-md mx-auto my-6">
    <div class="bg-white border border-gray-200 rounded-md shadow-xs p-6">
        
        <!-- Header -->
        <div class="mb-5 pb-3 border-b border-gray-200">
            <h1 class="text-lg font-bold text-gray-900">
                Portal Staff Operasional
            </h1>
            <p class="text-xs text-gray-500 mt-1">
                Masuk untuk mengelola transaksi kasir toko (Stock-Out) dan pencatatan penerimaan stok (Stock-In).
            </p>
        </div>

        <!-- Form Login Staff -->
        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="portal_role" value="staff">

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">
                    Email Staff <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" required autofocus
                    value="{{ old('email') }}"
                    placeholder="staff@hijabsociety.com"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
            </div>

            <!-- Password Input -->
            <div>
                <label for="password" class="block text-xs font-semibold text-gray-700 mb-1">
                    Kata Sandi <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password" id="password" required
                    placeholder="Masukkan kata sandi"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
            </div>

            <!-- Remember Me -->
            <div class="flex items-center text-xs">
                <input type="checkbox" name="remember" id="remember" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="remember" class="ml-2 text-gray-600 select-none">Ingat saya di perangkat ini</label>
            </div>

            <!-- Submit Button -->
            <div class="pt-1">
                <button type="submit"
                    class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md text-sm transition-colors">
                    Masuk Portal Staff
                </button>
            </div>
        </form>
    </div>

    <!-- Switch Portal Link -->
    <div class="mt-4 text-center text-xs text-gray-500 space-x-2">
        <span>Bukan Staff Operasional?</span>
        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Portal Pelanggan</a>
        <span>&bull;</span>
        <a href="{{ route('login.admin') }}" class="text-blue-600 hover:underline">Portal Administrator</a>
    </div>
</div>
@endsection
