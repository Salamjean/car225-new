@extends('gare-espace.layouts.template')

@section('title', 'Créer une ligne de transport')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    /* ── Tom Select ── */
    .ts-control {
        border-radius: 10px !important; padding: 10px 14px !important;
        background-color: #f8fafc !important; border: 1px solid #e2e8f0 !important;
        font-size: 13px !important; color: #1e293b !important;
    }
    .ts-wrapper.focus .ts-control {
        border-color: #f97316 !important;
        box-shadow: 0 0 0 3px rgba(249,115,22,0.15) !important;
        background-color: #fff !important;
    }
    .ts-dropdown { border-radius: 10px !important; border: 1px solid #e2e8f0 !important; font-size: 13px !important; box-shadow: 0 8px 24px rgba(0,0,0,0.1) !important; }
    .ts-dropdown .active { background-color: #fff7ed !important; color: #c2410c !important; }
    select.ts-hidden-accessible { display: none !important; }

    /* ── Carte Aller ── */
    .programme-card { background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; transition: box-shadow .3s, transform .3s; }
    .programme-card:hover { box-shadow: 0 10px 30px rgba(0,0,0,0.08); transform: translateY(-2px); }
    .programme-card.aller { border-top: 4px solid #16a34a; }

    .card-header-custom { padding: 14px 18px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid #f1f5f9; background: #f0fdf4; }
    .card-icon { width: 38px; height: 38px; border-radius: 10px; background: #16a34a; color: white; display: flex; align-items: center; justify-content: center; font-size: 15px; }
    .card-title h3 { font-size: 14px; font-weight: 800; color: #1e293b; margin: 0; }
    .card-title .route { font-size: 11px; font-weight: 600; color: #64748b; margin-top: 2px; }
    .card-body { padding: 18px; }

    /* ── Schedule Items ── */
    .schedule-item {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 14px; margin-bottom: 12px; position: relative; transition: border-color .2s;
    }
    .schedule-item:hover { border-color: #94a3b8; }
    .badge-horaire {
        position: absolute; top: -10px; left: 12px;
        background: #16a34a; color: white;
        font-size: 9px; font-weight: 800; padding: 2px 8px;
        border-radius: 6px; text-transform: uppercase;
    }
    .schedule-times { display: flex; align-items: center; gap: 12px; margin-top: 8px; }
    .time-input-group { flex: 1; }
    .time-input-group label { display: block; font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px; }
    .time-input {
        width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px;
        font-size: 14px; font-weight: 700; text-align: center;
        background: #f8fafc; color: #1e293b; transition: all .2s;
    }
    .time-input:focus { outline: none; border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,.15); background: #fff; }
    .time-input.arrival { background: #f1f5f9; color: #94a3b8; cursor: not-allowed; border-style: dashed; }
    .time-arrow { color: #94a3b8; margin-top: 14px; font-size: 11px; }
    .remove-schedule {
        position: absolute; top: -10px; right: -10px; width: 22px; height: 22px;
        border-radius: 50%; background: #fff; border: 1px solid #ef4444;
        color: #ef4444; cursor: pointer; display: flex; align-items: center;
        justify-content: center; font-size: 9px; transition: all .2s;
    }
    .remove-schedule:hover { background: #ef4444; color: white; }
    .add-schedule-btn {
        display: flex; align-items: center; justify-content: center; gap: 6px;
        width: 100%; padding: 11px; margin-top: 14px;
        border: 1px dashed #e2e8f0; border-radius: 10px;
        background: #f8fafc; color: #64748b;
        font-weight: 700; font-size: 12px; cursor: pointer; transition: all .2s;
    }
    .add-schedule-btn:hover { border-color: #16a34a; color: #16a34a; background: #f0fdf4; }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="max-w-3xl mx-auto">

        <a href="{{ route('gare-espace.programme.index') }}"
           class="inline-flex items-center gap-2 text-gray-400 hover:text-gray-600 text-sm font-semibold mb-6 transition-colors">
            <i class="fas fa-arrow-left"></i> Retour à la liste des lignes
        </a>

        @if(session('error'))
            <div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                <i class="fas fa-exclamation-circle mt-0.5 flex-shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        @if($itineraires->isEmpty())
            {{-- Pas d'itinéraires --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10 text-center">
                <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-map-marked-alt text-amber-500 text-2xl"></i>
                </div>
                <h3 class="font-extrabold text-gray-800 text-lg mb-2">Aucun itinéraire disponible</h3>
                <p class="text-gray-400 text-sm mb-6">
                    Votre gare n'a pas encore d'itinéraires configurés.<br>
                    Commencez par en créer un depuis l'onglet <strong>Itinéraires</strong>.
                </p>
                <a href="{{ route('gare-espace.itineraire.create') }}"
                   class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 py-3 rounded-xl shadow-md transition-all">
                    <i class="fas fa-plus"></i> Créer un itinéraire
                </a>
            </div>
        @else

        <form action="{{ route('gare-espace.programme.store') }}" method="POST" id="programmeFormGare">
            @csrf

            {{-- ÉTAPE 1 : Itinéraire et Gares --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
                <div class="flex items-center gap-3 mb-5">
                    <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">
                        <i class="fas fa-map-marked-alt"></i> Étape 1
                    </span>
                    <h2 class="font-extrabold text-gray-800">Choisir l'itinéraire et la gare d'arrivée</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    {{-- Itinéraire --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Itinéraire</label>
                        <select name="itineraire_id" id="itineraire_id" required>
                            <option value="">-- Sélectionner --</option>
                            @foreach($itineraires as $it)
                                <option value="{{ $it->id }}"
                                    data-depart="{{ $it->point_depart }}"
                                    data-arrive="{{ $it->point_arrive }}"
                                    data-duree="{{ $it->durer_parcours }}"
                                    {{ old('itineraire_id', $preselectedItineraireId ?? '') == $it->id ? 'selected' : '' }}>
                                    {{ $it->point_depart }} → {{ $it->point_arrive }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Gare départ : fixée, non modifiable --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Gare de départ</label>
                        <input type="hidden" name="gare_depart_id" value="{{ $gare->id }}">
                        <div class="flex items-center gap-2 px-4 py-3 bg-orange-50 border border-orange-200 rounded-xl">
                            <i class="fas fa-map-marker-alt text-orange-500 text-sm flex-shrink-0"></i>
                            <div>
                                <p class="font-bold text-orange-700 text-sm leading-tight">{{ $gare->nom_gare }}</p>
                                <p class="text-orange-400 text-xs">{{ $gare->ville }}</p>
                            </div>
                            <i class="fas fa-lock text-orange-300 text-xs ml-auto" title="Non modifiable"></i>
                        </div>
                    </div>

                    {{-- Gare d'arrivée --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Gare d'arrivée</label>
                        @if($garesArrivee->isEmpty())
                            <div class="px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-xs font-medium">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Aucune autre gare disponible
                            </div>
                        @else
                            <select name="gare_arrivee_id" id="gare_arrivee_id" required>
                                <option value="">-- Sélectionner --</option>
                                @foreach($garesArrivee as $g)
                                    <option value="{{ $g->id }}">{{ $g->nom_gare }} ({{ $g->ville }})</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ÉTAPE 2 : Horaires ALLER uniquement --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5" id="cards-container" style="display:none;">
                <div class="flex items-center gap-3 mb-5">
                    <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                        <i class="fas fa-clock"></i> Étape 2
                    </span>
                    <h2 class="font-extrabold text-gray-800">Configurer les horaires de départ</h2>
                </div>

                {{-- Info : seulement aller --}}
                <div class="flex items-center gap-3 bg-blue-50 border border-blue-200 text-blue-700 text-sm px-4 py-3 rounded-xl mb-5">
                    <i class="fas fa-info-circle flex-shrink-0"></i>
                    <p>
                        Vous créez des horaires <strong>au départ de votre gare uniquement</strong>.
                        La gare d'arrivée crée elle-même ses horaires retour.
                    </p>
                </div>

                <div class="programme-card aller">
                    <div class="card-header-custom">
                        <div class="card-icon"><i class="fas fa-bus-alt"></i></div>
                        <div class="card-title">
                            <h3>Horaires Aller</h3>
                            <div class="route" id="aller-route">-- → --</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="schedule-container" id="aller-schedules"></div>
                        <button type="button" class="add-schedule-btn" onclick="addSchedule()">
                            <i class="fas fa-plus-circle"></i> Ajouter un horaire
                        </button>
                    </div>
                </div>

                {{-- Étape 3 & 4 : Capacité + Tarif --}}
                <div class="mt-6 bg-amber-50 border border-amber-200 rounded-2xl p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="text-center">
                            <span class="inline-flex items-center gap-1.5 bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full mb-3">
                                <i class="fas fa-users"></i> Étape 3
                            </span>
                            <h4 class="font-extrabold text-gray-800 text-sm mb-1">Capacité du car</h4>
                            <p class="text-xs text-gray-400 mb-4">Nombre de places disponibles</p>
                            <div class="relative max-w-[200px] mx-auto">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg">💺</span>
                                <input type="number" name="capacity" id="capacity" min="1" max="200"
                                    value="{{ old('capacity', 64) }}" placeholder="64" required
                                    class="w-full pl-10 pr-16 py-3 text-center font-extrabold text-lg border border-amber-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-orange-400">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-amber-600">Places</span>
                            </div>
                        </div>
                        <div class="text-center">
                            <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-700 text-xs font-bold px-3 py-1 rounded-full mb-3">
                                <i class="fas fa-coins"></i> Étape 4
                            </span>
                            <h4 class="font-extrabold text-gray-800 text-sm mb-1">Tarif du billet</h4>
                            <p class="text-xs text-gray-400 mb-4">Prix du billet aller</p>
                            <div class="relative max-w-[200px] mx-auto">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg">💰</span>
                                <input type="number" name="montant_billet" id="montant_billet" min="0" step="100"
                                    value="{{ old('montant_billet', $existingMontantBillet ?? '') }}" placeholder="0" required
                                    class="w-full pl-10 pr-16 py-3 text-center font-extrabold text-lg border border-amber-300 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-orange-400">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-amber-600">FCFA</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RÉCAPITULATIF --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5" id="summary-section" style="display:none;">
                <h3 class="flex items-center gap-2 font-extrabold text-gray-800 mb-5">
                    <i class="fas fa-check-circle text-green-500"></i> Récapitulatif
                </h3>
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <div class="text-2xl mb-1">🚌</div>
                        <p class="text-2xl font-extrabold text-green-600" id="summary-aller">0</p>
                        <p class="text-xs font-bold text-gray-400 uppercase">Horaire(s)</p>
                    </div>
                    <div class="text-center bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <div class="text-2xl mb-1">💵</div>
                        <p class="text-2xl font-extrabold text-amber-600" id="summary-price">0</p>
                        <p class="text-xs font-bold text-gray-400 uppercase">FCFA / Billet</p>
                    </div>
                    <div class="text-center bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <div class="text-2xl mb-1">💺</div>
                        <p class="text-2xl font-extrabold text-blue-600" id="summary-capacity">64</p>
                        <p class="text-xs font-bold text-gray-400 uppercase">Places / Car</p>
                    </div>
                </div>
            </div>

            {{-- BOUTON SUBMIT --}}
            <div class="text-center pb-8" id="submit-section" style="display:none;">
                <button type="submit"
                    class="inline-flex items-center gap-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-extrabold px-10 py-4 rounded-2xl shadow-lg hover:shadow-xl transition-all text-base">
                    <i class="fas fa-rocket text-lg"></i>
                    <span>Enregistrer la ligne</span>
                </button>
            </div>

        </form>
        @endif

    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const itineraireSelect = document.getElementById('itineraire_id');
    const cardsContainer   = document.getElementById('cards-container');
    const summarySection   = document.getElementById('summary-section');
    const submitSection    = document.getElementById('submit-section');
    const montantBillet    = document.getElementById('montant_billet');
    const capacityInput    = document.getElementById('capacity');

    let currentDurationMinutes = 90;

    const tsSettings = { create: false, placeholder: '-- Choisir --', allowEmptyOption: true };
    new TomSelect('#itineraire_id', tsSettings);
    @if(!$garesArrivee->isEmpty())
    new TomSelect('#gare_arrivee_id', tsSettings);
    @endif

    function parseDurationToMinutes(str) {
        if (!str) return 90;
        let h = 0, m = 0;
        const hm = str.match(/(\d+)\s*heure/i); if (hm) h = parseInt(hm[1]);
        const mm = str.match(/(\d+)\s*min/i);   if (mm) m = parseInt(mm[1]);
        return (h * 60 + m) || 90;
    }

    window.calculateArrivalTime = function (departureInput) {
        if (!departureInput?.value) return;
        const [h, m] = departureInput.value.split(':').map(Number);
        if (isNaN(h) || isNaN(m)) return;
        const total        = h * 60 + m + currentDurationMinutes;
        const scheduleItem = departureInput.closest('.schedule-item');
        if (scheduleItem) {
            const arrivalInput = scheduleItem.querySelector('.time-input.arrival');
            if (arrivalInput)
                arrivalInput.value = `${String(Math.floor(total/60)%24).padStart(2,'0')}:${String(total%60).padStart(2,'0')}`;
        }
    };

    function updateSummary() {
        const count = document.querySelectorAll('#aller-schedules .schedule-item').length;
        document.getElementById('summary-aller').textContent    = count;
        document.getElementById('summary-price').textContent    = montantBillet?.value || 0;
        document.getElementById('summary-capacity').textContent = capacityInput?.value || 64;
    }

    itineraireSelect.addEventListener('change', function () {
        const val    = this.value;
        const option = this.options[this.selectedIndex];
        if (val) {
            cardsContainer.style.display = 'block';
            summarySection.style.display = 'block';
            submitSection.style.display  = 'block';
            document.getElementById('aller-route').textContent = `${option.dataset.depart} → ${option.dataset.arrive}`;
            currentDurationMinutes = parseDurationToMinutes(option.dataset.duree);
            document.querySelectorAll('.departure-time').forEach(i => calculateArrivalTime(i));
            updateSummary();
        } else {
            cardsContainer.style.display = 'none';
            summarySection.style.display = 'none';
            submitSection.style.display  = 'none';
        }
    });

    window.addSchedule = function (initialTime = null, initialArrivalTime = null) {
        const container = document.getElementById('aller-schedules');
        const items     = container.querySelectorAll('.schedule-item');
        const newIndex  = items.length;
        let nextTime    = '06:00';
        if (initialTime) {
            nextTime = initialTime;
        } else if (items.length > 0) {
            const lastInput = items[items.length - 1].querySelector('.departure-time');
            if (lastInput?.value) {
                const [h, m] = lastInput.value.split(':').map(Number);
                nextTime = `${String((h+1)%24).padStart(2,'0')}:${String(m).padStart(2,'0')}`;
            }
        }
        container.insertAdjacentHTML('beforeend', `
            <div class="schedule-item aller" data-index="${newIndex}">
                <span class="badge-horaire">Horaire ${newIndex + 1}</span>
                ${newIndex > 0 ? `<button type="button" class="remove-schedule" onclick="window.removeSchedule(${newIndex})"><i class="fas fa-times"></i></button>` : ''}
                <div class="schedule-times">
                    <div class="time-input-group">
                        <label>Heure de départ</label>
                        <input type="time" name="aller_horaires[${newIndex}][heure_depart]"
                            class="time-input departure-time" value="${nextTime}" required
                            data-index="${newIndex}">
                    </div>
                    <div class="time-arrow"><i class="fas fa-long-arrow-alt-right"></i></div>
                    <div class="time-input-group">
                        <label>Arrivée estimée</label>
                        <input type="time" name="aller_horaires[${newIndex}][heure_arrive]"
                            class="time-input arrival" value="${initialArrivalTime || ''}" readonly>
                    </div>
                </div>
            </div>`);
        const newRow = container.querySelector(`.schedule-item[data-index="${newIndex}"]`);
        calculateArrivalTime(newRow.querySelector('.departure-time'));
        updateSummary();
    };

    window.removeSchedule = function (index) {
        const container = document.getElementById('aller-schedules');
        if (container.querySelectorAll('.schedule-item').length <= 1) {
            alert('Vous devez conserver au moins un horaire.'); return;
        }
        container.querySelector(`.schedule-item[data-index="${index}"]`)?.remove();
        reindex();
        updateSummary();
    };

    function reindex() {
        document.querySelectorAll('#aller-schedules .schedule-item').forEach((item, idx) => {
            item.dataset.index = idx;
            item.querySelector('.badge-horaire').textContent = `Horaire ${idx + 1}`;
            item.querySelector('input[name*="heure_depart"]').name = `aller_horaires[${idx}][heure_depart]`;
            item.querySelector('input[name*="heure_arrive"]').name = `aller_horaires[${idx}][heure_arrive]`;
            const btn = item.querySelector('.remove-schedule');
            if (btn) btn.setAttribute('onclick', `window.removeSchedule(${idx})`);
        });
    }

    document.addEventListener('input', e => {
        if (e.target.classList.contains('departure-time')) calculateArrivalTime(e.target);
        if (e.target === montantBillet || e.target === capacityInput) updateSummary();
    });

    // ── Auto-init si itinéraire pré-sélectionné ──
    const existingAller = @json($existingAller ?? []);
    const initialId     = "{{ old('itineraire_id', $preselectedItineraireId ?? '') }}";

    if (initialId) {
        document.getElementById('itineraire_id').value = initialId;
        document.getElementById('itineraire_id').dispatchEvent(new Event('change'));
        setTimeout(() => {
            const c = document.getElementById('aller-schedules');
            if (existingAller.length > 0) {
                c.innerHTML = '';
                existingAller.forEach(h => window.addSchedule(h.heure_depart, h.heure_arrive));
            } else if (!c.children.length) window.addSchedule();
        }, 300);
    } else {
        window.addSchedule();
    }
});
</script>
@endsection
