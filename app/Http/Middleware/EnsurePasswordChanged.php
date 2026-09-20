<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && ! $user->isAdmin() && $user->harus_ganti_password) {
            // Izinkan route untuk ganti password wajib dan logout
            if ($request->routeIs('password.ganti-wajib', 'password.ganti-wajib.update', 'logout')) {
                return $next($request);
            }

            return redirect()->route('password.ganti-wajib');
        }

        return $next($request);
    }
}
