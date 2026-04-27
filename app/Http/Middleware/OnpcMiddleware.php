<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Garde l'accès aux routes ONPC : redirige vers la page de connexion
 * ONPC si aucun agent n'est authentifié sur le guard `onpc`.
 */
class OnpcMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('onpc')->check()) {
            return $next($request);
        }

        return redirect()->route('onpc.login');
    }
}
