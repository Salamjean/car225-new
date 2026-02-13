@extends('chauffeur.layouts.template')

@section('title', 'Programmes disponibles')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">S'assigner un voyage</h2>
            <p class="text-gray-500 text-lg">Choisissez un trajet et un véhicule disponible pour planifier votre course.</p>
        </div>

        <!-- Date Selector -->
        <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 mb-8">
            <form action="{{ route('chauffeur.programmes') }}" method="GET" class="flex items-end gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Date du voyage</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar text-gray-400"></i>
                        </div>
                        <input type="date" name="date" value="{{ $date }}" min="{{ date('Y-m-d') }}"
                            onchange="this.form.submit()"
                            class="block w-full pl-10 px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all bg-gray-50 focus:bg-white">
                    </div>
                </div>
                <button type="submit" class="bg-orange-600 text-white p-3.5 rounded-xl hover:bg-orange-700 transition-colors shadow-md hover:shadow-lg">
                    <i class="fas fa-search text-lg"></i>
                </button>
            </form>
        </div>

        <!-- Programmes List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($programmes as $programme)
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full">
                    <!-- Card Header -->
                    <div class="p-5 border-b border-gray-50 bg-gray-50/50">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-3">
                                <div class="w-14 h-14 bg-white rounded-2xl shadow-sm flex items-center justify-center text-orange-600 font-bold text-xl border border-gray-100">
                                    {{ \Carbon\Carbon::parse($programme->heure_depart)->format('H:i') }}
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Programme #{{ $programme->id }}</p>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-900 text-lg">{{ $programme->gareDepart->nom_gare }}</span>
                                        <span class="text-xs text-gray-400 my-0.5"><i class="fas fa-arrow-down"></i></span>
                                        <span class="font-bold text-gray-900 text-lg">{{ $programme->gareArrivee->nom_gare }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 flex-1 flex flex-col justify-end">
                        <form action="{{ route('chauffeur.voyage.assign') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="programme_id" value="{{ $programme->id }}">
                            <input type="hidden" name="date_voyage" value="{{ $date }}">

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Sélectionner un véhicule</label>
                                <div class="relative">
                                    <select name="vehicule_id" required class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm bg-gray-50 focus:bg-white appearance-none">
                                        <option value="">-- Choisir un véhicule --</option>
                                        @foreach($vehicules as $vehicule)
                                            <option value="{{ $vehicule->id }}">{{ $vehicule->immatriculation }} ({{ $vehicule->marque }} {{ $vehicule->modele }})</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-gradient-to-r from-orange-600 to-red-600 text-white py-3.5 rounded-xl font-bold flex items-center justify-center gap-2 hover:from-orange-700 hover:to-red-700 transition-all shadow-md hover:shadow-lg">
                                <i class="fas fa-check-circle"></i>
                                Confirmer mon affectation
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20 bg-white rounded-3xl border-2 border-dashed border-gray-200">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-inbox text-gray-300 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun programme disponible</h3>
                    <p class="text-gray-500 max-w-md mx-auto">Il n'y a pas de trajets disponibles pour cette date, ou tous les programmes ont déjà été assignés.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
