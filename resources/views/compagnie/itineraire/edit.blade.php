@extends('compagnie.layouts.template')
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
            <p class="text-gray-500 mt-2">Mettez à jour les informations du trajet</p>
        </div>

        <!-- Carte du formulaire -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="{{ route('itineraire.update', $itineraire->id) }}" method="POST" class="p-8" id="itineraireForm">
                @csrf
                @method('PUT')

                <!-- Section 0: Affectation à une Gare -->
                @if(isset($gares) && $gares->count() > 0)
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-blue-500 rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Affectation à une Gare</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Gare</span>
                            </label>
                            <select name="gare_id"
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white">
                                <option value="">-- Aucune gare (non affecté) --</option>
                                @foreach($gares as $gare)
                                    <option value="{{ $gare->id }}" {{ old('gare_id', $itineraire->gare_id) == $gare->id ? 'selected' : '' }}>
                                        {{ $gare->nom_gare }} — {{ $gare->ville }}
                                    </option>
                                @endforeach
                            </select>
                            @error('gare_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                @endif

                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-[#e94f1b] rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Informations du trajet</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">Point de départ</label>
                            <input type="text" id="point_depart" name="point_depart" value="{{ old('point_depart', $itineraire->point_depart) }}" required
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] transition-all bg-gray-50 focus:bg-white">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">Point d'arrivée</label>
                            <input type="text" id="point_arrive" name="point_arrive" value="{{ old('point_arrive', $itineraire->point_arrive) }}" required
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] transition-all bg-gray-50 focus:bg-white">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">Durée estimée</label>
                            <input type="text" id="durer_parcours" name="durer_parcours" value="{{ old('durer_parcours', $itineraire->durer_parcours) }}" required readonly
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl bg-blue-50 text-blue-900 font-bold cursor-not-allowed">
                        </div>
                    </div>
                </div>

                <!-- Carte Google Maps -->
                <div class="mb-12">
                    <div id="googleMap" class="w-full h-[400px] rounded-2xl border-2 border-gray-200"></div>
                </div>

                <div class="flex justify-between items-center pt-8 border-t border-gray-200">
                    <a href="{{ route('itineraire.index') }}" class="px-8 py-4 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all">Retour</a>
                    <button type="submit" id="submit-btn" class="px-12 py-4 bg-[#e94f1b] text-white font-bold rounded-xl hover:bg-[#e89116] shadow-lg transition-all">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=initMap" async defer></script>
<script>
    let map, directionsService, directionsRenderer, autocompleteDepart, autocompleteArrive;

    function initMap() {
        const ciCenter = { lat: 7.539989, lng: -5.547080 };
        map = new google.maps.Map(document.getElementById("googleMap"), {
            zoom: 7, center: ciCenter, mapTypeControl: false, streetViewControl: false
        });

        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({ map: map });

        const options = { componentRestrictions: { country: "ci" } };
        autocompleteDepart = new google.maps.places.Autocomplete(document.getElementById("point_depart"), options);
        autocompleteArrive = new google.maps.places.Autocomplete(document.getElementById("point_arrive"), options);

        autocompleteDepart.addListener("place_changed", calculateAndDisplayRoute);
        autocompleteArrive.addListener("place_changed", calculateAndDisplayRoute);

        // Calculer l'itinéraire au chargement
        calculateAndDisplayRoute();
    }

    function calculateAndDisplayRoute() {
        const origin = document.getElementById("point_depart").value;
        const destination = document.getElementById("point_arrive").value;

        if (!origin || !destination) return;

        directionsService.route({
            origin: origin,
            destination: destination,
            travelMode: google.maps.TravelMode.DRIVING,
        }, (response, status) => {
            if (status === 'OK') {
                directionsRenderer.setDirections(response);
                document.getElementById("durer_parcours").value = response.routes[0].legs[0].duration.text;
            }
        });
    }
</script>
@endsection
