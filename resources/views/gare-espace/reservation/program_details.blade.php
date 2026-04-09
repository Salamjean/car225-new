@extends('gare-espace.layouts.template')
@php \Carbon\Carbon::setLocale('fr'); @endphp

@section('title', 'Passagers - ' . ($programme->gareArrivee?->nom_gare ?? $programme->point_arrive))

@section('page-title', 'Réservations du ' . \Carbon\Carbon::parse($date)->translatedFormat('d F Y'))
@section('page-subtitle', 'Programme de ' . \Carbon\Carbon::parse($programme->heure_depart)->format('H:i') . ' vers ' .
    ($programme->gareArrivee?->nom_gare ?? $programme->point_arrive))

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

        .btn-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #FFF7ED;
            color: #f97316;
            border: 1px solid #FFEDD5;
            transition: all 0.2s;
            cursor: pointer;
        }

        .route-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #F8F9FB;
            padding: 6px 12px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 12px;
            color: #334155;
            border: 1px solid rgba(0, 0, 0, 0.03);
            width: max-content;
        }

        .route-arrow {
            font-size: 10px;
            color: #f97316;
            opacity: 0.7;
        }

        /* Stats Cards */
        .stats-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.04);
            transition: all 0.2s;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .stats-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .stats-value {
            font-size: 28px;
            font-weight: 900;
            color: #001A41;
            margin: 8px 0;
        }

        .stats-label {
            font-size: 11px;
            font-weight: 900;
            color: #6B6560;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Enhanced Table */
        .reservations-table {
            width: 100%;
            border-collapse: collapse;
        }

        .reservations-table th {
            background: #F9FAFB;
            padding: 12px 16px;
            text-align: left;
            font-size: 10px;
            font-weight: 900;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #E5E7EB;
        }

        .reservations-table td {
            padding: 16px;
            border-bottom: 1px solid #F3F4F6;
        }

        .reservations-table tbody tr:hover {
            background: #F9FAFB;
        }

        /* Enhanced Vehicle Layout */
        .vehicle-layout-header h4 {
            color: #001A41;
            font-weight: 900;
            font-size: 16px;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .vehicle-layout-header p {
            color: #6B7280;
            font-size: 12px;
            margin: 5px 0 0 0;
        }

        .no-vehicle-message {
            text-align: center;
            color: #6B7280;
            padding: 40px 20px;
        }

        .no-vehicle-message i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .no-vehicle-message h5 {
            color: #001A41;
            font-weight: 900;
            margin-bottom: 8px;
        }
    </style>
@endsection

@section('content')
    <div class="dashboard-page">

        {{-- HEADER SECTION --}}
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('gare-espace.reservations.index', ['date_voyage' => $date, 'tab' => $tab]) }}"
                        class="btn-back">
                        <i class="fas fa-arrow-left"></i> Retour aux programmes
                    </a>
                    <div class="h-8 w-px bg-gray-300"></div>
                    <div>
                        <h1 class="text-2xl font-black text-gray-900 mb-1">
                            Programme du {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
                        </h1>
                        <p class="text-sm text-gray-600">
                            Départ à {{ \Carbon\Carbon::parse($programme->heure_depart)->format('H:i') }} •
                            {{ $programme->point_depart }} → {{ $programme->point_arrive }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="time-badge">
                        <i class="fas fa-clock mr-1"></i>
                        {{ \Carbon\Carbon::parse($programme->heure_depart)->format('H:i') }}
                    </div>
                    <div class="bg-orange-50 px-3 py-2 rounded-lg border border-orange-200">
                        <div class="text-xs font-bold text-orange-700 uppercase tracking-wide">Programme</div>
                        <div class="text-sm font-black text-orange-800">#{{ $programme->id }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- STATS CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Total Réservations</p>
                        <p class="text-2xl font-black text-gray-900">{{ $reservations->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Places Occupées</p>
                        <p class="text-2xl font-black text-gray-900" id="occupied-count">
                            {{ $reservations->whereIn('statut', ['confirmee', 'terminee'])->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chair text-red-600 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Recettes Totales</p>
                        <p class="text-2xl font-black text-gray-900">
                            {{ number_format($reservations->sum('montant'), 0, ',', ' ') }} F</p>
                    </div>
                    <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-euro-sign text-green-600 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Taux Occupation</p>
                        <p class="text-2xl font-black text-gray-900" id="occupancy-rate">
                            @php
                                $totalSeats = $programme->getTotalSeats($date) ?: 50;
                                $occupiedSeats = $reservations->whereIn('statut', ['confirmee', 'terminee'])->count();
                                $rate = $totalSeats > 0 ? round(($occupiedSeats / $totalSeats) * 100) : 0;
                            @endphp
                            {{ $rate }}%
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-pie text-purple-600 text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 mb-6 shadow-sm border border-white">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-filter text-orange-500"></i>
                        <span class="text-sm font-bold text-gray-700">Filtres actifs</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-bold">
                            Date: {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                        </span>
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                            Programme: {{ \Carbon\Carbon::parse($programme->heure_depart)->format('H:i') }}
                        </span>
                    </div>
                </div>

                <form action="{{ route('gare-espace.reservations.program', $programme->id) }}" method="GET"
                    class="flex items-center gap-3">
                    <input type="hidden" name="date" value="{{ $date }}">
                    <input type="hidden" name="tab" value="{{ $tab }}">

                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="reference" value="{{ request('reference') }}"
                            placeholder="Rechercher par référence ou passager..."
                            class="pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none text-sm w-80">
                    </div>

                    <button type="submit"
                        class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors text-sm font-bold">
                        <i class="fas fa-search mr-2"></i>Rechercher
                    </button>
                </form>
            </div>
        </div>

        {{-- MAIN CONTENT GRID --}}
        <div class="reservations-layout">
            {{-- LEFT PANEL: RESERVATIONS LIST --}}
            <div class="reservations-list">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-black text-gray-900 mb-1">Liste des Réservations</h3>
                        <p class="text-sm text-gray-600">Cliquez sur une réservation pour voir sa place</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span
                            class="text-xs font-bold text-gray-600">{{ $reservations->whereIn('statut', ['confirmee', 'en_attente'])->count() }}
                            actives</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Passager</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Contact</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Trajet</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Siège</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Montant</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Statut</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($reservations as $reservation)
                                <tr class="reservation-row hover:bg-gray-50 transition-colors"
                                    data-reservation-id="{{ $reservation->id }}"
                                    data-programme-id="{{ $reservation->programme_id }}"
                                    data-date-voyage="{{ $reservation->date_voyage }}"
                                    data-amount="{{ $reservation->montant }}">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center">
                                            <div class="td-avatar text-orange flex-shrink-0 mr-3">
                                                {{ substr($reservation->passager_nom, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900">
                                                    {{ $reservation->passager_prenom }} {{ $reservation->passager_nom }}
                                                </div>
                                                <div class="text-xs text-gray-500 font-mono">{{ $reservation->reference }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm text-gray-900">{{ $reservation->passager_telephone ?? '---' }}
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $reservation->passager_email ?? '---' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="route-pill">
                                            {{ $reservation->programme->point_depart }}
                                            <i class="fas fa-arrow-right route-arrow"></i>
                                            {{ $reservation->programme->point_arrive }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ \Carbon\Carbon::parse($reservation->programme->heure_depart)->format('H:i') }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="seat-badge seat-badge-orange">{{ $reservation->seat_number }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <div class="text-sm font-bold text-gray-900">
                                            {{ number_format($reservation->montant, 0, ',', ' ') }} F</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $reservation->payment_transaction_id ?? '---' }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        @if ($reservation->statut === 'confirmee')
                                            <span class="status-pill sp-success"><span class="dot"></span>
                                                Confirmée</span>
                                        @elseif($reservation->statut === 'terminee')
                                            @php
                                                $isPastDate =
                                                    \Carbon\Carbon::parse($reservation->date_voyage)->format('Y-m-d') <
                                                    \Carbon\Carbon::now()->format('Y-m-d');
                                            @endphp
                                            @if ($reservation->voyage_id)
                                                <span class="status-pill"
                                                    style="background: #FFF7ED; color: #EA580C; padding: 6px 14px; white-space: nowrap;">
                                                    <span class="dot" style="background: #EA580C;"></span> À voyagé
                                                </span>
                                            @elseif($isPastDate)
                                                <span class="status-pill" style="background: #f3f4f6; color: #374151;">
                                                    <span class="dot" style="background: #374151;"></span> Voyage
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
                                    <td class="px-4 py-4 text-center">
                                        <button onclick="showDetails({{ $reservation->id }})" class="btn-icon"
                                            title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-users text-gray-300 text-4xl mb-4"></i>
                                            <h4 class="text-lg font-bold text-gray-900 mb-2">Aucune réservation trouvée
                                            </h4>
                                            <p class="text-gray-500">Il n'y a pas de réservations pour ce programme à cette
                                                date.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($reservations->hasPages())
                    <div class="mt-6 flex justify-center">
                        {{ $reservations->links() }}
                    </div>
                @endif
            </div>

            {{-- RIGHT PANEL: VEHICLE LAYOUT --}}
            <div class="vehicle-layout">
                <div class="vehicle-layout-header">
                    <h4 class="flex items-center gap-2">
                        <i class="fas fa-bus text-orange-500"></i>
                        Disposition du Véhicule
                    </h4>
                    <p id="vehicle-info" class="text-sm">Cliquez sur une réservation pour voir la disposition</p>
                </div>

                <div id="vehicle-layout-container">
                    <div class="no-vehicle-message">
                        <i class="fas fa-bus text-4xl text-gray-300 mb-4"></i>
                        <h5 class="text-lg font-bold text-gray-900 mb-2">Disposition des places</h5>
                        <p>Sélectionnez une réservation pour afficher la disposition des places du véhicule.</p>
                        <div class="mt-4 flex justify-center gap-4">
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 bg-white border border-gray-300 rounded"></div>
                                <span class="text-xs text-gray-600">Libre</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 bg-red-500 rounded"></div>
                                <span class="text-xs text-gray-600">Occupée</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('gare-espace.reservation._modal_details')

@endsection

@section('scripts')
    <script>
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
            // Add click event listeners to reservation rows
            document.querySelectorAll('.reservation-row').forEach(row => {
                row.addEventListener('click', function() {
                    const reservationId = this.dataset.reservationId;
                    const programmeId = this.dataset.programmeId;
                    const dateVoyage = this.dataset.dateVoyage;

                    // Remove selected class from all rows
                    document.querySelectorAll('.reservation-row').forEach(r => r.classList.remove(
                        'selected'));
                    // Add selected class to clicked row
                    this.classList.add('selected');

                    selectedReservationId = reservationId;
                    loadVehicleLayout(programmeId, dateVoyage);
                });
            });

            // Auto-select first reservation and load its vehicle layout
            const firstRow = document.querySelector('.reservation-row');
            if (firstRow) {
                firstRow.click();
            }
        });

        function loadVehicleLayout(programmeId, dateVoyage) {
            const container = document.getElementById('vehicle-layout-container');
            const info = document.getElementById('vehicle-info');

            // Show loading
            container.innerHTML = '<div class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Chargement...</div>';
            info.textContent = 'Chargement de la disposition...';

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Make AJAX call to get occupied seats
            fetch(`{{ route('gare-espace.reservations.occupied-seats') }}?programme_id=${programmeId}&date_voyage=${dateVoyage}`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.vehicle_name && data.vehicle_name !== 'Véhicule non assigné') {
                        // Get vehicle details - we need to fetch the programme to get vehicle info
                        fetch(`{{ route('gare-espace.reservations.programme-vehicle') }}?programme_id=${programmeId}&date_voyage=${dateVoyage}`, {
                                method: 'GET',
                                credentials: 'same-origin',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`HTTP error! status: ${response.status}`);
                                }
                                return response.json();
                            })
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
