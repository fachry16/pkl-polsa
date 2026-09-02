<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (! auth()->check()) {
            abort(403);
        }

        $user = auth()->user();

        foreach ($roles as $role) {
            $r = strtolower($role);
            if ($r === 'kaprodi') {
                if ($user->isAdmin() || $user->isKaprodi()) {
                    return $next($request);
                }
            } elseif ($r === 'dosen') {
                if ($user->isDosen()) {
                    return $next($request);
                }
            } elseif ($r === 'direktur') {
                if ($user->isDirektur()) {
                    return $next($request);
                }
            } elseif ($r === 'admin') {
                if ($user->isAdmin()) {
                    return $next($request);
                }
            } elseif ($user->hasRole($r) || $user->role === $role) {
                return $next($request);
            }
        }

        abort(403);
    }
}
