@extends('sapeur_pompier.layouts.app')

@section('content')

{{-- En-tête avec stats --}}
<div class="mb-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-gray-900">Tableau de bord</h2>
            <p class="text-gray-400 text-sm mt-1 font-medium">{{ $filterTitle ?? 'Surveillance des incidents en temps réel' }}</p>
        </div>
        <div class="flex items-center gap-2">
            <span id="live-dot" class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
            <span class="text-xs font-bold text-green-600 uppercase tracking-wide">En direct</span>
        </div>
    </div>

    {{-- Stat Cards --}}
    @php
        $totalSignalements = $signalements->count();
        $accidents = $signalements->where('type', 'accident')->count();
        $pannes = $signalements->where('type', 'panne')->count();
        $nouveaux = $signalements->where('statut', 'nouveau')->count();
        $traites = $signalements->where('statut', 'traite')->count();
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-car-crash text-red-600 text-sm"></i>
            </div>
            <div>
                <div class="text-xl font-black text-gray-900">{{ $accidents }}</div>
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Accidents</div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-tools text-orange-600 text-sm"></i>
            </div>
            <div>
                <div class="text-xl font-black text-gray-900">{{ $pannes }}</div>
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Pannes</div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-circle text-yellow-600 text-sm"></i>
            </div>
            <div>
                <div class="text-xl font-black text-gray-900">{{ $nouveaux }}</div>
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">En attente</div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle text-green-600 text-sm"></i>
            </div>
            <div>
                <div class="text-xl font-black text-gray-900">{{ $traites }}</div>
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Traités</div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 p-4 mb-6 rounded-xl flex items-center gap-3" role="alert">
        <i class="fas fa-check-circle text-green-500"></i>
        <p class="font-bold text-sm">{{ session('success') }}</p>
    </div>
@endif

{{-- Liste des signalements --}}
<div id="recent-signalements-container" class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center">
        <h3 class="text-sm font-black text-gray-800 flex items-center gap-2 uppercase tracking-wide">
            <i class="fas fa-bell text-red-500"></i> Signalements
        </h3>
        <div class="flex items-center gap-2">
            <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-3 py-1 rounded-full uppercase">
                Total: {{ $totalSignalements }}
            </span>
            @if($accidents > 0)
            <span class="bg-red-100 text-red-700 text-[10px] font-bold px-3 py-1 rounded-full uppercase animate-pulse">
                <i class="fas fa-car-crash mr-1"></i> {{ $accidents }} accident(s)
            </span>
            @endif
        </div>
    </div>

    @if($signalements->isEmpty())
        <div class="p-16 text-center">
            <div class="bg-gray-50 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shield-alt text-3xl text-gray-300"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-400 mb-1">Aucun signalement</h4>
            <p class="text-sm text-gray-300">Tout est calme pour le moment.</p>
        </div>
    @else
        <div class="divide-y divide-gray-50">
            @foreach($signalements as $signalement)
                @php
                    $isChauffeur = $signalement->personnel_id && !$signalement->user_id;
                    $isCompagnie = $signalement->compagnie_id && !$signalement->user_id && !$signalement->personnel_id;
                    $isAccident = $signalement->type == 'accident';
                    $isNew = $signalement->statut == 'nouveau';
                @endphp
                <a href="{{ route('sapeur-pompier.signalement.show', $signalement->id) }}"
                   class="block hover:bg-gray-50 transition-all {{ $isAccident && $isNew ? 'bg-red-50 hover:bg-red-100 border-l-4 border-red-500' : '' }} {{ $isNew && !$isAccident ? 'border-l-4 border-yellow-400' : '' }}">
                    <div class="px-6 py-4 flex items-center gap-4">

                        {{-- Icône type --}}
                        <div class="flex-shrink-0">
                            @if($isAccident)
                                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center {{ $isNew ? 'animate-pulse' : '' }}">
                                    <i class="fas fa-car-crash text-red-600 text-lg"></i>
                                </div>
                            @elseif($signalement->type == 'panne')
                                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-tools text-orange-600 text-lg"></i>
                                </div>
                            @elseif($signalement->type == 'retard')
                                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-clock text-yellow-600 text-lg"></i>
                                </div>
                            @else
                                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-info-circle text-blue-600 text-lg"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Infos principales --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                {{-- Badge type --}}
                                @if($isAccident)
                                    <span class="inline-flex items-center gap-1 bg-red-600 text-white text-[10px] px-2 py-0.5 rounded-md font-bold uppercase">Accident</span>
                                @elseif($signalement->type == 'panne')
                                    <span class="inline-flex items-center gap-1 bg-orange-500 text-white text-[10px] px-2 py-0.5 rounded-md font-bold uppercase">Panne</span>
                                @elseif($signalement->type == 'retard')
                                    <span class="inline-flex items-center gap-1 bg-yellow-500 text-white text-[10px] px-2 py-0.5 rounded-md font-bold uppercase">Retard</span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-blue-500 text-white text-[10px] px-2 py-0.5 rounded-md font-bold uppercase">{{ $signalement->type }}</span>
                                @endif

                                {{-- Badge source --}}
                                @if($isChauffeur)
                                    <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-600 text-[10px] px-2 py-0.5 rounded-md font-bold">
                                        <i class="fas fa-id-badge" style="font-size:8px;"></i> Chauffeur
                                    </span>
                                @elseif($isCompagnie || ($signalement->compagnie_id && !$signalement->user_id))
                                    <span class="inline-flex items-center gap-1 bg-orange-50 text-orange-600 text-[10px] px-2 py-0.5 rounded-md font-bold">
                                        <i class="fas fa-building" style="font-size:8px;"></i> Compagnie
                                    </span>
                                @elseif($signalement->user_id)
                                    <span class="inline-flex items-center gap-1 bg-purple-50 text-purple-600 text-[10px] px-2 py-0.5 rounded-md font-bold">
                                        <i class="fas fa-user" style="font-size:8px;"></i> Passager
                                    </span>
                                @endif

                                {{-- Badge statut --}}
                                @if($signalement->statut == 'nouveau')
                                    <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 text-[10px] px-2 py-0.5 rounded-md font-bold uppercase">
                                        <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full"></span> Nouveau
                                    </span>
                                @elseif($signalement->statut == 'traite')
                                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-[10px] px-2 py-0.5 rounded-md font-bold uppercase">
                                        <i class="fas fa-check" style="font-size:8px;"></i> Traité
                                    </span>
                                @endif
                            </div>

                            {{-- Description --}}
                            <p class="text-sm text-gray-700 font-medium truncate">{{ Str::limit($signalement->description, 80) }}</p>

                            {{-- Lieu --}}
                            <div class="flex items-center gap-3 mt-1.5">
                                @if($signalement->latitude && $signalement->longitude)
                                    <span class="location-container text-[11px] text-gray-400 font-medium"
                                          data-lat="{{ $signalement->latitude }}"
                                          data-lon="{{ $signalement->longitude }}">
                                        <i class="fas fa-spinner fa-spin mr-1"></i> Localisation...
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Date + flèche --}}
                        <div class="flex-shrink-0 text-right">
                            <div class="text-xs font-bold text-gray-700">{{ $signalement->created_at->format('H:i') }}</div>
                            <div class="text-[10px] text-gray-400 font-medium">{{ $signalement->created_at->diffForHumans() }}</div>
                            <div class="text-[10px] text-gray-300 mt-0.5">{{ $signalement->created_at->format('d/m/Y') }}</div>
                        </div>

                        <div class="flex-shrink-0 text-gray-300">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initGeocoding();
        startAutoRefresh();
        updateSapeurLocation();
    });

    function updateSapeurLocation() {
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(async function(position) {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                let communeStr = '';
                let adresseStr = '';

                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`);
                    const data = await response.json();
                    if (data && data.address) {
                        communeStr = data.address.city || data.address.town || data.address.village || data.address.suburb || data.address.county || '';
                        if (data.display_name) {
                            adresseStr = data.display_name.split(',').slice(0, 2).join(', ').trim();
                        }
                    }
                } catch (error) {
                    console.error('Erreur Reverse Geocoding:', error);
                }

                fetch('{{ route("sapeur-pompier.update-location") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ latitude: lat, longitude: lon, commune: communeStr, adresse: adresseStr })
                }).catch(err => console.error('Erreur MAJ GPS', err));

            }, function(error) {
                if (error.code === error.PERMISSION_DENIED) {
                    alert("Veuillez autoriser la localisation pour recevoir les alertes proches de vous.");
                }
            }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
        }
    }

    function initGeocoding() {
        const containers = document.querySelectorAll('.location-container:not([data-processed="true"])');
        containers.forEach(container => {
            container.setAttribute('data-processed', 'true');
            const lat = container.getAttribute('data-lat');
            const lon = container.getAttribute('data-lon');
            setTimeout(() => {
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                .then(response => response.json())
                .then(data => {
                    if(data && data.display_name) {
                        const shortAddress = data.display_name.split(',').slice(0, 3).join(',');
                        container.innerHTML = `<i class="fas fa-map-marker-alt text-red-400 mr-1"></i> ${shortAddress}`;
                        container.title = data.display_name;
                    } else {
                        container.innerText = "Lieu inconnu";
                    }
                })
                .catch(() => { container.innerText = "Err. réseau"; });
            }, Math.random() * 1500);
        });
    }

    function startAutoRefresh() {
        setInterval(() => {
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContainer = doc.getElementById('recent-signalements-container');
                    const currentContainer = document.getElementById('recent-signalements-container');
                    if (newContainer && currentContainer) {
                        currentContainer.innerHTML = newContainer.innerHTML;
                        initGeocoding();
                    }
                })
                .catch(err => console.error('Erreur auto-refresh', err));
        }, 3000); // Toutes les 3 secondes
    }
</script>
@endsection
