@extends('gare-espace.layouts.template')

@section('title', 'Gestion des Réservations')

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

    /* Mesh Gradient Background Elements - ORANGE THEME */
    .bg-shape {
        position: absolute;
        filter: blur(100px);
        z-index: -1;
        border-radius: 50%;
        opacity: 0.3;
        pointer-events: none;
    }
    .shape-1 { width: 400px; height: 400px; background: rgba(249, 115, 22, 0.15); top: -100px; right: -100px; }
    .shape-2 { width: 300px; height: 300px; background: rgba(251, 146, 60, 0.1); bottom: -50px; left: -50px; }

    /* ── METRICS (Glass Bubbles) ── */
    .metric-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
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
        flex-shrink: 0;
    }
    .mi-orange { color: #f97316; }
    .mi-green { color: #10b981; }
    .mi-blue { color: #3b82f6; }
    .mi-dark { color: #1e293b; }
    
    .metric-info h4 { font-size: 13px; font-weight: 700; color: #64748B; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
    .metric-info p { font-size: 26px; font-weight: 900; color: #1e293b; margin: 0; line-height: 1.2;}

    /* ── HEADER & SEARCH ── */
    .dash-section-header {
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;
        margin-bottom: 25px;
    }
    .header-text h3 {
        font-size: 22px; font-weight: 900; color: #1e293b; margin: 0;
        display: flex; align-items: center; gap: 12px;
    }
    .header-icon {
        width: 42px; height: 42px;
        background: #f97316;
        color: white;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
        box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);
    }

    /* Filter Form */
    .glass-filter {
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 24px;
        padding: 24px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    }
    .filter-label {
        font-size: 10px; font-weight: 900; color: #94A3B8; text-transform: uppercase; letter-spacing: 1px;
        margin-bottom: 8px; display: block;
    }
    .filter-input {
        background: rgba(255,255,255,0.8) !important;
        border: 1px solid rgba(0,0,0,0.05) !important;
        font-weight: 700;
        font-size: 13px;
        height: 48px;
        border-radius: 14px;
        padding-left: 45px !important;
        width: 100%;
        transition: all 0.3s;
        color: #1e293b;
    }
    .filter-input:focus {
        border-color: #f97316 !important;
        box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1) !important;
        background: #fff !important;
        outline: none;
    }
    .filter-icon {
        position: absolute; left: 16px; top: 16px;
        color: #94A3B8; z-index: 10;
        transition: color 0.3s;
    }
    .relative:focus-within .filter-icon { color: #f97316; }

    .btn-premium {
        background: #1e293b;
        color: white !important;
        height: 48px;
        border-radius: 14px;
        font-weight: 800; font-size: 13px;
        text-transform: uppercase; letter-spacing: 0.5px;
        border: none;
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        box-shadow: 0 4px 15px rgba(30, 41, 59, 0.2);
        transition: all 0.3s;
        cursor: pointer;
        padding: 0 24px;
    }
    .btn-premium:hover { transform: translateY(-2px); background: #0f172a; }
    .btn-premium-orange { background: #f97316; box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3); }
    .btn-premium-orange:hover { background: #ea580c; }
    .btn-premium-light { background: #f1f5f9; color: #64748b !important; box-shadow: none; border: 1px solid #e2e8f0; width: 48px; padding: 0;}
    .btn-premium-light:hover { background: #e2e8f0; color: #1e293b !important; }

    /* ── DASH TABLE ── */
    .dash-card {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 24px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.03);
        border: 1px solid rgba(0,0,0,0.04);
        overflow: hidden;
        backdrop-filter: blur(10px);
    }
    .dash-table { width: 100%; border-collapse: separate; border-spacing: 0; text-align: left; }
    .dash-table th {
        background: rgba(248, 249, 251, 0.8);
        padding: 18px 24px;
        font-size: 11px; font-weight: 800; color: #94A3B8; text-transform: uppercase; letter-spacing: 1px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    .dash-table td {
        padding: 16px 24px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(0,0,0,0.02);
        transition: all 0.2s;
    }
    .dash-table tbody tr:hover td { background: rgba(249, 115, 22, 0.02); }
    
    .td-avatar {
        width: 42px; height: 42px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 900; font-size: 16px;
        flex-shrink: 0;
    }
    .td-avatar.text-orange { background: #FFF7ED; color: #EA580C; }
    
    .cell-stack { display: flex; flex-direction: column; gap: 4px; }
    .td-name { font-weight: 800; color: #1e293b; font-size: 14px; }
    .td-phone { font-size: 12px; color: #64748B; font-weight: 600; }
    .text-ref { font-size: 11px; font-weight: 800; color: #EA580C; text-transform: uppercase; letter-spacing: 0.5px; }
    .text-trans-id { font-size: 10px; font-weight: 600; color: #94A3B8; font-family: monospace; line-height: 1; }
    
    .route-pill {
        display: inline-flex; align-items: center; gap: 8px;
        background: #F8F9FB; padding: 6px 12px; border-radius: 10px;
        font-weight: 800; font-size: 12px; color: #334155;
        border: 1px solid rgba(0,0,0,0.03);
        width: max-content;
    }
    .route-arrow { font-size: 10px; color: #f97316; opacity: 0.7; }
    .text-time { font-size: 12px; font-weight: 700; color: #64748B; }
    
    .seat-badge { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 10px; font-size: 13px; font-weight: 800; }
    .seat-badge-orange { background: #FFF7ED; border: 1px solid #FED7AA; color: #EA580C; }
    
    .td-amount { font-weight: 900; color: #1e293b; font-size: 15px; }
    
    .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
    .sp-success { background: #ECFDF5; color: #059669; }
    .sp-pending { background: #FEF3C7; color: #d97706; }
    .sp-done { background: #EFF6FF; color: #1d4ed8; }
    .sp-danger { background: #FEF2F2; color: #b91c1c; }
    .status-pill .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    .btn-icon {
        width: 38px; height: 38px; border-radius: 12px;
        display: inline-flex; align-items: center; justify-content: center;
        background: #FFF7ED; color: #f97316; border: 1px solid #FFEDD5;
        transition: all 0.2s; cursor: pointer;
    }
    .btn-icon:hover { background: #f97316; color: white; transform: scale(1.05); box-shadow: 0 4px 10px rgba(249, 115, 22, 0.2);}

    /* ── ANIMATIONS ── */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .stagger { animation: fadeInUp 0.5s ease both; }

    /* Fix pagination inside Glass card */
    .pagination-wrapper { padding: 20px 24px; border-top: 1px solid rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: space-between; }
    /* ── TABS ── */
    .premium-tabs {
        display: inline-flex;
        background: rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(10px);
        padding: 5px;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.5);
        margin-bottom: 25px;
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
    .p-tab:hover { color: #f97316; }
    .p-tab.active {
        background: #ffffff;
        color: #f97316;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
</style>
@endsection

@section('content')
<div class="dashboard-page overflow-hidden">
    {{-- Decorative Background Elements --}}
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    {{-- HEADER --}}
    <div class="dash-section-header stagger" style="animation-delay: 0.1s">
        <div class="header-text">
            <h3>
                <div class="header-icon">
                    @if($tab === 'en-cours')
                        <i class="fas fa-check-circle"></i>
                    @elseif($tab === 'terminees')
                        <i class="fas fa-history"></i>
                    @else
                        <i class="fas fa-chart-pie"></i>
                    @endif
                </div>
                @if($tab === 'en-cours')
                    Voyages <span class="text-orange-500 ml-2">Réservés</span>
                @elseif($tab === 'terminees')
                    Voyages <span class="text-orange-500 ml-2">Embarqués</span>
                @else
                    Détails & <span class="text-orange-500 ml-2">Statistiques</span>
                @endif
            </h3>
        </div>
    </div>

    {{-- TABS --}}
    <div class="premium-tabs stagger" style="animation-delay: 0.15s">
        <a href="{{ route('gare-espace.reservations.index', ['tab' => 'en-cours']) }}" 
           class="p-tab {{ $tab === 'en-cours' ? 'active' : '' }}">
           📦 Réservés
        </a>
        <a href="{{ route('gare-espace.reservations.index', ['tab' => 'terminees']) }}" 
           class="p-tab {{ $tab === 'terminees' ? 'active' : '' }}">
           🏁 Embarqués
        </a>
        <a href="{{ route('gare-espace.reservations.index', ['tab' => 'details']) }}" 
           class="p-tab {{ $tab === 'details' ? 'active' : '' }}">
           📊 Détails & Stats
        </a>
    </div>

    {{-- METRICS --}}
    <div class="metric-grid">
        <div class="glass-metric stagger" style="animation-delay: 0.2s">
            <div class="metric-icon-box mi-orange">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="metric-info">
                <h4>Total Réservations</h4>
                <p>{{ number_format($stats['total'], 0, ',', ' ') }}</p>
            </div>
        </div>
        <div class="glass-metric stagger" style="animation-delay: 0.3s">
            <div class="metric-icon-box mi-green">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="metric-info">
                <h4>Aujourd'hui</h4>
                <p>{{ number_format($stats['today'], 0, ',', ' ') }}</p>
            </div>
        </div>
        <div class="glass-metric stagger" style="animation-delay: 0.4s">
            <div class="metric-icon-box mi-blue">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="metric-info">
                <h4>Réservées</h4>
                <p>{{ number_format($stats['confirmed'], 0, ',', ' ') }}</p>
            </div>
        </div>
    </div>

    {{-- ── PROGRAM SELECTION GRID ── --}}
    @if($tab !== 'details')
    <div class="mb-10 stagger" style="animation-delay: 0.5s">
        <div class="flex items-center justify-between mb-6">
            <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                <i class="fas fa-route text-orange-500"></i> Sélectionner un trajet
            </h4>
            @if($tab !== 'details')
            <div class="bg-white p-2 rounded-xl shadow-sm border border-gray-100 flex items-center gap-2">
                <i class="fas fa-calendar-alt text-orange-500 ml-2"></i>
                <input type="date" value="{{ $date }}" 
                       onchange="window.location.href='{{ route('gare-espace.reservations.index', ['tab' => $tab]) }}&date_voyage=' + this.value"
                       class="border-none focus:ring-0 font-bold text-gray-700 bg-transparent text-sm">
            </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($availableProgrammes as $destinationId => $progs)
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-orange-500/5 transition-all group overflow-hidden relative">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-orange-50 rounded-full -mr-16 -mt-16 group-hover:bg-orange-100 transition-colors"></div>
                    
                    <div class="relative z-10">
                        <p class="text-[10px] font-black text-orange-500 uppercase tracking-widest mb-1">Vers</p>
                        <h5 class="text-xl font-black text-gray-900 mb-4 truncate uppercase tracking-tighter">
                            {{ $progs->first()->gareArrivee?->nom_gare ?? $progs->first()->point_arrive }}
                        </h5>
                        
                        <div class="grid grid-cols-2 gap-2">
                                @foreach($progs as $prog)
                                <a href="{{ route('gare-espace.reservations.program', ['programme' => $prog->id, 'date' => $date, 'tab' => $tab]) }}" 
                                   class="flex flex-col items-center p-3 rounded-2xl border {{ request('programme_id') == $prog->id ? 'bg-orange-500 border-orange-500 text-white shadow-lg shadow-orange-500/30' : 'bg-gray-50 border-gray-100 text-gray-600 hover:border-orange-300 hover:bg-orange-50' }} transition-all">
                                    <span class="text-lg font-black leading-none mb-1">{{ \Carbon\Carbon::parse($prog->heure_depart)->format('H:i') }}</span>
                                    <span class="text-[8px] font-black uppercase {{ request('programme_id') == $prog->id ? 'text-orange-100' : 'text-gray-400' }}">
                                        {{ $prog->getPlacesReserveesForDate($date) }}/{{ $prog->getTotalSeats($date) }} Réserv.
                                    </span>
                                </a>
                                @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white/50 border-2 border-dashed border-gray-200 rounded-[2rem] p-10 text-center">
                    <i class="fas fa-calendar-times text-3xl text-gray-300 mb-3"></i>
                    <p class="text-gray-400 font-bold">Aucun programme actif pour cette date.</p>
                </div>
            @endforelse
        </div>
    </div>
    @endif

    @if($tab === 'details')
        {{-- FILTRES --}}
        <div class="glass-filter stagger" style="animation-delay: 0.6s">
            <form method="GET" action="{{ route('gare-espace.reservations.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                <input type="hidden" name="tab" value="{{ $tab }}">
                
                <!-- Date -->
                <div>
                    <label class="filter-label">Date Voyage</label>
                    <div class="relative">
                        <i class="fas fa-calendar-day filter-icon"></i>
                        <input type="date" name="date_voyage" value="{{ request('date_voyage') ?? $date }}" class="filter-input">
                    </div>
                </div>

                <!-- Référence -->
                <div>
                    <label class="filter-label">Référence / Commande</label>
                    <div class="relative">
                        <i class="fas fa-barcode filter-icon"></i>
                        <input type="text" name="reference" value="{{ request('reference') }}" placeholder="Ex: RES-2026..." class="filter-input">
                    </div>
                </div>

                <!-- Passager -->
                <div>
                    <label class="filter-label">Nom du Passager</label>
                    <div class="relative">
                        <i class="fas fa-user filter-icon"></i>
                        <input type="text" name="passager" value="{{ request('passager') }}" placeholder="Nom ou Prénom" class="filter-input">
                    </div>
                </div>

                <!-- Statut -->
                <div>
                    <label class="filter-label">Statut</label>
                    <div class="relative">
                        <i class="fas fa-info-circle filter-icon"></i>
                        <select name="statut" class="filter-input hover:cursor-pointer" style="padding-right: 15px;">
                            <option value="all" {{ request('statut') == 'all' ? 'selected' : '' }}>Tous les statuts</option>
                            <option value="confirmee" {{ request('statut') == 'confirmee' ? 'selected' : '' }}>Réservée</option>
                            <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="terminee" {{ request('statut') == 'terminee' ? 'selected' : '' }}>Embarqué</option>
                            <option value="annulee" {{ request('statut') == 'annulee' ? 'selected' : '' }}>Annulée</option>
                        </select>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <button type="submit" class="btn-premium btn-premium-orange flex-1">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                </div>
            </form>
        </div>

        {{-- TABLEAU --}}
        <div class="dash-card stagger" style="animation-delay: 0.6s">
            <div class="overflow-x-auto">
                <table class="dash-table w-full">
                    <thead>
                        <tr>
                            <th>Référence & Trans.</th>
                            <th>Passager</th>
                            <th>Itinéraire</th>
                            <th>Voyage</th>
                            <th class="text-right">Montant</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center">Détails</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reservations as $reservation)
                        <tr class="hover:bg-orange-50/30 transition-colors cursor-pointer" onclick="showDetails({{ $reservation->id }})">
                            <td>
                                <div class="cell-stack">
                                    <span class="text-ref">
                                        <i class="fas fa-ticket-alt opacity-50 mr-1"></i> {{ $reservation->reference }}
                                    </span>
                                    <span class="text-trans-id">{{ $reservation->payment_transaction_id }}</span>
                                </div>
                            </td>

                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="td-avatar text-orange">
                                        {{ mb_substr($reservation->passager_nom, 0, 1) }}
                                    </div>
                                    <div class="cell-stack">
                                        <span class="td-name">{{ $reservation->passager_prenom }} {{ $reservation->passager_nom }}</span>
                                        <span class="td-phone">
                                            <i class="fas fa-phone-alt text-[10px] text-orange-400 mr-1"></i> {{ $reservation->passager_telephone }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="cell-stack">
                                    <div class="route-pill">
                                        {{ $reservation->programme->point_depart ?? 'N/A' }}
                                        <i class="fas fa-chevron-right route-arrow"></i>
                                        {{ $reservation->programme->point_arrive ?? 'N/A' }}
                                    </div>
                                    <div class="flex items-center justify-between mt-1.5">
                                        <span class="text-[10px] font-black text-orange-500 uppercase tracking-widest">Siège #{{ $reservation->seat_number }}</span>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="cell-stack">
                                    <span class="td-name">{{ \Carbon\Carbon::parse($reservation->date_voyage)->format('d/m/Y') }}</span>
                                    <span class="text-time">
                                        <i class="far fa-clock text-orange-400 mr-1"></i> {{ $reservation->heure_depart }}
                                    </span>
                                </div>
                            </td>

                            <td class="text-right">
                                <span class="td-amount">{{ number_format($reservation->montant, 0, ',', ' ') }}</span>
                                <span class="text-[10px] font-black text-gray-400 uppercase ml-1">FCFA</span>
                            </td>

                            <td class="text-center">
                                @if($reservation->statut == 'confirmee')
                                    <span class="status-pill sp-success"><span class="dot"></span> Réservée</span>
                                @elseif($reservation->statut == 'en_attente')
                                    <span class="status-pill sp-pending"><span class="dot"></span> En attente</span>
                                @elseif($reservation->statut == 'terminee')
                                    <span class="status-pill sp-done"><span class="dot"></span> Embarqué</span>
                                @elseif($reservation->statut == 'annulee')
                                    <span class="status-pill sp-danger"><span class="dot"></span> Annulée</span>
                                @else
                                    <span class="status-pill sp-pending"><span class="dot"></span> {{ $reservation->statut }}</span>
                                @endif
                            </td>

                            <td class="text-center" onclick="event.stopPropagation()">
                                <button onclick="showDetails({{ $reservation->id }})" class="btn-icon">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-24 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center text-orange-200 mb-6 border border-gray-100 shadow-sm">
                                        <i class="fas fa-search-minus text-3xl"></i>
                                    </div>
                                    <h3 class="text-xl font-black text-gray-900 mb-2 uppercase tracking-tighter">Aucune réservation trouvée</h3>
                                    <p class="text-gray-400 font-medium max-w-sm mx-auto text-sm">Nous n'avons trouvé aucune réservation correspondant à vos critères de recherche.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reservations->hasPages())
            <div class="pagination-wrapper bg-white">
                <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest hidden md:block">
                    Affichage de {{ $reservations->firstItem() }} à {{ $reservations->lastItem() }} sur {{ $reservations->total() }}
                </div>
                <div class="flex items-center gap-2">
                    {{ $reservations->onEachSide(1)->links('pagination::tailwind') }}
                </div>
            </div>
            @endif
        </div>
    @endif
</div>

@include('gare-espace.reservation._modal_details')
@endsection
