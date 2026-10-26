@extends('layouts.app', ['title' => 'Ubah Data Produk Boneka'])

@section('content')
<div class="max-w-3xl mx-auto py-4">
    <div class="mb-4">
        <a href="{{ route('products.index') }}" class="text-xs font-bold text-slate-500 hover:text-plush-600 inline-flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Master Produk
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Ubah Produk Boneka</h1>
                <p class="text-xs text-slate-500 mt-1">Perbarui data master, harga, dan spesifikasi boneka {{ $product->name }}.</p>
            </div>
            <!-- Current Stock Read-Only Badge -->
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 text-right">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Stok Saat Ini</span>
                <span class="text-lg font-black {{ $product->stock <= $product->minimum_stock ? 'text-rose-600' : 'text-slate-900' }}">
                    {{ $product->stock }} pcs
                </span>
            </div>
        </div>

        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- SKU & Category Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="sku" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Kode SKU Unik <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="sku" id="sku" required
                        value="{{ old('sku', $product->sku) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-plush-500 focus:border-plush-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all font-mono uppercase">
                    @error('sku')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Kategori Boneka <span class="text-rose-500">*</span>
                    </label>
                    <select name="category_id" id="category_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-plush-500 focus:border-plush-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-bold text-slate-700 mb-1.5">
                    Nama Boneka / Produk <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="name" required
                    value="{{ old('name', $product->name) }}"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-plush-500 focus:border-plush-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all">
                @error('name')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Size & Material Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="size" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Ukuran Boneka
                    </label>
                    <input type="text" name="size" id="size"
                        value="{{ old('size', $product->size) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-plush-500 focus:border-plush-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all">
                    @error('size')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="material" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Bahan Kain & Isian
                    </label>
                    <input type="text" name="material" id="material"
                        value="{{ old('material', $product->material) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-plush-500 focus:border-plush-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all">
                    @error('material')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Pricing Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <div>
                    <label for="purchase_price" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Harga Beli / Modal Pabrik (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="0.01" name="purchase_price" id="purchase_price" required min="0"
                        value="{{ old('purchase_price', $product->purchase_price) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-plush-500 focus:border-plush-500 text-sm bg-white font-semibold">
                    @error('purchase_price')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="selling_price" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Harga Jual ke Pelanggan (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="0.01" name="selling_price" id="selling_price" required min="0"
                        value="{{ old('selling_price', $product->selling_price) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-plush-500 focus:border-plush-500 text-sm bg-white font-semibold text-rose-600">
                    @error('selling_price')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            <!-- Minimum Stock -->
            <div>
                <label for="minimum_stock" class="block text-sm font-bold text-slate-700 mb-1.5">
                    Batas Minimum Stok (Alert) <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="minimum_stock" id="minimum_stock" required min="0"
                    value="{{ old('minimum_stock', $product->minimum_stock) }}"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all font-bold text-rose-600">
                <p class="text-[11px] text-slate-400 mt-1">Perubahan saldo stok fisik dilakukan melalui modul Pembelian (Stock-In) atau Penjualan (Stock-Out) agar jejak audit terjaga.</p>
                @error('minimum_stock')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image File & Preset Selection -->
            <div class="p-4 sm:p-5 rounded-2xl bg-pink-50/40 border border-pink-100 space-y-3">
                <label class="block text-sm font-bold text-slate-800">
                    <i class="fa-solid fa-camera mr-1 text-pink-500"></i> Foto Produk Boneka Asli
                </label>

                <div class="flex items-center space-x-4 mb-2">
                    <img src="{{ asset($product->image_url ?? 'images/plushies/teddy-bear-pita.jpg') }}" 
                         alt="{{ $product->name }}" 
                         id="current_image_preview"
                         class="w-16 h-16 object-cover rounded-2xl bg-pink-50 border border-pink-100 shadow-xs flex-shrink-0">
                    <div class="flex-1">
                        <label for="image_file" class="block text-xs font-bold text-slate-600 mb-1">
                            Unggah Foto Boneka Baru dari Komputer
                        </label>
                        <input type="file" name="image_file" id="image_file" accept="image/*"
                            onchange="if(this.files && this.files[0]) { document.getElementById('current_image_preview').src = URL.createObjectURL(this.files[0]); }"
                            class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-pink-100 file:text-pink-700 hover:file:bg-pink-200 cursor-pointer border border-pink-200 rounded-xl p-1 bg-white">
                        <p class="text-[11px] text-slate-400 mt-1">Mendukung format JPG, PNG, WEBP (Maksimal 5MB).</p>
                        @error('image_file')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="image_url" class="block text-xs font-bold text-slate-600 mb-1">
                        Atau Gunakan Path / URL Foto Boneka
                    </label>
                    <input type="text" name="image_url" id="image_url"
                        value="{{ old('image_url', $product->image_url) }}"
                        oninput="document.getElementById('current_image_preview').src = this.value || '{{ asset('images/plushies/teddy-bear-pita.jpg') }}'"
                        placeholder="Contoh: images/plushies/teddy-bear-pita.jpg atau https://..."
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-xs bg-white transition-all">
                    @error('image_url')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quick Preset Selector -->
                <div class="pt-2 border-t border-pink-100/70">
                    <div class="flex flex-wrap gap-2 items-center">
                        <span class="text-[11px] font-bold text-slate-500">Pilih Cepat Koleksi Asli:</span>
                        @php
                            $presets = [
                                ['label' => '🧸 Teddy Pita', 'path' => 'images/plushies/teddy-bear-pita.jpg'],
                                ['label' => '🧸 Teddy Sweater', 'path' => 'images/plushies/teddy-bear-mini.jpg'],
                                ['label' => '🐰 Kelinci Pink', 'path' => 'images/plushies/kelinci-pink.jpg'],
                                ['label' => '🐼 Panda Bambu', 'path' => 'images/plushies/panda-bambu.jpg'],
                                ['label' => '🧋 Kucing Boba', 'path' => 'images/plushies/kucing-boba.jpg'],
                                ['label' => '🎎 Chibi Idol', 'path' => 'images/plushies/chibi-idol.jpg'],
                                ['label' => '🦊 Rubah Pastel', 'path' => 'images/plushies/rubah-pastel.jpg'],
                                ['label' => '🎓 Beruang Wisuda', 'path' => 'images/plushies/beruang-wisuda.jpg'],
                                ['label' => '🌸 Buket Boneka', 'path' => 'images/plushies/buket-boneka.jpg'],
                                ['label' => '🐕 Bantal Shiba', 'path' => 'images/plushies/bantal-shiba.jpg'],
                            ];
                        @endphp
                        @foreach($presets as $pr)
                            <button type="button" onclick="document.getElementById('image_url').value = '{{ $pr['path'] }}'; document.getElementById('current_image_preview').src = '{{ asset('') }}' + '{{ $pr['path'] }}'"
                                class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-white hover:bg-pink-100 text-pink-700 border border-pink-200 transition-colors shadow-2xs">
                                {{ $pr['label'] }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="pt-4 flex items-center justify-end space-x-3">
                <a href="{{ route('products.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-sm transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-plush-600 hover:bg-plush-700 text-white font-bold text-sm shadow-sm transition-colors flex items-center space-x-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Perbarui Data Boneka</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
