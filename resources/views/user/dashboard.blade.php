@extends('user.layouts.template')

@section('content')
            <!-- Welcome Header -->
            <div class="mb-6 sm:mb-10">
                <h1 class="text-xl sm:text-3xl font-black text-[#1A1D1F] tracking-tight mb-1 font-outfit uppercase">Tableau de bord</h1>
                <p class="text-gray-500 text-sm font-medium">Bon retour, <span class="text-[#e94f1b] font-bold">{{ $user->name }}</span>. Voici un aperçu de vos activités.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Main Content Left (8 cols) -->
                <div class="lg:col-span-8 space-y-8">
                    
                    @if($currentTrip)
                    <!-- Ongoing Voyage Card (Steel UI Style) -->
                    <div id="btn-open-tracking-modal" class="bg-gradient-to-br from-teal-700 via-emerald-800 to-green-900 rounded-[32px] p-8 shadow-[inset_0_2px_1px_rgba(255,255,255,0.3),_0_20px_40px_rgba(20,83,45,0.5)] border-t border-l border-white/20 text-white relative overflow-hidden group cursor-pointer hover:shadow-[0_20px_50px_rgba(20,83,45,0.7)] hover:-translate-y-1 transition-all duration-300">
                        <div class="absolute -right-10 -top-10 w-48 h-48 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all duration-700"></div>
                        
                        <div class="relative z-10">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-4">
                                        <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-black uppercase tracking-widest border border-white/30">Voyage en cours</span>
                                        <span class="flex h-2 w-2 rounded-full bg-white animate-pulse"></span>
                                    </div>
                                    
                                    <h2 class="text-lg sm:text-2xl font-black mb-1 leading-tight">
                                        {{ $currentTrip->programme->point_depart }}
                                        <i class="fas fa-arrow-right text-white/50 mx-1 sm:mx-2 text-base sm:text-xl"></i>
                                        {{ $currentTrip->programme->point_arrive }}
                                    </h2>
                                    <p class="text-white/70 text-sm font-bold uppercase tracking-wider mb-6">
                                        {{ $currentTrip->programme->gareDepart->nom_gare }} &rarr; {{ $currentTrip->programme->gareArrivee->nom_gare }}
                                    </p>
                                    
                                    <div class="grid grid-cols-2 gap-8">
                                        <div>
                                            <p class="text-white/50 text-[10px] font-black uppercase tracking-widest mb-1">Arrivée prévue</p>
                                            <p class="text-xl font-black">{{ \Carbon\Carbon::parse($currentTrip->programme->heure_arrive)->format('H:i') }}</p>
                                        </div>
                                        <div class="flex flex-col">
                                            <p class="text-[10px] font-black text-white/60 uppercase tracking-widest mb-1">Siège n°</p>
                                            <p class="text-2xl font-black text-white">{{ $currentTrip->seat_number }}</p>
                                            <p class="text-[9px] font-medium text-white/40 mt-1 uppercase tracking-tighter">{{ $currentTrip->reference }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-4 sm:p-6 text-center w-full md:min-w-[200px] md:w-auto">
                                    <p class="text-white/70 text-[10px] font-black uppercase tracking-widest mb-3">Temps restant estimé</p>
                                    
                                    @php
                                        $dateVoyage = \Carbon\Carbon::parse($currentTrip->date_voyage)->format('Y-m-d');
                                        $heureArrive = $currentTrip->programme->heure_arrive;
                                        
                                        // Utiliser le voyage lié si disponible pour l'estimation dynamique
                                        $linkedVoyage = $currentTrip->programme->voyages->first();
                                        
                                        // Priorité à l'estimation dynamique
                                        $arrivalDateTime = ($linkedVoyage && $linkedVoyage->estimated_arrival_at) 
                                            ? \Carbon\Carbon::parse($linkedVoyage->estimated_arrival_at) 
                                            : \Carbon\Carbon::parse($dateVoyage . ' ' . $heureArrive);
                                        
                                        // Gérer le passage à minuit si pas d'estimation dynamique
                                        if (!($linkedVoyage && $linkedVoyage->estimated_arrival_at) && \Carbon\Carbon::parse($currentTrip->programme->heure_arrive)->lt(\Carbon\Carbon::parse($currentTrip->programme->heure_depart))) {
                                            $arrivalDateTime->addDay();
                                        }
                                    @endphp
                                    
                                    <div class="text-xl md:text-2xl font-black tracking-tight mb-2 min-h-[1.5em] flex items-center justify-center font-outfit" 
                                         id="user-timer-{{ $currentTrip->id }}">
                                        {{ $currentTrip->mission?->temps_restant ?: "Calcul en cours..." }}
                                    </div>
                                    <div class="w-full h-1.5 bg-white/20 rounded-full mt-4 overflow-hidden">
                                        <div class="h-full bg-white rounded-full animate-pulse shadow-[0_0_10px_rgba(255,255,255,0.5)]" style="width: 65%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                             <!-- Stats Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Total Reservations Card -->
                        <div class="bg-gradient-to-br from-white via-[#fcfcfd] to-[#f4f5f7] rounded-[32px] p-7 shadow-[inset_0_1px_1px_rgba(255,255,255,1),_0_10px_30px_rgba(0,0,0,0.03)] border border-gray-100 hover:shadow-[inset_0_1px_1px_rgba(255,255,255,1),_0_15px_40px_rgba(0,0,0,0.06)] transition-all duration-300 relative overflow-hidden group">
                            <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/80 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                            <div class="flex items-center gap-3 mb-6 relative z-10">
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
                        <div class="bg-gradient-to-br from-white via-[#fcfcfd] to-[#f4f5f7] rounded-[32px] p-7 shadow-[inset_0_1px_1px_rgba(255,255,255,1),_0_10px_30px_rgba(0,0,0,0.03)] border border-gray-100 hover:shadow-[inset_0_1px_1px_rgba(255,255,255,1),_0_15px_40px_rgba(0,0,0,0.06)] transition-all duration-300 relative overflow-hidden group">
                            <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/80 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                            <div class="flex items-center gap-3 mb-6 relative z-10">
                                <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-green-600">
                                    <i class="fas fa-bus-alt text-sm"></i>
                                </div>
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Réservations Actives</span>
                            </div>
                            <div class="flex items-baseline gap-2">
                                <h3 class="text-3xl font-black text-gray-900">{{ $activeReservations }}</h3>
                               
                            </div>
                            <p class="mt-4 text-[11px] font-bold text-gray-400 uppercase">Prochains départs gérés</p>
                        </div>
                    </div>

                    <!-- Chart Section -->
                    <div class="bg-gradient-to-br from-white via-[#fcfcfd] to-[#f4f5f7] rounded-[32px] p-8 shadow-[inset_0_1px_1px_rgba(255,255,255,1),_0_10px_30px_rgba(0,0,0,0.03)] border border-gray-100">
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
                                <div class="bg-white p-5 rounded-3xl shadow-[inset_0_1px_1px_rgba(255,255,255,1),_0_4px_15px_rgba(0,0,0,0.02)] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:shadow-[inset_0_1px_1px_rgba(255,255,255,1),_0_10px_25px_rgba(0,0,0,0.05)] hover:-translate-y-0.5 transition-all duration-300 group border border-gray-50 hover:border-orange-100 relative overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/80 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                                    <div class="flex items-center gap-4 relative z-10">
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
                    
                    <!-- Balance Card (Magnetic Card Orange) -->
                    <div class="bg-gradient-to-br from-[#ff8b5a] via-[#e94f1b] to-[#d33a0b] rounded-[32px] p-7 shadow-[inset_0_1px_2px_rgba(255,255,255,0.5),_0_20px_40px_rgba(233,79,27,0.3)] border border-[#ff8b5a]/50 text-white relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/20 rounded-full blur-2xl group-hover:bg-white/30 transition-all"></div>
                        <div class="absolute -left-10 bottom-0 w-40 h-40 bg-[#c62900]/20 rounded-full blur-3xl"></div>
                        <!-- Micro texture for magnetic card feel -->
                        <div class="absolute inset-0 opacity-[0.02] pointer-events-none" style="background-image: radial-gradient(circle at center, #fff 1px, transparent 1px); background-size: 4px 4px;"></div>

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
                    <div class="bg-gradient-to-br from-white via-[#fcfcfd] to-[#f4f5f7] rounded-[32px] p-8 shadow-[inset_0_1px_1px_rgba(255,255,255,1),_0_10px_30px_rgba(0,0,0,0.03)] border border-gray-100 group relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-orange-50 rounded-full blur-3xl -z-10 group-hover:scale-110 transition-transform duration-500"></div>
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

    <!-- Tracking Modal -->
    <div id="trackingModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
        <!-- Backdrop, blur effect -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm tracking-modal-close transition-opacity duration-300 opacity-0" id="trackingModalBackdrop"></div>
        
        <!-- Modal Content -->
        <div class="relative w-full max-w-4xl h-[85vh] bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col mx-4 transform scale-95 opacity-0 transition-all duration-300" id="trackingModalContent">
            
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-teal-50 to-white">
                <h3 class="text-xl font-black tracking-tight text-gray-900"><i class="fas fa-satellite-dish text-teal-600 mr-2"></i> Suivi en temps réel</h3>
                <button class="text-gray-400 hover:text-red-500 transition-colors tracking-modal-close p-2">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <!-- Info Bar -->
            <div class="px-6 py-3 bg-gray-50 border-b border-gray-100 flex flex-wrap gap-4 text-xs font-bold text-gray-600 uppercase tracking-widest justify-between items-center">
                <div id="tracking-itinerary" class="flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-gray-400"></i>
                    <span>--</span>
                    <i class="fas fa-arrow-right text-[10px] text-gray-300"></i>
                    <span>--</span>
                </div>
                <div id="tracking-status" class="text-teal-600 flex items-center gap-2">
                    <span class="flex h-2 w-2 rounded-full bg-teal-500 animate-pulse"></span>
                    <span>En attente GPS...</span>
                </div>
                <div class="flex items-center gap-4">
                    <div id="tracking-speed" class="flex items-center gap-1">
                        <i class="fas fa-tachometer-alt"></i> -- km/h
                    </div>
                    <div class="w-px h-4 bg-gray-300"></div>
                    <div id="tracking-time" class="flex items-center gap-1 text-orange-600">
                        <i class="fas fa-clock"></i> --:--:--
                    </div>
                </div>
            </div>

            <!-- Map Container -->
            <div class="w-full flex-1 relative bg-gray-100">
                <div id="trackingMap" class="absolute inset-0 z-10 w-full h-full"></div>
                <!-- Loading indicator -->
                <div id="trackingLoading" class="absolute inset-0 z-20 flex flex-col items-center justify-center bg-white/80 backdrop-blur-sm transition-opacity duration-300">
                    <i class="fas fa-circle-notch fa-spin text-teal-600 text-4xl mb-4"></i>
                    <p class="text-sm font-bold text-gray-600 uppercase tracking-widest">Connexion au véhicule...</p>
                </div>
            </div>
            
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* Custom Leaflet marker */
        .bus-marker {
            background: #10b981;
            border: 3px solid white;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
            color: white;
            font-size: 14px;
            transition: all 0.3s;
        }

        .bus-marker-offline {
            background: #9ca3af;
            box-shadow: 0 4px 12px rgba(156, 163, 175, 0.4);
        }

        .leaflet-popup-content {
            font-family: 'Outfit', 'Segoe UI', sans-serif;
            min-width: 200px;
        }

        .popup-title {
            font-weight: 800;
            font-size: 1rem;
            color: #111827;
            margin-bottom: 8px;
        }

        .popup-info {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.82rem;
            color: #6b7280;
            margin: 4px 0;
            font-weight: 500;
        }

        .popup-info i {
            width: 16px;
            text-align: center;
            color: #10b981;
        }
    </style>
    @endpush

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
            // Le calcul du temps est désormais exclusivement géré par le serveur via la distance GPS.
            // Nous ne faisons plus de compte à rebours local.
            function updateTimers() { return; }

            if (document.querySelectorAll('[data-arrival]').length > 0) {
                updateTimers();
                setInterval(updateTimers, 60000); // Mise à jour toutes les minutes
            }
        });
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnOpenTracking = document.getElementById('btn-open-tracking-modal');
            const trackingModal = document.getElementById('trackingModal');
            const trackingModalBackdrop = document.getElementById('trackingModalBackdrop');
            const trackingModalContent = document.getElementById('trackingModalContent');
            const btnCloseTracking = document.querySelectorAll('.tracking-modal-close');
            const trackingLoading = document.getElementById('trackingLoading');
            
            let map = null;
            let busMarker = null;
            let routeLayer = null;
            let trackingInterval = null;
            let isFirstLoad = true;

            // Gare coordinates from PHP
            @if($currentTrip)
            const gareDepart = {
                lat: {{ $currentTrip->programme->gareDepart->latitude ?? 'null' }},
                lng: {{ $currentTrip->programme->gareDepart->longitude ?? 'null' }},
                nom: @json($currentTrip->programme->gareDepart->nom_gare ?? $currentTrip->programme->point_depart)
            };
            const gareArrivee = {
                lat: {{ $currentTrip->programme->gareArrivee->latitude ?? 'null' }},
                lng: {{ $currentTrip->programme->gareArrivee->longitude ?? 'null' }},
                nom: @json($currentTrip->programme->gareArrivee->nom_gare ?? $currentTrip->programme->point_arrive)
            };
            @else
            const gareDepart = { lat: null, lng: null, nom: '--' };
            const gareArrivee = { lat: null, lng: null, nom: '--' };
            @endif

            function createBusIcon(isOnline = true) {
                return L.divIcon({
                    html: `<div class="bus-marker ${isOnline ? '' : 'bus-marker-offline'}"><i class="fas fa-bus"></i></div>`,
                    iconSize: [32, 32],
                    iconAnchor: [16, 16],
                    popupAnchor: [0, -20],
                    className: ''
                });
            }

            function createGareIcon(type) {
                const color = type === 'depart' ? '#10b981' : '#ef4444';
                const icon = type === 'depart' ? 'fa-sign-out-alt' : 'fa-flag-checkered';
                return L.divIcon({
                    html: `<div style="background:${color};width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;box-shadow:0 3px 10px ${color}88;border:2px solid #fff;font-size:14px;"><i class="fas ${icon}"></i></div>`,
                    iconSize: [36, 36],
                    iconAnchor: [18, 18],
                    popupAnchor: [0, -22],
                    className: ''
                });
            }

            function drawRoute() {
                if (!gareDepart.lat || !gareArrivee.lat) return;

                const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${gareDepart.lng},${gareDepart.lat};${gareArrivee.lng},${gareArrivee.lat}?overview=full&geometries=geojson`;

                fetch(osrmUrl)
                    .then(r => r.json())
                    .then(data => {
                        if (!data.routes || !data.routes[0]) return;
                        if (routeLayer) map.removeLayer(routeLayer);

                        const geojson = data.routes[0].geometry;

                        // Shadow layer
                        L.geoJSON(geojson, {
                            style: { color: 'rgba(0,0,0,0.15)', weight: 9, lineCap: 'round', lineJoin: 'round' }
                        }).addTo(map);

                        // Main green route
                        routeLayer = L.geoJSON(geojson, {
                            style: { color: '#10b981', weight: 5, lineCap: 'round', lineJoin: 'round', opacity: 0.9 }
                        }).addTo(map);

                        // Fit map to route
                        map.fitBounds(routeLayer.getBounds(), { padding: [40, 40] });
                    })
                    .catch(() => {});
            }

            function initMap() {
                if (map) return; // Already initialized

                // Default center (Côte d'Ivoire)
                const center = (gareDepart.lat && gareDepart.lng)
                    ? [gareDepart.lat, gareDepart.lng]
                    : [6.8276, -5.2893];

                map = L.map('trackingMap').setView(center, 7);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap',
                    maxZoom: 19,
                }).addTo(map);

                // Add gare markers
                if (gareDepart.lat && gareDepart.lng) {
                    L.marker([gareDepart.lat, gareDepart.lng], { icon: createGareIcon('depart') })
                        .addTo(map)
                        .bindPopup(`<div class="popup-title">Départ</div><div class="popup-info"><i class="fas fa-map-marker-alt" style="color:#10b981"></i> ${gareDepart.nom}</div>`);
                }
                if (gareArrivee.lat && gareArrivee.lng) {
                    L.marker([gareArrivee.lat, gareArrivee.lng], { icon: createGareIcon('arrivee') })
                        .addTo(map)
                        .bindPopup(`<div class="popup-title">Arrivée</div><div class="popup-info"><i class="fas fa-flag-checkered" style="color:#ef4444"></i> ${gareArrivee.nom}</div>`);
                }

                drawRoute();
            }

            function fetchLiveLocation() {
                fetch("{{ route('user.tracking.location') }}")
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            trackingLoading.querySelector('p').textContent = data.message || "Position indisponible";
                            trackingLoading.querySelector('i').className = "fas fa-exclamation-triangle text-orange-500 text-4xl mb-4";
                            return;
                        }

                        // Hide loading if showing
                        if (trackingLoading.style.opacity !== '0') {
                            trackingLoading.style.opacity = '0';
                            setTimeout(() => { trackingLoading.style.display = 'none'; }, 300);
                        }

                        const loc = data.location;
                        const latLng = [loc.latitude, loc.longitude];

                        // Update DOM elements
                        document.getElementById('tracking-itinerary').innerHTML = `
                            <i class="fas fa-map-marker-alt text-teal-600"></i>
                            <span class="text-gray-900">${loc.depart}</span>
                            <i class="fas fa-arrow-right mx-1 text-gray-300"></i>
                            <span class="text-gray-900">${loc.arrivee}</span>
                        `;
                        
                        document.getElementById('tracking-speed').innerHTML = `<i class="fas fa-tachometer-alt"></i> ${loc.speed ? Math.round(loc.speed) : 0} km/h`;
                        document.getElementById('tracking-status').innerHTML = `
                            <span class="flex h-2 w-2 rounded-full bg-teal-500 animate-[livePulse_1.5s_infinite]"></span>
                            <span>En direct</span>
                        `;

                        const popupContent = `
                            <div class="popup-title">${loc.depart} → ${loc.arrivee}</div>
                            <div class="popup-info"><i class="fas fa-user text-teal-600"></i> ${loc.chauffeur}</div>
                            <div class="popup-info"><i class="fas fa-bus text-teal-600"></i> ${loc.vehicule}</div>
                            <div class="popup-info"><i class="fas fa-tachometer-alt text-teal-600"></i> ${loc.speed ? Math.round(loc.speed) : 0} km/h</div>
                            <div class="popup-info text-xs mt-2 text-gray-400 border-t border-gray-100 pt-2">
                                <i class="fas fa-sync text-gray-400"></i> Màj: ${loc.last_update}
                            </div>
                        `;

                        if (busMarker) {
                            busMarker.setLatLng(latLng);
                            busMarker.setPopupContent(popupContent);
                        } else {
                            busMarker = L.marker(latLng, { icon: createBusIcon(true) }).addTo(map);
                            busMarker.bindPopup(popupContent).openPopup();
                        }

                        if (isFirstLoad) {
                            // Don't override route bounds — just pan to bus if no route
                            if (!gareDepart.lat || !gareArrivee.lat) {
                                map.setView(latLng, 13, { animate: true });
                            }
                            isFirstLoad = false;
                        }

                        // Update display with server-side distance-based ETA
                        if (loc.temps_restant) {
                            const timer = document.getElementById('user-timer-{{ $currentTrip->id ?? "" }}');
                            const trackingTimeEl = document.getElementById('tracking-time');
                            
                            if (timer) timer.innerHTML = loc.temps_restant;
                            if (trackingTimeEl) trackingTimeEl.innerHTML = `<i class="fas fa-clock"></i> Arrivée dans ${loc.temps_restant}`;
                        }
                    })
                    .catch(err => {
                        console.error('Erreur GPS:', err);
                    });
            }

            // Open Modal
            if (btnOpenTracking) {
                btnOpenTracking.addEventListener('click', function() {
                    trackingModal.classList.remove('hidden');
                    // Slight delay to allow display:block to apply before animating opacity/transform
                    setTimeout(() => {
                        trackingModalBackdrop.classList.remove('opacity-0');
                        trackingModalContent.classList.remove('opacity-0', 'scale-95');
                        trackingModalContent.classList.add('opacity-100', 'scale-100');
                    }, 10);
                    
                    document.body.style.overflow = 'hidden'; // Prevent scrolling Behind
                    
                    // Init map only when modal is visible (Leaflet rendering fix)
                    setTimeout(() => {
                        initMap();
                        map.invalidateSize();
                        isFirstLoad = true;
                        
                        // Show loading initially
                        trackingLoading.style.display = 'flex';
                        trackingLoading.style.opacity = '1';
                        trackingLoading.querySelector('p').textContent = "Connexion au véhicule...";
                        trackingLoading.querySelector('i').className = "fas fa-circle-notch fa-spin text-teal-600 text-4xl mb-4";
                        
                        fetchLiveLocation();
                        trackingInterval = setInterval(fetchLiveLocation, 5000);
                    }, 300);
                });
            }

            // Close Modal
            btnCloseTracking.forEach(btn => {
                btn.addEventListener('click', function() {
                    trackingModalBackdrop.classList.add('opacity-0');
                    trackingModalContent.classList.add('opacity-0', 'scale-95');
                    trackingModalContent.classList.remove('opacity-100', 'scale-100');
                    
                    if (trackingInterval) {
                        clearInterval(trackingInterval);
                        trackingInterval = null;
                    }

                    setTimeout(() => {
                        trackingModal.classList.add('hidden');
                        document.body.style.overflow = '';
                    }, 300);
                });
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