@extends('chauffeur.layouts.template')

@section('title', 'Mon Historique')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-4 px-3 sm:py-8 sm:px-4">
    <div class="mx-auto" style="width: 100%">
        <div class="mb-4 sm:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div>
                <h2 class="text-xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">Historique de mes missions</h2>
                <p class="text-gray-500 text-sm sm:text-lg">Voyages et convois effectués</p>
            </div>
        </div>

        {{-- ── Voyages ── --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-8">
            <div class="px-4 py-3 sm:px-6 sm:py-4 bg-gradient-to-r from-orange-50 to-amber-50 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center">
                    <i class="fas fa-route text-orange-500 text-sm"></i>
                </div>
                <h3 class="font-bold text-gray-800 text-sm sm:text-base">Voyages effectués</h3>
                <span class="ml-auto text-xs text-gray-400 font-semibold">{{ $voyages->total() }} au total</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 sm:px-6 sm:py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-3 py-3 sm:px-6 sm:py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Trajet</th>
                            <th class="px-3 py-3 sm:px-6 sm:py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Véhicule</th>
                            <th class="px-3 py-3 sm:px-6 sm:py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($voyages as $voyage)
                            <tr class="hover:bg-gray-50 transition group">
                                <td class="px-3 py-3 sm:px-6 sm:py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-9 h-9 sm:w-10 sm:h-10 bg-orange-50 text-orange-600 rounded-lg flex items-center justify-center mr-2 sm:mr-3 font-bold text-xs flex-shrink-0">
                                            {{ \Carbon\Carbon::parse($voyage->date_voyage)->format('d') }}
                                            <span class="text-[9px] uppercase ml-0.5">{{ \Carbon\Carbon::parse($voyage->date_voyage)->format('M') }}</span>
                                        </div>
                                        <div>
                                            <div class="text-xs sm:text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($voyage->date_voyage)->format('d/m/Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($voyage->programme->heure_depart)->format('H:i') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3 sm:px-6 sm:py-4">
                                    <div class="text-xs sm:text-sm text-gray-900 flex flex-col sm:flex-row sm:items-center sm:gap-2">
                                        <span class="font-semibold truncate max-w-[100px] sm:max-w-none">{{ $voyage->gareDepart->nom_gare }}</span>
                                        <i class="fas fa-long-arrow-alt-right text-gray-400 hidden sm:inline"></i>
                                        <i class="fas fa-arrow-down text-gray-300 text-xs sm:hidden"></i>
                                        <span class="font-semibold truncate max-w-[100px] sm:max-w-none">{{ $voyage->gareArrivee->nom_gare }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-3 sm:px-6 sm:py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                                    <div class="flex items-center bg-gray-100 rounded-lg px-2 py-1 w-fit">
                                        <i class="fas fa-bus mr-2 text-gray-400"></i>
                                        {{ $voyage->vehicule->immatriculation }}
                                    </div>
                                </td>
                                <td class="px-3 py-3 sm:px-6 sm:py-4 whitespace-nowrap">
                                    @php
                                        $vStatusClasses = [
                                            'en_attente' => 'bg-yellow-100 text-yellow-800',
                                            'en_cours'   => 'bg-blue-100 text-blue-800',
                                            'terminé'    => 'bg-green-100 text-green-800',
                                            'annulé'     => 'bg-red-100 text-red-800',
                                        ];
                                        $vStatusClass = $vStatusClasses[$voyage->statut] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full uppercase {{ $vStatusClass }}">
                                        {{ $voyage->statut }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500 italic">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-history text-gray-300 text-3xl mb-2"></i>
                                        Aucun historique de voyage trouvé.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-3 py-3 sm:px-6 sm:py-4 border-t border-gray-100 bg-gray-50">
                {{ $voyages->links() }}
            </div>
        </div>

        {{-- ── Convois terminés ── --}}
        @if(isset($convoisHistory) && $convoisHistory->count() > 0)
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="px-4 py-3 sm:px-6 sm:py-4 bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <i class="fas fa-shuttle-van text-indigo-500 text-sm"></i>
                </div>
                <h3 class="font-bold text-gray-800 text-sm sm:text-base">Convois effectués</h3>
                <span class="ml-auto text-xs text-gray-400 font-semibold">{{ $convoisHistory->count() }} au total</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 sm:px-6 sm:py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date départ</th>
                            <th class="px-3 py-3 sm:px-6 sm:py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Référence / Trajet</th>
                            <th class="px-3 py-3 sm:px-6 sm:py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Véhicule</th>
                            <th class="px-3 py-3 sm:px-6 sm:py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Passagers</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($convoisHistory as $convoi)
                        @php
                            $trajetConvoi = $convoi->itineraire
                                ? $convoi->itineraire->point_depart . ' → ' . $convoi->itineraire->point_arrive
                                : (($convoi->lieu_depart ?? '') . ' → ' . ($convoi->lieu_retour ?? ''));
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-3 py-3 sm:px-6 sm:py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center mr-2 sm:mr-3 font-bold text-xs flex-shrink-0">
                                        @if($convoi->date_depart)
                                            {{ \Carbon\Carbon::parse($convoi->date_depart)->format('d') }}
                                            <span class="text-[9px] uppercase ml-0.5">{{ \Carbon\Carbon::parse($convoi->date_depart)->format('M') }}</span>
                                        @else
                                            <i class="fas fa-route text-xs"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-xs sm:text-sm font-bold text-gray-900">
                                            {{ $convoi->date_depart ? \Carbon\Carbon::parse($convoi->date_depart)->format('d/m/Y') : '-' }}
                                        </div>
                                        @if($convoi->date_retour)
                                        <div class="text-xs text-indigo-500 font-semibold">
                                            <i class="fas fa-undo-alt text-[9px] mr-0.5"></i>Retour : {{ \Carbon\Carbon::parse($convoi->date_retour)->format('d/m/Y') }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-3 sm:px-6 sm:py-4">
                                <div class="text-xs font-black text-indigo-600 uppercase tracking-wider mb-0.5">{{ $convoi->reference ?? '-' }}</div>
                                <div class="text-xs sm:text-sm text-gray-700 font-semibold">{{ $trajetConvoi }}</div>
                            </td>
                            <td class="px-3 py-3 sm:px-6 sm:py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                                @if($convoi->vehicule)
                                <div class="flex items-center bg-gray-100 rounded-lg px-2 py-1 w-fit">
                                    <i class="fas fa-bus mr-2 text-gray-400"></i>
                                    {{ $convoi->vehicule->immatriculation }}
                                </div>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 sm:px-6 sm:py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex items-center gap-1 text-xs font-bold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-users text-[10px]"></i>
                                    {{ $convoi->nombre_personnes }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
