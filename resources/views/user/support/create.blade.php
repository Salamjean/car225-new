@extends('user.layouts.template')

@php
    $titles = [
        'bagage_perdu' => 'Déclarer un Bagage Perdu',
        'objet_oublie' => 'Signaler un Objet Oublié',
        'remboursement' => 'Demander un Remboursement',
        'qualite' => 'Signaler un Problème de Qualité',
        'compte' => 'Aide sur mon Compte',
        'autre' => 'Nouvelle Demande d\'Assistance',
    ];
    $title = $titles[$type] ?? 'Nouvelle Demande';
@endphp

@section('content')
<div class="min-h-screen bg-[#F8F9FA] py-8 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-[800px]">
        
        <!-- Back Button -->
        <a href="{{ route('user.support.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-[#e94f1b] mb-8 transition-colors">
            <i class="fas fa-arrow-left"></i> Retour au support
        </a>

        <!-- Header -->
        <div class="mb-10">
            <h1 class="text-3xl font-black text-[#1A1D1F] tracking-tight">{{ $title }}</h1>
            <p class="text-gray-500 font-medium">Veuillez remplir le formulaire ci-dessous pour que nos équipes puissent vous aider.</p>
        </div>

        <div class="bg-white rounded-[32px] p-8 sm:p-12 border border-gray-100 shadow-sm">
            <form action="{{ route('user.support.store') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">

                <div class="space-y-6">
                    <!-- Reservation Link (Optional) -->
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Voyage Concerné (Optionnel)</label>
                        <select name="reservation_id" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-6 py-4 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#e94f1b]/20 focus:bg-white transition-all text-gray-700">
                            <option value="">-- Sélectionner un voyage récent --</option>
                            @php
                                $reservations = \App\Models\Reservation::where('user_id', Auth::id())
                                    ->where('statut', 'confirmee')
                                    ->with('programme')
                                    ->orderBy('date_voyage', 'desc')
                                    ->take(10)
                                    ->get();
                            @endphp
                            @foreach($reservations as $res)
                                <option value="{{ $res->id }}">
                                    {{ $res->programme->point_depart }} &rarr; {{ $res->programme->point_arrive }} ({{ \Carbon\Carbon::parse($res->date_voyage)->isoFormat('LL') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Object -->
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Objet du message</label>
                        <input type="text" name="objet" required placeholder="Ex: Valise bleue manquante, Remboursement trajet annulé..." class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-6 py-4 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#e94f1b]/20 focus:bg-white transition-all text-gray-700">
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Détails de votre problème</label>
                        <textarea name="description" rows="5" required placeholder="Plus vous donnerez de détails (heure, numéro de place, description précise), plus vite nous pourrons vous aider." class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-6 py-4 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#e94f1b]/20 focus:bg-white transition-all text-gray-700"></textarea>
                    </div>

                    <!-- Action -->
                    <div class="pt-4">
                        <button type="submit" class="w-full bg-[#1A1D1F] hover:bg-[#e94f1b] text-white font-black py-5 rounded-3xl shadow-xl shadow-gray-200 transition-all uppercase tracking-widest text-sm flex items-center justify-center gap-3 group">
                            Envoyer ma demande
                            <i class="fas fa-paper-plane group-hover:translate-x-1 group-hover:-translate-y-1 transition-all"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap');
    body {
        font-family: 'Outfit', sans-serif;
    }
</style>
@endsection
