@extends('compagnie.layouts.template')
@php \Carbon\Carbon::setLocale('fr'); @endphp

@section('page-title', 'Gestion des Réservations')
@section('page-subtitle', 'Selectionnez un mois pour explorer vos réservations')

@section('styles')
<style>
    /* ── BASE & BACKGROUND ── */
    .dashboard-page {
        position: relative;
        min-height: 80vh;
        z-index: 1;
        border-radius: 30px;
        padding: 30px;
        background: #F8F9FB;
        box-shadow: inset 0 0 40px rgba(0,0,0,0.01);
    }

    /* Mesh Gradient Background Elements */
    .bg-shape {
        position: absolute;
        filter: blur(100px);
        z-index: -1;
        border-radius: 50%;
        opacity: 0.3;
    }
    .shape-1 { width: 400px; height: 400px; background: rgba(255, 90, 31, 0.15); top: -100px; right: -100px; }
    .shape-2 { width: 300px; height: 300px; background: rgba(0, 26, 65, 0.1); bottom: -50px; left: -50px; }

    /* ── METRICS (Glass Bubbles) ── */
    .metric-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }
    .glass-metric {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 24px;
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        transition: all 0.3s ease;
    }
    .glass-metric:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 15px 40px rgba(0,0,0,0.06);
    }
    .metric-icon-box {
        width: 56px; height: 56px;
        border-radius: 18px;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px;
        background: #ffffff;
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    }
    .mi-orange { color: #FF5A1F; }
    .mi-blue { color: #001A41; }
    .metric-info h4 { font-size: 13px; font-weight: 700; color: #64748B; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
    .metric-info p { font-size: 26px; font-weight: 900; color: #001A41; margin: 0; }

    /* ── MONTH GRID ── */
    .date-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 30px;
        padding-top: 20px;
    }

    .glass-month-card {
        background: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        border-radius: 28px;
        width: 100%;
        min-height: 180px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        cursor: pointer;
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 10px 25px rgba(0,0,0,0.02);
    }

    .glass-month-card::before {
        content: '';
        position: absolute; inset: 0;
        border-radius: 28px;
        padding: 2px;
        background: linear-gradient(135deg, rgba(255,255,255,1), rgba(255,255,255,0.1));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        pointer-events: none;
    }

    .month-badge {
        position: absolute;
        top: 12px; right: 12px;
        background: #FF5A1F;
        color: #ffffff !important;
        font-size: 14px; font-weight: 900;
        padding: 5px 14px;
        border-radius: 14px;
        box-shadow: 0 4px 12px rgba(255, 90, 31, 0.3);
        z-index: 2;
    }

    .month-header {
        font-size: 14px;
        font-weight: 800;
        color: #FF5A1F;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 5px;
    }

    .month-name {
        font-size: 38px;
        font-weight: 950;
        color: #001A41;
        line-height: 1;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .year-label {
        font-size: 14px;
        font-weight: 700;
        color: #94A3B8;
    }

    /* ── TABS ── */
    .premium-tabs {
        display: inline-flex;
        background: rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(10px);
        padding: 5px;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.5);
        margin-bottom: 30px;
        position: relative;
        z-index: 10;
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
    .p-tab:hover { color: #001A41; }
    .p-tab.active {
        background: #ffffff;
        color: #FF5A1F;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    
    /* Past Styles */
    .past-card { filter: contrast(0.8) brightness(0.95); opacity: 0.85; }
    .past-badge { background: #64748B !important; box-shadow: none !important; }
    .past-header { color: #64748B !important; }

    /* Animations */
    @keyframes pulseOrange {
        0% { box-shadow: 0 0 0 0 rgba(255, 90, 31, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(255, 90, 31, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 90, 31, 0); }
    }
    .pulse-badge { animation: pulseOrange 2s infinite; }

    /* Hover */
    .glass-month-card:hover {
        transform: translateY(-10px) scale(1.02);
        background: rgba(255, 255, 255, 0.85);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
        border-color: rgba(255, 90, 31, 0.3);
    }

    /* ── HEADERS ── */
    .dash-section-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 25px;
    }
    .header-text h3 {
        font-size: 22px; font-weight: 900; color: #001A41; margin: 0;
        display: flex; align-items: center; gap: 12px;
    }
    .header-icon {
        width: 42px; height: 42px;
        background: #FF5A1F;
        color: white;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
        box-shadow: 0 4px 15px rgba(255, 90, 31, 0.3);
    }
    
    .btn-premium {
        background: #001A41;
        color: white !important;
        padding: 12px 24px;
        border-radius: 14px;
        font-weight: 700; font-size: 14px;
        text-decoration: none !important;
        transition: all 0.3s;
        display: flex; align-items: center; gap: 8px;
        box-shadow: 0 4px 15px rgba(0, 26, 65, 0.2);
    }
    .btn-premium:hover { transform: scale(1.03); background: #002b6b; }

    /* ── SWAL CUSTOM ── */
    .swal2-glass {
        background: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.4) !important;
        border-radius: 32px !important;
    }
    .swal2-title { color: #001A41 !important; font-weight: 900 !important; }
    .swal2-confirm { border-radius: 16px !important; background: #FF5A1F !important; padding: 12px 30px !important; font-weight: 800 !important; }

    /* ── ANIMATIONS ── */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .stagger { animation: fadeInUp 0.6s ease both; }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    {{-- Decorative Background Elements --}}
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    {{-- ── STATS ROW ── --}}
    <div class="metric-grid">
        <div class="glass-metric stagger" style="animation-delay: 0.1s">
            <div class="metric-icon-box mi-orange">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <div class="metric-info">
                <h4>Réservations</h4>
                <p>{{ number_format($reservationsEnCours->total(), 0, ',', ' ') }}</p>
            </div>
        </div>
        <div class="glass-metric stagger" style="animation-delay: 0.2s">
            <div class="metric-icon-box mi-blue">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="metric-info">
                <h4>Ventes Estimées</h4>
                <p>{{ number_format($reservationsEnCours->sum('montant'), 0, ',', ' ') }} <span style="font-size: 14px; opacity: 0.6">F</span></p>
            </div>
        </div>
    </div>

    {{-- ── TAB SELECTOR ── --}}
    <div class="premium-tabs stagger" style="animation-delay: 0.25s">
        <a href="{{ route('company.reservation.index', ['tab' => 'en-cours']) }}" 
           class="p-tab {{ $tab === 'en-cours' ? 'active' : '' }}">
           📦 En cours
        </a>
        <a href="{{ route('company.reservation.index', ['tab' => 'terminees']) }}" 
           class="p-tab {{ $tab === 'terminees' ? 'active' : '' }}">
           🏁 Embarquées
        </a>
    </div>

    {{-- ── ACTIONS HEADER ── --}}
    <div class="dash-section-header stagger" style="animation-delay: 0.3s">
        <div class="header-text">
            <h3>
                <div class="header-icon {{ $tab === 'terminees' ? 'past-badge' : '' }}">
                    <i class="fas {{ $tab === 'terminees' ? 'fa-history' : 'fa-calendar-day' }}"></i>
                </div>
                {{ $tab === 'terminees' ? 'Historique par mois' : 'Calendrier Mensuel' }}
            </h3>
        </div>
        <div class="header-actions">
            <a href="{{ route('company.reservation.details') }}" class="btn-premium">
                <i class="fas fa-chart-pie"></i> Statistiques Détaillées
            </a>
        </div>
    </div>

    @php
        $months = $reservationsEnCours->groupBy(fn($r) => \Carbon\Carbon::parse($r->date_voyage)->format('Y-m'));
        $monthsData = [];
        foreach($months as $monthKey => $mRes) {
            $dates = $mRes->groupBy(fn($r) => \Carbon\Carbon::parse($r->date_voyage)->toDateString());
            foreach($dates as $date => $dRes) {
                $monthsData[$monthKey][$date] = [
                    'formatted' => \Carbon\Carbon::parse($date)->translatedFormat('l d F Y'),
                    'hours' => $dRes->pluck('programme.heure_depart')->filter()->unique()->sort()->values()->all()
                ];
            }
        }
    @endphp

    {{-- ── MONTH GRID ── --}}
    <div class="date-grid">
        @forelse ($months as $monthKey => $reservations)
            @php $delay = (0.4 + ($loop->index * 0.05)); @endphp
            <div class="glass-month-card stagger {{ $tab === 'terminees' ? 'past-card' : '' }}" 
                 style="animation-delay: {{ $delay }}s"
                 onclick="window.location.href = '{{ route('company.reservation.by_month', ['month' => $monthKey]) }}?tab={{ $tab }}'">
                <div class="month-badge {{ $tab === 'terminees' ? 'past-badge' : 'pulse-badge' }}">{{ count($reservations) }}</div>
                <div class="month-header {{ $tab === 'terminees' ? 'past-header' : '' }}">{{ \Carbon\Carbon::parse($monthKey . '-01')->translatedFormat('F') }}</div>
                <div class="month-name">{{ \Carbon\Carbon::parse($monthKey . '-01')->translatedFormat('M') }}</div>
                <div class="year-label">{{ \Carbon\Carbon::parse($monthKey . '-01')->format('Y') }}</div>
            </div>
        @empty
            <div class="glass-metric col-12 text-center p-5 stagger">
                <p class="m-0 text-muted">Aucune réservation {{ $tab === 'terminees' ? 'archivée' : 'active' }} pour le moment.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-5 stagger" style="animation-delay: 1s">
        {{ $reservationsEnCours->links('pagination::bootstrap-4') }}
    </div>
</div>

<script>
// Le script a été simplifié pour utiliser un redirect direct au lieu d'un popup séquentiel
</script>
@endsection