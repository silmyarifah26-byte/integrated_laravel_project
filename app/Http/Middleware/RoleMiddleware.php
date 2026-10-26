<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Support both comma-separated and multiple parameter roles
        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $r) {
                $trimmed = trim($r);
                if ($trimmed !== '') {
                    $allowedRoles[] = $trimmed;
                }
            }
        }

        if (! in_array($request->user()->role, $allowedRoles, true)) {
            abort(403, 'Akses Ditolak: Akun dengan role ' . strtoupper($request->user()->role) . ' tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
