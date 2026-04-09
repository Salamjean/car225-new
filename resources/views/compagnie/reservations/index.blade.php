@extends('compagnie.layouts.template')
@php \Carbon\Carbon::setLocale('fr'); @endphp

@section('page-title', 'Gestion des Réservations')
@section('page-subtitle', 'Vue similaire à la gare - sous-onglet Réservés')

@section('styles')
    <style>
        .dashboard-page {
            position: relative;
            min-height: 80vh;
            z-index: 1;
            border-radius: 30px;
            padding: 30px;
            background: #F8F9FB;
            box-shadow: inset 0 0 40px rgba(0, 0, 0, 0.01);
        }

        .bg-shape {
            position: absolute;
            filter: blur(100px);
            z-index: -1;
            border-radius: 50%;
            opacity: 0.3;
            pointer-events: none;
        }

        .shape-1 {
            width: 400px;
            height: 400px;
            background: rgba(249, 115, 22, 0.15);
            top: -100px;
            right: -100px;
        }

        .shape-2 {
            width: 300px;
            height: 300px;
            background: rgba(251, 146, 60, 0.1);
            bottom: -50px;
            left: -50px;
        }

        .metric-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .glass-metric {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 24px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        }

        .metric-icon-box {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: #fff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        .metric-info h4 {
            font-size: 13px;
            font-weight: 700;
            color: #64748B;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .metric-info p {
            font-size: 26px;
            font-weight: 900;
            color: #1e293b;
            margin: 0;
        }

        .premium-tabs {
            display: inline-flex;
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(10px);
            padding: 5px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            margin-bottom: 25px;
        }

        .p-tab {
            padding: 10px 24px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 800;
            color: #64748B;
            text-decoration: none !important;
            transition: all 0.3s ease;
        }

        .p-tab.active {
            background: #fff;
            color: #f97316;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .dash-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 25px;
        }

        .header-text h3 {
            font-size: 22px;
            font-weight: 900;
            color: #1e293b;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-icon {
            width: 42px;
            height: 42px;
            background: #f97316;
            color: #fff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .btn-premium {
            background: #001A41;
            color: #fff !important;
            padding: 12px 24px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none !important;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .accent {
            color: #f97316;
        }

        .programs-section {
            margin-top: 10px;
        }

        .programs-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .programs-title {
            font-size: 12px;
            font-weight: 900;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin: 0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .date-picker-wrap {
            background: #fff;
            padding: 8px 10px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .date-picker-input {
            border: none;
            outline: none;
            font-weight: 700;
            color: #374151;
            background: transparent;
            font-size: 13px;
        }

        .program-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 18px;
        }

        .program-card {
            background: #fff;
            border-radius: 24px;
            padding: 18px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
            border: 1px solid #eef2f7;
            position: relative;
            overflow: hidden;
        }

        .program-card::before {
            content: "";
            position: absolute;
            top: -35px;
            right: -35px;
            width: 110px;
            height: 110px;
            border-radius: 999px;
            background: #fff7ed;
        }

        .program-card-content {
            position: relative;
            z-index: 2;
        }

        .program-destination-label {
            font-size: 10px;
            font-weight: 900;
            color: #f97316;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .program-destination-name {
            font-size: 20px;
            font-weight: 900;
            color: #111827;
            margin: 0 0 12px 0;
            text-transform: uppercase;
        }

        .schedule-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .schedule-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none !important;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            background: #f8fafc;
            padding: 10px 8px;
            color: #4b5563;
            transition: all 0.2s ease;
        }

        .schedule-link:hover {
            border-color: #fdba74;
            background: #fff7ed;
        }

        .schedule-hour {
            font-size: 20px;
            line-height: 1;
            font-weight: 900;
            margin-bottom: 4px;
            color: #1f2937;
        }

        .schedule-meta {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            color: #9ca3af;
        }

        .program-empty {
            grid-column: 1 / -1;
            background: rgba(255, 255, 255, 0.6);
            border: 2px dashed #d1d5db;
            border-radius: 24px;
            padding: 34px;
            text-align: center;
            color: #9ca3af;
            font-weight: 700;
        }
    </style>
@endsection

@section('content')
    <div class="dashboard-page">
        <div class="bg-shape shape-1"></div>
        <div class="bg-shape shape-2"></div>

        <div class="dash-section-header">
            <div class="header-text">
                <h3>
                    <div class="header-icon">
                        @if ($tab === 'en-cours')
                            <i class="fas fa-check-circle"></i>
                        @else
                            <i class="fas fa-history"></i>
                        @endif
                    </div>
                    @if ($tab === 'en-cours')
                        Voyages <span class="accent">Réservés</span>
                    @else
                        Voyages <span class="accent">Embarqués</span>
                    @endif
                </h3>
            </div>
        </div>

        <div class="premium-tabs">
            <a href="{{ route('company.reservation.index', ['tab' => 'en-cours', 'date_voyage' => $date]) }}"
                class="p-tab {{ $tab === 'en-cours' ? 'active' : '' }}">
                📦 Réservés
            </a>
            <a href="{{ route('company.reservation.index', ['tab' => 'terminees', 'date_voyage' => $date]) }}"
                class="p-tab {{ $tab === 'terminees' ? 'active' : '' }}">
                🏁 Embarqués
            </a>
            <a href="{{ route('company.reservation.details') }}" class="p-tab">
                📊 Détails & Stats
            </a>
        </div>

        <div class="metric-grid">
            <div class="glass-metric">
                <div class="metric-icon-box accent"><i class="fas fa-layer-group"></i></div>
                <div class="metric-info">
                    <h4>Total Réservations</h4>
                    <p>{{ number_format($stats['total'] ?? 0, 0, ',', ' ') }}</p>
                </div>
            </div>
            <div class="glass-metric">
                <div class="metric-icon-box" style="color:#22c55e;"><i class="fas fa-calendar-day"></i></div>
                <div class="metric-info">
                    <h4>Aujourd'hui</h4>
                    <p>{{ number_format($stats['today'] ?? 0, 0, ',', ' ') }}</p>
                </div>
            </div>
            <div class="glass-metric">
                <div class="metric-icon-box" style="color:#3b82f6;"><i class="fas fa-check-circle"></i></div>
                <div class="metric-info">
                    <h4>Réservées</h4>
                    <p>{{ number_format($stats['confirmed'] ?? 0, 0, ',', ' ') }}</p>
                </div>
            </div>
        </div>

        <div class="programs-section">
            <div class="programs-header">
                <h4 class="programs-title">
                    <i class="fas fa-route accent"></i> Sélectionner un trajet
                </h4>
                <div class="date-picker-wrap">
                    <i class="fas fa-calendar-alt accent"></i>
                    <input type="date" value="{{ $date }}"
                        onchange="window.location.href='{{ route('company.reservation.index', ['tab' => $tab]) }}&date_voyage=' + this.value"
                        class="date-picker-input">
                </div>
            </div>

            <div class="program-grid">
                @forelse($availableProgrammes as $destinationId => $progs)
                    <div class="program-card">
                        <div class="program-card-content">
                            <p class="program-destination-label">Vers</p>
                            <h5 class="program-destination-name">
                                {{ $progs->first()->gareArrivee?->nom_gare ?? $progs->first()->point_arrive }}
                            </h5>

                            <div class="schedule-grid">
                                @foreach ($progs as $prog)
                                    <a href="{{ route('company.reservation.by_date', ['date' => $date, 'tab' => $tab, 'heure' => \Carbon\Carbon::parse($prog->heure_depart)->format('H:i'), 'programme_id' => $prog->id]) }}"
                                        class="schedule-link">
                                        <span class="schedule-hour">{{ \Carbon\Carbon::parse($prog->heure_depart)->format('H:i') }}</span>
                                        <span class="schedule-meta">
                                            {{ $prog->getPlacesReserveesForDate($date, $tab) }}/{{ $prog->getTotalSeats($date) }}
                                            @if (($tab ?? '') === 'terminees')
                                                Embarqués
                                            @else
                                                Réserv.
                                            @endif
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="program-empty">
                        <i class="fas fa-calendar-times" style="font-size: 28px; margin-bottom: 10px;"></i>
                        <p style="margin:0;">Aucun programme actif pour cette date.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection