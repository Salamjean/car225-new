@extends('compagnie.layouts.template')

@section('page-title', 'Détail Convoi')
@section('page-subtitle', 'Liste des passagers du convoi sélectionné')

@section('content')
    <div class="dashboard-page">
        <div class="mb-3">
            <a href="{{ route('compagnie.convois.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left mr-2"></i> Retour aux convois
            </a>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <div class="dash-card p-3">
                    <div class="text-muted small">Référence</div>
                    <div class="font-weight-bold">{{ $convoi->reference }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dash-card p-3">
                    <div class="text-muted small">Demandeur</div>
                    <div class="font-weight-bold">{{ trim(($convoi->user->name ?? '') . ' ' . ($convoi->user->prenom ?? '')) ?: 'Utilisateur' }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dash-card p-3">
                    <div class="text-muted small">Nombre de personnes</div>
                    <div class="font-weight-bold">{{ $convoi->nombre_personnes }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dash-card p-3">
                    <div class="text-muted small">Statut</div>
                    <div class="font-weight-bold text-capitalize">{{ str_replace('_', ' ', $convoi->statut) }}</div>
                </div>
            </div>
            <div class="col-md-12 mt-2">
                <div class="dash-card p-3">
                    <div class="text-muted small">Itinéraire</div>
                    <div class="font-weight-bold">
                        {{ $convoi->itineraire ? ($convoi->itineraire->point_depart . ' -> ' . $convoi->itineraire->point_arrive) : '-' }}
                    </div>
                </div>
            </div>
            <div class="col-md-12 mt-2">
                <div class="dash-card p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted small">Suivi GPS temps réel</div>
                        <span class="badge badge-light" id="trackingStatusBadge">--</span>
                    </div>
                    <div class="font-weight-bold mb-1" id="trackingCoords">Position: --</div>
                    <div class="small text-muted" id="trackingMeta">Dernière mise à jour: --</div>
                    <a href="#" id="trackingMapLink" target="_blank" class="btn btn-sm btn-outline-primary mt-2 d-none">
                        <i class="fas fa-map-marker-alt mr-1"></i> Ouvrir sur Google Maps
                    </a>
                </div>
            </div>
        </div>

        <div class="dash-card">
            <div class="dash-card-head">
                <h3 class="dash-card-title m-0"><i class="fas fa-list mr-2 text-orange"></i> Passagers</h3>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Prénoms</th>
                            <th>Contact</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($convoi->passagers as $index => $passager)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="font-weight-bold">{{ $passager->nom }}</td>
                                <td>{{ $passager->prenoms }}</td>
                                <td>{{ $passager->contact }}</td>
                                <td>{{ $passager->email ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Aucun passager enregistré.</td>
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
    const endpoint = "{{ route('compagnie.convois.location', $convoi->id) }}";
    const coordsEl = document.getElementById('trackingCoords');
    const metaEl = document.getElementById('trackingMeta');
    const badgeEl = document.getElementById('trackingStatusBadge');
    const mapLinkEl = document.getElementById('trackingMapLink');

    function updateTracking() {
        fetch(endpoint, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;

                const label = (data.statut || '').replace('_', ' ');
                badgeEl.textContent = label || '--';

                if (data.latitude !== null && data.longitude !== null) {
                    coordsEl.textContent = `Position: ${data.latitude}, ${data.longitude}`;
                    metaEl.textContent = `Dernière mise à jour: ${data.last_update} • Chauffeur: ${data.chauffeur} • Véhicule: ${data.vehicule}`;
                    mapLinkEl.href = `https://www.google.com/maps?q=${data.latitude},${data.longitude}`;
                    mapLinkEl.classList.remove('d-none');
                } else {
                    coordsEl.textContent = 'Position: en attente du GPS chauffeur';
                    metaEl.textContent = `Dernière mise à jour: ${data.last_update}`;
                    mapLinkEl.classList.add('d-none');
                }
            })
            .catch(() => {});
    }

    updateTracking();
    setInterval(updateTracking, 7000);
});
</script>
@endsection

