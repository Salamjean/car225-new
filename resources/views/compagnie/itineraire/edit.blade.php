@extends('compagnie.layouts.template')

@section('page-title', 'Modifier Itinéraire')
@section('page-subtitle', 'Mettez à jour les informations du trajet')

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
    .btn-submit {
        background: linear-gradient(135deg, var(--orange) 0%, var(--orange-dark) 100%);
        color: white; padding: 14px 28px; border-radius: var(--radius-sm);
        font-weight: 700; font-size: 13px; border: none; cursor: pointer;
        box-shadow: 0 4px 15px rgba(249,115,22,0.3); transition: 0.2s; display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(249,115,22,0.4); color: white; text-decoration: none; }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <div class="form-container">
        
        <div class="d-flex align-items-center justify-content-between mb-4">
            <a href="{{ route('itineraire.index') }}" class="btn btn-light btn-sm" style="font-weight: 700; font-size: 12px; border-radius: var(--radius-sm);">
                <i class="fas fa-arrow-left mr-2"></i> Annuler & Retour
            </a>
        </div>

        <form action="{{ route('itineraire.update', $itineraire->id) }}" method="POST" id="itineraireForm">
            @csrf
            @method('PUT')

            @if(isset($gares) && $gares->count() > 0)
            <div class="form-card">
                <div class="form-card-header">
                    <div class="form-step-badge" style="background: var(--blue);">01</div>
                    <h3 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin: 0; text-transform: uppercase;">Affectation à une Gare</h3>
                </div>
                <div class="p-4">
                    <div class="input-group-modern mb-0">
                        <label class="form-label">Gare de rattachement</label>
                        <i class="fas fa-building input-icon"></i>
                        <select name="gare_id" class="input-modern">
                            <option value="">-- Aucune gare (non affecté) --</option>
                            @foreach($gares as $gare)
                                <option value="{{ $gare->id }}" {{ old('gare_id', $itineraire->gare_id) == $gare->id ? 'selected' : '' }}>
                                    {{ $gare->nom_gare }} — {{ $gare->ville }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down select-chevron"></i>
                        @error('gare_id') <span class="text-danger" style="font-size: 10px; font-weight: 700; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            @endif

            <div class="form-card">
                <div class="form-card-header">
                    <div class="form-step-badge">02</div>
                    <h3 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin: 0; text-transform: uppercase;">Informations du trajet</h3>
                </div>
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-group-modern">
                                <label class="form-label">Point de départ</label>
                                <i class="fas fa-map-marker-alt input-icon text-success"></i>
                                <input type="text" id="point_depart" name="point_depart" value="{{ old('point_depart', $itineraire->point_depart) }}" required class="input-modern">
                                @error('point_depart') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="input-group-modern">
                                <label class="form-label">Point d'arrivée</label>
                                <i class="fas fa-map-marker-alt input-icon text-danger"></i>
                                <input type="text" id="point_arrive" name="point_arrive" value="{{ old('point_arrive', $itineraire->point_arrive) }}" required class="input-modern">
                                @error('point_arrive') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="input-group-modern">
                                <label class="form-label">Durée estimée</label>
                                <i class="fas fa-clock input-icon" style="color: var(--blue);"></i>
                                <input type="text" id="durer_parcours" name="durer_parcours" value="{{ old('durer_parcours', $itineraire->durer_parcours) }}" required readonly class="input-modern" style="background: #EFF6FF; color: var(--blue); font-weight: 800; cursor: not-allowed;">
                                @error('durer_parcours') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="form-card-header">
                    <i class="fas fa-map text-muted"></i>
                    <h3 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin: 0; text-transform: uppercase;">Aperçu de l'itinéraire</h3>
                </div>
                <div class="p-3">
                    <div class="map-container-wrapper">
                        <div id="googleMap"></div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4 pt-3" style="border-top: 1px solid var(--border);">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places,geometry&callback=initMap" async defer></script>
<script>
    let map, directionsService, directionsRenderer, autocompleteDepart, autocompleteArrive;

    function initMap() {
        const ciCenter = { lat: 7.539989, lng: -5.547080 };
        map = new google.maps.Map(document.getElementById("googleMap"), {
            zoom: 7, center: ciCenter, mapTypeControl: false, streetViewControl: false,
            styles: [{ featureType: "poi", elementType: "labels", stylers: [{ visibility: "off" }] }]
        });

        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            map: map,
            polylineOptions: { strokeColor: 'var(--orange)', strokeWeight: 6, strokeOpacity: 0.8 }
        });

        const options = { componentRestrictions: { country: "ci" } };
        autocompleteDepart = new google.maps.places.Autocomplete(document.getElementById("point_depart"), options);
        autocompleteArrive = new google.maps.places.Autocomplete(document.getElementById("point_arrive"), options);

        autocompleteDepart.addListener("place_changed", calculateAndDisplayRoute);
        autocompleteArrive.addListener("place_changed", calculateAndDisplayRoute);

        // Calculer l'itinéraire existant au chargement
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