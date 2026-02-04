@extends('compagnie.layouts.template')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- En-tête -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
            <div class="mb-6 lg:mb-0">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Lignes de Transport</h1>
                <p class="text-gray-600">Service continu 24h/24 - Les clients choisissent leur heure</p>
            </div>
            <a href="{{ route('programme.create') }}"
                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white font-bold rounded-xl hover:from-orange-600 hover:to-red-600 transform hover:-translate-y-1 transition-all duration-200 shadow-lg">
                <i class="fas fa-plus mr-2"></i>
                Nouvelle Ligne
            </a>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-md p-5 border-l-4 border-orange-500">
                <p class="text-sm text-gray-600">Total Lignes</p>
                <p class="text-2xl font-bold text-gray-900">{{ $programmes->count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-5 border-l-4 border-green-500">
                <p class="text-sm text-gray-600">Actives</p>
                <p class="text-2xl font-bold text-gray-900">{{ $programmes->where('statut', 'actif')->count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-5 border-l-4 border-blue-500">
                <p class="text-sm text-gray-600">Itinéraires</p>
                <p class="text-2xl font-bold text-gray-900">{{ $programmes->unique('itineraire_id')->count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-5 border-l-4 border-purple-500">
                <p class="text-sm text-gray-600">Routes (A↔R)</p>
                <p class="text-2xl font-bold text-gray-900">{{ intval($programmes->count() / 2) }}</p>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-r-xl">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-r-xl">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        <!-- Liste des lignes -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-800 to-gray-900 text-white">
                <h2 class="text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-route"></i>
                    Lignes de transport actives
                </h2>
            </div>

            @if($programmes->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($programmes as $programme)
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <!-- Route Info -->
                                <div class="flex items-center gap-4 flex-1">
                                    <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center text-white shadow-lg">
                                        <i class="fas fa-bus"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">
                                            {{ $programme->point_depart }} → {{ $programme->point_arrive }}
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            <i class="fas fa-clock mr-1"></i>{{ $programme->durer_parcours }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Période -->
                                <div class="text-center px-4">
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Période</p>
                                    <p class="font-semibold text-gray-800">
                                        {{ \Carbon\Carbon::parse($programme->date_depart)->format('d/m/Y') }}
                                        @if($programme->date_fin)
                                            - {{ \Carbon\Carbon::parse($programme->date_fin)->format('d/m/Y') }}
                                        @endif
                                    </p>
                                </div>

                                <!-- Service -->
                                <div class="text-center px-4">
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Service</p>
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">
                                        <i class="fas fa-infinity"></i>
                                        24h/24
                                    </span>
                                </div>

                                <!-- Prix -->
                                <div class="text-center px-4">
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Tarif</p>
                                    <p class="text-lg font-bold text-orange-600">
                                        {{ number_format($programme->montant_billet, 0, ',', ' ') }} FCFA
                                    </p>
                                </div>

                                <!-- Statut -->
                                <div class="text-center px-4">
                                    @if($programme->statut == 'actif')
                                        <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                            Actif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm font-medium">
                                            {{ ucfirst($programme->statut) }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('programme.edit', $programme->id) }}" 
                                       class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('programme.destroy', $programme->id) }}" method="POST" 
                                          onsubmit="return confirm('Supprimer cette ligne?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-route text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Aucune ligne créée</h3>
                    <p class="text-gray-500 mb-6">Commencez par créer votre première ligne de transport</p>
                    <a href="{{ route('programme.create') }}" 
                       class="inline-flex items-center px-6 py-3 bg-orange-500 text-white font-bold rounded-xl hover:bg-orange-600 transition">
                        <i class="fas fa-plus mr-2"></i>
                        Créer une ligne
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection