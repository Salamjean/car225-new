@extends('gare-espace.layouts.template')

@section('title', 'Tableau de bord')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="width: 97%">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                Bienvenue, {{ $gare->responsable_prenom }} 👋
            </h1>
            <p class="text-gray-500 text-lg">
                Gare de <span class="font-semibold text-orange-600">{{ $gare->nom_gare }}</span> — {{ $gare->ville }}
            </p>
        </div>

        <!-- Bannière voyage en cours -->
        @if(isset($liveVoyagesCount) && $liveVoyagesCount > 0)
        <a href="{{ route('gare-espace.tracking.index') }}" class="mb-6 flex items-center gap-4 bg-gradient-to-r from-emerald-600 to-green-500 rounded-2xl px-5 py-4 text-white hover:opacity-90 transition-all shadow-lg shadow-green-500/20" style="text-decoration:none;display:flex;">
            <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-satellite-dish text-white animate-pulse"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold" style="margin:0;">{{ $liveVoyagesCount }} voyage{{ $liveVoyagesCount > 1 ? 's' : '' }} en cours — Voir en direct</p>
                <p class="text-xs mt-1" style="margin:0;opacity:0.85;">Cliquez pour suivre sur la carte les voyages passant par votre gare</p>
            </div>
            <div class="flex-shrink-0 flex items-center gap-2 bg-white/20 px-3 py-1.5 rounded-full text-xs font-bold">
                <span style="width:8px;height:8px;background:#f87171;border-radius:50%;display:inline-block;animation:ping 1s infinite;"></span>
                LIVE <i class="fas fa-arrow-right ml-1"></i>
            </div>
        </a>
        @endif

        <!-- Notice GPS -->
        @if(!$gare->latitude || !$gare->longitude)
        <div id="gps-notice" class="mb-6 flex items-start gap-4 bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4">
            <div class="w-9 h-9 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="fas fa-map-marker-alt text-amber-500"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-amber-800">Localisation GPS non définie</p>
                <p class="text-xs text-amber-600 mt-0.5">La position de votre gare est nécessaire pour le suivi en temps réel des voyages. Rendez-vous dans votre <a href="{{ route('gare-espace.profile') }}" class="underline font-bold">profil</a> pour soumettre une demande de localisation.</p>
            </div>
            <a href="{{ route('gare-espace.profile') }}"
                class="flex-shrink-0 flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl transition-all">
                <i class="fas fa-arrow-right"></i> Profil
            </a>
        </div>
        @else
        <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 rounded-2xl px-5 py-3">
            <i class="fas fa-map-pin text-green-500 text-sm"></i>
            <p class="text-xs text-green-700 font-medium flex-1">
                Position GPS de votre gare enregistrée. Pensez à la <a href="{{ route('gare-espace.profile') }}" class="font-bold underline">mettre à jour</a> si votre gare a changé d'emplacement.
            </p>
        </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Programmes actifs -->
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium uppercase">Programmes actifs</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $programmesActifs }}</p>
                    </div>
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-route text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Chauffeurs disponibles -->
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium uppercase">Chauffeurs disponibles</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $chauffeursDisponibles }}<span class="text-lg text-gray-400">/{{ $totalChauffeurs }}</span></p>
                    </div>
                    <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-check text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Véhicules disponibles -->
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium uppercase">Véhicules disponibles</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $vehiculesDisponibles }}<span class="text-lg text-gray-400">/{{ $totalVehicules }}</span></p>
                    </div>
                    <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-bus text-orange-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Voyages aujourd'hui -->
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium uppercase">Voyages aujourd'hui</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $voyagesAujourdhui->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-calendar-day text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <a href="{{ route('gare-espace.voyages.index') }}" class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-plus-circle text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Programmer un voyage</h3>
                        <p class="text-blue-100 text-sm">Assigner chauffeur et véhicule</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('gare-espace.personnel.index') }}" class="bg-gradient-to-r from-green-500 to-emerald-600 text-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Gérer le personnel</h3>
                        <p class="text-green-100 text-sm">Chauffeurs et convoyeurs</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('gare-espace.vehicules.index') }}" class="bg-gradient-to-r from-orange-500 to-red-500 text-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-bus text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Voir les véhicules</h3>
                        <p class="text-orange-100 text-sm">Parc de véhicules actifs</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Today's Voyages -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 anim-fade-up">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                    <i class="fas fa-calendar-day text-orange-500"></i>
                    Voyages du jour
                </h2>
                <div class="flex gap-2">
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-wider">
                        Assignés: {{ $voyagesAujourdhui->count() }}
                    </span>
                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-[10px] font-black uppercase tracking-wider">
                        À assigner: {{ $programmesNonAssignes->count() }}
                    </span>
                </div>
            </div>
            <div class="p-6">
                @php
                    $allToday = $voyagesAujourdhui->map(function($v) {
                        return (object)[
                            'type' => 'assigned',
                            'time' => $v->programme?->heure_depart,
                            'route' => ($v->programme?->gareDepart?->nom_gare ?? 'Gare') . ' → ' . ($v->programme?->gareArrivee?->nom_gare ?? 'Gare'),
                            'chauffeur' => ($v->chauffeur?->prenom ?? '') . ' ' . ($v->chauffeur?->name ?? 'N/A'),
                            'vehicule' => $v->vehicule?->immatriculation ?? 'N/A',
                            'statut' => $v->statut,
                            'url' => route('gare-espace.voyages.index', ['show_details' => $v->programme_id])
                        ];
                    })->concat($programmesNonAssignes->map(function($p) {
                        return (object)[
                            'type' => 'unassigned',
                            'time' => $p->heure_depart,
                            'route' => ($p->gareDepart?->nom_gare ?? 'Gare') . ' → ' . ($p->gareArrivee?->nom_gare ?? 'Gare'),
                            'chauffeur' => 'Non assigné',
                            'vehicule' => 'Non assigné',
                            'statut' => 'à_assigner',
                            'url' => route('gare-espace.voyages.index')
                        ];
                    }))->sortBy('time');
                    
                    $displayToday = $allToday->take(5);
                    $hasMore = $allToday->count() > 5;
                @endphp

                @forelse($displayToday as $voyage)
                    <a href="{{ $voyage->url }}" class="flex items-center justify-between p-4 {{ $voyage->type === 'unassigned' ? 'bg-red-50/50 border-l-4 border-red-500' : 'bg-gray-50 border-l-4 border-blue-500' }} rounded-xl mb-3 last:mb-0 hover:bg-gray-100 transition-all duration-300 group block">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 {{ $voyage->type === 'unassigned' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600' }} rounded-xl flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                                    <i class="fas fa-route"></i>
                                </div>
                                <div>
                                    <p class="font-black text-gray-900 uppercase tracking-tight group-hover:text-blue-600 transition-colors">
                                        {{ $voyage->route }}
                                    </p>
                                    <p class="text-xs text-gray-500 flex items-center gap-3">
                                        <span class="flex items-center gap-1 font-bold">
                                            <i class="fas fa-clock text-orange-500"></i> {{ \Carbon\Carbon::parse($voyage->time)->format('H:i') }}
                                        </span>
                                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-user {{ $voyage->type === 'unassigned' ? 'text-gray-300' : 'text-blue-500' }}"></i> {{ $voyage->chauffeur }}
                                        </span>
                                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-bus {{ $voyage->type === 'unassigned' ? 'text-gray-300' : 'text-blue-500' }}"></i> {{ $voyage->vehicule }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                @if($voyage->type === 'unassigned')
                                    <div class="px-4 py-2 bg-red-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-red-200">
                                        Assigner
                                    </div>
                                @else
                                    <div class="flex flex-col items-end gap-1">
                                        @if($voyage->statut === 'en_attente')
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-[10px] font-black uppercase tracking-widest leading-none">En attente</span>
                                        @elseif($voyage->statut === 'en_cours')
                                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-[10px] font-black uppercase tracking-widest leading-none">En cours</span>
                                        @elseif($voyage->statut === 'terminé')
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-[10px] font-black uppercase tracking-widest leading-none">Terminé</span>
                                        @else
                                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-[10px] font-black uppercase tracking-widest leading-none">{{ $voyage->statut }}</span>
                                        @endif
                                        <span class="text-[9px] font-bold text-blue-400 uppercase tracking-tighter">Cliquez pour voir les détails</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-inbox text-gray-300 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-black text-gray-900 mb-1 uppercase tracking-tight">Aucun voyage aujourd'hui</h3>
                        <p class="text-gray-500 text-xs font-medium uppercase tracking-widest">Tous les programmes sont terminés ou assignés.</p>
                    </div>
                @endforelse

                @if($hasMore)
                <div class="mt-6 text-center">
                    <a href="{{ route('gare-espace.voyages.index') }}" class="inline-block px-6 py-2.5 bg-orange-100 text-orange-600 font-bold rounded-xl text-xs uppercase tracking-widest hover:bg-orange-200 transition-colors shadow-sm">
                        Voir plus
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if(isset($locationApprovedNotification) && $locationApprovedNotification)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const req = @json($locationApprovedNotification);

    Swal.fire({
        icon: 'success',
        title: '📍 Localisation mise à jour !',
        html: `
            <p class="text-sm text-gray-600">Votre compagnie a approuvé la mise à jour de la position GPS de votre gare.</p>
            <div class="mt-3 p-3 bg-green-50 rounded-xl font-mono text-xs text-green-700">
                Lat : ${parseFloat(req.latitude).toFixed(6)}<br>
                Lng : ${parseFloat(req.longitude).toFixed(6)}
            </div>
        `,
        confirmButtonColor: '#16a34a',
        confirmButtonText: 'Super, merci !',
        customClass: { popup: 'rounded-3xl' }
    });

    // Marquer comme notifié
    fetch("{{ route('gare-espace.profile.markLocationNotified') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: '{}'
    });
});
</script>
@endif
@endsection
