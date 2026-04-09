@extends('compagnie.layouts.template')
@php \Carbon\Carbon::setLocale('fr'); @endphp

@section('page-title', isset($isFullMonth) && $isFullMonth ? 'Réservations de ' . $monthLabel : 'Réservations du ' .
    \Carbon\Carbon::parse($date)->translatedFormat('d F Y'))
@section('page-subtitle', isset($isFullMonth) && $isFullMonth ? 'Toutes les réservations du mois' : ($heure && $heure
    !== 'all' ? 'Départ de ' . $heure : 'Toutes les heures de la journée'))

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
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.04);
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
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

        .text-dark-blue {
            color: #001A41 !important;
            font-weight: 800;
        }

        /* Table Utils */
        .cell-stack {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .text-ref {
            font-size: 11px;
            font-weight: 800;
            color: #EA580C;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .text-time {
            font-size: 11px;
            font-weight: 700;
            color: #6B6560;
            text-transform: uppercase;
        }

        .td-avatar.text-orange {
            background: #FFF7ED;
            color: #F97316;
        }

        .user-meta {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .seat-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #F5F4F1;
            border: 1px solid rgba(0, 0, 0, 0.07);
            font-size: 13px;
            font-weight: 800;
            color: #6B6560;
        }

        .seat-badge-orange {
            background: #FFF7ED;
            border-color: #FED7AA;
            color: #EA580C;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sp-success {
            background: #ECFDF5;
            color: #059669;
        }

        .status-pill .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

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
            border: 1px solid rgba(0, 0, 0, 0.07);
            text-decoration: none !important;
            transition: all 0.2s;
        }

        .btn-back:hover {
            background: rgba(0, 0, 0, 0.13);
        }

        .time-badge {
            background: #FFF7ED;
            color: #F97316;
            padding: 4px 10px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 14px;
            border: 1px solid #FED7AA;
            cursor: pointer;
            transition: all 0.2s;
        }

        .time-badge:hover {
            background: #F97316;
            color: #fff;
            border-color: #F97316;
            transform: scale(1.05);
        }

        .time-badge.active {
            background: #F97316;
            color: #fff;
            border-color: #F97316;
        }

        .dashboard-page {
            position: relative;
            min-height: 80vh;
            z-index: 1;
            border-radius: 30px;
            padding: 30px;
            background: #F8F9FB;
            box-shadow: inset 0 0 40px rgba(0, 0, 0, 0.01);
        }

        /* Split Layout */
        .reservations-layout {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
            min-height: 600px;
        }

        .reservations-list {
            background: #fff;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.04);
        }

        .vehicle-layout {
            background: #fff;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.04);
            position: sticky;
            top: 30px;
            height: fit-content;
        }

        .vehicle-layout-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #E5E7EB;
        }

        .vehicle-layout-header h4 {
            color: #001A41;
            font-weight: 900;
            font-size: 16px;
            margin: 0;
        }

        .vehicle-layout-header p {
            color: #6B7280;
            font-size: 12px;
            margin: 5px 0 0 0;
        }

        .no-vehicle-message {
            text-align: center;
            color: #6B7280;
            font-style: italic;
            padding: 40px 20px;
        }

        /* Seat Layout Styles */
        .seat-layout-container {
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .seat-layout-header {
            background: #F9FAFB;
            border-bottom: 1px solid #E5E7EB;
            padding: 12px 16px;
            display: grid;
            grid-template-columns: 60px 1fr 40px 1fr;
            font-size: 10px;
            font-weight: 900;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .seat-layout-body {
            max-height: 400px;
            overflow-y: auto;
            padding: 16px;
        }

        .seat-row {
            display: grid;
            grid-template-columns: 60px 1fr 40px 1fr;
            align-items: center;
            margin-bottom: 12px;
        }

        .seat-row-label {
            text-align: center;
            font-weight: 900;
            color: #D1D5DB;
            font-size: 12px;
        }

        .seats-left,
        .seats-right {
            display: flex;
            justify-content: center;
            gap: 8px;
        }

        .seat-aisle {
            display: flex;
            justify-content: center;
            height: 100%;
        }

        .seat-aisle div {
            width: 1px;
            background: #E5E7EB;
            height: 100%;
        }

        .seat {
            width: 32px;
            height: 32px;
            border: 1px solid #D1D5DB;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 11px;
            transition: all 0.2s;
            cursor: default;
        }

        .seat.available {
            background: #fff;
            color: #374151;
        }

        .seat.occupied {
            background: #EF4444;
            color: #fff;
            border-color: #EF4444;
        }

        .reservation-row {
            cursor: pointer;
            transition: all 0.2s;
        }

        .reservation-row:hover {
            background: #F9FAFB;
        }

        .reservation-row.selected {
            background: #FFF7ED;
            border-left: 4px solid #F97316;
        }

        @if (isset($isFullMonth) && $isFullMonth)
            .flatpickr-calendar .flatpickr-prev-month,
            .flatpickr-calendar .flatpickr-next-month {
                visibility: hidden;
            }
        @endif
    </style>
@endsection

@section('content')
    <div class="dashboard-page">

        <div class="mb-4 d-flex justify-content-between align-items-center">
            <a href="{{ route('gare-espace.reservations.index', ['tab' => $tab ?? 'en-cours']) }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Retour au calendrier
            </a>

            @if (isset($isFullMonth) && $isFullMonth)
                <div class="d-flex align-items-center gap-2">
                    <div class="input-group"
                        style="width: 280px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0;">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-0 text-orange">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                        </div>
                        <input type="text" id="dateFilter" class="form-control border-0"
                            placeholder="Filtrer par date..." style="font-weight: 700; height: 45px;">
                        <div class="input-group-append">
                            <button class="btn btn-white border-0 text-muted" type="button" onclick="clearDateFilter()"
                                title="Tous les jours">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </div>
                    </div>

                    <div class="input-group"
                        style="width: 200px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0;">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-0 text-orange">
                                <i class="fas fa-clock"></i>
                            </span>
                        </div>
                        <select id="heureFilter" class="form-control border-0" onchange="onHeureChange()"
                            style="font-weight: 700; height: 45px;">
                            <option value="all">Toutes les heures</option>
                        </select>
                    </div>
                </div>
            @else
                <div class="input-group"
                    style="width: 200px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0;">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-0 text-orange">
                            <i class="fas fa-clock"></i>
                        </span>
                    </div>
                    <select id="hoursList" class="form-control border-0" onchange="onHoursListChange()"
                        style="font-weight: 700; height: 45px;">
                        <option value="all">Toutes les heures</option>
                    </select>
                </div>
            @endif

            @if ($heure && $heure !== 'all')
                <span class="time-badge">
                    <i class="fas fa-clock mr-1"></i> Départ de {{ $heure }}
                </span>
            @endif
        </div>

        <div class="detail-date-header">
            <div class="new-date-card">
                @if (isset($isFullMonth) && $isFullMonth)
                    <span class="card-month">{{ \Carbon\Carbon::parse($date)->format('Y') }}</span>
                    <span class="card-day" style="font-size: 22px;">TOUT</span>
                @else
                    <span class="card-month">{{ \Carbon\Carbon::parse($date)->translatedFormat('M') }}</span>
                    <span class="card-day">{{ \Carbon\Carbon::parse($date)->format('d') }}</span>
                @endif
            </div>
            <div class="flex-grow-1">
                <h3 class="m-0 text-dark-blue">
                    {{ isset($isFullMonth) && $isFullMonth ? $monthLabel : \Carbon\Carbon::parse($date)->translatedFormat('l d F Y') }}
                </h3>
                <p class="text-muted m-0">
                    <span id="resCount">{{ count($reservations) }}</span> réservation(s)
                    @if (($tab ?? 'en-cours') === 'en-cours')
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

        <!-- Split Layout -->
        <div class="reservations-layout">
            <!-- Left Panel: Reservations List -->
            <div class="reservations-list">
                <div class="dash-table-wrap">
                    <table class="dash-table" id="reservationsTable">
                        <thead>
                            <tr>
                                <th>Passager & Réf</th>
                                <th>Trajet</th>
                                <th class="text-center">Heure</th>
                                <th class="text-center">Place</th>
                                <th class="text-right">Montant</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center">Détails</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reservations as $reservation)
                                <tr class="reservation-row" data-reservation-id="{{ $reservation->id }}"
                                    data-programme-id="{{ $reservation->programme_id }}"
                                    data-date-voyage="{{ $reservation->date_voyage }}"
                                    data-amount="{{ $reservation->montant }}">
                                    <td>
                                        <div class="td-user">
                                            <div class="td-avatar text-orange">
                                                {{ substr($reservation->passager_nom, 0, 1) }}
                                            </div>
                                            <div class="cell-stack">
                                                <span class="td-name">{{ $reservation->passager_prenom }}
                                                    {{ $reservation->passager_nom }}</span>
                                                <div class="user-meta">
                                                    <span
                                                        class="td-phone">{{ $reservation->passager_telephone ?? '---' }}</span>
                                                    <span class="text-ref">{{ $reservation->reference }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($reservation->programme)
                                            <div class="route-pill">
                                                {{ $reservation->programme->point_depart }}
                                                <i class="fas fa-chevron-right route-arrow"></i>
                                                {{ $reservation->programme->point_arrive }}
                                            </div>
                                        @else
                                            <span class="text-muted">Trajet inconnu</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($reservation->programme)
                                            <span class="time-badge"
                                                onclick="filterByHeure('{{ $reservation->programme->heure_depart }}')"
                                                style="padding: 6px 12px; cursor: pointer;">
                                                <i
                                                    class="fas fa-clock mr-1"></i>{{ $reservation->programme->heure_depart }}
                                            </span>
                                        @else
                                            <span class="text-muted">---</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="seat-badge seat-badge-orange">{{ $reservation->seat_number }}</span>
                                    </td>
                                    <td class="text-right">
                                        <span class="td-amount">{{ number_format($reservation->montant, 0, ',', ' ') }}
                                            F</span>
                                    </td>
                                    <td class="text-center">
                                        @if ($reservation->statut === 'confirmee')
                                            <span class="status-pill sp-success"><span class="dot"></span>
                                                Réserver</span>
                                        @elseif($reservation->statut === 'terminee')
                                            @php
                                                $isPastDate =
                                                    \Carbon\Carbon::parse($reservation->date_voyage)->format('Y-m-d') <
                                                    \Carbon\Carbon::now()->format('Y-m-d');
                                            @endphp
                                            @if ($reservation->voyage_id)
                                                <span class="status-pill"
                                                    style="background: #FFF7ED; color: #EA580C; padding: 6px 14px; white-space: nowrap;">
                                                    <span class="dot" style="background: #EA580C;"></span> à voyagé
                                                </span>
                                            @elseif($isPastDate)
                                                <span class="status-pill" style="background: #f3f4f6; color: #374151;">
                                                    <span class="dot" style="background: #374151;"></span> voyage
                                                    manqué
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
                                        <button onclick="showDetails({{ $reservation->id }})" class="btn-icon"
                                            style="width: 38px; height: 38px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; background: #FFF7ED; color: #f97316; border: 1px solid #FFEDD5; cursor: pointer; transition: all 0.2s;"
                                            onmouseover="this.style.background='#f97316'; this.style.color='white';"
                                            onmouseout="this.style.background='#FFF7ED'; this.style.color='#f97316';">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center p-5">
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

            <!-- Right Panel: Vehicle Layout -->
            <div class="vehicle-layout">
                <div class="vehicle-layout-header">
                    <h4>Disposition du Véhicule</h4>
                    <p id="vehicle-info">Cliquez sur une réservation pour voir la disposition</p>
                </div>
                <div id="vehicle-layout-container">
                    <div class="no-vehicle-message">
                        <i class="fas fa-bus text-muted" style="font-size: 48px; margin-bottom: 15px;"></i>
                        <p>Sélectionnez une réservation pour afficher la disposition des places</p>
                    </div>
                </div>
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
        let selectedReservationId = null;
        let selectedHeure = 'all';

        // Configuration des rangées (copié depuis caisse)
        const typeRangeConfig = {
            '1x1': {
                placesGauche: 1,
                placesDroite: 1
            },
            '2x1': {
                placesGauche: 2,
                placesDroite: 1
            },
            '1x2': {
                placesGauche: 1,
                placesDroite: 2
            },
            '2x2': {
                placesGauche: 2,
                placesDroite: 2
            },
            '3x2': {
                placesGauche: 3,
                placesDroite: 2
            },
            '2x3': {
                placesGauche: 2,
                placesDroite: 3
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            const availableDates = @json(isset($allDates)
                    ? $allDates->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())->unique()->values()
                    : $reservations->pluck('date_voyage')->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())->unique()->values());
            const pageDate = "{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}";
            const currentTab = "{{ $tab ?? 'en-cours' }}";
            const isFullMonth = @json(isset($isFullMonth) && $isFullMonth);
            const monthStart =
                "{{ isset($isFullMonth) && $isFullMonth ? \Carbon\Carbon::parse($date)->startOfMonth()->format('Y-m-d') : '' }}";
            const monthEnd =
                "{{ isset($isFullMonth) && $isFullMonth ? \Carbon\Carbon::parse($date)->endOfMonth()->format('Y-m-d') : '' }}";
            const initialDate = (() => {
                if (isFullMonth && Array.isArray(availableDates) && availableDates.length > 0) {
                    // Prendre la première date disponible dans le mois
                    return availableDates.sort().shift();
                }
                return pageDate;
            })();

            if (document.getElementById('dateFilter')) {
                const fpOptions = {
                    locale: "fr",
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "j F Y",
                    enable: availableDates,
                    defaultDate: initialDate,
                    onOpen: function(selectedDates, dateStr, instance) {
                        // Update hour filter when calendar opens
                        const curDate = selectedDates.length > 0 ? selectedDates[0].toISOString().split(
                            'T')[0] : initialDate;
                        populateHeureFilter(curDate, currentTab);
                    },
                    onChange: function(selectedDates, dateStr) {
                        // Reset hour filter when date changes
                        const heureSelect = document.getElementById('heureFilter');
                        if (heureSelect) {
                            heureSelect.value = 'all';
                            selectedHeure = 'all';
                        }
                        loadReservationsAndLayout(dateStr, currentTab, 'all');
                        // Update hour filter for the newly selected date
                        populateHeureFilter(dateStr, currentTab);
                    }
                };

                if (isFullMonth) {
                    fpOptions.minDate = monthStart;
                    fpOptions.maxDate = monthEnd;
                }

                // Peupler la liste des heures au chargement initial
                populateHeureFilter(initialDate, currentTab);

                fp = flatpickr("#dateFilter", fpOptions);
            }

            // Charger automatiquement les réservations et la disposition pour la date initiale
            loadReservationsAndLayout(initialDate, currentTab, 'all');

            // Initialiser le select hoursList pour le mode non-fullMonth
            if (!isFullMonth) {
                populateHoursListSelect(initialDate, currentTab);
            }
        });

        function loadReservationsAndLayout(dateStr, tab = 'en-cours', heure = 'all') {
            selectedHeure = heure;
            const tableBody = document.querySelector('#reservationsTable tbody');
            const vehicleContainer = document.getElementById('vehicle-layout-container');
            const vehicleInfo = document.getElementById('vehicle-info');
            const resCount = document.getElementById('resCount');
            const totalRevenue = document.getElementById('totalRevenue');

            // Show loading states
            tableBody.innerHTML =
                '<tr><td colspan="7" class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Chargement des réservations...</td></tr>';
            vehicleContainer.innerHTML =
                '<div class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Chargement de la disposition...</div>';
            vehicleInfo.textContent = 'Chargement...';

            // Fetch reservations for the selected date and time
            const heureParam = (heure && heure !== 'all') ? `&heure=${heure}` : '';
            fetch(`/gare-espace/reservations/reservations-by-date?date=${dateStr}&tab=${tab}${heureParam}`)
                .then(response => response.json())
                .then(data => {
                    // Update stats
                    resCount.textContent = data.stats.count;
                    totalRevenue.textContent = new Intl.NumberFormat('fr-FR').format(data.stats.revenue);

                    // Update table
                    if (data.reservations.length > 0) {
                        let html = '';
                        data.reservations.forEach(reservation => {
                            const statusClass = getStatusClass(reservation.statut);
                            const statusText = getStatusText(reservation.statut);

                            html += `
                        <tr class="reservation-row" 
                            data-reservation-id="${reservation.id}"
                            data-programme-id="${reservation.programme_id}"
                            data-date-voyage="${reservation.date_voyage}"
                            data-amount="${reservation.montant}">
                            <td>
                                <div class="td-user">
                                    <div class="td-avatar text-orange">
                                        ${reservation.passager_nom.charAt(0)}
                                    </div>
                                    <div class="cell-stack">
                                        <span class="td-name">${reservation.passager_prenom} ${reservation.passager_nom}</span>
                                        <div class="user-meta">
                                            <span class="td-phone">${reservation.passager_telephone || '---'}</span>
                                            <span class="text-ref">${reservation.reference}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                ${reservation.programme ? `
                                                                    <div class="route-pill">
                                                                        ${reservation.programme.point_depart}
                                                                        <i class="fas fa-chevron-right route-arrow"></i>
                                                                        ${reservation.programme.point_arrive}
                                                                    </div>
                                                                ` : '<span class="text-muted">Trajet inconnu</span>'}
                            </td>
                            <td class="text-center">
                                ${reservation.programme ? `
                                                                    <span class="time-badge" onclick="filterByHeure('${reservation.programme.heure_depart}')" style="padding: 6px 12px; cursor: pointer;">
                                                                        <i class="fas fa-clock mr-1"></i>${reservation.programme.heure_depart}
                                                                    </span>
                                                                ` : '<span class="text-muted">---</span>'}
                            </td>
                            <td class="text-center">
                                <span class="seat-badge seat-badge-orange">${reservation.seat_number}</span>
                            </td>
                            <td class="text-right">
                                <span class="td-amount">${new Intl.NumberFormat('fr-FR').format(reservation.montant)} F</span>
                            </td>
                            <td class="text-center">
                                <span class="status-pill ${statusClass}"><span class="dot"></span> ${statusText}</span>
                            </td>
                            <td class="text-center">
                                <button onclick="showDetails(${reservation.id})" class="btn-icon" style="width: 38px; height: 38px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; background: #FFF7ED; color: #f97316; border: 1px solid #FFEDD5; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#f97316'; this.style.color='white';" onmouseout="this.style.background='#FFF7ED'; this.style.color='#f97316';">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                        });
                        tableBody.innerHTML = html;

                        // Add click event listeners to new reservation rows
                        document.querySelectorAll('.reservation-row').forEach(row => {
                            row.addEventListener('click', function() {
                                const reservationId = this.dataset.reservationId;
                                const programmeId = this.dataset.programmeId;
                                const dateVoyage = this.dataset.dateVoyage;

                                // Remove selected class from all rows
                                document.querySelectorAll('.reservation-row').forEach(r => r.classList
                                    .remove('selected'));
                                // Add selected class to clicked row
                                this.classList.add('selected');

                                selectedReservationId = reservationId;
                                loadVehicleLayout(programmeId, dateVoyage);
                            });
                        });

                        // Update heure filter options for this date
                        if (heure === 'all') {
                            populateHeureFilterFromReservations(data.reservations);
                            // Also update hoursList if it exists (non-fullMonth mode)
                            const hoursList = document.getElementById('hoursList');
                            if (hoursList) {
                                const currentTab = "{{ $tab ?? 'en-cours' }}";
                                populateHoursListSelect(dateStr, currentTab);
                            }
                        }

                        // Auto-select first reservation and load its vehicle layout
                        if (data.reservations.length > 0) {
                            const firstRow = document.querySelector('.reservation-row');
                            if (firstRow) {
                                firstRow.click();
                            }
                        }
                    } else {
                        tableBody.innerHTML =
                            '<tr><td colspan="7" class="text-center p-5"><p class="text-muted m-0">Aucune réservation trouvée pour cette date.</p></td></tr>';
                        vehicleContainer.innerHTML =
                            '<div class="no-vehicle-message"><i class="fas fa-calendar-times text-muted"></i><p>Aucune réservation pour cette date</p></div>';
                        vehicleInfo.textContent = 'Aucune réservation';
                    }
                })
                .catch(error => {
                    console.error('Error loading reservations:', error);
                    tableBody.innerHTML =
                        '<tr><td colspan="7" class="text-center p-5"><p class="text-danger m-0">Erreur lors du chargement des réservations.</p></td></tr>';
                    vehicleContainer.innerHTML =
                        '<div class="no-vehicle-message"><i class="fas fa-exclamation-triangle text-danger"></i><p>Erreur de chargement</p></div>';
                    vehicleInfo.textContent = 'Erreur de chargement';
                });
        }

        function getStatusClass(statut) {
            switch (statut) {
                case 'confirmee':
                    return 'sp-success';
                case 'terminee':
                    return 'sp-success';
                case 'en_attente':
                    return 'sp-success';
                case 'annulee':
                    return 'sp-success';
                default:
                    return 'sp-success';
            }
        }

        function getStatusText(statut) {
            switch (statut) {
                case 'confirmee':
                    return 'Réserver';
                case 'terminee':
                    return 'Voyagé';
                case 'en_attente':
                    return 'En attente';
                case 'annulee':
                    return 'Annulée';
                case 'passe':
                    return 'Passé';
                default:
                    return statut;
            }
        }

        function populateHeureFilter(dateStr, tab) {
            const heureSelect = document.getElementById('heureFilter');
            if (!heureSelect) return;

            fetch(`/gare-espace/reservations/reservations-by-date?date=${dateStr}&tab=${tab}`)
                .then(response => response.json())
                .then(data => {
                    populateHeureFilterFromReservations(data.reservations);
                })
                .catch(error => console.error('Error fetching heures:', error));
        }

        function populateHeureFilterFromReservations(reservations) {
            const heureSelect = document.getElementById('heureFilter');
            if (!heureSelect) return;

            // Extract unique hours from reservations
            const heures = new Set();
            reservations.forEach(res => {
                if (res.programme && res.programme.heure_depart) {
                    heures.add(res.programme.heure_depart);
                }
            });

            // Build the options
            const heureOptions = ['all', ...Array.from(heures).sort()];

            // Store current value
            const currentValue = heureSelect.value;

            // Clear and rebuild
            heureSelect.innerHTML = '<option value="all">Toutes les heures</option>';
            heureOptions.forEach(heure => {
                if (heure !== 'all') {
                    const option = document.createElement('option');
                    option.value = heure;
                    option.textContent = `Départ à ${heure}`;
                    heureSelect.appendChild(option);
                }
            });

            // Restore previous selection if still available
            if (heureSelect.querySelector(`option[value="${currentValue}"]`)) {
                heureSelect.value = currentValue;
            } else {
                heureSelect.value = 'all';
                selectedHeure = 'all';
            }
        }

        function populateHoursListSelect(dateStr, tab) {
            const hoursList = document.getElementById('hoursList');
            if (!hoursList) return;

            fetch(`/gare-espace/reservations/reservations-by-date?date=${dateStr}&tab=${tab}`)
                .then(response => response.json())
                .then(data => {
                    // Extract unique hours from reservations
                    const heures = new Set();
                    data.reservations.forEach(res => {
                        if (res.programme && res.programme.heure_depart) {
                            heures.add(res.programme.heure_depart);
                        }
                    });

                    // Build the options
                    const heureOptions = Array.from(heures).sort();

                    // Store current value
                    const currentValue = hoursList.value;

                    // Clear and rebuild
                    hoursList.innerHTML = '<option value="all">Toutes les heures</option>';
                    heureOptions.forEach(heure => {
                        const option = document.createElement('option');
                        option.value = heure;
                        option.textContent = heure;
                        hoursList.appendChild(option);
                    });

                    // Restore previous selection if still available
                    if (hoursList.querySelector(`option[value="${currentValue}"]`)) {
                        hoursList.value = currentValue;
                    } else {
                        hoursList.value = 'all';
                    }
                })
                .catch(error => console.error('Error fetching hours:', error));
        }

        function filterByHeure(heure) {
            // Trouver le bon select selon le mode (fullMonth ou simple)
            let heureSelect = document.getElementById('heureFilter');
            if (!heureSelect) {
                heureSelect = document.getElementById('hoursList');
            }

            if (!heureSelect) return;

            // Mettre à jour la valeur du select
            heureSelect.value = heure;

            // Déclencher le changement
            const isFullMonth = @json(isset($isFullMonth) && $isFullMonth);
            if (isFullMonth) {
                onHeureChange();
            } else {
                onHoursListChange();
            }
        }

        function onHeureChange() {
            const heureSelect = document.getElementById('heureFilter');
            if (!heureSelect) return;

            const selectedDate = fp ? fp.selectedDates[0].toISOString().split('T')[0] :
                "{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}";
            const currentTab = "{{ $tab ?? 'en-cours' }}";
            const selectedHeurValue = heureSelect.value;

            loadReservationsAndLayout(selectedDate, currentTab, selectedHeurValue);
        }

        function onHoursListChange() {
            const hoursList = document.getElementById('hoursList');
            if (!hoursList) return;

            const selectedDate = "{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}";
            const currentTab = "{{ $tab ?? 'en-cours' }}";
            const selectedHour = hoursList.value;

            loadReservationsAndLayout(selectedDate, currentTab, selectedHour);
        }

        function clearDateFilter() {
            if (fp) {
                const pageDate = "{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}";
                const isFullMonth = @json(isset($isFullMonth) && $isFullMonth);
                const availableDates = @json(isset($allDates)
                        ? $allDates->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())->unique()->values()
                        : $reservations->pluck('date_voyage')->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())->unique()->values());
                const defaultDate = (isFullMonth && Array.isArray(availableDates) && availableDates.length > 0) ?
                    availableDates.sort().shift() :
                    pageDate;
                const currentTab = "{{ $tab ?? 'en-cours' }}";
                fp.setDate(defaultDate);

                // Also reset hour filter
                const heureSelect = document.getElementById('heureFilter');
                if (heureSelect) {
                    heureSelect.value = 'all';
                    selectedHeure = 'all';
                }

                loadReservationsAndLayout(defaultDate, currentTab, 'all');
            }
        }

        function loadVehicleLayout(programmeId, dateVoyage) {
            const container = document.getElementById('vehicle-layout-container');
            const info = document.getElementById('vehicle-info');

            // Show loading
            container.innerHTML = '<div class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Chargement...</div>';
            info.textContent = 'Chargement de la disposition...';

            // Make AJAX call to get occupied seats
            fetch(`/gare-espace/reservations/occupied-seats?programme_id=${programmeId}&date_voyage=${dateVoyage}`)
                .then(response => response.json())
                .then(data => {
                    if (data.vehicle_name && data.vehicle_name !== 'Véhicule non assigné') {
                        // Get vehicle details - we need to fetch the programme to get vehicle info
                        fetch(
                                `/gare-espace/reservations/programme-vehicle?programme_id=${programmeId}&date_voyage=${dateVoyage}`
                            )
                            .then(response => response.json())
                            .then(vehicleData => {
                                const visualization = generatePlacesVisualization(vehicleData, data.occupied);
                                container.innerHTML = visualization;
                                info.textContent = data.vehicle_name;
                            })
                            .catch(error => {
                                console.error('Error fetching vehicle data:', error);
                                container.innerHTML =
                                    '<div class="no-vehicle-message"><i class="fas fa-exclamation-triangle text-warning"></i><p>Erreur lors du chargement du véhicule</p></div>';
                                info.textContent = 'Erreur de chargement';
                            });
                    } else {
                        container.innerHTML =
                            '<div class="no-vehicle-message"><i class="fas fa-bus text-muted"></i><p>Aucun véhicule assigné pour ce programme</p></div>';
                        info.textContent = 'Aucun véhicule assigné';
                    }
                })
                .catch(error => {
                    console.error('Error fetching occupied seats:', error);
                    container.innerHTML =
                        '<div class="no-vehicle-message"><i class="fas fa-exclamation-triangle text-danger"></i><p>Erreur lors du chargement des places</p></div>';
                    info.textContent = 'Erreur de chargement';
                });
        }

        function generatePlacesVisualization(vehicle, occupiedSeats) {
            if (!vehicle || !vehicle.type_range) {
                return '<div class="no-vehicle-message"><i class="fas fa-exclamation-triangle text-warning"></i><p>Configuration du véhicule incomplète</p></div>';
            }

            let config = typeRangeConfig[vehicle.type_range] || typeRangeConfig['2x2'];
            const placesGauche = config.placesGauche;
            const placesDroite = config.placesDroite;
            const placesParRanger = placesGauche + placesDroite;
            const totalPlaces = parseInt(vehicle.nombre_place);
            const nombreRanger = Math.ceil(totalPlaces / placesParRanger);

            let html = `
        <div class="seat-layout-container">
            <div class="seat-layout-header">
                <div>RANG</div>
                <div>GAUCHE</div>
                <div>ALLÉE</div>
                <div>DROITE</div>
            </div>
            <div class="seat-layout-body">
    `;

            let numeroPlace = 1;
            for (let r = 1; r <= nombreRanger; r++) {
                html += `<div class="seat-row">
                    <div class="seat-row-label">R${r}</div>
                    <div class="seats-left">`;

                // Gauche
                for (let i = 0; i < placesGauche; i++) {
                    if (numeroPlace <= totalPlaces) {
                        const isOccupied = occupiedSeats.includes(numeroPlace);
                        const seatClass = isOccupied ? 'seat occupied' : 'seat available';

                        html += `<div class="${seatClass}">${numeroPlace}</div>`;
                        numeroPlace++;
                    }
                }

                html += `</div>
                <div class="seat-aisle"><div></div></div>
                <div class="seats-right">`;

                // Droite
                for (let i = 0; i < placesDroite; i++) {
                    if (numeroPlace <= totalPlaces) {
                        const isOccupied = occupiedSeats.includes(numeroPlace);
                        const seatClass = isOccupied ? 'seat occupied' : 'seat available';

                        html += `<div class="${seatClass}">${numeroPlace}</div>`;
                        numeroPlace++;
                    }
                }

                html += `</div></div>`;
            }

            html += `</div></div>`;
            return html;
        }
    </script>
@endsection
