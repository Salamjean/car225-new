<?php

namespace App\Http\Controllers\Compagnie;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompagnieDashboard extends Controller
{
    public function dashboard(){
        return view('compagnie.dashboard');
    }

    public function logout()
    {
        Auth::guard('compagnie')->logout();
        return redirect()->route('compagnie.login');
    }
}
