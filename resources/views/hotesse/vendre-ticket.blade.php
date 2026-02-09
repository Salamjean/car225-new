@extends('hotesse.layouts.template')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Titre -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Vendre des Tickets</h1>
            <p class="text-gray-600">Interface de vente hôtesse</p>
        </div>

        <!-- Zone de Recherche -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Rechercher un trajet</h2>
            
            <form action="{{ route('hotesse.vendre-ticket') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <!-- Départ -->
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Départ</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-map-marker-alt"></i>
                        </span>
                        <input type="text" id="point_depart" name="point_depart" 
                            value="{{ $searchParams['point_depart'] ?? '' }}"
                            class="pl-10 block w-full rounded-lg border-gray-300 focus:ring-[#e94e1a] focus:border-[#e94e1a]" 
                            placeholder="Ville de départ">
                    </div>
                </div>

                <!-- Arrivée -->
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Arrivée</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-flag"></i>
                        </span>
                        <input type="text" id="point_arrive" name="point_arrive" 
                            value="{{ $searchParams['point_arrive'] ?? '' }}"
                            class="pl-10 block w-full rounded-lg border-gray-300 focus:ring-[#e94e1a] focus:border-[#e94e1a]" 
                            placeholder="Ville d'arrivée">
                    </div>
                </div>

                <!-- Date -->
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-calendar"></i>
                        </span>
                        <input type="date" name="date_depart" 
                            value="{{ $searchParams['date_depart'] ?? date('Y-m-d') }}"
                            class="pl-10 block w-full rounded-lg border-gray-300 focus:ring-[#e94e1a] focus:border-[#e94e1a]">
                    </div>
                </div>

                <!-- Boutons -->
                <div class="md:col-span-3 flex gap-2">
                    <button type="submit" class="flex-1 bg-[#e94e1a] text-white px-4 py-2.5 rounded-lg font-bold hover:bg-[#d04415] transition shadow-sm">
                        <i class="fas fa-search mr-1"></i> Chercher
                    </button>
                    <!-- Le bouton Voir tout force l'affichage -->
                    <a href="{{ route('hotesse.vendre-ticket', ['view_all' => 1]) }}" class="bg-gray-800 text-white px-4 py-2.5 rounded-lg font-bold hover:bg-gray-700 transition shadow-sm flex items-center justify-center">
                        <i class="fas fa-list mr-1"></i> Voir tous les voyages
                    </a>
                </div>
            </form>
        </div>

        <!-- Liste des Résultats -->
        @if(isset($groupedRoutes) && count($groupedRoutes) > 0)
            <div class="grid grid-cols-1 gap-6">
                @foreach($groupedRoutes as $route)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition duration-300">
                    <div class="p-6 flex flex-col md:flex-row items-center justify-between gap-6">
                        
                        <!-- Info Route -->
                        <div class="flex items-center gap-4 flex-1">
                            <div class="w-16 h-16 rounded-full bg-orange-100 flex items-center justify-center text-[#e94e1a] text-2xl flex-shrink-0">
                                <i class="fas fa-bus"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">{{ $route->compagnie->name }}</h3>
                                <div class="flex items-center gap-2 text-gray-600 mt-1">
                                    <span class="font-semibold">{{ $route->point_depart }}</span>
                                    <i class="fas fa-long-arrow-alt-right text-[#e94e1a]"></i>
                                    <span class="font-semibold">{{ $route->point_arrive }}</span>
                                </div>
                                <div class="flex gap-2 mt-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ count($route->aller_horaires) }} départ(s)
                                    </span>
                                    @if($route->has_retour)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Retour disponible
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Prix & Action -->
                        <div class="text-center md:text-right">
                            <div class="text-2xl font-bold text-[#e94e1a] mb-2">{{ number_format($route->montant_billet, 0, ',', ' ') }} FCFA</div>
                            
                            <button onclick='startReservation(@json($route), "{{ $searchParams["date_depart"] ?? date('Y-m-d') }}")'
                                class="bg-[#e94e1a] text-white px-8 py-3 rounded-lg font-bold hover:bg-[#d04415] transition shadow-lg transform active:scale-95">
                                Réserver
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @elseif(isset($groupedRoutes))
            <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                    <i class="fas fa-search text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Aucun voyage trouvé</h3>
                <p class="text-gray-500 mt-1">Essayez de modifier vos critères ou cliquez sur "Tout".</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal Unique de Réservation -->
<div id="bookingModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                
                <!-- Header Modal -->
                <div class="bg-[#e94e1a] px-4 py-3 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg font-bold leading-6 text-white" id="modalTitle">Nouvelle Réservation</h3>
                    <button onclick="closeModal()" class="text-white hover:text-gray-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Contenu Modal -->
                <div class="px-4 py-5 sm:p-6" id="modalContent">
                    <!-- Le contenu sera injecté dynamiquement par JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Script Google Maps (Vérifiez votre clé API) -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=initAutocomplete" async defer></script>

<script>
    // 1. Initialisation Autocomplete
    function initAutocomplete() {
        // Options pour limiter à la Côte d'Ivoire par exemple
        const options = {
            componentRestrictions: { country: "ci" },
            fields: ["formatted_address", "name"],
        };

        const inputDepart = document.getElementById("point_depart");
        const inputArrive = document.getElementById("point_arrive");

        if (inputDepart) new google.maps.places.Autocomplete(inputDepart, options);
        if (inputArrive) new google.maps.places.Autocomplete(inputArrive, options);
    }

    // --- LOGIQUE DE RÉSERVATION ---

    let currentBooking = {
        route: null,
        isReturn: false,
        dateAller: null,
        timeAller: null,
        progIdAller: null,
        dateRetour: null,
        timeRetour: null,
        progIdRetour: null,
        passengers: 0,
        passengerData: []
    };

    // Fonction d'entrée
    function startReservation(routeData, defaultDate) {
        currentBooking = { ...currentBooking, route: routeData, dateAller: defaultDate };
        
        // Etape 1 : Type de voyage (Si retour possible)
        if (routeData.has_retour) {
            askTripType();
        } else {
            currentBooking.isReturn = false;
            askDepartureTime();
        }
        openModal();
    }

    // Gestion du Modal
    function openModal() { document.getElementById('bookingModal').classList.remove('hidden'); }
    function closeModal() { document.getElementById('bookingModal').classList.add('hidden'); }
    function setModalContent(html, title) {
        document.getElementById('modalContent').innerHTML = html;
        if(title) document.getElementById('modalTitle').textContent = title;
    }

    // Etape 1 : Type Voyage
    function askTripType() {
        const prix = currentBooking.route.montant_billet;
        const html = `
            <div class="grid grid-cols-2 gap-4">
                <button onclick="selectTripType(false)" class="p-6 border-2 border-gray-200 rounded-xl hover:border-[#e94e1a] hover:bg-orange-50 transition text-center">
                    <i class="fas fa-arrow-right text-3xl text-gray-400 mb-3"></i>
                    <div class="font-bold text-lg">Aller Simple</div>
                    <div class="text-[#e94e1a] font-bold mt-1">${new Intl.NumberFormat().format(prix)} FCFA</div>
                </button>
                <button onclick="selectTripType(true)" class="p-6 border-2 border-gray-200 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition text-center">
                    <i class="fas fa-exchange-alt text-3xl text-gray-400 mb-3"></i>
                    <div class="font-bold text-lg">Aller-Retour</div>
                    <div class="text-blue-600 font-bold mt-1">${new Intl.NumberFormat().format(prix * 2)} FCFA</div>
                </button>
            </div>
        `;
        setModalContent(html, "Type de voyage");
    }

    function selectTripType(isReturn) {
        currentBooking.isReturn = isReturn;
        askDepartureTime();
    }

    // Etape 2 : Heure Aller
    function askDepartureTime() {
        const horaires = currentBooking.route.aller_horaires;
        let html = `
            <div class="mb-4 text-gray-600">
                Trajet : <strong>${currentBooking.route.point_depart}</strong> vers <strong>${currentBooking.route.point_arrive}</strong><br>
                Date : ${currentBooking.dateAller}
            </div>
            <div class="grid grid-cols-2 gap-3 max-h-60 overflow-y-auto">`;
        
        horaires.forEach(h => {
            html += `
                <button onclick="selectTimeAller('${h.id}', '${h.heure_depart}')" class="p-4 border rounded-lg hover:bg-green-50 hover:border-green-500 text-left">
                    <div class="font-bold text-lg text-green-700">${h.heure_depart}</div>
                    <div class="text-xs text-gray-500">Arrivée: ${h.heure_arrive}</div>
                </button>`;
        });
        html += `</div>`;
        setModalContent(html, "Choisir l'heure de départ");
    }

    function selectTimeAller(id, time) {
        currentBooking.progIdAller = id;
        currentBooking.timeAller = time;
        
        if(currentBooking.isReturn) {
            askReturnDate();
        } else {
            askPassengerCount();
        }
    }

    // Etape 3 : Date Retour (Si A/R)
    function askReturnDate() {
        const html = `
            <div class="max-w-xs mx-auto">
                <label class="block text-sm font-medium text-gray-700 mb-2">Date de retour</label>
                <input type="date" id="inputDateRetour" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg p-2 border" min="${currentBooking.dateAller}" value="${currentBooking.dateAller}">
                <button onclick="confirmReturnDate()" class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg font-bold">Voir les horaires</button>
            </div>
        `;
        setModalContent(html, "Date de retour");
    }

    function confirmReturnDate() {
        const date = document.getElementById('inputDateRetour').value;
        if(!date) return;
        currentBooking.dateRetour = date;
        fetchReturnTimes();
    }

    // Etape 4 : Heure Retour
    function fetchReturnTimes() {
        setModalContent('<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-blue-500"></i><p>Chargement...</p></div>');
        
        const params = new URLSearchParams({
            original_depart: currentBooking.route.point_depart,
            original_arrive: currentBooking.route.point_arrive,
            min_date: currentBooking.dateRetour
        });

        fetch(`{{ route('hotesse.api.return-trips') }}?${params}`)
            .then(r => r.json())
            .then(data => {
                if(data.success && data.return_trips.length > 0) {
                    let html = `<div class="grid grid-cols-2 gap-3 max-h-60 overflow-y-auto">`;
                    data.return_trips.forEach(h => {
                        html += `
                            <button onclick="selectTimeRetour('${h.id}', '${h.heure_depart}')" class="p-4 border rounded-lg hover:bg-blue-50 hover:border-blue-500 text-left">
                                <div class="font-bold text-lg text-blue-700">${h.heure_depart}</div>
                                <div class="text-xs text-gray-500">Arrivée: ${h.heure_arrive}</div>
                            </button>`;
                    });
                    html += `</div>`;
                    setModalContent(html, "Choisir l'heure de retour");
                } else {
                    Swal.fire('Info', 'Aucun bus retour trouvé à cette date', 'info').then(() => askReturnDate());
                }
            });
    }

    function selectTimeRetour(id, time) {
        currentBooking.progIdRetour = id;
        currentBooking.timeRetour = time;
        askPassengerCount();
    }

    // Etape 5 : Nombre de passagers
    function askPassengerCount() {
        let html = `<div class="flex justify-center flex-wrap gap-4 py-6">`;
        for(let i=1; i<=8; i++) {
            html += `<button onclick="selectPassengerCount(${i})" class="w-16 h-16 rounded-xl border-2 border-gray-200 hover:border-[#e94e1a] hover:bg-orange-50 font-bold text-xl">${i}</button>`;
        }
        html += `</div>`;
        setModalContent(html, "Nombre de passagers");
    }

    function selectPassengerCount(n) {
        currentBooking.passengers = n;
        showPassengerForm();
    }

    // Etape 6 : Formulaire Infos
    function showPassengerForm() {
        let html = `<form id="finalForm" onsubmit="submitReservation(event)" class="space-y-4 max-h-[60vh] overflow-y-auto px-1">`;
        
        for(let i=0; i < currentBooking.passengers; i++) {
            html += `
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h4 class="font-bold text-[#e94e1a] text-sm mb-3">PASSAGER ${i+1}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <input type="text" name="passenger_details[${i}][nom]" placeholder="Nom *" required class="w-full rounded border-gray-300 p-2">
                        <input type="text" name="passenger_details[${i}][prenom]" placeholder="Prénom *" required class="w-full rounded border-gray-300 p-2">
                        <input type="tel" name="passenger_details[${i}][telephone]" placeholder="Téléphone *" required class="w-full rounded border-gray-300 p-2">
                        <input type="email" name="passenger_details[${i}][email]" placeholder="Email (Optionnel)" class="w-full rounded border-gray-300 p-2">
                    </div>
                </div>
            `;
        }
        html += `
            <div class="mt-4 flex justify-end gap-3 pt-4 border-t">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 rounded font-bold">Annuler</button>
                <button type="submit" class="px-6 py-2 bg-[#e94e1a] text-white rounded font-bold shadow hover:bg-[#d04415]">Confirmer la vente</button>
            </div>
        </form>`;
        
        setModalContent(html, "Informations des passagers");
    }

    // Etape 7 : Soumission
    function submitReservation(e) {
        e.preventDefault();
        const formData = new FormData(e.target);

        // Ajout des infos de contexte
        formData.append('programme_id', currentBooking.progIdAller);
        formData.append('date_voyage', currentBooking.dateAller);
        formData.append('heure_depart', currentBooking.timeAller);
        formData.append('nombre_passagers', currentBooking.passengers);

        if(currentBooking.isReturn) {
            formData.append('programme_retour_id', currentBooking.progIdRetour);
            formData.append('date_retour', currentBooking.dateRetour);
            formData.append('heure_retour', currentBooking.timeRetour);
        }

        Swal.fire({title: 'Validation en cours...', didOpen: () => Swal.showLoading()});

        fetch('{{ route("hotesse.vendre-ticket.submit") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                closeModal();
                Swal.fire('Vente réussie !', data.message, 'success').then(() => {
                    window.location.href = data.redirect;
                });
            } else {
                Swal.fire('Erreur', data.message, 'error');
            }
        })
        .catch(err => Swal.fire('Erreur système', 'Vérifiez votre connexion', 'error'));
    }
</script>
@endsection