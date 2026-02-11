@extends('hotesse.layouts.template')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Titre -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Vendre des Tickets</h1>
            <p class="text-gray-600">Interface de vente hôtesse</p>
        </div>

        <!-- Zone de Recherche -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Rechercher un voyage</h2>
            
            <form action="{{ route('hotesse.vendre-ticket') }}" method="GET" id="search-form">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-end">
                    <!-- Départ -->
                    <div class="relative lg:col-span-3">
                        <label for="point_depart" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt text-[#e94f1b] mr-2"></i>Point de départ
                        </label>
                        <div class="relative">
                            <input type="text" id="point_depart" name="point_depart" 
                                value="{{ $searchParams['point_depart'] ?? '' }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 pl-10" 
                                placeholder="Ville de départ">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Bouton d'inversion -->
                    <div class="lg:col-span-1 flex items-end justify-center pb-2">
                        <button type="button" onclick="swapLocations()" 
                            class="w-10 h-10 bg-[#e94f1b] text-white rounded-full hover:bg-orange-600 transition-all duration-300 transform hover:scale-110 shadow-lg flex items-center justify-center"
                            title="Inverser départ/arrivée">
                            <i class="fas fa-exchange-alt"></i>
                        </button>
                    </div>

                    <!-- Arrivée -->
                    <div class="relative lg:col-span-3">
                        <label for="point_arrive" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-flag text-green-500 mr-2"></i>Point d'arrivée
                        </label>
                        <div class="relative">
                            <input type="text" id="point_arrive" name="point_arrive" 
                                value="{{ $searchParams['point_arrive'] ?? '' }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 pl-10" 
                                placeholder="Ville d'arrivée">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-flag"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Date -->
                    <div class="relative lg:col-span-2">
                        <label for="date_depart" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar text-blue-500 mr-2"></i>Date de départ
                        </label>
                        <div class="relative">
                            <input type="date" name="date_depart" 
                                value="{{ $searchParams['date_depart'] ?? date('Y-m-d') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 pl-10">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-calendar"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Bouton Rechercher -->
                    <div class="lg:col-span-3">
                        <button type="submit" class="w-full bg-[#e94f1b] text-white px-4 py-3.5 rounded-xl font-bold hover:bg-orange-600 transition-all duration-300 shadow-lg flex items-center justify-center gap-2">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Liste des Résultats -->
        @if(isset($groupedRoutes) && count($groupedRoutes) > 0)
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 border border-gray-100 mb-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Voyages disponibles</h2>
                    <span class="bg-[#e94f1b] text-white px-4 py-2 rounded-xl font-bold text-lg">
                        {{ count($groupedRoutes) }} trajet{{ count($groupedRoutes) > 1 ? 's' : '' }}
                    </span>
                </div>
            </div>
            
            <div class="grid grid-cols-1 gap-6">
                @foreach($groupedRoutes as $route)
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1"
                         data-route="{{ json_encode($route) }}">
                        <div class="p-5">
                            <div class="flex flex-col lg:flex-row lg:items-center gap-6">
                                <!-- Compagnie & Trajet -->
                                <div class="flex items-center gap-4 min-w-[280px]">
                                    <div class="w-16 h-16 bg-gradient-to-br from-[#e94f1b] to-orange-400 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-inner">
                                        <i class="fas fa-bus text-white text-2xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-black text-gray-900 text-xl tracking-tight">
                                            {{ $route->compagnie->name ?? 'Compagnie' }}
                                        </h3>
                                        <div class="flex items-center gap-2 text-sm text-gray-500 mt-1 font-medium">
                                            <span>{{ $route->point_depart }}</span>
                                            <i class="fas fa-long-arrow-alt-right text-[#e94f1b]"></i>
                                            <span>{{ $route->point_arrive }}</span>
                                        </div>
                                        <div class="mt-2 text-xs font-bold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full inline-block">
                                            <i class="fas fa-hourglass-half mr-1"></i>{{ $route->durer_parcours ?? 'Durée non définie' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Horaires & Occupation -->
                                <div class="flex-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                                        <i class="fas fa-clock text-[#e94f1b]"></i> Horaires & Disponibilité
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                      @foreach($route->aller_horaires as $horaire)
    @php
        $totalSeats = $horaire['total_seats'] ?? 70;
        $reservedCount = $horaire['reserved_count'] ?? 0;
        $occupancyRate = ($totalSeats > 0) ? ($reservedCount / $totalSeats) * 100 : 0;
        $statusClass = $reservedCount >= $totalSeats ? 'bg-red-50 border-red-200 text-red-700' : 
                      ($occupancyRate > 80 ? 'bg-orange-50 border-orange-200 text-orange-700' : 'bg-green-50 border-green-200 text-green-700');
    @endphp
    
    <!-- MODIFICATION ICI : On retire l'onclick du conteneur principal -->
    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl border {{ $statusClass }} transition-all hover:scale-105 active:scale-95 shadow-sm cursor-pointer group hover:shadow-md">
          
          <!-- Zone 1 : Clic sur l'heure (Déclenche la vente) -->
          <div onclick="selectSpecificHoraire(this, '{{ $horaire['id'] }}', '{{ $horaire['heure_depart'] }}')" 
               class="flex-grow flex items-center gap-2"
               title="Sélectionner cet horaire">
              <span class="font-black text-sm">{{ substr($horaire['heure_depart'], 0, 5) }}</span>
          </div>

          <!-- Séparateur visuel -->
          <div class="w-px h-3 bg-current opacity-20"></div>

          <!-- Zone 2 : Clic sur les places (Déclenche le plan du bus) -->
          <!-- On garde event.stopPropagation() par sécurité, mais la séparation des divs fait le travail -->
          <div class="flex items-center gap-1 hover:text-[#e94f1b] transition-colors p-1" 
               onclick="openSeatMap(event, '{{ $horaire['vehicule_id'] }}', '{{ $horaire['id'] }}', '{{ $searchParams['date_depart'] ?? date('Y-m-d') }}')"
               title="Voir le plan des places">
              <i class="fas fa-couch text-[10px]"></i>
              <span class="text-[10px] font-black">{{ $reservedCount }}/{{ $totalSeats }}</span>
          </div>
    </div>
@endforeach
                                    </div>
                                </div>

                                <!-- Prix & Action -->
                                <div class="lg:text-right flex lg:flex-col items-center lg:items-end justify-between lg:justify-center gap-4 border-t lg:border-t-0 lg:border-l border-gray-100 pt-4 lg:pt-0 lg:pl-6 min-w-[200px]">
                                    <div>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">Prix à partir de</p>
                                        <p class="text-2xl font-black text-[#e94f1b]">
                                            {{ number_format($route->montant_billet, 0, ',', ' ') }} <small class="text-xs">FCFA</small>
                                        </p>
                                    </div>
                                    
                                    <button onclick="startReservation(this)"
                                        class="bg-gradient-to-r from-[#e94f1b] to-orange-600 text-white px-8 py-3 rounded-xl font-black text-sm hover:shadow-lg hover:shadow-orange-200 transition-all duration-300 transform active:scale-95 flex items-center gap-2">
                                        <span>RÉSERVER</span>
                                        <i class="fas fa-chevron-right text-[10px]"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @elseif(isset($groupedRoutes))
            <div class="bg-white rounded-xl shadow-lg p-12 text-center border border-gray-100">
                <div class="w-20 h-20 bg-gradient-to-br from-orange-100 to-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-route text-3xl text-[#e94f1b]"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun programme trouvé</h3>
                <p class="text-gray-600 mb-6">Essayez d'ajuster vos critères de recherche ou cliquez sur "Voir tous les voyages".</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal Unique de Réservation -->
<div id="bookingModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                
                <!-- Header Modal -->
                <div class="bg-[#e94e1a] px-4 py-3 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg font-bold leading-6 text-white" id="modalTitle">Nouvelle Réservation</h3>
                    <button onclick="closeModal()" class="text-white hover:text-gray-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Contenu Modal -->
                <div class="px-4 py-5 sm:p-6" id="modalContent">
                    <!-- Le contenu sera injecté dynamiquement par JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Script Google Maps (Vérifiez votre clé API) -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=initAutocomplete" async defer></script>

<script>
    // Configuration des rangées de bus
    const typeRangeConfig = {
        '1x1': { placesGauche: 1, placesDroite: 1 },
        '2x1': { placesGauche: 2, placesDroite: 1 },
        '1x2': { placesGauche: 1, placesDroite: 2 },
        '2x2': { placesGauche: 2, placesDroite: 2 },
        '3x2': { placesGauche: 3, placesDroite: 2 },
        '2x3': { placesGauche: 2, placesDroite: 3 }
    };

    // 1. Initialisation Autocomplete
    function initAutocomplete() {
        const options = {
            componentRestrictions: { country: "ci" },
            fields: ["formatted_address", "name"],
        };

        const inputDepart = document.getElementById("point_depart");
        const inputArrive = document.getElementById("point_arrive");

        if (inputDepart) new google.maps.places.Autocomplete(inputDepart, options);
        if (inputArrive) new google.maps.places.Autocomplete(inputArrive, options);
    }

    // Fonction pour inverser le point de départ et d'arrivée
    function swapLocations() {
        const departInput = document.getElementById('point_depart');
        const arriveeInput = document.getElementById('point_arrive');
        
        if (departInput && arriveeInput) {
            const temp = departInput.value;
            departInput.value = arriveeInput.value;
            arriveeInput.value = temp;
            
            // Animation visuelle
            departInput.classList.add('ring-2', 'ring-[#e94f1b]');
            arriveeInput.classList.add('ring-2', 'ring-[#e94f1b]');
            setTimeout(() => {
                departInput.classList.remove('ring-2', 'ring-[#e94f1b]');
                arriveeInput.classList.remove('ring-2', 'ring-[#e94f1b]');
            }, 300);
        }
    }

    // --- LOGIQUE DE RÉSERVATION ---

    let currentBooking = {
        route: null,
        isReturn: false,
        dateAller: null,
        timeAller: null,
        progIdAller: null,
        dateRetour: null,
        timeRetour: null,
        progIdRetour: null,
        passengers: 1,
        passengerData: [],
        seatsAller: [],
        seatsRetour: []
    };

    function resetBooking(routeData, dateAller) {
        // Obtenir la date depuis le champ date s'il n'est pas fourni (fallback)
        if (!dateAller) {
            const dateInput = document.querySelector('input[name="date_depart"]');
            dateAller = dateInput ? dateInput.value : new Date().toISOString().split('T')[0];
        }

        currentBooking = {
            route: routeData,
            dateAller: dateAller,
            isReturn: false,
            // ... reste initialisé
            progIdAller: null,
            timeAller: null,
            dateRetour: null,
            progIdRetour: null,
            timeRetour: null,
            passengers: 1,
            seatsAller: [], // Nouveau: pour stocker les places choisies
            seatsRetour: []
        };
    }

    // Démarrer la réservation depuis le bouton "RÉSERVER"
    function startReservation(element) {
        const routeData = JSON.parse(element.closest('[data-route]').dataset.route);
        // Date par défaut ou celle du champ de recherche
        const dateInput = document.querySelector('input[name="date_depart"]');
        const dateAller = dateInput ? dateInput.value : new Date().toISOString().split('T')[0];

        resetBooking(routeData, dateAller);
        
        if (routeData.has_retour) {
            askTripType();
        } else {
            currentBooking.isReturn = false;
            askDepartureTime(); // On demande d'abord l'heure car le bouton général ne sélectionne pas d'horaire
        }
        openModal();
    }

    // Fonction d'entrée par horaire spécifique
    function selectSpecificHoraire(element, horaireId, horaireTime) {
        const routeData = JSON.parse(element.closest('[data-route]').dataset.route);
        // Date par défaut ou celle du champ de recherche
        const dateInput = document.querySelector('input[name="date_depart"]');
        const dateAller = dateInput ? dateInput.value : new Date().toISOString().split('T')[0];

        resetBooking(routeData, dateAller);
        currentBooking.progIdAller = horaireId;
        currentBooking.timeAller = horaireTime;
        
        if (routeData.has_retour) {
            askTripType();
        } else {
            currentBooking.isReturn = false;
            askPassengerCount();
        }
        openModal();
    }

    // Gestion du Modal
    function openModal() { document.getElementById('bookingModal').classList.remove('hidden'); }
    function closeModal() { document.getElementById('bookingModal').classList.add('hidden'); }
    
    // Ouvrir le plan des places (wrapper pour stopPropagation)
    function openSeatMap(event, vehicleId, programId, date) {
        event.stopPropagation();
        showSeatMap(vehicleId, programId, date);
    }

    // Affichage du Plan des Places (Seat Map)
    async function showSeatMap(vehicleId, programId, date) {
        if (!vehicleId) {
            Swal.fire({ icon: 'info', title: 'Info', text: 'Aucun véhicule assigné.' });
            return;
        }

        Swal.fire({
            title: 'Chargement du plan...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const url = "{{ route('hotesse.api.vehicle', ':id') }}".replace(':id', vehicleId);
            const response = await fetch(`${url}?date=${encodeURIComponent(date)}&program_id=${programId}`);
            const data = await response.json();

            if (!data.success) throw new Error(data.error || 'Erreur lors du chargement');

            const marque = data.vehicule.marque || 'Bus';
            const modele = data.vehicule.modele || '';

            Swal.fire({
                title: `<div class="text-[#e94f1b] font-black">${marque} ${modele}</div>`,
                html: `
                    <div class="text-left py-2">
                        <div class="bg-gray-100 p-3 rounded-xl mb-4 flex justify-between items-center">
                            <div>
                                <div class="text-[10px] uppercase font-bold text-gray-400">Immatriculation</div>
                                <div class="font-black text-gray-800">${data.vehicule.immatriculation}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-[10px] uppercase font-bold text-gray-400">Date du voyage</div>
                                <div class="font-black text-[#e94f1b]">${new Date(date).toLocaleDateString('fr-FR')}</div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <h4 class="font-black text-sm uppercase mb-3 flex items-center gap-2">
                                <i class="fas fa-th text-[#e94f1b]"></i> Disposition des places
                            </h4>
                            ${generatePlacesVisualization(data.vehicule, data.reserved_seats || [])}
                        </div>
                        <div class="flex gap-4 mt-4 p-3 bg-gray-50 rounded-xl border border-gray-100">
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded shadow-sm bg-gradient-to-br from-orange-400 to-[#e94f1b]"></div>
                                <span class="text-[10px] font-bold text-gray-500">Libre (G)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded shadow-sm bg-gradient-to-br from-green-400 to-green-600"></div>
                                <span class="text-[10px] font-bold text-gray-500">Libre (D)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded shadow-sm bg-gray-300"></div>
                                <span class="text-[10px] font-bold text-gray-500">Occupé</span>
                            </div>
                        </div>
                    </div>
                `,
                width: 600,
                showCloseButton: true,
                showConfirmButton: false,
                customClass: {
                    title: 'border-b pb-4',
                    popup: 'rounded-3xl'
                }
            });
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Erreur', text: error.message });
        }
    }

   function generatePlacesVisualization(vehicle, reservedSeats) {
        let config = typeRangeConfig[vehicle.type_range] || typeRangeConfig['2x2'];
        const placesGauche = config.placesGauche;
        const placesDroite = config.placesDroite;
        const placesParRanger = placesGauche + placesDroite;
        const totalPlaces = parseInt(vehicle.nombre_place);
        const nombreRanger = Math.ceil(totalPlaces / placesParRanger);
        
        // Début du conteneur style tableau
        let html = `
            <div class="border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm">
                <!-- En-têtes -->
                <div class="grid grid-cols-[60px_1fr_40px_1fr] bg-gray-50 border-b border-gray-100 py-3 px-4">
                    <div class="text-xs font-black text-gray-400 uppercase text-center">RANG</div>
                    <div class="text-xs font-black text-gray-400 uppercase text-center">GAUCHE</div>
                    <div class="text-xs font-black text-gray-400 uppercase text-center border-l border-r border-gray-200 mx-2">ALLÉE</div>
                    <div class="text-xs font-black text-gray-400 uppercase text-center">DROITE</div>
                </div>
                
                <div class="max-h-[350px] overflow-y-auto scrollbar-thin p-4 space-y-3">
        `;

        let numeroPlace = 1;
        for (let r = 1; r <= nombreRanger; r++) {
            html += `<div class="grid grid-cols-[60px_1fr_40px_1fr] items-center">
                        <!-- Numéro Rangée -->
                        <div class="text-center font-black text-gray-300 text-sm">R${r}</div>
                        
                        <!-- Places Gauche -->
                        <div class="flex justify-center gap-3">`;
            
            for (let i = 0; i < placesGauche; i++) {
                if (numeroPlace <= totalPlaces) {
                    const isReserved = reservedSeats.includes(numeroPlace);
                    // Style conditionnel (Rouge plein vs Blanc avec bordure)
                    const styleClass = isReserved 
                        ? 'bg-[#ef4444] text-white border-transparent' 
                        : 'bg-white text-gray-700 border-gray-300 hover:border-[#e94f1b] hover:text-[#e94f1b]';
                    
                    html += `<div class="w-9 h-9 border-2 rounded-lg flex items-center justify-center font-bold text-sm shadow-sm transition-all ${styleClass}" title="Place ${numeroPlace}">
                                ${numeroPlace}
                             </div>`;
                    numeroPlace++;
                }
            }
            
            html += `</div>
                    
                    <!-- Allée (Ligne verticale visuelle si besoin, sinon vide) -->
                    <div class="flex justify-center h-full">
                        <div class="w-px bg-gray-100 h-full"></div>
                    </div>

                    <!-- Places Droite -->
                    <div class="flex justify-center gap-3">`;
            
            for (let i = 0; i < placesDroite; i++) {
                if (numeroPlace <= totalPlaces) {
                    const isReserved = reservedSeats.includes(numeroPlace);
                    const styleClass = isReserved 
                        ? 'bg-[#ef4444] text-white border-transparent' 
                        : 'bg-white text-gray-700 border-gray-300 hover:border-[#e94f1b] hover:text-[#e94f1b]';

                    html += `<div class="w-9 h-9 border-2 rounded-lg flex items-center justify-center font-bold text-sm shadow-sm transition-all ${styleClass}" title="Place ${numeroPlace}">
                                ${numeroPlace}
                             </div>`;
                    numeroPlace++;
                }
            }
            
            html += `</div></div>`;
        }
        
        // Légende (Footer du tableau)
        html += `   </div>
                    <div class="border-t border-gray-100 bg-gray-50 p-3 flex justify-center gap-6">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-[#ef4444]"></div>
                            <span class="text-xs font-bold text-gray-600">Occupé</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-white border border-gray-300"></div>
                            <span class="text-xs font-bold text-gray-600">Libre</span>
                        </div>
                    </div>
                </div>`;
        return html;
    }

    function setModalContent(html, title) {
        document.getElementById('modalContent').innerHTML = html;
        if(title) document.getElementById('modalTitle').textContent = title;
    }

    // Etape 1 : Type Voyage
    function askTripType() {
        const prix = currentBooking.route.montant_billet;
        const html = `
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <button onclick="selectTripType(false)" class="p-6 border-2 border-gray-200 rounded-xl hover:border-[#e94e1a] hover:bg-orange-50 transition text-center group">
                        <i class="fas fa-arrow-right text-3xl text-gray-400 mb-3 group-hover:text-[#e94e1a]"></i>
                        <div class="font-bold text-lg">Aller Simple</div>
                        <div class="text-[#e94e1a] font-bold mt-1">${new Intl.NumberFormat().format(prix)} FCFA</div>
                    </button>
                    <button onclick="selectTripType(true)" class="p-6 border-2 border-gray-200 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition text-center group">
                        <i class="fas fa-exchange-alt text-3xl text-gray-400 mb-3 group-hover:text-blue-500"></i>
                        <div class="font-bold text-lg">Aller-Retour</div>
                        <div class="text-blue-600 font-bold mt-1">${new Intl.NumberFormat().format(prix * 2)} FCFA</div>
                    </button>
                </div>
                <div class="flex justify-center pt-4 border-t">
                    <button onclick="closeModal()" class="px-6 py-2 bg-gray-100 text-gray-600 rounded-lg font-bold hover:bg-gray-200 transition">
                        Fermer
                    </button>
                </div>
            </div>
        `;
        setModalContent(html, "Type de voyage");
    }

    function selectTripType(isReturn) {
        currentBooking.isReturn = isReturn;
        if (currentBooking.progIdAller) {
            if (isReturn) {
                askReturnDate();
            } else {
                askPassengerCount();
            }
        } else {
            askDepartureTime();
        }
    }

    // Etape 2 : Heure Aller
    function askDepartureTime() {
        const horaires = currentBooking.route.aller_horaires;
        let html = `
            <div class="mb-4 text-gray-600">
                Trajet : <strong>${currentBooking.route.point_depart}</strong> vers <strong>${currentBooking.route.point_arrive}</strong><br>
                Date : ${currentBooking.dateAller}
            </div>
            <div class="grid grid-cols-2 gap-3 max-h-60 overflow-y-auto p-1">`;
        
        horaires.forEach(h => {
            html += `
                <button onclick="selectTimeAller('${h.id}', '${h.heure_depart}')" class="p-4 border rounded-lg hover:bg-green-50 hover:border-green-500 text-left transition">
                    <div class="font-bold text-lg text-green-700">${h.heure_depart}</div>
                    <div class="text-xs text-gray-500">Arrivée estimée: ${h.heure_arrive}</div>
                </button>`;
        });
        html += `</div>
            <div class="mt-6 flex justify-between gap-3 pt-4 border-t">
                <button onclick="${currentBooking.route.has_retour ? 'askTripType()' : 'closeModal()'}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg font-bold hover:bg-gray-200 transition">
                    <i class="fas fa-arrow-left mr-1"></i> Retour
                </button>
                <button onclick="closeModal()" class="px-4 py-2 text-gray-400 hover:text-gray-600 transition">
                    Annuler
                </button>
            </div>`;
        setModalContent(html, "Choisir l'heure de départ");
    }

    function selectTimeAller(id, time) {
        currentBooking.progIdAller = id;
        currentBooking.timeAller = time;
        
        if(currentBooking.isReturn) {
            askReturnDate();
        } else {
            askPassengerCount();
        }
    }

    // Etape 3 : Date Retour (Si A/R)
    function askReturnDate() {
        const html = `
            <div class="max-w-xs mx-auto py-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Date de retour</label>
                <input type="date" id="inputDateRetour" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg p-3 border" min="${currentBooking.dateAller}" value="${currentBooking.dateAller}">
                <button onclick="confirmReturnDate()" class="mt-6 w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 shadow-lg transition transform active:scale-95">
                    Voir les horaires retour
                </button>
                <button onclick="${currentBooking.timeAller ? 'askTripType()' : 'askDepartureTime()'}" class="mt-4 w-full bg-gray-100 text-gray-600 py-2 rounded-xl font-bold hover:bg-gray-200 transition">
                    <i class="fas fa-arrow-left mr-1"></i> Retour
                </button>
            </div>
        `;
        setModalContent(html, "Date de voyage retour");
    }

    function confirmReturnDate() {
        const date = document.getElementById('inputDateRetour').value;
        if(!date) {
            Swal.fire({ icon: 'warning', title: 'Attention', text: 'Veuillez sélectionner une date', showCloseButton: true });
            return;
        }
        currentBooking.dateRetour = date;
        fetchReturnTimes();
    }

    // Etape 4 : Heure Retour
    function fetchReturnTimes() {
        setModalContent('<div class="text-center py-12"><i class="fas fa-circle-notch fa-spin text-4xl text-blue-500"></i><p class="mt-4 text-gray-500">Recherche des trajets retour...</p></div>', "Chargement...");
        
        const params = new URLSearchParams({
            original_depart: currentBooking.route.point_depart,
            original_arrive: currentBooking.route.point_arrive,
            min_date: currentBooking.dateRetour
        });

        fetch(`{{ route('hotesse.api.return-trips') }}?${params}`)
            .then(r => r.json())
            .then(data => {
                if(data.success && data.return_trips.length > 0) {
                    let html = `
                        <div class="mb-4 text-sm text-gray-500">Sélectionnez l'heure du retour pour le ${currentBooking.dateRetour}</div>
                        <div class="grid grid-cols-2 gap-3 max-h-60 overflow-y-auto p-1">`;
                    data.return_trips.forEach(h => {
                        html += `
                            <button onclick="selectTimeRetour('${h.id}', '${h.heure_depart}')" class="p-4 border rounded-lg hover:bg-blue-50 hover:border-blue-500 text-left transition">
                                <div class="font-bold text-lg text-blue-700">${h.heure_depart}</div>
                                <div class="text-xs text-gray-500">Arrivée estimée: ${h.heure_arrive}</div>
                            </button>`;
                    });
                    html += `</div>
                        <div class="mt-6 flex justify-between gap-3 pt-4 border-t">
                            <button onclick="askReturnDate()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg font-bold hover:bg-gray-200 transition">
                                <i class="fas fa-arrow-left mr-1"></i> Changer la date
                            </button>
                            <button onclick="closeModal()" class="px-4 py-2 text-gray-400 hover:text-gray-600 transition">
                                Annuler
                            </button>
                        </div>`;
                    setModalContent(html, "Choisir l'heure de retour");
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Aucun retour trouvé',
                        text: 'Aucun bus retour n\'a été trouvé pour cette date spécifique.',
                        showCloseButton: true,
                        confirmButtonText: 'Changer de date',
                        confirmButtonColor: '#3085d6'
                    }).then(() => askReturnDate());
                }
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Erreur', text: 'Erreur lors du chargement des horaires retour', showCloseButton: true });
                askReturnDate();
            });
    }

    function selectTimeRetour(id, time) {
        currentBooking.progIdRetour = id;
        currentBooking.timeRetour = time;
        askPassengerCount();
    }

    // Etape 5 : Nombre de passagers
    function askPassengerCount() {
        let backAction = '';
        if (currentBooking.isReturn) {
            backAction = 'fetchReturnTimes()';
        } else if (currentBooking.timeAller && !currentBooking.route.has_retour) {
            backAction = 'closeModal()';
        } else if (currentBooking.progIdAller && !currentBooking.timeAller) {
            // Caso raro
            backAction = 'askDepartureTime()';
        } else {
            backAction = currentBooking.timeAller ? 'askTripType()' : 'askDepartureTime()';
        }

        let html = `
            <div class="text-center mb-6">
                <p class="text-gray-600">Combien de places souhaitez-vous réserver ?</p>
            </div>
            <div class="flex justify-center flex-wrap gap-3 py-4">`;
        for(let i=1; i<=8; i++) {
            html += `<button onclick="selectPassengerCount(${i})" class="w-14 h-14 rounded-xl border-2 border-gray-200 hover:border-[#e94f1b] hover:bg-orange-50 font-bold text-xl transition transform active:scale-95 shadow-sm">${i}</button>`;
        }
        html += `</div>
            <div class="mt-8 flex justify-between gap-3 pt-4 border-t">
                <button onclick="${backAction}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg font-bold hover:bg-gray-200 transition">
                    <i class="fas fa-arrow-left mr-1"></i> Retour
                </button>
                <button onclick="closeModal()" class="px-4 py-2 text-gray-400 hover:text-gray-600 transition">
                    Annuler
                </button>
            </div>`;
        setModalContent(html, "Nombre de passagers");
    }

    function selectPassengerCount(n) {
        currentBooking.passengers = n;
        showPassengerForm();
    }

    // Etape 6 : Formulaire Infos
    function showPassengerForm() {
        let html = `<form id="finalForm" onsubmit="submitReservation(event)" class="space-y-4 max-h-[60vh] overflow-y-auto px-1 scrollbar-thin">`;
        
        for(let i=0; i < currentBooking.passengers; i++) {
            html += `
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <h4 class="font-bold text-[#e94f1b] text-xs mb-3 uppercase tracking-wider">Passager ${i+1}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <input type="text" name="passenger_details[${i}][nom]" placeholder="Nom *" required class="w-full rounded-lg border-gray-300 p-2 focus:ring-[#e94f1b] focus:border-[#e94f1b]">
                        <input type="text" name="passenger_details[${i}][prenom]" placeholder="Prénom *" required class="w-full rounded-lg border-gray-300 p-2 focus:ring-[#e94f1b] focus:border-[#e94f1b]">
                        <input type="tel" name="passenger_details[${i}][telephone]" placeholder="Téléphone *" required class="w-full rounded-lg border-gray-300 p-2 focus:ring-[#e94f1b] focus:border-[#e94f1b]">
                        <input type="email" name="passenger_details[${i}][email]" placeholder="Email (Optionnel)" class="w-full rounded-lg border-gray-300 p-2 focus:ring-[#e94f1b] focus:border-[#e94f1b]">
                    </div>
                </div>
            `;
        }
        html += `
            <div class="mt-6 flex justify-between items-center pt-4 border-t">
                <button type="button" onclick="askPassengerCount()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg font-bold hover:bg-gray-200 transition">
                    <i class="fas fa-arrow-left mr-1"></i> Retour
                </button>
                <div class="flex gap-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-400 font-bold hover:text-gray-600 transition">Annuler</button>
                    <button type="submit" class="px-6 py-2 bg-[#e94e1a] text-white rounded-lg font-bold shadow-lg hover:bg-[#d04415] transition transform active:scale-95 flex items-center gap-2">
                        <i class="fas fa-check"></i> Confirmer la vente
                    </button>
                </div>
            </div>
        </form>`;
        
        setModalContent(html, "Informations des passagers");
    }

    // Etape 7 : Soumission
    function submitReservation(e) {
        e.preventDefault();
        const formData = new FormData(e.target);

        formData.append('programme_id', currentBooking.progIdAller);
        formData.append('date_voyage', currentBooking.dateAller);
        formData.append('heure_depart', currentBooking.timeAller);
        formData.append('nombre_passagers', currentBooking.passengers);

        if(currentBooking.isReturn) {
            formData.append('programme_retour_id', currentBooking.progIdRetour);
            formData.append('date_retour', currentBooking.dateRetour);
            formData.append('heure_retour', currentBooking.timeRetour);
        }

        Swal.fire({
            title: 'Validation en cours...',
            text: 'Veuillez patienter',
            allowOutsideClick: false,
            showCloseButton: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch('{{ route("hotesse.vendre-ticket.submit") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                closeModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Vente réussie !',
                    text: data.message,
                    showCloseButton: true,
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    window.location.href = data.redirect;
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: data.message,
                    showCloseButton: true,
                    confirmButtonColor: '#d33'
                });
            }
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Erreur système',
                text: 'Une erreur imprévue est survenue. Veuillez vérifier votre connexion.',
                showCloseButton: true,
                confirmButtonColor: '#d33'
            });
        });
    }
</script>
@endsection