@extends('compagnie.layouts.template')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Modifier le Programme</h1>
        
        <form action="{{ route('programme.update', $programme->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Info statique -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Trajet</label>
                    <input type="text" value="{{ $programme->point_depart }} -> {{ $programme->point_arrive }}" disabled class="mt-1 w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm">
                </div>
                 <div>
                    <label class="block text-sm font-medium text-gray-700">Date de départ</label>
                    <input type="text" value="{{ $programme->date_depart }}" disabled class="mt-1 w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm">
                </div>

                <!-- Véhicule -->
                <div>
                    <label for="vehicule_id" class="block text-sm font-medium text-gray-700">Véhicule</label>
                    <select name="vehicule_id" id="vehicule_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        @foreach($vehicules as $vehicule)
                            <option value="{{ $vehicule->id }}" {{ $programme->vehicule_id == $vehicule->id ? 'selected' : '' }}>
                                {{ $vehicule->marque }} - {{ $vehicule->immatriculation }} ({{ $vehicule->nombre_place }} places)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Chauffeur -->
                <div>
                    <label for="personnel_id" class="block text-sm font-medium text-gray-700">Chauffeur</label>
                    <select name="personnel_id" id="personnel_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        @foreach($chauffeurs as $chauffeur)
                            <option value="{{ $chauffeur->id }}" {{ $programme->personnel_id == $chauffeur->id ? 'selected' : '' }}>
                                {{ $chauffeur->prenom }} {{ $chauffeur->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Heure -->
                <div>
                    <label for="heure_depart" class="block text-sm font-medium text-gray-700">Heure de départ</label>
                    <input type="time" name="heure_depart" id="heure_depart" value="{{ $programme->heure_depart }}" class="mt-1 w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                
                <!-- Montant -->
                <div>
                    <label for="montant_billet" class="block text-sm font-medium text-gray-700">Montant (FCFA)</label>
                    <input type="number" name="montant_billet" id="montant_billet" value="{{ $programme->montant_billet }}" class="mt-1 w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('programme.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Annuler</a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#e94f1b] hover:bg-[#d6420f]">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection