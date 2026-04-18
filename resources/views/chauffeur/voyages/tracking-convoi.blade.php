@extends('chauffeur.layouts.template')

@section('title', 'Suivi convoi ' . ($isRetour ? 'retour' : 'aller'))

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
        inset: 118px 0 0 0;
        z-index: 1;
    }
    @media (max-width: 767.98px) {
        #driverMap { inset: 104px 0 0 0; }
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
    @media (max-width: 600px) {
        .driver-topbar {
            top: 56px;
            padding: 8px 12px;
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
        }
        #driverMap { inset: 130px 0 0 0; }
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
    .route-pill .arrow { color: #3b82f6; font-size: .9rem; }

    .live-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: rgba(239,68,68,0.15);
        border: 1px solid rgba(239,68,68,0.3);
        color: #f87171;
        padding: 5px 12px;
        border-radius: 999px;
        font-weight: 800;
        font-size: 0.72rem;
        letter-spacing: .05em;
        flex-shrink: 0;
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

    .retour-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(139,92,246,0.2);
        border: 1px solid rgba(139,92,246,0.4);
        color: #c4b5fd;
        padding: 3px 10px;
        border-radius: 999px;
        font-weight: 800;
        font-size: 0.68rem;
        letter-spacing: .06em;
        text-transform: uppercase;
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
    @media (max-width: 480px) {
        .driver-hud {
            bottom: 12px;
            gap: 8px;
        }
        .hud-card {
            min-width: 90px !important;
            padding: 10px 14px !important;
        }
        .hud-card .hud-val { font-size: 1.3rem !important; }
    }

    .hud-card {
        background: rgba(15,23,42,0.92);
        border: 1px solid rgba(255,255,255,0.1);
        backdrop-filter: blur(16px);
        border-radius: 18px;
        padding: 12px 18px;
        min-width: 110px;
        text-align: center;
        color: white;
        flex: 1;
        max-width: 150px;
    }
    .hud-card .hud-val {
        font-size: 1.5rem;
        font-weight: 900;
        line-height: 1;
    }
    .hud-card .hud-lbl {
        font-size: 0.62rem;
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
        border-radius: 12px;
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
    @media (max-width: 600px) {
        .back-btn { top: 138px; left: 10px; }
    }

    /* ── Driver marker ── */
    .driver-marker-wrap { position: relative; width: 44px; height: 44px; }
    .driver-pulse-ring {
        position: absolute; inset: -8px; border-radius: 50%;
        background: rgba(59,130,246,0.25);
        animation: driverPulse 2s ease-out infinite;
    }
    .driver-marker-inner {
        position: relative; width: 44px; height: 44px;
        background: linear-gradient(135deg,#1d4ed8,#3b82f6);
        border: 3px solid white; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 16px rgba(59,130,246,0.5);
        color: white; font-size: 18px; z-index: 1;
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
        <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(59,130,246,.2);">
            <i class="fas fa-bus text-blue-400 text-sm"></i>
        </div>
        <span class="city">{{ $depart }}</span>
        <span class="arrow"><i class="fas fa-arrow-right"></i></span>
        <span class="city">{{ $arrivee }}</span>
        @if($isRetour)
        <span class="retour-badge"><i class="fas fa-undo-alt"></i> Retour</span>
        @endif
    </div>

    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
        <span style="font-size:.75rem;color:rgba(255,255,255,.5);font-weight:600;">
            <i class="fas fa-calendar mr-1"></i>{{ $dateLabel }}
            @if($heureLabel)
                &nbsp;·&nbsp;<i class="fas fa-clock mr-1"></i>{{ $heureLabel }}
            @endif
            @if($convoi->vehicule)
                &nbsp;·&nbsp;<i class="fas fa-id-card mr-1"></i>{{ $convoi->vehicule->immatriculation }}
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
        <div class="hud-val" id="hud-gps-icon"><i class="fas fa-satellite-dish" style="font-size:1.3rem;color:#9ca3af;"></i></div>
        <div class="hud-lbl" id="hud-gps-lbl">GPS inactif</div>
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

    const UPDATE_URL = "{{ route('chauffeur.voyages.convois.update-location', $convoi->id) }}";
    const CSRF       = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const INIT_LAT   = {{ $initialLat }};
    const INIT_LNG   = {{ $initialLng }};

    /* ── Map ── */
    const map = L.map('driverMap', { zoomControl: false }).setView([INIT_LAT, INIT_LNG], 12);
    L.control.zoom({ position: 'topright' }).addTo(map);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OSM &copy; CARTO',
        maxZoom: 19,
        subdomains: 'abcd'
    }).addTo(map);

    /* ── Driver marker ── */
    function createDriverIcon() {
        return L.divIcon({
            html: `<div class="driver-marker-wrap">
                        <div class="driver-pulse-ring"></div>
                        <div class="driver-marker-inner"><i class="fas fa-bus"></i></div>
                   </div>`,
            iconSize: [44, 44], iconAnchor: [22, 22], popupAnchor: [0, -28], className: ''
        });
    }

    const driverMarker = L.marker([INIT_LAT, INIT_LNG], { icon: createDriverIcon() })
        .addTo(map)
        .bindPopup(`
            <div style="padding:4px 0;">
                <div style="font-weight:800;font-size:.9rem;color:#1e3a5f;margin-bottom:6px;">
                    <i class="fas fa-bus" style="color:#3b82f6;margin-right:6px;"></i>Ma position
                </div>
                <div style="font-size:.78rem;color:#6b7280;"><i class="fas fa-user mr-1" style="color:#3b82f6;"></i>{{ $chauffeur->prenom }} {{ $chauffeur->name ?? $chauffeur->nom }}</div>
                <div style="font-size:.78rem;color:#6b7280;" id="popup-speed"><i class="fas fa-tachometer-alt mr-1" style="color:#3b82f6;"></i>GPS en attente...</div>
            </div>
        `);

    /* ── Route OSRM (si coordonnées connues) ── */
    let routeLayers = [];
    function drawRoute(fromLat, fromLng) {
        // Pas de destination connue pour le convoi → pas de tracé automatique
        // On pourrait ajouter des marqueurs fixes si des coords sont disponibles plus tard
    }

    /* ── GPS ── */
    let gpsUpdateCount = 0;

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
                const speed   = pos.coords.speed !== null ? pos.coords.speed * 3.6 : null;
                const heading = pos.coords.heading;

                driverMarker.setLatLng([lat, lng]);
                map.panTo([lat, lng], { animate: true, duration: 1 });

                const kmh = speed !== null ? Math.round(speed) : null;
                document.getElementById('hud-speed').textContent = kmh !== null ? kmh : '—';

                const card = document.getElementById('hud-gps-card');
                card.classList.remove('gps-err');
                card.classList.add('gps-ok');
                document.getElementById('hud-gps-icon').innerHTML = `<i class="fas fa-satellite-dish" style="font-size:1.3rem;color:#22c55e;"></i>`;
                document.getElementById('hud-gps-lbl').textContent = 'GPS actif';

                const popupSpeed = document.getElementById('popup-speed');
                if (popupSpeed) {
                    popupSpeed.innerHTML = `<i class="fas fa-tachometer-alt mr-1" style="color:#3b82f6;"></i>${kmh !== null ? kmh + ' km/h' : 'GPS actif'}`;
                }

                const now = Date.now();
                if (now - lastSent >= SEND_INTERVAL_MS) {
                    sendLocation(lat, lng, speed, heading);
                    lastSent = now;
                }
            },
            function(err) {
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
