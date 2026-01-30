@extends('agent.layouts.template')

@section('content')
    <div class="mdc-layout-grid">
        <div class="mdc-layout-grid__inner">
            <div class="mdc-layout-grid__cell stretch-card mdc-layout-grid__cell--span-12">
                <div class="mdc-card p-4">
                    
                    <!-- En-tête -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="card-title mb-1">
                                <i class="material-icons mr-2" style="vertical-align: middle;">history</i>
                                Historique des Scans
                            </h6>
                            <p class="text-muted mb-0">Tous les passagers scannés</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('agent.reservations.index') }}" class="btn btn-primary mr-2">
                                <i class="material-icons mr-1" style="vertical-align: middle;">qr_code_scanner</i>
                                Scanner
                            </a>
                            <a href="{{ route('agent.reservations.recherche') }}" class="btn btn-secondary">
                                <i class="material-icons mr-1" style="vertical-align: middle;">search</i>
                                Rechercher
                            </a>
                        </div>
                    </div>

                    <!-- Filtres -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Date</label>
                            <input type="date" id="filterDate" class="form-control" value="{{ request('date', now()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Type de trajet</label>
                            <select id="filterType" class="form-control">
                                <option value="">Tous</option>
                                <option value="aller_simple" {{ request('type') == 'aller_simple' ? 'selected' : '' }}>Aller Simple</option>
                                <option value="aller" {{ request('type') == 'aller' ? 'selected' : '' }}>Aller (AR)</option>
                                <option value="retour" {{ request('type') == 'retour' ? 'selected' : '' }}>Retour (AR)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Trajet</label>
                            <select id="filterTrajet" class="form-control">
                                <option value="">Tous les trajets</option>
                                @foreach($trajets as $trajet)
                                    <option value="{{ $trajet }}">{{ $trajet }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-primary" onclick="applyFilters()">
                                <i class="material-icons mr-1" style="vertical-align: middle;">filter_list</i>
                                Filtrer
                            </button>
                            <button class="btn btn-outline-secondary ml-2" onclick="resetFilters()">
                                <i class="material-icons" style="vertical-align: middle;">refresh</i>
                            </button>
                        </div>
                    </div>

                    <!-- Statistiques rapides -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['total'] }}</h4>
                                            <small>Total scannés</small>
                                        </div>
                                        <i class="material-icons" style="font-size: 36px; opacity: 0.7;">check_circle</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['aller_simple'] }}</h4>
                                            <small>Aller Simple</small>
                                        </div>
                                        <i class="material-icons" style="font-size: 36px; opacity: 0.7;">arrow_forward</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['aller'] }}</h4>
                                            <small>Aller (AR)</small>
                                        </div>
                                        <i class="material-icons" style="font-size: 36px; opacity: 0.7;">east</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['retour'] }}</h4>
                                            <small>Retour (AR)</small>
                                        </div>
                                        <i class="material-icons" style="font-size: 36px; opacity: 0.7;">west</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau historique -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Référence</th>
                                    <th>Passager</th>
                                    <th>Trajet</th>
                                    <th>Type</th>
                                    <th>Place</th>
                                    <th>Scanné à</th>
                                    <th>Véhicule</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($scans as $scan)
                                    <tr>
                                        <td>
                                            <span class="badge badge-dark">{{ $scan->reference }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $scan->passager_prenom }} {{ $scan->passager_nom }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $scan->passager_telephone }}</small>
                                        </td>
                                        <td>
                                            @if($scan->programme)
                                                {{ $scan->programme->point_depart }} → {{ $scan->programme->point_arrive }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                // Déterminer le type de scan
                                                if (!$scan->is_aller_retour) {
                                                    $typeScan = 'aller_simple';
                                                    $badgeClass = 'badge-info';
                                                    $typeLabel = 'Aller Simple';
                                                    $icon = 'arrow_forward';
                                                } elseif ($scan->statut_aller === 'terminee' && $scan->statut_retour !== 'terminee') {
                                                    $typeScan = 'aller';
                                                    $badgeClass = 'badge-success';
                                                    $typeLabel = 'ALLER';
                                                    $icon = 'east';
                                                } elseif ($scan->statut_retour === 'terminee') {
                                                    // Si les deux sont terminés, on détermine par la date de scan
                                                    if ($scan->date_voyage && $scan->embarquement_scanned_at) {
                                                        $scanDate = \Carbon\Carbon::parse($scan->embarquement_scanned_at)->startOfDay();
                                                        $dateVoyage = \Carbon\Carbon::parse($scan->date_voyage)->startOfDay();
                                                        $dateRetour = $scan->date_retour ? \Carbon\Carbon::parse($scan->date_retour)->startOfDay() : null;
                                                        
                                                        if ($dateRetour && $scanDate->equalTo($dateRetour)) {
                                                            $typeScan = 'retour';
                                                            $badgeClass = 'badge-warning';
                                                            $typeLabel = 'RETOUR';
                                                            $icon = 'west';
                                                        } else {
                                                            $typeScan = 'aller';
                                                            $badgeClass = 'badge-success';
                                                            $typeLabel = 'ALLER';
                                                            $icon = 'east';
                                                        }
                                                    } else {
                                                        $typeScan = 'retour';
                                                        $badgeClass = 'badge-warning';
                                                        $typeLabel = 'RETOUR';
                                                        $icon = 'west';
                                                    }
                                                } else {
                                                    $typeScan = 'aller';
                                                    $badgeClass = 'badge-success';
                                                    $typeLabel = 'ALLER';
                                                    $icon = 'east';
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }} px-3 py-2">
                                                <i class="material-icons mr-1" style="font-size: 14px; vertical-align: middle;">{{ $icon }}</i>
                                                {{ $typeLabel }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary" style="font-size: 1rem; padding: 8px 12px;">
                                                {{ $scan->seat_number }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($scan->embarquement_scanned_at)
                                                <strong>{{ \Carbon\Carbon::parse($scan->embarquement_scanned_at)->format('H:i') }}</strong>
                                                <br>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($scan->embarquement_scanned_at)->format('d/m/Y') }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($scan->embarquementVehicule)
                                                <span class="badge badge-secondary">{{ $scan->embarquementVehicule->immatriculation }}</span>
                                                <br>
                                                <small class="text-muted">{{ $scan->embarquementVehicule->marque }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="material-icons text-muted" style="font-size: 64px;">history</i>
                                            <h5 class="mt-3 text-muted">Aucun scan trouvé</h5>
                                            <p class="text-muted">Modifiez les filtres ou sélectionnez une autre date</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($scans->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $scans->appends(request()->query())->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<style>
    .badge-info { background-color: #17a2b8; }
    .badge-success { background-color: #28a745; }
    .badge-warning { background-color: #ffc107; color: #212529; }
    .gap-2 > * { margin-left: 0.5rem; }
    .card.bg-primary, .card.bg-info, .card.bg-success, .card.bg-warning {
        border: none;
        border-radius: 10px;
    }
</style>

<script>
    function applyFilters() {
        var date = $('#filterDate').val();
        var type = $('#filterType').val();
        var trajet = $('#filterTrajet').val();
        
        var url = '{{ route("agent.reservations.historique") }}?';
        if (date) url += 'date=' + date + '&';
        if (type) url += 'type=' + type + '&';
        if (trajet) url += 'trajet=' + encodeURIComponent(trajet) + '&';
        
        window.location.href = url;
    }
    
    function resetFilters() {
        window.location.href = '{{ route("agent.reservations.historique") }}';
    }
    
    // Appliquer le filtre au changement de date
    $('#filterDate').change(function() {
        applyFilters();
    });
</script>
@endpush
