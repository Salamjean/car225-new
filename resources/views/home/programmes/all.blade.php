@extends('home.layouts.template')
@section('content')
    <div class="min-h-screen bg-gradient-to-br from-white to-gray-50 py-6 sm:py-8 lg:py-10">
        <div class="w-full px-3 sm:px-4 lg:px-6">
            <!-- En-tête -->
            <div class="mb-6 sm:mb-8">
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 border border-gray-100 text-center">
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-3">
                        Programmes du jour
                    </h1>
                    <p class="text-gray-600 mb-4 max-w-2xl mx-auto">
                        Voici les départs disponibles pour aujourd'hui le <span class="font-bold text-[#e94e1a]">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</span>.
                    </p>
                    <div class="inline-block bg-[#e94e1a] text-white px-4 py-2 rounded-xl font-bold text-lg shadow-md">
                        {{ $programmes->total() }} programme(s) disponible(s)
                    </div>
                    
                    <div class="mt-6 flex justify-center">
                         <a href="{{ url('/') }}#search-form"
                            class="bg-white text-[#e94e1a] border border-[#e94e1a] px-6 py-2 rounded-xl hover:bg-[#e94e1a] hover:text-white transition-all duration-300 font-semibold text-base flex items-center gap-2">
                            <i class="fas fa-search"></i>
                            Faire une recherche spécifique
                        </a>
                    </div>
                </div>
            </div>

            <!-- Résultats en liste -->
            @if ($programmes->count() > 0)
                <div class="w-full mb-6 sm:mb-8">
                    <!-- En-tête de la liste (version desktop) -->
                    <div class="hidden md:block mb-4">
                        <div class="bg-gradient-to-r from-[#e94e1a]/10 to-orange-500/10 rounded-xl p-4">
                            <div class="grid grid-cols-12 gap-4 text-sm font-semibold text-gray-700">
                                <div class="col-span-3">Compagnie & Trajet</div>
                                <div class="col-span-2 text-center">Date & Heures</div>
                                <div class="col-span-2 text-center">Durée & Prix</div>
                                <div class="col-span-2 text-center">Places</div>
                                <div class="col-span-3 text-right">Actions</div>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des programmes -->
                    <div class="space-y-4">
                        @foreach ($programmes as $programme)
                            @php
                                $displayDate = \Carbon\Carbon::now(); 
                                $formattedDisplayDate = $displayDate->format('d/m/Y');
                                $searchDateParam = $displayDate->format('Y-m-d');
                            @endphp
                            <div class="w-full bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden group">
                                <!-- Version Mobile -->
                                <div class="block md:hidden">
                                    <div class="p-4">
                                        <!-- En-tête mobile -->
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex items-center gap-2">
                                                <div class="w-10 h-10 bg-gradient-to-r from-[#e94e1a] to-orange-500 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-bus text-white"></i>
                                                </div>
                                                <div>
                                                    <h3 class="font-bold text-gray-900">
                                                        {{ $programme->compagnie->name ?? 'Compagnie' }}</h3>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-lg font-bold text-[#e94e1a]">
                                                    {{ number_format($programme->montant_billet, 0, ',', ' ') }} FCFA
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Trajet mobile -->
                                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="text-center">
                                                    <div class="font-bold text-gray-900">{{ $programme->point_depart }}</div>
                                                </div>
                                                <div class="mx-2"><i class="fas fa-arrow-right text-[#e94e1a]"></i></div>
                                                <div class="text-center">
                                                    <div class="font-bold text-gray-900">{{ $programme->point_arrive }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Informations mobile -->
                                        <div class="grid grid-cols-2 gap-3 mb-4">
                                            <div class="bg-blue-50 p-2 rounded-lg">
                                                 <div class="flex items-center gap-2 mb-1">
                                                    <i class="fas fa-calendar-day text-blue-500"></i>
                                                    <span class="text-sm font-semibold">Date</span>
                                                </div>
                                                <div class="text-xs font-bold text-blue-700">
                                                    {{ $formattedDisplayDate }}
                                                </div>
                                            </div>
                                            <div class="bg-green-50 p-2 rounded-lg">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <i class="fas fa-clock text-green-500"></i>
                                                    <span class="text-sm font-semibold">Heures</span>
                                                </div>
                                                <div class="text-xs">{{ $programme->heure_depart }} - {{ $programme->heure_arrive }}</div>
                                            </div>
                                            <div class="bg-purple-50 p-2 rounded-lg">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <i class="fas fa-hourglass-half text-purple-500"></i>
                                                    <span class="text-sm font-semibold">Durée</span>
                                                </div>
                                                <div class="text-xs">{{ $programme->durer_parcours }}</div>
                                            </div>
                                            <div class="bg-red-50 p-2 rounded-lg">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <i class="fas fa-chair text-red-500"></i>
                                                    <span class="text-sm font-semibold">Statut</span>
                                                </div>
                                                <div class="text-xs">
                                                    @php
                                                        $statusTexts = ['disponible' => 'Places disponibles', 'presque_complet' => 'Presque complet', 'complet' => 'Complet'];
                                                        $statusKey = $programme->statut_places;
                                                    @endphp
                                                    <span class="{{ $statusKey == 'complet' ? 'text-red-600' : ($statusKey == 'presque_complet' ? 'text-yellow-600' : 'text-green-600') }} font-semibold">
                                                        {{ $statusTexts[$statusKey] ?? 'Statut inconnu' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Actions mobile -->
                                        <div class="flex gap-2">
                                            @if ($programme->statut_places != 'complet')
                                                <a href="{{ auth()->check() ? route('user.dashboard') : route('login') }}"
                                                    class="flex-1 bg-[#e94e1a] text-white text-center py-2 rounded-lg font-bold hover:bg-[#d33d0f] transition-all duration-300 flex items-center justify-center gap-2">
                                                    <i class="fas fa-ticket-alt"></i> <span>Réserver</span>
                                                </a>
                                            @else
                                                <button class="flex-1 bg-gray-400 text-white text-center py-2 rounded-lg font-bold cursor-not-allowed flex items-center justify-center gap-2" disabled>
                                                    <i class="fas fa-times-circle"></i> <span>Complet</span>
                                                </button>
                                            @endif
                                            
                                            <!-- BOUTON DETAILS MOBILE CORRIGÉ -->
                                            <a href="#"
                                                onclick="showVehicleDetails({{ $programme->vehicule->id ?? 'null' }}, '{{ $searchDateParam }}', {{ $programme->id }}); return false;"
                                                class="w-12 bg-white text-[#e94e1a] border border-[#e94e1a] text-center py-2 rounded-lg font-bold hover:bg-gray-50 transition-all duration-300 flex items-center justify-center vehicle-details-btn">
                                                <i class="fas fa-info-circle"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Version Desktop -->
                                <div class="hidden md:block">
                                    <div class="grid grid-cols-12 gap-4 p-6 items-center">
                                        <!-- Compagnie & Trajet -->
                                        <div class="col-span-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-12 h-12 bg-gradient-to-r from-[#e94e1a] to-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-bus text-white text-lg"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2 mb-1 justify-center">
                                                        <h3 class="font-bold text-gray-900 truncate">{{ $programme->compagnie->name ?? 'Compagnie' }}</h3>
                                                        @if ($programme->is_aller_retour)
                                                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded-full font-semibold"><i class="fas fa-exchange-alt text-xs"></i> A/R</span>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center gap-2 text-sm text-gray-600 justify-center">
                                                        <div class="font-semibold text-gray-900">{{ $programme->point_depart }}</div>
                                                        <i class="fas fa-arrow-right text-[#e94e1a] text-xs"></i>
                                                        <div class="font-semibold text-gray-900">{{ $programme->point_arrive }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Date & Heure -->
                                        <div class="col-span-2 flex flex-col items-center justify-center">
                                            <div class="flex items-center gap-2 text-sm font-semibold mb-1">
                                                <i class="fas fa-calendar-day text-blue-500"></i>
                                                <span>{{ $formattedDisplayDate }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-sm">
                                                <i class="fas fa-clock text-green-500"></i>
                                                <span>{{ $programme->heure_depart }} → {{ $programme->heure_arrive }}</span>
                                            </div>
                                        </div>

                                        <!-- Durée & Prix -->
                                        <div class="col-span-2 flex justify-center flex-col items-center">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-hourglass-half text-purple-500"></i>
                                                <span class="font-semibold">{{ $programme->durer_parcours }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-money-bill-wave text-red-500"></i>
                                                <span class="font-bold text-lg text-[#e94e1a]">{{ number_format($programme->montant_billet, 0, ',', ' ') }} FCFA</span>
                                            </div>
                                        </div>

                                        <!-- Statut -->
                                        <div class="col-span-2 flex justify-center">
                                            @php
                                                $statusKey = $programme->statut_places;
                                            @endphp
                                            <div class="flex items-center gap-2">
                                                <div class="w-3 h-3 rounded-full {{ $statusKey == 'disponible' ? 'bg-green-500' : ($statusKey == 'presque_complet' ? 'bg-yellow-500' : 'bg-red-500') }}"></div>
                                                <span class="font-semibold {{ $statusKey == 'disponible' ? 'text-green-600' : ($statusKey == 'presque_complet' ? 'text-yellow-600' : 'text-red-600') }}">
                                                    {{ $statusTexts[$statusKey] ?? 'Statut inconnu' }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Actions -->
                                        <div class="col-span-3">
                                            <div class="flex gap-2 justify-end">
                                                <!-- BOUTON DETAILS DESKTOP CORRIGÉ (Ajout du programme ID en 3ème paramètre) -->
                                                <button
                                                    onclick="showVehicleDetails({{ $programme->vehicule->id ?? 'null' }}, '{{ $searchDateParam }}', {{ $programme->id }})"
                                                    class="bg-white text-[#e94e1a] border border-[#e94e1a] px-3 py-2 rounded-lg font-bold hover:bg-gray-50 transition-all duration-300 flex items-center gap-2 vehicle-details-btn text-sm">
                                                    <i class="fas fa-info-circle"></i>
                                                    <span class="hidden lg:inline">Détails</span>
                                                </button>

                                                @if ($programme->statut_places != 'complet')
                                                    <a href="{{ auth()->check() ? route('user.dashboard') : route('login') }}"
                                                        class="bg-[#e94e1a] text-white px-4 py-2 rounded-lg font-bold hover:bg-[#d33d0f] transition-all duration-300 flex items-center gap-2 text-sm shadow-sm hover:shadow-md">
                                                        <i class="fas fa-ticket-alt"></i> <span>Réserver</span>
                                                    </a>
                                                @else
                                                    <button class="bg-gray-400 text-white px-4 py-2 rounded-lg font-bold cursor-not-allowed flex items-center gap-2 text-sm" disabled>
                                                        <i class="fas fa-times-circle"></i> <span>Complet</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                @if ($programmes->hasPages())
                    <div class="w-full bg-white rounded-xl shadow-lg p-4 border border-gray-100 flex justify-center">
                        <div class="w-full pagination-wrapper">{{ $programmes->links() }}</div>
                    </div>
                @endif
            @else
                <!-- Aucun résultat -->
                <div class="w-full bg-white rounded-xl shadow-lg p-8 text-center border border-gray-100">
                    <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-route text-3xl text-[#e94e1a]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun programme disponible pour le moment</h3>
                </div>
            @endif
        </div>
    </div>

    <style>
        .pagination { display: flex; justify-content: center; list-style: none; padding: 0; margin: 0; gap: 4px; flex-wrap: wrap; }
        .pagination li a, .pagination li span { display: inline-flex; align-items: center; justify-content: center; padding: 8px 12px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; border: 2px solid transparent; min-width: 40px; font-size: 0.875rem; }
        .pagination li a { background-color: white; border-color: #e5e7eb; color: #6b7280; }
        .pagination li a:hover, .pagination li span { background-color: #e94e1a; border-color: #e94e1a; color: white; }
        .vehicle-details-popup { border-radius: 16px !important; position: relative; }
        /* Style pour la croix de fermeture personnalisée */
        .custom-close-btn { position: absolute; top: 12px; right: 12px; font-size: 24px; color: #9ca3af; cursor: pointer; transition: color 0.2s; z-index: 50; background: none; border: none; }
        .custom-close-btn:hover { color: #e94e1a; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const typeRangeConfig = {
            '2x2': { placesGauche: 2, placesDroite: 2, description: "2 places par côté" },
            '2x3': { placesGauche: 2, placesDroite: 3, description: "2 places à gauche, 3 à droite" },
            '2x4': { placesGauche: 2, placesDroite: 4, description: "2 places à gauche, 4 à droite" }
        };

        window.updateModalContent = async function(vehicleId, dateVoyage, programId) {
            try {
                Swal.showLoading();
                // On passe le programme_id dans l'URL pour un filtrage précis
                const url = `/vehicule/details/${vehicleId}?date=${encodeURIComponent(dateVoyage)}&programme_id=${programId}`;
                const response = await fetch(url);
                const data = await response.json();

                if (!data.success) throw new Error(data.error || 'Erreur');

                const vehicle = data.vehicule;
                const reservedSeats = (data.reservedSeats || []).map(seat => parseInt(seat));
                const formattedDate = new Date(dateVoyage).toLocaleDateString('fr-FR');
                const vehicleTitle = `${vehicle.marque ?? ''} ${vehicle.modele ?? ''}`.trim() || 'Détails du véhicule';

                const visualizationHTML = generatePlacesVisualization(vehicle, reservedSeats);

                Swal.update({
                    // Ajout du bouton de fermeture manuel dans le header
                    title: `
                        <div class="relative w-full">
                            <div class="text-xl font-bold text-[#e94e1a] pr-8">${vehicleTitle}</div>
                            <button onclick="Swal.close()" class="custom-close-btn">&times;</button>
                        </div>
                    `,
                    width: 700,
                    padding: '0',
                    // On remet le bouton de fermeture natif par sécurité
                    showCloseButton: true,
                    // On ajoute un bouton de confirmation qui sert de "Fermer"
                    showConfirmButton: true,
                    confirmButtonText: 'Fermer',
                    confirmButtonColor: '#6b7280',
                    customClass: {
                        popup: 'vehicle-details-popup rounded-2xl overflow-hidden',
                        content: 'p-0',
                        header: 'bg-gray-50 border-b border-gray-100 py-3 relative',
                        closeButton: 'focus:outline-none'
                    },
                    html: `
                    <div class="text-left w-full">
                        <div class="bg-blue-50 p-4 border-b border-blue-100">
                            <div class="flex justify-between items-center flex-wrap gap-2">
                                <div class="flex items-center gap-2">
                                    <div class="p-1.5 bg-white rounded shadow-sm text-blue-600">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium">Date du voyage</p>
                                        <p class="text-base font-bold text-gray-800 capitalize">${formattedDate}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg shadow-sm border border-gray-200">
                                    <label for="modal-date-picker" class="text-xs font-semibold text-gray-600">Changer :</label>
                                    <input type="date" id="modal-date-picker" value="${dateVoyage}" 
                                        class="border-none focus:ring-0 text-gray-800 font-bold bg-transparent p-0 text-sm cursor-pointer"
                                        onchange="window.updateModalContent(${vehicleId}, this.value, ${programId})"
                                    >
                                </div>
                            </div>
                            
                            <div class="flex gap-4 mt-3 pt-3 border-t border-blue-100/50 text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-500">Immat:</span>
                                    <span class="font-bold text-gray-800">${vehicle.immatriculation || 'N/A'}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-500">Capacité:</span>
                                    <span class="font-bold text-gray-800">
                                        ${vehicle.nombre_place} places 
                                        <span class="text-red-500 text-xs font-normal">(${reservedSeats.length} occupées)</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-white min-h-[300px]">
                            <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <span class="w-1 h-5 bg-[#e94e1a] rounded-full block"></span>
                                Disposition
                            </h3>
                            ${visualizationHTML}
                        </div>
                    </div>
                    `
                });
                Swal.hideLoading();
            } catch (error) {
                console.error(error);
                Swal.fire({icon: 'error', title: 'Erreur', text: 'Impossible de charger les détails.', confirmButtonColor: '#e94e1a'});
            }
        };

        async function showVehicleDetails(vehicleId, dateVoyage, programId) {
            if (!vehicleId) return;
            if (!dateVoyage) dateVoyage = new Date().toISOString().split('T')[0];
            // Si programId est undefined ou null, on le passe comme 'null' string ou chaîne vide pour l'URL
            const safeProgramId = programId ? programId : '';

            Swal.fire({
                title: 'Chargement...',
                html: '<div class="flex flex-col items-center p-4"><div class="w-8 h-8 border-4 border-[#e94e1a] border-t-transparent rounded-full animate-spin mb-2"></div></div>',
                allowOutsideClick: true, // Permet de fermer en cliquant à côté
                showConfirmButton: false,
                showCloseButton: true, // Bouton croix par défaut activé dès le début
                didOpen: async () => { await window.updateModalContent(vehicleId, dateVoyage, safeProgramId); }
            });
        }

        function generatePlacesVisualization(vehicle, reservedSeats = []) {
            const config = typeRangeConfig[vehicle.type_range];
            if (!config) return '<div class="p-4 text-red-600">Configuration inconnue</div>';

            const { placesGauche, placesDroite } = config;
            const placesParRanger = placesGauche + placesDroite;
            const totalPlaces = parseInt(vehicle.nombre_place);
            const nombreRanger = Math.ceil(totalPlaces / placesParRanger);

            let html = `
            <div class="flex flex-col items-center w-full">
                <!-- Conducteur -->
                <div class="w-full max-w-lg mb-4 relative h-10">
                    <div class="absolute left-4 top-0 bg-gray-200 border-2 border-gray-300 w-10 h-10 rounded-full flex items-center justify-center shadow-inner">
                        <i class="fas fa-steering-wheel text-gray-500"></i>
                    </div>
                </div>

                <div class="w-full max-w-lg border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm">
                    <div class="grid grid-cols-[50px_1fr_40px_1fr] gap-2 p-2 bg-gray-50 border-b text-xs font-bold text-gray-500 uppercase">
                        <div class="text-center">Rang</div>
                        <div class="text-center">Gauche</div>
                        <div class="text-center">Allée</div>
                        <div class="text-center">Droite</div>
                    </div>
                    <div class="max-h-[350px] overflow-y-auto custom-scrollbar">
            `;

            let numeroPlace = 1;
            for (let ranger = 1; ranger <= nombreRanger; ranger++) {
                const placesRestantes = totalPlaces - (numeroPlace - 1);
                const placesCetteRanger = Math.min(placesParRanger, placesRestantes);
                const placesGaucheCetteRanger = Math.min(placesGauche, placesCetteRanger);
                const placesDroiteCetteRanger = Math.min(placesDroite, placesCetteRanger - placesGaucheCetteRanger);

                html += `
                    <div class="grid grid-cols-[50px_1fr_40px_1fr] gap-2 p-2 items-center border-b border-gray-50 hover:bg-gray-50">
                        <div class="text-center font-bold text-gray-400 text-sm">R${ranger}</div>
                        <div class="flex justify-center flex-wrap gap-2">
                `;

                // Gauche
                for (let i = 0; i < placesGaucheCetteRanger; i++) {
                    const sn = numeroPlace + i;
                    const isRes = reservedSeats.includes(sn);
                    const colorClass = isRes ? 'bg-red-500 text-white opacity-80' : 'bg-white text-gray-700 border border-[#e94e1a] hover:bg-[#e94e1a] hover:text-white';
                    
                    html += `<div class="w-8 h-8 rounded flex items-center justify-center font-bold text-sm ${colorClass} cursor-default" title="Place ${sn}">${sn}</div>`;
                }

                html += `</div>
                        <div class="flex justify-center h-full"><div class="w-1 h-full bg-gray-100 rounded"></div></div>
                        <div class="flex justify-center flex-wrap gap-2">`;

                // Droite
                for (let i = 0; i < placesDroiteCetteRanger; i++) {
                    const sn = numeroPlace + placesGaucheCetteRanger + i;
                    const isRes = reservedSeats.includes(sn);
                     const colorClass = isRes ? 'bg-red-500 text-white opacity-80' : 'bg-white text-gray-700 border border-green-500 hover:bg-green-500 hover:text-white';

                     html += `<div class="w-8 h-8 rounded flex items-center justify-center font-bold text-sm ${colorClass} cursor-default" title="Place ${sn}">${sn}</div>`;
                }

                html += `</div></div>`;
                numeroPlace += placesCetteRanger;
            }

            html += `</div>
                <div class="bg-gray-50 p-2 text-xs flex justify-center gap-4 border-t">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-red-500 rounded"></span> Occupé</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 border border-[#e94e1a] bg-white rounded"></span> Libre</span>
                </div>
            </div></div>`;
            return html;
        }
    </script>
@endsection