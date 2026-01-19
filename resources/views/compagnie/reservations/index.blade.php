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

            <!-- Bouton Actualiser -->
            <button onclick="window.location.reload();"
                class="inline-flex items-center px-6 py-3 bg-white text-gray-700 font-bold rounded-xl hover:bg-gray-50 transform hover:-translate-y-1 transition-all duration-200 shadow-md hover:shadow-lg border border-gray-200">
                <i class="mdi mdi-refresh mr-2 text-xl"></i>
                Actualiser
            </button>
        </div>

        <!-- Statistiques rapides -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Réservations En Cours</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $reservationsEnCours->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="mdi mdi-ticket-account text-orange-500 text-2xl"></i>
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
                            {{ number_format($reservationsEnCours->sum('montant_total'), 0, ',', ' ') }} FCFA
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
                   class="flex-1 text-center py-4 px-6 text-sm font-bold uppercase tracking-wider transition-colors duration-200 {{ request('tab') == 'en-cours' || !request('tab') ? 'text-orange-600 border-b-4 border-orange-500 bg-orange-50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
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
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Référence</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Client</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Date & Heure</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Passagers</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Montant</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($reservationsTerminees as $reservation)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-bold text-gray-900">{{ $reservation->reference }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $reservation->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $reservation->user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="text-sm font-bold text-gray-900">
                                                {{ \Carbon\Carbon::parse($reservation->date_voyage)->format('d/m/Y') }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($reservation->programme->heure_depart)->format('H:i') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php 
                                                $passagersList = is_array($reservation->passagers) ? $reservation->passagers : json_decode($reservation->passagers, true);
                                                $vehicule = $reservation->programme->vehicule ?? null;
                                            @endphp
                                            <button onclick='showReservationFullDetails(
                                                @json($passagersList), 
                                                @json($vehicule), 
                                                "{{ \Carbon\Carbon::parse($reservation->date_voyage)->format("d/m/Y") }}", 
                                                "{{ \Carbon\Carbon::parse($reservation->programme->heure_depart)->format("H:i") }}",
                                                "{{ $reservation->reference }}"
                                            )'
                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                                                <i class="mdi mdi-account-group mr-2 text-gray-500"></i>
                                                {{ count($passagersList ?? []) }} Passager(s)
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-gray-600">
                                            {{ number_format($reservation->montant_total, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($reservation->statut == 'annulee')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Annulée
                                                </span>
                                            @elseif(\Carbon\Carbon::parse($reservation->date_voyage)->isPast())
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Terminée
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
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Client</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Trajet</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Date & Heure</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Détails</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Montant</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($reservationsEnCours as $reservation)
                                    <tr class="hover:bg-orange-50 transition-colors duration-200 group">
                                        <td class="px-6 py-4 whitespace-nowrap" style="display: flex; align-items: center; justify-content: center;">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-500 font-bold mr-3">
                                                    {{ substr($reservation->user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-gray-900">{{ $reservation->user->name.' '.$reservation->user->prenom }}</div>
                                                    <div class="text-xs text-gray-500">{{ $reservation->user->contact ?? 'Inconnu' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($reservation->programme->itineraire)
                                                <div class="text-sm font-medium text-gray-900" style="display: flex; align-items: center; justify-content: center;">
                                                    {{ $reservation->programme->itineraire->point_depart }} 
                                                    <i class="mdi mdi-arrow-right mx-1 text-gray-400"></i>
                                                    {{ $reservation->programme->itineraire->point_arrive }}
                                                </div>
                                            @else
                                                <span class="text-gray-400 italic">Trajet inconnu</span>
                                            @endif
                                            <div class="text-xs text-gray-500 mt-1 text-center" >Ref: <span class="font-mono">{{ $reservation->reference }}</span></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="text-sm font-bold text-gray-900">
                                                {{ \Carbon\Carbon::parse($reservation->date_voyage)->format('d M') }}
                                            </div>
                                            <div class="text-xs text-gray-500 font-medium bg-gray-100 rounded-lg px-2 py-1 inline-block mt-1">
                                                {{ \Carbon\Carbon::parse($reservation->programme->heure_depart)->format('H:i') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php 
                                                $passagersList = is_array($reservation->passagers) ? $reservation->passagers : json_decode($reservation->passagers, true);
                                                $vehicule = $reservation->programme->vehicule ?? null;
                                            @endphp
                                            <button onclick='showReservationFullDetails(
                                                @json($passagersList), 
                                                @json($vehicule), 
                                                "{{ \Carbon\Carbon::parse($reservation->date_voyage)->format("d/m/Y") }}", 
                                                "{{ \Carbon\Carbon::parse($reservation->programme->heure_depart)->format("H:i") }}",
                                                "{{ $reservation->reference }}"
                                            )'
                                            class="inline-flex items-center px-3 py-1.5 border border-orange-200 shadow-sm text-xs font-medium rounded-full text-orange-700 bg-orange-50 hover:bg-orange-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                                                <i class="mdi mdi-eye mr-2"></i>
                                                Voir {{ count($passagersList ?? []) }} Passager(s)
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="text-sm font-bold text-green-600">
                                                {{ number_format($reservation->montant_total, 0, ',', ' ') }} F
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
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showReservationFullDetails(passagers, vehicule, dateVoyage, heureDepart, reference) {
        if (!passagers) passagers = [];
        
        // Construction de la section Véhicule
        let vehiculeHtml = '';
        if (vehicule) {
            vehiculeHtml = `
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 mb-4 text-left">
                    <div class="flex items-center mb-2">
                        <i class="mdi mdi-bus text-orange-500 text-xl mr-2"></i>
                        <span class="font-bold text-gray-800">Détails du Véhicule</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <span class="block text-xs text-gray-500">Marque/Modèle</span>
                            <span class="font-medium text-gray-900">${vehicule.marque} ${vehicule.modele}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500">Immatriculation</span>
                            <span class="font-medium text-gray-900 uppercase bg-white border px-1 rounded inline-block">${vehicule.immatriculation}</span>
                        </div>
                    </div>
                    <div class="mt-2 text-sm border-t pt-2 border-gray-200">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Date du voyage</span>
                            <span class="font-bold text-gray-900">${dateVoyage} à ${heureDepart}</span>
                        </div>
                    </div>
                </div>
            `;
        } else {
             vehiculeHtml = `
                <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200 mb-4 text-left">
                    <span class="text-sm text-yellow-700 italic">Aucune information de véhicule disponible</span>
                </div>
            `;
        }

        // Construction de la liste des passagers
        let passagersHtml = '<div class="text-left"><h4 class="font-bold text-gray-700 mb-2 flex items-center"><i class="mdi mdi-account-group mr-2"></i>Liste des Passagers (' + passagers.length + ')</h4><div class="space-y-2 max-h-60 overflow-y-auto pr-1">';
        
        passagers.forEach(p => {
            passagersHtml += `
                <div class="flex justify-between items-center bg-white border border-gray-200 p-2.5 rounded-lg shadow-sm hover:border-orange-300 transition-colors">
                    <div class="text-left">
                        <div class="font-bold text-gray-800">${p.prenom} ${p.nom}</div>
                        <div class="text-xs text-gray-500 flex items-center mt-0.5">
                            <i class="mdi mdi-phone mr-1"></i> ${p.telephone || 'Non renseigné'}
                        </div>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="text-xs font-mono bg-orange-100 text-orange-800 border border-orange-200 px-2 py-1 rounded mb-1">Siège ${p.seat_number || '?'}</span>
                    </div>
                </div>
            `;
        });
        passagersHtml += '</div></div>';

        Swal.fire({
            title: `<div class="flex flex-col pb-2 border-b">
                        <span class="text-lg text-gray-600 font-medium">Réservation</span>
                        <span class="text-2xl font-bold text-orange-600">${reference}</span>
                    </div>`,
            html: `
                <div class="mt-4">
                    ${vehiculeHtml}
                    ${passagersHtml}
                </div>
            `,
            width: 550,
            showCloseButton: true,
            showConfirmButton: false,
            focusConfirm: false,
            customClass: {
                popup: 'rounded-2xl shadow-2xl',
                closeButton: 'text-gray-400 hover:text-gray-600'
            }
        });
    }
</script>
@endsection