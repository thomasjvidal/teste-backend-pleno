<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = auth('api')->user();
        if (!$user || $user->role !== $role) {
            return response()->json([
                'error' => 'Acesso negado. Permissão insuficiente.',
                'required_role' => $role,
                'user_role' => $user ? $user->role : 'não autenticado'
            ], 403);
        }

        return $next($request);
    }
} 