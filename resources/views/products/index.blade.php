@extends('layouts.app', ['title' => 'Manajemen Data Produk Hijab'])

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Master Produk Hijab</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                Kelola daftar produk hijab, penetapan harga beli/jual, dan batas minimum stok.
            </p>
        </div>

        <div class="flex items-center space-x-2">
            @if(auth()->user()->isAdmin())
                <a href="{{ route('products.create') }}" class="inline-flex items-center px-3.5 py-2 rounded-md bg-rose-600 hover:bg-rose-700 text-white font-medium text-xs transition-colors">
                    <i class="fa-solid fa-plus mr-1.5"></i> Tambah Hijab Baru
                </a>
            @else
                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                    Mode Akses: Hanya Lihat (Staff)
                </span>
            @endif
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
        <form action="{{ route('products.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- Search Keyword -->
            <div class="sm:col-span-5">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, SKU, ukuran, atau bahan..."
                    class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500">
            </div>

            <!-- Filter Category -->
            <div class="sm:col-span-3">
                <select name="category_id" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Low Stock Checkbox -->
            <div class="sm:col-span-2 flex items-center">
                <label class="flex items-center space-x-2 cursor-pointer select-none text-xs font-medium text-gray-700 bg-gray-50 p-2 rounded-md border border-gray-200 w-full">
                    <input type="checkbox" name="low_stock" value="1" {{ $lowStock === '1' ? 'checked' : '' }}
                        class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500 border-gray-300">
                    <span>Hanya Low Stock</span>
                </label>
            </div>

            <!-- Filter Action Buttons -->
            <div class="sm:col-span-2 flex items-center space-x-2">
                <button type="submit" class="w-full py-2 px-3 rounded-md bg-gray-900 hover:bg-gray-800 text-white text-xs font-medium transition-colors">
                    Filter
                </button>
                @if($search || $categoryId || $lowStock)
                    <a href="{{ route('products.index') }}" class="py-2 px-3 rounded-md border border-gray-300 hover:bg-gray-50 text-gray-600 text-xs font-medium transition-colors" title="Reset filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
            <span>Menampilkan <strong>{{ $products->count() }}</strong> dari <strong>{{ $products->total() }}</strong> total produk</span>
            @if($lowStock === '1')
                <span class="text-red-700 font-medium bg-red-50 px-2 py-0.5 rounded border border-red-200">
                    Filter Low Stock Aktif
                </span>
            @endif
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-600 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 w-12 text-center">No</th>
                        <th class="px-4 py-3">SKU & Hijab</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Spesifikasi</th>
                        <th class="px-4 py-3 text-right">Harga Beli</th>
                        <th class="px-4 py-3 text-right">Harga Jual</th>
                        <th class="px-4 py-3 text-center">Stok / Min</th>
                        <th class="px-4 py-3 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($products as $index => $prod)
                        <tr class="hover:bg-gray-50 {{ $prod->stock <= $prod->minimum_stock ? 'bg-red-50/20' : '' }}">
                            <td class="px-4 py-3 text-center text-gray-400 font-medium">
                                {{ $products->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ asset($prod->image_path) }}" 
                                         alt="{{ $prod->name }}" 
                                         class="w-12 h-12 object-cover object-center rounded-md border border-gray-200 flex-shrink-0">
                                    <div>
                                        <a href="{{ route('products.show', $prod) }}" class="font-semibold text-gray-900 hover:text-rose-600 transition-colors block text-sm">
                                            {{ $prod->name }}
                                        </a>
                                        <span class="text-xs text-gray-400 font-mono">{{ $prod->sku }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700 border border-gray-200">
                                    {{ $prod->category->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                <div>Ukuran: <span class="text-gray-800">{{ $prod->size ?? '-' }}</span></div>
                                <div class="text-gray-500 truncate max-w-xs" title="{{ $prod->material }}">
                                    Bahan: {{ $prod->material ?? '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-600">
                                Rp {{ number_format($prod->purchase_price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900">
                                Rp {{ number_format($prod->selling_price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($prod->stock <= $prod->minimum_stock)
                                    <div class="inline-flex flex-col items-center">
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-800">
                                            {{ $prod->stock }} pcs
                                        </span>
                                        <span class="text-[10px] text-red-600 mt-0.5">Min: {{ $prod->minimum_stock }}</span>
                                    </div>
                                @else
                                    <div class="inline-flex flex-col items-center">
                                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            {{ $prod->stock }} pcs
                                        </span>
                                        <span class="text-[10px] text-gray-400 mt-0.5">Min: {{ $prod->minimum_stock }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center space-x-1">
                                    <a href="{{ route('products.show', $prod) }}" class="p-1.5 rounded border border-gray-200 text-gray-600 hover:bg-gray-100 transition-colors" title="Lihat Detail">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('purchases.create', ['product_id' => $prod->id]) }}" class="p-1.5 rounded border border-blue-200 text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors" title="Restock Stok">
                                        <i class="fa-solid fa-truck-ramp-box text-xs"></i>
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('products.edit', $prod) }}" class="p-1.5 rounded border border-yellow-200 text-yellow-700 bg-yellow-50 hover:bg-yellow-100 transition-colors" title="Edit Produk">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </a>
                                        <form action="{{ route('products.destroy', $prod) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk {{ $prod->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded border border-red-200 text-red-700 bg-red-50 hover:bg-red-100 transition-colors" title="Hapus Produk">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400 text-sm">
                                Tidak ada produk hijab yang sesuai dengan kriteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="p-3 border-t border-gray-200 bg-gray-50">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
