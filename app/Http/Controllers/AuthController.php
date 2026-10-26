<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the dedicated Customer login form.
     */
    public function showLoginForm(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Backward compatibility: redirect portal query params to separate routes
        if ($request->query('portal') === 'staff') {
            return redirect()->route('login.staff');
        }
        if ($request->query('portal') === 'admin') {
            return redirect()->route('login.admin');
        }

        return view('auth.login');
    }

    /**
     * Show the dedicated Staff login form (/staff/login).
     */
    public function showStaffLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login-staff');
    }

    /**
     * Show the dedicated Administrator login form (/admin/login).
     */
    public function showAdminLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login-admin');
    }

    /**
     * Process login authentication across Customer, Staff, and Admin portals.
     */
    public function login(Request $request): RedirectResponse
    {
        $portalRole = $request->input('portal_role');

        // 1. Passwordless Customer Login Flow
        if ($portalRole === 'customer') {
            $validated = $request->validate([
                'email' => ['required', 'email'],
                'name' => ['required', 'string', 'max:150'],
                'password' => ['nullable', 'string'],
            ], [
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'name.required' => 'Nama panggilan / lengkap wajib diisi.',
            ]);

            $user = User::where('email', $validated['email'])->first();

            if ($user) {
                // If user is Admin or Staff, require dedicated portal
                if (! $user->isCustomer()) {
                    $portalUrl = $user->isAdmin() ? '/admin/login' : '/staff/login';
                    return back()->withErrors([
                        'email' => 'Email ini terdaftar sebagai ' . strtoupper($user->role) . '. Silakan masuk melalui portal ' . ($user->isAdmin() ? 'Administrator' : 'Staff Toko') . " ({$portalUrl}) dengan kata sandi.",
                    ])->withInput($request->only('email', 'name'))->with('portal', 'customer');
                }

                // If existing account name differs from current login name,
                // update to the current customer's name so their identity is isolated
                if ($user->name !== $validated['name']) {
                    $user->update(['name' => $validated['name']]);
                }
            } else {
                // Automatically create new Customer account on first instant login
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make(Str::random(32)),
                    'role' => 'customer',
                ]);
            }

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', "Halo {$user->name}! 💕 Selamat datang di Diza's Plushie, silakan jelajahi koleksi boneka gemas kami!");
        }

        // 2. Standard Password Login Flow (Staff, Admin, or Automated Tests without portal_role)
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'portal_role' => ['nullable', 'string', 'in:customer,staff,admin'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $remember)) {
            /** @var User $user */
            $user = Auth::user();

            // Validate Role Alignment if portal_role is supplied
            if ($portalRole) {
                $isRoleMatched = false;

                if ($portalRole === 'customer' && $user->isCustomer()) {
                    $isRoleMatched = true;
                } elseif ($portalRole === 'staff' && $user->isStaff()) {
                    $isRoleMatched = true;
                } elseif ($portalRole === 'admin' && $user->isAdmin()) {
                    $isRoleMatched = true;
                }

                if (! $isRoleMatched) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    if ($user->isCustomer()) {
                        $errorMessage = 'Akun Anda bukan akun Admin/Staff. Silakan masuk melalui tab Portal Customer.';
                    } elseif ($user->isStaff()) {
                        if ($portalRole === 'admin') {
                            $errorMessage = 'Akun Anda bukan akun Administrator. Silakan masuk melalui tab Portal Staff.';
                        } else {
                            $errorMessage = 'Akun Anda terdaftar sebagai Staff Toko. Silakan masuk melalui tab Portal Staff.';
                        }
                    } elseif ($user->isAdmin()) {
                        $errorMessage = 'Akun Anda terdaftar sebagai Administrator. Silakan masuk melalui tab Portal Admin.';
                    } else {
                        $errorMessage = 'Role akun tidak sesuai dengan portal login yang dipilih.';
                    }

                    return back()->withErrors([
                        'email' => $errorMessage,
                    ])->withInput($request->only('email'))->with('portal', $portalRole);
                }
            }

            $request->session()->regenerate();

            if ($user->isAdmin() || $user->isStaff()) {
                return redirect()->intended(route('dashboard'))
                    ->with('success', "Selamat datang kembali, {$user->name}! Anda masuk sebagai " . strtoupper($user->role) . '.');
            }

            return redirect()->intended(route('dashboard'))
                ->with('success', "Selamat datang di Diza's Plushie, {$user->name}! Silakan jelajahi koleksi boneka kami.");
        }

        return back()->withErrors([
            'email' => 'Kredensial email atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email')->with('portal', $portalRole ?: 'customer');
    }

    /**
     * Show the customer registration form.
     */
    public function showRegisterForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Process customer registration.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', "Pendaftaran berhasil! Selamat datang di Diza's Plushie, {$user->name}.");
    }

    /**
     * Process logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', "Anda telah berhasil keluar (logout) dari sistem Diza's Plushie.");
    }
}
