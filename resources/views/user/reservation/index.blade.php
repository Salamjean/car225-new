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
                        <option value="terminee" {{ request('statut') == 'terminee' ? 'selected' : '' }}>Terminée</option>
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
                    <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest pl-8">Transaction</th>
                    <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Itinéraire & Compagnie</th>
                    <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Départ</th>
                    <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Billets</th>
                    <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Passager Principal</th>
                    <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Total</th>
                    <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Statut</th>
                    <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center pr-8">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($reservations as $reservation)
                <tr class="group hover:bg-[#fff5f2] transition-all duration-300">
                    
                    <td class="px-6 py-5 pl-8">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white border-2 border-dashed border-gray-200 flex items-center justify-center text-gray-300 group-hover:border-[#e94f1b] group-hover:text-[#e94f1b] transition-colors">
                                <i class="fas fa-receipt text-lg"></i>
                            </div>
                            <div>
                                <span class="block text-xs font-mono font-semibold text-gray-600 bg-gray-100 px-2 py-1 rounded-md mb-1 group-hover:bg-white group-hover:shadow-sm transition-all">
                                    {{ $reservation->payment_transaction_id }}
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
                                <div>
                                    <span class="text-sm font-medium text-[#1A1D1F] block">{{ $reservation->programme->point_depart }}</span>
                                </div>
                                <div class="w-12 h-[2px] bg-gray-200 relative flex items-center justify-center mx-2">
                                    <i class="fas fa-bus text-[8px] text-gray-400 absolute bg-white px-1"></i>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-[#1A1D1F] block">{{ $reservation->programme->point_arrive }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] font-bold text-gray-900 tracking-wide">{{ $reservation->programme->compagnie->sigle ?? '' }}</span>
                                <span class="text-[10px] font-light text-gray-500 tracking-wide ml-1">{{ $reservation->programme->compagnie->name }}</span>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div class="flex flex-col items-center justify-center bg-gray-50 border border-gray-100 rounded-xl w-12 h-12 group-hover:bg-white group-hover:border-[#e94f1b]/20 group-hover:shadow-md transition-all">
                                <span class="text-[8px] font-semibold text-[#e94f1b] uppercase leading-none mt-1">
                                    {{ Str::upper(\Carbon\Carbon::parse($reservation->date_voyage)->locale('fr')->translatedFormat('M')) }}
                                </span>
                                <span class="text-lg font-bold text-gray-800 leading-none">
                                    {{ \Carbon\Carbon::parse($reservation->date_voyage)->format('d') }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($reservation->heure_depart ?? $reservation->programme->heure_depart)->format('H:i') }}</p>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-5 text-center">
                        <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-50 text-[#e94f1b] font-black text-xs border border-orange-100">
                            {{ $reservation->tickets_count }}
                        </div>
                    </td>

                    <td class="px-6 py-5 text-center">
                        <div class="flex flex-col items-center">
                            <span class="text-xs font-bold text-gray-800">{{ $reservation->passager_nom }} {{ $reservation->passager_prenom }}</span>
                            @if($reservation->tickets_count > 1)
                                <span class="text-[9px] text-gray-400 font-medium">+ {{ $reservation->tickets_count - 1 }} autre(s)</span>
                            @endif
                        </div>
                    </td>

                    <td class="px-6 py-5 text-right">
                        <p class="text-sm font-bold text-[#1A1D1F]">{{ number_format($reservation->total_group_amount, 0, ',', ' ') }}</p>
                        <p class="text-[9px] font-medium text-gray-400 uppercase tracking-wider">FCFA</p>
                    </td>

                    <td class="px-6 py-5 text-center">
                        @if(in_array($reservation->statut, ['confirmee', 'terminee']) && $reservation->mission && $reservation->mission->statut == 'en_cours')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-50 text-purple-600 text-[10px] font-bold rounded-lg uppercase tracking-widest border border-purple-100 italic">
                                <span class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-pulse"></span> En voyage
                            </span>
                        @elseif($reservation->statut == 'confirmee')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-600 text-[10px] font-bold rounded-lg uppercase tracking-widest border border-green-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Confirmé
                            </span>
                        @elseif($reservation->statut == 'terminee')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 text-[10px] font-bold rounded-lg uppercase tracking-widest border border-blue-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Terminé
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 text-[10px] font-bold rounded-lg uppercase tracking-widest border border-red-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> {{ ucfirst($reservation->statut) }}
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-5 text-center pr-8">
                        <a href="{{ route('user.reservation.group', $reservation->payment_transaction_id) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-[#e94f1b] text-white text-[10px] font-black rounded-xl uppercase tracking-widest hover:bg-[#d44518] transition-all shadow-md shadow-orange-200">
                            <i class="fas fa-ticket-alt"></i> Voir les billets
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-8 py-24 text-center">
                        <div class="flex flex-col items-center animate-fade-in-up">
                            <div class="w-24 h-24 bg-orange-50 rounded-full flex items-center justify-center text-[#e94f1b] mb-6 shadow-xl shadow-orange-100">
                                <i class="fas fa-ticket-alt text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 uppercase tracking-tight">Aucun voyage pour le moment</h3>
                            <p class="text-gray-400 font-medium mb-8 max-w-sm mx-auto">Votre historique de réservations est vide. Prêt à partir à l'aventure ?</p>
                            <a href="{{ route('reservation.create') }}" class="px-10 py-4 bg-[#e94f1b] text-white rounded-2xl font-bold text-sm uppercase tracking-widest shadow-lg shadow-[#e94f1b]/30 hover:-translate-y-1 transition-all">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle Filters (Uniquement sur l'index)
        const toggleBtn = document.getElementById('toggleFilters');
        const filtersSection = document.getElementById('filtersSection');
        const chevron = document.getElementById('filterChevron');

        if(toggleBtn && filtersSection) {
            toggleBtn.addEventListener('click', () => {
                filtersSection.classList.toggle('hidden');
                chevron.classList.toggle('rotate-180');
            });
        }
    });
</script>
@include('user.reservation.scripts')
@endpush
@endsection
