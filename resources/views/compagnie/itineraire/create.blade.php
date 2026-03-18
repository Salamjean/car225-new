@extends('compagnie.layouts.template')

@section('page-title', 'Nouvel Itinéraire')
@section('page-subtitle', 'Créez vos itinéraires avec précision grâce à Google Maps')

@section('styles')
<style>
    .form-container { max-width: 1000px; margin: 0 auto; }
    
    .form-card {
        background: var(--surface); border-radius: var(--radius);
        border: 1px solid var(--border); box-shadow: var(--shadow-sm);
        margin-bottom: 24px; overflow: hidden;
    }
    .form-card-header {
        padding: 16px 24px; background: var(--surface-2);
        border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px;
    }
    .form-step-badge {
        width: 32px; height: 32px; border-radius: 10px;
        background: var(--orange); color: white; display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 13px; box-shadow: 0 4px 10px rgba(249,115,22,0.3);
    }
    
    .input-group-modern { position: relative; margin-bottom: 20px; }
    .input-group-modern .form-label {
        display: block; font-size: 10px; font-weight: 700; color: var(--text-3);
        text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;
    }
    .input-group-modern .input-icon {
        position: absolute; left: 16px; top: 35px; color: var(--text-3); font-size: 14px; transition: 0.2s;
    }
    .input-group-modern .input-modern {
        width: 100%; padding: 12px 16px 12px 42px;
        border: 1px solid var(--border-strong); border-radius: var(--radius-sm);
        background: var(--surface-2); color: var(--text-1); font-size: 13px; font-weight: 600; transition: 0.2s;
    }
    .input-group-modern .input-modern:focus {
        outline: none; border-color: var(--orange); background: var(--surface); box-shadow: 0 0 0 3px var(--orange-light);
    }
    .input-group-modern .input-modern:focus + .input-icon, .input-group-modern:focus-within .input-icon { color: var(--orange); }
    
    select.input-modern { appearance: none; }
    .select-chevron { position: absolute; right: 16px; top: 35px; color: var(--text-3); font-size: 12px; pointer-events: none; }

    /* Map Styles */
    .map-container-wrapper { position: relative; border-radius: var(--radius); overflow: hidden; border: 2px solid var(--border); }
    #googleMap { width: 100%; height: 500px; background: var(--surface-2); }
    
    .map-overlay {
        position: absolute; inset: 0; background: rgba(255,255,255,0.8); z-index: 10;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
    }
    .map-error-box {
        position: absolute; top: 16px; left: 50%; transform: translateX(-50%); z-index: 20;
        background: #FEF2F2; border: 1px solid #F87171; color: #B91C1C;
        padding: 10px 16px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 600; box-shadow: var(--shadow-md);
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--orange) 0%, var(--orange-dark) 100%);
        color: white; padding: 14px 28px; border-radius: var(--radius-sm);
        font-weight: 700; font-size: 13px; border: none; cursor: pointer;
        box-shadow: 0 4px 15px rgba(249,115,22,0.3); transition: 0.2s; display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-submit:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(249,115,22,0.4); color: white; text-decoration: none; }
    .btn-submit:disabled { background: var(--border-strong); box-shadow: none; cursor: not-allowed; transform: none; color: var(--text-3); }

    .route-stats-pill {
        display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; margin-right: 8px;
    }
    .pill-green { background: #ECFDF5; color: #065F46; }
    .pill-blue { background: #EFF6FF; color: #1E3A8A; }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <div class="form-container">
        
        <div class="d-flex align-items-center justify-content-between mb-4">
            <a href="{{ route('itineraire.index') }}" class="btn btn-light btn-sm" style="font-weight: 700; font-size: 12px; border-radius: var(--radius-sm);">
                <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
            </a>
        </div>

        <form action="{{route('itineraire.store')}}" method="POST" id="itineraireForm">
            @csrf

            @if(isset($gares) && $gares->count() > 0)
            <div class="form-card">
                <div class="form-card-header">
                    <div class="form-step-badge" style="background: var(--blue); box-shadow: 0 4px 10px rgba(59,130,246,0.3);">01</div>
                    <h3 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin: 0; text-transform: uppercase;">Affectation à une Gare</h3>
                </div>
                <div class="p-4">
                    <div class="input-group-modern mb-0">
                        <label class="form-label">Gare de rattachement</label>
                        <i class="fas fa-building input-icon"></i>
                        <select name="gare_id" class="input-modern">
                            <option value="">-- Aucune gare (non affecté) --</option>
                            @foreach($gares as $gare)
                                <option value="{{ $gare->id }}" {{ old('gare_id') == $gare->id ? 'selected' : '' }}>
                                    {{ $gare->nom_gare }} — {{ $gare->ville }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down select-chevron"></i>
                        @error('gare_id') <span class="text-danger" style="font-size: 10px; font-weight: 700; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        <p style="font-size: 11px; color: var(--text-3); font-style: italic; margin-top: 8px; margin-bottom: 0;">
                            <i class="fas fa-info-circle mr-1"></i> Sélectionnez la gare à laquelle cet itinéraire sera affecté.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <div class="form-card">
                <div class="form-card-header">
                    <div class="form-step-badge">02</div>
                    <h3 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin: 0; text-transform: uppercase;">Planification du trajet</h3>
                </div>
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-group-modern">
                                <label class="form-label">Point de départ <span class="text-danger">*</span></label>
                                <i class="fas fa-map-marker-alt input-icon text-success"></i>
                                <input type="text" id="point_depart" name="point_depart" required class="input-modern" placeholder="Rechercher une ville, un lieu...">
                                @error('point_depart') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="input-group-modern">
                                <label class="form-label">Point d'arrivée <span class="text-danger">*</span></label>
                                <i class="fas fa-map-marker-alt input-icon text-danger"></i>
                                <input type="text" id="point_arrive" name="point_arrive" required class="input-modern" placeholder="Rechercher une destination...">
                                @error('point_arrive') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="input-group-modern">
                                <label class="form-label">Durée estimée <span class="text-danger">*</span></label>
                                <i class="fas fa-clock input-icon" style="color: var(--blue);"></i>
                                <input type="text" id="durer_parcours" name="durer_parcours" required readonly class="input-modern" style="background: #EFF6FF; color: var(--blue); font-weight: 800; cursor: not-allowed;" placeholder="Calcul automatique...">
                                @error('durer_parcours') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="form-card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-map text-muted"></i>
                        <h3 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin: 0; text-transform: uppercase;">Aperçu de l'itinéraire</h3>
                    </div>
                    <div id="route-stats" class="d-none">
                        <span class="route-stats-pill pill-green"><i class="fas fa-road mr-2"></i> <span id="distance-display">0 km</span></span>
                        <span class="route-stats-pill pill-blue"><i class="fas fa-clock mr-2"></i> <span id="duration-display">0 min</span></span>
                    </div>
                </div>
                <div class="p-3">
                    <div class="map-container-wrapper">
                        <div id="googleMap"></div>
                        
                        <div id="map-loading" class="map-overlay d-none">
                            <div class="spinner-border text-warning mb-2" role="status" style="width: 3rem; height: 3rem;"></div>
                            <span style="font-weight: 700; color: var(--text-2);">Calcul de l'itinéraire en cours...</span>
                        </div>
                        
                        <div id="map-error" class="map-error-box d-none">
                            <span id="error-message">Impossible de calculer l'itinéraire.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4 pt-3" style="border-top: 1px solid var(--border);">
                <button type="submit" id="submit-btn" class="btn-submit" disabled>
                    <i class="fas fa-paper-plane"></i> Créer l'itinéraire
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places,geometry&loading=async&callback=initMap" async defer></script>

<script>
    let map, directionsService, directionsRenderer, autocompleteDepart, autocompleteArrive;

    window.gm_authFailure = function() {
        const errorDiv = document.getElementById("map-error");
        const errorMsg = document.getElementById("error-message");
        if (errorDiv && errorMsg) {
            errorDiv.classList.remove("d-none");
            errorMsg.innerHTML = `<strong>Erreur de clé API Google Maps !</strong><br>Veuillez vérifier votre clé dans le fichier de configuration.`;
            disableSubmitButton();
        }
    };

    function initMap() {
        const ciCenter = { lat: 7.539989, lng: -5.547080 };
        try {
            map = new google.maps.Map(document.getElementById("googleMap"), {
                zoom: 7, center: ciCenter, mapTypeControl: false, streetViewControl: false, fullscreenControl: true,
                styles: [{ featureType: "poi", elementType: "labels", stylers: [{ visibility: "off" }] }]
            });

            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({
                map: map, suppressMarkers: false,
                polylineOptions: { strokeColor: 'var(--orange)', strokeWeight: 6, strokeOpacity: 0.8 }
            });

            initAutocomplete();
        } catch (e) {
            console.error("Erreur initMap:", e);
            showError("Erreur d'initialisation de la carte.");
        }
    }

    function initAutocomplete() {
        const options = { componentRestrictions: { country: "ci" }, fields: ["formatted_address", "geometry", "name"] };
        const inputDepart = document.getElementById("point_depart");
        const inputArrive = document.getElementById("point_arrive");

        if(!inputDepart || !inputArrive) return;
        try {
            autocompleteDepart = new google.maps.places.Autocomplete(inputDepart, options);
            autocompleteArrive = new google.maps.places.Autocomplete(inputArrive, options);
            autocompleteDepart.addListener("place_changed", calculateAndDisplayRoute);
            autocompleteArrive.addListener("place_changed", calculateAndDisplayRoute);
        } catch(e) { console.warn("Autocomplétion non chargée:", e); }
    }

    function calculateAndDisplayRoute() {
        const origin = document.getElementById("point_depart").value;
        const destination = document.getElementById("point_arrive").value;

        if (!origin || !destination) return;

        showLoading(true);
        hideError();

        directionsService.route({
            origin: origin, destination: destination, travelMode: google.maps.TravelMode.DRIVING,
        }).then((response) => {
            showLoading(false);
            directionsRenderer.setDirections(response);
            const leg = response.routes[0].legs[0];
            updateRouteInfo(leg.distance.text, leg.duration.text);
            enableSubmitButton();
        }).catch((e) => {
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
        document.getElementById("route-stats").classList.remove("d-none");
        document.getElementById("durer_parcours").value = duration;
    }

    function showLoading(show) {
        const loader = document.getElementById("map-loading");
        if(loader) show ? loader.classList.remove("d-none") : loader.classList.add("d-none");
    }

    function showError(msg) {
        const errorDiv = document.getElementById("map-error");
        const errorMsg = document.getElementById("error-message");
        if (errorDiv && errorMsg) {
            errorMsg.innerHTML = msg;
            errorDiv.classList.remove("d-none");
            if(!msg.includes("clé API")) setTimeout(() => errorDiv.classList.add("d-none"), 8000);
        }
    }

    function hideError() {
        const errorDiv = document.getElementById("map-error");
        if(errorDiv) errorDiv.classList.add("d-none");
    }

    function enableSubmitButton() {
        const btn = document.getElementById("submit-btn");
        if(btn) btn.disabled = false;
    }

    function disableSubmitButton() {
        const btn = document.getElementById("submit-btn");
        if(btn) btn.disabled = true;
    }
</script>
@endsection