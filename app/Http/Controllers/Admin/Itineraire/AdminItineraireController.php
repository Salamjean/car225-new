<?php

namespace App\Http\Controllers\Admin\Itineraire;

use App\Http\Controllers\Controller;
use App\Models\Itineraire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminItineraireController extends Controller
{
    /**
     * Afficher la liste des itinéraires
     */
    public function index(Request $request)
    {
        $query = Itineraire::query();
        
        // Recherche
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('point_depart', 'like', "%{$search}%")
                ->orWhere('point_arrive', 'like', "%{$search}%")
                ->orWhere('durer_parcours', 'like', "%{$search}%");
            });
        }
        
        $itineraires = $query->latest()->paginate(10);
        
        return view('admin.itineraire.index', compact('itineraires'));
    }
}
