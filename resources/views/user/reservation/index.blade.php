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
                                    <a href="{{ route('reservations.download', $reservation) }}" class="w-10 h-10 bg-gray-900 text-white rounded-xl flex items-center justify-center transition-transform hover:scale-110 active:scale-95 shadow-lg shadow-gray-900/10" title="Télécharger le billet">
                                        <i class="fas fa-file-pdf text-sm"></i>
                                    </a>
                                @endif
                                <a href="{{ route('reservations.show', $reservation) }}" class="w-10 h-10 bg-white border border-gray-100 text-gray-900 rounded-xl flex items-center justify-center transition-transform hover:scale-110 active:scale-95 shadow-sm" title="Voir les détails">
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
    });
</script>
@endpush

@stop