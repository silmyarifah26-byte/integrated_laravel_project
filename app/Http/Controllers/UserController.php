<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users with search, role filter, and pagination (Admin only).
     */
    public function index(Request $request): View
    {
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
     * Show the form for creating a new user (Admin only).
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * Store a newly created user (Admin only).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', Rule::in(['admin', 'staff', 'customer'])],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email pengguna wajib diisi.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'role.required' => 'Peran (Role) pengguna wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', "Pengguna '{$validated['name']}' dengan role " . strtoupper($validated['role']) . " berhasil dibuat.");
    }

    /**
     * Show the form for editing the user (Admin only).
     */
    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user (Admin only).
     */
    public function update(Request $request, User $user): RedirectResponse
    {
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
            'email.required' => 'Email pengguna wajib diisi.',
            'email.unique' => 'Alamat email ini sudah digunakan pengguna lain.',
            'password.min' => 'Password baru minimal terdiri dari 6 karakter.',
            'role.required' => 'Peran (Role) pengguna wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.',
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', "Data pengguna '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified user (Admin only).
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $hasTransactions = $user->sales()->exists() || $user->purchases()->exists() || $user->customerSales()->exists();

        if ($hasTransactions) {
            return back()->with('error', "Pengguna '{$user->name}' tidak dapat dihapus karena sudah memiliki riwayat transaksi penjualan atau pembelian di dalam sistem.");
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Pengguna '{$name}' berhasil dihapus.");
    }
}
