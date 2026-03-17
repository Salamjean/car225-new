@extends('home.layouts.template')
@section('content')
    <div class="min-h-screen bg-gradient-to-br from-white to-gray-50 pt-28 sm:pt-32 pb-6 sm:pb-8 lg:pb-10">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6">
            <!-- En-tête des résultats -->
            <div class="mb-6 sm:mb-8">
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 border border-gray-100">
                    <!-- Version Mobile -->
                    <div class="block lg:hidden">
                        <div class="text-center mb-4">
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-3">
                                Résultats de recherche
                            </h1>
                            <div class="bg-[#e94e1a] text-white px-4 py-2 rounded-xl font-bold text-lg shadow-md inline-block">
                                {{ $totalResults }} programme(s) trouvé(s)
                            </div>
                        </div>

                        <!-- Filtres de recherche -->
                        <div class="flex flex-wrap justify-center gap-2 mb-4">
                            <div class="flex items-center gap-2 bg-orange-50 px-3 py-1 rounded-full">
                                <i class="fas fa-map-marker-alt text-[#e94e1a] text-sm"></i>
                                <span class="font-semibold text-sm">{{ $searchParams['point_depart'] }}</span>
                            </div>
                            <i class="fas fa-arrow-right text-[#e94e1a]"></i>
                            <div class="flex items-center gap-2 bg-green-50 px-3 py-1 rounded-full">
                                <i class="fas fa-flag text-green-500 text-sm"></i>
                                <span class="font-semibold text-sm">{{ $searchParams['point_arrive'] }}</span>
                            </div>
                            <div class="flex items-center gap-2 bg-blue-50 px-3 py-1 rounded-full">
                                <i class="fas fa-calendar text-blue-500 text-sm"></i>
                                <span class="text-xs font-bold">{{ date('d/m/Y', strtotime($searchParams['date_depart'])) }}</span>
                            </div>
                            @if($searchParams['is_aller_retour'] && $searchParams['date_retour'])
                            <i class="fas fa-exchange-alt text-blue-400 mx-1"></i>
                            <div class="flex items-center gap-2 bg-blue-100 px-3 py-1 rounded-full">
                                <i class="fas fa-calendar-check text-blue-600 text-sm"></i>
                                <span class="text-xs font-bold">{{ date('d/m/Y', strtotime($searchParams['date_retour'])) }}</span>
                            </div>
                            @endif
                        </div>

                        <!-- Bouton mobile -->
                        <div class="text-center">
                            <a href="{{ url('/') }}#search-form"
                                class="bg-white text-[#e94e1a] border border-[#e94e1a] px-4 py-2 rounded-xl hover:bg-[#e94e1a] hover:text-white transition-all duration-300 font-semibold text-base flex items-center justify-center gap-2 mx-auto w-full max-w-xs">
                                <i class="fas fa-search"></i>
                                Modifier la recherche
                            </a>
                        </div>
                    </div>

                    <!-- Version Desktop -->
                    <div class="hidden lg:block">
                        <div class="flex justify-between items-center gap-6">
                            <div class="flex-1">
                                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-3">
                                    Résultats de recherche
                                </h1>
                                <div class="flex items-center gap-4 text-sm text-gray-600">
                                    <div class="flex items-center gap-2 bg-orange-50 px-3 py-1 rounded-full">
                                        <i class="fas fa-map-marker-alt text-[#e94e1a]"></i>
                                        <span class="font-semibold">{{ $searchParams['point_depart'] }}</span>
                                    </div>
                                    <i class="fas fa-arrow-right text-[#e94e1a]"></i>
                                    <div class="flex items-center gap-2 bg-green-50 px-3 py-1 rounded-full">
                                        <i class="fas fa-flag text-green-500"></i>
                                        <span class="font-semibold">{{ $searchParams['point_arrive'] }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 bg-blue-50 px-3 py-1 rounded-full">
                                        <i class="fas fa-calendar text-blue-500"></i>
                                        <span class="font-bold">{{ date('d/m/Y', strtotime($searchParams['date_depart'])) }}</span>
                                    </div>
                                    @if($searchParams['is_aller_retour'] && $searchParams['date_retour'])
                                    <i class="fas fa-exchange-alt text-blue-300"></i>
                                    <div class="flex items-center gap-2 bg-blue-100 px-3 py-1 rounded-full border border-blue-200">
                                        <i class="fas fa-calendar-check text-blue-600 font-bold"></i>
                                        <span class="font-black text-blue-700">{{ date('d/m/Y', strtotime($searchParams['date_retour'])) }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="bg-[#e94e1a] text-white px-4 py-2 rounded-xl font-bold text-lg shadow-md">
                                    {{ $totalResults }} programme(s)
                                </span>
                                <a href="{{ url('/') }}#search-form"
                                    class="bg-white text-[#e94e1a] border border-[#e94e1a] px-4 py-2 rounded-xl hover:bg-[#e94e1a] hover: transition-all duration-300 font-semibold text-base flex items-center gap-2">
                                    <i class="fas fa-search"></i>
                                    Modifier la recherche
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Résultats en liste -->
            @php
                $isAllerRetour = $searchParams['is_aller_retour'];
            @endphp

            <!-- Titre des sections -->
            <div class="flex flex-col sm:flex-row gap-4 mb-8">
                <div class="flex-1 py-4 px-6 rounded-2xl font-black text-lg shadow-sm bg-white text-[#e94e1a] flex items-center justify-center gap-3 border-2 border-orange-100">
                    <i class="fas fa-arrow-right"></i>
                    <span>TRAJETS ALLER</span>
                </div>
                @if($isAllerRetour)
                <div class="flex-1 py-4 px-6 rounded-2xl font-black text-lg shadow-sm bg-white text-blue-600 flex items-center justify-center gap-3 border-2 border-blue-100">
                    <i class="fas fa-arrow-left"></i>
                    <span>TRAJETS RETOUR</span>
                </div>
                @endif
            </div>

            <!-- Résultats Aller -->
            <div id="section-aller" class="trip-section">
                @if ($programmes_aller->count() > 0)
                    <div class="w-full mb-8">
                        @if($isAllerRetour)
                        <div class="inline-flex items-center gap-2 bg-orange-100 text-[#e94e1a] px-4 py-2 rounded-lg font-black text-xs uppercase tracking-widest mb-4">
                            <i class="fas fa-plane-departure"></i> Sélectionner votre trajet aller : {{ $searchParams['point_depart'] }} &rarr; {{ $searchParams['point_arrive'] }}
                        </div>
                        @else
                         <div class="inline-flex items-center gap-2 bg-orange-100 text-[#e94e1a] px-4 py-2 rounded-lg font-black text-xs uppercase tracking-widest mb-4">
                            <i class="fas fa-bus"></i> Voyages disponibles
                        </div>
                        @endif

                        <div class="hidden md:block mb-4 md:px-2">
                            <div class="grid grid-cols-12 gap-4 px-3 py-2 text-xs font-black uppercase tracking-widest text-gray-400">
                                <div class="col-span-3 pl-2">Compagnie & Trajet</div>
                                <div class="col-span-2 text-center">Date & Heure</div>
                                <div class="col-span-2 text-center">Tarif</div>
                                <div class="col-span-2 text-center">Disponibilité</div>
                                <div class="col-span-3 text-right pr-2">Action</div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @foreach ($programmes_aller->groupBy('compagnie_id') as $compId => $group)
                                @php $programme = $group->first(); @endphp
                                <div class="relative">
                                    <div class="absolute -left-2 top-4 bottom-4 w-1 bg-[#e94e1a] rounded-full z-10 hidden md:block"></div>
                                    @include('home.programmes.partials.card', ['programme' => $programme, 'searchDate' => $searchParams['date_depart'], 'is_aller' => true])
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="w-full bg-white rounded-2xl shadow-lg p-12 text-center border border-gray-100 mb-8">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-route text-2xl text-gray-300"></i>
                        </div>
                        <h3 class="font-bold text-gray-900">Aucun départ trouvé pour l'aller</h3>
                        <p class="text-gray-500 text-sm mt-1">Désolé, aucun voyage trouvé de {{ $searchParams['point_depart'] }}.</p>
                    </div>
                @endif
            </div>

            <!-- Résultats Retour -->
            @if($isAllerRetour)
            <div id="section-retour" class="trip-section">
                @if ($programmes_retour->count() > 0)
                    <div class="w-full mb-8">
                        <div class="inline-flex items-center gap-2 bg-blue-50 text-blue-600 px-4 py-2 rounded-lg font-black text-xs uppercase tracking-widest mb-4">
                            <i class="fas fa-plane-arrival"></i> Sélectionner votre trajet retour : {{ $searchParams['point_arrive'] }} &rarr; {{ $searchParams['point_depart'] }}
                        </div>

                        <div class="hidden md:block mb-4 md:px-2">
                            <div class="grid grid-cols-12 gap-4 px-3 py-2 text-xs font-black uppercase tracking-widest text-gray-400">
                                <div class="col-span-3 pl-2">Compagnie & Trajet</div>
                                <div class="col-span-2 text-center">Date & Heure</div>
                                <div class="col-span-2 text-center">Tarif</div>
                                <div class="col-span-2 text-center">Disponibilité</div>
                                <div class="col-span-3 text-right pr-2">Action</div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @foreach ($programmes_retour->groupBy('compagnie_id') as $compId => $group)
                                @php $programme = $group->first(); @endphp
                                <div class="relative">
                                    <div class="absolute -left-2 top-4 bottom-4 w-1 bg-blue-500 rounded-full z-10 hidden md:block"></div>
                                    @include('home.programmes.partials.card', ['programme' => $programme, 'searchDate' => $searchParams['date_retour'], 'is_retour' => true])
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="w-full bg-white rounded-2xl shadow-lg p-12 text-center border border-gray-100 mb-8">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-route text-2xl text-gray-300"></i>
                        </div>
                        <h3 class="font-bold text-gray-900">Aucun départ trouvé pour le retour</h3>
                        <p class="text-gray-500 text-sm mt-1">Désolé, aucun voyage trouvé de {{ $searchParams['point_arrive'] }}.</p>
                    </div>
                @endif
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
        .custom-close-btn { position: absolute; top: 12px; right: 12px; font-size: 24px; color: #9ca3af; cursor: pointer; transition: color 0.2s; z-index: 50; background: none; border: none; }
        .custom-close-btn:hover { color: #e94e1a; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const typeRangeConfig = {
            '2x2': { placesGauche: 2, placesDroite: 2, description: "2 places par côté" },
            '2x3': { placesGauche: 2, placesDroite: 3, description: "2 places à gauche, 3 à droite" },
            '2x4': { placesGauche: 2, placesDroite: 4, description: "2 places à gauche, 4 à droite" },
            'Gamme Prestige': { placesGauche: 2, placesDroite: 2, description: "Catégorie Prestige (2+2)" },
            'Gamme Standard': { placesGauche: 2, placesDroite: 3, description: "Catégorie Standard (2+3)" }
        };

        window.updateModalContent = async function(vehicleId, dateVoyage, programId) {
            try {
                Swal.showLoading();
                const safeVehicleId = (vehicleId && vehicleId !== 'null') ? vehicleId : 0;
                const url = `/vehicule/details/${safeVehicleId}?date=${encodeURIComponent(dateVoyage)}&programme_id=${programId}`;
                
                const response = await fetch(url);
                if (!response.ok) throw new Error(`Erreur HTTP: ${response.status}`);
                const data = await response.json();

                if (!data.success) {
                    Swal.close();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Attention',
                        text: data.error || 'Impossible de récupérer les détails.',
                        confirmButtonColor: '#e94e1a'
                    });
                    return;
                }

                const vehicle = data.vehicule;
                const reservedSeats = (data.reservedSeats || []).map(seat => parseInt(seat));
                const otherHours = data.otherHours || [];
                const formattedDate = new Date(dateVoyage).toLocaleDateString('fr-FR');
                
                let vehicleTitle = 'Détails du véhicule';
                if(vehicle.marque && vehicle.marque !== 'Bus') {
                     vehicleTitle = `${vehicle.marque} ${vehicle.modele ?? ''}`.trim();
                }

                const visualizationHTML = generatePlacesVisualization(vehicle, reservedSeats);

                // Génération HTML pour les horaires
                let hoursHTML = `
                    <div class="flex flex-wrap gap-2 mt-4 p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <p class="w-full text-[10px] font-black uppercase text-gray-400 mb-1 tracking-widest"><i class="fas fa-clock mr-1"></i> Autres Horaires Disponibles</p>
                `;
                
                if (otherHours.length > 0) {
                    otherHours.forEach(h => {
                        const isActive = h.id == programId;
                        hoursHTML += `
                            <button onclick="window.updateModalContent(${h.vehicule_id}, '${dateVoyage}', ${h.id})" 
                                class="px-3 py-1.5 rounded-lg text-sm font-bold transition-all border-2 ${isActive ? 'bg-[#e94e1a] text-white border-[#e94e1a] shadow-md scale-105' : 'bg-white text-gray-600 border-gray-100 hover:border-orange-200'}">
                                ${h.heure}
                            </button>
                        `;
                    });
                } else {
                    hoursHTML += `<p class="text-xs text-gray-500 italic">Aucun autre horaire</p>`;
                }
                hoursHTML += '</div>';

                Swal.update({
                    title: `
                        <div class="relative w-full">
                            <div class="text-xl font-bold text-[#e94e1a] pr-8">${vehicleTitle}</div>
                            <button onclick="Swal.close()" class="custom-close-btn">&times;</button>
                        </div>
                    `,
                    width: 700,
                    padding: '0',
                    showCloseButton: true,
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
                               
                            </div>
                            
                            ${hoursHTML}

                            <div class="flex gap-4 mt-3 pt-3 border-t border-blue-100/50 text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-500">Places :</span>
                                    <span class="font-bold text-gray-800">
                                        ${vehicle.nombre_place} au total
                                        <span class="bg-red-50 text-red-600 px-2 py-0.5 rounded ml-1 font-bold text-xs border border-red-100">${reservedSeats.length} réservées</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-white min-h-[300px]">
                            <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <span class="w-1 h-5 bg-[#e94e1a] rounded-full block"></span>
                                Occupation du véhicule (${formattedDate})
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
            const safeVehicleId = (vehicleId && vehicleId !== 'null') ? vehicleId : 0;
            const safeProgramId = programId ? programId : '';
            if (!dateVoyage) dateVoyage = new Date().toISOString().split('T')[0];

            Swal.fire({
                title: 'Chargement...',
                html: '<div class="flex flex-col items-center p-4"><div class="w-8 h-8 border-4 border-[#e94e1a] border-t-transparent rounded-full animate-spin mb-2"></div></div>',
                allowOutsideClick: true,
                showConfirmButton: false,
                showCloseButton: true,
                didOpen: async () => { await window.updateModalContent(safeVehicleId, dateVoyage, safeProgramId); }
            });
        }

       function generatePlacesVisualization(vehicle, reservedSeats = []) {
            let config = typeRangeConfig[vehicle.type_range];
            if (!config) {
                config = { placesGauche: 2, placesDroite: 2, description: "Configuration Standard" };
                console.warn(`Configuration de véhicule inconnue: ${vehicle.type_range}. Utilisation du mode par défaut 2x2.`);
            }

            const { placesGauche, placesDroite } = config;
            const placesParRanger = placesGauche + placesDroite;
            const totalPlaces = parseInt(vehicle.nombre_place);
            const nombreRanger = Math.ceil(totalPlaces / placesParRanger);

            let html = `
            <div class="flex flex-col items-center w-full font-sans bg-white pt-4">
                <div class="w-full max-h-[350px] overflow-y-auto scrollbar-thin px-2 pb-4 space-y-6">
            `;

            let numeroPlace = 1;
            for (let ranger = 1; ranger <= nombreRanger; ranger++) {
                const placesRestantes = totalPlaces - (numeroPlace - 1);
                const placesCetteRanger = Math.min(placesParRanger, placesRestantes);
                const placesGaucheCetteRanger = Math.min(placesGauche, placesCetteRanger);
                const placesDroiteCetteRanger = Math.min(placesDroite, placesCetteRanger - placesGaucheCetteRanger);

                html += `
                    <div class="flex items-center justify-center gap-4 sm:gap-8">
                        <!-- Places Gauche -->
                        <div class="flex flex-col items-center">
                            <span class="text-xs font-semibold text-gray-500 mb-2">Rangée ${ranger}</span>
                            <div class="flex justify-center gap-2 flex-wrap">
                `;

                for (let i = 0; i < placesGaucheCetteRanger; i++) {
                    const sn = numeroPlace + i;
                    const isRes = reservedSeats.includes(sn);
                    const styleClass = isRes 
                        ? 'bg-[#e94e1a] text-white border-transparent cursor-not-allowed opacity-90' 
                        : 'bg-blue-500 text-white hover:bg-blue-600 cursor-pointer shadow-sm';
                    
                    html += `<div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center font-bold text-sm sm:text-base transition-all duration-200 ${styleClass}" title="Place ${sn}">
                                ${sn}
                             </div>`;
                }

                html += `    </div>
                        </div>
                        <!-- Allée visuelle -->
                        <div class="flex flex-col justify-end h-full mt-6" style="min-width: 30px;">
                            <div class="w-full h-1 bg-gray-400 rounded-full"></div>
                        </div>
                        <!-- Places Droite -->
                        <div class="flex flex-col items-center">
                            <span class="text-xs font-semibold text-gray-500 mb-2">Rangée ${ranger}</span>
                            <div class="flex justify-center gap-2 flex-wrap">`;

                for (let i = 0; i < placesDroiteCetteRanger; i++) {
                    const sn = numeroPlace + placesGaucheCetteRanger + i;
                    const isRes = reservedSeats.includes(sn);
                    const styleClass = isRes 
                        ? 'bg-[#e94e1a] text-white border-transparent cursor-not-allowed opacity-90' 
                        : 'bg-green-500 text-white hover:bg-green-600 cursor-pointer shadow-sm';

                     html += `<div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center font-bold text-sm sm:text-base transition-all duration-200 ${styleClass}" title="Place ${sn}">
                                ${sn}
                             </div>`;
                }

                html += `    </div>
                        </div>
                    </div>`;
                numeroPlace += placesCetteRanger;
            }

            html += `   </div>
                        <div class="border-t border-gray-100 bg-gray-50 p-4 flex flex-wrap justify-center gap-4 sm:gap-6 rounded-xl mt-2 w-full shadow-inner">
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded-md bg-[#e94e1a]"></div>
                                <span class="text-xs font-bold text-gray-600">Occupé</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded-md bg-blue-500"></div>
                                <span class="text-xs font-bold text-gray-600">Libre (Gauche)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded-md bg-green-500"></div>
                                <span class="text-xs font-bold text-gray-600">Libre (Droite)</span>
                            </div>
                        </div>
                    </div>`;
            
            return html;
        }
    </script>
@endsection