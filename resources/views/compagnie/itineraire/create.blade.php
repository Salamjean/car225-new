@extends('compagnie.layouts.template')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
    <div class=" mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#fea219] rounded-2xl shadow-lg mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900">Nouvel Itinéraire</h1>
        </div>

        <!-- Carte du formulaire -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="{{route('itineraire.store')}}" method="POST" class="p-8">
                @csrf

                <!-- Section 1: Informations de base -->
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-[#fea219] rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Informations de l'itinéraire</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Point de départ -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Point de départ</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="point_depart" 
                                       name="point_depart" 
                                       required
                                       class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#fea219] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                       placeholder="Ex: Abidjan, Plateau">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                </div>
                            </div>
                            @error('point_depart')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Point d'arrivée -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Point d'arrivée</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="point_arrive" 
                                       name="point_arrive" 
                                       required
                                       class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#fea219] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                       placeholder="Ex: Yopougon, Abidjan">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                </div>
                            </div>
                            @error('point_arrive')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Durée du parcours (calculée automatiquement) -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Durée du parcours</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="durer_parcours" 
                                       name="durer_parcours" 
                                       required
                                       readonly
                                       class="w-full px-4 py-4 border border-gray-200 rounded-xl bg-green-50 text-gray-700 font-semibold cursor-not-allowed"
                                       placeholder="La durée sera calculée automatiquement">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">Calculée automatiquement via OpenRouteService</p>
                            @error('durer_parcours')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Carte -->
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-green-500 rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Visualisation sur la carte</h2>
                    </div>

                    <div class="rounded-2xl overflow-hidden border-2 border-gray-200 shadow-lg">
                        <div id="map" style="height: 400px; width: 100%;"></div>
                    </div>

                    <!-- Informations de l'itinéraire -->
                    <div id="route-info" class="hidden mt-4 p-4 bg-blue-50 rounded-xl border border-blue-200">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div class="text-center">
                                <div class="font-semibold text-blue-700">Distance</div>
                                <div id="distance" class="text-lg font-bold text-gray-900">-</div>
                            </div>
                            <div class="text-center">
                                <div class="font-semibold text-blue-700">Durée</div>
                                <div id="duration" class="text-lg font-bold text-gray-900">-</div>
                            </div>
                            <div class="text-center">
                                <div class="font-semibold text-blue-700">Statut</div>
                                <div id="route-status" class="text-lg font-bold text-green-600">Calculé</div>
                            </div>
                        </div>
                    </div>

                    <!-- Indicateur de chargement -->
                    <div id="loading-indicator" class="hidden mt-4 p-3 bg-yellow-50 rounded-xl border border-yellow-200 text-center">
                        <div class="flex items-center justify-center gap-2 text-yellow-700">
                            <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Calcul de l'itinéraire en cours...</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 justify-between items-center pt-8 border-t border-gray-200">
                    <a href="#" 
                       class="flex items-center px-8 py-4 text-gray-700 font-semibold rounded-xl border border-gray-300 hover:bg-gray-50 transition-all duration-200 group">
                        <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour à la liste
                    </a>
                    
                    <button type="submit" id="submit-btn"
                            class="flex items-center px-12 py-4 bg-[#fea219] text-white font-bold rounded-xl hover:bg-[#e89116] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Créer l'itinéraire
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Inclure Leaflet CSS et JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let map;
let calculationTimeout;

// Configuration OpenRouteService
const ORS_API_KEY = '5b3ce3597851110001cf6248eac8b375ba7a4fa1a63d7ef5f1046d1c';

function initMap() {
    map = L.map('map').setView([5.359951, -4.008256], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
}

async function geocodeAddress(address) {
    try {
        // Nettoyer et corriger les noms de villes
        const cleanedAddress = cleanCityName(address);
        
        const response = await fetch(`https://api.openrouteservice.org/geocode/search?api_key=${ORS_API_KEY}&text=${encodeURIComponent(cleanedAddress)}&boundary.country=CI`);
        const data = await response.json();
        
        if (data.features && data.features.length > 0) {
            return data.features[0].geometry.coordinates;
        }
        return null;
    } catch (error) {
        console.error('Erreur de géocodage:', error);
        return null;
    }
}

function cleanCityName(address) {
    const corrections = {
        'aboisso': 'Aboisso',
        'abidjan': 'Abidjan',
        'yamoussoukro': 'Yamoussoukro',
        'san-pedro': 'San-Pédro',
        'san pedro': 'San-Pédro',
        'bouake': 'Bouaké',
        'daloa': 'Daloa',
        'korhogo': 'Korhogo',
        'man': 'Man',
        'gagnoa': 'Gagnoa',
        'abengourou': 'Abengourou'
    };
    
    let cleaned = address.toLowerCase().trim();
    
    // Appliquer les corrections
    for (const [wrong, correct] of Object.entries(corrections)) {
        if (cleaned.includes(wrong)) {
            cleaned = cleaned.replace(wrong, correct);
        }
    }
    
    return cleaned + ', Côte d\'Ivoire';
}

async function calculateRoute() {
    const pointDepart = document.getElementById('point_depart').value;
    const pointArrive = document.getElementById('point_arrive').value;

    if (!pointDepart || !pointArrive) {
        resetRoute();
        return;
    }

    document.getElementById('loading-indicator').classList.remove('hidden');
    document.getElementById('route-info').classList.add('hidden');

    try {
        // Essayer d'abord avec l'API
        const [coordsDepart, coordsArrivee] = await Promise.all([
            geocodeAddress(pointDepart),
            geocodeAddress(pointArrive)
        ]);

        if (coordsDepart && coordsArrivee) {
            const route = await calculateORSRoute(coordsDepart, coordsArrivee);
            displayRouteResults(route, coordsDepart, coordsArrivee);
        } else {
            // Fallback vers la méthode manuelle
            fallbackCalculation(pointDepart, pointArrive);
        }
        
    } catch (error) {
        console.error('Erreur:', error);
        fallbackCalculation(pointDepart, pointArrive);
    } finally {
        document.getElementById('loading-indicator').classList.add('hidden');
    }
}

async function calculateORSRoute(coordsDepart, coordsArrivee) {
    const body = {
        coordinates: [coordsDepart, coordsArrivee],
        profile: "driving-car",
        format: "json"
    };

    const response = await fetch('https://api.openrouteservice.org/v2/directions/driving-car', {
        method: 'POST',
        headers: {
            'Authorization': ORS_API_KEY,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(body)
    });

    if (!response.ok) {
        throw new Error('Erreur API OpenRouteService');
    }

    return await response.json();
}

function displayRouteResults(route, coordsDepart, coordsArrivee) {
    const summary = route.routes[0].summary;
    const distance = (summary.distance / 1000).toFixed(1) + ' km';
    const duration = formatDuration(summary.duration);

    document.getElementById('distance').textContent = distance;
    document.getElementById('duration').textContent = duration;
    document.getElementById('route-status').textContent = 'Calculé';
    document.getElementById('route-info').classList.remove('hidden');
    document.getElementById('durer_parcours').value = duration;

    displayRouteOnMap(route, coordsDepart, coordsArrivee);
}

function fallbackCalculation(depart, arrive) {
    // Base de données étendue des distances en Côte d'Ivoire
    const distances = {
        // Format: 'ville_depart-ville_arrivee': { distance: km, duration: minutes }
        'abidjan-aboisso': { distance: 120, duration: 120 },
        'aboisso-abidjan': { distance: 120, duration: 120 },
        'abidjan-yamoussoukro': { distance: 240, duration: 180 },
        'yamoussoukro-abidjan': { distance: 240, duration: 180 },
        'abidjan-bouaké': { distance: 350, duration: 240 },
        'bouaké-abidjan': { distance: 350, duration: 240 },
        'abidjan-daloa': { distance: 400, duration: 300 },
        'daloa-abidjan': { distance: 400, duration: 300 },
        'abidjan-korhogo': { distance: 600, duration: 420 },
        'korhogo-abidjan': { distance: 600, duration: 420 },
        'abidjan-san-pédro': { distance: 350, duration: 240 },
        'san-pédro-abidjan': { distance: 350, duration: 240 },
        'abidjan-man': { distance: 550, duration: 360 },
        'man-abidjan': { distance: 550, duration: 360 },
        'abidjan-gagnoa': { distance: 280, duration: 200 },
        'gagnoa-abidjan': { distance: 280, duration: 200 },
        'abidjan-abengourou': { distance: 180, duration: 150 },
        'abengourou-abidjan': { distance: 180, duration: 150 },
        
        // Trajets intra-Abidjan
        'abidjan-yopougon': { distance: 15, duration: 30 },
        'yopougon-abidjan': { distance: 15, duration: 30 },
        'abidjan-cocody': { distance: 12, duration: 25 },
        'cocody-abidjan': { distance: 12, duration: 25 },
        'abidjan-plateau': { distance: 0, duration: 0 },
        'plateau-abidjan': { distance: 0, duration: 0 }
    };

    const villeDepart = extractCityName(depart);
    const villeArrivee = extractCityName(arrive);
    
    const key = `${villeDepart}-${villeArrivee}`.toLowerCase();
    const keyInverse = `${villeArrivee}-${villeDepart}`.toLowerCase();
    
    const routeData = distances[key] || distances[keyInverse];
    
    if (routeData) {
        const duration = formatDuration(routeData.duration * 60);
        
        document.getElementById('durer_parcours').value = duration;
        document.getElementById('distance').textContent = routeData.distance + ' km';
        document.getElementById('duration').textContent = duration;
        document.getElementById('route-status').textContent = 'Estimé';
        document.getElementById('route-info').classList.remove('hidden');
    } else {
        // Calcul approximatif basé sur la distance à vol d'oiseau
        const distanceApprox = calculateApproximateDistance(villeDepart, villeArrivee);
        const durationApprox = formatDuration((distanceApprox / 40) * 3600); // 40 km/h de moyenne
        
        document.getElementById('durer_parcours').value = durationApprox;
        document.getElementById('distance').textContent = distanceApprox + ' km (approx)';
        document.getElementById('duration').textContent = durationApprox;
        document.getElementById('route-status').textContent = 'Approximatif';
        document.getElementById('route-info').classList.remove('hidden');
    }

    addBasicMarkers(depart, arrive);
}

function extractCityName(lieu) {
    const villes = [
        'abidjan', 'aboisso', 'yamoussoukro', 'bouaké', 'daloa', 'korhogo', 
        'san-pédro', 'man', 'gagnoa', 'abengourou', 'yopougon', 'cocody', 'plateau'
    ];
    
    const lieuLower = lieu.toLowerCase();
    
    for (const ville of villes) {
        if (lieuLower.includes(ville)) {
            return ville;
        }
    }
    
    // Si non trouvé, prendre le premier mot
    return lieu.split(',')[0].split(' ')[0].toLowerCase();
}

function calculateApproximateDistance(ville1, ville2) {
    // Coordonnées approximatives des villes
    const coordinates = {
        'abidjan': { lat: 5.359951, lng: -4.008256 },
        'aboisso': { lat: 5.466667, lng: -3.200000 },
        'yamoussoukro': { lat: 6.816111, lng: -5.274167 },
        'bouaké': { lat: 7.690000, lng: -5.030000 },
        'daloa': { lat: 6.890000, lng: -6.450000 },
        'korhogo': { lat: 9.458333, lng: -5.629167 },
        'san-pédro': { lat: 4.748333, lng: -6.636111 },
        'man': { lat: 7.406667, lng: -7.556667 },
        'gagnoa': { lat: 6.133333, lng: -5.933333 },
        'abengourou': { lat: 6.733333, lng: -3.483333 }
    };
    
    const coord1 = coordinates[ville1] || coordinates['abidjan'];
    const coord2 = coordinates[ville2] || coordinates['abidjan'];
    
    // Calcul de distance approximative (formule simplifiée)
    const latDiff = Math.abs(coord1.lat - coord2.lat);
    const lngDiff = Math.abs(coord1.lng - coord2.lng);
    
    // Approximation: 1° ≈ 111 km
    const distance = Math.sqrt(latDiff * latDiff + lngDiff * lngDiff) * 111;
    
    return Math.round(distance);
}

function formatDuration(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    
    if (hours === 0) {
        return `${minutes} min`;
    } else if (minutes === 0) {
        return `${hours} h`;
    } else {
        return `${hours} h ${minutes} min`;
    }
}

function displayRouteOnMap(route, coordsDepart, coordsArrivee) {
    // Nettoyer la carte
    map.eachLayer((layer) => {
        if (layer instanceof L.Marker || layer instanceof L.Polyline) {
            map.removeLayer(layer);
        }
    });

    const latLngDepart = [coordsDepart[1], coordsDepart[0]];
    const latLngArrivee = [coordsArrivee[1], coordsArrivee[0]];

    // Ajouter les marqueurs
    L.marker(latLngDepart)
        .addTo(map)
        .bindPopup(`<b>Départ:</b> ${document.getElementById('point_depart').value}`)
        .openPopup();

    L.marker(latLngArrivee)
        .addTo(map)
        .bindPopup(`<b>Arrivée:</b> ${document.getElementById('point_arrive').value}`);

    // Afficher l'itinéraire
    const coordinates = route.routes[0].geometry.coordinates.map(coord => [coord[1], coord[0]]);
    L.polyline(coordinates, { color: '#fea219', weight: 5, opacity: 0.7 })
        .addTo(map);

    // Ajuster la vue
    const bounds = L.latLngBounds([latLngDepart, latLngArrivee]);
    map.fitBounds(bounds, { padding: [20, 20] });
}

function addBasicMarkers(depart, arrive) {
    const coords = {
        'abidjan': [5.359951, -4.008256],
        'aboisso': [5.466667, -3.200000],
        'yamoussoukro': [6.816111, -5.274167],
        'bouaké': [7.690000, -5.030000],
        'daloa': [6.890000, -6.450000],
        'korhogo': [9.458333, -5.629167],
        'san-pédro': [4.748333, -6.636111],
        'man': [7.406667, -7.556667],
        'gagnoa': [6.133333, -5.933333],
        'abengourou': [6.733333, -3.483333]
    };

    const coordDepart = getCoordinates(depart, coords);
    const coordArrivee = getCoordinates(arrive, coords);

    if (coordDepart) {
        L.marker(coordDepart).addTo(map).bindPopup(`Départ: ${depart}`).openPopup();
    }
    if (coordArrivee) {
        L.marker(coordArrivee).addTo(map).bindPopup(`Arrivée: ${arrive}`);
    }

    if (coordDepart && coordArrivee) {
        const bounds = L.latLngBounds([coordDepart, coordArrivee]);
        map.fitBounds(bounds, { padding: [20, 20] });
    }
}

function getCoordinates(lieu, coords) {
    const lieuLower = lieu.toLowerCase();
    for (const [ville, coord] of Object.entries(coords)) {
        if (lieuLower.includes(ville)) {
            return coord;
        }
    }
    return coords['abidjan'];
}

function resetRoute() {
    document.getElementById('durer_parcours').value = '';
    document.getElementById('route-info').classList.add('hidden');
    
    map.eachLayer((layer) => {
        if (layer instanceof L.Marker || layer instanceof L.Polyline) {
            map.removeLayer(layer);
        }
    });
}

function triggerCalculation() {
    if (calculationTimeout) {
        clearTimeout(calculationTimeout);
    }
    calculationTimeout = setTimeout(calculateRoute, 1500);
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    initMap();

    document.getElementById('point_depart').addEventListener('input', triggerCalculation);
    document.getElementById('point_arrive').addEventListener('input', triggerCalculation);
});
</script>

<style>
#map {
    border-radius: 8px;
}

.leaflet-container {
    font-family: inherit;
}

@media (max-width: 768px) {
    #map {
        height: 300px;
    }
}
</style>
@endsection