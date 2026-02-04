@extends('user.layouts.template')
@section('content')
    <div class="min-h-screen bg-gradient-to-br from-white to-blue-50 py-4 sm:py-6 lg:py-8">
        <div class="w-full px-3 sm:px-4 lg:px-6">

            <!-- Formulaire de recherche -->
            <div class="mb-6 sm:mb-8">
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 border border-gray-100">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">Rechercher un voyage</h2>

                    <form action="{{ route('reservation.create') }}" method="GET" id="search-form">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 sm:gap-6">
                            <!-- Point de départ -->
                            <div class="relative">
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

                            <!-- Point d'arrivée -->
                            <div class="relative">
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

                            <!-- Date de départ -->
                            <div class="relative">
                                <label for="date_depart" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar text-blue-500 mr-2"></i>Date de départ
                                </label>
                                <div class="relative">
                                    <input type="date" id="date_depart" name="date_depart"
                                        value="{{ $searchParams['date_depart'] ?? date('Y-m-d') }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 pl-12"
                                        min="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <!-- Heure de départ souhaitée -->
                            <div class="relative">
                                <label for="heure_depart" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-clock text-purple-500 mr-2"></i>Heure souhaitée
                                </label>
                                <div class="relative">
                                    <input type="time" id="heure_depart" name="heure_depart"
                                        value="{{ $searchParams['heure_depart'] ?? '' }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 pl-12"
                                        placeholder="Ex: 08:00">
                                    <p class="text-xs text-gray-400 mt-1">Service continu 24h/24</p>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row gap-4 justify-center items-center">
                            <button type="submit"
                                class="bg-[#e94f1b] text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg sm:rounded-xl font-bold hover:bg-orange-600 transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl flex items-center justify-center gap-3 text-sm sm:text-base">
                                <i class="fas fa-search"></i>
                                <span>Rechercher un voyage</span>
                            </button>
                            <button type="button" onclick="openProgramsListModal()"
                                class="bg-white text-[#e94f1b] border-2 border-[#e94f1b] px-6 sm:px-8 py-3 sm:py-4 rounded-lg sm:rounded-xl font-bold transition-all duration-300 transform hover:-translate-y-1 shadow-md hover:shadow-xl flex items-center justify-center gap-3 text-sm sm:text-base hover:bg-[#e94f1b]">
                                <i class="fas fa-list group-hover:text-white transition-colors"></i>
                                <span>Voir tous les voyages</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
             <!-- Résultats de recherche -->
            @if (isset($programmes) && $programmes->count() > 0)
                <div class="mb-6 sm:mb-8">
                    <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 border border-gray-100 mb-6">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Résultats de recherche</h2>
                            <span class="bg-[#e94f1b] text-white px-4 py-2 rounded-xl font-bold text-lg">
                                {{ $programmes->total() }} programme(s) trouvé(s)
                            </span>
                        </div>

                        <!-- Filtres appliqués -->
                        <div class="mt-4 flex flex-wrap gap-2">
                            <div class="flex items-center gap-2 bg-orange-50 px-3 py-1 rounded-full">
                                <i class="fas fa-map-marker-alt text-[#e94f1b]"></i>
                                <span class="font-semibold">{{ $searchParams['point_depart'] }}</span>
                            </div>
                            <i class="fas fa-arrow-right text-[#e94f1b] my-auto"></i>
                            <div class="flex items-center gap-2 bg-green-50 px-3 py-1 rounded-full">
                                <i class="fas fa-flag text-green-500"></i>
                                <span class="font-semibold">{{ $searchParams['point_arrive'] }}</span>
                            </div>
                            <div class="flex items-center gap-2 bg-blue-50 px-3 py-1 rounded-full">
                                <i class="fas fa-calendar text-blue-500"></i>
                                <span>{{ date('d/m/Y', strtotime($searchParams['date_depart'])) }}</span>
                            </div>
                            @if(!empty($searchParams['heure_depart']))
                            <div class="flex items-center gap-2 bg-purple-50 px-3 py-1 rounded-full">
                                <i class="fas fa-clock text-purple-500"></i>
                                <span>{{ $searchParams['heure_depart'] }}</span>
                            </div>
                            @else
                            <div class="flex items-center gap-2 bg-gray-50 px-3 py-1 rounded-full">
                                <i class="fas fa-infinity text-gray-500"></i>
                                <span>24h/24</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Liste des programmes -->
                    <!-- En-tête de la liste (version desktop) -->
                    <div class="hidden md:block mb-4">
                        <div class="bg-gradient-to-r from-[#e94f1b]/10 to-orange-500/10 rounded-xl p-4">
                            <div class="grid grid-cols-12 gap-4 text-sm font-semibold text-gray-700">
                                <div class="col-span-3 text-center">Compagnie & Trajet</div>
                                <div class="col-span-2 text-center">Date & Heure</div>
                                <div class="col-span-2 text-center">Durée & Prix</div>
                                <div class="col-span-2 text-center">Statut</div>
                                <div class="col-span-3 text-center">Actions</div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @foreach ($programmes as $programme)
                            <div
                                class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                                <!-- Version Mobile -->
                                <div class="block md:hidden">
                                    <div class="p-4 space-y-4">
                                        <!-- En-tête mobile -->
                                        <div class="flex items-start gap-3 pb-3 border-b border-gray-100">
                                            <div
                                                class="w-12 h-12 bg-gradient-to-r from-[#e94f1b] to-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-bus text-white text-lg"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h3 class="font-bold text-gray-900 truncate">
                                                        {{ $programme->compagnie->name ?? 'Compagnie' }}
                                                    </h3>
                                                </div>
                                                <div class="flex items-center gap-2 text-sm">
                                                    <span class="font-semibold text-gray-900">{{ $programme->point_depart }}</span>
                                                    <i class="fas fa-arrow-right text-[#e94f1b] text-xs"></i>
                                                    <span class="font-semibold text-gray-900">{{ $programme->point_arrive }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Infos mobile -->
                                        <div class="grid grid-cols-2 gap-3 text-sm">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-clock text-green-500"></i>
                                                <span>{{ $programme->heure_depart }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-hourglass-half text-purple-500"></i>
                                                <span class="font-semibold">{{ $programme->durer_parcours }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 col-span-2">
                                                <i class="fas fa-money-bill-wave text-red-500"></i>
                                                <span class="font-bold text-lg text-[#e94f1b]">
                                                    {{ number_format($programme->montant_billet, 0, ',', ' ') }} FCFA
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Statut mobile -->
                                        @php
                                            $statusTexts = [
                                                'actif' => 'Places disponibles',
                                                'complet' => 'Complet',
                                                'annule' => 'Annulé',
                                            ];
                                            $statusColors = [
                                                'actif' => 'bg-green-100 text-green-800 border-green-200',
                                                'complet' => 'bg-red-100 text-red-800 border-red-200',
                                                'annule' => 'bg-gray-100 text-gray-600 border-gray-200',
                                            ];
                                            $statut = $programme->statut ?? 'actif';
                                        @endphp
                                        <div class="flex items-center justify-center">
                                            <span
                                                class="px-3 py-1 rounded-full text-sm font-semibold border {{ $statusColors[$statut] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                                                {{ $statusTexts[$statut] ?? 'Statut inconnu' }}
                                            </span>
                                        </div>

                                        <!-- Actions mobile -->
                                        <div class="flex gap-2">
                                            @if ($statut != 'complet' && $statut != 'annule')
                                        <button 
    onclick="initiateReservationProcess(
        {{ $programme->id }}, 
        '{{ request('date_depart') ? date('Y-m-d', strtotime(request('date_depart'))) : (isset($programme->date_depart) ? date('Y-m-d', strtotime($programme->date_depart)) : '') }}',
        '{{ request('heure_depart') ?? '' }}' 
    )"
    class="bg-[#e94f1b] text-white px-4 py-2 rounded-lg font-bold hover:bg-orange-600 transition-all duration-300 flex items-center gap-2">
    <i class="fas fa-ticket-alt"></i>
    <span>Réserver</span>
</button>
                                            @else
                                                <button
                                                    class="flex-1 bg-gray-400 text-white text-center py-2 rounded-lg font-bold cursor-not-allowed flex items-center justify-center gap-2"
                                                    disabled>
                                                    <i class="fas fa-times-circle"></i>
                                                    <span>Complet</span>
                                                </button>
                                            @endif
                                            <button
                                                onclick="showVehicleDetails({{ $programme->vehicule_id ?? 'null' }}, {{ $programme->id }})"
                                                class="w-12 bg-white text-[#e94f1b] border border-[#e94f1b] text-center py-2 rounded-lg font-bold hover:bg-gray-50 transition-all duration-300 flex items-center justify-center">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
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
                                                    class="w-12 h-12 bg-gradient-to-r from-[#e94f1b] to-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-bus text-white text-lg"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2 mb-1"
                                                        style="display:flex; justify-content:center">
                                                        <h3 class="font-bold text-gray-900 truncate">
                                                            {{ $programme->compagnie->name ?? 'Compagnie' }}
                                                        </h3>
                                                        @if($programme->compagnie)
                                                            <span
                                                                class="bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full font-semibold">
                                                                <i class="fas fa-check-circle text-xs"></i> Vérifié
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center gap-2 text-sm text-gray-600"
                                                        style="display:flex; justify-content:center">
                                                        <div class="font-semibold text-gray-900">{{ $programme->point_depart }}
                                                        </div>
                                                        <i class="fas fa-arrow-right text-[#e94f1b] text-xs"></i>
                                                        <div class="font-semibold text-gray-900">{{ $programme->point_arrive }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Date & Heure -->
                                        <div class="col-span-2" style="display:flex; justify-content:center">
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-2 text-sm">
                                                    <i class="fas fa-clock text-green-500"></i>
                                                    <span>{{ $programme->heure_depart }} → {{ $programme->heure_arrive }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Durée & Prix -->
                                        <div class="col-span-2" style="display:flex; justify-content:center">
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-2" style="display:flex; justify-content:center">
                                                    <i class="fas fa-hourglass-half text-purple-500"></i>
                                                    <span class="font-semibold">{{ $programme->durer_parcours }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-money-bill-wave text-red-500"></i>
                                                    <span class="font-bold text-lg text-[#e94f1b]">
                                                        {{ number_format($programme->montant_billet, 0, ',', ' ') }} FCFA
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Statut -->
                                        <div class="col-span-2" style="display:flex; justify-content:center">
                                            <span
                                                class="px-3 py-1 rounded-full text-sm font-semibold border {{ $statusColors[$statut] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                                                {{ $statusTexts[$statut] ?? 'Statut inconnu' }}
                                            </span>
                                        </div>

                                        <!-- Actions -->
                                        <div class="col-span-3">
                                            <div class="flex gap-2 justify-end">
                                                <button
                                                    onclick="showVehicleDetails({{ $programme->vehicule_id ?? 'null' }}, {{ $programme->id }})"
                                                    class="bg-white text-[#e94f1b] border border-[#e94f1b] px-4 py-2 rounded-lg font-bold hover:bg-gray-50 transition-all duration-300 flex items-center gap-2">
                                                    <i class="fas fa-info-circle"></i>
                                                    <span>Détails véhicule</span>
                                                </button>

                                                @if ($statut != 'complet' && $statut != 'annule')
                                                  <button 
    onclick="initiateReservationProcess(
        {{ $programme->id }}, 
        '{{ request('date_depart') ? date('Y-m-d', strtotime(request('date_depart'))) : (isset($programme->date_depart) ? date('Y-m-d', strtotime($programme->date_depart)) : '') }}'
    )"
    class="bg-[#e94f1b] text-white px-4 py-2 rounded-lg font-bold hover:bg-orange-600 transition-all duration-300 flex items-center gap-2">
    <i class="fas fa-ticket-alt"></i>
    <span>Réserver</span>
</button>
                                                @else
                                                    <button
                                                        class="bg-gray-400 text-white px-4 py-2 rounded-lg font-bold cursor-not-allowed flex items-center gap-2"
                                                        disabled>
                                                        <i class="fas fa-times-circle"></i>
                                                        <span>Complet</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if ($programmes->hasPages())
                        <div class="mt-6">
                            {{ $programmes->links() }}
                        </div>
                    @endif
                </div>
            @elseif(isset($programmes))
                <!-- Aucun résultat -->
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-orange-100 to-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
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

                    <!-- Étape 3: Informations des passagers -->
                    <div id="step3" class="hidden">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900">Informations des passagers</h3>
                            <button onclick="backToStep2()"
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
                            <button onclick="backToStep2()"
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
        // Variables globales
         let currentProgramId = null;
        let currentSelectedProgram = null; 
        let selectedNumberOfPlaces = 0;
        let selectedSeats = [];
        let reservedSeats = [];
        let vehicleDetails = null;
        let currentRequestId = 0;
        let userWantsAllerRetour = false;


        // Configuration des types de rangées
        const typeRangeConfig = {
            '2x2': {
                placesGauche: 2,
                placesDroite: 2
            },
            '2x3': {
                placesGauche: 2,
                placesDroite: 3
            },
            '2x4': {
                placesGauche: 2,
                placesDroite: 4
            }
        };
 // --- NOUVELLE FONCTION PRINCIPALE D'INITIATION ---
        // C'est elle qui est appelée par le bouton "Réserver"
     async function initiateReservationProcess(programId, searchDateFormatted, searchedTime = null) {
    console.log("Initiation réservation pour ID:", programId, "Date:", searchDateFormatted, "Heure cherchée:", searchedTime);
    
    // 1. Réinitialisation des variables globales
    userWantsAllerRetour = false;
    window.selectedReturnProgram = null;
    window.outboundProgram = null;
    window.outboundDate = searchDateFormatted;
    window.selectedDepartureTime = null;
    window.selectedReturnTime = null;
    currentSelectedProgram = null; // Important pour éviter les erreurs de paiement
    
    // Fermer les autres modals
    closeProgramsListModal();

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
        currentSelectedProgram = program; // CRUCIAL : Définit le programme pour le calcul du paiement plus tard
        
        Swal.close();

        // LOGIQUE DE DÉTERMINATION DE L'HEURE
        if (searchedTime && searchedTime.trim() !== '') {
            // Cas 1 : L'utilisateur a filtré par heure dans la recherche
            window.selectedDepartureTime = searchedTime.substring(0, 5);
            console.log('Heure issue de la recherche:', window.selectedDepartureTime);
            showReturnTripOptionModal(program, searchDateFormatted);
        } 
        else if (program.heure_depart && program.heure_depart !== '00:00') {
            // Cas 2 : C'est un horaire fixe (ex: 08:00) du programme
            window.selectedDepartureTime = program.heure_depart.substring(0, 5);
            console.log('Horaire fixe du programme détecté:', window.selectedDepartureTime);
            showReturnTripOptionModal(program, searchDateFormatted);
        } 
        else {
            // Cas 3 : C'est un service continu (24/7) ou pas d'heure définie => On demande
            console.log('Service continu, demande heure...');
            showTimeSelectionModal(program, searchDateFormatted);
        }

    } catch (error) {
        console.error(error);
        Swal.close();
        Swal.fire({ icon: 'error', title: 'Erreur', text: 'Une erreur est survenue.' });
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
                    document.getElementById('choiceReturn').addEventListener('click', () => {
                        userWantsAllerRetour = true;
                        Swal.close();
                        showReturnTimeSelectionModal(program, departureDate);
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
        async function showReturnTripSelector(outboundProgram, outboundDate) {
            Swal.fire({
                title: 'Recherche des retours...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const params = new URLSearchParams({
                    original_depart: outboundProgram.point_depart,
                    original_arrive: outboundProgram.point_arrive,
                    min_date: outboundDate
                });
                
                const response = await fetch('{{ route("api.return-trips") }}?' + params);
                const data = await response.json();
                Swal.close();

                if (!data.success || data.count === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Aucun retour disponible',
                        html: `<p>Aucun voyage retour <strong>${outboundProgram.point_arrive} → ${outboundProgram.point_depart}</strong> n'est disponible après le ${new Date(outboundDate).toLocaleDateString('fr-FR')}.</p>
                               <p class="text-sm text-gray-500 mt-2">Vous pouvez continuer avec un aller simple.</p>`,
                        confirmButtonText: 'Continuer en aller simple',
                        confirmButtonColor: '#e94f1b'
                    }).then(() => {
                        userWantsAllerRetour = false;
                        openReservationModal(outboundProgram.id, outboundDate);
                    });
                    return;
                }

                // Afficher les options de retour
                displayReturnTripOptions(outboundProgram, outboundDate, data.return_trips);

            } catch (error) {
                console.error('Erreur:', error);
                Swal.close();
                Swal.fire({ icon: 'error', title: 'Erreur', text: 'Impossible de charger les voyages retour.' });
            }
        }

        // Afficher les options de voyage retour dans un modal
        function displayReturnTripOptions(outboundProgram, outboundDate, returnTrips) {
            // Grouper par date
            const grouped = {};
            returnTrips.forEach(trip => {
                const date = trip.date_depart;
                if (!grouped[date]) grouped[date] = [];
                grouped[date].push(trip);
            });

            let html = `
                <div class="text-left max-h-[60vh] overflow-y-auto">
                    <div class="bg-green-50 p-3 rounded-lg mb-4 text-sm">
                        <p class="font-bold text-green-800"><i class="fas fa-check-circle mr-2"></i>Aller confirmé</p>
                        <p class="text-green-700">${outboundProgram.point_depart} → ${outboundProgram.point_arrive} le ${new Date(outboundDate).toLocaleDateString('fr-FR')} à ${outboundProgram.heure_depart}</p>
                    </div>
                    <p class="font-bold text-gray-800 mb-3"><i class="fas fa-undo mr-2 text-blue-500"></i>Choisissez votre retour :</p>
            `;

            Object.keys(grouped).sort().forEach(date => {
                const dateFormatted = new Date(date).toLocaleDateString('fr-FR', { weekday: 'short', day: 'numeric', month: 'short' });
                html += `<div class="mb-4"><p class="font-medium text-gray-600 text-sm mb-2">${dateFormatted}</p>`;
                
                grouped[date].forEach(trip => {
                    html += `
                        <div class="return-trip-option border rounded-lg p-3 mb-2 cursor-pointer hover:border-[#e94f1b] hover:bg-orange-50 transition-all" 
                             data-trip-id="${trip.id}" data-trip-date="${date}">
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="font-bold text-lg">${trip.heure_depart}</span>
                                    <span class="text-gray-400 mx-2">→</span>
                                    <span class="text-gray-600">${trip.heure_arrive}</span>
                                </div>
                                <span class="font-bold text-[#e94f1b]">${Number(trip.montant_billet).toLocaleString('fr-FR')} FCFA</span>
                            </div>
                        </div>
                    `;
                });
                html += `</div>`;
            });
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
                                openReservationModal(outboundProgram.id, outboundDate);
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
                                ${window.selectedReturnTime ? `<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm"><i class="fas fa-undo"></i> Retour: ${window.selectedReturnTime}</span>` : ''}
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
        async function showVehicleDetails(vehicleId, programId) {
            console.log(`[DETAILS] Demande détails véhicule ${vehicleId} pour programme ${programId}`);
            if (!vehicleId) {
                Swal.fire({
                    icon: 'info',
                    title: 'Information',
                    text: 'Aucun véhicule associé à ce programme.',
                    confirmButtonColor: '#e94f1b',
                });
                return;
            }
            // Récupérer la date depuis le formulaire de recherche ou l'URL
            let dateVoyage = null;
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
                const response = await fetch(url + `?date=${encodeURIComponent(dateVoyage)}`);
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

            const config = typeRangeConfig[vehicleDetails.type_range];
            if (!config) {
                document.getElementById('seatSelectionArea').innerHTML =
                    '<p class="text-center text-red-500">Configuration de places non reconnue.</p>';
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
                if (!success) return; // Stop si échec
                
                // Petit délai pour montrer les places sélectionnées avant de passer à la suite
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

            sortedSeats.forEach((seat, index) => {
                const passengerHtml = `
                                                                                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                                                                                            <h4 class="font-bold text-[#e94f1b] mb-4 flex items-center gap-2">
                                                                                                <i class="fas fa-user"></i> Passager pour la place n°${seat}
                                                                                            </h4>
                                                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                                                                <div>
                                                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                                                                                                    <input type="text" name="passenger_${seat}_nom" required
                                                                                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all"
                                                                                                        placeholder="Nom du passager">
                                                                                                </div>
                                                                                                <div>
                                                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                                                                                                    <input type="text" name="passenger_${seat}_prenom" required
                                                                                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all"
                                                                                                        placeholder="Prénom du passager">
                                                                                                </div>
                                                                                                <div>
                                                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                                                                                                    <input type="tel" name="passenger_${seat}_telephone" required
                                                                                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all"
                                                                                                        placeholder="Ex: 0700000000">
                                                                                                </div>
                                                                                                <div>
                                                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                                                                                    <input type="email" name="passenger_${seat}_email" required
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

            document.getElementById('confirmReservationBtn').disabled = false;
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step3').classList.remove('hidden');
        }

        // ============================================
        // FONCTION 11: Confirmer la réservation
        // ============================================
        async function confirmReservation() {
            const passengers = [];
            const sortedSeats = [...selectedSeats].sort((a, b) => a - b);
            let isValid = true;

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
                    nom,
                    prenom,
                    telephone,
                    email,
                    urgence
                });
            });

            if (!isValid) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Informations manquantes',
                    text: 'Veuillez remplir toutes les informations pour chaque passager.',
                    confirmButtonColor: '#e94f1b',
                });
                return;
            }

            // Récupérer la date du voyage
            let dateVoyage = window.currentReservationDate;

            if (!dateVoyage) {
                const programInfo = document.getElementById('reservationProgramInfo');
                if (programInfo) {
                    const text = programInfo.textContent || programInfo.innerText;
                    const dateMatch = text.match(/\d{2}\/\d{2}\/\d{4}/);
                    if (dateMatch) {
                        const [day, month, year] = dateMatch[0].split('/');
                        dateVoyage = `${year}-${month}-${day}`;
                    }
                }
            }

            if (!dateVoyage) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Impossible de déterminer la date du voyage.',
                    confirmButtonColor: '#e94f1b',
                });
                return;
            }
            Swal.fire({
                title: 'Confirmer la réservation',
                html: `
                    <div class="text-left">
                        <p class="mb-3">Voulez-vous confirmer la réservation de <strong>${selectedNumberOfPlaces} place(s)</strong> ?</p>
                        <div class="bg-gray-50 p-4 rounded-lg mb-4">
                            <p class="font-semibold mb-2">Date du voyage :</p>
                            <p class="text-lg text-blue-600 font-bold">${dateVoyage}</p>
                            <p class="font-semibold mb-2 mt-4">Places :</p>
                            <p class="text-lg text-[#e94f1b] font-bold">${sortedSeats.join(', ')}</p>
                        </div>
                        <p class="text-sm text-gray-600">Un ticket sera envoyé à l'email de chaque passager.</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#e94f1b',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Oui, continuer',
                cancelButtonText: 'Non, annuler',
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {
                     // SÉCURITÉ : Si currentSelectedProgram est perdu, on utilise window.currentProgramPrice stocké précédemment
    let prixUnitaire = 0;
    if (currentSelectedProgram && currentSelectedProgram.montant_billet) {
        prixUnitaire = parseInt(currentSelectedProgram.montant_billet);
    } else if (window.currentProgramPrice) {
        // Fallback si le programme a été perdu en mémoire mais que le prix a été affiché
        prixUnitaire = window.currentProgramPrice; 
        // Si c'était déjà affiché en aller-retour (doublé), on divise par 2 pour le calcul propre ci-dessous
        if(window.userChoseAllerRetour) {
             prixUnitaire = prixUnitaire / 2;
        }
    } else {
        Swal.fire({icon: 'error', title: 'Erreur', text: 'Impossible de récupérer le prix du billet. Veuillez rafraîchir.'});
        return;
    }
                    // --- Logique de choix du paiement ---
                    const prixUnitaire = parseInt(currentSelectedProgram.montant_billet);
                    const multiplier = window.userChoseAllerRetour ? 2 : 1;
                    const montantTotal = prixUnitaire * selectedNumberOfPlaces * multiplier;
                    const userSolde = {{ auth()->check() ? (auth()->user()->solde ?? 0) : 0 }}; // Injection sécurisée

                    let paymentMethod = 'cinetpay'; // Valeur par défaut

                    // Demander le mode de paiement
                    const choiceResult = await Swal.fire({
                         title: 'Choisissez le mode de paiement',
                         html: `
                             <div class="flex flex-col gap-4 text-center">
                                 <div class="bg-gray-50 p-3 rounded-lg">
                                     <p class="text-gray-600 text-sm">Montant à payer</p>
                                     <p class="text-2xl font-bold text-[#e94f1b]">${new Intl.NumberFormat('fr-FR').format(montantTotal)} FCFA</p>
                                 </div>
                                 
                                 <div class="border-t pt-2">
                                     <p class="text-gray-600 text-sm mb-1">Votre solde actuel</p>
                                     <p class="text-lg font-bold">${new Intl.NumberFormat('fr-FR').format(userSolde)} FCFA</p>
                                     ${userSolde < montantTotal ? 
                                        '<p class="text-red-500 text-xs mt-1 font-bold"><i class="fas fa-exclamation-circle"></i> Solde insuffisant</p>' : 
                                        '<p class="text-green-500 text-xs mt-1"><i class="fas fa-check-circle"></i> Solde suffisant</p>'}
                                 </div>
                             </div>
                         `,
                         icon: 'question', // <--- CORRECTION ICI (au lieu de 'wallet')
                         showCancelButton: true,
                         showDenyButton: true,
                         confirmButtonText: `<i class="fas fa-wallet"></i> Payer via Mon Compte`,
                         denyButtonText: `<i class="fas fa-credit-card"></i> CinetPay (Mobile Money)`,
                         confirmButtonColor: '#e94f1b',
                         denyButtonColor: '#2dce89',
                         cancelButtonText: 'Annuler',
                         reverseButtons: true,
                         didOpen: () => {
                             // Désactiver le bouton Mon Compte si solde insuffisant
                             if (userSolde < montantTotal) {
                                  const confirmBtn = Swal.getConfirmButton();
                                  confirmBtn.disabled = true;
                                  confirmBtn.style.opacity = 0.5;
                                  confirmBtn.title = "Votre solde est insuffisant pour effectuer ce paiement.";
                             }
                         }
                    });

                    if (choiceResult.isConfirmed) {
                        paymentMethod = 'wallet';
                    } else if (choiceResult.isDenied) {
                        paymentMethod = 'cinetpay';
                    } else {
                        return; // Annulation par l'utilisateur
                    }

                    // Afficher loader
                    Swal.fire({
                        title: 'Traitement...',
                        text: paymentMethod === 'wallet' ? 'Paiement et confirmation en cours...' : 'Initialisation du paiement...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    try {
                        const response = await fetch("/user/booking/reservation", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                programme_id: currentProgramId,
                                seats: sortedSeats,
                                nombre_places: selectedNumberOfPlaces,
                                date_voyage: dateVoyage,
                                is_aller_retour: window.userChoseAllerRetour,
                                date_retour: window.selectedReturnDate,
                                passagers: passengers,
                                payment_method: paymentMethod // Transmission du choix
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            if (data.wallet_payment) {
                                // --- SUCCÈS PAIEMENT PORTEFEUILLE ---
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Paiement effectué',
                                    text: data.message,
                                    confirmButtonColor: '#e94f1b',
                                    allowOutsideClick: false
                                }).then(() => {
                                    window.location.href = data.redirect_url;
                                });
                            } else if (data.payment_url) {
                                // --- FLUX CINETPAY EXISTANT ---
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
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Échec du paiement',
                                            text: 'Le paiement n\'a pas pu être finalisé.',
                                            confirmButtonColor: '#e94f1b',
                                        }).then(() => {
                                            window.location.reload();
                                        });
                                    }
                                });
                            } else {
                                // Cas de succès générique (fallback)
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Réservation confirmée !',
                                    text: data.message,
                                    confirmButtonColor: '#e94f1b',
                                }).then(() => {
                                    window.location.reload();
                                });
                            }
                        } else {
                            throw new Error(data.message || 'Erreur lors de la réservation');
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: error.message,
                            confirmButtonColor: '#e94f1b',
                        });
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

        function openProgramsListModal() {
            modalCurrentStep = 1;
            selectedRoute = null;
            selectedDate = null;
            document.getElementById('programsListModal').classList.remove('hidden');
            updateModalStepIndicators(1);
            loadRoutes();
        }

        function closeProgramsListModal() { 
            document.getElementById('programsListModal').classList.add('hidden'); 
        }

        function goBackInModal() {
            if (modalCurrentStep === 2) {
                modalCurrentStep = 1;
                selectedRoute = null;
                updateModalStepIndicators(1);
                loadRoutes();
            } else if (modalCurrentStep === 3) {
                modalCurrentStep = 2;
                selectedDate = null;
                updateModalStepIndicators(2);
                loadDates(selectedRoute);
            }
        }

        function updateModalStepIndicators(step) {
            const backBtn = document.getElementById('modalBackBtn');
            const title = document.getElementById('modalTitle');
            const subtitle = document.getElementById('modalSubtitle');
            const step1 = document.getElementById('step1Indicator');
            const step2 = document.getElementById('step2Indicator');
            const step3 = document.getElementById('step3Indicator');

            // Reset all steps
            [step1, step2, step3].forEach(s => {
                s.classList.remove('bg-[#e94f1b]', 'text-white');
                s.classList.add('bg-gray-200', 'text-gray-500');
                s.querySelector('span:first-child').classList.remove('bg-white', 'text-[#e94f1b]');
                s.querySelector('span:first-child').classList.add('bg-gray-300', 'text-gray-600');
            });

            // Activate current step
            const activeStep = step === 1 ? step1 : step === 2 ? step2 : step3;
            activeStep.classList.remove('bg-gray-200', 'text-gray-500');
            activeStep.classList.add('bg-[#e94f1b]', 'text-white');
            activeStep.querySelector('span:first-child').classList.remove('bg-gray-300', 'text-gray-600');
            activeStep.querySelector('span:first-child').classList.add('bg-white', 'text-[#e94f1b]');

            // Update title/subtitle and back button
            if (step === 1) {
                title.textContent = 'Choisir une ligne';
                subtitle.textContent = 'Sélectionnez votre trajet parmi les lignes disponibles';
                backBtn.classList.add('hidden');
            } else if (step === 2) {
                title.textContent = selectedRoute.point_depart + ' → ' + selectedRoute.point_arrive;
                subtitle.textContent = 'Choisissez votre date de voyage';
                backBtn.classList.remove('hidden');
            } else {
                title.textContent = selectedRoute.point_depart + ' → ' + selectedRoute.point_arrive;
                const dateObj = new Date(selectedDate);
                subtitle.textContent = 'Horaires du ' + dateObj.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' });
                backBtn.classList.remove('hidden');
            }
        }

        async function loadRoutes() {
            const container = document.getElementById('programsListContent');
            container.innerHTML = `<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-4xl text-[#e94f1b]"></i><p class="mt-2 text-gray-500">Chargement des lignes...</p></div>`;

            try {
                const response = await fetch('{{ route("api.grouped-routes") }}');
                const data = await response.json();
                
                if (data.success && data.routes.length > 0) {
                    renderRoutesList(data.routes);
                } else {
                    container.innerHTML = `<div class="text-center py-8"><i class="fas fa-bus text-gray-300 text-4xl"></i><p class="mt-2 text-gray-500">Aucune ligne disponible pour le moment.</p></div>`;
                }
            } catch (error) {
                console.error('Erreur:', error);
                container.innerHTML = `<div class="text-center py-8 text-red-500"><i class="fas fa-exclamation-triangle text-4xl mb-2"></i><p>Impossible de charger les lignes.</p></div>`;
            }
        }

        function renderRoutesList(routes) {
            const container = document.getElementById('programsListContent');
            container.innerHTML = `<div class="grid grid-cols-1 md:grid-cols-2 gap-4">` + routes.map(route => {
                const prixDisplay = route.prix_min === route.prix_max 
                    ? `${Number(route.prix_min).toLocaleString('fr-FR')} FCFA`
                    : `À partir de ${Number(route.prix_min).toLocaleString('fr-FR')} FCFA`;
                
                return `
                    <div onclick="selectRoute(${JSON.stringify(route).replace(/"/g, '&quot;')})" 
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
                                <i class="fas fa-chevron-right"></i>
                            </span>
                        </div>
                    </div>
                `;
            }).join('') + `</div>`;
        }

        function selectRoute(route) {
            selectedRoute = route;
            modalCurrentStep = 2;
            updateModalStepIndicators(2);
            loadDates(route);
        }

        async function loadDates(route) {
            const container = document.getElementById('programsListContent');
            container.innerHTML = `<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-4xl text-[#e94f1b]"></i><p class="mt-2 text-gray-500">Chargement des dates...</p></div>`;

            try {
                const params = new URLSearchParams({
                    point_depart: route.point_depart,
                    point_arrive: route.point_arrive
                });
                const response = await fetch('{{ route("api.route-dates") }}?' + params);
                const data = await response.json();
                
                if (data.success && data.dates.length > 0) {
                    renderDatesList(data.dates);
                } else {
                    container.innerHTML = `<div class="text-center py-8"><i class="fas fa-calendar-times text-gray-300 text-4xl"></i><p class="mt-2 text-gray-500">Aucune date disponible.</p></div>`;
                }
            } catch (error) {
                console.error('Erreur:', error);
                container.innerHTML = `<div class="text-center py-8 text-red-500"><i class="fas fa-exclamation-triangle text-4xl mb-2"></i><p>Impossible de charger les dates.</p></div>`;
            }
        }

        function renderDatesList(dates) {
            const container = document.getElementById('programsListContent');
            container.innerHTML = `<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3">` + dates.map(dateObj => {
                const date = new Date(dateObj.date_depart);
                const isToday = date.toDateString() === new Date().toDateString();
                const dayName = date.toLocaleDateString('fr-FR', { weekday: 'short' });
                const dayNum = date.getDate();
                const monthName = date.toLocaleDateString('fr-FR', { month: 'short' });
                
                return `
                    <div onclick="selectDate('${dateObj.date_depart}')" 
                         class="bg-white p-4 rounded-xl border-2 ${isToday ? 'border-[#e94f1b] bg-orange-50' : 'border-gray-200'} hover:border-[#e94f1b] cursor-pointer transition-all duration-200 hover:shadow-md text-center group">
                        <p class="text-xs font-medium text-gray-500 uppercase">${dayName}</p>
                        <p class="text-2xl font-bold text-gray-900 my-1">${dayNum}</p>
                        <p class="text-sm text-gray-600">${monthName}</p>
                        ${isToday ? '<span class="inline-block mt-2 text-xs bg-[#e94f1b] text-white px-2 py-0.5 rounded-full">Aujourd\'hui</span>' : ''}
                        <p class="mt-2 text-xs text-gray-400 group-hover:text-[#e94f1b]">${dateObj.horaires_count} horaire(s)</p>
                    </div>
                `;
            }).join('') + `</div>`;
        }

        function selectDate(date) {
            selectedDate = date;
            modalCurrentStep = 3;
            updateModalStepIndicators(3);
            loadSchedules(selectedRoute, date);
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
                                <button onclick="closeProgramsListModal(); initiateReservationProcess(${prog.id}, '${selectedDate}')" 
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
    </script>

     <!-- Modal Liste des programmes (Step-by-step) -->
    <div id="programsListModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[60]">
        <div class="relative top-10 mx-auto p-5 border w-11/12 lg:w-3/4 max-w-4xl shadow-lg rounded-2xl bg-white">
            <div class="flex flex-col gap-4">
                <!-- En-tête avec navigation -->
                <div class="flex justify-between items-center border-b pb-4">
                    <div class="flex items-center gap-3">
                        <button id="modalBackBtn" onclick="goBackInModal()" class="hidden text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-arrow-left text-lg"></i>
                        </button>
                        <div>
                            <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Choisir une ligne</h3>
                            <p id="modalSubtitle" class="text-sm text-gray-500">Sélectionnez votre trajet parmi les lignes disponibles</p>
                        </div>
                    </div>
                    <button onclick="closeProgramsListModal()" class="text-gray-400 hover:text-gray-500 p-2">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <!-- Indicateur d'étapes -->
                <div class="flex items-center justify-center gap-2 py-2">
                    <div id="step1Indicator" class="flex items-center gap-2 px-4 py-2 rounded-full bg-[#e94f1b] text-white font-medium">
                        <span class="w-6 h-6 rounded-full bg-white text-[#e94f1b] flex items-center justify-center text-sm font-bold">1</span>
                        <span>Ligne</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-300"></i>
                    <div id="step2Indicator" class="flex items-center gap-2 px-4 py-2 rounded-full bg-gray-200 text-gray-500 font-medium">
                        <span class="w-6 h-6 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-bold">2</span>
                        <span>Date</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-300"></i>
                    <div id="step3Indicator" class="flex items-center gap-2 px-4 py-2 rounded-full bg-gray-200 text-gray-500 font-medium">
                        <span class="w-6 h-6 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-bold">3</span>
                        <span>Horaire</span>
                    </div>
                </div>
                
                <!-- Contenu dynamique -->
                <div id="programsListContent" class="max-h-[60vh] overflow-y-auto p-2">
                    <div class="col-span-full text-center py-8">
                        <i class="fas fa-spinner fa-spin text-4xl text-[#e94f1b]"></i>
                        <p class="mt-2 text-gray-500">Chargement des lignes...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
      <!-- Intégration Google Maps Autocomplete -->
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&loading=async&callback=initAutocompleteUser"
        async defer></script>

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
@endsection