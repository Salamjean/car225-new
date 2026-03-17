@php
    $isToday = \Carbon\Carbon::parse($searchDate)->isToday();
    $statusKey = $programme->getStatutPlacesForDate($searchDate);
    $totalSeats = $programme->getTotalSeats($searchDate);
    $reservedSeatsCount = $programme->getPlacesReserveesForDate($searchDate);
    $statusTexts = ['disponible' => 'Disponible', 'presque_complet' => 'Presque complet', 'complet' => 'Complet'];
@endphp

<div class="w-full bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden group">
    <!-- Version Mobile -->
    <div class="block md:hidden">
        <div class="p-4">
            <!-- En-tête mobile -->
            <div class="flex justify-between items-start mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-r from-[#e94e1a] to-orange-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-bus text-white"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-gray-900 leading-tight">{{ $programme->compagnie->sigle ?: ($programme->compagnie->name ?? 'Compagnie') }}</h3>
                            @if(isset($is_aller) && $is_aller)
                                <span class="bg-orange-100 text-[#e94e1a] text-[9px] font-black px-1.5 py-0.5 rounded border border-orange-200 uppercase">Aller</span>
                            @elseif(isset($is_retour) && $is_retour)
                                <span class="bg-blue-100 text-blue-600 text-[9px] font-black px-1.5 py-0.5 rounded border border-blue-200 uppercase">Retour</span>
                            @endif
                        </div>
                        <span class="text-xs text-green-600 font-semibold block mt-0.5">
                            <i class="fas fa-check-circle"></i> Vérifié
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold text-[#e94e1a]">
                        {{ number_format($programme->montant_billet, 0, ',', ' ') }} FCFA
                    </div>
                </div>
            </div>

            <!-- Trajet mobile -->
            <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-100">
                <div class="flex items-center justify-between mb-1">
                    <div class="text-center flex-1">
                        <div class="font-black text-gray-900 text-base leading-tight">{{ $programme->point_depart }}</div>
                        <div class="text-[10px] text-blue-600 font-bold uppercase mt-1 bg-blue-50/50 px-1.5 py-0.5 border border-blue-100 rounded inline-flex items-start gap-1 max-w-full text-left">
                            <i class="fas fa-map-marker-alt mt-0.5 flex-shrink-0"></i>
                            <span class="whitespace-normal leading-tight">{{ $programme->gareDepart->nom_gare ?? 'Gare' }}</span>
                        </div>
                    </div>
                    <div class="mx-3 text-gray-300"><i class="fas fa-long-arrow-alt-right text-lg"></i></div>
                    <div class="text-center flex-1">
                        <div class="font-black text-gray-900 text-base leading-tight">{{ $programme->point_arrive }}</div>
                        <div class="text-[10px] text-green-600 font-bold uppercase mt-1 bg-green-50/50 px-1.5 py-0.5 border border-green-100 rounded inline-flex items-start gap-1 max-w-full text-left">
                            <i class="fas fa-map-marker-alt mt-0.5 flex-shrink-0"></i>
                            <span class="whitespace-normal leading-tight">{{ $programme->gareArrivee->nom_gare ?? 'Gare' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations mobile -->
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-green-50 p-2 rounded-lg">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fas fa-clock text-green-500"></i>
                        <span class="text-sm font-semibold">Heures</span>
                    </div>
                    <div class="text-xs font-bold">{{ substr($programme->heure_depart, 0, 5) }} - {{ substr($programme->heure_arrive, 0, 5) }}</div>
                </div>
                <div class="bg-red-50 p-2 rounded-lg">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fas fa-chair text-red-500"></i>
                        <span class="text-sm font-semibold">Places</span>
                    </div>
                    <div class="text-xs">
                        <span class="{{ $statusKey == 'complet' ? 'text-red-600' : ($statusKey == 'presque_complet' ? 'text-yellow-600' : 'text-green-600') }} font-bold">
                            {{ $totalSeats - $reservedSeatsCount }} rest.
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions mobile -->
            <div class="flex gap-2">
                @if ($statusKey != 'complet' && !$isToday)
                    <a href="{{ route('reservation.create', [
                            'point_depart' => $programme->point_depart,
                            'point_arrive' => $programme->point_arrive,
                            'date_depart' => $searchDate,
                            'auto_reserve' => $programme->id
                        ]) }}"
                        class="flex-1 bg-[#e94e1a] text-white text-center py-2 rounded-lg font-bold hover:bg-[#d14316] shadow-sm flex items-center justify-center gap-2 text-sm">
                        <i class="fas fa-ticket-alt"></i> <span>Réserver</span>
                    </a>
                @else
                    <button class="flex-1 bg-gray-400 text-white text-center py-2 rounded-lg font-bold cursor-not-allowed flex items-center justify-center gap-2 text-sm" disabled>
                        <span>{{ $isToday ? 'Fermé (Jour J)' : 'Complet' }}</span>
                    </button>
                @endif
                <button onclick="showVehicleDetails({{ optional($programme->getVehiculeForDate($searchDate))->id ?? 'null' }}, '{{ $searchDate }}', {{ $programme->id }})"
                    class="px-3 bg-white text-gray-600 border border-gray-200 text-center py-2 rounded-lg font-bold hover:bg-gray-50 flex items-center justify-center gap-1.5 text-xs">
                    <i class="fas fa-info-circle"></i> Détails
                </button>
            </div>
        </div>
    </div>

    <!-- Version Desktop -->
    <div class="hidden md:block">
        <div class="grid grid-cols-12 gap-4 p-5 items-center">
            <div class="col-span-3">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm overflow-hidden p-1">
                        @if($programme->compagnie->path_logo ?? false)
                            <img src="{{ asset('storage/' . $programme->compagnie->path_logo) }}" class="w-full h-full object-contain" alt="Logo">
                        @else
                            <i class="fas fa-bus text-gray-400 text-2xl"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-black text-gray-900 text-sm line-clamp-1">{{ $programme->compagnie->sigle ?: ($programme->compagnie->name ?? 'Compagnie') }}</h3>
                            @if(isset($is_aller) && $is_aller)
                                <span class="bg-orange-100 text-[#e94e1a] text-[10px] font-black px-2 py-0.5 rounded-full border border-orange-200 uppercase tracking-tighter shadow-sm">Aller</span>
                            @elseif(isset($is_retour) && $is_retour)
                                <span class="bg-blue-100 text-blue-600 text-[10px] font-black px-2 py-0.5 rounded-full border border-blue-200 uppercase tracking-tighter shadow-sm">Retour</span>
                            @endif
                        </div>
                        <div class="flex items-start gap-2 text-xs">
                            <div class="flex flex-col">
                                <div class="font-bold text-gray-800">{{ $programme->point_depart }}</div>
                                <div class="text-[9px] text-blue-600 font-bold uppercase truncate max-w-[80px]">{{ $programme->gareDepart->nom_gare ?? 'Gare' }}</div>
                            </div>
                            <i class="fas fa-long-arrow-alt-right text-gray-300 mt-1"></i>
                            <div class="flex flex-col">
                                <div class="font-bold text-gray-800">{{ $programme->point_arrive }}</div>
                                <div class="text-[9px] text-green-600 font-bold uppercase truncate max-w-[80px]">{{ $programme->gareArrivee->nom_gare ?? 'Gare' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-2 flex flex-col items-center justify-center border-l border-gray-100 h-full">
                <div class="text-xs font-bold text-gray-800 mb-1">{{ date('d/m/Y', strtotime($searchDate)) }}</div>
                <div class="text-xs font-semibold text-gray-500 bg-gray-50 px-2.5 py-1 rounded-md">
                    {{ substr($programme->heure_depart, 0, 5) }} &rarr; {{ substr($programme->heure_arrive, 0, 5) }}
                </div>
            </div>

            <div class="col-span-2 flex justify-center flex-col items-center border-l border-gray-100 h-full">
                <div class="text-xs text-gray-500 mb-1"><i class="fas fa-hourglass-half mr-1"></i>{{ $programme->durer_parcours }}</div>
                <div class="text-lg font-black text-[#e94e1a]">
                    {{ number_format($programme->montant_billet, 0, ',', ' ') }} FCFA
                </div>
            </div>

            <div class="col-span-2 flex justify-center border-l border-gray-100 h-full">
                <div class="flex flex-col items-center">
                    <div class="flex items-center gap-2 bg-gray-50 px-3 py-1 rounded-lg border border-gray-100 mb-1">
                        <div class="w-2 rounded-full h-2 {{ $statusKey == 'complet' ? 'bg-red-500' : ($statusKey == 'presque_complet' ? 'bg-yellow-500' : 'bg-green-500') }}"></div>
                        <span class="font-black text-xs {{ $statusKey == 'complet' ? 'text-red-600' : ($statusKey == 'presque_complet' ? 'text-yellow-600' : 'text-green-600') }}">
                            {{ $totalSeats - $reservedSeatsCount }} rest.
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-span-3 flex items-center justify-end gap-2 text-right">
                <button onclick="showVehicleDetails({{ optional($programme->getVehiculeForDate($searchDate))->id ?? 'null' }}, '{{ $searchDate }}', {{ $programme->id }})"
                    class="px-4 py-2 rounded-xl bg-gray-50 text-gray-600 border border-gray-200 font-bold text-xs">
                    Détails
                </button>
                @if($statusKey != 'complet' && !$isToday)
                    <a href="{{ route('reservation.create', [
                            'point_depart' => $programme->point_depart,
                            'point_arrive' => $programme->point_arrive,
                            'date_depart' => $searchDate,
                            'auto_reserve' => $programme->id
                        ]) }}"
                        class="bg-[#e94e1a] text-white px-6 py-2 rounded-xl font-bold hover:bg-[#d14316] text-xs">
                        Réserver
                    </a>
                @else
                    <button class="bg-gray-100 text-gray-400 px-6 py-2 rounded-xl font-bold cursor-not-allowed text-xs" disabled>
                        Complet
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
