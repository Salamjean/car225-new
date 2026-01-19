<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccueilController extends Controller
{
    public function about()
    {
        return view('home.pages.about');
    }

    public function destination()
    {
        return view('home.pages.destination');
    }
    public function compagny()
    {
        return view('home.pages.compagny');
    }
    public function infos()
    {
        return view('home.pages.infos');
    }
    public function services()
    {
        return view('home.pages.services');
    }
    public function contact()
    {
        return view('home.pages.contact');
    }
}
