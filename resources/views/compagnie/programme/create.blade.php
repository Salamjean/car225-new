@extends('compagnie.layouts.template')

@section('page-title', 'Créer une ligne de transport')
@section('page-subtitle', 'Configurez les horaires Aller et Retour en un seul formulaire')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    /* ═══════════════════════════════════════════
       ADAPTATION AU DESIGN SYSTEM
    ═══════════════════════════════════════════ */
    .form-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    /* Tom Select - Adaptation aux couleurs du template */
    .ts-control {
        border-radius: var(--radius-sm) !important;
        padding: 0.75rem 1rem !important;
        background-color: var(--surface-2) !important;
        border: 1px solid var(--border) !important;
        font-size: 13px !important;
        color: var(--text-1) !important;
        transition: all 0.2s ease !important;
        box-shadow: none !important;
    }
    .ts-wrapper.focus .ts-control {
        border-color: var(--orange) !important;
        background-color: var(--surface) !important;
        box-shadow: 0 0 0 3px var(--orange-light) !important;
    }
    .ts-dropdown {
        border-radius: var(--radius-sm) !important;
        border: 1px solid var(--border-strong) !important;
        box-shadow: var(--shadow-md) !important;
        font-size: 13px !important;
    }
    .ts-dropdown .active {
        background-color: var(--orange-light) !important;
        color: var(--orange-dark) !important;
    }

    /* Cartes principales */
    .form-card {
        background: var(--surface);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
    }

    .section-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge-blue { background: #EFF6FF; color: #1D4ED8; }
    .badge-purple { background: #F3E8FF; color: #7E22CE; }
    .badge-amber { background: #FFFBEB; color: #D97706; }

    /* Inputs standards */
    .input-modern {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 13px;
        color: var(--text-1);
        background: var(--surface-2);
        transition: all 0.2s ease;
    }
    .input-modern:focus {
        outline: none;
        border-color: var(--orange);
        background: var(--surface);
        box-shadow: 0 0 0 3px var(--orange-light);
    }
    select.input-modern { display: none !important; }

    /* Bouton d'action principal */
    .btn-submit {
        background: linear-gradient(135deg, var(--orange) 0%, var(--orange-dark) 100%);
        color: white;
        padding: 14px 30px;
        border-radius: var(--radius-sm);
        font-weight: 700;
        font-size: 14px;
        border: none;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(249, 115, 22, 0.4);
        color: white;
        text-decoration: none;
    }

    /* Cartes Aller/Retour */
    .dual-card-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-top: 20px;
    }
    @media (max-width: 768px) {
        .dual-card-container { grid-template-columns: 1fr; }
    }
    .dual-card-container.single {
        grid-template-columns: 1fr;
        max-width: 600px;
        margin-left: auto; margin-right: auto;
    }

    .programme-card {
        background: var(--surface);
        border-radius: var(--radius);
        overflow: hidden;
        border: 1px solid var(--border);
        transition: box-shadow 0.3s, transform 0.3s;
    }
    .programme-card:hover { box-shadow: var(--shadow-md); transform: translateY(-3px); }
    
    .programme-card.aller { border-top: 4px solid var(--emerald); }
    .programme-card.retour { border-top: 4px solid var(--blue); }

    .card-header-custom {
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        border-bottom: 1px solid var(--border);
    }
    .card-header-custom.aller { background: #ECFDF5; }
    .card-header-custom.retour { background: #EFF6FF; }

    .card-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
    }
    .card-header-custom.aller .card-icon { background: var(--emerald); color: white; }
    .card-header-custom.retour .card-icon { background: var(--blue); color: white; }

    .card-title-custom h3 {
        font-size: 14px; font-weight: 800; color: var(--text-1); margin: 0;
    }
    .card-title-custom .route {
        font-size: 11px; font-weight: 600; color: var(--text-2); margin-top: 2px;
    }

    .card-body-custom { padding: 20px; }

    /* Horaires Items */
    .schedule-container {
        display: flex; flex-direction: column; gap: 14px;
    }
    .schedule-item {
        background: var(--surface);
        border: 1px solid var(--border-strong);
        border-radius: var(--radius-sm);
        padding: 16px;
        position: relative;
        transition: border-color 0.2s;
    }
    .schedule-item:hover { border-color: var(--text-3); }

    .schedule-item .badge-horaire {
        position: absolute;
        top: -10px; left: 14px;
        color: white;
        font-size: 10px; font-weight: 800;
        padding: 2px 8px; border-radius: 6px;
        text-transform: uppercase;
    }
    .schedule-item.aller .badge-horaire { background: var(--emerald); }
    .schedule-item.retour .badge-horaire { background: var(--blue); }

    .schedule-times { display: flex; align-items: center; gap: 14px; margin-top: 6px; }
    .time-input-group { flex: 1; }
    .time-input-group label {
        display: block; font-size: 10px; font-weight: 700;
        color: var(--text-3); text-transform: uppercase; margin-bottom: 4px;
    }
    .time-input {
        width: 100%; padding: 8px 12px;
        border: 1px solid var(--border-strong); border-radius: 8px;
        font-size: 14px; font-weight: 700; text-align: center;
        background: var(--surface-2); color: var(--text-1);
        transition: all 0.2s ease;
    }
    .time-input:focus {
        outline: none; border-color: var(--orange);
        background: var(--surface); box-shadow: 0 0 0 3px var(--orange-light);
    }
    .time-input.arrival {
        background: #F1F5F9; color: var(--text-3); cursor: not-allowed; border-style: dashed;
    }
    .time-arrow { color: var(--text-3); margin-top: 14px; font-size: 12px; }

    .remove-schedule {
        position: absolute; top: -10px; right: -10px;
        width: 24px; height: 24px; border-radius: 50%;
        background: var(--surface); border: 1px solid var(--red);
        color: var(--red); cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: 10px; transition: all 0.2s; z-index: 10;
    }
    .remove-schedule:hover { background: var(--red); color: white; transform: scale(1.1); }

    /* Boutons d'ajout */
    .add-schedule-btn {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        width: 100%; padding: 12px; margin-top: 16px;
        border: 1px dashed var(--border-strong); border-radius: var(--radius-sm);
        background: var(--surface-2); color: var(--text-2);
        font-weight: 700; font-size: 12px; cursor: pointer; transition: all 0.2s;
    }
    .add-schedule-btn.aller:hover { border-color: var(--emerald); color: var(--emerald); background: #ECFDF5; }
    .add-schedule-btn.retour:hover { border-color: var(--blue); color: var(--blue); background: #EFF6FF; }

    /* Toggle Switch */
    .switch-wrapper {
        display: flex; align-items: center; justify-content: space-between;
        background: var(--surface-2); padding: 16px 20px;
        border-radius: var(--radius-sm); border: 1px solid var(--border);
        margin-bottom: 24px;
    }
    .switch-info { display: flex; align-items: center; gap: 14px; }
    .switch-icon {
        width: 40px; height: 40px; border-radius: 10px;
        background: #EFF6FF; color: var(--blue);
        display: flex; align-items: center; justify-content: center; font-size: 16px;
    }
    .switch-text h4 { font-size: 13px; font-weight: 800; color: var(--text-1); margin: 0; }
    .switch-text p { font-size: 11px; color: var(--text-3); margin: 0; }

    .toggle-switch { position: relative; display: inline-block; width: 50px; height: 26px; margin: 0; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
        position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
        background-color: var(--border-strong); transition: .4s; border-radius: 34px;
    }
    .toggle-slider:before {
        position: absolute; content: ""; height: 18px; width: 18px;
        left: 4px; bottom: 4px; background-color: white; transition: .4s;
        border-radius: 50%; box-shadow: var(--shadow-sm);
    }
    input:checked + .toggle-slider { background-color: var(--blue); }
    input:checked + .toggle-slider:before { transform: translateX(24px); }

    /* Section Tarif */
    .tarif-section {
        background: var(--orange-light);
        border: 1px solid var(--orange-mid);
        border-radius: var(--radius);
        padding: 24px;
        margin-top: 24px;
    }
    .tarif-input-wrapper { position: relative; max-width: 260px; margin: 0 auto; }
    .tarif-input-wrapper .icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); font-size: 18px; }
    .tarif-input-wrapper .currency { position: absolute; right: 16px; top: 50%; transform: translateY(-50%); font-weight: 700; color: var(--orange-dark); font-size: 12px; }
    .tarif-input {
        width: 100%; padding: 14px 60px 14px 44px;
        border: 1px solid var(--orange-mid); border-radius: var(--radius-sm);
        font-size: 18px; font-weight: 800; text-align: center; color: var(--text-1);
        transition: all 0.3s ease;
    }
    .tarif-input:focus { outline: none; border-color: var(--orange); box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.15); }

    /* Summary Grid */
    .summary-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px;
    }
    .feature-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--radius-sm); padding: 16px; text-align: center;
    }
    .feature-card .icon { font-size: 24px; margin-bottom: 8px; }
    .feature-card .value { font-size: 20px; font-weight: 800; color: var(--orange); margin-bottom: 2px; }
    .feature-card .label { font-size: 10px; font-weight: 700; color: var(--text-3); text-transform: uppercase; }

    #retour-section-container { transition: all 0.3s ease; }
    #retour-section-container.collapsed { opacity: 0; transform: scale(0.95); pointer-events: none; display: none; }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <div class="form-container">
        
        <a href="{{ route('programme.index') }}" class="btn btn-link px-0 mb-4" style="color: var(--text-3); font-size: 13px; font-weight: 600; text-decoration: none;">
            <i class="fas fa-arrow-left mr-1"></i> Retour à la liste des lignes
        </a>

        @if(session('error'))
            <div class="alert alert-danger shadow-sm border-0" style="border-radius: var(--radius-sm); font-size: 13px;">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger shadow-sm border-0" style="border-radius: var(--radius-sm); font-size: 13px;">
                <ul class="mb-0 pl-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('programme.store') }}" method="POST" id="programmeForm">
            @csrf

            <div class="form-card">
                <div class="d-flex align-items-center mb-4" style="gap: 12px;">
                    <span class="section-badge badge-blue">
                        <i class="fas fa-map-marked-alt"></i> Étape 1
                    </span>
                    <h2 style="font-size: 16px; font-weight: 800; color: var(--text-1); margin: 0;">Choisir l'itinéraire et les gares</h2>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-2); margin-bottom: 6px;">Itinéraire</label>
                        <select name="itineraire_id" id="itineraire_id" class="input-modern" required>
                            <option value="">-- Sélectionner un itinéraire --</option>
                            @foreach($itineraires as $itineraire)
                                <option value="{{ $itineraire->id }}" 
                                        data-depart="{{ $itineraire->point_depart }}"
                                        data-arrive="{{ $itineraire->point_arrive }}"
                                        data-duree="{{ $itineraire->durer_parcours }}"
                                        {{ old('itineraire_id', $preselectedItineraireId ?? '') == $itineraire->id ? 'selected' : '' }}>
                                    {{ $itineraire->point_depart }} → {{ $itineraire->point_arrive }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-2); margin-bottom: 6px;">Gare de départ</label>
                        <select name="gare_depart_id" id="gare_depart_id" class="input-modern" required>
                            <option value="">-- Sélectionner --</option>
                            @foreach($gares as $gare)
                                <option value="{{ $gare->id }}">{{ $gare->nom_gare }} ({{ $gare->ville }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-2); margin-bottom: 6px;">Gare d'arrivée</label>
                        <select name="gare_arrivee_id" id="gare_arrivee_id" class="input-modern" required>
                            <option value="">-- Sélectionner --</option>
                            @foreach($gares as $gare)
                                <option value="{{ $gare->id }}">{{ $gare->nom_gare }} ({{ $gare->ville }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div id="cards-container" class="form-card" style="display: none;">
                <div class="d-flex align-items-center mb-4" style="gap: 12px;">
                    <span class="section-badge badge-purple">
                        <i class="fas fa-clock"></i> Étape 2
                    </span>
                    <h2 style="font-size: 16px; font-weight: 800; color: var(--text-1); margin: 0;">Configurer les horaires</h2>
                </div>

                <div class="switch-wrapper">
                    <div class="switch-info">
                        <div class="switch-icon"><i class="fas fa-exchange-alt"></i></div>
                        <div class="switch-text">
                            <h4>Créer le trajet retour ?</h4>
                            <p>Activez cette option pour configurer le voyage dans le sens inverse</p>
                        </div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="with_retour" id="toggle-retour">
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="dual-card-container" id="dual-cards">
                    <div class="programme-card aller">
                        <div class="card-header-custom aller">
                            <div class="card-icon"><i class="fas fa-bus-alt"></i></div>
                            <div class="card-title-custom">
                                <h3>Aller</h3>
                                <div class="route" id="aller-route">-- → --</div>
                            </div>
                        </div>
                        <div class="card-body-custom">
                            <div class="schedule-container" id="aller-schedules">
                                </div>
                            <button type="button" class="add-schedule-btn aller" onclick="addSchedule('aller')">
                                <i class="fas fa-plus-circle"></i> Ajouter un horaire
                            </button>
                        </div>
                    </div>

                    <div id="retour-section-container">
                        <div class="programme-card retour">
                            <div class="card-header-custom retour">
                                <div class="card-icon"><i class="fas fa-bus"></i></div>
                                <div class="card-title-custom">
                                    <h3>Retour</h3>
                                    <div class="route" id="retour-route">-- → --</div>
                                </div>
                            </div>
                            <div class="card-body-custom">
                                <div class="schedule-container" id="retour-schedules">
                                    </div>
                                <button type="button" class="add-schedule-btn retour" onclick="addSchedule('retour')">
                                    <i class="fas fa-plus-circle"></i> Ajouter un horaire
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tarif-section">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <div class="text-center mb-3">
                                <span class="section-badge badge-blue">
                                    <i class="fas fa-users"></i> Étape 3
                                </span>
                                <h2 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin: 8px 0 2px;">Capacité du car</h2>
                                <p style="font-size: 11px; color: var(--text-3); margin: 0;">Nombre de places par défaut</p>
                            </div>
                            <div class="tarif-input-wrapper">
                                <span class="icon">💺</span>
                                <input type="number" name="capacity" id="capacity" class="tarif-input" min="1" max="100" value="{{ old('capacity', 64) }}" placeholder="64" required>
                                <span class="currency">Places</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="text-center mb-3">
                                <span class="section-badge badge-amber">
                                    <i class="fas fa-coins"></i> Étape 4
                                </span>
                                <h2 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin: 8px 0 2px;">Tarif du billet</h2>
                                <p style="font-size: 11px; color: var(--text-3); margin: 0;">Prix identique Aller/Retour</p>
                            </div>
                            <div class="tarif-input-wrapper">
                                <span class="icon" style="color: var(--orange);">💰</span>
                                <input type="number" name="montant_billet" id="montant_billet" class="tarif-input" min="0" step="100" value="{{ old('montant_billet', $existingMontantBillet ?? '') }}" placeholder="0" required>
                                <span class="currency">FCFA</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-card" id="summary-section" style="display: none;">
                <h3 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin-bottom: 20px;" class="d-flex align-items-center gap-2">
                    <i class="fas fa-check-circle" style="color: var(--emerald);"></i> Récapitulatif
                </h3>
                
                <div class="summary-grid">
                    <div class="feature-card">
                        <div class="icon">🚌</div>
                        <p class="value" id="summary-aller">1</p>
                        <p class="label">Programme(s) Aller</p>
                    </div>
                    <div class="feature-card" id="summary-retour-card">
                        <div class="icon">🔄</div>
                        <p class="value" id="summary-retour">1</p>
                        <p class="label">Programme(s) Retour</p>
                    </div>
                    <div class="feature-card">
                        <div class="icon">📋</div>
                        <p class="value" id="summary-total">2</p>
                        <p class="label">Total Programmes</p>
                    </div>
                    <div class="feature-card">
                        <div class="icon">💵</div>
                        <p class="value" id="summary-price">0</p>
                        <p class="label">FCFA / Billet</p>
                    </div>
                </div>
            </div>

            <div class="text-center pb-5" id="submit-section" style="display: none;">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-rocket text-lg"></i>
                    <span>Enregistrer et Créer les lignes</span>
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const itineraireSelect = document.getElementById('itineraire_id');
    const cardsContainer = document.getElementById('cards-container');
    const summarySection = document.getElementById('summary-section');
    const submitSection = document.getElementById('submit-section');
    const montantBillet = document.getElementById('montant_billet');
    
    let currentDurationMinutes = 90;

    const toggleRetour = document.getElementById('toggle-retour');
    const retourSection = document.getElementById('retour-section-container');
    const dualCards = document.getElementById('dual-cards');
    const summaryRetourCard = document.getElementById('summary-retour-card');

    // Initialisation Tom Select
    const tsSettings = {
        create: false,
        placeholder: "-- Choisir --",
        maxOptions: 50,
        allowEmptyOption: true
    };

    const tsItineraire = new TomSelect("#itineraire_id", tsSettings);
    const tsGareDepart = new TomSelect("#gare_depart_id", tsSettings);
    const tsGareArrivee = new TomSelect("#gare_arrivee_id", tsSettings);

    function handleRetourToggle() {
        const isWithRetour = toggleRetour.checked;
        if (isWithRetour) {
            retourSection.classList.remove('collapsed');
            dualCards.classList.remove('single');
            summaryRetourCard.style.display = 'block';
            retourSection.querySelectorAll('input, select').forEach(el => el.disabled = false);
        } else {
            retourSection.classList.add('collapsed');
            dualCards.classList.add('single');
            summaryRetourCard.style.display = 'none';
            retourSection.querySelectorAll('input, select').forEach(el => el.disabled = true);
        }
        updateSummary();
    }

    toggleRetour.addEventListener('change', handleRetourToggle);

    function parseDurationToMinutes(durationStr) {
        if (!durationStr) return 90;
        let hours = 0, minutes = 0;
        const hourMatch = durationStr.match(/(\d+)\s*heure/i);
        if (hourMatch) hours = parseInt(hourMatch[1]);
        const minMatch = durationStr.match(/(\d+)\s*min/i);
        if (minMatch) minutes = parseInt(minMatch[1]);
        return (hours * 60) + minutes;
    }

    window.calculateArrivalTime = function(departureInput) {
        if (!departureInput || !departureInput.value) return;
        const [hours, mins] = departureInput.value.split(':').map(Number);
        if (isNaN(hours) || isNaN(mins)) return;
        const departDate = new Date(2026, 0, 1, hours, mins);
        departDate.setMinutes(departDate.getMinutes() + currentDurationMinutes);
        const arriveHours = departDate.getHours().toString().padStart(2, '0');
        const arriveMins = departDate.getMinutes().toString().padStart(2, '0');
        const scheduleItem = departureInput.closest('.schedule-item');
        if (scheduleItem) {
            const arrivalInput = scheduleItem.querySelector('.time-input.arrival');
            if (arrivalInput) arrivalInput.value = `${arriveHours}:${arriveMins}`;
        }
    };

    function updateAllArrivalTimes() {
        document.querySelectorAll('.departure-time').forEach(input => calculateArrivalTime(input));
    }

    function updateSummary() {
        const allerSchedules = document.querySelectorAll('#aller-schedules .schedule-item').length;
        const retourSchedules = toggleRetour.checked ? document.querySelectorAll('#retour-schedules .schedule-item').length : 0;
        document.getElementById('summary-aller').textContent = allerSchedules;
        document.getElementById('summary-retour').textContent = retourSchedules;
        document.getElementById('summary-total').textContent = allerSchedules + retourSchedules;
        document.getElementById('summary-price').textContent = montantBillet.value || 0;
    }

    itineraireSelect.addEventListener('change', function() {
        const selectedValue = this.value;
        const option = this.options[this.selectedIndex];
        
        if (selectedValue) {
            cardsContainer.style.display = 'block';
            summarySection.style.display = 'block';
            submitSection.style.display = 'block';
            
            const depart = option.dataset.depart;
            const arrive = option.dataset.arrive;
            document.getElementById('aller-route').textContent = `${depart} → ${arrive}`;
            document.getElementById('retour-route').textContent = `${arrive} → ${depart}`;
            
            currentDurationMinutes = parseDurationToMinutes(option.dataset.duree);
            updateAllArrivalTimes();
            updateSummary();
        } else {
            cardsContainer.style.display = 'none';
            summarySection.style.display = 'none';
            submitSection.style.display = 'none';
        }
    });

    window.addSchedule = function(type, initialTime = null, initialArrivalTime = null) {
        const container = document.getElementById(`${type}-schedules`);
        const items = container.querySelectorAll('.schedule-item');
        const newIndex = items.length;
        let nextTime = type === 'aller' ? '06:00' : '14:00';
        
        if (initialTime) nextTime = initialTime;
        else if (items.length > 0) {
            const lastItem = items[items.length - 1];
            const lastTimeInput = lastItem.querySelector('.departure-time');
            if (lastTimeInput && lastTimeInput.value) {
                const [hours, mins] = lastTimeInput.value.split(':').map(Number);
                let nextHour = (hours + 1) % 24;
                nextTime = `${nextHour.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}`;
            }
        }
        
        const scheduleHtml = `
            <div class="schedule-item ${type}" data-index="${newIndex}">
                <span class="badge-horaire">Horaire ${newIndex + 1}</span>
                ${newIndex > 0 ? `<button type="button" class="remove-schedule" onclick="window.removeSchedule('${type}', ${newIndex})"><i class="fas fa-times"></i></button>` : ''}
                <div class="schedule-times">
                    <div class="time-input-group">
                        <label>Départ</label>
                        <input type="time" name="${type}_horaires[${newIndex}][heure_depart]" 
                               class="time-input ${type} departure-time" 
                               value="${nextTime}" required
                               data-type="${type}" data-index="${newIndex}">
                    </div>
                    <div class="time-arrow"><i class="fas fa-long-arrow-alt-right"></i></div>
                    <div class="time-input-group">
                        <label>Arrivée estimée</label>
                        <input type="time" name="${type}_horaires[${newIndex}][heure_arrive]" 
                               class="time-input arrival ${type}" 
                               value="${initialArrivalTime || ''}" readonly>
                    </div>
                </div>
            </div>`;
        container.insertAdjacentHTML('beforeend', scheduleHtml);
        const newRow = container.querySelector(`.schedule-item[data-index="${newIndex}"]`);
        calculateArrivalTime(newRow.querySelector('.departure-time'));
        updateSummary();
    };

    window.removeSchedule = function(type, index) {
        const container = document.getElementById(`${type}-schedules`);
        const items = container.querySelectorAll('.schedule-item');
        if (items.length <= 1) { alert('Vous devez conserver au moins un horaire'); return; }
        const item = container.querySelector(`.schedule-item[data-index="${index}"]`);
        if (item) { item.remove(); reindexSchedules(type); updateSummary(); }
    };

    function reindexSchedules(type) {
        const container = document.getElementById(`${type}-schedules`);
        const items = container.querySelectorAll('.schedule-item');
        items.forEach((item, idx) => {
            item.dataset.index = idx;
            item.querySelector('.badge-horaire').textContent = `Horaire ${idx + 1}`;
            const departInput = item.querySelector('input[name*="heure_depart"]');
            const arriveInput = item.querySelector('input[name*="heure_arrive"]');
            departInput.name = `${type}_horaires[${idx}][heure_depart]`;
            departInput.dataset.index = idx;
            arriveInput.name = `${type}_horaires[${idx}][heure_arrive]`;
            const removeBtn = item.querySelector('.remove-schedule');
            if (removeBtn) removeBtn.setAttribute('onclick', `window.removeSchedule('${type}', ${idx})`);
        });
    }

    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('departure-time')) calculateArrivalTime(e.target);
    });

    document.getElementById('programmeForm').addEventListener('submit', function(e) {
        if (document.getElementById('gare_depart_id').value === document.getElementById('gare_arrivee_id').value) {
            e.preventDefault();
            alert('La gare de départ et la gare d\'arrivée doivent être différentes.');
        }
    });

    // Auto-init si des valeurs existent (Edit ou retour en cas d'erreur)
    const existingAller = @json($existingAller ?? []);
    const existingRetour = @json($existingRetour ?? []);
    const initialId = "{{ old('itineraire_id', $preselectedItineraireId ?? '') }}";

    if (initialId) {
        tsItineraire.setValue(initialId);
        setTimeout(() => {
            const allerContainer = document.getElementById('aller-schedules');
            const retourContainer = document.getElementById('retour-schedules');
            
            if (existingAller.length > 0) {
                allerContainer.innerHTML = '';
                existingAller.forEach(h => window.addSchedule('aller', h.heure_depart, h.heure_arrive));
            } else if (allerContainer.children.length === 0) window.addSchedule('aller');
            
            if (existingRetour.length > 0) {
                retourContainer.innerHTML = '';
                existingRetour.forEach(h => window.addSchedule('retour', h.heure_depart, h.heure_arrive));
                toggleRetour.checked = true;
                handleRetourToggle();
            } else if (retourContainer.children.length === 0) window.addSchedule('retour');
        }, 300);
    } else {
        document.getElementById('aller-schedules').innerHTML = '';
        document.getElementById('retour-schedules').innerHTML = '';
        window.addSchedule('aller');
        window.addSchedule('retour');
    }

    montantBillet.addEventListener('input', updateSummary);
    handleRetourToggle();
});
</script>
@endsection