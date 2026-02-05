<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CaisseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('caisse')->check()) {
            return redirect()->route('caisse.auth.login')
                ->with('error', 'Veuillez vous connecter pour accéder à cette page.');
        }

        // Check if caisse is archived
        $caisse = Auth::guard('caisse')->user();
        if ($caisse && $caisse->isArchived()) {
            Auth::guard('caisse')->logout();
            return redirect()->route('caisse.auth.login')
                ->with('error', 'Votre compte a été archivé. Contactez votre compagnie.');
        }

        return $next($request);
    }
}
