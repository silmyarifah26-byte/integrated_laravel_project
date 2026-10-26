@extends('layouts.app', ['title' => 'Tambah Pemasok Boneka'])

@section('content')
<div class="max-w-2xl mx-auto py-4">
    <div class="mb-4">
        <a href="{{ route('suppliers.index') }}" class="text-xs font-bold text-slate-500 hover:text-blue-600 inline-flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Daftar Pemasok
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
        <div class="mb-6">
            <h1 class="text-xl font-extrabold text-slate-900">Tambah Pemasok / Pabrik Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Registrasikan data kontak pabrik produsen atau distributor boneka.</p>
        </div>

        <form action="{{ route('suppliers.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-bold text-slate-700 mb-1.5">
                    Nama Pemasok / Badan Usaha <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="name" required autofocus
                    value="{{ old('name') }}"
                    placeholder="Contoh: PT Kreasi Boneka Nusantara (Bandung)"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all">
                @error('name')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone & Email Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Nomor Telepon / WhatsApp <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="phone" id="phone" required
                        value="{{ old('phone') }}"
                        placeholder="Contoh: 022-7654321 atau 0812345678"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all">
                    @error('phone')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Email Resmi (Opsional)
                    </label>
                    <input type="email" name="email" id="email"
                        value="{{ old('email') }}"
                        placeholder="order@pabrikboneka.com"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all">
                    @error('email')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Address -->
            <div>
                <label for="address" class="block text-sm font-bold text-slate-700 mb-1.5">
                    Alamat Pabrik / Gudang Pemasok
                </label>
                <textarea name="address" id="address" rows="3"
                    placeholder="Alamat lengkap pabrik, kawasan industri, kota..."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all">{{ old('address') }}</textarea>
                @error('address')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="pt-4 flex items-center justify-end space-x-3">
                <a href="{{ route('suppliers.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-sm transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-sm transition-colors flex items-center space-x-2">
                    <i class="fa-solid fa-save"></i>
                    <span>Simpan Pemasok</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
