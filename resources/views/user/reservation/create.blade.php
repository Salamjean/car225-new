@extends('user.layouts.template')
@section('content')

@push('styles')
<style>
    /* Hero Search */
    .hero-search {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        position: relative;
        overflow: hidden;
    }
    .hero-search::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(233,79,27,0.15) 0%, transparent 70%);
        border-radius: 50%;
    }
    .hero-search::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(59,130,246,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }
    .glass-input {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        color: #fff;
        transition: all 0.3s ease;
    }
    .glass-input::placeholder { color: rgba(255,255,255,0.5); }
    .glass-input:focus {
        background: rgba(255,255,255,0.15);
        border-color: #e94f1b;
        box-shadow: 0 0 0 3px rgba(233,79,27,0.2);
        outline: none;
    }
    /* Route Card */
    .route-card {
        border-left: 4px solid #e94f1b;
        transition: all 0.35s cubic-bezier(.4,0,.2,1);
    }
    .route-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08), 0 0 0 1px rgba(233,79,27,0.1);
        border-left-width: 6px;
    }
    /* Timeline */
    .timeline-line {
        background: repeating-linear-gradient(90deg, #d1d5db 0, #d1d5db 6px, transparent 6px, transparent 12px);
        height: 2px;
    }
    /* Schedule chip */
    .schedule-chip {
        transition: all 0.2s ease;
        position: relative;
    }
    .schedule-chip:hover {
        transform: scale(1.08);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .schedule-chip::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        position: absolute;
        top: -3px;
        left: 50%;
        transform: translateX(-50%);
        background: currentColor;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .schedule-chip:hover::before { opacity: 1; }
    /* Swap button rotation */
    .swap-btn { transition: all 0.4s cubic-bezier(.4,0,.2,1); }
    .swap-btn:hover { transform: rotate(180deg) scale(1.1); }
    .swap-btn:active { transform: rotate(180deg) scale(0.95); }
    /* Price pulse */
    @keyframes pricePulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.03)} }
    .route-card:hover .price-value { animation: pricePulse 0.5s ease; }
</style>
@endpush

     <div {{--class="min-h-screen"--}} style="margin-top:-20px"> 

        {{-- ============================================ --}}
        {{-- HERO SEARCH BAR --}}
        {{-- ============================================ --}}
        <div class="hero-search rounded-b-3xl sm:rounded-b-[2.5rem] shadow-2xl px-4 sm:px-6 lg:px-8 pt-6 pb-10 sm:pt-8 sm:pb-14 relative z-10">
            <div class="relative z-20 max-w-6xl mx-auto">
                {{-- Title & Wallet --}}
                <div class="flex flex-col md:flex-row justify-between items-center md:items-start mb-6 sm:mb-8 gap-4">
                    <div class="text-center md:text-left">
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight">
                            Où allez-vous <span class="text-[#e94f1b]">?</span>
                        </h1>
                        <p class="text-blue-200/70 text-sm sm:text-base mt-1 font-medium">Trouvez le meilleur trajet au meilleur prix</p>
                    </div>

                    {{-- Wallet Balance --}}
                    <div class="flex items-center gap-4 bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl px-6 py-4 shadow-2xl transition-all hover:bg-white/15 cursor-default group">
                        <div class="w-12 h-12 bg-gradient-to-br from-[#e94f1b] to-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-wallet text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-blue-100/70 uppercase tracking-[0.2em] mb-0.5">Mon Solde CarPay</p>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-2xl font-black text-white leading-none">
                                    {{ number_format(auth()->user()->solde ?? 0, 0, ',', ' ') }}
                                </span>
                                <span class="text-xs font-bold text-[#e94f1b]">FCFA</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Search Form --}}
                <form action="{{ route('reservation.create') }}" method="GET" id="search-form">
                    {{-- Type de voyage --}}
                    <div class="flex gap-6 mb-4 px-2">
                        <label class="flex items-center gap-2 cursor-pointer text-white font-semibold">
                            <input type="radio" name="type_voyage" value="aller_simple" class="form-radio text-[#e94f1b] focus:ring-[#e94f1b]" {{ ($searchParams['type_voyage'] ?? 'aller_simple') === 'aller_simple' ? 'checked' : '' }} onchange="toggleDateRetour()">
                            Aller simple
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-white font-semibold">
                            <input type="radio" name="type_voyage" value="aller_retour" class="form-radio text-[#e94f1b] focus:ring-[#e94f1b]" {{ ($searchParams['type_voyage'] ?? '') === 'aller_retour' ? 'checked' : '' }} onchange="toggleDateRetour()">
                            Aller-Retour
                        </label>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 sm:gap-4 items-end" id="search-grid">

                        {{-- Départ --}}
                        <div class="lg:col-span-4 transition-all duration-300" id="div_depart">
                            <label class="block text-xs font-bold text-blue-200/80 uppercase tracking-widest mb-2">
                                <i class="fas fa-map-marker-alt text-[#e94f1b] mr-1"></i> De
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-map-marker-alt text-[#e94f1b]/60"></i>
                                </span>
                                <input type="text" id="point_depart" name="point_depart"
                                    value="{{ $searchParams['point_depart'] ?? '' }}"
                                    class="glass-input w-full pl-11 pr-4 py-3.5 rounded-xl font-semibold text-sm"
                                    placeholder="Ville de départ" required>
                            </div>
                        </div>

                        {{-- Swap --}}
                        <div class="lg:col-span-1 flex items-end justify-center pb-1 transition-all duration-300" id="div_swap">
                            <button type="button" onclick="swapLocations()"
                                class="swap-btn w-11 h-11 bg-[#e94f1b] text-white rounded-full shadow-lg shadow-orange-500/30 flex items-center justify-center"
                                title="Inverser départ/arrivée">
                                <i class="fas fa-exchange-alt text-sm"></i>
                            </button>
                        </div>

                        {{-- Arrivée --}}
                        <div class="lg:col-span-4 transition-all duration-300" id="div_arrive">
                            <label class="block text-xs font-bold text-blue-200/80 uppercase tracking-widest mb-2">
                                <i class="fas fa-flag text-emerald-400 mr-1"></i> À
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-flag text-emerald-400/60"></i>
                                </span>
                                <input type="text" id="point_arrive" name="point_arrive"
                                    value="{{ $searchParams['point_arrive'] ?? '' }}"
                                    class="glass-input w-full pl-11 pr-4 py-3.5 rounded-xl font-semibold text-sm"
                                    placeholder="Ville d'arrivée" required>
                            </div>
                        </div>

                        {{-- Date Aller --}}
                        <div class="lg:col-span-2 transition-all duration-300" id="div_date_depart">
                            <label class="block text-xs font-bold text-blue-200/80 uppercase tracking-widest mb-2 truncate">
                                <i class="fas fa-calendar-alt text-blue-400 mr-1"></i> Date aller
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-1 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar-alt text-blue-400/60 text-xs"></i>
                                </span>
                                <input type="date" id="date_depart" name="date_depart"
                                    value="{{ $searchParams['date_depart'] ?? date('Y-m-d', strtotime('+1 day')) }}"
                                    class="glass-input w-full pl-7 pr-1 py-3.5 rounded-xl font-semibold text-xs sm:text-sm"
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                            </div>
                        </div>

                        {{-- Date Retour --}}
                        <div class="lg:col-span-2 hidden transition-all duration-300" id="div_date_retour">
                            <label class="block text-xs font-bold text-blue-200/80 uppercase tracking-widest mb-2 truncate">
                                <i class="fas fa-calendar-check text-purple-400 mr-1"></i> Date retour
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-1 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar-check text-purple-400/60 text-xs"></i>
                                </span>
                                <input type="date" id="date_retour" name="date_retour"
                                    value="{{ $searchParams['date_retour'] ?? '' }}"
                                    class="glass-input w-full pl-7 pr-1 py-3.5 rounded-xl font-semibold text-xs sm:text-sm"
                                    min="{{ $searchParams['date_depart'] ?? date('Y-m-d', strtotime('+1 day')) }}">
                            </div>
                        </div>

                        {{-- Rechercher et Réinitialiser --}}
                        <div class="lg:col-span-1 transition-all duration-300 flex flex-col sm:flex-row gap-2" id="div_submit">
                            <button type="submit" title="Rechercher"
                                class="flex-1 bg-[#e94f1b] hover:bg-[#d4430f] text-white p-3.5 rounded-xl font-black text-sm uppercase shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 transition-all duration-300 flex items-center justify-center active:scale-[0.97]">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('reservation.create') }}" title="Réinitialiser"
                                class="w-full sm:w-11 flex-shrink-0 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-xl shadow-lg shadow-white/5 backdrop-blur-sm transition-all duration-300 flex items-center justify-center active:scale-[0.97] p-3.5 sm:p-0">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </form>

                <script>
                    function toggleDateRetour() {
                        const isAllerRetour = document.querySelector('input[name="type_voyage"]:checked').value === 'aller_retour';
                        const divDateRetour = document.getElementById('div_date_retour');
                        const inputDateRetour = document.getElementById('date_retour');
                        const divDepart = document.getElementById('div_depart');
                        const divArrive = document.getElementById('div_arrive');
                        const divDateDepart = document.getElementById('div_date_depart');
                        
                        if (isAllerRetour) {
                            divDateRetour.classList.remove('hidden');
                            inputDateRetour.setAttribute('required', 'required');
                            
                            // 3 + 1 + 3 + 2 + 2 + 1 = 12
                            divDepart.className = "lg:col-span-3 transition-all duration-300";
                            divArrive.className = "lg:col-span-3 transition-all duration-300";
                            divDateDepart.className = "lg:col-span-2 transition-all duration-300";
                        } else {
                            divDateRetour.classList.add('hidden');
                            inputDateRetour.removeAttribute('required');
                            inputDateRetour.value = '';
                            
                            // 4 + 1 + 4 + 2 + 1 = 11? No, 4+1+4+2+1 = 12.
                            divDepart.className = "lg:col-span-4 transition-all duration-300";
                            divArrive.className = "lg:col-span-4 transition-all duration-300";
                            divDateDepart.className = "lg:col-span-2 transition-all duration-300";
                        }
                    }
                    
                    document.addEventListener('DOMContentLoaded', function() {
                        toggleDateRetour();
                        
                        const dateDepartInput = document.getElementById('date_depart');
                        const dateRetourInput = document.getElementById('date_retour');
                        if (dateDepartInput && dateRetourInput) {
                            dateDepartInput.addEventListener('change', function() {
                                dateRetourInput.min = this.value;
                                if (dateRetourInput.value && dateRetourInput.value < this.value) {
                                    dateRetourInput.value = this.value;
                                }
                            });
                        }
                    });
                </script>
            </div>
        </div>

        <div class="mx-auto px-3 sm:px-4 lg:px-6 -mt-4 sm:-mt-6 relative z-20" style="width:80%;">

            {{-- ============================================ --}}
            {{-- ALERTE HEURE NON DISPONIBLE --}}
            {{-- ============================================ --}}
            @if (isset($timeMismatch) && $timeMismatch && isset($availableTimesMessage))
                <div class="mb-6 bg-amber-50 border border-amber-200 p-4 rounded-2xl shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-amber-500"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-amber-800 text-sm">Heure non disponible</h4>
                            <p class="text-amber-700 text-sm mt-1">{{ $availableTimesMessage }}</p>
                            <p class="text-xs text-amber-500 mt-1">Nous affichons quand même les programmes disponibles pour cette route.</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ============================================ --}}
            {{-- RÉSULTATS --}}
            {{-- ============================================ --}}
            @if (isset($groupedRoutes) && $groupedRoutes->count() > 0)
                <div class="mb-6 sm:mb-8">

                    {{-- Results Header --}}
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm p-4 sm:p-5 border border-gray-100/80 mb-5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-[#e94f1b]/10 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-route text-[#e94f1b]"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg sm:text-xl font-black text-gray-900">Voyages disponibles</h2>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        @if(isset($searchParams['point_depart']) && $searchParams['point_depart'])
                                            <span class="text-xs font-bold text-gray-700 bg-gray-100 px-2.5 py-1 rounded-lg">
                                                <i class="fas fa-map-marker-alt text-[#e94f1b] mr-1"></i>{{ $searchParams['point_depart'] }}
                                            </span>
                                            <i class="fas fa-arrow-right text-[#e94f1b] text-[10px]"></i>
                                            <span class="text-xs font-bold text-gray-700 bg-gray-100 px-2.5 py-1 rounded-lg">
                                                <i class="fas fa-flag text-emerald-500 mr-1"></i>{{ $searchParams['point_arrive'] }}
                                            </span>
                                        @else
                                            <span class="text-xs font-bold text-purple-700 bg-purple-50 px-2.5 py-1 rounded-lg">
                                                <i class="fas fa-globe mr-1"></i>Toutes les destinations
                                            </span>
                                        @endif
                                        <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg">
                                            <i class="fas fa-calendar-alt mr-1"></i>{{ date('d/m/Y', strtotime($searchParams['date_depart'])) }}
                                        </span>
                                        @if(isset($searchParams['type_voyage']) && $searchParams['type_voyage'] === 'aller_retour' && isset($searchParams['date_retour']) && $searchParams['date_retour'])
                                            <i class="fas fa-arrows-alt-h text-purple-400 text-[10px]"></i>
                                            <span class="text-xs font-bold text-purple-700 bg-purple-50 px-2.5 py-1 rounded-lg">
                                                <i class="fas fa-calendar-check mr-1"></i>{{ date('d/m/Y', strtotime($searchParams['date_retour'])) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="bg-[#e94f1b] text-white px-4 py-2 rounded-xl font-black text-sm shadow-lg shadow-orange-500/20">
                                    {{ $groupedRoutes->count() }} trajet{{ $groupedRoutes->count() > 1 ? 's' : '' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Route Cards --}}
                    <div class="space-y-4">
                        @foreach ($groupedRoutes as $route)
                            <div class="route-card bg-white rounded-2xl shadow-md border border-gray-100/80 overflow-hidden">
                                <div class="p-5 sm:p-6">
                                    <div class="flex flex-col lg:flex-row lg:items-stretch gap-5 lg:gap-6">

                                        {{-- LEFT: Company & Route --}}
                                        <div class="flex-1 min-w-0">
                                            {{-- Company --}}
                                            <div class="flex items-center gap-3 mb-4">
                                                <div class="w-12 h-12 bg-gradient-to-br from-[#e94f1b] to-orange-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md shadow-orange-500/20">
                                                    <i class="fas fa-bus text-white text-lg"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <h3 class="text-base sm:text-lg font-black text-gray-900 tracking-tight truncate">
                                                        <span class="text-[#e94f1b]">{{ $route->compagnie->sigle ?? '' }}</span>
                                                        <span class="font-medium text-gray-500 ml-1">{{ $route->gare_depart ? $route->gare_depart->nom_gare : ($route->compagnie->name ?? 'Compagnie') }}</span>
                                                    </h3>
                                                </div>
                                            </div>

                                            {{-- Route Timeline --}}
                                            <div class="flex items-center gap-3 sm:gap-4">
                                                {{-- Departure --}}
                                                <div class="text-center sm:text-left flex-shrink-0">
                                                    <p class="font-black text-gray-900 text-sm sm:text-base leading-tight">{{ $route->point_depart }}</p>
                                                    @if($route->gare_depart)
                                                        <p class="text-[10px] text-gray-400 font-semibold mt-0.5 flex items-center gap-1">
                                                            <i class="fas fa-building"></i>{{ $route->gare_depart->nom_gare }}
                                                        </p>
                                                    @endif
                                                </div>

                                                {{-- Timeline line --}}
                                                <div class="flex-1 flex flex-col items-center gap-1 min-w-[80px]">
                                                    <span class="text-[10px] font-black text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full whitespace-nowrap">
                                                        <i class="fas fa-clock mr-0.5"></i>{{ $route->durer_parcours }}
                                                    </span>
                                                    <div class="w-full relative flex items-center">
                                                        <div class="w-2 h-2 rounded-full bg-[#e94f1b] flex-shrink-0 z-10"></div>
                                                        <div class="timeline-line flex-1"></div>
                                                        <div class="absolute left-1/2 -translate-x-1/2 bg-white px-1">
                                                            <i class="fas fa-bus text-[#e94f1b] text-[10px]"></i>
                                                        </div>
                                                        <div class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0 z-10"></div>
                                                    </div>
                                                </div>

                                                {{-- Arrival --}}
                                                <div class="text-center sm:text-right flex-shrink-0">
                                                    <p class="font-black text-gray-900 text-sm sm:text-base leading-tight">{{ $route->point_arrive }}</p>
                                                    @if($route->gare_arrivee)
                                                        <p class="text-[10px] text-gray-400 font-semibold mt-0.5 flex items-center gap-1 justify-end">
                                                            <i class="fas fa-building"></i>{{ $route->gare_arrivee->nom_gare }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Schedules --}}
                                            <div class="mt-4">
                                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.15em] mb-2 flex items-center gap-1.5">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                                    Horaires & Disponibilité
                                                </p>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($route->aller_horaires as $horaire)
                                                        @php
                                                            $occupancyRate = ($horaire['reserved_count'] / $horaire['total_seats']) * 100;
                                                            $isFull = $horaire['reserved_count'] >= $horaire['total_seats'];
                                                            $isAlmost = $occupancyRate > 80;
                                                            $chipBg = $isFull ? 'bg-red-50 border-red-200 text-red-600' : ($isAlmost ? 'bg-amber-50 border-amber-200 text-amber-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700');
                                                            $dotColor = $isFull ? 'bg-red-400' : ($isAlmost ? 'bg-amber-400' : 'bg-emerald-400');
                                                        @endphp
                                                        <div onclick="showVehicleDetails('{{ $horaire['vehicule_id'] ?? 0 }}', '{{ $horaire['id'] }}', '{{ $searchParams['date_depart'] }}', '{{ substr($horaire['heure_depart'], 0, 5) }}')"
                                                             class="schedule-chip flex items-center gap-2 px-3 py-2 rounded-xl border {{ $chipBg }} cursor-pointer group shadow-sm"
                                                             title="Cliquez pour voir les places disponibles">
                                                            <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
                                                            <span class="font-black text-sm">{{ substr($horaire['heure_depart'], 0, 5) }}</span>
                                                            <div class="w-px h-3 bg-current opacity-15"></div>
                                                            <div class="flex items-center gap-1 text-[10px] font-bold opacity-80">
                                                                <i class="fas fa-couch"></i>
                                                                <span>{{ $horaire['reserved_count'] }}/{{ $horaire['total_seats'] }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        {{-- RIGHT: Price & Book --}}
                                        <div class="flex lg:flex-col items-center lg:items-end justify-between lg:justify-center gap-4 border-t lg:border-t-0 lg:border-l border-gray-100 pt-4 lg:pt-0 lg:pl-6 lg:min-w-[180px]">
                                            <div class="text-right">
                                                <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest">Prix à partir de</p>
                                                <p class="price-value text-2xl sm:text-3xl font-black text-[#e94f1b] leading-tight mt-0.5">
                                                    {{ number_format($route->montant_billet, 0, ',', ' ') }}
                                                    <span class="text-xs font-bold text-gray-400">FCFA</span>
                                                </p>
                                            </div>

                                            @php
                                                $routeData = [
                                                    'id' => $route->id,
                                                    'compagnie_id' => $route->compagnie_id ?? null,
                                                    'compagnie' => $route->compagnie->name ?? 'Compagnie',
                                                    'sigle' => $route->compagnie->sigle ?? '',
                                                    'point_depart' => $route->point_depart,
                                                    'point_arrive' => $route->point_arrive,
                                                    'gare_depart' => $route->gare_depart,
                                                    'gare_arrivee' => $route->gare_arrivee,
                                                    'montant_billet' => $route->montant_billet,
                                                    'durer_parcours' => $route->durer_parcours,
                                                    'aller_horaires' => $route->aller_horaires,
                                                    'retour_horaires' => $route->retour_horaires,
                                                    'has_retour' => $route->has_retour,
                                                    'date_fin' => $route->date_fin ?? null,
                                                    'capacity' => $route->capacity ?? 50,
                                                ];
                                            @endphp
                                            <button type="button"
                                                data-route="{{ json_encode($routeData, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP) }}"
                                                data-date="{{ $searchParams['date_depart'] }}"
                                                onclick="handleReservationClick(this)"
                                                class="bg-gradient-to-r from-[#e94f1b] to-orange-500 text-white px-6 sm:px-8 py-3 rounded-xl font-black text-sm shadow-lg shadow-orange-500/25 hover:shadow-orange-500/40 hover:from-[#d4430f] hover:to-orange-600 transition-all duration-300 active:scale-95 flex items-center gap-2 whitespace-nowrap">
                                                <span>RÉSERVER</span>
                                                <i class="fas fa-chevron-right text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            @elseif(isset($groupedRoutes))
                {{-- Empty State --}}
                <div class="bg-white rounded-2xl shadow-md p-10 sm:p-14 text-center border border-gray-100/80 mt-6">
                    <div class="w-24 h-24 bg-gradient-to-br from-orange-100 to-blue-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                        <i class="fas fa-route text-4xl text-[#e94f1b]"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-2">Aucun trajet trouvé</h3>
                    <p class="text-gray-500 font-medium max-w-md mx-auto mb-8">Nous n'avons trouvé aucun programme pour cette recherche. Essayez de modifier vos critères.</p>
                    <div class="flex flex-wrap justify-center gap-3">
                        <button onclick="document.getElementById('date_depart').focus()" class="px-5 py-2.5 bg-blue-50 text-blue-700 rounded-xl font-bold text-sm hover:bg-blue-100 transition-colors flex items-center gap-2">
                            <i class="fas fa-calendar-alt"></i> Changer la date
                        </button>
                        <button onclick="document.getElementById('point_depart').focus()" class="px-5 py-2.5 bg-orange-50 text-[#e94f1b] rounded-xl font-bold text-sm hover:bg-orange-100 transition-colors flex items-center gap-2">
                            <i class="fas fa-map-marker-alt"></i> Modifier le trajet
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Modal Sélection Gare (conservé) -->
    <div id="gareSelectionModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4 modal-overlay">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 transform transition-all">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Sélectionnez votre gare</h2>
                <button onclick="closeGareSelectionModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>
            <div class="mb-6">
                <p class="text-sm text-gray-600 mb-4">De quelle gare souhaitez-vous partir ?</p>
                <div id="gareOptions" class="space-y-3"></div>
            </div>
        </div>
    </div>
    
    <!-- Modal Type de Voyage (conservé, amélioré visuellement) -->
    <div id="tripTypeModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4 modal-overlay">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 transform transition-all">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-bus text-[#e94f1b]"></i>
                        Type de voyage
                    </h2>
                    <p id="tripRouteInfo" class="text-sm text-gray-600 mt-2"></p>
                </div>
                <button onclick="closeTripTypeModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 gap-4 mt-6">
                <button onclick="selectTripType('simple')" 
                    class="p-6 border-2 border-gray-200 rounded-xl hover:border-[#e94f1b] hover:bg-orange-50 transition-all duration-300 text-left group">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center group-hover:bg-[#e94f1b] transition-colors">
                                <i class="fas fa-arrow-right text-[#e94f1b] text-xl group-hover:text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-900">Aller Simple</h3>
                                <p id="simplePrice" class="text-sm text-gray-600">--  FCFA</p>
                            </div>
                        </div>
                    </div>
                </button>
                <button onclick="selectTripType('round')" id="roundTripBtn"
                    class="p-6 border-2 border-gray-200 rounded-xl hover:border-[#e94f1b] hover:bg-orange-50 transition-all duration-300 text-left group disabled:opacity-50 disabled:cursor-not-allowed">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-[#e94f1b] transition-colors">
                                <i class="fas fa-exchange-alt text-blue-500 text-xl group-hover:text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-900">Aller-Retour</h3>
                                <p id="roundPrice" class="text-sm text-gray-600">-- FCFA</p>
                                <p id="roundStatus" class="text-xs text-gray-500"></p>
                            </div>
                        </div>
                    </div>
                </button>
            </div>
            <div class="mt-6 flex justify-end">
                <button onclick="closeTripTypeModal()" class="px-6 py-2 bg-gray-100 rounded-lg font-bold hover:bg-gray-200 transition-colors">
                    Annuler
                </button>
            </div>
        </div>
    </div>
    
    <!-- ============================================= -->
    <!-- MODAL UNIFIÉ DE RÉSERVATION (REDESIGNED)      -->
    <!-- ============================================= -->
    <div id="reservationModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50 overflow-y-auto modal-overlay">
        <div class="min-h-screen flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-5xl overflow-hidden reservation-modal-content" style="max-height: 95vh;">
                
                <!-- ===== HEADER PREMIUM ===== -->
                <div class="relative overflow-hidden">
                    <!-- Gradient Background -->
                    <div class="bg-gradient-to-r from-[#e94f1b] via-orange-500 to-amber-500 px-6 py-5">
                        <!-- Decorative circles -->
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                        <div class="absolute bottom-0 left-10 w-20 h-20 bg-white/5 rounded-full translate-y-1/2"></div>
                        
                        <div class="relative flex justify-between items-start">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <div class="w-8 h-8 bg-white/20 backdrop-blur rounded-lg flex items-center justify-center">
                                        <i class="fas fa-ticket-alt text-white text-sm"></i>
                                    </div>
                                    <h2 class="text-xl font-black text-white tracking-tight">Réservation</h2>
                                </div>
                                <div id="reservationProgramInfo" class="text-white/90 text-sm font-medium"></div>
                            </div>
                            <button onclick="closeReservationModal()" class="w-9 h-9 rounded-xl bg-white/20 backdrop-blur hover:bg-white/30 flex items-center justify-center transition-all">
                                <i class="fas fa-times text-white"></i>
                            </button>
                        </div>

                        <!-- ===== STEPPER ===== -->
                        <div class="mt-5 flex items-center justify-between" id="reservationStepper">
                            <div class="stepper-item active" data-step="1">
                                <div class="stepper-circle">
                                    <span class="stepper-number">1</span>
                                    <i class="fas fa-check stepper-check"></i>
                                </div>
                                <span class="stepper-label">Places</span>
                            </div>
                            <div class="stepper-line" id="stepperLine1"></div>
                            <div class="stepper-item" data-step="2">
                                <div class="stepper-circle">
                                    <span class="stepper-number">2</span>
                                    <i class="fas fa-check stepper-check"></i>
                                </div>
                                <span class="stepper-label">Sièges</span>
                            </div>
                            <div class="stepper-line" id="stepperLine2"></div>
                            <div class="stepper-item" data-step="3">
                                <div class="stepper-circle">
                                    <span class="stepper-number">3</span>
                                    <i class="fas fa-check stepper-check"></i>
                                </div>
                                <span class="stepper-label">Passagers</span>
                            </div>
                            <div class="stepper-line" id="stepperLine3"></div>
                            <div class="stepper-item" data-step="4">
                                <div class="stepper-circle">
                                    <span class="stepper-number">4</span>
                                    <i class="fas fa-check stepper-check"></i>
                                </div>
                                <span class="stepper-label">Paiement</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== CONTENU DES ÉTAPES ===== -->
                <div class="p-6 md:p-8" style="max-height: calc(95vh - 180px); overflow-y: auto;">
                    
                    <!-- ═══ Étape 1: Nombre de places ═══ -->
                    <div id="step1" class="step-content active-step">
                        <div class="text-center mb-6">
                            <div class="inline-flex items-center gap-2 bg-orange-50 text-[#e94f1b] px-4 py-2 rounded-full text-sm font-bold mb-3">
                                <i class="fas fa-users"></i>
                                <span>Étape 1 sur 4</span>
                            </div>
                            <h3 class="text-2xl font-black text-gray-900">Combien de places ?</h3>
                            <p class="text-gray-500 mt-1">Sélectionnez le nombre de passagers</p>
                        </div>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8 max-w-2xl mx-auto">
                            @for ($i = 1; $i <= 8; $i++)
                                <button onclick="selectNumberOfPlaces({{ $i }}, this)"
                                    class="place-count-btn group relative p-5 border-2 border-gray-200 rounded-2xl hover:border-[#e94f1b] hover:bg-orange-50 transition-all duration-300 text-center">
                                    <div class="text-3xl font-black text-gray-800 group-hover:text-[#e94f1b] transition-colors">{{ $i }}</div>
                                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mt-1">place{{ $i > 1 ? 's' : '' }}</div>
                                    <div class="absolute inset-0 border-2 border-[#e94f1b] rounded-2xl opacity-0 scale-105 transition-all duration-300 pointer-events-none"></div>
                                </button>
                            @endfor
                        </div>

                        <div class="flex justify-between items-center">
                            <button onclick="backToTripTypeChoice()"
                                class="px-6 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl font-bold transition-all flex items-center gap-2 text-sm">
                                <i class="fas fa-arrow-left text-xs"></i>
                                <span>Retour</span>
                            </button>
                            <button id="nextStepBtn" onclick="showSeatSelection()"
                                class="bg-gradient-to-r from-[#e94f1b] to-orange-500 text-white px-8 py-3.5 rounded-2xl font-bold hover:shadow-lg hover:shadow-orange-500/25 transition-all duration-300 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:shadow-none flex items-center gap-3 text-sm"
                                disabled>
                                <span>Choisir les sièges</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ═══ Étape 2: Sélection des sièges ALLER ═══ -->
                    <div id="step2" class="step-content hidden">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                            <div>
                                <div class="inline-flex items-center gap-2 bg-orange-50 text-[#e94f1b] px-3 py-1.5 rounded-full text-xs font-bold mb-2">
                                    <i class="fas fa-couch"></i>
                                    <span>Étape 2 — Sièges Aller</span>
                                </div>
                                <h3 class="text-xl font-black text-gray-900">Choisissez vos places</h3>
                            </div>
                            <div class="flex items-center gap-3">
                                <span id="selectedSeatsCount" class="px-4 py-2 bg-[#e94f1b]/10 text-[#e94f1b] rounded-xl font-bold text-sm">
                                    0 place sélectionnée
                                </span>
                                <button onclick="backToStep1()"
                                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-xl text-gray-600 font-medium text-sm transition-colors flex items-center gap-2">
                                    <i class="fas fa-arrow-left text-xs"></i>
                                    <span>Retour</span>
                                </button>
                            </div>
                        </div>

                        <!-- Plan des sièges -->
                        <div id="seatSelectionArea" class="mb-6"></div>

                        <!-- Légende améliorée -->
                        <div class="flex flex-wrap items-center gap-x-6 gap-y-2 mb-6 p-4 bg-gray-50 rounded-2xl">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-emerald-500 rounded-lg shadow-sm flex items-center justify-center">
                                    <i class="fas fa-couch text-white text-[10px]"></i>
                                </div>
                                <span class="text-xs font-medium text-gray-600">Disponible</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-[#e94f1b] rounded-lg shadow-sm flex items-center justify-center">
                                    <i class="fas fa-check text-white text-[10px]"></i>
                                </div>
                                <span class="text-xs font-medium text-gray-600">Sélectionné</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-red-400/80 rounded-lg shadow-sm flex items-center justify-center">
                                    <i class="fas fa-times text-white text-[10px]"></i>
                                </div>
                                <span class="text-xs font-medium text-gray-600">Réservé</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-sky-500 rounded-lg shadow-sm flex items-center justify-center">
                                    <i class="fas fa-couch text-white text-[10px]"></i>
                                </div>
                                <span class="text-xs font-medium text-gray-600">Côté gauche</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-between hidden" id="step2OldActions">
                            <button onclick="backToStep1()"
                                class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl font-bold transition-all flex items-center gap-2 text-sm">
                                <i class="fas fa-arrow-left text-xs"></i>
                                <span>Retour</span>
                            </button>
                            <button id="showPassengerInfoBtn" onclick="showPassengerInfo()"
                                class="bg-gradient-to-r from-[#e94f1b] to-orange-500 text-white px-8 py-3 rounded-2xl font-bold hover:shadow-lg hover:shadow-orange-500/25 transition-all duration-300 disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-3 text-sm"
                                disabled>
                                <span>Informations passagers</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ═══ Étape 2.5: Sièges RETOUR (si Aller-Retour) ═══ -->
                    <div id="step2_5" class="step-content hidden">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                            <div>
                                <div class="inline-flex items-center gap-2 bg-blue-50 text-blue-600 px-3 py-1.5 rounded-full text-xs font-bold mb-2">
                                    <i class="fas fa-undo"></i>
                                    <span>Étape 2 — Sièges Retour</span>
                                </div>
                                <h3 class="text-xl font-black text-gray-900">Places pour le retour</h3>
                            </div>
                            <div class="flex items-center gap-3">
                                <span id="selectedSeatsCountRetour" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-xl font-bold text-sm">0 place sélectionnée</span>
                                <button onclick="backToStep2()"
                                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-xl text-gray-600 font-medium text-sm transition-colors flex items-center gap-2">
                                    <i class="fas fa-arrow-left text-xs"></i>
                                    <span>Retour</span>
                                </button>
                            </div>
                        </div>

                        <!-- Info retour -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-2xl mb-6 border border-blue-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-undo text-white"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-blue-900 text-sm">Voyage Retour</p>
                                    <p id="returnProgramInfo" class="text-xs text-blue-700"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Plan retour -->
                        <div id="seatSelectionAreaRetour" class="mb-6"></div>

                        <!-- Légende -->
                        <div class="flex flex-wrap items-center gap-x-6 gap-y-2 mb-6 p-4 bg-gray-50 rounded-2xl">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-emerald-500 rounded-lg shadow-sm"></div>
                                <span class="text-xs font-medium text-gray-600">Disponible</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-blue-600 rounded-lg shadow-sm"></div>
                                <span class="text-xs font-medium text-gray-600">Sélectionné</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-red-400/80 rounded-lg shadow-sm"></div>
                                <span class="text-xs font-medium text-gray-600">Réservé</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-sky-500 rounded-lg shadow-sm"></div>
                                <span class="text-xs font-medium text-gray-600">Côté gauche</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-between hidden" id="step2_5OldActions">
                            <button onclick="backToStep2()"
                                class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl font-bold transition-all flex items-center gap-2 text-sm">
                                <i class="fas fa-arrow-left text-xs"></i>
                                <span>Retour</span>
                            </button>
                            <button id="showPassengerInfoBtnRetour" onclick="proceedToPassengerInfoFromRetour()"
                                class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-3 rounded-2xl font-bold hover:shadow-lg hover:shadow-blue-500/25 transition-all duration-300 disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-3 text-sm"
                                disabled>
                                <span>Informations passagers</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ═══ Étape 3: Informations passagers ═══ -->
                    <div id="step3" class="step-content hidden">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                            <div>
                                <div class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-full text-xs font-bold mb-2">
                                    <i class="fas fa-user-edit"></i>
                                    <span>Étape 3 — Passagers</span>
                                </div>
                                <h3 class="text-xl font-black text-gray-900">Informations des passagers</h3>
                            </div>
                            <button onclick="backFromPassengerInfo()"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-xl text-gray-600 font-medium text-sm transition-colors flex items-center gap-2">
                                <i class="fas fa-arrow-left text-xs"></i>
                                <span>Retour aux places</span>
                            </button>
                        </div>

                        <div id="passengersFormArea" class="space-y-6 mb-8"></div>

                        <!-- Actions -->
                        <div class="flex justify-between">
                            <button onclick="backFromPassengerInfo()"
                                class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl font-bold transition-all flex items-center gap-2 text-sm">
                                <i class="fas fa-arrow-left text-xs"></i>
                                <span>Retour</span>
                            </button>
                            <button id="confirmReservationBtn" onclick="confirmReservation()"
                                class="bg-gradient-to-r from-emerald-500 to-green-500 text-white px-8 py-3.5 rounded-2xl font-bold hover:shadow-lg hover:shadow-green-500/25 transition-all duration-300 flex items-center gap-3 text-sm">
                                <i class="fas fa-shield-alt"></i>
                                <span>Confirmer & Payer</span>
                                <i class="fas fa-arrow-right text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================= -->
    <!-- STYLES PREMIUM                                -->
    <!-- ============================================= -->
    <style>
        /* === Modal Animations === */
        .modal-overlay {
            animation: modalFadeIn 0.3s ease-out;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .reservation-modal-content {
            animation: modalSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes modalSlideUp {
            from { opacity: 0; transform: translateY(30px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* === Step Content Transitions === */
        .step-content {
            animation: stepFadeIn 0.4s ease-out;
        }
        .step-content.hidden { display: none; }
        @keyframes stepFadeIn {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* === Stepper === */
        .stepper-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            min-width: 60px;
        }
        .stepper-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }
        .stepper-number {
            color: rgba(255,255,255,0.7);
            font-weight: 800;
            font-size: 13px;
            transition: all 0.3s;
        }
        .stepper-check {
            display: none;
            color: white;
            font-size: 11px;
        }
        .stepper-label {
            font-size: 10px;
            font-weight: 700;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.3s;
        }
        .stepper-line {
            flex: 1;
            height: 2px;
            background: rgba(255,255,255,0.15);
            border-radius: 1px;
            margin: 0 4px;
            margin-bottom: 22px;
            position: relative;
            overflow: hidden;
        }
        .stepper-line::after {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 0;
            background: white;
            border-radius: 1px;
            transition: width 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .stepper-line.filled::after {
            width: 100%;
        }

        /* Active step */
        .stepper-item.active .stepper-circle {
            background: white;
            box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
        }
        .stepper-item.active .stepper-number {
            color: #e94f1b;
        }
        .stepper-item.active .stepper-label {
            color: white;
        }

        /* Completed step */
        .stepper-item.completed .stepper-circle {
            background: rgba(255,255,255,0.9);
        }
        .stepper-item.completed .stepper-number {
            display: none;
        }
        .stepper-item.completed .stepper-check {
            display: block;
            color: #10b981;
        }
        .stepper-item.completed .stepper-label {
            color: rgba(255,255,255,0.8);
        }

        /* === Place Count Button === */
        .place-count-btn.active {
            border-color: #e94f1b !important;
            background: linear-gradient(135deg, #fff7ed, #ffedd5) !important;
            box-shadow: 0 0 0 3px rgba(233, 79, 27, 0.15), 0 4px 12px rgba(233, 79, 27, 0.1) !important;
        }
        .place-count-btn.active > div:first-child {
            color: #e94f1b !important;
        }

        /* === Seat styles === */
        .seat {
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
        }
        .seat:hover {
            transform: scale(1.12);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .seat.selected {
            transform: scale(1.08);
            box-shadow: 0 0 0 3px rgba(233, 79, 27, 0.3), 0 4px 12px rgba(233, 79, 27, 0.2);
        }
        .seat.reserved {
            cursor: not-allowed;
            opacity: 0.45;
        }
        .seat.reserved:hover {
            transform: none;
            box-shadow: none;
        }

        /* === Responsive === */
        @media (max-width: 640px) {
            .stepper-label { font-size: 8px; }
            .stepper-circle { width: 26px; height: 26px; }
            .stepper-number { font-size: 11px; }
        }

        /* === SweetAlert Premium Overrides === */
        .swal2-popup.rounded-2xl {
            border-radius: 1.5rem !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        }
        .swal2-popup {
            font-family: inherit !important;
        }
        .swal2-title {
            font-weight: 800 !important;
            font-size: 1.3rem !important;
        }
        .swal2-confirm {
            border-radius: 0.75rem !important;
            font-weight: 700 !important;
            padding: 0.65rem 1.5rem !important;
            box-shadow: 0 4px 14px rgba(233, 79, 27, 0.3) !important;
            transition: all 0.3s !important;
        }
        .swal2-cancel {
            border-radius: 0.75rem !important;
            font-weight: 600 !important;
            padding: 0.65rem 1.5rem !important;
        }
        .swal2-confirm:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 20px rgba(233, 79, 27, 0.4) !important;
        }
        div:where(.swal2-container) {
            backdrop-filter: blur(4px);
        }

        /* === Passenger Card Styling === */
        #passengersFormArea .bg-gray-50 {
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
            transition: all 0.3s;
        }
        #passengersFormArea .bg-gray-50:hover {
            border-color: #e94f1b;
            box-shadow: 0 4px 12px rgba(233, 79, 27, 0.08);
        }
        #passengersFormArea input {
            border-radius: 0.75rem;
        }
        #passengersFormArea input:focus {
            border-color: #e94f1b;
            box-shadow: 0 0 0 3px rgba(233, 79, 27, 0.1);
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.cinetpay.com/seamless/main.js"></script>
     <script>
        function initAutocompleteUser() {
            const options = {
                componentRestrictions: { country: "ci" }, // Restreindre Ã  la CÃ´te d'Ivoire
                fields: ["formatted_address", "geometry", "name"],
            };

            const inputDepart = document.getElementById("point_depart");
            const inputArrive = document.getElementById("point_arrive");

            if (inputDepart) {
                new google.maps.places.Autocomplete(inputDepart, options);
            }

            if (inputArrive) {
                new google.maps.places.Autocomplete(inputArrive, options);
            }
        }
    </script>
       <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&loading=async&callback=initAutocompleteUser"
        async defer></script>
    <script>
        // Variables globales
         var currentProgramId = null;
        var currentSelectedProgram = null; 
        var selectedNumberOfPlaces = 0;
        var selectedSeats = [];
        var reservedSeats = [];
        var vehicleDetails = null;
        var currentRequestId = 0;
        var selectedSeatsRetour = [];
var reservedSeatsRetour = [];
var vehicleDetailsRetour = null;
var currentRetourProgramId = null;
        var userWantsAllerRetour = false;
        var selectedReturnDate = null; // Date de retour sÃ©lectionnÃ©e pour Aller-Retour


        window.currentUser = @json(Auth::user()); // Injecter l'utilisateur connecté
        window.handleReservationClick = function(button) {
            console.log("Bouton réserver cliqué"); // Debug
            
            // Vérification si le profil est rempli
            const user = window.currentUser;
            const isProfileComplete = user && 
                                    user.contact && user.contact.trim() !== "" && 
                                    user.nom_urgence && user.nom_urgence.trim() !== "" && 
                                    user.contact_urgence && user.contact_urgence.trim() !== "";

            if (!isProfileComplete) {
                let html = '<div class="text-left space-y-4 p-2">';
                html += '<p class="text-sm text-gray-600 mb-4">Pour votre sécurité, veuillez renseigner ces informations avant de continuer.</p>';
                
                if (!user.contact || user.contact.trim() === "") {
                    html += `<div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Votre numéro de téléphone</label>
                                <input type="text" id="swal_contact" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94f1b]" value="${user.contact || ''}" placeholder="07XXXXXXXX">
                             </div>`;
                }

                if (!user.nom_urgence || user.nom_urgence.trim() === "") {
                    html += `<div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Nom du contact d'urgence</label>
                                <input type="text" id="swal_nom_urgence" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94f1b]" value="${user.nom_urgence || ''}" placeholder="Nom et Prénom">
                             </div>`;
                }

                html += `<div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Lien de parenté</label>
                            <select id="swal_lien_parente" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94f1b]">
                                <option value="">Sélectionner</option>
                                <option value="Père" ${user.lien_parente_urgence == 'Père' ? 'selected' : ''}>Père</option>
                                <option value="Mère" ${user.lien_parente_urgence == 'Mère' ? 'selected' : ''}>Mère</option>
                                <option value="Frère" ${user.lien_parente_urgence == 'Frère' ? 'selected' : ''}>Frère</option>
                                <option value="Sœur" ${user.lien_parente_urgence == 'Sœur' ? 'selected' : ''}>Sœur</option>
                                <option value="Conjoint(e)" ${user.lien_parente_urgence == 'Conjoint(e)' ? 'selected' : ''}>Conjoint(e)</option>
                                <option value="Ami(e)" ${user.lien_parente_urgence == 'Ami(e)' ? 'selected' : ''}>Ami(e)</option>
                                <option value="Autre" ${user.lien_parente_urgence == 'Autre' ? 'selected' : ''}>Autre</option>
                            </select>
                         </div>`;

                if (!user.contact_urgence || user.contact_urgence.trim() === "") {
                    html += `<div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Téléphone d'urgence</label>
                                <input type="text" id="swal_contact_urgence" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94f1b]" value="${user.contact_urgence || ''}" placeholder="01XXXXXXXX">
                             </div>`;
                }

                html += '</div>';

                Swal.fire({
                    icon: 'info',
                    title: '<span class="text-xl font-black">Complétez votre profil</span>',
                    html: html,
                    showCancelButton: true,
                    confirmButtonText: 'Enregistrer et continuer',
                    cancelButtonText: 'Plus tard',
                    confirmButtonColor: '#e94f1b',
                    cancelButtonColor: '#cbd5e0',
                    customClass: { popup: 'rounded-3xl' },
                    preConfirm: () => {
                        const contact = document.getElementById('swal_contact')?.value || user.contact;
                        const nom_urgence = document.getElementById('swal_nom_urgence')?.value || user.nom_urgence;
                        const lien_parente = document.getElementById('swal_lien_parente').value;
                        const contact_urgence = document.getElementById('swal_contact_urgence')?.value || user.contact_urgence;

                        if (!contact || contact.length !== 10) {
                            Swal.showValidationMessage('Le numéro de téléphone doit comporter 10 chiffres');
                            return false;
                        }
                        if (!nom_urgence || nom_urgence.trim() === "") {
                            Swal.showValidationMessage('Le nom du contact d\'urgence est requis');
                            return false;
                        }
                        if (!contact_urgence || contact_urgence.length !== 10) {
                            Swal.showValidationMessage('Le numéro d\'urgence doit comporter 10 chiffres');
                            return false;
                        }
                        if (contact === contact_urgence) {
                            Swal.showValidationMessage('Le numéro d\'urgence doit être différent du vôtre');
                            return false;
                        }

                        return { contact, nom_urgence, lien_parente_urgence: lien_parente, contact_urgence };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Mise à jour...',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });

                        $.ajax({
                            url: "{{ route('user.profile.update-emergency') }}",
                            method: "POST",
                            data: {
                                ...result.value,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.success) {
                                    window.currentUser = response.user;
                                    Swal.close();
                                    // Continuer le flux de réservation
                                    const routeDataJson = button.getAttribute('data-route');
                                    const dateDepartInitial = button.getAttribute('data-date');
                                    const routeData = JSON.parse(routeDataJson);
                                    showRouteTripTypeModal(routeData, dateDepartInitial);
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: xhr.responseJSON?.message || 'Une erreur est survenue lors de la mise à jour.'
                                });
                            }
                        });
                    }
                });
                return;
            }

            try {
                const routeDataJson = button.getAttribute('data-route');
                const dateDepartInitial = button.getAttribute('data-date');
                
                if (!routeDataJson) {
                    console.error("Pas de données data-route trouvées");
                    return;
                }

                const routeData = JSON.parse(routeDataJson);
                console.log('Données route:', routeData);
                
                const searchTypeVoyage = '{{ $searchParams["type_voyage"] ?? "aller_simple" }}';
                const searchDateRetour = '{{ $searchParams["date_retour"] ?? "" }}';

                if (searchTypeVoyage === 'aller_retour' && routeData.has_retour && searchDateRetour) {
                    window.userWantsAllerRetour = true;
                    window.userChoseAllerRetour = true;
                    window.selectedReturnDate = searchDateRetour;
                    window.currentRouteData = routeData;
                    window.currentDateDepart = dateDepartInitial;
                    showRouteDepartureTimes(routeData, dateDepartInitial, true); 
                } else {
                    // Toujours demander le type de voyage en premier si on n'a pas sélectionné Aller-Retour avec Date en amont
                    showRouteTripTypeModal(routeData, dateDepartInitial);
                }
            } catch (e) {
                console.error('Erreur JS lors du clic:', e);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Une erreur technique est survenue. Consultez la console.'
                });
            }
        };

        // Fonction pour inverser le point de dÃ©part et d'arrivÃ©e
        window.swapLocations = function() {
            const departInput = document.getElementById('point_depart');
            const arriveeInput = document.getElementById('point_arrive');
            
            if (departInput && arriveeInput) {
                const temp = departInput.value;
                departInput.value = arriveeInput.value;
                arriveeInput.value = temp;
                
                // Animation visuelle
                departInput.classList.add('ring-2', 'ring-green-400');
                arriveeInput.classList.add('ring-2', 'ring-green-400');
                setTimeout(() => {
                    departInput.classList.remove('ring-2', 'ring-green-400');
                    arriveeInput.classList.remove('ring-2', 'ring-green-400');
                }, 300);
            }
        };

        // Fonction handler pour le clic sur RÃ©server (gÃ¨re le parsing JSON depuis data attributes)
       

        // Configuration des types de rangées
        const typeRangeConfig = {
            '2x2': { placesGauche: 2, placesDroite: 2 },
            '2x3': { placesGauche: 2, placesDroite: 3 },
            '2x4': { placesGauche: 2, placesDroite: 4 },
            'Gamme Prestige': { placesGauche: 2, placesDroite: 2 },
            'Gamme Standard': { placesGauche: 2, placesDroite: 3 }
        };
 // --- NOUVELLE FONCTION: Modal de sÃ©lection des horaires pour les routes groupÃ©es ---
        window.showRouteSchedulesModal = function(routeData, dateDepart) {
            console.log('Ouverture modal sÃ©lection horaires:', routeData);
            
            // Stocker les donnÃ©es courantes
            window.currentRouteData = routeData;
            window.currentDateDepart = dateDepart;

            // Toujours demander le type de voyage d'abord
            showRouteTripTypeModal(routeData, dateDepart);
        };

        // Ã‰TAPE 1: Choix Type de Voyage (Aller Simple / Aller-Retour)
        window.showRouteTripTypeModal = function(routeData, dateDepart) {
            window.currentRouteData = routeData;
            window.currentDateDepart = dateDepart;
    // Conversion sécurisée du prix en nombre
    // On convertit d'abord en string, on enlève tout sauf chiffres et points, puis on parse
    let priceString = String(routeData.montant_billet || '0');
    let priceRaw = priceString.replace(/[^\d.]/g, '');
    const priceSimple = parseFloat(priceRaw) || 0;
    const priceReturn = priceSimple * 2;

    Swal.fire({
        title: '<i class="fas fa-bus text-[#e94f1b]"></i> Type de voyage',
        html: `
            <div class="text-left space-y-4">
                <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                    <p class="font-bold text-gray-900">${routeData.point_depart} <i class="fas fa-long-arrow-alt-right mx-1"></i> ${routeData.point_arrive}</p>
                    <div class="flex items-center gap-2 text-sm text-gray-600 mt-1">
                        <span class="flex items-center gap-1"><i class="fas fa-map-marker-alt text-xs text-[#e94f1b]"></i> ${routeData.gare_depart?.nom_gare || 'Ville'}</span>
                        <i class="fas fa-arrow-right text-[10px] text-gray-400"></i>
                        <span class="flex items-center gap-1"><i class="fas fa-flag text-xs text-green-500"></i> ${routeData.gare_arrivee?.nom_gare || 'Ville'}</span>
                    </div>
                        <p class="text-sm font-bold text-gray-800 mt-2">
                            <span class="font-bold">${routeData.sigle || ''}</span>
                            <span class="font-light text-gray-600">${routeData.compagnie}</span>
                        </p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="border-2 border-gray-200 rounded-lg p-4 text-center hover:border-[#e94f1b] hover:bg-orange-50 transition-all cursor-pointer" id="btnRouteSimple">
                        <i class="fas fa-arrow-right text-3xl text-gray-500 mb-2"></i>
                        <p class="font-bold">Aller Simple</p>
                        <p class="text-lg font-bold text-[#e94f1b]">${priceSimple.toLocaleString('fr-FR')} FCFA</p>
                    </div>
                    <div class="border-2 ${routeData.has_retour ? 'border-[#e94f1b] bg-orange-50' : 'border-gray-100 bg-gray-50 opacity-60 cursor-not-allowed'} rounded-lg p-4 text-center transition-all" id="btnRouteReturn">
                        <i class="fas fa-exchange-alt text-3xl ${routeData.has_retour ? 'text-[#e94f1b]' : 'text-gray-300'} mb-2"></i>
                        <p class="font-bold">Aller-Retour</p>
                        <p class="text-lg font-bold ${routeData.has_retour ? 'text-[#e94f1b]' : 'text-gray-400'}">${priceReturn.toLocaleString('fr-FR')} FCFA</p>
                        ${!routeData.has_retour ? '<p class="text-[10px] text-red-500 font-bold">Non disponible</p>' : '<p class="text-xs text-gray-500">Prix estimé</p>'}
                    </div>
                </div>
            </div>
        `,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Annuler',
                customClass: { popup: 'rounded-2xl' },
                didOpen: () => {
                    document.getElementById('btnRouteSimple').addEventListener('click', () => {
                        window.userWantsAllerRetour = false;
                        window.userChoseAllerRetour = false;
                        Swal.close();
                        // AprÃ¨s le type, on demande la date (Aller Simple)
                        showDepartureDateSelection(routeData); 
                    });
                    
                    if (routeData.has_retour) {
                        document.getElementById('btnRouteReturn').addEventListener('click', () => {
                            window.userWantsAllerRetour = true;
                            window.userChoseAllerRetour = true;
                            Swal.close();
                            // AprÃ¨s le type, on demande la date (Aller-Retour)
                            showDepartureDateSelection(routeData);
                        });
                        document.getElementById('btnRouteReturn').classList.add('cursor-pointer', 'hover:bg-orange-100');
                    }
                }
            });
        }

        // Ã‰TAPE 2: Choix de l'heure de dÃ©part
    function showRouteDepartureTimes(routeData, dateDepart, isAllerRetour) {
    const dateFormatted = new Date(dateDepart).toLocaleDateString('fr-FR', { 
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
    });

    // MODIFICATION MAJEURE : On ne filtre plus rien. On prend tout ce que la BDD donne.
    const validSchedules = routeData.aller_horaires || [];

    // Construire la grille
    let timeSlotsHtml = '';
    if (validSchedules.length > 0) {
        timeSlotsHtml = '<div class="grid grid-cols-2 gap-3 max-h-60 overflow-y-auto p-2">';
        validSchedules.forEach(h => {
            timeSlotsHtml += `
                <div class="route-time-btn border-2 border-green-200 bg-green-50 rounded-lg p-4 cursor-pointer hover:border-green-500 hover:bg-green-100 transition-all text-center"
                     data-id="${h.id}" data-time="${h.heure_depart}">
                    <p class="font-bold text-xl text-green-700">${h.heure_depart}</p>
                  <p class="text-sm text-gray-500"><i class="fas fa-arrow-right mr-1"></i> ${h.heure_arrive}</p>
                </div>
            `;
        });
        timeSlotsHtml += '</div>';
    } else {
        timeSlotsHtml = '<p class="text-center text-red-500 font-medium py-4">Aucun horaire de départ programmé pour cette date.</p>';
    }

    Swal.fire({
        title: '<i class="fas fa-clock text-green-600"></i> Heure de départ',
        html: `
            <div class="text-left space-y-4">
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                     <p class="font-bold text-gray-800">${routeData.point_depart} <i class="fas fa-long-arrow-alt-right mx-1"></i> ${routeData.point_arrive}</p>
                     <div class="flex items-center gap-2 text-[10px] text-gray-500 mt-1 mb-1">
                        <span><i class="fas fa-map-marker-alt"></i> ${routeData.gare_depart?.nom_gare || 'Ville'}</span>
                        <span>→</span>
                        <span><i class="fas fa-flag"></i> ${routeData.gare_arrivee?.nom_gare || 'Ville'}</span>
                     </div>
                     <p class="text-sm text-gray-600">${dateFormatted}</p>
                     <p class="text-xs text-${isAllerRetour ? 'orange' : 'green'}-600 font-semibold mt-1">
                        <i class="fas fa-${isAllerRetour ? 'exchange-alt' : 'arrow-right'} mr-1"></i>
                        ${isAllerRetour ? 'Aller-Retour' : 'Aller Simple'}
                     </p>
                </div>
                <p class="font-medium text-gray-700">Choisissez l'heure de départ :</p>
                ${timeSlotsHtml}
            </div>
        `,
        showCancelButton: true,
        showConfirmButton: false,
        cancelButtonText: 'Retour',
        customClass: { popup: 'rounded-2xl' },
        didOpen: () => {
            document.querySelectorAll('.route-time-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const progId = this.dataset.id;
            const time = this.dataset.time;
            
            // IMPORTANT: Stocker l'heure sélectionnée
            window.selectedDepartureTime = time;
            window.selectedAllerProgramId = progId;
            
            Swal.close();
            
            if (isAllerRetour) {
                // Si une date de retour a déjà été sélectionnée lors de la recherche, on l'utilise directement
                if (window.selectedReturnDate) {
                    loadReturnSchedulesForDate(routeData, window.selectedReturnDate);
                } else {
                    showReturnDateSelection(routeData, dateDepart);
                }
            } else {
                startReservationFromRoute(progId, dateDepart, false);
            }
        });
    });
}
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.cancel && routeData.has_retour) {
            showRouteTripTypeModal(routeData, dateDepart);
        }
    });
}
        // Ã‰TAPE 2.5: SÃ©lection de la date de retour (pour Aller-Retour) avec calendrier mensuel
        function showReturnDateSelection(routeData, dateDep) {
            const minDate = new Date(dateDep); // Date de retour minimum = date de dÃ©part
            minDate.setHours(0, 0, 0, 0);
            
            // Date max = date_fin du programme
            const maxDate = routeData.date_fin ? new Date(routeData.date_fin) : new Date(minDate.getFullYear(), 11, 31);
            
            let currentMonth = minDate.getMonth();
            let currentYear = minDate.getFullYear();
            
            function updateCalendar() {
                const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                                   'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                
                const calendarHtml = generateMonthlyCalendar(currentMonth, currentYear, minDate, maxDate, 'purple');
                
                Swal.update({
                    html: `
                        <div class="text-left space-y-4">
                            <div class="bg-purple-100 p-3 rounded-lg border border-purple-200">
                                <p class="font-bold text-purple-900">Retour : ${routeData.point_arrive} <i class="fas fa-long-arrow-alt-right mx-1"></i> ${routeData.point_depart}</p>
                                <p class="text-sm text-gray-700">Sélectionnez la date de votre retour</p>
                            </div>
                            <div class="bg-green-50 p-2 rounded border border-green-200 text-sm flex justify-between items-center">
                                <div><span class="font-bold text-green-700">Départ :</span> ${new Date(dateDep).toLocaleDateString('fr-FR')}</div>
                                <div class="text-xs bg-green-100 px-2 py-1 rounded font-bold">${window.selectedDepartureTime}</div>
                            </div>

                            <!-- Options rapides Retour -->
                            <div class="flex justify-center gap-4">
                                <button id="btnReturnSameDay" class="flex-1 bg-purple-50 hover:bg-purple-100 text-purple-700 px-3 py-2 rounded-lg font-bold border-2 border-purple-200 transition-all text-sm">
                                    Même jour
                                </button>
                                <button id="btnReturnNextDay" class="flex-1 bg-purple-50 hover:bg-purple-100 text-purple-700 px-3 py-2 rounded-lg font-bold border-2 border-purple-200 transition-all text-sm">
                                    Lendemain
                                </button>
                            </div>
                            
                            <!-- Navigation mois -->
                            <div class="flex items-center justify-between bg-gray-50 p-2 rounded">
                                <button id="prevMonthReturn" class="px-3 py-1 bg-white border rounded hover:bg-gray-100">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <span class="font-bold text-gray-800">${monthNames[currentMonth]} ${currentYear}</span>
                                <button id="nextMonthReturn" class="px-3 py-1 bg-white border rounded hover:bg-gray-100">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            
                            <!-- Calendrier -->
                            <div class="calendar-container">
                                ${calendarHtml}
                            </div>
                        </div>
                    `
                });
                
                // RÃ©attacher les Ã©vÃ©nements
                attachCalendarEvents();
            }
            
            function attachCalendarEvents() {
                // Bouton MÃªme Jour
                const btnSame = document.getElementById('btnReturnSameDay');
                if (btnSame) {
                    btnSame.addEventListener('click', () => {
                        const sameDayStr = new Date(dateDep).toISOString().split('T')[0];
                        window.selectedReturnDate = sameDayStr;
                        Swal.close();
                        loadReturnSchedulesForDate(routeData, sameDayStr);
                    });
                }

                // Bouton Lendemain
                const btnNext = document.getElementById('btnReturnNextDay');
                if (btnNext) {
                    btnNext.addEventListener('click', () => {
                        const nextDay = new Date(dateDep);
                        nextDay.setDate(nextDay.getDate() + 1);
                        const nextDayStr = nextDay.toISOString().split('T')[0];
                        window.selectedReturnDate = nextDayStr;
                        Swal.close();
                        loadReturnSchedulesForDate(routeData, nextDayStr);
                    });
                }
                // Navigation mois précédent
                const prevBtn = document.getElementById('prevMonthReturn');
                if (prevBtn) {
                    prevBtn.addEventListener('click', () => {
                        if (currentMonth === 0) {
                            currentMonth = 11;
                            currentYear--;
                        } else {
                            currentMonth--;
                        }
                        // Ne pas aller avant le mois de minDate
                        const checkDate = new Date(currentYear, currentMonth, 1);
                        if (checkDate >= new Date(minDate.getFullYear(), minDate.getMonth(), 1)) {
                            updateCalendar();
                        } else {
                            currentMonth = minDate.getMonth();
                            currentYear = minDate.getFullYear();
                        }
                    });
                }
                
                // Navigation mois suivant
                const nextBtn = document.getElementById('nextMonthReturn');
                if (nextBtn) {
                    nextBtn.addEventListener('click', () => {
                        if (currentMonth === 11) {
                            currentMonth = 0;
                            currentYear++;
                        } else {
                            currentMonth++;
                        }
                        // Ne pas aller après le mois de maxDate
                        const checkDate = new Date(currentYear, currentMonth, 1);
                        if (checkDate <= new Date(maxDate.getFullYear(), maxDate.getMonth(), 1)) {
                            updateCalendar();
                        } else {
                            currentMonth = maxDate.getMonth();
                            currentYear = maxDate.getFullYear();
                        }
                    });
                }
                
                // SÃ©lection de date
                const dayBtns = document.querySelectorAll('.calendar-day-btn');
                dayBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const selectedDate = this.dataset.date;
                        window.selectedReturnDate = selectedDate;
                        Swal.close();
                        loadReturnSchedulesForDate(routeData, selectedDate);
                    });
                });
            }
            
            // Ouvrir le modal initial
            Swal.fire({
                title: '<i class="fas fa-calendar-alt text-purple-600"></i> Date de retour',
                html: '', // Sera rempli par updateCalendar()
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonText: 'Retour',
                customClass: { popup: 'rounded-2xl' },
                width: '600px',
                didOpen: () => {
                    updateCalendar();
                }
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    showRouteDepartureTimes(routeData, dateDep, true); // Retour au choix de l'heure de dÃ©part
                }
            });
        }

        // Charger les horaires de retour pour une date spÃ©cifique
        async function loadReturnSchedulesForDate(routeData, returnDate) {
            Swal.fire({
                title: 'Chargement des horaires...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const paramsRetour = new URLSearchParams({
                    original_arrive: routeData.point_arrive,
                    original_depart: routeData.point_depart,
                    min_date: returnDate
                });
                const response = await fetch('{{ route("api.return-trips") }}?' + paramsRetour);
                const data = await response.json();

                Swal.close();

                if (data.success && data.return_trips && data.return_trips.length > 0) {
                    // Mettre Ã  jour routeData avec les nouveaux horaires de retour
                    const updatedRouteData = {
                        ...routeData,
                        retour_horaires: data.return_trips
                    };
                    showRouteReturnTimes(updatedRouteData, returnDate);
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Aucun horaire disponible',
                        text: 'Aucun horaire de retour n\'est disponible pour cette date. Veuillez choisir une autre date.',
                        confirmButtonText: 'Choisir une autre date'
                    }).then(() => {
                        showReturnDateSelection(routeData, window.currentDateDepart);
                    });
                }
            } catch (error) {
                console.error('Erreur:', error);
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Impossible de charger les horaires de retour.'
                });
            }
        }

        // Ã‰TAPE 3: Choix de l'heure de retour (si Aller-Retour)
 function showRouteReturnTimes(routeData, returnDate) { // Note: le 2ème paramètre est la date de retour
    const validReturnSchedules = routeData.retour_horaires || [];
    
    // --- CORRECTION DEBUT : Logique de filtrage des heures ---
    
    // 1. Récupérer la date de départ initiale
    // window.currentDateDepart ou window.outboundDate contient la date aller
    const dateDepartStr = window.currentDateDepart || window.outboundDate; 
    
    // On compare les dates (format string YYYY-MM-DD)
    const isSameDay = (dateDepartStr === returnDate);

    // 2. Convertir l'heure de départ choisie en minutes
    let departureTimeMinutes = 0;
    if (window.selectedDepartureTime) {
        const [depH, depM] = window.selectedDepartureTime.split(':').map(Number);
        departureTimeMinutes = (depH * 60) + depM;
    }

    // 3. Estimer la durée du trajet
    let durationMinutes = 0;
    if (routeData.durer_parcours) {
        // Essai de parsing si format "05:00" ou "5h30"
        const match = String(routeData.durer_parcours).match(/(\d+)/g);
        if (match && match.length >= 2) {
            durationMinutes = (parseInt(match[0]) * 60) + parseInt(match[1]);
        } else if (match && match.length === 1) {
             durationMinutes = parseInt(match[0]) * 60; // Juste des heures
        } else {
            durationMinutes = 240; // 4h par défaut si inconnu
        }
    }
    
    // Heure minimum acceptable = Départ + Durée + 60min de battement
    const minReturnMinutes = departureTimeMinutes + durationMinutes + 60; 

    // --- CORRECTION FIN ---

    // Filtrer les horaires pour l'affichage
    const availableSchedules = validReturnSchedules.filter(h => {
        if (!isSameDay) return true; // Si pas le même jour, on affiche tout

        const [retH, retM] = h.heure_depart.split(':').map(Number);
        const returnMinutes = (retH * 60) + retM;

        // On ne garde que ceux qui sont APRES l'arrivée estimée
        return returnMinutes > minReturnMinutes;
    });

    // Construire la grille retour
    let timeSlotsHtml = '';
    
    if (availableSchedules.length > 0) {
        timeSlotsHtml = '<div class="grid grid-cols-2 gap-3 max-h-60 overflow-y-auto p-2">';
        availableSchedules.forEach(h => {
            timeSlotsHtml += `
                <div class="route-return-btn border-2 border-blue-200 bg-blue-50 rounded-lg p-4 cursor-pointer hover:border-blue-500 hover:bg-blue-100 transition-all text-center"
                     data-id="${h.id}" data-time="${h.heure_depart}">
                    <p class="font-bold text-xl text-blue-700">${h.heure_depart}</p>
                    <p class="text-sm text-gray-500">→ ${h.heure_arrive}</p>
                </div>
            `;
        });
        timeSlotsHtml += '</div>';
    } else {
        if (isSameDay && validReturnSchedules.length > 0) {
             timeSlotsHtml = `
                <div class="text-center text-orange-500 mb-4 bg-orange-50 p-3 rounded border border-orange-200">
                    <p class="font-bold">Aucun retour possible ce jour.</p>
                    <p class="text-sm">Départ à ${window.selectedDepartureTime}. Le bus n'arrivera pas à temps pour reprendre un retour aujourd'hui.</p>
                    <button class="mt-2 bg-white border border-orange-300 px-3 py-1 rounded text-sm hover:bg-orange-100" onclick="showReturnDateSelection(window.currentRouteData, '${dateDepartStr}')">Choisir le lendemain</button>
                </div>`;
        } else {
            timeSlotsHtml = '<div class="text-center text-orange-500 mb-4"><p>Aucun horaire retour disponible.</p></div>';
        }
    }

    Swal.fire({
        title: '<i class="fas fa-undo text-blue-600"></i> Heure de retour',
        html: `
            <div class="text-left space-y-4">
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                     <p class="font-bold text-gray-800">Retour : ${routeData.point_arrive} → ${routeData.point_depart}</p>
                     <p class="text-sm text-gray-600">Date : ${new Date(returnDate).toLocaleDateString('fr-FR')}</p>
                </div>
                 <div class="bg-green-50 p-2 rounded border border-green-200 text-sm mb-2">
                    <span class="font-bold text-green-700">Départ choisi :</span> ${window.selectedDepartureTime}
                </div>
                <p class="font-medium text-gray-700"><i class="fas fa-hand-point-right mr-1"></i> Choisissez l'heure de retour :</p>
                ${timeSlotsHtml}
            </div>
        `,
        showCancelButton: true,
        showConfirmButton: false,
        cancelButtonText: 'Retour',
        customClass: { popup: 'rounded-2xl' },
        didOpen: () => {
            document.querySelectorAll('.route-return-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const progId = this.dataset.id;
                    const time = this.dataset.time;
                    
                    window.selectedReturnTime = time;
                    window.selectedRetourProgramId = progId;
                    
                    const returnProg = routeData.retour_horaires.find(p => p.id == progId);
                    window.selectedReturnProgram = returnProg; 

                    Swal.close();
                    startReservationFromRoute(window.selectedAllerProgramId, dateDepartStr, true);
                });
            });
        }
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.cancel) {
            // IMPORTANT: Si on annule, on retourne au choix de la DATE de retour
            showReturnDateSelection(routeData, dateDepartStr);
        }
    });
}

        // Helper pour lancer la rÃ©servation finale
        function startReservationFromRoute(programId, dateVoyage, isAllerRetour) {
            window.userWantsAllerRetour = isAllerRetour;
            window.userChoseAllerRetour = isAllerRetour;
            
            // CORRECTION: Si on vient du flux "Grouped Routes", on s'assure d'utiliser la date de départ initiale
            if (window.currentDateDepart && isAllerRetour) {
                 console.log('DEBUG: Using Date Depart from global scope:', window.currentDateDepart);
                 dateVoyage = window.currentDateDepart;
            }

            // Ouvrir directement le modal de sélection des places (Step 1)
            openReservationModal(programId, dateVoyage);
        }
        

        
 // --- NOUVELLE FONCTION PRINCIPALE D'INITIATION ---
        // C'est elle qui est appelÃ©e par le bouton "RÃ©server"
     async function initiateReservationProcess(programId, searchDateFormatted, searchedTime = null) {
        console.log("Initiation réservation pour ID:", programId, "Date:", searchDateFormatted, "Heure cherchée:", searchedTime);
        
        // 1. Réinitialisation des variables globales
        userWantsAllerRetour = false;
        window.userChoseAllerRetour = false;
        window.selectedReturnProgram = null;
        window.outboundProgram = null;
        window.outboundDate = searchDateFormatted;
        window.selectedReturnDate = null; 
        window.selectedDepartureTime = null;
        window.selectedReturnTime = null;
        currentSelectedProgram = null;
        selectedReturnDate = null; 
        
        Swal.fire({
            title: 'Chargement...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const response = await fetch(`/user/booking/program/${programId}`);
            const data = await response.json();
            
            if (!data.success) throw new Error("Impossible de charger les détails du programme");
            
            const program = data.programme;
            window.outboundProgram = program;
            currentSelectedProgram = program;
            
            // Vérifier la disponibilité du retour via l'API
            const paramsRetour = new URLSearchParams({
                original_arrive: program.point_arrive,
                original_depart: program.point_depart,
                min_date: searchDateFormatted
            });
            const responseRetour = await fetch('{{ route("api.return-trips") }}?' + paramsRetour);
            const dataRetour = await responseRetour.json();
            
            // Construire un objet routeData compatible
            const routeData = {
                id: program.id,
                compagnie: program.compagnie?.name || 'Compagnie',
                compagnie_id: program.compagnie_id,
                point_depart: program.point_depart,
                point_arrive: program.point_arrive,
                gare_depart: program.gare_depart,
                gare_arrivee: program.gare_arrivee,
                montant_billet: program.montant_billet,
                durer_parcours: program.durer_parcours,
                has_retour: (dataRetour.success && dataRetour.return_trips && dataRetour.return_trips.length > 0),
                aller_horaires: [{
                    id: program.id,
                    heure_depart: program.heure_depart,
                    heure_arrive: program.heure_arrive,
                    montant_billet: program.montant_billet
                }],
                retour_horaires: dataRetour.success ? dataRetour.return_trips : [],
                date_fin: program.date_fin_programmation || program.date_fin || null
            };

            Swal.close();

            // Lancer le flux unifiÃ©
            showRouteTripTypeModal(routeData, searchDateFormatted);

        } catch (error) {
            console.error(error);
            Swal.close();
            Swal.fire({ icon: 'error', title: 'Erreur', text: 'Une erreur est survenue.' });
        }
    }

// Redundant function removed as it is now unified in showRouteTripTypeModal

// === NOUVEAU: Popup sÃ©lection heure de dÃ©part (depuis BDD) ===
async function showDepartureSchedulesModal(program, departureDate, isAllerRetour) {
    const dateFormatted = new Date(departureDate).toLocaleDateString('fr-FR', { 
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
    });
    
    Swal.fire({
        title: 'Chargement des horaires...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    try {
        // Charger les horaires de dÃ©part depuis la BDD
        const params = new URLSearchParams({
            point_depart: program.point_depart,
            point_arrive: program.point_arrive,
            date: departureDate
        });
        const response = await fetch('{{ route("api.route-schedules") }}?' + params);
        const data = await response.json();
        
        Swal.close();
        
        if (!data.success || data.schedules.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Aucun horaire disponible',
                text: 'Aucun horaire n\'est disponible pour cette date.',
                confirmButtonColor: '#e94f1b'
            });
            return;
        }
        
        // Construire la grille des horaires
        let timeSlotsHtml = '<div class="grid grid-cols-2 gap-3 max-h-60 overflow-y-auto p-2">';
        data.schedules.forEach(sched => {
            timeSlotsHtml += `
                <div class="departure-schedule-btn border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-[#e94f1b] hover:bg-orange-50 transition-all text-center" 
                     data-schedule-id="${sched.id}" data-time="${sched.heure_depart}" data-arrival="${sched.heure_arrive}">
                    <p class="font-bold text-xl text-[#e94f1b]">${sched.heure_depart}</p>
                    <p class="text-sm text-gray-500">â†’ ${sched.heure_arrive}</p>
                </div>
            `;
        });
        timeSlotsHtml += '</div>';
        
        Swal.fire({
            title: '<i class="fas fa-clock text-[#e94f1b]"></i> Heure de départ',
            html: `
                <div class="text-left space-y-4">
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <p class="font-bold text-gray-800">${program.point_depart} â†’ ${program.point_arrive}</p>
                        <div class="flex items-center gap-2 text-[10px] text-gray-500 mt-1 mb-1">
                            <span><i class="fas fa-map-marker-alt"></i> ${program.gare_depart?.nom_gare || 'Ville'}</span>
                            <span>→</span>
                            <span><i class="fas fa-flag"></i> ${program.gare_arrivee?.nom_gare || 'Ville'}</span>
                        </div>
                        <p class="text-sm text-gray-600"><i class="fas fa-calendar mr-2"></i>${dateFormatted}</p>
                        <p class="text-xs text-${isAllerRetour ? 'orange' : 'green'}-600 font-semibold mt-1">
                            <i class="fas fa-${isAllerRetour ? 'exchange-alt' : 'arrow-right'} mr-1"></i>
                            ${isAllerRetour ? 'Aller-Retour' : 'Aller Simple'}
                        </p>
                    </div>
                    <p class="font-medium text-gray-700">â†’ Choisissez l'heure de départ :</p>
                    ${timeSlotsHtml}
                </div>
            `,
            showCancelButton: true,
            showConfirmButton: false,
            cancelButtonText: 'Annuler',
            customClass: { popup: 'rounded-2xl' },
            didOpen: () => {
                document.querySelectorAll('.departure-schedule-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        window.selectedDepartureTime = this.dataset.time;
                        window.selectedDepartureProgramId = this.dataset.scheduleId;
                        currentProgramId = parseInt(this.dataset.scheduleId);
                        Swal.close();
                        
                        if (isAllerRetour) {
                            // Aller-Retour: maintenant on demande l'heure de retour
                            showReturnTripSelector(program, departureDate);
                        } else {
                            // Aller Simple: on passe directement Ã  la sÃ©lection des places
                            openReservationModal(currentProgramId, departureDate);
                        }
                    });
                });
            }
        });
        
    } catch (error) {
        console.error('Erreur chargement horaires:', error);
        Swal.close();
        Swal.fire({ icon: 'error', title: 'Erreur', text: 'Impossible de charger les horaires.' });
    }
}
        // GÃ©nÃ¨re les crÃ©neaux horaires disponibles (toutes les 30 min)
        function generateTimeSlots(selectedDate, tripDurationMinutes = 90) {
            const slots = [];
            const now = new Date();
            const isToday = selectedDate === now.toISOString().split('T')[0];
            
            // Minimum 4h Ã  l'avance
            const minBookingHours = 4;
            let startHour = 6; // Service commence Ã  6h
            let startMinute = 0;
            
            if (isToday) {
                const minTime = new Date(now.getTime() + (minBookingHours * 60 * 60 * 1000));
                startHour = Math.max(startHour, minTime.getHours());
                if (minTime.getMinutes() > 0) {
                    startMinute = minTime.getMinutes() <= 30 ? 30 : 0;
                    if (minTime.getMinutes() > 30) startHour++;
                }
            }
            
            // GÃ©nÃ©rer crÃ©neaux de 6h Ã  22h
            for (let h = startHour; h <= 22; h++) {
                for (let m = (h === startHour ? startMinute : 0); m < 60; m += 30) {
                    if (h === 22 && m > 0) break; // Dernier dÃ©part Ã  22h00
                    
                    const timeStr = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}`;
                    slots.push({
                        time: timeStr,
                        label: timeStr,
                        available: true
                    });
                }
            }
            
            return slots;
        }

        // Modal de sÃ©lection d'heure de dÃ©part
        function showTimeSelectionModal(program, departureDate) {
            const dateFormatted = new Date(departureDate).toLocaleDateString('fr-FR', { 
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
            });
            
            const timeSlots = generateTimeSlots(departureDate);
            
            if (timeSlots.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Aucun créneau disponible',
                    html: `<p>Pour la date du <strong>${dateFormatted}</strong>, aucun créneau horaire n'est disponible.</p>
                           <p class="text-sm text-gray-500 mt-2">Rappel: La réservation doit être faite au minimum 4 heures à l'avance.</p>`,
                    confirmButtonText: 'Choisir une autre date',
                    confirmButtonColor: '#e94f1b'
                });
                return;
            }
            
            let timeSlotsHtml = '<div class="grid grid-cols-4 gap-2 max-h-60 overflow-y-auto p-2">';
            timeSlots.forEach(slot => {
                timeSlotsHtml += `
                    <button type="button" 
                            class="time-slot-btn p-3 text-center rounded-lg border-2 border-gray-200 hover:border-[#e94f1b] hover:bg-orange-50 transition-all font-semibold"
                            data-time="${slot.time}">
                        ${slot.label}
                    </button>
                `;
            });
            timeSlotsHtml += '</div>';
            
            Swal.fire({
                title: '<i class="fas fa-clock text-[#e94f1b]"></i> Heure de départ',
                html: `
                    <div class="text-left space-y-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="font-bold text-gray-800">${program.point_depart} â†’ ${program.point_arrive}</p>
                            <div class="flex items-center gap-2 text-[10px] text-gray-500 mt-1 mb-1">
                                <span><i class="fas fa-map-marker-alt"></i> ${program.gare_depart?.nom_gare || 'Ville'}</span>
                                <span>→</span>
                                <span><i class="fas fa-flag"></i> ${program.gare_arrivee?.nom_gare || 'Ville'}</span>
                            </div>
                            <p class="text-sm text-gray-600"><i class="fas fa-calendar mr-2"></i>${dateFormatted}</p>
                            <p class="text-sm text-gray-500 mt-1"><i class="fas fa-hourglass-half mr-2"></i>Durée: ${program.durer_parcours || '~1h30'}</p>
                        </div>
                        <p class="text-gray-600 font-medium">A quelle heure souhaitez-vous partir ?</p>
                        <p class="text-xs text-gray-400"><i class="fas fa-info-circle mr-1"></i>Réservation minimum 4h à l'avance • Service 6h-22h</p>
                        ${timeSlotsHtml}
                        <div id="selectedTimeDisplay" class="hidden bg-green-50 p-3 rounded-lg text-center">
                            <span class="font-bold text-green-800">Départ sélectionné: <span id="selectedTimeValue"></span></span>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check mr-2"></i>Valider cette heure',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#e94f1b',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-lg px-4 py-2'
                },
                preConfirm: () => {
                    if (!window.selectedDepartureTime) {
                        Swal.showValidationMessage('Veuillez sélectionner une heure de départ');
                        return false;
                    }
                    return window.selectedDepartureTime;
                },
                didOpen: () => {
                    document.querySelectorAll('.time-slot-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            // DÃ©sÃ©lectionner tous les autres
                            document.querySelectorAll('.time-slot-btn').forEach(b => {
                                b.classList.remove('bg-[#e94f1b]', 'text-white', 'border-[#e94f1b]');
                                b.classList.add('border-gray-200');
                            });
                            // SÃ©lectionner celui-ci
                            this.classList.add('bg-[#e94f1b]', 'text-white', 'border-[#e94f1b]');
                            this.classList.remove('border-gray-200');
                            
                            window.selectedDepartureTime = this.dataset.time;
                            document.getElementById('selectedTimeDisplay').classList.remove('hidden');
                            document.getElementById('selectedTimeValue').textContent = this.dataset.time;
                        });
                    });
                }
            }).then((result) => {
                if (result.isConfirmed && window.selectedDepartureTime) {
                    // Stocker l'heure et passer au choix du type de voyage
                    showReturnTripOptionModal(program, departureDate);
                }
            });
        }

        // Modal FlixBus pour choix Aller Simple ou Aller-Retour
        function showReturnTripOptionModal(program, departureDate) {
            const dateFormatted = new Date(departureDate).toLocaleDateString('fr-FR', { 
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
            });
            
            const priceSimple = Number(program.montant_billet);
            const priceReturn = priceSimple * 2;
            
            Swal.fire({
                title: '<i class="fas fa-bus text-[#e94f1b]"></i> Type de voyage',
                html: `
                    <div class="text-left space-y-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="font-bold text-gray-800">${program.point_depart} â†’ ${program.point_arrive}</p>
                            <div class="flex items-center gap-2 text-xs text-gray-600 mt-1 mb-2">
                                <span class="flex items-center gap-1"><i class="fas fa-map-marker-alt text-[10px] text-[#e94f1b]"></i> ${program.gare_depart?.nom_gare || 'Ville'}</span>
                                <i class="fas fa-arrow-right text-[8px] text-gray-400"></i>
                                <span class="flex items-center gap-1"><i class="fas fa-flag text-[10px] text-green-500"></i> ${program.gare_arrivee?.nom_gare || 'Ville'}</span>
                            </div>
                            <p class="text-sm text-gray-600"><i class="fas fa-calendar mr-2"></i>${dateFormatted}</p>
                            <p class="text-sm text-green-600 font-semibold"><i class="fas fa-clock mr-2"></i>Départ à ${window.selectedDepartureTime}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="border-2 border-gray-200 rounded-lg p-4 text-center hover:border-gray-400 transition-all cursor-pointer" id="choiceSimple">
                                <i class="fas fa-arrow-right text-3xl text-gray-500 mb-2"></i>
                                <p class="font-bold">Aller Simple</p>
                                <p class="text-lg font-bold text-[#e94f1b]">${priceSimple.toLocaleString('fr-FR')} FCFA</p>
                            </div>
                            <div class="border-2 border-[#e94f1b] bg-orange-50 rounded-lg p-4 text-center hover:bg-orange-100 transition-all cursor-pointer" id="choiceReturn">
                                <i class="fas fa-exchange-alt text-3xl text-[#e94f1b] mb-2"></i>
                                <p class="font-bold">Aller-Retour</p>
                                <p class="text-lg font-bold text-[#e94f1b]">${priceReturn.toLocaleString('fr-FR')} FCFA</p>
                                <p class="text-xs text-gray-500">Prix estimé</p>
                            </div>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Annuler',
                customClass: {
                    popup: 'rounded-2xl'
                },
                didOpen: () => {
                    document.getElementById('choiceSimple').addEventListener('click', () => {
                        userWantsAllerRetour = false;
                        Swal.close();
                        openReservationModal(program.id, departureDate);
                    });
                    document.getElementById('choiceReturn').addEventListener('click', async () => {
                        userWantsAllerRetour = true;
                        window.userChoseAllerRetour = true;
                        Swal.close();
                        console.log('DEBUG: choiceReturn clicked. Original Departure:', departureDate);
                        
                        // Demander la date de retour
                        const { value: returnDate } = await Swal.fire({
                            title: '<i class="fas fa-calendar-alt text-[#e94f1b]"></i> Date de retour',
                            html: '<p class="text-sm text-gray-600 mb-4">Veuillez choisir la date de votre voyage retour</p>',
                            input: 'date',
                            inputLabel: '',
                            inputValue: departureDate, // Par dÃ©faut la date aller
                            inputAttributes: {
                                min: departureDate // Impossible de revenir avant l'aller
                            },
                            confirmButtonColor: '#e94f1b',
                            confirmButtonText: 'Rechercher',
                            showCancelButton: true,
                            cancelButtonText: 'Annuler',
                            customClass: {
                                input: 'text-center text-lg border-2 border-gray-300 rounded-lg focus:border-[#e94f1b] focus:ring-0'
                            }
                        });

                        console.log('DEBUG: User selected return date:', returnDate);

                        if (returnDate) {
                            // Utiliser showReturnTripSelector avec la date choisie
                            showReturnTripSelector(program, departureDate, returnDate);
                        }
                    });
                }
            });
        }

        // Modal de sÃ©lection d'heure de retour (aprÃ¨s le choix aller-retour)
        function showReturnTimeSelectionModal(program, departureDate) {
            const dateFormatted = new Date(departureDate).toLocaleDateString('fr-FR', { 
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
            });
            
            // Calculer l'heure d'arrivÃ©e estimÃ©e
            const durationMatch = (program.durer_parcours || '01:30').match(/(\d+):(\d+)/);
            const durationHours = durationMatch ? parseInt(durationMatch[1]) : 1;
            const durationMinutes = durationMatch ? parseInt(durationMatch[2]) : 30;
            const totalDurationMinutes = (durationHours * 60) + durationMinutes;
            
            // Heure d'arrivÃ©e = heure de dÃ©part + durÃ©e
            const [depH, depM] = window.selectedDepartureTime.split(':').map(Number);
            const arrivalDate = new Date(2026, 0, 1, depH, depM);
            arrivalDate.setMinutes(arrivalDate.getMinutes() + totalDurationMinutes);
            const arrivalTimeStr = `${arrivalDate.getHours().toString().padStart(2, '0')}:${arrivalDate.getMinutes().toString().padStart(2, '0')}`;
            
            // Retour minimum 1h aprÃ¨s l'arrivÃ©e
            const minReturnDate = new Date(arrivalDate);
            minReturnDate.setHours(minReturnDate.getHours() + 1);
            const minReturnHour = minReturnDate.getHours();
            const minReturnMinute = minReturnDate.getMinutes() <= 30 ? 30 : 0;
            const actualMinReturnHour = minReturnDate.getMinutes() > 30 ? minReturnHour + 1 : minReturnHour;
            
            // GÃ©nÃ©rer crÃ©neaux pour le retour
            const returnSlots = [];
            for (let h = actualMinReturnHour; h <= 22; h++) {
                for (let m = (h === actualMinReturnHour ? minReturnMinute : 0); m < 60; m += 30) {
                    if (h === 22 && m > 0) break;
                    const timeStr = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}`;
                    returnSlots.push({ time: timeStr, label: timeStr });
                }
            }
            
            if (returnSlots.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Retour le lendemain',
                    html: `<p>Votre arrivÃ©e estimÃ©e est Ã  <strong>${arrivalTimeStr}</strong>.</p>
                           <p class="mt-2">Le retour ne peut pas Ãªtre fait le mÃªme jour. Veuillez choisir un autre jour.</p>`,
                    confirmButtonText: 'Continuer en aller simple',
                    confirmButtonColor: '#e94f1b'
                }).then(() => {
                    userWantsAllerRetour = false;
                    openReservationModal(program.id, departureDate);
                });
                return;
            }
            
            let timeSlotsHtml = '<div class="grid grid-cols-4 gap-2 max-h-48 overflow-y-auto p-2">';
            returnSlots.forEach(slot => {
                timeSlotsHtml += `
                    <button type="button" 
                            class="return-time-slot-btn p-3 text-center rounded-lg border-2 border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition-all font-semibold"
                            data-time="${slot.time}">
                        ${slot.label}
                    </button>
                `;
            });
            timeSlotsHtml += '</div>';
            
            Swal.fire({
                title: '<i class="fas fa-undo text-blue-500"></i> Heure de retour',
                html: `
                    <div class="text-left space-y-4">
                        <div class="bg-green-50 p-3 rounded-lg">
                            <p class="font-bold text-green-800"><i class="fas fa-check-circle mr-2"></i>Aller confirmÃ©</p>
                            <p class="text-sm text-green-700">${program.point_depart} â†’ ${program.point_arrive}</p>
                            <p class="text-sm text-green-600">${dateFormatted} â€¢ Départ: ${window.selectedDepartureTime} â€¢ Arrivée: ~${arrivalTimeStr}</p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <p class="font-bold text-blue-800">${program.point_arrive} â†’ ${program.point_depart}</p>
                            <p class="text-sm text-blue-600">${dateFormatted} (mÃªme jour)</p>
                        </div>
                        <p class="text-gray-600 font-medium">À quelle heure souhaitez-vous repartir ?</p>
                        <p class="text-xs text-gray-400"><i class="fas fa-info-circle mr-1"></i>Minimum 1h après votre arrivée</p>
                        ${timeSlotsHtml}
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check mr-2"></i>Confirmer le retour',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#e94f1b',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-lg px-4 py-2'
                },
                preConfirm: () => {
                    if (!window.selectedReturnTime) {
                        Swal.showValidationMessage('Veuillez sélectionner une heure de retour');
                        return false;
                    }
                    return window.selectedReturnTime;
                },
                didOpen: () => {
                    document.querySelectorAll('.return-time-slot-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            document.querySelectorAll('.return-time-slot-btn').forEach(b => {
                                b.classList.remove('bg-blue-500', 'text-white', 'border-blue-500');
                                b.classList.add('border-gray-200');
                            });
                            this.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
                            this.classList.remove('border-gray-200');
                            window.selectedReturnTime = this.dataset.time;
                        });
                    });
                }
            }).then((result) => {
                if (result.isConfirmed && window.selectedReturnTime) {
                    // RÃ©capitulatif et continuation
                    window.selectedReturnDate = departureDate; // MÃªme jour
                    showTripSummaryAndContinue(program, departureDate);
                }
            });
        }

        // RÃ©capitulatif du voyage aller-retour
        function showTripSummaryAndContinue(program, departureDate) {
            const dateFormatted = new Date(departureDate).toLocaleDateString('fr-FR');
            const totalPrice = Number(program.montant_billet) * 2;
            
            Swal.fire({
                icon: 'success',
                title: 'Voyage Aller-Retour confirmÃ© !',
                html: `
                    <div class="text-left space-y-3">
                        <div class="border-l-4 border-green-500 pl-3">
                            <p class="font-bold text-gray-800">â†— ALLER</p>
                            <p class="text-sm">${program.point_depart} â†’ ${program.point_arrive}</p>
                            <p class="text-sm text-gray-500">${dateFormatted} Ã  ${window.selectedDepartureTime}</p>
                        </div>
                        <div class="border-l-4 border-blue-500 pl-3">
                            <p class="font-bold text-gray-800">â†™ RETOUR</p>
                            <p class="text-sm">${program.point_arrive} â†’ ${program.point_depart}</p>
                            <p class="text-sm text-gray-500">${dateFormatted} Ã  ${window.selectedReturnTime}</p>
                        </div>
                        <div class="bg-orange-50 p-3 rounded-lg text-center mt-4">
                            <p class="text-lg font-bold text-[#e94f1b]">Total: ${totalPrice.toLocaleString('fr-FR')} FCFA</p>
                        </div>
                    </div>
                `,
                confirmButtonText: 'Continuer la réservation',
                confirmButtonColor: '#e94f1b'
            }).then(() => {
                openReservationModal(program.id, departureDate);
            });
        }

        // Afficher le sÃ©lecteur de voyage retour
        async function showReturnTripSelector(outboundProgram, outboundDate, returnDate) {
            console.log('DEBUG: showReturnTripSelector called', { outboundDate, returnDate });
            Swal.fire({
                title: 'Recherche des retours...',
                text: `Pour le ${new Date(returnDate).toLocaleDateString('fr-FR')}`,
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const params = new URLSearchParams({
                    original_depart: outboundProgram.point_depart,
                    original_arrive: outboundProgram.point_arrive,
                    min_date: returnDate // Utilise la date choisie par l'utilisateur
                });
                
                const response = await fetch('{{ route("api.return-trips") }}?' + params);
                const data = await response.json();
                Swal.close();

                if (!data.success || data.count === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Aucun retour disponible',
                        html: `<p>Aucun voyage retour <strong>${outboundProgram.point_arrive} â†’ ${outboundProgram.point_depart}</strong> n'est disponible le/aprÃ¨s le ${new Date(returnDate).toLocaleDateString('fr-FR')}.</p>
                               <p class="text-sm text-gray-500 mt-2">Essayez une autre date ou continuez en aller simple.</p>`,
                        confirmButtonText: 'Continuer en aller simple',
                        confirmButtonColor: '#e94f1b',
                        showCancelButton: true,
                        cancelButtonText: 'Changer date retour'
                    }).then((result) => {
                         if (result.isConfirmed) {
                             userWantsAllerRetour = false;
                             openReservationModal(outboundProgram.id, outboundDate);
                         } else if (result.dismiss === Swal.DismissReason.cancel) {
                             // RÃ©-ouvrir le choix de date
                             document.getElementById('choiceReturn').click(); 
                         }
                    });
                    return;
                }

                // Afficher les options de retour
                displayReturnTripOptions(outboundProgram, outboundDate, data.return_trips, returnDate);

            } catch (error) {
                console.error('Erreur:', error);
                Swal.close();
                Swal.fire({ icon: 'error', title: 'Erreur', text: 'Impossible de charger les voyages retour.' });
            }
        }

        // Afficher les options de voyage retour dans un modal
        function displayReturnTripOptions(outboundProgram, outboundDate, returnTrips, requestedReturnDate) {
            console.log('DEBUG: displayReturnTripOptions called', { outboundDate, requestedReturnDate });
            
            // Capture explicite de la date aller
            const savedOutboundDate = outboundDate;

            // Pour le mÃªme jour, on affiche simplement les horaires disponibles
            const dateFormatted = new Date(outboundDate).toLocaleDateString('fr-FR', { 
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
            });
            const returnDateFormatted = new Date(requestedReturnDate).toLocaleDateString('fr-FR', { 
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
            });

            let timeSlotsHtml = '<div class="grid grid-cols-3 gap-3 max-h-60 overflow-y-auto p-2">';
            returnTrips.forEach(trip => {
                // Utiliser la date du trip si disponible (via backend display_date), sinon la date aller
                const tripDate = trip.display_date || outboundDate;
                
                timeSlotsHtml += `
                    <div class="return-trip-option border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-[#e94f1b] hover:bg-orange-50 transition-all text-center" 
                         data-trip-id="${trip.id}" data-trip-date="${tripDate}">
                        <p class="font-bold text-xl text-gray-800">${trip.heure_depart}</p>
                        <p class="text-sm text-gray-500">â†’ ${trip.heure_arrive}</p>
                        <p class="text-sm font-bold text-[#e94f1b] mt-1">${Number(trip.montant_billet).toLocaleString('fr-FR')} FCFA</p>
                         ${trip.display_date && trip.display_date !== outboundDate ? `<p class="text-xs text-blue-600 mt-1 font-bold">${new Date(tripDate).toLocaleDateString('fr-FR', {day: 'numeric', month: 'short'})}</p>` : ''}
                    </div>
                `;
            });
            timeSlotsHtml += '</div>';

            let html = `
                <div class="text-left max-h-[60vh] overflow-y-auto">
                    <div class="bg-green-50 p-3 rounded-lg mb-4 text-sm">
                        <p class="font-bold text-green-800"><i class="fas fa-check-circle mr-2"></i>Aller confirmÃ©</p>
                        <p class="text-green-700">${outboundProgram.point_depart} â†’ ${outboundProgram.point_arrive}</p>
                        <div class="flex items-center gap-2 text-[10px] text-green-600 mt-1 mb-1">
                            <span><i class="fas fa-map-marker-alt"></i> ${outboundProgram.gare_depart?.nom_gare || 'Ville'}</span>
                            <span>→</span>
                            <span><i class="fas fa-flag"></i> ${outboundProgram.gare_arrivee?.nom_gare || 'Ville'}</span>
                        </div>
                        <p class="text-green-600 text-xs">${dateFormatted} â€¢ Départ: ${window.selectedDepartureTime || outboundProgram.heure_depart}</p>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-lg mb-4">
                        <p class="font-bold text-blue-800"><i class="fas fa-undo mr-2"></i>Retour: ${outboundProgram.point_arrive} â†’ ${outboundProgram.point_depart}</p>
                        <p class="text-blue-600 text-sm font-semibold">${returnDateFormatted}</p>
                         <p class="text-blue-600 text-xs">Options disponibles</p>
                    </div>
                    <p class="font-medium text-gray-700 mb-3">Sélectionnez votre heure de retour :</p>
                    ${timeSlotsHtml}
                </div>
            `;
            html += `</div>`;

            Swal.fire({
                title: `<i class="fas fa-undo text-blue-500"></i> Retour: ${outboundProgram.point_arrive} â†’ ${outboundProgram.point_depart}`,
                html: html,
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonText: 'Annuler',
                customClass: {
                    popup: 'rounded-2xl',
                    htmlContainer: 'px-2'
                },
                didOpen: () => {
                    document.querySelectorAll('.return-trip-option').forEach(el => {
                        el.addEventListener('click', function() {
                            const tripId = this.dataset.tripId;
                            const tripDate = this.dataset.tripDate;
                            window.selectedReturnProgram = returnTrips.find(t => t.id == tripId);
                            window.selectedReturnDate = tripDate;
                            Swal.close();
                            
                            // Calculer le prix total
                            const totalPrice = Number(outboundProgram.montant_billet) + Number(window.selectedReturnProgram.montant_billet);
                            
                            // Afficher rÃ©capitulatif et ouvrir modal rÃ©servation
                            Swal.fire({
                                icon: 'success',
                                title: 'Aller-Retour sélectionné !',
                                html: `
                                    <div class="text-left space-y-2">
                                        <p><strong>Aller:</strong> ${outboundProgram.point_depart} à ${outboundProgram.point_arrive}<br>
                                           <span class="text-sm text-gray-500">${new Date(outboundDate).toLocaleDateString('fr-FR')} à  ${outboundProgram.heure_depart}</span></p>
                                        <p><strong>Retour:</strong> ${window.selectedReturnProgram.point_depart} à ${window.selectedReturnProgram.point_arrive}<br>
                                           <span class="text-sm text-gray-500">${new Date(tripDate).toLocaleDateString('fr-FR')} à  ${window.selectedReturnProgram.heure_depart}</span></p>
                                        <p class="text-lg font-bold text-[#e94f1b] mt-3">Total: ${totalPrice.toLocaleString('fr-FR')} FCFA</p>
                                    </div>
                                `,
                                confirmButtonText: 'Continuer la réservation',
                                confirmButtonColor: '#e94f1b'
                            }).then(() => {
                                // Ouvrir le modal de réservation pour l'aller
                                console.log('DEBUG: Opening final reservation modal with OutboundDate:', savedOutboundDate);
                                
                                // FORCE RESTORE: On s'assure que globalement la date aller est correcte
                                window.outboundDate = savedOutboundDate;
                                window.currentReservationDate = savedOutboundDate; // Double sÃ©curitÃ©
                                console.log('DEBUG: Forced window.outboundDate restored to:', window.outboundDate);

                                openReservationModal(outboundProgram.id, savedOutboundDate);
                            });
                        });
                    });
                }
            });
        }
     function getNextAvailableDate(program) {
        if(program.type_programmation === 'ponctuel') return program.date_depart.split('T')[0];
        // Pour rÃ©current, on prend demain si possible, ou une logique plus complexe
        // Ici on simplifie en renvoyant la date de jour ou la date de dÃ©but
        return new Date().toISOString().split('T')[0]; 
    }
    // ============================================
        // FONCTION 3: Ouvrir le modal de rÃ©servation
        // ============================================
         function showReservationModal(programId, searchDate = null) {
            // IncrÃ©menter l'ID de requÃªte
            currentRequestId++;
            const thisRequestId = currentRequestId;

            console.log(`[REQ #${thisRequestId}] Ouverture modal Réservation pour ID ${programId}`);

            // RÃ©initialisation
            currentProgramId = programId;
            selectedNumberOfPlaces = 0;
            selectedSeats = [];
            reservedSeats = [];
            vehicleDetails = null;
            window.currentReservationDate = null;

            // Reset UI
            document.getElementById('reservationProgramInfo').innerHTML = '<div class="text-center p-4"><i class="fas fa-spinner fa-spin text-2xl text-[#e94f1b]"></i><p>Chargement...</p></div>';
            document.getElementById('selectedSeatsCount').textContent = '0 place sélectionnée';
            document.getElementById('seatSelectionArea').innerHTML = '';
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step3').classList.add('hidden');
            document.getElementById('step1').classList.remove('hidden');
            document.getElementById('nextStepBtn').disabled = true;
            document.querySelectorAll('.place-count-btn').forEach(btn => btn.classList.remove('active'));

            document.getElementById('reservationModal').classList.remove('hidden');
            updateStepper(1);

            // Fetch info programme
            fetch("{{ route('user.reservation.program', ':id') }}".replace(':id', programId))
                .then(response => response.json())
                .then(data => {
                    if (thisRequestId !== currentRequestId) return;

                    if (data.success) {
                        const program = data.programme;
                         currentSelectedProgram = program; 
                        // DÃ©terminer la date de voyage finale
                       let dateVoyage = searchDate || window.selectedDepartureDate || window.outboundDate;

if (!dateVoyage) {
    // Fallback ultime : si aucune date n'est trouvée, on prend la date du programme
    dateVoyage = program.date_depart.split('T')[0];
}

// FORCE la mise à jour des variables globales pour la confirmation
window.currentReservationDate = dateVoyage;
window.outboundDate = dateVoyage; 
                        const dateDisplay = new Date(dateVoyage).toLocaleDateString('fr-FR');
                        
                        // Prix et badge
                        let prixAffiche = parseInt(program.montant_billet);
                        window.currentProgramPrice = prixAffiche; // IMPORTANT pour le calcul final

                        let allerRetourBadge = '';
                        if (window.userChoseAllerRetour) {
                            prixAffiche = prixAffiche * 2;
                            allerRetourBadge = '<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm"><i class="fas fa-exchange-alt me-1"></i>Aller-Retour</span>';
                        }

                        document.getElementById('reservationProgramInfo').innerHTML = `
                            <div class="flex flex-wrap gap-4">
                                <span class="bg-red-50 text-red-700 px-2 py-1 rounded font-bold">
                                    <i class="fas fa-building"></i>
                                    ${program.compagnie?.sigle || ''} ${program.compagnie?.name || ''}
                                </span>
                                <span><i class="fas fa-calendar"></i> ${dateDisplay}</span>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded"><i class="fas fa-clock"></i> Départ: ${window.selectedDepartureTime || program.heure_depart}</span>
                                <span><i class="fas fa-money-bill-wave"></i> ${prixAffiche.toLocaleString('fr-FR')} FCFA</span>
                                ${allerRetourBadge}
                                ${window.selectedReturnTime ? `<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm"><i class="fas fa-undo"></i> Retour: ${selectedReturnDate ? new Date(selectedReturnDate).toLocaleDateString('fr-FR') + ' à ' : ''}${window.selectedReturnTime}</span>` : ''}
                            </div>
                        `;

                        // PrÃ©charger les places
                        fetch("{{ route('user.reservation.reserved-seats', ':id') }}".replace(':id', programId) + `?date=${encodeURIComponent(dateVoyage)}`)
                            .then(r => r.json())
                            .then(d => {
                                if (d.success && thisRequestId === currentRequestId) {
                                    reservedSeats = d.reservedSeats || [];
                                }
                            });
                    }
                });
        }

        // Exposer globalement pour compatibilitÃ©
        window.openReservationModal = showReservationModal;

        window.backToTripTypeChoice = function() {
            closeReservationModal();
            if (window.currentRouteData && window.currentDateDepart) {
                // Petit délai pour permettre l'animation de fermeture
                setTimeout(() => {
                    showRouteTripTypeModal(window.currentRouteData, window.currentDateDepart);
                }, 100);
            }
        };

        // ============================================
        // STEPPER UPDATE FUNCTION
        // ============================================
        function updateStepper(currentStep) {
            const items = document.querySelectorAll('#reservationStepper .stepper-item');
            const lines = document.querySelectorAll('#reservationStepper .stepper-line');
            
            items.forEach((item, idx) => {
                const step = idx + 1;
                item.classList.remove('active', 'completed');
                if (step < currentStep) {
                    item.classList.add('completed');
                } else if (step === currentStep) {
                    item.classList.add('active');
                }
            });
            
            lines.forEach((line, idx) => {
                const lineAfterStep = idx + 1;
                if (lineAfterStep < currentStep) {
                    line.classList.add('filled');
                } else {
                    line.classList.remove('filled');
                }
            });
        }



        // ============================================
        // FONCTION 4: Fermer le modal de rÃ©servation
        // ============================================
        function closeReservationModal() {
            document.getElementById('reservationModal').classList.add('hidden');
        }

        // ============================================
        // FONCTION 5: SÃ©lectionner le nombre de places
        // ============================================
        function selectNumberOfPlaces(number, element) {
            selectedNumberOfPlaces = number;

            // Activer le bouton sélectionné
            document.querySelectorAll('.place-count-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            if (element) {
                element.classList.add('active');
            } else if (window.event && window.event.target) {
                window.event.target.closest('button').classList.add('active');
            }

            // Activer le bouton suivant
            document.getElementById('nextStepBtn').disabled = false;
        }
 // --- 3. GESTION DES FLUX ALLER-RETOUR ---

        function openAllerRetourConfirmModal(program, selectedDepartureDate = null) {
            // Fermer les autres modals potentiels
            document.getElementById('programsListModal').classList.add('hidden');
            document.getElementById('dateSelectionModal').classList.add('hidden');
            
            const modal = document.getElementById('allerRetourConfirmModal');
            
            // Stocker la date
            window.selectedDepartureDate = selectedDepartureDate || program.date_depart?.split('T')[0];
            currentSelectedProgram = program;

            // UI Info
            document.getElementById('allerRetourTripInfo').innerHTML = `
                <div class="text-center">
                    <div class="text-lg font-bold text-gray-800 mb-1">
                        ${program.point_depart} <i class="fas fa-arrow-right text-gray-400 mx-2"></i> ${program.point_arrive}
                    </div>
                    <div class="flex items-center justify-center gap-4 text-xs text-gray-500 mb-3">
                        <span class="flex items-center gap-1"><i class="fas fa-map-marker-alt text-[#e94f1b]"></i> ${program.gare_depart?.nom_gare || 'Ville'}</span>
                        <span class="flex items-center gap-1"><i class="fas fa-flag text-green-500"></i> ${program.gare_arrivee?.nom_gare || 'Ville'}</span>
                    </div>
                    <div class="text-sm text-gray-500 mb-3">${program.compagnie?.name || 'Compagnie'}</div>
                </div>
            `;
            
            // Détection automatique du choix basé sur la recherche
            const searchType = new URLSearchParams(window.location.search).get('is_aller_retour');
            if (searchType === '1') {
                userWantsAllerRetour = true;
                document.getElementById('allerRetourChoice').value = 'aller_retour';
            } else {
                userWantsAllerRetour = false;
                document.getElementById('allerRetourChoice').value = 'aller_simple';
            }
            
            updateAllerRetourPriceDisplay(program);
            onAllerRetourChoiceChange(); // Mettre à jour l'affichage dynamique (dates, etc)
            modal.classList.remove('hidden');
        }
function onAllerRetourChoiceChange() {
            const choice = document.getElementById('allerRetourChoice').value;
            userWantsAllerRetour = (choice === 'aller_retour');
            
            updateAllerRetourPriceDisplay(currentSelectedProgram);
            
            const returnDateSection = document.getElementById('returnDateSection');
            // Afficher section date retour SI A/R ET (RÃ©current OU (Ponctuel ET Date Retour non fixÃ©e par dÃ©faut))
            // Note: Pour ponctuel, le retour est souvent le mÃªme jour par dÃ©faut, mais ici on gÃ¨re le cas rÃ©current
            if (userWantsAllerRetour && currentSelectedProgram.type_programmation === 'recurrent') {
                returnDateSection.classList.remove('hidden');
                populateReturnDateSelect(currentSelectedProgram, window.selectedDepartureDate);
            } else {
                returnDateSection.classList.add('hidden');
            }
        }
 function updateAllerRetourPriceDisplay(program) {
            const priceDiv = document.getElementById('allerRetourPriceDisplay');
            const prixSimple = parseInt(program.montant_billet);
            const prixDouble = prixSimple * 2;
            
            if (userWantsAllerRetour) {
                priceDiv.innerHTML = `
                    <div class="bg-blue-50 rounded-lg p-3 border-2 border-blue-200">
                        <div class="text-sm text-blue-600 mb-1"><i class="fas fa-exchange-alt me-1"></i> Prix Aller-Retour</div>
                        <div class="text-2xl font-bold text-[#e94f1b]">${prixDouble.toLocaleString('fr-FR')} FCFA</div>
                    </div>
                `;
            } else {
                priceDiv.innerHTML = `
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="text-sm text-gray-600 mb-1"><i class="fas fa-arrow-right me-1"></i> Prix Aller Simple</div>
                        <div class="text-2xl font-bold text-[#e94f1b]">${prixSimple.toLocaleString('fr-FR')} FCFA</div>
                    </div>
                `;
            }
        }
 function populateReturnDateSelect(program, departureDateStr) {
            const select = document.getElementById('returnDateSelect');
            select.innerHTML = '<option value="">Chargement...</option>';

            // RÃ©cupÃ©rer les jours de rÃ©currence du programme RETOUR si dispo
            let rawDays = program.jours_recurrence;
            if (program.programme_retour && program.programme_retour.jours_recurrence) {
                rawDays = program.programme_retour.jours_recurrence;
            }

            let allowedDays = [];
            try {
                allowedDays = (typeof rawDays === 'string') ? JSON.parse(rawDays) : (rawDays || []);
            } catch(e) { allowedDays = []; }
            allowedDays = allowedDays.map(d => d.toLowerCase());

            const daysMap = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
            const dates = [];
            
            // Date de dÃ©but : lendemain du dÃ©part
            let start = departureDateStr ? new Date(departureDateStr) : new Date();
            start.setDate(start.getDate() + 1);
            
            // Date limite programme retour
            let returnStartDate = program.programme_retour?.date_depart ? new Date(program.programme_retour.date_depart) : null;
            if (returnStartDate && returnStartDate > start) start = returnStartDate;

            let current = new Date(start);
            let count = 0;

            while (count < 10) {
                const dayName = daysMap[current.getDay()];
                if (allowedDays.includes(dayName)) {
                    // Check fin validitÃ©
                    let isValid = true;
                    if (program.date_fin_programmation) {
                        if (current.toISOString().split('T')[0] > program.date_fin_programmation) isValid = false;
                    }
                    
                    if (isValid) {
                        dates.push({
                            val: current.toISOString().split('T')[0],
                            txt: current.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
                        });
                        count++;
                    }
                }
                current.setDate(current.getDate() + 1);
                // SÃ©curitÃ© boucle infinie
                if ((current - start) > 5184000000) break; // 60 jours
            }

            select.innerHTML = '<option value="">Choisir une date de retour...</option>';
            if (dates.length === 0) {
                select.innerHTML += '<option value="" disabled>Aucune date disponible</option>';
            } else {
                dates.forEach(d => {
                    select.innerHTML += `<option value="${d.val}">${d.txt}</option>`;
                });
            }
        }
        function closeAllerRetourConfirmModal() {
            document.getElementById('allerRetourConfirmModal').classList.add('hidden');
        }
 function confirmAllerRetour() {
            // Enregistrer le choix de l'utilisateur
            window.userChoseAllerRetour = userWantsAllerRetour;
            
            if (userWantsAllerRetour) {
                if (currentSelectedProgram.type_programmation === 'recurrent') {
                    const returnDate = document.getElementById('returnDateSelect').value;
                    if (!returnDate) {
                        Swal.fire({ icon: 'warning', text: 'Veuillez sélectionner une date de retour.' });
                        return;
                    }
                    window.selectedReturnDate = returnDate;
                } else {
                    // Ponctuel : retour le mÃªme jour
                    window.selectedReturnDate = window.selectedDepartureDate;
                }
            } else {
                // L'utilisateur a choisi Aller Simple (mÃªme sur un programme A/R)
                window.selectedReturnDate = null;
            }
            
            closeAllerRetourConfirmModal();
            
            // Suite du flux
            const program = currentSelectedProgram;
            // Si c'est un rÃ©current et qu'on n'a pas encore de date de dÃ©part
            if (program.type_programmation === 'recurrent' && !window.selectedDepartureDate) {
                openDateSelectionModal(program);
            } else {
                // On a tout ce qu'il faut
                openReservationModal(program.id, window.selectedDepartureDate);
            }
        }
        // ============================================
        // FONCTION 1: Afficher les dÃ©tails du vÃ©hicule
        // ============================================
       async function showVehicleDetails(vehicleId, programId, dateVoyageInput = null, heureDepart = null) {
    console.log(`[DETAILS] Demande détails véhicule ${vehicleId} pour programme ${programId} (Date: ${dateVoyageInput}, Heure: ${heureDepart})`);
    
    // Si ID null ou 0, on laisse passer pour que le backend génère un véhicule virtuel
    if (!vehicleId || vehicleId === '0') {
        console.warn('Aucun ID véhicule, utilisation du mode virtuel via backend');
    }

    // Récupérer l'heure de départ depuis le contexte global si non fournie
    if (!heureDepart && window.selectedDepartureTime) {
        heureDepart = window.selectedDepartureTime;
    }

    // Récupérer la date
    let dateVoyage = dateVoyageInput;
    if (!dateVoyage) {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('date_depart')) {
            dateVoyage = urlParams.get('date_depart');
        } else {
            const dateInput = document.getElementById('date_depart');
            if (dateInput && dateInput.value) {
                dateVoyage = dateInput.value;
            } else {
                dateVoyage = new Date().toISOString().split('T')[0];
            }
        }
    }

    console.log(`[DETAILS] Date utilisée: ${dateVoyage}, Heure: ${heureDepart}`);

    Swal.fire({
        title: 'Chargement...',
        text: 'Récupération des informations du véhicule',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        // CORRECTION: Ajouter heure_depart dans l'URL
        const url = "{{ route('user.reservation.vehicle', ':id') }}".replace(':id', vehicleId);
        let queryParams = `?date=${encodeURIComponent(dateVoyage)}&program_id=${programId}`;
        
        if (heureDepart) {
            queryParams += `&heure_depart=${encodeURIComponent(heureDepart)}`;
        }
        
        const response = await fetch(url + queryParams);
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.error || 'Véhicule non trouvé');
        }

        Swal.fire({
            title: `<strong>Places disponibles</strong>`,
            html: data.html,
            width: 850,
            padding: '20px',
            showCloseButton: true,
            showConfirmButton: false,
        });
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: error.message,
            confirmButtonColor: '#e94f1b',
        });
    }
}

        // ============================================
        // FONCTION 2: GÃ©nÃ©rer la visualisation des places
        // ============================================
        function generatePlacesVisualization(vehicle) {
            let config = typeRangeConfig[vehicle.type_range];
            if (!config) {
                config = { placesGauche: 2, placesDroite: 2 };
                console.warn(`Configuration de vÃ©hicule inconnue: ${vehicle.type_range}. Utilisation du mode par dÃ©faut 2x2.`);
            }
            const placesGauche = config.placesGauche;
            const placesDroite = config.placesDroite;
            const placesParRanger = placesGauche + placesDroite;
            const totalPlaces = parseInt(vehicle.nombre_place);
            const nombreRanger = Math.ceil(totalPlaces / placesParRanger);
            let html = `
                                                                                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                                                                                    <!-- En-tête -->
                                                                                    <div style="display: grid; grid-template-columns: 100px 1fr 80px 1fr; gap: 10px; padding: 15px; background: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                                                                                        <div style="text-align: center; font-weight: 600; color: #4b5563;">Rangee</div>
                                                                                        <div style="text-align: center; font-weight: 600; color: #4b5563;">CÃ´tÃ© gauche</div>
                                                                                        <div style="text-align: center; font-weight: 600; color: #4b5563;">AllÃ©e</div>
                                                                                        <div style="text-align: center; font-weight: 600; color: #4b5563;">CÃ´tÃ© droit</div>
                                                                                    </div>

                                                                                    <!-- Rangée -->
                                                                                    <div style="max-height: 400px; overflow-y:                                                 auto;">
                                                                            `;

            let numeroPlace = 1;

            for (let ranger = 1; ranger <= nombreRanger; ranger++) {
                const placesRestantes = totalPlaces - (numeroPlace - 1);
                const placesCetteRanger = Math.min(placesParRanger, placesRestantes);
                const placesGaucheCetteRanger = Math.min(placesGauche, placesCetteRanger);
                const placesDroiteCetteRanger = Math.min(placesDroite, placesCetteRanger - placesGaucheCetteRanger);

                html += `
                                                                                    <div style="display: grid; grid-template-columns: 100px 1fr 80px 1fr; gap: 10px; padding: 20px; align-items: center; ${ranger < nombreRanger ? 'border-bottom: 1px solid #e5e7eb;' : ''}">
                                                                                        <!-- Numéro de rangée -->
                                                                                        <div style="text-align: center; font-weight: 600; color: #6b7280;">Rangée ${ranger}</div>

                                                                                        <!-- Places côté gauche -->
                                                                                        <div style="display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
                                                                                `;

                // Places cÃ´tÃ© gauche
                for (let i = 0; i < placesGaucheCetteRanger; i++) {
                    html += `
                                                                                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #e94f1b, #e89116); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.1rem; box-shadow: 0 2px 4px rgba(254, 162, 25, 0.3); cursor: help;" title="Place ${numeroPlace + i}">
                                                                                            ${numeroPlace + i}
                                                                                        </div>
                                                                                    `;
                }

                html += `
                                                                                        </div>

                                                                                        <!-- AllÃ©e -->
                                                                                        <div style="text-align: center;">
                                                                                            <div style="width: 10px; height: 40px; background: #9ca3af; border-radius: 5px; margin: 0 auto;"></div>
                                                                                        </div>

                                                                                        <!-- Places cÃ´tÃ© droit -->
                                                                                        <div style="display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
                                                                                `;

                // Places cÃ´tÃ© droit
                for (let i = 0; i < placesDroiteCetteRanger; i++) {
                    html += `
                                                                                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.1rem; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3); cursor: help;" title="Place ${numeroPlace + placesGaucheCetteRanger + i}">
                                                                                            ${numeroPlace + placesGaucheCetteRanger + i}
                                                                                        </div>
                                                                                    `;
                }

                html += `
                                                                                        </div>
                                                                                    </div>
                                                                                `;

                numeroPlace += placesCetteRanger;
            }

            html += `
                                                                                    </div>
                                                                                </div>

                                                                                <!-- Légende -->
                                                                                <div style="display: flex; justify-content: center; gap: 20px; margin-top: 20px; padding: 15px; background: #f9fafb; border-radius: 8px;">
                                                                                    <div style="display: flex; align-items: center; gap: 8px;">
                                                                                        <div style="width: 20px; height: 20px; background: linear-gradient(135deg, #e94f1b, #e89116); border-radius: 4px;"></div>
                                                                                        <span style="color: #4b5563; font-size: 0.9rem;">Côté gauche (conducteur)</span>
                                                                                    </div>
                                                                                    <div style="display: flex; align-items: center; gap: 8px;">
                                                                                        <div style="width: 20px; height: 20px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 4px;"></div>
                                                                                        <span style="color: #4b5563; font-size: 0.9rem;">Côté droit</span>
                                                                                    </div>
                                                                                </div>
                                                                            `;

            return html;
        }

        // Variables globales (existantes, rappel)
        // let currentProgramId = null;
        // let selectedNumberOfPlaces = 0;
        // let selectedSeats = [];
        // let vehicleDetails = null;
        // let reservedSeats = [];

        // NOUVEAU: ID de requÃªte pour Ã©viter les conflits asynchrones
        // currentRequestId dÃ©jÃ  dÃ©clarÃ© en haut du script

        
        // ============================================
        // FONCTION 6: Afficher la sÃ©lection des places
        // ============================================
        async function showSeatSelection() {
            if (!currentProgramId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Aucun programme sÃ©lectionnÃ©.',
                    confirmButtonColor: '#e94f1b',
                });
                return;
            }

            // Afficher le loader
            Swal.fire({
                title: 'Chargement...',
                text: 'Récupération des informations',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                // 1. Récupérer le programme
                const programUrl = "{{ route('user.reservation.program', ':id') }}".replace(':id', currentProgramId) + `?date=${encodeURIComponent(window.currentReservationDate || '')}`;
                const programResponse = await fetch(programUrl);
                const programData = await programResponse.json();

                if (!programData.success) {
                    throw new Error(programData.error || 'Programme non trouvÃ©');
                }

                const program = programData.programme;

                // IMPORTANT: Utiliser la date stockÃ©e, pas la date du programme
                let dateVoyage = window.currentReservationDate;

                if (!dateVoyage) {
                    // Si pas de date stockÃ©e, utiliser la date du programme
                    const dateDepart = new Date(program.date_depart);
                    dateVoyage = dateDepart.toISOString().split('T')[0];
                    window.currentReservationDate = dateVoyage;
                }

                // 2. RÃ©cupÃ©rer le vÃ©hicule (avec fallback si pas de vÃ©hicule associÃ©)
                let vehicleId = program.vehicule_id;
                
                // Si pas de vÃ©hicule associÃ©, rÃ©cupÃ©rer le premier vÃ©hicule de la compagnie
                if (!vehicleId) {
                    console.log('Pas de vÃ©hicule associÃ© au programme, recherche vÃ©hicule par dÃ©faut...');
                    try {
                        const defaultVehicleUrl = "{{ route('user.reservation.default-vehicle', ':id') }}".replace(':id', currentProgramId) + `?date=${encodeURIComponent(dateVoyage)}`;
                        const defaultVehicleResponse = await fetch(defaultVehicleUrl);
                        if (defaultVehicleResponse.ok) {
                            const defaultVehicleData = await defaultVehicleResponse.json();
                            if (defaultVehicleData.success && defaultVehicleData.vehicule_id) {
                                vehicleId = defaultVehicleData.vehicule_id;
                            }
                        }
                    } catch (e) {
                        console.log('Erreur rÃ©cupÃ©ration vÃ©hicule par dÃ©faut:', e);
                    }
                }
                
                if (!vehicleId) {
                    // Utiliser une configuration de places par dÃ©faut (70 places)
                    vehicleDetails = {
                        type_range: '2x3',
                        capacite_total: 70,
                        marque: 'Bus',
                        modele: 'Standard'
                    };
                    console.log('Utilisation de la configuration par dÃ©faut (70 places):', vehicleDetails);
                } else {
                    const vehicleUrl = "{{ route('user.reservation.vehicle', ':id') }}".replace(':id', vehicleId);
                    const vehicleResponse = await fetch(vehicleUrl + `?date=${encodeURIComponent(dateVoyage)}&program_id=${currentProgramId}&heure_depart=${encodeURIComponent(window.selectedDepartureTime || '')}`);

                    if (!vehicleResponse.ok) {
                         const errText = await vehicleResponse.text();
                         console.error('Fetch Vehicle Error:', errText);
                         throw new Error(`Erreur HTTP ${vehicleResponse.status}`);
                    }

                    const responseText = await vehicleResponse.text();
                    let vehicleData;
                    try {
                        vehicleData = JSON.parse(responseText);
                    } catch(e) {
                        console.error('JSON Parse Error:', responseText);
                        // Show HTML preview in alert
                        const preview = responseText.substring(0, 100).replace(/<[^>]*>?/gm, '');
                        throw new Error('Réponse serveur invalide (JSON): ' + preview);
                    }

                    if (!vehicleData.success) {
                        throw new Error(vehicleData.error || 'VÃ©hicule non trouvÃ©');
                    }

                    vehicleDetails = vehicleData.vehicule;
                }

                // 3. Récupérer les places réservées POUR CETTE DATE SPÉCIFIQUE
                const seatsUrl = "{{ route('user.reservation.reserved-seats', ':id') }}".replace(':id', currentProgramId) + `?date=${encodeURIComponent(dateVoyage)}&heure_depart=${encodeURIComponent(window.selectedDepartureTime || '')}`;
                const seatsResponse = await fetch(seatsUrl);

                if (seatsResponse.ok) {
                    const seatsData = await seatsResponse.json();
                    if (seatsData.success) {
                        reservedSeats = seatsData.reservedSeats || [];
                        console.log('Places rÃ©servÃ©es pour', dateVoyage, ':', reservedSeats);
                    }
                }

                // Fermer le loader
                Swal.close();

                // GÃ©nÃ©rer la vue de sÃ©lection des places
                generateSeatSelectionView(program);

                // Changer d'Ã©tape
                document.getElementById('step1').classList.add('hidden');
                document.getElementById('step2').classList.remove('hidden');
                updateStepper(2);


            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    html: `
                                                                                    <div class="text-left">
                                                                                        <p class="mb-2">${error.message}</p>
                                                                                        <p class="text-sm text-gray-600 mt-2">
                                                                                            VÃ©rifiez que :
                                                                                            <ul class="list-disc pl-5 mt-1">
                                                                                                <li>Vous Ãªtes bien connectÃ©</li>
                                                                                                <li>Le programme existe toujours</li>
                                                                                                <li>Le vÃ©hicule est associÃ© au programme</li>
                                                                                            </ul>
                                                                                        </p>
                                                                                    </div>
                                                                                `,
                    confirmButtonColor: '#e94f1b',
                });
            }
        }

        // ============================================
        // FONCTION 7: GÃ©nÃ©rer la vue de sÃ©lection des places
        // ============================================
        function generateSeatSelectionView(program) {
            if (!vehicleDetails) {
                document.getElementById('seatSelectionArea').innerHTML =
                    '<p class="text-center text-red-500">Impossible de charger les informations du vÃ©hicule.</p>';
                return;
            }

            const config = typeRangeConfig[vehicleDetails.type_range] || typeRangeConfig['2x3'];
            if (!config) {
                document.getElementById('seatSelectionArea').innerHTML =
                    '<p class="text-center text-red-500">Impossible de charger la configuration des places.</p>';
                return;
            }

            const placesGauche = config.placesGauche;
            const placesDroite = config.placesDroite;
            const placesParRanger = placesGauche + placesDroite;
            // Utiliser capacite_total ou nombre_place selon ce qui est disponible
            const totalPlaces = parseInt(program.capacity || vehicleDetails.capacite_total || vehicleDetails.nombre_place || 70);
            window.currentTotalPlacesAller = totalPlaces;
            const nombreRanger = Math.ceil(totalPlaces / placesParRanger);
            
            // On ne montre plus les dÃ©tails du vÃ©hicule selon la demande utilisateur
            const programTitle = (program.compagnie?.sigle || 'Compagnie') + ' - ' + program.point_depart + ' → ' + program.point_arrive;

            window.seatAssignmentMode = null; // Réinitialiser le mode
            const basePriceTemp = parseInt(window.currentProgramPrice) * selectedNumberOfPlaces;

            let html = `
                <div class="bg-white p-2 sm:p-6 mb-6 rounded-xl">
                    <div class="text-center mb-6">
                        <h4 class="font-bold text-lg mb-2">${programTitle}</h4>
                        <p class="text-gray-600">Sélectionnez vos places | Total places: ${totalPlaces}</p>
                    </div>

                    <!-- Choix du mode d'assignation (Boutons larges style Image 2) -->
                    <div id="seatModeSelectionArea" class="grid sm:grid-cols-2 gap-4 sm:gap-6 mb-6 max-w-4xl mx-auto">
                        <!-- Automatique -->
                        <button type="button" onclick="selectSeatMode('auto')" id="modeAutoBtn" class="bg-blue-600 text-white rounded-2xl p-4 sm:p-6 shadow-md hover:shadow-lg transition-all flex flex-col items-center justify-center text-center relative overflow-hidden group border-4 border-transparent">
                            <div class="absolute inset-0 bg-blue-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative z-10 flex flex-col items-center">
                                <div class="flex items-center gap-3 mb-4">
                                    <i class="fas fa-cog text-4xl"></i>
                                    <span class="text-2xl font-black text-left leading-tight">PLACEMENT<br>AUTOMATIQUE</span>
                                </div>
                                <p class="font-medium text-blue-100 text-sm">Laissez-nous vous attribuer un siège.<br>Simple et rapide.</p>
                            </div>
                        </button>

                        <!-- Manuel -->
                        <button type="button" onclick="selectSeatMode('manual')" id="modeManualBtn" class="bg-[#f08800] text-white rounded-2xl p-4 sm:p-6 shadow-md hover:shadow-lg transition-all flex flex-col items-center justify-center text-center relative overflow-hidden group border-4 border-transparent">
                            <div class="absolute inset-0 bg-[#d97a00] opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative z-10 w-full flex flex-col items-center">
                                <div class="flex items-center gap-3 mb-4">
                                    <i class="fas fa-hand-pointer text-4xl"></i>
                                    <span class="text-2xl font-black text-left leading-tight">CHOISIR<br>SA PLACE</span>
                                </div>
                                <div class="bg-white text-gray-800 rounded-xl p-3 flex items-center justify-between w-full mt-2 shadow-sm">
                                    <p class="text-xs text-left font-medium leading-tight flex-1">Le choix de places ajoutera <strong class="text-[#e94f1b]">100 FCFA</strong> par place sur le prix du billet.</p>
                                    <div class="ml-2 w-10 h-10 border-2 border-gray-200 rounded grid grid-cols-2 gap-0.5 p-0.5" style="transform: scale(0.8)">
                                        <div class="bg-gray-200 rounded-sm"></div><div class="bg-gray-200 rounded-sm"></div>
                                        <div class="bg-orange-500 rounded-sm"></div><div class="bg-gray-200 rounded-sm"></div>
                                        <div class="bg-gray-200 rounded-sm"></div><div class="bg-orange-500 rounded-sm"></div>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </div>

                    <!-- Le conteneur du plan du bus -->
                    <div id="seatMapContainer" class="hidden">
                        <div class="flex justify-center mb-8 mt-10">
                            <div class="w-32 h-16 bg-gray-800 rounded-t-2xl flex items-center justify-center">
                                <i class="fas fa-bus text-white text-2xl"></i>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                                                                            `;

            let numeroPlace = 1;

            for (let ranger = 1; ranger <= nombreRanger; ranger++) {
                const placesRestantes = totalPlaces - (numeroPlace - 1);
                const placesCetteRanger = Math.min(placesParRanger, placesRestantes);
                const placesGaucheCetteRanger = Math.min(placesGauche, placesCetteRanger);
                const placesDroiteCetteRanger = Math.min(placesDroite, placesCetteRanger - placesGaucheCetteRanger);

                html += `
                                                                                    <div class="flex items-center justify-center mb-8 gap-8">
                                                                                        <!-- CÃ´tÃ© gauche -->
                                                                                        <div class="flex flex-col items-center">
                                                                                            <div class="text-sm text-gray-600 mb-2">Rangee ${ranger}</div>
                                                                                            <div class="flex gap-3">
                                                                                `;

                // Places cÃ´tÃ© gauche
                for (let i = 0; i < placesGaucheCetteRanger; i++) {
                    const seatNumber = numeroPlace + i;
                    const isReserved = reservedSeats.includes(seatNumber);
                    const isSelected = selectedSeats.includes(seatNumber);
                    const canSelect = !isReserved && selectedSeats.length < selectedNumberOfPlaces;

                    html += `
                                                                                        <div class="seat w-14 h-14 rounded-lg flex flex-col items-center justify-center font-bold transition-all duration-200
                                                                                                  ${isReserved ? 'bg-red-500 text-white cursor-not-allowed opacity-60' :
                            isSelected ? 'bg-[#e94f1b] text-white shadow-lg transform scale-110' :
                                'bg-blue-500 text-white hover:bg-blue-600 hover:shadow-md cursor-pointer'}"
                                                                                             ${!isReserved ? `onclick="toggleSeat(${seatNumber})"` : ''}
                                                                                             title="Place ${seatNumber}${isReserved ? ' (Réservée)' : ''}">
                                                                                            <span class="text-lg">${seatNumber}</span>
                                                                                            <span class="text-xs">${isReserved ? '✘' : (isSelected ? '✓' : '')}</span>
                                                                                        </div>
                                                                                    `;
                }

                html += `
                                                                                            </div>
                                                                                        </div>

                                                                                        <!-- AllÃ©e -->
                                                                                        <div class="w-20 h-2 bg-gray-400 rounded my-8"></div>

                                                                                        <!-- CÃ´tÃ© droit -->
                                                                                        <div class="flex flex-col items-center">
                                                                                            <div class="text-sm text-gray-600 mb-2">Rangée ${ranger}</div>
                                                                                            <div class="flex gap-3">
                                                                                `;

                // Places cÃ´tÃ© droit
                for (let i = 0; i < placesDroiteCetteRanger; i++) {
                    const seatNumber = numeroPlace + placesGaucheCetteRanger + i;
                    const isReserved = reservedSeats.includes(seatNumber);
                    const isSelected = selectedSeats.includes(seatNumber);
                    const canSelect = !isReserved && selectedSeats.length < selectedNumberOfPlaces;

                    html += `
                                                                                        <div class="seat w-14 h-14 rounded-lg flex flex-col items-center justify-center font-bold transition-all duration-200
                                                                                                  ${isReserved ? 'bg-red-500 text-white cursor-not-allowed opacity-60' :
                            isSelected ? 'bg-[#e94f1b] text-white shadow-lg transform scale-110' :
                                'bg-green-500 text-white hover:bg-green-600 hover:shadow-md cursor-pointer'}"
                                                                                             ${!isReserved ? `onclick="toggleSeat(${seatNumber})"` : ''}
                                                                                             title="Place ${seatNumber}${isReserved ? ' (Réservée)' : ''}">
                                                                                            <span class="text-lg">${seatNumber}</span>
                                                                                            <span class="text-xs">${isReserved ? '✘' : (isSelected ? '✓' : '')}</span>
                                                                                        </div>
                                                                                    `;
                }

                html += `
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                `;

                numeroPlace += placesCetteRanger;
            }

            html += `
                        </div>

                        <!-- Information -->
                        <div class="p-4 bg-blue-50 rounded-lg mt-6">
                            <p class="text-sm text-gray-700">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                Sélectionnez ${selectedNumberOfPlaces} place${selectedNumberOfPlaces > 1 ? 's' : ''} en cliquant sur les places disponibles.
                                Les places en rouge sont déjà réservées.
                            </p>
                        </div>
                    </div>

                    <!-- Barre de prix fixe en bas (style image 2) -->
                    <div class="bg-gray-100 rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between mt-8 max-w-4xl mx-auto border border-gray-200 shadow-sm relative overflow-hidden">
                        <div class="flex items-center gap-6 mb-4 sm:mb-0 w-full sm:w-auto">
                            <div>
                                <p class="text-gray-500 text-[10px] font-bold uppercase tracking-wider">Prix du billet:</p>
                                <p class="font-black text-lg text-gray-800">${parseInt(window.currentProgramPrice).toLocaleString('fr-FR')} FCFA</p>
                            </div>
                            <div class="w-px h-10 bg-gray-300 hidden sm:block"></div>
                            <div>
                                <p class="text-gray-500 text-[10px] font-bold uppercase tracking-wider">Prix Total:</p>
                                <p class="font-black text-lg text-gray-800"><span id="basePriceDisplay">${basePriceTemp.toLocaleString('fr-FR')} FCFA</span> <span id="extraPriceDisplay" class="text-orange-500 hidden"></span></p>
                            </div>
                        </div>
                        <button onclick="handleSeatValidation()" id="validateSeatBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl w-full sm:w-auto transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            VALIDER LE CHOIX
                        </button>
                    </div>
                </div>
            `;

            document.getElementById('seatSelectionArea').innerHTML = html;
            updateSelectedSeatsCount();
        }

        window.seatAssignmentMode = null;
        window.seatSelectionExtraCost = 0;

        function selectSeatMode(mode) {
            window.seatAssignmentMode = mode;
            const modeAutoBtn = document.getElementById('modeAutoBtn');
            const modeManualBtn = document.getElementById('modeManualBtn');
            const seatMapContainer = document.getElementById('seatMapContainer');
            
            if (mode === 'auto') {
                modeAutoBtn.classList.add('ring-4', 'ring-blue-300', 'scale-[1.02]');
                modeManualBtn.classList.remove('ring-4', 'ring-orange-300', 'scale-[1.02]');
                modeManualBtn.style.opacity = '0.6';
                modeAutoBtn.style.opacity = '1';
                
                seatMapContainer.classList.add('hidden');
                autoAssignSeats();
            } else {
                modeManualBtn.classList.add('ring-4', 'ring-orange-300', 'scale-[1.02]');
                modeAutoBtn.classList.remove('ring-4', 'ring-blue-300', 'scale-[1.02]');
                modeAutoBtn.style.opacity = '0.6';
                modeManualBtn.style.opacity = '1';
                
                selectedSeats.forEach(seat => {
                    const el = document.querySelector(`[onclick="toggleSeat(${seat})"]`);
                    if(el) {
                        el.classList.remove('bg-[#e94f1b]', 'shadow-lg', 'transform', 'scale-110');
                        el.querySelector('.text-xs').textContent = '';
                        const isLeftSide = seat <= typeRangeConfig[vehicleDetails.type_range].placesGauche;
                        el.classList.add(isLeftSide ? 'bg-blue-500' : 'bg-green-500');
                    }
                });
                selectedSeats = [];
                updateSelectedSeatsCount();
                
                seatMapContainer.classList.remove('hidden');
                setTimeout(() => { seatMapContainer.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 100);
            }
        }

        function updatePriceDisplay() {
            const basePrice = parseInt(window.currentProgramPrice) * selectedNumberOfPlaces;
            const extraPriceElem = document.getElementById('extraPriceDisplay');
            
            if (window.seatAssignmentMode === 'manual') {
                const addedPrice = selectedSeats.length * 100;
                if (addedPrice > 0) {
                    extraPriceElem.textContent = ' / ' + (basePrice + addedPrice).toLocaleString('fr-FR') + ' FCFA';
                    extraPriceElem.classList.remove('hidden');
                } else {
                    extraPriceElem.classList.add('hidden');
                }
            } else {
                extraPriceElem.classList.add('hidden');
            }
        }
        
        function handleSeatValidation() {
            if (selectedSeats.length !== parseInt(selectedNumberOfPlaces)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sélection incomplète',
                    text: `Veuillez sélectionner ${selectedNumberOfPlaces} place(s).`,
                    confirmButtonColor: '#e94f1b'
                });
                return;
            }
            if (window.seatAssignmentMode === 'manual') {
                window.seatSelectionExtraCost = selectedSeats.length * 100;
            } else {
                window.seatSelectionExtraCost = 0;
            }
            showPassengerInfo();
        }

        // ============================================
        // FONCTION 7.1: Basculer le mode de sÃ©lection
        // ============================================
        function toggleSelectionMode(mode) {
             // RÃ©initialiser la sÃ©lection si on change de mode
            selectedSeats.forEach(seat => {
                const el = document.querySelector(`[onclick="toggleSeat(${seat})"]`);
                if(el) {
                    el.classList.remove('bg-[#e94f1b]', 'shadow-lg', 'transform', 'scale-110');
                    el.querySelector('.text-xs').textContent = '';
                    const isLeftSide = seat <= typeRangeConfig[vehicleDetails.type_range].placesGauche;
                    el.classList.add(isLeftSide ? 'bg-blue-500' : 'bg-green-500');
                }
            });
            selectedSeats = [];
            updateSelectedSeatsCount();

            // Feedback visuel sur les boutons
            const btnManual = document.getElementById('btnManualSelect');
            const btnAuto = document.getElementById('btnAutoAssign');
            
            if (mode === 'manual') {
                btnManual.classList.add('bg-[#e94f1b]', 'text-white');
                btnManual.classList.remove('bg-gray-200', 'text-gray-800');
                btnAuto.classList.remove('bg-blue-600', 'text-white');
                btnAuto.classList.add('bg-blue-500', 'text-white'); // Keep auto button generic
            }
        }

        // ============================================
        // FONCTION 7.2: Assignation automatique
        // ============================================
        function autoAssignSeats() {
            // 1. RÃ©initialiser la sÃ©lection actuelle
            selectedSeats = [];
            
            // 2. Trouver toutes les places disponibles
            const totalPlaces = window.currentTotalPlacesAller || parseInt(vehicleDetails.capacite_total || vehicleDetails.nombre_place || 70);
            const availableSeats = [];
            
            for (let i = 1; i <= totalPlaces; i++) {
                if (!reservedSeats.includes(i)) {
                    availableSeats.push(i);
                }
            }
            
            // 3. VÃ©rifier s'il y a assez de places
            if (availableSeats.length < selectedNumberOfPlaces) {
                Swal.fire({
                    icon: 'error',
                    title: 'Pas assez de places',
                    text: 'Il ne reste pas suffisamment de places disponibles pour votre demande.',
                    confirmButtonColor: '#e94f1b'
                });
                return false;
            }
            
            // 4. SÃ©lectionner alÃ©atoirement
            const shuffled = availableSeats.sort(() => 0.5 - Math.random());
            const selected = shuffled.slice(0, selectedNumberOfPlaces);
            
            // 5. Appliquer la sÃ©lection visuellement (sans passer par toggleSeat pour Ã©viter les alertes)
            selected.forEach(seat => {
                selectedSeats.push(seat);
                const seatElement = document.querySelector(`[onclick="toggleSeat(${seat})"]`);
                if (seatElement) {
                    // Simuler l'affichage sÃ©lectionnÃ©
                    const isLeftSide = seat <= typeRangeConfig[vehicleDetails.type_range].placesGauche;
                    seatElement.classList.add('bg-[#e94f1b]', 'text-white', 'shadow-lg', 'transform', 'scale-110');
                    seatElement.classList.remove(isLeftSide ? 'bg-blue-500' : 'bg-green-500');
                    seatElement.classList.remove('hover:bg-blue-600', 'hover:bg-green-600'); // Optional cleanup
                    seatElement.querySelector('.text-xs').textContent = '✓';
                }
            });
            
            updateSelectedSeatsCount();
            return true;
        }

        // ============================================
        // FONCTION 8: SÃ©lection/dÃ©sÃ©lection d'une place
        // ============================================
        function toggleSeat(seatNumber) {
            const index = selectedSeats.indexOf(seatNumber);

            if (index === -1) {
                // VÃ©rifier si on n'a pas dÃ©passÃ© le nombre de places sÃ©lectionnÃ©es
                if (selectedSeats.length >= selectedNumberOfPlaces) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Limite atteinte',
                        text: `Vous ne pouvez sélectionner que ${selectedNumberOfPlaces} place(s). désélectionnez d'abord une place si vous voulez en choisir une autre.`,
                        confirmButtonColor: '#e94f1b',
                    });
                    return;
                }
                selectedSeats.push(seatNumber);
            } else {
                selectedSeats.splice(index, 1);
            }

            // Mettre Ã  jour l'affichage de la place
            const seatElement = document.querySelector(`[onclick="toggleSeat(${seatNumber})"]`);
            if (seatElement) {
                const isSelected = selectedSeats.includes(seatNumber);
                const isLeftSide = seatNumber <= typeRangeConfig[vehicleDetails.type_range].placesGauche;

                seatElement.classList.toggle('bg-[#e94f1b]', isSelected);
                seatElement.classList.toggle('transform', isSelected);
                seatElement.classList.toggle('scale-110', isSelected);
                seatElement.classList.toggle('shadow-lg', isSelected);

                if (!isSelected) {
                    seatElement.classList.add(isLeftSide ? 'bg-blue-500' : 'bg-green-500');
                    seatElement.classList.remove(isLeftSide ? 'bg-green-500' : 'bg-blue-500');
                } else {
                    seatElement.classList.remove('bg-blue-500', 'bg-green-500');
                }

                // Mettre Ã  jour le checkmark
                const checkmark = seatElement.querySelector('.text-xs');
                if (checkmark) {
                    checkmark.textContent = isSelected ? '✓' : '';
                }
            }

            updateSelectedSeatsCount();
        }

        // ============================================
        // FONCTION 9: Mettre Ã  jour le compteur
        // ============================================
        function updateSelectedSeatsCount() {
            const count = selectedSeats.length;
            const countElement = document.getElementById('selectedSeatsCount');
            const nextBtn = document.getElementById('showPassengerInfoBtn');
            const validateBtn = document.getElementById('validateSeatBtn');

            if (countElement) {
                countElement.textContent =
                    `${count} place${count > 1 ? 's' : ''} sélectionnée${count > 1 ? 's' : ''} / ${selectedNumberOfPlaces} demandée${selectedNumberOfPlaces > 1 ? 's' : ''}`;

                countElement.classList.remove('text-[#e94f1b]', 'text-red-500', 'text-green-500');
                if (count === 0) {
                    countElement.classList.add('text-gray-600');
                } else if (count < selectedNumberOfPlaces) {
                    countElement.classList.add('text-[#e94f1b]');
                } else if (count === parseInt(selectedNumberOfPlaces)) {
                    countElement.classList.add('text-green-500');
                }
            }

            const isComplete = count === parseInt(selectedNumberOfPlaces);

            if (nextBtn) {
                nextBtn.disabled = !isComplete;
            }
            if (validateBtn) {
                validateBtn.disabled = !isComplete;
            }

            if (typeof updatePriceDisplay === 'function') {
                updatePriceDisplay();
            }
        }

        // ============================================
        // FONCTION 10: Retour Ã  l'Ã©tape 1
        // ============================================
        function backToStep1() {
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step1').classList.remove('hidden');
            // RÃ©initialiser la sÃ©lection des places
            selectedSeats = [];
        }

        // ============================================
        // FONCTION 10.1: Retour Ã  l'Ã©tape 2
        // ============================================
        function backToStep2() {
            document.getElementById('step3').classList.add('hidden');
            document.getElementById('step2_5').classList.add('hidden');
            document.getElementById('step2').classList.remove('hidden');
            updateStepper(2);
        }

        // ============================================
        // FONCTION 10.2: Afficher les infos passagers
        // ============================================
      function showPassengerInfo() {
    // Si aucune place sÃ©lectionnÃ©e, on lance l'assignation automatique
    if (selectedSeats.length === 0) {
        const success = autoAssignSeats();
        if (!success) return;
        
        setTimeout(() => {
            checkIfNeedRetourSelection();
        }, 500);
    } else {
        checkIfNeedRetourSelection();
    }
}

function checkIfNeedRetourSelection() {
    // Si c'est un aller-retour ET qu'on n'a pas encore sÃ©lectionnÃ© les places retour
    if (window.userChoseAllerRetour && selectedSeatsRetour.length === 0) {
        // Charger et afficher la sÃ©lection des places retour
        loadRetourSeatsSelection();
    } else {
        // Sinon, passer directement aux infos passagers
        proceedToPassengerInfo();
    }
}
async function loadRetourSeatsSelection() {
    // RÃ©cupÃ©rer le programme retour
    const retourProgId = window.selectedRetourProgramId || window.selectedReturnProgram?.id;
    
    if (!retourProgId) {
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Programme retour introuvable.'
        });
        return;
    }

    currentRetourProgramId = retourProgId;

    Swal.fire({
        title: 'Chargement...',
        text: 'Récupération des places disponibles pour le retour',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    try {
        // 1. Récupérer le programme retour
        const programUrl = "{{ route('user.reservation.program', ':id') }}".replace(':id', retourProgId) + `?date=${encodeURIComponent(window.selectedReturnDate || '')}`;
        const programResponse = await fetch(programUrl);
        const programData = await programResponse.json();

        if (!programData.success) {
            throw new Error(programData.error || 'Programme retour non trouvÃ©');
        }

        const programRetour = programData.programme;

        // 2. RÃ©cupÃ©rer le vÃ©hicule
        let vehicleId = programRetour.vehicule_id;
        
        if (!vehicleId) {
            // Fallback : vÃ©hicule par dÃ©faut
            const defaultVehicleUrl = "{{ route('user.reservation.default-vehicle', ':id') }}".replace(':id', retourProgId) + `?date=${encodeURIComponent(window.selectedReturnDate || '')}`;
            const defaultVehicleResponse = await fetch(defaultVehicleUrl);
            if (defaultVehicleResponse.ok) {
                const defaultVehicleData = await defaultVehicleResponse.json();
                if (defaultVehicleData.success && defaultVehicleData.vehicule_id) {
                    vehicleId = defaultVehicleData.vehicule_id;
                }
            }
        }
        
        if (!vehicleId) {
            vehicleDetailsRetour = {
                type_range: '2x3',
                capacite_total: 70,
                marque: 'Bus',
                modele: 'Standard'
            };
        } else {
            const vehicleUrl = "{{ route('user.reservation.vehicle', ':id') }}".replace(':id', vehicleId);
            const vehicleResponse = await fetch(vehicleUrl + `?date=${encodeURIComponent(window.selectedReturnDate || '')}&program_id=${retourProgId}&heure_depart=${encodeURIComponent(window.selectedReturnTime || '')}`);
            const vehicleData = await vehicleResponse.json();
            
            if (!vehicleData.success) {
                throw new Error('VÃ©hicule retour non trouvÃ©');
            }
            
            vehicleDetailsRetour = vehicleData.vehicule;
        }

        // 3. Récupérer les places réservées pour le retour
        let heureDepart = window.selectedReturnTime || programRetour.heure_depart; // Corrected variable name
        const dateRetour = window.selectedReturnDate || window.currentReservationDate;
        const seatsUrl = "{{ route('user.reservation.reserved-seats', ':id') }}".replace(':id', retourProgId) + `?date=${encodeURIComponent(dateRetour)}&heure_depart=${encodeURIComponent(heureDepart)}`;
        const seatsResponse = await fetch(seatsUrl);

         if (seatsResponse.ok) {
        const seatsData = await seatsResponse.json();
        if (seatsData.success) {
            reservedSeatsRetour = seatsData.reservedSeats || [];
            console.log('Places réservées pour le retour', dateRetour, 'à', heureDepart, ':', reservedSeatsRetour);
        }
    }

        Swal.close();

        // 4. Afficher les infos du retour
        const dateRetourFormatted = new Date(dateRetour).toLocaleDateString('fr-FR');
        document.getElementById('returnProgramInfo').innerHTML = `
            <span><i class="fas fa-map-marker-alt"></i> ${programRetour.point_depart} → ${programRetour.point_arrive}</span>
            <span class="mx-2">|</span>
            <span><i class="fas fa-calendar"></i> ${dateRetourFormatted}</span>
            <span class="mx-2">|</span>
            <span><i class="fas fa-clock"></i> ${programRetour.heure_depart}</span>
        `;

        // 5. GÃ©nÃ©rer la vue de sÃ©lection des places retour
        generateSeatSelectionViewRetour(programRetour);

        // 6. Masquer step2, afficher step2_5
        document.getElementById('step2').classList.add('hidden');
        document.getElementById('step2_5').classList.remove('hidden');
        updateStepper(2);

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: error.message,
            confirmButtonColor: '#e94f1b',
        });
    }
}
function generateSeatSelectionViewRetour(program) {
    if (!vehicleDetailsRetour) {
        document.getElementById('seatSelectionAreaRetour').innerHTML =
            '<p class="text-center text-red-500">Impossible de charger les informations du vÃ©hicule.</p>';
        return;
    }

    const config = typeRangeConfig[vehicleDetailsRetour.type_range];
    if (!config) {
        document.getElementById('seatSelectionAreaRetour').innerHTML =
            '<p class="text-center text-red-500">Configuration de places non reconnue.</p>';
        return;
    }

    const placesGauche = config.placesGauche;
    const placesDroite = config.placesDroite;
    const placesParRanger = placesGauche + placesDroite;
    // Utiliser capacite_total ou nombre_place selon ce qui est disponible
    const totalPlaces = parseInt(program.capacity || vehicleDetailsRetour.capacite_total || vehicleDetailsRetour.nombre_place || 70);
    window.currentTotalPlacesRetour = totalPlaces;
    const nombreRanger = Math.ceil(totalPlaces / placesParRanger);
    
    // On ne montre plus les dÃ©tails du vÃ©hicule
    const programTitle = (program.compagnie?.sigle || 'Compagnie') + ' - ' + program.point_depart + ' → ' + program.point_arrive;

    window.seatAssignmentModeRetour = null;

    let html = `
        <div class="bg-white p-2 sm:p-6 mb-6 rounded-xl">
            <div class="text-center mb-6">
                <h4 class="font-bold text-lg mb-2">${programTitle} (Retour)</h4>
                <p class="text-gray-600">Sélectionnez vos places | Total places: ${totalPlaces}</p>
            </div>
            
            <!-- Choix du mode d'assignation -->
            <div id="seatModeSelectionAreaRetour" class="grid sm:grid-cols-2 gap-4 sm:gap-6 mb-6 max-w-4xl mx-auto">
                <button type="button" onclick="selectSeatModeRetour('auto')" id="modeAutoBtnRetour" class="bg-blue-600 text-white rounded-2xl p-4 sm:p-6 shadow-md hover:shadow-lg transition-all flex flex-col items-center justify-center text-center relative overflow-hidden group border-4 border-transparent">
                    <div class="absolute inset-0 bg-blue-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="flex items-center gap-3 mb-4">
                            <i class="fas fa-cog text-4xl"></i>
                            <span class="text-2xl font-black text-left leading-tight">PLACEMENT<br>AUTOMATIQUE</span>
                        </div>
                        <p class="font-medium text-blue-100 text-sm">Laissez-nous vous attribuer un siège.<br>Simple et rapide.</p>
                    </div>
                </button>

                <button type="button" onclick="selectSeatModeRetour('manual')" id="modeManualBtnRetour" class="bg-[#f08800] text-white rounded-2xl p-4 sm:p-6 shadow-md hover:shadow-lg transition-all flex flex-col items-center justify-center text-center relative overflow-hidden group border-4 border-transparent">
                    <div class="absolute inset-0 bg-[#d97a00] opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10 w-full flex flex-col items-center">
                        <div class="flex items-center gap-3 mb-4">
                            <i class="fas fa-hand-pointer text-4xl"></i>
                            <span class="text-2xl font-black text-left leading-tight">CHOISIR<br>SA PLACE</span>
                        </div>
                        <div class="bg-white text-gray-800 rounded-xl p-3 flex items-center justify-between w-full shadow-inner mt-2">
                            <p class="text-xs text-left font-medium leading-tight flex-1">Le choix de places ajoutera <strong class="text-[#e94f1b]">100 FCFA</strong> par place sur le prix du billet.</p>
                            <div class="ml-2 w-10 h-10 border-2 border-gray-200 rounded grid grid-cols-2 gap-0.5 p-0.5" style="transform: scale(0.8)">
                                <div class="bg-gray-200 rounded-sm"></div><div class="bg-gray-200 rounded-sm"></div>
                                <div class="bg-orange-500 rounded-sm"></div><div class="bg-gray-200 rounded-sm"></div>
                                <div class="bg-gray-200 rounded-sm"></div><div class="bg-orange-500 rounded-sm"></div>
                            </div>
                        </div>
                    </div>
                </button>
            </div>

            <!-- Conteneur du plan du bus retour -->
            <div id="seatMapContainerRetour" class="hidden">
                <div class="flex justify-center mb-8 mt-10">
                    <div class="w-32 h-16 bg-gray-800 rounded-t-2xl flex items-center justify-center">
                        <i class="fas fa-bus text-white text-2xl"></i>
                    </div>
                </div>
                <div class="overflow-x-auto">
    `;

    let numeroPlace = 1;

    for (let ranger = 1; ranger <= nombreRanger; ranger++) {
        const placesRestantes = totalPlaces - (numeroPlace - 1);
        const placesCetteRanger = Math.min(placesParRanger, placesRestantes);
        const placesGaucheCetteRanger = Math.min(placesGauche, placesCetteRanger);
        const placesDroiteCetteRanger = Math.min(placesDroite, placesCetteRanger - placesGaucheCetteRanger);

        html += `
            <div class="flex items-center justify-center mb-8 gap-8">
                <!-- CÃ´tÃ© gauche -->
                <div class="flex flex-col items-center">
                    <div class="text-sm text-gray-600 mb-2">Rangée ${ranger}</div>
                    <div class="flex gap-3">
        `;

        // Places cÃ´tÃ© gauche
        for (let i = 0; i < placesGaucheCetteRanger; i++) {
            const seatNumber = numeroPlace + i;
            const isReserved = reservedSeatsRetour.includes(seatNumber);
            const isSelected = selectedSeatsRetour.includes(seatNumber);

            html += `
                <div class="seat w-14 h-14 rounded-lg flex flex-col items-center justify-center font-bold transition-all duration-200
                          ${isReserved ? 'bg-red-500 text-white cursor-not-allowed opacity-60' :
                    isSelected ? 'bg-blue-600 text-white shadow-lg transform scale-110' :
                        'bg-blue-500 text-white hover:bg-blue-600 hover:shadow-md cursor-pointer'}"
                     ${!isReserved ? `onclick="toggleSeatRetour(${seatNumber})"` : ''}
                     title="Place ${seatNumber}${isReserved ? ' (RÃ©servÃ©e)' : ''}">
                    <span class="text-lg">${seatNumber}</span>
                    <span class="text-xs">${isReserved ? '✘' : (isSelected ? '✓' : '')}</span>
                </div>
            `;
        }

        html += `
                    </div>
                </div>

                <!-- AllÃ©e -->
                <div class="w-20 h-2 bg-gray-400 rounded my-8"></div>

                <!-- CÃ´tÃ© droit -->
                <div class="flex flex-col items-center">
                    <div class="text-sm text-gray-600 mb-2">Rangée ${ranger}</div>
                    <div class="flex gap-3">
        `;

        // Places cÃ´tÃ© droit
        for (let i = 0; i < placesDroiteCetteRanger; i++) {
            const seatNumber = numeroPlace + placesGaucheCetteRanger + i;
            const isReserved = reservedSeatsRetour.includes(seatNumber);
            const isSelected = selectedSeatsRetour.includes(seatNumber);

            html += `
                <div class="seat w-14 h-14 rounded-lg flex flex-col items-center justify-center font-bold transition-all duration-200
                          ${isReserved ? 'bg-red-500 text-white cursor-not-allowed opacity-60' :
                    isSelected ? 'bg-blue-600 text-white shadow-lg transform scale-110' :
                        'bg-green-500 text-white hover:bg-green-600 hover:shadow-md cursor-pointer'}"
                     ${!isReserved ? `onclick="toggleSeatRetour(${seatNumber})"` : ''}
                     title="Place ${seatNumber}${isReserved ? ' (Réservée)' : ''}">
                    <span class="text-lg">${seatNumber}</span>
                    <span class="text-xs">${isReserved ? '✘' : (isSelected ? '✓' : '')}</span>
                </div>
            `;
        }

        html += `
                    </div>
                </div>
            </div>
        `;

        numeroPlace += placesCetteRanger;
    }

        html += `
                </div>

                <!-- Information -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-gray-700">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        Sélectionnez ${selectedNumberOfPlaces} place${selectedNumberOfPlaces > 1 ? 's' : ''} pour le retour.
                        Les places en rouge sont déjà réservées.
                    </p>
                </div>
            </div>
            
            <!-- Barre de validation de validation retour -->
            <div class="bg-gray-100 rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-end mt-8 max-w-4xl mx-auto border border-gray-200 shadow-sm relative overflow-hidden">
                <button onclick="handleSeatValidationRetour()" id="validateSeatBtnRetour" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl w-full sm:w-auto transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    VALIDER LE CHOIX DU RETOUR
                </button>
            </div>
        </div>
    `;

    document.getElementById('seatSelectionAreaRetour').innerHTML = html;
    updateSelectedSeatsCountRetour();
}
function toggleSeatRetour(seatNumber) {
    const index = selectedSeatsRetour.indexOf(seatNumber);

    if (index === -1) {
        if (selectedSeatsRetour.length >= selectedNumberOfPlaces) {
            Swal.fire({
                icon: 'warning',
                title: 'Limite atteinte',
                text: `Vous ne pouvez sélectionner que ${selectedNumberOfPlaces} place(s). Désélectionnez d'abord une place si vous voulez en choisir une autre.`,
                confirmButtonColor: '#3b82f6',
            });
            return;
        }
        selectedSeatsRetour.push(seatNumber);
    } else {
        selectedSeatsRetour.splice(index, 1);
    }

    // Mettre Ã  jour l'affichage
    const seatElement = document.querySelector(`[onclick="toggleSeatRetour(${seatNumber})"]`);
    if (seatElement) {
        const isSelected = selectedSeatsRetour.includes(seatNumber);
        const isLeftSide = seatNumber <= typeRangeConfig[vehicleDetailsRetour.type_range].placesGauche;

        seatElement.classList.toggle('bg-blue-600', isSelected);
        seatElement.classList.toggle('transform', isSelected);
        seatElement.classList.toggle('scale-110', isSelected);
        seatElement.classList.toggle('shadow-lg', isSelected);

        if (!isSelected) {
            seatElement.classList.add(isLeftSide ? 'bg-blue-500' : 'bg-green-500');
            seatElement.classList.remove(isLeftSide ? 'bg-green-500' : 'bg-blue-500');
        } else {
            seatElement.classList.remove('bg-blue-500', 'bg-green-500');
        }

        const checkmark = seatElement.querySelector('.text-xs');
        if (checkmark) {
            checkmark.textContent = isSelected ? '✓' : '';
        }
    }

    updateSelectedSeatsCountRetour();
}

function updateSelectedSeatsCountRetour() {
    const count = selectedSeatsRetour.length;
    const countElement = document.getElementById('selectedSeatsCountRetour');
    const nextBtn = document.getElementById('showPassengerInfoBtnRetour');
    const validateBtn = document.getElementById('validateSeatBtnRetour');

    if (countElement) {
        countElement.textContent =
            `${count} place${count > 1 ? 's' : ''} sélectionnée${count > 1 ? 's' : ''} / ${selectedNumberOfPlaces} demandée${selectedNumberOfPlaces > 1 ? 's' : ''}`;

        countElement.classList.remove('text-blue-600', 'text-red-500', 'text-green-500');
        if (count === 0) {
            countElement.classList.add('text-gray-600');
        } else if (count < selectedNumberOfPlaces) {
            countElement.classList.add('text-blue-600');
        } else if (count === parseInt(selectedNumberOfPlaces)) {
            countElement.classList.add('text-green-500');
        }
    }

    const isComplete = count === parseInt(selectedNumberOfPlaces);

    if (nextBtn) {
        nextBtn.disabled = !isComplete;
    }
    if (validateBtn) {
        validateBtn.disabled = !isComplete;
    }
}

function autoAssignSeatsRetour() {
    selectedSeatsRetour = [];
    
    const totalPlaces = window.currentTotalPlacesRetour || parseInt(vehicleDetailsRetour.capacite_total || vehicleDetailsRetour.nombre_place || 70);
    const availableSeats = [];
    
    for (let i = 1; i <= totalPlaces; i++) {
        if (!reservedSeatsRetour.includes(i)) {
            availableSeats.push(i);
        }
    }
    
    if (availableSeats.length < selectedNumberOfPlaces) {
        Swal.fire({
            icon: 'error',
            title: 'Pas assez de places',
            text: 'Il ne reste pas suffisamment de places disponibles pour le retour.',
            confirmButtonColor: '#3b82f6'
        });
        return false;
    }
    
    const shuffled = availableSeats.sort(() => 0.5 - Math.random());
    const selected = shuffled.slice(0, selectedNumberOfPlaces);
    
    selected.forEach(seat => {
        selectedSeatsRetour.push(seat);
        const seatElement = document.querySelector(`[onclick="toggleSeatRetour(${seat})"]`);
        if (seatElement) {
            const isLeftSide = seat <= typeRangeConfig[vehicleDetailsRetour.type_range].placesGauche;
            seatElement.classList.add('bg-blue-600', 'text-white', 'shadow-lg', 'transform', 'scale-110');
            seatElement.classList.remove(isLeftSide ? 'bg-blue-500' : 'bg-green-500');
            seatElement.querySelector('.text-xs').textContent = '✓';
        }
    });
    
    updateSelectedSeatsCountRetour();
    return true;
}

function selectSeatModeRetour(mode) {
    window.seatAssignmentModeRetour = mode;
    const modeAutoBtn = document.getElementById('modeAutoBtnRetour');
    const modeManualBtn = document.getElementById('modeManualBtnRetour');
    const seatMapContainer = document.getElementById('seatMapContainerRetour');
    
    if (mode === 'auto') {
        modeAutoBtn.classList.add('ring-4', 'ring-blue-300', 'scale-[1.02]');
        modeManualBtn.classList.remove('ring-4', 'ring-orange-300', 'scale-[1.02]');
        modeManualBtn.style.opacity = '0.6';
        modeAutoBtn.style.opacity = '1';
        
        seatMapContainer.classList.add('hidden');
        autoAssignSeatsRetour();
    } else {
        modeManualBtn.classList.add('ring-4', 'ring-orange-300', 'scale-[1.02]');
        modeAutoBtn.classList.remove('ring-4', 'ring-blue-300', 'scale-[1.02]');
        modeAutoBtn.style.opacity = '0.6';
        modeManualBtn.style.opacity = '1';
        
        selectedSeatsRetour.forEach(seat => {
            const el = document.querySelector(`[onclick="toggleSeatRetour(${seat})"]`);
            if(el) {
                el.classList.remove('bg-blue-600', 'shadow-lg', 'transform', 'scale-110');
                el.querySelector('.text-xs').textContent = '';
                const isLeftSide = seat <= typeRangeConfig[vehicleDetailsRetour.type_range].placesGauche;
                el.classList.add(isLeftSide ? 'bg-blue-500' : 'bg-green-500');
            }
        });
        selectedSeatsRetour = [];
        updateSelectedSeatsCountRetour();
        
        seatMapContainer.classList.remove('hidden');
        setTimeout(() => { seatMapContainer.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 100);
    }
}

function handleSeatValidationRetour() {
    if (selectedSeatsRetour.length !== parseInt(selectedNumberOfPlaces)) {
        Swal.fire({
            icon: 'warning',
            title: 'Sélection incomplète',
            text: `Veuillez sélectionner ${selectedNumberOfPlaces} place(s).`,
            confirmButtonColor: '#e94f1b'
        });
        return;
    }
    
    if (window.seatAssignmentModeRetour === 'manual') {
        window.seatSelectionExtraCostRetour = selectedSeatsRetour.length * 100; 
    } else {
        window.seatSelectionExtraCostRetour = 0;
    }
    
    proceedToPassengerInfoFromRetour();
}

function toggleSelectionModeRetour(mode) {
    // legacy
}

function proceedToPassengerInfoFromRetour() {
    if (selectedSeatsRetour.length === 0) {
        const success = autoAssignSeatsRetour();
        if (!success) return;
        
        setTimeout(() => {
            proceedToPassengerInfo();
        }, 500);
    } else {
        proceedToPassengerInfo();
    }
}
    function proceedToPassengerInfo() {
            const formArea = document.getElementById('passengersFormArea');
            formArea.innerHTML = '';

            // Trier les places pour assigner les passagers dans l'ordre
            const sortedSeats = [...selectedSeats].sort((a, b) => a - b);
            
            // AUTOFILL TOGGLE
            if (window.currentUser) {
                const toggleHtml = `
                    <div class="flex items-center justify-end mb-4 bg-blue-50 p-3 rounded-lg border border-blue-100">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="autofillToggle" class="sr-only peer" onchange="toggleAutofill(this.checked)">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#e94f1b]"></div>
                            <span class="ms-3 text-sm font-medium text-gray-900"> <i class="fas fa-user-check me-1"></i> Remplir avec mes informations (Passager 1)</span>
                        </label>
                    </div>
                `;
                formArea.insertAdjacentHTML('beforeend', toggleHtml);
                // AJOUTER CECI POUR ACTIVER LE PASSAGE A L'ETAPE 3
              document.getElementById('step2').classList.add('hidden');
    // On cache AUSSI l'étape Retour (C'est ça qui manque)
    document.getElementById('step2_5').classList.add('hidden');
    
    // On affiche l'étape 3
    document.getElementById('step3').classList.remove('hidden');
    document.getElementById('confirmReservationBtn').disabled = false;
    updateStepper(3);
            }

            sortedSeats.forEach((seat, index) => {
                // On met des valeurs vides par dÃ©faut, l'autofill se fera via le toggle
                let defaultNom = '';
                let defaultPrenom = '';
                let defaultTel = '';
                let defaultEmail = '';

                // On garde l'index pour savoir si c'est le "Passager principal"
                const isMainPassenger = index === 0;

                const passengerHtml = `
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 transition-all hover:shadow-md" id="passenger_card_${seat}">
                        <h4 class="font-bold text-[#e94f1b] mb-4 flex items-center gap-2">
                            <i class="fas fa-user"></i> Passager pour la place n°${seat}
                            ${isMainPassenger ? '<span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded ml-2">Principal</span>' : ''}
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                                <input type="text" name="passenger_${seat}_nom" required
                                    value="${defaultNom}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all"
                                    placeholder="Nom du passager">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                                <input type="text" name="passenger_${seat}_prenom" required
                                    value="${defaultPrenom}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all"
                                    placeholder="Prénom du passager">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                                <input type="tel" name="passenger_${seat}_telephone" required
                                    value="${defaultTel}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all"
                                    placeholder="Ex: 0700000000"  maxlength="10" minlength="10" pattern="[0-9]{10}"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10)">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="passenger_${seat}_email"
                                    value="${defaultEmail}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all"
                                    placeholder="email@exemple.com (optionnel)">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nom et prenom de la personne à contacter en cas d'urgence</label>
                                <input type="text" name="passenger_${seat}_nom_urgence" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all"
                                    placeholder="Nom de la personne à contacter">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tél d'urgence</label>
                                <input type="tel" name="passenger_${seat}_urgence" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all"
                                    placeholder="Ex: 0500000000"  maxlength="10" minlength="10" pattern="[0-9]{10}"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10)">
                            </div>
                        </div>
                    </div>
                `;
                formArea.insertAdjacentHTML('beforeend', passengerHtml);
            });
        }

        // ============================================
        // Retour INTELLIGENT depuis Informations Passagers
        // ============================================
        function backFromPassengerInfo() {
            // Si c'est un aller-retour et que l'utilisateur a sÃ©lectionnÃ© des places retour,
            // retourner Ã  step2_5 (sÃ©lection places retour)
            if (window.userChoseAllerRetour && selectedSeatsRetour.length > 0) {
                document.getElementById('step3').classList.add('hidden');
                document.getElementById('step2_5').classList.remove('hidden');
            } else {
                // Sinon, retourner Ã  step2 (sÃ©lection places aller)
                document.getElementById('step3').classList.add('hidden');
                document.getElementById('step2').classList.remove('hidden');
            }
        }

        // ============================================
        // FONCTION 10.3: Autofill Passager 1
        // ============================================
        function toggleAutofill(isChecked) {
            if (!window.currentUser || selectedSeats.length === 0) return;
            
            // Le premier siÃ¨ge sÃ©lectionnÃ© (triÃ©)
            const sortedSeats = [...selectedSeats].sort((a, b) => a - b);
            const firstSeat = sortedSeats[0];
            
            const user = window.currentUser;
            const fields = [
                { name: 'nom', value: user.name || '' },
                { name: 'prenom', value: user.prenom || '' },
                { name: 'telephone', value: user.contact || '' },
                { name: 'email', value: user.email || '' }
            ];
            
            // Si l'utilisateur a un contact d'urgence
            if (user.contact_urgence) {
                fields.push({ name: 'urgence', value: user.contact_urgence });
            }
            if (user.nom_urgence) {
                fields.push({ name: 'nom_urgence', value: user.nom_urgence });
            }

            fields.forEach(field => {
                const input = document.querySelector(`[name="passenger_${firstSeat}_${field.name}"]`);
                if (input) {
                    if (isChecked) {
                        input.value = field.value;
                        input.classList.add('bg-orange-50', 'border-orange-300');
                        setTimeout(() => input.classList.remove('bg-orange-50', 'border-orange-300'), 500);
                    } else {
                        input.value = '';
                    }
                }
            });
            
            // Feedback visuel sur la carte
            const card = document.getElementById(`passenger_card_${firstSeat}`);
            if(card) {
                if(isChecked) card.classList.add('ring-2', 'ring-[#e94f1b]', 'ring-offset-2');
                else card.classList.remove('ring-2', 'ring-[#e94f1b]', 'ring-offset-2');
            }
        }

        // ============================================
        // FONCTION 11: Confirmer la rÃ©servation
        // ============================================
        async function confirmReservation() {
            const passengers = [];
            const sortedSeats = [...selectedSeats].sort((a, b) => a - b);
            let isValid = true;

            // Validation des champs passagers
            sortedSeats.forEach(seat => {
                const nomEl = document.querySelector(`[name="passenger_${seat}_nom"]`);
                const prenomEl = document.querySelector(`[name="passenger_${seat}_prenom"]`);
                const telephoneEl = document.querySelector(`[name="passenger_${seat}_telephone"]`);
                const emailEl = document.querySelector(`[name="passenger_${seat}_email"]`);
                const urgenceEl = document.querySelector(`[name="passenger_${seat}_urgence"]`);
                const nomUrgenceEl = document.querySelector(`[name="passenger_${seat}_nom_urgence"]`);

                const nom = nomEl ? nomEl.value.trim() : '';
                const prenom = prenomEl ? prenomEl.value.trim() : '';
                const telephone = telephoneEl ? telephoneEl.value.trim() : '';
                const email = emailEl ? emailEl.value.trim() : '';
                const urgence = urgenceEl ? urgenceEl.value.trim() : '';
                const nom_urgence = nomUrgenceEl ? nomUrgenceEl.value.trim() : '';

                if (!nom || !prenom || !telephone || !urgence || !nom_urgence) {
                    isValid = false;
                }

                passengers.push({
                    seat_number: seat,
                    nom, prenom, telephone, email, urgence, nom_urgence
                });
            });

            if (!isValid) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Informations manquantes',
                    text: 'Veuillez remplir toutes les informations pour chaque passager.',
                    confirmButtonColor: '#e94f1b'
                });
                return;
            }

            let dateVoyageFinal = window.outboundDate || window.currentReservationDate;

            if (!dateVoyageFinal && document.getElementById('reservationProgramInfo')) {
                const text = document.getElementById('reservationProgramInfo').innerText;
                const dateMatch = text.match(/\d{2}\/\d{2}\/\d{4}/);
                if (dateMatch) {
                    const [day, month, year] = dateMatch[0].split('/');
                    dateVoyageFinal = `${year}-${month}-${day}`;
                }
            }

            if (!dateVoyageFinal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Impossible de déterminer la date du voyage.',
                    confirmButtonColor: '#e94f1b'
                });
                return;
            }

            let prixUnitaireCalc = 0;
            if (currentSelectedProgram && currentSelectedProgram.montant_billet) {
                prixUnitaireCalc = parseInt(currentSelectedProgram.montant_billet);
            } else if (window.currentProgramPrice) {
                prixUnitaireCalc = window.currentProgramPrice;
                if(window.userChoseAllerRetour) {
                    prixUnitaireCalc = prixUnitaireCalc / 2;
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Erreur technique: Prix introuvable. Veuillez rafraîchir la page.'
                });
                return;
            }

            const multiplier = window.userChoseAllerRetour ? 2 : 1;
            const baseTotal = prixUnitaireCalc * parseInt(selectedNumberOfPlaces) * multiplier;
            const extraCostAller = window.seatSelectionExtraCost || 0;
            const extraCostRetour = window.seatSelectionExtraCostRetour || 0;
            const totalExtraCost = extraCostAller + extraCostRetour;
            const montantTotal = baseTotal + totalExtraCost;
            const commission = Math.round(montantTotal * 0.02);
            const totalWithCommission = montantTotal + commission;
            
            const userSolde = {{ auth()->check() ? (auth()->user()->solde ?? 0) : 0 }};
            let paymentMethod = 'cinetpay';

            const choiceResult = await Swal.fire({
                title: 'Mode de paiement',
                html: `
                    <div class="flex flex-col gap-4 text-center">
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 shadow-sm">
                            <p class="text-gray-500 text-[10px] uppercase tracking-wider font-extrabold mb-3">Résumé de la commande</p>
                            
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-gray-600 text-xs font-semibold">Total Réservation</span>
                                <span class="font-bold text-gray-800">${new Intl.NumberFormat('fr-FR').format(montantTotal)} FCFA</span>
                            </div>
                            
                            <div class="border-t border-dashed border-gray-200 my-2"></div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-gray-800 font-bold text-sm">Prix (Via Mon CarPay)</span>
                                <span class="text-xl font-black text-[#e94f1b]">${new Intl.NumberFormat('fr-FR').format(montantTotal)} FCFA</span>
                            </div>
                            <div class="text-[10px] text-gray-500 text-right mt-1">Votre solde: <span class="font-bold text-gray-700">${new Intl.NumberFormat('fr-FR').format(userSolde)} FCFA</span></div>
                        </div>

                        <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 text-left relative overflow-hidden">
                            <p class="text-blue-800 text-xs font-bold mb-3 flex items-center gap-1 uppercase tracking-tight">
                                <i class="fas fa-hand-holding-usd"></i> Frais de service
                            </p>
                            
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-blue-600 text-[11px] font-medium">Frais de service</span>
                                <span class="text-blue-800 font-bold text-xs">${new Intl.NumberFormat('fr-FR').format(commission)} FCFA</span>
                            </div>

                            <div class="flex justify-between items-center pt-2 border-t border-blue-200/50">
                                <span class="text-gray-700 text-sm font-black uppercase">Total à payer :</span>
                                <span class="text-xl font-black text-blue-700">${new Intl.NumberFormat('fr-FR').format(totalWithCommission)} FCFA</span>
                            </div>
                        </div>
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: `<i class="fas fa-wallet mr-2"></i>Payer via Mon CarPay`,
                denyButtonText: `<i class="fas fa-mobile-alt mr-2"></i>Payer par Wave`,
                confirmButtonColor: '#e94f1b',
                denyButtonColor: '#3b82f6',
                cancelButtonText: 'Annuler',
                customClass: {
                    confirmButton: 'mb-2 w-full sm:w-auto',
                    denyButton: 'mb-2 w-full sm:w-auto',
                    cancelButton: 'w-full sm:w-auto'
                },
                didOpen: () => {
                    if (userSolde < montantTotal) {
                        const confirmBtn = Swal.getConfirmButton();
                        confirmBtn.disabled = true;
                        confirmBtn.style.opacity = 0.5;
                        confirmBtn.classList.add('cursor-not-allowed');
                        confirmBtn.title = 'Solde insuffisant';
                    }
                }
            });

            if (choiceResult.isConfirmed) paymentMethod = 'wallet';
            else if (choiceResult.isDenied) paymentMethod = 'cinetpay';
            else return;

            Swal.fire({
                title: 'Traitement...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const response = await fetch("{{ route('reservation.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        programme_id: currentProgramId,
                        seats: sortedSeats,
                        seats_retour: selectedSeatsRetour.length > 0 ? selectedSeatsRetour.sort((a, b) => a - b) : [],
                        nombre_places: selectedNumberOfPlaces,
                        date_voyage: dateVoyageFinal,
                        is_aller_retour: window.userChoseAllerRetour,
                        date_retour: window.selectedReturnDate,
                        passagers: passengers,
                        payment_method: paymentMethod,
                        frais_choix_siege: totalExtraCost,
                        gare_depart_id: window.selectedGareDepartId || null,
                        gare_arrivee_id: window.selectedGareArriveeId || null,
                        heure_depart: window.selectedDepartureTime || null
                    })
                });

                const data = await response.json();

                if (data.success) {
                    if (data.wallet_payment) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Réservation confirmée !',
                            text: 'Votre réservation a été effectuée avec succès.',
                            showConfirmButton: false,
                            timer: 2000,
                            willClose: () => {
                                window.location.href = data.redirect_url;
                            }
                        });
                    } else if (data.payment_url && data.checkout_url) {
                        window.location.href = data.checkout_url;
                    }
                } else {
                    throw new Error(data.message || 'Erreur');
                }
            } catch (error) {
                if (error.message && error.message.toLowerCase().includes('csrf')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Session expirée',
                        text: 'Votre session a expiré. Veuillez rafraîchir la page (F5) et réessayer.',
                        confirmButtonColor: '#e94f1b'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: error.message
                    });
                }
            }
        }

        // ============================================
        // FONCTION 12: Initialisation au chargement
        // ============================================
        document.addEventListener('DOMContentLoaded', function () {
            // Gestion du click sur tous les boutons dÃ©tails vÃ©hicule
            document.querySelectorAll('.vehicle-details-btn').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const vehicleId = this.getAttribute('data-vehicle-id');
                    if (vehicleId) {
                        showVehicleDetails(parseInt(vehicleId));
                    }
                });
            });

            // EmpÃªcher la fermeture du modal en cliquant Ã  l'extÃ©rieur
            const modal = document.getElementById('reservationModal');
            if (modal) {
                modal.addEventListener('click', function (e) {
                    if (e.target === this) {
                        closeReservationModal();
                    }
                });
            }

            // Touche Ã‰chap pour fermer le modal
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                    closeReservationModal();
                }
            });
        });

       // ============================================
        // MODAL STEP-BY-STEP NAVIGATION STATE
        // ============================================
        let modalCurrentStep = 1;
        let selectedRoute = null;
        let selectedDate = null;

        window.toggleProgramsList = function() {
            const container = document.getElementById('inlineProgramsList');
            if (container.classList.contains('hidden')) {
                container.classList.remove('hidden');
                loadRoutesForSelection();
                // Scroll to container
                container.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                container.classList.add('hidden');
            }
        };


        async function loadRoutesForSelection() {
            const container = document.getElementById('programsListContent');
            const title = document.getElementById('inlineListTitle');
            const subtitle = document.getElementById('inlineListSubtitle');
            
            // Mettre Ã  jour le titre
            if(title) title.textContent = 'Choisir votre ligne';
            if(subtitle) subtitle.textContent = 'Sélectionnez votre trajet pour voir les disponibilités';
            
            container.innerHTML = `<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-4xl text-[#e94f1b]"></i><p class="mt-2 text-gray-500">Chargement des lignes...</p></div>`;

            try {
                const response = await fetch('{{ route("api.grouped-routes") }}');
                const data = await response.json();
                
                if (data.success && data.routes.length > 0) {
                    renderRoutesForSelection(data.routes);
                } else {
                    container.innerHTML = `<div class="text-center py-8"><i class="fas fa-bus text-gray-300 text-4xl"></i><p class="mt-2 text-gray-500">Aucune ligne disponible pour le moment.</p></div>`;
                }
            } catch (error) {
                console.error('Erreur:', error);
                container.innerHTML = `<div class="text-center py-8 text-red-500"><i class="fas fa-exclamation-triangle text-4xl mb-2"></i><p>Impossible de charger les lignes.</p></div>`;
            }
        }

        function renderRoutesForSelection(routes) {
            const container = document.getElementById('programsListContent');
            container.innerHTML = `<div class="grid grid-cols-1 md:grid-cols-2 gap-4">` + routes.map(route => {
                const prixDisplay = route.prix_min === route.prix_max 
                    ? `${Number(route.prix_min).toLocaleString('fr-FR')} FCFA`
                    : `Ã€ partir de ${Number(route.prix_min).toLocaleString('fr-FR')} FCFA`;
                
                return `
                    <div onclick="selectRouteAndLaunchFlow(${JSON.stringify(route).replace(/"/g, '&quot;')})" 
                         class="bg-gradient-to-br from-blue-50 to-white p-5 rounded-xl border-2 border-blue-100 hover:border-[#e94f1b] cursor-pointer transition-all duration-200 hover:shadow-lg group">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-10 h-10 rounded-full bg-[#e94f1b]/10 flex items-center justify-center">
                                    <i class="fas fa-bus text-[#e94f1b]"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-600">${route.compagnie?.name || 'Compagnie'}</span>
                            </div>
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-bold">
                                ${route.total_voyages} voyage(s)
                            </span>
                        </div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex-1">
                                <p class="font-bold text-gray-900">${route.point_depart}</p>
                            </div>
                            <div class="flex flex-col items-center">
                                <i class="fas fa-arrow-right text-[#e94f1b]"></i>
                            </div>
                            <div class="flex-1 text-right">
                                <p class="font-bold text-gray-900">${route.point_arrive}</p>
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                            <span class="text-[#e94f1b] font-bold">${prixDisplay}</span>
                            <span class="text-gray-400 group-hover:text-[#e94f1b] transition-colors">
                                <i class="fas fa-arrow-circle-right text-xl"></i>
                            </span>
                        </div>
                    </div>
                `;
            }).join('') + `</div>`;
        }

        // Nouvelle fonction: dès qu'on sélectionne une ligne, demander la date de départ
        function selectRouteAndLaunchFlow(route) {
            // Cacher la liste inline pour faire place à la suite ou la laisser ? 
            // UX: On peut la laisser visible ou la cacher. Cachons-la pour focus.
            // document.getElementById('inlineProgramsList').classList.add('hidden');
            
            // Afficher sélecteur de date de départ
            showDepartureDateSelection(route);
        }

        // Générateur de calendrier mensuel
        function generateMonthlyCalendar(currentMonth, currentYear, minDate, maxDate, colorScheme = 'orange') {
            const firstDay = new Date(currentYear, currentMonth, 1);
            const lastDay = new Date(currentYear, currentMonth + 1, 0);
            const startDay = firstDay.getDay(); // 0 = dimanche
            const daysInMonth = lastDay.getDate();
            
            // Ajuster pour que lundi soit le premier jour (0 = lundi, 6 = dimanche)
            const startDayAdjusted = startDay === 0 ? 6 : startDay - 1;
            
            const colors = {
                orange: {
                    border: 'border-orange-200',
                    bg: 'bg-orange-50',
                    hoverBorder: 'hover:border-orange-500',
                    hoverBg: 'hover:bg-orange-100',
                    text: 'text-orange-700',
                    disabled: 'bg-gray-100 text-gray-400 cursor-not-allowed'
                },
                purple: {
                    border: 'border-purple-200',
                    bg: 'bg-purple-50',
                    hoverBorder: 'hover:border-purple-500',
                    hoverBg: 'hover:bg-purple-100',
                    text: 'text-purple-700',
                    disabled: 'bg-gray-100 text-gray-400 cursor-not-allowed'
                }
            };
            
            const scheme = colors[colorScheme];
            
            let html = '<div class="calendar-grid">';
            
            // En-tête avec jours de la semaine
            html += '<div class="grid grid-cols-7 gap-1 mb-2">';
            const dayNames = ['Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa', 'Di'];
            dayNames.forEach(day => {
                html += `<div class="text-center text-xs font-semibold text-gray-600 py-1">${day}</div>`;
            });
            html += '</div>';
            
            // Grille de dates
            html += '<div class="grid grid-cols-7 gap-1">';
            
            // Cases vides avant le premier jour
            for (let i = 0; i < startDayAdjusted; i++) {
                html += '<div></div>';
            }
            
            // Jours du mois
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(currentYear, currentMonth, day);
                const dateStr = date.toISOString().split('T')[0];
                const isDisabled = date < minDate || date > maxDate;
                
                if (isDisabled) {
                    html += `
                        <div class="border ${scheme.disabled} rounded p-2 text-center">
                            <span class="text-sm">${day}</span>
                        </div>
                    `;
                } else {
                    html += `
                        <div class="calendar-day-btn border-2 ${scheme.border} ${scheme.bg} rounded p-2 cursor-pointer ${scheme.hoverBorder} ${scheme.hoverBg} transition-all text-center"
                             data-date="${dateStr}">
                            <span class="font-bold ${scheme.text} text-sm">${day}</span>
                        </div>
                    `;
                }
            }
            
            html += '</div></div>';
            return html;
        }

        // SÃ©lection de la date de dÃ©part pour "Voir tous les voyages" avec calendrier mensuel
        function showDepartureDateSelection(route) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            // Calculer demain pour le début des réservations possibles
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            
            // Date max = date_fin du programme (31/12/2026 par dÃ©faut ou depuis route)
            const maxDate = route.date_fin ? new Date(route.date_fin) : new Date(today.getFullYear(), 11, 31);
            
            let currentMonth = tomorrow.getMonth();
            let currentYear = tomorrow.getFullYear();
            
            function updateCalendar() {
                const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                                   'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                
                // Utiliser tomorrow comme minDate
                const calendarHtml = generateMonthlyCalendar(currentMonth, currentYear, tomorrow, maxDate, 'orange');
                
                Swal.update({
                    html: `
                        <div class="text-left space-y-4">
                            <div class="bg-orange-50 p-3 rounded-lg border border-orange-200">
                                <p class="font-bold text-gray-800">${route.point_depart} → ${route.point_arrive}</p>
                                <p class="text-sm text-gray-600">Sélectionnez votre date de départ</p>
                            </div>
                            
                            <!-- Option rapide Demain -->
                            <div class="flex justify-center">
                                <button id="btnSelectTomorrow" class="flex items-center gap-2 bg-orange-100 text-orange-700 px-4 py-2 rounded-lg font-bold hover:bg-orange-200 transition-colors border border-orange-300">
                                    <i class="fas fa-magic"></i>
                                    <span>Sélectionner demain (${tomorrow.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' })})</span>
                                </button>
                            </div>

                            <!-- Navigation mois -->
                            <div class="flex items-center justify-between bg-gray-50 p-2 rounded">
                                <button id="prevMonth" class="px-3 py-1 bg-white border rounded hover:bg-gray-100">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <span class="font-bold text-gray-800">${monthNames[currentMonth]} ${currentYear}</span>
                                <button id="nextMonth" class="px-3 py-1 bg-white border rounded hover:bg-gray-100">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            
                            <!-- Calendrier -->
                            <div class="calendar-container">
                                ${calendarHtml}
                            </div>
                        </div>
                    `
                });
                
                // RÃ©attacher les Ã©vÃ©nements
                attachCalendarEvents();
            }
            
            function attachCalendarEvents() {
                // Bouton Demain
                const btnTomorrow = document.getElementById('btnSelectTomorrow');
                if (btnTomorrow) {
                    btnTomorrow.addEventListener('click', () => {
                        const tomorrowStr = tomorrow.toISOString().split('T')[0];
                        Swal.close();
                        loadSchedulesAndLaunchFlow(route, tomorrowStr);
                    });
                }
                // Navigation mois précédent
                const prevBtn = document.getElementById('prevMonth');
                if (prevBtn) {
                    prevBtn.addEventListener('click', () => {
                        if (currentMonth === 0) {
                            currentMonth = 11;
                            currentYear--;
                        } else {
                            currentMonth--;
                        }
                        // Ne pas aller avant le mois actuel (de 'tomorrow')
                        const checkDate = new Date(currentYear, currentMonth, 1);
                        // comparer avec tomorrow
                        if (checkDate >= new Date(tomorrow.getFullYear(), tomorrow.getMonth(), 1)) {
                            updateCalendar();
                        } else {
                            currentMonth = tomorrow.getMonth();
                            currentYear = tomorrow.getFullYear();
                        }
                    });
                }
                
                // Navigation mois suivant
                const nextBtn = document.getElementById('nextMonth');
                if (nextBtn) {
                    nextBtn.addEventListener('click', () => {
                        if (currentMonth === 11) {
                            currentMonth = 0;
                            currentYear++;
                        } else {
                            currentMonth++;
                        }
                        // Ne pas aller après le mois de maxDate
                        const checkDate = new Date(currentYear, currentMonth, 1);
                        if (checkDate <= new Date(maxDate.getFullYear(), maxDate.getMonth(), 1)) {
                            updateCalendar();
                        } else {
                            currentMonth = maxDate.getMonth();
                            currentYear = maxDate.getFullYear();
                        }
                    });
                }
                
                // SÃ©lection de date
                const dayBtns = document.querySelectorAll('.calendar-day-btn');
                dayBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const selectedDate = this.dataset.date;
                        Swal.close();
                        loadSchedulesAndLaunchFlow(route, selectedDate);
                    });
                });
            }
            
            // Ouvrir le modal initial
            Swal.fire({
                title: '<i class="fas fa-calendar-day text-orange-600"></i> Date de départ',
                html: '', // Sera rempli par updateCalendar()
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonText: 'Retour',
                customClass: { popup: 'rounded-2xl' },
                width: '600px',
                didOpen: () => {
                    updateCalendar();
                }
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    document.getElementById('programsListModal').classList.remove('hidden');
                }
            });
        }

        // Charger les horaires et lancer le flux unifiÃ©
        async function loadSchedulesAndLaunchFlow(route, selectedDate) {
              // --- AJOUT IMPORTANT ---
    window.selectedDepartureDate = selectedDate; // Sauvegarder la date choisie explicitement
    window.outboundDate = selectedDate;
   
            // Afficher un loader
            Swal.fire({
                title: 'Chargement des disponibilités...',
                text: `${route.point_depart} → ${route.point_arrive}`,
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            
            try {
                // 1. Charger les horaires ALLER pour la date sélectionnée
                const paramsAller = new URLSearchParams({
                    point_depart: route.point_depart,
                    point_arrive: route.point_arrive,
                    date: selectedDate,
                    compagnie_id: route.compagnie_id // CRUCIAL : Filtrer par compagnie
                });
                const responseAller = await fetch('{{ route("api.route-schedules") }}?' + paramsAller);
                const dataAller = await responseAller.json();
                
                // 2. Charger les horaires RETOUR pour vérifier la disponibilité
                const paramsRetour = new URLSearchParams({
                    original_arrive: route.point_arrive,
                    original_depart: route.point_depart,
                    min_date: selectedDate
                });
                const responseRetour = await fetch('{{ route("api.return-trips") }}?' + paramsRetour);
                const dataRetour = await responseRetour.json();
                
                Swal.close();
                
                // 3. Construire l'objet routeData pour le modal unifiÃ©
                const routeData = {
                    ...route, // Inclut date_fin si présent dans route
                    aller_horaires: dataAller.success ? dataAller.schedules : [],
                    has_retour: (dataRetour.success && dataRetour.return_trips && dataRetour.return_trips.length > 0),
                    retour_horaires: (dataRetour.success ? dataRetour.return_trips : []),
                    compagnie: route.compagnie?.name || 'Compagnie',
                    montant_billet: dataAller.schedules && dataAller.schedules.length > 0 ? dataAller.schedules[0].montant_billet : (route.prix_min || 0),
                    date_fin: route.date_fin || dataAller.schedules?.[0]?.date_fin || null // S'assurer que date_fin est présent
                };
                
                // 4. Lancer la sélection de l'heure (Départ)
                if (typeof window.showRouteDepartureTimes === 'function') {
                    window.showRouteDepartureTimes(routeData, selectedDate, window.userWantsAllerRetour);
                } else {
                    console.error('showRouteDepartureTimes non disponible');
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur technique',
                        text: 'Impossible de charger le système de réservation.'
                    });
                }
                
            } catch (error) {
                console.error('Erreur lors du chargement:', error);
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Impossible de charger les horaires. Veuillez réessayer.'
                }).then(() => {
                    // Retour Ã  la sÃ©lection de date
                    showDepartureDateSelection(route);
                });
            }
        }

        async function loadSchedules(route, date) {
            const container = document.getElementById('programsListContent');
            container.innerHTML = `<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-4xl text-[#e94f1b]"></i><p class="mt-2 text-gray-500">Chargement des horaires...</p></div>`;

            try {
                const params = new URLSearchParams({
                    point_depart: route.point_depart,
                    point_arrive: route.point_arrive,
                    date: date
                });
                const response = await fetch('{{ route("api.route-schedules") }}?' + params);
                const data = await response.json();
                
                if (data.success && data.schedules.length > 0) {
                    renderSchedulesList(data.schedules);
                } else {
                    container.innerHTML = `<div class="text-center py-8"><i class="fas fa-clock text-gray-300 text-4xl"></i><p class="mt-2 text-gray-500">Aucun horaire disponible.</p></div>`;
                }
            } catch (error) {
                console.error('Erreur:', error);
                container.innerHTML = `<div class="text-center py-8 text-red-500"><i class="fas fa-exclamation-triangle text-4xl mb-2"></i><p>Impossible de charger les horaires.</p></div>`;
            }
        }

        function renderSchedulesList(schedules) {
            const container = document.getElementById('programsListContent');
            container.innerHTML = `<div class="space-y-3">` + schedules.map(prog => {
                return `
                    <div class="bg-white p-4 rounded-xl border border-gray-200 hover:border-[#e94f1b] transition-all duration-200 hover:shadow-md">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-[#e94f1b]">${prog.heure_depart}</p>
                                    <p class="text-xs text-gray-500">Départ</p>
                                </div>
                                <div class="flex items-center gap-2 text-gray-400">
                                    <div class="w-8 h-0.5 bg-gray-300"></div>
                                    <i class="fas fa-bus text-sm"></i>
                                    <div class="w-8 h-0.5 bg-gray-300"></div>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-gray-700">${prog.heure_arrive}</p>
                                    <p class="text-xs text-gray-500">Arrivée</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <p class="text-lg font-bold text-[#e94f1b]">${Number(prog.montant_billet).toLocaleString('fr-FR')} FCFA</p>
                                    <p class="text-xs text-gray-500">${prog.compagnie?.name || ''}</p>
                                </div>
                                <button onclick="toggleProgramsList(); initiateReservationProcess(${prog.id}, '${selectedDate}')" 
                                        class="bg-[#e94f1b] text-white px-5 py-2.5 rounded-lg font-bold hover:bg-orange-600 transition-colors flex items-center gap-2">
                                    <i class="fas fa-ticket-alt"></i>
                                    <span>Réserver</span>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('') + `</div>`;
        }

       function selectProgramFromList(program) {
            // Utiliser la date de recherche si disponible
            const searchDate = new URLSearchParams(window.location.search).get('date_depart');
            initiateReservationProcess(program.id, searchDate);
        }


        // ============================================
        // FONCTION 14: Gestion modale sélection date
        // ============================================
         function openDateSelectionModal(program) {
            currentSelectedProgram = program;
            document.getElementById('dateSelectionModal').classList.remove('hidden');
            // Logique de remplissage date similaire Ã  populateReturnDateSelect mais pour l'aller...
            const select = document.getElementById('recurrenceDateSelect');
            select.innerHTML = '<option value="">Chargement...</option>';
            
            // Jours Aller
            let allowedDays = [];
            try { allowedDays = JSON.parse(program.jours_recurrence || '[]'); } catch(e){}
            allowedDays = allowedDays.map(d => d.toLowerCase());
            
            const daysMap = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
            const dates = [];
            let current = new Date();
             current.setDate(current.getDate() + 1); // Commencer à demain
            let count = 0;
            
            while(count < 10 && dates.length < 10) {
                 const dayName = daysMap[current.getDay()];
                 if (allowedDays.includes(dayName)) {
                     // Check date fin
                     let isValid = true;
                     if(program.date_fin_programmation && current.toISOString().split('T')[0] > program.date_fin_programmation) isValid = false;
                     if(isValid) {
                         dates.push({
                            val: current.toISOString().split('T')[0],
                            txt: current.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
                        });
                        count++;
                     }
                 }
                 current.setDate(current.getDate() + 1);
                 if(count > 100) break; // Sécurité
            }
            
            select.innerHTML = '<option value="">Choisir une date...</option>';
            dates.forEach(d => select.innerHTML += `<option value="${d.val}">${d.txt}</option>`);
        }
       function closeDateSelectionModal() { document.getElementById('dateSelectionModal').classList.add('hidden'); }
        function confirmDateSelection() {
            const date = document.getElementById('recurrenceDateSelect').value;
            if(!date) {
                document.getElementById('recurrenceDateError').classList.remove('hidden');
                return;
            }
            document.getElementById('dateSelectionModal').classList.add('hidden');
            
            // Si on avait pas encore choisi A/R et qu'il est dispo, on le propose maintenant
            if(currentSelectedProgram.is_aller_retour && window.userChoseAllerRetour === false) {
                 openAllerRetourConfirmModal(currentSelectedProgram, date);
            } else {
                 openReservationModal(currentSelectedProgram.id, date);
            }
        }

        // Fonction pour afficher les dÃ©tails (places disponibles)
         // Fonction pour afficher les détails (places disponibles)
        async function openDetailsModal(btn) {
            const route = JSON.parse(btn.dataset.route);
            const dateDepart = btn.dataset.date;
            
            Swal.fire({
                title: 'Chargement des détails...',
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                // Fetch Aller availability
                const paramsAller = new URLSearchParams({
                    point_depart: route.point_depart,
                    point_arrive: route.point_arrive,
                    date: dateDepart
                });
                const responseAller = await fetch('{{ route("api.route-schedules") }}?' + paramsAller);
                const dataAller = await responseAller.json();

                // Build HTML
                let html = `<div class="text-left">`;
                
                // Section Aller
                html += `<h3 class="font-bold text-lg text-[#e94f1b] mb-3 uppercase border-b pb-2">Aller : ${dateDepart}</h3>`;
                if (dataAller.success && dataAller.schedules.length > 0) {
                    html += buildSchedulesTable(dataAller.schedules, dateDepart);
                } else {
                    html += `<p class="text-gray-500 italic">Aucun horaire disponible pour cette date.</p>`;
                }

                // Section Retour (si applicable)
                if (route.has_retour) {
                    html += `<h3 class="font-bold text-lg text-blue-600 mt-6 mb-3 uppercase border-b pb-2">Retour (Aperçu)</h3>`;
                    
                    const paramsRetour = new URLSearchParams({
                        point_depart: route.point_arrive,
                        point_arrive: route.point_depart,
                        date: dateDepart
                    });
                    const responseRetour = await fetch('{{ route("api.route-schedules") }}?' + paramsRetour);
                    const dataRetour = await responseRetour.json();
                    
                    if (dataRetour.success && dataRetour.schedules.length > 0) {
                        html += buildSchedulesTable(dataRetour.schedules, dateDepart);
                    } else {
                         html += `<p class="text-gray-500 italic">Aucun horaire retour trouvé pour cette date.</p>`;
                    }
                }

                html += `</div>`;

                Swal.fire({
                    title: `Détails du voyage`,
                    html: html,
                    width: '800px',
                    showConfirmButton: false,
                    showCloseButton: true,
                    customClass: {
                        popup: 'rounded-xl'
                    }
                });

            } catch (error) {
                console.error(error);
                Swal.fire('Erreur', 'Impossible de charger les dÃ©tails.', 'error');
            }
        }

        function buildSchedulesTable(schedules, dateVoyage) {
            let table = `
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-4 py-2">Départ</th>
                                <th class="px-4 py-2">Arrivée</th>
                                <th class="px-4 py-2">Véhicule</th>
                                <th class="px-4 py-2 text-center">Places</th>
                                <th class="px-4 py-2 text-center">Statut</th>
                                <th class="px-4 py-2 text-center">Voir</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            schedules.forEach(sch => {
                const isFull = sch.places_disponibles <= 0;
                const statusClass = isFull ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';
                const statusText = isFull ? 'Complet' : 'Disponible';
                
                table += `
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-4 py-2 font-bold text-gray-900">${sch.heure_depart}</td>
                        <td class="px-4 py-2">${sch.heure_arrive}</td>
                        <td class="px-4 py-2">${sch.vehicule}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="font-bold text-gray-900">${sch.places_disponibles}</span> / ${sch.places_totales}
                        </td>
                        <td class="px-4 py-2 text-center">
                            <span class="${statusClass} px-2 py-1 rounded-full text-xs font-bold">${statusText}</span>
                        </td>
                        <td class="px-4 py-2 text-center">
                             <button type="button" onclick="showVehicleDetails('${sch.vehicule_id}', '${sch.id}', '${dateVoyage}')" class="text-blue-600 hover:text-blue-800 bg-blue-100 p-2 rounded-full transition-colors">
                                <i class="fas fa-eye"></i>
                             </button>
                        </td>
                    </tr>
                `;
            });
            
            table += `</tbody></table></div>`;
            return table;
        }
    </script>


 <!-- Modal Confirmation Aller-Retour -->
    <div id="allerRetourConfirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[75] flex items-center justify-center">
        <div class="relative w-[450px] mx-auto p-6 border shadow-2xl rounded-2xl bg-white">
            <div class="flex flex-col gap-4">
                <!-- En-tête -->
                <div class="text-center border-b pb-4">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fas fa-bus text-[#e94f1b] text-2xl"></i></div>
                    <h3 class="text-xl font-bold text-gray-900">Ce programme propose un aller-retour</h3>
                    <p class="text-sm text-gray-500 mt-1">Choisissez le type de voyage que vous souhaitez</p>
                </div>
                
                <!-- Infos du trajet -->
                <div id="allerRetourTripInfo" class="py-2">
                    <!-- Contenu injecté par JS -->
                </div>
                
                <!-- Choix du type de voyage -->
                <div class="py-2">
                    <label for="allerRetourChoice" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-route me-1"></i> Type de voyage
                    </label>
                    <div class="relative">
                        <select id="allerRetourChoice" onchange="onAllerRetourChoiceChange()" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-[#e94f1b] appearance-none bg-white font-medium text-gray-700">
                            <option value="aller_simple">🚌 Aller Simple</option>
                            <option value="aller_retour">🔄 Aller-Retour</option>
                        </select>
                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Affichage du prix dynamique -->
                <div id="allerRetourPriceDisplay" class="text-center">
                    <!-- Contenu injecté par JS -->
                </div>
                
                <!-- Sélection date retour (pour récurrents + aller-retour) -->
                <div id="returnDateSection" class="hidden py-2 border-t">
                    <label for="returnDateSelect" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-plane-arrival text-blue-500 me-1"></i> Date de retour
                    </label>
                    <div class="relative">
                        <select id="returnDateSelect" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 appearance-none bg-white"></select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t pt-4">
                    <button onclick="closeAllerRetourConfirmModal()" class="px-5 py-2 bg-gray-100 rounded-lg font-bold">Annuler</button>
                    <button onclick="confirmAllerRetour()" class="px-5 py-2 bg-[#e94f1b] text-white rounded-lg font-bold">Continuer</button>
                </div>
            </div>
        </div>
    </div>
     <!-- Modal Date Selection (RÃ©current) -->
    <div id="dateSelectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[70] flex items-center justify-center">
        <div class="relative w-96 mx-auto p-6 border shadow-2xl rounded-2xl bg-white">
            <div class="flex flex-col gap-4">
                <div class="border-b pb-4">
                    <h3 class="text-xl font-bold text-gray-900">Choisir une date de voyage</h3>
                    <p class="text-sm text-gray-500 mt-1">Ce programme est récurrent.</p>
                </div>
                
                <div class="py-4">
                    <label for="recurrenceDateSelect" class="block text-sm font-medium text-gray-700 mb-2">Sélectionnez une date parmi les prochains jours disponibles :</label>
                    <div class="relative">
                        <select id="recurrenceDateSelect" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent appearance-none bg-white">
                            <!-- Options générées par JS -->
                        </select>
                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                    <p id="recurrenceDateError" class="text-red-500 text-xs mt-1 hidden">Veuillez choisir une date.</p>
                </div>

                <div class="flex justify-end gap-3 border-t pt-4">
                    <button onclick="closeDateSelectionModal()" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition-colors">Annuler</button>
                    <button onclick="confirmDateSelection()" class="px-5 py-2.5 bg-[#e94f1b] text-white rounded-xl font-bold hover:bg-orange-600 transition-colors shadow-lg hover:shadow-xl">Confirmer</button>
                </div>
            </div>
        </div>
    </div>

<input type="hidden" id="selected_gare_depart_id" name="gare_depart_id">
<input type="hidden" id="selected_gare_arrivee_id" name="gare_arrivee_id">
@endsection

@push('scripts')
<script>
    // Variables globales pour stocker la gare sélectionnée
    // Ces variables seront utilisées lors de la confirmation de réservation
    window.selectedGareDepartId = null;
    window.selectedGareArriveeId = null;

    // Remplacement de la fonction handleReservationClick pour intégrer la sélection de gare
    window.handleReservationClick = function(button) {
        console.log("Bouton réserver cliqué - Flux avec Gare");
        
        const routeDataJson = button.getAttribute('data-route');
        const dateDepart = button.getAttribute('data-date');
        
        if (!routeDataJson) {
            console.error("Pas de données data-route trouvées");
            return;
        }

        try {
            const routeData = JSON.parse(routeDataJson);
            // Stocker les infos courantes dans window pour accès global
            window.currentRouteData = routeData; 
            window.currentRouteData.date_depart = dateDepart;

            // Réinitialiser la sélection de gare
            window.selectedGareDepartId = null;
            window.selectedGareArriveeId = null;

            // Vérifier s'il y a des gares configurées
            const gareDepart = routeData.gare_depart;
            
            // Si pas de gare ou gare vide -> Flux standard direct
            if (!gareDepart) {
                launchOriginalFlow(routeData, dateDepart);
                return;
            }

            // Sinon -> Afficher le modal de sélection de gare
            showGareSelectionModal();
            
        } catch (e) {
            console.error('Erreur JS lors du clic réservation:', e);
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur est survenue lors de l\'initialisation de la réservation.'
            });
        }
    };

    function showGareSelectionModal() {
        const routeData = window.currentRouteData;
        const gareDepart = routeData.gare_depart;
        const gareOptions = document.getElementById('gareOptions');
        const modal = document.getElementById('gareSelectionModal');
        
        if (!modal) {
            console.error("Modal de sélection de gare introuvable (#gareSelectionModal)");
            // Fallback flux normal
            launchOriginalFlow(routeData, routeData.date_depart);
            return;
        }
        
        if (gareOptions) gareOptions.innerHTML = '';
        
        // Cas 1: Une seule gare (Objet) ou Tableau de 1 élément
        let singleGare = null;
        if (gareDepart && !Array.isArray(gareDepart) && typeof gareDepart === 'object') {
            singleGare = gareDepart;
        } else if (Array.isArray(gareDepart) && gareDepart.length === 1) {
            singleGare = gareDepart[0];
        }

        if (singleGare) {
            console.log("Une seule gare détectée, sélection automatique:", singleGare);
            selectGareAndContinue(singleGare.id);
            return;
        }
        
        // Cas 2: Plusieurs gares (Tableau)
        if (Array.isArray(gareDepart) && gareDepart.length > 0) {
             gareDepart.forEach(gare => {
                 const btn = document.createElement('button');
                 btn.className = 'w-full p-4 border-2 border-gray-200 rounded-xl hover:border-[#e94f1b] hover:bg-orange-50 transition-all text-left mb-3 group';
                 btn.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center group-hover:bg-[#e94f1b] transition-colors">
                            <i class="fas fa-building text-gray-500 group-hover:text-white text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">${gare.nom_gare}</h4>
                            ${gare.adresse ? `<p class="text-sm text-gray-600">${gare.adresse}</p>` : ''}
                        </div>
                    </div>
                `;
                btn.onclick = () => selectGareAndContinue(gare.id);
                gareOptions.appendChild(btn);
             });
        } 
        // Cas 3: Objet unique (pour être sûr)
        else if (gareDepart && typeof gareDepart === 'object') {
             const btn = document.createElement('button');
             btn.className = 'w-full p-4 border-2 border-gray-200 rounded-xl hover:border-[#e94f1b] hover:bg-orange-50 transition-all text-left mb-3 group';
             btn.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center group-hover:bg-[#e94f1b] transition-colors">
                        <i class="fas fa-building text-gray-500 group-hover:text-white text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">${gareDepart.nom_gare}</h4>
                        ${gareDepart.adresse ? `<p class="text-sm text-gray-600">${gareDepart.adresse}</p>` : ''}
                    </div>
                </div>
            `;
            btn.onclick = () => selectGareAndContinue(gareDepart.id);
            gareOptions.appendChild(btn);
        }

        modal.classList.remove('hidden');
    }

    function closeGareSelectionModal() {
        const modal = document.getElementById('gareSelectionModal');
        if (modal) modal.classList.add('hidden');
    }

    function selectGareAndContinue(gareId) {
        window.selectedGareDepartId = gareId;
        // On suppose que la gare d'arrivée est unique ou déduite du trajet
        if (window.currentRouteData.gare_arrivee) {
            window.selectedGareArriveeId = window.currentRouteData.gare_arrivee.id;
        }
        
        closeGareSelectionModal();
        launchOriginalFlow(window.currentRouteData, window.currentRouteData.date_depart);
    }

    function launchOriginalFlow(routeData, dateDepart) {
        // Appeler la fonction SweetAlert existante pour choisir le type de voyage
        if (typeof window.showRouteTripTypeModal === 'function') {
            window.showRouteTripTypeModal(routeData, dateDepart);
        } else {
            console.error("La fonction window.showRouteTripTypeModal est introuvable. Code manquant ?");
            // Fallback
             Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Impossible de lancer le flux de réservation.'
            });
        }
    }

    // Auto-reservation logic
    // --- SMART AUTOCOMPLETE ---
    function setupLocalAutocomplete(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;

        const container = document.createElement('div');
        container.className = 'absolute left-0 right-0 z-[100] bg-white border border-gray-100 rounded-xl shadow-2xl mt-2 max-h-60 overflow-y-auto hidden';
        
        const parentDiv = input.parentElement;
        if (parentDiv) {
            if (!getComputedStyle(parentDiv).position || getComputedStyle(parentDiv).position === 'static') {
                parentDiv.style.position = 'relative';
            }
            parentDiv.appendChild(container);
        }

        let currentIndex = -1;

        function fetchLocations(query = '') {
            fetch(`/api/locations?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    renderSuggestions(data);
                });
        }

        function renderSuggestions(data) {
            container.innerHTML = '';
            currentIndex = -1;
            
            if (data.length > 0) {
                data.forEach((location, index) => {
                    const div = document.createElement('div');
                    div.className = 'suggestion-item px-4 py-3 hover:bg-orange-50 cursor-pointer text-gray-800 font-bold transition-all border-b border-gray-50 last:border-0 flex items-center justify-between group';
                    div.dataset.index = index;
                    div.innerHTML = `
                        <div class="flex items-center gap-3">
                            <i class="fas fa-map-marker-alt text-[#e94f1b] text-xs opacity-40 group-hover:opacity-100"></i>
                            <span class="text-sm">${location}</span>
                        </div>
                        <i class="fas fa-arrow-right text-[10px] text-gray-300 opacity-0 group-hover:opacity-100 transition-all"></i>
                    `;
                    div.addEventListener('click', () => {
                        input.value = location;
                        container.classList.add('hidden');
                        input.dispatchEvent(new Event('change'));
                        // Optional: trigger search button
                    });
                    container.appendChild(div);
                });
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }

        input.addEventListener('input', function() { fetchLocations(this.value); });
        input.addEventListener('focus', function() { fetchLocations(this.value); });

        input.addEventListener('keydown', function(e) {
            const items = container.querySelectorAll('.suggestion-item');
            if (container.classList.contains('hidden') || !items.length) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                currentIndex = (currentIndex + 1) % items.length;
                updateHighlight(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                currentIndex = (currentIndex - 1 + items.length) % items.length;
                updateHighlight(items);
            } else if (e.key === 'Enter' && currentIndex >= 0) {
                e.preventDefault();
                items[currentIndex].click();
            } else if (e.key === 'Escape') {
                container.classList.add('hidden');
            }
        });

        function updateHighlight(items) {
            items.forEach((item, index) => {
                if (index === currentIndex) {
                    item.classList.add('bg-orange-50');
                    item.scrollIntoView({ block: 'nearest' });
                } else {
                    item.classList.remove('bg-orange-50');
                }
            });
        }

        document.addEventListener('click', function(e) {
            if (!parentDiv.contains(e.target)) {
                container.classList.add('hidden');
            }
        });
    }

    // Initialize autocompletes
    document.addEventListener('DOMContentLoaded', function() {
        setupLocalAutocomplete("point_depart");
        setupLocalAutocomplete("point_arrive");

        // Auto-reservation logic
        const urlParams = new URLSearchParams(window.location.search);
        const autoReserveId = urlParams.get('auto_reserve');
        
        if (autoReserveId) {
            console.log("Auto-reserve detected for ID:", autoReserveId);
            // Wait a bit for the page to be fully interactive
            setTimeout(() => {
                const reserveButtons = document.querySelectorAll('button[onclick="handleReservationClick(this)"], button[onclick*="handleReservationClick"]');
                console.log("Found reserve buttons:", reserveButtons.length);
                
                for (let btn of reserveButtons) {
                    try {
                        const routeDataStr = btn.getAttribute('data-route');
                        if (!routeDataStr) continue;
                        
                        const routeData = JSON.parse(routeDataStr);
                        console.log("Checking routeData:", routeData.id);
                        
                        // Check if it's the main ID or one of the horaires
                        let match = (routeData.id == autoReserveId);
                        if (!match && routeData.aller_horaires) {
                            match = routeData.aller_horaires.some(h => h.id == autoReserveId);
                        }
                        
                        if (match) {
                            console.log("Match found! Triggering click.");
                            btn.click();
                            // Clean URL
                            const newUrl = window.location.pathname + window.location.search.replace(/&?auto_reserve=[^&]*/, '').replace(/\?$/, '');
                            window.history.replaceState({}, '', newUrl);
                            break;
                        }
                    } catch (e) {
                        console.error("Error parsing route data:", e);
                    }
                }
            }, 800);
        }
    });
    // --- END SMART AUTOCOMPLETE ---
    });
</script>
@endpush