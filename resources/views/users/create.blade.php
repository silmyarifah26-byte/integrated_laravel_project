@extends('layouts.app', ['title' => 'Tambah Pengguna Baru'])

@section('content')
<div class="max-w-2xl mx-auto py-4">
    <div class="mb-4">
        <a href="{{ route('users.index') }}" class="text-xs font-bold text-slate-500 hover:text-purple-600 inline-flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Manajemen Pengguna
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
        <div class="mb-6">
            <h1 class="text-xl font-extrabold text-slate-900">Tambah Akun Pengguna Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Buat akun untuk Administrator, Staff operasional kasir & gudang, atau Pelanggan.</p>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-bold text-slate-700 mb-1.5">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="name" required autofocus
                    value="{{ old('name') }}"
                    placeholder="Contoh: Rina Anggraini"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all">
                @error('name')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email & Role Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" required
                        value="{{ old('email') }}"
                        placeholder="user@bonekaku.test"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all">
                    @error('email')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="role" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Peran Akun (Role) <span class="text-rose-500">*</span>
                    </label>
                    <select name="role" id="role" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all">
                        <option value="">-- Pilih Role --</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (Full Akses)</option>
                        <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff (Kasir & Inventori)</option>
                        <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>Customer (Pelanggan)</option>
                    </select>
                    @error('role')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Password & Phone Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Kata Sandi Awal <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password" id="password" required
                        placeholder="Minimal 6 karakter"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all">
                    @error('password')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Nomor Telepon
                    </label>
                    <input type="text" name="phone" id="phone"
                        value="{{ old('phone') }}"
                        placeholder="08123456789"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all">
                    @error('phone')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Address -->
            <div>
                <label for="address" class="block text-sm font-bold text-slate-700 mb-1.5">
                    Alamat Lengkap
                </label>
                <textarea name="address" id="address" rows="3"
                    placeholder="Alamat domisili atau alamat toko..."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all">{{ old('address') }}</textarea>
                @error('address')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="pt-4 flex items-center justify-end space-x-3">
                <a href="{{ route('users.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-sm transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-sm shadow-sm transition-colors flex items-center space-x-2">
                    <i class="fa-solid fa-save"></i>
                    <span>Simpan Pengguna</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
