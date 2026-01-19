@extends('sapeur_pompier.layouts.app')

@section('content')
<div class="mb-8 flex justify-between items-end">
    <div>
        <h2 class="text-3xl font-bold text-gray-900">Tableau de bord</h2>
        <p class="text-gray-500 mt-1">{{ $filterTitle ?? 'Surveillance des incidents en temps réel' }}</p>
    </div>
    <!-- Stat Cards could go here -->
</div>

@if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
        <p class="font-bold">Succès</p>
        <p>{{ session('success') }}</p>
    </div>
@endif

<div id="recent-signalements-container" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
    <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-bell text-red-500"></i> Signalements Récents
        </h3>
        <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full uppercase">
            Accidents: {{ $signalements->where('type', 'accident')->count() }}
        </span>
    </div>
    
    @if($signalements->isEmpty())
        <div class="p-12 text-center text-gray-500">
            <div class="bg-gray-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-3xl text-gray-400"></i>
            </div>
            <h4 class="text-xl font-medium mb-2">Aucun signalement</h4>
            <p>Tout semble calme pour le moment.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-sm uppercase tracking-wider">
                        <th class="text-center p-4 font-semibold">Type</th>
                        <th class="text-center p-4 font-semibold">Date & Heure</th>
                        <th class="text-center p-4 font-semibold">Description</th>
                        <th class="text-center p-4 font-semibold">Statut</th>
                        <th class="text-center p-4 font-semibold">Lieu</th>
                        <th class="text-center p-4 font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($signalements as $signalement)
                        <tr class="hover:bg-gray-50 transition-colors {{ $signalement->type == 'accident' ? 'bg-red-50' : '' }}">
                            <td class="p-4" style="text-align: center;">
                                @if($signalement->type == 'accident')
                                    <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full font-bold uppercase">
                                        <i class="fas fa-car-crash"></i> Accident
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full font-bold uppercase">
                                        {{ $signalement->type }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-sm font-medium text-gray-700" style="text-align: center;">
                                {{ $signalement->created_at->format('d/m/Y H:i') }}
                                <div class="text-xs text-gray-400">{{ $signalement->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="p-4 text-sm text-gray-600 max-w-xs truncate" style="text-align: center;">
                                {{ $signalement->description }}
                            </td>
                            <td class="p-4" style="text-align: center;">
                                @if($signalement->statut == 'nouveau')
                                    <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded-full font-bold uppercase">
                                        Nouveau
                                    </span>
                                @elseif($signalement->statut == 'traite')
                                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-bold uppercase">
                                        Traité
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full font-bold uppercase">
                                        {{ $signalement->statut }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-sm text-gray-600" style="display: flex; justify-content: center; align-items: center;">
                                @if($signalement->latitude && $signalement->longitude)
                                    <span class="location-container text-xs font-medium text-gray-700 block max-w-xs cursor-help" 
                                          data-lat="{{ $signalement->latitude }}" 
                                          data-lon="{{ $signalement->longitude }}">
                                        <i class="fas fa-spinner fa-spin text-gray-400"></i> Localisation...
                                    </span>
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $signalement->latitude }},{{ $signalement->longitude }}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1 text-xs mt-1">
                                        <i class="fas fa-external-link-alt"></i> Ouvrir GPS
                                    </a>
                                @else
                                    <span class="text-gray-400 italic">Non localisé</span>
                                @endif
                            </td>
                            <td class="p-4 text-right" style="text-align: center;">
                                <a href="{{ route('sapeur-pompier.signalement.show', $signalement->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white border border-gray-200 hover:bg-red-50 hover:border-red-200 hover:text-red-600 transition-all shadow-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    
    <!-- Pagination if needed -->
    {{-- {{ $signalements->links() }} --}}
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initGeocoding();
        startAutoRefresh();
    });

    function initGeocoding() {
        // On cible uniquement les conteneurs qui n'ont pas encore été traités (facultatif mais plus propre)
        const containers = document.querySelectorAll('.location-container:not([data-processed="true"])');
        
        containers.forEach(container => {
            container.setAttribute('data-processed', 'true'); // Marquer comme traité
            const lat = container.getAttribute('data-lat');
            const lon = container.getAttribute('data-lon');
            
            // Petit délai aléatoire pour éviter de spammer l'API OSM
            setTimeout(() => {
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                .then(response => response.json())
                .then(data => {
                    if(data && data.display_name) {
                        // On raccourcit un peu l'adresse pour l'affichage tableau
                        const shortAddress = data.display_name.split(',').slice(0, 3).join(','); 
                        container.innerHTML = `<i class="fas fa-map-marker-alt text-red-500"></i> ${shortAddress}...`;
                        container.title = data.display_name; // Full address on hover
                    } else {
                        container.innerText = "Lieu inconnu";
                    }
                })
                .catch(err => {
                    console.error('Erreur geo', err);
                    container.innerText = "Err. réseau";
                });
            }, Math.random() * 2000); 
        });
    }

    function startAutoRefresh() {
        setInterval(() => {
            // Recharger le contenu du tableau via AJAX
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContainer = doc.getElementById('recent-signalements-container');
                    const currentContainer = document.getElementById('recent-signalements-container');

                    if (newContainer && currentContainer) {
                        currentContainer.innerHTML = newContainer.innerHTML;
                        // Relancer le géocodage sur les nouveaux éléments
                        initGeocoding();
                    }
                })
                .catch(err => console.error('Erreur auto-refresh', err));
        }, 15000); // 15 secondes
    }
</script>
@endsection
