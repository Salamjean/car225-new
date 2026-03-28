@extends('user.layouts.template')

@section('content')
<div class="min-h-screen bg-[#F8F9FA] py-4 sm:py-8 px-3 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-[1100px]">

        {{-- Header --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-3xl font-black text-[#1A1D1F] tracking-tight">Mes Rechargements</h1>
                <p class="text-gray-500 font-medium mt-1">Historique complet de vos rechargements de portefeuille</p>
            </div>
            <a href="{{ route('user.wallet.index') }}"
               class="inline-flex items-center gap-2 bg-[#1A1D1F] text-white px-5 py-3 rounded-2xl font-bold text-sm hover:bg-[#e94f1b] transition-all duration-300 shadow-md">
                <i class="fas fa-wallet text-sm"></i>
                Retour au portefeuille
            </a>
        </div>

        {{-- Statistiques --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

            {{-- Total rechargé --}}
            <div class="bg-white rounded-[24px] border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-arrow-down text-green-500"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-0.5">Total rechargé</p>
                    <p class="text-2xl font-black text-[#1A1D1F]">{{ number_format($totalRecharge, 0, ',', ' ') }} <span class="text-sm font-bold text-gray-400">FCFA</span></p>
                </div>
            </div>

            {{-- Total opérations --}}
            <div class="bg-white rounded-[24px] border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-layer-group text-blue-500"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-0.5">Total opérations</p>
                    <p class="text-2xl font-black text-[#1A1D1F]">{{ $totalCount }}</p>
                </div>
            </div>

            {{-- En attente --}}
            <div class="bg-white rounded-[24px] border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clock text-amber-500"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-0.5">En attente</p>
                    <p class="text-2xl font-black text-[#1A1D1F]">{{ $pendingCount }}</p>
                </div>
            </div>
        </div>

        {{-- Filtres & Recherche --}}
        <div class="bg-white rounded-[24px] border border-gray-100 shadow-sm p-5 mb-6">
            <form method="GET" action="{{ route('user.wallet.recharges') }}" id="filterForm">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                    {{-- Recherche --}}
                    <div class="lg:col-span-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Référence, méthode, description..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent text-sm font-medium outline-none transition-all">
                    </div>

                    {{-- Filtre statut --}}
                    <div class="relative">
                        <select name="statut"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent text-sm font-medium outline-none transition-all appearance-none">
                            <option value="">Tous les statuts</option>
                            <option value="completed" {{ request('statut') == 'completed' ? 'selected' : '' }}>✅ Complété</option>
                            <option value="pending"   {{ request('statut') == 'pending'   ? 'selected' : '' }}>⏳ En attente</option>
                            <option value="failed"    {{ request('statut') == 'failed'    ? 'selected' : '' }}>❌ Échoué</option>
                            <option value="cancelled" {{ request('statut') == 'cancelled' ? 'selected' : '' }}>🚫 Annulé</option>
                        </select>
                        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </div>
                    </div>

                    {{-- Boutons --}}
                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-1 flex items-center justify-center gap-2 bg-[#e94f1b] text-white py-3 px-4 rounded-xl font-bold text-sm hover:bg-[#d33d0f] transition-all">
                            <i class="fas fa-filter text-xs"></i> Filtrer
                        </button>
                        <a href="{{ route('user.wallet.recharges') }}"
                            class="flex items-center justify-center gap-2 bg-gray-100 text-gray-600 py-3 px-4 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all">
                            <i class="fas fa-times text-xs"></i>
                        </a>
                    </div>
                </div>

                {{-- Filtres dates (affichés en 2e ligne) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                    <div class="relative">
                        <label class="absolute -top-2 left-3 text-[10px] font-bold text-gray-400 bg-white px-1">Du</label>
                        <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent text-sm font-medium outline-none transition-all">
                    </div>
                    <div class="relative">
                        <label class="absolute -top-2 left-3 text-[10px] font-bold text-gray-400 bg-white px-1">Au</label>
                        <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent text-sm font-medium outline-none transition-all">
                    </div>
                </div>

                {{-- Résumé des filtres actifs --}}
                @if(request()->hasAny(['search', 'statut', 'date_debut', 'date_fin']))
                    <div class="mt-3 flex flex-wrap gap-2 items-center">
                        <span class="text-xs font-bold text-gray-400">Filtres actifs :</span>
                        @if(request('search'))
                            <span class="inline-flex items-center gap-1 text-xs font-bold bg-[#e94f1b]/10 text-[#e94f1b] px-2.5 py-1 rounded-full">
                                <i class="fas fa-search fa-xs"></i> "{{ request('search') }}"
                            </span>
                        @endif
                        @if(request('statut'))
                            <span class="inline-flex items-center gap-1 text-xs font-bold bg-blue-50 text-blue-600 px-2.5 py-1 rounded-full">
                                <i class="fas fa-tag fa-xs"></i> {{ ucfirst(request('statut')) }}
                            </span>
                        @endif
                        @if(request('date_debut'))
                            <span class="inline-flex items-center gap-1 text-xs font-bold bg-green-50 text-green-600 px-2.5 py-1 rounded-full">
                                <i class="fas fa-calendar fa-xs"></i> Depuis {{ \Carbon\Carbon::parse(request('date_debut'))->format('d/m/Y') }}
                            </span>
                        @endif
                        @if(request('date_fin'))
                            <span class="inline-flex items-center gap-1 text-xs font-bold bg-green-50 text-green-600 px-2.5 py-1 rounded-full">
                                <i class="fas fa-calendar fa-xs"></i> Jusqu'au {{ \Carbon\Carbon::parse(request('date_fin'))->format('d/m/Y') }}
                            </span>
                        @endif
                        <span class="text-xs text-gray-400 font-medium">— {{ $transactions->total() }} résultat(s)</span>
                    </div>
                @endif
            </form>
        </div>

        {{-- Tableau --}}
        <div class="bg-white rounded-[24px] border border-gray-100 shadow-sm overflow-hidden">

            @if($transactions->isEmpty())
                {{-- État vide --}}
                <div class="py-16 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-history text-2xl text-gray-300"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-700 mb-1">Aucun rechargement trouvé</h3>
                    <p class="text-sm text-gray-400 max-w-xs mx-auto">
                        @if(request()->hasAny(['search', 'statut', 'date_debut', 'date_fin']))
                            Modifiez vos critères de recherche ou
                            <a href="{{ route('user.wallet.recharges') }}" class="text-[#e94f1b] font-bold">réinitialisez les filtres</a>.
                        @else
                            Vous n'avez effectué aucun rechargement pour le moment.
                        @endif
                    </p>
                    @if(!request()->hasAny(['search', 'statut', 'date_debut', 'date_fin']))
                        <a href="{{ route('user.wallet.index') }}"
                            class="inline-flex items-center gap-2 mt-4 bg-[#e94f1b] text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-[#d33d0f] transition-all">
                            <i class="fas fa-plus"></i> Recharger maintenant
                        </a>
                    @endif
                </div>

            @else

                {{-- Desktop table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="text-left px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.15em]">Référence</th>
                                <th class="text-left px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.15em]">Montant</th>
                                <th class="text-left px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.15em]">Méthode</th>
                                <th class="text-left px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.15em]">Statut</th>
                                <th class="text-left px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.15em]">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($transactions as $tx)
                                <tr class="hover:bg-gray-50/60 transition-colors group">

                                    {{-- Référence --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-arrow-down text-green-500 text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-800 font-mono">{{ $tx->reference }}</p>
                                                @if($tx->description)
                                                    <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($tx->description, 40) }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Montant --}}
                                    <td class="px-6 py-4">
                                        <span class="text-base font-black text-green-600">
                                            +{{ number_format($tx->amount, 0, ',', ' ') }}
                                        </span>
                                        <span class="text-xs text-gray-400 font-bold ml-1">FCFA</span>
                                    </td>

                                    {{-- Méthode --}}
                                    <td class="px-6 py-4">
                                        @php
                                            $method = $tx->payment_method ?? 'cinetpay';
                                            $methodLabel = match(strtolower($method)) {
                                                'cinetpay'         => ['label' => 'CinetPay',    'bg' => 'bg-indigo-50',  'text' => 'text-indigo-600'],
                                                'wave'             => ['label' => 'Wave',         'bg' => 'bg-sky-50',     'text' => 'text-sky-600'],
                                                'orange_money'     => ['label' => 'Orange Money', 'bg' => 'bg-orange-50',  'text' => 'text-orange-600'],
                                                'mtn_mobile_money' => ['label' => 'MTN Money',    'bg' => 'bg-yellow-50',  'text' => 'text-yellow-600'],
                                                default            => ['label' => ucfirst($method), 'bg' => 'bg-gray-50', 'text' => 'text-gray-500'],
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $methodLabel['bg'] }} {{ $methodLabel['text'] }}">
                                            {{ $methodLabel['label'] }}
                                        </span>
                                    </td>

                                    {{-- Statut --}}
                                    <td class="px-6 py-4">
                                        @php
                                            $statusMap = [
                                                'completed' => ['label' => 'Complété',   'dot' => 'bg-green-500', 'bg' => 'bg-green-50',  'text' => 'text-green-700'],
                                                'pending'   => ['label' => 'En attente', 'dot' => 'bg-amber-400', 'bg' => 'bg-amber-50',  'text' => 'text-amber-700'],
                                                'failed'    => ['label' => 'Échoué',     'dot' => 'bg-red-500',   'bg' => 'bg-red-50',    'text' => 'text-red-700'],
                                                'cancelled' => ['label' => 'Annulé',     'dot' => 'bg-gray-400',  'bg' => 'bg-gray-50',   'text' => 'text-gray-500'],
                                            ];
                                            $s = $statusMap[$tx->status] ?? $statusMap['pending'];
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold {{ $s['bg'] }} {{ $s['text'] }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $s['dot'] }} {{ $tx->status === 'pending' ? 'animate-pulse' : '' }}"></span>
                                            {{ $s['label'] }}
                                        </span>
                                    </td>

                                    {{-- Date --}}
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-bold text-gray-700">{{ $tx->created_at->format('d/m/Y') }}</p>
                                        <p class="text-xs text-gray-400">{{ $tx->created_at->format('H:i') }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile cards --}}
                <div class="md:hidden divide-y divide-gray-50">
                    @foreach($transactions as $tx)
                        @php
                            $statusMap = [
                                'completed' => ['label' => 'Complété',   'dot' => 'bg-green-500', 'text' => 'text-green-700', 'bg' => 'bg-green-50'],
                                'pending'   => ['label' => 'En attente', 'dot' => 'bg-amber-400', 'text' => 'text-amber-700', 'bg' => 'bg-amber-50'],
                                'failed'    => ['label' => 'Échoué',     'dot' => 'bg-red-500',   'text' => 'text-red-700',   'bg' => 'bg-red-50'],
                                'cancelled' => ['label' => 'Annulé',     'dot' => 'bg-gray-400',  'text' => 'text-gray-500',  'bg' => 'bg-gray-50'],
                            ];
                            $s = $statusMap[$tx->status] ?? $statusMap['pending'];
                        @endphp
                        <div class="p-4 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-arrow-down text-green-500 text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="text-xs font-bold text-gray-800 font-mono truncate">{{ $tx->reference }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $tx->created_at->format('d/m/Y · H:i') }}</p>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <p class="text-base font-black text-green-600">+{{ number_format($tx->amount, 0, ',', ' ') }} <span class="text-xs text-gray-400">F</span></p>
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full mt-0.5 {{ $s['bg'] }} {{ $s['text'] }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $s['dot'] }}"></span>
                                            {{ $s['label'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($transactions->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between gap-4 flex-wrap">
                        <p class="text-xs font-bold text-gray-400">
                            Page {{ $transactions->currentPage() }} sur {{ $transactions->lastPage() }}
                            · {{ $transactions->total() }} résultat(s)
                        </p>
                        <div class="flex items-center gap-1">
                            {{-- Précédent --}}
                            @if($transactions->onFirstPage())
                                <span class="px-3 py-2 rounded-xl text-sm font-bold text-gray-300 bg-gray-50 cursor-not-allowed">
                                    <i class="fas fa-chevron-left text-xs"></i>
                                </span>
                            @else
                                <a href="{{ $transactions->previousPageUrl() }}"
                                    class="px-3 py-2 rounded-xl text-sm font-bold text-gray-600 bg-gray-50 hover:bg-gray-100 transition-all">
                                    <i class="fas fa-chevron-left text-xs"></i>
                                </a>
                            @endif

                            {{-- Pages numérotées --}}
                            @foreach($transactions->getUrlRange(max(1, $transactions->currentPage()-2), min($transactions->lastPage(), $transactions->currentPage()+2)) as $page => $url)
                                <a href="{{ $url }}"
                                    class="px-3 py-2 rounded-xl text-sm font-bold transition-all
                                        {{ $page == $transactions->currentPage()
                                            ? 'bg-[#e94f1b] text-white shadow-sm'
                                            : 'text-gray-600 bg-gray-50 hover:bg-gray-100' }}">
                                    {{ $page }}
                                </a>
                            @endforeach

                            {{-- Suivant --}}
                            @if($transactions->hasMorePages())
                                <a href="{{ $transactions->nextPageUrl() }}"
                                    class="px-3 py-2 rounded-xl text-sm font-bold text-gray-600 bg-gray-50 hover:bg-gray-100 transition-all">
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </a>
                            @else
                                <span class="px-3 py-2 rounded-xl text-sm font-bold text-gray-300 bg-gray-50 cursor-not-allowed">
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="px-6 py-3 border-t border-gray-50">
                        <p class="text-xs text-gray-400 font-bold">{{ $transactions->total() }} rechargement(s) au total</p>
                    </div>
                @endif

            @endif
        </div>

    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap');
body { font-family: 'Outfit', sans-serif; }
</style>
@endsection
