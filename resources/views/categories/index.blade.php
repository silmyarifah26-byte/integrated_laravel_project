@extends('layouts.app', ['title' => 'Daftar Kategori Hijab'])

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Kategori Hijab</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Klasifikasi kategori untuk mengelompokkan koleksi hijab, pashmina, dan khimar.</p>
        </div>

        @if(auth()->user()->isAdmin())
            <div>
                <a href="{{ route('categories.create') }}" class="inline-flex items-center px-3.5 py-2 rounded-md bg-rose-600 hover:bg-rose-700 text-white font-medium text-xs transition-colors">
                    <i class="fa-solid fa-plus mr-1.5"></i> Tambah Kategori
                </a>
            </div>
        @else
            <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                Mode Akses: Hanya Lihat (Staff)
            </span>
        @endif
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form action="{{ route('categories.index') }}" method="GET" class="w-full sm:max-w-md flex items-center space-x-2">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau deskripsi kategori..."
                class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500">
            <button type="submit" class="px-3.5 py-2 rounded-md bg-gray-900 hover:bg-gray-800 text-white text-xs font-medium transition-colors">
                Cari
            </button>
            @if($search)
                <a href="{{ route('categories.index') }}" class="px-3 py-2 rounded-md border border-gray-300 text-gray-600 hover:bg-gray-50 text-xs font-medium transition-colors" title="Reset filter">
                    Reset
                </a>
            @endif
        </form>

        <span class="text-xs text-gray-500">
            Total: <strong>{{ $categories->total() }}</strong> Kategori
        </span>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-600 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 w-12 text-center">No</th>
                        <th class="px-4 py-3">Nama Kategori</th>
                        <th class="px-4 py-3">Deskripsi</th>
                        <th class="px-4 py-3 text-center">Jumlah Produk</th>
                        @if(auth()->user()->isAdmin())
                            <th class="px-4 py-3 text-center w-28">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($categories as $index => $cat)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-center font-medium text-gray-400">
                                {{ $categories->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-gray-900 text-sm">
                                {{ $cat->name }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs max-w-md">
                                {{ $cat->description ?: '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $cat->products_count > 0 ? 'bg-gray-100 text-gray-700 border border-gray-200' : 'bg-gray-50 text-gray-400' }}">
                                    {{ $cat->products_count }} Produk
                                </span>
                            </td>
                            @if(auth()->user()->isAdmin())
                                <td class="px-4 py-3 text-center">
                                    <div class="inline-flex items-center space-x-1">
                                        <a href="{{ route('categories.edit', $cat) }}" class="p-1.5 rounded border border-yellow-200 text-yellow-700 bg-yellow-50 hover:bg-yellow-100 transition-colors" title="Ubah Kategori">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </a>
                                        <form action="{{ route('categories.destroy', $cat) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $cat->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded border border-red-200 text-red-700 bg-red-50 hover:bg-red-100 transition-colors" title="Hapus Kategori">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isAdmin() ? 5 : 4 }}" class="px-4 py-8 text-center text-gray-400 text-sm">
                                Tidak ada kategori yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($categories->hasPages())
            <div class="p-3 border-t border-gray-200 bg-gray-50">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
