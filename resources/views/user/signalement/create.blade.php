@extends('user.layouts.template')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-red-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            
            <!-- Header Section -->
            <div class="text-center mb-10">
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl">
                    Signaler un Problème
                </h2>
                <p class="mt-2 text-lg text-gray-600">
                    Nous sommes là pour vous assister. Remplissez ce formulaire pour nous informer de la situation.
                </p>
            </div>

            <!-- Main Form Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-red-600 p-1 h-2"></div> <!-- Decorative Top Bar -->
                
                <form action="{{ route('signalement.store') }}" method="POST" class="p-8 sm:p-10 space-y-8" id="signalementForm" enctype="multipart/form-data">
                    @csrf

                    @if(session('error'))
                        <div class="rounded-xl bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Section 1: Le Voyage -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2 mb-4 border-b border-gray-100 pb-2">
                            <span class="bg-red-100 text-red-600 w-8 h-8 rounded-full flex items-center justify-center text-sm">1</span>
                            Détails du Voyage
                        </h3>
                        
                        <!-- Voyage Selection -->
                        <div class="relative group">
                            <label for="programme_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-route text-red-500 mr-1"></i> Sélectionner le trajet concerné
                            </label>
                            <div class="relative">
                                <select id="programme_id" name="programme_id" required onchange="updateVehicleInfo(this)"
                                    class="block w-full pl-4 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent sm:text-sm rounded-xl shadow-sm transition-all duration-300 appearance-none bg-gray-50 hover:bg-white">
                                    <option value="">-- Choisissez votre voyage d'aujourd'hui --</option>
                                    @foreach($reservations as $reservation)
                                        @php 
                                            $prog = $reservation->programme; 
                                            $compagnie = $prog->compagnie;
                                            $vehicule = $prog->vehicule;
                                        @endphp
                                        <option value="{{ $prog->id }}" 
                                                data-compagnie-id="{{ $compagnie->id }}"
                                                data-compagnie-name="{{ $compagnie->name ?? 'Compagnie' }}"
                                                data-vehicule-id="{{ $vehicule->id ?? '' }}"
                                                data-vehicule-immatriculation="{{ $vehicule->immatriculation ?? 'Non assigné' }}"
                                                data-vehicule-marque="{{ $vehicule->marque ?? '' }}">
                                            {{ $prog->point_depart }} → {{ $prog->point_arrive }} ({{ $prog->heure_depart_formatee }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Info Véhicule Dynamique -->
                        <div id="vehicule-section" class="hidden mt-6 bg-gradient-to-r from-gray-50 to-white p-5 rounded-xl border border-gray-200 shadow-inner">
                            <div class="flex items-start gap-4">
                                <div class="bg-white p-3 rounded-full shadow-sm">
                                    <i class="fas fa-bus text-red-500 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Véhicule Identifié</h4>
                                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs text-gray-500">Compagnie</p>
                                            <p id="display-compagnie" class="font-medium text-gray-800 text-sm">-</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Immatriculation</p>
                                            <p id="display-vehicule" class="font-medium text-gray-800 text-sm">-</p>
                                        </div>
                                    </div>

                                    <!-- Changement de véhicule -->
                                    <div class="mt-4 pt-3 border-t border-gray-100">
                                        <label for="vehicule_id" class="text-xs text-gray-500 flex items-center gap-1 cursor-pointer mb-2">
                                            <i class="fas fa-edit"></i> Le véhicule a été remplacé ?
                                        </label>
                                        <select id="vehicule_id" name="vehicule_id" class="block w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-white py-2">
                                            <option value="">-- Conserver le véhicule prévu --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Le Problème -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2 mb-4 border-b border-gray-100 pb-2">
                            <span class="bg-red-100 text-red-600 w-8 h-8 rounded-full flex items-center justify-center text-sm">2</span>
                            Nature du Problème
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Type Selection -->
                            <div class="relative">
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-exclamation-triangle text-orange-500 mr-1"></i> Type d'incident
                                </label>
                                <div class="relative">
                                    <select id="type" name="type" required onchange="checkAccident(this.value)"
                                        class="block w-full pl-4 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent sm:text-sm rounded-xl shadow-sm transition-all duration-300 appearance-none bg-gray-50 hover:bg-white">
                                        <option value="">-- Sélectionner --</option>
                                        <option value="accident">🚨 Accident Grave</option>
                                        <option value="panne">🔧 Panne Mécanique</option>
                                        <option value="retard">⏱️ Retard Important</option>
                                        <option value="comportement">😡 Comportement Inapproprié</option>
                                        <option value="autre">📝 Autre Sujet</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Photo Upload -->
                            <div>
                                <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-camera text-blue-500 mr-1"></i> Photo (Optionnel)
                                </label>
                                <label class="flex justify-center px-6 pt-3 pb-3 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer hover:border-red-400 hover:bg-red-50 transition-colors group">
                                    <div class="space-y-1 text-center">
                                        <i class="fas fa-image text-gray-400 text-2xl group-hover:text-red-500 transition-colors"></i>
                                        <div class="text-xs text-gray-600">
                                            <span class="font-medium text-red-600 hover:text-red-500">Ajouter une image</span>
                                        </div>
                                        <input id="photo" name="photo" type="file" class="sr-only" accept="image/*" onchange="previewImage(this)">
                                        <p class="text-xs text-gray-500" id="photo-filename">PNG, JPG jusqu'à 10MB</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Warning Accident -->
                        <div id="accident-warning" class="hidden mt-6 bg-red-50 border-l-4 border-red-600 p-4 rounded-r-lg animate-fade-in-down">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-ambulance text-red-600 text-2xl animate-pulse"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-bold text-red-800 uppercase tracking-wide">Urgence Détectée</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>Nous activons la géolocalisation haute précision pour guider les secours.</p>
                                        <p class="font-bold mt-1">Gardez votre calme, les pompiers seront alertés.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mt-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-align-left text-gray-400 mr-1"></i> Description détaillée
                            </label>
                            <textarea id="description" name="description" rows="5" required
                                class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-xl bg-gray-50 hover:bg-white transition-colors p-4"
                                placeholder="Décrivez la situation avec le plus de précisions possible..."></textarea>
                        </div>
                    </div>

                    <!-- Hidden Fields -->
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">

                    <!-- Submit Button -->
                    <div class="pt-6">
                        <button type="submit" id="submitBtn"
                            class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-lg text-lg font-bold text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transform hover:-translate-y-1 transition-all duration-200">
                            <i class="fas fa-satellite-dish mr-2 animate-pulse"></i> Envoyer le Signalement
                        </button>
                    </div>
                </form>
            </div>
            
            <p class="text-center text-gray-400 text-xs mt-8">
                &copy; {{ date('Y') }} Sécurité Transport. En cas d'urgence vitale, composez le 180.
            </p>
        </div>
    </div>

    <!-- Scripts Logic -->
    <script>
        // Géolocalisation Initiale
        if (navigator.geolocation) {
             navigator.geolocation.getCurrentPosition(
                 (pos) => {
                     document.getElementById('latitude').value = pos.coords.latitude;
                     document.getElementById('longitude').value = pos.coords.longitude;
                 }, 
                 (err) => console.log('Géolocalisation non disponible par défaut', err),
                 { enableHighAccuracy: true }
             );
        }

        function checkAccident(val) {
            const warning = document.getElementById('accident-warning');
            if (val === 'accident') {
                warning.classList.remove('hidden');
                getLocationImportant(); // Force refresh loc
            } else {
                warning.classList.add('hidden');
            }
        }

        function getLocationImportant() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        document.getElementById('latitude').value = pos.coords.latitude;
                        document.getElementById('longitude').value = pos.coords.longitude;
                    },
                    (error) => {
                        let msg = "";
                        switch(error.code) {
                            case error.PERMISSION_DENIED: msg = "Geolocalisation refusée."; break;
                            case error.POSITION_UNAVAILABLE: msg = "Position indisponible."; break;
                            case error.TIMEOUT: msg = "Délai d'attente dépassé."; break;
                            default: msg = "Erreur inconnue.";
                        }
                        alert("⚠️ Important : " + msg + " Pour les accidents, la position est cruciale.");
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            }
        }

        function updateVehicleInfo(select) {
            const section = document.getElementById('vehicule-section');
            const displayCompagnie = document.getElementById('display-compagnie');
            const displayVehicule = document.getElementById('display-vehicule');
            const vehiculeSelect = document.getElementById('vehicule_id');
            const option = select.options[select.selectedIndex];

            if (select.value) {
                section.classList.remove('hidden');
                
                const compId = option.getAttribute('data-compagnie-id');
                const compName = option.getAttribute('data-compagnie-name');
                const vehId = option.getAttribute('data-vehicule-id'); // ID du véhicule PREVU
                const vehImmat = option.getAttribute('data-vehicule-immatriculation');
                const vehMarque = option.getAttribute('data-vehicule-marque');

                displayCompagnie.textContent = compName;
                displayVehicule.textContent = `${vehImmat} (${vehMarque})`;
                
                // Charger la liste des véhicules de la compagnie pour permettre un changement
                fetchVehicles(compId, vehId);

            } else {
                section.classList.add('hidden');
            }
        }

        function fetchVehicles(compagnieId, defaultVehiculeId) {
            const select = document.getElementById('vehicule_id');
            select.innerHTML = '<option value="">Chargement...</option>';

            fetch(`/api/company/${compagnieId}/vehicles`)
                .then(res => res.json())
                .then(data => {
                    select.innerHTML = '<option value="">-- Conserver le véhicule prévu --</option>';
                    data.forEach(v => {
                        // On n'affiche pas le véhicule prévu dans la liste des choix de "changement" pour éviter la confusion, 
                        // ou alors on le marque. Ici on l'ajoute simplement.
                        const isDefault = v.id == defaultVehiculeId ? ' (Prévu)' : '';
                        const option = document.createElement('option');
                        option.value = v.id;
                        option.textContent = `${v.immatriculation} - ${v.marque} ${v.modele}${isDefault}`;
                        if(v.id == defaultVehiculeId) option.className = "font-bold bg-gray-100";
                        select.appendChild(option);
                    });
                })
                .catch(err => {
                    console.error(err);
                    select.innerHTML = '<option value="">Erreur chargement liste</option>';
                });
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                document.getElementById('photo-filename').textContent = input.files[0].name;
            }
        }
    </script>
    
    <style>
        .animate-fade-in-down {
            animation: fadeInDown 0.5s ease-out;
        }
        @keyframes fadeInDown {
            0% { opacity: 0; transform: translateY(-10px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
@endsection