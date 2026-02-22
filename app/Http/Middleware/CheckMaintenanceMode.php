<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        // Si le mode maintenance est activé
        if (Setting::isMaintenanceMode()) {
            // Laisser passer les admins
            if (Auth::guard('admin')->check()) {
                return $next($request);
            }

            // Laisser passer les routes admin (login etc.)
            if ($request->is('admin/*') || $request->is('admin')) {
                return $next($request);
            }

            // Laisser passer la route de maintenance elle-même
            if ($request->is('maintenance')) {
                return $next($request);
            }

            // Laisser passer les assets
            if ($request->is('assetsPoster/*') || $request->is('assets/*') || $request->is('storage/*')) {
                return $next($request);
            }

            // Bloquer tout le reste
            return response()->view('maintenance', [
                'message' => Setting::getMaintenanceMessage()
            ], 503);
        }

        return $next($request);
    }
}
