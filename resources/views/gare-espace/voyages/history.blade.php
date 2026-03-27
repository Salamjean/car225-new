@extends('gare-espace.layouts.template')

@section('title', 'Historique des Voyages')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8 px-4">
    <div class="mx-auto" style="width: 100%">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Historique des Voyages</h2>
            <p class="text-gray-500 text-lg">Consultez les voyages terminés de votre compagnie</p>
        </div>

        <div class="space-y-4">
            @forelse($voyages as $voyage)
                <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-100 hover:shadow-lg transition-all">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <span class="font-bold text-gray-900">{{ $voyage->programme->gareDepart->nom_gare }}</span>
                                    <i class="fas fa-arrow-right text-blue-500 text-sm"></i>
                                    <span class="font-bold text-gray-900">{{ $voyage->programme->gareArrivee->nom_gare }}</span>
                                </div>
                                <div class="flex items-center gap-4 text-sm text-gray-500">
                                    <span><i class="fas fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($voyage->date_voyage)->format('d/m/Y') }}</span>
                                    <span><i class="fas fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($voyage->programme->heure_depart)->format('H:i') }}</span>
                                    <span><i class="fas fa-user mr-1"></i> {{ $voyage->chauffeur->prenom }} {{ $voyage->chauffeur->name }}</span>
                                    <span><i class="fas fa-bus mr-1"></i> {{ $voyage->vehicule->immatriculation }}</span>
                                </div>
                            </div>
                        </div>
                        <span class="px-4 py-2 bg-green-100 text-green-700 rounded-xl font-semibold text-sm">
                            Terminé
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-gray-200">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-history text-gray-300 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun voyage terminé</h3>
                    <p class="text-gray-500">L'historique des voyages apparaîtra ici.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $voyages->links() }}
        </div>
    </div>
</div>
@endsection
