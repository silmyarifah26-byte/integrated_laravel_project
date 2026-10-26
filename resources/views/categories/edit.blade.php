@extends('layouts.app', ['title' => 'Ubah Kategori Hijab'])

@section('content')
<div class="max-w-xl mx-auto space-y-4">
    <div>
        <a href="{{ route('categories.index') }}" class="text-xs font-medium text-gray-500 hover:text-gray-700 inline-flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Daftar Kategori
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-lg border border-gray-200 shadow-sm">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h1 class="text-xl font-bold text-gray-900">Ubah Kategori Hijab</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Perbarui nama atau deskripsi kategori {{ $category->name }}.</p>
        </div>

        <form action="{{ route('categories.update', $category) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" required autofocus
                    value="{{ old('name', $category->name) }}"
                    class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 text-sm">
                @error('name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Deskripsi Kategori (Opsional)
                </label>
                <textarea name="description" id="description" rows="3"
                    class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 text-sm">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="pt-3 flex items-center justify-end space-x-2">
                <a href="{{ route('categories.index') }}" class="px-3.5 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-medium transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 rounded-md bg-yellow-600 hover:bg-yellow-700 text-white text-xs font-medium transition-colors flex items-center space-x-1.5">
                    <i class="fa-solid fa-save"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
