@extends('layouts.app', ['title' => 'Tambah Kategori Boneka'])

@section('content')
<div class="max-w-xl mx-auto py-4">
    <div class="mb-4">
        <a href="{{ route('categories.index') }}" class="text-xs font-bold text-slate-500 hover:text-plush-600 inline-flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Daftar Kategori
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
        <div class="mb-6">
            <h1 class="text-xl font-extrabold text-slate-900">Tambah Kategori Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Buat nama kategori baru untuk mengorganisasi produk boneka.</p>
        </div>

        <form action="{{ route('categories.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-bold text-slate-700 mb-1.5">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="name" required autofocus
                    value="{{ old('name') }}"
                    placeholder="Contoh: Boneka Wisuda & Buket"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-plush-500 focus:border-plush-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all">
                @error('name')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-bold text-slate-700 mb-1.5">
                    Deskripsi Kategori (Opsional)
                </label>
                <textarea name="description" id="description" rows="3"
                    placeholder="Penjelasan singkat mengenai kategori boneka ini..."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-plush-500 focus:border-plush-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="pt-4 flex items-center justify-end space-x-3">
                <a href="{{ route('categories.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-sm transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-plush-600 hover:bg-plush-700 text-white font-bold text-sm shadow-sm transition-colors flex items-center space-x-2">
                    <i class="fa-solid fa-save"></i>
                    <span>Simpan Kategori</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
