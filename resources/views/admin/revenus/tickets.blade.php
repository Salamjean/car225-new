@extends('admin.layouts.template')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto" style="max-width: 1400px;">

        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider">
                        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-blue-600 transition">Dashboard</a></li>
                        <li class="text-gray-300">/</li>
                        <li class="text-blue-600">Revenus Tickets</li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Détails des Revenus Rechargements</h1>
                <p class="text-gray-500 mt-1">Analyse approfondie des ventes de quotas aux compagnies</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition shadow-sm text-sm font-bold">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-700 to-indigo-800 rounded-2xl p-6 shadow-lg text-white">
                <p class="text-xs font-bold text-white/70 uppercase mb-1">Portefeuille Admin</p>
                <h4 class="text-2xl font-black">{{ number_format($portefeuilleAdmin, 0, ',', ' ') }} <span class="text-sm font-normal">FCFA</span></h4>
                <div class="mt-4 text-[10px] bg-white/20 rounded-lg px-2 py-1 w-fit font-bold uppercase tracking-widest">Global</div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-coins text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase">Chiffre Tickets</p>
                        <h4 class="text-xl font-black text-gray-900">{{ number_format($totalTicketRevenue, 0, ',', ' ') }} FCFA</h4>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-history text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase">Total Rechargements</p>
                        <h4 class="text-xl font-black text-gray-900">{{ $totalRecharges }} opérations</h4>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase">Panier Moyen</p>
                        <h4 class="text-xl font-black text-gray-900">{{ number_format($averageRecharge, 0, ',', ' ') }} FCFA</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Company Revenue Breakdown -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden mb-8">
            <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-gray-900">Répartition des Revenus par Compagnie</h3>
                    <p class="text-xs text-gray-500 font-medium">Cumul des rechargements pour la période sélectionnée</p>
                </div>
                <div class="px-4 py-2 bg-blue-50 text-blue-600 rounded-xl text-xs font-black uppercase tracking-widest">
                    {{ count($companyStats) }} compagnies actives
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-0 divide-x divide-y divide-gray-50">
                @forelse($companyStats as $stat)
                <div class="p-8 hover:bg-gray-50 transition group">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-blue-600 text-white rounded-2xl flex items-center justify-center font-black text-lg shadow-lg shadow-blue-200 group-hover:scale-110 transition-transform">
                            {{ substr($stat->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-black text-gray-900 truncate uppercase">{{ $stat->name }}</h4>
                            <div class="text-[10px] text-gray-400 font-bold tracking-widest uppercase">{{ $stat->count }} rechargements</div>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black text-blue-600">{{ number_format($stat->total_revenue, 0, ',', ' ') }}</span>
                        <span class="text-xs font-bold text-gray-400">FCFA</span>
                    </div>
                    <div class="mt-4 w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                        @php
                            $percent = $totalTicketRevenue > 0 ? ($stat->total_revenue / $totalTicketRevenue) * 100 : 0;
                        @endphp
                        <div class="bg-blue-600 h-full rounded-full transition-all duration-1000" style="width: {{ $percent }}%"></div>
                    </div>
                    <div class="mt-2 text-[10px] text-right font-black text-blue-400 tracking-tighter">{{ round($percent, 1) }}% du total</div>
                </div>
                @empty
                <div class="col-span-full py-12 text-center text-gray-400 font-bold uppercase tracking-widest">
                    Aucune donnée disponible
                </div>
                @endforelse
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 mb-8">
            <!-- History -->
            <div>



        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <form action="{{ route('admin.revenus.tickets') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase mb-2">Compagnie</label>
                    <select name="compagnie_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 transition">
                        <option value="">Toutes les compagnies</option>
                        @foreach($compagnies as $comp)
                            <option value="{{ $comp->id }}" {{ request('compagnie_id') == $comp->id ? 'selected' : '' }}>{{ $comp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase mb-2">Date début</label>
                    <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase mb-2">Date fin</label>
                    <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 transition">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 text-white font-bold py-3 px-4 rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                        <i class="fas fa-filter mr-2"></i> Filtrer
                    </button>
                    <a href="{{ route('admin.revenus.tickets') }}" class="p-3 bg-gray-100 text-gray-500 rounded-xl hover:bg-gray-200 transition">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-wider">Date & Heure</th>
                            <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-wider">Compagnie</th>
                            <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-wider">Quantité Tickets</th>
                            <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-wider">Motif / Description</th>
                            <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-wider text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recharges as $rech)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $rech->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-400 font-medium">{{ $rech->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center font-black text-xs">
                                        {{ substr($rech->compagnie->name ?? '?', 0, 1) }}
                                    </div>
                                    <span class="text-sm font-bold text-gray-900">{{ $rech->compagnie->name ?? 'Inconnue' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-black border border-gray-200">
                                    {{ number_format($rech->quantite ?? 0, 0, ',', ' ') }} tickets
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs text-gray-500 italic max-w-xs">{{ $rech->motif ?? 'Rechargement de quota' }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="text-lg font-black text-emerald-600">+ {{ number_format($rech->montant, 0, ',', ' ') }} FCFA</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-search text-gray-200 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-400">Aucun rechargement trouvé</h3>
                                <p class="text-sm text-gray-400">Modifiez vos filtres pour voir plus de résultats.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($recharges->hasPages())
            <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/50">
                {{ $recharges->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
