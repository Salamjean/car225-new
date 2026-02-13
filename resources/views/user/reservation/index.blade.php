@extends('user.layouts.template')

@section('title', 'Mes Réservations')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-[#1A1D1F] tracking-tight flex items-center gap-3 uppercase">
                <i class="fas fa-ticket-alt text-[#e94f1b]"></i>
                Mes <span class="text-[#e94f1b]">Réservations</span>
            </h1>
            <p class="text-gray-500 font-medium">Consultez et gérez toutes vos réservations de voyage</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-5 py-2.5 bg-[#e94f1b] text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-[#e94f1b]/20">
                {{ $reservations->total() }} réservation(s)
            </span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Confirmed -->
        <div class="bg-white rounded-[32px] p-6 border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center text-green-600 transition-colors group-hover:bg-green-600 group-hover:text-white">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Confirmées</span>
            </div>
            <h3 class="text-3xl font-black text-gray-900">{{ $stats['confirmed'] }}</h3>
        </div>

        <!-- Pending -->
        <div class="bg-white rounded-[32px] p-6 border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-50 rounded-2xl flex items-center justify-center text-yellow-600 transition-colors group-hover:bg-yellow-600 group-hover:text-white">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">En attente</span>
            </div>
            <h3 class="text-3xl font-black text-gray-900">{{ $stats['pending'] }}</h3>
        </div>

        <!-- Cancelled -->
        <div class="bg-white rounded-[32px] p-6 border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center text-red-600 transition-colors group-hover:bg-red-600 group-hover:text-white">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Annulées</span>
            </div>
            <h3 class="text-3xl font-black text-gray-900">{{ $stats['cancelled'] }}</h3>
        </div>

        <!-- Total Amount -->
        <div class="bg-white rounded-[32px] p-6 border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 transition-colors group-hover:bg-blue-600 group-hover:text-white">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Dépense Totale</span>
            </div>
            <h3 class="text-3xl font-black text-gray-900">{{ number_format($stats['total_amount'], 0, ',', ' ') }} <span class="text-sm">FCFA</span></h3>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
        <button id="toggleFilters" class="w-full px-8 py-5 flex items-center justify-between hover:bg-gray-50 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center text-gray-500">
                    <i class="fas fa-filter text-xs"></i>
                </div>
                <span class="text-sm font-black text-gray-700 uppercase tracking-wider">Filtres de recherche</span>
            </div>
            <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300" id="filterChevron"></i>
        </button>
        <div id="filtersSection" class="hidden border-t border-gray-50 p-8">
            <form method="GET" action="{{ route('reservation.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Reference -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Référence / Nom</label>
                    <input type="text" name="reference" value="{{ request('reference') }}" placeholder="Ex: RES-2026..." 
                        class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none transition-all text-sm font-bold">
                </div>

                <!-- Statut -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Statut</label>
                    <select name="statut" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none transition-all text-sm font-bold">
                        <option value="">Tous les statuts</option>
                        <option value="confirmee" {{ request('statut') == 'confirmee' ? 'selected' : '' }}>Confirmée</option>
                        <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="annulee" {{ request('statut') == 'annulee' ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>

                <!-- Date Voyage -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Date du voyage</label>
                    <input type="date" name="date_voyage" value="{{ request('date_voyage') }}"
                        class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none transition-all text-sm font-bold">
                </div>

                <!-- Compagnie -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Compagnie</label>
                    <input type="text" name="compagnie" value="{{ request('compagnie') }}" placeholder="Nom de la compagnie"
                        class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none transition-all text-sm font-bold">
                </div>

                <!-- Buttons -->
                <div class="lg:col-span-2 flex items-end gap-3">
                    <button type="submit" class="flex-1 py-3.5 bg-[#e94f1b] text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-[#e94f1b]/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    <a href="{{ route('reservation.index') }}" class="px-8 py-3.5 bg-gray-100 text-gray-600 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Reservations Table -->
  <div class="bg-white rounded-[32px] border border-gray-100 shadow-xl shadow-gray-200/40 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100">
                    <th class="px-6 py-5 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest pl-8">Billet</th>
                    <th class="px-6 py-5 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Itinéraire & Compagnie</th>
                    <th class="px-6 py-5 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Départ</th>
                    <th class="px-6 py-5 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest text-center">Siège</th>
                    <th class="px-6 py-5 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest text-center">Info Passager</th>
                    <th class="px-6 py-5 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest text-right">Tarif</th>
                    <th class="px-6 py-5 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest text-center">Statut</th>
                    <th class="px-6 py-5 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest text-center pr-8">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($reservations as $reservation)
                <tr class="group hover:bg-[#fff5f2] transition-all duration-300">
                    
                    <td class="px-6 py-5 pl-8">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white border-2 border-dashed border-gray-200 flex items-center justify-center text-gray-300 group-hover:border-[#e94f1b] group-hover:text-[#e94f1b] transition-colors">
                                <i class="fas fa-barcode text-lg"></i>
                            </div>
                            <div>
                                <span class="block text-xs font-mono font-bold text-gray-600 bg-gray-100 px-2 py-1 rounded-md mb-1 group-hover:bg-white group-hover:shadow-sm transition-all">
                                    {{ $reservation->reference }}
                                </span>
                                <span class="text-[10px] font-medium text-gray-400 flex items-center gap-1">
                                    <i class="far fa-clock text-[8px]"></i> 
                                    {{ $reservation->created_at->locale('fr')->translatedFormat('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-5">
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-sm font-black text-[#1A1D1F]">{{ $reservation->programme->point_depart }}</span>
                                <div class="w-12 h-[2px] bg-gray-200 relative flex items-center justify-center">
                                    <i class="fas fa-bus text-[8px] text-gray-400 absolute bg-white px-1"></i>
                                </div>
                                <span class="text-sm font-black text-[#1A1D1F]">{{ $reservation->programme->point_arrive }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-5 h-5 rounded-full bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-building text-[8px] text-gray-500"></i>
                                </div>
                                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wide">{{ $reservation->programme->compagnie->name }}</span>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div class="flex flex-col items-center justify-center bg-gray-50 border border-gray-100 rounded-xl w-12 h-12 group-hover:bg-white group-hover:border-[#e94f1b]/20 group-hover:shadow-md transition-all">
                                <span class="text-[8px] font-bold text-[#e94f1b] uppercase leading-none mt-1">
                                    {{ Str::upper(\Carbon\Carbon::parse($reservation->date_voyage)->locale('fr')->translatedFormat('M')) }}
                                </span>
                                <span class="text-lg font-black text-gray-800 leading-none">
                                    {{ \Carbon\Carbon::parse($reservation->date_voyage)->format('d') }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($reservation->heure_depart ?? $reservation->programme->heure_depart)->format('H:i') }}</p>
                                @if(str_contains($reservation->reference, '-RET-'))
                                    <span class="inline-flex items-center gap-1 text-[9px] font-black text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">
                                        <i class="fas fa-undo-alt text-[8px]"></i> Retour
                                    </span>
                                @elseif($reservation->is_aller_retour)
                                    <span class="inline-flex items-center gap-1 text-[9px] font-black text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full">
                                        <i class="fas fa-exchange-alt text-[8px]"></i> Aller
                                    </span>
                                @else 
                                    <span class="text-[10px] text-gray-400">Voyage simple</span>
                                @endif
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-5 text-center">
                        <div class="inline-block relative">
                            <svg class="w-8 h-8 text-gray-200 group-hover:text-[#1A1D1F] transition-colors" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/></svg>
                            <span class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-[10px] font-black text-gray-600 group-hover:text-white">{{ $reservation->seat_number }}</span>
                        </div>
                    </td>

                    <td class="px-6 py-5 text-center">
                        <button type="button" 
                            class="view-passenger-details-btn relative inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-100 text-gray-500 hover:bg-[#e94f1b] hover:text-white hover:shadow-lg hover:shadow-[#e94f1b]/30 transition-all duration-300"
                            data-nom="{{ $reservation->passager_nom }}"
                            data-prenom="{{ $reservation->passager_prenom }}"
                            data-email="{{ $reservation->passager_email ?? 'Non renseigné' }}"
                            data-telephone="{{ $reservation->passager_telephone ?? 'Non renseigné' }}"
                            data-urgence="{{ $reservation->passager_urgence ?? 'Non renseigné' }}">
                            <i class="far fa-user"></i>
                            <div class="absolute -top-1 -right-1 w-3 h-3 bg-white rounded-full flex items-center justify-center">
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            </div>
                        </button>
                    </td>

                    <td class="px-6 py-5 text-right">
                        <p class="text-sm font-black text-[#1A1D1F]">{{ number_format($reservation->montant, 0, ',', ' ') }}</p>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">FCFA</p>
                    </td>

                    <td class="px-6 py-5 text-center">
                        @if($reservation->statut == 'confirmee')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-600 text-[10px] font-black rounded-lg uppercase tracking-widest border border-green-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Confirmé
                            </span>
                        @elseif($reservation->statut == 'en_attente')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-yellow-50 text-yellow-600 text-[10px] font-black rounded-lg uppercase tracking-widest border border-yellow-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span> En attente
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 text-[10px] font-black rounded-lg uppercase tracking-widest border border-red-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Annulé
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-5 text-center pr-8">
                        <div class="flex items-center justify-center gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                            @if($reservation->statut == 'confirmee')
                                @php
                                    $heureDepart = $reservation->heure_depart ?? $reservation->programme->heure_depart ?? '00:00';
                                    $dateVoyage = \Carbon\Carbon::parse($reservation->date_voyage)->format('Y-m-d');
                                    $departureDateTime = \Carbon\Carbon::parse("{$dateVoyage} {$heureDepart}");
                                    $canAct = $departureDateTime->diffInMinutes(now(), false) < -15;
                                @endphp
                                
                                <button type="button" class="modify-btn w-8 h-8 rounded-lg flex items-center justify-center transition-all {{ $canAct ? 'bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}"
                                    data-id="{{ $reservation->id }}"
                                    data-reference="{{ $reservation->reference }}"
                                    data-departure="{{ $departureDateTime->toISOString() }}"
                                    title="Modifier" {{ !$canAct ? 'disabled' : '' }}>
                                    <i class="fas fa-pen text-xs"></i>
                                </button>
                                
                                <button type="button" class="cancel-btn w-8 h-8 rounded-lg flex items-center justify-center transition-all {{ $canAct ? 'bg-red-50 text-red-600 hover:bg-red-600 hover:text-white' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}"
                                    data-id="{{ $reservation->id }}"
                                    data-reference="{{ $reservation->reference }}"
                                    data-montant="{{ $reservation->montant }}"
                                    data-departure="{{ $departureDateTime->toISOString() }}"
                                    title="Annuler" {{ !$canAct ? 'disabled' : '' }}>
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>

                                <a href="{{ route('reservations.download', $reservation) }}" class="w-8 h-8 bg-gray-900 text-white rounded-lg flex items-center justify-center hover:scale-110 transition-transform shadow-lg shadow-gray-900/20" title="Télécharger">
                                    <i class="fas fa-download text-xs"></i>
                                </a>

                            @elseif($reservation->statut == 'annulee')
                                <span class="text-[9px] font-bold text-[#e94f1b] uppercase flex items-center gap-1">
                                    @if($reservation->refund_amount) 
                                        <i class="fas fa-hand-holding-usd"></i> Remboursé
                                    @else 
                                        - 
                                    @endif
                                </span>
                            @endif
                            
                            <a href="{{ route('reservations.show', $reservation) }}" class="w-8 h-8 bg-white border border-gray-200 text-gray-400 rounded-lg flex items-center justify-center hover:border-[#e94f1b] hover:text-[#e94f1b] transition-all" title="Détails">
                                <i class="fas fa-angle-right"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-8 py-24 text-center">
                        <div class="flex flex-col items-center animate-fade-in-up">
                            <div class="w-24 h-24 bg-orange-50 rounded-full flex items-center justify-center text-[#e94f1b] mb-6 shadow-xl shadow-orange-100">
                                <i class="fas fa-ticket-alt text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-black text-gray-900 mb-2 uppercase tracking-tight">Aucun voyage pour le moment</h3>
                            <p class="text-gray-400 font-medium mb-8 max-w-sm mx-auto">Votre historique de réservations est vide. Prêt à partir à l'aventure ?</p>
                            <a href="{{ route('reservation.create') }}" class="px-10 py-4 bg-[#e94f1b] text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-lg shadow-[#e94f1b]/30 hover:-translate-y-1 transition-all">
                                <i class="fas fa-plus mr-2"></i> Réserver un billet
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($reservations->hasPages())
    <div class="px-8 py-6 border-t border-gray-50 bg-gray-50">
        {{ $reservations->links() }}
    </div>
    @endif
</div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle Filters
        const toggleBtn = document.getElementById('toggleFilters');
        const filtersSection = document.getElementById('filtersSection');
        const chevron = document.getElementById('filterChevron');

        if(toggleBtn && filtersSection) {
            toggleBtn.addEventListener('click', () => {
                filtersSection.classList.toggle('hidden');
                chevron.classList.toggle('rotate-180');
            });
        }

        // Passenger Details Popup
        $('.view-passenger-details-btn').on('click', function() {
            const data = $(this).data();
            Swal.fire({
                title: '<span class="text-xl font-black uppercase tracking-tight">Détails du Passager</span>',
                html: `
                    <div class="text-left space-y-4 py-4">
                        <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-2xl">
                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm text-[#e94f1b]">
                                <i class="fas fa-user text-xl"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Nom Complet</p>
                                <p class="text-lg font-black text-gray-900 leading-none">${data.nom} ${data.prenom}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-4">
                            <div class="bg-gray-50 p-4 rounded-2xl">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Téléphone</p>
                                <p class="font-black text-gray-900">${data.telephone}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-2xl">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Email</p>
                                <p class="font-black text-gray-900">${data.email}</p>
                            </div>
                            <div class="bg-red-50 p-4 rounded-2xl border border-red-100">
                                <p class="text-[10px] font-black text-red-400 uppercase tracking-widest mb-1">Contact d'urgence</p>
                                <p class="font-black text-red-600">${data.urgence}</p>
                            </div>
                        </div>
                    </div>
                `,
                showConfirmButton: true,
                confirmButtonText: 'Fermer',
                confirmButtonColor: '#1A1D1F',
                width: '450px',
                padding: '2rem',
                customClass: {
                    container: 'font-outfit',
                    popup: 'rounded-[32px]',
                    confirmButton: 'rounded-xl px-8 py-3 font-black uppercase tracking-widest text-xs'
                }
            });
        });

        // =========================================
        // CANCELLATION LOGIC
        // =========================================
        $(document).on('click', '.cancel-btn:not([disabled])', function() {
            const reservationId = $(this).data('id');
            const reference = $(this).data('reference');

            // Step 1: Fetch refund preview
            Swal.fire({
                title: '<span class="text-lg font-black uppercase tracking-tight">Annulation de réservation</span>',
                html: '<div class="flex items-center justify-center py-8"><div class="animate-spin w-8 h-8 border-4 border-[#e94f1b] border-t-transparent rounded-full"></div></div><p class="text-sm text-gray-500">Calcul du remboursement...</p>',
                showConfirmButton: false,
                allowOutsideClick: false,
                width: '480px',
                customClass: { popup: 'rounded-[32px]' }
            });

            $.get(`/user/booking/reservations/${reservationId}/refund-preview`)
                .done(function(data) {
                    if (!data.can_cancel) {
                        Swal.fire({
                            icon: 'error',
                            title: '<span class="text-lg font-black uppercase tracking-tight text-red-600">Action impossible</span>',
                            html: '<p class="text-sm text-gray-600">L\'annulation est impossible moins de 15 minutes avant le départ.</p>',
                            confirmButtonText: 'Compris',
                            confirmButtonColor: '#1A1D1F',
                            customClass: { popup: 'rounded-[32px]', confirmButton: 'rounded-xl px-8 py-3 font-black uppercase tracking-widest text-xs' }
                        });
                        return;
                    }

                    // Determine color based on percentage
                    let color = '#22c55e'; // green
                    if (data.percentage <= 20) color = '#ef4444'; // red
                    else if (data.percentage <= 40) color = '#f97316'; // orange
                    else if (data.percentage <= 70) color = '#eab308'; // yellow

                    // Build title
                    const title = data.is_round_trip 
                        ? '<span class="text-lg font-black uppercase tracking-tight">Confirmer l\'annulation aller-retour</span>'
                        : '<span class="text-lg font-black uppercase tracking-tight">Confirmer l\'annulation</span>';

                    // Build references section
                    let referencesHtml = '';
                    if (data.is_round_trip) {
                        referencesHtml = `
                            <div class="bg-orange-50 p-4 rounded-2xl border border-orange-100">
                                <p class="text-[10px] font-black text-orange-600 uppercase tracking-widest mb-2"><i class="fas fa-info-circle mr-1"></i> Aller-Retour</p>
                                <p class="text-xs text-gray-700 mb-2">Les deux billets seront annulés :</p>
                                <div class="flex gap-2">
                                    <span class="text-xs font-bold text-[#e94f1b] bg-white px-3 py-1 rounded-lg">${data.reference}</span>
                                    <span class="text-xs font-bold text-[#e94f1b] bg-white px-3 py-1 rounded-lg">${data.paired_reference}</span>
                                </div>
                            </div>
                        `;
                    } else {
                        referencesHtml = `
                            <div class="bg-gray-50 p-4 rounded-2xl">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Réservation</p>
                                <p class="text-sm font-black text-[#e94f1b]">${data.reference}</p>
                            </div>
                        `;
                    }

                    Swal.fire({
                        title: title,
                        html: `
                            <div class="text-left space-y-4 py-4">
                                ${referencesHtml}
                                
                                <div class="bg-gray-50 p-4 rounded-2xl">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Délai avant départ</p>
                                    <p class="text-sm font-bold text-gray-900">${data.time_remaining}</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-4 rounded-2xl border-2" style="border-color: ${color}; background: ${color}10">
                                        <p class="text-[10px] font-black uppercase tracking-widest mb-1" style="color: ${color}">Remboursement</p>
                                        <p class="text-2xl font-black" style="color: ${color}">
                                            ${data.percentage !== null ? data.percentage + '%' : Number(data.refund_amount).toLocaleString('fr-FR')}
                                        </p>
                                        <p class="text-xs font-bold text-gray-500">${data.percentage !== null ? Number(data.refund_amount).toLocaleString('fr-FR') + ' FCFA' : 'Montant final'}</p>
                                    </div>
                                    <div class="bg-red-50 p-4 rounded-2xl border-2 border-red-200">
                                        <p class="text-[10px] font-black text-red-400 uppercase tracking-widest mb-1">Frais / Pénalité</p>
                                        <p class="text-2xl font-black text-red-500">
                                            ${data.percentage !== null ? (100 - data.percentage) + '%' : '-' + Number(data.penalty || (data.montant_original - data.refund_amount)).toLocaleString('fr-FR')}
                                        </p>
                                        <p class="text-xs font-bold text-gray-500">${data.percentage !== null ? Number(data.montant_original - data.refund_amount).toLocaleString('fr-FR') + ' FCFA' : 'Dếduit'}</p>
                                    </div>
                                </div>

                                <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100">
                                    <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Montant crédité sur votre Wallet</p>
                                    <p class="text-xl font-black text-blue-600">${Number(data.refund_amount).toLocaleString('fr-FR')} FCFA</p>
                                    ${data.is_round_trip ? '<p class="text-xs text-gray-500 mt-1">Remboursement total pour les deux billets</p>' : ''}
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-check mr-2"></i> Confirmer l\'annulation',
                        cancelButtonText: 'Annuler',
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        width: '520px',
                        padding: '2rem',
                        customClass: {
                            popup: 'rounded-[32px]',
                            confirmButton: 'rounded-xl px-6 py-3 font-black uppercase tracking-widest text-xs',
                            cancelButton: 'rounded-xl px-6 py-3 font-black uppercase tracking-widest text-xs'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Execute cancellation
                            Swal.fire({
                                title: '<span class="text-lg font-black uppercase tracking-tight">Annulation en cours...</span>',
                                html: '<div class="flex items-center justify-center py-8"><div class="animate-spin w-8 h-8 border-4 border-red-500 border-t-transparent rounded-full"></div></div>',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                customClass: { popup: 'rounded-[32px]' }
                            });

                            $.ajax({
                                url: `/user/booking/reservations/${reservationId}/cancel`,
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                data: { reason: 'Annulé par l\'utilisateur' },
                                success: function(result) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '<span class="text-lg font-black uppercase tracking-tight text-green-600">Annulation réussie !</span>',
                                        html: `<p class="text-sm text-gray-600 mb-2">${result.message}</p>`,
                                        confirmButtonText: 'OK',
                                        confirmButtonColor: '#22c55e',
                                        customClass: { popup: 'rounded-[32px]', confirmButton: 'rounded-xl px-8 py-3 font-black uppercase tracking-widest text-xs' }
                                    }).then(() => window.location.reload());
                                },
                                error: function(xhr) {
                                    const error = xhr.responseJSON;
                                    Swal.fire({
                                        icon: 'error',
                                        title: '<span class="text-lg font-black uppercase tracking-tight text-red-600">Erreur</span>',
                                        html: `<p class="text-sm text-gray-600">${error?.message || 'Une erreur est survenue.'}</p>`,
                                        confirmButtonText: 'OK',
                                        confirmButtonColor: '#1A1D1F',
                                        customClass: { popup: 'rounded-[32px]', confirmButton: 'rounded-xl px-8 py-3 font-black uppercase tracking-widest text-xs' }
                                    });
                                }
                            });
                        }
                    });
                })
                .fail(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Impossible de récupérer les détails du remboursement.',
                        confirmButtonColor: '#1A1D1F',
                        customClass: { popup: 'rounded-[32px]' }
                    });
                });
        });

        // =========================================
        // MODIFICATION LOGIC - NEW IMPLEMENTATION
      let modifState = {
        resId: null,
        residualValue: 0,
        userSolde: 0,
        isRoundTrip: false,
        current: {
            progId: null,
            date: null,
            time: null,
            seat: null,
            retProgId: null,
            retDate: null,
            retTime: null,
            retSeat: null 
        }
    };

    $('body').on('click', '.modify-btn:not([disabled])', async function() {
        const resId = $(this).data('id');
        modifState.resId = resId;

        Swal.fire({
            title: 'Chargement...',
            html: '<div class="flex flex-col items-center"><div class="animate-spin w-8 h-8 border-4 border-[#e94f1b] border-t-transparent rounded-full mb-2"></div><span class="text-sm text-gray-500">Récupération des données...</span></div>',
            showConfirmButton: false,
            allowOutsideClick: false,
            width: '450px',
            customClass: { popup: 'rounded-[32px]' }
        });

        try {
            const response = await $.get(`/user/booking/reservations/${resId}/modification-data`);
            
            if(!response.success) throw new Error(response.message);

            // 1. Initialiser l'état
            modifState.residualValue = response.residual_value;
            modifState.userSolde = response.user_solde;
            modifState.isRoundTrip = response.is_aller_retour;

            // Données ALLER (Normalisation des formats)
            modifState.current.progId = response.reservation.programme_id;
            modifState.current.date = response.formatted_date_aller;
            // On garde seulement HH:mm pour la comparaison
            modifState.current.time = response.reservation.heure_depart.substring(0, 5); 
            modifState.current.seat = response.reservation.seat_number;

            // Données RETOUR
            if(modifState.isRoundTrip && response.return_details) {
                modifState.current.retProgId = response.return_details.prog_id;
                modifState.current.retDate = response.return_details.date;
                modifState.current.retTime = response.return_details.time ? response.return_details.time.substring(0, 5) : null;
                modifState.current.retSeat = response.return_details.seat;
            }

            // 2. Construire le Select Trajet
            let routeOptions = '';
            response.available_routes.forEach(route => {
                const isSelected = route.unique_key === response.current_route_key ? 'selected' : '';
                routeOptions += `<option value="${route.id}" ${isSelected} 
                    data-depart="${route.depart}" 
                    data-arrive="${route.arrive}" 
                    data-compagnie="${route.compagnie_id}"
                    data-prix="${route.prix}">
                    ${route.name} - ${route.compagnie}
                </option>`;
            });

            // HTML Retour
            let returnHtml = '';
            if(modifState.isRoundTrip) {
                returnHtml = `
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="px-2 py-1 bg-orange-100 text-orange-600 text-[10px] font-black rounded uppercase">Retour</span>
                            <p class="text-xs font-bold text-gray-700">Informations de retour</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div>
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Date Retour</label>
                                <input type="date" id="mod-ret-date" class="w-full p-3 bg-white border border-gray-200 rounded-xl text-sm font-bold outline-none focus:ring-2 focus:ring-orange-500">
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Heure Retour</label>
                                <select id="mod-ret-time" class="w-full p-3 bg-white border border-gray-200 rounded-xl text-sm font-bold outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="">Chargement...</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Place Retour</label>
                            <div id="mod-ret-seat-container" class="mt-1 p-3 bg-gray-50 border border-gray-100 rounded-xl min-h-[50px] flex items-center justify-center text-xs text-gray-400">
                                En attente...
                            </div>
                            <input type="hidden" id="mod-ret-seat-input">
                        </div>
                    </div>
                `;
            }

            const modalHtml = `
                <div class="text-left font-outfit space-y-5">
                    <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100 flex justify-between items-center">
                        <div>
                            <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Valeur Actuelle</p>
                            <p class="text-xl font-black text-blue-600">${Number(response.residual_value).toLocaleString()} FCFA</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-400">Pénalité: ${response.penalty_info}</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Trajet</label>
                        <select id="mod-route" class="w-full p-2 bg-white border border-gray-200 rounded-lg text-sm font-bold outline-none focus:ring-2 focus:ring-[#e94f1b]">
                            ${routeOptions}
                        </select>
                    </div>

                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Voyage Aller</p>
                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div>
                                <label class="text-[9px] font-bold text-gray-400 uppercase block mb-1">Date</label>
                                <input type="date" id="mod-date" class="w-full p-3 bg-white border border-gray-200 rounded-xl text-sm font-bold outline-none focus:ring-2 focus:ring-[#e94f1b]" min="${new Date().toISOString().split('T')[0]}">
                            </div>
                            <div>
                                <label class="text-[9px] font-bold text-gray-400 uppercase block mb-1">Heure</label>
                                <select id="mod-time" class="w-full p-3 bg-white border border-gray-200 rounded-xl text-sm font-bold outline-none focus:ring-2 focus:ring-[#e94f1b]">
                                    <option value="">Chargement...</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-[9px] font-bold text-gray-400 uppercase block mb-1">Place</label>
                            <div id="mod-seat-container" class="mt-1 p-3 bg-gray-50 border border-gray-100 rounded-xl min-h-[50px] flex items-center justify-center text-xs text-gray-400">
                                En attente...
                            </div>
                            <input type="hidden" id="mod-seat-input">
                        </div>
                    </div>

                    ${returnHtml}

                    <div id="delta-box" class="hidden bg-gray-900 text-white p-4 rounded-2xl mt-4">
                        <div class="flex justify-between items-center">
                            <span id="delta-label" class="text-xs font-medium text-gray-400 uppercase">Total à payer</span>
                            <span id="delta-amount" class="text-xl font-black text-white">0 FCFA</span>
                        </div>
                        <p id="wallet-error" class="text-[10px] text-red-400 mt-1 hidden">Solde insuffisant</p>
                    </div>
                </div>
            `;

            Swal.fire({
                title: '<span class="text-xl font-black uppercase tracking-tight">Modifier Réservation</span>',
                html: modalHtml,
                showCancelButton: true,
                confirmButtonText: 'Confirmer',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#1A1D1F',
                cancelButtonColor: '#f3f4f6',
                width: '600px',
                padding: '2rem',
                customClass: { popup: 'rounded-[32px]', confirmButton: 'rounded-xl px-8 py-3', cancelButton: 'rounded-xl px-8 py-3 text-gray-800' },
                didOpen: async () => {
                    initEvents();
                    
                    // --- PRE-REMPLISSAGE ET CHARGEMENT ---
                    
                    // 1. ALLER
                    if(modifState.current.date) {
                        $('#mod-date').val(modifState.current.date);
                        await preloadAllerData();
                    }

                    // 2. RETOUR
                    if(modifState.isRoundTrip && modifState.current.retDate) {
                        $('#mod-ret-date').val(modifState.current.retDate);
                        await preloadRetourData();
                    }
                },
                preConfirm: handleModificationSubmit
            });

        } catch (error) {
            Swal.fire('Erreur', error.message, 'error');
        }
    });

    // --- FONCTIONS AJAX (Avec normalisation) ---

    async function preloadAllerData() {
        const date = $('#mod-date').val();
        // loadTimes va déjà mettre l'attribut 'selected' si l'heure correspond
        await loadTimes('aller', date, modifState.current.time);
        
        const time = $('#mod-time').val();
        if(time) {
            const progId = $('#mod-time option:selected').data('prog-id');
            await loadSeats('aller', progId, date, time, modifState.current.seat);
        }
    }

    async function preloadRetourData() {
        const date = $('#mod-ret-date').val();
        // loadTimes va déjà mettre l'attribut 'selected' si l'heure correspond
        await loadTimes('retour', date, modifState.current.retTime);

        const time = $('#mod-ret-time').val();
        if(time) {
            const progId = $('#mod-ret-time option:selected').data('prog-id');
            await loadSeats('retour', progId, date, time, modifState.current.retSeat); 
        }
    }

    async function loadTimes(type, date, preSelectedTime = null) {
        const routeOption = $('#mod-route option:selected');
        let depart, arrive;
        if (type === 'retour') {
            depart = routeOption.data('arrive'); 
            arrive = routeOption.data('depart');
        } else {
            depart = routeOption.data('depart');
            arrive = routeOption.data('arrive');
        }
        
        const compagnie = routeOption.data('compagnie');
        const selector = type === 'retour' ? '#mod-ret-time' : '#mod-time';

        $(selector).html('<option value="">Chargement...</option>').prop('disabled', true);

        try {
            // Utilisation de l'endpoint interne correct
            const res = await $.get(`/user/booking/api/route-schedules?point_depart=${depart}&point_arrive=${arrive}&compagnie_id=${compagnie}&date=${date}`);
            
            if(res.success && res.schedules.length > 0) {
                let opts = '<option value="">-- Choisir Heure --</option>';
                res.schedules.forEach(sch => {
                    // Normalisation pour affichage : HH:mm
                    const schDisplay = sch.heure_depart.substring(0, 5);
                    const schTime = sch.heure_depart; // On garde la valeur brute (ex: 08:00:00)
                    
                    const preTime = preSelectedTime ? preSelectedTime.substring(0, 5) : '';
                    const isSelected = (preTime && schDisplay === preTime) ? 'selected' : '';
                    
                    opts += `<option value="${schTime}" ${isSelected} data-prog-id="${sch.id}" data-prix="${sch.montant_billet}">${schDisplay}</option>`;
                });
                $(selector).html(opts).prop('disabled', false);
            } else {
                $(selector).html('<option value="">Aucun départ</option>');
            }
        } catch(e) {
            $(selector).html('<option>Erreur</option>');
        }
    }

    async function loadSeats(type, progId, date, time, preSelectedSeat = null) {
        const container = type === 'retour' ? '#mod-ret-seat-container' : '#mod-seat-container';
        const input = type === 'retour' ? '#mod-ret-seat-input' : '#mod-seat-input';

        $(container).html('<div class="flex justify-center p-2"><div class="animate-spin w-4 h-4 border-2 border-gray-400 border-t-transparent rounded-full"></div></div>');

        try {
            // Note: time doit être envoyé au format attendu par ton API (peut-être ajouter :00 si besoin)
            const res = await $.get(`/user/booking/programmes/${progId}/seats?date=${date}&heure=${time}`);
            
            if(res.success) {
                let html = '<div class="grid grid-cols-7 gap-2">';
                res.seats.forEach(seat => {
                    const isMine = (preSelectedSeat && seat.number == preSelectedSeat);
                    
                    let css = 'bg-gray-100 text-gray-300 cursor-not-allowed';
                    let action = '';

                    if (seat.available) {
                        css = 'bg-white border border-gray-200 text-gray-700 hover:border-[#e94f1b] hover:text-[#e94f1b] cursor-pointer';
                        action = `onclick="selectSeat('${type}', this, ${seat.number})"`;
                    } else if (isMine) {
                        css = 'bg-[#e94f1b] text-white border border-[#e94f1b] cursor-pointer shadow-md transform scale-105';
                        action = `onclick="selectSeat('${type}', this, ${seat.number})"`;
                        $(input).val(seat.number);
                    }

                    html += `<div ${action} class="seat-item-${type} h-8 rounded-lg flex items-center justify-center text-xs font-bold transition-all ${css}">${seat.number}</div>`;
                });
                html += '</div>';
                $(container).html(html);
                
                if($(input).val()) calculateTotal();
            }
        } catch(e) {
            $(container).text('Erreur chargement');
        }
    }

    // --- EVENTS UI & UTILS ---
    function initEvents() {
        $('#mod-route').change(function() {
            $('#mod-time, #mod-ret-time').html('<option value="">Date requise</option>').prop('disabled', true);
            $('#mod-seat-container, #mod-ret-seat-container').html('<span class="text-xs text-gray-400">Sélectionnez une heure</span>');
            $('#mod-seat-input, #mod-ret-seat-input').val('');
            $('#delta-box').addClass('hidden');
            
            if($('#mod-date').val()) loadTimes('aller', $('#mod-date').val());
            if(modifState.isRoundTrip && $('#mod-ret-date').val()) loadTimes('retour', $('#mod-ret-date').val());
        });

        $('#mod-date').change(function() { loadTimes('aller', $(this).val()); });
        $('#mod-ret-date').change(function() { loadTimes('retour', $(this).val()); });

        $('#mod-time').change(function() {
            const progId = $(this).find(':selected').data('prog-id');
            if(progId) loadSeats('aller', progId, $('#mod-date').val(), $(this).val());
        });

        $('#mod-ret-time').change(function() {
            const progId = $(this).find(':selected').data('prog-id');
            if(progId) loadSeats('retour', progId, $('#mod-ret-date').val(), $(this).val());
        });
    }

    window.selectSeat = function(type, el, number) {
        const selector = `.seat-item-${type}`;
        const input = type === 'retour' ? '#mod-ret-seat-input' : '#mod-seat-input';

        $(selector).removeClass('bg-[#e94f1b] text-white border-[#e94f1b]').addClass('bg-white text-gray-700');
        $(el).removeClass('bg-white text-gray-700').addClass('bg-[#e94f1b] text-white border-[#e94f1b]');
        
        $(input).val(number);
        calculateTotal();
    };

    async function calculateTotal() {
        const progIdAller = $('#mod-time option:selected').data('prog-id');
        const seatAller = $('#mod-seat-input').val();
        
        if(!progIdAller || !seatAller) return;

        let data = {
            new_programme_id: progIdAller,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        if(modifState.isRoundTrip) {
            const progIdRetour = $('#mod-ret-time option:selected').data('prog-id');
            const seatRetour = $('#mod-ret-seat-input').val();
            if(!progIdRetour || !seatRetour) return;
            data.new_return_programme_id = progIdRetour;
        }

        $('#delta-box').removeClass('hidden').addClass('opacity-50');

        try {
            const res = await $.post(`/user/booking/reservations/${modifState.resId}/calculate-delta`, data);
            
            $('#delta-box').removeClass('opacity-50');
            $('#delta-amount').text(Number(res.delta).toLocaleString() + ' FCFA');
            
            const btn = Swal.getConfirmButton();
            if(res.action === 'pay') {
                $('#delta-label').text('Reste à payer');
                $('#delta-amount').removeClass('text-green-400').addClass('text-red-400');
                if(!res.can_afford) {
                    $('#wallet-error').removeClass('hidden');
                    btn.disabled = true;
                } else {
                    $('#wallet-error').addClass('hidden');
                    btn.disabled = false;
                }
            } else {
                $('#delta-label').text(res.action === 'refund' ? 'Crédit à rembourser' : 'Aucune différence');
                $('#delta-amount').removeClass('text-red-400').addClass('text-green-400');
                $('#wallet-error').addClass('hidden');
                btn.disabled = false;
            }
        } catch(e) { console.error(e); }
    }

    async function handleModificationSubmit() {
        const seatAller = $('#mod-seat-input').val();
        const progIdAller = $('#mod-time option:selected').data('prog-id');
        const dateAller = $('#mod-date').val();
        const heureAller = $('#mod-time').val();

        if(!seatAller || !progIdAller) {
            Swal.showValidationMessage('Veuillez sélectionner le voyage aller complet');
            return false;
        }

        let payload = {
            programme_id: progIdAller,
            date_voyage: dateAller,
            heure_depart: heureAller,
            seat_number: seatAller,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        if(modifState.isRoundTrip) {
            const seatRetour = $('#mod-ret-seat-input').val();
            const progIdRetour = $('#mod-ret-time option:selected').data('prog-id');
            const dateRetour = $('#mod-ret-date').val();
            const heureRetour = $('#mod-ret-time').val();

            if(!seatRetour || !progIdRetour) {
                Swal.showValidationMessage('Veuillez compléter le voyage retour');
                return false;
            }

            payload.return_programme_id = progIdRetour;
            payload.return_date_voyage = dateRetour;
            payload.return_heure_depart = heureRetour;
            payload.return_seat_number = seatRetour;
        }

        try {
            const result = await $.post(`/user/booking/reservations/${modifState.resId}/modify`, payload);
            return result;
        } catch (error) {
            Swal.showValidationMessage(error.responseJSON?.message || 'Erreur technique');
            return false;
        }
    }
});
</script>
@endpush

@stop