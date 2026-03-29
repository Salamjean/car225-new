@extends('compagnie.layouts.template')

@section('page-title', 'Tableau de bord')
@section('page-subtitle', 'Vue d\'ensemble de votre activité')

@section('content')
<div class="dashboard-page">

    {{-- ── PAGE HEADER ── --}}
    <div class="dash-header">
        <div>
            <h1 class="dash-title">
                Bonjour, {{ Auth::guard('compagnie')->user()->sigle }} 👋
            </h1>
            <p class="dash-subtitle">
                Suivez l'évolution de vos activités en temps réel.
            </p>
        </div>
        <div class="dash-live-badge">
            <span class="dash-live-dot"></span>
            Mis à jour à <span id="dashTime">{{ now()->format('H:i') }}</span>
            <div class="dash-refresh-divider"></div>
            <button onclick="window.location.reload()" class="dash-refresh-btn" title="Actualiser">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>

    {{-- ── BANNIÈRE VOYAGES EN COURS ── --}}
    @if(isset($liveVoyagesCount) && $liveVoyagesCount > 0)
    <a href="{{ route('compagnie.tracking.index') }}" style="display:flex;align-items:center;gap:16px;background:linear-gradient(135deg,#065f46,#10b981);border-radius:14px;padding:16px 22px;color:white;text-decoration:none;margin-bottom:20px;box-shadow:0 8px 24px rgba(16,185,129,0.2);transition:opacity 0.15s;" onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
        <div style="width:38px;height:38px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fas fa-satellite-dish" style="animation:pulse 2s infinite;"></i>
        </div>
        <div style="flex:1;min-width:0;">
            <div style="font-size:14px;font-weight:800;margin:0;">{{ $liveVoyagesCount }} voyage{{ $liveVoyagesCount > 1 ? 's' : '' }} en cours — Voir sur la carte</div>
            <div style="font-size:12px;opacity:0.85;margin-top:2px;">Suivez vos chauffeurs en temps réel avec tracé GPS et itinéraire</div>
        </div>
        <div style="display:flex;align-items:center;gap:6px;background:rgba(255,255,255,0.2);padding:6px 14px;border-radius:999px;font-size:12px;font-weight:700;flex-shrink:0;">
            <span style="width:7px;height:7px;background:#f87171;border-radius:50%;display:inline-block;animation:ping 1s infinite;"></span>
            LIVE <i class="fas fa-arrow-right" style="margin-left:4px;font-size:11px;"></i>
        </div>
    </a>
    @endif

    {{-- ── METRIC CARDS ── --}}
    <div class="metric-grid">

        {{-- Solde Total --}}
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-green"><i class="fas fa-wallet"></i></div>
                <span class="metric-tag mt-green">Global</span>
            </div>
            <p class="metric-label">Soldes Total</p>
            <h3 class="metric-value">
                {{ number_format($totalRevenue, 0, ',', ' ') }}
                <span class="metric-unit">CFA</span>
            </h3>
        </div>

        {{-- Réservations --}}
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-blue"><i class="fas fa-ticket-alt"></i></div>
                <span class="metric-tag mt-slate">Cumul</span>
            </div>
            <p class="metric-label">Réservations</p>
            <h3 class="metric-value">{{ $totalReservations }}</h3>
        </div>

        {{-- Flotte --}}
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-rose"><i class="fas fa-bus"></i></div>
                <span class="metric-tag mt-rose">Actifs</span>
            </div>
            <p class="metric-label">Flotte Véhicules</p>
            <h3 class="metric-value">{{ $totalVehicles }}</h3>
        </div>

        {{-- Incidents --}}
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-amber"><i class="fas fa-exclamation-triangle"></i></div>
                <span class="metric-tag mt-amber">Alertes</span>
            </div>
            <p class="metric-label">Incidents</p>
            <h3 class="metric-value">{{ $totalSignalements }}</h3>
        </div>

        {{-- Solde Compagnie (featured) --}}
        <div class="metric-card metric-featured">
            <div class="metric-top">
                <div class="metric-icon mi-white"><i class="fas fa-coins"></i></div>
                <span class="metric-tag mt-white">Crédit</span>
            </div>
            <p class="metric-label">Solde Compagnie</p>
            <h3 class="metric-value">
                {{ number_format(Auth::guard('compagnie')->user()->tickets, 0, ',', ' ') }}
                <span class="metric-unit">CFA</span>
            </h3>
        </div>

    </div>

    {{-- ── CHARTS + SIGNALEMENTS ── --}}
    <div class="dash-charts-row">

        {{-- Graphique revenus --}}
        <div class="dash-card">
            <div class="dash-card-head">
                <div class="dash-card-head-left">
                    <div class="dash-card-icon dci-green"><i class="fas fa-chart-line"></i></div>
                    <h3 class="dash-card-title">Évolution des revenus</h3>
                </div>
                <span class="dash-card-tag">7 derniers jours</span>
            </div>
            <div class="dash-chart-body">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Signalements --}}
        <div class="dash-card dash-card-column">
            <div class="dash-card-head">
                <div class="dash-card-head-left">
                    <div class="dash-card-icon dci-red"><i class="fas fa-bell"></i></div>
                    <h3 class="dash-card-title">Signalements</h3>
                </div>
                <a href="{{ route('compagnie.signalements.index') }}" class="dash-card-action">
                    Tout voir
                </a>
            </div>

            <div class="sig-list">
                @forelse($recentSignalements as $sig)
                    <a href="{{ route('compagnie.signalements.show', $sig->id) }}"
                       class="sig-item {{ !$sig->is_read_by_company ? 'sig-unread' : '' }}">
                        <div class="sig-item-top">
                            <span class="sig-type-badge">{{ $sig->type }}</span>
                            @if(!$sig->is_read_by_company)
                                <span class="sig-dot"></span>
                            @endif
                            <span class="sig-time">{{ $sig->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="sig-desc">{{ $sig->description }}</p>
                        <div class="sig-author">
                            <div class="sig-avatar">
                                {{ strtoupper(substr($sig->user?->name ?? 'I', 0, 1)) }}
                            </div>
                            <span class="sig-author-name">{{ $sig->user?->name ?? 'Inconnu' }}</span>
                        </div>
                    </a>
                @empty
                    <div class="sig-empty">
                        <div class="sig-empty-icon"><i class="fas fa-check"></i></div>
                        <p class="sig-empty-title">Tout est calme</p>
                        <p class="sig-empty-sub">Aucun incident signalé récemment.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ── TABLEAU RÉSERVATIONS ── --}}
    <div class="dash-card">
        <div class="dash-card-head">
            <div class="dash-card-head-left">
                <div class="dash-card-icon dci-blue"><i class="fas fa-ticket-alt"></i></div>
                <h3 class="dash-card-title">Dernières Réservations</h3>
            </div>
            <a href="{{ route('company.reservation.index') }}" class="dash-card-action">
                Gestion complète
            </a>
        </div>
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Trajet</th>
                        <th class="text-right">Montant</th>
                        <th class="text-center">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentReservations as $res)
                        <tr>
                            <td>
                                <div class="td-user">
                                    <div class="td-avatar">
                                        {{ strtoupper(substr($res->user?->name ?? 'I', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="td-name">{{ $res->user?->name ?? 'Inconnu' }}</div>
                                        <div class="td-phone">{{ $res->user?->telephone ?? '---' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="route-pill">
                                    <span>{{ $res->programme->point_depart }}</span>
                                    <i class="fas fa-arrow-right route-arrow"></i>
                                    <span>{{ $res->programme->point_arrive }}</span>
                                    <span class="route-time-badge">
                                        {{ substr($res->programme->heure_depart, 0, 5) }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-right">
                                <span class="td-amount">
                                    {{ number_format($res->montant, 0, ',', ' ') }}
                                    <span class="td-unit">CFA</span>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="td-date">{{ $res->created_at->format('d/m/Y') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="table-empty">
                                    <i class="fas fa-inbox table-empty-icon"></i>
                                    <p>Aucune réservation trouvée</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Horloge
    (function() {
        const el = document.getElementById('dashTime');
        if (!el) return;
        setInterval(() => {
            const n = new Date();
            el.textContent = n.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        }, 60000);
    })();

    // Graphique
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 280);
        gradient.addColorStop(0, 'rgba(249,115,22,0.18)');
        gradient.addColorStop(1, 'rgba(249,115,22,0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($days) !!},
                datasets: [{
                    label: 'Revenus',
                    data: {!! json_encode($revenuePerDay) !!},
                    borderColor: '#F97316',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#F97316',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#F97316',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 2,
                    fill: true,
                    backgroundColor: gradient,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(26,23,20,0.92)',
                        titleFont: { size: 12, family: "'Plus Jakarta Sans', sans-serif", weight: '600' },
                        bodyFont: { size: 13, weight: 'bold', family: "'Plus Jakarta Sans', sans-serif" },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: c => c.parsed.y.toLocaleString('fr-FR') + ' CFA'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: { color: 'rgba(0,0,0,0.05)', drawTicks: false },
                        ticks: {
                            font: { weight: '600', size: 11, family: "'Plus Jakarta Sans', sans-serif" },
                            color: '#A09A94',
                            padding: 10,
                            callback: v => {
                                if (v >= 1000000) return (v / 1000000).toFixed(1) + 'M';
                                if (v >= 1000)    return (v / 1000).toFixed(0) + 'k';
                                return v;
                            }
                        }
                    },
                    x: {
                        border: { display: false },
                        grid: { display: false },
                        ticks: {
                            font: { weight: '600', size: 11, family: "'Plus Jakarta Sans', sans-serif" },
                            color: '#A09A94',
                            padding: 10
                        }
                    }
                }
            }
        });
    });
</script>
@endsection