<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Admin bypass semua role
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Jika role cocok lanjutkan
        if ($user->role === $role) {
            return $next($request);
        }

        // Jika role tidak cocok, arahkan pengguna ke dashboard sesuai peran
        switch ($user->role) {
            case 'mentor':
                return redirect()->route('mentor.index');
            case 'user':
                return redirect()->route('dashboard');
            default:
                return redirect()->route('login');
        }
    }
}