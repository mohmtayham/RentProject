<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $roles = null)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (! $roles) {
            return $next($request);
        }

        $allowed = explode('|', $roles);

        if (! in_array($user->user_type, $allowed)) {
            return response()->json([
                'error' => 'Access denied. Requires one of these roles: ' . implode(', ', $allowed)
            ], 403);
        }

        return $next($request);
    }
}
