<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Compagnie;
use App\Models\Convoi;
use App\Models\Itineraire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ConvoiController extends Controller
{
    public function index()
    {
        $convois = Convoi::with(['compagnie', 'itineraire'])
            ->withCount('passagers')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('user.convoi.index', compact('convois'));
    }

    public function create()
    {
        $compagnies = Compagnie::where('statut', 'actif')
            ->orderBy('name')
            ->get(['id', 'name', 'sigle']);

        return view('user.convoi.create', compact('compagnies'));
    }

    public function itinerairesByCompagnie($compagnieId)
    {
        $itineraires = Itineraire::where('compagnie_id', $compagnieId)
            ->orderBy('point_depart')
            ->orderBy('point_arrive')
            ->get(['id', 'point_depart', 'point_arrive']);

        return response()->json([
            'itineraires' => $itineraires->map(function ($it) {
                return [
                    'id' => $it->id,
                    'label' => $it->point_depart . ' -> ' . $it->point_arrive,
                ];
            }),
        ]);
    }

    public function stepTwo(Request $request)
    {
        $validated = $request->validate([
            'compagnie_id' => 'required|exists:compagnies,id',
            'itineraire_id' => [
                'required',
                Rule::exists('itineraires', 'id')->where(function ($query) use ($request) {
                    return $query->where('compagnie_id', $request->compagnie_id);
                }),
            ],
            'nombre_personnes' => 'required|integer|min:1|max:100',
        ], [
            'compagnie_id.required' => 'Veuillez choisir une compagnie.',
            'itineraire_id.required' => 'Veuillez choisir un itinéraire.',
            'nombre_personnes.required' => 'Veuillez indiquer le nombre de personnes.',
        ]);

        session([
            'convoi.draft' => [
                'compagnie_id' => (int) $validated['compagnie_id'],
                'itineraire_id' => (int) $validated['itineraire_id'],
                'nombre_personnes' => (int) $validated['nombre_personnes'],
            ],
        ]);

        return redirect()->route('user.convoi.passengers');
    }

    public function passengers()
    {
        $draft = session('convoi.draft');
        if (!$draft) {
            return redirect()->route('user.convoi.create')
                ->with('error', 'Veuillez d abord choisir la compagnie et le nombre de personnes.');
        }

        $compagnie = Compagnie::findOrFail($draft['compagnie_id']);
        $itineraire = Itineraire::find($draft['itineraire_id']);
        $nombrePersonnes = (int) $draft['nombre_personnes'];

        return view('user.convoi.passengers', compact('compagnie', 'itineraire', 'nombrePersonnes'));
    }

    public function store(Request $request)
    {
        $draft = session('convoi.draft');
        if (!$draft) {
            return redirect()->route('user.convoi.create')
                ->with('error', 'Session expirée. Veuillez recommencer.');
        }

        $nombrePersonnes = (int) $draft['nombre_personnes'];

        $rules = [
            'passagers' => 'required|array|size:' . $nombrePersonnes,
        ];

        for ($i = 0; $i < $nombrePersonnes; $i++) {
            $rules["passagers.$i.nom"] = 'required|string|max:100';
            $rules["passagers.$i.prenoms"] = 'required|string|max:150';
            $rules["passagers.$i.contact"] = 'required|string|max:30';
            $rules["passagers.$i.email"] = 'nullable|email|max:150';
        }

        $validated = $request->validate($rules, [
            'passagers.required' => 'Veuillez renseigner les informations des passagers.',
        ]);

        DB::transaction(function () use ($draft, $validated, $nombrePersonnes) {
            $itineraire = Itineraire::find($draft['itineraire_id'] ?? null);

            $convoi = Convoi::create([
                'user_id' => Auth::id(),
                'compagnie_id' => $draft['compagnie_id'],
                'itineraire_id' => $draft['itineraire_id'] ?? null,
                'gare_id' => $itineraire?->gare_id,
                'nombre_personnes' => $nombrePersonnes,
                'reference' => 'CONV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'statut' => 'en_attente',
            ]);

            foreach ($validated['passagers'] as $passager) {
                $convoi->passagers()->create([
                    'nom' => $passager['nom'],
                    'prenoms' => $passager['prenoms'],
                    'contact' => $passager['contact'],
                    'email' => $passager['email'] ?? null,
                ]);
            }
        });

        session()->forget('convoi.draft');

        return redirect()->route('user.convoi.create')
            ->with('success', 'Convoi enregistré avec succès. Aucune opération de paiement n a été effectuée.');
    }

    public function show(Convoi $convoi)
    {
        if ($convoi->user_id !== Auth::id()) {
            abort(403);
        }

        $convoi->load(['compagnie', 'itineraire', 'passagers']);

        return view('user.convoi.show', compact('convoi'));
    }
}

