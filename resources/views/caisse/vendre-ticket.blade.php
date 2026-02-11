@extends('caisse.layouts.template')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Vendre des Tickets</h1>
            <p class="text-lg text-gray-600">Gérez vos ventes de tickets</p>
        </div>


        <!-- Formulaire de vente -->
        <div class="bg-white rounded-3xl shadow-xl p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-[#e94e1a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
                Nouvelle Vente
            </h2>

            <form action="{{ route('caisse.vendre-ticket.submit') }}" method="POST" id="ticket-form">
                @csrf

                <!-- Sélection du programme -->
<div class="mb-6">
    <label class="block text-sm font-semibold text-gray-700 mb-2">Programme (Départs d'aujourd'hui) *</label>
    <select name="programme_id" id="programme_id" required
        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
        <option value="">-- Sélectionnez un départ --</option>
        @foreach($programmes as $programme)
            <option value="{{ $programme->id }}" 
                data-vehicle-id="{{ $programme->vehicule_id }}"
                data-trajet="{{ $programme->point_depart }} → {{ $programme->point_arrive }}"
                data-date="{{ \Carbon\Carbon::now()->format('d/m/Y') }}"
                data-heure="{{ \Carbon\Carbon::parse($programme->heure_depart)->format('H:i') }}">
                {{-- 1. L'heure (c'est le plus important pour un départ imminent) --}}
                {{ \Carbon\Carbon::parse($programme->heure_depart)->format('H:i') }} 
                
                {{-- 2. Le trajet --}}
                | {{ $programme->point_depart }} → {{ $programme->point_arrive }} 
                
                {{-- 3. La date (On affiche explicitement la date d'aujourd'hui) --}}
                | {{ \Carbon\Carbon::now()->format('d/m/Y') }} 
                
                {{-- 4. Infos véhicule et prix --}}
                | {{ $programme->vehicule->immatriculation ?? 'Bus' }}
                | {{ number_format($programme->montant_billet, 0, ',', ' ') }} FCFA
            </option>
        @endforeach
    </select>
    @error('programme_id')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>


            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- COLONNE GAUCHE : Formulaire (Tickets + Passagers) -->
                <div class="lg:col-span-7 space-y-6">
                    
                    <!-- Nombre de tickets -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre de tickets *</label>
                        <div class="flex items-center">
                            <button type="button" onclick="decrementTickets()" 
                                class="px-4 py-3 bg-gray-200 text-gray-700 font-bold rounded-l-xl hover:bg-gray-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>
                            <input type="number" name="nombre_tickets" id="nombre_tickets" value="1" min="1" required
                                class="w-32 px-4 py-3 border-y border-gray-300 text-center font-bold text-lg focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                            <button type="button" onclick="incrementTickets()" 
                                class="px-4 py-3 bg-gray-200 text-gray-700 font-bold rounded-r-xl hover:bg-gray-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                        @error('nombre_tickets')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Informations des passagers -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-4">Informations des Passagers</label>
                        <div class="max-h-[60vh] overflow-y-auto scrollbar-thin pr-2 rounded-xl border border-gray-100 bg-gray-50/50 p-2">
                            <div id="passengers-container" class="space-y-4">
                                <!-- Les formulaires passagers seront ajoutés ici dynamiquement -->
                            </div>
                        </div>
                    </div>

                    <!-- Bouton de soumission -->
                    <div class="flex gap-4 pt-4">
                        <button type="submit" 
                            class="px-8 py-4 bg-[#e94e1a] text-white font-bold rounded-xl hover:bg-[#d33d0f] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl w-full md:w-auto">
                            Confirmer la vente
                        </button>
                        <button type="reset" 
                            class="px-8 py-4 bg-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-300 transition-all duration-200 w-full md:w-auto">
                            Réinitialiser
                        </button>
                    </div>

                </div>

                <!-- COLONNE DROITE : Plan de Siège Animé -->
                <div class="lg:col-span-5 lg:sticky lg:top-4 order-first lg:order-last">
                    <div id="seat-map-wrapper" class="overflow-hidden transition-all duration-500 max-h-0 opacity-0">
                        <div class="border border-gray-200 rounded-2xl p-6 bg-gray-50 shadow-inner">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center justify-between">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-bus text-[#e94e1a]"></i>
                                    <span>Disponibilité</span>
                                </span>
                                <span id="vehicle-info" class="text-xs font-bold text-gray-500 bg-white px-2 py-1 rounded border border-gray-200"></span>
                            </h3>

                            <!-- Nouveau Bloc d'Informations -->
                            <div id="trip-info" class="mb-4 bg-white p-4 rounded-xl border border-gray-100 shadow-sm hidden">
                                <div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-50">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Trajet</span>
                                    <span id="info-trajet" class="text-sm font-bold text-gray-800"></span>
                                </div>
                                <div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-50">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Départ</span>
                                    <div class="text-right">
                                        <span id="info-date" class="text-xs font-semibold text-gray-500 block"></span>
                                        <span id="info-heure" class="text-sm font-bold text-gray-800"></span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Occupation</span>
                                    <span id="info-occupied" class="text-sm font-bold text-[#e94e1a]"></span>
                                </div>
                            </div>
                            
                            <div id="seat-map-container" class="flex justify-center min-h-[200px] items-center bg-white rounded-xl border border-gray-100 p-4">
                                <!-- Le SVG/HTML du bus sera injecté ici -->
                                <div class="animate-pulse flex space-x-4">
                                    <div class="h-12 w-12 bg-gray-200 rounded"></div>
                                    <div class="h-12 w-12 bg-gray-200 rounded"></div>
                                </div>
                            </div>

                            <div class="mt-4 flex justify-center gap-6 text-sm bg-white p-3 rounded-xl border border-gray-100">
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 rounded-md bg-white border border-gray-300 shadow-sm"></div>
                                    <span class="text-gray-600 font-medium">Libre</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 rounded-md bg-[#ef4444] shadow-sm"></div>
                                    <span class="text-gray-600 font-medium">Occupé</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Configuration des rangées (copié depuis hostesse)
    const typeRangeConfig = {
        '1x1': { placesGauche: 1, placesDroite: 1 },
        '2x1': { placesGauche: 2, placesDroite: 1 },
        '1x2': { placesGauche: 1, placesDroite: 2 },
        '2x2': { placesGauche: 2, placesDroite: 2 },
        '3x2': { placesGauche: 3, placesDroite: 2 },
        '2x3': { placesGauche: 2, placesDroite: 3 }
    };

    // Écouteur pour le changement de programme
    document.getElementById('programme_id').addEventListener('change', function() {
        const programId = this.value;
        const wrapper = document.getElementById('seat-map-wrapper');
        const container = document.getElementById('seat-map-container');
        const info = document.getElementById('vehicle-info');
        
        // Info Elements
        const tripInfoDiv = document.getElementById('trip-info');
        const infoTrajet = document.getElementById('info-trajet');
        const infoDate = document.getElementById('info-date');
        const infoHeure = document.getElementById('info-heure');
        const infoOccupied = document.getElementById('info-occupied');
        
        if (!programId) {
            wrapper.style.maxHeight = '0';
            wrapper.style.opacity = '0';
            if(tripInfoDiv) tripInfoDiv.classList.add('hidden');
            return;
        }

        const selectedOption = this.options[this.selectedIndex];
        const vehicleId = selectedOption.getAttribute('data-vehicle-id');
        
        // Get data from attributes
        const trajet = selectedOption.getAttribute('data-trajet');
        const date = selectedOption.getAttribute('data-date');
        const heure = selectedOption.getAttribute('data-heure');
        
        if(!vehicleId) return;

        // Animation d'ouverture (placeholder loading)
        wrapper.style.maxHeight = '800px'; 
        wrapper.style.opacity = '1';
        container.innerHTML = '<div class="animate-pulse flex space-x-4 p-4"><div class="h-12 w-full bg-gray-200 rounded">Chargement...</div></div>';

        // Fetch Data
        const dateQuery = new Date().toISOString().split('T')[0];
        
        fetch(`{{ url("/caisse/api/vehicle") }}/${vehicleId}?date=${dateQuery}&program_id=${programId}`)
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    info.textContent = `${data.vehicule.marque || 'Bus'} - ${data.vehicule.immatriculation}`;
                    
                    // Update Trip Info
                    if(tripInfoDiv) {
                        tripInfoDiv.classList.remove('hidden');
                        if(infoTrajet) infoTrajet.textContent = trajet;
                        if(infoDate) infoDate.textContent = date;
                        if(infoHeure) infoHeure.textContent = heure;
                        
                        // Calculate Occupation
                        const occupiedCount = (data.reserved_seats || []).length;
                        const totalSeats = parseInt(data.vehicule.nombre_place);
                        const percentage = totalSeats > 0 ? Math.round((occupiedCount / totalSeats) * 100) : 0;
                        
                        if(infoOccupied) {
                            infoOccupied.innerHTML = `
                                <span class="mr-1">${occupiedCount} / ${totalSeats}</span>
                                <span class="text-xs font-normal text-gray-500">(${percentage}%)</span>
                            `;
                        }
                    }

                    container.innerHTML = generatePlacesVisualization(data.vehicule, data.reserved_seats || []);
                } else {
                    container.innerHTML = '<p class="text-red-500">Erreur de chargement du plan</p>';
                }
            })
            .catch(err => {
                console.error(err);
                container.innerHTML = '<p class="text-red-500">Erreur de connexion</p>';
            });
    });

    function generatePlacesVisualization(vehicle, reservedSeats) {
        let config = typeRangeConfig[vehicle.type_range] || typeRangeConfig['2x2'];
        const placesGauche = config.placesGauche;
        const placesDroite = config.placesDroite;
        const placesParRanger = placesGauche + placesDroite;
        const totalPlaces = parseInt(vehicle.nombre_place);
        const nombreRanger = Math.ceil(totalPlaces / placesParRanger);
        
        let html = `
            <div class="border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm w-full max-w-2xl">
                <div class="grid grid-cols-[60px_1fr_40px_1fr] bg-gray-50 border-b border-gray-100 py-3 px-4">
                    <div class="text-xs font-black text-gray-400 uppercase text-center">RANG</div>
                    <div class="text-xs font-black text-gray-400 uppercase text-center">GAUCHE</div>
                    <div class="text-xs font-black text-gray-400 uppercase text-center border-l border-r border-gray-200 mx-2">ALLÉE</div>
                    <div class="text-xs font-black text-gray-400 uppercase text-center">DROITE</div>
                </div>
                
                <div class="max-h-[300px] overflow-y-auto scrollbar-thin p-4 space-y-3">
        `;

        let numeroPlace = 1;
        for (let r = 1; r <= nombreRanger; r++) {
            html += `<div class="grid grid-cols-[60px_1fr_40px_1fr] items-center">
                        <div class="text-center font-black text-gray-300 text-sm">R${r}</div>
                        <div class="flex justify-center gap-3">`;
            
            // Gauche
            for (let i = 0; i < placesGauche; i++) {
                if (numeroPlace <= totalPlaces) {
                    const isReserved = reservedSeats.includes(numeroPlace);
                    const styleClass = isReserved 
                        ? 'bg-[#ef4444] text-white border-transparent shadow-sm' 
                        : 'bg-white text-gray-700 border-gray-300 shadow-sm';
                    
                    html += `<div class="w-8 h-8 border rounded flex items-center justify-center font-bold text-xs transition-all ${styleClass}">
                                ${numeroPlace}
                             </div>`;
                    numeroPlace++;
                }
            }
            
            html += `</div>
                    <div class="flex justify-center h-full"><div class="w-px bg-gray-100 h-full"></div></div>
                    <div class="flex justify-center gap-3">`;
            
            // Droite
            for (let i = 0; i < placesDroite; i++) {
                if (numeroPlace <= totalPlaces) {
                    const isReserved = reservedSeats.includes(numeroPlace);
                    const styleClass = isReserved 
                        ? 'bg-[#ef4444] text-white border-transparent shadow-sm' 
                        : 'bg-white text-gray-700 border-gray-300 shadow-sm';

                    html += `<div class="w-8 h-8 border rounded flex items-center justify-center font-bold text-xs transition-all ${styleClass}">
                                ${numeroPlace}
                             </div>`;
                    numeroPlace++;
                }
            }
            html += `</div></div>`;
        }
        html += `</div></div>`;
        return html;
    }

    function incrementTickets() {
        const input = document.getElementById('nombre_tickets');
        input.value = parseInt(input.value) + 1;
        updatePassengerForms();
    }

    function decrementTickets() {
        const input = document.getElementById('nombre_tickets');
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
            updatePassengerForms();
        }
    }

    document.getElementById('nombre_tickets').addEventListener('input', updatePassengerForms);

    function updatePassengerForms() {
        const count = parseInt(document.getElementById('nombre_tickets').value) || 1;
        const container = document.getElementById('passengers-container');
        
        // Sauvegarder les valeurs actuelles pour ne pas les perdre
        const currentData = [];
        const inputs = container.querySelectorAll('input');
        inputs.forEach(input => {
            currentData[input.name] = input.value;
        });

        container.innerHTML = '';

        for (let i = 1; i <= count; i++) {
            const passengerForm = `
                <div class="p-4 border border-gray-300 rounded-xl bg-gray-50">
                    <h4 class="font-bold text-gray-900 mb-3">Passager ${i}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                            <input type="text" name="passenger_details[${i-1}][nom]" required
                                value="${currentData[`passenger_details[${i-1}][nom]`] || ''}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                            <input type="text" name="passenger_details[${i-1}][prenom]" required
                                value="${currentData[`passenger_details[${i-1}][prenom]`] || ''}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                            <input type="tel" name="passenger_details[${i-1}][telephone]" required
                                value="${currentData[`passenger_details[${i-1}][telephone]`] || ''}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="passenger_details[${i-1}][email]"
                                value="${currentData[`passenger_details[${i-1}][email]`] || ''}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                        </div>
                    </div>
                </div>
            `;
            container.innerHTML += passengerForm;
        }
    }

    // Initialiser les formulaires au chargement
    document.addEventListener('DOMContentLoaded', updatePassengerForms);

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Succès!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#e94e1a',
            timer: 3000,
            timerProgressBar: true
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Erreur!',
            html: '@foreach($errors->all() as $error) {{ $error }}<br> @endforeach',
            confirmButtonColor: '#d33'
        });
    @endif
</script>
@endsection
