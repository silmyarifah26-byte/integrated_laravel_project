@extends('layouts.app', ['title' => 'Tambah Pengguna Baru'])

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <div>
        <a href="{{ route('users.index') }}" class="text-xs font-medium text-gray-500 hover:text-gray-700 inline-flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Manajemen Pengguna
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-lg border border-gray-200 shadow-sm">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h1 class="text-xl font-bold text-gray-900">Tambah Akun Pengguna Baru</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Buat akun untuk Administrator, Staff kasir toko, atau Customer.</p>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" required autofocus
                    value="{{ old('name') }}"
                    placeholder="Contoh: Rina Anggraini"
                    class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-purple-600 focus:border-purple-600 text-sm">
                @error('name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email & Role Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Alamat Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" required
                        value="{{ old('email') }}"
                        placeholder="user@hijabsociety.com"
                        class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-purple-600 focus:border-purple-600 text-sm">
                    @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                        Peran (Role) <span class="text-red-500">*</span>
                    </label>
                    <select name="role" id="role" required
                        class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-purple-600 focus:border-purple-600 text-sm">
                        <option value="">-- Pilih Role --</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                    </select>
                    @error('role')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Password & Phone Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Kata Sandi Awal <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password" id="password" required
                        placeholder="Minimal 6 karakter"
                        class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-purple-600 focus:border-purple-600 text-sm">
                    @error('password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                        Nomor Telepon
                    </label>
                    <input type="text" name="phone" id="phone"
                        value="{{ old('phone') }}"
                        placeholder="08123456789"
                        class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-purple-600 focus:border-purple-600 text-sm">
                    @error('phone')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Address -->
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                    Alamat Lengkap
                </label>
                <textarea name="address" id="address" rows="3"
                    placeholder="Alamat domisili..."
                    class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-purple-600 focus:border-purple-600 text-sm">{{ old('address') }}</textarea>
                @error('address')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="pt-3 flex items-center justify-end space-x-2">
                <a href="{{ route('users.index') }}" class="px-3.5 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-medium transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 rounded-md bg-purple-600 hover:bg-purple-700 text-white text-xs font-medium transition-colors flex items-center space-x-1.5">
                    <i class="fa-solid fa-save"></i>
                    <span>Simpan Pengguna</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
