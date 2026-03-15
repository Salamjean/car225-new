@extends('gare-espace.layouts.template')
@section('title', 'Modifier Itinéraire')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#e94f1b] rounded-2xl shadow-lg mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900">Modifier Itinéraire</h1>
            <p class="text-gray-500 mt-2">Mettez à jour les informations du trajet avec précision</p>
        </div>

        <!-- Carte du formulaire -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="{{ route('gare-espace.itineraire.update', $itineraire->id) }}" method="POST" class="p-8" id="itineraireForm">
                @csrf
                @method('PUT')

                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-[#e94f1b] rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Informations du trajet</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">Point de départ</label>
                            <div class="relative">
                                <input type="text" id="point_depart" name="point_depart" value="{{ old('point_depart', $itineraire->point_depart) }}" required
                                       class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] transition-all bg-gray-50 focus:bg-white pl-10">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <div class="w-2 h-2 rounded-full bg-green-500 ring-4 ring-green-100"></div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">Point d'arrivée</label>
                            <div class="relative">
                                <input type="text" id="point_arrive" name="point_arrive" value="{{ old('point_arrive', $itineraire->point_arrive) }}" required
                                       class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] transition-all bg-gray-50 focus:bg-white pl-10">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <div class="w-2 h-2 rounded-full bg-red-500 ring-4 ring-red-100"></div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">Durée estimée</label>
                            <div class="relative">
                                <input type="text" id="durer_parcours" name="durer_parcours" value="{{ old('durer_parcours', $itineraire->durer_parcours) }}" required readonly
                                       class="w-full px-4 py-4 border border-gray-200 rounded-xl bg-blue-50 text-blue-900 font-bold cursor-not-allowed">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <i class="fas fa-clock text-blue-500 text-xl"></i>
                                </div>
                            </div>
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
                        <div id="route-stats" class="flex space-x-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-clock mr-1"></i> <span id="duration-display">{{ $itineraire->durer_parcours }}</span>
                            </span>
                        </div>
                    </div>

                    <div class="relative rounded-2xl overflow-hidden border-2 border-gray-200 shadow-lg group">
                        <div id="googleMap" class="w-full h-[400px] bg-gray-100"></div>
                        
                        <div id="map-loading" class="absolute inset-0 bg-white bg-opacity-80 z-10 flex items-center justify-center hidden">
                            <div class="flex flex-col items-center">
                                <svg class="animate-spin h-10 w-10 text-[#e94f1b] mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-gray-600 font-medium">Recalcul de l'itinéraire...</span>
                            </div>
                        </div>
                        
                        <div id="map-error" class="hidden absolute top-4 left-1/2 transform -translate-x-1/2 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg z-20" role="alert">
                            <span id="error-message">Impossible de calculer l'itinéraire.</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-between items-center pt-8 border-t border-gray-200">
                    <a href="{{ route('gare-espace.itineraire.index') }}" 
                       class="flex items-center px-8 py-4 text-gray-700 font-semibold rounded-xl border border-gray-300 hover:bg-gray-50 transition-all duration-200 group">
                        <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour
                    </a>
                    
                    <button type="submit" id="submit-btn" 
                            class="flex items-center px-12 py-4 bg-[#e94f1b] text-white font-bold rounded-xl hover:bg-[#d33d0f] shadow-lg transition-all duration-200 cursor-pointer">
                        <i class="fas fa-save mr-2"></i>
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places,geometry&loading=async&callback=initMap" async defer></script>
<script>
    let map, directionsService, directionsRenderer, autocompleteDepart, autocompleteArrive;

    function initMap() {
        const ciCenter = { lat: 7.539989, lng: -5.547080 };
        
        try {
            map = new google.maps.Map(document.getElementById("googleMap"), {
                zoom: 7,
                center: ciCenter,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true
            });

            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({
                map: map,
                polylineOptions: { strokeColor: '#e94f1b', strokeWeight: 6, strokeOpacity: 0.8 }
            });

            initAutocomplete();
            calculateAndDisplayRoute();

        } catch (e) {
            console.error("Erreur initMap:", e);
        }
    }

    function initAutocomplete() {
        const options = { componentRestrictions: { country: "ci" } };
        autocompleteDepart = new google.maps.places.Autocomplete(document.getElementById("point_depart"), options);
        autocompleteArrive = new google.maps.places.Autocomplete(document.getElementById("point_arrive"), options);

        autocompleteDepart.addListener("place_changed", calculateAndDisplayRoute);
        autocompleteArrive.addListener("place_changed", calculateAndDisplayRoute);
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
        }, (response, status) => {
            showLoading(false);
            if (status === 'OK') {
                directionsRenderer.setDirections(response);
                const duration = response.routes[0].legs[0].duration.text;
                document.getElementById("durer_parcours").value = duration;
                document.getElementById("duration-display").textContent = duration;
                enableSubmitButton();
            } else {
                showError("Impossible de calculer l'itinéraire: " + status);
                disableSubmitButton();
            }
        });
    }

    function showLoading(show) {
        const loader = document.getElementById("map-loading");
        if(loader) show ? loader.classList.remove("hidden") : loader.classList.add("hidden");
    }

    function showError(msg) {
        const errorDiv = document.getElementById("map-error");
        const errorMsg = document.getElementById("error-message");
        if (errorDiv && errorMsg) {
            errorMsg.textContent = msg;
            errorDiv.classList.remove("hidden");
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
            btn.classList.add("bg-[#e94f1b]", "hover:bg-[#d33d0f]", "cursor-pointer");
            btn.classList.remove("bg-gray-300", "cursor-not-allowed");
        }
    }

    function disableSubmitButton() {
        const btn = document.getElementById("submit-btn");
        if(btn) {
            btn.disabled = true;
            btn.classList.remove("bg-[#e94f1b]", "hover:bg-[#d33d0f]", "cursor-pointer");
            btn.classList.add("bg-gray-300", "cursor-not-allowed");
        }
    }
</script>
@endsection
