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

    // Labels & hints contextuels pour le select de réservation
    $reservationLabels = [
        'bagage_perdu' => 'Voyage concerné',
        'objet_oublie' => 'Voyage concerné',
        'remboursement' => 'Réservation annulée',
        'qualite' => 'Voyage concerné',
    ];

    $reservationHints = [
        'bagage_perdu' => 'Sélectionnez le voyage durant lequel votre bagage a été perdu.',
        'objet_oublie' => 'Sélectionnez le voyage durant lequel vous avez oublié votre objet.',
        'remboursement' => 'Sélectionnez la réservation annulée pour laquelle vous demandez un remboursement.',
        'qualite' => 'Sélectionnez le voyage concerné par votre signalement.',
    ];

    $emptyMessages = [
        'bagage_perdu' => 'Vous n\'avez aucun voyage terminé. Vous pourrez déclarer un bagage perdu une fois votre voyage effectué.',
        'objet_oublie' => 'Vous n\'avez aucun voyage terminé. Vous pourrez signaler un objet oublié une fois votre voyage effectué.',
        'remboursement' => 'Vous n\'avez aucune réservation annulée. Vous ne pouvez demander un remboursement que pour une réservation déjà annulée.',
        'qualite' => 'Vous n\'avez aucun voyage récent à signaler.',
    ];

    $objectPlaceholders = [
        'bagage_perdu' => 'Ex: Valise bleue à roulettes, sac à dos noir...',
        'objet_oublie' => 'Ex: Téléphone Samsung, lunettes de soleil, clés...',
        'remboursement' => 'Ex: Remboursement réservation annulée du 15/02...',
        'qualite' => 'Ex: Comportement du chauffeur, propreté du véhicule...',
        'compte' => 'Ex: Erreur de solde portefeuille, accès impossible...',
        'autre' => 'Ex: Suggestion, question sur un service...',
    ];

    $descriptionPlaceholders = [
        'bagage_perdu' => 'Décrivez votre bagage (couleur, taille, contenu) et les circonstances de la perte. Plus vous êtes précis, plus vite nous pourrons le retrouver.',
        'objet_oublie' => 'Décrivez l\'objet oublié et où il se trouvait dans le véhicule (sous le siège, dans le compartiment...). Indiquez aussi votre numéro de place.',
        'remboursement' => 'Expliquez les circonstances de l\'annulation et le montant que vous attendez. Si vous avez une preuve d\'annulation, mentionnez-le.',
        'qualite' => 'Décrivez le problème rencontré (chauffeur, hôtesse, véhicule, ponctualité...). Soyez aussi précis que possible.',
        'compte' => 'Décrivez votre problème : erreur de solde, problème de connexion, modification de profil...',
        'autre' => 'Décrivez votre demande ou question en détail.',
    ];

    $selectLabel = $reservationLabels[$type] ?? '';
    $selectHint = $reservationHints[$type] ?? '';
    $emptyMsg = $emptyMessages[$type] ?? '';
    $objPlaceholder = $objectPlaceholders[$type] ?? 'Ex: Décrivez brièvement votre demande...';
    $descPlaceholder = $descriptionPlaceholders[$type] ?? 'Décrivez votre problème en détail...';
    $hasReservations = $needsReservation && $reservations->count() > 0;
@endphp

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto" style="width:75%">
        
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

            {{-- Message si aucune réservation disponible (et qu'on en a besoin) --}}
            @if($needsReservation && !$hasReservations)
                <div class="mb-8 bg-amber-50 border border-amber-200 rounded-2xl p-6 flex items-start gap-4">
                    <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-exclamation-triangle text-amber-500"></i>
                    </div>
                    <div>
                        <p class="font-bold text-amber-800 text-sm mb-1">Aucune réservation disponible</p>
                        <p class="text-amber-700 text-xs leading-relaxed">{{ $emptyMsg }}</p>
                        <a href="{{ route('user.support.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-[#e94f1b] mt-3 hover:underline">
                            <i class="fas fa-arrow-left text-[10px]"></i> Choisir une autre catégorie
                        </a>
                    </div>
                </div>
            @endif

            <form action="{{ route('user.support.store') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">

                <div class="space-y-6">

                    {{-- Reservation / Voyage select — affiché uniquement si le type le nécessite --}}
                    @if($needsReservation)
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">
                                {{ $selectLabel }} <span class="text-red-400">*</span>
                            </label>
                            <select name="reservation_id" required 
                                class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-6 py-4 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#e94f1b]/20 focus:bg-white transition-all text-gray-700 @if(!$hasReservations) opacity-50 cursor-not-allowed @endif"
                                @if(!$hasReservations) disabled @endif>
                                <option value="">-- Sélectionner --</option>
                                @foreach($reservations as $res)
                                    <option value="{{ $res->id }}">
                                        @if($res->programme)
                                            {{ $res->programme->point_depart }} &rarr; {{ $res->programme->point_arrive }}
                                        @else
                                            Réservation #{{ $res->reference ?? $res->id }}
                                        @endif
                                        ({{ \Carbon\Carbon::parse($res->date_voyage)->isoFormat('LL') }})
                                        @if($type === 'remboursement' && $res->montant)
                                            — {{ number_format($res->montant, 0, ',', ' ') }} FCFA
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-[11px] text-gray-400 leading-relaxed">
                                <i class="fas fa-info-circle mr-1"></i>{{ $selectHint }}
                            </p>
                        </div>
                    @endif

                    <!-- Object -->
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Objet du message <span class="text-red-400">*</span></label>
                        <input type="text" name="objet" required placeholder="{{ $objPlaceholder }}" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-6 py-4 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#e94f1b]/20 focus:bg-white transition-all text-gray-700">
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Détails de votre problème <span class="text-red-400">*</span></label>
                        <textarea name="description" rows="5" required placeholder="{{ $descPlaceholder }}" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-6 py-4 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#e94f1b]/20 focus:bg-white transition-all text-gray-700"></textarea>
                    </div>

                    <!-- Action -->
                    <div class="pt-4">
                        <button type="submit" 
                            class="w-full bg-[#1A1D1F] hover:bg-[#e94f1b] text-white font-black py-5 rounded-3xl shadow-xl shadow-gray-200 transition-all uppercase tracking-widest text-sm flex items-center justify-center gap-3 group @if($needsReservation && !$hasReservations) opacity-50 cursor-not-allowed @endif"
                            @if($needsReservation && !$hasReservations) disabled @endif>
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

