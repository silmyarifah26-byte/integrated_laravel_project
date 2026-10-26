@extends('layouts.app', ['title' => 'Manajemen Pengguna & Hak Akses'])

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Manajemen Pengguna & Hak Akses</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Kelola akun Admin, Staff operasional toko, dan Customer terdaftar.</p>
        </div>

        <div>
            <a href="{{ route('users.create') }}" class="inline-flex items-center px-3.5 py-2 rounded-md bg-purple-600 hover:bg-purple-700 text-white font-medium text-xs transition-colors">
                <i class="fa-solid fa-user-plus mr-1.5"></i> Tambah Pengguna Baru
            </a>
        </div>
    </div>

    <!-- Search & Role Filter Bar -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form action="{{ route('users.index') }}" method="GET" class="flex-1 grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-8">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, atau no telepon..."
                    class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-purple-500">
            </div>

            <div class="sm:col-span-4 flex items-center space-x-2">
                <select name="role" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Semua Peran</option>
                    <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="staff" {{ $role === 'staff' ? 'selected' : '' }}>Staff</option>
                    <option value="customer" {{ $role === 'customer' ? 'selected' : '' }}>Customer</option>
                </select>
                <button type="submit" class="px-3.5 py-2 rounded-md bg-gray-900 hover:bg-gray-800 text-white text-xs font-medium transition-colors">
                    Filter
                </button>
                @if($search || $role)
                    <a href="{{ route('users.index') }}" class="px-3 py-2 rounded-md border border-gray-300 hover:bg-gray-50 text-gray-600 text-xs font-medium transition-colors" title="Reset filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <span class="text-xs text-gray-500 whitespace-nowrap">
            Total: <strong>{{ $users->total() }}</strong> Pengguna
        </span>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-600 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 w-12 text-center">No</th>
                        <th class="px-4 py-3">Nama Pengguna</th>
                        <th class="px-4 py-3">Alamat Email</th>
                        <th class="px-4 py-3 text-center">Peran</th>
                        <th class="px-4 py-3">Kontak & Alamat</th>
                        <th class="px-4 py-3 text-center">Aktivitas</th>
                        <th class="px-4 py-3 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $index => $u)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-center font-medium text-gray-400">
                                {{ $users->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-gray-900 text-sm block">
                                    {{ $u->name }}
                                </span>
                                @if($u->id === auth()->id())
                                    <span class="text-[11px] text-green-600 font-medium">(Akun Anda)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                {{ $u->email }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($u->isAdmin())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                                        ADMIN
                                    </span>
                                @elseif($u->isStaff())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                        STAFF
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                        CUSTOMER
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                <div>Telp: {{ $u->phone ?: '-' }}</div>
                                <div class="text-gray-400 truncate max-w-xs">{{ $u->address ?: '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-center text-xs text-gray-600">
                                @if($u->isAdmin() || $u->isStaff())
                                    <span>{{ $u->sales_count }} Jual &bull; {{ $u->purchases_count }} Beli</span>
                                @else
                                    <span>{{ $u->customer_sales_count }} Pesanan</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center space-x-1">
                                    <a href="{{ route('users.edit', $u) }}" class="p-1.5 rounded border border-yellow-200 text-yellow-700 bg-yellow-50 hover:bg-yellow-100 transition-colors" title="Ubah Pengguna">
                                        <i class="fa-solid fa-user-pen text-xs"></i>
                                    </a>
                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $u) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $u->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded border border-red-200 text-red-700 bg-red-50 hover:bg-red-100 transition-colors" title="Hapus Pengguna">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400 text-sm">
                                Tidak ada data pengguna yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="p-3 border-t border-gray-200 bg-gray-50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
