@extends('home.layouts.template')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-white to-green-50 py-8">
    <div class="container mx-auto px-4">
        
        <!-- Retour -->
        <a href="{{ url()->previous() }}" class="inline-flex items-center text-gray-600 hover:text-[#e94f1b] mb-6 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Retour aux résultats
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Colonne gauche: Détails du programme -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Carte Informations Programme -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="bg-gradient-to-r from-[#e94f1b] to-orange-500 p-6 text-white text-center">
                        <h2 class="text-2xl font-bold mb-1">Détails du voyage</h2>
                        <p class="opacity-90">Réservez vos places maintenant</p>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <!-- Compagnie -->
                        <div class="flex items-center gap-4 p-4 bg-orange-50 rounded-xl">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-sm text-[#e94f1b] text-2xl">
                                <i class="fas fa-bus"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg">{{ $programme->compagnie->name ?? 'Compagnie' }}</h3>
                                <p class="text-sm text-gray-600">Transporteur vérifié</p>
                            </div>
                        </div>

                        <!-- Date du voyage -->
                        <div class="bg-blue-50 p-4 rounded-xl flex items-center gap-4">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-blue-500 text-xl shadow-sm">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div>
                                <div class="text-xs text-blue-500 font-bold uppercase">Date du départ</div>
                                <div class="font-bold text-gray-900 text-lg">
                                    {{ \Carbon\Carbon::parse($programme->date_depart)->translatedFormat('d F Y') }}
                                </div>
                            </div>
                        </div>

                        <!-- Trajet -->
                        <div class="relative pl-8 border-l-2 border-gray-200 ml-4 space-y-8">
                            <div class="relative">
                                <div class="absolute -left-[39px] bg-white border-2 border-[#e94f1b] w-6 h-6 rounded-full flex items-center justify-center">
                                    <div class="w-2 h-2 bg-[#e94f1b] rounded-full"></div>
                                </div>
                                <h4 class="font-bold text-gray-900">{{ $programme->point_depart }}</h4>
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-clock mr-1"></i> {{ $programme->heure_depart }}
                                </div>
                            </div>

                            <div class="relative">
                                <div class="absolute -left-[39px] bg-white border-2 border-green-500 w-6 h-6 rounded-full flex items-center justify-center">
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                </div>
                                <h4 class="font-bold text-gray-900">{{ $programme->point_arrive }}</h4>
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-clock mr-1"></i> {{ $programme->heure_arrive }}
                                </div>
                            </div>
                        </div>

                        <!-- Infos supplémentaires -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-3 rounded-lg text-center">
                                <div class="text-gray-500 text-xs uppercase font-bold mb-1">Durée</div>
                                <div class="font-bold text-gray-900">{{ $programme->durer_parcours }}</div>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg text-center">
                                <div class="text-gray-500 text-xs uppercase font-bold mb-1">Type</div>
                                <div class="font-bold text-gray-900">
                                    @if($programme->is_aller_retour)
                                        Aller-Retour
                                    @else
                                        Aller Simple
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Prix -->
                        <div class="bg-gray-900 text-white p-4 rounded-xl text-center">
                            <div class="text-sm opacity-75 mb-1">Prix par personne</div>
                            <div class="text-3xl font-bold text-[#e94f1b]">
                                {{ number_format($programme->montant_billet, 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte Véhicule -->
                @if($programme->vehicule)
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-car text-[#e94f1b]"></i> Véhicule
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Modèle</span>
                            <span class="font-semibold">{{ $programme->vehicule->marque }} {{ $programme->vehicule->modele }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Immatriculation</span>
                            <span class="font-semibold">{{ $programme->vehicule->immatriculation }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Type</span>
                            <span class="font-semibold">{{ $programme->vehicule->type_range }} (Climatisé)</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Colonne droite: Processus de réservation -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 min-h-[600px]">
                    <div class="p-6 md:p-8">
                        
                        <!-- Barre de progression -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between relative">
                                <div class="absolute left-0 top-1/2 transform -translate-y-1/2 w-full h-1 bg-gray-200 -z-10"></div>
                                <div class="step-indicator active bg-[#e94f1b] text-white w-10 h-10 rounded-full flex items-center justify-center font-bold relative z-10 transition-all duration-300" data-step="1">1</div>
                                <div class="step-indicator bg-white border-2 border-gray-300 text-gray-500 w-10 h-10 rounded-full flex items-center justify-center font-bold relative z-10 transition-all duration-300" data-step="2">2</div>
                                <div class="step-indicator bg-white border-2 border-gray-300 text-gray-500 w-10 h-10 rounded-full flex items-center justify-center font-bold relative z-10 transition-all duration-300" data-step="3">3</div>
                            </div>
                            <div class="flex justify-between mt-2 text-sm font-medium text-gray-600">
                                <span>Quantité</span>
                                <span>Places</span>
                                <span>Passagers</span>
                            </div>
                        </div>

                        <!-- Étape 1: Nombre de places & Date -->
                        <div id="step1" class="step-content">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">Configuration de votre voyage</h3>
                            
                            <!-- Date de voyage fixée par le programme -->
                            <div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-100">
                                <label class="block text-sm font-medium text-blue-800 mb-1">Date du voyage</label>
                                <div class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-blue-500"></i>
                                    {{ \Carbon\Carbon::parse($programme->date_depart)->translatedFormat('d F Y') }}
                                </div>
                            </div>

                            <h4 class="font-semibold text-gray-900 mb-4">Combien de places souhaitez-vous ?</h4>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                                @for ($i = 1; $i <= 8; $i++)
                                    <button onclick="selectNumberOfPlaces({{ $i }})"
                                        class="place-count-btn p-4 border-2 border-gray-200 rounded-xl hover:border-[#e94f1b] hover:bg-orange-50 transition-all duration-300 text-center"
                                        data-count="{{ $i }}">
                                        <div class="text-2xl font-bold text-gray-800">{{ $i }}</div>
                                        <div class="text-sm text-gray-600">place{{ $i > 1 ? 's' : '' }}</div>
                                    </button>
                                @endfor
                            </div>

                            <div class="flex justify-end">
                                <button onclick="goToStep2()" id="btn-step1-next" disabled
                                    class="bg-[#e94f1b] text-white px-8 py-3 rounded-lg font-bold hover:bg-orange-600 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                    Suivant <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Étape 2: Sélection des places (Visuelle) -->
                        <div id="step2" class="step-content hidden">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Choisissez vos sièges</h3>
                            <p class="text-gray-500 mb-6">Veuillez sélectionner <span id="nb-places-txt" class="font-bold text-[#e94f1b]">0</span> place(s) sur le plan.</p>
                            
                            <!-- Légende -->
                            <div class="flex flex-wrap gap-4 mb-6 text-sm justify-center bg-gray-50 p-3 rounded-lg">
                                <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-[#e94f1b]"></div> Disponible</div>
                                <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-[#10b981]"></div> Sélectionné</div>
                                <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-red-400 opacity-50"></div> Indisponible</div>
                            </div>

                            <!-- Zone de visualisation -->
                            <div id="seat-visualization" class="mb-8 flex justify-center">
                                <div class="text-center py-10">
                                    <i class="fas fa-spinner fa-spin text-4xl text-[#e94f1b]"></i>
                                    <p class="mt-2 text-gray-500">Chargement du plan...</p>
                                </div>
                            </div>

                            <div class="flex justify-between">
                                <button onclick="showStep(1)" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                                    <i class="fas fa-arrow-left mr-2"></i> Retour
                                </button>
                                <button onclick="goToStep3()" id="btn-step2-next" disabled
                                    class="bg-[#e94f1b] text-white px-8 py-3 rounded-lg font-bold hover:bg-orange-600 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                    Continuer <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Étape 3: Informations Passagers -->
                        <div id="step3" class="step-content hidden">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">Informations des passagers</h3>
                            
                            <form id="booking-form" onsubmit="submitBooking(event)">
                                @csrf
                                <input type="hidden" name="programme_id" value="{{ $programme->id }}">
                                <input type="hidden" name="nombre_places" id="form_nombre_places">
                                <input type="hidden" name="date_voyage" id="form_date_voyage" value="{{ \Carbon\Carbon::parse($programme->date_depart)->format('Y-m-d') }}">
                                
                                <div id="passengers-container" class="space-y-6 mb-8">
                                    <!-- Champs générés dynamiquement -->
                                </div>

                                <div class="flex justify-between items-center pt-6 border-t border-gray-100">
                                    <button type="button" onclick="showStep(2)" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                                        <i class="fas fa-arrow-left mr-2"></i> Retour
                                    </button>
                                    <button type="submit" id="btn-submit"
                                        class="bg-green-600 text-white px-8 py-4 rounded-xl font-bold hover:bg-green-700 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center gap-3 text-lg">
                                        <i class="fas fa-check-circle"></i> Confirmer la réservation
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .place-count-btn.selected {
        background-color: #fff7ed;
        border-color: #e94f1b;
        color: #c2410c;
        box-shadow: 0 0 0 2px rgba(254, 162, 25, 0.2);
    }
    
    .seat-item {
        transition: all 0.2s;
    }
    .seat-item:hover:not(.occupied) {
        transform: scale(1.1);
        z-index: 10;
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Configuration
    const PROGRAMME_ID = {{ $programme->id }};
    const VEHICLE_ID = {{ $programme->vehicule_id ?? 'null' }};
    const TYPE_RANGE = "{{ $programme->vehicule->type_range ?? '2x2' }}";
    const TOTAL_PLACES = {{ $programme->vehicule->nombre_place ?? 0 }};
    
    // État
    let state = {
        step: 1,
        nbPlaces: 0,
        selectedSeats: [], // [1, 2, ...]
        reservedSeats: [], // [5, 6, ...] (Occupés)
        dateVoyage: "{{ \Carbon\Carbon::parse($programme->date_depart)->format('Y-m-d') }}"
    };

    // Config Visuelle
    const typeRangeConfig = {
        '2x2': { left: 2, right: 2 },
        '2x3': { left: 2, right: 3 },
        '2x4': { left: 2, right: 4 }
    };

    // Initialisation
    document.addEventListener('DOMContentLoaded', () => {
        // Plus de gestion de changement de date car date fixe
    });

    // Navigation
    function showStep(step) {
        document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
        document.getElementById(`step${step}`).classList.remove('hidden');
        
        // Indicateurs
        document.querySelectorAll('.step-indicator').forEach(el => {
            const s = parseInt(el.dataset.step);
            el.classList.remove('bg-[#e94f1b]', 'text-white', 'bg-green-500');
            el.classList.add('bg-white', 'text-gray-500');
            
            if (s === step) {
                el.classList.remove('bg-white', 'text-gray-500');
                el.classList.add('bg-[#e94f1b]', 'text-white');
            } else if (s < step) {
                el.classList.remove('bg-white', 'text-gray-500');
                el.classList.add('bg-green-500', 'text-white', 'border-transparent');
                el.innerHTML = '<i class="fas fa-check"></i>';
            } else {
                el.innerHTML = s;
            }
        });
        
        state.step = step;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Étape 1 Logic
    function selectNumberOfPlaces(n) {
        state.nbPlaces = n;
        document.querySelectorAll('.place-count-btn').forEach(b => b.classList.remove('selected'));
        document.querySelector(`.place-count-btn[data-count="${n}"]`).classList.add('selected');
        document.getElementById('btn-step1-next').disabled = false;
        document.getElementById('nb-places-txt').textContent = n;
        document.getElementById('form_nombre_places').value = n;
    }

    function goToStep2() {
        if (state.nbPlaces === 0) return;
        state.selectedSeats = []; // Reset selections
        updateNextStepButton2();
        showStep(2);
        loadSeats();
    }

    // Étape 2 Logic (Visualisation)
    async function loadSeats() {
        const visualizationArea = document.getElementById('seat-visualization');
        
        try {
            // Récupérer les places occupées via l'API existante
            const url = `/user/booking/reservation/reserved-seats/${PROGRAMME_ID}?date=` + state.dateVoyage;
            const response = await fetch(url);
            const data = await response.json();
            
            
            if (data.success) {
                state.reservedSeats = data.reservedSeats || []; // Array of seat numbers
                renderSeatingPlan();
            } else {
                visualizationArea.innerHTML = `<div class="text-red-500">Erreur lors du chargement des places.</div>`;
            }
        } catch (e) {
            console.error(e);
            visualizationArea.innerHTML = `<div class="text-red-500">Erreur de connexion.</div>`;
        }
    }

    function renderSeatingPlan() {
        if (!VEHICLE_ID) {
            document.getElementById('seat-visualization').innerHTML = '<div class="alert alert-warning">Véhicule non assigné</div>';
            return;
        }

        const config = typeRangeConfig[TYPE_RANGE] || typeRangeConfig['2x2'];
        const placesLeft = config.left;
        const placesRight = config.right;
        const rowSize = placesLeft + placesRight;
        const totalRows = Math.ceil(TOTAL_PLACES / rowSize);
        
        let html = '<div class="inline-block bg-white p-4 rounded-xl border border-gray-200 shadow-sm">';
        
        // Volant / Chauffeur
        html += `
            <div class="flex justify-between mb-8 px-4 opacity-50">
                <div class="w-10 h-10 border-2 border-gray-400 rounded-full flex items-center justify-center">
                    <i class="fas fa-steering-wheel text-gray-400"></i>
                </div>
                <div class="text-xs uppercase font-bold text-gray-400 py-2">Avant</div>
            </div>
        `;

        let seatCounter = 1;

        for (let r = 0; r < totalRows; r++) {
            html += '<div class="flex gap-8 mb-4 justify-center">';
            
            // Côté Gauche
            html += '<div class="flex gap-2">';
            for (let i = 0; i < placesLeft; i++) {
                if (seatCounter > TOTAL_PLACES) break;
                html += renderSeat(seatCounter);
                seatCounter++;
            }
            html += '</div>';

            // Allée
            html += '<div class="w-4"></div>';

            // Côté Droit
            html += '<div class="flex gap-2">';
            for (let i = 0; i < placesRight; i++) {
                if (seatCounter > TOTAL_PLACES) break;
                html += renderSeat(seatCounter);
                seatCounter++;
            }
            html += '</div>';

            html += '</div>';
        }

        html += '</div>';
        document.getElementById('seat-visualization').innerHTML = html;
    }

    function renderSeat(num) {
        const isReserved = state.reservedSeats.includes(num);
        const isSelected = state.selectedSeats.includes(num);
        
        let classes = "w-10 h-10 rounded-lg flex items-center justify-center font-bold text-sm cursor-pointer seat-item transition-colors shadow-sm ";
        let onclick = "";

        if (isReserved) {
            classes += "bg-red-100 text-red-400 cursor-not-allowed occupied";
            return `<div class="${classes}" title="Occupé">${num}</div>`;
        } else if (isSelected) {
            classes += "bg-[#10b981] text-white ring-2 ring-offset-1 ring-[#10b981]";
            onclick = `toggleSeat(${num})`;
        } else {
            classes += "bg-gray-100 text-gray-600 hover:bg-[#e94f1b] hover:text-white";
            onclick = `toggleSeat(${num})`;
        }

        return `<div class="${classes}" onclick="${onclick}">${num}</div>`;
    }

    function toggleSeat(num) {
        const index = state.selectedSeats.indexOf(num);
        if (index > -1) {
            state.selectedSeats.splice(index, 1);
        } else {
            if (state.selectedSeats.length >= state.nbPlaces) {
                // Remplacer le premier si on a déjà atteint la limite, ou bloquer?
                // Le comportement UX standard est de bloquer ou de remplacer le premier.
                // Ici, on va bloquer et dire à l'utilisateur de désélectionner.
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: `Vous avez déjà sélectionné ${state.nbPlaces} place(s)`,
                    showConfirmButton: false,
                    timer: 2000
                });
                return;
            }
            state.selectedSeats.push(num);
        }
        
        renderSeatingPlan();
        updateNextStepButton2();
    }

    function updateNextStepButton2() {
        const btn = document.getElementById('btn-step2-next');
        const isValid = state.selectedSeats.length === state.nbPlaces;
        btn.disabled = !isValid;
    }

    function goToStep3() {
        if (state.selectedSeats.length !== state.nbPlaces) return;
        generatePassengerForms();
        showStep(3);
    }

    // Étape 3 Logic
    function generatePassengerForms() {
        const container = document.getElementById('passengers-container');
        container.innerHTML = '';

        state.selectedSeats.forEach((seatNum, index) => {
            const isMainPassenger = index === 0;
            // Pré-remplir avec les infos de l'user connecté si c'est le premier passager
            const user = @json(Auth::user());
            const prefill = isMainPassenger && user ? {
                nom: user.name.split(' ').slice(1).join(' '), // Approximation brute
                prenom: user.name.split(' ')[0], 
                email: user.email,
                phone: user.telephone ?? ''
            } : { nom: '', prenom: '', email: '', phone: '' };

            const html = `
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 relative">
                    <div class="absolute -top-3 -right-3 bg-[#e94f1b] text-white w-8 h-8 rounded-full flex items-center justify-center font-bold shadow-md">
                        ${seatNum}
                    </div>
                    <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-user"></i> Passager ${index + 1} (Siège ${seatNum})
                    </h4>
                    
                    <input type="hidden" name="passagers[${index}][seat_number]" value="${seatNum}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nom</label>
                            <input type="text" name="passagers[${index}][nom]" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-[#e94f1b] focus:ring-1 focus:ring-[#e94f1b]" placeholder="Nom de famille" value="${prefill.nom}">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Prénom</label>
                            <input type="text" name="passagers[${index}][prenom]" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-[#e94f1b] focus:ring-1 focus:ring-[#e94f1b]" placeholder="Prénoms" value="${prefill.prenom}">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email</label>
                            <input type="email" name="passagers[${index}][email]" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-[#e94f1b] focus:ring-1 focus:ring-[#e94f1b]" placeholder="email@exemple.com" value="${prefill.email}">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Téléphone</label>
                            <input type="tel" name="passagers[${index}][telephone]" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-[#e94f1b] focus:ring-1 focus:ring-[#e94f1b]" placeholder="+225..." value="${prefill.phone}">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Contact d'urgence</label>
                            <input type="text" name="passagers[${index}][urgence]" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-[#e94f1b] focus:ring-1 focus:ring-[#e94f1b]" placeholder="Nom et Numéro à contacter">
                        </div>
                    </div>
                </div>
            `;
            container.innerHTML += html;
        });
    }

    // Problème: le formulaire n'est pas envoyé en JSON mais en form-data classique
    // Or le contrôleur store() attend un payload pour une requête API (retourne du JSON).
    // Donc on doit intercepter le submit.
    async function submitBooking(e) {
        e.preventDefault();
        
        const btn = document.getElementById('btn-submit');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';
        
        // Construction des données
        const formData = new FormData(e.target);
        
        // Il faut transformer ça en structure JSON pour que le validate() du controller passe, 
        // surtout pour les arrays indexés comme seats[].
        // Mais FormData fonctionne aussi si le controller utilise $request->all().
        // Cependant le JS 'seats' array doit être ajouté explicitement.
        
        state.selectedSeats.forEach(seat => {
            formData.append('seats[]', seat);
        });

        try {
            const response = await fetch("{{ route('reservation.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json' // Force JSON response
                },
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Réservation confirmée !',
                    text: 'Vos billets ont été envoyés par email.',
                    confirmButtonText: 'Voir mes réservations',
                    confirmButtonColor: '#10b981',
                    allowOutsideClick: false
                }).then(() => {
                    window.location.href = "{{ route('reservation.index') }}";
                });
            } else {
                throw new Error(result.message || 'Erreur inconnue');
            }
            
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Oups...',
                text: error.message || 'Une erreur est survenue lors de la réservation.',
                confirmButtonColor: '#e94f1b'
            });
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }
</script>
@endpush
@endsection
