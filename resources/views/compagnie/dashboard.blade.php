@extends('compagnie.layouts.template')

@section('content')
    <div class="min-h-screen bg-slate-50 py-8 px-4 sm:px-6 lg:px-8 font-sans">
        <div class="mx-auto space-y-8" style="width:100%">

            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-black text-slate-900 tracking-tight">Tableau de bord</h1>
                    <p class="text-slate-500 mt-2 text-sm font-medium">Bienvenue dans votre espace partenaire, suivez l'évolution de vos activités.</p>
                </div>
                <div class="flex items-center gap-4 bg-white px-4 py-2 rounded-2xl shadow-sm border border-slate-200/60">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Mise à jour: {{ now()->format('H:i') }}</span>
                    </div>
                    <div class="w-px h-4 bg-slate-200"></div>
                    <button onclick="window.location.reload()" class="text-slate-400 hover:text-emerald-600 transition-colors" title="Actualiser">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>

            <!-- Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                <!-- Solde Global -->
                <div class="bg-white rounded-[1.5rem] p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/60 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-emerald-50 to-emerald-100/50 rounded-full blur-2xl -mr-8 -mt-8 transition-transform group-hover:scale-150"></div>
                    <div class="relative z-10 flex flex-col h-full justify-between">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center shadow-sm border border-emerald-100/50">
                                <i class="fas fa-wallet text-xl"></i>
                            </div>
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-black tracking-widest uppercase rounded-lg">Global</span>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Soldes Total</p>
                            <h3 class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($totalRevenue, 0, ',', ' ') }} <span class="text-sm text-slate-500 font-bold">CFA</span></h3>
                        </div>
                    </div>
                </div>

                <!-- Réservations -->
                <div class="bg-white rounded-[1.5rem] p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/60 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-50 to-blue-100/50 rounded-full blur-2xl -mr-8 -mt-8 transition-transform group-hover:scale-150"></div>
                    <div class="relative z-10 flex flex-col h-full justify-between">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center shadow-sm border border-blue-100/50">
                                <i class="fas fa-ticket-alt text-xl"></i>
                            </div>
                            <span class="px-2.5 py-1 bg-slate-50 text-slate-500 text-[10px] font-black tracking-widest uppercase rounded-lg">Cumul</span>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Réservations</p>
                            <h3 class="text-2xl font-black text-slate-900 tracking-tight">{{ $totalReservations }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Flotte -->
                <div class="bg-white rounded-[1.5rem] p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/60 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-rose-50 to-rose-100/50 rounded-full blur-2xl -mr-8 -mt-8 transition-transform group-hover:scale-150"></div>
                    <div class="relative z-10 flex flex-col h-full justify-between">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center shadow-sm border border-rose-100/50">
                                <i class="fas fa-bus text-xl"></i>
                            </div>
                            <span class="px-2.5 py-1 bg-rose-50 text-rose-700 text-[10px] font-black tracking-widest uppercase rounded-lg">Actifs</span>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Flotte Véhicules</p>
                            <h3 class="text-2xl font-black text-slate-900 tracking-tight">{{ $totalVehicles }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Signalements -->
                <div class="bg-white rounded-[1.5rem] p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/60 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-amber-50 to-amber-100/50 rounded-full blur-2xl -mr-8 -mt-8 transition-transform group-hover:scale-150"></div>
                    <div class="relative z-10 flex flex-col h-full justify-between">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center shadow-sm border border-amber-100/50">
                                <i class="fas fa-exclamation-triangle text-xl"></i>
                            </div>
                            <span class="px-2.5 py-1 bg-amber-50 text-amber-700 text-[10px] font-black tracking-widest uppercase rounded-lg">Alertes</span>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Incidents</p>
                            <h3 class="text-2xl font-black text-slate-900 tracking-tight">{{ $totalSignalements }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Solde Compagnie -->
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-[1.5rem] p-6 shadow-[0_8px_30px_rgb(249,115,22,0.3)] border border-orange-400 hover:shadow-[0_8px_30px_rgb(249,115,22,0.4)] transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-6 -left-6 w-24 h-24 bg-black/10 rounded-full blur-2xl"></div>
                    
                    <div class="relative z-10 flex flex-col h-full justify-between">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-white/20 text-white rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                                <i class="fas fa-coins text-xl"></i>
                            </div>
                            <span class="px-2.5 py-1 bg-white/20 text-white text-[10px] font-black tracking-widest uppercase rounded-lg backdrop-blur-sm border border-white/20">Crédit</span>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-orange-100 uppercase tracking-widest mb-1">Solde Compagnie</p>
                            <h3 class="text-2xl font-black text-white tracking-tight">
                                {{ number_format(Auth::guard('compagnie')->user()->tickets, 0, ',', ' ') }} <span class="text-sm text-orange-200 font-bold">CFA</span>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts & Lists row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Chart -->
                <div class="lg:col-span-2 bg-white rounded-[1.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/60 overflow-hidden flex flex-col">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between z-10">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-500 border border-emerald-100">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3 class="text-base font-black text-slate-900 tracking-tight uppercase">Évolution des revenus</h3>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 bg-slate-50 text-slate-500 rounded-lg text-[10px] font-black tracking-widest uppercase">7 Derniers jours</span>
                        </div>
                    </div>
                    <div class="p-6 flex-1 min-h-[300px] relative">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Incidents List -->
                <div class="bg-white rounded-[1.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/60 overflow-hidden flex flex-col lg:h-auto h-[400px]">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-white z-10 shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-red-500 border border-red-100">
                                <i class="fas fa-bell"></i>
                            </div>
                            <h3 class="text-base font-black text-slate-900 tracking-tight uppercase">Signalements</h3>
                        </div>
                        <a href="{{ route('compagnie.signalements.index') }}" class="text-[10px] text-red-600 font-black hover:text-red-800 tracking-wider uppercase transition-colors">Tout voir</a>
                    </div>
                    <div class="flex-1 overflow-y-auto p-2 space-y-1 custom-scrollbar">
                        @forelse($recentSignalements as $sig)
                            <a href="{{ route('compagnie.signalements.show', $sig->id) }}" class="block p-4 rounded-xl {{ !$sig->is_read_by_company ? 'bg-red-50/50 border border-red-100' : 'hover:bg-slate-50' }} transition-colors group">
                                <div class="flex justify-between items-start mb-2 gap-4">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 bg-red-50 text-red-600 text-[10px] font-black uppercase tracking-wider rounded border border-red-100 shrink-0">{{ $sig->type }}</span>
                                        @if(!$sig->is_read_by_company)
                                            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                                        @endif
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-bold tracking-tight shrink-0">{{ $sig->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-slate-600 font-medium line-clamp-2 leading-relaxed mb-3 group-hover:text-slate-900 transition-colors">"{{ $sig->description }}"</p>
                                <div class="flex items-center gap-2">
                                    <div class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-500">
                                        {{ substr($sig->user?->name ?? 'I', 0, 1) }}
                                    </div>
                                    <span class="font-bold text-slate-700 text-xs">{{ $sig->user?->name ?? 'Inconnu' }}</span>
                                </div>
                            </a>
                        @empty
                            <div class="h-full flex flex-col items-center justify-center text-center px-4 py-8">
                                <div class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-check text-2xl"></i>
                                </div>
                                <h4 class="text-sm font-black text-slate-900 mb-1">Tout est calme</h4>
                                <p class="text-xs text-slate-500 font-medium">Aucun incident n'a été signalé récemment.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Reservations -->
            <div class="bg-white rounded-[1.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/60 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-white">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500 border border-blue-100">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <h3 class="text-base font-black text-slate-900 tracking-tight uppercase">Dernières réservations</h3>
                    </div>
                    <a href="{{ route('company.reservation.index') }}" class="text-[10px] text-blue-600 font-black hover:text-blue-800 tracking-wider uppercase transition-colors">Gestion complète</a>
                </div>
                <div class="overflow-x-auto relative">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Client</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Trajet</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap text-right">Montant</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap text-center">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100/60">
                            @forelse($recentReservations as $res)
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-black text-slate-600">
                                                {{ substr($res->user?->name ?? 'I', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 text-sm group-hover:text-blue-600 transition-colors">{{ $res->user?->name ?? 'Inconnu' }}</div>
                                                <div class="text-[11px] text-slate-400 font-medium">{{ $res->user?->telephone ?? '---' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3 bg-slate-50 w-fit px-3 py-1.5 rounded-lg border border-slate-100 group-hover:bg-white group-hover:shadow-sm transition-all">
                                            <span class="font-bold text-slate-700 text-xs">{{ $res->programme->point_depart }}</span>
                                            <i class="fas fa-arrow-right text-[10px] text-slate-300"></i>
                                            <span class="font-bold text-slate-700 text-xs">{{ $res->programme->point_arrive }}</span>
                                            <div class="w-px h-3 bg-slate-200 mx-1"></div>
                                            <span class="text-[10px] font-black text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">{{ substr($res->programme->heure_depart, 0, 5) }}</span>
                                            <div class="w-px h-3 bg-slate-200 mx-1"></div>
                                            <span class="text-[10px] font-black bg-white px-2 py-0.5 rounded shadow-sm text-slate-600">{{ $res->nombre_places }} pl</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="font-black text-slate-900 text-sm">
                                            {{ number_format($res->montant, 0, ',', ' ') }} <span class="text-[10px] text-slate-400">CFA</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="font-bold text-slate-500 text-xs">{{ $res->created_at->format('d/m/Y') }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 text-slate-400 mb-3">
                                            <i class="fas fa-inbox text-xl"></i>
                                        </div>
                                        <p class="text-sm font-bold text-slate-500">Aucune réservation trouvée</p>
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
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('revenueChart').getContext('2d');

            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)'); // emerald-500 transparent
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($days) !!},
                    datasets: [{
                        label: 'Revenus',
                        data: {!! json_encode($revenuePerDay) !!},
                        borderColor: '#10b981', // emerald-500
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#10b981',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#10b981',
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
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.95)',
                            titleFont: { size: 13, family: "'Inter', sans-serif" },
                            bodyFont: { size: 14, weight: 'bold', family: "'Inter', sans-serif" },
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function (context) {
                                    return context.parsed.y.toLocaleString('fr-FR') + ' CFA';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            border: { display: false },
                            grid: { 
                                color: 'rgba(241, 245, 249, 1)',
                                drawTicks: false,
                            },
                            ticks: {
                                font: { weight: '600', size: 11, family: "'Inter', sans-serif" },
                                color: '#94a3b8',
                                padding: 10,
                                callback: function(value) {
                                    if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                                    if (value >= 1000) return (value / 1000).toFixed(0) + 'k';
                                    return value;
                                }
                            }
                        },
                        x: {
                            border: { display: false },
                            grid: { display: false },
                            ticks: { 
                                font: { weight: '600', size: 11, family: "'Inter', sans-serif" },
                                color: '#94a3b8',
                                padding: 10
                            }
                        }
                    }
                }
            });
        });
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        
        .font-sans {
            font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 4px;
        }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background: #cbd5e1;
        }
        
        .min-h-\[300px\] {
            min-height: 300px;
        }
    </style>
@endsection