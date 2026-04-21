@extends('sapeur_pompier.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 pb-12">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6 pt-2">
        <a href="{{ route('sapeur-pompier.signalement.show', $signalement->id) }}"
           class="flex items-center gap-2 text-gray-500 hover:text-gray-800 font-medium text-sm transition-colors">
            <i class="fas fa-arrow-left"></i> Retour au signalement
        </a>
        <div class="text-center">
            <h1 class="text-2xl font-black text-gray-900">Bilan de l'intervention</h1>
            <p class="text-sm text-gray-400 mt-0.5">
                Signalement #{{ $signalement->id }}
                @if($signalement->programme)
                    &mdash; {{ $signalement->programme->point_depart }} → {{ $signalement->programme->point_arrive }}
                @endif
            </p>
        </div>
        <div class="w-40"></div>
    </div>

    {{-- Variables calculées (avant les compteurs) --}}
    @php
        $allPassengers  = $reservations->count() > 0 ? $reservations : $convoisPassagers;
        $totalPassengers = $allPassengers->count();
        $isConvoiMode   = $reservations->isEmpty() && $convoisPassagers->count() > 0;
    @endphp

    {{-- Bilan chiffré (4 compteurs auto) --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-skull-crossbones text-red-600 text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Morts</p>
                <p class="text-3xl font-black text-red-600" id="cnt-mort">0</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-injured text-orange-500 text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Blessés</p>
                <p class="text-3xl font-black text-orange-500" id="cnt-blesse">0</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-ambulance text-blue-600 text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Évacués</p>
                <p class="text-3xl font-black text-blue-600" id="cnt-evacue">0</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-heart text-green-600 text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Indemnes</p>
                <p class="text-3xl font-black text-green-600" id="cnt-indemne">{{ $totalPassengers }}</p>
            </div>
        </div>
    </div>

    {{-- Formulaire principal --}}
    <form id="bilan-form"
          action="{{ route('sapeur-pompier.signalement.mark-as-treated', $signalement->id) }}"
          method="POST">
        @csrf
        @method('PATCH')

        {{-- Inputs cachés générés dynamiquement --}}
        <input type="hidden" name="nombre_morts"        id="input-morts"   value="0">
        <input type="hidden" name="nombre_blesses"      id="input-blesses" value="0">
        <input type="hidden" name="details_intervention" id="input-rapport" value="">
        <div id="dynamic-passenger-inputs"></div>

        {{-- Corps principal : liste gauche + panneau droit --}}
        <div class="flex gap-6 mb-6" style="min-height: 480px;">

            {{-- ─── Panneau Gauche : liste des passagers ─── --}}
            <div class="w-72 flex-shrink-0 bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col overflow-hidden">
                <div class="bg-gray-800 text-white px-5 py-4 flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center gap-2 font-bold text-sm">
                        <i class="fas fa-users"></i>
                        Passagers <span class="ml-1 bg-gray-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $totalPassengers }}</span>
                        @if($isConvoiMode)
                            <span class="ml-1 text-[10px] font-bold bg-blue-600 px-2 py-0.5 rounded-full">CONVOI</span>
                        @endif
                    </div>
                    <div class="text-xs text-gray-400">Cliquer pour sélectionner</div>
                </div>

                @if($totalPassengers === 0)
                    <div class="flex-1 flex items-center justify-center p-6 text-center text-gray-400">
                        <div>
                            <i class="fas fa-user-slash text-3xl mb-3"></i>
                            @if($signalement->convoi_id && $signalement->convoi && $signalement->convoi->is_garant)
                                <p class="text-sm">Le client a déclaré se porter garant pour tous les passagers — aucun passager individuel enregistré</p>
                            @else
                                <p class="text-sm">Aucun passager trouvé pour ce {{ $signalement->convoi_id ? 'convoi' : 'voyage' }}</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="overflow-y-auto flex-1">

                        {{-- ── Passagers de VOYAGE (reservations) ── --}}
                        @foreach($reservations as $res)
                            @php
                                $name = trim(($res->passager_nom ?? '') . ' ' . ($res->passager_prenom ?? ''));
                                if (!$name) $name = $res->user->name ?? 'Inconnu';
                                $seat = $res->seat_number ?? '?';
                                $photoPath = ($res->user && $res->user->photo_profile_path) ? $res->user->photo_profile_path : null;
                                $photo = $photoPath ? asset('storage/' . $photoPath) : '';
                                $urgenceNom = $res->nom_passager_urgence ?: ($res->user->nom_urgence ?? '');
                                $urgenceContact = $res->passager_urgence ?: ($res->user->contact_urgence ?? '');
                            @endphp
                            <div class="passenger-item border-b border-gray-50 px-4 py-3 cursor-pointer hover:bg-gray-50 transition-colors flex items-center gap-3"
                                 data-res-id="{{ $res->id }}"
                                 data-name="{{ $name }}"
                                 data-seat="{{ $seat }}"
                                 data-photo="{{ $photo }}"
                                 data-urgence-nom="{{ $urgenceNom }}"
                                 data-urgence-contact="{{ $urgenceContact }}"
                                 data-statut="indemne"
                                 onclick="selectPassenger(this)">
                                <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0 text-indigo-700 font-black text-sm overflow-hidden border border-indigo-200 shadow-sm">
                                    @if($photo)
                                        <img src="{{ $photo }}" alt="{{ $name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ $seat }}
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-gray-800 text-sm truncate">{{ $name }}</p>
                                    <p class="text-xs text-gray-400">Place n°{{ $seat }}</p>
                                </div>
                                <span class="status-badge text-[10px] font-black px-2 py-0.5 rounded-full bg-green-100 text-green-700 flex-shrink-0">
                                    Indemne
                                </span>
                            </div>
                        @endforeach

                        {{-- ── Passagers de CONVOI (manuellement ajoutés, non-garant) ── --}}
                        @foreach($convoisPassagers as $index => $cp)
                            @php
                                $name = trim(($cp->prenoms ?? '') . ' ' . ($cp->nom ?? '')) ?: 'Passager '.($index+1);
                                $seat = $index + 1; // numéro d'ordre
                                $urgenceContact = $cp->contact_urgence ?? '';
                            @endphp
                            <div class="passenger-item border-b border-gray-50 px-4 py-3 cursor-pointer hover:bg-gray-50 transition-colors flex items-center gap-3"
                                 data-res-id="{{ $cp->id }}"
                                 data-name="{{ $name }}"
                                 data-seat="{{ $seat }}"
                                 data-photo=""
                                 data-urgence-nom=""
                                 data-urgence-contact="{{ $urgenceContact }}"
                                 data-statut="indemne"
                                 onclick="selectPassenger(this)">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0 text-blue-700 font-black text-sm border border-blue-200 shadow-sm">
                                    {{ $seat }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-gray-800 text-sm truncate">{{ $name }}</p>
                                    @if($cp->contact)
                                        <p class="text-xs text-gray-400"><i class="fas fa-phone mr-1"></i>{{ $cp->contact }}</p>
                                    @else
                                        <p class="text-xs text-gray-400">Passager n°{{ $seat }}</p>
                                    @endif
                                </div>
                                <span class="status-badge text-[10px] font-black px-2 py-0.5 rounded-full bg-green-100 text-green-700 flex-shrink-0">
                                    Indemne
                                </span>
                            </div>
                        @endforeach

                    </div>
                @endif
            </div>

            {{-- ─── Panneau Droit : sélection du statut ─── --}}
            <div class="flex-1 bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col overflow-hidden">

                {{-- État : rien sélectionné --}}
                <div id="panel-empty" class="flex-1 flex flex-col items-center justify-center text-center p-8 text-gray-300">
                    <i class="fas fa-hand-pointer text-6xl mb-4"></i>
                    <p class="text-xl font-bold text-gray-400">Sélectionnez un passager</p>
                    <p class="text-sm text-gray-300 mt-2">Cliquez sur un passager dans la liste pour définir son état</p>
                </div>

                {{-- État : passager sélectionné --}}
                <div id="panel-passenger" class="hidden flex-1 flex flex-col p-6">
                    {{-- Info passager sélectionné --}}
                    <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                        <div id="sel-seat-badge" class="w-16 h-16 rounded-2xl bg-indigo-100 flex items-center justify-center text-indigo-700 font-black text-xl flex-shrink-0 overflow-hidden border border-indigo-200 shadow-md"></div>
                        <div>
                            <p class="text-xl font-black text-gray-900" id="sel-name">—</p>
                            <p class="text-sm text-gray-400" id="sel-seat-label">Place n°—</p>
                        </div>
                        <div class="ml-auto">
                            <span id="sel-status-badge" class="text-xs font-black px-3 py-1.5 rounded-full bg-green-100 text-green-700">Indemne</span>
                        </div>
                    </div>

                    {{-- Informations d'urgence --}}
                    <div id="urgence-section" class="hidden mb-6 p-4 bg-red-50 border border-red-100 rounded-2xl animate-pulse-subtle">
                        <p class="text-xs font-bold text-red-600 uppercase tracking-widest mb-2 flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle"></i> Informations d'urgence
                        </p>
                        <div class="grid grid-cols-1 gap-1">
                            <div id="urgence-nom-row" class="flex items-center gap-2 text-sm font-black text-gray-900">
                                <span class="text-red-400 text-xs font-medium uppercase tracking-tighter">Nom:</span>
                                <span id="sel-urgence-nom">—</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm font-bold text-gray-600">
                                <span class="text-red-400 text-xs font-medium uppercase tracking-tighter">Tel urgence:</span>
                                <span id="sel-urgence-contact">—</span>
                            </div>
                        </div>
                    </div>

                    {{-- 4 boutons de statut --}}
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Définir l'état du passager</p>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <button type="button" onclick="setStatus('indemne')"
                            id="btn-indemne"
                            class="status-btn flex flex-col items-center justify-center gap-2 p-5 rounded-2xl border-2 border-green-200 bg-green-50 text-green-700 hover:bg-green-100 transition-all font-bold active-indemne">
                            <i class="fas fa-heart text-2xl"></i>
                            <span class="text-sm">Indemne</span>
                        </button>
                        <button type="button" onclick="setStatus('evacue')"
                            id="btn-evacue"
                            class="status-btn flex flex-col items-center justify-center gap-2 p-5 rounded-2xl border-2 border-gray-200 bg-gray-50 text-gray-400 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 transition-all font-bold">
                            <i class="fas fa-ambulance text-2xl"></i>
                            <span class="text-sm">Évacué</span>
                        </button>
                        <button type="button" onclick="setStatus('blesse')"
                            id="btn-blesse"
                            class="status-btn flex flex-col items-center justify-center gap-2 p-5 rounded-2xl border-2 border-gray-200 bg-gray-50 text-gray-400 hover:bg-orange-50 hover:border-orange-200 hover:text-orange-600 transition-all font-bold">
                            <i class="fas fa-user-injured text-2xl"></i>
                            <span class="text-sm">Blessé</span>
                        </button>
                        <button type="button" onclick="setStatus('mort')"
                            id="btn-mort"
                            class="status-btn flex flex-col items-center justify-center gap-2 p-5 rounded-2xl border-2 border-gray-200 bg-gray-50 text-gray-400 hover:bg-red-50 hover:border-red-300 hover:text-red-700 transition-all font-bold">
                            <i class="fas fa-skull-crossbones text-2xl"></i>
                            <span class="text-sm">Décédé</span>
                        </button>
                    </div>

                    {{-- Champs hôpital (Évacué seulement) --}}
                    <div id="hopital-section" class="hidden bg-blue-50 border border-blue-200 rounded-2xl p-4">
                        <p class="text-xs font-bold text-blue-700 uppercase tracking-widest mb-3">
                            <i class="fas fa-hospital mr-1"></i> Informations d'évacuation
                        </p>
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Hôpital d'évacuation *</label>
                                <input type="text" id="hopital-nom" placeholder="Ex: CHU de Cocody"
                                    class="w-full px-4 py-2.5 border border-blue-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-300 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Adresse / Localisation</label>
                                <input type="text" id="hopital-adresse" placeholder="Ex: Cocody, Abidjan"
                                    class="w-full px-4 py-2.5 border border-blue-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-300 bg-white">
                            </div>
                        </div>
                    </div>

                    <p class="text-xs text-gray-300 mt-auto pt-4">
                        <i class="fas fa-info-circle mr-1"></i>
                        Par défaut, chaque passager est marqué <strong>Indemne</strong>.
                    </p>
                </div>
            </div>
        </div>

        {{-- Rapport d'intervention --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">
                <i class="fas fa-pen text-gray-300 mr-1"></i> Rapport d'intervention
            </label>
            <textarea id="rapport-textarea" rows="4" placeholder="Décrivez les circonstances de l'accident, les actions menées, les difficultés rencontrées..."
                class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300 resize-none bg-gray-50"></textarea>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('sapeur-pompier.signalement.show', $signalement->id) }}"
               class="flex items-center gap-2 px-6 py-3 border border-gray-300 rounded-xl font-bold text-gray-600 hover:bg-gray-50 transition-colors text-sm">
                <i class="fas fa-times"></i> Annuler
            </a>
            <button type="button" onclick="submitBilan()"
                class="flex items-center gap-2 px-8 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-green-200 transition-all">
                <i class="fas fa-check-circle"></i> Valider et Clôturer
            </button>
        </div>
    </form>
</div>

<style>
    .status-btn.is-active-indemne { border-color: #16a34a !important; background: #dcfce7 !important; color: #15803d !important; }
    .status-btn.is-active-evacue  { border-color: #2563eb !important; background: #dbeafe !important; color: #1d4ed8 !important; }
    .status-btn.is-active-blesse  { border-color: #ea580c !important; background: #ffedd5 !important; color: #c2410c !important; }
    .status-btn.is-active-mort    { border-color: #dc2626 !important; background: #fee2e2 !important; color: #b91c1c !important; }

    .passenger-item.is-selected   { background: #eff6ff !important; border-left: 3px solid #2563eb; }
    .passenger-item:hover.is-selected { background: #eff6ff !important; }

    @keyframes pulse-subtle {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.85; }
    }
    .animate-pulse-subtle {
        animation: pulse-subtle 2s infinite ease-in-out;
    }
</style>

<script>
    // État de tous les passagers : resId → { statut, hopital_nom, hopital_adresse }
    const passengerStates = {};

    // Initialiser tous les passagers à "indemne"
    document.querySelectorAll('.passenger-item').forEach(item => {
        const resId = item.dataset.resId;
        passengerStates[resId] = { statut: 'indemne', hopital_nom: '', hopital_adresse: '' };
    });

    let currentResId = null;

    // ─── Sélectionner un passager ───────────────────────────────
    function selectPassenger(elem) {
        // Sauvegarder les champs hôpital du passager précédent
        if (currentResId && passengerStates[currentResId]?.statut === 'evacue') {
            passengerStates[currentResId].hopital_nom     = document.getElementById('hopital-nom').value.trim();
            passengerStates[currentResId].hopital_adresse = document.getElementById('hopital-adresse').value.trim();
        }

        // Désélectionner tous
        document.querySelectorAll('.passenger-item').forEach(i => i.classList.remove('is-selected'));
        elem.classList.add('is-selected');

        currentResId = elem.dataset.resId;
        const state  = passengerStates[currentResId];

        // Mettre à jour le panneau droit
        document.getElementById('panel-empty').classList.add('hidden');
        document.getElementById('panel-passenger').classList.remove('hidden');

        document.getElementById('sel-name').textContent        = elem.dataset.name;
        document.getElementById('sel-seat-label').textContent  = 'Place n°' + elem.dataset.seat;
        
        // Photo ou Badge
        const badge = document.getElementById('sel-seat-badge');
        const photo = elem.dataset.photo;
        if (photo && photo !== '') {
            badge.innerHTML = `<img src="${photo}" class="w-full h-full object-cover">`;
        } else {
            badge.innerHTML = elem.dataset.seat;
        }

        // Informations d'urgence
        const urgenceSection = document.getElementById('urgence-section');
        const urgNom = elem.dataset.urgenceNom;
        const urgTel = elem.dataset.urgenceContact;

        if (urgNom || urgTel) {
            urgenceSection.classList.remove('hidden');
            document.getElementById('sel-urgence-nom').textContent = urgNom || 'Non renseigné';
            document.getElementById('sel-urgence-contact').textContent = urgTel || 'Non renseigné';
            // Masquer la ligne "Nom" si vide (passagers convoi)
            const nomRow = document.getElementById('urgence-nom-row');
            if (nomRow) nomRow.style.display = urgNom ? '' : 'none';
        } else {
            urgenceSection.classList.add('hidden');
        }

        // Restaurer les champs hôpital si évacué
        document.getElementById('hopital-nom').value     = state.hopital_nom || '';
        document.getElementById('hopital-adresse').value = state.hopital_adresse || '';

        // Afficher le bon bouton actif
        applyActiveButton(state.statut);
    }

    // ─── Définir le statut du passager sélectionné ──────────────
    function setStatus(statut) {
        if (!currentResId) return;

        // Sauvegarder les champs hôpital si applicable
        if (passengerStates[currentResId].statut === 'evacue') {
            passengerStates[currentResId].hopital_nom     = document.getElementById('hopital-nom').value.trim();
            passengerStates[currentResId].hopital_adresse = document.getElementById('hopital-adresse').value.trim();
        }

        passengerStates[currentResId].statut = statut;
        if (statut !== 'evacue') {
            passengerStates[currentResId].hopital_nom     = '';
            passengerStates[currentResId].hopital_adresse = '';
            document.getElementById('hopital-nom').value     = '';
            document.getElementById('hopital-adresse').value = '';
        }

        applyActiveButton(statut);
        updateBadge(currentResId, statut);
        updateCounters();
    }

    // ─── Mettre à jour les boutons visuellement ─────────────────
    function applyActiveButton(statut) {
        const btns = { indemne: 'btn-indemne', evacue: 'btn-evacue', blesse: 'btn-blesse', mort: 'btn-mort' };
        Object.keys(btns).forEach(s => {
            const btn = document.getElementById(btns[s]);
            btn.classList.remove('is-active-indemne','is-active-evacue','is-active-blesse','is-active-mort');
        });
        const active = document.getElementById(btns[statut]);
        if (active) active.classList.add(`is-active-${statut}`);

        // Champs hôpital
        const hopSection = document.getElementById('hopital-section');
        hopSection.classList.toggle('hidden', statut !== 'evacue');

        // Badge sélectionné
        const labels  = { indemne: 'Indemne', evacue: 'Évacué', blesse: 'Blessé', mort: 'Décédé' };
        const colors  = {
            indemne: 'bg-green-100 text-green-700',
            evacue:  'bg-blue-100 text-blue-700',
            blesse:  'bg-orange-100 text-orange-600',
            mort:    'bg-red-100 text-red-700',
        };
        const badge = document.getElementById('sel-status-badge');
        badge.textContent  = labels[statut];
        badge.className    = `text-xs font-black px-3 py-1.5 rounded-full ${colors[statut]}`;
    }

    // ─── Badge dans la liste gauche ──────────────────────────────
    function updateBadge(resId, statut) {
        const item   = document.querySelector(`.passenger-item[data-res-id="${resId}"]`);
        if (!item) return;
        const badge  = item.querySelector('.status-badge');
        const labels = { indemne: 'Indemne', evacue: 'Évacué', blesse: 'Blessé', mort: 'Décédé' };
        const colors = {
            indemne: 'bg-green-100 text-green-700',
            evacue:  'bg-blue-100 text-blue-700',
            blesse:  'bg-orange-100 text-orange-600',
            mort:    'bg-red-100 text-red-700',
        };
        badge.textContent = labels[statut];
        badge.className   = `status-badge text-[10px] font-black px-2 py-0.5 rounded-full flex-shrink-0 ${colors[statut]}`;
    }

    // ─── Mettre à jour les 4 compteurs en haut ──────────────────
    function updateCounters() {
        let counts = { indemne: 0, evacue: 0, blesse: 0, mort: 0 };
        Object.values(passengerStates).forEach(s => { if (counts[s.statut] !== undefined) counts[s.statut]++; });
        document.getElementById('cnt-indemne').textContent = counts.indemne;
        document.getElementById('cnt-evacue').textContent  = counts.evacue;
        document.getElementById('cnt-blesse').textContent  = counts.blesse;
        document.getElementById('cnt-mort').textContent    = counts.mort;
    }

    // ─── Soumission du formulaire ────────────────────────────────
    function submitBilan() {
        // Sauvegarder les champs hôpital du passager actif
        if (currentResId && passengerStates[currentResId]?.statut === 'evacue') {
            passengerStates[currentResId].hopital_nom     = document.getElementById('hopital-nom').value.trim();
            passengerStates[currentResId].hopital_adresse = document.getElementById('hopital-adresse').value.trim();
        }

        // Vérifier que les évacués ont un hôpital
        const manquants = Object.entries(passengerStates)
            .filter(([, s]) => s.statut === 'evacue' && !s.hopital_nom);

        if (manquants.length > 0) {
            const noms = manquants.map(([id]) => {
                const el = document.querySelector(`.passenger-item[data-res-id="${id}"]`);
                return el ? el.dataset.name : 'Passager inconnu';
            }).join(', ');
            alert(`Veuillez préciser l'hôpital pour les passagers évacués : ${noms}`);
            return;
        }

        // Mettre à jour les inputs cachés
        const counts = { indemne: 0, evacue: 0, blesse: 0, mort: 0 };
        Object.values(passengerStates).forEach(s => { if (counts[s.statut] !== undefined) counts[s.statut]++; });

        document.getElementById('input-morts').value   = counts.mort;
        document.getElementById('input-blesses').value = counts.blesse;
        document.getElementById('input-rapport').value = document.getElementById('rapport-textarea').value;

        // Construire les inputs cachés pour chaque passager
        const container = document.getElementById('dynamic-passenger-inputs');
        container.innerHTML = '';
        Object.entries(passengerStates).forEach(([resId, state]) => {
            const addInput = (key, val) => {
                const inp = document.createElement('input');
                inp.type  = 'hidden';
                inp.name  = `passagers[${resId}][${key}]`;
                inp.value = val || '';
                container.appendChild(inp);
            };
            addInput('statut', state.statut);
            if (state.statut === 'evacue') {
                addInput('hopital_nom',     state.hopital_nom);
                addInput('hopital_adresse', state.hopital_adresse);
            }
        });

        document.getElementById('bilan-form').submit();
    }

    // ─── Recherche automatique de l'adresse de l'hôpital ────────
    let searchTimeout = null;
    document.getElementById('hopital-nom').addEventListener('input', function(e) {
        const query = e.target.value.trim();
        const addressInput = document.getElementById('hopital-adresse');
        
        if (query.length < 3) return;
        
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const originalPlaceholder = addressInput.placeholder;
            addressInput.placeholder = "Recherche de l'adresse...";
            
            // Recherche via Nominatim (limitée à la Côte d'Ivoire pour plus de précision)
            fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&limit=1&countrycodes=ci`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        addressInput.value = data[0].display_name;
                        // Synchroniser avec l'état du passager courant
                        if (currentResId) {
                            passengerStates[currentResId].hopital_adresse = data[0].display_name;
                        }
                    }
                    addressInput.placeholder = originalPlaceholder;
                })
                .catch(err => {
                    console.error("Erreur Nominatim:", err);
                    addressInput.placeholder = originalPlaceholder;
                });
        }, 800);
    });
</script>
@endsection
