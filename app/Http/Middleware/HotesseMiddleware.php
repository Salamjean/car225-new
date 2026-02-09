<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HotesseMiddleware
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
        if (!Auth::guard('hotesse')->check()) {
            return redirect()->route('hotesse.auth.login')
                ->with('error', 'Veuillez vous connecter pour accéder à cette page.');
        }

        // Check if hotesse is archived
        $hotesse = Auth::guard('hotesse')->user();
        if ($hotesse && $hotesse->isArchived()) {
            Auth::guard('hotesse')->logout();
            return redirect()->route('hotesse.auth.login')
                ->with('error', 'Votre compte a été archivé. Contactez votre compagnie.');
        }

        return $next($request);
    }
}
