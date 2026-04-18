@extends('chauffeur.layouts.template')

@section('title', 'Suivi de mon voyage')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* ── Cacher le sidebar sur cette page ── */
    #chauffeurSidebar, #sidebarOverlay { display: none !important; }
    .chauffeur-main-wrapper {
        margin-left: 0 !important;
        padding: 0 !important;
        background: #0f172a !important;
    }

    #driverMap {
        position: fixed;
        inset: 118px 0 0 0; /* sous navbar (64px) + topbar (~54px) */
        z-index: 1;
    }
    @media (max-width: 767.98px) {
        #driverMap { inset: 110px 0 0 0; }
    }
    @media (max-width: 480px) {
        #driverMap { inset: 126px 0 0 0; }
    }

    /* ── Top bar ── */
    .driver-topbar {
        position: fixed;
        top: 64px;
        left: 0;
        right: 0;
        z-index: 10;
        background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
        padding: 10px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        backdrop-filter: blur(10px);
    }
    @media (max-width: 767.98px) {
        .driver-topbar { top: 56px; padding: 8px 14px; }
    }
    @media (max-width: 480px) {
        .driver-topbar {
            flex-direction: column;
            align-items: flex-start;
            padding: 8px 12px;
            gap: 5px;
        }
    }

    .route-pill {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 800;
        color: white;
        font-size: .95rem;
        flex-wrap: wrap;
    }
    .route-pill .city { font-size: 0.85rem; }
    .route-pill .arrow { color: #3b82f6; font-size: 0.9rem; }

    .live-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: rgba(239,68,68,0.15);
        border: 1px solid rgba(239,68,68,0.3);
        color: #f87171;
        padding: 5px 14px;
        border-radius: 999px;
        font-weight: 800;
        font-size: 0.75rem;
        letter-spacing: .05em;
    }
    .live-dot {
        width: 8px; height: 8px;
        background: #ef4444;
        border-radius: 50%;
        animation: livePulse 1.4s ease-in-out infinite;
    }
    @keyframes livePulse {
        0%,100% { opacity:1; transform:scale(1); }
        50%      { opacity:.4; transform:scale(1.4); }
    }

    /* ── HUD bottom ── */
    .driver-hud {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: center;
        padding: 0 12px;
        width: 100%;
        box-sizing: border-box;
    }
    @media (max-width: 767.98px) {
        .driver-hud { bottom: 14px; gap: 8px; }
    }
    @media (max-width: 480px) {
        .driver-hud { bottom: 10px; }
        .hud-card {
            min-width: 80px !important;
            padding: 9px 12px !important;
            border-radius: 14px !important;
        }
        .hud-card .hud-val { font-size: 1.2rem !important; }
        .hud-card .hud-lbl { font-size: 0.58rem !important; }
    }

    .hud-card {
        background: rgba(15,23,42,0.92);
        border: 1px solid rgba(255,255,255,0.1);
        backdrop-filter: blur(16px);
        border-radius: 18px;
        padding: 12px 16px;
        min-width: 110px;
        flex: 1;
        max-width: 150px;
        text-align: center;
        color: white;
    }
    .hud-card .hud-val {
        font-size: 1.5rem;
        font-weight: 900;
        line-height: 1;
    }
    .hud-card .hud-lbl {
        font-size: 0.63rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: rgba(255,255,255,0.5);
        margin-top: 4px;
    }
    .hud-card.speed .hud-val { color: #3b82f6; }
    .hud-card.gps-ok .hud-val { color: #22c55e; }
    .hud-card.gps-err .hud-val { color: #ef4444; }

    /* ── Back button ── */
    .back-btn {
        position: fixed;
        top: 126px;
        left: 14px;
        z-index: 10;
        background: rgba(15,23,42,0.85);
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 11px;
        color: rgba(255,255,255,0.7);
        padding: 7px 12px;
        font-size: 0.75rem;
        font-weight: 700;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
        backdrop-filter: blur(8px);
        transition: all .2s;
    }
    .back-btn:hover { color: white; border-color: rgba(59,130,246,.5); text-decoration: none; }
    @media (max-width: 767.98px) { .back-btn { left: 10px; top: 118px; } }
    @media (max-width: 480px) { .back-btn { top: 134px; } }

    /* ── Driver marker ── */
    .driver-marker-wrap {
        position: relative;
        width: 44px; height: 44px;
    }
    .driver-pulse-ring {
        position: absolute;
        inset: -8px;
        border-radius: 50%;
        background: rgba(59,130,246,0.25);
        animation: driverPulse 2s ease-out infinite;
    }
    .driver-marker-inner {
        position: relative;
        width: 44px; height: 44px;
        background: linear-gradient(135deg,#1d4ed8,#3b82f6);
        border: 3px solid white;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 16px rgba(59,130,246,0.5);
        color: white; font-size: 18px;
        z-index: 1;
    }
    @keyframes driverPulse {
        0%   { transform: scale(1);   opacity: .6; }
        100% { transform: scale(2.2); opacity: 0;  }
    }

    .leaflet-popup-content { font-family: 'Inter', sans-serif; }
    .leaflet-popup-content-wrapper { border-radius: 16px !important; border: none !important; box-shadow: 0 8px 32px rgba(0,0,0,.18) !important; }
</style>
@endsection

@section('content')

{{-- Map full screen --}}
<div id="driverMap"></div>

{{-- Top bar --}}
<div class="driver-topbar">
    <div class="route-pill">
        <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background:rgba(59,130,246,.2);">
            <i class="fas fa-bus text-blue-400 text-sm"></i>
        </div>
        <span class="city">{{ $gareDepart->nom_gare ?? $voyage->programme->point_depart }}</span>
        <span class="arrow"><i class="fas fa-arrow-right"></i></span>
        <span class="city">{{ $gareArrivee->nom_gare ?? $voyage->programme->point_arrive }}</span>
    </div>

    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
        <span style="font-size:.72rem;color:rgba(255,255,255,.5);font-weight:600;">
            <i class="fas fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($voyage->date_voyage)->format('d/m/Y') }}
            &nbsp;·&nbsp;
            <i class="fas fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($voyage->programme->heure_depart)->format('H:i') }}
            @if($voyage->vehicule)
                &nbsp;·&nbsp;<i class="fas fa-id-card mr-1"></i>{{ $voyage->vehicule->immatriculation }}
            @endif
        </span>
        <div class="live-badge">
            <div class="live-dot"></div> EN COURS
        </div>
    </div>
</div>

{{-- Back button --}}
<a href="{{ route('chauffeur.voyages.index') }}" class="back-btn">
    <i class="fas fa-arrow-left"></i> Mes voyages
</a>

{{-- HUD bottom --}}
<div class="driver-hud">
    <div class="hud-card speed">
        <div class="hud-val" id="hud-speed">—</div>
        <div class="hud-lbl">km/h</div>
    </div>
    <div class="hud-card" id="hud-gps-card">
        <div class="hud-val" id="hud-gps-icon"><i class="fas fa-satellite-dish" style="font-size:1.4rem;color:#9ca3af;"></i></div>
        <div class="hud-lbl" id="hud-gps-lbl">GPS inactif</div>
    </div>
    <div class="hud-card">
        <div class="hud-val" id="hud-eta" style="color:#a78bfa;font-size:1.1rem;">—</div>
        <div class="hud-lbl">Arrivée estimée</div>
    </div>
    <div class="hud-card">
        <div class="hud-val" id="hud-updates" style="color:#f59e0b;font-size:1.1rem;">0</div>
        <div class="hud-lbl">Mises à jour GPS</div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Config ── */
    const UPDATE_URL  = "{{ route('chauffeur.voyages.update-location', $voyage) }}";
    const CSRF        = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const INIT_LAT    = {{ $initialLat }};
    const INIT_LNG    = {{ $initialLng }};
    const VOYAGE_ID   = {{ $voyage->id }};

    const DEPART = {
        lat: {{ $gareDepart?->latitude ?? 'null' }},
        lng: {{ $gareDepart?->longitude ?? 'null' }},
        nom: "{{ addslashes($gareDepart->nom_gare ?? $voyage->programme->point_depart) }}"
    };
    const ARRIVEE = {
        lat: {{ $gareArrivee?->latitude ?? 'null' }},
        lng: {{ $gareArrivee?->longitude ?? 'null' }},
        nom: "{{ addslashes($gareArrivee->nom_gare ?? $voyage->programme->point_arrive) }}"
    };

    /* ── Map ── */
    const map = L.map('driverMap', { zoomControl: false }).setView([INIT_LAT, INIT_LNG], 12);
    L.control.zoom({ position: 'topright' }).addTo(map);

    // Tuiles détaillées (CartoDB Voyager — routes visibles avec labels)
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; <a href="https://carto.com/">CARTO</a>',
        maxZoom: 19,
        subdomains: 'abcd'
    }).addTo(map);

    /* ── Marker chauffeur ── */
    function createDriverIcon() {
        return L.divIcon({
            html: `<div class="driver-marker-wrap">
                        <div class="driver-pulse-ring"></div>
                        <div class="driver-marker-inner"><i class="fas fa-bus"></i></div>
                   </div>`,
            iconSize: [44, 44],
            iconAnchor: [22, 22],
            popupAnchor: [0, -28],
            className: ''
        });
    }

    function createGareIcon(type) {
        const color = type === 'depart' ? '#22c55e' : '#ef4444';
        const icon  = type === 'depart' ? 'fa-sign-out-alt' : 'fa-flag-checkered';
        return L.divIcon({
            html: `<div style="background:${color};border:3px solid white;border-radius:50%;width:30px;height:30px;display:flex;align-items:center;justify-content:center;box-shadow:0 3px 12px rgba(0,0,0,.4);color:white;font-size:12px;"><i class="fas ${icon}"></i></div>`,
            iconSize: [30, 30],
            iconAnchor: [15, 15],
            popupAnchor: [0, -20],
            className: ''
        });
    }

    const driverMarker = L.marker([INIT_LAT, INIT_LNG], { icon: createDriverIcon() })
        .addTo(map)
        .bindPopup(`
            <div style="padding:4px 0;">
                <div style="font-weight:800;font-size:.9rem;color:#1e3a5f;margin-bottom:6px;">
                    <i class="fas fa-bus" style="color:#3b82f6;margin-right:6px;"></i>Ma position
                </div>
                <div style="font-size:.78rem;color:#6b7280;"><i class="fas fa-user mr-1" style="color:#3b82f6;"></i>{{ $chauffeur->prenom }} {{ $chauffeur->nom ?? $chauffeur->name }}</div>
                <div style="font-size:.78rem;color:#6b7280;" id="popup-speed"><i class="fas fa-tachometer-alt mr-1" style="color:#3b82f6;"></i>GPS en attente...</div>
            </div>
        `);

    /* ── Marqueurs gares (si coords disponibles) ── */
    if (DEPART.lat && DEPART.lng) {
        L.marker([DEPART.lat, DEPART.lng], { icon: createGareIcon('depart') })
            .addTo(map)
            .bindPopup(`<div style="font-weight:700;font-size:.85rem;color:#16a34a;"><i class="fas fa-sign-out-alt mr-1"></i>Départ</div><div style="font-size:.78rem;color:#374151;">${DEPART.nom}</div>`);
    }
    if (ARRIVEE.lat && ARRIVEE.lng) {
        L.marker([ARRIVEE.lat, ARRIVEE.lng], { icon: createGareIcon('arrivee') })
            .addTo(map)
            .bindPopup(`<div style="font-weight:700;font-size:.85rem;color:#dc2626;"><i class="fas fa-flag-checkered mr-1"></i>Arrivée</div><div style="font-size:.78rem;color:#374151;">${ARRIVEE.nom}</div>`);
    }

    /* ── Tracé OSRM dynamique (recalcul depuis position chauffeur) ── */
    let currentRouteLayers = []; // Stocke les couches du tracé actuel pour les supprimer
    let currentRouteCoords  = []; // Coordonnées [lat,lng] du tracé actuel pour le calcul de déviation
    let isRerouting         = false; // Verrou pour éviter les requêtes simultanées
    const REROUTE_THRESHOLD_M = 100; // Recalcul si le chauffeur s'éloigne de plus de 100m du tracé

    /**
     * Calcule la distance en mètres d'un point [lat,lng] au segment le plus proche du tracé.
     */
    function distanceToRoute(lat, lng) {
        if (!currentRouteCoords.length) return Infinity;
        const p = L.latLng(lat, lng);
        let minDist = Infinity;
        for (let i = 0; i < currentRouteCoords.length - 1; i++) {
            const a = L.latLng(currentRouteCoords[i]);
            const b = L.latLng(currentRouteCoords[i + 1]);
            // Distance du point P au segment AB
            const dist = p.distanceTo(a);
            if (dist < minDist) minDist = dist;
        }
        return minDist;
    }

    /**
     * Dessine ou redessine l'itinéraire depuis (fromLat, fromLng) jusqu'à la gare d'arrivée.
     * @param {number} fromLat - Latitude actuelle du chauffeur
     * @param {number} fromLng - Longitude actuelle du chauffeur
     * @param {boolean} fitView - Si true, zoome sur le tracé entier
     */
    function drawRoute(fromLat, fromLng, fitView = false) {
        if (isRerouting || !ARRIVEE.lat || !ARRIVEE.lng) return;
        isRerouting = true;

        const url = `https://router.project-osrm.org/route/v1/driving/${fromLng},${fromLat};${ARRIVEE.lng},${ARRIVEE.lat}?overview=full&geometries=geojson`;

        fetch(url)
            .then(r => r.json())
            .then(data => {
                if (!data.routes?.length) return;
                const geojson = data.routes[0].geometry;

                // 1. Supprimer l'ancien tracé
                currentRouteLayers.forEach(layer => map.removeLayer(layer));
                currentRouteLayers = [];

                // 2. Dessiner le nouveau tracé
                const shadow = L.geoJSON(geojson, {
                    style: { color: 'rgba(0,0,0,0.3)', weight: 10, opacity: 1, lineCap: 'round', lineJoin: 'round' }
                }).addTo(map);
                const line = L.geoJSON(geojson, {
                    style: { color: '#3b82f6', weight: 5, opacity: 0.95, lineCap: 'round', lineJoin: 'round' }
                }).addTo(map);

                currentRouteLayers.push(shadow, line);

                // 3. Mettre à jour les coordonnées du tracé pour la détection de déviation
                currentRouteCoords = geojson.coordinates.map(c => [c[1], c[0]]);

                // 4. Si premier tracé, zoomer pour tout voir
                if (fitView && currentRouteCoords.length) {
                    map.fitBounds(L.latLngBounds(currentRouteCoords), { padding: [60, 60], maxZoom: 13 });
                }
            })
            .catch(() => {})
            .finally(() => { isRerouting = false; });
    }

    // Tracé initial depuis la gare de départ (ou position initiale si pas de coords gare)
    if (DEPART.lat && DEPART.lng) {
        drawRoute(DEPART.lat, DEPART.lng, true);
    } else {
        drawRoute(INIT_LAT, INIT_LNG, true);
    }

    /* ── GPS & envoi position ── */
    let gpsUpdateCount = 0;
    let lastSpeed = null;

    function sendLocation(lat, lng, speed, heading) {
        fetch(UPDATE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ latitude: lat, longitude: lng, speed: speed, heading: heading })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                gpsUpdateCount++;
                document.getElementById('hud-updates').textContent = gpsUpdateCount;
                if (data.temps_restant) {
                    document.getElementById('hud-eta').textContent = data.temps_restant;
                }
            }
        })
        .catch(() => {});
    }

    let lastSent = 0;
    const SEND_INTERVAL_MS = 5000;

    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(
            function(pos) {
                const lat     = pos.coords.latitude;
                const lng     = pos.coords.longitude;
                const speed   = pos.coords.speed !== null ? pos.coords.speed * 3.6 : null; // m/s → km/h
                const heading = pos.coords.heading;
                lastSpeed = speed;

                // Mettre à jour marker
                driverMarker.setLatLng([lat, lng]);

                // Centrer carte doucement sur le chauffeur
                map.panTo([lat, lng], { animate: true, duration: 1 });

                // HUD vitesse
                const kmh = speed !== null ? Math.round(speed) : null;
                document.getElementById('hud-speed').textContent = kmh !== null ? kmh : '—';

                // HUD GPS ok
                const card = document.getElementById('hud-gps-card');
                card.classList.remove('gps-err');
                card.classList.add('gps-ok');
                document.getElementById('hud-gps-icon').innerHTML = `<i class="fas fa-satellite-dish" style="font-size:1.4rem;color:#22c55e;"></i>`;
                document.getElementById('hud-gps-lbl').textContent = 'GPS actif';

                // Popup
                const popupSpeed = document.getElementById('popup-speed');
                if (popupSpeed) {
                    popupSpeed.innerHTML = `<i class="fas fa-tachometer-alt mr-1" style="color:#3b82f6;"></i>${kmh !== null ? kmh + ' km/h' : 'GPS actif'}`;
                }

                // ── Détection de déviation et recalcul d'itinéraire ──
                // Si le chauffeur s'éloigne de plus de REROUTE_THRESHOLD_M mètres du tracé,
                // on recalcule l'itinéraire depuis sa position actuelle.
                const deviation = distanceToRoute(lat, lng);
                if (deviation > REROUTE_THRESHOLD_M) {
                    drawRoute(lat, lng, false);
                }

                // Envoyer au serveur max toutes les 5s
                const now = Date.now();
                if (now - lastSent >= SEND_INTERVAL_MS) {
                    sendLocation(lat, lng, speed, heading);
                    lastSent = now;
                }
            },
            function(err) {
                // GPS refusé ou erreur
                const card = document.getElementById('hud-gps-card');
                card.classList.remove('gps-ok');
                card.classList.add('gps-err');
                document.getElementById('hud-gps-icon').innerHTML = `<i class="fas fa-exclamation-triangle" style="font-size:1.3rem;color:#ef4444;"></i>`;
                document.getElementById('hud-gps-lbl').textContent = 'GPS refusé';
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 3000 }
        );
    } else {
        document.getElementById('hud-gps-lbl').textContent = 'GPS non supporté';
    }

});
</script>
@endsection
