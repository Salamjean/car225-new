@extends('user.layouts.template')
@section('content')
    <div class="min-h-screen bg-gradient-to-br from-white to-blue-50 py-4 sm:py-6 lg:py-8">
        <div class="w-full px-3 sm:px-4 lg:px-6">

            <!-- Formulaire de recherche -->
            <div class="mb-6 sm:mb-8">
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 border border-gray-100">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">Rechercher un voyage</h2>

                  <form action="{{ route('reservation.create') }}" method="GET" id="search-form">
                        <!-- Modification ici : passage à lg:grid-cols-12 pour une ligne parfaite -->
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 sm:gap-4 items-end">
                            
                            <!-- Point de départ (Prend 3 colonnes sur 12) -->
                            <div class="relative lg:col-span-3">
                                <label for="point_depart" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-map-marker-alt text-[#e94f1b] mr-2"></i>Point de départ
                                </label>
                                <div class="relative">
                                    <input type="text" id="point_depart" name="point_depart"
                                        value="{{ $searchParams['point_depart'] ?? '' }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 pl-12"
                                        placeholder="Ville ou gare de départ" required>
                                </div>
                            </div>

                            <!-- Bouton d'inversion (Prend 1 colonne sur 12, centré) -->
                            <div class="lg:col-span-1 flex items-end justify-center pb-2">
                                <button type="button" onclick="swapLocations()" 
                                    class="w-10 h-10 bg-[#e94f1b] text-white rounded-full hover:bg-orange-600 transition-all duration-300 transform hover:scale-110 shadow-lg flex items-center justify-center"
                                    title="Inverser départ/arrivée">
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                            </div>

                            <!-- Point d'arrivée (Prend 3 colonnes sur 12) -->
                            <div class="relative lg:col-span-3">
                                <label for="point_arrive" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-flag text-green-500 mr-2"></i>Point d'arrivée
                                </label>
                                <div class="relative">
                                    <input type="text" id="point_arrive" name="point_arrive"
                                        value="{{ $searchParams['point_arrive'] ?? '' }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 pl-12"
                                        placeholder="Ville ou gare d'arrivée" required>
                                </div>
                            </div>

                            <!-- Date de départ (Prend 2 colonnes sur 12) -->
                            <div class="relative lg:col-span-2">
                                <label for="date_depart" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar text-blue-500 mr-2"></i>Date
                                </label>
                                <div class="relative">
                                    <input type="date" id="date_depart" name="date_depart"
                                        value="{{ $searchParams['date_depart'] ?? date('Y-m-d', strtotime('+1 day')) }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 pl-12"
                                        min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                </div>
                            </div>

                            <!-- Bouton Rechercher (Prend 3 colonnes sur 12) -->
                            <div class="lg:col-span-3">
                                <button type="submit"
                                    class="w-full bg-[#e94f1b] text-white px-4 py-3.5 rounded-lg font-bold hover:bg-orange-600 transition-all duration-300 shadow-lg flex items-center justify-center gap-2">
                                    <i class="fas fa-search"></i>
                                    <span>Rechercher</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>


            </div>
             <!-- Alerte si l'heure recherchée n'existe pas -->
            @if (isset($timeMismatch) && $timeMismatch && isset($availableTimesMessage))
                <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-xl">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mt-1"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-yellow-800 mb-1">Heure non disponible</h4>
                            <p class="text-yellow-700">{{ $availableTimesMessage }}</p>
                            <p class="text-sm text-yellow-600 mt-2">Nous affichons quand même les programmes disponibles pour cette route.</p>
                        </div>
                    </div>
                </div>
            @endif
             <!-- Résultats de recherche - Routes groupées -->
            @if (isset($groupedRoutes) && $groupedRoutes->count() > 0)
                <div class="mb-6 sm:mb-8">
                    <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 border border-gray-100 mb-6">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Voyages disponibles</h2>
                            <span class="bg-[#e94f1b] text-white px-4 py-2 rounded-xl font-bold text-lg">
                                {{ $groupedRoutes->count() }} trajet(s) disponible(s)
                            </span>
                        </div>

                        <!-- Filtres ou Date actuelle -->
                        <div class="mt-4 flex flex-wrap gap-2">
                            @if(isset($searchParams['point_depart']) && $searchParams['point_depart'])
                                <div class="flex items-center gap-2 bg-orange-50 px-3 py-1 rounded-full">
                                    <i class="fas fa-map-marker-alt text-[#e94f1b]"></i>
                                    <span class="font-semibold">{{ $searchParams['point_depart'] }}</span>
                                </div>
                                <i class="fas fa-arrow-right text-[#e94f1b] my-auto"></i>
                                <div class="flex items-center gap-2 bg-green-50 px-3 py-1 rounded-full">
                                    <i class="fas fa-flag text-green-500"></i>
                                    <span class="font-semibold">{{ $searchParams['point_arrive'] }}</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2 bg-purple-50 px-3 py-1 rounded-full">
                                    <i class="fas fa-globe text-purple-600"></i>
                                    <span class="font-semibold text-purple-700">Toutes les destinations</span>
                                </div>
                            @endif
                            
                            <div class="flex items-center gap-2 bg-blue-50 px-3 py-1 rounded-full">
                                <i class="fas fa-calendar text-blue-500"></i>
                                <span class="font-bold text-blue-700">{{ date('d/m/Y', strtotime($searchParams['date_depart'])) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des routes -->
                    <div class="space-y-4">
                        @foreach ($groupedRoutes as $route)
                            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
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
                                                    <i class="fas fa-hourglass-half mr-1"></i>{{ $route->durer_parcours }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Horaires & Occupation (Liste défilante ou grille) -->
                                        <div class="flex-1">
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                                                <i class="fas fa-clock text-[#e94f1b]"></i> Horaires & Disponibilité
                                            </p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($route->aller_horaires as $horaire)
                                                    @php
                                                        $occupancyRate = ($horaire['reserved_count'] / $horaire['total_seats']) * 100;
                                                        $statusClass = $horaire['reserved_count'] >= $horaire['total_seats'] ? 'bg-red-50 border-red-200 text-red-700' : 
                                                                      ($occupancyRate > 80 ? 'bg-orange-50 border-orange-200 text-orange-700' : 'bg-green-50 border-green-200 text-green-700');
                                                    @endphp
                                                    <div onclick="showVehicleDetails('{{ $horaire['vehicule_id'] }}', '{{ $horaire['id'] }}', '{{ $searchParams['date_depart'] }}')" 
                                                         class="flex items-center gap-2 px-3 py-1.5 rounded-xl border {{ $statusClass }} transition-all hover:scale-110 active:scale-95 shadow-sm cursor-pointer group hover:shadow-md" 
                                                         title="Cliquez pour voir les places disponibles">
                                                         <span class="font-black text-sm">{{ substr($horaire['heure_depart'], 0, 5) }}</span>
                                                         <div class="w-px h-3 bg-current opacity-20"></div>
                                                         <div class="flex items-center gap-1">
                                                             <i class="fas fa-couch text-[10px] group-hover:text-[#e94f1b]"></i>
                                                             <span class="text-[10px] font-black">{{ $horaire['reserved_count'] }}/{{ $horaire['total_seats'] }}</span>
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
                                            
                                            @php
                                                $routeData = [
                                                    'id' => $route->id,
                                                    'compagnie_id' => $route->compagnie_id ?? null,
                                                    'compagnie' => $route->compagnie->name ?? 'Compagnie',
                                                    'point_depart' => $route->point_depart,
                                                    'point_arrive' => $route->point_arrive,
                                                    'montant_billet' => $route->montant_billet,
                                                    'durer_parcours' => $route->durer_parcours,
                                                    'aller_horaires' => $route->aller_horaires,
                                                    'retour_horaires' => $route->retour_horaires,
                                                    'has_retour' => $route->has_retour,
                                                    'date_fin' => $route->date_fin ?? null,
                                                ];
                                            @endphp
                                            <button type="button" 
                                                data-route="{{ json_encode($routeData, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP) }}"
                                                data-date="{{ $searchParams['date_depart'] }}"
                                                onclick="handleReservationClick(this)"
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
                </div>
            @elseif(isset($groupedRoutes))
                <!-- Aucun résultat -->
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-orange-100 to-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-route text-3xl text-[#e94f1b]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun programme trouvé</h3>
                    <p class="text-gray-600 mb-6">Essayez d'ajuster vos critères de recherche.</p>
                </div>
            @endif
        </div>
    </div>
    <!-- Modal pour la réservation -->
    <div id="reservationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto">
        <div class="min-h-screen flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl overflow-hidden" style="max-height: 95vh;">
                <!-- En-tête -->
                <div class="bg-gradient-to-r from-[#e94f1b] to-orange-500 p-6 text-white">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold">Réservation de places</h2>
                        <button onclick="closeReservationModal()" class="text-white hover:text-gray-200 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div id="reservationProgramInfo" class="mt-2 text-lg"></div>
                </div>

                <!-- Contenu -->
                <div class="p-6" style="max-height: calc(95vh - 120px); overflow-y: auto;">
                    <!-- Étape 1: Nombre de places -->
                    <div id="step1" class="mb-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Combien de places souhaitez-vous réserver ?
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            @for ($i = 1; $i <= 8; $i++)
                                <button onclick="selectNumberOfPlaces({{ $i }}, this)"
                                    class="place-count-btn p-4 border-2 border-gray-200 rounded-xl hover:border-[#e94f1b] hover:bg-orange-50 transition-all duration-300 text-center">
                                    <div class="text-2xl font-bold text-gray-800">{{ $i }}</div>
                                    <div class="text-sm text-gray-600">place{{ $i > 1 ? 's' : '' }}</div>
                                </button>
                            @endfor
                        </div>

                        <!-- Bouton suivant -->
                        <div class="flex justify-end">
                            <button id="nextStepBtn" onclick="showSeatSelection()"
                                class="bg-[#e94f1b] text-white px-6 py-3 rounded-lg font-bold hover:bg-orange-600 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                disabled>
                                <span>Suivant</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Étape 2: Sélection des places -->
                    <div id="step2" class="hidden">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900">Sélectionnez vos places</h3>
                            <div class="flex items-center gap-4">
                                <span id="selectedSeatsCount" class="text-lg font-bold text-[#e94f1b]">0 place
                                    sélectionnée</span>
                                <button onclick="backToStep1()"
                                    class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
                                    <i class="fas fa-arrow-left"></i>
                                    <span>Retour</span>
                                </button>
                            </div>
                        </div>

                        <!-- Visualisation des places -->
                        <div id="seatSelectionArea" class="mb-8">
                            <!-- Les places seront générées dynamiquement -->
                        </div>

                        <!-- Légende -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-green-500 rounded"></div>
                                <span class="text-sm">Place disponible</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-[#e94f1b] rounded"></div>
                                <span class="text-sm">Place sélectionnée</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-red-500 rounded"></div>
                                <span class="text-sm">Place réservée</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-blue-500 rounded"></div>
                                <span class="text-sm">Place côté gauche</span>
                            </div>
                        </div>

                        <!-- Bouton de confirmation -->
                        <div class="flex justify-between">
                            <button onclick="backToStep1()"
                                class="bg-gray-200 text-gray-800 px-6 py-3 rounded-lg font-bold hover:bg-gray-300 transition-all duration-300 flex items-center gap-2">
                                <i class="fas fa-arrow-left"></i>
                                <span>Retour</span>
                            </button>
                            <button id="showPassengerInfoBtn" onclick="showPassengerInfo()"
                                class="bg-[#e94f1b] text-white px-6 py-3 rounded-lg font-bold hover:bg-orange-600 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                disabled>
                                <span>Informations passagers</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
<!-- Étape 2.5: Sélection des places RETOUR (si Aller-Retour) -->
<div id="step2_5" class="hidden">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-gray-900">Sélectionnez vos places RETOUR</h3>
        <div class="flex items-center gap-4">
            <span id="selectedSeatsCountRetour" class="text-lg font-bold text-blue-600">0 place sélectionnée</span>
            <button onclick="backToStep2()"
                class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                <span>Retour</span>
            </button>
        </div>
    </div>

    <!-- Info programme retour -->
    <div class="bg-blue-50 p-4 rounded-lg mb-6 border border-blue-200">
        <div class="flex items-center gap-3">
            <i class="fas fa-undo text-blue-600 text-2xl"></i>
            <div>
                <p class="font-bold text-blue-900">Voyage Retour</p>
                <p id="returnProgramInfo" class="text-sm text-blue-700"></p>
            </div>
        </div>
    </div>

    <!-- Visualisation des places RETOUR -->
    <div id="seatSelectionAreaRetour" class="mb-8">
        <!-- Les places seront générées dynamiquement -->
    </div>

    <!-- Légende -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-green-500 rounded"></div>
            <span class="text-sm">Place disponible</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-600 rounded"></div>
            <span class="text-sm">Place sélectionnée</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-red-500 rounded"></div>
            <span class="text-sm">Place réservée</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-500 rounded"></div>
            <span class="text-sm">Place côté gauche</span>
        </div>
    </div>

    <!-- Boutons de navigation -->
    <div class="flex justify-between">
        <button onclick="backToStep2()"
            class="bg-gray-200 text-gray-800 px-6 py-3 rounded-lg font-bold hover:bg-gray-300 transition-all duration-300 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Retour</span>
        </button>
        <button id="showPassengerInfoBtnRetour" onclick="proceedToPassengerInfoFromRetour()"
            class="bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            disabled>
            <span>Informations passagers</span>
            <i class="fas fa-arrow-right"></i>
        </button>
    </div>
</div>
                    <!-- Étape 3: Informations des passagers -->
                    <div id="step3" class="hidden">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900">Informations des passagers</h3>
                            <button onclick="backFromPassengerInfo()"
                                class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
                                <i class="fas fa-arrow-left"></i>
                                <span>Retour aux places</span>
                            </button>
                        </div>

                        <div id="passengersFormArea" class="space-y-6 mb-8">
                            <!-- Les formulaires passagers seront générés dynamiquement -->
                        </div>

                        <!-- Bouton de confirmation finale -->
                        <div class="flex justify-between">
                            <button onclick="backFromPassengerInfo()"
                                class="bg-gray-200 text-gray-800 px-6 py-3 rounded-lg font-bold hover:bg-gray-300 transition-all duration-300 flex items-center gap-2">
                                <i class="fas fa-arrow-left"></i>
                                <span>Retour</span>
                            </button>
                            <button id="confirmReservationBtn" onclick="confirmReservation()"
                                class="bg-green-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-green-600 transition-all duration-300 flex items-center gap-2">
                                <i class="fas fa-check-circle"></i>
                                <span>Confirmer la réservation</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .place-count-btn.active {
            border-color: #e94f1b;
            background-color: #fff7ed;
            box-shadow: 0 0 0 3px rgba(254, 162, 25, 0.2);
        }

        .seat {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .seat:hover {
            transform: scale(1.1);
        }

        .seat.selected {
            transform: scale(1.1);
            box-shadow: 0 0 15px rgba(254, 162, 25, 0.5);
        }

        .seat.reserved {
            cursor: not-allowed;
            opacity: 0.5;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.cinetpay.com/seamless/main.js"></script>
     <script>
        function initAutocompleteUser() {
            const options = {
                componentRestrictions: { country: "ci" }, // Restreindre à la Côte d'Ivoire
                fields: ["formatted_address", "geometry", "name"],
            };

            const inputDepart = document.getElementById("point_depart");
            const inputArrive = document.getElementById("point_arrive");

            if (inputDepart) {
                new google.maps.places.Autocomplete(inputDepart, options);
            }

            if (inputArrive) {
                new google.maps.places.Autocomplete(inputArrive, options);
            }
        }
    </script>
       <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&loading=async&callback=initAutocompleteUser"
        async defer></script>
    <script>
        // Variables globales
         var currentProgramId = null;
        var currentSelectedProgram = null; 
        var selectedNumberOfPlaces = 0;
        var selectedSeats = [];
        var reservedSeats = [];
        var vehicleDetails = null;
        var currentRequestId = 0;
        var selectedSeatsRetour = [];
var reservedSeatsRetour = [];
var vehicleDetailsRetour = null;
var currentRetourProgramId = null;
        var userWantsAllerRetour = false;
        var selectedReturnDate = null; // Date de retour sélectionnée pour Aller-Retour


        window.currentUser = @json(Auth::user()); // Injecter l'utilisateur connecté
     // Définition explicite sur window pour s'assurer que le HTML peut voir la fonction
        window.handleReservationClick = function(button) {
            console.log("Bouton réserver cliqué"); // Debug
            try {
                const routeDataJson = button.getAttribute('data-route');
                const dateDepartInitial = button.getAttribute('data-date');
                
                if (!routeDataJson) {
                    console.error("Pas de données data-route trouvées");
                    return;
                }

                const routeData = JSON.parse(routeDataJson);
                console.log('Données route:', routeData);
                
                // Toujours demander le type de voyage en premier
                showRouteTripTypeModal(routeData, dateDepartInitial);
            } catch (e) {
                console.error('Erreur JS lors du clic:', e);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Une erreur technique est survenue. Consultez la console.'
                });
            }
        };

        // Fonction pour inverser le point de départ et d'arrivée
        window.swapLocations = function() {
            const departInput = document.getElementById('point_depart');
            const arriveeInput = document.getElementById('point_arrive');
            
            if (departInput && arriveeInput) {
                const temp = departInput.value;
                departInput.value = arriveeInput.value;
                arriveeInput.value = temp;
                
                // Animation visuelle
                departInput.classList.add('ring-2', 'ring-green-400');
                arriveeInput.classList.add('ring-2', 'ring-green-400');
                setTimeout(() => {
                    departInput.classList.remove('ring-2', 'ring-green-400');
                    arriveeInput.classList.remove('ring-2', 'ring-green-400');
                }, 300);
            }
        };

        // Fonction handler pour le clic sur Réserver (gère le parsing JSON depuis data attributes)
       

        // Configuration des types de rangées
        const typeRangeConfig = {
            '2x2': { placesGauche: 2, placesDroite: 2 },
            '2x3': { placesGauche: 2, placesDroite: 3 },
            '2x4': { placesGauche: 2, placesDroite: 4 },
            'Gamme Prestige': { placesGauche: 2, placesDroite: 2 },
            'Gamme Standard': { placesGauche: 2, placesDroite: 3 }
        };
 // --- NOUVELLE FONCTION: Modal de sélection des horaires pour les routes groupées ---
        window.showRouteSchedulesModal = function(routeData, dateDepart) {
            console.log('Ouverture modal sélection horaires:', routeData);
            
            // Stocker les données courantes
            window.currentRouteData = routeData;
            window.currentDateDepart = dateDepart;

            // Toujours demander le type de voyage d'abord
            showRouteTripTypeModal(routeData, dateDepart);
        };

        // ÉTAPE 1: Choix Type de Voyage (Aller Simple / Aller-Retour)
        function showRouteTripTypeModal(routeData, dateDepart) {
    // Conversion sécurisée du prix en nombre
    // On convertit d'abord en string, on enlève tout sauf chiffres et points, puis on parse
    let priceString = String(routeData.montant_billet || '0');
    let priceRaw = priceString.replace(/[^\d.]/g, '');
    const priceSimple = parseFloat(priceRaw) || 0;
    const priceReturn = priceSimple * 2;

    Swal.fire({
        title: '<i class="fas fa-bus text-[#e94f1b]"></i> Type de voyage',
        html: `
            <div class="text-left space-y-4">
                <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                    <p class="font-bold text-gray-800">${routeData.point_depart} → ${routeData.point_arrive}</p>
                    <p class="text-sm font-bold text-gray-800 mt-1">${routeData.compagnie}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="border-2 border-gray-200 rounded-lg p-4 text-center hover:border-[#e94f1b] hover:bg-orange-50 transition-all cursor-pointer" id="btnRouteSimple">
                        <i class="fas fa-arrow-right text-3xl text-gray-500 mb-2"></i>
                        <p class="font-bold">Aller Simple</p>
                        <p class="text-lg font-bold text-[#e94f1b]">${priceSimple.toLocaleString('fr-FR')} FCFA</p>
                    </div>
                    <div class="border-2 ${routeData.has_retour ? 'border-[#e94f1b] bg-orange-50' : 'border-gray-100 bg-gray-50 opacity-60 cursor-not-allowed'} rounded-lg p-4 text-center transition-all" id="btnRouteReturn">
                        <i class="fas fa-exchange-alt text-3xl ${routeData.has_retour ? 'text-[#e94f1b]' : 'text-gray-300'} mb-2"></i>
                        <p class="font-bold">Aller-Retour</p>
                        <p class="text-lg font-bold ${routeData.has_retour ? 'text-[#e94f1b]' : 'text-gray-400'}">${priceReturn.toLocaleString('fr-FR')} FCFA</p>
                        ${!routeData.has_retour ? '<p class="text-[10px] text-red-500 font-bold">Non disponible</p>' : '<p class="text-xs text-gray-500">Prix estimé</p>'}
                    </div>
                </div>
            </div>
        `,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Annuler',
                customClass: { popup: 'rounded-2xl' },
                didOpen: () => {
                    document.getElementById('btnRouteSimple').addEventListener('click', () => {
                        window.userWantsAllerRetour = false;
                        window.userChoseAllerRetour = false;
                        Swal.close();
                        // Après le type, on demande la date (Aller Simple)
                        showDepartureDateSelection(routeData); 
                    });
                    
                    if (routeData.has_retour) {
                        document.getElementById('btnRouteReturn').addEventListener('click', () => {
                            window.userWantsAllerRetour = true;
                            window.userChoseAllerRetour = true;
                            Swal.close();
                            // Après le type, on demande la date (Aller-Retour)
                            showDepartureDateSelection(routeData);
                        });
                        document.getElementById('btnRouteReturn').classList.add('cursor-pointer', 'hover:bg-orange-100');
                    }
                }
            });
        }

        // ÉTAPE 2: Choix de l'heure de départ
    function showRouteDepartureTimes(routeData, dateDepart, isAllerRetour) {
    const dateFormatted = new Date(dateDepart).toLocaleDateString('fr-FR', { 
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
    });

    // MODIFICATION MAJEURE : On ne filtre plus rien. On prend tout ce que la BDD donne.
    const validSchedules = routeData.aller_horaires || [];

    // Construire la grille
    let timeSlotsHtml = '';
    if (validSchedules.length > 0) {
        timeSlotsHtml = '<div class="grid grid-cols-2 gap-3 max-h-60 overflow-y-auto p-2">';
        validSchedules.forEach(h => {
            timeSlotsHtml += `
                <div class="route-time-btn border-2 border-green-200 bg-green-50 rounded-lg p-4 cursor-pointer hover:border-green-500 hover:bg-green-100 transition-all text-center"
                     data-id="${h.id}" data-time="${h.heure_depart}">
                    <p class="font-bold text-xl text-green-700">${h.heure_depart}</p>
                    <p class="text-sm text-gray-500">→ ${h.heure_arrive}</p>
                </div>
            `;
        });
        timeSlotsHtml += '</div>';
    } else {
        timeSlotsHtml = '<p class="text-center text-red-500 font-medium py-4">Aucun horaire de départ programmé pour cette date.</p>';
    }

    Swal.fire({
        title: '<i class="fas fa-clock text-green-600"></i> Heure de départ',
        html: `
            <div class="text-left space-y-4">
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                     <p class="font-bold text-gray-800">${routeData.point_depart} → ${routeData.point_arrive}</p>
                     <p class="text-sm text-gray-600">${dateFormatted}</p>
                     <p class="text-xs text-${isAllerRetour ? 'orange' : 'green'}-600 font-semibold mt-1">
                        <i class="fas fa-${isAllerRetour ? 'exchange-alt' : 'arrow-right'} mr-1"></i>
                        ${isAllerRetour ? 'Aller-Retour' : 'Aller Simple'}
                     </p>
                </div>
                <p class="font-medium text-gray-700">→ Choisissez l'heure de départ :</p>
                ${timeSlotsHtml}
            </div>
        `,
        showCancelButton: true,
        showConfirmButton: false,
        cancelButtonText: 'Retour',
        customClass: { popup: 'rounded-2xl' },
        didOpen: () => {
            document.querySelectorAll('.route-time-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const progId = this.dataset.id;
                    const time = this.dataset.time;
                    
                    window.selectedDepartureTime = time;
                    window.selectedAllerProgramId = progId;
                    
                    Swal.close();
                    
                    if (isAllerRetour) {
                        showReturnDateSelection(routeData, dateDepart);
                    } else {
                        startReservationFromRoute(progId, dateDepart, false);
                    }
                });
            });
        }
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.cancel && routeData.has_retour) {
            showRouteTripTypeModal(routeData, dateDepart);
        }
    });
}
        // ÉTAPE 2.5: Sélection de la date de retour (pour Aller-Retour) avec calendrier mensuel
        function showReturnDateSelection(routeData, dateDep) {
            const minDate = new Date(dateDep); // Date de retour minimum = date de départ
            minDate.setHours(0, 0, 0, 0);
            
            // Date max = date_fin du programme
            const maxDate = routeData.date_fin ? new Date(routeData.date_fin) : new Date(minDate.getFullYear(), 11, 31);
            
            let currentMonth = minDate.getMonth();
            let currentYear = minDate.getFullYear();
            
            function updateCalendar() {
                const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                                   'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                
                const calendarHtml = generateMonthlyCalendar(currentMonth, currentYear, minDate, maxDate, 'purple');
                
                Swal.update({
                    html: `
                        <div class="text-left space-y-4">
                            <div class="bg-purple-100 p-3 rounded-lg border border-purple-200">
                                <p class="font-bold text-purple-900">Retour : ${routeData.point_arrive} → ${routeData.point_depart}</p>
                                <p class="text-sm text-gray-700">Sélectionnez la date de votre retour</p>
                            </div>
                            <div class="bg-green-50 p-2 rounded border border-green-200 text-sm flex justify-between items-center">
                                <div><span class="font-bold text-green-700">Départ :</span> ${new Date(dateDep).toLocaleDateString('fr-FR')}</div>
                                <div class="text-xs bg-green-100 px-2 py-1 rounded font-bold">${window.selectedDepartureTime}</div>
                            </div>

                            <!-- Options rapides Retour -->
                            <div class="flex justify-center gap-4">
                                <button id="btnReturnSameDay" class="flex-1 bg-purple-50 hover:bg-purple-100 text-purple-700 px-3 py-2 rounded-lg font-bold border-2 border-purple-200 transition-all text-sm">
                                    Même jour
                                </button>
                                <button id="btnReturnNextDay" class="flex-1 bg-purple-50 hover:bg-purple-100 text-purple-700 px-3 py-2 rounded-lg font-bold border-2 border-purple-200 transition-all text-sm">
                                    Lendemain
                                </button>
                            </div>
                            
                            <!-- Navigation mois -->
                            <div class="flex items-center justify-between bg-gray-50 p-2 rounded">
                                <button id="prevMonthReturn" class="px-3 py-1 bg-white border rounded hover:bg-gray-100">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <span class="font-bold text-gray-800">${monthNames[currentMonth]} ${currentYear}</span>
                                <button id="nextMonthReturn" class="px-3 py-1 bg-white border rounded hover:bg-gray-100">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            
                            <!-- Calendrier -->
                            <div class="calendar-container">
                                ${calendarHtml}
                            </div>
                        </div>
                    `
                });
                
                // Réattacher les événements
                attachCalendarEvents();
            }
            
            function attachCalendarEvents() {
                // Bouton Même Jour
                const btnSame = document.getElementById('btnReturnSameDay');
                if (btnSame) {
                    btnSame.addEventListener('click', () => {
                        const sameDayStr = new Date(dateDep).toISOString().split('T')[0];
                        window.selectedReturnDate = sameDayStr;
                        Swal.close();
                        loadReturnSchedulesForDate(routeData, sameDayStr);
                    });
                }

                // Bouton Lendemain
                const btnNext = document.getElementById('btnReturnNextDay');
                if (btnNext) {
                    btnNext.addEventListener('click', () => {
                        const nextDay = new Date(dateDep);
                        nextDay.setDate(nextDay.getDate() + 1);
                        const nextDayStr = nextDay.toISOString().split('T')[0];
                        window.selectedReturnDate = nextDayStr;
                        Swal.close();
                        loadReturnSchedulesForDate(routeData, nextDayStr);
                    });
                }
                // Navigation mois précédent
                const prevBtn = document.getElementById('prevMonthReturn');
                if (prevBtn) {
                    prevBtn.addEventListener('click', () => {
                        if (currentMonth === 0) {
                            currentMonth = 11;
                            currentYear--;
                        } else {
                            currentMonth--;
                        }
                        // Ne pas aller avant le mois de minDate
                        const checkDate = new Date(currentYear, currentMonth, 1);
                        if (checkDate >= new Date(minDate.getFullYear(), minDate.getMonth(), 1)) {
                            updateCalendar();
                        } else {
                            currentMonth = minDate.getMonth();
                            currentYear = minDate.getFullYear();
                        }
                    });
                }
                
                // Navigation mois suivant
                const nextBtn = document.getElementById('nextMonthReturn');
                if (nextBtn) {
                    nextBtn.addEventListener('click', () => {
                        if (currentMonth === 11) {
                            currentMonth = 0;
                            currentYear++;
                        } else {
                            currentMonth++;
                        }
                        // Ne pas aller après le mois de maxDate
                        const checkDate = new Date(currentYear, currentMonth, 1);
                        if (checkDate <= new Date(maxDate.getFullYear(), maxDate.getMonth(), 1)) {
                            updateCalendar();
                        } else {
                            currentMonth = maxDate.getMonth();
                            currentYear = maxDate.getFullYear();
                        }
                    });
                }
                
                // Sélection de date
                const dayBtns = document.querySelectorAll('.calendar-day-btn');
                dayBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const selectedDate = this.dataset.date;
                        window.selectedReturnDate = selectedDate;
                        Swal.close();
                        loadReturnSchedulesForDate(routeData, selectedDate);
                    });
                });
            }
            
            // Ouvrir le modal initial
            Swal.fire({
                title: '<i class="fas fa-calendar-alt text-purple-600"></i> Date de retour',
                html: '', // Sera rempli par updateCalendar()
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonText: 'Retour',
                customClass: { popup: 'rounded-2xl' },
                width: '600px',
                didOpen: () => {
                    updateCalendar();
                }
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    showRouteDepartureTimes(routeData, dateDep, true); // Retour au choix de l'heure de départ
                }
            });
        }

        // Charger les horaires de retour pour une date spécifique
        async function loadReturnSchedulesForDate(routeData, returnDate) {
            Swal.fire({
                title: 'Chargement des horaires...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const paramsRetour = new URLSearchParams({
                    original_arrive: routeData.point_arrive,
                    original_depart: routeData.point_depart,
                    min_date: returnDate
                });
                const response = await fetch('{{ route("api.return-trips") }}?' + paramsRetour);
                const data = await response.json();

                Swal.close();

                if (data.success && data.return_trips && data.return_trips.length > 0) {
                    // Mettre à jour routeData avec les nouveaux horaires de retour
                    const updatedRouteData = {
                        ...routeData,
                        retour_horaires: data.return_trips
                    };
                    showRouteReturnTimes(updatedRouteData, returnDate);
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Aucun horaire disponible',
                        text: 'Aucun horaire de retour n\'est disponible pour cette date. Veuillez choisir une autre date.',
                        confirmButtonText: 'Choisir une autre date'
                    }).then(() => {
                        showReturnDateSelection(routeData, window.currentDateDepart);
                    });
                }
            } catch (error) {
                console.error('Erreur:', error);
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Impossible de charger les horaires de retour.'
                });
            }
        }

        // ÉTAPE 3: Choix de l'heure de retour (si Aller-Retour)
  function showRouteReturnTimes(routeData, dateDepart) {
    // MODIFICATION MAJEURE : On ne filtre plus rien.
    const validReturnSchedules = routeData.retour_horaires || [];

    // Construire la grille retour
    let timeSlotsHtml = '';
    if (validReturnSchedules.length > 0) {
        timeSlotsHtml = '<div class="grid grid-cols-2 gap-3 max-h-60 overflow-y-auto p-2">';
        validReturnSchedules.forEach(h => {
            timeSlotsHtml += `
                <div class="route-return-btn border-2 border-blue-200 bg-blue-50 rounded-lg p-4 cursor-pointer hover:border-blue-500 hover:bg-blue-100 transition-all text-center"
                     data-id="${h.id}" data-time="${h.heure_depart}">
                    <p class="font-bold text-xl text-blue-700">${h.heure_depart}</p>
                    <p class="text-sm text-gray-500">→ ${h.heure_arrive}</p>
                </div>
            `;
        });
        timeSlotsHtml += '</div>';
    } else {
        timeSlotsHtml = '<div class="text-center text-orange-500 mb-4"><p>Aucun horaire retour disponible pour cette date.</p></div>';
    }

    Swal.fire({
        title: '<i class="fas fa-undo text-blue-600"></i> Heure de retour',
        html: `
            <div class="text-left space-y-4">
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                     <p class="font-bold text-gray-800">Retour : ${routeData.point_arrive} → ${routeData.point_depart}</p>
                     <p class="text-sm text-gray-600">Date : ${new Date(dateDepart).toLocaleDateString('fr-FR')}</p>
                </div>
                 <div class="bg-green-50 p-2 rounded border border-green-200 text-sm mb-2">
                    <span class="font-bold text-green-700">Départ choisi :</span> ${window.selectedDepartureTime}
                </div>
                <p class="font-medium text-gray-700">→ Choisissez l'heure de retour :</p>
                ${timeSlotsHtml}
            </div>
        `,
        showCancelButton: true,
        showConfirmButton: false,
        cancelButtonText: 'Retour',
        customClass: { popup: 'rounded-2xl' },
        didOpen: () => {
            document.querySelectorAll('.route-return-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const progId = this.dataset.id;
                    const time = this.dataset.time;
                    
                    window.selectedReturnTime = time;
                    window.selectedRetourProgramId = progId;
                    
                    // Trouver les détails du programme retour dans routeData si nécessaire
                    const returnProg = routeData.retour_horaires.find(p => p.id == progId);
                    window.selectedReturnProgram = returnProg; 

                    Swal.close();
                    startReservationFromRoute(window.selectedAllerProgramId, dateDepart, true);
                });
            });
        }
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.cancel) {
            showRouteDepartureTimes(routeData, dateDepart, true);
        }
    });
}

        // Helper pour lancer la réservation finale
        function startReservationFromRoute(programId, dateVoyage, isAllerRetour) {
            window.userWantsAllerRetour = isAllerRetour;
            window.userChoseAllerRetour = isAllerRetour;
            
            // CORRECTION: Si on vient du flux "Grouped Routes", on s'assure d'utiliser la date de départ initiale
            if (window.currentDateDepart && isAllerRetour) {
                 console.log('DEBUG: Using Date Depart from global scope:', window.currentDateDepart);
                 dateVoyage = window.currentDateDepart;
            }

            // Ouvrir directement le modal de sélection des places (Step 1)
            openReservationModal(programId, dateVoyage);
        }
        

        
 // --- NOUVELLE FONCTION PRINCIPALE D'INITIATION ---
        // C'est elle qui est appelée par le bouton "Réserver"
     async function initiateReservationProcess(programId, searchDateFormatted, searchedTime = null) {
        console.log("Initiation réservation pour ID:", programId, "Date:", searchDateFormatted, "Heure cherchée:", searchedTime);
        
        // 1. Réinitialisation des variables globales
        userWantsAllerRetour = false;
        window.userChoseAllerRetour = false;
        window.selectedReturnProgram = null;
        window.outboundProgram = null;
        window.outboundDate = searchDateFormatted;
        window.selectedReturnDate = null; 
        window.selectedDepartureTime = null;
        window.selectedReturnTime = null;
        currentSelectedProgram = null;
        selectedReturnDate = null; 
        
        Swal.fire({
            title: 'Chargement...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const response = await fetch(`/user/booking/program/${programId}`);
            const data = await response.json();
            
            if (!data.success) throw new Error("Impossible de charger les détails du programme");
            
            const program = data.programme;
            window.outboundProgram = program;
            currentSelectedProgram = program;
            
            // Vérifier la disponibilité du retour via l'API
            const paramsRetour = new URLSearchParams({
                original_arrive: program.point_arrive,
                original_depart: program.point_depart,
                min_date: searchDateFormatted
            });
            const responseRetour = await fetch('{{ route("api.return-trips") }}?' + paramsRetour);
            const dataRetour = await responseRetour.json();
            
            // Construire un objet routeData compatible
            const routeData = {
                id: program.id,
                compagnie: program.compagnie?.name || 'Compagnie',
                compagnie_id: program.compagnie_id,
                point_depart: program.point_depart,
                point_arrive: program.point_arrive,
                montant_billet: program.montant_billet,
                durer_parcours: program.durer_parcours,
                has_retour: (dataRetour.success && dataRetour.return_trips && dataRetour.return_trips.length > 0),
                aller_horaires: [{
                    id: program.id,
                    heure_depart: program.heure_depart,
                    heure_arrive: program.heure_arrive,
                    montant_billet: program.montant_billet
                }],
                retour_horaires: dataRetour.success ? dataRetour.return_trips : [],
                date_fin: program.date_fin_programmation || program.date_fin || null
            };

            Swal.close();

            // Lancer le flux unifié
            showRouteTripTypeModal(routeData, searchDateFormatted);

        } catch (error) {
            console.error(error);
            Swal.close();
            Swal.fire({ icon: 'error', title: 'Erreur', text: 'Une erreur est survenue.' });
        }
    }

// Redundant function removed as it is now unified in showRouteTripTypeModal

// === NOUVEAU: Popup sélection heure de départ (depuis BDD) ===
async function showDepartureSchedulesModal(program, departureDate, isAllerRetour) {
    const dateFormatted = new Date(departureDate).toLocaleDateString('fr-FR', { 
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
    });
    
    Swal.fire({
        title: 'Chargement des horaires...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    try {
        // Charger les horaires de départ depuis la BDD
        const params = new URLSearchParams({
            point_depart: program.point_depart,
            point_arrive: program.point_arrive,
            date: departureDate
        });
        const response = await fetch('{{ route("api.route-schedules") }}?' + params);
        const data = await response.json();
        
        Swal.close();
        
        if (!data.success || data.schedules.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Aucun horaire disponible',
                text: 'Aucun horaire n\'est disponible pour cette date.',
                confirmButtonColor: '#e94f1b'
            });
            return;
        }
        
        // Construire la grille des horaires
        let timeSlotsHtml = '<div class="grid grid-cols-2 gap-3 max-h-60 overflow-y-auto p-2">';
        data.schedules.forEach(sched => {
            timeSlotsHtml += `
                <div class="departure-schedule-btn border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-[#e94f1b] hover:bg-orange-50 transition-all text-center" 
                     data-schedule-id="${sched.id}" data-time="${sched.heure_depart}" data-arrival="${sched.heure_arrive}">
                    <p class="font-bold text-xl text-[#e94f1b]">${sched.heure_depart}</p>
                    <p class="text-sm text-gray-500">→ ${sched.heure_arrive}</p>
                </div>
            `;
        });
        timeSlotsHtml += '</div>';
        
        Swal.fire({
            title: '<i class="fas fa-clock text-[#e94f1b]"></i> Heure de départ',
            html: `
                <div class="text-left space-y-4">
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <p class="font-bold text-gray-800">${program.point_depart} → ${program.point_arrive}</p>
                        <p class="text-sm text-gray-600"><i class="fas fa-calendar mr-2"></i>${dateFormatted}</p>
                        <p class="text-xs text-${isAllerRetour ? 'orange' : 'green'}-600 font-semibold mt-1">
                            <i class="fas fa-${isAllerRetour ? 'exchange-alt' : 'arrow-right'} mr-1"></i>
                            ${isAllerRetour ? 'Aller-Retour' : 'Aller Simple'}
                        </p>
                    </div>
                    <p class="font-medium text-gray-700">→ Choisissez l'heure de départ :</p>
                    ${timeSlotsHtml}
                </div>
            `,
            showCancelButton: true,
            showConfirmButton: false,
            cancelButtonText: 'Annuler',
            customClass: { popup: 'rounded-2xl' },
            didOpen: () => {
                document.querySelectorAll('.departure-schedule-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        window.selectedDepartureTime = this.dataset.time;
                        window.selectedDepartureProgramId = this.dataset.scheduleId;
                        currentProgramId = parseInt(this.dataset.scheduleId);
                        Swal.close();
                        
                        if (isAllerRetour) {
                            // Aller-Retour: maintenant on demande l'heure de retour
                            showReturnTripSelector(program, departureDate);
                        } else {
                            // Aller Simple: on passe directement à la sélection des places
                            openReservationModal(currentProgramId, departureDate);
                        }
                    });
                });
            }
        });
        
    } catch (error) {
        console.error('Erreur chargement horaires:', error);
        Swal.close();
        Swal.fire({ icon: 'error', title: 'Erreur', text: 'Impossible de charger les horaires.' });
    }
}
        // Génère les créneaux horaires disponibles (toutes les 30 min)
        function generateTimeSlots(selectedDate, tripDurationMinutes = 90) {
            const slots = [];
            const now = new Date();
            const isToday = selectedDate === now.toISOString().split('T')[0];
            
            // Minimum 4h à l'avance
            const minBookingHours = 4;
            let startHour = 6; // Service commence à 6h
            let startMinute = 0;
            
            if (isToday) {
                const minTime = new Date(now.getTime() + (minBookingHours * 60 * 60 * 1000));
                startHour = Math.max(startHour, minTime.getHours());
                if (minTime.getMinutes() > 0) {
                    startMinute = minTime.getMinutes() <= 30 ? 30 : 0;
                    if (minTime.getMinutes() > 30) startHour++;
                }
            }
            
            // Générer créneaux de 6h à 22h
            for (let h = startHour; h <= 22; h++) {
                for (let m = (h === startHour ? startMinute : 0); m < 60; m += 30) {
                    if (h === 22 && m > 0) break; // Dernier départ à 22h00
                    
                    const timeStr = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}`;
                    slots.push({
                        time: timeStr,
                        label: timeStr,
                        available: true
                    });
                }
            }
            
            return slots;
        }

        // Modal de sélection d'heure de départ
        function showTimeSelectionModal(program, departureDate) {
            const dateFormatted = new Date(departureDate).toLocaleDateString('fr-FR', { 
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
            });
            
            const timeSlots = generateTimeSlots(departureDate);
            
            if (timeSlots.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Aucun créneau disponible',
                    html: `<p>Pour la date du <strong>${dateFormatted}</strong>, aucun créneau horaire n'est disponible.</p>
                           <p class="text-sm text-gray-500 mt-2">Rappel: La réservation doit être faite au minimum 4 heures à l'avance.</p>`,
                    confirmButtonText: 'Choisir une autre date',
                    confirmButtonColor: '#e94f1b'
                });
                return;
            }
            
            let timeSlotsHtml = '<div class="grid grid-cols-4 gap-2 max-h-60 overflow-y-auto p-2">';
            timeSlots.forEach(slot => {
                timeSlotsHtml += `
                    <button type="button" 
                            class="time-slot-btn p-3 text-center rounded-lg border-2 border-gray-200 hover:border-[#e94f1b] hover:bg-orange-50 transition-all font-semibold"
                            data-time="${slot.time}">
                        ${slot.label}
                    </button>
                `;
            });
            timeSlotsHtml += '</div>';
            
            Swal.fire({
                title: '<i class="fas fa-clock text-[#e94f1b]"></i> Heure de départ',
                html: `
                    <div class="text-left space-y-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="font-bold text-gray-800">${program.point_depart} → ${program.point_arrive}</p>
                            <p class="text-sm text-gray-600"><i class="fas fa-calendar mr-2"></i>${dateFormatted}</p>
                            <p class="text-sm text-gray-500 mt-1"><i class="fas fa-hourglass-half mr-2"></i>Durée: ${program.durer_parcours || '~1h30'}</p>
                        </div>
                        <p class="text-gray-600 font-medium">À quelle heure souhaitez-vous partir ?</p>
                        <p class="text-xs text-gray-400"><i class="fas fa-info-circle mr-1"></i>Réservation minimum 4h à l'avance • Service 6h-22h</p>
                        ${timeSlotsHtml}
                        <div id="selectedTimeDisplay" class="hidden bg-green-50 p-3 rounded-lg text-center">
                            <span class="font-bold text-green-800">Départ sélectionné: <span id="selectedTimeValue"></span></span>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check mr-2"></i>Valider cette heure',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#e94f1b',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-lg px-4 py-2'
                },
                preConfirm: () => {
                    if (!window.selectedDepartureTime) {
                        Swal.showValidationMessage('Veuillez sélectionner une heure de départ');
                        return false;
                    }
                    return window.selectedDepartureTime;
                },
                didOpen: () => {
                    document.querySelectorAll('.time-slot-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            // Désélectionner tous les autres
                            document.querySelectorAll('.time-slot-btn').forEach(b => {
                                b.classList.remove('bg-[#e94f1b]', 'text-white', 'border-[#e94f1b]');
                                b.classList.add('border-gray-200');
                            });
                            // Sélectionner celui-ci
                            this.classList.add('bg-[#e94f1b]', 'text-white', 'border-[#e94f1b]');
                            this.classList.remove('border-gray-200');
                            
                            window.selectedDepartureTime = this.dataset.time;
                            document.getElementById('selectedTimeDisplay').classList.remove('hidden');
                            document.getElementById('selectedTimeValue').textContent = this.dataset.time;
                        });
                    });
                }
            }).then((result) => {
                if (result.isConfirmed && window.selectedDepartureTime) {
                    // Stocker l'heure et passer au choix du type de voyage
                    showReturnTripOptionModal(program, departureDate);
                }
            });
        }

        // Modal FlixBus pour choix Aller Simple ou Aller-Retour
        function showReturnTripOptionModal(program, departureDate) {
            const dateFormatted = new Date(departureDate).toLocaleDateString('fr-FR', { 
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
            });
            
            const priceSimple = Number(program.montant_billet);
            const priceReturn = priceSimple * 2;
            
            Swal.fire({
                title: '<i class="fas fa-bus text-[#e94f1b]"></i> Type de voyage',
                html: `
                    <div class="text-left space-y-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="font-bold text-gray-800">${program.point_depart} → ${program.point_arrive}</p>
                            <p class="text-sm text-gray-600"><i class="fas fa-calendar mr-2"></i>${dateFormatted}</p>
                            <p class="text-sm text-green-600 font-semibold"><i class="fas fa-clock mr-2"></i>Départ à ${window.selectedDepartureTime}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="border-2 border-gray-200 rounded-lg p-4 text-center hover:border-gray-400 transition-all cursor-pointer" id="choiceSimple">
                                <i class="fas fa-arrow-right text-3xl text-gray-500 mb-2"></i>
                                <p class="font-bold">Aller Simple</p>
                                <p class="text-lg font-bold text-[#e94f1b]">${priceSimple.toLocaleString('fr-FR')} FCFA</p>
                            </div>
                            <div class="border-2 border-[#e94f1b] bg-orange-50 rounded-lg p-4 text-center hover:bg-orange-100 transition-all cursor-pointer" id="choiceReturn">
                                <i class="fas fa-exchange-alt text-3xl text-[#e94f1b] mb-2"></i>
                                <p class="font-bold">Aller-Retour</p>
                                <p class="text-lg font-bold text-[#e94f1b]">${priceReturn.toLocaleString('fr-FR')} FCFA</p>
                                <p class="text-xs text-gray-500">Prix estimé</p>
                            </div>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Annuler',
                customClass: {
                    popup: 'rounded-2xl'
                },
                didOpen: () => {
                    document.getElementById('choiceSimple').addEventListener('click', () => {
                        userWantsAllerRetour = false;
                        Swal.close();
                        openReservationModal(program.id, departureDate);
                    });
                    document.getElementById('choiceReturn').addEventListener('click', async () => {
                        userWantsAllerRetour = true;
                        window.userChoseAllerRetour = true;
                        Swal.close();
                        console.log('DEBUG: choiceReturn clicked. Original Departure:', departureDate);
                        
                        // Demander la date de retour
                        const { value: returnDate } = await Swal.fire({
                            title: '<i class="fas fa-calendar-alt text-[#e94f1b]"></i> Date de retour',
                            html: '<p class="text-sm text-gray-600 mb-4">Veuillez choisir la date de votre voyage retour</p>',
                            input: 'date',
                            inputLabel: '',
                            inputValue: departureDate, // Par défaut la date aller
                            inputAttributes: {
                                min: departureDate // Impossible de revenir avant l'aller
                            },
                            confirmButtonColor: '#e94f1b',
                            confirmButtonText: 'Rechercher',
                            showCancelButton: true,
                            cancelButtonText: 'Annuler',
                            customClass: {
                                input: 'text-center text-lg border-2 border-gray-300 rounded-lg focus:border-[#e94f1b] focus:ring-0'
                            }
                        });

                        console.log('DEBUG: User selected return date:', returnDate);

                        if (returnDate) {
                            // Utiliser showReturnTripSelector avec la date choisie
                            showReturnTripSelector(program, departureDate, returnDate);
                        }
                    });
                }
            });
        }

        // Modal de sélection d'heure de retour (après le choix aller-retour)
        function showReturnTimeSelectionModal(program, departureDate) {
            const dateFormatted = new Date(departureDate).toLocaleDateString('fr-FR', { 
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
            });
            
            // Calculer l'heure d'arrivée estimée
            const durationMatch = (program.durer_parcours || '01:30').match(/(\d+):(\d+)/);
            const durationHours = durationMatch ? parseInt(durationMatch[1]) : 1;
            const durationMinutes = durationMatch ? parseInt(durationMatch[2]) : 30;
            const totalDurationMinutes = (durationHours * 60) + durationMinutes;
            
            // Heure d'arrivée = heure de départ + durée
            const [depH, depM] = window.selectedDepartureTime.split(':').map(Number);
            const arrivalDate = new Date(2026, 0, 1, depH, depM);
            arrivalDate.setMinutes(arrivalDate.getMinutes() + totalDurationMinutes);
            const arrivalTimeStr = `${arrivalDate.getHours().toString().padStart(2, '0')}:${arrivalDate.getMinutes().toString().padStart(2, '0')}`;
            
            // Retour minimum 1h après l'arrivée
            const minReturnDate = new Date(arrivalDate);
            minReturnDate.setHours(minReturnDate.getHours() + 1);
            const minReturnHour = minReturnDate.getHours();
            const minReturnMinute = minReturnDate.getMinutes() <= 30 ? 30 : 0;
            const actualMinReturnHour = minReturnDate.getMinutes() > 30 ? minReturnHour + 1 : minReturnHour;
            
            // Générer créneaux pour le retour
            const returnSlots = [];
            for (let h = actualMinReturnHour; h <= 22; h++) {
                for (let m = (h === actualMinReturnHour ? minReturnMinute : 0); m < 60; m += 30) {
                    if (h === 22 && m > 0) break;
                    const timeStr = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}`;
                    returnSlots.push({ time: timeStr, label: timeStr });
                }
            }
            
            if (returnSlots.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Retour le lendemain',
                    html: `<p>Votre arrivée estimée est à <strong>${arrivalTimeStr}</strong>.</p>
                           <p class="mt-2">Le retour ne peut pas être fait le même jour. Veuillez choisir un autre jour.</p>`,
                    confirmButtonText: 'Continuer en aller simple',
                    confirmButtonColor: '#e94f1b'
                }).then(() => {
                    userWantsAllerRetour = false;
                    openReservationModal(program.id, departureDate);
                });
                return;
            }
            
            let timeSlotsHtml = '<div class="grid grid-cols-4 gap-2 max-h-48 overflow-y-auto p-2">';
            returnSlots.forEach(slot => {
                timeSlotsHtml += `
                    <button type="button" 
                            class="return-time-slot-btn p-3 text-center rounded-lg border-2 border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition-all font-semibold"
                            data-time="${slot.time}">
                        ${slot.label}
                    </button>
                `;
            });
            timeSlotsHtml += '</div>';
            
            Swal.fire({
                title: '<i class="fas fa-undo text-blue-500"></i> Heure de retour',
                html: `
                    <div class="text-left space-y-4">
                        <div class="bg-green-50 p-3 rounded-lg">
                            <p class="font-bold text-green-800"><i class="fas fa-check-circle mr-2"></i>Aller confirmé</p>
                            <p class="text-sm text-green-700">${program.point_depart} → ${program.point_arrive}</p>
                            <p class="text-sm text-green-600">${dateFormatted} • Départ: ${window.selectedDepartureTime} • Arrivée: ~${arrivalTimeStr}</p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <p class="font-bold text-blue-800">${program.point_arrive} → ${program.point_depart}</p>
                            <p class="text-sm text-blue-600">${dateFormatted} (même jour)</p>
                        </div>
                        <p class="text-gray-600 font-medium">À quelle heure souhaitez-vous repartir ?</p>
                        <p class="text-xs text-gray-400"><i class="fas fa-info-circle mr-1"></i>Minimum 1h après votre arrivée</p>
                        ${timeSlotsHtml}
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check mr-2"></i>Confirmer le retour',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#e94f1b',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-lg px-4 py-2'
                },
                preConfirm: () => {
                    if (!window.selectedReturnTime) {
                        Swal.showValidationMessage('Veuillez sélectionner une heure de retour');
                        return false;
                    }
                    return window.selectedReturnTime;
                },
                didOpen: () => {
                    document.querySelectorAll('.return-time-slot-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            document.querySelectorAll('.return-time-slot-btn').forEach(b => {
                                b.classList.remove('bg-blue-500', 'text-white', 'border-blue-500');
                                b.classList.add('border-gray-200');
                            });
                            this.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
                            this.classList.remove('border-gray-200');
                            window.selectedReturnTime = this.dataset.time;
                        });
                    });
                }
            }).then((result) => {
                if (result.isConfirmed && window.selectedReturnTime) {
                    // Récapitulatif et continuation
                    window.selectedReturnDate = departureDate; // Même jour
                    showTripSummaryAndContinue(program, departureDate);
                }
            });
        }

        // Récapitulatif du voyage aller-retour
        function showTripSummaryAndContinue(program, departureDate) {
            const dateFormatted = new Date(departureDate).toLocaleDateString('fr-FR');
            const totalPrice = Number(program.montant_billet) * 2;
            
            Swal.fire({
                icon: 'success',
                title: 'Voyage Aller-Retour confirmé !',
                html: `
                    <div class="text-left space-y-3">
                        <div class="border-l-4 border-green-500 pl-3">
                            <p class="font-bold text-gray-800">↗ ALLER</p>
                            <p class="text-sm">${program.point_depart} → ${program.point_arrive}</p>
                            <p class="text-sm text-gray-500">${dateFormatted} à ${window.selectedDepartureTime}</p>
                        </div>
                        <div class="border-l-4 border-blue-500 pl-3">
                            <p class="font-bold text-gray-800">↙ RETOUR</p>
                            <p class="text-sm">${program.point_arrive} → ${program.point_depart}</p>
                            <p class="text-sm text-gray-500">${dateFormatted} à ${window.selectedReturnTime}</p>
                        </div>
                        <div class="bg-orange-50 p-3 rounded-lg text-center mt-4">
                            <p class="text-lg font-bold text-[#e94f1b]">Total: ${totalPrice.toLocaleString('fr-FR')} FCFA</p>
                        </div>
                    </div>
                `,
                confirmButtonText: 'Continuer la réservation',
                confirmButtonColor: '#e94f1b'
            }).then(() => {
                openReservationModal(program.id, departureDate);
            });
        }

        // Afficher le sélecteur de voyage retour
        async function showReturnTripSelector(outboundProgram, outboundDate, returnDate) {
            console.log('DEBUG: showReturnTripSelector called', { outboundDate, returnDate });
            Swal.fire({
                title: 'Recherche des retours...',
                text: `Pour le ${new Date(returnDate).toLocaleDateString('fr-FR')}`,
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const params = new URLSearchParams({
                    original_depart: outboundProgram.point_depart,
                    original_arrive: outboundProgram.point_arrive,
                    min_date: returnDate // Utilise la date choisie par l'utilisateur
                });
                
                const response = await fetch('{{ route("api.return-trips") }}?' + params);
                const data = await response.json();
                Swal.close();

                if (!data.success || data.count === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Aucun retour disponible',
                        html: `<p>Aucun voyage retour <strong>${outboundProgram.point_arrive} → ${outboundProgram.point_depart}</strong> n'est disponible le/après le ${new Date(returnDate).toLocaleDateString('fr-FR')}.</p>
                               <p class="text-sm text-gray-500 mt-2">Essayez une autre date ou continuez en aller simple.</p>`,
                        confirmButtonText: 'Continuer en aller simple',
                        confirmButtonColor: '#e94f1b',
                        showCancelButton: true,
                        cancelButtonText: 'Changer date retour'
                    }).then((result) => {
                         if (result.isConfirmed) {
                             userWantsAllerRetour = false;
                             openReservationModal(outboundProgram.id, outboundDate);
                         } else if (result.dismiss === Swal.DismissReason.cancel) {
                             // Ré-ouvrir le choix de date
                             document.getElementById('choiceReturn').click(); 
                         }
                    });
                    return;
                }

                // Afficher les options de retour
                displayReturnTripOptions(outboundProgram, outboundDate, data.return_trips, returnDate);

            } catch (error) {
                console.error('Erreur:', error);
                Swal.close();
                Swal.fire({ icon: 'error', title: 'Erreur', text: 'Impossible de charger les voyages retour.' });
            }
        }

        // Afficher les options de voyage retour dans un modal
        function displayReturnTripOptions(outboundProgram, outboundDate, returnTrips, requestedReturnDate) {
            console.log('DEBUG: displayReturnTripOptions called', { outboundDate, requestedReturnDate });
            
            // Capture explicite de la date aller
            const savedOutboundDate = outboundDate;

            // Pour le même jour, on affiche simplement les horaires disponibles
            const dateFormatted = new Date(outboundDate).toLocaleDateString('fr-FR', { 
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
            });
            const returnDateFormatted = new Date(requestedReturnDate).toLocaleDateString('fr-FR', { 
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
            });

            let timeSlotsHtml = '<div class="grid grid-cols-3 gap-3 max-h-60 overflow-y-auto p-2">';
            returnTrips.forEach(trip => {
                // Utiliser la date du trip si disponible (via backend display_date), sinon la date aller
                const tripDate = trip.display_date || outboundDate;
                
                timeSlotsHtml += `
                    <div class="return-trip-option border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-[#e94f1b] hover:bg-orange-50 transition-all text-center" 
                         data-trip-id="${trip.id}" data-trip-date="${tripDate}">
                        <p class="font-bold text-xl text-gray-800">${trip.heure_depart}</p>
                        <p class="text-sm text-gray-500">→ ${trip.heure_arrive}</p>
                        <p class="text-sm font-bold text-[#e94f1b] mt-1">${Number(trip.montant_billet).toLocaleString('fr-FR')} FCFA</p>
                         ${trip.display_date && trip.display_date !== outboundDate ? `<p class="text-xs text-blue-600 mt-1 font-bold">${new Date(tripDate).toLocaleDateString('fr-FR', {day: 'numeric', month: 'short'})}</p>` : ''}
                    </div>
                `;
            });
            timeSlotsHtml += '</div>';

            let html = `
                <div class="text-left max-h-[60vh] overflow-y-auto">
                    <div class="bg-green-50 p-3 rounded-lg mb-4 text-sm">
                        <p class="font-bold text-green-800"><i class="fas fa-check-circle mr-2"></i>Aller confirmé</p>
                        <p class="text-green-700">${outboundProgram.point_depart} → ${outboundProgram.point_arrive}</p>
                        <p class="text-green-600 text-xs">${dateFormatted} • Départ: ${window.selectedDepartureTime || outboundProgram.heure_depart}</p>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-lg mb-4">
                        <p class="font-bold text-blue-800"><i class="fas fa-undo mr-2"></i>Retour: ${outboundProgram.point_arrive} → ${outboundProgram.point_depart}</p>
                        <p class="text-blue-600 text-sm font-semibold">${returnDateFormatted}</p>
                         <p class="text-blue-600 text-xs">Options disponibles</p>
                    </div>
                    <p class="font-medium text-gray-700 mb-3">Sélectionnez votre heure de retour :</p>
                    ${timeSlotsHtml}
                </div>
            `;
            html += `</div>`;

            Swal.fire({
                title: `<i class="fas fa-undo text-blue-500"></i> Retour: ${outboundProgram.point_arrive} → ${outboundProgram.point_depart}`,
                html: html,
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonText: 'Annuler',
                customClass: {
                    popup: 'rounded-2xl',
                    htmlContainer: 'px-2'
                },
                didOpen: () => {
                    document.querySelectorAll('.return-trip-option').forEach(el => {
                        el.addEventListener('click', function() {
                            const tripId = this.dataset.tripId;
                            const tripDate = this.dataset.tripDate;
                            window.selectedReturnProgram = returnTrips.find(t => t.id == tripId);
                            window.selectedReturnDate = tripDate;
                            Swal.close();
                            
                            // Calculer le prix total
                            const totalPrice = Number(outboundProgram.montant_billet) + Number(window.selectedReturnProgram.montant_billet);
                            
                            // Afficher récapitulatif et ouvrir modal réservation
                            Swal.fire({
                                icon: 'success',
                                title: 'Aller-Retour sélectionné !',
                                html: `
                                    <div class="text-left space-y-2">
                                        <p><strong>Aller:</strong> ${outboundProgram.point_depart} → ${outboundProgram.point_arrive}<br>
                                           <span class="text-sm text-gray-500">${new Date(outboundDate).toLocaleDateString('fr-FR')} à ${outboundProgram.heure_depart}</span></p>
                                        <p><strong>Retour:</strong> ${window.selectedReturnProgram.point_depart} → ${window.selectedReturnProgram.point_arrive}<br>
                                           <span class="text-sm text-gray-500">${new Date(tripDate).toLocaleDateString('fr-FR')} à ${window.selectedReturnProgram.heure_depart}</span></p>
                                        <p class="text-lg font-bold text-[#e94f1b] mt-3">Total: ${totalPrice.toLocaleString('fr-FR')} FCFA</p>
                                    </div>
                                `,
                                confirmButtonText: 'Continuer la réservation',
                                confirmButtonColor: '#e94f1b'
                            }).then(() => {
                                // Ouvrir le modal de réservation pour l'aller
                                console.log('DEBUG: Opening final reservation modal with OutboundDate:', savedOutboundDate);
                                
                                // FORCE RESTORE: On s'assure que globalement la date aller est correcte
                                window.outboundDate = savedOutboundDate;
                                window.currentReservationDate = savedOutboundDate; // Double sécurité
                                console.log('DEBUG: Forced window.outboundDate restored to:', window.outboundDate);

                                openReservationModal(outboundProgram.id, savedOutboundDate);
                            });
                        });
                    });
                }
            });
        }
     function getNextAvailableDate(program) {
        if(program.type_programmation === 'ponctuel') return program.date_depart.split('T')[0];
        // Pour récurrent, on prend demain si possible, ou une logique plus complexe
        // Ici on simplifie en renvoyant la date de jour ou la date de début
        return new Date().toISOString().split('T')[0]; 
    }
    // ============================================
        // FONCTION 3: Ouvrir le modal de réservation
        // ============================================
         function showReservationModal(programId, searchDate = null) {
            // Incrémenter l'ID de requête
            currentRequestId++;
            const thisRequestId = currentRequestId;

            console.log(`[REQ #${thisRequestId}] Ouverture modal Réservation pour ID ${programId}`);

            // Réinitialisation
            currentProgramId = programId;
            selectedNumberOfPlaces = 0;
            selectedSeats = [];
            reservedSeats = [];
            vehicleDetails = null;
            window.currentReservationDate = null;

            // Reset UI
            document.getElementById('reservationProgramInfo').innerHTML = '<div class="text-center p-4"><i class="fas fa-spinner fa-spin text-2xl text-[#e94f1b]"></i><p>Chargement...</p></div>';
            document.getElementById('selectedSeatsCount').textContent = '0 place sélectionnée';
            document.getElementById('seatSelectionArea').innerHTML = '';
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step3').classList.add('hidden');
            document.getElementById('step1').classList.remove('hidden');
            document.getElementById('nextStepBtn').disabled = true;
            document.querySelectorAll('.place-count-btn').forEach(btn => btn.classList.remove('active'));

            document.getElementById('reservationModal').classList.remove('hidden');

            // Fetch info programme
            fetch(`/user/booking/program/${programId}`)
                .then(response => response.json())
                .then(data => {
                    if (thisRequestId !== currentRequestId) return;

                    if (data.success) {
                        const program = data.programme;
                         currentSelectedProgram = program; 
                        // Déterminer la date de voyage finale
                        let dateVoyage = searchDate;
                        if (!dateVoyage) {
                            dateVoyage = program.date_depart.split('T')[0];
                        }
                        window.currentReservationDate = dateVoyage;

                        const dateDisplay = new Date(dateVoyage).toLocaleDateString('fr-FR');
                        
                        // Prix et badge
                        let prixAffiche = parseInt(program.montant_billet);
                        window.currentProgramPrice = prixAffiche; // IMPORTANT pour le calcul final

                        let allerRetourBadge = '';
                        if (window.userChoseAllerRetour) {
                            prixAffiche = prixAffiche * 2;
                            allerRetourBadge = '<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm"><i class="fas fa-exchange-alt me-1"></i>Aller-Retour</span>';
                        }

                        document.getElementById('reservationProgramInfo').innerHTML = `
                            <div class="flex flex-wrap gap-4">
                                <span><i class="fas fa-map-marker-alt"></i> ${program.point_depart} → ${program.point_arrive}</span>
                                <span><i class="fas fa-calendar"></i> ${dateDisplay}</span>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded"><i class="fas fa-clock"></i> Départ: ${window.selectedDepartureTime || program.heure_depart}</span>
                                <span><i class="fas fa-money-bill-wave"></i> ${prixAffiche.toLocaleString('fr-FR')} FCFA</span>
                                ${allerRetourBadge}
                                ${window.selectedReturnTime ? `<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm"><i class="fas fa-undo"></i> Retour: ${selectedReturnDate ? new Date(selectedReturnDate).toLocaleDateString('fr-FR') + ' à ' : ''}${window.selectedReturnTime}</span>` : ''}
                            </div>
                        `;

                        // Précharger les places
                        fetch(`/user/booking/reservation/reserved-seats/${programId}?date=${encodeURIComponent(dateVoyage)}`)
                            .then(r => r.json())
                            .then(d => {
                                if (d.success && thisRequestId === currentRequestId) {
                                    reservedSeats = d.reservedSeats || [];
                                }
                            });
                    }
                });
        }

        // Exposer globalement pour compatibilité
        window.openReservationModal = showReservationModal;



        // ============================================
        // FONCTION 4: Fermer le modal de réservation
        // ============================================
        function closeReservationModal() {
            document.getElementById('reservationModal').classList.add('hidden');
        }

        // ============================================
        // FONCTION 5: Sélectionner le nombre de places
        // ============================================
        function selectNumberOfPlaces(number, element) {
            selectedNumberOfPlaces = number;

            // Activer le bouton sélectionné
            document.querySelectorAll('.place-count-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            if (element) {
                element.classList.add('active');
            } else if (window.event && window.event.target) {
                window.event.target.closest('button').classList.add('active');
            }

            // Activer le bouton suivant
            document.getElementById('nextStepBtn').disabled = false;
        }
 // --- 3. GESTION DES FLUX ALLER-RETOUR ---

        function openAllerRetourConfirmModal(program, selectedDepartureDate = null) {
            // Fermer les autres modals potentiels
            document.getElementById('programsListModal').classList.add('hidden');
            document.getElementById('dateSelectionModal').classList.add('hidden');
            
            const modal = document.getElementById('allerRetourConfirmModal');
            
            // Stocker la date
            window.selectedDepartureDate = selectedDepartureDate || program.date_depart?.split('T')[0];
            currentSelectedProgram = program;

            // UI Info
            document.getElementById('allerRetourTripInfo').innerHTML = `
                <div class="text-center">
                    <div class="text-lg font-bold text-gray-800 mb-2">
                        ${program.point_depart} <i class="fas fa-arrow-right text-gray-400 mx-2"></i> ${program.point_arrive}
                    </div>
                    <div class="text-sm text-gray-500 mb-3">${program.compagnie?.name || 'Compagnie'}</div>
                </div>
            `;
            
            // Détection automatique du choix basé sur la recherche
            const searchType = new URLSearchParams(window.location.search).get('is_aller_retour');
            if (searchType === '1') {
                userWantsAllerRetour = true;
                document.getElementById('allerRetourChoice').value = 'aller_retour';
            } else {
                userWantsAllerRetour = false;
                document.getElementById('allerRetourChoice').value = 'aller_simple';
            }
            
            updateAllerRetourPriceDisplay(program);
            onAllerRetourChoiceChange(); // Mettre à jour l'affichage dynamique (dates, etc)
            modal.classList.remove('hidden');
        }
function onAllerRetourChoiceChange() {
            const choice = document.getElementById('allerRetourChoice').value;
            userWantsAllerRetour = (choice === 'aller_retour');
            
            updateAllerRetourPriceDisplay(currentSelectedProgram);
            
            const returnDateSection = document.getElementById('returnDateSection');
            // Afficher section date retour SI A/R ET (Récurrent OU (Ponctuel ET Date Retour non fixée par défaut))
            // Note: Pour ponctuel, le retour est souvent le même jour par défaut, mais ici on gère le cas récurrent
            if (userWantsAllerRetour && currentSelectedProgram.type_programmation === 'recurrent') {
                returnDateSection.classList.remove('hidden');
                populateReturnDateSelect(currentSelectedProgram, window.selectedDepartureDate);
            } else {
                returnDateSection.classList.add('hidden');
            }
        }
 function updateAllerRetourPriceDisplay(program) {
            const priceDiv = document.getElementById('allerRetourPriceDisplay');
            const prixSimple = parseInt(program.montant_billet);
            const prixDouble = prixSimple * 2;
            
            if (userWantsAllerRetour) {
                priceDiv.innerHTML = `
                    <div class="bg-blue-50 rounded-lg p-3 border-2 border-blue-200">
                        <div class="text-sm text-blue-600 mb-1"><i class="fas fa-exchange-alt me-1"></i> Prix Aller-Retour</div>
                        <div class="text-2xl font-bold text-[#e94f1b]">${prixDouble.toLocaleString('fr-FR')} FCFA</div>
                    </div>
                `;
            } else {
                priceDiv.innerHTML = `
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="text-sm text-gray-600 mb-1"><i class="fas fa-arrow-right me-1"></i> Prix Aller Simple</div>
                        <div class="text-2xl font-bold text-[#e94f1b]">${prixSimple.toLocaleString('fr-FR')} FCFA</div>
                    </div>
                `;
            }
        }
 function populateReturnDateSelect(program, departureDateStr) {
            const select = document.getElementById('returnDateSelect');
            select.innerHTML = '<option value="">Chargement...</option>';

            // Récupérer les jours de récurrence du programme RETOUR si dispo
            let rawDays = program.jours_recurrence;
            if (program.programme_retour && program.programme_retour.jours_recurrence) {
                rawDays = program.programme_retour.jours_recurrence;
            }

            let allowedDays = [];
            try {
                allowedDays = (typeof rawDays === 'string') ? JSON.parse(rawDays) : (rawDays || []);
            } catch(e) { allowedDays = []; }
            allowedDays = allowedDays.map(d => d.toLowerCase());

            const daysMap = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
            const dates = [];
            
            // Date de début : lendemain du départ
            let start = departureDateStr ? new Date(departureDateStr) : new Date();
            start.setDate(start.getDate() + 1);
            
            // Date limite programme retour
            let returnStartDate = program.programme_retour?.date_depart ? new Date(program.programme_retour.date_depart) : null;
            if (returnStartDate && returnStartDate > start) start = returnStartDate;

            let current = new Date(start);
            let count = 0;

            while (count < 10) {
                const dayName = daysMap[current.getDay()];
                if (allowedDays.includes(dayName)) {
                    // Check fin validité
                    let isValid = true;
                    if (program.date_fin_programmation) {
                        if (current.toISOString().split('T')[0] > program.date_fin_programmation) isValid = false;
                    }
                    
                    if (isValid) {
                        dates.push({
                            val: current.toISOString().split('T')[0],
                            txt: current.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
                        });
                        count++;
                    }
                }
                current.setDate(current.getDate() + 1);
                // Sécurité boucle infinie
                if ((current - start) > 5184000000) break; // 60 jours
            }

            select.innerHTML = '<option value="">Choisir une date de retour...</option>';
            if (dates.length === 0) {
                select.innerHTML += '<option value="" disabled>Aucune date disponible</option>';
            } else {
                dates.forEach(d => {
                    select.innerHTML += `<option value="${d.val}">${d.txt}</option>`;
                });
            }
        }
        function closeAllerRetourConfirmModal() {
            document.getElementById('allerRetourConfirmModal').classList.add('hidden');
        }
 function confirmAllerRetour() {
            // Enregistrer le choix de l'utilisateur
            window.userChoseAllerRetour = userWantsAllerRetour;
            
            if (userWantsAllerRetour) {
                if (currentSelectedProgram.type_programmation === 'recurrent') {
                    const returnDate = document.getElementById('returnDateSelect').value;
                    if (!returnDate) {
                        Swal.fire({ icon: 'warning', text: 'Veuillez sélectionner une date de retour.' });
                        return;
                    }
                    window.selectedReturnDate = returnDate;
                } else {
                    // Ponctuel : retour le même jour
                    window.selectedReturnDate = window.selectedDepartureDate;
                }
            } else {
                // L'utilisateur a choisi Aller Simple (même sur un programme A/R)
                window.selectedReturnDate = null;
            }
            
            closeAllerRetourConfirmModal();
            
            // Suite du flux
            const program = currentSelectedProgram;
            // Si c'est un récurrent et qu'on n'a pas encore de date de départ
            if (program.type_programmation === 'recurrent' && !window.selectedDepartureDate) {
                openDateSelectionModal(program);
            } else {
                // On a tout ce qu'il faut
                openReservationModal(program.id, window.selectedDepartureDate);
            }
        }
        // ============================================
        // FONCTION 1: Afficher les détails du véhicule
        // ============================================
        async function showVehicleDetails(vehicleId, programId, dateVoyageInput = null) {
            console.log(`[DETAILS] Demande détails véhicule ${vehicleId} pour programme ${programId} (Date: ${dateVoyageInput})`);
            if (!vehicleId) {
                Swal.fire({
                    icon: 'info',
                    title: 'Information',
                    text: 'Aucun véhicule associé à ce programme.',
                    confirmButtonColor: '#e94f1b',
                });
                return;
            }
            // Récupérer la date : soit passée en paramètre, soit depuis l'URL/Input
            let dateVoyage = dateVoyageInput;
            if (!dateVoyage) {
                const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('date_depart')) {
                dateVoyage = urlParams.get('date_depart');
            } else {
                const dateInput = document.getElementById('date_depart');
                if (dateInput && dateInput.value) {
                    dateVoyage = dateInput.value;
                } else {
                    dateVoyage = new Date().toISOString().split('T')[0];
                }
            }
        }
            console.log(`[DETAILS] Date utilisée: ${dateVoyage}`);
            Swal.fire({
                title: 'Chargement...',
                text: 'Récupération des informations du véhicule',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            try {
                // CORRECTION: Passer l'ID et la date dans la route
                const url = "{{ route('user.reservation.vehicle', ':id') }}".replace(':id', vehicleId);
                const response = await fetch(url + `?date=${encodeURIComponent(dateVoyage)}&program_id=${programId}`);
                const data = await response.json();
                if (!data.success) {
                    throw new Error(data.error || 'Véhicule non trouvé');
                }
                Swal.fire({
                    title: `<strong>${data.vehicule.marque} ${data.vehicule.modele}</strong>`,
                    html: data.html,
                    width: 850,
                    padding: '20px',
                    showCloseButton: true,
                    showConfirmButton: false,
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: error.message,
                    confirmButtonColor: '#e94f1b',
                });
            }
        }

        // ============================================
        // FONCTION 2: Générer la visualisation des places
        // ============================================
        function generatePlacesVisualization(vehicle) {
            let config = typeRangeConfig[vehicle.type_range];
            if (!config) {
                config = { placesGauche: 2, placesDroite: 2 };
                console.warn(`Configuration de véhicule inconnue: ${vehicle.type_range}. Utilisation du mode par défaut 2x2.`);
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
                                                                                    <div style="max-height: 400px; overflow-y:                                                 auto;">
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
                    html += `
                                                                                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #e94f1b, #e89116); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.1rem; box-shadow: 0 2px 4px rgba(254, 162, 25, 0.3); cursor: help;" title="Place ${numeroPlace + i}">
                                                                                            ${numeroPlace + i}
                                                                                        </div>
                                                                                    `;
                }

                html += `
                                                                                        </div>

                                                                                        <!-- Allée -->
                                                                                        <div style="text-align: center;">
                                                                                            <div style="width: 10px; height: 40px; background: #9ca3af; border-radius: 5px; margin: 0 auto;"></div>
                                                                                        </div>

                                                                                        <!-- Places côté droit -->
                                                                                        <div style="display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
                                                                                `;

                // Places côté droit
                for (let i = 0; i < placesDroiteCetteRanger; i++) {
                    html += `
                                                                                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.1rem; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3); cursor: help;" title="Place ${numeroPlace + placesGaucheCetteRanger + i}">
                                                                                            ${numeroPlace + placesGaucheCetteRanger + i}
                                                                                        </div>
                                                                                    `;
                }

                html += `
                                                                                        </div>
                                                                                    </div>
                                                                                `;

                numeroPlace += placesCetteRanger;
            }

            html += `
                                                                                    </div>
                                                                                </div>

                                                                                <!-- Légende -->
                                                                                <div style="display: flex; justify-content: center; gap: 20px; margin-top: 20px; padding: 15px; background: #f9fafb; border-radius: 8px;">
                                                                                    <div style="display: flex; align-items: center; gap: 8px;">
                                                                                        <div style="width: 20px; height: 20px; background: linear-gradient(135deg, #e94f1b, #e89116); border-radius: 4px;"></div>
                                                                                        <span style="color: #4b5563; font-size: 0.9rem;">Côté gauche (conducteur)</span>
                                                                                    </div>
                                                                                    <div style="display: flex; align-items: center; gap: 8px;">
                                                                                        <div style="width: 20px; height: 20px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 4px;"></div>
                                                                                        <span style="color: #4b5563; font-size: 0.9rem;">Côté droit</span>
                                                                                    </div>
                                                                                </div>
                                                                            `;

            return html;
        }

        // Variables globales (existantes, rappel)
        // let currentProgramId = null;
        // let selectedNumberOfPlaces = 0;
        // let selectedSeats = [];
        // let vehicleDetails = null;
        // let reservedSeats = [];

        // NOUVEAU: ID de requête pour éviter les conflits asynchrones
        // currentRequestId déjà déclaré en haut du script

        
        // ============================================
        // FONCTION 6: Afficher la sélection des places
        // ============================================
        async function showSeatSelection() {
            if (!currentProgramId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Aucun programme sélectionné.',
                    confirmButtonColor: '#e94f1b',
                });
                return;
            }

            // Afficher le loader
            Swal.fire({
                title: 'Chargement...',
                text: 'Récupération des informations',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                // 1. Récupérer le programme
                const programUrl = `/user/booking/program/${currentProgramId}`;
                const programResponse = await fetch(programUrl);
                const programData = await programResponse.json();

                if (!programData.success) {
                    throw new Error(programData.error || 'Programme non trouvé');
                }

                const program = programData.programme;

                // IMPORTANT: Utiliser la date stockée, pas la date du programme
                let dateVoyage = window.currentReservationDate;

                if (!dateVoyage) {
                    // Si pas de date stockée, utiliser la date du programme
                    const dateDepart = new Date(program.date_depart);
                    dateVoyage = dateDepart.toISOString().split('T')[0];
                    window.currentReservationDate = dateVoyage;
                }

                // 2. Récupérer le véhicule (avec fallback si pas de véhicule associé)
                let vehicleId = program.vehicule_id;
                
                // Si pas de véhicule associé, récupérer le premier véhicule de la compagnie
                if (!vehicleId) {
                    console.log('Pas de véhicule associé au programme, recherche véhicule par défaut...');
                    try {
                        const defaultVehicleUrl = `/user/booking/program/${currentProgramId}/default-vehicle`;
                        const defaultVehicleResponse = await fetch(defaultVehicleUrl);
                        if (defaultVehicleResponse.ok) {
                            const defaultVehicleData = await defaultVehicleResponse.json();
                            if (defaultVehicleData.success && defaultVehicleData.vehicule_id) {
                                vehicleId = defaultVehicleData.vehicule_id;
                            }
                        }
                    } catch (e) {
                        console.log('Erreur récupération véhicule par défaut:', e);
                    }
                }
                
                if (!vehicleId) {
                    // Utiliser une configuration de places par défaut (70 places)
                    vehicleDetails = {
                        type_range: '2x3',
                        capacite_total: 70,
                        marque: 'Bus',
                        modele: 'Standard'
                    };
                    console.log('Utilisation de la configuration par défaut (70 places):', vehicleDetails);
                } else {
                    const vehicleUrl = `/user/booking/vehicle/${vehicleId}`;
                    const vehicleResponse = await fetch(vehicleUrl);

                    if (!vehicleResponse.ok) {
                        throw new Error('Erreur lors du chargement du véhicule');
                    }

                    const vehicleData = await vehicleResponse.json();

                    if (!vehicleData.success) {
                        throw new Error(vehicleData.error || 'Véhicule non trouvé');
                    }

                    vehicleDetails = vehicleData.vehicule;
                }

                // 3. Récupérer les places réservées POUR CETTE DATE SPÉCIFIQUE
                const seatsUrl =
                    `/user/booking/reservation/reserved-seats/${currentProgramId}?date=${encodeURIComponent(dateVoyage)}`;
                const seatsResponse = await fetch(seatsUrl);

                if (seatsResponse.ok) {
                    const seatsData = await seatsResponse.json();
                    if (seatsData.success) {
                        reservedSeats = seatsData.reservedSeats || [];
                        console.log('Places réservées pour', dateVoyage, ':', reservedSeats);
                    }
                }

                // Fermer le loader
                Swal.close();

                // Générer la vue de sélection des places
                generateSeatSelectionView();

                // Changer d'étape
                document.getElementById('step1').classList.add('hidden');
                document.getElementById('step2').classList.remove('hidden');


            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    html: `
                                                                                    <div class="text-left">
                                                                                        <p class="mb-2">${error.message}</p>
                                                                                        <p class="text-sm text-gray-600 mt-2">
                                                                                            Vérifiez que :
                                                                                            <ul class="list-disc pl-5 mt-1">
                                                                                                <li>Vous êtes bien connecté</li>
                                                                                                <li>Le programme existe toujours</li>
                                                                                                <li>Le véhicule est associé au programme</li>
                                                                                            </ul>
                                                                                        </p>
                                                                                    </div>
                                                                                `,
                    confirmButtonColor: '#e94f1b',
                });
            }
        }

        // ============================================
        // FONCTION 7: Générer la vue de sélection des places
        // ============================================
        function generateSeatSelectionView() {
            if (!vehicleDetails) {
                document.getElementById('seatSelectionArea').innerHTML =
                    '<p class="text-center text-red-500">Impossible de charger les informations du véhicule.</p>';
                return;
            }

            const config = typeRangeConfig[vehicleDetails.type_range] || typeRangeConfig['2x3'];
            if (!config) {
                document.getElementById('seatSelectionArea').innerHTML =
                    '<p class="text-center text-red-500">Impossible de charger la configuration des places.</p>';
                return;
            }

            const placesGauche = config.placesGauche;
            const placesDroite = config.placesDroite;
            const placesParRanger = placesGauche + placesDroite;
            // Utiliser capacite_total ou nombre_place selon ce qui est disponible
            const totalPlaces = parseInt(vehicleDetails.capacite_total || vehicleDetails.nombre_place || 70);
            const nombreRanger = Math.ceil(totalPlaces / placesParRanger);
            
            // Construire le nom du véhicule de manière sécurisée
            const vehicleName = vehicleDetails.marque + ' ' + (vehicleDetails.modele || '');
            const vehicleImmat = vehicleDetails.immatriculation || '';
            const vehicleTitle = vehicleImmat ? `${vehicleName.trim()} - ${vehicleImmat}` : vehicleName.trim();

            let html = `
                                                                                <div class="bg-gray-50 p-6 rounded-xl mb-6">
                                                                                    <div class="text-center mb-4">
                                                                                        <h4 class="font-bold text-lg mb-2">${vehicleTitle}</h4>
                                                                                        <p class="text-gray-600">Type: ${vehicleDetails.type_range} | Total places: ${totalPlaces}</p>
                                                                                    </div>
                                                                                    
                                                                                    <!-- Option assignation automatique -->
                                                                                    <div class="flex justify-center gap-4 mb-6">
                                                                                        <button type="button" onclick="toggleSelectionMode('manual')" id="btnManualSelect" class="px-4 py-2 rounded-lg font-semibold transition bg-[#e94f1b] text-white">
                                                                                            <i class="fas fa-hand-pointer mr-2"></i>Sélection manuelle
                                                                                        </button>
                                                                                        <button type="button" onclick="autoAssignSeats()" id="btnAutoAssign" class="px-4 py-2 rounded-lg font-semibold transition bg-blue-500 text-white hover:bg-blue-600">
                                                                                            <i class="fas fa-random mr-2"></i>Assignation automatique
                                                                                        </button>
                                                                                    </div>

                                                                                    <!-- Vue avant du bus -->
                                                                                    <div class="flex justify-center mb-8">
                                                                                        <div class="w-32 h-16 bg-gray-800 rounded-t-2xl flex items-center justify-center">
                                                                                            <i class="fas fa-bus text-white text-2xl"></i>
                                                                                        </div>
                                                                                    </div>

                                                                                    <!-- Grille des places -->
                                                                                    <div class="overflow-x-auto">
                                                                            `;

            let numeroPlace = 1;

            for (let ranger = 1; ranger <= nombreRanger; ranger++) {
                const placesRestantes = totalPlaces - (numeroPlace - 1);
                const placesCetteRanger = Math.min(placesParRanger, placesRestantes);
                const placesGaucheCetteRanger = Math.min(placesGauche, placesCetteRanger);
                const placesDroiteCetteRanger = Math.min(placesDroite, placesCetteRanger - placesGaucheCetteRanger);

                html += `
                                                                                    <div class="flex items-center justify-center mb-8 gap-8">
                                                                                        <!-- Côté gauche -->
                                                                                        <div class="flex flex-col items-center">
                                                                                            <div class="text-sm text-gray-600 mb-2">Rangée ${ranger}</div>
                                                                                            <div class="flex gap-3">
                                                                                `;

                // Places côté gauche
                for (let i = 0; i < placesGaucheCetteRanger; i++) {
                    const seatNumber = numeroPlace + i;
                    const isReserved = reservedSeats.includes(seatNumber);
                    const isSelected = selectedSeats.includes(seatNumber);
                    const canSelect = !isReserved && selectedSeats.length < selectedNumberOfPlaces;

                    html += `
                                                                                        <div class="seat w-14 h-14 rounded-lg flex flex-col items-center justify-center font-bold transition-all duration-200
                                                                                                  ${isReserved ? 'bg-red-500 text-white cursor-not-allowed opacity-60' :
                            isSelected ? 'bg-[#e94f1b] text-white shadow-lg transform scale-110' :
                                'bg-blue-500 text-white hover:bg-blue-600 hover:shadow-md cursor-pointer'}"
                                                                                             ${!isReserved ? `onclick="toggleSeat(${seatNumber})"` : ''}
                                                                                             title="Place ${seatNumber}${isReserved ? ' (Réservée)' : ''}">
                                                                                            <span class="text-lg">${seatNumber}</span>
                                                                                            <span class="text-xs">${isReserved ? '✗' : (isSelected ? '✓' : '')}</span>
                                                                                        </div>
                                                                                    `;
                }

                html += `
                                                                                            </div>
                                                                                        </div>

                                                                                        <!-- Allée -->
                                                                                        <div class="w-20 h-2 bg-gray-400 rounded my-8"></div>

                                                                                        <!-- Côté droit -->
                                                                                        <div class="flex flex-col items-center">
                                                                                            <div class="text-sm text-gray-600 mb-2">Rangée ${ranger}</div>
                                                                                            <div class="flex gap-3">
                                                                                `;

                // Places côté droit
                for (let i = 0; i < placesDroiteCetteRanger; i++) {
                    const seatNumber = numeroPlace + placesGaucheCetteRanger + i;
                    const isReserved = reservedSeats.includes(seatNumber);
                    const isSelected = selectedSeats.includes(seatNumber);
                    const canSelect = !isReserved && selectedSeats.length < selectedNumberOfPlaces;

                    html += `
                                                                                        <div class="seat w-14 h-14 rounded-lg flex flex-col items-center justify-center font-bold transition-all duration-200
                                                                                                  ${isReserved ? 'bg-red-500 text-white cursor-not-allowed opacity-60' :
                            isSelected ? 'bg-[#e94f1b] text-white shadow-lg transform scale-110' :
                                'bg-green-500 text-white hover:bg-green-600 hover:shadow-md cursor-pointer'}"
                                                                                             ${!isReserved ? `onclick="toggleSeat(${seatNumber})"` : ''}
                                                                                             title="Place ${seatNumber}${isReserved ? ' (Réservée)' : ''}">
                                                                                            <span class="text-lg">${seatNumber}</span>
                                                                                            <span class="text-xs">${isReserved ? '✗' : (isSelected ? '✓' : '')}</span>
                                                                                        </div>
                                                                                    `;
                }

                html += `
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                `;

                numeroPlace += placesCetteRanger;
            }

            html += `
                                                                                    </div>

                                                                                    <!-- Information -->
                                                                                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                                                                                        <p class="text-sm text-gray-700">
                                                                                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                                                                            Sélectionnez ${selectedNumberOfPlaces} place${selectedNumberOfPlaces > 1 ? 's' : ''} en cliquant sur les places disponibles.
                                                                                            Les places en rouge sont déjà réservées.
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            `;

            document.getElementById('seatSelectionArea').innerHTML = html;
            updateSelectedSeatsCount();
        }

        // ============================================
        // FONCTION 7.1: Basculer le mode de sélection
        // ============================================
        function toggleSelectionMode(mode) {
             // Réinitialiser la sélection si on change de mode
            selectedSeats.forEach(seat => {
                const el = document.querySelector(`[onclick="toggleSeat(${seat})"]`);
                if(el) {
                    el.classList.remove('bg-[#e94f1b]', 'shadow-lg', 'transform', 'scale-110');
                    el.querySelector('.text-xs').textContent = '';
                    const isLeftSide = seat <= typeRangeConfig[vehicleDetails.type_range].placesGauche;
                    el.classList.add(isLeftSide ? 'bg-blue-500' : 'bg-green-500');
                }
            });
            selectedSeats = [];
            updateSelectedSeatsCount();

            // Feedback visuel sur les boutons
            const btnManual = document.getElementById('btnManualSelect');
            const btnAuto = document.getElementById('btnAutoAssign');
            
            if (mode === 'manual') {
                btnManual.classList.add('bg-[#e94f1b]', 'text-white');
                btnManual.classList.remove('bg-gray-200', 'text-gray-800');
                btnAuto.classList.remove('bg-blue-600', 'text-white');
                btnAuto.classList.add('bg-blue-500', 'text-white'); // Keep auto button generic
            }
        }

        // ============================================
        // FONCTION 7.2: Assignation automatique
        // ============================================
        function autoAssignSeats() {
            // 1. Réinitialiser la sélection actuelle
            selectedSeats = [];
            
            // 2. Trouver toutes les places disponibles
            const totalPlaces = parseInt(vehicleDetails.capacite_total || vehicleDetails.nombre_place || 70);
            const availableSeats = [];
            
            for (let i = 1; i <= totalPlaces; i++) {
                if (!reservedSeats.includes(i)) {
                    availableSeats.push(i);
                }
            }
            
            // 3. Vérifier s'il y a assez de places
            if (availableSeats.length < selectedNumberOfPlaces) {
                Swal.fire({
                    icon: 'error',
                    title: 'Pas assez de places',
                    text: 'Il ne reste pas suffisamment de places disponibles pour votre demande.',
                    confirmButtonColor: '#e94f1b'
                });
                return false;
            }
            
            // 4. Sélectionner aléatoirement
            const shuffled = availableSeats.sort(() => 0.5 - Math.random());
            const selected = shuffled.slice(0, selectedNumberOfPlaces);
            
            // 5. Appliquer la sélection visuellement (sans passer par toggleSeat pour éviter les alertes)
            selected.forEach(seat => {
                selectedSeats.push(seat);
                const seatElement = document.querySelector(`[onclick="toggleSeat(${seat})"]`);
                if (seatElement) {
                    // Simuler l'affichage sélectionné
                    const isLeftSide = seat <= typeRangeConfig[vehicleDetails.type_range].placesGauche;
                    seatElement.classList.add('bg-[#e94f1b]', 'text-white', 'shadow-lg', 'transform', 'scale-110');
                    seatElement.classList.remove(isLeftSide ? 'bg-blue-500' : 'bg-green-500');
                    seatElement.classList.remove('hover:bg-blue-600', 'hover:bg-green-600'); // Optional cleanup
                    seatElement.querySelector('.text-xs').textContent = '✓';
                }
            });
            
            updateSelectedSeatsCount();
            return true;
        }

        // ============================================
        // FONCTION 8: Sélection/désélection d'une place
        // ============================================
        function toggleSeat(seatNumber) {
            const index = selectedSeats.indexOf(seatNumber);

            if (index === -1) {
                // Vérifier si on n'a pas dépassé le nombre de places sélectionnées
                if (selectedSeats.length >= selectedNumberOfPlaces) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Limite atteinte',
                        text: `Vous ne pouvez sélectionner que ${selectedNumberOfPlaces} place(s). Désélectionnez d'abord une place si vous voulez en choisir une autre.`,
                        confirmButtonColor: '#e94f1b',
                    });
                    return;
                }
                selectedSeats.push(seatNumber);
            } else {
                selectedSeats.splice(index, 1);
            }

            // Mettre à jour l'affichage de la place
            const seatElement = document.querySelector(`[onclick="toggleSeat(${seatNumber})"]`);
            if (seatElement) {
                const isSelected = selectedSeats.includes(seatNumber);
                const isLeftSide = seatNumber <= typeRangeConfig[vehicleDetails.type_range].placesGauche;

                seatElement.classList.toggle('bg-[#e94f1b]', isSelected);
                seatElement.classList.toggle('transform', isSelected);
                seatElement.classList.toggle('scale-110', isSelected);
                seatElement.classList.toggle('shadow-lg', isSelected);

                if (!isSelected) {
                    seatElement.classList.add(isLeftSide ? 'bg-blue-500' : 'bg-green-500');
                    seatElement.classList.remove(isLeftSide ? 'bg-green-500' : 'bg-blue-500');
                } else {
                    seatElement.classList.remove('bg-blue-500', 'bg-green-500');
                }

                // Mettre à jour le checkmark
                const checkmark = seatElement.querySelector('.text-xs');
                if (checkmark) {
                    checkmark.textContent = isSelected ? '✓' : '';
                }
            }

            updateSelectedSeatsCount();
        }

        // ============================================
        // FONCTION 9: Mettre à jour le compteur
        // ============================================
        function updateSelectedSeatsCount() {
            const count = selectedSeats.length;
            const countElement = document.getElementById('selectedSeatsCount');
            const nextBtn = document.getElementById('showPassengerInfoBtn');

            countElement.textContent =
                `${count} place${count > 1 ? 's' : ''} sélectionnée${count > 1 ? 's' : ''} / ${selectedNumberOfPlaces} demandée${selectedNumberOfPlaces > 1 ? 's' : ''}`;

            // Mettre à jour le style du compteur
            countElement.classList.remove('text-[#e94f1b]', 'text-red-500', 'text-green-500');
            if (count === 0) {
                countElement.classList.add('text-gray-600');
            } else if (count < selectedNumberOfPlaces) {
                countElement.classList.add('text-[#e94f1b]');
            } else if (count === selectedNumberOfPlaces) {
                countElement.classList.add('text-green-500');
            }

            // Activer le bouton si le nombre est exact OU si 0 (pour auto-assign)
            nextBtn.disabled = (count !== 0 && count !== parseInt(selectedNumberOfPlaces));
            
            if (count === 0) {
                nextBtn.innerHTML = '<i class="fas fa-magic mr-2"></i>Selection Auto & Continuer';
                nextBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                nextBtn.classList.remove('bg-[#e94f1b]', 'hover:bg-[#d6420f]');
            } else {
                nextBtn.innerHTML = '<span class="mr-2">Suivant</span> <i class="fas fa-arrow-right"></i>';
                nextBtn.classList.add('bg-[#e94f1b]', 'hover:bg-[#d6420f]');
                nextBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            }
        }

        // ============================================
        // FONCTION 10: Retour à l'étape 1
        // ============================================
        function backToStep1() {
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step1').classList.remove('hidden');
            // Réinitialiser la sélection des places
            selectedSeats = [];
        }

        // ============================================
        // FONCTION 10.1: Retour à l'étape 2
        // ============================================
        function backToStep2() {
            document.getElementById('step3').classList.add('hidden');
            document.getElementById('step2').classList.remove('hidden');
        }

        // ============================================
        // FONCTION 10.2: Afficher les infos passagers
        // ============================================
      function showPassengerInfo() {
    // Si aucune place sélectionnée, on lance l'assignation automatique
    if (selectedSeats.length === 0) {
        const success = autoAssignSeats();
        if (!success) return;
        
        setTimeout(() => {
            checkIfNeedRetourSelection();
        }, 500);
    } else {
        checkIfNeedRetourSelection();
    }
}

function checkIfNeedRetourSelection() {
    // Si c'est un aller-retour ET qu'on n'a pas encore sélectionné les places retour
    if (window.userChoseAllerRetour && selectedSeatsRetour.length === 0) {
        // Charger et afficher la sélection des places retour
        loadRetourSeatsSelection();
    } else {
        // Sinon, passer directement aux infos passagers
        proceedToPassengerInfo();
    }
}
async function loadRetourSeatsSelection() {
    // Récupérer le programme retour
    const retourProgId = window.selectedRetourProgramId || window.selectedReturnProgram?.id;
    
    if (!retourProgId) {
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Programme retour introuvable.'
        });
        return;
    }

    currentRetourProgramId = retourProgId;

    Swal.fire({
        title: 'Chargement...',
        text: 'Récupération des places disponibles pour le retour',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    try {
        // 1. Récupérer le programme retour
        const programUrl = `/user/booking/program/${retourProgId}`;
        const programResponse = await fetch(programUrl);
        const programData = await programResponse.json();

        if (!programData.success) {
            throw new Error(programData.error || 'Programme retour non trouvé');
        }

        const programRetour = programData.programme;

        // 2. Récupérer le véhicule
        let vehicleId = programRetour.vehicule_id;
        
        if (!vehicleId) {
            // Fallback : véhicule par défaut
            const defaultVehicleUrl = `/user/booking/program/${retourProgId}/default-vehicle`;
            const defaultVehicleResponse = await fetch(defaultVehicleUrl);
            if (defaultVehicleResponse.ok) {
                const defaultVehicleData = await defaultVehicleResponse.json();
                if (defaultVehicleData.success && defaultVehicleData.vehicule_id) {
                    vehicleId = defaultVehicleData.vehicule_id;
                }
            }
        }
        
        if (!vehicleId) {
            vehicleDetailsRetour = {
                type_range: '2x3',
                capacite_total: 70,
                marque: 'Bus',
                modele: 'Standard'
            };
        } else {
            const vehicleUrl = `/user/booking/vehicle/${vehicleId}`;
            const vehicleResponse = await fetch(vehicleUrl);
            const vehicleData = await vehicleResponse.json();
            
            if (!vehicleData.success) {
                throw new Error('Véhicule retour non trouvé');
            }
            
            vehicleDetailsRetour = vehicleData.vehicule;
        }

        // 3. Récupérer les places réservées pour le retour
        const dateRetour = window.selectedReturnDate || window.currentReservationDate;
        const seatsUrl = `/user/booking/reservation/reserved-seats/${retourProgId}?date=${encodeURIComponent(dateRetour)}`;
        const seatsResponse = await fetch(seatsUrl);

        if (seatsResponse.ok) {
            const seatsData = await seatsResponse.json();
            if (seatsData.success) {
                reservedSeatsRetour = seatsData.reservedSeats || [];
            }
        }

        Swal.close();

        // 4. Afficher les infos du retour
        const dateRetourFormatted = new Date(dateRetour).toLocaleDateString('fr-FR');
        document.getElementById('returnProgramInfo').innerHTML = `
            <span><i class="fas fa-map-marker-alt"></i> ${programRetour.point_depart} → ${programRetour.point_arrive}</span>
            <span class="mx-2">|</span>
            <span><i class="fas fa-calendar"></i> ${dateRetourFormatted}</span>
            <span class="mx-2">|</span>
            <span><i class="fas fa-clock"></i> ${programRetour.heure_depart}</span>
        `;

        // 5. Générer la vue de sélection des places retour
        generateSeatSelectionViewRetour();

        // 6. Masquer step2, afficher step2_5
        document.getElementById('step2').classList.add('hidden');
        document.getElementById('step2_5').classList.remove('hidden');

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: error.message,
            confirmButtonColor: '#e94f1b',
        });
    }
}
function generateSeatSelectionViewRetour() {
    if (!vehicleDetailsRetour) {
        document.getElementById('seatSelectionAreaRetour').innerHTML =
            '<p class="text-center text-red-500">Impossible de charger les informations du véhicule.</p>';
        return;
    }

    const config = typeRangeConfig[vehicleDetailsRetour.type_range];
    if (!config) {
        document.getElementById('seatSelectionAreaRetour').innerHTML =
            '<p class="text-center text-red-500">Configuration de places non reconnue.</p>';
        return;
    }

    const placesGauche = config.placesGauche;
    const placesDroite = config.placesDroite;
    const placesParRanger = placesGauche + placesDroite;
    const totalPlaces = parseInt(vehicleDetailsRetour.capacite_total || vehicleDetailsRetour.nombre_place || 70);
    const nombreRanger = Math.ceil(totalPlaces / placesParRanger);
    
    const vehicleName = vehicleDetailsRetour.marque + ' ' + (vehicleDetailsRetour.modele || '');
    const vehicleImmat = vehicleDetailsRetour.immatriculation || '';
    const vehicleTitle = vehicleImmat ? `${vehicleName.trim()} - ${vehicleImmat}` : vehicleName.trim();

    let html = `
        <div class="bg-gray-50 p-6 rounded-xl mb-6">
            <div class="text-center mb-4">
                <h4 class="font-bold text-lg mb-2">${vehicleTitle}</h4>
                <p class="text-gray-600">Type: ${vehicleDetailsRetour.type_range} | Total places: ${totalPlaces}</p>
            </div>
            
            <!-- Option assignation automatique -->
            <div class="flex justify-center gap-4 mb-6">
                <button type="button" onclick="toggleSelectionModeRetour('manual')" id="btnManualSelectRetour" class="px-4 py-2 rounded-lg font-semibold transition bg-blue-600 text-white">
                    <i class="fas fa-hand-pointer mr-2"></i>Sélection manuelle
                </button>
                <button type="button" onclick="autoAssignSeatsRetour()" id="btnAutoAssignRetour" class="px-4 py-2 rounded-lg font-semibold transition bg-green-500 text-white hover:bg-green-600">
                    <i class="fas fa-random mr-2"></i>Assignation automatique
                </button>
            </div>

            <!-- Vue avant du bus -->
            <div class="flex justify-center mb-8">
                <div class="w-32 h-16 bg-gray-800 rounded-t-2xl flex items-center justify-center">
                    <i class="fas fa-bus text-white text-2xl"></i>
                </div>
            </div>

            <!-- Grille des places -->
            <div class="overflow-x-auto">
    `;

    let numeroPlace = 1;

    for (let ranger = 1; ranger <= nombreRanger; ranger++) {
        const placesRestantes = totalPlaces - (numeroPlace - 1);
        const placesCetteRanger = Math.min(placesParRanger, placesRestantes);
        const placesGaucheCetteRanger = Math.min(placesGauche, placesCetteRanger);
        const placesDroiteCetteRanger = Math.min(placesDroite, placesCetteRanger - placesGaucheCetteRanger);

        html += `
            <div class="flex items-center justify-center mb-8 gap-8">
                <!-- Côté gauche -->
                <div class="flex flex-col items-center">
                    <div class="text-sm text-gray-600 mb-2">Rangée ${ranger}</div>
                    <div class="flex gap-3">
        `;

        // Places côté gauche
        for (let i = 0; i < placesGaucheCetteRanger; i++) {
            const seatNumber = numeroPlace + i;
            const isReserved = reservedSeatsRetour.includes(seatNumber);
            const isSelected = selectedSeatsRetour.includes(seatNumber);

            html += `
                <div class="seat w-14 h-14 rounded-lg flex flex-col items-center justify-center font-bold transition-all duration-200
                          ${isReserved ? 'bg-red-500 text-white cursor-not-allowed opacity-60' :
                    isSelected ? 'bg-blue-600 text-white shadow-lg transform scale-110' :
                        'bg-blue-500 text-white hover:bg-blue-600 hover:shadow-md cursor-pointer'}"
                     ${!isReserved ? `onclick="toggleSeatRetour(${seatNumber})"` : ''}
                     title="Place ${seatNumber}${isReserved ? ' (Réservée)' : ''}">
                    <span class="text-lg">${seatNumber}</span>
                    <span class="text-xs">${isReserved ? '✗' : (isSelected ? '✓' : '')}</span>
                </div>
            `;
        }

        html += `
                    </div>
                </div>

                <!-- Allée -->
                <div class="w-20 h-2 bg-gray-400 rounded my-8"></div>

                <!-- Côté droit -->
                <div class="flex flex-col items-center">
                    <div class="text-sm text-gray-600 mb-2">Rangée ${ranger}</div>
                    <div class="flex gap-3">
        `;

        // Places côté droit
        for (let i = 0; i < placesDroiteCetteRanger; i++) {
            const seatNumber = numeroPlace + placesGaucheCetteRanger + i;
            const isReserved = reservedSeatsRetour.includes(seatNumber);
            const isSelected = selectedSeatsRetour.includes(seatNumber);

            html += `
                <div class="seat w-14 h-14 rounded-lg flex flex-col items-center justify-center font-bold transition-all duration-200
                          ${isReserved ? 'bg-red-500 text-white cursor-not-allowed opacity-60' :
                    isSelected ? 'bg-blue-600 text-white shadow-lg transform scale-110' :
                        'bg-green-500 text-white hover:bg-green-600 hover:shadow-md cursor-pointer'}"
                     ${!isReserved ? `onclick="toggleSeatRetour(${seatNumber})"` : ''}
                     title="Place ${seatNumber}${isReserved ? ' (Réservée)' : ''}">
                    <span class="text-lg">${seatNumber}</span>
                    <span class="text-xs">${isReserved ? '✗' : (isSelected ? '✓' : '')}</span>
                </div>
            `;
        }

        html += `
                    </div>
                </div>
            </div>
        `;

        numeroPlace += placesCetteRanger;
    }

    html += `
            </div>

            <!-- Information -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-gray-700">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    Sélectionnez ${selectedNumberOfPlaces} place${selectedNumberOfPlaces > 1 ? 's' : ''} pour le retour.
                    Les places en rouge sont déjà réservées.
                </p>
            </div>
        </div>
    `;

    document.getElementById('seatSelectionAreaRetour').innerHTML = html;
    updateSelectedSeatsCountRetour();
}
function toggleSeatRetour(seatNumber) {
    const index = selectedSeatsRetour.indexOf(seatNumber);

    if (index === -1) {
        if (selectedSeatsRetour.length >= selectedNumberOfPlaces) {
            Swal.fire({
                icon: 'warning',
                title: 'Limite atteinte',
                text: `Vous ne pouvez sélectionner que ${selectedNumberOfPlaces} place(s). Désélectionnez d'abord une place si vous voulez en choisir une autre.`,
                confirmButtonColor: '#3b82f6',
            });
            return;
        }
        selectedSeatsRetour.push(seatNumber);
    } else {
        selectedSeatsRetour.splice(index, 1);
    }

    // Mettre à jour l'affichage
    const seatElement = document.querySelector(`[onclick="toggleSeatRetour(${seatNumber})"]`);
    if (seatElement) {
        const isSelected = selectedSeatsRetour.includes(seatNumber);
        const isLeftSide = seatNumber <= typeRangeConfig[vehicleDetailsRetour.type_range].placesGauche;

        seatElement.classList.toggle('bg-blue-600', isSelected);
        seatElement.classList.toggle('transform', isSelected);
        seatElement.classList.toggle('scale-110', isSelected);
        seatElement.classList.toggle('shadow-lg', isSelected);

        if (!isSelected) {
            seatElement.classList.add(isLeftSide ? 'bg-blue-500' : 'bg-green-500');
            seatElement.classList.remove(isLeftSide ? 'bg-green-500' : 'bg-blue-500');
        } else {
            seatElement.classList.remove('bg-blue-500', 'bg-green-500');
        }

        const checkmark = seatElement.querySelector('.text-xs');
        if (checkmark) {
            checkmark.textContent = isSelected ? '✓' : '';
        }
    }

    updateSelectedSeatsCountRetour();
}

function updateSelectedSeatsCountRetour() {
    const count = selectedSeatsRetour.length;
    const countElement = document.getElementById('selectedSeatsCountRetour');
    const nextBtn = document.getElementById('showPassengerInfoBtnRetour');

    countElement.textContent =
        `${count} place${count > 1 ? 's' : ''} sélectionnée${count > 1 ? 's' : ''} / ${selectedNumberOfPlaces} demandée${selectedNumberOfPlaces > 1 ? 's' : ''}`;

    countElement.classList.remove('text-blue-600', 'text-red-500', 'text-green-500');
    if (count === 0) {
        countElement.classList.add('text-gray-600');
    } else if (count < selectedNumberOfPlaces) {
        countElement.classList.add('text-blue-600');
    } else if (count === selectedNumberOfPlaces) {
        countElement.classList.add('text-green-500');
    }

    nextBtn.disabled = (count !== 0 && count !== parseInt(selectedNumberOfPlaces));
    
    if (count === 0) {
        nextBtn.innerHTML = '<i class="fas fa-magic mr-2"></i>Selection Auto & Continuer';
        nextBtn.classList.add('bg-green-600', 'hover:bg-green-700');
        nextBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
    } else {
        nextBtn.innerHTML = '<span class="mr-2">Suivant</span> <i class="fas fa-arrow-right"></i>';
        nextBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
        nextBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
    }
}

function autoAssignSeatsRetour() {
    selectedSeatsRetour = [];
    
    const totalPlaces = parseInt(vehicleDetailsRetour.capacite_total || vehicleDetailsRetour.nombre_place || 70);
    const availableSeats = [];
    
    for (let i = 1; i <= totalPlaces; i++) {
        if (!reservedSeatsRetour.includes(i)) {
            availableSeats.push(i);
        }
    }
    
    if (availableSeats.length < selectedNumberOfPlaces) {
        Swal.fire({
            icon: 'error',
            title: 'Pas assez de places',
            text: 'Il ne reste pas suffisamment de places disponibles pour le retour.',
            confirmButtonColor: '#3b82f6'
        });
        return false;
    }
    
    const shuffled = availableSeats.sort(() => 0.5 - Math.random());
    const selected = shuffled.slice(0, selectedNumberOfPlaces);
    
    selected.forEach(seat => {
        selectedSeatsRetour.push(seat);
        const seatElement = document.querySelector(`[onclick="toggleSeatRetour(${seat})"]`);
        if (seatElement) {
            const isLeftSide = seat <= typeRangeConfig[vehicleDetailsRetour.type_range].placesGauche;
            seatElement.classList.add('bg-blue-600', 'text-white', 'shadow-lg', 'transform', 'scale-110');
            seatElement.classList.remove(isLeftSide ? 'bg-blue-500' : 'bg-green-500');
            seatElement.querySelector('.text-xs').textContent = '✓';
        }
    });
    
    updateSelectedSeatsCountRetour();
    return true;
}

function toggleSelectionModeRetour(mode) {
    selectedSeatsRetour.forEach(seat => {
        const el = document.querySelector(`[onclick="toggleSeatRetour(${seat})"]`);
        if(el) {
            el.classList.remove('bg-blue-600', 'shadow-lg', 'transform', 'scale-110');
            el.querySelector('.text-xs').textContent = '';
            const isLeftSide = seat <= typeRangeConfig[vehicleDetailsRetour.type_range].placesGauche;
            el.classList.add(isLeftSide ? 'bg-blue-500' : 'bg-green-500');
        }
    });
    selectedSeatsRetour = [];
    updateSelectedSeatsCountRetour();
}

function proceedToPassengerInfoFromRetour() {
    if (selectedSeatsRetour.length === 0) {
        const success = autoAssignSeatsRetour();
        if (!success) return;
        
        setTimeout(() => {
            proceedToPassengerInfo();
        }, 500);
    } else {
        proceedToPassengerInfo();
    }
}
    function proceedToPassengerInfo() {
            const formArea = document.getElementById('passengersFormArea');
            formArea.innerHTML = '';

            // Trier les places pour assigner les passagers dans l'ordre
            const sortedSeats = [...selectedSeats].sort((a, b) => a - b);
            
            // AUTOFILL TOGGLE
            if (window.currentUser) {
                const toggleHtml = `
                    <div class="flex items-center justify-end mb-4 bg-blue-50 p-3 rounded-lg border border-blue-100">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="autofillToggle" class="sr-only peer" onchange="toggleAutofill(this.checked)">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#e94f1b]"></div>
                            <span class="ms-3 text-sm font-medium text-gray-900"> <i class="fas fa-user-check me-1"></i> Remplir avec mes informations (Passager 1)</span>
                        </label>
                    </div>
                `;
                formArea.insertAdjacentHTML('beforeend', toggleHtml);
                // AJOUTER CECI POUR ACTIVER LE PASSAGE A L'ETAPE 3
              document.getElementById('step2').classList.add('hidden');
    // On cache AUSSI l'étape Retour (C'est ça qui manque)
    document.getElementById('step2_5').classList.add('hidden');
    
    // On affiche l'étape 3
    document.getElementById('step3').classList.remove('hidden');
    document.getElementById('confirmReservationBtn').disabled = false;
            }

            sortedSeats.forEach((seat, index) => {
                // On met des valeurs vides par défaut, l'autofill se fera via le toggle
                let defaultNom = '';
                let defaultPrenom = '';
                let defaultTel = '';
                let defaultEmail = '';

                // On garde l'index pour savoir si c'est le "Passager principal"
                const isMainPassenger = index === 0;

                const passengerHtml = `
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 transition-all hover:shadow-md" id="passenger_card_${seat}">
                        <h4 class="font-bold text-[#e94f1b] mb-4 flex items-center gap-2">
                            <i class="fas fa-user"></i> Passager pour la place n°${seat}
                            ${isMainPassenger ? '<span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded ml-2">Principal</span>' : ''}
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                                <input type="text" name="passenger_${seat}_nom" required
                                    value="${defaultNom}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all"
                                    placeholder="Nom du passager">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                                <input type="text" name="passenger_${seat}_prenom" required
                                    value="${defaultPrenom}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all"
                                    placeholder="Prénom du passager">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                                <input type="tel" name="passenger_${seat}_telephone" required
                                    value="${defaultTel}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all"
                                    placeholder="Ex: 0700000000">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="passenger_${seat}_email" required
                                    value="${defaultEmail}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all"
                                    placeholder="email@exemple.com">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Contact d'urgence (Nom & Tél)</label>
                                <input type="text" name="passenger_${seat}_urgence" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all"
                                    placeholder="Ex: Jean Dupont - 0500000000">
                            </div>
                        </div>
                    </div>
                `;
                formArea.insertAdjacentHTML('beforeend', passengerHtml);
            });
        }

        // ============================================
        // Retour INTELLIGENT depuis Informations Passagers
        // ============================================
        function backFromPassengerInfo() {
            // Si c'est un aller-retour et que l'utilisateur a sélectionné des places retour,
            // retourner à step2_5 (sélection places retour)
            if (window.userChoseAllerRetour && selectedSeatsRetour.length > 0) {
                document.getElementById('step3').classList.add('hidden');
                document.getElementById('step2_5').classList.remove('hidden');
            } else {
                // Sinon, retourner à step2 (sélection places aller)
                document.getElementById('step3').classList.add('hidden');
                document.getElementById('step2').classList.remove('hidden');
            }
        }

        // ============================================
        // FONCTION 10.3: Autofill Passager 1
        // ============================================
        function toggleAutofill(isChecked) {
            if (!window.currentUser || selectedSeats.length === 0) return;
            
            // Le premier siège sélectionné (trié)
            const sortedSeats = [...selectedSeats].sort((a, b) => a - b);
            const firstSeat = sortedSeats[0];
            
            const user = window.currentUser;
            const fields = [
                { name: 'nom', value: user.name || '' },
                { name: 'prenom', value: user.prenom || '' },
                { name: 'telephone', value: user.contact || '' },
                { name: 'email', value: user.email || '' }
            ];
            
            // Si l'utilisateur a un contact d'urgence (à vérifier si dispo dans le modèle User)
            if (user.contact_urgence) {
                fields.push({ name: 'urgence', value: user.contact_urgence });
            }

            fields.forEach(field => {
                const input = document.querySelector(`[name="passenger_${firstSeat}_${field.name}"]`);
                if (input) {
                    if (isChecked) {
                        input.value = field.value;
                        input.classList.add('bg-orange-50', 'border-orange-300');
                        setTimeout(() => input.classList.remove('bg-orange-50', 'border-orange-300'), 500);
                    } else {
                        input.value = '';
                    }
                }
            });
            
            // Feedback visuel sur la carte
            const card = document.getElementById(`passenger_card_${firstSeat}`);
            if(card) {
                if(isChecked) card.classList.add('ring-2', 'ring-[#e94f1b]', 'ring-offset-2');
                else card.classList.remove('ring-2', 'ring-[#e94f1b]', 'ring-offset-2');
            }
        }

        // ============================================
        // FONCTION 11: Confirmer la réservation
        // ============================================
    async function confirmReservation() {
            const passengers = [];
            const sortedSeats = [...selectedSeats].sort((a, b) => a - b);
            let isValid = true;

            // Validation des champs passagers
            sortedSeats.forEach(seat => {
                const nomEl = document.querySelector(`[name="passenger_${seat}_nom"]`);
                const prenomEl = document.querySelector(`[name="passenger_${seat}_prenom"]`);
                const telephoneEl = document.querySelector(`[name="passenger_${seat}_telephone"]`);
                const emailEl = document.querySelector(`[name="passenger_${seat}_email"]`);
                const urgenceEl = document.querySelector(`[name="passenger_${seat}_urgence"]`);

                const nom = nomEl ? nomEl.value.trim() : '';
                const prenom = prenomEl ? prenomEl.value.trim() : '';
                const telephone = telephoneEl ? telephoneEl.value.trim() : '';
                const email = emailEl ? emailEl.value.trim() : '';
                const urgence = urgenceEl ? urgenceEl.value.trim() : '';

                if (!nom || !prenom || !telephone || !email || !urgence) {
                    isValid = false;
                }

                passengers.push({
                    seat_number: seat,
                    nom, prenom, telephone, email, urgence
                });
            });

            if (!isValid) {
                Swal.fire({ icon: 'warning', title: 'Informations manquantes', text: 'Veuillez remplir toutes les informations pour chaque passager.', confirmButtonColor: '#e94f1b' });
                return;
            }

            // --- CORRECTION DATE ICI ---
            // 1. Définition de la variable
            let dateVoyageFinal = window.outboundDate || window.currentReservationDate;

            // 2. Tentative de récupération depuis le HTML si vide
            if (!dateVoyageFinal && document.getElementById('reservationProgramInfo')) {
                const text = document.getElementById('reservationProgramInfo').innerText;
                const dateMatch = text.match(/\d{2}\/\d{2}\/\d{4}/);
                if (dateMatch) {
                    const [day, month, year] = dateMatch[0].split('/');
                    dateVoyageFinal = `${year}-${month}-${day}`;
                }
            }

            console.log("Date finale pour réservation:", dateVoyageFinal);
            console.log("DEBUG VARIABLES:", {
                'window.outboundDate': window.outboundDate,
                'window.currentReservationDate': window.currentReservationDate,
                'determinedDate': dateVoyageFinal
            });

            if (!dateVoyageFinal) {
                Swal.fire({ icon: 'error', title: 'Erreur', text: 'Impossible de déterminer la date du voyage.', confirmButtonColor: '#e94f1b' });
                return;
            }

            Swal.fire({
                title: 'Confirmer la réservation',
                html: `
                    <div class="text-left">
                        <p class="mb-3">Voulez-vous confirmer la réservation de <strong>${selectedNumberOfPlaces} place(s)</strong> ?</p>
                        <div class="bg-gray-50 p-4 rounded-lg mb-4">
                            <!-- CORRECTION ICI : utilisation de dateVoyageFinal au lieu de dateVoyage -->
                            <p class="font-semibold mb-2">Date : <span class="text-blue-600">${new Date(dateVoyageFinal).toLocaleDateString('fr-FR')}</span></p>
                            <p class="font-semibold mb-2">Places : <span class="text-[#e94f1b]">${sortedSeats.join(', ')}</span></p>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#e94f1b',
                confirmButtonText: 'Oui, continuer',
                cancelButtonText: 'Non'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    
                    let prixUnitaireCalc = 0;

                    if (currentSelectedProgram && currentSelectedProgram.montant_billet) {
                        prixUnitaireCalc = parseInt(currentSelectedProgram.montant_billet);
                    } 
                    else if (window.currentProgramPrice) {
                        prixUnitaireCalc = window.currentProgramPrice;
                        if(window.userChoseAllerRetour) {
                             prixUnitaireCalc = prixUnitaireCalc / 2;
                        }
                    } else {
                        Swal.fire({icon: 'error', title: 'Erreur', text: 'Erreur technique: Prix introuvable. Veuillez rafraîchir la page.'});
                        return;
                    }

                    const multiplier = window.userChoseAllerRetour ? 2 : 1;
                    const montantTotal = prixUnitaireCalc * selectedNumberOfPlaces * multiplier;
                    const userSolde = {{ auth()->check() ? (auth()->user()->solde ?? 0) : 0 }};
                    let paymentMethod = 'cinetpay';

                    const choiceResult = await Swal.fire({
                         title: 'Mode de paiement',
                         html: `
                             <div class="flex flex-col gap-4 text-center">
                                 <div class="bg-gray-50 p-3 rounded-lg">
                                     <p class="text-gray-600 text-sm">Total à payer</p>
                                     <p class="text-2xl font-bold text-[#e94f1b]">${new Intl.NumberFormat('fr-FR').format(montantTotal)} FCFA</p>
                                 </div>
                                 <div class="text-sm text-gray-500">Votre solde: ${new Intl.NumberFormat('fr-FR').format(userSolde)} FCFA</div>
                             </div>
                         `,
                         icon: 'info',
                         showCancelButton: true,
                         showDenyButton: true,
                         confirmButtonText: `Mon Compte Solde`,
                         denyButtonText: `Mobile Money (CinetPay)`,
                         confirmButtonColor: '#e94f1b',
                         denyButtonColor: '#2dce89',
                         cancelButtonText: 'Annuler',
                         didOpen: () => {
                             if (userSolde < montantTotal) {
                                  const confirmBtn = Swal.getConfirmButton();
                                  confirmBtn.disabled = true;
                                  confirmBtn.style.opacity = 0.5;
                                  confirmBtn.innerHTML += ' (Solde insuffisant)';
                             }
                         }
                    });

                    if (choiceResult.isConfirmed) paymentMethod = 'wallet';
                    else if (choiceResult.isDenied) paymentMethod = 'cinetpay';
                    else return;

                    Swal.fire({ title: 'Traitement...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                    try {
                        const response = await fetch("/user/booking/reservation", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                           body: JSON.stringify({
    programme_id: currentProgramId,
    seats: sortedSeats,
    seats_retour: selectedSeatsRetour.length > 0 ? selectedSeatsRetour.sort((a, b) => a - b) : [], // AJOUTÉ
    nombre_places: selectedNumberOfPlaces,
    date_voyage: dateVoyageFinal,
    is_aller_retour: window.userChoseAllerRetour,
    date_retour: window.selectedReturnDate,
    passagers: passengers,
    payment_method: paymentMethod
})
                        });

                        const data = await response.json();

                        if (data.success) {
                            if (data.wallet_payment) {
                                window.location.href = data.redirect_url;
                            } else if (data.payment_url) {
                                CinetPay.setConfig({
                                    apikey: '{{ $cinetpay_api_key }}',
                                    site_id: '{{ $cinetpay_site_id }}',
                                    notify_url: '{{ route("payment.notify") }}',
                                    mode: '{{ $cinetpay_mode }}'
                                });
                                CinetPay.getCheckout({
                                    transaction_id: data.transaction_id,
                                    amount: data.amount,
                                    currency: data.currency,
                                    channels: 'ALL',
                                    description: data.description,
                                    customer_name: data.customer_name,
                                    customer_surname: data.customer_surname,
                                    customer_email: data.customer_email,
                                    customer_phone_number: data.customer_phone_number,
                                    customer_address: 'Abidjan',
                                    customer_city: 'Abidjan',
                                    customer_country: 'CI',
                                    customer_state: 'Abidjan',
                                    customer_zip_code: '00225',
                                });
                                CinetPay.waitResponse(function (response) {
                                    if (response.status === "ACCEPTED") {
                                        window.location.href = "{{ route('payment.return') }}?transaction_id=" + data.transaction_id;
                                    } else {
                                        window.location.reload();
                                    }
                                });
                            }
                        } else {
                            throw new Error(data.message || 'Erreur');
                        }
                    } catch (error) {
                        Swal.fire({ icon: 'error', title: 'Erreur', text: error.message });
                    }
                }
            });
        }

        // ============================================
        // FONCTION 12: Initialisation au chargement
        // ============================================
        document.addEventListener('DOMContentLoaded', function () {
            // Gestion du click sur tous les boutons détails véhicule
            document.querySelectorAll('.vehicle-details-btn').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const vehicleId = this.getAttribute('data-vehicle-id');
                    if (vehicleId) {
                        showVehicleDetails(parseInt(vehicleId));
                    }
                });
            });

            // Empêcher la fermeture du modal en cliquant à l'extérieur
            const modal = document.getElementById('reservationModal');
            if (modal) {
                modal.addEventListener('click', function (e) {
                    if (e.target === this) {
                        closeReservationModal();
                    }
                });
            }

            // Touche Échap pour fermer le modal
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                    closeReservationModal();
                }
            });
        });

       // ============================================
        // MODAL STEP-BY-STEP NAVIGATION STATE
        // ============================================
        let modalCurrentStep = 1;
        let selectedRoute = null;
        let selectedDate = null;

        window.toggleProgramsList = function() {
            const container = document.getElementById('inlineProgramsList');
            if (container.classList.contains('hidden')) {
                container.classList.remove('hidden');
                loadRoutesForSelection();
                // Scroll to container
                container.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                container.classList.add('hidden');
            }
        };


        async function loadRoutesForSelection() {
            const container = document.getElementById('programsListContent');
            const title = document.getElementById('inlineListTitle');
            const subtitle = document.getElementById('inlineListSubtitle');
            
            // Mettre à jour le titre
            if(title) title.textContent = 'Choisir votre ligne';
            if(subtitle) subtitle.textContent = 'Sélectionnez votre trajet pour voir les disponibilités';
            
            container.innerHTML = `<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-4xl text-[#e94f1b]"></i><p class="mt-2 text-gray-500">Chargement des lignes...</p></div>`;

            try {
                const response = await fetch('{{ route("api.grouped-routes") }}');
                const data = await response.json();
                
                if (data.success && data.routes.length > 0) {
                    renderRoutesForSelection(data.routes);
                } else {
                    container.innerHTML = `<div class="text-center py-8"><i class="fas fa-bus text-gray-300 text-4xl"></i><p class="mt-2 text-gray-500">Aucune ligne disponible pour le moment.</p></div>`;
                }
            } catch (error) {
                console.error('Erreur:', error);
                container.innerHTML = `<div class="text-center py-8 text-red-500"><i class="fas fa-exclamation-triangle text-4xl mb-2"></i><p>Impossible de charger les lignes.</p></div>`;
            }
        }

        function renderRoutesForSelection(routes) {
            const container = document.getElementById('programsListContent');
            container.innerHTML = `<div class="grid grid-cols-1 md:grid-cols-2 gap-4">` + routes.map(route => {
                const prixDisplay = route.prix_min === route.prix_max 
                    ? `${Number(route.prix_min).toLocaleString('fr-FR')} FCFA`
                    : `À partir de ${Number(route.prix_min).toLocaleString('fr-FR')} FCFA`;
                
                return `
                    <div onclick="selectRouteAndLaunchFlow(${JSON.stringify(route).replace(/"/g, '&quot;')})" 
                         class="bg-gradient-to-br from-blue-50 to-white p-5 rounded-xl border-2 border-blue-100 hover:border-[#e94f1b] cursor-pointer transition-all duration-200 hover:shadow-lg group">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-10 h-10 rounded-full bg-[#e94f1b]/10 flex items-center justify-center">
                                    <i class="fas fa-bus text-[#e94f1b]"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-600">${route.compagnie?.name || 'Compagnie'}</span>
                            </div>
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-bold">
                                ${route.total_voyages} voyage(s)
                            </span>
                        </div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex-1">
                                <p class="font-bold text-gray-900">${route.point_depart}</p>
                            </div>
                            <div class="flex flex-col items-center">
                                <i class="fas fa-arrow-right text-[#e94f1b]"></i>
                            </div>
                            <div class="flex-1 text-right">
                                <p class="font-bold text-gray-900">${route.point_arrive}</p>
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                            <span class="text-[#e94f1b] font-bold">${prixDisplay}</span>
                            <span class="text-gray-400 group-hover:text-[#e94f1b] transition-colors">
                                <i class="fas fa-arrow-circle-right text-xl"></i>
                            </span>
                        </div>
                    </div>
                `;
            }).join('') + `</div>`;
        }

        // Nouvelle fonction: dès qu'on sélectionne une ligne, demander la date de départ
        function selectRouteAndLaunchFlow(route) {
            // Cacher la liste inline pour faire place à la suite ou la laisser ? 
            // UX: On peut la laisser visible ou la cacher. Cachons-la pour focus.
            // document.getElementById('inlineProgramsList').classList.add('hidden');
            
            // Afficher sélecteur de date de départ
            showDepartureDateSelection(route);
        }

        // Générateur de calendrier mensuel
        function generateMonthlyCalendar(currentMonth, currentYear, minDate, maxDate, colorScheme = 'orange') {
            const firstDay = new Date(currentYear, currentMonth, 1);
            const lastDay = new Date(currentYear, currentMonth + 1, 0);
            const startDay = firstDay.getDay(); // 0 = dimanche
            const daysInMonth = lastDay.getDate();
            
            // Ajuster pour que lundi soit le premier jour (0 = lundi, 6 = dimanche)
            const startDayAdjusted = startDay === 0 ? 6 : startDay - 1;
            
            const colors = {
                orange: {
                    border: 'border-orange-200',
                    bg: 'bg-orange-50',
                    hoverBorder: 'hover:border-orange-500',
                    hoverBg: 'hover:bg-orange-100',
                    text: 'text-orange-700',
                    disabled: 'bg-gray-100 text-gray-400 cursor-not-allowed'
                },
                purple: {
                    border: 'border-purple-200',
                    bg: 'bg-purple-50',
                    hoverBorder: 'hover:border-purple-500',
                    hoverBg: 'hover:bg-purple-100',
                    text: 'text-purple-700',
                    disabled: 'bg-gray-100 text-gray-400 cursor-not-allowed'
                }
            };
            
            const scheme = colors[colorScheme];
            
            let html = '<div class="calendar-grid">';
            
            // En-tête avec jours de la semaine
            html += '<div class="grid grid-cols-7 gap-1 mb-2">';
            const dayNames = ['Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa', 'Di'];
            dayNames.forEach(day => {
                html += `<div class="text-center text-xs font-semibold text-gray-600 py-1">${day}</div>`;
            });
            html += '</div>';
            
            // Grille de dates
            html += '<div class="grid grid-cols-7 gap-1">';
            
            // Cases vides avant le premier jour
            for (let i = 0; i < startDayAdjusted; i++) {
                html += '<div></div>';
            }
            
            // Jours du mois
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(currentYear, currentMonth, day);
                const dateStr = date.toISOString().split('T')[0];
                const isDisabled = date < minDate || date > maxDate;
                
                if (isDisabled) {
                    html += `
                        <div class="border ${scheme.disabled} rounded p-2 text-center">
                            <span class="text-sm">${day}</span>
                        </div>
                    `;
                } else {
                    html += `
                        <div class="calendar-day-btn border-2 ${scheme.border} ${scheme.bg} rounded p-2 cursor-pointer ${scheme.hoverBorder} ${scheme.hoverBg} transition-all text-center"
                             data-date="${dateStr}">
                            <span class="font-bold ${scheme.text} text-sm">${day}</span>
                        </div>
                    `;
                }
            }
            
            html += '</div></div>';
            return html;
        }

        // Sélection de la date de départ pour "Voir tous les voyages" avec calendrier mensuel
        function showDepartureDateSelection(route) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            // Calculer demain pour le début des réservations possibles
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            
            // Date max = date_fin du programme (31/12/2026 par défaut ou depuis route)
            const maxDate = route.date_fin ? new Date(route.date_fin) : new Date(today.getFullYear(), 11, 31);
            
            let currentMonth = tomorrow.getMonth();
            let currentYear = tomorrow.getFullYear();
            
            function updateCalendar() {
                const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                                   'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                
                // Utiliser tomorrow comme minDate
                const calendarHtml = generateMonthlyCalendar(currentMonth, currentYear, tomorrow, maxDate, 'orange');
                
                Swal.update({
                    html: `
                        <div class="text-left space-y-4">
                            <div class="bg-orange-50 p-3 rounded-lg border border-orange-200">
                                <p class="font-bold text-gray-800">${route.point_depart} → ${route.point_arrive}</p>
                                <p class="text-sm text-gray-600">Sélectionnez votre date de départ</p>
                            </div>
                            
                            <!-- Option rapide Demain -->
                            <div class="flex justify-center">
                                <button id="btnSelectTomorrow" class="flex items-center gap-2 bg-orange-100 text-orange-700 px-4 py-2 rounded-lg font-bold hover:bg-orange-200 transition-colors border border-orange-300">
                                    <i class="fas fa-magic"></i>
                                    <span>Sélectionner demain (${tomorrow.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' })})</span>
                                </button>
                            </div>

                            <!-- Navigation mois -->
                            <div class="flex items-center justify-between bg-gray-50 p-2 rounded">
                                <button id="prevMonth" class="px-3 py-1 bg-white border rounded hover:bg-gray-100">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <span class="font-bold text-gray-800">${monthNames[currentMonth]} ${currentYear}</span>
                                <button id="nextMonth" class="px-3 py-1 bg-white border rounded hover:bg-gray-100">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            
                            <!-- Calendrier -->
                            <div class="calendar-container">
                                ${calendarHtml}
                            </div>
                        </div>
                    `
                });
                
                // Réattacher les événements
                attachCalendarEvents();
            }
            
            function attachCalendarEvents() {
                // Bouton Demain
                const btnTomorrow = document.getElementById('btnSelectTomorrow');
                if (btnTomorrow) {
                    btnTomorrow.addEventListener('click', () => {
                        const tomorrowStr = tomorrow.toISOString().split('T')[0];
                        Swal.close();
                        loadSchedulesAndLaunchFlow(route, tomorrowStr);
                    });
                }
                // Navigation mois précédent
                const prevBtn = document.getElementById('prevMonth');
                if (prevBtn) {
                    prevBtn.addEventListener('click', () => {
                        if (currentMonth === 0) {
                            currentMonth = 11;
                            currentYear--;
                        } else {
                            currentMonth--;
                        }
                        // Ne pas aller avant le mois actuel (de 'tomorrow')
                        const checkDate = new Date(currentYear, currentMonth, 1);
                        // comparer avec tomorrow
                        if (checkDate >= new Date(tomorrow.getFullYear(), tomorrow.getMonth(), 1)) {
                            updateCalendar();
                        } else {
                            currentMonth = tomorrow.getMonth();
                            currentYear = tomorrow.getFullYear();
                        }
                    });
                }
                
                // Navigation mois suivant
                const nextBtn = document.getElementById('nextMonth');
                if (nextBtn) {
                    nextBtn.addEventListener('click', () => {
                        if (currentMonth === 11) {
                            currentMonth = 0;
                            currentYear++;
                        } else {
                            currentMonth++;
                        }
                        // Ne pas aller après le mois de maxDate
                        const checkDate = new Date(currentYear, currentMonth, 1);
                        if (checkDate <= new Date(maxDate.getFullYear(), maxDate.getMonth(), 1)) {
                            updateCalendar();
                        } else {
                            currentMonth = maxDate.getMonth();
                            currentYear = maxDate.getFullYear();
                        }
                    });
                }
                
                // Sélection de date
                const dayBtns = document.querySelectorAll('.calendar-day-btn');
                dayBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const selectedDate = this.dataset.date;
                        Swal.close();
                        loadSchedulesAndLaunchFlow(route, selectedDate);
                    });
                });
            }
            
            // Ouvrir le modal initial
            Swal.fire({
                title: '<i class="fas fa-calendar-day text-orange-600"></i> Date de départ',
                html: '', // Sera rempli par updateCalendar()
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonText: 'Retour',
                customClass: { popup: 'rounded-2xl' },
                width: '600px',
                didOpen: () => {
                    updateCalendar();
                }
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    document.getElementById('programsListModal').classList.remove('hidden');
                }
            });
        }

        // Charger les horaires et lancer le flux unifié
        async function loadSchedulesAndLaunchFlow(route, selectedDate) {
            // Afficher un loader
            Swal.fire({
                title: 'Chargement des disponibilités...',
                text: `${route.point_depart} → ${route.point_arrive}`,
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            
            try {
                // 1. Charger les horaires ALLER pour la date sélectionnée
                const paramsAller = new URLSearchParams({
                    point_depart: route.point_depart,
                    point_arrive: route.point_arrive,
                    date: selectedDate,
                    compagnie_id: route.compagnie_id // CRUCIAL : Filtrer par compagnie
                });
                const responseAller = await fetch('{{ route("api.route-schedules") }}?' + paramsAller);
                const dataAller = await responseAller.json();
                
                // 2. Charger les horaires RETOUR pour vérifier la disponibilité
                const paramsRetour = new URLSearchParams({
                    original_arrive: route.point_arrive,
                    original_depart: route.point_depart,
                    min_date: selectedDate
                });
                const responseRetour = await fetch('{{ route("api.return-trips") }}?' + paramsRetour);
                const dataRetour = await responseRetour.json();
                
                Swal.close();
                
                // 3. Construire l'objet routeData pour le modal unifié
                const routeData = {
                    ...route, // Inclut date_fin si présent dans route
                    aller_horaires: dataAller.success ? dataAller.schedules : [],
                    has_retour: (dataRetour.success && dataRetour.return_trips && dataRetour.return_trips.length > 0),
                    retour_horaires: (dataRetour.success ? dataRetour.return_trips : []),
                    compagnie: route.compagnie?.name || 'Compagnie',
                    montant_billet: dataAller.schedules && dataAller.schedules.length > 0 ? dataAller.schedules[0].montant_billet : (route.prix_min || 0),
                    date_fin: route.date_fin || dataAller.schedules?.[0]?.date_fin || null // S'assurer que date_fin est présent
                };
                
                // 4. Lancer la sélection de l'heure (Départ)
                if (typeof window.showRouteDepartureTimes === 'function') {
                    window.showRouteDepartureTimes(routeData, selectedDate, window.userWantsAllerRetour);
                } else {
                    console.error('showRouteDepartureTimes non disponible');
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur technique',
                        text: 'Impossible de charger le système de réservation.'
                    });
                }
                
            } catch (error) {
                console.error('Erreur lors du chargement:', error);
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Impossible de charger les horaires. Veuillez réessayer.'
                }).then(() => {
                    // Retour à la sélection de date
                    showDepartureDateSelection(route);
                });
            }
        }

        async function loadSchedules(route, date) {
            const container = document.getElementById('programsListContent');
            container.innerHTML = `<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-4xl text-[#e94f1b]"></i><p class="mt-2 text-gray-500">Chargement des horaires...</p></div>`;

            try {
                const params = new URLSearchParams({
                    point_depart: route.point_depart,
                    point_arrive: route.point_arrive,
                    date: date
                });
                const response = await fetch('{{ route("api.route-schedules") }}?' + params);
                const data = await response.json();
                
                if (data.success && data.schedules.length > 0) {
                    renderSchedulesList(data.schedules);
                } else {
                    container.innerHTML = `<div class="text-center py-8"><i class="fas fa-clock text-gray-300 text-4xl"></i><p class="mt-2 text-gray-500">Aucun horaire disponible.</p></div>`;
                }
            } catch (error) {
                console.error('Erreur:', error);
                container.innerHTML = `<div class="text-center py-8 text-red-500"><i class="fas fa-exclamation-triangle text-4xl mb-2"></i><p>Impossible de charger les horaires.</p></div>`;
            }
        }

        function renderSchedulesList(schedules) {
            const container = document.getElementById('programsListContent');
            container.innerHTML = `<div class="space-y-3">` + schedules.map(prog => {
                return `
                    <div class="bg-white p-4 rounded-xl border border-gray-200 hover:border-[#e94f1b] transition-all duration-200 hover:shadow-md">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-[#e94f1b]">${prog.heure_depart}</p>
                                    <p class="text-xs text-gray-500">Départ</p>
                                </div>
                                <div class="flex items-center gap-2 text-gray-400">
                                    <div class="w-8 h-0.5 bg-gray-300"></div>
                                    <i class="fas fa-bus text-sm"></i>
                                    <div class="w-8 h-0.5 bg-gray-300"></div>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-gray-700">${prog.heure_arrive}</p>
                                    <p class="text-xs text-gray-500">Arrivée</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <p class="text-lg font-bold text-[#e94f1b]">${Number(prog.montant_billet).toLocaleString('fr-FR')} FCFA</p>
                                    <p class="text-xs text-gray-500">${prog.compagnie?.name || ''}</p>
                                </div>
                                <button onclick="toggleProgramsList(); initiateReservationProcess(${prog.id}, '${selectedDate}')" 
                                        class="bg-[#e94f1b] text-white px-5 py-2.5 rounded-lg font-bold hover:bg-orange-600 transition-colors flex items-center gap-2">
                                    <i class="fas fa-ticket-alt"></i>
                                    <span>Réserver</span>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('') + `</div>`;
        }

       function selectProgramFromList(program) {
            // Utiliser la date de recherche si disponible
            const searchDate = new URLSearchParams(window.location.search).get('date_depart');
            initiateReservationProcess(program.id, searchDate);
        }


        // ============================================
        // FONCTION 14: Gestion modale sélection date
        // ============================================
         function openDateSelectionModal(program) {
            currentSelectedProgram = program;
            document.getElementById('dateSelectionModal').classList.remove('hidden');
            // Logique de remplissage date similaire à populateReturnDateSelect mais pour l'aller...
            const select = document.getElementById('recurrenceDateSelect');
            select.innerHTML = '<option value="">Chargement...</option>';
            
            // Jours Aller
            let allowedDays = [];
            try { allowedDays = JSON.parse(program.jours_recurrence || '[]'); } catch(e){}
            allowedDays = allowedDays.map(d => d.toLowerCase());
            
            const daysMap = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
            const dates = [];
            let current = new Date();
             current.setDate(current.getDate() + 1); // Commencer à demain
            let count = 0;
            
            while(count < 10 && dates.length < 10) {
                 const dayName = daysMap[current.getDay()];
                 if (allowedDays.includes(dayName)) {
                     // Check date fin
                     let isValid = true;
                     if(program.date_fin_programmation && current.toISOString().split('T')[0] > program.date_fin_programmation) isValid = false;
                     if(isValid) {
                         dates.push({
                            val: current.toISOString().split('T')[0],
                            txt: current.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
                        });
                        count++;
                     }
                 }
                 current.setDate(current.getDate() + 1);
                 if(count > 100) break; // Sécurité
            }
            
            select.innerHTML = '<option value="">Choisir une date...</option>';
            dates.forEach(d => select.innerHTML += `<option value="${d.val}">${d.txt}</option>`);
        }
       function closeDateSelectionModal() { document.getElementById('dateSelectionModal').classList.add('hidden'); }
        function confirmDateSelection() {
            const date = document.getElementById('recurrenceDateSelect').value;
            if(!date) {
                document.getElementById('recurrenceDateError').classList.remove('hidden');
                return;
            }
            document.getElementById('dateSelectionModal').classList.add('hidden');
            
            // Si on avait pas encore choisi A/R et qu'il est dispo, on le propose maintenant
            if(currentSelectedProgram.is_aller_retour && window.userChoseAllerRetour === false) {
                 openAllerRetourConfirmModal(currentSelectedProgram, date);
            } else {
                 openReservationModal(currentSelectedProgram.id, date);
            }
        }

        // Fonction pour afficher les détails (places disponibles)
        async function openDetailsModal(btn) {
            const route = JSON.parse(btn.dataset.route);
            const dateDepart = btn.dataset.date;
            
            Swal.fire({
                title: 'Chargement des détails...',
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                // Fetch Aller availability
                const paramsAller = new URLSearchParams({
                    point_depart: route.point_depart,
                    point_arrive: route.point_arrive,
                    date: dateDepart
                });
                const responseAller = await fetch('{{ route("api.route-schedules") }}?' + paramsAller);
                const dataAller = await responseAller.json();

                // Build HTML
                let html = `<div class="text-left">`;
                
                // Section Aller
                html += `<h3 class="font-bold text-lg text-[#e94f1b] mb-3 uppercase border-b pb-2">Aller : ${dateDepart}</h3>`;
                if (dataAller.success && dataAller.schedules.length > 0) {
                    html += buildSchedulesTable(dataAller.schedules, dateDepart);
                } else {
                    html += `<p class="text-gray-500 italic">Aucun horaire disponible pour cette date.</p>`;
                }

                // Section Retour (si applicable)
                if (route.has_retour) {
                    html += `<h3 class="font-bold text-lg text-blue-600 mt-6 mb-3 uppercase border-b pb-2">Retour (Aperçu)</h3>`;
                    
                    const paramsRetour = new URLSearchParams({
                        point_depart: route.point_arrive,
                        point_arrive: route.point_depart,
                        date: dateDepart
                    });
                    const responseRetour = await fetch('{{ route("api.route-schedules") }}?' + paramsRetour);
                    const dataRetour = await responseRetour.json();
                    
                    if (dataRetour.success && dataRetour.schedules.length > 0) {
                        html += buildSchedulesTable(dataRetour.schedules, dateDepart);
                    } else {
                         html += `<p class="text-gray-500 italic">Aucun horaire retour trouvé pour cette date.</p>`;
                    }
                }

                html += `</div>`;

                Swal.fire({
                    title: `Détails du voyage`,
                    html: html,
                    width: '800px',
                    showConfirmButton: false,
                    showCloseButton: true,
                    customClass: {
                        popup: 'rounded-xl'
                    }
                });

            } catch (error) {
                console.error(error);
                Swal.fire('Erreur', 'Impossible de charger les détails.', 'error');
            }
        }

        function buildSchedulesTable(schedules, dateVoyage) {
            let table = `
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-4 py-2">Départ</th>
                                <th class="px-4 py-2">Arrivée</th>
                                <th class="px-4 py-2">Véhicule</th>
                                <th class="px-4 py-2 text-center">Places</th>
                                <th class="px-4 py-2 text-center">Statut</th>
                                <th class="px-4 py-2 text-center">Voir</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            schedules.forEach(sch => {
                const isFull = sch.places_disponibles <= 0;
                const statusClass = isFull ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';
                const statusText = isFull ? 'Complet' : 'Disponible';
                
                table += `
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-4 py-2 font-bold text-gray-900">${sch.heure_depart}</td>
                        <td class="px-4 py-2">${sch.heure_arrive}</td>
                        <td class="px-4 py-2">${sch.vehicule}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="font-bold text-gray-900">${sch.places_disponibles}</span> / ${sch.places_totales}
                        </td>
                        <td class="px-4 py-2 text-center">
                            <span class="${statusClass} px-2 py-1 rounded-full text-xs font-bold">${statusText}</span>
                        </td>
                        <td class="px-4 py-2 text-center">
                             <button type="button" onclick="showVehicleDetails('${sch.vehicule_id}', '${sch.id}', '${dateVoyage}')" class="text-blue-600 hover:text-blue-800 bg-blue-100 p-2 rounded-full transition-colors">
                                <i class="fas fa-eye"></i>
                             </button>
                        </td>
                    </tr>
                `;
            });
            
            table += `</tbody></table></div>`;
            return table;
        }
    </script>


 <!-- Modal Confirmation Aller-Retour -->
    <div id="allerRetourConfirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[75] flex items-center justify-center">
        <div class="relative w-[450px] mx-auto p-6 border shadow-2xl rounded-2xl bg-white">
            <div class="flex flex-col gap-4">
                <!-- En-tête -->
                <div class="text-center border-b pb-4">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fas fa-bus text-[#e94f1b] text-2xl"></i></div>
                    <h3 class="text-xl font-bold text-gray-900">Ce programme propose un aller-retour</h3>
                    <p class="text-sm text-gray-500 mt-1">Choisissez le type de voyage que vous souhaitez</p>
                </div>
                
                <!-- Infos du trajet -->
                <div id="allerRetourTripInfo" class="py-2">
                    <!-- Contenu injecté par JS -->
                </div>
                
                <!-- Choix du type de voyage -->
                <div class="py-2">
                    <label for="allerRetourChoice" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-route me-1"></i> Type de voyage
                    </label>
                    <div class="relative">
                        <select id="allerRetourChoice" onchange="onAllerRetourChoiceChange()" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-[#e94f1b] appearance-none bg-white font-medium text-gray-700">
                            <option value="aller_simple">🚌 Aller Simple</option>
                            <option value="aller_retour">🔄 Aller-Retour</option>
                        </select>
                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Affichage du prix dynamique -->
                <div id="allerRetourPriceDisplay" class="text-center">
                    <!-- Contenu injecté par JS -->
                </div>
                
                <!-- Sélection date retour (pour récurrents + aller-retour) -->
                <div id="returnDateSection" class="hidden py-2 border-t">
                    <label for="returnDateSelect" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-plane-arrival text-blue-500 me-1"></i> Date de retour
                    </label>
                    <div class="relative">
                        <select id="returnDateSelect" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 appearance-none bg-white"></select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t pt-4">
                    <button onclick="closeAllerRetourConfirmModal()" class="px-5 py-2 bg-gray-100 rounded-lg font-bold">Annuler</button>
                    <button onclick="confirmAllerRetour()" class="px-5 py-2 bg-[#e94f1b] text-white rounded-lg font-bold">Continuer</button>
                </div>
            </div>
        </div>
    </div>
     <!-- Modal Date Selection (Récurrent) -->
    <div id="dateSelectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[70] flex items-center justify-center">
        <div class="relative w-96 mx-auto p-6 border shadow-2xl rounded-2xl bg-white">
            <div class="flex flex-col gap-4">
                <div class="border-b pb-4">
                    <h3 class="text-xl font-bold text-gray-900">Choisir une date de voyage</h3>
                    <p class="text-sm text-gray-500 mt-1">Ce programme est récurrent.</p>
                </div>
                
                <div class="py-4">
                    <label for="recurrenceDateSelect" class="block text-sm font-medium text-gray-700 mb-2">Sélectionnez une date parmi les prochains jours disponibles :</label>
                    <div class="relative">
                        <select id="recurrenceDateSelect" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent appearance-none bg-white">
                            <!-- Options générées par JS -->
                        </select>
                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                    <p id="recurrenceDateError" class="text-red-500 text-xs mt-1 hidden">Veuillez choisir une date.</p>
                </div>

                <div class="flex justify-end gap-3 border-t pt-4">
                    <button onclick="closeDateSelectionModal()" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition-colors">Annuler</button>
                    <button onclick="confirmDateSelection()" class="px-5 py-2.5 bg-[#e94f1b] text-white rounded-xl font-bold hover:bg-orange-600 transition-colors shadow-lg hover:shadow-xl">Confirmer</button>
                </div>
            </div>
        </div>
    </div>
@endsection