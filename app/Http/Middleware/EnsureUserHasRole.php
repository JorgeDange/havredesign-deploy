<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Uso: ->middleware('role:ADMIN') — laravel.md B3.
 * Aplica-se ao painel (ADMIN, prefixo configuravel via ADMIN_PATH) e a /conta
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($roles !== [] && ! in_array($user->role, $roles, true)) {
            abort(403, 'Acesso negado.');
        }

        return $next($request);
    }
}
