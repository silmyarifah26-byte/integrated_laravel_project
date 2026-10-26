@extends('layouts.app', ['title' => 'Ubah Data Produk Hijab'])

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <div>
        <a href="{{ route('products.index') }}" class="text-xs font-medium text-gray-500 hover:text-gray-700 inline-flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Master Produk
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-lg border border-gray-200 shadow-sm">
        <div class="mb-6 pb-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Ubah Produk Hijab</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Perbarui data master, harga, dan spesifikasi produk {{ $product->name }}.</p>
            </div>
            <!-- Current Stock Badge -->
            <div class="p-2.5 rounded-md bg-gray-50 border border-gray-200 text-right">
                <span class="text-[10px] uppercase font-semibold text-gray-400 block">Stok Saat Ini</span>
                <span class="text-base font-bold {{ $product->stock <= $product->minimum_stock ? 'text-red-600' : 'text-gray-900' }}">
                    {{ $product->stock }} pcs
                </span>
            </div>
        </div>

        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- SKU & Category Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">
                        Kode SKU Unik <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="sku" id="sku" required
                        value="{{ old('sku', $product->sku) }}"
                        class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 text-sm font-mono uppercase">
                    @error('sku')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Kategori Hijab <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" id="category_id" required
                        class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 text-sm">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Hijab / Produk <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" required
                    value="{{ old('name', $product->name) }}"
                    class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 text-sm">
                @error('name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Size & Material Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="size" class="block text-sm font-medium text-gray-700 mb-1">
                        Ukuran Hijab
                    </label>
                    <input type="text" name="size" id="size"
                        value="{{ old('size', $product->size) }}"
                        class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 text-sm">
                    @error('size')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="material" class="block text-sm font-medium text-gray-700 mb-1">
                        Bahan / Material Kain
                    </label>
                    <input type="text" name="material" id="material"
                        value="{{ old('material', $product->material) }}"
                        class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 text-sm">
                    @error('material')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Pricing Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-md bg-gray-50 border border-gray-200">
                <div>
                    <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-1">
                        Harga Beli / Modal Pabrik (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.01" name="purchase_price" id="purchase_price" required min="0"
                        value="{{ old('purchase_price', $product->purchase_price) }}"
                        class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 text-sm bg-white">
                    @error('purchase_price')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="selling_price" class="block text-sm font-medium text-gray-700 mb-1">
                        Harga Jual ke Pelanggan (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.01" name="selling_price" id="selling_price" required min="0"
                        value="{{ old('selling_price', $product->selling_price) }}"
                        class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 text-sm bg-white font-medium text-rose-600">
                    @error('selling_price')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Minimum Stock -->
            <div>
                <label for="minimum_stock" class="block text-sm font-medium text-gray-700 mb-1">
                    Batas Minimum Stok <span class="text-red-500">*</span>
                </label>
                <input type="number" name="minimum_stock" id="minimum_stock" required min="0"
                    value="{{ old('minimum_stock', $product->minimum_stock) }}"
                    class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 text-sm">
                @error('minimum_stock')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image File & Preset Selection -->
            <div class="p-4 rounded-md bg-gray-50 border border-gray-200 space-y-3">
                <label class="block text-sm font-medium text-gray-800">
                    Foto Produk Hijab
                </label>

                <!-- Current Image Preview -->
                <div class="flex items-center space-x-3 mb-2">
                    <img src="{{ asset($product->image_path) }}" 
                         alt="{{ $product->name }}" 
                         class="w-16 h-16 object-cover rounded-md border border-gray-300">
                    <div>
                        <span class="text-xs font-medium text-gray-700 block">Foto Saat Ini:</span>
                        <span class="text-xs text-gray-400 font-mono">{{ $product->image_url ?? 'Menggunakan default' }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="image_file" class="block text-xs font-medium text-gray-600 mb-1">
                            Unggah Foto Baru (JPG, PNG, WEBP)
                        </label>
                        <input type="file" name="image_file" id="image_file" accept="image/*"
                            class="w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300 cursor-pointer border border-gray-300 rounded-md p-1 bg-white">
                        @error('image_file')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="image_url" class="block text-xs font-medium text-gray-600 mb-1">
                            Atau Ganti Path File
                        </label>
                        <input type="text" name="image_url" id="image_url"
                            value="{{ old('image_url', $product->image_url) }}"
                            class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 text-xs bg-white">
                        @error('image_url')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Quick Preset Selector -->
                <div class="pt-2 border-t border-gray-200">
                    <span class="text-xs font-medium text-gray-500 block mb-1.5">Pilihan Cepat Koleksi Standar:</span>
                    <div class="flex flex-wrap gap-1.5 items-center">
                        @php
                            $presets = [
                                ['label' => 'Pashmina Ceruty', 'path' => 'images/hijab/pashmina-ceruty.jpg'],
                                ['label' => 'Pashmina Silk', 'path' => 'images/hijab/pashmina-silk.jpg'],
                                ['label' => 'Pashmina Rayon', 'path' => 'images/hijab/pashmina-rayon.jpg'],
                                ['label' => 'Segi Empat Voal', 'path' => 'images/hijab/segiempat-voal.jpg'],
                                ['label' => 'Segi Empat Paris', 'path' => 'images/hijab/segiempat-paris.jpg'],
                                ['label' => 'Segi Empat Motif', 'path' => 'images/hijab/segiempat-motif.jpg'],
                                ['label' => 'Bergo Hamidah', 'path' => 'images/hijab/bergo-hamidah.jpg'],
                                ['label' => 'Bergo Sport', 'path' => 'images/hijab/bergo-sport.jpg'],
                                ['label' => 'Khimar French', 'path' => 'images/hijab/khimar-french.jpg'],
                                ['label' => 'Inner Ciput', 'path' => 'images/hijab/inner-ciput.jpg'],
                            ];
                        @endphp
                        @foreach($presets as $pr)
                            <button type="button" onclick="document.getElementById('image_url').value = '{{ $pr['path'] }}'"
                                class="px-2 py-0.5 rounded text-xs bg-white hover:bg-gray-100 text-gray-700 border border-gray-300 transition-colors">
                                {{ $pr['label'] }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="pt-3 flex items-center justify-end space-x-2">
                <a href="{{ route('products.index') }}" class="px-3.5 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-medium transition-colors">
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
