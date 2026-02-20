@extends('chauffeur.layouts.template')

@section('title', 'Mes Voyages Assignés')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Mes Voyages Assignés</h2>
            <p class="text-gray-500 text-lg">Gérez vos voyages : confirmation, démarrage et fin de trajet</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg animate-fade-in">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg animate-fade-in">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                    <p class="text-red-700 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Date Selector -->
        <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 mb-8">
            <form action="{{ route('chauffeur.voyages.index') }}" method="GET" class="flex items-end gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Date du voyage</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar text-gray-400"></i>
                        </div>
                        <input type="date" name="date" value="{{ $date }}" min="{{ date('Y-m-d') }}"
                            onchange="this.form.submit()"
                            class="block w-full pl-10 px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all bg-gray-50 focus:bg-white">
                    </div>
                </div>
                <button type="submit" class="bg-green-600 text-white p-3.5 rounded-xl hover:bg-green-700 transition-colors shadow-md hover:shadow-lg">
                    <i class="fas fa-search text-lg"></i>
                </button>
            </form>
        </div>

        <!-- Voyages List -->
        <div class="space-y-6">
            @forelse($voyages as $voyage)
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300"
                    @if($voyage->statut === 'en_cours')
                        data-voyage-tracking="{{ $voyage->id }}"
                        data-tracking-url="{{ route('chauffeur.voyages.update-location', $voyage) }}"
                    @endif
                >
                    <!-- Voyage Header -->
                    <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-green-50 to-emerald-50">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center text-green-600 font-bold text-xl border border-gray-100">
                                    {{ \Carbon\Carbon::parse($voyage->programme->heure_depart)->format('H:i') }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Programme #{{ $voyage->programme->id }}</p>
                                    <div class="flex items-center gap-3">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-gray-900 text-lg leading-tight">{{ $voyage->programme->point_depart }}</span>
                                            <span class="text-xs text-green-600 font-bold uppercase tracking-wider">{{ $voyage->programme->gareDepart->nom_gare }}</span>
                                        </div>
                                        <i class="fas fa-arrow-right text-green-500"></i>
                                        <div class="flex flex-col">
                                            <span class="font-bold text-gray-900 text-lg leading-tight">{{ $voyage->programme->point_arrive }}</span>
                                            <span class="text-xs text-green-600 font-bold uppercase tracking-wider">{{ $voyage->programme->gareArrivee->nom_gare }}</span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-1 mt-1">
                                        <p class="text-sm text-gray-500">
                                            <i class="fas fa-clock mr-1"></i>
                                            Arrivée prévue: {{ \Carbon\Carbon::parse($voyage->programme->heure_arrive)->format('H:i') }}
                                        </p>
                                        @if($voyage->statut === 'en_cours')
                                            @php
                                                $dateVoyage = \Carbon\Carbon::parse($voyage->date_voyage)->format('Y-m-d');
                                                $heureArrive = $voyage->programme->heure_arrive;
                                                $arrivalDateTime = \Carbon\Carbon::parse($dateVoyage . ' ' . $heureArrive);
                                                
                                                // Handle midnight crossing if arrival time is earlier than departure time
                                                if (\Carbon\Carbon::parse($voyage->programme->heure_arrive)->lt(\Carbon\Carbon::parse($voyage->programme->heure_depart))) {
                                                    $arrivalDateTime->addDay();
                                                }
                                            @endphp
                                            <p class="text-sm font-bold text-blue-600 flex items-center gap-2" 
                                               id="timer-{{ $voyage->id }}" 
                                               data-arrival="{{ $arrivalDateTime->toIso8601String() }}">
                                                <i class="fas fa-hourglass-half animate-spin-slow"></i>
                                                <span class="countdown-text">Temps restant: --:--:--</span>
                                            </p>
                                            <!-- GPS Status Indicator -->
                                            <div class="flex items-center gap-1 mt-1" id="gps-indicator-{{ $voyage->id }}" style="display: none;">
                                                <span style="width:8px;height:8px;background:#10b981;border-radius:50%;display:inline-block;animation:gpsPulse 1.5s infinite;"></span>
                                                <span class="text-xs text-green-600 font-bold" id="gps-text-{{ $voyage->id }}">Activation GPS...</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            @if($voyage->statut === 'en_attente')
                                <span class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-xl font-semibold text-sm flex items-center gap-2">
                                    <i class="fas fa-hourglass-half"></i>
                                    En attente
                                </span>
                            @elseif($voyage->statut === 'confirmé')
                                <span class="px-4 py-2 bg-blue-100 text-blue-700 rounded-xl font-semibold text-sm flex items-center gap-2">
                                    <i class="fas fa-check"></i>
                                    Confirmé
                                </span>
                            @elseif($voyage->statut === 'en_cours')
                                <span class="px-4 py-2 bg-purple-100 text-purple-700 rounded-xl font-semibold text-sm flex items-center gap-2 animate-pulse">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    En cours
                                </span>
                            @else
                                <span class="px-4 py-2 bg-green-100 text-green-700 rounded-xl font-semibold text-sm flex items-center gap-2">
                                    <i class="fas fa-check-circle"></i>
                                    Terminé
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Voyage Details -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-gray-50 p-4 rounded-xl">
                                <p class="text-xs font-bold text-gray-500 uppercase mb-2">Véhicule assigné</p>
                                <p class="font-bold text-gray-900">{{ $voyage->vehicule->immatriculation }}</p>
                                <p class="text-sm text-gray-500">{{ $voyage->vehicule->marque }} {{ $voyage->vehicule->modele }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $voyage->vehicule->nombre_place }} places</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl">
                                <p class="text-xs font-bold text-gray-500 uppercase mb-2">Date du voyage</p>
                                <p class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($voyage->date_voyage)->format('d/m/Y') }}</p>
                                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($voyage->date_voyage)->locale('fr')->isoFormat('dddd') }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl flex items-center justify-between col-span-1 md:col-span-1">
                                <div>
                                    <p class="text-xs font-bold text-gray-500 uppercase mb-2">Occupation</p>
                                    <div class="flex items-center gap-2">
                                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                                            <i class="fas fa-users text-lg"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 text-lg">{{ $voyage->occupancy }} / {{ $voyage->vehicule->nombre_place }}</p>
                                            <div class="w-24 h-1.5 bg-gray-200 rounded-full mt-1 overflow-hidden">
                                                <div class="h-full bg-green-500" style="width: {{ ($voyage->occupancy / ($voyage->vehicule->nombre_place ?: 1)) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl">
                                <p class="text-xs font-bold text-gray-500 uppercase mb-2">Tarif</p>
                                <p class="font-bold text-gray-900 text-xl">{{ number_format($voyage->programme->montant_billet, 0, ',', ' ') }} FCFA</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        @if($voyage->statut === 'en_attente')
                            <form action="{{ route('chauffeur.voyages.confirm', $voyage->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-4 rounded-xl font-bold flex items-center justify-center gap-3 hover:from-blue-700 hover:to-indigo-700 transition-all shadow-md hover:shadow-lg">
                                    <i class="fas fa-check-circle text-xl"></i>
                                    Confirmer le voyage
                                </button>
                            </form>
                        @elseif($voyage->statut === 'confirmé')
                            @if(\Carbon\Carbon::parse($voyage->date_voyage)->isToday())
                                <form action="{{ route('chauffeur.voyages.start', $voyage->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-4 rounded-xl font-bold flex items-center justify-center gap-3 hover:from-green-700 hover:to-emerald-700 transition-all shadow-md hover:shadow-lg">
                                        <i class="fas fa-play-circle text-xl"></i>
                                        Démarrer le voyage
                                    </button>
                                </form>
                            @else
                                <div class="bg-blue-50 border border-blue-200 p-4 rounded-xl text-center">
                                    <i class="fas fa-calendar-check text-blue-400 text-2xl mb-2"></i>
                                    <p class="text-blue-700 font-semibold">Voyage confirmé</p>
                                    <p class="text-blue-500 text-sm mt-1">Vous pourrez démarrer le jour du voyage ({{ \Carbon\Carbon::parse($voyage->date_voyage)->format('d/m/Y') }})</p>
                                </div>
                            @endif
                        @elseif($voyage->statut === 'en_cours')
                            <div class="flex flex-col md:flex-row gap-3">
                                <form action="{{ route('chauffeur.voyages.complete', $voyage->id) }}" method="POST" onsubmit="return confirm('Confirmez-vous l\'arrivée à destination ?')" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-4 rounded-xl font-bold flex items-center justify-center gap-3 hover:from-purple-700 hover:to-pink-700 transition-all shadow-md hover:shadow-lg">
                                        <i class="fas fa-flag-checkered text-xl"></i>
                                        Terminer le voyage
                                    </button>
                                </form>
                                <a href="{{ route('chauffeur.signalements.create', ['voyage_id' => $voyage->id]) }}" class="flex-1 bg-red-500 text-white py-4 rounded-xl font-bold flex items-center justify-center gap-3 hover:bg-red-600 transition-all shadow-md hover:shadow-lg">
                                    <i class="fas fa-exclamation-triangle text-xl"></i>
                                    Signaler un problème
                                </a>
                            </div>
                        @else
                            <div class="bg-green-50 border border-green-200 p-4 rounded-xl text-center">
                                <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                                <p class="text-green-700 font-semibold">Voyage terminé avec succès</p>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-gray-200">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-inbox text-gray-300 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun voyage assigné</h3>
                    <p class="text-gray-500 max-w-md mx-auto">Vous n'avez pas de voyages assignés pour cette date. Veuillez contacter votre agent.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $voyages->appends(['date' => $date])->links() }}
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin-slow {
    animation: spin-slow 3s linear infinite;
}

@keyframes gpsPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.4); }
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateTimers() {
        const timers = document.querySelectorAll('[data-arrival]');
        
        timers.forEach(timer => {
            const arrivalTime = new Date(timer.dataset.arrival).getTime();
            const now = new Date().getTime();
            const distance = arrivalTime - now;
            
            const textElement = timer.querySelector('.countdown-text');
            
            if (distance < 0) {
                if (textElement) {
                    textElement.innerHTML = "Arrivé à destination";
                }
                timer.classList.remove('text-blue-600');
                timer.classList.add('text-green-600');
                return;
            }
            
            const hours = Math.floor(distance / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            if (textElement) {
                textElement.innerHTML = `Temps restant: ${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            } else {
                // For cases like dashboard where data-arrival is on the text element itself
                timer.innerHTML = `Temps restant: ${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
        });
    }

    // Update every second
    updateTimers();
    setInterval(updateTimers, 1000);

    // ============================================
    // GPS Location Sharing for active voyages
    // ============================================
    const activeVoyages = document.querySelectorAll('[data-voyage-tracking]');
    let gpsIntervals = {};

    function startGPSTracking(voyageId, url) {
        const indicator = document.getElementById('gps-indicator-' + voyageId);
        const gpsText = document.getElementById('gps-text-' + voyageId);

        if (!navigator.geolocation) {
            console.warn('Geolocation non supportée par ce navigateur');
            if (indicator) {
                indicator.style.display = 'flex';
                if (gpsText) gpsText.textContent = 'GPS non supporté';
            }
            return;
        }

        // Show activating state
        if (indicator) {
            indicator.style.display = 'flex';
            if (gpsText) gpsText.textContent = 'Activation GPS...';
        }

        navigator.geolocation.getCurrentPosition(
            function(pos) {
                // Success - GPS is active
                if (indicator) {
                    indicator.style.display = 'flex';
                    if (gpsText) {
                        gpsText.textContent = 'Position GPS partagée';
                        gpsText.style.color = '#10b981';
                    }
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
                if (indicator) {
                    indicator.style.display = 'flex';
                    const dot = indicator.querySelector('span:first-child');
                    if (dot) dot.style.background = '#f59e0b';
                    if (gpsText) {
                        gpsText.textContent = 'GPS désactivé - Activez la localisation';
                        gpsText.style.color = '#d97706';
                    }
                }
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }

    function sendPosition(voyageId, url, position) {
        const data = {
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
            speed: position.coords.speed ? (position.coords.speed * 3.6) : null,
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
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                console.log('GPS position sent successfully for voyage', voyageId);
            }
        })
        .catch(err => console.error('GPS send error:', err));
    }

    // Auto-start GPS for all active en_cours trips
    activeVoyages.forEach(function(el) {
        const voyageId = el.getAttribute('data-voyage-tracking');
        const url = el.getAttribute('data-tracking-url');
        console.log('Starting GPS tracking for voyage', voyageId, 'url:', url);
        startGPSTracking(voyageId, url);
    });
});
</script>
@endsection
