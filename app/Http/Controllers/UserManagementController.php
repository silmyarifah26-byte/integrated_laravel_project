<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users with search, role filter, and pagination (Admin only).
     */
    public function index(Request $request): View
    {
        abort_if(! Auth::check() || ! Auth::user()->isAdmin(), 403, 'Akses Ditolak: Hanya Administrator yang berhak mengelola data pengguna sistem.');

        $search = $request->query('search');
        $role = $request->query('role');

        $users = User::withCount(['sales', 'customerSales', 'purchases'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($role, function ($query, $role) {
                $query->where('role', $role);
            })
            ->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users', 'search', 'role'));
    }

    /**
     * Show the form for creating a new user (Staff/Customer/Admin).
     */
    public function create(): View
    {
        abort_if(! Auth::check() || ! Auth::user()->isAdmin(), 403, 'Akses Ditolak: Hanya Administrator yang berhak mengelola data pengguna sistem.');

        return view('users.create');
    }

    /**
     * Store a newly created user with server-side validation.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_if(! Auth::check() || ! Auth::user()->isAdmin(), 403, 'Akses Ditolak: Hanya Administrator yang berhak mengelola data pengguna sistem.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', Rule::in(['admin', 'staff', 'customer'])],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'name.max' => 'Nama pengguna maksimal 150 karakter.',
            'email.required' => 'Alamat email pengguna wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar di sistem.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal terdiri dari 6 karakter.',
            'role.required' => 'Peran (Role) pengguna wajib dipilih.',
            'role.in' => 'Peran yang dipilih tidak valid. Pilih Admin, Staff, atau Customer.',
            'phone.max' => 'Nomor telepon maksimal 30 karakter.',
            'address.max' => 'Alamat maksimal 500 karakter.',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        $roleLabel = match ($user->role) {
            'admin' => 'Administrator',
            'staff' => 'Staff Toko',
            'customer' => 'Customer',
            default => strtoupper($user->role),
        };

        return redirect()->route('users.index')
            ->with('success', "Akun pengguna '{$user->name}' dengan hak akses {$roleLabel} berhasil dibuat.");
    }

    /**
     * Display the specified user detail or redirect to edit.
     */
    public function show(User $user): RedirectResponse
    {
        abort_if(! Auth::check() || ! Auth::user()->isAdmin(), 403, 'Akses Ditolak: Hanya Administrator yang berhak mengelola data pengguna sistem.');

        return redirect()->route('users.edit', $user);
    }

    /**
     * Show the form for editing the user profile and role.
     */
    public function edit(User $user): View
    {
        abort_if(! Auth::check() || ! Auth::user()->isAdmin(), 403, 'Akses Ditolak: Hanya Administrator yang berhak mengelola data pengguna sistem.');

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user with server-side validation.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        abort_if(! Auth::check() || ! Auth::user()->isAdmin(), 403, 'Akses Ditolak: Hanya Administrator yang berhak mengelola data pengguna sistem.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'string',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', Rule::in(['admin', 'staff', 'customer'])],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'name.max' => 'Nama pengguna maksimal 150 karakter.',
            'email.required' => 'Alamat email pengguna wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah digunakan pengguna lain.',
            'password.min' => 'Kata sandi baru minimal terdiri dari 6 karakter.',
            'role.required' => 'Peran (Role) pengguna wajib dipilih.',
            'role.in' => 'Peran yang dipilih tidak valid. Pilih Admin, Staff, atau Customer.',
            'phone.max' => 'Nomor telepon maksimal 30 karakter.',
            'address.max' => 'Alamat maksimal 500 karakter.',
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', "Data akun pengguna '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified user with integrity and self-deletion checks.
     */
    public function destroy(User $user): RedirectResponse
    {
        abort_if(! Auth::check() || ! Auth::user()->isAdmin(), 403, 'Akses Ditolak: Hanya Administrator yang berhak mengelola data pengguna sistem.');

        // 1. Prevent Admin from deleting their own active account
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.');
        }

        // 2. Prevent deletion if user has associated transactions
        $salesCount = $user->sales()->count();
        $purchasesCount = $user->purchases()->count();
        $customerSalesCount = $user->customerSales()->count();

        if ($salesCount > 0 || $purchasesCount > 0 || $customerSalesCount > 0) {
            $totalTx = $salesCount + $purchasesCount + $customerSalesCount;
            return back()->with('error', "Pengguna '{$user->name}' tidak dapat dihapus karena memiliki riwayat {$totalTx} transaksi di dalam sistem (Penjualan/Pembelian).");
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Akun pengguna '{$name}' berhasil dihapus dari sistem.");
    }
}
