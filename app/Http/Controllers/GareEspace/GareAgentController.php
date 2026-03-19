<?php

namespace App\Http\Controllers\GareEspace;

use App\Http\Controllers\Controller;
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
use Illuminate\Support\Str;

class GareAgentController extends Controller
{
    public function index()
    {
        $gare = Auth::guard('gare')->user();
        $agents = Agent::where('gare_id', $gare->id)
            ->whereNull('archived_at')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $agentsArchivedCount = Agent::where('gare_id', $gare->id)->whereNotNull('archived_at')->count();

        return view('gare-espace.agent.index', compact('agents', 'agentsArchivedCount'));
    }

    public function create()
    {
        return view('gare-espace.agent.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:255',
            'prenom' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:agents,email',
            'contact' => 'required|string|max:15',
            'commune' => 'required|string|max:255',
            'cas_urgence' => 'required|string|max:15',
            'nom_urgence' => 'required|string|max:255',
            'lien_parente_urgence' => 'required|string|max:100',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        try {
            DB::beginTransaction();
            $gare = Auth::guard('gare')->user();

            $agent = new Agent();
            $agent->name = $request->name;
            $agent->prenom = $request->prenom;
            $agent->email = $request->email;
            $agent->contact = $this->formatPhoneNumber($request->contact);
            $agent->cas_urgence = $this->formatPhoneNumber($request->cas_urgence);
            $agent->nom_urgence = $request->nom_urgence;
            $agent->lien_parente_urgence = $request->lien_parente_urgence;
            $agent->password = Hash::make(Str::random(16));
            $agent->commune = $request->commune;
            $agent->compagnie_id = $gare->compagnie_id;
            $agent->gare_id = $gare->id;

            if ($request->hasFile('profile_picture')) {
                $agent->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
            }

            $agent->save();

            // OTP / Verification Logic
            ResetCodePasswordAgent::where('email', $agent->email)->delete();
            $code = rand(1000, 4000) . $agent->id;
            ResetCodePasswordAgent::create(['code' => $code, 'email' => $agent->email]);
            Notification::route('mail', $agent->email)->notify(new SendEmailToAgentAfterRegistrationNotification($code, $agent->email, $agent->code_id));

            DB::commit();
            return redirect()->route('gare-espace.agents.index')->with('success', 'L\'agent a bien été créé.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur creation agent: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur lors de l\'enregistrement.'])->withInput();
        }
    }

    public function edit(Agent $agent)
    {
        return view('gare-espace.agent.edit', compact('agent'));
    }

    public function update(Request $request, Agent $agent)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:255',
            'prenom' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:agents,email,' . $agent->id,
            'contact' => 'required|string|max:15',
            'commune' => 'required|string|max:255',
            'cas_urgence' => 'required|string|max:15',
            'nom_urgence' => 'required|string|max:255',
            'lien_parente_urgence' => 'required|string|max:100',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        try {
            $agent->name = $request->name;
            $agent->prenom = $request->prenom;
            $agent->email = $request->email;
            $agent->contact = $this->formatPhoneNumber($request->contact);
            $agent->cas_urgence = $this->formatPhoneNumber($request->cas_urgence);
            $agent->nom_urgence = $request->nom_urgence;
            $agent->lien_parente_urgence = $request->lien_parente_urgence;
            $agent->commune = $request->commune;

            if ($request->hasFile('profile_picture')) {
                if ($agent->profile_picture) Storage::disk('public')->delete($agent->profile_picture);
                $agent->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
            }

            $agent->save();
            return redirect()->route('gare-espace.agents.index')->with('success', 'L\'agent a été mis à jour.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour.'])->withInput();
        }
    }

    public function destroy(Agent $agent)
    {
        $agent->update(['archived_at' => now()]);
        return redirect()->route('gare-espace.agents.index')->with('success', 'L\'agent a été archivé.');
    }

    private function formatPhoneNumber($number)
    {
        $number = preg_replace('/[^0-9+]/', '', $number);
        if (substr($number, 0, 2) === '00') $number = '+' . substr($number, 2);
        if (substr($number, 0, 1) !== '+') $number = '+225' . $number;
        return $number;
    }
}
