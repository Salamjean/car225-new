@extends('gare-espace.layouts.template')

@section('title', 'Programmer les Voyages')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    .ts-wrapper .ts-control {
        border-radius: 0.75rem !important;
        padding: 0.75rem 1rem !important;
        border-color: #e5e7eb !important;
        background-color: #f9fafb !important;
        font-size: 0.875rem !important;
        transition: all 0.2s;
    }
    .ts-wrapper.focus .ts-control {
        box-shadow: 0 0 0 4px rgba(233, 79, 27, 0.1) !important;
        border-color: #e94f1b !important;
        background-color: #fff !important;
    }
    .ts-dropdown {
        border-radius: 0.75rem !important;
        margin-top: 5px !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        border: 1px solid #e5e7eb !important;
    }
    .time-slot-btn {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .time-slot-btn.active {
        background-color: #e94f1b !important;
        color: white !important;
        border-color: #e94f1b !important;
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(233, 79, 27, 0.3);
    }
    .assignment-card {
        transition: all 0.3s ease;
    }
    .assignment-card:hover {
        transform: translateY(-4px);
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-4xl font-black text-gray-900 mb-2 leading-tight">Programmation <span class="text-orange-600">des Voyages</span></h2>
                <p class="text-gray-500 text-lg">Gérez vos départs et assignations avec précision</p>
            </div>
            <div class="bg-white p-2 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-2">
                <form action="{{ route('gare-espace.voyages.index') }}" method="GET" class="flex items-center gap-2">
                    <div class="relative">
                        <i class="fas fa-calendar absolute left-4 top-1/2 -translate-y-1/2 text-blue-500"></i>
                        <input type="date" name="date" value="{{ $date }}" min="{{ date('Y-m-d') }}"
                            onchange="this.form.submit()"
                            class="pl-11 pr-4 py-2.5 border-none rounded-xl bg-gray-50 focus:ring-0 font-bold text-gray-700 cursor-pointer hover:bg-gray-100 transition-colors">
                    </div>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-2xl flex items-center shadow-sm">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4 text-green-600">
                    <i class="fas fa-check"></i>
                </div>
                <p class="text-green-700 font-bold">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-2xl flex items-center shadow-sm">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-4 text-red-600">
                    <i class="fas fa-exclamation"></i>
                </div>
                <p class="text-red-700 font-bold">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-6">
                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                    <i class="fas fa-calendar-alt text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Missions du jour</p>
                    <p class="text-3xl font-black text-gray-900">{{ $totalProgrammesCount }}</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-6">
                <div class="w-16 h-16 bg-green-50 rounded-2xl flex items-center justify-center text-green-600">
                    <i class="fas fa-users-cog text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Chauffeurs Libres</p>
                    <p class="text-3xl font-black text-gray-900">{{ $chauffeurs->count() }}</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-6">
                <div class="w-16 h-16 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-600">
                    <i class="fas fa-bus text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Véhicules Prêts</p>
                    <p class="text-3xl font-black text-gray-900">{{ $vehicules->count() }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Column: Search & New Assignment -->
            <div class="lg:col-span-12">
                <div class="bg-white rounded-[2rem] shadow-xl shadow-blue-900/5 border border-gray-100 overflow-hidden mb-12">
                    <div class="p-8 md:p-12">
                        <div class="max-w-4xl mx-auto text-center mb-10">
                            <h3 class="text-3xl font-bold text-gray-900 mb-4 tracking-tight">Nouvelle <span class="text-orange-600 underline decoration-orange-200 underline-offset-8">Assignation</span></h3>
                            <p class="text-gray-500">Sélectionnez une destination pour voir les horaires disponibles</p>
                        </div>

                        <!-- Step 1: Destination Selection -->
                        <div class="max-w-xl mx-auto mb-10 text-left">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-2">Destination d'arrivée</label>
                            <select id="itinerary-filter" class="tom-select">
                                <option value="">Rechercher une destination...</option>
                                @foreach($itineraries as $it)
                                    <option value="{{ $it['id'] }}">{{ $it['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Step 2: Time Slot Selection (Dynamic) -->
                        <div id="time-slots-container" class="hidden transition-all duration-500">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="h-px bg-gray-200 flex-1"></div>
                                <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Horaires disponibles</span>
                                <div class="h-px bg-gray-200 flex-1"></div>
                            </div>
                            
                            <div id="time-slots-grid" class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4 mb-10">
                                <!-- Slots will be injected here via JS -->
                            </div>
                        </div>

                        <!-- Step 3: Assignment Form (Dynamic) -->
                        <div id="assignment-form-container" class="hidden animate__animated animate__fadeIn border-t border-gray-100 pt-10">
                            <form action="{{ route('gare-espace.voyages.store') }}" method="POST" id="main-assignment-form">
                                @csrf
                                <input type="hidden" name="programme_id" id="selected-programme-id">
                                <input type="hidden" name="date_voyage" value="{{ $date }}">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                                    <div class="space-y-3">
                                        <label class="block text-sm font-bold text-gray-700 flex items-center gap-2">
                                            <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xs">1</span>
                                            Chauffeur
                                        </label>
                                        <select name="personnel_id" required class="form-select-ts">
                                            <option value="">Choisir un chauffeur...</option>
                                            @foreach($chauffeurs as $ch)
                                                <option value="{{ $ch->id }}">{{ $ch->name }} {{ $ch->prenom }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="space-y-3">
                                        <label class="block text-sm font-bold text-gray-700 flex items-center gap-2">
                                            <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xs">2</span>
                                            Véhicule
                                        </label>
                                        <select name="vehicule_id" required class="form-select-ts vehicule-select">
                                            <option value="">Choisir un véhicule...</option>
                                            @foreach($vehicules as $v)
                                                <option value="{{ $v->id }}" data-capacity="{{ $v->nombre_place }}">
                                                    {{ $v->immatriculation }} ({{ $v->nombre_place }} places - {{ $v->marque }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="flex justify-center">
                                    <button type="submit" class="group flex items-center gap-3 bg-blue-600 text-white px-10 py-4 rounded-2xl font-bold hover:bg-blue-700 shadow-xl shadow-blue-500/20 transition-all transform hover:-translate-y-1">
                                        <span>Confirmer l'assignation</span>
                                        <i class="fas fa-chevron-right text-xs group-hover:translate-x-1 transition-transform"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Section: Assigned Voyages -->
                <div class="mb-12">
                    <div class="flex items-center gap-4 mb-6">
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight">Voyages <span class="text-orange-600">Assignés</span></h3>
                        <div class="h-px bg-gray-200 flex-1"></div>
                    </div>

                    @if($assignedVoyages->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach($assignedVoyages as $prog)
                                @php 
                                    $voyage = $prog->voyages->first(); 
                                    $occ = $prog->getPourcentageOccupationForDate($date);
                                    $res = $prog->getPlacesReserveesForDate($date);
                                    $totalS = $prog->getTotalSeats($date);
                                @endphp
                                <div class="assignment-card bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col cursor-pointer hover:border-blue-300 group" 
                                     onclick="showVoyageDetails({{ json_encode([
                                         'id' => $prog->id,
                                         'route' => ($prog->point_depart ?: $prog->gareDepart?->nom_gare) . ' → ' . ($prog->point_arrive ?: $prog->gareArrivee?->nom_gare),
                                         'gare_depart' => $prog->gareDepart?->nom_gare,
                                         'gare_arrivee' => $prog->gareArrivee?->nom_gare,
                                         'time' => \Carbon\Carbon::parse($prog->heure_depart)->format('H:i'),
                                         'arrival' => $voyage->estimated_arrival_at ? $voyage->estimated_arrival_at->format('H:i') : null,
                                         'tarif' => number_format($prog->montant_billet, 0, ',', ' '),
                                         'occupancy' => $occ,
                                         'reservations' => $res,
                                         'total_seats' => $totalS,
                                         'chauffeur' => $voyage->chauffeur?->name . ' ' . $voyage->chauffeur?->prenom,
                                         'vehicule' => $voyage->vehicule?->immatriculation,
                                         'statut' => $voyage->statut,
'temps_restant' => $voyage->temps_restant
                                     ]) }})">
                                    <div class="p-6 bg-gradient-to-br from-gray-50 to-blue-50 border-b border-gray-100">
                                        <div class="flex justify-between items-start mb-4">
                                            <div class="bg-white px-4 py-2 rounded-xl border border-blue-100 font-black text-blue-700 text-lg shadow-sm">
                                                {{ \Carbon\Carbon::parse($prog->heure_depart)->format('H:i') }}
                                            </div>
                                            @if($voyage->statut === 'en_cours')
                                                <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-lg text-xs font-bold uppercase tracking-wider flex items-center gap-2">
                                                    <span class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></span>
                                                    En cours
                                                </span>
                                            @elseif($voyage->statut === 'confirmé')
                                                 <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold uppercase tracking-wider">Confirmé</span>
                                            @elseif($voyage->statut === 'en_attente')
                                                 <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-bold uppercase tracking-wider">Assigné</span>
                                            @else
                                                 <span class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-bold uppercase tracking-wider">Terminé</span>
                                            @endif
                                        </div>
                                        <h4 class="font-bold text-gray-900 flex items-center gap-2 group-hover:text-blue-600 transition-colors">
                                            {{ $prog->point_depart ?: $prog->gareDepart?->nom_gare }} 
                                            <i class="fas fa-long-arrow-alt-right text-gray-300"></i>
                                            {{ $prog->point_arrive ?: $prog->gareArrivee?->nom_gare }}
                                        </h4>
                                        <div class="flex items-center gap-4 mt-3">
                                            <div class="flex-1 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-full {{ $occ > 80 ? 'bg-orange-500' : 'bg-blue-500' }}" style="width: {{ $occ }}%"></div>
                                            </div>
                                            <span class="text-[10px] font-black text-gray-500">{{ $occ }}%</span>
                                        </div>
                                    </div>
                                    <div class="p-6 space-y-4 flex-1">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 text-sm">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <div class="truncate">
                                                    <p class="text-[9px] font-black text-gray-400 uppercase leading-none mb-1">Passagers</p>
                                                    <p class="text-xs font-bold text-gray-800">{{ $res }}/{{ $totalS }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center text-orange-600 text-sm">
                                                    <i class="fas fa-tag"></i>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase leading-none mb-1">Tarif</p>
                                                    <p class="text-xs font-bold text-gray-800">{{ number_format($prog->montant_billet, 0, ',', ' ') }} F</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if($voyage->statut !== 'terminé')
                                        <div class="px-3 pb-3">
                                            <form action="{{ route('gare-espace.voyages.destroy', $voyage->id) }}" method="POST" onsubmit="return confirm('Confirmer l\'annulation ?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="w-full py-2 text-[10px] font-black text-red-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all uppercase tracking-widest border border-red-50">
                                                    Annuler
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white/50 backdrop-blur-sm border-2 border-dashed border-gray-200 rounded-[2rem] p-12 text-center">
                            <div class="w-20 h-20 bg-gray-100 rounded-3xl flex items-center justify-center mx-auto mb-6 text-gray-300">
                                <i class="fas fa-calendar-times text-4xl"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-400">Aucun voyage assigné pour aujourd'hui</h4>
                            <p class="text-gray-400 text-sm mt-2">Utilisez le formulaire ci-dessus pour commencer.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data injection for JS -->
<script id="available-programmes-data" type="application/json">
    {!! json_encode($availableProgrammes->map(function($p) use ($date) {
        return [
            'id' => $p->id,
            'destination_id' => $p->gare_arrivee_id,
            'time' => \Carbon\Carbon::parse($p->heure_depart)->format('H:i'),
            'capacity' => $p->getTotalSeats(),
            'reservations' => $p->getPlacesReserveesForDate($date)
        ];
    })) !!}
</script>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const availableProgrammes = JSON.parse(document.getElementById('available-programmes-data').textContent);
    const itineraryFilter = document.getElementById('itinerary-filter');
    const timeSlotsContainer = document.getElementById('time-slots-container');
    const timeSlotsGrid = document.getElementById('time-slots-grid');
    const assignmentFormContainer = document.getElementById('assignment-form-container');
    const selectedProgrammeInput = document.getElementById('selected-programme-id');
    
    // Initialize TomSelect for main filter
    const tsFilter = new TomSelect(itineraryFilter, {
        create: false,
        placeholder: "Rechercher un trajet...",
        valueField: 'id',
        labelField: 'route',
        searchField: ['route', 'name'],
        options: {!! json_encode($itineraries) !!},
        render: {
            option: function(data, escape) {
                return `<div>
                    <div class="flex justify-between items-center">
                        <span class="block font-bold text-gray-900">${escape(data.route)}</span>
                        <span class="text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">${escape(data.capacity)} Places</span>
                    </div>
                    <span class="block text-xs text-gray-500">${escape(data.name)}</span>
                </div>`;
            },
            item: function(data, escape) {
                return `<div class="flex items-center gap-2">
                    <i class="fas fa-route text-blue-500 text-xs"></i>
                    <span class="font-bold text-gray-700">${escape(data.route)}</span>
                </div>`;
            }
        },
        onChange: function(value) {
            handleItineraryChange(value);
        }
    });

    // Initialize TomSelect for other selects
    document.querySelectorAll('.form-select-ts').forEach(el => {
        new TomSelect(el, { create: false });
    });

    function handleItineraryChange(destinationId) {
        if (!destinationId) {
            timeSlotsContainer.classList.add('hidden');
            assignmentFormContainer.classList.add('hidden');
            return;
        }

        const filtered = availableProgrammes.filter(p => p.destination_id == destinationId);
        
        // Clear and build grid
        timeSlotsGrid.innerHTML = '';
        if (filtered.length > 0) {
            filtered.forEach(p => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'time-slot-btn py-4 px-6 bg-white border border-gray-200 rounded-2xl font-bold text-gray-700 hover:border-orange-500 hover:text-orange-600 transition-all flex flex-col items-center justify-center gap-1 shadow-sm';
                btn.innerHTML = `
                    <span class="text-xl font-black">${p.time}</span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full">${p.reservations} / ${p.capacity} Réserv.</span>
                `;
                btn.onclick = () => selectProgramme(p, btn);
                timeSlotsGrid.appendChild(btn);
            });
            timeSlotsContainer.classList.remove('hidden');
            assignmentFormContainer.classList.add('hidden');
        } else {
            timeSlotsContainer.classList.add('hidden');
            Swal.fire('Oups', 'Aucun programme disponible pour cette destination.', 'info');
        }
    }

    function selectProgramme(programme, btn) {
        // Active visual state
        document.querySelectorAll('.time-slot-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        // Set form data
        selectedProgrammeInput.value = programme.id;
        
        // Show form
        assignmentFormContainer.classList.remove('hidden');
        assignmentFormContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Validation capacity
    const form = document.getElementById('main-assignment-form');
    form.addEventListener('submit', function(e) {
        const progId = selectedProgrammeInput.value;
        const prog = availableProgrammes.find(p => p.id == progId);
        const vehiculeSelect = form.querySelector('.vehicule-select');
        const selectedVehicule = vehiculeSelect.options[vehiculeSelect.selectedIndex];

        if (selectedVehicule && selectedVehicule.dataset.capacity) {
            const cap = parseInt(selectedVehicule.dataset.capacity);
            // Allow multiple assignments per program (doubler les bus)
            if (cap < prog.reservations) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Capacité insuffisante',
                    text: `Ce véhicule (${cap} places) ne peut pas accueillir les ${prog.reservations} réservations existantes.`
                });
            }
        }
    });
    window.showVoyageDetails = function(data) {
        Swal.fire({
            title: `<div class="text-left"><span class="block text-xs uppercase tracking-widest text-gray-400 mb-1">Détails Programme #${data.id}</span><span class="text-2xl font-black text-gray-900">${data.route}</span></div>`,
            html: `
                <div class="text-left mt-6 space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Départ Prévu</p>
                            <p class="text-xl font-black text-blue-600">${data.time}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            <p class="text-[10px] font-black text-gray-400 uppercase mb-1">${data.statut === 'en_cours' ? 'Arrivée Estimée' : 'Tarif Billet'}</p>
                            <p class="text-xl font-black ${data.statut === 'en_cours' ? 'text-purple-600' : 'text-gray-900'}">${data.statut === 'en_cours' ? (data.arrival || 'Calcul...') : (data.tarif + ' F')}</p>
                        </div>
                    </div>
                    
                    ${data.statut === 'en_cours' ? `
                    <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-blue-400 uppercase mb-1">Temps Restant (GPS)</p>
                            <p class="text-xl font-black text-blue-700 animate-pulse">${data.temps_restant || 'Localisation en cours...'}</p>
                        </div>
                        <i class="fas fa-satellite-dish text-2xl text-blue-300"></i>
                    </div>
                    ` : ''}
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center gap-3 p-3 bg-gray-50/50 rounded-2xl border border-gray-100/50 transition-colors">
                            <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center shrink-0"><i class="fas fa-building"></i></div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-black text-gray-400 uppercase leading-none mb-1">DÉPART</p>
                                <p class="font-bold text-gray-800 text-sm truncate">${data.gare_depart}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-gray-50/50 rounded-2xl border border-gray-100/50 transition-colors">
                            <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center shrink-0"><i class="fas fa-map-marker-alt"></i></div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-black text-gray-400 uppercase leading-none mb-1">ARRIVÉE</p>
                                <p class="font-bold text-gray-800 text-sm truncate">${data.gare_arrivee}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-gray-50/50 rounded-2xl border border-gray-100/50 transition-colors">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0"><i class="fas fa-user-tie"></i></div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-black text-gray-400 uppercase leading-none mb-1">CHAUFFEUR</p>
                                <p class="font-bold text-gray-800 text-sm truncate">${data.chauffeur}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-gray-50/50 rounded-2xl border border-gray-100/50 transition-colors">
                            <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center shrink-0"><i class="fas fa-bus"></i></div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-black text-gray-400 uppercase leading-none mb-1">VÉHICULE</p>
                                <p class="font-bold text-gray-800 text-sm truncate">${data.vehicule}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-orange-600 p-6 rounded-[2rem] shadow-xl shadow-orange-500/20 text-white relative overflow-hidden">
                        <div class="relative z-10">
                            <div class="flex justify-between items-end mb-4">
                                <div>
                                    <p class="text-[10px] font-black uppercase opacity-60">Occupation</p>
                                    <p class="text-3xl font-black">${data.occupancy}%</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-black uppercase opacity-60">Passagers</p>
                                    <p class="text-xl font-bold">${data.reservations} / ${data.total_seats}</p>
                                </div>
                            </div>
                            <div class="w-full h-2 bg-white/20 rounded-full overflow-hidden">
                                <div class="h-full bg-white" style="width: ${data.occupancy}%"></div>
                            </div>
                        </div>
                        <i class="fas fa-users absolute -right-4 -bottom-4 text-8xl opacity-10 rotate-12"></i>
                    </div>
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'Fermer',
            confirmButtonColor: '#e94f1b',
            width: '95%',
            customClass: {
                popup: 'rounded-[2.5rem] p-6 md:p-10 max-w-[550px]',
                title: 'text-left border-none p-0',
                htmlContainer: 'text-left p-0 m-0'
            }
        });
    };
});
</script>
@endsection
@endsection
