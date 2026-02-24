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
                        @foreach ($programmes as $programme)
                            @php
                                $displayDate = \Carbon\Carbon::now()->addDay(); 
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
                                                    <h3 class="font-bold text-gray-900 leading-tight">
                                                        {{ $programme->compagnie->slogan ?: ($programme->compagnie->name ?? 'Compagnie') }}</h3>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-lg font-bold text-[#e94e1a]">
                                                    {{ number_format($programme->montant_billet, 0, ',', ' ') }} FCFA
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Trajet mobile -->
                                        <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-100">
                                            <div class="flex items-center justify-between mb-1">
                                                <div class="text-center flex-1">
                                                    <div class="font-black text-gray-900 text-base leading-tight">{{ $programme->point_depart }}</div>
                                                    <div class="text-[10px] text-blue-600 font-bold uppercase mt-1 bg-blue-50/50 px-1.5 py-0.5 border border-blue-100 rounded inline-flex items-start gap-1 max-w-full text-left"><i class="fas fa-map-marker-alt mt-0.5 flex-shrink-0"></i><span class="whitespace-normal leading-tight">{{ $programme->gareDepart->nom_gare ?? 'Gare Départ' }}</span></div>
                                                </div>
                                                <div class="mx-3 text-gray-300"><i class="fas fa-long-arrow-alt-right text-lg"></i></div>
                                                <div class="text-center flex-1">
                                                    <div class="font-black text-gray-900 text-base leading-tight">{{ $programme->point_arrive }}</div>
                                                    <div class="text-[10px] text-green-600 font-bold uppercase mt-1 bg-green-50/50 px-1.5 py-0.5 border border-green-100 rounded inline-flex items-start gap-1 max-w-full text-left"><i class="fas fa-map-marker-alt mt-0.5 flex-shrink-0"></i><span class="whitespace-normal leading-tight">{{ $programme->gareArrivee->nom_gare ?? 'Gare Arrivée' }}</span></div>
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
                                                        $statusTexts = ['disponible' => 'Disponible', 'presque_complet' => 'Presque complet', 'complet' => 'Complet'];
                                                        $totalSeats = $programme->getTotalSeats($searchDateParam);
                                                        $reservedSeatsCount = $programme->getPlacesReserveesForDate($searchDateParam);
                                                        $statusKey = $programme->getStatutPlacesForDate($searchDateParam);
                                                    @endphp
                                                    <span class="{{ $statusKey == 'complet' ? 'text-red-600' : ($statusKey == 'presque_complet' ? 'text-yellow-600' : 'text-green-600') }} font-bold">
                                                        {{ $reservedSeatsCount }}/{{ $totalSeats }}
                                                    </span>
                                                    <span class="text-[10px] text-gray-500 ml-1">({{ $statusTexts[$statusKey] ?? '' }})</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Actions mobile -->
                                        <div class="flex gap-2">
                                            @if ($programme->statut_places != 'complet')
                                                <a href="{{ route('reservation.create', [
                                                        'point_depart' => $programme->point_depart,
                                                        'point_arrive' => $programme->point_arrive,
                                                        'date_depart' => $searchDateParam,
                                                        'auto_reserve' => $programme->id
                                                    ]) }}"
                                                    class="flex-1 bg-[#e94e1a] text-white text-center py-2 rounded-lg font-bold hover:bg-orange-600 transition-all duration-300 flex items-center justify-center gap-2">
                                                    <i class="fas fa-ticket-alt"></i> <span>Réserver</span>
                                                </a>
                                            @else
                                                <button class="flex-1 bg-gray-400 text-white text-center py-2 rounded-lg font-bold cursor-not-allowed flex items-center justify-center gap-2" disabled>
                                                    <i class="fas fa-times-circle"></i> <span>Complet</span>
                                                </button>
                                            @endif
                                            
                                            <!-- BOUTON DETAILS MOBILE CORRIGÉ -->
                                            <a href="#"
                                                onclick="showVehicleDetails({{ optional($programme->getVehiculeForDate($searchDateParam))->id ?? 'null' }}, '{{ $searchDateParam }}', {{ $programme->id }}); return false;"
                                                class="px-3 bg-white text-gray-600 border border-gray-200 text-center py-2 rounded-lg font-bold hover:bg-gray-50 transition-all duration-300 flex items-center justify-center gap-1.5 vehicle-details-btn text-xs hover:text-gray-900">
                                                <i class="fas fa-info-circle"></i> Détails
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Version Desktop -->
                                <div class="hidden md:block">
                                    <div class="grid grid-cols-12 gap-4 p-5 items-center">
                                        <!-- Compagnie & Trajet -->
                                        <div class="col-span-3">
                                            <div class="flex items-center gap-4">
                                                <div class="w-14 h-14 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm overflow-hidden p-1">
                                                    @if($programme->compagnie->path_logo ?? false)
                                                        <img src="{{ asset('storage/' . $programme->compagnie->path_logo) }}" class="w-full h-full object-contain" alt="Logo">
                                                    @else
                                                        <i class="fas fa-bus text-gray-400 text-2xl"></i>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0 flex flex-col justify-center">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <h3 class="font-black text-gray-900 text-sm whitespace-normal leading-tight line-clamp-2 max-w-[180px]">{{ $programme->compagnie->sigle ?: ($programme->compagnie->name ?? 'Compagnie') }}</h3>
                                                        @if ($programme->is_aller_retour)
                                                            <span class="bg-blue-50 text-blue-600 border border-blue-100 text-[10px] px-1.5 py-0.5 rounded font-bold uppercase tracking-wider"><i class="fas fa-exchange-alt"></i></span>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-start gap-2 text-xs">
                                                        <div class="flex flex-col">
                                                            <div class="font-bold text-gray-800 leading-none">{{ $programme->point_depart }}</div>
                                                            <div class="text-[9px] text-blue-600 font-bold uppercase mt-1 flex items-start gap-1 max-w-[120px]"><i class="fas fa-map-marker-alt mt-0.5"></i><span class="whitespace-normal leading-tight text-left">{{ $programme->gareDepart->nom_gare ?? 'Gare' }}</span></div>
                                                        </div>
                                                        <div class="flex flex-col justify-center translate-y-0.5">
                                                            <i class="fas fa-long-arrow-alt-right text-gray-300"></i>
                                                        </div>
                                                        <div class="flex flex-col">
                                                            <div class="font-bold text-gray-800 leading-none">{{ $programme->point_arrive }}</div>
                                                            <div class="text-[9px] text-green-600 font-bold uppercase mt-1 flex items-start gap-1 max-w-[120px]"><i class="fas fa-map-marker-alt mt-0.5"></i><span class="whitespace-normal leading-tight text-left">{{ $programme->gareArrivee->nom_gare ?? 'Gare' }}</span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Date & Heure -->
                                        <div class="col-span-2 flex flex-col items-center justify-center border-l border-gray-100 h-full">
                                            <div class="flex items-center gap-2 text-xs font-bold text-gray-800 mb-1">
                                                <i class="fas fa-calendar-day text-gray-400"></i>
                                                <span>{{ $formattedDisplayDate }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 bg-gray-50 px-2.5 py-1 rounded-md">
                                                <i class="fas fa-clock text-gray-400"></i>
                                                <span>{{ substr($programme->heure_depart, 0, 5) }} &rarr; {{ substr($programme->heure_arrive, 0, 5) }}</span>
                                            </div>
                                        </div>

                                        <!-- Durée & Prix -->
                                        <div class="col-span-2 flex justify-center flex-col items-center border-l border-gray-100 h-full">
                                            <div class="flex items-center gap-1.5 text-[11px] font-bold text-gray-500 mb-1">
                                                <i class="fas fa-hourglass-half"></i>
                                                <span>{{ $programme->durer_parcours }}</span>
                                            </div>
                                            <div class="text-lg font-black text-[#e94e1a]">
                                                {{ number_format($programme->montant_billet, 0, ',', ' ') }} <span class="text-[10px] font-bold text-gray-500">FCFA</span>
                                            </div>
                                        </div>

                                        <!-- Statut -->
                                        <div class="col-span-2 flex justify-center border-l border-gray-100 h-full">
                                            @php
                                                $totalSeats = $programme->getTotalSeats($searchDateParam);
                                                $reservedSeatsCount = $programme->getPlacesReserveesForDate($searchDateParam);
                                                $statusKey = $programme->getStatutPlacesForDate($searchDateParam);
                                            @endphp
                                            <div class="flex flex-col items-center">
                                                <div class="flex items-center gap-2 bg-gray-50 px-3 py-1 rounded-lg border border-gray-100 mb-1.5">
                                                    <div class="w-2.5 h-2.5 rounded-full {{ $statusKey == 'disponible' ? 'bg-green-500 animate-pulse' : ($statusKey == 'presque_complet' ? 'bg-yellow-500' : 'bg-red-500') }}"></div>
                                                    <span class="font-black text-sm {{ $statusKey == 'disponible' ? 'text-green-600' : ($statusKey == 'presque_complet' ? 'text-yellow-600' : 'text-red-600') }}">
                                                        {{ $totalSeats - $reservedSeatsCount }} <span class="text-[10px] font-bold text-gray-500 uppercase">rest.</span>
                                                    </span>
                                                </div>
                                                <span class="text-[9px] uppercase font-black tracking-widest text-gray-400">
                                                    {{ $statusTexts[$statusKey] ?? '' }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Actions -->
                                        <div class="col-span-3 flex items-center justify-end gap-2 text-right">
                                            <button
                                                onclick="showVehicleDetails({{ optional($programme->getVehiculeForDate($searchDateParam))->id ?? 'null' }}, '{{ $searchDateParam }}', {{ $programme->id }})"
                                                class="px-4 py-2.5 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-600 hover:text-gray-900 border border-gray-200 transition-colors flex items-center justify-center font-bold text-xs gap-2" title="Détails du véhicule">
                                                <i class="fas fa-info-circle text-base"></i> Détails
                                            </button>

                                            @if ($programme->statut_places != 'complet')
                                                <a href="{{ route('reservation.create', [
                                                        'point_depart' => $programme->point_depart,
                                                        'point_arrive' => $programme->point_arrive,
                                                        'date_depart' => $searchDateParam,
                                                        'auto_reserve' => $programme->id
                                                    ]) }}"
                                                    class="flex-1 max-w-[140px] bg-[#e94e1a] text-white px-4 py-2.5 rounded-xl font-bold hover:bg-[#d14316] shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2 text-sm">
                                                    <i class="fas fa-ticket-alt"></i> Réserver
                                                </a>
                                            @else
                                                <button class="flex-1 max-w-[140px] bg-gray-100 text-gray-400 px-4 py-2.5 rounded-xl font-bold cursor-not-allowed justify-center flex items-center gap-2 text-sm" disabled>
                                                    <i class="fas fa-times-circle"></i> Complet
                                                </button>
                                            @endif
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
        // --- CORRECTION MAJEURE ICI ---
        // On supprime la ligne : if (!vehicleId) return; 
        // On autorise l'ouverture même si l'ID est null (le backend gérera le fallback)
        
        if (!dateVoyage) dateVoyage = new Date().toISOString().split('T')[0];
        
        // On s'assure que programId n'est pas undefined
        const safeProgramId = programId ? programId : '';
        
        // On s'assure que vehicleId n'est pas 'null' (string) ou undefined
        const safeVehicleId = (vehicleId && vehicleId !== 'null') ? vehicleId : 0;

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

    // 2. Début du HTML (Style Tableau Clean)
    let html = `
    <div class="flex flex-col items-center w-full font-sans">
        


        <!-- Conteneur Tableau -->
        <div class="w-full border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm">
            <!-- En-têtes -->
            <div class="grid grid-cols-[60px_1fr_40px_1fr] bg-gray-50 border-b border-gray-100 py-3 px-2">
                <div class="text-xs font-black text-gray-400 uppercase text-center">RANG</div>
                <div class="text-xs font-black text-gray-400 uppercase text-center">GAUCHE</div>
                <div class="text-xs font-black text-gray-400 uppercase text-center border-l border-r border-gray-200 mx-1">ALLÉE</div>
                <div class="text-xs font-black text-gray-400 uppercase text-center">DROITE</div>
            </div>
            
            <div class="max-h-[350px] overflow-y-auto scrollbar-thin p-3 space-y-2">
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
            <div class="grid grid-cols-[60px_1fr_40px_1fr] items-center py-1">
                <!-- Numéro Rangée -->
                <div class="text-center font-black text-gray-300 text-sm">R${ranger}</div>
                
                <!-- Places Gauche -->
                <div class="flex justify-center gap-3 flex-wrap">
        `;

        // Génération Gauche
        for (let i = 0; i < placesGaucheCetteRanger; i++) {
            const sn = numeroPlace + i; // sn = Seat Number
            const isRes = reservedSeats.includes(sn);
            
            // STYLE EXACT DU POPUP PRÉCÉDENT
            // Occupé : Fond rouge, texte blanc
            // Libre : Fond blanc, bordure grise. Hover : Bordure orange, texte orange
            const styleClass = isRes 
                ? 'bg-[#e94e1a] text-white border-transparent cursor-not-allowed opacity-100' 
                : 'bg-white text-gray-700 border-gray-300 hover:border-[#e94f1b] hover:text-[#e94f1b] cursor-pointer shadow-sm';
            
            html += `<div class="w-9 h-9 border-2 rounded-lg flex items-center justify-center font-bold text-sm transition-all duration-200 ${styleClass}" title="Place ${sn}">
                        ${sn}
                     </div>`;
        }

        html += `</div>
                <!-- Allée visuelle -->
                <div class="flex justify-center h-full">
                    <div class="w-px bg-gray-100 h-full"></div>
                </div>
                <!-- Places Droite -->
                <div class="flex justify-center gap-3 flex-wrap">`;

        // Génération Droite
        for (let i = 0; i < placesDroiteCetteRanger; i++) {
            const sn = numeroPlace + placesGaucheCetteRanger + i;
            const isRes = reservedSeats.includes(sn);
            
            // Même style ici
            const styleClass = isRes 
                ? 'bg-[#e94e1a] text-white border-transparent cursor-not-allowed opacity-100' 
                : 'bg-white text-gray-700 border-gray-300 hover:border-[#e94f1b] hover:text-[#e94f1b] cursor-pointer shadow-sm';

             html += `<div class="w-9 h-9 border-2 rounded-lg flex items-center justify-center font-bold text-sm transition-all duration-200 ${styleClass}" title="Place ${sn}">
                        ${sn}
                     </div>`;
        }

        html += `</div></div>`;
        
        // Mise à jour du compteur global
        numeroPlace += placesCetteRanger;
    }

    // 4. Pied de page (Légende)
    html += `   </div>
                <div class="border-t border-gray-100 bg-gray-50 p-3 flex justify-center gap-6 rounded-b-xl">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-[#e94e1a]"></div>
                        <span class="text-xs font-bold text-gray-600">Occupé</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-white border border-gray-300"></div>
                        <span class="text-xs font-bold text-gray-600">Libre</span>
                    </div>
                </div>
            </div>
        </div>`;
    
    return html;
}
    </script>
@endsection