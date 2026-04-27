<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Onpc;
use App\Models\ResetCodePasswordOnpc;
use App\Notifications\SendEmailToOnpcAfterRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * CRUD admin pour les agents ONPC. Lors de la création, un code OTP est
 * généré et envoyé par email afin que l'agent définisse son mot de passe
 * via l'écran `onpc.define-access`.
 */
class OnpcController extends Controller
{
    public function index(Request $request)
    {
        $query = Onpc::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact', 'like', "%{$search}%")
                  ->orWhere('localisation', 'like', "%{$search}%");
            });
        }

        $onpcs = $query->latest()->paginate(10)->withQueryString();

        return view('admin.onpc.index', compact('onpcs'));
    }

    public function create()
    {
        return view('admin.onpc.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:onpcs,email',
            'contact'      => 'required|digits:10',
            'localisation' => 'required|string|max:255',
            'photo_path'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'email.unique'    => 'Cet email est déjà associé à un agent ONPC.',
            'contact.digits'  => 'Le numéro de téléphone doit contenir exactement 10 chiffres.',
        ]);

        try {
            DB::beginTransaction();

            $photoPath = null;
            if ($request->hasFile('photo_path')) {
                $file = $request->file('photo_path');
                $name = 'onpc_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $photoPath = $file->storeAs('onpcs/photos', $name, 'public');
            }

            $onpc = Onpc::create([
                'name'         => $validated['name'],
                'email'        => $validated['email'],
                'password'     => Hash::make('default-onpc-' . Str::random(8)),
                'contact'      => $validated['contact'],
                'localisation' => $validated['localisation'],
                'photo_path'   => $photoPath,
                'statut'       => 'actif',
            ]);

            // Génère un code unique (random + id) — même pattern que SapeurPompier.
            ResetCodePasswordOnpc::where('email', $onpc->email)->delete();
            $code = rand(1000, 4000) . $onpc->id;

            ResetCodePasswordOnpc::create([
                'code'  => $code,
                'email' => $onpc->email,
            ]);

            Notification::route('mail', $onpc->email)
                ->notify(new SendEmailToOnpcAfterRegistrationNotification($code, $onpc->email));

            DB::commit();

            return redirect()->route('admin.onpc.index')
                ->with('success', 'Agent ONPC créé avec succès. Un email lui a été envoyé pour définir son mot de passe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la création : ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Onpc $onpc)
    {
        return view('admin.onpc.edit', compact('onpc'));
    }

    public function update(Request $request, Onpc $onpc)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:onpcs,email,' . $onpc->id,
            'contact'      => 'required|digits:10',
            'localisation' => 'required|string|max:255',
            'photo_path'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'statut'       => 'nullable|in:actif,desactive',
        ], [
            'contact.digits' => 'Le numéro de téléphone doit contenir exactement 10 chiffres.',
        ]);

        try {
            if ($request->hasFile('photo_path')) {
                if ($onpc->photo_path && Storage::disk('public')->exists($onpc->photo_path)) {
                    Storage::disk('public')->delete($onpc->photo_path);
                }
                $file = $request->file('photo_path');
                $name = 'onpc_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $validated['photo_path'] = $file->storeAs('onpcs/photos', $name, 'public');
            }

            $onpc->update($validated);

            return redirect()->route('admin.onpc.index')
                ->with('success', 'Agent ONPC mis à jour avec succès.');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Onpc $onpc)
    {
        try {
            if ($onpc->photo_path && Storage::disk('public')->exists($onpc->photo_path)) {
                Storage::disk('public')->delete($onpc->photo_path);
            }
            $onpc->delete();

            return redirect()->route('admin.onpc.index')
                ->with('success', 'Agent ONPC supprimé.');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Renvoie un nouvel OTP par mail à l'agent (utile si l'email d'origine
     * est perdu ou expiré côté client).
     */
    public function resendOtp(Onpc $onpc)
    {
        try {
            ResetCodePasswordOnpc::where('email', $onpc->email)->delete();
            $code = rand(1000, 4000) . $onpc->id;
            ResetCodePasswordOnpc::create(['code' => $code, 'email' => $onpc->email]);

            Notification::route('mail', $onpc->email)
                ->notify(new SendEmailToOnpcAfterRegistrationNotification($code, $onpc->email));

            return redirect()->back()->with('success', 'Nouvel OTP envoyé à ' . $onpc->email);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Échec de l\'envoi : ' . $e->getMessage());
        }
    }
}
