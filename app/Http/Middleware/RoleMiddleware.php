<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            abort(403);
        }

        $user = auth()->user();

        foreach ($roles as $role) {
            if (strtolower($role) === 'kaprodi') {
                if ($user->role === 'admin' || $user->isKaprodi()) {
                    return $next($request);
                }
            } elseif ($user->role === $role) {
                return $next($request);
            }
        }

        abort(403);
    }
}