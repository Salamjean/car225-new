@extends('compagnie.layouts.template')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4">
    <div class="mx-auto" style="width: 100%">
        <!-- En-tête -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
            <div class="mb-6 lg:mb-0">
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-3">Gestion des Réservations</h1>
                <p class="text-lg text-gray-600">
                    Suivez et gérez les réservations de vos voyages
                </p>
            </div>

            <!-- Actions -->
            <div class="flex flex-col md:flex-row gap-4">
                <a href="{{ route('company.reservation.details') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-[#e94f1b] text-white font-bold rounded-xl hover:bg-orange-600 transform hover:-translate-y-1 transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="mdi mdi-chart-bar mr-2 text-xl"></i>
                    Détails & Statistiques
                </a>

                <button onclick="window.location.reload();"
                    class="inline-flex items-center justify-center px-6 py-3 bg-white text-gray-700 font-bold rounded-xl hover:bg-gray-50 transform hover:-translate-y-1 transition-all duration-200 shadow-md hover:shadow-lg border border-gray-200">
                    <i class="mdi mdi-refresh mr-2 text-xl"></i>
                    Actualiser
                </button>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Réservations En Cours</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $reservationsEnCours->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <i class="mdi mdi-ticket-account text-red-500 text-2xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-gray-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Historique Total</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $reservationsTerminees->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                        <i class="mdi mdi-history text-gray-500 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Revenus En Cours</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">
                            {{ number_format($reservationsEnCours->sum('montant'), 0, ',', ' ') }} FCFA
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="mdi mdi-cash-multiple text-green-500 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte principale -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden min-h-[500px]">
            <!-- Navigation Onglets -->
            <div class="flex border-b border-gray-200">
                <a href="?tab=en-cours" 
                   class="flex-1 text-center py-4 px-6 text-sm font-bold uppercase tracking-wider transition-colors duration-200 {{ request('tab') == 'en-cours' || !request('tab') ? 'text-red-600 border-b-4 border-red-500 bg-red-50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                   <i class="mdi mdi-clock-outline mr-2 text-lg"></i> En cours / A venir
                </a>
                <a href="?tab=terminees" 
                   class="flex-1 text-center py-4 px-6 text-sm font-bold uppercase tracking-wider transition-colors duration-200 {{ request('tab') == 'terminees' ? 'text-gray-800 border-b-4 border-gray-600 bg-gray-50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                   <i class="mdi mdi-history mr-2 text-lg"></i> Terminées / Passées
                </a>
            </div>

            <!-- Contenu -->
            <div class="p-0">
                @if(request('tab') == 'terminees')
                    <!-- Table Terminées -->
                    @if($reservationsTerminees->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Réf</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Passager</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Trajet</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Date & Heure</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Place</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Montant</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($reservationsTerminees as $reservation)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">{{ $reservation->reference }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">{{ $reservation->passager_prenom }} {{ $reservation->passager_nom }}</div>
                                            <div class="text-xs text-gray-500">{{ $reservation->passager_telephone ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($reservation->programme)
                                                <div class="text-sm text-gray-900">
                                                    {{ $reservation->programme->point_depart }} → {{ $reservation->programme->point_arrive }}
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="text-sm font-bold text-gray-900">
                                                {{ \Carbon\Carbon::parse($reservation->date_voyage)->format('d/m/Y') }}
                                            </div>
                                            @if($reservation->programme)
                                            <div class="text-xs text-gray-500">
                                                {{ $reservation->programme->heure_depart }}
                                            </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-800 font-bold rounded">
                                                {{ $reservation->seat_number }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-gray-600">
                                            {{ number_format($reservation->montant, 0, ',', ' ') }} F
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($reservation->statut == 'terminee')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="mdi mdi-check-circle mr-1"></i> Scannée
                                                </span>
                                            @elseif($reservation->statut == 'annulee')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i class="mdi mdi-close-circle mr-1"></i> Annulée
                                                </span>
                                            @elseif(\Carbon\Carbon::parse($reservation->date_voyage)->isPast())
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <i class="mdi mdi-clock-outline mr-1"></i> Passée
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ ucfirst($reservation->statut) }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $reservationsTerminees->appends(['tab' => 'terminees'])->links() }}
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                            <i class="mdi mdi-history text-6xl mb-4 text-gray-300"></i>
                            <p class="text-lg font-medium">Aucune réservation passée</p>
                        </div>
                    @endif

                @else
                    <!-- Table En Cours (Défaut) -->
                    @if($reservationsEnCours->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Passager</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Trajet</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Date & Heure</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Place</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Montant</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($reservationsEnCours as $reservation)
                                    <tr class="hover:bg-red-50 transition-colors duration-200 group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center text-red-500 font-bold mr-3">
                                                    {{ substr($reservation->passager_prenom, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-gray-900">{{ $reservation->passager_prenom }} {{ $reservation->passager_nom }}</div>
                                                    <div class="text-xs text-gray-500">{{ $reservation->passager_telephone ?? 'N/A' }}</div>
                                                    <div class="text-xs text-gray-400 font-mono">{{ $reservation->reference }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($reservation->programme)
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $reservation->programme->point_depart }} 
                                                    <i class="mdi mdi-arrow-right mx-1 text-gray-400"></i>
                                                    {{ $reservation->programme->point_arrive }}
                                                </div>
                                            @else
                                                <span class="text-gray-400 italic">Trajet inconnu</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="text-sm font-bold text-gray-900">
                                                {{ \Carbon\Carbon::parse($reservation->date_voyage)->format('d M') }}
                                            </div>
                                            @if($reservation->programme)
                                            <div class="text-xs text-gray-500 font-medium bg-gray-100 rounded-lg px-2 py-1 inline-block mt-1">
                                                {{ $reservation->programme->heure_depart }}
                                            </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center justify-center w-10 h-10 bg-red-100 text-red-800 font-bold rounded-lg text-lg">
                                                {{ $reservation->seat_number }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="text-sm font-bold text-green-600">
                                                {{ number_format($reservation->montant, 0, ',', ' ') }} F
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($reservation->statut == 'confirmee')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span> Confirmée
                                                </span>
                                            @elseif($reservation->statut == 'en_attente')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                    <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span> En attente
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ ucfirst($reservation->statut) }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $reservationsEnCours->appends(['tab' => 'en-cours'])->links() }}
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                            <i class="mdi mdi-calendar-blank text-6xl mb-4 text-gray-300"></i>
                            <p class="text-lg font-medium">Aucune réservation en cours</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection