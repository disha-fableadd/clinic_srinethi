<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class RoleMiddleware
{
       public function handle(Request $request, Closure $next, ...$roles)
    {
        
        $user = Auth::user();

        if (!$user || !in_array($user->role->name, $roles)) {
            return response()->json(['message' => 'Unauthorized'], 403); // Forbidden if role doesn't match
        }

        return $next($request);
    }
}
