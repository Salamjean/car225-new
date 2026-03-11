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

class GareAgentController extends Controller
{
    public function index()
    {
        $gare = Auth::guard('gare')->user();
        $agents = Agent::where('gare_id', $gare->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('gare-espace.agent.index', compact('agents'));
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
            'profile_picture' => 'nullable|image|max:2048',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'name.min' => 'Le nom doit avoir au moins 3 caractères.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.min' => 'Le prénom doit avoir au moins 3 caractères.',
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'L\'adresse e-mail n\'est pas valide.',
            'email.unique' => 'Cette adresse e-mail est déjà associée à un compte.',
            'contact.required' => 'Le contact est obligatoire.',
            'commune.required' => 'La commune est obligatoire.',
            'cas_urgence.required' => 'Le contact d\'urgence est obligatoire.',
            'profile_picture.image' => 'Le fichier doit être une image.',
            'profile_picture.max' => 'L\'image ne doit pas dépasser 2 Mo.',
        ]);

        try {
            DB::beginTransaction();

            $gare = Auth::guard('gare')->user();

            if ($request->contact === $request->cas_urgence) {
                return redirect()->back()->withErrors(['cas_urgence' => 'Le contact d\'urgence doit être différent du contact principal.'])->withInput();
            }

            $agent = new Agent();
            $agent->name = $request->name;
            $agent->prenom = $request->prenom;
            $agent->email = $request->email;
            $agent->contact = $this->formatPhoneNumber($request->contact);
            $agent->cas_urgence = $this->formatPhoneNumber($request->cas_urgence);
            $agent->password = Hash::make('default');

            if ($request->hasFile('profile_picture')) {
                $agent->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
            }

            $agent->commune = $request->commune;
            $agent->compagnie_id = $gare->compagnie_id;
            $agent->gare_id = $gare->id;

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
                ->notify(new SendEmailToAgentAfterRegistrationNotification($code, $agent->email, $agent->code_id));

            DB::commit();

            return redirect()->route('gare-espace.agents.index')->with('success', 'L\'agent a bien été créé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de l\'agent depuis la gare: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement. Veuillez réessayer.'])->withInput();
        }
    }

    private function formatPhoneNumber($number)
    {
        $number = preg_replace('/[^0-9+]/', '', $number);

        if (substr($number, 0, 2) === '00') {
            $number = '+' . substr($number, 2);
        }

        if (substr($number, 0, 1) !== '+') {
            $number = '+225' . $number;
        }

        return $number;
    }
}
