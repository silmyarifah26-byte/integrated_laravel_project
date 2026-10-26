@extends('layouts.app', ['title' => 'Daftar Pemasok Tekstil & Hijab'])

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Pemasok Tekstil & Hijab</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Daftar mitra pabrik dan distributor pengadaan bahan atau stok hijab (Stock-In).</p>
        </div>

        @if(auth()->user()->isAdmin())
            <div>
                <a href="{{ route('suppliers.create') }}" class="inline-flex items-center px-3.5 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs transition-colors">
                    <i class="fa-solid fa-plus mr-1.5"></i> Tambah Pemasok
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
        <form action="{{ route('suppliers.index') }}" method="GET" class="w-full sm:max-w-md flex items-center space-x-2">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, telepon, atau alamat supplier..."
                class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            <button type="submit" class="px-3.5 py-2 rounded-md bg-gray-900 hover:bg-gray-800 text-white text-xs font-medium transition-colors">
                Cari
            </button>
            @if($search)
                <a href="{{ route('suppliers.index') }}" class="px-3 py-2 rounded-md border border-gray-300 text-gray-600 hover:bg-gray-50 text-xs font-medium transition-colors" title="Reset filter">
                    Reset
                </a>
            @endif
        </form>

        <span class="text-xs text-gray-500">
            Total: <strong>{{ $suppliers->total() }}</strong> Pemasok
        </span>
    </div>

    <!-- Suppliers Table -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-600 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 w-12 text-center">No</th>
                        <th class="px-4 py-3">Nama Supplier</th>
                        <th class="px-4 py-3">Kontak</th>
                        <th class="px-4 py-3">Alamat</th>
                        <th class="px-4 py-3 text-center">Riwayat Stock-In</th>
                        @if(auth()->user()->isAdmin())
                            <th class="px-4 py-3 text-center w-28">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($suppliers as $index => $sup)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-center font-medium text-gray-400">
                                {{ $suppliers->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-gray-900 text-sm">
                                {{ $sup->name }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                <div class="font-medium text-gray-800">{{ $sup->phone }}</div>
                                @if($sup->email)
                                    <div class="text-gray-400">{{ $sup->email }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs max-w-xs">
                                {{ $sup->address ?: '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $sup->purchases_count > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-gray-50 text-gray-400' }}">
                                    {{ $sup->purchases_count }} Transaksi
                                </span>
                            </td>
                            @if(auth()->user()->isAdmin())
                                <td class="px-4 py-3 text-center">
                                    <div class="inline-flex items-center space-x-1">
                                        <a href="{{ route('suppliers.edit', $sup) }}" class="p-1.5 rounded border border-yellow-200 text-yellow-700 bg-yellow-50 hover:bg-yellow-100 transition-colors" title="Ubah Pemasok">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </a>
                                        <form action="{{ route('suppliers.destroy', $sup) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pemasok {{ $sup->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded border border-red-200 text-red-700 bg-red-50 hover:bg-red-100 transition-colors" title="Hapus Pemasok">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isAdmin() ? 6 : 5 }}" class="px-4 py-8 text-center text-gray-400 text-sm">
                                Tidak ada data pemasok yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($suppliers->hasPages())
            <div class="p-3 border-t border-gray-200 bg-gray-50">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
