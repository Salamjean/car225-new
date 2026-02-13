@extends('user.layouts.template')
@section('content')
    <div class="min-h-screen bg-gradient-to-br from-white to-blue-50 py-4 sm:py-6 lg:py-8">
        <div class="w-full px-3 sm:px-4 lg:px-6">

            <!-- Formulaire de recherche -->
            <div class="mb-6 sm:mb-8">
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 border border-gray-100">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">Rechercher un voyage</h2>

                  <form action="{{ route('reservation.create') }}" method="GET" id="search-form">
                        <!-- Modification ici : passage Ã  lg:grid-cols-12 pour une ligne parfaite -->
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 sm:gap-4 items-end">
                            
                            <!-- Point de dÃ©part (Prend 3 colonnes sur 12) -->
                            <div class="relative lg:col-span-3">
                                <label for="point_depart" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-map-marker-alt text-[#e94f1b] mr-2"></i>Point de dÃ©part
                                </label>
                                <div class="relative">
                                    <input type="text" id="point_depart" name="point_depart"
                                        value="{{ $searchParams['point_depart'] ?? '' }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 pl-12"
                                        placeholder="Ville ou gare de dÃ©part" required>
                                </div>
                            </div>

                            <!-- Bouton d'inversion (Prend 1 colonne sur 12, centrÃ©) -->
                            <div class="lg:col-span-1 flex items-end justify-center pb-2">
                                <button type="button" onclick="swapLocations()" 
                                    class="w-10 h-10 bg-[#e94f1b] text-white rounded-full hover:bg-orange-600 transition-all duration-300 transform hover:scale-110 shadow-lg flex items-center justify-center"
                                    title="Inverser dÃ©part/arrivÃ©e">
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                            </div>

                            <!-- Point d'arrivÃ©e (Prend 3 colonnes sur 12) -->
                            <div class="relative lg:col-span-3">
                                <label for="point_arrive" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-flag text-green-500 mr-2"></i>Point d'arrivÃ©e
                                </label>
                                <div class="relative">
                                    <input type="text" id="point_arrive" name="point_arrive"
                                        value="{{ $searchParams['point_arrive'] ?? '' }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 pl-12"
                                        placeholder="Ville ou gare d'arrivÃ©e" required>
                                </div>
                            </div>

                            <!-- Date de dÃ©part (Prend 2 colonnes sur 12) -->
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
             <!-- Alerte si l'heure recherchÃ©e n'existe pas -->
            @if (isset($timeMismatch) && $timeMismatch && isset($availableTimesMessage))
                <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-xl">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mt-1"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-yellow-800 mb-1">Heure non disponible</h4>
                            <p class="text-yellow-700">{{ $availableTimesMessage }}</p>
                            <p class="text-sm text-yellow-600 mt-2">Nous affichons quand mÃªme les programmes disponibles pour cette route.</p>
                        </div>
                    </div>
                </div>
            @endif
             <!-- RÃ©sultats de recherche - Routes groupÃ©es -->
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
                                                    <div class="flex flex-col">
                                                        <span class="font-semibold text-gray-700">{{ $route->point_depart }}</span>
                                                        @if($route->gare_depart)
                                                            <span class="text-xs text-gray-400">
                                                                <i class="fas fa-building mr-1"></i>{{ $route->gare_depart->nom_gare }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <i class="fas fa-long-arrow-alt-right text-[#e94f1b] mx-2"></i>
                                                    <div class="flex flex-col">
                                                        <span class="font-semibold text-gray-700">{{ $route->point_arrive }}</span>
                                                        @if($route->gare_arrivee)
                                                            <span class="text-xs text-gray-400">
                                                                <i class="fas fa-building mr-1"></i>{{ $route->gare_arrivee->nom_gare }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="mt-2 text-xs font-bold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full inline-block">
                                                    <i class="fas fa-hourglass-half mr-1"></i>{{ $route->durer_parcours }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Horaires & Occupation (Liste dÃ©filante ou grille) -->
                                        <div class="flex-1">
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                                                <i class="fas fa-clock text-[#e94f1b]"></i> Horaires & DisponibilitÃ©
                                            </p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($route->aller_horaires as $horaire)
                                                    @php
                                                        $occupancyRate = ($horaire['reserved_count'] / $horaire['total_seats']) * 100;
                                                        $statusClass = $horaire['reserved_count'] >= $horaire['total_seats'] ? 'bg-red-50 border-red-200 text-red-700' : 
                                                                      ($occupancyRate > 80 ? 'bg-orange-50 border-orange-200 text-orange-700' : 'bg-green-50 border-green-200 text-green-700');
                                                    @endphp
                                                    <div onclick="showVehicleDetails('{{ $horaire['vehicule_id'] ?? 0 }}', '{{ $horaire['id'] }}', '{{ $searchParams['date_depart'] }}', '{{ substr($horaire['heure_depart'], 0, 5) }}')"
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
                                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">Prix Ã  partir de</p>
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
                                                    'gare_depart' => $route->gare_depart,
                                                    'gare_arrivee' => $route->gare_arrivee,
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
                                                <span>RÃ‰SERVER</span>
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
                <!-- Aucun rÃ©sultat -->
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-orange-100 to-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-route text-3xl text-[#e94f1b]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun programme trouvÃ©</h3>
                    <p class="text-gray-600 mb-6">Essayez d'ajuster vos critÃ¨res de recherche.</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Modal SÃ©lection Gare -->
    <div id="gareSelectionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">SÃ©lectionnez votre gare</h2>
                <button onclick="closeGareSelectionModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <div class="mb-6">
                <p class="text-sm text-gray-600 mb-4">De quelle gare souhaitez-vous partir ?</p>
                <div id="gareOptions" class="space-y-3">
                    <!-- Options gÃ©nÃ©rÃ©es par JavaScript -->
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Type de Voyage -->
    <div id="tripTypeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-bus text-[#e94f1b]"></i>
                        Type de voyage
                    </h2>
                    <p id="tripRouteInfo" class="text-sm text-gray-600 mt-2"></p>
                </div>
                <button onclick="closeTripTypeModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-1 gap-4 mt-6">
                <!-- Aller Simple -->
                <button onclick="selectTripType('simple')" 
                    class="p-6 border-2 border-gray-200 rounded-xl hover:border-[#e94f1b] hover:bg-orange-50 transition-all duration-300 text-left group">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center group-hover:bg-[#e94f1b] transition-colors">
                                <i class="fas fa-arrow-right text-[#e94f1b] text-xl group-hover:text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-900">Aller Simple</h3>
                                <p id="simplePrice" class="text-sm text-gray-600">--  FCFA</p>
                            </div>
                        </div>
                    </div>
                </button>
                
                <!-- Aller-Retour -->
                <button onclick="selectTripType('round')" id="roundTripBtn"
                    class="p-6 border-2 border-gray-200 rounded-xl hover:border-[#e94f1b] hover:bg-orange-50 transition-all duration-300 text-left group disabled:opacity-50 disabled:cursor-not-allowed">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-[#e94f1b] transition-colors">
                                <i class="fas fa-exchange-alt text-blue-500 text-xl group-hover:text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-900">Aller-Retour</h3>
                                <p id="roundPrice" class="text-sm text-gray-600">-- FCFA</p>
                                <p id="roundStatus" class="text-xs text-gray-500"></p>
                            </div>
                        </div>
                    </div>
                </button>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button onclick="closeTripTypeModal()" class="px-6 py-2 bg-gray-100 rounded-lg font-bold hover:bg-gray-200 transition-colors">
                    Annuler
                </button>
            </div>
        </div>
    </div>
    
    <!-- Modal pour la rÃ©servation -->
    <div id="reservationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto">
        <div class="min-h-screen flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl overflow-hidden" style="max-height: 95vh;">
                <!-- En-tÃªte -->
                <div class="bg-gradient-to-r from-[#e94f1b] to-orange-500 p-6 text-white">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold">RÃ©servation de places</h2>
                        <button onclick="closeReservationModal()" class="text-white hover:text-gray-200 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div id="reservationProgramInfo" class="mt-2 text-lg"></div>
                </div>

                <!-- Contenu -->
                <div class="p-6" style="max-height: calc(95vh - 120px); overflow-y: auto;">
                    <!-- Ã‰tape 1: Nombre de places -->
                    <div id="step1" class="mb-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Combien de places souhaitez-vous rÃ©server ?
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

                    <!-- Ã‰tape 2: SÃ©lection des places -->
                    <div id="step2" class="hidden">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900">SÃ©lectionnez vos places</h3>
                            <div class="flex items-center gap-4">
                                <span id="selectedSeatsCount" class="text-lg font-bold text-[#e94f1b]">0 place
                                    sÃ©lectionnÃ©e</span>
                                <button onclick="backToStep1()"
                                    class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
                                    <i class="fas fa-arrow-left"></i>
                                    <span>Retour</span>
                                </button>
                            </div>
                        </div>

                        <!-- Visualisation des places -->
                        <div id="seatSelectionArea" class="mb-8">
                            <!-- Les places seront gÃ©nÃ©rÃ©es dynamiquement -->
                        </div>

                        <!-- LÃ©gende -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-green-500 rounded"></div>
                                <span class="text-sm">Place disponible</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-[#e94f1b] rounded"></div>
                                <span class="text-sm">Place sÃ©lectionnÃ©e</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-red-500 rounded"></div>
                                <span class="text-sm">Place rÃ©servÃ©e</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-blue-500 rounded"></div>
                                <span class="text-sm">Place cÃ´tÃ© gauche</span>
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
<!-- Ã‰tape 2.5: SÃ©lection des places RETOUR (si Aller-Retour) -->
<div id="step2_5" class="hidden">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-gray-900">SÃ©lectionnez vos places RETOUR</h3>
        <div class="flex items-center gap-4">
            <span id="selectedSeatsCountRetour" class="text-lg font-bold text-blue-600">0 place sÃ©lectionnÃ©e</span>
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
        <!-- Les places seront gÃ©nÃ©rÃ©es dynamiquement -->
    </div>

    <!-- LÃ©gende -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-green-500 rounded"></div>
            <span class="text-sm">Place disponible</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-600 rounded"></div>
            <span class="text-sm">Place sÃ©lectionnÃ©e</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-red-500 rounded"></div>
            <span class="text-sm">Place rÃ©servÃ©e</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-500 rounded"></div>
            <span class="text-sm">Place cÃ´tÃ© gauche</span>
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
                    <!-- Ã‰tape 3: Informations des passagers -->
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
                            <!-- Les formulaires passagers seront gÃ©nÃ©rÃ©s dynamiquement -->
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
                                <span>Confirmer la rÃ©servation</span>
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
                componentRestrictions: { country: "ci" }, // Restreindre Ã  la CÃ´te d'Ivoire
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
        var selectedReturnDate = null; // Date de retour sÃ©lectionnÃ©e pour Aller-Retour


        window.currentUser = @json(Auth::user()); // Injecter l'utilisateur connectÃ©
     // DÃ©finition explicite sur window pour s'assurer que le HTML peut voir la fonction
        window.handleReservationClick = function(button) {
            console.log("Bouton rÃ©server cliquÃ©"); // Debug
            try {
                const routeDataJson = button.getAttribute('data-route');
                const dateDepartInitial = button.getAttribute('data-date');
                
                if (!routeDataJson) {
                    console.error("Pas de donnÃ©es data-route trouvÃ©es");
                    return;
                }

                const routeData = JSON.parse(routeDataJson);
                console.log('DonnÃ©es route:', routeData);
                
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

        // Fonction pour inverser le point de dÃ©part et d'arrivÃ©e
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

        // Fonction handler pour le clic sur RÃ©server (gÃ¨re le parsing JSON depuis data attributes)
       

        // Configuration des types de rangÃ©es
        const typeRangeConfig = {
            '2x2': { placesGauche: 2, placesDroite: 2 },
            '2x3': { placesGauche: 2, placesDroite: 3 },
            '2x4': { placesGauche: 2, placesDroite: 4 },
            'Gamme Prestige': { placesGauche: 2, placesDroite: 2 },
            'Gamme Standard': { placesGauche: 2, placesDroite: 3 }
        };
 // --- NOUVELLE FONCTION: Modal de sÃ©lection des horaires pour les routes groupÃ©es ---
        window.showRouteSchedulesModal = function(routeData, dateDepart) {
            console.log('Ouverture modal sÃ©lection horaires:', routeData);
            
            // Stocker les donnÃ©es courantes
            window.currentRouteData = routeData;
            window.currentDateDepart = dateDepart;

            // Toujours demander le type de voyage d'abord
            showRouteTripTypeModal(routeData, dateDepart);
        };

        // Ã‰TAPE 1: Choix Type de Voyage (Aller Simple / Aller-Retour)
        function showRouteTripTypeModal(routeData, dateDepart) {
    // Conversion sÃ©curisÃ©e du prix en nombre
    // On convertit d'abord en string, on enlÃ¨ve tout sauf chiffres et points, puis on parse
    let priceString = String(routeData.montant_billet || '0');
    let priceRaw = priceString.replace(/[^\d.]/g, '');
    const priceSimple = parseFloat(priceRaw) || 0;
    const priceReturn = priceSimple * 2;

    Swal.fire({
        title: '<i class="fas fa-bus text-[#e94f1b]"></i> Type de voyage',
        html: `
            <div class="text-left space-y-4">
                <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                    <p class="font-bold text-gray-800">${routeData.point_depart} â†’ ${routeData.point_arrive}</p>
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
                        ${!routeData.has_retour ? '<p class="text-[10px] text-red-500 font-bold">Non disponible</p>' : '<p class="text-xs text-gray-500">Prix estimÃ©</p>'}
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
                        // AprÃ¨s le type, on demande la date (Aller Simple)
                        showDepartureDateSelection(routeData); 
                    });
                    
                    if (routeData.has_retour) {
                        document.getElementById('btnRouteReturn').addEventListener('click', () => {
                            window.userWantsAllerRetour = true;
                            window.userChoseAllerRetour = true;
                            Swal.close();
                            // AprÃ¨s le type, on demande la date (Aller-Retour)
                            showDepartureDateSelection(routeData);
                        });
                        document.getElementById('btnRouteReturn').classList.add('cursor-pointer', 'hover:bg-orange-100');
                    }
                }
            });
        }

        // Ã‰TAPE 2: Choix de l'heure de dÃ©part
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
                    <p class="text-sm text-gray-500">â†’ ${h.heure_arrive}</p>
                </div>
            `;
        });
        timeSlotsHtml += '</div>';
    } else {
        timeSlotsHtml = '<p class="text-center text-red-500 font-medium py-4">Aucun horaire de dÃ©part programmÃ© pour cette date.</p>';
    }

    Swal.fire({
        title: '<i class="fas fa-clock text-green-600"></i> Heure de dÃ©part',
        html: `
            <div class="text-left space-y-4">
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                     <p class="font-bold text-gray-800">${routeData.point_depart} â†’ ${routeData.point_arrive}</p>
                     <p class="text-sm text-gray-600">${dateFormatted}</p>
                     <p class="text-xs text-${isAllerRetour ? 'orange' : 'green'}-600 font-semibold mt-1">
                        <i class="fas fa-${isAllerRetour ? 'exchange-alt' : 'arrow-right'} mr-1"></i>
                        ${isAllerRetour ? 'Aller-Retour' : 'Aller Simple'}
                     </p>
                </div>
                <p class="font-medium text-gray-700">â†’ Choisissez l'heure de dÃ©part :</p>
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
            
            // IMPORTANT: Stocker l'heure sélectionnée
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
        // Ã‰TAPE 2.5: SÃ©lection de la date de retour (pour Aller-Retour) avec calendrier mensuel
        function showReturnDateSelection(routeData, dateDep) {
            const minDate = new Date(dateDep); // Date de retour minimum = date de dÃ©part
            minDate.setHours(0, 0, 0, 0);
            
            // Date max = date_fin du programme
            const maxDate = routeData.date_fin ? new Date(routeData.date_fin) : new Date(minDate.getFullYear(), 11, 31);
            
            let currentMonth = minDate.getMonth();
            let currentYear = minDate.getFullYear();
            
            function updateCalendar() {
                const monthNames = ['Janvier', 'FÃ©vrier', 'Mars', 'Avril', 'Mai', 'Juin', 
                                   'Juillet', 'AoÃ»t', 'Septembre', 'Octobre', 'Novembre', 'DÃ©cembre'];
                
                const calendarHtml = generateMonthlyCalendar(currentMonth, currentYear, minDate, maxDate, 'purple');
                
                Swal.update({
                    html: `
                        <div class="text-left space-y-4">
                            <div class="bg-purple-100 p-3 rounded-lg border border-purple-200">
                                <p class="font-bold text-purple-900">Retour : ${routeData.point_arrive} â†’ ${routeData.point_depart}</p>
                                <p class="text-sm text-gray-700">SÃ©lectionnez la date de votre retour</p>
                            </div>
                            <div class="bg-green-50 p-2 rounded border border-green-200 text-sm flex justify-between items-center">
                                <div><span class="font-bold text-green-700">DÃ©part :</span> ${new Date(dateDep).toLocaleDateString('fr-FR')}</div>
                                <div class="text-xs bg-green-100 px-2 py-1 rounded font-bold">${window.selectedDepartureTime}</div>
                            </div>

                            <!-- Options rapides Retour -->
                            <div class="flex justify-center gap-4">
                                <button id="btnReturnSameDay" class="flex-1 bg-purple-50 hover:bg-purple-100 text-purple-700 px-3 py-2 rounded-lg font-bold border-2 border-purple-200 transition-all text-sm">
                                    MÃªme jour
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
                
                // RÃ©attacher les Ã©vÃ©nements
                attachCalendarEvents();
            }
            
            function attachCalendarEvents() {
                // Bouton MÃªme Jour
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
                // Navigation mois prÃ©cÃ©dent
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
                        // Ne pas aller aprÃ¨s le mois de maxDate
                        const checkDate = new Date(currentYear, currentMonth, 1);
                        if (checkDate <= new Date(maxDate.getFullYear(), maxDate.getMonth(), 1)) {
                            updateCalendar();
                        } else {
                            currentMonth = maxDate.getMonth();
                            currentYear = maxDate.getFullYear();
                        }
                    });
                }
                
                // SÃ©lection de date
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
                    showRouteDepartureTimes(routeData, dateDep, true); // Retour au choix de l'heure de dÃ©part
                }
            });
        }

        // Charger les horaires de retour pour une date spÃ©cifique
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
                    // Mettre Ã  jour routeData avec les nouveaux horaires de retour
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

        // Ã‰TAPE 3: Choix de l'heure de retour (si Aller-Retour)
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
                    <p class="text-sm text-gray-500">â†’ ${h.heure_arrive}</p>
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
                     <p class="font-bold text-gray-800">Retour : ${routeData.point_arrive} â†’ ${routeData.point_depart}</p>
                     <p class="text-sm text-gray-600">Date : ${new Date(dateDepart).toLocaleDateString('fr-FR')}</p>
                </div>
                 <div class="bg-green-50 p-2 rounded border border-green-200 text-sm mb-2">
                    <span class="font-bold text-green-700">DÃ©part choisi :</span> ${window.selectedDepartureTime}
                </div>
                <p class="font-medium text-gray-700">â†’ Choisissez l'heure de retour :</p>
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
                    
                    // Trouver les dÃ©tails du programme retour dans routeData si nÃ©cessaire
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

        // Helper pour lancer la rÃ©servation finale
        function startReservationFromRoute(programId, dateVoyage, isAllerRetour) {
            window.userWantsAllerRetour = isAllerRetour;
            window.userChoseAllerRetour = isAllerRetour;
            
            // CORRECTION: Si on vient du flux "Grouped Routes", on s'assure d'utiliser la date de dÃ©part initiale
            if (window.currentDateDepart && isAllerRetour) {
                 console.log('DEBUG: Using Date Depart from global scope:', window.currentDateDepart);
                 dateVoyage = window.currentDateDepart;
            }

            // Ouvrir directement le modal de sÃ©lection des places (Step 1)
            openReservationModal(programId, dateVoyage);
        }
        

        
 // --- NOUVELLE FONCTION PRINCIPALE D'INITIATION ---
        // C'est elle qui est appelÃ©e par le bouton "RÃ©server"
     async function initiateReservationProcess(programId, searchDateFormatted, searchedTime = null) {
        console.log("Initiation rÃ©servation pour ID:", programId, "Date:", searchDateFormatted, "Heure cherchÃ©e:", searchedTime);
        
        // 1. RÃ©initialisation des variables globales
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
            
            if (!data.success) throw new Error("Impossible de charger les dÃ©tails du programme");
            
            const program = data.programme;
            window.outboundProgram = program;
            currentSelectedProgram = program;
            
            // VÃ©rifier la disponibilitÃ© du retour via l'API
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

            // Lancer le flux unifiÃ©
            showRouteTripTypeModal(routeData, searchDateFormatted);

        } catch (error) {
            console.error(error);
            Swal.close();
            Swal.fire({ icon: 'error', title: 'Erreur', text: 'Une erreur est survenue.' });
        }
    }

// Redundant function removed as it is now unified in showRouteTripTypeModal

// === NOUVEAU: Popup sÃ©lection heure de dÃ©part (depuis BDD) ===
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
        // Charger les horaires de dÃ©part depuis la BDD
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
                    <p class="text-sm text-gray-500">â†’ ${sched.heure_arrive}</p>
                </div>
            `;
        });
        timeSlotsHtml += '</div>';
        
        Swal.fire({
            title: '<i class="fas fa-clock text-[#e94f1b]"></i> Heure de dÃ©part',
            html: `
                <div class="text-left space-y-4">
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <p class="font-bold text-gray-800">${program.point_depart} â†’ ${program.point_arrive}</p>
                        <p class="text-sm text-gray-600"><i class="fas fa-calendar mr-2"></i>${dateFormatted}</p>
                        <p class="text-xs text-${isAllerRetour ? 'orange' : 'green'}-600 font-semibold mt-1">
                            <i class="fas fa-${isAllerRetour ? 'exchange-alt' : 'arrow-right'} mr-1"></i>
                            ${isAllerRetour ? 'Aller-Retour' : 'Aller Simple'}
                        </p>
                    </div>
                    <p class="font-medium text-gray-700">â†’ Choisissez l'heure de dÃ©part :</p>
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
                            // Aller Simple: on passe directement Ã  la sÃ©lection des places
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
        // GÃ©nÃ¨re les crÃ©neaux horaires disponibles (toutes les 30 min)
        function generateTimeSlots(selectedDate, tripDurationMinutes = 90) {
            const slots = [];
            const now = new Date();
            const isToday = selectedDate === now.toISOString().split('T')[0];
            
            // Minimum 4h Ã  l'avance
            const minBookingHours = 4;
            let startHour = 6; // Service commence Ã  6h
            let startMinute = 0;
            
            if (isToday) {
                const minTime = new Date(now.getTime() + (minBookingHours * 60 * 60 * 1000));
                startHour = Math.max(startHour, minTime.getHours());
                if (minTime.getMinutes() > 0) {
                    startMinute = minTime.getMinutes() <= 30 ? 30 : 0;
                    if (minTime.getMinutes() > 30) startHour++;
                }
            }
            
            // GÃ©nÃ©rer crÃ©neaux de 6h Ã  22h
            for (let h = startHour; h <= 22; h++) {
                for (let m = (h === startHour ? startMinute : 0); m < 60; m += 30) {
                    if (h === 22 && m > 0) break; // Dernier dÃ©part Ã  22h00
                    
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

        // Modal de sÃ©lection d'heure de dÃ©part
        function showTimeSelectionModal(program, departureDate) {
            const dateFormatted = new Date(departureDate).toLocaleDateString('fr-FR', { 
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
            });
            
            const timeSlots = generateTimeSlots(departureDate);
            
            if (timeSlots.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Aucun crÃ©neau disponible',
                    html: `<p>Pour la date du <strong>${dateFormatted}</strong>, aucun crÃ©neau horaire n'est disponible.</p>
                           <p class="text-sm text-gray-500 mt-2">Rappel: La rÃ©servation doit Ãªtre faite au minimum 4 heures Ã  l'avance.</p>`,
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
                title: '<i class="fas fa-clock text-[#e94f1b]"></i> Heure de dÃ©part',
                html: `
                    <div class="text-left space-y-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="font-bold text-gray-800">${program.point_depart} â†’ ${program.point_arrive}</p>
                            <p class="text-sm text-gray-600"><i class="fas fa-calendar mr-2"></i>${dateFormatted}</p>
                            <p class="text-sm text-gray-500 mt-1"><i class="fas fa-hourglass-half mr-2"></i>DurÃ©e: ${program.durer_parcours || '~1h30'}</p>
                        </div>
                        <p class="text-gray-600 font-medium">Ã€ quelle heure souhaitez-vous partir ?</p>
                        <p class="text-xs text-gray-400"><i class="fas fa-info-circle mr-1"></i>RÃ©servation minimum 4h Ã  l'avance â€¢ Service 6h-22h</p>
                        ${timeSlotsHtml}
                        <div id="selectedTimeDisplay" class="hidden bg-green-50 p-3 rounded-lg text-center">
                            <span class="font-bold text-green-800">DÃ©part sÃ©lectionnÃ©: <span id="selectedTimeValue"></span></span>
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
                        Swal.showValidationMessage('Veuillez sÃ©lectionner une heure de dÃ©part');
                        return false;
                    }
                    return window.selectedDepartureTime;
                },
                didOpen: () => {
                    document.querySelectorAll('.time-slot-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            // DÃ©sÃ©lectionner tous les autres
                            document.querySelectorAll('.time-slot-btn').forEach(b => {
                                b.classList.remove('bg-[#e94f1b]', 'text-white', 'border-[#e94f1b]');
                                b.classList.add('border-gray-200');
                            });
                            // SÃ©lectionner celui-ci
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
                            <p class="font-bold text-gray-800">${program.point_depart} â†’ ${program.point_arrive}</p>
                            <p class="text-sm text-gray-600"><i class="fas fa-calendar mr-2"></i>${dateFormatted}</p>
                            <p class="text-sm text-green-600 font-semibold"><i class="fas fa-clock mr-2"></i>DÃ©part Ã  ${window.selectedDepartureTime}</p>
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
                                <p class="text-xs text-gray-500">Prix estimÃ©</p>
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
                            inputValue: departureDate, // Par dÃ©faut la date aller
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

        // Modal de sÃ©lection d'heure de retour (aprÃ¨s le choix aller-retour)
        function showReturnTimeSelectionModal(program, departureDate) {
            const dateFormatted = new Date(departureDate).toLocaleDateString('fr-FR', { 
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
            });
            
            // Calculer l'heure d'arrivÃ©e estimÃ©e
            const durationMatch = (program.durer_parcours || '01:30').match(/(\d+):(\d+)/);
            const durationHours = durationMatch ? parseInt(durationMatch[1]) : 1;
            const durationMinutes = durationMatch ? parseInt(durationMatch[2]) : 30;
            const totalDurationMinutes = (durationHours * 60) + durationMinutes;
            
            // Heure d'arrivÃ©e = heure de dÃ©part + durÃ©e
            const [depH, depM] = window.selectedDepartureTime.split(':').map(Number);
            const arrivalDate = new Date(2026, 0, 1, depH, depM);
            arrivalDate.setMinutes(arrivalDate.getMinutes() + totalDurationMinutes);
            const arrivalTimeStr = `${arrivalDate.getHours().toString().padStart(2, '0')}:${arrivalDate.getMinutes().toString().padStart(2, '0')}`;
            
            // Retour minimum 1h aprÃ¨s l'arrivÃ©e
            const minReturnDate = new Date(arrivalDate);
            minReturnDate.setHours(minReturnDate.getHours() + 1);
            const minReturnHour = minReturnDate.getHours();
            const minReturnMinute = minReturnDate.getMinutes() <= 30 ? 30 : 0;
            const actualMinReturnHour = minReturnDate.getMinutes() > 30 ? minReturnHour + 1 : minReturnHour;
            
            // GÃ©nÃ©rer crÃ©neaux pour le retour
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
                    html: `<p>Votre arrivÃ©e estimÃ©e est Ã  <strong>${arrivalTimeStr}</strong>.</p>
                           <p class="mt-2">Le retour ne peut pas Ãªtre fait le mÃªme jour. Veuillez choisir un autre jour.</p>`,
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
                            <p class="font-bold text-green-800"><i class="fas fa-check-circle mr-2"></i>Aller confirmÃ©</p>
                            <p class="text-sm text-green-700">${program.point_depart} â†’ ${program.point_arrive}</p>
                            <p class="text-sm text-green-600">${dateFormatted} â€¢ DÃ©part: ${window.selectedDepartureTime} â€¢ ArrivÃ©e: ~${arrivalTimeStr}</p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <p class="font-bold text-blue-800">${program.point_arrive} â†’ ${program.point_depart}</p>
                            <p class="text-sm text-blue-600">${dateFormatted} (mÃªme jour)</p>
                        </div>
                        <p class="text-gray-600 font-medium">Ã€ quelle heure souhaitez-vous repartir ?</p>
                        <p class="text-xs text-gray-400"><i class="fas fa-info-circle mr-1"></i>Minimum 1h aprÃ¨s votre arrivÃ©e</p>
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
                        Swal.showValidationMessage('Veuillez sÃ©lectionner une heure de retour');
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
                    // RÃ©capitulatif et continuation
                    window.selectedReturnDate = departureDate; // MÃªme jour
                    showTripSummaryAndContinue(program, departureDate);
                }
            });
        }

        // RÃ©capitulatif du voyage aller-retour
        function showTripSummaryAndContinue(program, departureDate) {
            const dateFormatted = new Date(departureDate).toLocaleDateString('fr-FR');
            const totalPrice = Number(program.montant_billet) * 2;
            
            Swal.fire({
                icon: 'success',
                title: 'Voyage Aller-Retour confirmÃ© !',
                html: `
                    <div class="text-left space-y-3">
                        <div class="border-l-4 border-green-500 pl-3">
                            <p class="font-bold text-gray-800">â†— ALLER</p>
                            <p class="text-sm">${program.point_depart} â†’ ${program.point_arrive}</p>
                            <p class="text-sm text-gray-500">${dateFormatted} Ã  ${window.selectedDepartureTime}</p>
                        </div>
                        <div class="border-l-4 border-blue-500 pl-3">
                            <p class="font-bold text-gray-800">â†™ RETOUR</p>
                            <p class="text-sm">${program.point_arrive} â†’ ${program.point_depart}</p>
                            <p class="text-sm text-gray-500">${dateFormatted} Ã  ${window.selectedReturnTime}</p>
                        </div>
                        <div class="bg-orange-50 p-3 rounded-lg text-center mt-4">
                            <p class="text-lg font-bold text-[#e94f1b]">Total: ${totalPrice.toLocaleString('fr-FR')} FCFA</p>
                        </div>
                    </div>
                `,
                confirmButtonText: 'Continuer la rÃ©servation',
                confirmButtonColor: '#e94f1b'
            }).then(() => {
                openReservationModal(program.id, departureDate);
            });
        }

        // Afficher le sÃ©lecteur de voyage retour
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
                        html: `<p>Aucun voyage retour <strong>${outboundProgram.point_arrive} â†’ ${outboundProgram.point_depart}</strong> n'est disponible le/aprÃ¨s le ${new Date(returnDate).toLocaleDateString('fr-FR')}.</p>
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
                             // RÃ©-ouvrir le choix de date
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

            // Pour le mÃªme jour, on affiche simplement les horaires disponibles
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
                        <p class="text-sm text-gray-500">â†’ ${trip.heure_arrive}</p>
                        <p class="text-sm font-bold text-[#e94f1b] mt-1">${Number(trip.montant_billet).toLocaleString('fr-FR')} FCFA</p>
                         ${trip.display_date && trip.display_date !== outboundDate ? `<p class="text-xs text-blue-600 mt-1 font-bold">${new Date(tripDate).toLocaleDateString('fr-FR', {day: 'numeric', month: 'short'})}</p>` : ''}
                    </div>
                `;
            });
            timeSlotsHtml += '</div>';

            let html = `
                <div class="text-left max-h-[60vh] overflow-y-auto">
                    <div class="bg-green-50 p-3 rounded-lg mb-4 text-sm">
                        <p class="font-bold text-green-800"><i class="fas fa-check-circle mr-2"></i>Aller confirmÃ©</p>
                        <p class="text-green-700">${outboundProgram.point_depart} â†’ ${outboundProgram.point_arrive}</p>
                        <p class="text-green-600 text-xs">${dateFormatted} â€¢ DÃ©part: ${window.selectedDepartureTime || outboundProgram.heure_depart}</p>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-lg mb-4">
                        <p class="font-bold text-blue-800"><i class="fas fa-undo mr-2"></i>Retour: ${outboundProgram.point_arrive} â†’ ${outboundProgram.point_depart}</p>
                        <p class="text-blue-600 text-sm font-semibold">${returnDateFormatted}</p>
                         <p class="text-blue-600 text-xs">Options disponibles</p>
                    </div>
                    <p class="font-medium text-gray-700 mb-3">SÃ©lectionnez votre heure de retour :</p>
                    ${timeSlotsHtml}
                </div>
            `;
            html += `</div>`;

            Swal.fire({
                title: `<i class="fas fa-undo text-blue-500"></i> Retour: ${outboundProgram.point_arrive} â†’ ${outboundProgram.point_depart}`,
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
                            
                            // Afficher rÃ©capitulatif et ouvrir modal rÃ©servation
                            Swal.fire({
                                icon: 'success',
                                title: 'Aller-Retour sÃ©lectionnÃ© !',
                                html: `
                                    <div class="text-left space-y-2">
                                        <p><strong>Aller:</strong> ${outboundProgram.point_depart} â†’ ${outboundProgram.point_arrive}<br>
                                           <span class="text-sm text-gray-500">${new Date(outboundDate).toLocaleDateString('fr-FR')} Ã  ${outboundProgram.heure_depart}</span></p>
                                        <p><strong>Retour:</strong> ${window.selectedReturnProgram.point_depart} â†’ ${window.selectedReturnProgram.point_arrive}<br>
                                           <span class="text-sm text-gray-500">${new Date(tripDate).toLocaleDateString('fr-FR')} Ã  ${window.selectedReturnProgram.heure_depart}</span></p>
                                        <p class="text-lg font-bold text-[#e94f1b] mt-3">Total: ${totalPrice.toLocaleString('fr-FR')} FCFA</p>
                                    </div>
                                `,
                                confirmButtonText: 'Continuer la rÃ©servation',
                                confirmButtonColor: '#e94f1b'
                            }).then(() => {
                                // Ouvrir le modal de rÃ©servation pour l'aller
                                console.log('DEBUG: Opening final reservation modal with OutboundDate:', savedOutboundDate);
                                
                                // FORCE RESTORE: On s'assure que globalement la date aller est correcte
                                window.outboundDate = savedOutboundDate;
                                window.currentReservationDate = savedOutboundDate; // Double sÃ©curitÃ©
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
        // Pour rÃ©current, on prend demain si possible, ou une logique plus complexe
        // Ici on simplifie en renvoyant la date de jour ou la date de dÃ©but
        return new Date().toISOString().split('T')[0]; 
    }
    // ============================================
        // FONCTION 3: Ouvrir le modal de rÃ©servation
        // ============================================
         function showReservationModal(programId, searchDate = null) {
            // IncrÃ©menter l'ID de requÃªte
            currentRequestId++;
            const thisRequestId = currentRequestId;

            console.log(`[REQ #${thisRequestId}] Ouverture modal RÃ©servation pour ID ${programId}`);

            // RÃ©initialisation
            currentProgramId = programId;
            selectedNumberOfPlaces = 0;
            selectedSeats = [];
            reservedSeats = [];
            vehicleDetails = null;
            window.currentReservationDate = null;

            // Reset UI
            document.getElementById('reservationProgramInfo').innerHTML = '<div class="text-center p-4"><i class="fas fa-spinner fa-spin text-2xl text-[#e94f1b]"></i><p>Chargement...</p></div>';
            document.getElementById('selectedSeatsCount').textContent = '0 place sÃ©lectionnÃ©e';
            document.getElementById('seatSelectionArea').innerHTML = '';
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step3').classList.add('hidden');
            document.getElementById('step1').classList.remove('hidden');
            document.getElementById('nextStepBtn').disabled = true;
            document.querySelectorAll('.place-count-btn').forEach(btn => btn.classList.remove('active'));

            document.getElementById('reservationModal').classList.remove('hidden');

            // Fetch info programme
            fetch("{{ route('user.reservation.program', ':id') }}".replace(':id', programId))
                .then(response => response.json())
                .then(data => {
                    if (thisRequestId !== currentRequestId) return;

                    if (data.success) {
                        const program = data.programme;
                         currentSelectedProgram = program; 
                        // DÃ©terminer la date de voyage finale
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
                                <span><i class="fas fa-map-marker-alt"></i> ${program.point_depart} â†’ ${program.point_arrive}</span>
                                <span><i class="fas fa-calendar"></i> ${dateDisplay}</span>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded"><i class="fas fa-clock"></i> DÃ©part: ${window.selectedDepartureTime || program.heure_depart}</span>
                                <span><i class="fas fa-money-bill-wave"></i> ${prixAffiche.toLocaleString('fr-FR')} FCFA</span>
                                ${allerRetourBadge}
                                ${window.selectedReturnTime ? `<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm"><i class="fas fa-undo"></i> Retour: ${selectedReturnDate ? new Date(selectedReturnDate).toLocaleDateString('fr-FR') + ' Ã  ' : ''}${window.selectedReturnTime}</span>` : ''}
                            </div>
                        `;

                        // PrÃ©charger les places
                        fetch("{{ route('user.reservation.reserved-seats', ':id') }}".replace(':id', programId) + `?date=${encodeURIComponent(dateVoyage)}`)
                            .then(r => r.json())
                            .then(d => {
                                if (d.success && thisRequestId === currentRequestId) {
                                    reservedSeats = d.reservedSeats || [];
                                }
                            });
                    }
                });
        }

        // Exposer globalement pour compatibilitÃ©
        window.openReservationModal = showReservationModal;



        // ============================================
        // FONCTION 4: Fermer le modal de rÃ©servation
        // ============================================
        function closeReservationModal() {
            document.getElementById('reservationModal').classList.add('hidden');
        }

        // ============================================
        // FONCTION 5: SÃ©lectionner le nombre de places
        // ============================================
        function selectNumberOfPlaces(number, element) {
            selectedNumberOfPlaces = number;

            // Activer le bouton sÃ©lectionnÃ©
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
            
            // DÃ©tection automatique du choix basÃ© sur la recherche
            const searchType = new URLSearchParams(window.location.search).get('is_aller_retour');
            if (searchType === '1') {
                userWantsAllerRetour = true;
                document.getElementById('allerRetourChoice').value = 'aller_retour';
            } else {
                userWantsAllerRetour = false;
                document.getElementById('allerRetourChoice').value = 'aller_simple';
            }
            
            updateAllerRetourPriceDisplay(program);
            onAllerRetourChoiceChange(); // Mettre Ã  jour l'affichage dynamique (dates, etc)
            modal.classList.remove('hidden');
        }
function onAllerRetourChoiceChange() {
            const choice = document.getElementById('allerRetourChoice').value;
            userWantsAllerRetour = (choice === 'aller_retour');
            
            updateAllerRetourPriceDisplay(currentSelectedProgram);
            
            const returnDateSection = document.getElementById('returnDateSection');
            // Afficher section date retour SI A/R ET (RÃ©current OU (Ponctuel ET Date Retour non fixÃ©e par dÃ©faut))
            // Note: Pour ponctuel, le retour est souvent le mÃªme jour par dÃ©faut, mais ici on gÃ¨re le cas rÃ©current
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

            // RÃ©cupÃ©rer les jours de rÃ©currence du programme RETOUR si dispo
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
            
            // Date de dÃ©but : lendemain du dÃ©part
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
                    // Check fin validitÃ©
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
                // SÃ©curitÃ© boucle infinie
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
                        Swal.fire({ icon: 'warning', text: 'Veuillez sÃ©lectionner une date de retour.' });
                        return;
                    }
                    window.selectedReturnDate = returnDate;
                } else {
                    // Ponctuel : retour le mÃªme jour
                    window.selectedReturnDate = window.selectedDepartureDate;
                }
            } else {
                // L'utilisateur a choisi Aller Simple (mÃªme sur un programme A/R)
                window.selectedReturnDate = null;
            }
            
            closeAllerRetourConfirmModal();
            
            // Suite du flux
            const program = currentSelectedProgram;
            // Si c'est un rÃ©current et qu'on n'a pas encore de date de dÃ©part
            if (program.type_programmation === 'recurrent' && !window.selectedDepartureDate) {
                openDateSelectionModal(program);
            } else {
                // On a tout ce qu'il faut
                openReservationModal(program.id, window.selectedDepartureDate);
            }
        }
        // ============================================
        // FONCTION 1: Afficher les dÃ©tails du vÃ©hicule
        // ============================================
       async function showVehicleDetails(vehicleId, programId, dateVoyageInput = null, heureDepart = null) {
    console.log(`[DETAILS] Demande détails véhicule ${vehicleId} pour programme ${programId} (Date: ${dateVoyageInput}, Heure: ${heureDepart})`);
    
    // Si ID null ou 0, on laisse passer pour que le backend génère un véhicule virtuel
    if (!vehicleId || vehicleId === '0') {
        console.warn('Aucun ID véhicule, utilisation du mode virtuel via backend');
    }

    // Récupérer l'heure de départ depuis le contexte global si non fournie
    if (!heureDepart && window.selectedDepartureTime) {
        heureDepart = window.selectedDepartureTime;
    }

    // Récupérer la date
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

    console.log(`[DETAILS] Date utilisée: ${dateVoyage}, Heure: ${heureDepart}`);

    Swal.fire({
        title: 'Chargement...',
        text: 'Récupération des informations du véhicule',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        // CORRECTION: Ajouter heure_depart dans l'URL
        const url = "{{ route('user.reservation.vehicle', ':id') }}".replace(':id', vehicleId);
        let queryParams = `?date=${encodeURIComponent(dateVoyage)}&program_id=${programId}`;
        
        if (heureDepart) {
            queryParams += `&heure_depart=${encodeURIComponent(heureDepart)}`;
        }
        
        const response = await fetch(url + queryParams);
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
        // FONCTION 2: GÃ©nÃ©rer la visualisation des places
        // ============================================
        function generatePlacesVisualization(vehicle) {
            let config = typeRangeConfig[vehicle.type_range];
            if (!config) {
                config = { placesGauche: 2, placesDroite: 2 };
                console.warn(`Configuration de vÃ©hicule inconnue: ${vehicle.type_range}. Utilisation du mode par dÃ©faut 2x2.`);
            }
            const placesGauche = config.placesGauche;
            const placesDroite = config.placesDroite;
            const placesParRanger = placesGauche + placesDroite;
            const totalPlaces = parseInt(vehicle.nombre_place);
            const nombreRanger = Math.ceil(totalPlaces / placesParRanger);
            let html = `
                                                                                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                                                                                    <!-- En-tÃªte -->
                                                                                    <div style="display: grid; grid-template-columns: 100px 1fr 80px 1fr; gap: 10px; padding: 15px; background: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                                                                                        <div style="text-align: center; font-weight: 600; color: #4b5563;">RangÃ©e</div>
                                                                                        <div style="text-align: center; font-weight: 600; color: #4b5563;">CÃ´tÃ© gauche</div>
                                                                                        <div style="text-align: center; font-weight: 600; color: #4b5563;">AllÃ©e</div>
                                                                                        <div style="text-align: center; font-weight: 600; color: #4b5563;">CÃ´tÃ© droit</div>
                                                                                    </div>

                                                                                    <!-- RangÃ©es -->
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
                                                                                        <!-- NumÃ©ro de rangÃ©e -->
                                                                                        <div style="text-align: center; font-weight: 600; color: #6b7280;">RangÃ©e ${ranger}</div>

                                                                                        <!-- Places cÃ´tÃ© gauche -->
                                                                                        <div style="display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
                                                                                `;

                // Places cÃ´tÃ© gauche
                for (let i = 0; i < placesGaucheCetteRanger; i++) {
                    html += `
                                                                                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #e94f1b, #e89116); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.1rem; box-shadow: 0 2px 4px rgba(254, 162, 25, 0.3); cursor: help;" title="Place ${numeroPlace + i}">
                                                                                            ${numeroPlace + i}
                                                                                        </div>
                                                                                    `;
                }

                html += `
                                                                                        </div>

                                                                                        <!-- AllÃ©e -->
                                                                                        <div style="text-align: center;">
                                                                                            <div style="width: 10px; height: 40px; background: #9ca3af; border-radius: 5px; margin: 0 auto;"></div>
                                                                                        </div>

                                                                                        <!-- Places cÃ´tÃ© droit -->
                                                                                        <div style="display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
                                                                                `;

                // Places cÃ´tÃ© droit
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

                                                                                <!-- LÃ©gende -->
                                                                                <div style="display: flex; justify-content: center; gap: 20px; margin-top: 20px; padding: 15px; background: #f9fafb; border-radius: 8px;">
                                                                                    <div style="display: flex; align-items: center; gap: 8px;">
                                                                                        <div style="width: 20px; height: 20px; background: linear-gradient(135deg, #e94f1b, #e89116); border-radius: 4px;"></div>
                                                                                        <span style="color: #4b5563; font-size: 0.9rem;">CÃ´tÃ© gauche (conducteur)</span>
                                                                                    </div>
                                                                                    <div style="display: flex; align-items: center; gap: 8px;">
                                                                                        <div style="width: 20px; height: 20px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 4px;"></div>
                                                                                        <span style="color: #4b5563; font-size: 0.9rem;">CÃ´tÃ© droit</span>
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

        // NOUVEAU: ID de requÃªte pour Ã©viter les conflits asynchrones
        // currentRequestId dÃ©jÃ  dÃ©clarÃ© en haut du script

        
        // ============================================
        // FONCTION 6: Afficher la sÃ©lection des places
        // ============================================
        async function showSeatSelection() {
            if (!currentProgramId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Aucun programme sÃ©lectionnÃ©.',
                    confirmButtonColor: '#e94f1b',
                });
                return;
            }

            // Afficher le loader
            Swal.fire({
                title: 'Chargement...',
                text: 'RÃ©cupÃ©ration des informations',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                // 1. Récupérer le programme
                const programUrl = "{{ route('user.reservation.program', ':id') }}".replace(':id', currentProgramId) + `?date=${encodeURIComponent(window.currentReservationDate || '')}`;
                const programResponse = await fetch(programUrl);
                const programData = await programResponse.json();

                if (!programData.success) {
                    throw new Error(programData.error || 'Programme non trouvÃ©');
                }

                const program = programData.programme;

                // IMPORTANT: Utiliser la date stockÃ©e, pas la date du programme
                let dateVoyage = window.currentReservationDate;

                if (!dateVoyage) {
                    // Si pas de date stockÃ©e, utiliser la date du programme
                    const dateDepart = new Date(program.date_depart);
                    dateVoyage = dateDepart.toISOString().split('T')[0];
                    window.currentReservationDate = dateVoyage;
                }

                // 2. RÃ©cupÃ©rer le vÃ©hicule (avec fallback si pas de vÃ©hicule associÃ©)
                let vehicleId = program.vehicule_id;
                
                // Si pas de vÃ©hicule associÃ©, rÃ©cupÃ©rer le premier vÃ©hicule de la compagnie
                if (!vehicleId) {
                    console.log('Pas de vÃ©hicule associÃ© au programme, recherche vÃ©hicule par dÃ©faut...');
                    try {
                        const defaultVehicleUrl = "{{ route('user.reservation.default-vehicle', ':id') }}".replace(':id', currentProgramId) + `?date=${encodeURIComponent(dateVoyage)}`;
                        const defaultVehicleResponse = await fetch(defaultVehicleUrl);
                        if (defaultVehicleResponse.ok) {
                            const defaultVehicleData = await defaultVehicleResponse.json();
                            if (defaultVehicleData.success && defaultVehicleData.vehicule_id) {
                                vehicleId = defaultVehicleData.vehicule_id;
                            }
                        }
                    } catch (e) {
                        console.log('Erreur rÃ©cupÃ©ration vÃ©hicule par dÃ©faut:', e);
                    }
                }
                
                if (!vehicleId) {
                    // Utiliser une configuration de places par dÃ©faut (70 places)
                    vehicleDetails = {
                        type_range: '2x3',
                        capacite_total: 70,
                        marque: 'Bus',
                        modele: 'Standard'
                    };
                    console.log('Utilisation de la configuration par dÃ©faut (70 places):', vehicleDetails);
                } else {
                    const vehicleUrl = "{{ route('user.reservation.vehicle', ':id') }}".replace(':id', vehicleId);
                    const vehicleResponse = await fetch(vehicleUrl + `?date=${encodeURIComponent(dateVoyage)}&program_id=${currentProgramId}&heure_depart=${encodeURIComponent(window.selectedDepartureTime || '')}`);

                    if (!vehicleResponse.ok) {
                         const errText = await vehicleResponse.text();
                         console.error('Fetch Vehicle Error:', errText);
                         throw new Error(`Erreur HTTP ${vehicleResponse.status}`);
                    }

                    const responseText = await vehicleResponse.text();
                    let vehicleData;
                    try {
                        vehicleData = JSON.parse(responseText);
                    } catch(e) {
                        console.error('JSON Parse Error:', responseText);
                        // Show HTML preview in alert
                        const preview = responseText.substring(0, 100).replace(/<[^>]*>?/gm, '');
                        throw new Error('Réponse serveur invalide (JSON): ' + preview);
                    }

                    if (!vehicleData.success) {
                        throw new Error(vehicleData.error || 'VÃ©hicule non trouvÃ©');
                    }

                    vehicleDetails = vehicleData.vehicule;
                }

                // 3. Récupérer les places réservées POUR CETTE DATE SPÉCIFIQUE
                const seatsUrl = "{{ route('user.reservation.reserved-seats', ':id') }}".replace(':id', currentProgramId) + `?date=${encodeURIComponent(dateVoyage)}&heure_depart=${encodeURIComponent(window.selectedDepartureTime || '')}`;
                const seatsResponse = await fetch(seatsUrl);

                if (seatsResponse.ok) {
                    const seatsData = await seatsResponse.json();
                    if (seatsData.success) {
                        reservedSeats = seatsData.reservedSeats || [];
                        console.log('Places rÃ©servÃ©es pour', dateVoyage, ':', reservedSeats);
                    }
                }

                // Fermer le loader
                Swal.close();

                // GÃ©nÃ©rer la vue de sÃ©lection des places
                generateSeatSelectionView();

                // Changer d'Ã©tape
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
                                                                                            VÃ©rifiez que :
                                                                                            <ul class="list-disc pl-5 mt-1">
                                                                                                <li>Vous Ãªtes bien connectÃ©</li>
                                                                                                <li>Le programme existe toujours</li>
                                                                                                <li>Le vÃ©hicule est associÃ© au programme</li>
                                                                                            </ul>
                                                                                        </p>
                                                                                    </div>
                                                                                `,
                    confirmButtonColor: '#e94f1b',
                });
            }
        }

        // ============================================
        // FONCTION 7: GÃ©nÃ©rer la vue de sÃ©lection des places
        // ============================================
        function generateSeatSelectionView() {
            if (!vehicleDetails) {
                document.getElementById('seatSelectionArea').innerHTML =
                    '<p class="text-center text-red-500">Impossible de charger les informations du vÃ©hicule.</p>';
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
            
            // Construire le nom du vÃ©hicule de maniÃ¨re sÃ©curisÃ©e
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
                                                                                            <i class="fas fa-hand-pointer mr-2"></i>SÃ©lection manuelle
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
                                                                                        <!-- CÃ´tÃ© gauche -->
                                                                                        <div class="flex flex-col items-center">
                                                                                            <div class="text-sm text-gray-600 mb-2">RangÃ©e ${ranger}</div>
                                                                                            <div class="flex gap-3">
                                                                                `;

                // Places cÃ´tÃ© gauche
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
                                                                                             title="Place ${seatNumber}${isReserved ? ' (RÃ©servÃ©e)' : ''}">
                                                                                            <span class="text-lg">${seatNumber}</span>
                                                                                            <span class="text-xs">${isReserved ? 'âœ—' : (isSelected ? 'âœ“' : '')}</span>
                                                                                        </div>
                                                                                    `;
                }

                html += `
                                                                                            </div>
                                                                                        </div>

                                                                                        <!-- AllÃ©e -->
                                                                                        <div class="w-20 h-2 bg-gray-400 rounded my-8"></div>

                                                                                        <!-- CÃ´tÃ© droit -->
                                                                                        <div class="flex flex-col items-center">
                                                                                            <div class="text-sm text-gray-600 mb-2">RangÃ©e ${ranger}</div>
                                                                                            <div class="flex gap-3">
                                                                                `;

                // Places cÃ´tÃ© droit
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
                                                                                             title="Place ${seatNumber}${isReserved ? ' (RÃ©servÃ©e)' : ''}">
                                                                                            <span class="text-lg">${seatNumber}</span>
                                                                                            <span class="text-xs">${isReserved ? 'âœ—' : (isSelected ? 'âœ“' : '')}</span>
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
                                                                                            SÃ©lectionnez ${selectedNumberOfPlaces} place${selectedNumberOfPlaces > 1 ? 's' : ''} en cliquant sur les places disponibles.
                                                                                            Les places en rouge sont dÃ©jÃ  rÃ©servÃ©es.
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            `;

            document.getElementById('seatSelectionArea').innerHTML = html;
            updateSelectedSeatsCount();
        }

        // ============================================
        // FONCTION 7.1: Basculer le mode de sÃ©lection
        // ============================================
        function toggleSelectionMode(mode) {
             // RÃ©initialiser la sÃ©lection si on change de mode
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
            // 1. RÃ©initialiser la sÃ©lection actuelle
            selectedSeats = [];
            
            // 2. Trouver toutes les places disponibles
            const totalPlaces = parseInt(vehicleDetails.capacite_total || vehicleDetails.nombre_place || 70);
            const availableSeats = [];
            
            for (let i = 1; i <= totalPlaces; i++) {
                if (!reservedSeats.includes(i)) {
                    availableSeats.push(i);
                }
            }
            
            // 3. VÃ©rifier s'il y a assez de places
            if (availableSeats.length < selectedNumberOfPlaces) {
                Swal.fire({
                    icon: 'error',
                    title: 'Pas assez de places',
                    text: 'Il ne reste pas suffisamment de places disponibles pour votre demande.',
                    confirmButtonColor: '#e94f1b'
                });
                return false;
            }
            
            // 4. SÃ©lectionner alÃ©atoirement
            const shuffled = availableSeats.sort(() => 0.5 - Math.random());
            const selected = shuffled.slice(0, selectedNumberOfPlaces);
            
            // 5. Appliquer la sÃ©lection visuellement (sans passer par toggleSeat pour Ã©viter les alertes)
            selected.forEach(seat => {
                selectedSeats.push(seat);
                const seatElement = document.querySelector(`[onclick="toggleSeat(${seat})"]`);
                if (seatElement) {
                    // Simuler l'affichage sÃ©lectionnÃ©
                    const isLeftSide = seat <= typeRangeConfig[vehicleDetails.type_range].placesGauche;
                    seatElement.classList.add('bg-[#e94f1b]', 'text-white', 'shadow-lg', 'transform', 'scale-110');
                    seatElement.classList.remove(isLeftSide ? 'bg-blue-500' : 'bg-green-500');
                    seatElement.classList.remove('hover:bg-blue-600', 'hover:bg-green-600'); // Optional cleanup
                    seatElement.querySelector('.text-xs').textContent = 'âœ“';
                }
            });
            
            updateSelectedSeatsCount();
            return true;
        }

        // ============================================
        // FONCTION 8: SÃ©lection/dÃ©sÃ©lection d'une place
        // ============================================
        function toggleSeat(seatNumber) {
            const index = selectedSeats.indexOf(seatNumber);

            if (index === -1) {
                // VÃ©rifier si on n'a pas dÃ©passÃ© le nombre de places sÃ©lectionnÃ©es
                if (selectedSeats.length >= selectedNumberOfPlaces) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Limite atteinte',
                        text: `Vous ne pouvez sÃ©lectionner que ${selectedNumberOfPlaces} place(s). DÃ©sÃ©lectionnez d'abord une place si vous voulez en choisir une autre.`,
                        confirmButtonColor: '#e94f1b',
                    });
                    return;
                }
                selectedSeats.push(seatNumber);
            } else {
                selectedSeats.splice(index, 1);
            }

            // Mettre Ã  jour l'affichage de la place
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

                // Mettre Ã  jour le checkmark
                const checkmark = seatElement.querySelector('.text-xs');
                if (checkmark) {
                    checkmark.textContent = isSelected ? 'âœ“' : '';
                }
            }

            updateSelectedSeatsCount();
        }

        // ============================================
        // FONCTION 9: Mettre Ã  jour le compteur
        // ============================================
        function updateSelectedSeatsCount() {
            const count = selectedSeats.length;
            const countElement = document.getElementById('selectedSeatsCount');
            const nextBtn = document.getElementById('showPassengerInfoBtn');

            countElement.textContent =
                `${count} place${count > 1 ? 's' : ''} sÃ©lectionnÃ©e${count > 1 ? 's' : ''} / ${selectedNumberOfPlaces} demandÃ©e${selectedNumberOfPlaces > 1 ? 's' : ''}`;

            // Mettre Ã  jour le style du compteur
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
        // FONCTION 10: Retour Ã  l'Ã©tape 1
        // ============================================
        function backToStep1() {
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step1').classList.remove('hidden');
            // RÃ©initialiser la sÃ©lection des places
            selectedSeats = [];
        }

        // ============================================
        // FONCTION 10.1: Retour Ã  l'Ã©tape 2
        // ============================================
        function backToStep2() {
            document.getElementById('step3').classList.add('hidden');
            document.getElementById('step2').classList.remove('hidden');
        }

        // ============================================
        // FONCTION 10.2: Afficher les infos passagers
        // ============================================
      function showPassengerInfo() {
    // Si aucune place sÃ©lectionnÃ©e, on lance l'assignation automatique
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
    // Si c'est un aller-retour ET qu'on n'a pas encore sÃ©lectionnÃ© les places retour
    if (window.userChoseAllerRetour && selectedSeatsRetour.length === 0) {
        // Charger et afficher la sÃ©lection des places retour
        loadRetourSeatsSelection();
    } else {
        // Sinon, passer directement aux infos passagers
        proceedToPassengerInfo();
    }
}
async function loadRetourSeatsSelection() {
    // RÃ©cupÃ©rer le programme retour
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
        text: 'RÃ©cupÃ©ration des places disponibles pour le retour',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    try {
        // 1. Récupérer le programme retour
        const programUrl = "{{ route('user.reservation.program', ':id') }}".replace(':id', retourProgId) + `?date=${encodeURIComponent(window.selectedReturnDate || '')}`;
        const programResponse = await fetch(programUrl);
        const programData = await programResponse.json();

        if (!programData.success) {
            throw new Error(programData.error || 'Programme retour non trouvÃ©');
        }

        const programRetour = programData.programme;

        // 2. RÃ©cupÃ©rer le vÃ©hicule
        let vehicleId = programRetour.vehicule_id;
        
        if (!vehicleId) {
            // Fallback : vÃ©hicule par dÃ©faut
            const defaultVehicleUrl = "{{ route('user.reservation.default-vehicle', ':id') }}".replace(':id', retourProgId) + `?date=${encodeURIComponent(window.selectedReturnDate || '')}`;
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
            const vehicleUrl = "{{ route('user.reservation.vehicle', ':id') }}".replace(':id', vehicleId);
            const vehicleResponse = await fetch(vehicleUrl + `?date=${encodeURIComponent(window.selectedReturnDate || '')}&program_id=${retourProgId}&heure_depart=${encodeURIComponent(window.selectedReturnTime || '')}`);
            const vehicleData = await vehicleResponse.json();
            
            if (!vehicleData.success) {
                throw new Error('VÃ©hicule retour non trouvÃ©');
            }
            
            vehicleDetailsRetour = vehicleData.vehicule;
        }

        // 3. Récupérer les places réservées pour le retour
        let heureDepart = window.selectedReturnTime || programRetour.heure_depart; // Corrected variable name
        const dateRetour = window.selectedReturnDate || window.currentReservationDate;
        const seatsUrl = "{{ route('user.reservation.reserved-seats', ':id') }}".replace(':id', retourProgId) + `?date=${encodeURIComponent(dateRetour)}&heure_depart=${encodeURIComponent(heureDepart)}`;
        const seatsResponse = await fetch(seatsUrl);

         if (seatsResponse.ok) {
        const seatsData = await seatsResponse.json();
        if (seatsData.success) {
            reservedSeatsRetour = seatsData.reservedSeats || [];
            console.log('Places réservées pour le retour', dateRetour, 'à', heureDepart, ':', reservedSeatsRetour);
        }
    }

        Swal.close();

        // 4. Afficher les infos du retour
        const dateRetourFormatted = new Date(dateRetour).toLocaleDateString('fr-FR');
        document.getElementById('returnProgramInfo').innerHTML = `
            <span><i class="fas fa-map-marker-alt"></i> ${programRetour.point_depart} â†’ ${programRetour.point_arrive}</span>
            <span class="mx-2">|</span>
            <span><i class="fas fa-calendar"></i> ${dateRetourFormatted}</span>
            <span class="mx-2">|</span>
            <span><i class="fas fa-clock"></i> ${programRetour.heure_depart}</span>
        `;

        // 5. GÃ©nÃ©rer la vue de sÃ©lection des places retour
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
            '<p class="text-center text-red-500">Impossible de charger les informations du vÃ©hicule.</p>';
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
                    <i class="fas fa-hand-pointer mr-2"></i>SÃ©lection manuelle
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
                <!-- CÃ´tÃ© gauche -->
                <div class="flex flex-col items-center">
                    <div class="text-sm text-gray-600 mb-2">RangÃ©e ${ranger}</div>
                    <div class="flex gap-3">
        `;

        // Places cÃ´tÃ© gauche
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
                     title="Place ${seatNumber}${isReserved ? ' (RÃ©servÃ©e)' : ''}">
                    <span class="text-lg">${seatNumber}</span>
                    <span class="text-xs">${isReserved ? 'âœ—' : (isSelected ? 'âœ“' : '')}</span>
                </div>
            `;
        }

        html += `
                    </div>
                </div>

                <!-- AllÃ©e -->
                <div class="w-20 h-2 bg-gray-400 rounded my-8"></div>

                <!-- CÃ´tÃ© droit -->
                <div class="flex flex-col items-center">
                    <div class="text-sm text-gray-600 mb-2">RangÃ©e ${ranger}</div>
                    <div class="flex gap-3">
        `;

        // Places cÃ´tÃ© droit
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
                     title="Place ${seatNumber}${isReserved ? ' (RÃ©servÃ©e)' : ''}">
                    <span class="text-lg">${seatNumber}</span>
                    <span class="text-xs">${isReserved ? 'âœ—' : (isSelected ? 'âœ“' : '')}</span>
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
                    SÃ©lectionnez ${selectedNumberOfPlaces} place${selectedNumberOfPlaces > 1 ? 's' : ''} pour le retour.
                    Les places en rouge sont dÃ©jÃ  rÃ©servÃ©es.
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
                text: `Vous ne pouvez sÃ©lectionner que ${selectedNumberOfPlaces} place(s). DÃ©sÃ©lectionnez d'abord une place si vous voulez en choisir une autre.`,
                confirmButtonColor: '#3b82f6',
            });
            return;
        }
        selectedSeatsRetour.push(seatNumber);
    } else {
        selectedSeatsRetour.splice(index, 1);
    }

    // Mettre Ã  jour l'affichage
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
            checkmark.textContent = isSelected ? 'âœ“' : '';
        }
    }

    updateSelectedSeatsCountRetour();
}

function updateSelectedSeatsCountRetour() {
    const count = selectedSeatsRetour.length;
    const countElement = document.getElementById('selectedSeatsCountRetour');
    const nextBtn = document.getElementById('showPassengerInfoBtnRetour');

    countElement.textContent =
        `${count} place${count > 1 ? 's' : ''} sÃ©lectionnÃ©e${count > 1 ? 's' : ''} / ${selectedNumberOfPlaces} demandÃ©e${selectedNumberOfPlaces > 1 ? 's' : ''}`;

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
            seatElement.querySelector('.text-xs').textContent = 'âœ“';
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
    // On cache AUSSI l'Ã©tape Retour (C'est Ã§a qui manque)
    document.getElementById('step2_5').classList.add('hidden');
    
    // On affiche l'Ã©tape 3
    document.getElementById('step3').classList.remove('hidden');
    document.getElementById('confirmReservationBtn').disabled = false;
            }

            sortedSeats.forEach((seat, index) => {
                // On met des valeurs vides par dÃ©faut, l'autofill se fera via le toggle
                let defaultNom = '';
                let defaultPrenom = '';
                let defaultTel = '';
                let defaultEmail = '';

                // On garde l'index pour savoir si c'est le "Passager principal"
                const isMainPassenger = index === 0;

                const passengerHtml = `
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 transition-all hover:shadow-md" id="passenger_card_${seat}">
                        <h4 class="font-bold text-[#e94f1b] mb-4 flex items-center gap-2">
                            <i class="fas fa-user"></i> Passager pour la place nÂ°${seat}
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">PrÃ©nom</label>
                                <input type="text" name="passenger_${seat}_prenom" required
                                    value="${defaultPrenom}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all"
                                    placeholder="PrÃ©nom du passager">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">TÃ©lÃ©phone</label>
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">Contact d'urgence (Nom & TÃ©l)</label>
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
            // Si c'est un aller-retour et que l'utilisateur a sÃ©lectionnÃ© des places retour,
            // retourner Ã  step2_5 (sÃ©lection places retour)
            if (window.userChoseAllerRetour && selectedSeatsRetour.length > 0) {
                document.getElementById('step3').classList.add('hidden');
                document.getElementById('step2_5').classList.remove('hidden');
            } else {
                // Sinon, retourner Ã  step2 (sÃ©lection places aller)
                document.getElementById('step3').classList.add('hidden');
                document.getElementById('step2').classList.remove('hidden');
            }
        }

        // ============================================
        // FONCTION 10.3: Autofill Passager 1
        // ============================================
        function toggleAutofill(isChecked) {
            if (!window.currentUser || selectedSeats.length === 0) return;
            
            // Le premier siÃ¨ge sÃ©lectionnÃ© (triÃ©)
            const sortedSeats = [...selectedSeats].sort((a, b) => a - b);
            const firstSeat = sortedSeats[0];
            
            const user = window.currentUser;
            const fields = [
                { name: 'nom', value: user.name || '' },
                { name: 'prenom', value: user.prenom || '' },
                { name: 'telephone', value: user.contact || '' },
                { name: 'email', value: user.email || '' }
            ];
            
            // Si l'utilisateur a un contact d'urgence (Ã  vÃ©rifier si dispo dans le modÃ¨le User)
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
        // FONCTION 11: Confirmer la rÃ©servation
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
            // 1. DÃ©finition de la variable
            let dateVoyageFinal = window.outboundDate || window.currentReservationDate;

            // 2. Tentative de rÃ©cupÃ©ration depuis le HTML si vide
            if (!dateVoyageFinal && document.getElementById('reservationProgramInfo')) {
                const text = document.getElementById('reservationProgramInfo').innerText;
                const dateMatch = text.match(/\d{2}\/\d{2}\/\d{4}/);
                if (dateMatch) {
                    const [day, month, year] = dateMatch[0].split('/');
                    dateVoyageFinal = `${year}-${month}-${day}`;
                }
            }

            console.log("Date finale pour rÃ©servation:", dateVoyageFinal);
            console.log("DEBUG VARIABLES:", {
                'window.outboundDate': window.outboundDate,
                'window.currentReservationDate': window.currentReservationDate,
                'determinedDate': dateVoyageFinal
            });

            if (!dateVoyageFinal) {
                Swal.fire({ icon: 'error', title: 'Erreur', text: 'Impossible de dÃ©terminer la date du voyage.', confirmButtonColor: '#e94f1b' });
                return;
            }

            Swal.fire({
                title: 'Confirmer la rÃ©servation',
                html: `
                    <div class="text-left">
                        <p class="mb-3">Voulez-vous confirmer la rÃ©servation de <strong>${selectedNumberOfPlaces} place(s)</strong> ?</p>
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
                        Swal.fire({icon: 'error', title: 'Erreur', text: 'Erreur technique: Prix introuvable. Veuillez rafraÃ®chir la page.'});
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
                                     <p class="text-gray-600 text-sm">Total Ã  payer</p>
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
        const response = await fetch("{{ route('reservation.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                programme_id: currentProgramId,
                seats: sortedSeats,
                seats_retour: selectedSeatsRetour.length > 0 ? selectedSeatsRetour.sort((a, b) => a - b) : [],
                nombre_places: selectedNumberOfPlaces,
                date_voyage: dateVoyageFinal,
                is_aller_retour: window.userChoseAllerRetour,
                date_retour: window.selectedReturnDate,
                passagers: passengers,
                payment_method: paymentMethod,
                // CORRECTION: Ajouter les IDs de gares
                gare_depart_id: window.selectedGareDepartId || null,
                gare_arrivee_id: window.selectedGareArriveeId || null,
                // CORRECTION IMPORTANTE: Ajouter l'heure de départ
                heure_depart: window.selectedDepartureTime || null
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
            // Gestion du click sur tous les boutons dÃ©tails vÃ©hicule
            document.querySelectorAll('.vehicle-details-btn').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const vehicleId = this.getAttribute('data-vehicle-id');
                    if (vehicleId) {
                        showVehicleDetails(parseInt(vehicleId));
                    }
                });
            });

            // EmpÃªcher la fermeture du modal en cliquant Ã  l'extÃ©rieur
            const modal = document.getElementById('reservationModal');
            if (modal) {
                modal.addEventListener('click', function (e) {
                    if (e.target === this) {
                        closeReservationModal();
                    }
                });
            }

            // Touche Ã‰chap pour fermer le modal
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
            
            // Mettre Ã  jour le titre
            if(title) title.textContent = 'Choisir votre ligne';
            if(subtitle) subtitle.textContent = 'SÃ©lectionnez votre trajet pour voir les disponibilitÃ©s';
            
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
                    : `Ã€ partir de ${Number(route.prix_min).toLocaleString('fr-FR')} FCFA`;
                
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

        // Nouvelle fonction: dÃ¨s qu'on sÃ©lectionne une ligne, demander la date de dÃ©part
        function selectRouteAndLaunchFlow(route) {
            // Cacher la liste inline pour faire place Ã  la suite ou la laisser ? 
            // UX: On peut la laisser visible ou la cacher. Cachons-la pour focus.
            // document.getElementById('inlineProgramsList').classList.add('hidden');
            
            // Afficher sÃ©lecteur de date de dÃ©part
            showDepartureDateSelection(route);
        }

        // GÃ©nÃ©rateur de calendrier mensuel
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
            
            // En-tÃªte avec jours de la semaine
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

        // SÃ©lection de la date de dÃ©part pour "Voir tous les voyages" avec calendrier mensuel
        function showDepartureDateSelection(route) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            // Calculer demain pour le dÃ©but des rÃ©servations possibles
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            
            // Date max = date_fin du programme (31/12/2026 par dÃ©faut ou depuis route)
            const maxDate = route.date_fin ? new Date(route.date_fin) : new Date(today.getFullYear(), 11, 31);
            
            let currentMonth = tomorrow.getMonth();
            let currentYear = tomorrow.getFullYear();
            
            function updateCalendar() {
                const monthNames = ['Janvier', 'FÃ©vrier', 'Mars', 'Avril', 'Mai', 'Juin', 
                                   'Juillet', 'AoÃ»t', 'Septembre', 'Octobre', 'Novembre', 'DÃ©cembre'];
                
                // Utiliser tomorrow comme minDate
                const calendarHtml = generateMonthlyCalendar(currentMonth, currentYear, tomorrow, maxDate, 'orange');
                
                Swal.update({
                    html: `
                        <div class="text-left space-y-4">
                            <div class="bg-orange-50 p-3 rounded-lg border border-orange-200">
                                <p class="font-bold text-gray-800">${route.point_depart} â†’ ${route.point_arrive}</p>
                                <p class="text-sm text-gray-600">SÃ©lectionnez votre date de dÃ©part</p>
                            </div>
                            
                            <!-- Option rapide Demain -->
                            <div class="flex justify-center">
                                <button id="btnSelectTomorrow" class="flex items-center gap-2 bg-orange-100 text-orange-700 px-4 py-2 rounded-lg font-bold hover:bg-orange-200 transition-colors border border-orange-300">
                                    <i class="fas fa-magic"></i>
                                    <span>SÃ©lectionner demain (${tomorrow.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' })})</span>
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
                
                // RÃ©attacher les Ã©vÃ©nements
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
                // Navigation mois prÃ©cÃ©dent
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
                        // Ne pas aller aprÃ¨s le mois de maxDate
                        const checkDate = new Date(currentYear, currentMonth, 1);
                        if (checkDate <= new Date(maxDate.getFullYear(), maxDate.getMonth(), 1)) {
                            updateCalendar();
                        } else {
                            currentMonth = maxDate.getMonth();
                            currentYear = maxDate.getFullYear();
                        }
                    });
                }
                
                // SÃ©lection de date
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
                title: '<i class="fas fa-calendar-day text-orange-600"></i> Date de dÃ©part',
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

        // Charger les horaires et lancer le flux unifiÃ©
        async function loadSchedulesAndLaunchFlow(route, selectedDate) {
            // Afficher un loader
            Swal.fire({
                title: 'Chargement des disponibilitÃ©s...',
                text: `${route.point_depart} â†’ ${route.point_arrive}`,
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            
            try {
                // 1. Charger les horaires ALLER pour la date sÃ©lectionnÃ©e
                const paramsAller = new URLSearchParams({
                    point_depart: route.point_depart,
                    point_arrive: route.point_arrive,
                    date: selectedDate,
                    compagnie_id: route.compagnie_id // CRUCIAL : Filtrer par compagnie
                });
                const responseAller = await fetch('{{ route("api.route-schedules") }}?' + paramsAller);
                const dataAller = await responseAller.json();
                
                // 2. Charger les horaires RETOUR pour vÃ©rifier la disponibilitÃ©
                const paramsRetour = new URLSearchParams({
                    original_arrive: route.point_arrive,
                    original_depart: route.point_depart,
                    min_date: selectedDate
                });
                const responseRetour = await fetch('{{ route("api.return-trips") }}?' + paramsRetour);
                const dataRetour = await responseRetour.json();
                
                Swal.close();
                
                // 3. Construire l'objet routeData pour le modal unifiÃ©
                const routeData = {
                    ...route, // Inclut date_fin si prÃ©sent dans route
                    aller_horaires: dataAller.success ? dataAller.schedules : [],
                    has_retour: (dataRetour.success && dataRetour.return_trips && dataRetour.return_trips.length > 0),
                    retour_horaires: (dataRetour.success ? dataRetour.return_trips : []),
                    compagnie: route.compagnie?.name || 'Compagnie',
                    montant_billet: dataAller.schedules && dataAller.schedules.length > 0 ? dataAller.schedules[0].montant_billet : (route.prix_min || 0),
                    date_fin: route.date_fin || dataAller.schedules?.[0]?.date_fin || null // S'assurer que date_fin est prÃ©sent
                };
                
                // 4. Lancer la sÃ©lection de l'heure (DÃ©part)
                if (typeof window.showRouteDepartureTimes === 'function') {
                    window.showRouteDepartureTimes(routeData, selectedDate, window.userWantsAllerRetour);
                } else {
                    console.error('showRouteDepartureTimes non disponible');
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur technique',
                        text: 'Impossible de charger le systÃ¨me de rÃ©servation.'
                    });
                }
                
            } catch (error) {
                console.error('Erreur lors du chargement:', error);
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Impossible de charger les horaires. Veuillez rÃ©essayer.'
                }).then(() => {
                    // Retour Ã  la sÃ©lection de date
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
                                    <p class="text-xs text-gray-500">DÃ©part</p>
                                </div>
                                <div class="flex items-center gap-2 text-gray-400">
                                    <div class="w-8 h-0.5 bg-gray-300"></div>
                                    <i class="fas fa-bus text-sm"></i>
                                    <div class="w-8 h-0.5 bg-gray-300"></div>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-gray-700">${prog.heure_arrive}</p>
                                    <p class="text-xs text-gray-500">ArrivÃ©e</p>
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
                                    <span>RÃ©server</span>
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
        // FONCTION 14: Gestion modale sÃ©lection date
        // ============================================
         function openDateSelectionModal(program) {
            currentSelectedProgram = program;
            document.getElementById('dateSelectionModal').classList.remove('hidden');
            // Logique de remplissage date similaire Ã  populateReturnDateSelect mais pour l'aller...
            const select = document.getElementById('recurrenceDateSelect');
            select.innerHTML = '<option value="">Chargement...</option>';
            
            // Jours Aller
            let allowedDays = [];
            try { allowedDays = JSON.parse(program.jours_recurrence || '[]'); } catch(e){}
            allowedDays = allowedDays.map(d => d.toLowerCase());
            
            const daysMap = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
            const dates = [];
            let current = new Date();
             current.setDate(current.getDate() + 1); // Commencer Ã  demain
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
                 if(count > 100) break; // SÃ©curitÃ©
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

        // Fonction pour afficher les dÃ©tails (places disponibles)
        async function openDetailsModal(btn) {
            const route = JSON.parse(btn.dataset.route);
            const dateDepart = btn.dataset.date;
            
            Swal.fire({
                title: 'Chargement des dÃ©tails...',
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
                    html += `<h3 class="font-bold text-lg text-blue-600 mt-6 mb-3 uppercase border-b pb-2">Retour (AperÃ§u)</h3>`;
                    
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
                         html += `<p class="text-gray-500 italic">Aucun horaire retour trouvÃ© pour cette date.</p>`;
                    }
                }

                html += `</div>`;

                Swal.fire({
                    title: `DÃ©tails du voyage`,
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
                Swal.fire('Erreur', 'Impossible de charger les dÃ©tails.', 'error');
            }
        }

        function buildSchedulesTable(schedules, dateVoyage) {
            let table = `
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-4 py-2">DÃ©part</th>
                                <th class="px-4 py-2">ArrivÃ©e</th>
                                <th class="px-4 py-2">VÃ©hicule</th>
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
                <!-- En-tÃªte -->
                <div class="text-center border-b pb-4">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fas fa-bus text-[#e94f1b] text-2xl"></i></div>
                    <h3 class="text-xl font-bold text-gray-900">Ce programme propose un aller-retour</h3>
                    <p class="text-sm text-gray-500 mt-1">Choisissez le type de voyage que vous souhaitez</p>
                </div>
                
                <!-- Infos du trajet -->
                <div id="allerRetourTripInfo" class="py-2">
                    <!-- Contenu injectÃ© par JS -->
                </div>
                
                <!-- Choix du type de voyage -->
                <div class="py-2">
                    <label for="allerRetourChoice" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-route me-1"></i> Type de voyage
                    </label>
                    <div class="relative">
                        <select id="allerRetourChoice" onchange="onAllerRetourChoiceChange()" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-[#e94f1b] appearance-none bg-white font-medium text-gray-700">
                            <option value="aller_simple">ðŸšŒ Aller Simple</option>
                            <option value="aller_retour">ðŸ”„ Aller-Retour</option>
                        </select>
                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Affichage du prix dynamique -->
                <div id="allerRetourPriceDisplay" class="text-center">
                    <!-- Contenu injectÃ© par JS -->
                </div>
                
                <!-- SÃ©lection date retour (pour rÃ©currents + aller-retour) -->
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
     <!-- Modal Date Selection (RÃ©current) -->
    <div id="dateSelectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[70] flex items-center justify-center">
        <div class="relative w-96 mx-auto p-6 border shadow-2xl rounded-2xl bg-white">
            <div class="flex flex-col gap-4">
                <div class="border-b pb-4">
                    <h3 class="text-xl font-bold text-gray-900">Choisir une date de voyage</h3>
                    <p class="text-sm text-gray-500 mt-1">Ce programme est rÃ©current.</p>
                </div>
                
                <div class="py-4">
                    <label for="recurrenceDateSelect" class="block text-sm font-medium text-gray-700 mb-2">SÃ©lectionnez une date parmi les prochains jours disponibles :</label>
                    <div class="relative">
                        <select id="recurrenceDateSelect" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent appearance-none bg-white">
                            <!-- Options gÃ©nÃ©rÃ©es par JS -->
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

<input type="hidden" id="selected_gare_depart_id" name="gare_depart_id">
<input type="hidden" id="selected_gare_arrivee_id" name="gare_arrivee_id">
@endsection

@push('scripts')
<script>
    // Variables globales pour stocker la gare sélectionnée
    // Ces variables seront utilisées lors de la confirmation de réservation
    window.selectedGareDepartId = null;
    window.selectedGareArriveeId = null;

    // Remplacement de la fonction handleReservationClick pour intégrer la sélection de gare
    window.handleReservationClick = function(button) {
        console.log("Bouton réserver cliqué - Flux avec Gare");
        
        const routeDataJson = button.getAttribute('data-route');
        const dateDepart = button.getAttribute('data-date');
        
        if (!routeDataJson) {
            console.error("Pas de données data-route trouvées");
            return;
        }

        try {
            const routeData = JSON.parse(routeDataJson);
            // Stocker les infos courantes dans window pour accès global
            window.currentRouteData = routeData; 
            window.currentRouteData.date_depart = dateDepart;

            // Réinitialiser la sélection de gare
            window.selectedGareDepartId = null;
            window.selectedGareArriveeId = null;

            // Vérifier s'il y a des gares configurées
            const gareDepart = routeData.gare_depart;
            
            // Si pas de gare ou gare vide -> Flux standard direct
            if (!gareDepart) {
                launchOriginalFlow(routeData, dateDepart);
                return;
            }

            // Sinon -> Afficher le modal de sélection de gare
            showGareSelectionModal();
            
        } catch (e) {
            console.error('Erreur JS lors du clic réservation:', e);
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur est survenue lors de l\'initialisation de la réservation.'
            });
        }
    };

    function showGareSelectionModal() {
        const routeData = window.currentRouteData;
        const gareDepart = routeData.gare_depart;
        const gareOptions = document.getElementById('gareOptions');
        const modal = document.getElementById('gareSelectionModal');
        
        if (!modal) {
            console.error("Modal de sélection de gare introuvable (#gareSelectionModal)");
            // Fallback flux normal
            launchOriginalFlow(routeData, routeData.date_depart);
            return;
        }
        
        if (gareOptions) gareOptions.innerHTML = '';
        
        // Cas 1: Une seule gare (Objet) ou Tableau de 1 élément
        let singleGare = null;
        if (gareDepart && !Array.isArray(gareDepart) && typeof gareDepart === 'object') {
            singleGare = gareDepart;
        } else if (Array.isArray(gareDepart) && gareDepart.length === 1) {
            singleGare = gareDepart[0];
        }

        if (singleGare) {
            console.log("Une seule gare détectée, sélection automatique:", singleGare);
            selectGareAndContinue(singleGare.id);
            return;
        }
        
        // Cas 2: Plusieurs gares (Tableau)
        if (Array.isArray(gareDepart) && gareDepart.length > 0) {
             gareDepart.forEach(gare => {
                 const btn = document.createElement('button');
                 btn.className = 'w-full p-4 border-2 border-gray-200 rounded-xl hover:border-[#e94f1b] hover:bg-orange-50 transition-all text-left mb-3 group';
                 btn.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center group-hover:bg-[#e94f1b] transition-colors">
                            <i class="fas fa-building text-gray-500 group-hover:text-white text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">${gare.nom_gare}</h4>
                            ${gare.adresse ? `<p class="text-sm text-gray-600">${gare.adresse}</p>` : ''}
                        </div>
                    </div>
                `;
                btn.onclick = () => selectGareAndContinue(gare.id);
                gareOptions.appendChild(btn);
             });
        } 
        // Cas 3: Objet unique (pour être sûr)
        else if (gareDepart && typeof gareDepart === 'object') {
             const btn = document.createElement('button');
             btn.className = 'w-full p-4 border-2 border-gray-200 rounded-xl hover:border-[#e94f1b] hover:bg-orange-50 transition-all text-left mb-3 group';
             btn.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center group-hover:bg-[#e94f1b] transition-colors">
                        <i class="fas fa-building text-gray-500 group-hover:text-white text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">${gareDepart.nom_gare}</h4>
                        ${gareDepart.adresse ? `<p class="text-sm text-gray-600">${gareDepart.adresse}</p>` : ''}
                    </div>
                </div>
            `;
            btn.onclick = () => selectGareAndContinue(gareDepart.id);
            gareOptions.appendChild(btn);
        }

        modal.classList.remove('hidden');
    }

    function closeGareSelectionModal() {
        const modal = document.getElementById('gareSelectionModal');
        if (modal) modal.classList.add('hidden');
    }

    function selectGareAndContinue(gareId) {
        window.selectedGareDepartId = gareId;
        // On suppose que la gare d'rivée est unique ou déduite du trajet
        if (window.currentRouteData.gare_arrivee) {
            window.selectedGareArriveeId = window.currentRouteData.gare_arrivee.id;
        }
        
        closeGareSelectionModal();
        launchOriginalFlow(window.currentRouteData, window.currentRouteData.date_depart);
    }

    function launchOriginalFlow(routeData, dateDepart) {
        // Appeler la fonction SweetAlert existante pour choisir le type de voyage
        if (typeof window.showRouteTripTypeModal === 'function') {
            window.showRouteTripTypeModal(routeData, dateDepart);
        } else {
            console.error("La fonction window.showRouteTripTypeModal est introuvable. Code manquant ?");
            // Fallback
             Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Impossible de lancer le flux de réservation.'
            });
        }
    }
</script>
@endpush
