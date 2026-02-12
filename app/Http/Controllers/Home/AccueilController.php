<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Compagnie;
use App\Models\Itineraire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ContactFormNotification;

class AccueilController extends Controller
{
    public function about()
    {
        return view('home.pages.about');
    }

    public function destination(Request $request)
    {
        $query = Itineraire::with('compagnie');

        if ($request->has('compagnie_id')) {
            $query->where('compagnie_id', $request->compagnie_id);
        }

        $itineraires = $query->get();
        return view('home.pages.destination', compact('itineraires'));
    }
    public function compagny()
    {
        $compagnies = Compagnie::where('statut', 'actif')->get();
        return view('home.pages.company', compact('compagnies'));
    }
    public function services()
    {
        return view('home.pages.services');
    }
    public function contact()
    {
        return view('home.pages.contact');
    }

    public function storeContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $contactData = [
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
        ];

        // Envoyer la notification par email
        Notification::route('mail', 'salamjeanlouis3@gmail.com')
            ->notify(new ContactFormNotification($contactData));

        return back()->with('success', 'Votre message a bien été envoyé. Notre équipe vous répondra très prochainement.');
    }
}
