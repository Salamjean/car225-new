@extends('compagnie.layouts.template')

@section('content')
<div class="p-4 md:p-8 space-y-8">
    <!-- Header -->
    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6 pt-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-[#1A1D1F] tracking-tight flex items-center gap-3 uppercase">
                <i class="fas fa-exclamation-triangle text-red-500"></i>
                Gestion des <span class="text-red-500">Signalements</span>
            </h1>
            <p class="text-sm md:text-base text-gray-500 font-medium mt-1">Suivez et traitez les incidents signalés sur vos trajets</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-5 py-2.5 bg-red-500 text-white rounded-2xl font-black text-[10px] md:text-xs uppercase tracking-wider shadow-lg shadow-red-500/20">
                {{ $stats['total'] }} signalement(s)
            </span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
        <!-- New -->
        <div class="bg-white rounded-[32px] p-6 border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center text-red-600 transition-colors group-hover:bg-red-600 group-hover:text-white">
                    <i class="fas fa-bell text-xl"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-4 text-right">Nouveaux</span>
            </div>
            <h3 class="text-3xl font-black text-gray-900">{{ $stats['nouveaux'] }}</h3>
        </div>

        <!-- Treated -->
        <div class="bg-white rounded-[32px] p-6 border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center text-green-600 transition-colors group-hover:bg-green-600 group-hover:text-white">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-4 text-right">Traités</span>
            </div>
            <h3 class="text-3xl font-black text-gray-900">{{ $stats['traites'] }}</h3>
        </div>

        <!-- From Drivers -->
        <div class="bg-white rounded-[32px] p-6 border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 transition-colors group-hover:bg-blue-600 group-hover:text-white">
                    <i class="fas fa-id-badge text-xl"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-4 text-right">Chauffeurs</span>
            </div>
            <h3 class="text-3xl font-black text-gray-900">{{ $stats['from_chauffeurs'] }}</h3>
        </div>

        <!-- From Users -->
        <div class="bg-white rounded-[32px] p-6 border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600 transition-colors group-hover:bg-purple-600 group-hover:text-white">
                    <i class="fas fa-user text-xl"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-4 text-right">Passagers</span>
            </div>
            <h3 class="text-3xl font-black text-gray-900">{{ $stats['from_users'] }}</h3>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
        <button id="toggleFilters" class="w-full px-6 md:px-8 py-5 flex items-center justify-between hover:bg-gray-50 transition-colors text-left">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center text-gray-500 flex-shrink-0">
                    <i class="fas fa-filter text-xs"></i>
                </div>
                <span class="text-sm font-black text-gray-700 uppercase tracking-wider">Filtres de recherche</span>
            </div>
            <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300" id="filterChevron"></i>
        </button>
        <div id="filtersSection" class="hidden border-t border-gray-50 p-6 md:p-8">
            <form method="GET" action="{{ route('compagnie.signalements.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Source -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Source</label>
                    <select name="source" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-red-500 focus:bg-white outline-none transition-all text-sm font-bold">
                        <option value="">Toutes les sources</option>
                        <option value="chauffeur" {{ request('source') == 'chauffeur' ? 'selected' : '' }}>🚗 Chauffeurs</option>
                        <option value="utilisateur" {{ request('source') == 'utilisateur' ? 'selected' : '' }}>👤 Passagers</option>
                    </select>
                </div>

                <!-- Type -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Type</label>
                    <select name="type" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-red-500 focus:bg-white outline-none transition-all text-sm font-bold">
                        <option value="">Tous les types</option>
                        <option value="accident" {{ request('type') == 'accident' ? 'selected' : '' }}>Accident</option>
                        <option value="panne" {{ request('type') == 'panne' ? 'selected' : '' }}>Panne</option>
                        <option value="retard" {{ request('type') == 'retard' ? 'selected' : '' }}>Retard</option>
                        <option value="comportement" {{ request('type') == 'comportement' ? 'selected' : '' }}>Comportement</option>
                        <option value="autre" {{ request('type') == 'autre' ? 'selected' : '' }}>Autre</option>
                    </select>
                </div>

                <!-- Statut -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Statut</label>
                    <select name="statut" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-red-500 focus:bg-white outline-none transition-all text-sm font-bold">
                        <option value="">Tous les statuts</option>
                        <option value="nouveau" {{ request('statut') == 'nouveau' ? 'selected' : '' }}>Nouveau</option>
                        <option value="traite" {{ request('statut') == 'traite' ? 'selected' : '' }}>Traité</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex items-end gap-3">
                    <button type="submit" class="flex-1 py-3.5 bg-red-500 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-red-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                    <a href="{{ route('compagnie.signalements.index') }}" class="px-6 py-3.5 bg-gray-100 text-gray-600 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Signalements Table -->
    <div class="bg-white rounded-[32px] border border-gray-100 shadow-xl shadow-gray-200/40 overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-200">
            <table class="w-full text-left border-collapse min-w-[1100px]">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest pl-8">Source</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Type</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Date</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Véhicule</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Trajet</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Signalé par</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Statut</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center pr-8">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($signalements as $signalement)
                    @php
                        $isChauffeur = $signalement->personnel_id && !$signalement->user_id;
                        $isUser = (bool) $signalement->user_id;

                        // Determine the reporter's name
                        if ($isChauffeur && $signalement->personnel) {
                            $reporterName = $signalement->personnel->name . ' ' . ($signalement->personnel->prenom ?? '');
                            $reporterContact = $signalement->personnel->contact ?? '';
                            $reporterInitial = strtoupper(substr($signalement->personnel->name, 0, 1));
                        } elseif ($isUser && $signalement->user) {
                            $reporterName = $signalement->user->name . ' ' . ($signalement->user->prenom ?? '');
                            $reporterContact = $signalement->user->contact ?? $signalement->user->telephone ?? '';
                            $reporterInitial = strtoupper(substr($signalement->user->name, 0, 1));
                        } else {
                            $reporterName = 'Inconnu';
                            $reporterContact = '';
                            $reporterInitial = '?';
                        }

                        $typeColors = [
                            'accident' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'icon' => 'fa-car-crash'],
                            'panne' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'border' => 'border-orange-200', 'icon' => 'fa-tools'],
                            'retard' => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200', 'icon' => 'fa-clock'],
                            'comportement' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'icon' => 'fa-user-slash'],
                            'autre' => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200', 'icon' => 'fa-question-circle'],
                        ];
                        $tc = $typeColors[$signalement->type] ?? $typeColors['autre'];
                    @endphp
                    <tr class="group hover:bg-gray-50/60 transition-all duration-300 {{ !$signalement->is_read_by_company ? 'bg-red-50/30' : '' }}">
                        
                        {{-- SOURCE BADGE --}}
                        <td class="px-6 py-5 pl-8">
                            @if($isChauffeur)
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 text-[10px] font-black rounded-lg uppercase tracking-widest border border-blue-200">
                                        <i class="fas fa-id-badge text-[9px]"></i> Chauffeur
                                    </span>
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-50 text-purple-700 text-[10px] font-black rounded-lg uppercase tracking-widest border border-purple-200">
                                        <i class="fas fa-user text-[9px]"></i> Passager
                                    </span>
                                </div>
                            @endif
                        </td>

                        {{-- TYPE --}}
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 {{ $tc['bg'] }} {{ $tc['text'] }} text-[10px] font-bold rounded-lg uppercase tracking-widest border {{ $tc['border'] }}">
                                <i class="fas {{ $tc['icon'] }} text-[9px]"></i>
                                {{ ucfirst($signalement->type) }}
                            </span>
                        </td>

                        {{-- DATE --}}
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col items-center justify-center bg-gray-50 border border-gray-100 rounded-xl w-12 h-12 group-hover:bg-white group-hover:border-red-200 group-hover:shadow-md transition-all">
                                    <span class="text-[8px] font-semibold text-red-500 uppercase leading-none mt-1">
                                        {{ Str::upper(\Carbon\Carbon::parse($signalement->created_at)->locale('fr')->translatedFormat('M')) }}
                                    </span>
                                    <span class="text-lg font-bold text-gray-800 leading-none">
                                        {{ $signalement->created_at->format('d') }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $signalement->created_at->format('H:i') }}</p>
                                    @if(!$signalement->is_read_by_company)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-red-600 text-white animate-pulse">
                                            Nouveau
                                        </span>
                                    @else
                                        <span class="text-[10px] text-gray-400">{{ $signalement->created_at->diffForHumans() }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- VEHICULE --}}
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 group-hover:bg-gray-900 group-hover:text-white transition-colors">
                                    <i class="fas fa-bus text-xs"></i>
                                </div>
                                <span class="text-xs font-mono font-bold text-gray-900">
                                    {{ $signalement->vehicule?->immatriculation ?? $signalement->programme?->vehicule?->immatriculation ?? 'N/A' }}
                                </span>
                            </div>
                        </td>

                        {{-- TRAJET --}}
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-1.5">
                                <span class="text-sm font-medium text-gray-900">{{ $signalement->programme?->point_depart ?? '?' }}</span>
                                <i class="fas fa-arrow-right text-[8px] text-gray-300 mx-1"></i>
                                <span class="text-sm font-medium text-gray-900">{{ $signalement->programme?->point_arrive ?? '?' }}</span>
                            </div>
                        </td>

                        {{-- SIGNALE PAR --}}
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm shadow-sm
                                    {{ $isChauffeur ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-purple-100 text-purple-700 border border-purple-200' }}">
                                    {{ $reporterInitial }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 leading-tight">{{ trim($reporterName) }}</p>
                                    @if($reporterContact)
                                        <p class="text-[10px] text-gray-400 font-medium">{{ $reporterContact }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- STATUT --}}
                        <td class="px-6 py-5 text-center">
                            @if($signalement->statut === 'traite')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-600 text-[10px] font-bold rounded-lg uppercase tracking-widest border border-green-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Traité
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 text-[10px] font-bold rounded-lg uppercase tracking-widest border border-red-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> Nouveau
                                </span>
                            @endif
                        </td>

                        {{-- ACTIONS --}}
                        <td class="px-6 py-5 text-center pr-8">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Lien direct : la méthode show() du contrôleur gère déjà le marquage comme lu --}}
                                <a href="{{ route('compagnie.signalements.show', $signalement->id) }}"
                                   onclick="if(!{{ $signalement->is_read_by_company ? 'true' : 'false' }}) { markReadOptimistic(); }"
                                   class="w-8 h-8 {{ $signalement->is_read_by_company ? 'bg-gray-100 text-gray-400' : 'bg-blue-50 text-blue-600' }} rounded-lg flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm"
                                   title="Cliquer pour voir les détails">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center animate-fade-in-up">
                                <div class="w-24 h-24 bg-green-50 rounded-full flex items-center justify-center text-green-500 mb-6 shadow-xl shadow-green-100">
                                    <i class="fas fa-check-circle text-4xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2 uppercase tracking-tight">Aucun signalement</h3>
                                <p class="text-gray-400 font-medium max-w-sm mx-auto">Tout semble calme pour le moment. Bonne route !</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($signalements->hasPages())
        <div class="px-8 py-6 border-t border-gray-50 bg-gray-50">
            {{ $signalements->links() }}
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
    });

    function markReadOptimistic() {
        // Décrémenter instantanément le badge de la sidebar au clic (effet visuel rapide)
        const badge = document.querySelector('.sidebar-signalement-badge');
        if (badge) {
            let count = parseInt(badge.textContent.trim()) - 1;
            if (count <= 0) {
                badge.style.display = 'none';
            } else {
                badge.textContent = count;
            }
        }
        // Pas besoin de fetch ici car la page show() du contrôleur
        // s'occupera de marquer comme lu en base de données lors du chargement.
        return true; 
    }
</script>
@endpush
@endsection