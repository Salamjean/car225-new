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
    <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Référence</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Itinéraire</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Voyage</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Place</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Passager</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Montant</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Statut</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($reservations as $reservation)
                    <tr class="group hover:bg-gray-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 group-hover:text-[#e94f1b] transition-colors">
                                    <i class="fas fa-receipt text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-[#e94f1b] leading-none mb-1">{{ $reservation->reference }}</p>
                                    <p class="text-[10px] font-bold text-gray-400">{{ $reservation->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div>
                                <p class="text-sm font-black text-gray-900 flex items-center gap-2">
                                    {{ $reservation->programme->point_depart }} 
                                    <i class="fas fa-arrow-right text-[10px] text-[#e94f1b]"></i>
                                    {{ $reservation->programme->point_arrive }}
                                </p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $reservation->programme->compagnie->name }}</p>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 text-sm font-black text-gray-900">
                                    <i class="far fa-calendar text-[#e94f1b]"></i>
                                    {{ \Carbon\Carbon::parse($reservation->date_voyage)->format('d/m/Y') }}
                                </div>
                                <div class="text-[10px] font-bold text-gray-400 flex items-center gap-2">
                                    <i class="far fa-clock"></i>
                                    {{ \Carbon\Carbon::parse($reservation->heure_depart ?? $reservation->programme->heure_depart)->format('H:i') }}
                                </div>
                                @if(str_contains($reservation->reference, '-RET-'))
                                    <span class="inline-flex px-2 py-0.5 bg-blue-50 text-blue-600 text-[8px] font-black rounded uppercase tracking-widest">Retour</span>
                                @elseif($reservation->is_aller_retour)
                                    <span class="inline-flex px-2 py-0.5 bg-orange-50 text-orange-600 text-[8px] font-black rounded uppercase tracking-widest">Aller</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <div class="inline-flex items-center justify-center w-8 h-8 bg-gray-900 text-white text-xs font-black rounded-lg">
                                {{ $reservation->seat_number }}
                            </div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <button type="button" 
                                class="view-passenger-details-btn group/pass w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition-all mx-auto"
                                data-nom="{{ $reservation->passager_nom }}"
                                data-prenom="{{ $reservation->passager_prenom }}"
                                data-email="{{ $reservation->passager_email ?? 'Non renseigné' }}"
                                data-telephone="{{ $reservation->passager_telephone ?? 'Non renseigné' }}"
                                data-urgence="{{ $reservation->passager_urgence ?? 'Non renseigné' }}">
                                <i class="far fa-eye text-sm group-hover/pass:scale-110 transition-transform"></i>
                            </button>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="text-sm font-black text-gray-900">{{ number_format($reservation->montant, 0, ',', ' ') }} <span class="text-[10px] text-gray-400">FCFA</span></span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            @if($reservation->statut == 'confirmee')
                                <span class="px-3 py-1.5 bg-green-50 text-green-600 text-[10px] font-black rounded-xl uppercase tracking-widest">Confirmé</span>
                            @elseif($reservation->statut == 'en_attente')
                                <span class="px-3 py-1.5 bg-yellow-50 text-yellow-600 text-[10px] font-black rounded-xl uppercase tracking-widest">En attente</span>
                            @else
                                <span class="px-3 py-1.5 bg-red-50 text-red-600 text-[10px] font-black rounded-xl uppercase tracking-widest">Annulé</span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-center">
                            <div class="flex items-center justify-center gap-2">
                                @if($reservation->statut == 'confirmee')
                                    @php
                                        $heureDepart = $reservation->heure_depart ?? $reservation->programme->heure_depart ?? '00:00';
                                        $dateVoyage = \Carbon\Carbon::parse($reservation->date_voyage)->format('Y-m-d');
                                        $departureDateTime = \Carbon\Carbon::parse("{$dateVoyage} {$heureDepart}");
                                        $canAct = $departureDateTime->diffInMinutes(now(), false) < -15;
                                    @endphp
                                    {{-- Modify Button --}}
                                    <button 
                                        type="button"
                                        class="modify-btn w-9 h-9 rounded-xl flex items-center justify-center transition-all shadow-sm {{ $canAct ? 'bg-blue-500 text-white hover:bg-blue-600 hover:scale-110 active:scale-95' : 'bg-gray-200 text-gray-400 cursor-not-allowed' }}"
                                        data-id="{{ $reservation->id }}"
                                        data-reference="{{ $reservation->reference }}"
                                        data-departure="{{ $departureDateTime->toISOString() }}"
                                        title="Modifier"
                                        {{ !$canAct ? 'disabled' : '' }}>
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                    {{-- Cancel Button --}}
                                    <button 
                                        type="button"
                                        class="cancel-btn w-9 h-9 rounded-xl flex items-center justify-center transition-all shadow-sm {{ $canAct ? 'bg-red-500 text-white hover:bg-red-600 hover:scale-110 active:scale-95' : 'bg-gray-200 text-gray-400 cursor-not-allowed' }}"
                                        data-id="{{ $reservation->id }}"
                                        data-reference="{{ $reservation->reference }}"
                                        data-montant="{{ $reservation->montant }}"
                                        data-departure="{{ $departureDateTime->toISOString() }}"
                                        title="Annuler"
                                        {{ !$canAct ? 'disabled' : '' }}>
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                    {{-- PDF Download --}}
                                    <a href="{{ route('reservations.download', $reservation) }}" class="w-9 h-9 bg-gray-900 text-white rounded-xl flex items-center justify-center transition-transform hover:scale-110 active:scale-95 shadow-lg shadow-gray-900/10" title="Télécharger le billet">
                                        <i class="fas fa-file-pdf text-xs"></i>
                                    </a>
                                @elseif($reservation->statut == 'annulee')
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                        @if($reservation->refund_amount)
                                            {{ number_format($reservation->refund_amount, 0, ',', ' ') }} FCFA remboursé
                                        @else
                                            Annulée
                                        @endif
                                    </span>
                                @elseif($reservation->statut == 'terminee')
                                    <span class="text-[9px] font-bold text-green-500 uppercase tracking-widest">Terminée</span>
                                @endif
                                <a href="{{ route('reservations.show', $reservation) }}" class="w-9 h-9 bg-white border border-gray-100 text-gray-900 rounded-xl flex items-center justify-center transition-transform hover:scale-110 active:scale-95 shadow-sm" title="Voir les détails">
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-50 rounded-[32px] flex items-center justify-center text-gray-200 mb-6">
                                    <i class="fas fa-ticket-alt text-4xl"></i>
                                </div>
                                <h3 class="text-xl font-black text-gray-900 mb-2 uppercase tracking-tight">Aucune réservation</h3>
                                <p class="text-gray-400 font-medium mb-8">Commencez par planifier votre prochain voyage.</p>
                                <a href="{{ route('reservation.create') }}" class="px-8 py-4 bg-[#e94f1b] text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl shadow-[#e94f1b]/20 hover:scale-105 active:scale-95 transition-all">
                                    Réserver un voyage
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($reservations->hasPages())
        <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/50">
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
        // MODIFICATION LOGIC - ALL-IN-ONE MODAL
        // =========================================
        let modificationState = {
            reservationId: null,
            isRoundTrip: false,
            residualValue: 0,
            selectedTrip: null,
            selectedDate: null,
            selectedTime: null,
            selectedSeat: null,
            selectedReturnProgramme: null,
            selectedReturnDate: null,
            selectedReturnTime: null,
            selectedReturnSeat: null,
            availableTrips: [],
            userSolde: 0,
            currentReservation: null
        };

        $(document).on('click', '.modify-btn:not([disabled])', function() {
            const reservationId = $(this).data('id');
            modificationState.reservationId = reservationId;

            // Show loading modal
            Swal.fire({
                title: '<span class="text-lg font-black uppercase tracking-tight">Chargement...</span>',
                html: '<div class="flex items-center justify-center py-8"><div class="animate-spin w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full"></div></div>',
                showConfirmButton: false,
                allowOutsideClick: false,
                width: '480px',
                customClass: { popup: 'rounded-[32px]' }
            });

            // Fetch modification data
            $.get(`/user/booking/reservations/${reservationId}/modification-data`)
                .done(function(data) {
                    if (!data.success) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: data.message || 'Impossible de charger les données',
                            confirmButtonColor: '#1A1D1F',
                            customClass: { popup: 'rounded-[32px]' }
                        });
                        return;
                    }

                    if (!data.can_modify) {
                        Swal.fire({
                            icon: 'error',
                            title: '<span class="text-lg font-black uppercase tracking-tight text-red-600">Action impossible</span>',
                            html: '<p class="text-sm text-gray-600">La modification est impossible moins de 15 minutes avant le départ.</p>',
                            confirmButtonText: 'Compris',
                            confirmButtonColor: '#1A1D1F',
                            customClass: { popup: 'rounded-[32px]', confirmButton: 'rounded-xl px-8 py-3 font-black uppercase tracking-widest text-xs' }
                        });
                        return;
                    }

                    // Store data
                    modificationState.isRoundTrip = data.is_round_trip;
                    modificationState.residualValue = data.residual_value;
                    modificationState.availableTrips = data.available_trips;
                    modificationState.userSolde = data.user_solde;
                    modificationState.currentReservation = data.reservation;
                    modificationState.pairedReservation = data.paired_reservation;

                    // Show modification modal
                    showModificationModal(data);
                })
                .fail(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Impossible de récupérer les détails.',
                        confirmButtonColor: '#1A1D1F',
                        customClass: { popup: 'rounded-[32px]' }
                        });
                });
        });

        function showModificationModal(data) {
            const reservation = data.reservation;
            const isRoundTrip = data.is_round_trip;
            const pairedReservation = data.paired_reservation;

            // Create trip options HTML
            const tripOptions = data.available_trips.map(trip => {
                const isSelected = reservation.programme_id == trip.id || 
                    (reservation.programme && reservation.programme.point_depart == trip.point_depart && reservation.programme.point_arrive == trip.point_arrive);
                return `<option value="${trip.id}" ${isSelected ? 'selected' : ''} data-has-return="${trip.has_return}" data-prix="${trip.prix}" data-depart="${trip.point_depart}" data-arrive="${trip.point_arrive}">
                    ${trip.name} - ${trip.compagnie} (${Number(trip.prix).toLocaleString('fr-FR')} FCFA)
                </option>`;
            }).join('');

            const modalHtml = `
                <div class="text-left space-y-4 py-4" style="max-height: 70vh; overflow-y: auto;">
                    <!-- Current Reservation Info -->
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-200">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">📊 Réservation actuelle</p>
                        <div class="text-xs space-y-1">
                            <p><strong>Référence:</strong> ${reservation.reference}</p>
                            <p><strong>Trajet:</strong> ${reservation.programme?.point_depart || 'N/A'} → ${reservation.programme?.point_arrive || 'N/A'}</p>
                            <p><strong>Date:</strong> ${reservation.date_voyage} | <strong>Heure:</strong> ${reservation.heure_depart || 'N/A'}</p>
                            <p><strong>Place:</strong> ${reservation.seat_number}</p>
                            <p><strong>Montant:</strong> ${Number(reservation.montant).toLocaleString('fr-FR')} FCFA</p>
                        </div>
                    </div>

                    ${isRoundTrip && pairedReservation ? `
                    <div class="bg-orange-50 p-4 rounded-2xl border border-orange-200">
                        <p class="text-[10px] font-black text-orange-600 uppercase tracking-widest mb-2">🔄 Retour</p>
                        <div class="text-xs space-y-1">
                            <p><strong>Référence:</strong> ${pairedReservation.reference}</p>
                            <p><strong>Date:</strong> ${pairedReservation.date_voyage} | <strong>Heure:</strong> ${pairedReservation.heure_depart || 'N/A'}</p>
                            <p><strong>Place:</strong> ${pairedReservation.seat_number}</p>
                        </div>
                    </div>
                    ` : ''}

                    <!-- Residual Value -->
                    <div class="bg-blue-50 p-4 rounded-2xl border border-blue-200">
                        <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">💰 Valeur résiduelle</p>
                        <p class="text-2xl font-black text-blue-600">
                            ${Number(data.residual_value).toLocaleString('fr-FR')} FCFA 
                            <span class="text-sm font-normal text-gray-500">
                                (${data.residual_percentage !== null ? data.residual_percentage + '%' : '-' + Number(data.residual_penalty).toLocaleString('fr-FR') + ' FCFA'})
                            </span>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">${isRoundTrip ? 'Total pour les deux billets' : ''}</p>
                    </div>

                    <hr class="border-gray-300">

                    <!-- NEW RESERVATION SECTION -->
                    <div class="space-y-4">
                        <p class="text-sm font-black text-gray-700 uppercase">✏️ Nouvelle réservation</p>

                        <!-- Trip Selection -->
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">🚌 Trajet</label>
                            <select id="modify-trip" class="w-full p-2 border border-gray-300 rounded-lg text-sm">
                                <option value="">Sélectionner un trajet...</option>
                                ${tripOptions}
                            </select>
                            <p id="modify-trip-warning" class="text-xs text-red-500 mt-1 hidden">⚠️ Ce trajet n'a pas de retour disponible. Modification impossible pour aller-retour.</p>
                        </div>

                        <!-- Date Selection -->
                        <div id="modify-date-container" class="hidden">
                            <label class="block text-xs font-bold text-gray-600 mb-1">📅 Date de voyage</label>
                            <input type="date" id="modify-date" class="w-full p-2 border border-gray-300 rounded-lg text-sm" min="${new Date().toISOString().split('T')[0]}">
                        </div>

                        <!-- Time Selection -->
                        <div id="modify-time-container" class="hidden">
                            <label class="block text-xs font-bold text-gray-600 mb-1">🕒 Heure de départ</label>
                            <select id="modify-time" class="w-full p-2 border border-gray-300 rounded-lg text-sm">
                                <option value="">Chargement...</option>
                            </select>
                        </div>

                        <!-- Seat Selection -->
                        <div id="modify-seat-container" class="hidden">
                            <label class="block text-xs font-bold text-gray-600 mb-2">💺 Sélectionner une place</label>
                            <div id="modify-seat-grid" class="grid grid-cols-7 gap-2">
                                <!-- Seats will be loaded here -->
                            </div>
                            <p class="text-xs text-gray-500 mt-2"><span class="inline-block w-3 h-3 bg-gray-300 rounded"></span> Occupé | <span class="inline-block w-3 h-3 bg-blue-500 rounded"></span> Sélectionné</p>
                        </div>

                        ${isRoundTrip ? `
                        <hr class="border-gray-300">
                        
                        <!-- RETURN SECTION -->
                        <div id="modify-return-section" class="space-y-4 hidden">
                            <p class="text-sm font-black text-orange-600 uppercase">🔄 Retour</p>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">📅 Date retour</label>
                                <input type="date" id="modify-return-date" class="w-full p-2 border border-gray-300 rounded-lg text-sm" min="${new Date().toISOString().split('T')[0]}">
                            </div>

                            <div id="modify-return-time-container" class="hidden">
                                <label class="block text-xs font-bold text-gray-600 mb-1">🕒 Heure retour</label>
                                <select id="modify-return-time" class="w-full p-2 border border-gray-300 rounded-lg text-sm">
                                    <option value="">Chargement...</option>
                                </select>
                            </div>

                            <div id="modify-return-seat-container" class="hidden">
                                <label class="block text-xs font-bold text-gray-600 mb-2">💺 Place retour</label>
                                <div id="modify-return-seat-grid" class="grid grid-cols-7 gap-2"></div>
                            </div>
                        </div>
                        ` : ''}
                    </div>

                    <hr class="border-gray-300">

                    <!-- Delta Summary -->
                    <div id="modify-delta-summary" class="hidden">
                        <div class="bg-green-50 p-4 rounded-2xl border border-green-200">
                            <p class="text-[10px] font-black text-green-600 uppercase tracking-widest mb-2">📊 Résumé</p>
                            <div class="text-sm space-y-1">
                                <p><strong>Nouveau prix total:</strong> <span id="modify-new-price">0</span> FCFA</p>
                                <p><strong>Valeur résiduelle:</strong> -${Number(data.residual_value).toLocaleString('fr-FR')} FCFA</p>
                                <hr class="my-2">
                                <p class="text-lg font-bold"><span id="modify-action-label"></span>: <span id="modify-delta-amount" class="text-green-600"></span> FCFA</p>
                                <p id="modify-wallet-warning" class="text-xs text-red-500 hidden">⚠️ Solde insuffisant (Solde: ${Number(data.user_solde).toLocaleString('fr-FR')} FCFA)</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            Swal.fire({
                title: '<span class="text-lg font-black uppercase tracking-tight">🔄 Modifier la réservation</span>',
                html: modalHtml,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check mr-2"></i> Confirmer la modification',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                width: '700px',
                padding: '2rem',
                showConfirmButton: true,
                preConfirm: () => {
                    return validateAndSubmitModification();
                },
                customClass: {
                    popup: 'rounded-[32px]',
                    confirmButton: 'rounded-xl px-6 py-3 font-black uppercase tracking-widest text-xs',
                    cancelButton: 'rounded-xl px-6 py-3 font-black uppercase tracking-widest text-xs'
                },
                didOpen: () => {
                    setupModificationHandlers();
                    
                    // Pre-fill and trigger loading
                    const currentTripId = $('#modify-trip').val();
                    if (currentTripId) {
                        $('#modify-trip').trigger('change');
                        
                        // Set current date if possible
                        if (reservation.date_voyage) {
                            $('#modify-date').val(reservation.date_voyage).trigger('change');
                        }
                    }
                }
            });
        }

        function setupModificationHandlers() {
            // Trip selection handler
            $('#modify-trip').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const tripId = $(this).val();
                const hasReturn = selectedOption.data('has-return') === true || selectedOption.data('has-return') === 'true';
                
                if (!tripId) {
                    resetModificationForm();
                    return;
                }

                modificationState.selectedTrip = tripId;

                // Check if round-trip and selected trip has no return
                if (modificationState.isRoundTrip && !hasReturn) {
                    $('#modify-trip-warning').removeClass('hidden');
                    $('#modify-date-container').addClass('hidden');
                    return;
                } else {
                    $('#modify-trip-warning').addClass('hidden');
                }

                // Show date picker
                $('#modify-date-container').removeClass('hidden');
                
                // For round trip, show return section
                if (modificationState.isRoundTrip && hasReturn) {
                    $('#modify-return-section').removeClass('hidden');
                }
            });

            // Date selection handler
            $('#modify-date').on('change', function() {
                const date = $(this).val();
                if (!date || !modificationState.selectedTrip) return;

                modificationState.selectedDate = date;
                loadAvailableTimes(modificationState.selectedTrip, date);
            });

            // Time selection handler
            $('#modify-time').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const programmeId = selectedOption.data('programme-id');
                const time = $(this).val();
                const arriveTime = selectedOption.data('heure-arrive');
                
                if (!time || !modificationState.selectedDate) return;

                modificationState.selectedTime = time;
                modificationState.selectedHeureArrive = arriveTime;
                modificationState.selectedProgrammeId = programmeId;
                loadSeats(programmeId || modificationState.selectedTrip, modificationState.selectedDate, time);
            });

            // Return date handler
            $('#modify-return-date').on('change', function() {
                const returnDate = $(this).val();
                if (!returnDate) return;

                modificationState.selectedReturnDate = returnDate;
                loadReturnTimes(returnDate);
            });

            // Return time handler
            $('#modify-return-time').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const programmeId = selectedOption.data('programme-id');
                const time = $(this).val();
                const arriveTime = selectedOption.data('heure-arrive');
                
                if (!time || !modificationState.selectedReturnDate) return;

                modificationState.selectedReturnTime = time;
                modificationState.selectedReturnHeureArrive = arriveTime;
                modificationState.selectedReturnProgrammeId = programmeId;
                loadReturnSeats(programmeId, modificationState.selectedReturnDate, time);
            });
        }

        function loadAvailableTimes(programmeId, date) {
            $('#modify-time').html('<option value="">Chargement...</option>');
            $('#modify-time-container').removeClass('hidden');

            $.get(`/user/booking/programmes/${programmeId}/available-times?date=${date}`)
                .done(function(data) {
                    if (data.success && data.heures.length > 0) {
                        const options = data.heures.map(h => 
                            `<option value="${h.heure_depart}" data-programme-id="${h.programme_id}" data-heure-arrive="${h.heure_arrive}">${h.heure_depart} → ${h.heure_arrive}</option>`
                        ).join('');
                        $('#modify-time').html('<option value="">Sélectionner une heure...</option>' + options);
                        
                        // Try auto-select if current reservation time matches
                        if (modificationState.currentReservation.heure_depart) {
                            $('#modify-time').val(modificationState.currentReservation.heure_depart).trigger('change');
                        }
                    } else {
                        $('#modify-time').html('<option value="">Aucune heure disponible</option>');
                    }
                })
                .fail(function() {
                    $('#modify-time').html('<option value="">Erreur de chargement</option>');
                });
        }

        function loadSeats(programmeId, date, heure) {
            $('#modify-seat-grid').html('<div class="col-span-7 text-center text-sm text-gray-500">Chargement...</div>');
            $('#modify-seat-container').removeClass('hidden');

            $.get(`/user/booking/programmes/${programmeId}/seats?date=${date}&heure=${heure}`)
                .done(function(data) {
                    if (data.success) {
                        renderSeatGrid(data.seats, '#modify-seat-grid', 'seat');
                    } else {
                        $('#modify-seat-grid').html('<div class="col-span-7 text-center text-sm text-red-500">Erreur</div>');
                    }
                })
                .fail(function() {
                    $('#modify-seat-grid').html('<div class="col-span-7 text-center text-sm text-red-500">Erreur de chargement</div>');
                });
        }

        function loadReturnTimes(date) {
            const tripOption = $('#modify-trip').find('option:selected');
            const depart = tripOption.data('arrive'); // Return is reverse
            const arrive = tripOption.data('depart');

            // Find return programme
            const returnTrip = modificationState.availableTrips.find(t => 
                t.point_depart === depart && t.point_arrive === arrive
            );

            if (!returnTrip) {
                $('#modify-return-time').html('<option value="">Aucun retour trouvé</option>');
                return;
            }

            modificationState.selectedReturnProgramme = returnTrip.id;
            
            $('#modify-return-time').html('<option value="">Chargement...</option>');
            $('#modify-return-time-container').removeClass('hidden');

            $.get(`/user/booking/programmes/${returnTrip.id}/available-times?date=${date}`)
                .done(function(data) {
                    if (data.success && data.heures.length > 0) {
                        const options = data.heures.map(h => 
                            `<option value="${h.heure_depart}" data-programme-id="${h.programme_id}" data-heure-arrive="${h.heure_arrive}">${h.heure_depart} → ${h.heure_arrive}</option>`
                        ).join('');
                        $('#modify-return-time').html('<option value="">Sélectionner une heure...</option>' + options);

                        // Try auto-select return time
                        if (modificationState.pairedReservation && modificationState.pairedReservation.heure_depart) {
                            $('#modify-return-time').val(modificationState.pairedReservation.heure_depart).trigger('change');
                        }
                    } else {
                        $('#modify-return-time').html('<option value="">Aucun retour disponible</option>');
                    }
                })
                .fail(function() {
                    $('#modify-return-time').html('<option value="">Erreur</option>');
                });
        }

        function loadReturnSeats(programmeId, date, heure) {
            $('#modify-return-seat-grid').html('<div class="col-span-7 text-center text-sm text-gray-500">Chargement...</div>');
            $('#modify-return-seat-container').removeClass('hidden');

            $.get(`/user/booking/programmes/${programmeId}/seats?date=${date}&heure=${heure}`)
                .done(function(data) {
                    if (data.success) {
                        renderSeatGrid(data.seats, '#modify-return-seat-grid', 'return-seat');
                    } else {
                        $('#modify-return-seat-grid').html('<div class="col-span-7 text-center text-sm text-red-500">Erreur</div>');
                    }
                })
                .fail(function() {
                    $('#modify-return-seat-grid').html('<div class="col-span-7 text-center text-sm text-red-500">Erreur de chargement</div>');
                });
        }

        function renderSeatGrid(seats, containerId, type) {
            // Find if current reservation seat should be pre-selected
            let preSelectedSeat = null;
            if (type === 'seat') {
                if (modificationState.currentReservation.programme_id == modificationState.selectedProgrammeId &&
                    modificationState.currentReservation.date_voyage == modificationState.selectedDate &&
                    modificationState.currentReservation.heure_depart == modificationState.selectedTime) {
                    preSelectedSeat = modificationState.currentReservation.seat_number;
                }
            } else if (type === 'return-seat') { // Changed from 'return' to 'return-seat' to match data-type
                if (modificationState.pairedReservation &&
                    modificationState.pairedReservation.programme_id == modificationState.selectedReturnProgrammeId &&
                    modificationState.pairedReservation.date_voyage == modificationState.selectedReturnDate &&
                    modificationState.pairedReservation.heure_depart == modificationState.selectedReturnTime) {
                    preSelectedSeat = modificationState.pairedReservation.seat_number;
                }
            }

            const html = seats.map(seat => {
                const isReserved = !seat.available;
                const isCurrentSeat = seat.number == preSelectedSeat;

                // If it's the current seat of the reservation being modified, treat it as available and selected
                const bgClass = (seat.available || isCurrentSeat) ? 'bg-white hover:bg-blue-100' : 'bg-gray-300 cursor-not-allowed';
                const borderClass = 'border-2 border-gray-300';
                const selectedClass = isCurrentSeat ? 'bg-blue-500 text-white border-blue-600' : '';
                if (isCurrentSeat) {
                    if (type === 'seat') modificationState.selectedSeat = seat.number;
                    else modificationState.selectedReturnSeat = seat.number;
                }

                return `
                    <button type="button"
                            class="seat-btn ${bgClass} ${borderClass} ${selectedClass} rounded-lg p-2 text-xs font-bold transition-all"
                            data-seat="${seat.number}"
                            data-type="${type}"
                            ${(!seat.available && !isCurrentSeat) ? 'disabled' : ''}>
                        ${seat.number}
                    </button>
                `;
            }).join('');

            $(containerId).html(html);

            // If seat was pre-selected, trigger delta
            if (preSelectedSeat) {
                calculateDelta();
            }

            // Seat click handler
            $(containerId).find('.seat-btn:not([disabled])').on('click', function() {
                const seatNumber = $(this).data('seat');
                const seatType = $(this).data('type');

                // Remove previous selection
                $(containerId).find('.seat-btn').removeClass('bg-blue-500 text-white border-blue-600').addClass('bg-white');
                
                // Highlight selected
                $(this).removeClass('bg-white').addClass('bg-blue-500 text-white border-blue-600');

                // Update state
                if (seatType === 'seat') {
                    modificationState.selectedSeat = seatNumber;
                } else {
                    modificationState.selectedReturnSeat = seatNumber;
                }

                // Calculate delta after seat selection                                
                calculateDelta();
            });
        }

        function calculateDelta() {
            // Check if we have enough info
            if (!modificationState.selectedSeat || !modificationState.selectedProgrammeId) {
                return;
            }

            // For round trip, need both seats
            if (modificationState.isRoundTrip && (!modificationState.selectedReturnSeat || !modificationState.selectedReturnProgrammeId)) {
                return;
            }

            const requestData = {
                programme_id: modificationState.selectedProgrammeId,
                date_voyage: modificationState.selectedDate,
                heure_depart: modificationState.selectedTime,
                seat_number: modificationState.selectedSeat,
                heure_arrive: modificationState.selectedHeureArrive || ''
            };

            if (modificationState.isRoundTrip) {
                requestData.return_programme_id = modificationState.selectedReturnProgrammeId;
                requestData.return_date_voyage = modificationState.selectedReturnDate;
                requestData.return_heure_depart = modificationState.selectedReturnTime;
                requestData.return_seat_number = modificationState.selectedReturnSeat;
                requestData.return_heure_arrive = modificationState.selectedReturnHeureArrive || '';
            }

            $.post(`/user/booking/reservations/${modificationState.reservationId}/calculate-delta`, requestData)
                .done(function(data) {
                    if (data.success) {
                        $('#modify-new-price').text(Number(data.new_total).toLocaleString('fr-FR'));
                        $('#modify-delta-amount').text(Number(data.delta).toLocaleString('fr-FR'));
                        
                        if (data.action === 'debit') {
                            $('#modify-action-label').text('À PAYER');
                            $('#modify-delta-amount').removeClass('text-green-600').addClass('text-red-600');
                            
                            if (!data.wallet_sufficient) {
                                $('#modify-wallet-warning').removeClass('hidden');
                            } else {
                                $('#modify-wallet-warning').addClass('hidden');
                            }
                        } else if (data.action === 'credit') {
                            $('#modify-action-label').text('À CRÉDITER');
                            $('#modify-delta-amount').removeClass('text-red-600').addClass('text-green-600');
                            $('#modify-wallet-warning').addClass('hidden');
                        } else {
                            $('#modify-action-label').text('Différence');
                            $('#modify-delta-amount').text('0');
                            $('#modify-wallet-warning').addClass('hidden');
                        }

                        $('#modify-delta-summary').removeClass('hidden');
                        modificationState.deltaData = data;
                    }
                })
                .fail(function() {
                    console.error('Failed to calculate delta');
                });
        }

        function validateAndSubmitModification() {
            // Validation
            if (!modificationState.selectedProgrammeId || !modificationState.selectedDate || !modificationState.selectedSeat) {
                Swal.showValidationMessage('Veuillez sélectionner un trajet, une date et une place');
                return false;
            }

            if (modificationState.isRoundTrip && (!modificationState.selectedReturnProgrammeId || !modificationState.selectedReturnDate || !modificationState.selectedReturnSeat)) {
                Swal.showValidationMessage('Veuillez compléter les informations de retour');
                return false;
            }

            if (modificationState.deltaData && !modificationState.deltaData.wallet_sufficient) {
                Swal.showValidationMessage('Solde wallet insuffisant');
                return false;
            }

            // Prepare submission data
            const submitData = {
                programme_id: modificationState.selectedProgrammeId,
                date_voyage: modificationState.selectedDate,
                heure_depart: modificationState.selectedTime,
                seat_number: modificationState.selectedSeat,
                heure_arrive: modificationState.selectedHeureArrive || ''
            };

            if (modificationState.isRoundTrip) {
                submitData.return_programme_id = modificationState.selectedReturnProgrammeId;
                submitData.return_date_voyage = modificationState.selectedReturnDate;
                submitData.return_heure_depart = modificationState.selectedReturnTime;
                submitData.return_seat_number = modificationState.selectedReturnSeat;
                submitData.return_heure_arrive = modificationState.selectedReturnHeureArrive || '';
            }

            // Submit modification
            return $.ajax({
                url: `/user/booking/reservations/${modificationState.reservationId}/modify`,
                method: 'POST',
                data: submitData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).then(function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '<span class="text-lg font-black uppercase tracking-tight text-green-600">✅ Modification réussie !</span>',
                        html: `
                            <div class="text-left space-y-2 py-4">
                                <p class="text-sm text-gray-600">Votre réservation a été modifiée avec succès.</p>
                                <div class="bg-blue-50 p-3 rounded-xl border border-blue-200 text-xs">
                                    <p><strong>Nouvelle référence:</strong> ${response.new_reservation?.reference || 'N/A'}</p>
                                    <p><strong>Nouvelle date:</strong> ${modificationState.selectedDate}</p>
                                    <p><strong>Nouvelle place:</strong> ${modificationState.selectedSeat}</p>
                                </div>
                            </div>
                        `,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#10b981',
                        customClass: {
                            popup: 'rounded-[32px]',
                            confirmButton: 'rounded-xl px-8 py-3 font-black uppercase tracking-widest text-xs'
                        }
                    }).then(() => {
                        // Reload page to show updated reservation
                        window.location.reload();
                    });
                } else {
                    throw new Error(response.message || 'Erreur de modification');
                }
            }).catch(function(error) {
                const errorMsg = error.responseJSON?.message || error.message || 'Une erreur est survenue';
                Swal.showValidationMessage(errorMsg);
                return false;
            });
        }

        function resetModificationForm() {
            $('#modify-date-container, #modify-time-container, #modify-seat-container, #modify-return-section, #modify-delta-summary').addClass('hidden');
            $('#modify-trip-warning').addClass('hidden');
            modificationState.selectedTrip = null;
            modificationState.selectedDate = null;
            modificationState.selectedTime = null;
            modificationState.selectedSeat = null;
        }

        // =========================================
        // AUTO-DISABLE BUTTONS (every 30 seconds)
        // =========================================
        function checkAndDisableButtons() {
            const now = new Date();
            const fifteenMinutesMs = 15 * 60 * 1000;

            document.querySelectorAll('.cancel-btn:not([disabled]), .modify-btn:not([disabled])').forEach(btn => {
                const departure = new Date(btn.dataset.departure);
                if (departure - now < fifteenMinutesMs) {
                    btn.disabled = true;
                    btn.classList.remove('bg-red-500', 'bg-blue-500', 'text-white', 'hover:bg-red-600', 'hover:bg-blue-600', 'hover:scale-110', 'active:scale-95');
                    btn.classList.add('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
                }
            });
        }
        
        setInterval(checkAndDisableButtons, 30000);
    });
</script>
@endpush

@stop