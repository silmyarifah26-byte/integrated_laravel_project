@extends('layouts.app', ['title' => 'Tambah Produk Hijab'])

@section('content')
<div class="max-w-3xl mx-auto py-4">
    <div class="mb-4">
        <a href="{{ route('products.index') }}" class="text-xs font-bold text-slate-500 hover:text-plush-600 inline-flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Master Produk
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
        <div class="mb-6">
            <h1 class="text-xl font-extrabold text-slate-900">Tambah Produk Hijab Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Lengkapi data master hijab, harga modal, harga jual, dan spesifikasi bahan kain.</p>
        </div>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- SKU & Category Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="sku" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Kode SKU Unik <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="sku" id="sku" required
                        value="{{ old('sku') }}"
                        placeholder="Contoh: HJB-PSH-004"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-plush-500 focus:border-plush-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all font-mono uppercase">
                    @error('sku')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Kategori Hijab <span class="text-rose-500">*</span>
                    </label>
                    <select name="category_id" id="category_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-plush-500 focus:border-plush-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
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
                    Nama Hijab / Produk <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="name" required
                    value="{{ old('name') }}"
                    placeholder="Contoh: Pashmina Plisket Babydoll Mocca"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-plush-500 focus:border-plush-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all">
                @error('name')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Size & Material Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="size" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Ukuran Hijab
                    </label>
                    <input type="text" name="size" id="size"
                        value="{{ old('size') }}"
                        placeholder="Contoh: 175 x 75 cm atau 115 x 115 cm"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-plush-500 focus:border-plush-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all">
                    @error('size')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="material" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Bahan / Material Kain
                    </label>
                    <input type="text" name="material" id="material"
                        value="{{ old('material') }}"
                        placeholder="Contoh: Ceruty Babydoll Premium, Voal Ultrafine, Silk Sutra"
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
                        value="{{ old('purchase_price') }}"
                        placeholder="50000"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-plush-500 focus:border-plush-500 text-sm bg-white font-semibold">
                    <p class="text-[11px] text-slate-400 mt-1">Hanya terlihat oleh Admin & Staff.</p>
                    @error('purchase_price')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="selling_price" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Harga Jual ke Pelanggan (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="0.01" name="selling_price" id="selling_price" required min="0"
                        value="{{ old('selling_price') }}"
                        placeholder="85000"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-plush-500 focus:border-plush-500 text-sm bg-white font-semibold text-rose-600">
                    <p class="text-[11px] text-slate-400 mt-1">Harga publik yang tampil di katalog belanja.</p>
                    @error('selling_price')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Stock & Minimum Stock Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="stock" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Stok Awal di Toko
                    </label>
                    <input type="number" name="stock" id="stock" min="0"
                        value="{{ old('stock', 0) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-plush-500 focus:border-plush-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all font-bold">
                    <p class="text-[11px] text-slate-400 mt-1">Stok selanjutnya akan ter-update otomatis lewat Stock-In / Out.</p>
                    @error('stock')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="minimum_stock" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Batas Minimum Stok (Alert) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="minimum_stock" id="minimum_stock" required min="0"
                        value="{{ old('minimum_stock', 5) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-sm bg-slate-50/50 hover:bg-white focus:bg-white transition-all font-bold text-rose-600">
                    <p class="text-[11px] text-slate-400 mt-1">Sistem memberi peringatan jika sisa stok &le; angka ini.</p>
                    @error('minimum_stock')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Image File & Preset Selection -->
            <div class="p-4 sm:p-5 rounded-2xl bg-pink-50/40 border border-pink-100 space-y-3">
                <label class="block text-sm font-bold text-slate-800">
                    <i class="fa-solid fa-camera mr-1 text-pink-500"></i> Foto Produk Hijab Asli
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- File Upload Input -->
                    <div>
                        <label for="image_file" class="block text-xs font-bold text-slate-600 mb-1">
                            Unggah Foto Hijab dari Komputer (Disarankan)
                        </label>
                        <input type="file" name="image_file" id="image_file" accept="image/*"
                            class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-pink-100 file:text-pink-700 hover:file:bg-pink-200 cursor-pointer border border-pink-200 rounded-xl p-1 bg-white">
                        <p class="text-[11px] text-slate-400 mt-1">Mendukung format JPG, PNG, WEBP (Maksimal 5MB).</p>
                        @error('image_file')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- URL / Path Input -->
                    <div>
                        <label for="image_url" class="block text-xs font-bold text-slate-600 mb-1">
                            Atau Gunakan Path / URL Foto Hijab
                        </label>
                        <input type="text" name="image_url" id="image_url"
                            value="{{ old('image_url') }}"
                            placeholder="Contoh: images/hijab/pashmina-ceruty.svg atau https://..."
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-xs bg-white transition-all">
                        <p class="text-[11px] text-slate-400 mt-1">Gunakan URL foto online atau pilih dari preset di bawah.</p>
                        @error('image_url')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Quick Preset Selector -->
                <div class="pt-2 border-t border-pink-100/70">
                    <div class="flex flex-wrap gap-2 items-center">
                        <span class="text-[11px] font-bold text-slate-500">Pilih Cepat Koleksi Asli:</span>
                        @php
                            $presets = [
                                ['label' => '🧕 Pashmina Ceruty', 'path' => 'images/hijab/pashmina-ceruty.svg'],
                                ['label' => '✨ Pashmina Silk', 'path' => 'images/hijab/pashmina-silk.svg'],
                                ['label' => '🌿 Pashmina Rayon', 'path' => 'images/hijab/pashmina-rayon.svg'],
                                ['label' => '🌸 Segi Empat Voal', 'path' => 'images/hijab/segiempat-voal.svg'],
                                ['label' => '🎀 Segi Empat Paris', 'path' => 'images/hijab/segiempat-paris.svg'],
                                ['label' => '🎨 Segi Empat Motif', 'path' => 'images/hijab/segiempat-motif.svg'],
                                ['label' => '⚡ Bergo Hamidah', 'path' => 'images/hijab/bergo-hamidah.svg'],
                                ['label' => '🏃‍♀️ Bergo Sport', 'path' => 'images/hijab/bergo-sport.svg'],
                                ['label' => '🕊️ Khimar French', 'path' => 'images/hijab/khimar-french.svg'],
                                ['label' => '👑 Inner Ciput Rajut', 'path' => 'images/hijab/inner-ciput.svg'],
                            ];
                        @endphp
                        @foreach($presets as $pr)
                            <button type="button" onclick="document.getElementById('image_url').value = '{{ $pr['path'] }}'"
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
                    <i class="fa-solid fa-save"></i>
                    <span>Simpan Data Hijab</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
