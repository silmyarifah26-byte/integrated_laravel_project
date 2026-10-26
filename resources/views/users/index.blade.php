@extends('layouts.app', ['title' => 'Manajemen Pengguna & Hak Akses'])

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-users-gear"></i>
                </span>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Pengguna & Role</h1>
            </div>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">Kelola akun Admin, Staff operasional toko, serta akun Pelanggan terdaftar.</p>
        </div>

        <div>
            <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-sm shadow-sm transition-all duration-200">
                <i class="fa-solid fa-user-plus mr-1.5"></i> Tambah Pengguna Baru
            </a>
        </div>
    </div>

    <!-- Search & Role Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form action="{{ route('users.index') }}" method="GET" class="flex-1 grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-8 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, atau no telepon..."
                    class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-slate-50 focus:bg-white transition-all">
            </div>

            <div class="sm:col-span-4 flex items-center space-x-2">
                <select name="role" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-slate-50 focus:bg-white transition-all">
                    <option value="">Semua Peran (Role)</option>
                    <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="staff" {{ $role === 'staff' ? 'selected' : '' }}>Staff</option>
                    <option value="customer" {{ $role === 'customer' ? 'selected' : '' }}>Customer</option>
                </select>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs shadow-sm transition-colors">
                    Filter
                </button>
                @if($search || $role)
                    <a href="{{ route('users.index') }}" class="py-2 px-3 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-600 text-xs font-bold transition-colors" title="Reset filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>

        <span class="text-xs font-semibold text-slate-500 whitespace-nowrap">
            Total: <strong>{{ $users->total() }}</strong> Pengguna
        </span>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-extrabold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 w-12 text-center">No</th>
                        <th class="px-6 py-4">Nama Pengguna</th>
                        <th class="px-6 py-4">Alamat Email</th>
                        <th class="px-6 py-4 text-center">Peran (Role)</th>
                        <th class="px-6 py-4">Kontak & Alamat</th>
                        <th class="px-6 py-4 text-center">Aktivitas Transaksi</th>
                        <th class="px-6 py-4 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $index => $u)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 text-center font-bold text-slate-400">
                                {{ $users->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-extrabold text-slate-900 text-base block">
                                    {{ $u->name }}
                                </span>
                                @if($u->id === auth()->id())
                                    <span class="inline-flex items-center text-[10px] font-bold text-emerald-600 mt-0.5">
                                        <i class="fa-solid fa-circle text-[8px] mr-1"></i> Akun Anda Saat Ini
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-700">
                                {{ $u->email }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($u->isAdmin())
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-purple-100 text-purple-800 border border-purple-300">
                                        <i class="fa-solid fa-crown mr-1 text-[10px]"></i> ADMIN
                                    </span>
                                @elseif($u->isStaff())
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                                        <i class="fa-solid fa-user-gear mr-1 text-[10px]"></i> STAFF
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800 border border-rose-300">
                                        <i class="fa-solid fa-heart mr-1 text-[10px]"></i> CUSTOMER
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                <div><strong class="text-slate-800">Telp:</strong> {{ $u->phone ?: '-' }}</div>
                                <div class="text-slate-400 truncate max-w-xs">{{ $u->address ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center text-xs">
                                @if($u->isAdmin() || $u->isStaff())
                                    <span class="text-slate-700 font-semibold">
                                        {{ $u->sales_count }} Jual &bull; {{ $u->purchases_count }} Beli
                                    </span>
                                @else
                                    <span class="text-slate-700 font-semibold">
                                        {{ $u->customer_sales_count }} Pesanan
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center space-x-1.5">
                                    <a href="{{ route('users.edit', $u) }}" class="p-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 transition-colors" title="Ubah Pengguna">
                                        <i class="fa-solid fa-user-pen text-xs"></i>
                                    </a>
                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $u) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $u->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition-colors" title="Hapus Pengguna">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-users text-4xl mb-2 text-slate-300"></i>
                                <p class="font-semibold">Tidak ada pengguna yang sesuai kriteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        @if($users->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
