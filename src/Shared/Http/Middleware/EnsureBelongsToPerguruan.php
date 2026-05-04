<?php

declare(strict_types=1);

namespace Src\Shared\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBelongsToPerguruan
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Super admin boleh akses semua
        if ($user?->hasRole('super_admin')) {
            return $next($request);
        }

        // User lain wajib punya perguruan_id
        if (! $user?->perguruan_id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun tidak terhubung ke perguruan manapun.',
                ], 403);
            }

            abort(403, 'Akun tidak terhubung ke perguruan manapun.');
        }

        return $next($request);
    }
}
