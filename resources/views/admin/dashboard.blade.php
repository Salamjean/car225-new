@extends('admin.layouts.template')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto" style="max-width: 1800px;">

        <!-- Header -->
        <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Tableau de bord Administrateur</h1>
                <p class="text-gray-500 mt-1">Vue d'ensemble complète du système CAR225</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-gray-400">{{ now()->format('d/m/Y H:i') }}</span>
                <button onclick="window.location.reload()"
                    class="p-2 bg-white rounded-lg shadow-sm border border-gray-200 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-sync-alt text-gray-600"></i>
                </button>
            </div>
        </div>

        <!-- Row 1: Main Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Revenue Card -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 shadow-xl text-white transform hover:-translate-y-1 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-wallet text-2xl"></i>
                    </div>
                    @if($revenueVariation != 0)
                    <span class="px-2 py-1 bg-white/20 rounded-lg text-xs font-bold flex items-center gap-1">
                        <i class="fas fa-arrow-{{ $revenueVariation > 0 ? 'up' : 'down' }}"></i>
                        {{ abs($revenueVariation) }}%
                    </span>
                    @endif
                </div>
                <p class="text-sm font-bold text-white/80 uppercase tracking-wider">Revenus Totaux</p>
                <h3 class="text-3xl font-black mt-2">{{ number_format($totalRevenue, 0, ',', ' ') }} <span class="text-lg">FCFA</span></h3>
                <p class="mt-4 text-xs text-white/70 font-medium">Ce mois: {{ number_format($revenueThisMonth, 0, ',', ' ') }} FCFA</p>
            </div>

            <!-- Reservations Card -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 shadow-xl text-white transform hover:-translate-y-1 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-ticket-alt text-2xl"></i>
                    </div>
                    <span class="px-2 py-1 bg-white/20 rounded-lg text-xs font-bold">
                        {{ $reservationsConfirmees }} confirmées
                    </span>
                </div>
                <p class="text-sm font-bold text-white/80 uppercase tracking-wider">Réservations</p>
                <h3 class="text-3xl font-black mt-2">{{ $totalReservations }}</h3>
                <div class="mt-4 flex gap-3 text-xs text-white/70 font-medium">
                    <span><i class="fas fa-clock mr-1"></i>{{ $reservationsEnAttente }} en attente</span>
                    <span><i class="fas fa-check mr-1"></i>{{ $reservationsTerminees }} terminées</span>
                </div>
            </div>

            <!-- Companies Card -->
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-6 shadow-xl text-white transform hover:-translate-y-1 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-building text-2xl"></i>
                    </div>
                </div>
                <p class="text-sm font-bold text-white/80 uppercase tracking-wider">Compagnies</p>
                <h3 class="text-3xl font-black mt-2">{{ $totalCompagnies }}</h3>
                <div class="mt-4 flex gap-3 text-xs text-white/70 font-medium">
                    <span><i class="fas fa-bus mr-1"></i>{{ $totalVehicules }} véhicules</span>
                    <span><i class="fas fa-users mr-1"></i>{{ $totalAgents }} agents</span>
                </div>
            </div>

            <!-- Users Card -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 shadow-xl text-white transform hover:-translate-y-1 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                </div>
                <p class="text-sm font-bold text-white/80 uppercase tracking-wider">Utilisateurs</p>
                <h3 class="text-3xl font-black mt-2">{{ $totalUsers }}</h3>
                <div class="mt-4 flex gap-3 text-xs text-white/70 font-medium">
                    <span><i class="fas fa-id-badge mr-1"></i>{{ $totalPersonnel }} personnel</span>
                    <span><i class="fas fa-fire-extinguisher mr-1"></i>{{ $totalSapeurPompiers }} pompiers</span>
                </div>
            </div>
        </div>

        <!-- Row 2: Secondary Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
            <div class="bg-white rounded-xl p-4 shadow-md border border-gray-100 text-center">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-calendar-check text-green-600"></i>
                </div>
                <p class="text-2xl font-black text-gray-900">{{ $programmesAujourdhui }}</p>
                <p class="text-xs text-gray-500 font-medium">Programmes aujourd'hui</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-md border border-gray-100 text-center">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-route text-blue-600"></i>
                </div>
                <p class="text-2xl font-black text-gray-900">{{ $totalProgrammes }}</p>
                <p class="text-xs text-gray-500 font-medium">Programmes totaux</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-md border border-gray-100 text-center">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-bus text-emerald-600"></i>
                </div>
                <p class="text-2xl font-black text-gray-900">{{ $vehiculesActifs }}</p>
                <p class="text-xs text-gray-500 font-medium">Véhicules actifs</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-md border border-gray-100 text-center">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-ban text-red-600"></i>
                </div>
                <p class="text-2xl font-black text-gray-900">{{ $vehiculesInactifs }}</p>
                <p class="text-xs text-gray-500 font-medium">Véhicules inactifs</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-md border border-gray-100 text-center">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                </div>
                <p class="text-2xl font-black text-gray-900">{{ $signalementsNouveaux }}</p>
                <p class="text-xs text-gray-500 font-medium">Signalements actifs</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-md border border-gray-100 text-center">
                <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-check-double text-teal-600"></i>
                </div>
                <p class="text-2xl font-black text-gray-900">{{ $signalementsTraites }}</p>
                <p class="text-xs text-gray-500 font-medium">Signalements traités</p>
            </div>
        </div>

        <!-- Row 3: Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Revenue Chart -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gradient-to-r from-green-50 to-white">
                    <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">Évolution des revenus (7 jours)</h3>
                    <span class="text-xs text-gray-400 font-bold uppercase tracking-widest">FCFA / Jour</span>
                </div>
                <div class="p-8">
                    <canvas id="revenueChart" height="280"></canvas>
                </div>
            </div>

            <!-- Pie Chart -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 bg-gradient-to-r from-orange-50 to-white">
                    <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">Répartition par compagnie</h3>
                </div>
                <div class="p-6 flex items-center justify-center">
                    <canvas id="compagnieChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Row 4: Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Top Companies -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 bg-gradient-to-r from-blue-50 to-white">
                    <h3 class="text-lg font-black text-blue-900 uppercase tracking-tight">🏆 Top Compagnies</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-xs uppercase font-bold text-gray-500">
                            <tr>
                                <th class="px-6 py-3">#</th>
                                <th class="px-6 py-3">Compagnie</th>
                                <th class="px-6 py-3 text-right">Réservations</th>
                                <th class="px-6 py-3 text-right">Revenus</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">
                            @forelse($topCompagnies as $index => $company)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    @if($index == 0)
                                        <span class="text-2xl">🥇</span>
                                    @elseif($index == 1)
                                        <span class="text-2xl">🥈</span>
                                    @elseif($index == 2)
                                        <span class="text-2xl">🥉</span>
                                    @else
                                        <span class="text-gray-400 font-bold">{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900">{{ $company->name }}</td>
                                <td class="px-6 py-4 text-right">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-bold">{{ $company->total_reservations }}</span>
                                </td>
                                <td class="px-6 py-4 text-right font-black text-green-600">
                                    {{ number_format($company->total_revenue, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-400">Aucune donnée disponible</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Signalements -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden flex flex-col">
                <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gradient-to-r from-red-50 to-white">
                    <h3 class="text-lg font-black text-red-800 uppercase tracking-tight">⚠️ Signalements récents</h3>
                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-black">{{ $signalementsNouveaux }} actifs</span>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-4 max-h-80">
                    @forelse($recentSignalements as $sig)
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 hover:border-red-200 transition-all">
                        <div class="flex justify-between items-start mb-2">
                            <span class="px-2 py-0.5 bg-{{ $sig->type == 'accident' ? 'red' : 'yellow' }}-100 text-{{ $sig->type == 'accident' ? 'red' : 'yellow' }}-700 text-xs font-black uppercase rounded">{{ $sig->type }}</span>
                            <span class="text-xs text-gray-400 font-bold">{{ $sig->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-600 line-clamp-2 italic mb-2">"{{ Str::limit($sig->description, 80) }}"</p>
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-gray-900">{{ $sig->user->name ?? 'Anonyme' }}</span>
                            <span class="px-2 py-1 rounded {{ $sig->statut == 'traite' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }} font-bold">
                                {{ $sig->statut == 'traite' ? 'Traité' : 'En cours' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-10">
                        <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                        <p class="text-gray-400 font-bold">Aucun signalement récent</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Row 5: Recent Reservations -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gradient-to-r from-purple-50 to-white">
                <h3 class="text-lg font-black text-purple-900 uppercase tracking-tight">📋 Dernières réservations</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs uppercase font-bold text-gray-500 tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Référence</th>
                            <th class="px-6 py-4">Client</th>
                            <th class="px-6 py-4">Compagnie</th>
                            <th class="px-6 py-4">Trajet</th>
                            <th class="px-6 py-4 text-right">Montant</th>
                            <th class="px-6 py-4 text-center">Statut</th>
                            <th class="px-6 py-4">Date</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        @forelse($recentReservations as $res)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $res->reference }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $res->user->name ?? 'Inconnu' }}</div>
                                <div class="text-xs text-gray-400">{{ $res->passager_email ?? $res->user->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-red-600">{{ $res->programme->compagnie->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 text-gray-700">
                                    {{ $res->programme->point_depart ?? 'N/A' }}
                                    <i class="fas fa-arrow-right text-xs text-gray-300"></i>
                                    {{ $res->programme->point_arrive ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right font-black text-gray-900">
                                {{ number_format($res->montant, 0, ',', ' ') }} <span class="text-xs text-gray-400">FCFA</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusColors = [
                                        'confirmee' => 'bg-green-100 text-green-700',
                                        'en_attente' => 'bg-yellow-100 text-yellow-700',
                                        'annulee' => 'bg-red-100 text-red-700',
                                        'terminee' => 'bg-blue-100 text-blue-700',
                                    ];
                                    $statusLabels = [
                                        'confirmee' => 'Confirmée',
                                        'en_attente' => 'En attente',
                                        'annulee' => 'Annulée',
                                        'terminee' => 'Terminée',
                                    ];
                                @endphp
                                <span class="px-2 py-1 rounded-lg text-xs font-bold {{ $statusColors[$res->statut] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $statusLabels[$res->statut] ?? $res->statut }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $res->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400 font-medium">
                                Aucune réservation enregistrée
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Line Chart
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    const gradient = ctxRevenue.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(34, 197, 94, 0.4)');
    gradient.addColorStop(1, 'rgba(34, 197, 94, 0)');

    new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: {!! json_encode($days) !!},
            datasets: [{
                label: 'Revenus (FCFA)',
                data: {!! json_encode($revenuePerDay) !!},
                borderColor: '#22c55e',
                borderWidth: 4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#22c55e',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8,
                fill: true,
                backgroundColor: gradient,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: '#1f2937',
                    titleFont: { size: 12, weight: 'bold' },
                    bodyFont: { size: 14 },
                    padding: 12,
                    cornerRadius: 12,
                    callbacks: {
                        label: ctx => ctx.parsed.y.toLocaleString('fr-FR') + ' FCFA'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.03)' },
                    ticks: {
                        font: { weight: 'bold', size: 10 },
                        callback: v => v.toLocaleString('fr-FR') + ' CFA'
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { weight: 'bold', size: 10 } }
                }
            }
        }
    });

    // Pie Chart for Companies
    const ctxPie = document.getElementById('compagnieChart').getContext('2d');
    const pieColors = ['#e94f1b', '#3b82f6', '#22c55e', '#a855f7', '#ef4444', '#06b6d4'];
    
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($compagnieLabels) !!},
            datasets: [{
                data: {!! json_encode($compagnieCounts) !!},
                backgroundColor: pieColors,
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        font: { size: 11, weight: 'bold' }
                    }
                }
            }
        }
    });
});
</script>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .overflow-y-auto::-webkit-scrollbar {
        width: 4px;
    }
    .overflow-y-auto::-webkit-scrollbar-track {
        background: transparent;
    }
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 10px;
    }
</style>
@endsection