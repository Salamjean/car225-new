@extends('compagnie.layouts.template')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Détails des Réservations</h1>
                <p class="text-gray-500 mt-1">Suivi détaillé de la billetterie et des passagers</p>
            </div>
            <div class="mt-4 md:mt-0 flex gap-3">
                <a href="{{ route('company.reservation.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                    <i class="mdi mdi-arrow-left mr-2"></i> Retour
                </a>
                <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors shadow-sm">
                    <i class="mdi mdi-printer mr-2"></i> Imprimer
                </button>
            </div>
        </div>

        <!-- Cartes KPI -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Stock Tickets -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-[#e94f1b]">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Stock Tickets Restant</p>
                        <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stockTickets, 0, ',', ' ') }}</h3>
                    </div>
                    <div class="p-3 bg-orange-50 rounded-lg">
                        <i class="mdi mdi-ticket-percent text-[#e94f1b] text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-500">
                    Disponible sur le compte
                </div>
            </div>

            <!-- Tickets Consommés (Sélection) -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Tickets Écoulés</p>
                        <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($ticketsConsommes, 0, ',', ' ') }}</h3>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <i class="mdi mdi-ticket-confirmation text-blue-500 text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-500">
                    Sur la période sélectionnée
                </div>
            </div>

            <!-- Revenu (Sélection) -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Chiffre d'Affaires</p>
                        <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($revenuTotal, 0, ',', ' ') }} <span class="text-lg text-gray-500">FCFA</span></h3>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg">
                        <i class="mdi mdi-cash-multiple text-green-500 text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-500">
                    Montant total des ventes
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8 border border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i class="mdi mdi-filter-variant mr-2"></i> Filtres de recherche
            </h3>
            <form action="{{ route('company.reservation.details') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Recherche -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Réf, Nom, Tél..." 
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-[#e94f1b] focus:border-[#e94f1b]">
                        <i class="mdi mdi-magnify absolute left-3 top-2.5 text-gray-400"></i>
                    </div>
                </div>

                <!-- Période -->
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Du</label>
                        <input type="date" name="date_debut" value="{{ request('date_debut') }}" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#e94f1b] focus:border-[#e94f1b]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Au</label>
                        <input type="date" name="date_fin" value="{{ request('date_fin') }}" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#e94f1b] focus:border-[#e94f1b]">
                    </div>
                </div>

                <!-- Programme -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Programme / Trajet</label>
                    <select name="programme_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#e94f1b] focus:border-[#e94f1b]">
                        <option value="all">Tous les programmes</option>
                        @foreach($programmes as $prog)
                            <option value="{{ $prog->id }}" {{ request('programme_id') == $prog->id ? 'selected' : '' }}>
                                {{ $prog->point_depart }} → {{ $prog->point_arrive }} ({{ substr($prog->heure_depart, 0, 5) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Statut -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="statut" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#e94f1b] focus:border-[#e94f1b]">
                        <option value="all">Tous les statuts</option>
                        <option value="confirmee" {{ request('statut') == 'confirmee' ? 'selected' : '' }}>Confirmée</option>
                        <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="terminee" {{ request('statut') == 'terminee' ? 'selected' : '' }}>Terminée</option>
                        <option value="annulee" {{ request('statut') == 'annulee' ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>

                <!-- Type de vente -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de vente</label>
                    <select name="type_vente" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#e94f1b] focus:border-[#e94f1b]">
                        <option value="all">Tous types</option>
                        <option value="ligne" {{ request('type_vente') === 'ligne' ? 'selected' : '' }}>Vente en ligne</option>
                        <option value="caisse" {{ request('type_vente') === 'caisse' ? 'selected' : '' }}>Vente en caisse</option>
                    </select>
                </div>

                <!-- Boutons -->
                <div class="col-span-full flex justify-end gap-3 mt-2">
                    <a href="{{ route('company.reservation.details') }}" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 font-medium transition-colors">
                        Réinitialiser
                    </a>
                    <button type="submit" class="px-5 py-2 bg-[#e94f1b] text-white rounded-lg hover:bg-orange-600 font-medium transition-colors shadow-md">
                        Filtrer les résultats
                    </button>
                </div>
            </form>
        </div>

        <!-- Tableau des résultats -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900">Liste des réservations</h3>
                <span class="bg-white px-3 py-1 rounded-full text-sm font-bold text-gray-600 border border-gray-200">
                    {{ $reservations->total() }} résultats
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date & Réf</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Passager</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Trajet & Véhicule</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Place</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Ticket</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($reservations as $reservation)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <!-- Date & Réf -->
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-900">
                                        {{ \Carbon\Carbon::parse($reservation->created_at)->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="text-xs font-mono text-gray-500 bg-gray-100 inline-block px-2 py-0.5 rounded mt-1">
                                        {{ $reservation->reference }}
                                    </div>
                                </td>

                                <!-- Passager -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs mr-3">
                                            {{ substr($reservation->passager_nom, 0, 1) }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-gray-900">{{ $reservation->passager_prenom }} {{ $reservation->passager_nom }}</div>
                                            <div class="text-xs text-gray-500">{{ $reservation->passager_telephone }}</div>
                                            @if($reservation->hotesse_id)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200 mt-1">
                                                    <i class="mdi mdi-account-tie mr-1"></i>
                                                    Vente Hôtesse
                                                </span>
                                            @elseif($reservation->caisse_id)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200 mt-1">
                                                    <i class="mdi mdi-cash-register mr-1"></i>
                                                    Vente Caisse
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200 mt-1">
                                                    <i class="mdi mdi-earth mr-1"></i>
                                                    Vente en Ligne
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Trajet -->
                                <td class="px-6 py-4">
                                    @if($reservation->programme)
                                        <div class="text-sm text-gray-900">
                                            <span class="font-medium">{{ $reservation->programme->point_depart }}</span> 
                                            <span class="text-gray-400">→</span> 
                                            <span class="font-medium">{{ $reservation->programme->point_arrive }}</span>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Voyage du {{ \Carbon\Carbon::parse($reservation->date_voyage)->format('d/m/Y') }} à {{ substr($reservation->programme->heure_depart, 0, 5) }}
                                        </div>
                                    @else
                                        <span class="text-red-500 italic text-sm">Programme supprimé</span>
                                    @endif
                                </td>

                                <!-- Place -->
                                <td class="px-6 py-4 text-center">
                                     <button type="button" 
                                             onclick="showOccupiedSeats({{ $reservation->programme_id }}, '{{ $reservation->date_voyage->format('Y-m-d') }}', {{ $reservation->seat_number }})"
                                             class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 text-gray-700 font-bold rounded-lg border border-gray-200 hover:bg-[#e94f1b] hover:text-white hover:border-[#e94f1b] transition-all cursor-pointer shadow-sm"
                                             title="Voir la carte des places">
                                        {{ $reservation->seat_number }}
                                     </button>
                                     @if($reservation->programme && $reservation->programme->vehicule)
                                        @php
                                            $totalPlaces = $reservation->programme->vehicule->nombre_place;
                                            $placesOccupees = \App\Models\Reservation::where('programme_id', $reservation->programme_id)
                                                ->where('date_voyage', $reservation->date_voyage)
                                                ->where('statut', 'confirmee')
                                                ->count();
                                            $placesRestantes = max(0, $totalPlaces - $placesOccupees);
                                        @endphp
                                        <div class="mt-1 text-[10px] font-medium {{ $placesRestantes > 5 ? 'text-green-600' : 'text-red-500' }}">
                                            {{ $placesRestantes }} place(s) restante(s)
                                        </div>
                                     @endif
                                </td>

                                <!-- Ticket (Débit) -->
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="text-sm font-bold text-gray-800">1</span>
                                        <span class="text-[10px] uppercase text-gray-400">Débité</span>
                                    </div>
                                </td>

                                <!-- Montant -->
                                <td class="px-6 py-4 text-right">
                                    <div class="text-sm font-bold text-[#e94f1b]">
                                        {{ number_format($reservation->montant, 0, ',', ' ') }} F
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $reservation->paiement ? ucfirst($reservation->paiement->payment_method) : '-' }}</div>
                                </td>

                                <!-- Statut -->
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusClasses = [
                                            'confirmee' => 'bg-green-100 text-green-800 border-green-200',
                                            'en_attente' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                            'annulee' => 'bg-red-100 text-red-800 border-red-200',
                                            'terminee' => 'bg-gray-100 text-gray-800 border-gray-200',
                                        ];
                                        $statusClass = $statusClasses[$reservation->statut] ?? 'bg-blue-100 text-blue-800';
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-medium border {{ $statusClass }}">
                                        {{ ucfirst($reservation->statut) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="mdi mdi-clipboard-text-off text-4xl text-gray-300 mb-3"></i>
                                        <p>Aucune réservation trouvée pour ces critères.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $reservations->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showOccupiedSeats(programmeId, dateVoyage, currentSeat) {
        // Afficher un loader
        Swal.fire({
            title: 'Chargement du plan...',
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Appel AJAX
        fetch(`{{ route('company.reservation.occupied-seats') }}?programme_id=${programmeId}&date_voyage=${dateVoyage}`)
            .then(response => response.json())
            .then(data => {
                if(data.error) throw new Error(data.error);

                // Générer la grille
                let seatsHtml = '<div class="grid grid-cols-4 gap-2 max-h-[400px] overflow-y-auto p-2">';
                const totalSeats = data.total_seats;
                const occupied = data.occupied; // Array of ints
                
                for (let i = 1; i <= totalSeats; i++) {
                    let statusClass = '';
                    let icon = 'mdi-seat-recline-normal';
                    let title = `Place ${i}`;

                    if (i == currentSeat) {
                        statusClass = 'bg-blue-500 text-white ring-2 ring-blue-300 ring-offset-1';
                        title += ' (Cette réservation)';
                    } else if (occupied.includes(i)) {
                        statusClass = 'bg-red-500 text-white';
                        title += ' (Occupée)';
                    } else {
                        statusClass = 'bg-gray-100 text-gray-500 hover:bg-green-100 hover:text-green-600 transition-colors';
                        title += ' (Libre)';
                    }

                    seatsHtml += `
                        <div class="flex flex-col items-center justify-center p-2 rounded-lg ${statusClass}" title="${title}">
                            <i class="mdi ${icon} text-xl"></i>
                            <span class="text-sm font-bold">${i}</span>
                        </div>
                    `;
                }
                seatsHtml += '</div>';

                // Afficher le popup
                Swal.fire({
                    title: 'Plan des sièges',
                    html: `
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 font-medium mb-1"><i class="mdi mdi-bus"></i> ${data.vehicle_name}</p>
                            <p class="text-xs text-gray-500 flex justify-center gap-3">
                                <span class="flex items-center"><span class="w-3 h-3 bg-red-500 rounded-full mr-1"></span> Occupé (${occupied.length})</span>
                                <span class="flex items-center"><span class="w-3 h-3 bg-gray-100 border border-gray-300 rounded-full mr-1"></span> Libre (${totalSeats - occupied.length})</span>
                                <span class="flex items-center"><span class="w-3 h-3 bg-blue-500 rounded-full mr-1"></span> Actuel</span>
                            </p>
                        </div>
                        ${seatsHtml}
                    `,
                    width: '400px',
                    showConfirmButton: true,
                    confirmButtonText: 'Fermer',
                    confirmButtonColor: '#e94f1b'
                });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: "Impossible de charger le plan des sièges.",
                    confirmButtonColor: '#e94f1b'
                });
                console.error(error);
            });
    }
</script>
@endsection
