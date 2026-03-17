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
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                    <i class="fas fa-calendar-day text-orange-500"></i>
                    Voyages du jour
                </h2>
            </div>
            <div class="p-6">
                @forelse($voyagesAujourdhui as $voyage)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl mb-3 last:mb-0 hover:bg-gray-100 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-route text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">
                                    {{ $voyage->programme?->gareDepart?->nom_gare ?? 'Gare inconnue' }} → {{ $voyage->programme?->gareArrivee?->nom_gare ?? 'Gare inconnue' }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-clock mr-1"></i> {{ $voyage->programme?->heure_depart ? \Carbon\Carbon::parse($voyage->programme->heure_depart)->format('H:i') : '--:--' }}
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-user mr-1"></i> {{ $voyage->chauffeur?->prenom ?? 'N/A' }} {{ $voyage->chauffeur?->name ?? '' }}
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-bus mr-1"></i> {{ $voyage->vehicule?->immatriculation ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <div>
                            @if($voyage->statut === 'en_attente')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-sm font-semibold">En attente</span>
                            @elseif($voyage->statut === 'en_cours')
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-sm font-semibold">En cours</span>
                            @elseif($voyage->statut === 'terminé')
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-sm font-semibold">Terminé</span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold">{{ $voyage->statut }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-inbox text-gray-300 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Aucun voyage aujourd'hui</h3>
                        <p class="text-gray-500 text-sm">Rendez-vous sur la page de programmation pour assigner des voyages.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
