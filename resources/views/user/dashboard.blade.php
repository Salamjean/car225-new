@extends('user.layouts.template')

@section('content')
            <!-- Welcome Header -->
            <div class="mb-10">
                <h1 class="text-3xl font-black text-[#1A1D1F] tracking-tight mb-1 font-outfit uppercase">Tableau de bord</h1>
                <p class="text-gray-500 text-sm font-medium">Bon retour, <span class="text-[#e94f1b] font-bold">{{ $user->name }}</span>. Voici un aperçu de vos activités.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Main Content Left (8 cols) -->
                <div class="lg:col-span-8 space-y-8">
                    
                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <!-- Total Reservations Card -->
                        <div class="bg-white rounded-[32px] p-7 border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600">
                                    <i class="fas fa-history text-sm"></i>
                                </div>
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Réservations</span>
                            </div>
                            <div class="flex items-baseline gap-2">
                                <h3 class="text-3xl font-black text-gray-900">{{ $totalReservations }}</h3>
                                <span class="text-xs font-bold text-purple-600">Historique</span>
                            </div>
                            <p class="mt-4 text-[11px] font-bold text-gray-400 uppercase">Billets confirmés</p>
                        </div>

                        <!-- Active Trips Card -->
                        <div class="bg-white rounded-[32px] p-7 border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-green-600">
                                    <i class="fas fa-bus-alt text-sm"></i>
                                </div>
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Voyages Actifs</span>
                            </div>
                            <div class="flex items-baseline gap-2">
                                <h3 class="text-3xl font-black text-gray-900">{{ $activeReservations }}</h3>
                                <span class="text-xs font-bold text-green-600">+12%</span>
                            </div>
                            <p class="mt-4 text-[11px] font-bold text-gray-400 uppercase">Prochains départs gérés</p>
                        </div>

                        <!-- Total Spent Card -->
                        <div class="bg-white rounded-[32px] p-7 border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                                    <i class="fas fa-chart-line text-sm"></i>
                                </div>
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Dépenses Totales</span>
                            </div>
                            <div class="flex items-baseline gap-2">
                                <h3 class="text-2xl font-black text-gray-900">{{ number_format($totalSpent, 0, ',', ' ') }}</h3>
                                <span class="text-[10px] font-bold text-blue-600 uppercase">CFA</span>
                            </div>
                            <p class="mt-4 text-[11px] font-bold text-gray-400 uppercase">Total cumulé</p>
                        </div>
                    </div>

                    <!-- Chart Section -->
                    <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Analyse de vos voyages</h3>
                                <p class="text-xs text-gray-500 font-medium">Fréquence de réservation sur les 6 derniers mois</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button class="px-4 py-2 bg-gray-50 text-gray-600 text-[10px] font-bold rounded-lg border border-gray-100 uppercase tracking-wider">Par Mois</button>
                            </div>
                        </div>
                        <div class="h-64 relative">
                            <canvas id="userTravelChart"></canvas>
                        </div>
                    </div>

                    <!-- Recent Reservations Table/List alternative style -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900 tracking-tight">Activités Récentes</h3>
                            <a href="{{ route('reservation.index') }}" class="text-[11px] font-bold text-[#e94f1b] hover:underline uppercase tracking-widest">Tout voir</a>
                        </div>
                        <div class="grid grid-cols-1 gap-4">
                            @forelse($recentReservations as $res)
                                <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:shadow-md transition-all group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center border border-gray-100 group-hover:bg-[#e94f1b]/5 transition-colors">
                                            @if($res->programme->compagnie->path_logo)
                                                <img src="{{ asset('storage/' . $res->programme->compagnie->path_logo) }}" class="w-8 h-8 object-contain" alt="Logo">
                                            @else
                                                <i class="fas fa-bus text-[#e94f1b] text-lg"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 text-sm leading-tight">{{ $res->programme->point_depart }} &rarr; {{ $res->programme->point_arrive }}</h4>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-[10px] font-medium text-gray-500">{{ \Carbon\Carbon::parse($res->date_voyage)->isoFormat('LL') }}</span>
                                                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                                <span class="text-[10px] font-bold text-[#e94f1b] uppercase tracking-tighter">{{ $res->programme->compagnie->name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto mt-2 sm:mt-0 pt-3 sm:pt-0 border-t sm:border-0 border-gray-50">
                                        <div class="text-left sm:text-right">
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Montant</p>
                                            <p class="text-sm font-black text-gray-900">{{ number_format($res->montant, 0, ',', ' ') }} <span class="text-[9px]">CFA</span></p>
                                        </div>
                                        <a href="{{ route('reservations.show', $res->id) }}" class="p-2.5 bg-gray-50 text-gray-400 rounded-xl hover:bg-[#e94f1b] hover:text-white transition-all">
                                            <i class="fas fa-chevron-right text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="bg-white rounded-3xl p-10 text-center border-2 border-dashed border-gray-100">
                                    <p class="text-sm text-gray-400 font-medium">Aucun voyage récent.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Sidebar Right (4 cols) -->
                <div class="lg:col-span-4 space-y-8">
                    
                    <!-- Balance Card (Now in Sidebar) -->
                    <div class="bg-gradient-to-br from-[#e94f1b] to-[#ff7a4d] rounded-[32px] p-7 shadow-2xl shadow-[#e94f1b]/20 text-white relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all"></div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md">
                                    <i class="fas fa-wallet text-sm"></i>
                                </div>
                                <span class="text-xs font-bold uppercase tracking-wider opacity-80">SOLDE PORTEFEUILLE CAR225</span>
                            </div>
                            <div class="flex items-baseline gap-2">
                                <h3 class="text-3xl font-black">{{ number_format($user->solde, 0, ',', ' ') }}</h3>
                                <span class="text-sm font-bold opacity-70 uppercase tracking-tighter">CFA</span>
                            </div>
                            <div class="mt-8 grid grid-cols-2 gap-3">
                                <a href="{{ route('user.wallet.index') }}" class="flex items-center justify-center gap-2 text-[10px] font-bold bg-white text-[#e94f1b] px-4 py-3 rounded-2xl hover:bg-gray-50 transition-all uppercase tracking-tight">
                                    <i class="fas fa-plus-circle"></i> Recharger
                                </a>
                                <a href="{{ route('reservation.create') }}" class="flex items-center justify-center gap-2 text-[10px] font-bold bg-white/20 text-white px-4 py-3 rounded-2xl backdrop-blur-md hover:bg-white/30 transition-all uppercase tracking-tight">
                                    <i class="fas fa-bus"></i> Réserver
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Wallets / Shortcuts Section -->
                    <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm">
                        <div class="flex items-center justify-between mb-6">
                            <h4 class="text-sm font-bold text-gray-900 tracking-tight uppercase">Mes Raccourcis</h4>
                            <button class="text-gray-400 hover:text-[#e94f1b] transition-colors"><i class="fas fa-ellipsis-h"></i></button>
                        </div>
                        <div class="space-y-4">
                            <a href="{{ route('reservation.index') }}" class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-2xl transition-all cursor-pointer border border-transparent hover:border-gray-100 group">
                                <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center text-[#e94f1b] group-hover:bg-[#e94f1b] group-hover:text-white transition-all">
                                    <i class="fas fa-ticket-alt text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-bold text-gray-800 tracking-tight leading-none mb-1">Mes Billets</p>
                                    <p class="text-[10px] text-gray-400 font-medium tracking-tight">Accédez à vos QR codes</p>
                                </div>
                            </a>
                            <a href="{{ route('signalement.create') }}" class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-2xl transition-all cursor-pointer border border-transparent hover:border-gray-100 group">
                                <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-red-500 group-hover:bg-red-500 group-hover:text-white transition-all">
                                    <i class="fas fa-exclamation-triangle text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-bold text-gray-800 tracking-tight leading-none mb-1">Signaler un Incident</p>
                                    <p class="text-[10px] text-gray-400 font-medium tracking-tight">Rapporter une panne ou retard</p>
                                </div>
                            </a>
                            <a href="{{ route('user.support.index') }}" class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-2xl transition-all cursor-pointer border border-transparent hover:border-gray-100 group">
                                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500 group-hover:bg-blue-500 group-hover:text-white transition-all">
                                    <i class="fas fa-envelope text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-bold text-gray-800 tracking-tight leading-none mb-1">Support Client</p>
                                    <p class="text-[10px] text-gray-400 font-medium tracking-tight">Besoin d'aide ?</p>
                                </div>
                            </a>
                        </div>
                    </div>

                </div>
            </div>

    @push('scripts')
    <script src="{{asset('assetsPoster/assets/vendors/chartjs/Chart.min.js')}}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('userTravelChart').getContext('2d');
            
            // On récupère les données de PHP
            const labels = {!! json_encode($chartData['labels']) !!};
            const dataValues = {!! json_encode($chartData['values']) !!};

            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(233, 79, 27, 0.4)');
            gradient.addColorStop(1, 'rgba(233, 79, 27, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Nombre de voyages',
                        data: dataValues,
                        borderColor: '#e94f1b',
                        borderWidth: 4,
                        pointBackgroundColor: '#e94f1b',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 4,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    tooltips: {
                        backgroundColor: '#1A1D1F',
                        titleFontSize: 13,
                        titleFontStyle: 'bold',
                        bodyFontSize: 12,
                        cornerRadius: 12,
                        displayColors: false,
                        xPadding: 12,
                        yPadding: 12
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                fontSize: 10,
                                fontStyle: 'bold',
                                fontColor: '#9CA3AF',
                                stepSize: 1
                            },
                            gridLines: {
                                display: true,
                                color: 'rgba(0,0,0,0.03)',
                                drawBorder: false
                            }
                        }],
                        xAxes: [{
                            ticks: {
                                fontSize: 10,
                                fontStyle: 'bold',
                                fontColor: '#9CA3AF'
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false
                            }
                        }]
                    }
                }
            });
        });
    </script>
    @endpush

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap');
        
        body {
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.01em;
        }

        .font-outfit {
            font-family: 'Outfit', sans-serif;
        }
    </style>
@endsection