<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\AgentRequest;
use App\Models\Agent;
use App\Models\ResetCodePasswordAgent;
use App\Notifications\SendEmailToAgentAfterRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class AgentController extends Controller
{
    public function index()
    {
        $compagnie = Auth::guard('compagnie')->user();
        $agents = Agent::where('compagnie_id', $compagnie->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('compagnie.agent.index', compact('agents'));
    }
    public function create()
    {
        return view('compagnie.agent.create');
    }

    public function store(AgentRequest $request)
    {
        try {
            DB::beginTransaction();

            $compagnie = Auth::guard('compagnie')->user();

            $existingAgent = Agent::where('email', $request->email)->first();
            if ($existingAgent) {
                return redirect()->back()->withErrors(['email' => 'Cet email est déjà utilisé.'])->withInput();
            }

            $agent = new Agent();
            $agent->name = $request->name;
            $agent->prenom = $request->prenom;
            $agent->email = $request->email;
            $agent->contact = $request->contact;
            $agent->cas_urgence = $request->cas_urgence;
            $agent->password = Hash::make('default');

            if ($request->hasFile('profile_picture')) {
                $request->validate([
                    'profile_picture' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
                ]);

                $agent->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
            }

            $agent->commune = $request->commune;
            $agent->compagnie_id = $compagnie->id;

            $agent->save();

            // Envoi de l'e-mail de vérification
            ResetCodePasswordAgent::where('email', $agent->email)->delete();
            $code1 = rand(1000, 4000);
            $code = $code1 . '' . $agent->id;

            ResetCodePasswordAgent::create([
                'code' => $code,
                'email' => $agent->email,
            ]);

            Notification::route('mail', $agent->email)
                ->notify(new SendEmailToAgentAfterRegistrationNotification($code, $agent->email));
            DB::commit();

            return redirect()->route('compagnie.agents.index')->with('success', 'L\'agent a bien été enregistré avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'enregistrement de l\'agent: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement. Veuillez réessayer.'])->withInput();
        }
    }

    public function show(Agent $agent)
    {
        return view('compagnie.agent.show', compact('agent'));
    }

    public function edit(Agent $agent)
    {
        return view('compagnie.agent.edit', compact('agent'));
    }

    public function update(Request $request, Agent $agent)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|unique:agents,email,' . $agent->id,
                'contact' => 'required|string|max:20',
                'cas_urgence' => 'required|string|max:20',
                'commune' => 'required|string|max:255',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Gérer l'upload de l'image
            if ($request->hasFile('profile_picture')) {
                // Supprimer l'ancienne image si elle existe
                if ($agent->profile_picture) {
                    Storage::delete($agent->profile_picture);
                }

                $imagePath = $request->file('profile_picture')->store('agents', 'public');
                $validated['profile_picture'] = $imagePath;
            }

            $agent->update($validated);

            return redirect()->route('compagnie.agents.index')
                ->with('success', 'Agent modifié avec succès!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la modification de l\'agent: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function archive(Request $request, Agent $agent)
    {
        try {
            $agent->update(['archived_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Agent archivé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'archivage'
            ], 500);
        }
    }

    public function unarchive(Request $request, Agent $agent)
    {
        try {
            $agent->update(['archived_at' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Agent désarchivé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du désarchivage'
            ], 500);
        }
    }
}
