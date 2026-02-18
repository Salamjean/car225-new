@extends('gare-espace.layouts.template')

@section('title', 'Véhicules')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Véhicules</h2>
                <p class="text-gray-500 text-lg">Parc de véhicules actifs de votre compagnie</p>
            </div>
            <a href="{{ route('gare-espace.vehicules.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-[#e94f1b] text-white font-bold rounded-xl hover:bg-[#d33d0f] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                <i class="fas fa-plus mr-2"></i>
                Créer un Véhicule
            </a>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium uppercase">Total Véhicules</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $vehicules->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-bus text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium uppercase">Disponibles</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $vehicules->where('statut', 'disponible')->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium uppercase">En service</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $vehicules->where('statut', 'indisponible')->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-road text-orange-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vehicle Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($vehicules as $vehicule)
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition-all group">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="fas fa-bus text-orange-600 text-2xl"></i>
                            </div>
                            @if($vehicule->statut === 'disponible')
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-semibold">Disponible</span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-xs font-semibold">En service</span>
                            @endif
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg mb-1">{{ $vehicule->immatriculation }}</h3>
                        <p class="text-gray-500 text-sm mb-3">{{ $vehicule->marque }} {{ $vehicule->modele }}</p>
                        <div class="flex items-center gap-4 text-sm text-gray-500">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-chair text-gray-400"></i>
                                {{ $vehicule->nombre_place }} places
                            </span>
                            @if($vehicule->climatisation)
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-snowflake text-blue-400"></i>
                                    Clim
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20 bg-white rounded-3xl border-2 border-dashed border-gray-200">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-bus text-gray-300 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun véhicule trouvé</h3>
                    <p class="text-gray-500">Le parc de véhicules est vide.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
