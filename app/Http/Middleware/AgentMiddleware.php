<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AgentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::guard('agent')->check()){
            return $next($request);
        }else{
            return redirect()->route('agent.login');
        }
    }
}