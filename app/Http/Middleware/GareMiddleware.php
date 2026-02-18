<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class GareMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::guard('gare')->check()){
            return $next($request);
        }else{
            return redirect()->route('gare-espace.login');
        }
    }
}
