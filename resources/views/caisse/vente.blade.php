@extends('caisse.layouts.template')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Vente Express</h1>
                <p class="text-lg text-gray-600">Sélectionnez les places et confirmez la vente</p>
            </div>
            <a href="{{ route('caisse.vendre-ticket') }}" class="px-6 py-2 bg-white border border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50 flex items-center gap-2">
                <i class="fas fa-user-edit text-orange-500"></i>
                Vente avec détails passagers
            </a>
        </div>

        <!-- Formulaire de vente -->
        <div class="bg-white rounded-3xl shadow-xl p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-[#e94e1a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
                Nouvelle achat de places
            </h2>

            <form action="{{ route('caisse.vente.submit') }}" method="POST" id="ticket-form">
                @csrf
                <input type="hidden" name="seat_numbers" id="seat_numbers_input" value="">

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
                                data-heure="{{ \Carbon\Carbon::parse($programme->heure_depart)->format('H:i') }}"
                                data-price="{{ $programme->montant_billet }}">
                                {{ \Carbon\Carbon::parse($programme->heure_depart)->format('H:i') }} 
                                | {{ $programme->point_depart }} → {{ $programme->point_arrive }} 
                                | {{ \Carbon\Carbon::now()->format('d/m/Y') }} 
                                | {{ $programme->vehicule->immatriculation ?? 'Bus' }}
                                | {{ number_format($programme->montant_billet, 0, ',', ' ') }} FCFA
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- COLONNE GAUCHE : Sélection -->
                    <div class="lg:col-span-8 space-y-6">
                        
                        <!-- Nombre de tickets -->
                        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                            <label class="block text-sm font-bold text-gray-700 mb-3">Nombre de places à réserver *</label>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center">
                                    <button type="button" onclick="changeTicketCount(-1)" 
                                        class="w-12 h-12 flex items-center justify-center bg-white border border-gray-300 text-gray-700 font-bold rounded-l-xl hover:bg-gray-100 transition-colors">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" id="nombre_tickets" value="0" min="0" readonly
                                        class="w-20 h-12 border-y border-gray-300 text-center font-bold text-xl focus:outline-none bg-white">
                                    <button type="button" onclick="changeTicketCount(1)" 
                                        class="w-12 h-12 flex items-center justify-center bg-white border border-gray-300 text-gray-700 font-bold rounded-r-xl hover:bg-gray-100 transition-colors">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <div class="text-sm text-gray-500 font-medium">
                                    <i class="fas fa-info-circle mr-1"></i> Cliquez sur les places pour les sélectionner manuellement
                                </div>
                            </div>
                        </div>

                        <!-- PLAN DE SIÈGE (Directement sous le nombre de tickets) -->
                        <div id="seat-map-wrapper" class="overflow-hidden transition-all duration-500 max-h-0 opacity-0">
                            <div class="border border-gray-200 rounded-2xl p-6 bg-white shadow-sm">
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                        <i class="fas fa-bus text-[#e94e1a]"></i>
                                        <span>Disposition du bus</span>
                                    </h3>
                                    <div class="flex items-center gap-4">
                                        <div id="live-indicator" class="hidden flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-[10px] font-bold animate-pulse">
                                            <div class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
                                            LIVE
                                        </div>
                                        <span id="available-count" class="px-3 py-1 bg-green-50 text-green-700 rounded-full text-xs font-bold">0 places libres</span>
                                        <span id="vehicle-info" class="text-xs font-bold text-gray-500 bg-gray-50 px-3 py-1 rounded border border-gray-200"></span>
                                    </div>
                                </div>

                                <div id="seat-map-container" class="bg-gray-50 rounded-2xl border border-gray-100 p-8 min-h-[400px] flex flex-col items-center">
                                    <!-- Le bus sera généré ici -->
                                    <div class="flex flex-col items-center justify-center h-full text-gray-400 gap-3">
                                        <i class="fas fa-bus-alt fa-3x opacity-20"></i>
                                        <span class="italic">Veuillez sélectionner un programme pour voir les places...</span>
                                    </div>
                                </div>

                                <!-- Légende -->
                                <div class="mt-6 flex flex-wrap justify-center gap-6 text-xs bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 rounded-md bg-white border border-gray-300 shadow-sm"></div>
                                        <span class="text-gray-600 font-medium">Libre</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 rounded-md bg-green-500 shadow-sm"></div>
                                        <span class="text-gray-600 font-medium">Votre sélection</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 rounded-md bg-red-500 shadow-sm"></div>
                                        <span class="text-gray-600 font-medium">Occupé</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- COLONNE DROITE : Recapitulatif & Actions -->
                    <div class="lg:col-span-4 lg:sticky lg:top-4 space-y-6">
                        
                        <div id="selection-summary" class="bg-white rounded-2xl p-6 border-2 border-orange-100 shadow-lg hidden">
                            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <i class="fas fa-file-invoice-dollar text-orange-500"></i>
                                Récapitulatif
                            </h3>
                            <div class="space-y-4">
                                <div class="flex flex-col gap-2">
                                    <span class="text-xs font-bold text-gray-400 uppercase">Places sélectionnées</span>
                                    <div id="summary-seats" class="flex flex-wrap gap-2 text-sm font-bold text-orange-600">
                                        Aucune
                                    </div>
                                </div>
                                <div class="flex justify-between items-center py-3 border-y border-gray-50">
                                    <span class="text-sm text-gray-500">Prix unitaire</span>
                                    <span id="summary-price" class="font-bold text-gray-900">0 FCFA</span>
                                </div>
                                <div class="pt-2">
                                    <div class="flex justify-between items-center text-xl font-black text-gray-900">
                                        <span>TOTAL</span>
                                        <span id="summary-total" class="text-3xl text-[#e94e1a]">0 FCFA</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="mt-8 space-y-3">
                                <button type="submit" id="submit-btn" disabled
                                    class="w-full py-4 bg-[#e94e1a] text-white font-bold rounded-xl hover:bg-[#d33d0f] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl opacity-50 cursor-not-allowed flex items-center justify-center gap-2">
                                    <i class="fas fa-check-circle"></i>
                                    Confirmer les places
                                </button>
                                <button type="reset" onclick="resetSelection()"
                                    class="w-full py-4 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200">
                                    <i class="fas fa-undo mr-2"></i>
                                    Réinitialiser
                                </button>
                            </div>
                        </div>

                        <!-- Aide rapide -->
                        <div class="bg-blue-50 p-6 rounded-2xl border border-blue-100">
                            <h4 class="text-blue-800 font-bold mb-3 flex items-center gap-2 text-sm">
                                <i class="fas fa-question-circle"></i>
                                Comment ça marche ?
                            </h4>
                            <ul class="text-xs text-blue-700 space-y-2">
                                <li class="flex gap-2"><i class="fas fa-1 text-blue-300"></i> Sélectionnez le programme</li>
                                <li class="flex gap-2"><i class="fas fa-2 text-blue-300"></i> Utilisez les boutons +/- ou cliquez sur les sièges</li>
                                <li class="flex gap-2"><i class="fas fa-3 text-blue-300"></i> Le total se calcule automatiquement</li>
                                <li class="flex gap-2"><i class="fas fa-4 text-blue-300"></i> Cliquez sur confirmer pour générer les tickets</li>
                            </ul>
                        </div>

                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .seat-item {
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        user-select: none;
    }
    .seat-item:hover:not(.occupied) {
        transform: scale(1.1);
        z-index: 10;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .seat-item.selected {
        background-color: #22c55e !important;
        color: white !important;
        border-color: #16a34a !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(34, 197, 94, 0.4);
    }
    .seat-item.occupied {
        background-color: #ef4444 !important;
        color: white !important;
        border-color: #dc2626 !important;
        cursor: not-allowed;
        opacity: 0.8;
    }
    .scrollbar-thin::-webkit-scrollbar {
        width: 6px;
    }
    .scrollbar-thin::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 10px;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #ccc;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let selectedSeats = [];
    let currentPrice = 0;
    let availableSeats = [];
    let currentReservedSeats = [];
    let pollingInterval = null;
    let typeRangeConfig = {
        '1x1': { placesGauche: 1, placesDroite: 1 },
        '2x1': { placesGauche: 2, placesDroite: 1 },
        '1x2': { placesGauche: 1, placesDroite: 2 },
        '2x2': { placesGauche: 2, placesDroite: 2 },
        '3x2': { placesGauche: 3, placesDroite: 2 },
        '2x3': { placesGauche: 2, placesDroite: 3 }
    };

    const programmeSelect = document.getElementById('programme_id');
    const wrapper = document.getElementById('seat-map-wrapper');
    const container = document.getElementById('seat-map-container');
    const info = document.getElementById('vehicle-info');
    const numInput = document.getElementById('nombre_tickets');
    const seatsInput = document.getElementById('seat_numbers_input');
    const summaryDiv = document.getElementById('selection-summary');
    const summarySeats = document.getElementById('summary-seats');
    const summaryPrice = document.getElementById('summary-price');
    const summaryTotal = document.getElementById('summary-total');
    const submitBtn = document.getElementById('submit-btn');
    const availableSpan = document.getElementById('available-count');

    programmeSelect.addEventListener('change', function() {
        const programId = this.value;
        if (!programId) {
            wrapper.style.maxHeight = '0';
            wrapper.style.opacity = '0';
            resetSelection();
            return;
        }

        const selectedOption = this.options[this.selectedIndex];
        const vehicleId = selectedOption.getAttribute('data-vehicle-id');
        currentPrice = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        
        wrapper.style.maxHeight = '1200px'; 
        wrapper.style.opacity = '1';
        container.innerHTML = '<div class="flex flex-col items-center gap-4 text-orange-500"><i class="fas fa-circle-notch fa-spin fa-2x"></i><span>Chargement du plan...</span></div>';
        
        if (pollingInterval) clearInterval(pollingInterval);
        
        const dateQuery = '{{ now()->toDateString() }}';
        fetchData(vehicleId, dateQuery, programId);

        // --- Laravel Reverb (Echo) Listener ---
        if (window.Echo) {
            window.Echo.leaveChannel(`program.${programId}.${dateQuery}`);
            window.Echo.channel(`program.${programId}.${dateQuery}`)
                .listen('.seat.updated', (e) => {
                    console.log('Mise à jour temps réel reçue:', e);
                    updateReservedSeatsUI(e.reservedSeats);
                });
            document.getElementById('live-indicator').classList.remove('hidden');
        }

        pollingInterval = setInterval(() => {
            fetchData(vehicleId, dateQuery, programId, true);
        }, 1500); // 1.5s backup instead of 10s
    });

    function updateReservedSeatsUI(newReservedSeats) {
        const total = document.querySelectorAll('.seat-item').length;
        
        document.querySelectorAll('.seat-item').forEach(seat => {
            const num = parseInt(seat.getAttribute('data-num'));
            if (newReservedSeats.includes(num)) {
                seat.classList.add('occupied');
                seat.classList.remove('selected');
                const idx = selectedSeats.indexOf(num);
                if (idx > -1) selectedSeats.splice(idx, 1);
            } else {
                seat.classList.remove('occupied');
            }
        });

        currentReservedSeats = [...newReservedSeats];
        availableSeats = [];
        for(let i=1; i<=total; i++) {
            if(!newReservedSeats.includes(i)) availableSeats.push(i);
        }
        if (availableSpan) {
            availableSpan.textContent = `${availableSeats.length} places libres`;
        }
        
        updateUI();
    }

    function fetchData(vehicleId, date, programId, isPolling = false) {
        fetch(`{{ url("/caisse/api/vehicle") }}/${vehicleId}?date=${date}&program_id=${programId}`)
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    if (!isPolling) {
                        info.textContent = `${data.vehicule.marque || 'Bus'} - ${data.vehicule.immatriculation}`;
                        generateSeatMap(data.vehicule, data.reserved_seats || []);
                    } else {
                        updateOccupiedSeats(data.reserved_seats || []);
                    }
                } else if (!isPolling) {
                    container.innerHTML = '<p class="text-red-500">Erreur lors de la récupération des données.</p>';
                }
            })
            .catch(err => {
                console.error(err);
                if (!isPolling) container.innerHTML = '<p class="text-red-500">Erreur de connexion.</p>';
            });
    }

    function updateOccupiedSeats(newReservedSeats) {
        // Compare with old reserved seats to show warnings if needed
        const newlyOccupied = newReservedSeats.filter(s => !currentReservedSeats.includes(s));
        
        // Update current reference
        currentReservedSeats = [...newReservedSeats];

        // Update UI
        document.querySelectorAll('.seat-item').forEach(seat => {
            const num = parseInt(seat.getAttribute('data-num'));
            if (newReservedSeats.includes(num)) {
                if (!seat.classList.contains('occupied')) {
                    seat.classList.add('occupied');
                    seat.classList.remove('selected');
                    // If the seat was selected by user, remove it from selection
                    const sIndex = selectedSeats.indexOf(num);
                    if (sIndex > -1) {
                        selectedSeats.splice(sIndex, 1);
                        updateUI();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Place vendue !',
                            text: `La place ${num} vient d'être vendue à un autre guichet.`,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000
                        });
                    }
                }
            } else {
                seat.classList.remove('occupied');
            }
        });

        // Update available count
        const total = document.querySelectorAll('.seat-item').length;
        const free = total - newReservedSeats.length;
        availableSpan.textContent = `${free} places libres`;
        
        // Update availableSeats array for the +/- logic
        availableSeats = [];
        for(let i=1; i<=total; i++) {
            if(!newReservedSeats.includes(i)) availableSeats.push(i);
        }
    }

    function generateSeatMap(vehicle, reservedSeats) {
        let config = typeRangeConfig[vehicle.type_range] || typeRangeConfig['2x2'];
        const pg = config.placesGauche;
        const pd = config.placesDroite;
        const total = parseInt(vehicle.nombre_place);
        const rows = Math.ceil(total / (pg + pd));
        
        currentReservedSeats = [...reservedSeats];
        availableSeats = [];
        for(let i=1; i<=total; i++) {
            if(!reservedSeats.includes(i)) availableSeats.push(i);
        }
        availableSpan.textContent = `${availableSeats.length} places libres`;

        let html = `
            <div class="w-full max-w-md border-4 border-gray-200 rounded-[3rem] p-8 relative bg-white shadow-2xl">
                <!-- Volant/Avant -->
                <div class="flex justify-between items-center mb-8 pb-4 border-b-2 border-dashed border-gray-100">
                    <div class="bg-gray-100 p-3 rounded-full"><i class="fas fa-steering-wheel fa-lg text-gray-400"></i></div>
                    <div class="bg-orange-100 px-4 py-1 rounded-full text-orange-600 font-bold text-xs">AVANT</div>
                </div>

                <div class="space-y-4 max-h-[450px] overflow-y-auto scrollbar-thin pr-2">
        `;

        let currentNum = 1;
        for (let r = 1; r <= rows; r++) {
            html += `<div class="grid grid-cols-[1fr_60px_1fr] items-center gap-3">`;
            
            // Gauche
            html += `<div class="flex justify-end gap-3">`;
            for (let i = 0; i < pg; i++) {
                if (currentNum <= total) {
                    html += renderSeat(currentNum, reservedSeats.includes(currentNum));
                    currentNum++;
                }
            }
            html += `</div>`;

            // Allée
            html += `<div class="flex justify-center"><div class="text-[10px] font-bold text-gray-300 transform rotate-90 tracking-widest">ALLÉE</div></div>`;

            // Droite
            html += `<div class="flex justify-start gap-3">`;
            for (let i = 0; i < pd; i++) {
                if (currentNum <= total) {
                    html += renderSeat(currentNum, reservedSeats.includes(currentNum));
                    currentNum++;
                }
            }
            html += `</div>`;

            html += `</div>`;
        }

        html += `</div></div>`;
        container.innerHTML = html;
        
        // Add event listeners
        document.querySelectorAll('.seat-item:not(.occupied)').forEach(seat => {
            seat.addEventListener('click', function() {
                const num = parseInt(this.getAttribute('data-num'));
                toggleSeat(num, this);
            });
        });
    }

    function renderSeat(num, isOccupied) {
        const cls = isOccupied ? 'occupied' : '';
        return `<div class="seat-item ${cls} w-12 h-14 border-2 border-gray-100 rounded-xl flex flex-col items-center justify-center font-bold text-sm" data-num="${num}">
                    <div class="w-8 h-1.5 bg-current opacity-20 rounded-full mb-1"></div>
                    ${num}
                </div>`;
    }

    function toggleSeat(num, element) {
        const index = selectedSeats.indexOf(num);
        if (index > -1) {
            selectedSeats.splice(index, 1);
            element.classList.remove('selected');
        } else {
            selectedSeats.push(num);
            element.classList.add('selected');
        }
        updateUI();
    }

    // Logic: If user changes count manually
    function changeTicketCount(delta) {
        if (!programmeSelect.value) {
            Swal.fire({ icon: 'warning', title: 'Attention', text: 'Veuillez d\'abord sélectionner un programme.' });
            return;
        }

        let currentCount = selectedSeats.length;
        let newCount = Math.max(0, currentCount + delta);
        
        if (newCount > availableSeats.length) {
            Swal.fire({ icon: 'error', title: 'Limite atteinte', text: 'Toutes les places disponibles sont déjà sélectionnées.' });
            return;
        }

        if (newCount > currentCount) {
            // Add seats
            let added = 0;
            let diff = newCount - currentCount;
            for (let seatNum of availableSeats) {
                if (!selectedSeats.includes(seatNum)) {
                    selectedSeats.push(seatNum);
                    const el = document.querySelector(`.seat-item[data-num="${seatNum}"]`);
                    if (el) el.classList.add('selected');
                    added++;
                    if (added === diff) break;
                }
            }
        } else if (newCount < currentCount) {
            // Remove last selected seats
            let removed = 0;
            let countToRemove = currentCount - newCount;
            while (removed < countToRemove && selectedSeats.length > 0) {
                let seatNum = selectedSeats.pop();
                const el = document.querySelector(`.seat-item[data-num="${seatNum}"]`);
                if (el) el.classList.remove('selected');
                removed++;
            }
        }
        updateUI();
    }

    function updateUI() {
        selectedSeats.sort((a, b) => a - b);
        numInput.value = selectedSeats.length;
        seatsInput.value = selectedSeats.join(',');
        
        if (selectedSeats.length > 0) {
            summaryDiv.classList.remove('hidden');
            summarySeats.textContent = selectedSeats.join(', ');
            summaryPrice.textContent = formatPrice(currentPrice) + ' FCFA';
            summaryTotal.textContent = formatPrice(currentPrice * selectedSeats.length) + ' FCFA';
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            summaryDiv.classList.add('hidden');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    function formatPrice(p) {
        return new Intl.NumberFormat('fr-FR').format(p);
    }

    function resetSelection() {
        selectedSeats = [];
        document.querySelectorAll('.seat-item.selected').forEach(s => s.classList.remove('selected'));
        updateUI();
    }

    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Succès!', text: "{{ session('success') }}", confirmButtonColor: '#e94e1a' });
    @endif

    @if($errors->any())
        Swal.fire({ icon: 'error', title: 'Erreur!', html: "@foreach($errors->all() as $error) {{ $error }}<br> @endforeach", confirmButtonColor: '#d33' });
    @endif
</script>
@endsection
