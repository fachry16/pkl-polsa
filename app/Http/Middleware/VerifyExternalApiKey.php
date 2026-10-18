<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyExternalApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $configuredKey = config('services.eduva_api.key');

        if (empty($configuredKey)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'API eksternal Eduva belum dikonfigurasi di server.',
            ], 503);
        }

        $providedKey = $request->header('X-API-KEY') ?? $request->bearerToken();

        if (! $providedKey || ! hash_equals($configuredKey, (string) $providedKey)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Unauthorized: API Key tidak valid.',
            ], 401);
        }

        return $next($request);
    }
}
