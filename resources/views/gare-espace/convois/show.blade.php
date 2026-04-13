@extends('gare-espace.layouts.template')

@section('title', 'Détail convoi')

@section('styles')
<style>
    .convoi-show-shell {
        background: linear-gradient(180deg, #f9fafb 0%, #f3f4f6 100%);
        border-radius: 28px;
        padding: 24px;
        border: 1px solid #eef2f7;
    }

    .show-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    .show-head h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 900;
        color: #0f172a;
        letter-spacing: -0.4px;
    }

    .show-head p {
        margin: 4px 0 0;
        color: #6b7280;
        font-size: 13px;
        font-weight: 600;
    }

    .text-orange {
        color: #f97316;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 13px;
        border-radius: 10px;
        background: #fff;
        border: 1px solid #e5e7eb;
        color: #475569;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        text-decoration: none !important;
        letter-spacing: 0.5px;
    }

    .metric-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }

    .metric-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 14px 16px;
    }

    .metric-label {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #9ca3af;
        font-weight: 900;
        margin-bottom: 6px;
    }

    .metric-value {
        font-size: 17px;
        line-height: 1.2;
        font-weight: 900;
        color: #111827;
    }

    .metric-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        padding: 6px 10px;
        border-radius: 999px;
        background: #fff7ed;
        color: #ea580c;
    }

    .metric-status::before {
        content: "";
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
    }

    .table-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        overflow: hidden;
    }

    .table-modern thead th {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        font-weight: 900;
        color: #94a3b8;
        background: #f8fafc;
        padding: 14px 16px;
        border-bottom: 1px solid #eef2f7;
    }

    .table-modern tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
    }

    .table-modern tbody tr:hover td {
        background: #fff7ed;
    }

    .index-pill {
        display: inline-flex;
        min-width: 28px;
        justify-content: center;
        border-radius: 8px;
        background: #fff7ed;
        color: #ea580c;
        font-size: 11px;
        font-weight: 900;
        padding: 4px 8px;
    }

    @media (max-width: 991px) {
        .metric-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
</style>
@endsection

@section('content')
<div class="convoi-show-shell">
    <div class="show-head">
        <div>
            <h1>Détail <span class="text-orange">Convoi</span></h1>
            <p>Référence : {{ $convoi->reference }}</p>
        </div>
        <a href="{{ route('gare-espace.convois.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="metric-grid">
        <div class="metric-card">
            <div class="metric-label">Compagnie</div>
            <div class="metric-value">{{ $convoi->compagnie->name ?? '-' }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Itinéraire</div>
            <div class="metric-value" style="font-size:14px;">
                {{ $convoi->itineraire ? ($convoi->itineraire->point_depart . ' -> ' . $convoi->itineraire->point_arrive) : '-' }}
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Personnes</div>
            <div class="metric-value">{{ $convoi->nombre_personnes }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Statut</div>
            <div class="metric-status">{{ str_replace('_', ' ', $convoi->statut) }}</div>
        </div>
    </div>

    <div class="table-card mb-3">
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 d-flex justify-content-between align-items-center">
            <h3 class="text-sm font-black text-gray-800 uppercase m-0">Suivi GPS temps réel</h3>
            <span class="index-pill" id="trackingStatusBadge">--</span>
        </div>
        <div class="p-4">
            <div class="font-weight-bold mb-1" id="trackingCoords">Position: --</div>
            <div class="text-muted" style="font-size:12px;" id="trackingMeta">Dernière mise à jour: --</div>
            <a href="#" id="trackingMapLink" target="_blank" class="btn-back mt-2" style="display:none;">
                <i class="fas fa-map-marker-alt"></i> Ouvrir sur Google Maps
            </a>
        </div>
    </div>

    <div class="table-card">
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
            <h3 class="text-sm font-black text-gray-800 uppercase m-0">Passagers</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left table-modern">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Nom</th>
                        <th class="text-center">Prénoms</th>
                        <th class="text-center">Contact</th>
                        <th class="text-center">Email</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($convoi->passagers as $index => $passager)
                        <tr>
                            <td><span class="index-pill">{{ $index + 1 }}</span></td>
                            <td>{{ $passager->nom }}</td>
                            <td>{{ $passager->prenoms }}</td>
                            <td>{{ $passager->contact }}</td>
                            <td>{{ $passager->email ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-sm text-gray-500 py-8">Aucun passager enregistré.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const endpoint = "{{ route('gare-espace.convois.location', $convoi->id) }}";
    const coordsEl = document.getElementById('trackingCoords');
    const metaEl = document.getElementById('trackingMeta');
    const badgeEl = document.getElementById('trackingStatusBadge');
    const mapLinkEl = document.getElementById('trackingMapLink');

    function updateTracking() {
        fetch(endpoint, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;

                badgeEl.textContent = (data.statut || '').replace('_', ' ') || '--';

                if (data.latitude !== null && data.longitude !== null) {
                    coordsEl.textContent = `Position: ${data.latitude}, ${data.longitude}`;
                    metaEl.textContent = `Dernière mise à jour: ${data.last_update} • Chauffeur: ${data.chauffeur} • Véhicule: ${data.vehicule}`;
                    mapLinkEl.href = `https://www.google.com/maps?q=${data.latitude},${data.longitude}`;
                    mapLinkEl.style.display = 'inline-flex';
                } else {
                    coordsEl.textContent = 'Position: en attente du GPS chauffeur';
                    metaEl.textContent = `Dernière mise à jour: ${data.last_update}`;
                    mapLinkEl.style.display = 'none';
                }
            })
            .catch(() => {});
    }

    updateTracking();
    setInterval(updateTracking, 7000);
});
</script>
@endsection

