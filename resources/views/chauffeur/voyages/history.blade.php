@extends('chauffeur.layouts.template')

@section('title', 'Mon Historique')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Historique de mes voyages</h2>
                <p class="text-gray-500 text-lg">Liste exhaustive de vos courses effectuées ou prévues</p>
            </div>
            <!--
            <div class="flex gap-2">
                <button class="bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-200 text-gray-700 font-medium hover:bg-gray-50">Exporter PDF</button>
            </div>
            -->
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date & Heure</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Trajet</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Véhicule</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Statut</th>
                            <!-- <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th> -->
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($voyages as $voyage)
                            <tr class="hover:bg-gray-50 transition group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-lg flex items-center justify-center mr-3 font-bold text-xs">
                                            {{ \Carbon\Carbon::parse($voyage->date_voyage)->format('d') }}
                                            <span class="text-[9px] uppercase ml-0.5">{{ \Carbon\Carbon::parse($voyage->date_voyage)->format('M') }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($voyage->date_voyage)->format('d/m/Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($voyage->programme->heure_depart)->format('H:i') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 flex items-center gap-2">
                                        <span class="font-semibold">{{ $voyage->gareDepart->nom_gare }}</span>
                                        <i class="fas fa-long-arrow-alt-right text-gray-400"></i>
                                        <span class="font-semibold">{{ $voyage->gareArrivee->nom_gare }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center bg-gray-100 rounded-lg px-2 py-1 w-fit">
                                        <i class="fas fa-bus mr-2 text-gray-400"></i>
                                        {{ $voyage->vehicule->immatriculation }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClasses = [
                                            'en_attente' => 'bg-yellow-100 text-yellow-800',
                                            'en_cours' => 'bg-blue-100 text-blue-800',
                                            'termine' => 'bg-green-100 text-green-800',
                                            'annule' => 'bg-red-100 text-red-800',
                                        ];
                                        $statusClass = $statusClasses[$voyage->statut] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full uppercase {{ $statusClass }}">
                                        {{ $voyage->statut }}
                                    </span>
                                </td>
                                <!--
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#" class="text-orange-600 hover:text-orange-900 bg-orange-50 hover:bg-orange-100 p-2 rounded-lg transition-colors">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                                -->
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 italic">
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
            
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $voyages->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
