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
</style>
@endsection

@section('content')
<div class="dashboard-page">

    <div class="mb-4 d-flex justify-content-between align-items-center">
        <a href="{{ route('company.reservation.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Retour au calendrier
        </a>
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
                <span class="card-day" style="font-size: 22px;">ALL</span>
            @else
                <span class="card-month">{{ \Carbon\Carbon::parse($date)->translatedFormat('M') }}</span>
                <span class="card-day">{{ \Carbon\Carbon::parse($date)->format('d') }}</span>
            @endif
        </div>
        <div class="flex-grow-1">
            <h3 class="m-0 text-dark-blue">{{ isset($isFullMonth) && $isFullMonth ? $monthLabel : \Carbon\Carbon::parse($date)->translatedFormat('l d F Y') }}</h3>
            <p class="text-muted m-0">
                {{ count($reservations) }} réservation(s) confirmée(s)
                @if($heure && $heure !== 'all') pour ce voyage @endif
            </p>
        </div>
        <div class="text-right">
            <div class="metric-label small">Total Recettes</div>
            <div class="h4 font-weight-bold text-orange m-0">{{ number_format($reservations->sum('montant'), 0, ',', ' ') }} F</div>
        </div>
    </div>

    <div class="dash-card">
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Passager & Réf</th>
                        <th>Trajet & Heure</th>
                        <th class="text-center">Place</th>
                        <th class="text-right">Montant</th>
                        <th class="text-center">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reservations as $reservation)
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
                            <span class="seat-badge seat-badge-orange">{{ $reservation->seat_number }}</span>
                        </td>
                        <td class="text-right">
                            <span class="td-amount">{{ number_format($reservation->montant, 0, ',', ' ') }} F</span>
                        </td>
                        <td class="text-center">
                            <span class="status-pill sp-success"><span class="dot"></span> Confirmée</span>
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
    </div>
</div>
@endsection
