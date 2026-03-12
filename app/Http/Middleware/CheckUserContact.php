<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserContact
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si l'utilisateur est connecté mais n'a pas de contact
        // On le redirige vers la page de complétion de profil
        // Sauf s'il est déjà sur une route autorisée (complete-profile, logout, etc.)
        if (Auth::check() && empty(Auth::user()->contact)) {
            $allowedRoutes = [
                'user.complete-profile',
                'user.update-contact',
                'user.logout',
            ];

            if (!$request->routeIs($allowedRoutes)) {
                return redirect()->route('user.complete-profile')
                    ->with('warning', 'Veuillez renseigner votre numéro de téléphone avant de continuer.');
            }
        }

        return $next($request);
    }
}
