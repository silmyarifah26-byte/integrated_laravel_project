@extends('layouts.app', ['title' => 'Daftar Pemasok Boneka'])

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-industry"></i>
                </span>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pemasok & Pabrik Boneka</h1>
            </div>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">Daftar mitra pabrik produsen dan distributor pengadaan boneka (Stock-In).</p>
        </div>

        @if(auth()->user()->isAdmin())
            <div>
                <a href="{{ route('suppliers.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-sm transition-all duration-200">
                    <i class="fa-solid fa-plus mr-1.5"></i> Tambah Pemasok
                </a>
            </div>
        @else
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                <i class="fa-solid fa-eye mr-1.5"></i> Mode Akses: Hanya Lihat (Staff)
            </span>
        @endif
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form action="{{ route('suppliers.index') }}" method="GET" class="w-full sm:max-w-md relative flex items-center">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, telepon, atau alamat supplier..."
                class="w-full pl-10 pr-24 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-slate-50 focus:bg-white transition-all">
            @if($search)
                <a href="{{ route('suppliers.index') }}" class="absolute right-12 text-slate-400 hover:text-slate-600 text-xs font-bold" title="Hapus filter">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
            <button type="submit" class="absolute right-1.5 px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition-colors">
                Cari
            </button>
        </form>

        <span class="text-xs font-semibold text-slate-500">
            Total: <strong>{{ $suppliers->total() }}</strong> Pemasok
        </span>
    </div>

    <!-- Suppliers Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-extrabold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 w-12 text-center">No</th>
                        <th class="px-6 py-4">Nama Supplier</th>
                        <th class="px-6 py-4">Kontak (Telp & Email)</th>
                        <th class="px-6 py-4">Alamat Pabrik / Gudang</th>
                        <th class="px-6 py-4 text-center">Riwayat Stock-In</th>
                        @if(auth()->user()->isAdmin())
                            <th class="px-6 py-4 text-center w-36">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($suppliers as $index => $sup)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 text-center font-bold text-slate-400">
                                {{ $suppliers->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-extrabold text-slate-900 text-base block">
                                    {{ $sup->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 text-xs flex items-center">
                                    <i class="fa-solid fa-phone text-blue-500 mr-1.5 text-[10px]"></i> {{ $sup->phone }}
                                </div>
                                @if($sup->email)
                                    <div class="text-xs text-slate-500 flex items-center mt-0.5">
                                        <i class="fa-regular fa-envelope text-slate-400 mr-1.5 text-[10px]"></i> {{ $sup->email }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-xs max-w-xs">
                                {{ $sup->address ?: '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $sup->purchases_count > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-500' }}">
                                    <i class="fa-solid fa-truck-ramp-box mr-1.5 text-[10px]"></i>
                                    {{ $sup->purchases_count }} Transaksi
                                </span>
                            </td>
                            @if(auth()->user()->isAdmin())
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex items-center space-x-1.5">
                                        <a href="{{ route('suppliers.edit', $sup) }}" class="p-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 transition-colors" title="Ubah Pemasok">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </a>
                                        <form action="{{ route('suppliers.destroy', $sup) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pemasok {{ $sup->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition-colors" title="Hapus Pemasok">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isAdmin() ? 6 : 5 }}" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-industry text-4xl mb-2 text-slate-300"></i>
                                <p class="font-semibold">Tidak ada pemasok yang ditemukan.</p>
                                @if($search)
                                    <p class="text-xs text-slate-400 mt-1">Coba kata kunci lain atau bersihkan kotak pencarian.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        @if($suppliers->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
