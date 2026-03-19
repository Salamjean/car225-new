@extends('compagnie.layouts.template')

@section('page-title', 'Gestion des Réservations')
@section('page-subtitle', 'Suivez et gérez les réservations de vos voyages')

@section('content')
<div class="dashboard-page">

    {{-- ── STATS ROW ── --}}
    <div class="metric-grid mb-4">
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-amber"><i class="fas fa-ticket-alt"></i></div>
            </div>
            <div class="metric-label">Actives</div>
            <div class="metric-value">{{ number_format($reservationsEnCours->total(), 0, ',', ' ') }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-blue"><i class="fas fa-history"></i></div>
            </div>
            <div class="metric-label">Historique</div>
            <div class="metric-value">{{ number_format($reservationsTerminees->total(), 0, ',', ' ') }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-green"><i class="fas fa-wallet"></i></div>
            </div>
            <div class="metric-label">Chiffre d'Affaires</div>
            <div class="metric-value">{{ number_format($reservationsEnCours->sum('montant'), 0, ',', ' ') }} <span class="metric-unit">F</span></div>
        </div>
    </div>

    {{-- ── ACTIONS HEADER ── --}}
    <div class="dash-card mb-4 mt-4">
        <div class="dash-header-actions">
            <div class="tab-modern">
                <a href="?tab=en-cours" class="tab-item {{ request('tab') == 'en-cours' || !request('tab') ? 'active' : '' }}">
                    <i class="fas fa-clock"></i> En Cours
                </a>
                <a href="?tab=terminees" class="tab-item {{ request('tab') == 'terminees' ? 'active' : '' }}">
                    <i class="fas fa-check-double"></i> Passées
                </a>
            </div>
            <div class="action-buttons">
                <a href="{{ route('company.reservation.details') }}" class="btn-action btn-secondary">
                    <i class="fas fa-chart-line"></i> Analyses
                </a>
                <button onclick="window.location.reload();" class="btn-action btn-primary">
                    <i class="fas fa-sync-alt"></i> Actualiser
                </button>
            </div>
        </div>
    </div>

    {{-- ── TABLE CARD ── --}}
    <div class="dash-card">
        <div class="dash-table-wrap">
            <table class="dash-table">
                @if(request('tab') == 'terminees')
                    <thead>
                        <tr>
                            <th>Réf & Date</th>
                            <th>Passager</th>
                            <th>Trajet</th>
                            <th class="text-center">Place</th>
                            <th class="text-right">Montant</th>
                            <th class="text-center">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reservationsTerminees as $reservation)
                        <tr>
                            <td>
                                <div class="cell-stack">
                                    <span class="text-ref">{{ $reservation->reference }}</span>
                                    <span class="text-date">{{ \Carbon\Carbon::parse($reservation->date_voyage)->format('d/m/Y') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="td-user">
                                    <div class="td-avatar text-blue">
                                        {{ substr($reservation->passager_nom, 0, 1) }}
                                    </div>
                                    <div class="cell-stack">
                                        <span class="td-name">{{ $reservation->passager_prenom }} {{ $reservation->passager_nom }}</span>
                                        <span class="td-phone">{{ $reservation->passager_telephone ?? '---' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($reservation->programme)
                                    <div class="cell-stack">
                                        <div class="route-pill">
                                            {{ $reservation->programme->point_depart }}
                                            <i class="fas fa-arrow-right route-arrow"></i>
                                            {{ $reservation->programme->point_arrive }}
                                        </div>
                                        <span class="text-time mt-1">{{ $reservation->programme->heure_depart }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">---</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="seat-badge">{{ $reservation->seat_number }}</span>
                            </td>
                            <td class="text-right">
                                <span class="td-amount">{{ number_format($reservation->montant, 0, ',', ' ') }} F</span>
                            </td>
                            <td class="text-center">
                                @if($reservation->statut == 'terminee')
                                    <span class="status-pill sp-success"><span class="dot"></span> Scannée</span>
                                @elseif($reservation->statut == 'annulee')
                                    <span class="status-pill sp-danger"><span class="dot"></span> Annulée</span>
                                @else
                                    <span class="status-pill sp-gray"><span class="dot"></span> Passée</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="table-empty">
                                    <i class="fas fa-history table-empty-icon"></i>
                                    <p>Aucun historique de réservation</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                @else
                    <thead>
                        <tr>
                            <th>Passager & Réf</th>
                            <th>Trajet & Heure</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Place</th>
                            <th class="text-right">Montant</th>
                            <th class="text-center">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reservationsEnCours as $reservation)
                        <tr>
                            <td>
                                <div class="td-user">
                                    <div class="td-avatar text-orange">
                                        {{ substr($reservation->passager_nom, 0, 1) }}
                                    </div>
                                    <div class="cell-stack">
                                        <span class="td-name">{{ $reservation->passager_prenom }} {{ $reservation->passager_nom }}</span>
                                        <div class="user-meta">
                                            <span class="td-phone">{{ $reservation->passager_telephone ?? '---' }}</span>
                                            <span class="text-ref">{{ $reservation->reference }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($reservation->programme)
                                    <div class="cell-stack">
                                        <div class="route-pill">
                                            {{ $reservation->programme->point_depart }}
                                            <i class="fas fa-chevron-right route-arrow"></i>
                                            {{ $reservation->programme->point_arrive }}
                                        </div>
                                        <span class="text-time mt-1">{{ $reservation->programme->heure_depart }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">Trajet inconnu</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="cell-stack text-center">
                                    <span class="date-day">{{ \Carbon\Carbon::parse($reservation->date_voyage)->format('d M') }}</span>
                                    <span class="date-year">{{ \Carbon\Carbon::parse($reservation->date_voyage)->format('Y') }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="seat-badge seat-badge-orange">{{ $reservation->seat_number }}</span>
                            </td>
                            <td class="text-right">
                                <span class="td-amount">{{ number_format($reservation->montant, 0, ',', ' ') }} F</span>
                            </td>
                            <td class="text-center">
                                @php
                                    $st = match($reservation->statut) {
                                        'confirmee' => ['class' => 'sp-success', 'label' => 'Confirmée'],
                                        'en_attente' => ['class' => 'sp-warning', 'label' => 'Attente'],
                                        default => ['class' => 'sp-blue', 'label' => ucfirst($reservation->statut)]
                                    };
                                @endphp
                                <span class="status-pill {{ $st['class'] }}"><span class="dot"></span> {{ $st['label'] }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="table-empty">
                                    <i class="fas fa-ticket-alt table-empty-icon"></i>
                                    <p>Aucune réservation active</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                @endif
            </table>
        </div>

        <div class="dash-card-footer">
            <div class="pagination-info">
                Page {{ (request('tab') == 'terminees' ? $reservationsTerminees->currentPage() : $reservationsEnCours->currentPage()) }} sur {{ (request('tab') == 'terminees' ? $reservationsTerminees->lastPage() : $reservationsEnCours->lastPage()) }}
            </div>
            <div class="pagination-wrapper">
                @if(request('tab') == 'terminees')
                    {{ $reservationsTerminees->appends(['tab' => 'terminees'])->links('pagination::bootstrap-4') }}
                @else
                    {{ $reservationsEnCours->appends(['tab' => 'en-cours'])->links('pagination::bootstrap-4') }}
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    /* Headers & Actions */
    .dash-header-actions { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; padding: 16px 20px; }
    .action-buttons { display: flex; gap: 10px; }
    .btn-action { display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; border: none; text-decoration: none; }
    .btn-primary { background: var(--orange); color: #fff; }
    .btn-primary:hover { background: var(--orange-dark); color: #fff; text-decoration: none; }
    .btn-secondary { background: var(--surface-2); color: var(--text-1); border: 1px solid var(--border); }
    .btn-secondary:hover { background: var(--border-strong); color: var(--text-1); text-decoration: none; }

    /* Tabs */
    .tab-modern { display: flex; background: var(--surface-2); padding: 5px; border-radius: 12px; border: 1px solid var(--border); }
    .tab-item { padding: 8px 20px; border-radius: 8px; font-size: 12px; font-weight: 800; color: var(--text-3); text-decoration: none; display: flex; align-items: center; gap: 8px; transition: all 0.2s; text-transform: uppercase; letter-spacing: 0.5px; }
    .tab-item:hover { text-decoration: none; color: var(--text-2); }
    .tab-item.active { background: var(--surface); color: var(--orange); box-shadow: 0 2px 6px rgba(0,0,0,0.05); }

    /* Table Utils */
    .cell-stack { display: flex; flex-direction: column; gap: 4px; }
    .text-ref { font-size: 11px; font-weight: 800; color: var(--orange-dark); text-transform: uppercase; letter-spacing: 0.5px; }
    .text-date { font-size: 12px; font-weight: 600; color: var(--text-3); }
    .text-time { font-size: 11px; font-weight: 700; color: var(--text-3); text-transform: uppercase; }
    .text-muted { font-size: 12px; font-style: italic; color: var(--text-3); }
    .td-avatar.text-blue { background: #EFF6FF; color: #2563EB; }
    .td-avatar.text-orange { background: var(--orange-light); color: var(--orange); }
    .user-meta { display: flex; align-items: center; gap: 10px; }
    
    .date-day { font-size: 13px; font-weight: 800; color: var(--text-1); text-transform: uppercase; }
    .date-year { font-size: 11px; font-weight: 700; color: var(--text-3); }

    /* Badges */
    .seat-badge { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 10px; background: var(--surface-2); border: 1px solid var(--border); font-size: 13px; font-weight: 800; color: var(--text-2); }
    .seat-badge-orange { background: var(--orange-light); border-color: var(--orange-mid); color: var(--orange-dark); }
    
    .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
    .sp-success { background: #ECFDF5; color: #059669; }
    .sp-warning { background: #FFFBEB; color: #D97706; }
    .sp-danger { background: #FEF2F2; color: #DC2626; }
    .sp-blue { background: #EFF6FF; color: #2563EB; }
    .sp-gray { background: var(--surface-2); color: var(--text-2); }
    .status-pill .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    /* Footer Pagination */
    .dash-card-footer { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-top: 1px solid var(--border); }
    .pagination-info { font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-3); letter-spacing: 0.5px; }
    .pagination-wrapper svg { max-width: 20px; } /* Fixe un bug courant sans Tailwind */
    .pagination-wrapper p { margin: 0; }
</style>
@endsection