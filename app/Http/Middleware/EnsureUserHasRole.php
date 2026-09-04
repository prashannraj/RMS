<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * Usage: role:admin  |  role:admin,manager
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if (empty($roles) || $user->hasAnyRole($roles)) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Forbidden. You do not have the required role.',
        ], 403);
    }
}