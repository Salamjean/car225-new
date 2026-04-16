@extends('user.layouts.template')

@section('title', 'Nouveau Convoi')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="inline-flex bg-white border border-gray-100 rounded-2xl p-1">
            <a href="{{ route('user.convoi.create') }}" class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider bg-[#e94f1b] text-white">
                Nouveau convoi
            </a>
            <a href="{{ route('user.convoi.index') }}" class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider text-gray-600">
                Mes convois
            </a>
        </div>

        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-[#1A1D1F] tracking-tight">
                Demande de <span class="text-[#e94f1b]">Convoi</span>
            </h1>
            <p class="text-sm text-gray-500 font-medium mt-1">Remplissez le formulaire. Choisissez votre gare la plus proche — elle traitera votre demande.</p>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-700 font-semibold text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700 text-sm">
                <p class="font-black mb-2">Veuillez corriger les erreurs suivantes :</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-[28px] border border-gray-100 shadow-sm p-6 sm:p-8">
            <form action="{{ route('user.convoi.store') }}" method="POST" class="space-y-6" id="convoiForm">
                @csrf

                {{-- Compagnie --}}
                <div class="space-y-2">
                    <label class="block text-[11px] font-black uppercase tracking-widest text-gray-500">Compagnie <span class="text-red-500">*</span></label>
                    <select id="compagnieSelect" name="compagnie_id"
                        class="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none text-sm font-bold">
                        <option value="">Choisir une compagnie</option>
                        @foreach ($compagnies as $compagnie)
                            <option value="{{ $compagnie->id }}" @selected(old('compagnie_id') == $compagnie->id)>
                                {{ $compagnie->name }}{{ $compagnie->sigle ? ' (' . $compagnie->sigle . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('compagnie_id')
                        <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Gare la plus proche --}}
                <div class="space-y-2" id="gareSection">
                    <label class="block text-[11px] font-black uppercase tracking-widest text-gray-500">Gare la plus proche <span class="text-red-500">*</span></label>
                    <select id="gareSelect" name="gare_id"
                        class="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none text-sm font-bold"
                        disabled required>
                        <option value="">Choisir d'abord une compagnie</option>
                    </select>
                    <p class="text-[11px] text-gray-400 font-semibold">La gare sélectionnée recevra et traitera votre demande directement.</p>
                    @error('gare_id')
                        <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Itinéraire (Select2 avec recherche) --}}
                <div class="space-y-2">
                    <label class="block text-[11px] font-black uppercase tracking-widest text-gray-500">Itinéraire <span class="text-gray-400 font-normal">(optionnel — ou saisir manuellement)</span></label>
                    <select id="itineraireSelect" name="itineraire_id" disabled
                        class="w-full itineraire-select2">
                        <option value="">Choisir d'abord une compagnie</option>
                    </select>
                    @error('itineraire_id')
                        <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Lieux (auto-rempli ou manuel) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-[11px] font-black uppercase tracking-widest text-gray-500">
                            Lieu de départ <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-green-500 text-sm pointer-events-none">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            <input type="text" id="lieuDepart" name="lieu_depart"
                                value="{{ old('lieu_depart') }}"
                                placeholder="Rechercher un lieu..."
                                class="w-full pl-10 pr-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none text-sm font-bold transition-colors">
                        </div>
                        @error('lieu_depart')
                            <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-black uppercase tracking-widest text-gray-500">
                            Lieu d'arrivée <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-red-500 text-sm pointer-events-none">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            <input type="text" id="lieuArrivee" name="lieu_retour"
                                value="{{ old('lieu_retour') }}"
                                placeholder="Rechercher une destination..."
                                class="w-full pl-10 pr-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none text-sm font-bold transition-colors">
                        </div>
                        @error('lieu_retour')
                            <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Durée estimée --}}
                <div id="durationSection" class="hidden">
                    <div class="flex items-center gap-3 px-5 py-3.5 bg-blue-50 border border-blue-100 rounded-2xl">
                        <i class="fas fa-clock text-blue-500 text-sm"></i>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-wider text-blue-500">Durée estimée du trajet</p>
                            <p id="durationText" class="text-sm font-black text-blue-800">—</p>
                        </div>
                        <div class="ml-auto">
                            <i class="fas fa-road text-blue-400 text-sm mr-1"></i>
                            <span id="distanceText" class="text-sm font-bold text-blue-600">—</span>
                        </div>
                    </div>
                </div>

                {{-- Dates et heures --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-[11px] font-black uppercase tracking-widest text-gray-500">Date de départ <span class="text-red-500">*</span></label>
                        <input type="date" name="date_depart" id="date_depart" value="{{ old('date_depart') }}"
                            min="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none text-sm font-bold">
                        @error('date_depart')
                            <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[11px] font-black uppercase tracking-widest text-gray-500">Heure de départ <span class="text-red-500">*</span></label>
                        <input type="time" name="heure_depart" id="heure_depart" value="{{ old('heure_depart') }}"
                            class="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none text-sm font-bold">
                        @error('heure_depart')
                            <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[11px] font-black uppercase tracking-widest text-gray-500">Date de retour <span class="text-gray-400 font-normal">(optionnel)</span></label>
                        <input type="date" name="date_retour" id="date_retour" value="{{ old('date_retour') }}"
                            min="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none text-sm font-bold">
                        @error('date_retour')
                            <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[11px] font-black uppercase tracking-widest text-gray-500">Heure de retour <span class="text-gray-400 font-normal">(optionnel)</span></label>
                        <input type="time" name="heure_retour" id="heure_retour" value="{{ old('heure_retour') }}"
                            class="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none text-sm font-bold">
                        @error('heure_retour')
                            <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Nombre de personnes --}}
                <div class="space-y-2">
                    <label class="block text-[11px] font-black uppercase tracking-widest text-gray-500">Nombre de personnes <span class="text-red-500">*</span></label>
                    <input type="number" name="nombre_personnes" id="nombre_personnes" min="10" max="1000"
                        value="{{ old('nombre_personnes', 10) }}"
                        class="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none text-sm font-bold">
                    <p class="text-[11px] text-gray-400 font-semibold">Minimum 10 personnes pour un convoi.</p>
                    @error('nombre_personnes')
                        <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Boutons --}}
                <div class="pt-2 border-t border-gray-100 flex flex-col sm:flex-row gap-3">
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-10 py-4 rounded-2xl bg-[#e94f1b] text-white text-xs font-black uppercase tracking-widest shadow-lg shadow-[#e94f1b]/20 hover:bg-[#d44518] transition-all">
                        <i class="fas fa-paper-plane"></i>
                        Envoyer la demande
                    </button>
                    <button type="button" id="resetBtn"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl bg-gray-100 text-gray-600 text-xs font-black uppercase tracking-widest hover:bg-gray-200 transition-all">
                        <i class="fas fa-redo-alt"></i>
                        Réinitialiser
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Adapter Select2 au style Tailwind du formulaire */
        .select2-container--default .select2-selection--single {
            height: auto;
            padding: 11px 16px;
            background: #f9fafb;
            border: 1px solid #f3f4f6;
            border-radius: 1rem;
            font-size: 14px;
            font-weight: 700;
            color: #111827;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            top: 0;
            right: 12px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding: 0;
            line-height: 1.5;
            color: #111827;
        }
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #e94f1b;
            box-shadow: 0 0 0 2px rgba(233,79,27,0.15);
            background: #fff;
            outline: none;
        }
        .select2-container { width: 100% !important; }
        .select2-dropdown { border-radius: 1rem; border: 1px solid #e5e7eb; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 8px 12px; font-size: 13px;
        }
        .select2-container--default .select2-results__option--highlighted {
            background-color: #e94f1b;
        }
        .select2-container--default .select2-results__option {
            font-size: 13px; font-weight: 600; padding: 9px 14px;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af;
        }
    </style>

    {{-- Google Maps --}}
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&loading=async&callback=initConvoiMap" async defer></script>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    // ── State ──────────────────────────────────────────────────────────────
    const oldItineraire = "{{ old('itineraire_id') }}";
    const oldGare       = "{{ old('gare_id') }}";
    let autocompleteDepart = null;
    let autocompleteArrive = null;
    let directionsService  = null;
    let mapsReady          = false;
    let itineraireMode     = false;

    // ── DOM refs ───────────────────────────────────────────────────────────
    const compagnieSelect  = document.getElementById('compagnieSelect');
    const gareSelect       = document.getElementById('gareSelect');
    const itineraireSelect = document.getElementById('itineraireSelect');
    const lieuDepart       = document.getElementById('lieuDepart');
    const lieuArrivee      = document.getElementById('lieuArrivee');
    const durationSection  = document.getElementById('durationSection');
    const durationText     = document.getElementById('durationText');
    const distanceText     = document.getElementById('distanceText');

    // ── Init Select2 ──────────────────────────────────────────────────────
    $(document).ready(function () {
        $('#itineraireSelect').select2({
            placeholder: "Choisir d'abord une compagnie",
            allowClear: true,
            language: {
                noResults: () => "Aucun itinéraire trouvé",
                searching: () => "Recherche..."
            }
        });

        // Écouter le changement Select2
        $('#itineraireSelect').on('change', applyItineraireSelection);

        // Bouton réinitialiser
        document.getElementById('resetBtn').addEventListener('click', resetForm);
    });

    // ── Google Maps ready ─────────────────────────────────────────────────
    function initConvoiMap() {
        mapsReady = true;
        directionsService = new google.maps.DirectionsService();
        const opts = { componentRestrictions: { country: 'ci' }, fields: ['formatted_address', 'geometry', 'name'] };
        autocompleteDepart = new google.maps.places.Autocomplete(lieuDepart, opts);
        autocompleteArrive = new google.maps.places.Autocomplete(lieuArrivee, opts);
        autocompleteDepart.addListener('place_changed', tryCalculateDuration);
        autocompleteArrive.addListener('place_changed', tryCalculateDuration);
    }

    // ── Gares AJAX ────────────────────────────────────────────────────────
    function resetGares(msg) {
        gareSelect.innerHTML = `<option value="">${msg}</option>`;
        gareSelect.disabled = true;
    }

    async function loadGares(compagnieId) {
        if (!compagnieId) { resetGares('Choisir d\'abord une compagnie'); return; }
        gareSelect.innerHTML = '<option value="">Chargement...</option>';
        gareSelect.disabled = true;
        try {
            const res   = await fetch(`/user/convoi/compagnie/${compagnieId}/gares`, { headers: { 'Accept': 'application/json' } });
            const data  = await res.json();
            const items = data.gares || [];
            if (items.length === 0) {
                resetGares('Aucune gare disponible');
                return;
            }
            let opts = '<option value="">Choisir une gare...</option>';
            items.forEach(g => {
                const label = g.nom_gare + (g.ville ? ' — ' + g.ville : '');
                const selected = oldGare && String(oldGare) === String(g.id) ? ' selected' : '';
                opts += `<option value="${g.id}"${selected}>${label}</option>`;
            });
            gareSelect.innerHTML = opts;
            gareSelect.disabled = false;
        } catch (e) {
            resetGares('Erreur de chargement');
        }
    }

    // ── Itinéraires AJAX ───────────────────────────────────────────────────
    function resetItineraires(msg) {
        const $sel = $('#itineraireSelect');
        $sel.empty().append(new Option(msg, '', true, true)).prop('disabled', true);
        $sel.trigger('change.select2');
        setManualMode();
    }

    async function loadItineraires(compagnieId) {
        if (!compagnieId) { resetItineraires("Choisir d'abord une compagnie"); return; }

        const $sel = $('#itineraireSelect');
        $sel.empty().append(new Option('Chargement...', '', true, true)).prop('disabled', true);
        $sel.trigger('change.select2');

        try {
            const res  = await fetch(`/user/convoi/compagnie/${compagnieId}/itineraires`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            const items = data.itineraires || [];

            $sel.empty().append(new Option('— Saisir manuellement —', '', true, true));
            items.forEach(it => {
                const opt = new Option(it.point_depart + ' → ' + it.point_arrive, it.id);
                opt.dataset = { depart: it.point_depart, arrive: it.point_arrive, duration: it.durer_parcours || '' };
                $(opt).data('depart', it.point_depart).data('arrive', it.point_arrive).data('duration', it.durer_parcours || '');
                if (oldItineraire && String(oldItineraire) === String(it.id)) opt.selected = true;
                $sel.append(opt);
            });
            $sel.prop('disabled', false).trigger('change.select2');
            applyItineraireSelection();
        } catch (e) {
            resetItineraires('Erreur de chargement');
        }
    }

    function applyItineraireSelection() {
        const $sel = $('#itineraireSelect');
        const selectedVal = $sel.val();
        if (!selectedVal) { setManualMode(); return; }

        const $opt = $sel.find('option:selected');
        const depart   = $opt.data('depart');
        const arrive   = $opt.data('arrive');
        const duration = $opt.data('duration');

        if (depart && arrive) {
            setReadonlyMode(depart, arrive, duration);
        } else {
            setManualMode();
        }
    }

    function setReadonlyMode(depart, arrive, duration) {
        itineraireMode = true;
        lieuDepart.value    = depart;
        lieuArrivee.value   = arrive;
        lieuDepart.readOnly  = true;
        lieuArrivee.readOnly = true;
        lieuDepart.classList.add('bg-gray-100', 'cursor-not-allowed');
        lieuDepart.classList.remove('bg-gray-50');
        lieuArrivee.classList.add('bg-gray-100', 'cursor-not-allowed');
        lieuArrivee.classList.remove('bg-gray-50');

        if (duration) {
            durationText.textContent = duration;
            distanceText.textContent = '';
            durationSection.classList.remove('hidden');
        } else {
            durationSection.classList.add('hidden');
        }
    }

    function setManualMode() {
        itineraireMode = false;
        lieuDepart.readOnly  = false;
        lieuArrivee.readOnly = false;
        lieuDepart.classList.remove('bg-gray-100', 'cursor-not-allowed');
        lieuDepart.classList.add('bg-gray-50');
        lieuArrivee.classList.remove('bg-gray-100', 'cursor-not-allowed');
        lieuArrivee.classList.add('bg-gray-50');
        durationSection.classList.add('hidden');
    }

    // ── Calcul durée via Google Directions ─────────────────────────────────
    function tryCalculateDuration() {
        if (itineraireMode || !mapsReady || !directionsService) return;
        const origin      = lieuDepart.value.trim();
        const destination = lieuArrivee.value.trim();
        if (!origin || !destination) return;

        directionsService.route({
            origin, destination, travelMode: google.maps.TravelMode.DRIVING
        }).then(response => {
            const leg = response.routes[0].legs[0];
            durationText.textContent = leg.duration.text;
            distanceText.textContent = leg.distance.text;
            durationSection.classList.remove('hidden');
        }).catch(() => {
            durationSection.classList.add('hidden');
        });
    }

    // ── Réinitialiser le formulaire ─────────────────────────────────────────
    function resetForm() {
        // Compagnie
        compagnieSelect.value = '';
        // Gare
        resetGares('Choisir d\'abord une compagnie');
        // Itinéraire
        const $sel = $('#itineraireSelect');
        $sel.empty().append(new Option("Choisir d'abord une compagnie", '', true, true)).prop('disabled', true);
        $sel.trigger('change.select2');
        // Lieux
        setManualMode();
        lieuDepart.value  = '';
        lieuArrivee.value = '';
        // Durée
        durationSection.classList.add('hidden');
        // Dates / heures
        document.getElementById('date_depart').value  = '';
        document.getElementById('heure_depart').value = '';
        document.getElementById('date_retour').value  = '';
        document.getElementById('heure_retour').value = '';
        // Personnes
        document.getElementById('nombre_personnes').value = 10;
    }

    // ── Events ─────────────────────────────────────────────────────────────
    compagnieSelect.addEventListener('change', () => {
        loadItineraires(compagnieSelect.value);
        loadGares(compagnieSelect.value);
    });
    lieuDepart.addEventListener('blur',  () => { if (!itineraireMode) tryCalculateDuration(); });
    lieuArrivee.addEventListener('blur', () => { if (!itineraireMode) tryCalculateDuration(); });

    if (compagnieSelect.value) {
        loadItineraires(compagnieSelect.value);
        loadGares(compagnieSelect.value);
    }
    </script>
    @endpush
@endsection
