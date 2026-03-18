@extends('home.layouts.template')
@section('content')
    <div class="min-h-screen bg-gray-50 pt-28 pb-10 sm:pt-32 sm:pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- En-tête -->
            <div class="mb-8">
                <div class="bg-white rounded-2xl shadow-sm p-6 md:p-8 border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="text-center md:text-left">
                        <h1 class="text-2xl md:text-3xl font-black text-gray-900 mb-2">
                            Prochains Programmes
                        </h1>
                        <p class="text-gray-500 text-sm md:text-base">
                            Voici les départs disponibles pour le <span class="font-bold text-[#e94e1a]">{{ \Carbon\Carbon::now()->addDay()->format('d/m/Y') }}</span>.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
                        <div class="bg-orange-50 text-[#e94e1a] px-5 py-3 md:py-2.5 rounded-xl font-bold text-sm md:text-base border border-orange-100 w-full md:w-auto text-center">
                            {{ $programmes->total() }} programme(s) disponible(s)
                        </div>
                        <a href="{{ url('/') }}#search-form"
                           class="bg-[#e94e1a] text-white px-6 py-3 md:py-2.5 w-full md:w-auto justify-center rounded-xl hover:bg-[#d14316] shadow-md shadow-orange-500/20 transition-all duration-300 font-bold text-sm md:text-base flex items-center gap-2">
                            <i class="fas fa-search"></i>
                            Recherche spécifique
                        </a>
                    </div>
                </div>
            </div>

            <!-- Résultats en liste -->
            @if ($programmes->count() > 0)
                <div class="w-full mb-6 sm:mb-8">
                    <!-- En-tête de la liste (version desktop) -->
                    <div class="hidden md:block mb-4 md:px-2">
                        <div class="grid grid-cols-12 gap-4 px-3 py-2 text-xs font-black uppercase tracking-widest text-gray-400">
                            <div class="col-span-3 pl-2">Compagnie & Trajet</div>
                            <div class="col-span-2 text-center">Départ</div>
                            <div class="col-span-2 text-center">Tarif</div>
                            <div class="col-span-2 text-center">Disponibilité</div>
                            <div class="col-span-3 text-right pr-2">Action</div>
                        </div>
                    </div>

                    <!-- Liste des programmes -->
                    <div class="space-y-4">
                        @foreach ($programmes->groupBy(function($p) { 
                            return $p->compagnie_id . '-' . $p->point_depart . '-' . $p->point_arrive; 
                        }) as $groupKey => $group)
                            @php 
                                $firstProgramme = $group->first();
                                $displayDate = \Carbon\Carbon::now()->addDay(); 
                                $searchDateParam = $displayDate->format('Y-m-d');
                            @endphp
                            <div class="relative">
                                @include('home.programmes.partials.card', [
                                    'programme' => $firstProgramme, 
                                    'programmes' => $group,
                                    'searchDate' => $searchDateParam
                                ])
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
            '2x4': { placesGauche: 2, placesDroite: 4, description: "2 places à gauche, 4 à droite" },
            'Gamme Prestige': { placesGauche: 2, placesDroite: 2, description: "Catégorie Prestige (2+2)" },
            'Gamme Standard': { placesGauche: 2, placesDroite: 3, description: "Catégorie Standard (2+3)" }
        };

        window.updateModalContent = async function(vehicleId, dateVoyage, programId) {
        try {
            Swal.showLoading();
            
            // CORRECTION : Si vehicleId est null/undefined/0, on envoie '0' au backend
            const safeVehicleId = (vehicleId && vehicleId !== 'null') ? vehicleId : 0;
            
            // On passe le programme_id dans l'URL pour un filtrage précis
            const url = `/vehicule/details/${safeVehicleId}?date=${encodeURIComponent(dateVoyage)}&programme_id=${programId}`;
            
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }

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
            const formattedDate = new Date(dateVoyage).toLocaleDateString('fr-FR');
            
            // Gestion du titre si véhicule par défaut
            let vehicleTitle = 'Détails du véhicule';
            if(vehicle.marque && vehicle.marque !== 'Bus') {
                 vehicleTitle = `${vehicle.marque} ${vehicle.modele ?? ''}`.trim();
            }

            const visualizationHTML = generatePlacesVisualization(vehicle, reservedSeats);

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
                            <!-- Sélecteur de date (optionnel, recharge le modal) -->
                            <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg shadow-sm border border-gray-200">
                                <label for="modal-date-picker" class="text-xs font-semibold text-gray-600">Changer :</label>
                                <input type="date" id="modal-date-picker" value="${dateVoyage}" 
                                    class="border-none focus:ring-0 text-gray-800 font-bold bg-transparent p-0 text-sm cursor-pointer"
                                    onchange="window.updateModalContent(${safeVehicleId}, this.value, ${programId})"
                                >
                            </div>
                        </div>
                        
                        <div class="flex gap-4 mt-3 pt-3 border-t border-blue-100/50 text-sm">
                            ${!vehicle.is_default && vehicle.immatriculation !== 'N/A' ? `
                            
                            ` : ''}
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
                            Disposition des sièges
                        </h3>
                        ${visualizationHTML}
                    </div>
                </div>
                `
            });
            Swal.hideLoading();
        } catch (error) {
            console.error(error);
            Swal.fire({icon: 'error', title: 'Erreur', text: 'Une erreur technique est survenue.', confirmButtonColor: '#e94e1a'});
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

    /**
     * Affiche le premier pop-up pour choisir l'heure
     */
    window.chooseProgramHour = function(programsJson, searchDate) {
        const programs = JSON.parse(programsJson);
        
        if (programs.length === 1) {
            // Si un seul programme, on passe directement aux détails du véhicule
            const p = programs[0];
            showVehicleDetails(p.vehicule_id, searchDate, p.id);
            return;
        }

        let buttonsHtml = '<div class="grid grid-cols-2 gap-3 p-4">';
        programs.forEach(p => {
            buttonsHtml += `
                <button onclick="Swal.close(); showVehicleDetails(${p.vehicule_id || 0}, '${searchDate}', ${p.id})" 
                    class="flex flex-col items-center justify-center p-4 bg-white border-2 border-orange-100 rounded-2xl hover:border-[#e94e1a] hover:bg-orange-50 transition-all group">
                    <span class="text-2xl font-black text-gray-900 group-hover:text-[#e94e1a]">${p.heure.substring(0, 5)}</span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Départ</span>
                </button>
            `;
        });
        buttonsHtml += '</div>';

        Swal.fire({
            title: `
                <div class="text-xl font-black text-gray-900 pt-4">
                    <i class="fas fa-clock text-[#e94e1a] mr-2"></i>CHOISIR L'HEURE
                </div>
            `,
            html: `
                <p class="text-sm text-gray-500 mb-2">Plusieurs départs sont disponibles pour ce trajet.</p>
                ${buttonsHtml}
            `,
            showConfirmButton: false,
            showCloseButton: true,
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                closeButton: 'focus:outline-none'
            }
        });
    }


        function generatePlacesVisualization(vehicle, reservedSeats = []) {
    // 1. Récupération de la configuration
    let config = typeRangeConfig[vehicle.type_range];
    
    if (!config) {
        config = { placesGauche: 2, placesDroite: 2, description: "Configuration Standard" };
        console.warn(`Configuration de véhicule inconnue: ${vehicle.type_range}. Utilisation du mode par défaut 2x2.`);
    }

    const { placesGauche, placesDroite } = config;
    const placesParRanger = placesGauche + placesDroite;
    const totalPlaces = parseInt(vehicle.nombre_place);
    const nombreRanger = Math.ceil(totalPlaces / placesParRanger);

    // 2. Début du HTML
    let html = `
    <div class="flex flex-col items-center w-full font-sans bg-white pt-4">
        <div class="w-full max-h-[350px] overflow-y-auto scrollbar-thin px-2 pb-4 space-y-6">
    `;

    // 3. Boucle des places
    let numeroPlace = 1;
    for (let ranger = 1; ranger <= nombreRanger; ranger++) {
        // Calcul des places pour cette rangée spécifique
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

        // Génération Gauche
        for (let i = 0; i < placesGaucheCetteRanger; i++) {
            const sn = numeroPlace + i; // sn = Seat Number
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

        // Génération Droite
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
        
        // Mise à jour du compteur global
        numeroPlace += placesCetteRanger;
    }

    // 4. Pied de page (Légende)
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