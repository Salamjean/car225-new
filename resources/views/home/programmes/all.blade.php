@extends('home.layouts.template')
@section('content')
    <div class="min-h-screen bg-gradient-to-br from-white to-green-50 py-4 sm:py-6 lg:py-8">
        <div class="w-full px-3 sm:px-4 lg:px-6">
            <!-- En-tête -->
            <div class="mb-6 sm:mb-8">
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 border border-gray-100 text-center">
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-3">
                        Tous nos Programmes
                    </h1>
                    <p class="text-gray-600 mb-4 max-w-2xl mx-auto">
                        Découvrez tous les trajets disponibles proposés par nos compagnies partenaires.
                        Réservez votre place dès maintenant !
                    </p>
                    <div class="inline-block bg-[#fea219] text-white px-4 py-2 rounded-xl font-bold text-lg shadow-md">
                        {{ $programmes->total() }} programme(s) disponible(s)
                    </div>
                    
                    <div class="mt-6 flex justify-center">
                         <a href="{{ url('/') }}#search-form"
                            class="bg-white text-[#fea219] border border-[#fea219] px-6 py-2 rounded-xl hover:bg-[#fea219] hover:text-white transition-all duration-300 font-semibold text-base flex items-center gap-2">
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
                        <div class="bg-gradient-to-r from-[#fea219]/10 to-orange-500/10 rounded-xl p-4">
                            <div class="grid grid-cols-12 gap-4 text-sm font-semibold text-gray-700">
                                <div class="col-span-3 text-center">Compagnie & Trajet</div>
                                <div class="col-span-2 text-center">Date & Heure</div>
                                <div class="col-span-2 text-center">Durée & Prix</div>
                                <div class="col-span-2 text-center">Statut</div>
                                <div class="col-span-3 text-center">Actions</div>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des programmes -->
                    <div class="space-y-4">
                        @foreach ($programmes as $programme)
                            <div
                                class="w-full bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden group">
                                <!-- Version Mobile -->
                                <div class="block md:hidden">
                                    <div class="p-4">
                                        <!-- En-tête mobile -->
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-10 h-10 bg-gradient-to-r from-[#fea219] to-orange-500 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-bus text-white"></i>
                                                </div>
                                                <div>
                                                    <h3 class="font-bold text-gray-900">
                                                        {{ $programme->compagnie->name ?? 'Compagnie' }}</h3>
                                                    @if ($programme->is_aller_retour)
                                                        <span class="text-xs text-blue-600 font-semibold block">
                                                            <i class="fas fa-exchange-alt"></i> Aller-Retour
                                                        </span>
                                                    @endif
                                                    @if ($programme->compagnie)
                                                        <span class="text-xs text-green-600 font-semibold">
                                                            <i class="fas fa-check-circle"></i> Vérifié
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-lg font-bold text-[#fea219]">
                                                    {{ number_format($programme->montant_billet, 0, ',', ' ') }} FCFA
                                                </div>
                                                <div class="text-xs text-gray-500">Prix</div>
                                            </div>
                                        </div>

                                        <!-- Trajet mobile -->
                                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="text-center">
                                                    <div class="font-bold text-gray-900">{{ $programme->point_depart }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">Départ</div>
                                                </div>
                                                <div class="mx-2">
                                                    <i class="fas fa-arrow-right text-[#fea219]"></i>
                                                </div>
                                                <div class="text-center">
                                                    <div class="font-bold text-gray-900">{{ $programme->point_arrive }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">Arrivée</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Informations mobile -->
                                        <div class="grid grid-cols-2 gap-3 mb-4">
                                            <div class="bg-green-50 p-2 rounded-lg">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <i class="fas fa-clock text-green-500"></i>
                                                    <span class="text-sm font-semibold">Heures</span>
                                                </div>
                                                <div class="text-xs">{{ $programme->heure_depart }} -
                                                    {{ $programme->heure_arrive }}</div>
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
                                                        $statusTexts = [
                                                            'vide' => 'Places disponibles',
                                                            'presque_complet' => 'Presque complet',
                                                            'rempli' => 'Complet',
                                                        ];
                                                    @endphp
                                                    <span
                                                        class="{{ $programme->staut_place == 'rempli'
                                                            ? 'text-red-600'
                                                            : ($programme->staut_place == 'presque_complet'
                                                                ? 'text-yellow-600'
                                                                : 'text-green-600') }} font-semibold">
                                                        {{ $statusTexts[$programme->staut_place] ?? 'Statut inconnu' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="bg-blue-50 p-2 rounded-lg">
                                                 <div class="flex items-center gap-2 mb-1">
                                                    <i class="fas fa-calendar-alt text-blue-500"></i>
                                                    <span class="text-sm font-semibold">Date</span>
                                                </div>
                                                <div class="text-xs">
                                                    {{ $programme->date_depart ? \Carbon\Carbon::parse($programme->date_depart)->format('d/m/Y') : 'Récurrent' }}
                                                </div>
                                                @if($programme->type_programmation === 'recurrent' && $programme->jours_recurrence)
                                                <div class="text-[10px] text-blue-600 mt-1 font-medium">
                                                    <i class="fas fa-redo-alt mr-1"></i>{{ implode(', ', json_decode($programme->jours_recurrence, true) ?? []) }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Actions mobile -->
                                        <div class="flex gap-2">
                                            @if ($programme->staut_place != 'rempli')
                                                <a href="{{ route('reservation.create', ['programme_id' => $programme->id, 'date_depart' => $programme->date_depart ? \Carbon\Carbon::parse($programme->date_depart)->format('Y-m-d') : date('Y-m-d')]) }}"
                                                    class="flex-1 bg-[#fea219] text-white text-center py-2 rounded-lg font-bold hover:bg-orange-600 transition-all duration-300 flex items-center justify-center gap-2">
                                                    <i class="fas fa-ticket-alt"></i>
                                                    <span>Réserver</span>
                                                </a>
                                            @else
                                                <button
                                                    class="flex-1 bg-gray-400 text-white text-center py-2 rounded-lg font-bold cursor-not-allowed flex items-center justify-center gap-2"
                                                    disabled>
                                                    <i class="fas fa-times-circle"></i>
                                                    <span>Complet</span>
                                                </button>
                                            @endif
                                            <!-- Actions mobile - Modifiez le bouton info -->
                                            <a href="#"
                                                onclick="showVehicleDetails({{ $programme->vehicule->id ?? 'null' }}, '{{ $programme->date_depart ? \Carbon\Carbon::parse($programme->date_depart)->format('Y-m-d') : date('Y-m-d') }}'); return false;"
                                                class="w-12 bg-white text-[#fea219] border border-[#fea219] text-center py-2 rounded-lg font-bold hover:bg-gray-50 transition-all duration-300 flex items-center justify-center vehicle-details-btn"
                                                data-vehicle-id="{{ $programme->vehicule->id ?? '' }}"
                                                data-search-date="{{ $programme->date_depart ? \Carbon\Carbon::parse($programme->date_depart)->format('Y-m-d') : date('Y-m-d') }}">
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
                                                <div
                                                    class="w-12 h-12 bg-gradient-to-r from-[#fea219] to-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-bus text-white text-lg"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2 mb-1"
                                                        style="display:flex; justify-content:center">
                                                        <h3 class="font-bold text-gray-900 truncate">
                                                            {{ $programme->compagnie->name ?? 'Compagnie' }}</h3>
                                                        @if ($programme->is_aller_retour)
                                                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded-full font-semibold">
                                                                <i class="fas fa-exchange-alt text-xs"></i> Aller-Retour
                                                            </span>
                                                        @endif
                                                        @if ($programme->compagnie)
                                                            <span
                                                                class="bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full font-semibold">
                                                                <i class="fas fa-check-circle text-xs"></i> Vérifié
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center gap-2 text-sm text-gray-600"
                                                        style="display:flex; justify-content:center">
                                                        <div class="font-semibold text-gray-900">
                                                            {{ $programme->point_depart }}</div>
                                                        <i class="fas fa-arrow-right text-[#fea219] text-xs"></i>
                                                        <div class="font-semibold text-gray-900">
                                                            {{ $programme->point_arrive }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Date & Heure -->
                                        <div class="col-span-2" style="display:flex; justify-content:center; flex-direction: column; align-items: center;">
                                            <div class="flex items-center gap-2 text-sm font-semibold mb-1">
                                                <i class="fas fa-calendar-alt text-blue-500"></i>
                                                <span>{{ $programme->date_depart ? \Carbon\Carbon::parse($programme->date_depart)->format('d/m/Y') : 'Récurrent' }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-sm">
                                                <i class="fas fa-clock text-green-500"></i>
                                                <span>{{ $programme->heure_depart }} →
                                                    {{ $programme->heure_arrive }}</span>
                                            </div>
                                            @if($programme->type_programmation === 'recurrent' && $programme->jours_recurrence)
                                            <div class="text-xs text-blue-600 mt-1 font-medium">
                                                <i class="fas fa-redo-alt mr-1"></i>{{ implode(', ', json_decode($programme->jours_recurrence, true) ?? []) }}
                                            </div>
                                            @endif
                                        </div>

                                        <!-- Durée & Prix -->
                                        <div class="col-span-2" style="display:flex; justify-content:center">
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-2"
                                                    style="display:flex; justify-content:center">
                                                    <i class="fas fa-hourglass-half text-purple-500"></i>
                                                    <span class="font-semibold">{{ $programme->durer_parcours }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-money-bill-wave text-red-500"></i>
                                                    <span class="font-bold text-lg text-[#fea219]">
                                                        {{ number_format($programme->montant_billet, 0, ',', ' ') }} FCFA
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Statut -->
                                        <div class="col-span-2" style="display:flex; justify-content:center">
                                            @php
                                                $statusColors = [
                                                    'vide' => 'bg-green-100 text-green-800 border-green-200',
                                                    'presque_complet' =>
                                                        'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                    'rempli' => 'bg-red-100 text-red-800 border-red-200',
                                                ];
                                                $statusTexts = [
                                                    'vide' => 'Places disponibles',
                                                    'presque_complet' => 'Presque complet',
                                                    'rempli' => 'Complet',
                                                ];
                                            @endphp
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-3 h-3 rounded-full {{ $programme->staut_place == 'vide'
                                                        ? 'bg-green-500'
                                                        : ($programme->staut_place == 'presque_complet'
                                                            ? 'bg-yellow-500'
                                                            : 'bg-red-500') }}">
                                                </div>
                                                <span
                                                    class="font-semibold {{ $programme->staut_place == 'vide'
                                                        ? 'text-green-600'
                                                        : ($programme->staut_place == 'presque_complet'
                                                            ? 'text-yellow-600'
                                                            : 'text-red-600') }}">
                                                    {{ $statusTexts[$programme->staut_place] ?? 'Statut inconnu' }}
                                                </span>
                                            </div>
                                            @if ($programme->staut_place == 'presque_complet')
                                                <div class="text-xs text-yellow-600 mt-1">
                                                    <i class="fas fa-exclamation-triangle"></i> Dernières places !
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Actions -->
                                        <div class="col-span-3" style="display:flex; justify-content:center">
                                            <div class="flex gap-2 justify-end">
                                                <button
                                                    onclick="showVehicleDetails({{ $programme->vehicule->id ?? 'null' }}, '{{ $programme->date_depart ? \Carbon\Carbon::parse($programme->date_depart)->format('Y-m-d') : date('Y-m-d') }}')"
                                                    class="bg-white text-[#fea219] border border-[#fea219] px-4 py-2 rounded-lg font-bold hover:bg-gray-50 transition-all duration-300 flex items-center gap-2 vehicle-details-btn"
                                                    data-vehicle-id="{{ $programme->vehicule->id ?? '' }}"
                                                    data-search-date="{{ $programme->date_depart ? \Carbon\Carbon::parse($programme->date_depart)->format('Y-m-d') : date('Y-m-d') }}">
                                                    <i class="fas fa-info-circle"></i>
                                                    <span class="hidden lg:inline">Détails véhicule</span>
                                                </button>
                                                @if ($programme->staut_place != 'rempli')
                                                    <a href="{{ route('reservation.create', ['programme_id' => $programme->id, 'date_depart' => $programme->date_depart ? \Carbon\Carbon::parse($programme->date_depart)->format('Y-m-d') : date('Y-m-d')]) }}"
                                                        class="bg-[#fea219] text-white px-4 py-2 rounded-lg font-bold hover:bg-orange-600 transition-all duration-300 flex items-center gap-2">
                                                        <i class="fas fa-ticket-alt"></i>
                                                        <span class="hidden lg:inline">Réserver</span>
                                                    </a>
                                                @else
                                                    <button
                                                        class="bg-gray-400 text-white px-4 py-2 rounded-lg font-bold cursor-not-allowed flex items-center gap-2"
                                                        disabled>
                                                        <i class="fas fa-times-circle"></i>
                                                        <span class="hidden lg:inline">Complet</span>
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
                    <div class="w-full bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 border border-gray-100">
                        <div class="w-full flex justify-center">
                            <div class="w-full pagination-wrapper">
                                {{ $programmes->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <!-- Aucun résultat -->
                <div
                    class="w-full bg-white rounded-xl sm:rounded-2xl shadow-lg p-6 sm:p-8 lg:p-12 text-center border border-gray-100">
                    <div class="w-full max-w-4xl mx-auto">
                        <div
                            class="w-16 h-16 sm:w-20 sm:h-20 lg:w-24 lg:h-24 bg-gradient-to-br from-orange-100 to-green-100 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
                            <i class="fas fa-route text-2xl sm:text-3xl lg:text-4xl text-[#fea219]"></i>
                        </div>
                        <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 mb-2 sm:mb-3">Aucun programme
                            disponible pour le moment</h3>
                        <p class="text-gray-600 mb-6 sm:mb-8 leading-relaxed text-sm sm:text-base max-w-2xl mx-auto">
                            Revenez plus tard ou effectuez une recherche spécifique.
                        </p>
                        <div class="w-full flex justify-center">
                            <a href="{{ url('/') }}#search-form"
                                class="bg-[#fea219] text-white px-4 sm:px-6 py-3 rounded-lg sm:rounded-xl font-bold hover:bg-orange-600 transition-all duration-300 transform hover:-translate-y-0.5 shadow-md flex items-center justify-center gap-2 text-sm sm:text-base">
                                <i class="fas fa-search"></i>
                                Rechercher un trajet
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        /* Styles responsifs pour la pagination */
        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
            margin: 0;
            gap: 4px;
            flex-wrap: wrap;
            width: 100%;
        }

        .pagination li {
            margin: 0;
        }

        .pagination li a,
        .pagination li span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            min-width: 40px;
            font-size: 0.875rem;
        }

        .pagination li a {
            background-color: white;
            border-color: #e5e7eb;
            color: #6b7280;
        }

        .pagination li a:hover {
            background-color: #fea219;
            border-color: #fea219;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(254, 162, 25, 0.3);
        }

        .pagination li span {
            background-color: #fea219;
            border-color: #fea219;
            color: white;
            box-shadow: 0 2px 8px rgba(254, 162, 25, 0.3);
        }

        .pagination li.active span {
            background-color: #e89116;
            border-color: #e89116;
        }

        /* Animation pour les éléments de liste */
        .group {
            transition: all 0.3s ease;
        }

        .group:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Styles pour la grille en liste */
        @media (max-width: 768px) {
            .grid.grid-cols-12 {
                display: block;
            }
        }

        /* Pour s'assurer que tout prend 100% de largeur */
        .w-full {
            width: 100% !important;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Configuration des types de rangées (identique à celle du formulaire de création)
        const typeRangeConfig = {
            '2x2': {
                placesGauche: 2,
                placesDroite: 2,
                description: "2 places par côté"
            },
            '2x3': {
                placesGauche: 2,
                placesDroite: 3,
                description: "2 places à gauche, 3 à droite"
            },
            '2x4': {
                placesGauche: 2,
                placesDroite: 4,
                description: "2 places à gauche, 4 à droite"
            }
        };

        // Fonction pour afficher les détails du véhicule avec les places réservées
        async function showVehicleDetails(vehicleId, dateVoyage) {
            if (!vehicleId) {
                Swal.fire({
                    icon: 'info',
                    title: 'Information',
                    text: 'Aucun véhicule associé à ce programme.',
                    confirmButtonColor: '#fea219',
                });
                return;
            }

            if (!dateVoyage) {
                dateVoyage = new Date().toISOString().split('T')[0];
            }

            console.log('Date utilisée pour la recherche:', dateVoyage);

            // Afficher un loader pendant le chargement
            Swal.fire({
                title: 'Chargement...',
                text: `Récupération des informations du véhicule pour le ${new Date(dateVoyage).toLocaleDateString('fr-FR')}`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                // Utiliser la route web
                const url = `/vehicule/details/${vehicleId}?date=${encodeURIComponent(dateVoyage)}`;
                
                const response = await fetch(url);

                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status} - ${response.statusText}`);
                }

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.error || 'Erreur inconnue');
                }

                const vehicle = data.vehicule;
                const reservedSeats = data.reservedSeats || [];

                // Générer la visualisation des places avec les places réservées
                const visualizationHTML = generatePlacesVisualization(vehicle, reservedSeats);

                // Formater la date pour l'affichage
                const formattedDate = new Date(dateVoyage).toLocaleDateString('fr-FR');

                // Afficher le pop-up avec les détails
                Swal.fire({
                    title: `<strong>${vehicle.marque} ${vehicle.modele}</strong>`,
                    html: `
                <div style="text-align: left; max-width: 800px;">
                    <!-- Date du voyage -->
                    <div style="background: #f0f9ff; padding: 10px 15px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid #3b82f6;">
                        <strong style="color: #1e40af;">Date du voyage :</strong> 
                        <span style="color: #1e40af; font-weight: bold;">${formattedDate}</span>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 20px;">
                            <div style="background: #f8f9fa; padding: 12px; border-radius: 8px;">
                                <strong style="color: #6b7280; font-size: 0.9rem;">Immatriculation</strong>
                                <div style="font-weight: bold; font-size: 1.1rem;">${vehicle.immatriculation}</div>
                            </div>
                            <div style="background: #f8f9fa; padding: 12px; border-radius: 8px;">
                                <strong style="color: #6b7280; font-size: 0.9rem;">Numéro de série</strong>
                                <div style="font-weight: bold; font-size: 1.1rem;">${vehicle.numero_serie || 'N/A'}</div>
                            </div>
                        </div>
                        
                        <div style="background: #f8f9fa; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;">
                                <div style="text-align: center;">
                                    <div style="color: #6b7280; font-size: 0.9rem;">Type de rangée</div>
                                    <div style="color: #fea219; font-weight: bold; font-size: 1.2rem;">${vehicle.type_range}</div>
                                </div>
                                <div style="text-align: center;">
                                    <div style="color: #6b7280; font-size: 0.9rem;">Rangées</div>
                                    <div style="color: #10b981; font-weight: bold; font-size: 1.2rem;">
                                        ${Math.ceil(vehicle.nombre_place / (typeRangeConfig[vehicle.type_range]?.placesGauche + typeRangeConfig[vehicle.type_range]?.placesDroite))}
                                    </div>
                                </div>
                                <div style="text-align: center;">
                                    <div style="color: #6b7280; font-size: 0.9rem;">Total places</div>
                                    <div style="color: #3b82f6; font-weight: bold; font-size: 1.2rem;">${vehicle.nombre_place}</div>
                                </div>
                                <div style="text-align: center;">
                                    <div style="color: #6b7280; font-size: 0.9rem;">Places occupées</div>
                                    <div style="color: #ef4444; font-weight: bold; font-size: 1.2rem;">${reservedSeats.length}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h3 style="margin-bottom: 15px; color: #374151; font-size: 1.1rem; font-weight: 600;">Configuration des places</h3>
                    ${visualizationHTML}
                </div>
            `,
                    width: 850,
                    padding: '20px',
                    showCloseButton: true,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'vehicle-details-popup'
                    }
                });

            } catch (error) {
                console.error('Erreur détaillée:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    html: `
                <div style="text-align: left;">
                    <p style="margin-bottom: 10px;">Impossible de charger les détails du véhicule.</p>
                    <p style="color: #6b7280; font-size: 0.9rem;">Détail: ${error.message}</p>
                </div>
            `,
                    confirmButtonColor: '#fea219',
                });
            }
        }

        // Fonction pour générer la visualisation des places avec les places réservées
        function generatePlacesVisualization(vehicle, reservedSeats = []) {
            const config = typeRangeConfig[vehicle.type_range];
            if (!config) {
                return '<p style="color: #ef4444;">Configuration non reconnue</p>';
            }

            const placesGauche = config.placesGauche;
            const placesDroite = config.placesDroite;
            const placesParRanger = placesGauche + placesDroite;
            const totalPlaces = parseInt(vehicle.nombre_place);
            const nombreRanger = Math.ceil(totalPlaces / placesParRanger);

            let html = `
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
            <!-- En-tête -->
            <div style="display: grid; grid-template-columns: 100px 1fr 80px 1fr; gap: 10px; padding: 15px; background: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                <div style="text-align: center; font-weight: 600; color: #4b5563;">Rangée</div>
                <div style="text-align: center; font-weight: 600; color: #4b5563;">Côté gauche</div>
                <div style="text-align: center; font-weight: 600; color: #4b5563;">Allée</div>
                <div style="text-align: center; font-weight: 600; color: #4b5563;">Côté droit</div>
            </div>
            
            <!-- Rangées -->
            <div style="max-height: 400px; overflow-y: auto;">
    `;

            let numeroPlace = 1;

            for (let ranger = 1; ranger <= nombreRanger; ranger++) {
                const placesRestantes = totalPlaces - (numeroPlace - 1);
                const placesCetteRanger = Math.min(placesParRanger, placesRestantes);
                const placesGaucheCetteRanger = Math.min(placesGauche, placesCetteRanger);
                const placesDroiteCetteRanger = Math.min(placesDroite, placesCetteRanger - placesGaucheCetteRanger);

                html += `
            <div style="display: grid; grid-template-columns: 100px 1fr 80px 1fr; gap: 10px; padding: 20px; align-items: center; ${ranger < nombreRanger ? 'border-bottom: 1px solid #e5e7eb;' : ''}">
                <!-- Numéro de rangée -->
                <div style="text-align: center; font-weight: 600; color: #6b7280;">Rangée ${ranger}</div>
                
                <!-- Places côté gauche -->
                <div style="display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
        `;

                // Places côté gauche
                for (let i = 0; i < placesGaucheCetteRanger; i++) {
                    const seatNumber = numeroPlace + i;
                    const isReserved = reservedSeats.includes(seatNumber);

                    const bgColor = isReserved ?
                        'background: linear-gradient(135deg, #ef4444, #dc2626); opacity: 0.7;' :
                        'background: linear-gradient(135deg, #fea219, #e89116);';

                    const cursorStyle = isReserved ? 'cursor: not-allowed;' : 'cursor: help;';
                    const title = isReserved ? `Place ${seatNumber} (Occupée)` : `Place ${seatNumber}`;

                    html += `
                <div style="width: 50px; height: 50px; ${bgColor} ${cursorStyle} border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.1rem; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);" title="${title}">
                    ${seatNumber}
                </div>
            `;
                }
                html += `</div>`;

                numeroPlace += placesGaucheCetteRanger;

                // Allée
                html += `<div style="text-align: center; color: #9ca3af; font-size: 0.8rem; font-style: italic;"></div>`;

                // Places côté droit
                html += `<div style="display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">`;
                for (let i = 0; i < placesDroiteCetteRanger; i++) {
                    const seatNumber = numeroPlace + i;
                    const isReserved = reservedSeats.includes(seatNumber);

                    const bgColor = isReserved ?
                        'background: linear-gradient(135deg, #ef4444, #dc2626); opacity: 0.7;' :
                        'background: linear-gradient(135deg, #fea219, #e89116);';

                    const cursorStyle = isReserved ? 'cursor: not-allowed;' : 'cursor: help;';
                    const title = isReserved ? `Place ${seatNumber} (Occupée)` : `Place ${seatNumber}`;

                    html += `
                <div style="width: 50px; height: 50px; ${bgColor} ${cursorStyle} border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.1rem; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);" title="${title}">
                    ${seatNumber}
                </div>
            `;
                }
                html += `</div>
            </div>`;

                numeroPlace += placesDroiteCetteRanger;
            }

            html += `
            </div>
            <div style="padding: 15px; background: #f9fafb; border-top: 1px solid #e5e7eb; display: flex; gap: 20px; justify-content: center;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 20px; height: 20px; background: linear-gradient(135deg, #fea219, #e89116); border-radius: 4px;"></div>
                    <span style="font-size: 0.9rem; color: #4b5563;">Disponible</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 20px; height: 20px; background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 4px; opacity: 0.7;"></div>
                    <span style="font-size: 0.9rem; color: #4b5563;">Occupé</span>
                </div>
            </div>
        </div>
    `;

            return html;
        }
    </script>
@endsection
