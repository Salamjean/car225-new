@extends('agent.layouts.template')

@section('title', 'Historique des Voyages')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Historique des Voyages</h2>
                <p class="text-gray-500 text-lg">Consultez tous les voyages terminés de votre compagnie</p>
            </div>
            <a href="{{ route('agent.voyages.index') }}" class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition-all font-semibold shadow-sm">
                <i class="fas fa-arrow-left"></i>
                Retour à la programmation
            </a>
        </div>

        <!-- Voyages History List -->
        <div class="space-y-6">
            @forelse($voyages as $voyage)
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                    <!-- Voyage Header -->
                    <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-blue-50">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center text-blue-600 font-bold text-xl border border-gray-100">
                                    {{ \Carbon\Carbon::parse($voyage->programme->heure_depart)->format('H:i') }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">PROGRAMME #{{ $voyage->programme->id }}</p>
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="font-bold text-gray-900 text-lg">{{ $voyage->programme->gareDepart->nom_gare }}</span>
                                        <i class="fas fa-arrow-right text-blue-500"></i>
                                        <span class="font-bold text-gray-900 text-lg">{{ $voyage->programme->gareArrivee->nom_gare }}</span>
                                    </div>
                                    
                                    <!-- Trip details -->
                                    <div class="flex flex-wrap items-center gap-4 text-sm">
                                        <div class="flex items-center gap-2 bg-blue-50 px-3 py-1 rounded-lg">
                                            <i class="fas fa-map-marker-alt text-blue-600"></i>
                                            <span class="text-gray-700">
                                                <span class="font-semibold">{{ $voyage->programme->point_depart }}</span>
                                                <i class="fas fa-long-arrow-alt-right text-blue-500 mx-1"></i>
                                                <span class="font-semibold">{{ $voyage->programme->point_arrive }}</span>
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-2 bg-gray-50 px-3 py-1 rounded-lg">
                                            <i class="fas fa-calendar-alt text-gray-600"></i>
                                            <span class="text-gray-700">
                                                <span class="font-semibold">Date:</span> {{ \Carbon\Carbon::parse($voyage->date_voyage)->format('d/m/Y') }}
                                            </span>
                                        </div>
                                        
                                        @php
                                            $depart = \Carbon\Carbon::parse($voyage->programme->heure_depart);
                                            $arrivee = \Carbon\Carbon::parse($voyage->programme->heure_arrive);
                                            $duree = $depart->diff($arrivee);
                                            $heures = $duree->h;
                                            $minutes = $duree->i;
                                        @endphp
                                        
                                        <div class="flex items-center gap-2 bg-purple-50 px-3 py-1 rounded-lg">
                                            <i class="fas fa-clock text-purple-600"></i>
                                            <span class="text-gray-700">
                                                <span class="font-semibold">Durée:</span> 
                                                @if($heures > 0) {{ $heures }}h @endif {{ $minutes }}min
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-center gap-2 bg-orange-50 px-3 py-1 rounded-lg">
                                            <i class="fas fa-arrow-down text-orange-600"></i>
                                            <span class="text-gray-700">
                                                <span class="font-semibold">Arrivée:</span> {{ \Carbon\Carbon::parse($voyage->programme->heure_arrive)->format('H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <span class="px-4 py-2 bg-green-100 text-green-700 rounded-xl font-semibold text-sm flex items-center gap-2">
                                <i class="fas fa-check-circle"></i>
                                Assigné
                            </span>
                        </div>
                    </div>

                    <!-- Voyage Content -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Chauffeur -->
                            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">CHAUFFEUR ASSIGNÉ</p>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-gray-400">
                                        <i class="fas fa-user-circle text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $voyage->chauffeur->prenom }} {{ $voyage->chauffeur->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $voyage->chauffeur->contact }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Véhicule -->
                            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">VÉHICULE ASSIGNÉ</p>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-gray-400">
                                        <i class="fas fa-bus text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $voyage->vehicule->immatriculation }}</p>
                                        <p class="text-sm text-gray-500 text-xs">{{ $voyage->vehicule->marque }} {{ $voyage->vehicule->modele }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Statut -->
                            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">STATUT DU VOYAGE</p>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 text-green-700 rounded-xl font-bold text-sm">
                                        <i class="fas fa-check-circle"></i> Terminé
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-gray-200">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-history text-gray-300 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun historique disponible</h3>
                    <p class="text-gray-500 max-w-md mx-auto">Vous n'avez pas encore de voyages terminés dans votre historique.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $voyages->links() }}
        </div>
    </div>
</div>
@endsection
