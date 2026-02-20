@extends('admin.layouts.template')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto" style="max-width: 1800px;">

        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 border-l-4 border-[#e94f1b] pl-4 tracking-tight">Voyages en Cours</h1>
                <p class="mt-2 text-gray-500 pl-5">Suivi en temps réel des voyages actifs sur la plateforme</p>
            </div>
            <div class="flex items-center gap-3">
                <div id="live-counter" class="flex items-center gap-2 px-4 py-2 bg-green-100 text-green-700 rounded-xl font-bold text-sm">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    <span id="voyage-count">{{ $voyages->count() }}</span> voyage(s) en cours
                </div>
                <button onclick="refreshVoyages()" id="refresh-btn"
                    class="p-2.5 bg-white rounded-xl shadow-sm border border-gray-200 hover:bg-gray-50 transition-colors" title="Rafraîchir">
                    <i class="fas fa-sync-alt text-gray-600"></i>
                </button>
                <div class="text-xs text-gray-400 font-medium" id="last-update">
                    Mis à jour : {{ now()->format('H:i:s') }}
                </div>
            </div>
        </div>

        <!-- Auto-refresh indicator -->
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-3 flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm text-blue-700">
                <i class="fas fa-satellite-dish"></i>
                <span>Rafraîchissement automatique toutes les <strong>30 secondes</strong></span>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="auto-refresh-toggle" checked class="sr-only peer">
                <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
            </label>
        </div>

        <!-- Voyages Grid -->
        <div id="voyages-container">
            @if($voyages->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="voyages-grid">
                @foreach($voyages as $voyage)
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <!-- Card Header -->
                    <div class="bg-gradient-to-r from-[#e94f1b] to-[#e89116] px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <i class="fas fa-bus text-white text-lg"></i>
                            </div>
                            <div>
                                <p class="text-white font-bold text-sm">{{ $voyage->programme->compagnie->name ?? 'N/A' }}</p>
                                <p class="text-white/70 text-xs">Voyage #{{ $voyage->id }}</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-white/20 rounded-full text-white text-xs font-bold flex items-center gap-1">
                            <span class="w-2 h-2 bg-green-300 rounded-full animate-pulse"></span>
                            EN COURS
                        </span>
                    </div>

                    <!-- Route -->
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="flex flex-col items-center">
                                <span class="w-3 h-3 bg-green-500 rounded-full border-2 border-white shadow"></span>
                                <span class="w-0.5 h-8 bg-gray-300"></span>
                                <span class="w-3 h-3 bg-red-500 rounded-full border-2 border-white shadow"></span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-gray-900">{{ $voyage->programme->point_depart ?? ($voyage->gareDepart->nom ?? 'N/A') }}</p>
                                <div class="text-xs text-gray-400 my-1">
                                    @if($voyage->programme)
                                        {{ $voyage->programme->heure_depart ?? '' }} → {{ $voyage->programme->heure_arrive ?? '' }}
                                    @endif
                                </div>
                                <p class="text-sm font-bold text-gray-900">{{ $voyage->programme->point_arrive ?? ($voyage->gareArrivee->nom ?? 'N/A') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="px-6 py-4 space-y-3">
                        <!-- Chauffeur -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 text-xs">Chauffeur</p>
                                    <p class="text-gray-500 text-xs">{{ $voyage->chauffeur->prenom ?? '' }} {{ $voyage->chauffeur->name ?? 'Non assigné' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Véhicule -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-car text-purple-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 text-xs">Véhicule</p>
                                    <p class="text-gray-500 text-xs">{{ $voyage->vehicule->immatriculation ?? '' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Stats row -->
                        <div class="flex items-center gap-4 pt-2 border-t border-gray-100">
                            <!-- Passagers -->
                            <div class="flex items-center gap-1.5 text-xs">
                                <div class="w-7 h-7 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-users text-green-600 text-[10px]"></i>
                                </div>
                                <span class="font-bold text-gray-700">{{ $voyage->occupancy }} passagers</span>
                            </div>

                            <!-- Temps restant -->
                            @if($voyage->temps_restant)
                            <div class="flex items-center gap-1.5 text-xs">
                                <div class="w-7 h-7 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-clock text-orange-600 text-[10px]"></i>
                                </div>
                                <span class="font-bold text-orange-600">{{ $voyage->temps_restant }}</span>
                            </div>
                            @endif
                        </div>

                        <!-- GPS Location -->
                        @if($voyage->latestLocation)
                        <div class="mt-2 p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-emerald-600"></i>
                                    <span class="text-xs font-semibold text-emerald-700">Position GPS active</span>
                                </div>
                                @if($voyage->latestLocation->speed)
                                <span class="px-2 py-0.5 bg-emerald-200 text-emerald-800 rounded-full text-[10px] font-bold">
                                    {{ round($voyage->latestLocation->speed) }} km/h
                                </span>
                                @endif
                            </div>
                            <p class="text-[10px] text-emerald-500 mt-1">
                                Dernière mise à jour: {{ $voyage->latestLocation->updated_at->diffForHumans() }}
                            </p>
                        </div>
                        @else
                        <div class="mt-2 p-3 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-gray-400"></i>
                                <span class="text-xs text-gray-400">Aucune position GPS disponible</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Date footer -->
                    <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs text-gray-400 font-medium">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            {{ \Carbon\Carbon::parse($voyage->date_voyage)->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div id="empty-state" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-16 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-route text-gray-300 text-4xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-400 mb-2">Aucun voyage en cours</h3>
                <p class="text-gray-400 text-sm">Les voyages actifs apparaîtront ici automatiquement.</p>
            </div>
            @endif
        </div>

    </div>
</div>

<script>
let autoRefreshInterval = null;
let isAutoRefresh = true;

function refreshVoyages() {
    const btn = document.getElementById('refresh-btn');
    btn.querySelector('i').classList.add('fa-spin');
    
    fetch('{{ route("admin.voyages.en-cours.api") }}')
        .then(res => res.json())
        .then(data => {
            // Update counter
            document.getElementById('voyage-count').textContent = data.count;
            document.getElementById('last-update').textContent = 'Mis à jour : ' + new Date().toLocaleTimeString('fr-FR');
            
            const container = document.getElementById('voyages-container');
            
            if (data.count === 0) {
                container.innerHTML = `
                    <div id="empty-state" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-16 text-center">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-route text-gray-300 text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-400 mb-2">Aucun voyage en cours</h3>
                        <p class="text-gray-400 text-sm">Les voyages actifs apparaîtront ici automatiquement.</p>
                    </div>`;
            } else {
                let html = '<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">';
                data.voyages.forEach(v => {
                    const locationHtml = v.location 
                        ? `<div class="mt-2 p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-emerald-600"></i>
                                    <span class="text-xs font-semibold text-emerald-700">Position GPS active</span>
                                </div>
                                ${v.location.speed ? `<span class="px-2 py-0.5 bg-emerald-200 text-emerald-800 rounded-full text-[10px] font-bold">${Math.round(v.location.speed)} km/h</span>` : ''}
                            </div>
                            <p class="text-[10px] text-emerald-500 mt-1">Dernière mise à jour: ${v.location.updated_at}</p>
                        </div>`
                        : `<div class="mt-2 p-3 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-gray-400"></i>
                                <span class="text-xs text-gray-400">Aucune position GPS</span>
                            </div>
                        </div>`;

                    html += `
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="bg-gradient-to-r from-[#e94f1b] to-[#e89116] px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                    <i class="fas fa-bus text-white text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-white font-bold text-sm">${v.compagnie}</p>
                                    <p class="text-white/70 text-xs">Voyage #${v.id}</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-white/20 rounded-full text-white text-xs font-bold flex items-center gap-1">
                                <span class="w-2 h-2 bg-green-300 rounded-full animate-pulse"></span> EN COURS
                            </span>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col items-center">
                                    <span class="w-3 h-3 bg-green-500 rounded-full border-2 border-white shadow"></span>
                                    <span class="w-0.5 h-8 bg-gray-300"></span>
                                    <span class="w-3 h-3 bg-red-500 rounded-full border-2 border-white shadow"></span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-gray-900">${v.depart}</p>
                                    <div class="text-xs text-gray-400 my-1">${v.heure_depart || ''} → ${v.heure_arrivee || ''}</div>
                                    <p class="text-sm font-bold text-gray-900">${v.arrivee}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-4 space-y-3">
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 text-xs">Chauffeur</p>
                                    <p class="text-gray-500 text-xs">${v.chauffeur_prenom} ${v.chauffeur}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-car text-purple-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 text-xs">Véhicule</p>
                                    <p class="text-gray-500 text-xs">${v.vehicule_marque} — ${v.vehicule_immat}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 pt-2 border-t border-gray-100">
                                <div class="flex items-center gap-1.5 text-xs">
                                    <div class="w-7 h-7 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-users text-green-600 text-[10px]"></i>
                                    </div>
                                    <span class="font-bold text-gray-700">${v.occupancy} passagers</span>
                                </div>
                                ${v.temps_restant ? `
                                <div class="flex items-center gap-1.5 text-xs">
                                    <div class="w-7 h-7 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-clock text-orange-600 text-[10px]"></i>
                                    </div>
                                    <span class="font-bold text-orange-600">${v.temps_restant}</span>
                                </div>` : ''}
                            </div>
                            ${locationHtml}
                        </div>
                        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100">
                            <span class="text-xs text-gray-400 font-medium">
                                <i class="fas fa-calendar-alt mr-1"></i> ${v.date_voyage}
                            </span>
                        </div>
                    </div>`;
                });
                html += '</div>';
                container.innerHTML = html;
            }
        })
        .catch(err => console.error('Erreur refresh:', err))
        .finally(() => {
            btn.querySelector('i').classList.remove('fa-spin');
        });
}

function startAutoRefresh() {
    autoRefreshInterval = setInterval(refreshVoyages, 30000);
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
    }
}

document.getElementById('auto-refresh-toggle').addEventListener('change', function() {
    if (this.checked) {
        startAutoRefresh();
    } else {
        stopAutoRefresh();
    }
});

// Start auto-refresh on load
startAutoRefresh();
</script>
@endsection
