@extends('compagnie.layouts.template')
@php \Carbon\Carbon::setLocale('fr'); @endphp

@section('page-title', isset($isFullMonth) && $isFullMonth ? 'Réservations de ' . $monthLabel : 'Réservations du ' . \Carbon\Carbon::parse($date)->translatedFormat('d F Y'))
@section('page-subtitle', isset($isFullMonth) && $isFullMonth ? 'Toutes les réservations du mois' : ($heure && $heure !== 'all' ? 'Départ de ' . $heure : 'Toutes les heures de la journée'))

@section('styles')
<style>
    /* Date Header Card */
    .detail-date-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
        background: #fff;
        padding: 20px;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.04);
    }
    .new-date-card {
        background: #fff;
        border-radius: 12px;
        padding: 5px;
        width: 75px;
        height: 75px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        flex-shrink: 0;
    }
    .card-month {
        color: #FF5A1F !important;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        line-height: 1;
        margin-bottom: 2px;
    }
    .card-day {
        color: #001A41 !important;
        font-size: 28px;
        font-weight: 900;
        line-height: 0.9;
    }
    .text-dark-blue { color: #001A41 !important; font-weight: 800; }

    /* Table Utils */
    .cell-stack { display: flex; flex-direction: column; gap: 4px; }
    .text-ref { font-size: 11px; font-weight: 800; color: #EA580C; text-transform: uppercase; letter-spacing: 0.5px; }
    .text-time { font-size: 11px; font-weight: 700; color: #6B6560; text-transform: uppercase; }
    .td-avatar.text-orange { background: #FFF7ED; color: #F97316; }
    .user-meta { display: flex; align-items: center; gap: 10px; }
    
    .seat-badge { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 10px; background: #F5F4F1; border: 1px solid rgba(0,0,0,0.07); font-size: 13px; font-weight: 800; color: #6B6560; }
    .seat-badge-orange { background: #FFF7ED; border-color: #FED7AA; color: #EA580C; }
    
    .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
    .sp-success { background: #ECFDF5; color: #059669; }
    .status-pill .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        background: #F5F4F1;
        color: #1A1714;
        border: 1px solid rgba(0,0,0,0.07);
        text-decoration: none !important;
        transition: all 0.2s;
    }
    .btn-back:hover { background: rgba(0,0,0,0.13); }
    
    .time-badge {
        background: #FFF7ED;
        color: #F97316;
        padding: 4px 10px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 14px;
        border: 1px solid #FED7AA;
    }

    .dashboard-page {
        position: relative;
        min-height: 80vh;
        z-index: 1;
        border-radius: 30px;
        padding: 30px;
        background: #F8F9FB;
        box-shadow: inset 0 0 40px rgba(0,0,0,0.01);
    }
</style>
@endsection

@section('content')
<div class="dashboard-page">

    <div class="mb-4 d-flex justify-content-between align-items-center">
        <a href="{{ route('company.reservation.index', ['tab' => $tab ?? 'en-cours']) }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Retour au calendrier
        </a>
        
        @if(isset($isFullMonth) && $isFullMonth)
            <div class="d-flex align-items-center gap-2">
                <div class="input-group" style="width: 280px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0;">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-0 text-orange">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
                    <input type="text" id="dateFilter" class="form-control border-0" placeholder="Filtrer par date..." style="font-weight: 700; height: 45px;">
                    <div class="input-group-append">
                        <button class="btn btn-white border-0 text-muted" type="button" onclick="clearDateFilter()" title="Tous les jours">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if($heure && $heure !== 'all')
            <span class="time-badge">
                <i class="fas fa-clock mr-1"></i> Départ de {{ $heure }}
            </span>
        @endif
    </div>

    <div class="detail-date-header">
        <div class="new-date-card">
            @if(isset($isFullMonth) && $isFullMonth)
                <span class="card-month">{{ \Carbon\Carbon::parse($date)->format('Y') }}</span>
                <span class="card-day" style="font-size: 22px;">TOUT</span>
            @else
                <span class="card-month">{{ \Carbon\Carbon::parse($date)->translatedFormat('M') }}</span>
                <span class="card-day">{{ \Carbon\Carbon::parse($date)->format('d') }}</span>
            @endif
        </div>
        <div class="flex-grow-1">
            <h3 class="m-0 text-dark-blue">{{ isset($isFullMonth) && $isFullMonth ? $monthLabel : \Carbon\Carbon::parse($date)->translatedFormat('l d F Y') }}</h3>
            <p class="text-muted m-0">
                <span id="resCount">{{ count($reservations) }}</span> réservation(s) 
                @if(($tab ?? 'en-cours') === 'en-cours')
                    réservée(s)
                @else
                    historiques
                @endif
            </p>
        </div>
        <div class="text-right">
            <div class="metric-label small">Total Recettes</div>
            <div class="h4 font-weight-bold text-orange m-0">
                <span id="totalRevenue">{{ number_format($reservations->sum('montant'), 0, ',', ' ') }}</span> F
            </div>
        </div>
    </div>

    <div class="dash-card">
        <div class="dash-table-wrap">
            <table class="dash-table" id="reservationsTable">
                <thead>
                    <tr>
                        <th>Passager & Réf</th>
                        <th>Trajet & Heure</th>
                        <th class="text-center">Place</th>
                        <th class="text-right">Montant</th>
                        <th class="text-center">Statut</th>
                        <th class="text-center">Détails</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reservations as $reservation)
                    <tr class="reservation-row" 
                        data-date="{{ \Carbon\Carbon::parse($reservation->date_voyage)->toDateString() }}"
                        data-amount="{{ $reservation->montant }}">
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
                                    <span class="text-time mt-1">
                                        @if(isset($isFullMonth) && $isFullMonth)
                                            {{ \Carbon\Carbon::parse($reservation->date_voyage)->translatedFormat('d M') }} à 
                                        @endif
                                        {{ $reservation->programme->heure_depart }}
                                    </span>
                                </div>
                            @else
                                <span class="text-muted">Trajet inconnu</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="seat-badge seat-badge-orange">{{ $reservation->seat_number }}</span>
                        </td>
                        <td class="text-right">
                            <span class="td-amount">{{ number_format($reservation->montant, 0, ',', ' ') }} F</span>
                        </td>
                        <td class="text-center">
                            @if($reservation->statut === 'confirmee')
                                <span class="status-pill sp-success"><span class="dot"></span> Réserver</span>
                            @elseif($reservation->statut === 'terminee')
                                @php
                                    $isPastDate = \Carbon\Carbon::parse($reservation->date_voyage)->format('Y-m-d') < \Carbon\Carbon::now()->format('Y-m-d');
                                @endphp
                                @if($reservation->voyage_id)
                                    <span class="status-pill" style="background: #FFF7ED; color: #EA580C; padding: 6px 14px; white-space: nowrap;">
                                        <span class="dot" style="background: #EA580C;"></span> à voyagé
                                    </span>
                                @elseif($isPastDate)
                                    <span class="status-pill" style="background: #f3f4f6; color: #374151;">
                                        <span class="dot" style="background: #374151;"></span> voyage manqué
                                    </span>
                                @else
                                    <span class="status-pill" style="background: #FFF7ED; color: #EA580C;">
                                        <span class="dot" style="background: #EA580C;"></span> A embarqué
                                    </span>
                                @endif
                            @elseif($reservation->statut === 'passe')
                                <span class="status-pill" style="background: #f3f4f6; color: #374151;">
                                    <span class="dot" style="background: #374151;"></span> Passé
                                </span>
                            @elseif($reservation->statut === 'en_attente')
                                <span class="status-pill" style="background: #f3f4f6; color: #374151;">
                                    <span class="dot" style="background: #374151;"></span> En attente
                                </span>
                            @elseif($reservation->statut === 'annulee')
                                <span class="status-pill" style="background: #fef2f2; color: #991b1b;">
                                    <span class="dot" style="background: #991b1b;"></span> Annulée
                                </span>
                            @else
                                <span class="status-pill sp-success">{{ $reservation->statut }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button onclick="showDetails({{ $reservation->id }})" class="btn-icon" style="width: 38px; height: 38px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; background: #FFF7ED; color: #f97316; border: 1px solid #FFEDD5; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#f97316'; this.style.color='white';" onmouseout="this.style.background='#FFF7ED'; this.style.color='#f97316';">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center p-5">
                            <p class="text-muted m-0">Aucune réservation trouvée pour cette sélection.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $reservations->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

@include('gare-espace.reservation._modal_details')

{{-- Flatpickr Styles & Scripts --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>

<script>
let fp;
document.addEventListener('DOMContentLoaded', function() {
    const availableDates = @json(isset($allDates) ? $allDates->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())->unique()->values() : $reservations->pluck('date_voyage')->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())->unique()->values());
    const monthYear = "{{ \Carbon\Carbon::parse($date)->format('Y-m') }}";

    if (document.getElementById('dateFilter')) {
        fp = flatpickr("#dateFilter", {
            locale: "fr",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "j F Y",
            enable: availableDates,
            defaultDate: null,
            onChange: function(selectedDates, dateStr) {
                filterByDate(dateStr);
            }
        });
    }
});

function filterByDate(dateStr) {
    const rows = document.querySelectorAll('.reservation-row');
    let count = 0;
    let revenue = 0;

    rows.forEach(row => {
        if (!dateStr || row.dataset.date === dateStr) {
            row.style.display = '';
            count++;
            revenue += parseFloat(row.dataset.amount);
        } else {
            row.style.display = 'none';
        }
    });

    // Update stats
    document.getElementById('resCount').textContent = count;
    document.getElementById('totalRevenue').textContent = new Intl.NumberFormat('fr-FR').format(revenue);
}

function clearDateFilter() {
    if (fp) {
        fp.clear();
        filterByDate(null);
    }
}
</script>
@endsection
