@extends('compagnie.layouts.template')

@section('page-title', 'Détails des Réservations')
@section('page-subtitle', 'Suivi approfondi de la billetterie et des flux passagers')

@section('content')
<div class="dashboard-page">

    {{-- ── STATS ROW ── --}}
    <div class="metric-grid mb-4">
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-amber"><i class="fas fa-ticket-alt"></i></div>
            </div>
            <div class="metric-label">Tickets Restants</div>
            <div class="metric-value">{{ number_format($stockTickets, 0, ',', ' ') }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-blue"><i class="fas fa-shopping-cart"></i></div>
            </div>
            <div class="metric-label">Tickets Écoulés</div>
            <div class="metric-value">{{ number_format($ticketsConsommes, 0, ',', ' ') }}</div>
        </div>
        <div class="metric-card metric-featured">
            <div class="metric-top">
                <div class="metric-icon mi-white"><i class="fas fa-wallet"></i></div>
            </div>
            <div class="metric-label">Chiffre d'Affaires</div>
            <div class="metric-value">{{ number_format($revenuTotal, 0, ',', ' ') }} <span class="metric-unit">F</span></div>
        </div>
    </div>

    {{-- ── FILTERS CARD ── --}}
    <div class="dash-card mb-4 mt-4 print-hide">
        <div class="dash-card-head">
            <div class="dash-card-head-left">
                <div class="filter-indicator"></div>
                <h3 class="dash-card-title">Filtres Dynamiques</h3>
            </div>
            <div class="action-buttons">
                <a href="{{ route('company.reservation.index') }}" class="btn-action btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
                <button onclick="window.print()" class="btn-action btn-primary">
                    <i class="fas fa-print"></i> Imprimer
                </button>
            </div>
        </div>
        
        <div class="filter-body">
            <form action="{{ route('company.reservation.details') }}" method="GET">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Recherche</label>
                        <div class="input-with-icon">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Réf, Nom, Tél...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Début</label>
                        <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fin</label>
                        <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Programme</label>
                        <select name="programme_id" class="form-control">
                            <option value="all">Tous trajets</option>
                            @foreach($programmes as $prog)
                                <option value="{{ $prog->id }}" {{ request('programme_id') == $prog->id ? 'selected' : '' }}>
                                    {{ $prog->point_depart }} → {{ $prog->point_arrive }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group form-actions-group">
                        <button type="submit" class="btn-apply">Appliquer</button>
                        <a href="{{ route('company.reservation.details') }}" class="btn-reset" title="Réinitialiser">
                            <i class="fas fa-redo-alt"></i>
                        </a>
                    </div>
                </div>

                <div class="filter-options-row">
                    <div class="filter-option-group">
                        <span class="option-label">Statut :</span>
                        <div class="pills-container">
                            @foreach(['all' => 'Tous', 'confirmee' => 'Confirmée', 'en_attente' => 'Attente', 'terminee' => 'Terminée', 'annulee' => 'Annulée'] as $val => $label)
                                <a href="{{ request()->fullUrlWithQuery(['statut' => $val]) }}" 
                                   class="filter-pill {{ (request('statut', 'all') == $val) ? 'active-orange' : '' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="filter-option-group">
                        <span class="option-label">Vente :</span>
                        <div class="pills-container">
                            @foreach(['all' => 'Tous', 'ligne' => 'En Ligne', 'caisse' => 'Caisse'] as $val => $label)
                                <a href="{{ request()->fullUrlWithQuery(['type_vente' => $val]) }}" 
                                   class="filter-pill {{ (request('type_vente', 'all') == $val) ? 'active-dark' : '' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <input type="hidden" name="statut" value="{{ request('statut', 'all') }}">
                <input type="hidden" name="type_vente" value="{{ request('type_vente', 'all') }}">
            </form>
        </div>
    </div>

    {{-- ── TABLE CARD ── --}}
    <div class="dash-card">
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Réf & Date</th>
                        <th>Passager</th>
                        <th>Trajet & Détails</th>
                        <th class="text-center">Place</th>
                        <th class="text-right">Montant</th>
                        <th class="text-center">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $reservation)
                    <tr>
                        <td>
                            <div class="cell-stack">
                                <span class="text-ref">{{ $reservation->reference }}</span>
                                <span class="text-date">{{ \Carbon\Carbon::parse($reservation->created_at)->format('d/m/Y H:i') }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="td-user">
                                <div class="td-avatar {{ $reservation->caisse_id || $reservation->hotesse_id ? 'text-blue' : 'text-orange' }}">
                                    {{ substr($reservation->passager_nom, 0, 1) }}
                                </div>
                                <div class="cell-stack">
                                    <span class="td-name">{{ $reservation->passager_prenom }} {{ $reservation->passager_nom }}</span>
                                    <div class="user-meta">
                                        <span class="td-phone">{{ $reservation->passager_telephone }}</span>
                                        @if($reservation->hotesse_id)
                                            <span class="tag-role tag-purple">Hôtesse</span>
                                        @elseif($reservation->caisse_id)
                                            <span class="tag-role tag-blue">Caisse</span>
                                        @else
                                            <span class="tag-role tag-green">Ligne</span>
                                        @endif
                                    </div>
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
                                    <span class="text-time mt-1">
                                        Du {{ \Carbon\Carbon::parse($reservation->date_voyage)->format('d/m/Y') }} à {{ substr($reservation->programme->heure_depart, 0, 5) }}
                                    </span>
                                </div>
                            @else
                                <span class="status-pill sp-danger">Programme supprimé</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button type="button" 
                                    onclick="showOccupiedSeats({{ $reservation->programme_id }}, '{{ $reservation->date_voyage->format('Y-m-d') }}', {{ $reservation->seat_number }})"
                                    class="seat-btn">
                                {{ $reservation->seat_number }}
                            </button>
                        </td>
                        <td class="text-right">
                            <div class="cell-stack align-end">
                                <span class="td-amount">{{ number_format($reservation->montant, 0, ',', ' ') }} F</span>
                                <span class="text-time">{{ $reservation->paiement ? $reservation->paiement->payment_method : '---' }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            @php
                                $statusMap = [
                                    'confirmee' => ['class' => 'sp-success', 'label' => 'Confirmée'],
                                    'en_attente' => ['class' => 'sp-warning', 'label' => 'Attente'],
                                    'terminee' => ['class' => 'sp-gray', 'label' => 'Terminée'],
                                    'annulee' => ['class' => 'sp-danger', 'label' => 'Annulée']
                                ];
                                $st = $statusMap[$reservation->statut] ?? ['class' => 'sp-gray', 'label' => $reservation->statut];
                            @endphp
                            <span class="status-pill {{ $st['class'] }}"><span class="dot"></span> {{ $st['label'] }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="table-empty">
                                <i class="fas fa-filter table-empty-icon"></i>
                                <p>Aucune réservation pour ces filtres</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="dash-card-footer print-hide">
            <div class="pagination-info">
                Affichage de {{ $reservations->count() }} sur {{ $reservations->total() }} réservations
            </div>
            <div class="pagination-wrapper">
                {{ $reservations->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<style>
    /* Headers & Actions */
    .filter-indicator { width: 4px; height: 18px; background: var(--orange); border-radius: 4px; }
    .action-buttons { display: flex; gap: 10px; }
    .btn-action { display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; border: none; text-decoration: none; }
    .btn-primary { background: var(--orange); color: #fff; }
    .btn-primary:hover { background: var(--orange-dark); color: #fff; text-decoration: none; }
    .btn-secondary { background: var(--surface-2); color: var(--text-1); border: 1px solid var(--border); }
    .btn-secondary:hover { background: var(--border-strong); color: var(--text-1); text-decoration: none; }

    /* Forms */
    .filter-body { padding: 20px; }
    .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; align-items: flex-end; margin-bottom: 20px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-label { font-size: 10px; font-weight: 800; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.5px; margin: 0; }
    .form-control { width: 100%; background: var(--surface-2); border: 1px solid var(--border); border-radius: 10px; padding: 10px 14px; font-size: 13px; font-weight: 600; color: var(--text-1); transition: all 0.2s; outline: none; }
    .form-control:focus { background: var(--surface); border-color: var(--orange); box-shadow: 0 0 0 3px var(--orange-light); }
    
    .input-with-icon { position: relative; }
    .input-with-icon i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-3); }
    .input-with-icon .form-control { padding-left: 36px; }

    .form-actions-group { flex-direction: row; gap: 8px; }
    .btn-apply { flex: 1; background: var(--orange); color: #fff; border: none; border-radius: 10px; height: 42px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; cursor: pointer; transition: background 0.2s; }
    .btn-apply:hover { background: var(--orange-dark); }
    .btn-reset { width: 42px; height: 42px; background: var(--surface-2); color: var(--text-3); border-radius: 10px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.2s; border: 1px solid var(--border); }
    .btn-reset:hover { background: var(--border); color: var(--text-1); }

    /* Filter Options (Pills) */
    .filter-options-row { display: flex; flex-wrap: wrap; gap: 24px; padding-top: 20px; border-top: 1px solid var(--border); }
    .filter-option-group { display: flex; align-items: center; gap: 12px; }
    .option-label { font-size: 10px; font-weight: 800; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.5px; }
    .pills-container { display: flex; flex-wrap: wrap; gap: 6px; }
    .filter-pill { padding: 6px 12px; background: var(--surface-2); color: var(--text-3); font-size: 10px; font-weight: 800; text-transform: uppercase; border-radius: 8px; text-decoration: none; transition: all 0.2s; }
    .filter-pill:hover { background: var(--border); color: var(--text-2); text-decoration: none; }
    .filter-pill.active-orange { background: var(--orange); color: #fff; }
    .filter-pill.active-dark { background: var(--orange); color: #fff; }

    /* Table Utils */
    .cell-stack { display: flex; flex-direction: column; gap: 4px; }
    .align-end { align-items: flex-end; }
    .text-ref { font-size: 11px; font-weight: 800; color: var(--orange-dark); text-transform: uppercase; letter-spacing: 0.5px; }
    .text-date { font-size: 12px; font-weight: 600; color: var(--text-3); }
    .text-time { font-size: 11px; font-weight: 700; color: var(--text-3); text-transform: uppercase; }
    .td-avatar.text-blue { background: #EFF6FF; color: #2563EB; }
    .td-avatar.text-orange { background: var(--orange-light); color: var(--orange); }
    .user-meta { display: flex; align-items: center; gap: 10px; }
    
    .tag-role { padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: 800; text-transform: uppercase; }
    .tag-purple { background: #F3E8FF; color: #9333EA; }
    .tag-blue { background: #EFF6FF; color: #2563EB; }
    .tag-green { background: #ECFDF5; color: #059669; }

    /* Badge & Status */
    .seat-btn { width: 34px; height: 34px; border-radius: 10px; background: var(--surface-2); border: 1px solid var(--border); font-size: 13px; font-weight: 800; color: var(--text-2); cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; }
    .seat-btn:hover { background: var(--orange); color: #fff; border-color: var(--orange); }
    
    .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
    .sp-success { background: #ECFDF5; color: #059669; }
    .sp-warning { background: #FFFBEB; color: #D97706; }
    .sp-danger { background: #FEF2F2; color: #DC2626; }
    .sp-gray { background: var(--surface-2); color: var(--text-2); }
    .status-pill .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    /* Footer Pagination */
    .dash-card-footer { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-top: 1px solid var(--border); }
    .pagination-info { font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-3); letter-spacing: 0.5px; }

    /* Modal Seats Custom CSS */
    .sa-modal-title { font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--text-3); letter-spacing: 1px; display: block; margin-bottom: 5px; }
    .sa-modal-vehicle { font-size: 16px; font-weight: 800; color: var(--text-1); }
    .sa-legend { display: flex; justify-content: center; gap: 15px; margin: 15px 0 20px; }
    .sa-legend-item { font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--text-3); display: flex; align-items: center; gap: 6px; }
    .sa-seat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; max-height: 350px; overflow-y: auto; padding: 10px; }
    .sa-seat { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px 0; border-radius: 12px; border: 1px solid transparent; }
    .sa-seat i { font-size: 14px; margin-bottom: 4px; }
    .sa-seat span { font-size: 11px; font-weight: 800; }
    .sa-seat-free { background: #ECFDF5; color: #059669; border-color: #D1FAE5; }
    .sa-seat-occ { background: #FEF2F2; color: #DC2626; }
    .sa-seat-cur { background: #EFF6FF; color: #2563EB; border-color: #BFDBFE; box-shadow: 0 4px 10px rgba(37,99,235,0.15); }

    @media print {
        .print-hide { display: none !important; }
        .dashboard-page { padding: 0 !important; background: #fff !important; }
        .dash-card { box-shadow: none !important; border: 1px solid #ddd !important; }
    }
</style>
@endsection

@section('scripts')
<script>
    function showOccupiedSeats(programmeId, dateVoyage, currentSeat) {
        Swal.fire({
            title: 'Chargement du plan...',
            didOpen: () => { Swal.showLoading(); }
        });

        fetch(`{{ route('company.reservation.occupied-seats') }}?programme_id=${programmeId}&date_voyage=${dateVoyage}`)
            .then(response => response.json())
            .then(data => {
                if(data.error) throw new Error(data.error);

                let seatsHtml = '<div class="sa-seat-grid">';
                const totalSeats = data.total_seats;
                const occupied = data.occupied; 
                
                for (let i = 1; i <= totalSeats; i++) {
                    let seatClass = 'sa-seat-free';
                    if (i == currentSeat) {
                        seatClass = 'sa-seat-cur';
                    } else if (occupied.includes(i)) {
                        seatClass = 'sa-seat-occ';
                    }

                    seatsHtml += `
                        <div class="sa-seat ${seatClass}">
                            <i class="fas fa-chair"></i>
                            <span>${i}</span>
                        </div>
                    `;
                }
                seatsHtml += '</div>';

                Swal.fire({
                    width: 400,
                    html: `
                        <span class="sa-modal-title">Configuration Flotte</span>
                        <div class="sa-modal-vehicle">${data.vehicle_name}</div>
                        <div class="sa-legend">
                            <span class="sa-legend-item"><span class="dot" style="background:#DC2626; width:8px; height:8px; border-radius:50%;"></span> Occupé</span>
                            <span class="sa-legend-item"><span class="dot" style="background:#059669; width:8px; height:8px; border-radius:50%;"></span> Libre</span>
                            <span class="sa-legend-item"><span class="dot" style="background:#2563EB; width:8px; height:8px; border-radius:50%;"></span> Actuel</span>
                        </div>
                        ${seatsHtml}
                    `,
                    confirmButtonText: 'Fermer',
                    confirmButtonColor: 'var(--orange)',
                    customClass: { popup: 'rounded-[20px] shadow-lg' }
                });
            })
            .catch(error => {
                Swal.fire({ icon: 'error', title: 'Erreur', text: "Impossible de charger le plan.", confirmButtonColor: 'var(--orange)' });
            });
    }
</script>
@endsection