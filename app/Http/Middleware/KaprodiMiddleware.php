<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KaprodiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            abort(403);
        }
        if (
            auth()->user()->role === 'admin' ||
            auth()->user()->isKaprodi()
        ) {
            return $next($request);
        }
        abort(403);
    }
}
