@extends('compagnie.layouts.template')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto" style="width: 95%;">

            <!-- Header -->
            <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Tableau de bord</h1>
                    <p class="text-gray-500 mt-1">Bienvenue dans votre espace partenaire CAR225.</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-gray-400">Dernière mise à jour: Aujourd'hui à
                        {{ now()->format('H:i') }}</span>
                    <button onclick="window.location.reload()"
                        class="p-2 bg-white rounded-lg shadow-sm border border-gray-200 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-sync-alt text-gray-600"></i>
                    </button>
                </div>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-10">
                <!-- Revenue Card -->
                <div
                    class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100 transform hover:-translate-y-1 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-wallet text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Soldes</p>
                    <h3 class="text-2xl font-black text-gray-900 mt-2">{{ number_format($totalRevenue, 0, ',', ' ') }} FCFA
                    </h3>
                    <div class="mt-4 flex items-center text-xs text-green-600 font-bold">
                        <i class="fas fa-arrow-up mr-1"></i> Global
                    </div>
                </div>

                <!-- Reservations Card -->
                <div
                    class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100 transform hover:-translate-y-1 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-ticket-alt text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Réservations Totales</p>
                    <h3 class="text-2xl font-black text-gray-900 mt-2">{{ $totalReservations }}</h3>
                    <p class="mt-4 text-xs text-blue-600 font-bold">Total cumulé</p>
                </div>

                <!-- Vehicles Card -->
                <div
                    class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100 transform hover:-translate-y-1 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-bus text-red-600 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Flotte Véhicules</p>
                    <h3 class="text-2xl font-black text-gray-900 mt-2">{{ $totalVehicles }}</h3>
                    <p class="mt-4 text-xs text-red-600 font-bold">Véhicules actifs</p>
                </div>

                <!-- Signalements Card -->
                <div
                    class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100 transform hover:-translate-y-1 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600 font-bold">
                            <i class="fas fa-exclamation-triangle text-xl"></i>
                        </div>
                    </div>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Incidents Signalés</p>
                    <h3 class="text-2xl font-black text-gray-900 mt-2">{{ $totalSignalements }}</h3>
                    <p class="mt-4 text-xs text-amber-600 font-bold">Nécessitant attention</p>
                </div>

                <!-- Balance Card -->
                <div class="bg-white rounded-2xl p-6 shadow-xl border border-purple-100 transform hover:-translate-y-1 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600 font-bold">
                            <i class="fas fa-wallet text-xl"></i>
                        </div>
                    </div>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Solde Compagnie</p>
                    <h3 class="text-2xl font-black text-purple-900 mt-2">
                        {{ number_format(Auth::guard('compagnie')->user()->tickets, 0, ',', ' ') }} FCFA
                    </h3>
                    <p class="mt-4 text-xs text-purple-600 font-bold">Crédit disponible pour les réservations</p>
                </div>
            </div>


            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
                <!-- Chart Section -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div
                        class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">Évolution des revenus (7j)
                        </h3>
                        <span class="text-xs text-gray-400 font-bold uppercase tracking-widest">FCFA / Jour</span>
                    </div>
                    <div class="p-8">
                        <canvas id="revenueChart" height="300"></canvas>
                    </div>
                </div>

                <!-- Recent Signalements Section -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden flex flex-col">
                    <div
                        class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gradient-to-r from-red-50 to-white">
                        <h3 class="text-lg font-black text-red-800 uppercase tracking-tight">Signalements récents</h3>
                        <a href="{{ route('compagnie.signalements.index') }}"
                            class="text-xs text-red-600 font-extrabold hover:underline">VOIR TOUT</a>
                    </div>
                    <div class="flex-1 overflow-y-auto p-4 space-y-4">
                        @forelse($recentSignalements as $sig)
                            <div
                                class="bg-gray-50 p-4 rounded-xl border border-gray-100 hover:border-red-200 transition-all group">
                                <div class="flex justify-between items-start mb-2">
                                    <span
                                        class="px-2 py-0.5 bg-red-100 text-red-700 text-[10px] font-black uppercase rounded">{{ $sig->type }}</span>
                                    <span
                                        class="text-[10px] text-gray-400 font-bold uppercase">{{ $sig->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-gray-600 line-clamp-2 italic mb-2">"{{ $sig->description }}"</p>
                                <div class="flex items-center justify-between mt-3 text-[10px]">
                                    <span class="font-bold text-gray-900 text-xs">{{ $sig->user->name ?? 'Inconnu' }}</span>
                                    <a href="{{ route('compagnie.signalements.show', $sig->id) }}"
                                        class="text-blue-600 font-black hover:underline">DÉTAILS</a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 flex flex-col items-center justify-center">
                                <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                                <p class="text-xs text-gray-400 font-bold uppercase">Aucun incident à signaler</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Reservations Table -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div
                    class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gradient-to-r from-blue-50 to-white">
                    <h3 class="text-lg font-black text-blue-900 uppercase tracking-tight">Dernières réservations</h3>
                    <a href="{{ route('company.reservation.index') }}"
                        class="text-xs text-blue-600 font-extrabold hover:underline">GERER LES RESERVATIONS</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-[10px] uppercase font-black text-gray-500 tracking-widest">
                            <tr>
                                <th class="px-8 py-4 text-center">Client</th>
                                <th class="px-8 py-4 text-center">Trajet</th>
                                <th class="px-8 py-4 text-center">Montant</th>
                                <th class="px-8 py-4 text-center">Places</th>
                                <th class="px-8 py-4 text-center">Date</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">
                            @forelse($recentReservations as $res)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-8 py-4 text-center">
                                        <div class="font-bold text-gray-900">{{ $res->user->name ?? 'Inconnu' }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">
                                            {{ $res->user->telephone ?? '' }}</div>
                                    </td>
                                    <td class="px-8 py-4 text-center" style="display: flex; align-items: center; justify-content: center;">
                                        <div class="font-bold text-gray-700 flex items-center gap-2">
                                            {{ $res->programme->point_depart }} <i
                                                class="fas fa-arrow-right text-[10px] text-gray-300"></i>
                                            {{ $res->programme->point_arrive }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-4 text-center">
                                        <span
                                            class="font-black text-gray-900">{{ number_format($res->montant, 0, ',', ' ') }}
                                            <span class="text-[10px]">CFA</span></span>
                                    </td>
                                    <td class="px-8 py-4 text-center">
                                        <span
                                            class="px-2 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-black">{{ $res->nombre_places }}</span>
                                    </td>
                                    <td class="px-8 py-4 text-center">
                                        {{ $res->created_at->format('d/m/Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-10 text-center text-gray-400 font-bold uppercase italic">
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
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('revenueChart').getContext('2d');

            // Gradient for line
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(34, 197, 94, 0.4)');
            gradient.addColorStop(1, 'rgba(34, 197, 94, 0)');

            new Chart(ctx, {
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
                                label: function (context) {
                                    return context.parsed.y.toLocaleString('fr-FR') + ' FCFA';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.03)' },
                            ticks: {
                                font: { weight: 'bold', size: 10 },
                                callback: value => value.toLocaleString('fr-FR') + ' CFA'
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { weight: 'bold', size: 10 } }
                        }
                    }
                }
            });
        });

    </script>

    <style>
        /* Custom scrollbar for recent incidents list if needed */
        .overflow-y-auto::-webkit-scrollbar {
            width: 4px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: transparent;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #eee;
            border-radius: 10px;
        }
    </style>
@endsection