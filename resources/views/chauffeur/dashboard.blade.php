@extends('chauffeur.layouts.template')

@section('title', 'Tableau de bord')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- Header Summary -->
        <div class="bg-gradient-to-r from-orange-600 to-red-600 rounded-2xl p-6 text-white shadow-lg mb-8">
            <h2 class="text-2xl font-bold">Bonjour, {{ Auth::guard('chauffeur')->user()->prenom }} !</h2>
            <p class="opacity-90 mt-1">Prêt pour la route ?</p>
            
            <div class="mt-8 grid grid-cols-2 gap-4">
                <div class="bg-white/20 backdrop-blur rounded-xl p-4 border border-white/30">
                    <p class="text-sm opacity-80 uppercase font-bold tracking-wider">Aujourd'hui</p>
                    <p class="text-3xl font-bold mt-1">{{ $todayVoyages->count() }}</p>
                    <p class="text-xs mt-1">Voyage(s)</p>
                </div>
                <div class="bg-white/20 backdrop-blur rounded-xl p-4 border border-white/30">
                    <p class="text-sm opacity-80 uppercase font-bold tracking-wider">A venir</p>
                    <p class="text-3xl font-bold mt-1">{{ $upcomingVoyages->count() }}</p>
                    <p class="text-xs mt-1">Prochains jours</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Today's Trips -->
            <section>
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center">
                        <i class="fas fa-calendar-day text-orange-500"></i>
                    </div>
                    Voyages du jour
                </h3>

                @if($todayVoyages->count() > 0)
                    <div class="space-y-4">
                        @foreach($todayVoyages as $voyage)
                            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition-shadow">
                                <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-orange-600 font-bold text-xl border border-orange-100">
                                    {{ \Carbon\Carbon::parse($voyage->programme->heure_depart)->format('H:i') }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <span class="font-bold text-gray-900">{{ $voyage->gareDepart->nom_gare }}</span>
                                        <i class="fas fa-long-arrow-alt-right text-orange-300"></i>
                                        <span class="font-bold text-gray-900">{{ $voyage->gareArrivee->nom_gare }}</span>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-bus mr-1"></i> {{ $voyage->vehicule->immatriculation }}
                                        </span>
                                    </p>
                                </div>
                                <div class="flex sm:flex-col items-center sm:items-end gap-2">
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full uppercase">
                                        {{ $voyage->statut }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 bg-white rounded-2xl border border-dashed border-gray-200 shadow-sm">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-road text-gray-300 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 font-medium">Aucun voyage prévu aujourd'hui</p>
                        <a href="{{ route('chauffeur.programmes') }}" class="text-orange-600 font-bold mt-2 inline-block hover:text-orange-700 transition">
                            S'assigner un horaire &rarr;
                        </a>
                    </div>
                @endif
            </section>

            <!-- Upcoming Trips -->
            <section>
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-clock text-blue-500"></i>
                    </div>
                    Voyages à venir
                </h3>

                @if($upcomingVoyages->count() > 0)
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($upcomingVoyages as $voyage)
                            <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-3">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1">
                                        <i class="far fa-calendar"></i>
                                        {{ \Carbon\Carbon::parse($voyage->date_voyage)->translatedFormat('d F Y') }}
                                    </span>
                                    <span class="text-sm font-bold text-gray-900 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">
                                        {{ \Carbon\Carbon::parse($voyage->programme->heure_depart)->format('H:i') }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="font-semibold text-gray-800 text-sm">{{ $voyage->gareDepart->nom_gare }}</span>
                                    <i class="fas fa-long-arrow-alt-right text-gray-300 text-xs"></i>
                                    <span class="font-semibold text-gray-800 text-sm">{{ $voyage->gareArrivee->nom_gare }}</span>
                                </div>
                                <div class="text-xs text-gray-500 flex items-center justify-between border-t border-dashed pt-3 mt-2">
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-bus text-gray-400"></i>
                                        {{ $voyage->vehicule->immatriculation }}
                                    </span>
                                    <span class="capitalize bg-gray-50 px-2 py-0.5 rounded text-gray-600">{{ $voyage->statut }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 bg-white rounded-2xl text-center shadow-sm border border-gray-100">
                        <p class="text-gray-500 text-sm italic">Aucun voyage futur programmé.</p>
                    </div>
                @endif
            </section>
        </div>

        <!-- Quick Action -->
        <a href="{{ route('chauffeur.programmes') }}" class="block mt-8 group">
            <div class="bg-white rounded-2xl p-6 shadow-md border border-orange-100 flex items-center justify-between group-hover:shadow-lg transition-all duration-300 transform group-hover:-translate-y-1">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center group-hover:bg-orange-600 transition-colors duration-300">
                        <i class="fas fa-plus text-orange-600 text-xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">Nouveau Voyage</h3>
                        <p class="text-gray-500 text-sm">S'assigner à une nouvelle course disponible</p>
                    </div>
                </div>
                <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center">
                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-orange-600"></i>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
