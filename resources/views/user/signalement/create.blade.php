@extends('user.layouts.template')

@section('content')
    <div class="py-4 sm:py-8 px-3 sm:px-6 lg:px-8">
        <div class="mx-auto w-full max-w-3xl">
            
            <!-- Header Section -->
            <div class="text-center mb-10">
                <h2 class="text-xl font-extrabold text-gray-900 tracking-tight sm:text-3xl">
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

                    @if(!$enVoyage && !$preselectedReservationId)
                        <div class="rounded-xl bg-amber-50 border-l-4 border-amber-500 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-amber-500 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-bold text-amber-800 uppercase tracking-wide">Signalement Restreint</h3>
                                    <p class="text-sm font-medium text-amber-700 mt-1">
                                        Vous n'avez aucun voyage en cours actuellement. Le signalement d'incident est réservé aux passagers en cours de trajet.
                                    </p>
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
                        <div class="relative group {{ $preselectedReservationId ? 'hidden' : '' }}">
                            <label for="programme_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-route text-red-500 mr-1"></i> Sélectionner le trajet concerné
                            </label>
                            <div class="relative">
                                <select id="programme_id" name="programme_id" {{ $preselectedReservationId ? '' : 'required' }} onchange="updateVehicleInfo(this)"
                                    {{ (!$enVoyage && !$preselectedReservationId) ? 'disabled' : '' }}
                                    class="block w-full pl-4 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent sm:text-sm rounded-xl shadow-sm transition-all duration-300 appearance-none bg-gray-50 hover:bg-white {{ (!$enVoyage && !$preselectedReservationId) ? 'opacity-50 cursor-not-allowed' : '' }}">
                                    <option value="">-- Choisissez votre voyage d'aujourd'hui --</option>
                                    @foreach($reservations as $reservation)
                                        @php 
                                            $prog = $reservation->programme; 
                                            $compagnie = $prog->compagnie;
                                            $vehicule = $prog->vehicule;
                                        @endphp
                                        <option value="{{ $prog->id }}" 
                                                {{ $preselectedReservationId == $reservation->id ? 'selected' : '' }}
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

                        @if($selectedReservation)
                            <input type="hidden" name="programme_id" value="{{ $selectedReservation->programme_id }}">
                            <div class="bg-red-50 p-4 rounded-xl border border-red-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-red-600 shadow-sm">
                                        <i class="fas fa-ticket-alt"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Référence Voyage</p>
                                        <p class="text-sm font-bold text-gray-900">{{ $selectedReservation->programme->point_depart }} → {{ $selectedReservation->programme->point_arrive }}</p>
                                        @if($actualVoyage && $actualVoyage->statut === 'en_cours')
                                            @php
                                                $dateV = \Carbon\Carbon::parse($selectedReservation->date_voyage)->format('Y-m-d');
                                                $arrTime = \Carbon\Carbon::parse($dateV . ' ' . $selectedReservation->programme->heure_arrive);
                                                if (\Carbon\Carbon::parse($selectedReservation->programme->heure_arrive)->lt(\Carbon\Carbon::parse($selectedReservation->programme->heure_depart))) {
                                                    $arrTime->addDay();
                                                }
                                            @endphp
                                            <div class="mt-2 flex items-center gap-2">
                                                <p class="text-[11px] font-bold text-blue-600 flex items-center gap-1">
                                                    <i class="far fa-clock"></i> Temps restant : 
                                                </p>
                                                <span class="inline-flex items-center font-mono bg-blue-100 text-blue-700 px-3 py-0.5 rounded-full text-[11px] font-black shadow-sm border border-blue-200 timer-display" 
                                                      data-arrival="{{ $arrTime->toIso8601String() }}">
                                                    --:--:--
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-white text-red-600 text-[10px] font-black rounded-lg border border-red-100 uppercase">Pré-sélectionné</span>
                            </div>
                        @endif

                        <!-- Info Véhicule Dynamique -->
                        <div id="vehicule-section" class="{{ $preselectedReservationId ? 'block' : 'hidden' }} mt-6 bg-gradient-to-r from-gray-50 to-white p-5 rounded-xl border border-gray-200 shadow-inner">
                            <div class="flex items-start gap-4">
                                <div class="bg-white p-3 rounded-full shadow-sm">
                                    <i class="fas fa-bus text-red-500 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Véhicule Identifié</h4>
                                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        @if($actualVehicule)
                                            <div>
                                                <p class="text-xs text-gray-500">Compagnie</p>
                                                <p class="font-medium text-gray-800 text-sm">{{ $actualVehicule->compagnie->name ?? 'N/A' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Immatriculation</p>
                                                <p class="font-medium text-gray-800 text-sm">{{ $actualVehicule->immatriculation }} ({{ $actualVehicule->marque }})</p>
                                            </div>
                                        @else
                                            <div>
                                                <p class="text-xs text-gray-500">Compagnie</p>
                                                <p id="display-compagnie" class="font-medium text-gray-800 text-sm">-</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Immatriculation</p>
                                                <p id="display-vehicule" class="font-medium text-gray-800 text-sm">-</p>
                                            </div>
                                        @endif
                                    </div>

                                    @if($actualVoyage && $actualVoyage->chauffeur)
                                        <div class="mt-3 bg-white/50 p-2 rounded-lg border border-gray-100">
                                            <p class="text-[10px] text-gray-400 uppercase font-bold">Chauffeur en service</p>
                                            <p class="text-sm font-medium text-gray-700">
                                                <i class="fas fa-user-tie text-red-400 mr-2"></i> {{ $actualVoyage->chauffeur->name }} {{ $actualVoyage->chauffeur->prenom }}
                                            </p>
                                        </div>
                                    @endif

                                    <!-- <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Passagers à bord</p>
                                            <p id="display-occupancy" class="font-black text-red-600 text-lg">
                                                @if($preselectedReservationId)
                                                    chargement...
                                                @else
                                                    -
                                                @endif
                                            </p>
                                        </div>
                                        <button type="button" onclick="showPassengersPopup()" 
                                            {{ (!$enVoyage && !$preselectedReservationId) ? 'disabled' : '' }}
                                            class="bg-red-50 text-red-600 px-4 py-2 rounded-xl text-xs font-bold hover:bg-red-100 transition-colors border border-red-100 flex items-center gap-2 {{ (!$enVoyage && !$preselectedReservationId) ? 'opacity-50 cursor-not-allowed' : '' }}">
                                            <i class="fas fa-users"></i> Liste Passagers
                                        </button>
                                    </div> -->
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
                                    <select id="type" name="type" required onchange="handleProblemType(this.value)"
                                        {{ (!$enVoyage && !$preselectedReservationId) ? 'disabled' : '' }}
                                        class="block w-full pl-4 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent sm:text-sm rounded-xl shadow-sm transition-all duration-300 appearance-none bg-gray-50 hover:bg-white {{ (!$enVoyage && !$preselectedReservationId) ? 'opacity-50 cursor-not-allowed' : '' }}">
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
                                
                                <!-- Lien pour afficher la photo manuellement (pour les cas non urgents) -->
                                <div id="manual-photo-trigger" class="hidden mt-3 text-right">
                                    <button type="button" onclick="showPhotoUploader()" class="text-sm text-red-600 hover:text-red-800 underline flex items-center justify-end gap-1 ml-auto">
                                        <i class="fas fa-camera"></i> Ajouter une photo justificative ?
                                    </button>
                                </div>
                            </div>

                            <!-- Photo Upload (Caché par défaut) -->
                            <div id="photo-container" class="hidden animate-fade-in-down">
                                <label for="photo" class="block text-sm font-medium text-gray-700 mb-2" id="photo-label">
                                    <i class="fas fa-camera text-blue-500 mr-1"></i> Preuve Photo
                                </label>
                                <label class="flex justify-center px-6 pt-3 pb-3 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer hover:border-red-400 hover:bg-red-50 transition-colors group">
                                    <div class="space-y-1 text-center">
                                        <i class="fas fa-image text-gray-400 text-2xl group-hover:text-red-500 transition-colors"></i>
                                        <div class="text-xs text-gray-600">
                                            <span class="font-medium text-red-600 hover:text-red-500">Cliquez pour ajouter</span>
                                        </div>
                                        <input id="photo" name="photo" type="file" class="sr-only" accept="image/*" onchange="previewImage(this)"
                                            {{ (!$enVoyage && !$preselectedReservationId) ? 'disabled' : '' }}>
                                        <p class="text-xs text-gray-500" id="photo-filename">PNG, JPG (Max 10MB)</p>
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
                                        <p>Veuillez prendre une photo de la situation si cela est possible sans danger.</p>
                                        <p class="font-bold mt-1">Géolocalisation activée pour les secours.</p>
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
                                {{ (!$enVoyage && !$preselectedReservationId) ? 'disabled' : '' }}
                                class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-xl bg-gray-50 hover:bg-white transition-colors p-4 {{ (!$enVoyage && !$preselectedReservationId) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                placeholder="Décrivez la situation avec le plus de précisions possible..."></textarea>
                        </div>
                    </div>

                    <!-- Hidden Fields -->
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">

                    <!-- Submit Button -->
                    <div class="pt-6">
                        <button type="submit" id="submitBtn"
                            {{ (!$enVoyage && !$preselectedReservationId) ? 'disabled' : '' }}
                            class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-lg text-lg font-bold text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transform hover:-translate-y-1 transition-all duration-200 {{ (!$enVoyage && !$preselectedReservationId) ? 'opacity-50 cursor-not-allowed' : '' }}">
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
        function handleProblemType(val) {
        const photoContainer = document.getElementById('photo-container');
        const manualTrigger = document.getElementById('manual-photo-trigger');
        const accidentWarning = document.getElementById('accident-warning');
        const photoLabel = document.getElementById('photo-label');

        // Reset visuel initial
        photoContainer.classList.add('hidden');
        manualTrigger.classList.add('hidden');
        accidentWarning.classList.add('hidden');

        if (val === 'accident') {
            // CAS ACCIDENT : On force l'affichage de l'upload et du message d'urgence
            photoContainer.classList.remove('hidden');
            accidentWarning.classList.remove('hidden');
            photoLabel.innerHTML = '<i class="fas fa-camera text-red-500 mr-1"></i> Photo de l\'accident (Fortement recommandé)';
            
            getLocationImportant(); // Force refresh loc
        } 
        else if (val !== "") {
            // AUTRES CAS (Panne, Retard, etc) : On affiche juste le petit lien
            manualTrigger.classList.remove('hidden');
            photoLabel.innerHTML = '<i class="fas fa-camera text-blue-500 mr-1"></i> Photo (Optionnel)';
        }
    }

    // Fonction déclenchée quand on clique sur le lien texte "Ajouter une photo"
    function showPhotoUploader() {
        document.getElementById('manual-photo-trigger').classList.add('hidden'); // Cache le lien
        document.getElementById('photo-container').classList.remove('hidden'); // Affiche l'uploader
    }
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

        @if($selectedReservation)
            document.addEventListener('DOMContentLoaded', function() {
                const progId = "{{ $selectedReservation->programme_id }}";
                if(progId) fetchOccupancy(progId);
            });
        @endif

        // Countdown Timer Logic
        function updateTimers() {
            const timers = document.querySelectorAll('.timer-display[data-arrival]');
            timers.forEach(timer => {
                const arrivalTime = new Date(timer.dataset.arrival).getTime();
                const now = new Date().getTime();
                const distance = arrivalTime - now;
                
                if (distance < 0) {
                    timer.innerHTML = "Arrivée imminente";
                    timer.classList.remove('text-blue-500', 'bg-blue-50');
                    timer.classList.add('text-emerald-500', 'bg-emerald-50');
                    return;
                }
                
                const hours = Math.floor(distance / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                timer.innerHTML = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            });
        }

        if (document.querySelectorAll('.timer-display[data-arrival]').length > 0) {
            updateTimers();
            setInterval(updateTimers, 1000);
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
                
                // NOUVEAU: Récupérer l'occupation du véhicule
                fetchOccupancy(select.value);

            } else {
                section.classList.add('hidden');
            }
        }

        let currentPassengers = [];

        function fetchOccupancy(programmeId) {
            const occupancyDisplay = document.getElementById('display-occupancy');
            const timer = document.querySelector('.timer-display');
            
            if (occupancyDisplay) occupancyDisplay.textContent = '...';
            
            fetch(`/api/program/${programmeId}/occupancy`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (occupancyDisplay) occupancyDisplay.textContent = `${data.count} / ${data.total_capacity} passagers à bord`;
                        currentPassengers = data.passengers;
                    }
                })
                .catch(err => console.error(err));
            
            // Also fetch live tracking for dynamic arrival if possible
            fetch("{{ route('user.tracking.location') }}")
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.location.estimated_arrival && timer) {
                        timer.setAttribute('data-arrival', data.location.estimated_arrival);
                        updateTimers();
                    }
                })
                .catch(err => console.error(err));
        }

        @if($selectedReservation)
            // Periodic update for signalement page timer (every 10s)
            @if($actualVoyage && $actualVoyage->statut === 'en_cours')
                setInterval(() => fetchOccupancy("{{ $selectedReservation->programme_id }}"), 10000);
            @endif
        @endif

        function showPassengersPopup() {
            if (currentPassengers.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Liste des passagers',
                    text: 'Aucun autre passager confirmé sur ce trajet pour le moment.',
                    confirmButtonColor: '#dc2626'
                });
                return;
            }

            let passengerHtml = `
                <div class="max-h-60 overflow-y-auto mt-4 px-2">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-3 py-2">Place</th>
                                <th class="px-3 py-2">Nom & Prénoms</th>
                                <th class="px-3 py-2">Contact</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            currentPassengers.forEach(p => {
                passengerHtml += `
                    <tr class="border-b">
                        <td class="px-3 py-2 font-bold text-red-600">#${p.seat_number}</td>
                        <td class="px-3 py-2 font-medium">${p.passager_nom} ${p.passager_prenom}</td>
                        <td class="px-3 py-2 text-gray-600">${p.passager_telephone || 'N/A'}</td>
                    </tr>
                `;
            });

            passengerHtml += `
                        </tbody>
                    </table>
                </div>
            `;

            Swal.fire({
                title: 'Passagers du Bus',
                html: passengerHtml,
                width: '600px',
                confirmButtonText: 'Fermer',
                confirmButtonColor: '#dc2626',
                customClass: {
                    title: 'text-xl font-black text-gray-900',
                    html_container: 'text-left'
                }
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