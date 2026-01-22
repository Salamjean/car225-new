@extends('compagnie.layouts.template')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
    <div class=" mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#e94e1a] rounded-2xl shadow-lg mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900">Nouvel Itinéraire</h1>
            <p class="text-gray-500 mt-2">Créez vos itinéraires avec précision grâce à Google Maps</p>
        </div>

        <!-- Carte du formulaire -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="{{route('itineraire.store')}}" method="POST" class="p-8" id="itineraireForm">
                @csrf

                <!-- Section 1: Informations de base -->
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-[#e94e1a] rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Planification du trajet</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Point de départ -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Point de départ</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="point_depart" 
                                       name="point_depart" 
                                       required
                                       class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white pl-10"
                                       placeholder="Rechercher une ville, un lieu...">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <div class="w-2 h-2 rounded-full bg-green-500 ring-4 ring-green-100"></div>
                                </div>
                            </div>
                            @error('point_depart')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Point d'arrivée -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Point d'arrivée</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="point_arrive" 
                                       name="point_arrive" 
                                       required
                                       class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white pl-10"
                                       placeholder="Rechercher une destination...">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <div class="w-2 h-2 rounded-full bg-red-500 ring-4 ring-red-100"></div>
                                </div>
                            </div>
                            @error('point_arrive')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Durée du parcours -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Durée estimée</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="durer_parcours" 
                                       name="durer_parcours" 
                                       required
                                       readonly
                                       class="w-full px-4 py-4 border border-gray-200 rounded-xl bg-blue-50 text-blue-900 font-bold cursor-not-allowed"
                                       placeholder="Calcul automatique...">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <i class="mdi mdi-clock-outline text-blue-500 text-xl"></i>
                                </div>
                            </div>
                            @error('durer_parcours')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Carte Google Maps -->
                <div class="mb-12">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="w-2 h-8 bg-blue-500 rounded-full mr-4"></div>
                            <h2 class="text-2xl font-bold text-gray-900">Aperçu de l'itinéraire</h2>
                        </div>
                        <div id="route-stats" class="hidden flex space-x-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="mdi mdi-road-variant mr-1"></i> <span id="distance-display">0 km</span>
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <i class="mdi mdi-clock-outline mr-1"></i> <span id="duration-display">0 min</span>
                            </span>
                        </div>
                    </div>

                    <div class="relative rounded-2xl overflow-hidden border-2 border-gray-200 shadow-lg group">
                        <!-- Conteneur de la carte -->
                        <div id="googleMap" class="w-full h-[500px] bg-gray-100"></div>
                        
                        <!-- Overlay de chargement -->
                        <div id="map-loading" class="absolute inset-0 bg-white bg-opacity-80 z-10 flex items-center justify-center hidden">
                            <div class="flex flex-col items-center">
                                <svg class="animate-spin h-10 w-10 text-[#e94e1a] mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-gray-600 font-medium">Calcul de l'itinéraire en cours...</span>
                            </div>
                        </div>
                        
                        <!-- Message d'erreur -->
                        <div id="map-error" class="hidden absolute top-4 left-1/2 transform -translate-x-1/2 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg z-20" role="alert">
                            <span id="error-message">Impossible de calculer l'itinéraire.</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 justify-between items-center pt-8 border-t border-gray-200">
                    <a href="{{ route('itineraire.index') }}" 
                       class="flex items-center px-8 py-4 text-gray-700 font-semibold rounded-xl border border-gray-300 hover:bg-gray-50 transition-all duration-200 group">
                        <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour à la liste
                    </a>
                    
                    <button type="submit" id="submit-btn" disabled
                            class="flex items-center px-12 py-4 bg-gray-300 cursor-not-allowed text-white font-bold rounded-xl transition-all duration-200 shadow-none">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Créer l'itinéraire
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- REMPLACEZ 'YOUR_API_KEY' CI-DESSOUS PAR VOTRE VRAIE CLÉ API GOOGLE MAPS (commençant par AIza...) -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places,geometry&loading=async&callback=initMap" async defer></script>

<script>
    let map;
    let directionsService;
    let directionsRenderer;
    let autocompleteDepart;
    let autocompleteArrive;

    // Gestionnaire d'erreur d'authentification Google (Clé invalide)
    window.gm_authFailure = function() {
        const errorDiv = document.getElementById("map-error");
        const errorMsg = document.getElementById("error-message");
        if (errorDiv && errorMsg) {
            errorDiv.classList.remove("hidden");
            errorMsg.innerHTML = `
                <strong>Erreur de clé API Google Maps !</strong><br>
                La carte ne peut pas charger.<br>
                Veuillez ouvrir le fichier : <code class="text-xs bg-red-200 px-1">create.blade.php</code><br>
                et remplacer <b>YOUR_API_KEY</b> (en bas) par votre clé valide.
            `;
            disableSubmitButton();
        }
    };

    function initMap() {
        // Initialiser la carte centrée sur la Côte d'Ivoire par défaut
        const ciCenter = { lat: 7.539989, lng: -5.547080 };
        
        try {
            map = new google.maps.Map(document.getElementById("googleMap"), {
                zoom: 7,
                center: ciCenter,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
                styles: [{ featureType: "poi", elementType: "labels", stylers: [{ visibility: "off" }] }]
            });

            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({
                map: map,
                suppressMarkers: false,
                polylineOptions: { strokeColor: '#e94e1a', strokeWeight: 6, strokeOpacity: 0.8 }
            });

            initAutocomplete();

        } catch (e) {
            console.error("Erreur initMap:", e);
            showError("Erreur d'initialisation de la carte.");
        }
    }

    function initAutocomplete() {
        const options = {
            componentRestrictions: { country: "ci" },
            fields: ["formatted_address", "geometry", "name"],
        };

        const inputDepart = document.getElementById("point_depart");
        const inputArrive = document.getElementById("point_arrive");

        if(!inputDepart || !inputArrive) return;

        try {
            autocompleteDepart = new google.maps.places.Autocomplete(inputDepart, options);
            autocompleteArrive = new google.maps.places.Autocomplete(inputArrive, options);

            autocompleteDepart.addListener("place_changed", calculateAndDisplayRoute);
            autocompleteArrive.addListener("place_changed", calculateAndDisplayRoute);
        } catch(e) {
            console.warn("Autocomplétion non chargée:", e);
        }
    }

    function calculateAndDisplayRoute() {
        const origin = document.getElementById("point_depart").value;
        const destination = document.getElementById("point_arrive").value;

        if (!origin || !destination) return;

        showLoading(true);
        hideError();

        directionsService.route({
            origin: origin,
            destination: destination,
            travelMode: google.maps.TravelMode.DRIVING,
        })
        .then((response) => {
            showLoading(false);
            directionsRenderer.setDirections(response);
            const leg = response.routes[0].legs[0];
            updateRouteInfo(leg.distance.text, leg.duration.text);
            enableSubmitButton();
        })
        .catch((e) => {
            showLoading(false);
            let msg = "Impossible de calculer l'itinéraire.";
            if(e.code === 'ZERO_RESULTS') msg = "Aucun trajet trouvé.";
            if(e.code === 'NOT_FOUND') msg = "Lieu non trouvé.";
            showError(msg + " ("+e.code+")");
            disableSubmitButton();
        });
    }

    function updateRouteInfo(distance, duration) {
        document.getElementById("distance-display").textContent = distance;
        document.getElementById("duration-display").textContent = duration;
        document.getElementById("route-stats").classList.remove("hidden");
        document.getElementById("durer_parcours").value = duration;
    }

    function showLoading(show) {
        const loader = document.getElementById("map-loading");
        if(loader) show ? loader.classList.remove("hidden") : loader.classList.add("hidden");
    }

    function showError(msg) {
        const errorDiv = document.getElementById("map-error");
        const errorMsg = document.getElementById("error-message");
        if (errorDiv && errorMsg) {
            errorMsg.innerHTML = msg;
            errorDiv.classList.remove("hidden");
            if(!msg.includes("clé API")) setTimeout(() => errorDiv.classList.add("hidden"), 8000);
        }
    }

    function hideError() {
        const errorDiv = document.getElementById("map-error");
        if(errorDiv) errorDiv.classList.add("hidden");
    }

    function enableSubmitButton() {
        const btn = document.getElementById("submit-btn");
        if(btn) {
            btn.disabled = false;
            btn.classList.remove("bg-gray-300", "cursor-not-allowed", "shadow-none");
            btn.classList.add("bg-[#e94e1a]", "hover:bg-[#d33d0f]", "transform", "hover:-translate-y-1", "shadow-lg", "cursor-pointer"); 
        }
    }

    function disableSubmitButton() {
        const btn = document.getElementById("submit-btn");
        if(btn) {
            btn.disabled = true;
            btn.classList.add("bg-gray-300", "cursor-not-allowed", "shadow-none");
            btn.classList.remove("bg-[#e94e1a]", "hover:bg-[#d33d0f]", "transform", "hover:-translate-y-1", "shadow-lg", "cursor-pointer");
        }
    }
</script>
@endsection