@extends('gare-espace.layouts.template')

@section('title', 'Suivi en temps réel')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #trackingMap {
        height: 65vh;
        width: 100%;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.12);
        z-index: 1;
    }

    .tracking-header {
        background: linear-gradient(135deg, #92400e 0%, #f97316 100%);
        border-radius: 16px;
        padding: 24px 32px;
        color: white;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }

    .tracking-header h1 {
        font-size: 1.6rem;
        font-weight: 800;
        margin: 0;
    }

    .tracking-header .subtitle {
        font-size: 0.85rem;
        opacity: 0.85;
        margin-top: 4px;
    }

    .live-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255,255,255,0.2);
        padding: 6px 16px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 0.8rem;
        backdrop-filter: blur(8px);
    }

    .live-dot {
        width: 10px;
        height: 10px;
        background: #ef4444;
        border-radius: 50%;
        animation: livePulse 1.5s ease-in-out infinite;
    }

    @keyframes livePulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.3); }
    }

    .voyage-sidebar {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        max-height: 65vh;
        display: flex;
        flex-direction: column;
    }

    .voyage-sidebar-header {
        padding: 16px 20px;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 700;
        font-size: 0.9rem;
        color: #374151;
    }

    .voyage-sidebar-list {
        overflow-y: auto;
        flex: 1;
        padding: 8px;
    }

    .voyage-card {
        padding: 14px 16px;
        border-radius: 12px;
        border: 2px solid transparent;
        cursor: pointer;
        transition: all 0.2s;
        margin-bottom: 6px;
        background: #f9fafb;
    }

    .voyage-card:hover {
        border-color: #f97316;
        background: #fff7ed;
    }

    .voyage-card.active {
        border-color: #f97316;
        background: #fff7ed;
    }

    .voyage-card .route-label {
        font-weight: 700;
        font-size: 0.9rem;
        color: #111827;
    }

    .voyage-card .driver-label {
        font-size: 0.78rem;
        color: #6b7280;
        margin-top: 2px;
    }

    .voyage-card .vehicle-label {
        font-size: 0.72rem;
        color: #9ca3af;
        margin-top: 2px;
    }

    .voyage-card .speed-label {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.72rem;
        font-weight: 700;
        color: #f97316;
        margin-top: 4px;
    }

    .no-tracking {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        padding: 40px 20px;
        text-align: center;
        color: #9ca3af;
    }

    .no-tracking i {
        font-size: 3rem;
        margin-bottom: 16px;
        color: #d1d5db;
    }

    .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,0.2);
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .bus-marker {
        background: #f97316;
        border: 3px solid white;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.4);
        color: white;
        font-size: 14px;
    }

    .bus-marker-offline {
        background: #9ca3af;
        box-shadow: 0 4px 12px rgba(156, 163, 175, 0.4);
    }

    .leaflet-popup-content {
        font-family: 'Inter', sans-serif;
        min-width: 200px;
    }

    .popup-title {
        font-weight: 800;
        font-size: 1rem;
        color: #111827;
        margin-bottom: 8px;
    }

    .popup-info {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.82rem;
        color: #6b7280;
        margin: 4px 0;
    }

    .popup-info i {
        width: 16px;
        text-align: center;
        color: #f97316;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 py-6 px-4">
    <div class="mx-auto max-w-[1600px]">

        <!-- Header -->
        <div class="tracking-header">
            <div>
                <h1><i class="fas fa-satellite-dish mr-2"></i> Suivi en temps réel</h1>
                <div class="subtitle">Voyages au départ ou à l'arrivée de <strong>{{ auth('gare')->user()->nom_gare }}</strong></div>
            </div>
            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                <div class="stat-pill">
                    <i class="fas fa-bus"></i>
                    <span id="totalEnCours">{{ $activeVoyages->count() }}</span> voyage(s) en cours
                </div>
                <div class="stat-pill">
                    <i class="fas fa-map-pin"></i>
                    <span id="totalTracked">0</span> suivi(s) GPS
                </div>
                <div class="live-badge">
                    <div class="live-dot"></div>
                    LIVE
                </div>
            </div>
        </div>

        <!-- Map + Sidebar -->
        <div style="display:flex; gap:24px; flex-wrap:wrap; align-items:flex-start;">
            <!-- Map -->
            <div style="flex:1; min-width:0;">
                <div id="trackingMap"></div>
            </div>

            <!-- Sidebar -->
            <div style="width:320px; flex-shrink:0;">
                <div class="voyage-sidebar">
                    <div class="voyage-sidebar-header">
                        <i class="fas fa-list-ul mr-2"></i> Voyages en cours
                    </div>
                    <div class="voyage-sidebar-list" id="voyageSidebarList">
                        @if($activeVoyages->count() > 0)
                            @foreach($activeVoyages as $voyage)
                            <div class="voyage-card" data-voyage-id="{{ $voyage->id }}" onclick="focusVoyage({{ $voyage->id }})">
                                <div class="route-label">
                                    {{ optional($voyage->programme->gareDepart)->nom_gare ?? $voyage->programme->point_depart }}
                                    →
                                    {{ optional($voyage->programme->gareArrivee)->nom_gare ?? $voyage->programme->point_arrive }}
                                </div>
                                <div class="driver-label">
                                    <i class="fas fa-user-circle mr-1"></i>
                                    {{ $voyage->chauffeur->nom ?? '' }} {{ $voyage->chauffeur->prenom ?? '' }}
                                </div>
                                <div class="vehicle-label">
                                    <i class="fas fa-bus mr-1"></i>
                                    {{ $voyage->vehicule->immatriculation ?? 'N/A' }}
                                </div>
                                <div class="speed-label" id="speed-{{ $voyage->id }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>En attente GPS...</span>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="no-tracking">
                                <i class="fas fa-road"></i>
                                <p style="font-weight:600;">Aucun voyage en cours</p>
                                <p style="font-size:0.75rem;">Les voyages passant par votre gare apparaîtront ici</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('trackingMap').setView([6.8276, -5.2893], 7);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 19,
    }).addTo(map);

    const markers = {};         // bus markers, updated every 3s
    const routeLayers = {};     // OSRM route polylines (main + shadow), drawn once per voyage
    const routeShadows = {};    // shadow layers for cleanup
    const gareMarkers = {};     // departure/arrival gare pins, drawn once per voyage
    let isFirstLoad = true;

    function createBusIcon(isOnline = true) {
        return L.divIcon({
            html: `<div class="bus-marker ${isOnline ? '' : 'bus-marker-offline'}"><i class="fas fa-bus"></i></div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 16],
            popupAnchor: [0, -20],
            className: ''
        });
    }

    function createGareIcon(type) {
        // type: 'depart' (green) or 'arrivee' (red)
        const color = type === 'depart' ? '#16a34a' : '#dc2626';
        const icon  = type === 'depart' ? 'fa-sign-out-alt' : 'fa-flag-checkered';
        return L.divIcon({
            html: `<div style="background:${color};border:3px solid white;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;box-shadow:0 3px 10px rgba(0,0,0,0.25);color:white;font-size:11px;"><i class="fas ${icon}"></i></div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 14],
            popupAnchor: [0, -18],
            className: ''
        });
    }

    /**
     * Fetch OSRM road route and draw an orange polyline on the map.
     * Stores the result in routeLayers[voyageId] so it is not re-fetched.
     */
    function drawRoute(voyageId, departLat, departLng, arriveeLat, arriveeLng, departNom, arriveeNom) {
        const url = `https://router.project-osrm.org/route/v1/driving/${departLng},${departLat};${arriveeLng},${arriveeLat}?overview=full&geometries=geojson`;

        fetch(url)
            .then(r => r.json())
            .then(data => {
                if (!data.routes || !data.routes.length) return;

                const geojson = data.routes[0].geometry;
                // Ombre portée (trait épais semi-transparent en dessous)
                const shadow = L.geoJSON(geojson, {
                    style: { color: 'rgba(0,0,0,0.18)', weight: 9, opacity: 1, lineCap: 'round', lineJoin: 'round' }
                }).addTo(map);

                // Trait principal orange style Google Maps
                const polyline = L.geoJSON(geojson, {
                    style: { color: '#f97316', weight: 5, opacity: 1, lineCap: 'round', lineJoin: 'round' }
                }).addTo(map);

                routeShadows[voyageId] = shadow;
                routeLayers[voyageId] = polyline;

                // Gare markers (drawn once alongside the route)
                if (!gareMarkers[voyageId]) {
                    const mDepart = L.marker([departLat, departLng], { icon: createGareIcon('depart') })
                        .addTo(map)
                        .bindPopup(`<div style="font-weight:700;font-size:0.9rem;">Départ</div><div style="font-size:0.82rem;color:#374151;">${departNom}</div>`);
                    const mArrivee = L.marker([arriveeLat, arriveeLng], { icon: createGareIcon('arrivee') })
                        .addTo(map)
                        .bindPopup(`<div style="font-weight:700;font-size:0.9rem;">Arrivée</div><div style="font-size:0.82rem;color:#374151;">${arriveeNom}</div>`);
                    gareMarkers[voyageId] = [mDepart, mArrivee];
                }
            })
            .catch(() => {}); // silencieux si OSRM indisponible
    }

    function fetchLocations() {
        fetch("{{ route('gare-espace.tracking.locations') }}")
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;

                document.getElementById('totalEnCours').textContent = data.total_en_cours;
                document.getElementById('totalTracked').textContent = data.total_tracked;

                const activeIds = new Set();

                data.locations.forEach(loc => {
                    activeIds.add(loc.voyage_id);

                    const popupContent = `
                        <div class="popup-title">${loc.depart} → ${loc.arrivee}</div>
                        <div class="popup-info"><i class="fas fa-user"></i> ${loc.chauffeur}</div>
                        <div class="popup-info"><i class="fas fa-bus"></i> ${loc.vehicule}</div>
                        <div class="popup-info"><i class="fas fa-tachometer-alt"></i> ${loc.speed ? Math.round(loc.speed) + ' km/h' : 'N/A'}</div>
                        <div class="popup-info"><i class="fas fa-clock"></i> ${loc.temps_restant || 'N/A'}</div>
                        <div class="popup-info"><i class="fas fa-calendar"></i> ${loc.date_voyage} à ${loc.heure_depart}</div>
                        <div class="popup-info" style="color:#9ca3af;font-size:0.72rem;"><i class="fas fa-sync"></i> ${loc.last_update}</div>
                    `;

                    // Update or create bus marker
                    if (markers[loc.voyage_id]) {
                        markers[loc.voyage_id].setLatLng([loc.latitude, loc.longitude]);
                        markers[loc.voyage_id].setPopupContent(popupContent);
                    } else {
                        const marker = L.marker([loc.latitude, loc.longitude], {
                            icon: createBusIcon(true)
                        }).addTo(map);
                        marker.bindPopup(popupContent);
                        markers[loc.voyage_id] = marker;
                    }

                    // Draw route once (only if not already drawn and coordinates available)
                    if (!routeLayers[loc.voyage_id]
                        && loc.gare_depart_lat && loc.gare_depart_lng
                        && loc.gare_arrivee_lat && loc.gare_arrivee_lng) {
                        drawRoute(
                            loc.voyage_id,
                            loc.gare_depart_lat, loc.gare_depart_lng,
                            loc.gare_arrivee_lat, loc.gare_arrivee_lng,
                            loc.depart, loc.arrivee
                        );
                    }

                    const speedEl = document.getElementById('speed-' + loc.voyage_id);
                    if (speedEl) {
                        speedEl.innerHTML = `<i class="fas fa-tachometer-alt"></i> <span>${loc.speed ? Math.round(loc.speed) + ' km/h' : 'GPS actif'}</span>`;
                    }
                });

                // Supprimer les couches des voyages terminés
                Object.keys(markers).forEach(id => {
                    if (!activeIds.has(parseInt(id))) {
                        map.removeLayer(markers[id]);
                        delete markers[id];

                        if (routeShadows[id]) {
                            map.removeLayer(routeShadows[id]);
                            delete routeShadows[id];
                        }
                        if (routeLayers[id]) {
                            map.removeLayer(routeLayers[id]);
                            delete routeLayers[id];
                        }
                        if (gareMarkers[id]) {
                            gareMarkers[id].forEach(m => map.removeLayer(m));
                            delete gareMarkers[id];
                        }
                    }
                });

                // Centrer la carte au premier chargement
                if (isFirstLoad && data.locations.length > 0) {
                    const bounds = L.latLngBounds(data.locations.map(l => [l.latitude, l.longitude]));
                    map.fitBounds(bounds, { padding: [50, 50], maxZoom: 13 });
                    isFirstLoad = false;
                }
            })
            .catch(err => console.error('Tracking fetch error:', err));
    }

    window.focusVoyage = function(voyageId) {
        document.querySelectorAll('.voyage-card').forEach(c => c.classList.remove('active'));
        const card = document.querySelector(`.voyage-card[data-voyage-id="${voyageId}"]`);
        if (card) card.classList.add('active');

        if (markers[voyageId]) {
            map.setView(markers[voyageId].getLatLng(), 14, { animate: true });
            markers[voyageId].openPopup();
        }
    };

    // Actualisation toutes les 3 secondes (routes dessinées une seule fois)
    fetchLocations();
    setInterval(fetchLocations, 3000);
});
</script>
@endsection
