@extends('chauffeur.layouts.template')

@section('title', 'Tableau de bord')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="width: 100%">
        <!-- Header Summary -->
        <div class="bg-gradient-to-r from-orange-600 to-red-600 rounded-2xl p-6 text-white shadow-lg mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold">Bonjour, {{ Auth::guard('chauffeur')->user()->prenom }} !</h2>
                    <p class="opacity-90 mt-1">Prêt pour la route ?</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md px-4 py-2 rounded-xl border border-white/20">
                    <span class="text-xs uppercase font-black tracking-widest opacity-70 block">Votre Code ID</span>
                    <span class="text-xl font-black tracking-tighter">{{ Auth::guard('chauffeur')->user()->code_id ?? 'N/A' }}</span>
                </div>
            </div>
            
            <div class="mt-8 grid grid-cols-2 gap-4">
                <div class="bg-white/20 backdrop-blur rounded-xl p-4 border border-white/30">
                    <p class="text-sm opacity-80 uppercase font-bold tracking-wider">Missions en cours</p>
                    <p class="text-3xl font-bold mt-1">{{ $todayMissionsCount ?? ($todayVoyages->count() + $activeConvois->count()) }}</p>
                    <p class="text-xs mt-1">Voyages + Convois actifs</p>
                </div>
                <div class="bg-white/20 backdrop-blur rounded-xl p-4 border border-white/30">
                    <p class="text-sm opacity-80 uppercase font-bold tracking-wider">Missions terminées (aujourd'hui)</p>
                    <p class="text-3xl font-bold mt-1">{{ $completedMissionsTodayCount ?? 0 }}</p>
                    <p class="text-xs mt-1">Voyages + Convois terminés</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Today's Missions -->
            <section>
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center">
                        <i class="fas fa-route text-orange-500"></i>
                    </div>
                    Missions du jour (Voyages + Convois)
                </h3>

                @if(($todayVoyages->count() + $activeConvois->count()) > 0)
                    <div class="space-y-4">
                        @foreach($todayVoyages as $voyage)
                            <a href="{{ route('chauffeur.voyages.index') }}" class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition-shadow text-decoration-none"
                                @if($voyage->statut === 'en_cours')
                                    data-voyage-tracking="{{ $voyage->id }}"
                                    data-tracking-url="{{ route('chauffeur.voyages.update-location', $voyage) }}"
                                @endif
                            >
                                <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-orange-600 font-bold text-xl border border-orange-100">
                                    {{ \Carbon\Carbon::parse($voyage->programme->heure_depart)->format('H:i') }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-3">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-gray-900">{{ $voyage->programme->point_depart }}</span>
                                                <span class="text-[10px] text-green-600 font-bold uppercase">{{ $voyage->gareDepart->nom_gare }}</span>
                                            </div>
                                            <i class="fas fa-long-arrow-alt-right text-orange-300"></i>
                                            <div class="flex flex-col">
                                                <span class="font-bold text-gray-900">{{ $voyage->programme->point_arrive }}</span>
                                                <span class="text-[10px] text-green-600 font-bold uppercase">{{ $voyage->gareArrivee->nom_gare }}</span>
                                            </div>
                                        </div>
                                        @if($voyage->statut === 'en_cours')

                                            <!-- GPS Status Indicator -->
                                            <div class="mt-1 flex items-center gap-1 hidden" id="gps-indicator-{{ $voyage->id }}">
                                                <span style="width:7px;height:7px;background:#10b981;border-radius:50%;display:inline-block;animation:livePulse 1.5s infinite;"></span>
                                                <span class="text-[10px] text-green-600 font-bold">Position GPS partagée</span>
                                            </div>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-500 mt-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-bus mr-1"></i> {{ $voyage->vehicule->immatriculation }}
                                        </span>
                                    </p>
                                </div>
                                <div class="flex sm:flex-col items-center sm:items-end gap-2">
                                    @if($voyage->statut === 'interrompu')
                                        <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full uppercase flex items-center gap-1">
                                            🚨 Interrompu
                                        </span>
                                    @elseif($voyage->statut === 'en_cours')
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full uppercase animate-pulse">
                                            En cours
                                        </span>
                                    @elseif($voyage->statut === 'terminé')
                                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full uppercase">
                                            Terminé
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full uppercase">
                                            {{ $voyage->statut }}
                                        </span>
                                    @endif
                                </div>
                            </a>
                        @endforeach

                        @foreach($activeConvois as $convoi)
                            <a href="{{ route('chauffeur.voyages.index') }}" class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow text-decoration-none block">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div>
                                        <p class="text-xs text-indigo-600 uppercase font-bold">Convoi • Référence</p>
                                        <p class="font-extrabold text-gray-900">{{ $convoi->reference ?? '-' }}</p>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $convoi->statut === 'en_cours' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700' }}">
                                        {{ $convoi->statut === 'en_cours' ? 'En cours' : 'Assigné' }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-700 font-semibold">
                                    {{ $convoi->itineraire ? ($convoi->itineraire->point_depart . ' -> ' . $convoi->itineraire->point_arrive) : '-' }}
                                </p>
                                <div class="mt-2 text-xs text-gray-500 flex flex-wrap gap-3">
                                    <span><i class="fas fa-users mr-1"></i>{{ $convoi->nombre_personnes ?? 0 }} passagers</span>
                                    <span><i class="fas fa-map-marker-alt mr-1"></i>{{ $convoi->gare->nom_gare ?? '-' }}</span>
                                    <span><i class="fas fa-bus mr-1"></i>{{ $convoi->vehicule->immatriculation ?? '-' }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 bg-white rounded-2xl border border-dashed border-gray-200 shadow-sm">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-route text-gray-300 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 font-medium">Aucune mission prévue aujourd'hui</p>
                        <a href="{{ route('chauffeur.voyages.index') }}" class="text-orange-600 font-bold mt-2 inline-block hover:text-orange-700 transition">
                            Voir mes missions &rarr;
                        </a>
                    </div>
                @endif
            </section>

            <!-- Upcoming Trips -->
            <section>
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-clock text-blue-500"></i>
                    </div>
                    Voyages à venir
                </h3>

                @if($upcomingVoyages->count() > 0)
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($upcomingVoyages as $voyage)
                            <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-3">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1">
                                        <i class="far fa-calendar"></i>
                                        {{ \Carbon\Carbon::parse($voyage->date_voyage)->translatedFormat('d F Y') }}
                                    </span>
                                    <span class="text-sm font-bold text-gray-900 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">
                                        {{ \Carbon\Carbon::parse($voyage->programme->heure_depart)->format('H:i') }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="font-semibold text-gray-800 text-sm">{{ $voyage->gareDepart->nom_gare }}</span>
                                    <i class="fas fa-long-arrow-alt-right text-gray-300 text-xs"></i>
                                    <span class="font-semibold text-gray-800 text-sm">{{ $voyage->gareArrivee->nom_gare }}</span>
                                </div>
                                <div class="text-xs text-gray-500 flex items-center justify-between border-t border-dashed pt-3 mt-2">
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-bus text-gray-400"></i>
                                        {{ $voyage->vehicule->immatriculation }}
                                    </span>
                                    <span class="capitalize bg-gray-50 px-2 py-0.5 rounded text-gray-600">{{ $voyage->statut }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 bg-white rounded-2xl text-center shadow-sm border border-gray-100">
                        <p class="text-gray-500 text-sm italic">Aucun voyage futur programmé.</p>
                    </div>
                @endif
            </section>
        </div>

        <!-- Quick Action -->
        <a href="{{ route('chauffeur.voyages.index') }}" class="block mt-8 group">
            <div class="bg-white rounded-2xl p-6 shadow-md border border-orange-100 flex items-center justify-between group-hover:shadow-lg transition-all duration-300 transform group-hover:-translate-y-1">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center group-hover:bg-orange-600 transition-colors duration-300">
                        <i class="fas fa-plus text-orange-600 text-xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">Nouveau Voyage</h3>
                        <p class="text-gray-500 text-sm">S'assigner à une nouvelle course disponible</p>
                    </div>
                </div>
                <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center">
                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-orange-600"></i>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {


    // ============================================
    // GPS Location Sharing for active voyages
    // ============================================
    const activeVoyages = document.querySelectorAll('[data-voyage-tracking]');
    let gpsIntervals = {};

    function startGPSTracking(voyageId, url) {
        if (!navigator.geolocation) {
            console.warn('Geolocation non supportée');
            return;
        }

        // Request permission and start tracking
        navigator.geolocation.getCurrentPosition(
            function(pos) {
                // First successful position - mark as tracking
                const indicator = document.getElementById('gps-indicator-' + voyageId);
                if (indicator) {
                    indicator.classList.remove('hidden');
                }

                // Send position immediately
                sendPosition(voyageId, url, pos);

                // Then poll every 5 seconds
                gpsIntervals[voyageId] = setInterval(function() {
                    navigator.geolocation.getCurrentPosition(
                        function(p) { sendPosition(voyageId, url, p); },
                        function(err) { console.warn('GPS error:', err.message); },
                        { enableHighAccuracy: true, timeout: 4000, maximumAge: 2000 }
                    );
                }, 5000);
            },
            function(err) {
                console.warn('GPS permission denied or error:', err.message);
                const indicator = document.getElementById('gps-indicator-' + voyageId);
                if (indicator) {
                    indicator.innerHTML = '<i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i><span class="text-yellow-600 text-[10px]">GPS désactivé</span>';
                    indicator.classList.remove('hidden');
                }
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }

    function sendPosition(voyageId, url, position) {
        const data = {
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
            speed: position.coords.speed ? (position.coords.speed * 3.6) : null, // m/s to km/h
            heading: position.coords.heading
        };

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        }).catch(err => console.error('GPS send error:', err));
    }

    // Auto-start GPS for all active trips
    activeVoyages.forEach(function(el) {
        const voyageId = el.getAttribute('data-voyage-tracking');
        const url = el.getAttribute('data-tracking-url');
        startGPSTracking(voyageId, url);
    });
});
</script>
@endsection

@section('styles')
<style>
@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin-slow {
    animation: spin-slow 3s linear infinite;
}

@keyframes livePulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.3); }
}
</style>
@endsection
