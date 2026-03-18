@extends('agent.layouts.template')

@section('content')
    <div class="container-fluid content-wrapper">
        <!-- Style personnalisé pour cette vue uniquement -->
        <style>
            :root {
                --primary-brand: #f97316;
                --primary-gradient: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
                --secondary-brand: #0f172a;
                --bg-soft: #f8fafc;
                --success-accent: #22c55e;
                --danger-accent: #ef4444;
                --info-accent: #3b82f6;
            }

            .content-wrapper {
                background-color: var(--bg-soft);
                min-height: calc(100vh - 64px);
                padding: 2rem;
            }

            /* Adaptations pour Tablettes (iPad, Samsung Galaxy Tab, etc) */
            @media (max-width: 1024px) {
                .content-wrapper {
                    padding: 1.5rem;
                }

                .d-flex.justify-content-between.align-items-center.mb-4.px-2 {
                    flex-direction: column;
                    text-align: center;
                    gap: 1.25rem;
                }

                .filter-section {
                    padding: 1.25rem;
                }

                .stat-card-premium .stat-value {
                    font-size: 1.5rem;
                }
            }

            /* Adaptations pour Mobiles et petites Tablettes */
            @media (max-width: 850px) {
                .page-title {
                    font-size: 1.5rem;
                    text-align: center;
                }

                .btn-premium-filter {
                    height: 50px;
                    padding: 0 1.5rem;
                    justify-content: center;
                }

                .btn-premium-reset {
                    height: 50px;
                    width: 50px;
                }

                .stat-card-premium {
                    padding: 1.25rem;
                    border-radius: 20px;
                }

                .table-premium thead {
                    display: none;
                }

                .table-premium tbody td {
                    display: block;
                    width: 100%;
                    text-align: right;
                    padding: 0.5rem 1rem !important;
                    border-bottom: none !important;
                    position: relative;
                }

                .table-premium tbody td:before {
                    content: attr(data-label);
                    position: absolute;
                    left: 1rem;
                    font-weight: 700;
                    color: #94a3b8;
                    text-transform: uppercase;
                    font-size: 0.7rem;
                }

                .table-premium tbody td:last-child {
                    border-bottom: 1px solid #f1f5f9 !important;
                    padding-bottom: 1.5rem !important;
                }

                .table-premium tbody tr {
                    display: block;
                    padding-top: 1rem;
                }
            }

            .page-title {
                font-family: 'Inter', sans-serif;
                font-weight: 800;
                color: var(--secondary-brand);
                font-size: 1.75rem;
                letter-spacing: -0.025em;
                margin-bottom: 0.5rem;
            }

            /* Cartes Statistiques Premium */
            .stat-card-premium {
                background: white;
                border-radius: 24px;
                border: 1px solid rgba(249, 115, 22, 0.05);
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
                padding: 1.5rem;
                position: relative;
                overflow: hidden;
                transition: all 0.3s ease;
                height: 100%;
            }

            .stat-card-premium:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.1);
            }

            .stat-icon-wrapper {
                width: 48px;
                height: 48px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 1rem;
            }

            .stat-value {
                font-size: 1.75rem;
                font-weight: 800;
                color: var(--secondary-brand);
                line-height: 1;
                margin-bottom: 0.25rem;
            }

            .stat-label {
                font-size: 0.75rem;
                font-weight: 600;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            /* Filtres modernisés */
            .filter-section {
                background: white;
                border-radius: 24px;
                padding: 1.75rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                border: 1px solid #e2e8f0;
                margin-bottom: 2rem;
            }

            .filter-row {
                display: flex;
                flex-wrap: wrap;
                gap: 1rem;
                align-items: flex-end;
            }

            .filter-field {
                display: flex;
                flex-direction: column;
                flex: 1 1 160px;
                min-width: 0;
            }

            .filter-field.field-date     { flex: 1 1 170px; max-width: 220px; }
            .filter-field.field-type     { flex: 1 1 180px; max-width: 250px; }
            .filter-field.field-trajet   { flex: 2 1 220px; }

            .filter-actions {
                display: flex;
                align-items: flex-end;
                gap: 0.6rem;
                flex-shrink: 0;
                margin-left: auto;
            }

            /* Tablette ≤ 991px */
            @media (max-width: 991px) {
                .filter-field.field-date   { flex: 1 1 calc(50% - 0.5rem); max-width: calc(50% - 0.5rem); }
                .filter-field.field-type   { flex: 1 1 calc(50% - 0.5rem); max-width: calc(50% - 0.5rem); }
                .filter-field.field-trajet { flex: 1 1 100%; }
                .filter-actions            { flex: 1 1 100%; margin-left: 0; justify-content: flex-end; }
            }

            /* Mobile ≤ 575px */
            @media (max-width: 575px) {
                .filter-field.field-date,
                .filter-field.field-type,
                .filter-field.field-trajet { flex: 1 1 100%; max-width: 100%; }
                .filter-actions            { flex: 1 1 100%; justify-content: center; }
                .btn-premium-filter        { flex: 1; justify-content: center; }
            }

            .filter-label {
                font-size: 0.7rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.1em;
                color: #64748b;
                margin-bottom: 0.5rem;
                display: block;
            }

            .form-control-premium {
                background: #f8fafc;
                border: 2px solid #f1f5f9;
                border-radius: 12px;
                padding: 0.65rem 1rem;
                font-weight: 600;
                color: var(--secondary-brand);
                transition: all 0.3s ease;
                min-height: 46px;
                line-height: 1.4;
            }

            .form-control-premium:focus {
                background: white;
                border-color: var(--primary-brand);
                box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1);
                outline: none;
            }

            .btn-premium-filter {
                background: var(--secondary-brand);
                color: white !important;
                border: none;
                padding: 0.65rem 1.5rem;
                border-radius: 12px;
                font-weight: 700;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                transition: all 0.3s ease;
                height: 46px;
            }

            .btn-premium-filter:hover {
                background: #1e293b;
                transform: translateY(-2px);
            }

            .btn-premium-reset {
                background: #f1f5f9;
                color: #64748b !important;
                border: none;
                width: 45px;
                height: 45px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
            }

            .btn-premium-reset:hover {
                background: #e2e8f0;
                color: var(--secondary-brand) !important;
            }

            /* Table Premium */
            .table-card-premium {
                background: white;
                border-radius: 24px;
                border: 1px solid #e2e8f0;
                overflow: hidden;
            }

            .table-premium thead th {
                background: #f8fafc;
                border-bottom: 2px solid #f1f5f9;
                color: #64748b;
                font-weight: 700;
                text-transform: uppercase;
                font-size: 0.75rem;
                padding: 1.25rem 1.5rem;
            }

            .table-premium tbody td {
                padding: 1.25rem 1.5rem;
                vertical-align: middle;
                color: var(--secondary-brand);
            }

            .ref-badge {
                background: #f1f5f9;
                color: var(--secondary-brand);
                font-family: 'JetBrains Mono', monospace;
                font-weight: 700;
                padding: 4px 10px;
                border-radius: 8px;
                font-size: 0.85rem;
            }

            .type-badge {
                padding: 6px 12px;
                border-radius: 8px;
                font-weight: 800;
                font-size: 0.7rem;
                text-transform: uppercase;
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
            }

            .type-aller-simple {
                background: rgba(59, 130, 246, 0.1);
                color: #2563eb;
            }

            .type-aller {
                background: rgba(34, 197, 94, 0.1);
                color: #16a34a;
            }

            .type-retour {
                background: rgba(245, 158, 11, 0.1);
                color: #d97706;
            }

            .seat-badge {
                background: var(--primary-gradient);
                color: white;
                font-weight: 800;
                padding: 6px 12px;
                border-radius: 10px;
                font-size: 0.9rem;
            }

            .immat-badge {
                background: var(--secondary-brand);
                color: white;
                font-family: 'JetBrains Mono', monospace;
                font-weight: 700;
                padding: 4px 10px;
                border-radius: 8px;
                font-size: 0.75rem;
            }

            .btn-action-header {
                background: white;
                color: var(--secondary-brand) !important;
                border: 1px solid #e2e8f0;
                padding: 0.5rem 1.25rem;
                border-radius: 12px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                transition: all 0.3s ease;
            }

            .btn-action-header:hover {
                background: #f8fafc;
                border-color: #cbd5e1;
                transform: translateY(-2px);
            }

            .btn-action-primary {
                background: var(--primary-gradient);
                color: white !important;
                border: none;
                box-shadow: 0 4px 6px rgba(249, 115, 22, 0.2);
            }

            .btn-action-primary:hover {
                box-shadow: 0 8px 15px rgba(249, 115, 22, 0.3);
            }
        </style>

        <div class="row justify-content-center">
            <div class="col-12">

                <!-- En-tête -->
                <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                    <div>
                        <h2 class="page-title">Historique des Scans</h2>
                        <p class="text-slate-500 mb-0">Consultez et filtrez tous les passagers ayant embarqué.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('agent.reservations.recherche') }}" class="btn-action-header">
                            <i class="material-icons">search</i>
                            Rechercher
                        </a>
                        <a href="{{ route('agent.reservations.index') }}" class="btn-action-header btn-action-primary">
                            <i class="material-icons">qr_code_scanner</i>
                            Nouveau Scan
                        </a>
                    </div>
                </div>

                <!-- Section Statistiques -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-3 col-sm-6">
                        <div class="stat-card-premium">
                            <div class="stat-icon-wrapper bg-slate-100 text-slate-600">
                                <i class="material-icons">history</i>
                            </div>
                            <div class="stat-value">{{ $stats['total'] }}</div>
                            <div class="stat-label">Total scannés</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="stat-card-premium">
                            <div class="stat-icon-wrapper bg-blue-50 text-blue-500">
                                <i class="material-icons">directions_bus</i>
                            </div>
                            <div class="stat-value">{{ $stats['aller_simple'] }}</div>
                            <div class="stat-label">Aller Simple</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="stat-card-premium">
                            <div class="stat-icon-wrapper bg-green-50 text-green-500">
                                <i class="material-icons">east</i>
                            </div>
                            <div class="stat-value">{{ $stats['aller'] }}</div>
                            <div class="stat-label">Aller (AR)</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="stat-card-premium">
                            <div class="stat-icon-wrapper bg-amber-50 text-amber-500">
                                <i class="material-icons">west</i>
                            </div>
                            <div class="stat-value">{{ $stats['retour'] }}</div>
                            <div class="stat-label">Retour (AR)</div>
                        </div>
                    </div>
                </div>

                <!-- Section Filtres -->
                <div class="filter-section">
                    <div class="filter-row">

                        <!-- Champ Date -->
                        <div class="filter-field field-date">
                            <label class="filter-label">Date du voyage</label>
                            <input type="date" id="filterDate" class="form-control form-control-premium"
                                value="{{ request('date', now()->format('Y-m-d')) }}">
                        </div>

                        <!-- Champ Type de billet -->
                        <div class="filter-field field-type">
                            <label class="filter-label">Type de billet</label>
                            <select id="filterType" class="form-select form-control-premium">
                                <option value="">Tous les types</option>
                                <option value="aller_simple" {{ request('type') == 'aller_simple' ? 'selected' : '' }}>Aller Simple</option>
                                <option value="aller" {{ request('type') == 'aller' ? 'selected' : '' }}>Aller (Aller-Retour)</option>
                                <option value="retour" {{ request('type') == 'retour' ? 'selected' : '' }}>Retour (Aller-Retour)</option>
                            </select>
                        </div>

                        <!-- Champ Trajet -->
                        <div class="filter-field field-trajet">
                            <label class="filter-label">Trajet spécifique</label>
                            <select id="filterTrajet" class="form-select form-control-premium text-uppercase">
                                <option value="">Tous les trajets</option>
                                @foreach ($trajets as $trajet)
                                    <option value="{{ $trajet }}" {{ request('trajet') == $trajet ? 'selected' : '' }}>{{ $trajet }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="filter-actions">
                            <button class="btn btn-premium-filter" onclick="applyFilters()">
                                <i class="material-icons">filter_list</i>
                                Appliquer
                            </button>
                            <button class="btn btn-premium-reset" onclick="resetFilters()" title="Réinitialiser">
                                <i class="material-icons">refresh</i>
                            </button>
                        </div>

                    </div>
                </div>

                <!-- Tableau Historique -->
                <div class="table-card-premium">
                    <div class="table-responsive">
                        <table class="table table-hover table-premium m-0">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Passager</th>
                                    <th>Trajet / Parcours</th>
                                    <th>Type</th>
                                    <th>Place</th>
                                    <th>Embarquement</th>
                                    <th>Bus</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($scans as $scan)
                                    <tr>
                                        <td data-label="Référence"><span class="ref-badge">{{ $scan->reference }}</span>
                                        </td>
                                        <td data-label="Passager">
                                            <div class="d-flex flex-column">
                                                <span class="font-bold">{{ $scan->passager_prenom }}
                                                    {{ $scan->passager_nom }}</span>
                                                <span class="text-xs text-slate-400">{{ $scan->passager_telephone }}</span>
                                            </div>
                                        </td>
                                        <td data-label="Trajet / Parcours">
                                            @if ($scan->programme)
                                                <div class="d-flex flex-column text-uppercase font-bold text-sm">
                                                    <span>{{ $scan->programme->point_depart }} →
                                                        {{ $scan->programme->point_arrive }}</span>
                                                    <span class="text-xs text-slate-400 font-normal">Gare:
                                                        {{ $scan->programme->gareDepart->nom_gare ?? 'N/A' }}</span>
                                                </div>
                                            @else
                                                <span class="text-slate-400">Voyage supprimé</span>
                                            @endif
                                        </td>
                                        <td data-label="Type">
                                            @php
                                                if (!$scan->is_aller_retour) {
                                                    $typeClass = 'type-aller-simple';
                                                    $typeLabel = 'Aller Simple';
                                                    $icon = 'arrow_forward';
                                                } elseif (
                                                    $scan->statut_aller === 'terminee' &&
                                                    $scan->statut_retour !== 'terminee'
                                                ) {
                                                    $typeClass = 'type-aller';
                                                    $typeLabel = 'ALLER';
                                                    $icon = 'east';
                                                } elseif ($scan->statut_retour === 'terminee') {
                                                    if ($scan->date_voyage && $scan->embarquement_scanned_at) {
                                                        $scanDate = \Carbon\Carbon::parse(
                                                            $scan->embarquement_scanned_at,
                                                        )->startOfDay();
                                                        $dateRetour = $scan->date_retour
                                                            ? \Carbon\Carbon::parse($scan->date_retour)->startOfDay()
                                                            : null;
                                                        if ($dateRetour && $scanDate->equalTo($dateRetour)) {
                                                            $typeClass = 'type-retour';
                                                            $typeLabel = 'RETOUR';
                                                            $icon = 'west';
                                                        } else {
                                                            $typeClass = 'type-aller';
                                                            $typeLabel = 'ALLER';
                                                            $icon = 'east';
                                                        }
                                                    } else {
                                                        $typeClass = 'type-retour';
                                                        $typeLabel = 'RETOUR';
                                                        $icon = 'west';
                                                    }
                                                } else {
                                                    $typeClass = 'type-aller';
                                                    $typeLabel = 'ALLER';
                                                    $icon = 'east';
                                                }
                                            @endphp
                                            <span class="type-badge {{ $typeClass }}">
                                                <i class="material-icons" style="font-size: 14px;">{{ $icon }}</i>
                                                {{ $typeLabel }}
                                            </span>
                                        </td>
                                        <td data-label="Place"><span class="seat-badge">{{ $scan->seat_number }}</span>
                                        </td>
                                        <td data-label="Embarquement">
                                            @if ($scan->embarquement_scanned_at)
                                                <div class="d-flex flex-column">
                                                    <span
                                                        class="font-bold">{{ \Carbon\Carbon::parse($scan->embarquement_scanned_at)->format('H:i') }}</span>
                                                    <span
                                                        class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($scan->embarquement_scanned_at)->format('d/m/y') }}</span>
                                                </div>
                                            @else
                                                <span class="text-slate-300">-</span>
                                            @endif
                                        </td>
                                        <td data-label="Bus">
                                            @if ($scan->embarquementVehicule)
                                                <div class="d-flex flex-column align-items-center">
                                                    <span
                                                        class="immat-badge mb-1">{{ $scan->embarquementVehicule->immatriculation }}</span>
                                                    <span
                                                        class="text-[10px] text-slate-400 uppercase font-bold">{{ $scan->embarquementVehicule->marque }}</span>
                                                </div>
                                            @else
                                                <span class="text-slate-300">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div
                                                class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-4">
                                                <i class="material-icons" style="font-size: 32px;">history_toggle_off</i>
                                            </div>
                                            <h5 class="font-bold text-slate-900">Aucun scan enregistré</h5>
                                            <p class="text-slate-500 mb-0">Aucun embarquement ne correspond à vos critères.
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Premium -->
                    @if ($scans->hasPages())
                        <div class="p-4 border-t border-slate-100 bg-slate-50 d-flex justify-content-center">
                            {{ $scans->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function applyFilters() {
            var date = $('#filterDate').val();
            var type = $('#filterType').val();
            var trajet = $('#filterTrajet').val();

            var url = '{{ route('agent.reservations.historique') }}?';
            var params = [];
            if (date) params.push('date=' + date);
            if (type) params.push('type=' + type);
            if (trajet) params.push('trajet=' + encodeURIComponent(trajet));

            window.location.href = url + params.join('&');
        }

        function resetFilters() {
            window.location.href = '{{ route('agent.reservations.historique') }}';
        }

        // Filtre automatique au changement de date
        $('#filterDate').change(function() {
            applyFilters();
        });
    </script>
@endpush
