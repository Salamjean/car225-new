@extends('gare-espace.layouts.template')

@section('title', 'Créer un convoi – Client sur place')

@section('styles')
<style>
    .walkin-shell {
        max-width: 860px;
        margin: 0 auto;
        padding: 28px 16px;
    }

    /* ── Header ── */
    .walkin-header {
        margin-bottom: 28px;
    }
    .walkin-header h1 {
        font-size: 26px;
        font-weight: 900;
        color: #0f172a;
        margin: 0 0 6px;
        letter-spacing: -0.4px;
    }
    .walkin-header h1 span { color: #f97316; }
    .walkin-header p {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
        margin: 0;
    }

    /* ── Section card ── */
    .wk-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 22px 24px;
        margin-bottom: 18px;
    }
    .wk-card-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        color: #475569;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }
    .wk-card-title i {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
    .icon-orange { background: #fff7ed; color: #f97316; }
    .icon-blue   { background: #eff6ff; color: #3b82f6; }
    .icon-green  { background: #f0fdf4; color: #22c55e; }
    .icon-purple { background: #faf5ff; color: #a855f7; }

    /* ── Form fields ── */
    .wk-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }
    .wk-grid-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 14px;
    }
    .wk-field { display: flex; flex-direction: column; gap: 5px; }
    .wk-label {
        font-size: 11px;
        font-weight: 900;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .wk-label .req { color: #ef4444; margin-left: 2px; }
    .wk-input, .wk-select, .wk-textarea {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 13px;
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        background: #f8fafc;
        width: 100%;
        transition: border-color .2s, box-shadow .2s;
        outline: none;
    }
    .wk-input:focus, .wk-select:focus, .wk-textarea:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.12);
        background: #fff;
    }
    .wk-textarea { resize: vertical; min-height: 80px; }

    /* Toggle trajet */
    .trajet-toggle {
        display: flex;
        gap: 8px;
        margin-bottom: 14px;
    }
    .trajet-btn {
        flex: 1;
        padding: 10px 14px;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        cursor: pointer;
        transition: all .2s;
        text-align: center;
    }
    .trajet-btn.active {
        border-color: #f97316;
        background: #fff7ed;
        color: #f97316;
    }

    /* Montant highlight */
    .montant-wrap {
        position: relative;
    }
    .montant-wrap .currency {
        position: absolute;
        right: 13px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 11px;
        font-weight: 900;
        color: #94a3b8;
        pointer-events: none;
    }
    .montant-wrap .wk-input { padding-right: 55px; }

    /* Submit area */
    .wk-submit-area {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        padding-top: 8px;
    }
    .btn-cancel-wk {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 11px 20px;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        text-decoration: none !important;
        transition: all .2s;
    }
    .btn-cancel-wk:hover { background: #f8fafc; border-color: #cbd5e1; color: #0f172a; }
    .btn-submit-wk {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 28px;
        border-radius: 12px;
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: #fff;
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(249, 115, 22, 0.35);
        transition: all .2s;
    }
    .btn-submit-wk:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(249, 115, 22, 0.45); }
    .btn-submit-wk:active { transform: translateY(0); }

    /* Alert errors */
    .wk-error-box {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 12px;
        padding: 14px 18px;
        margin-bottom: 18px;
        font-size: 13px;
        font-weight: 700;
        color: #dc2626;
    }
    .wk-error-box ul { margin: 6px 0 0; padding-left: 18px; }
    .wk-error-box li { margin-bottom: 4px; }

    @media (max-width: 640px) {
        .wk-grid-2, .wk-grid-3 { grid-template-columns: 1fr; }
        .walkin-shell { padding: 16px 10px; }
    }
</style>
@endsection

@section('content')
<div class="walkin-shell">

    <!-- Header -->
    <div class="walkin-header">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <div>
                <h1>Créer un <span>convoi</span></h1>
                <p>Client sur place — prise en charge directe à la gare</p>
            </div>
            <a href="{{ route('gare-espace.convois.index') }}" class="btn-cancel-wk">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- Erreurs de validation -->
    @if($errors->any())
    <div class="wk-error-box">
        <strong><i class="fas fa-exclamation-circle mr-1"></i>Veuillez corriger les erreurs suivantes :</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('gare-espace.convois.store') }}" id="walkinForm">
        @csrf

        <!-- ── 1. Infos client ── -->
        <div class="wk-card">
            <div class="wk-card-title">
                <i class="fas fa-user icon-orange"></i>
                Informations du client
            </div>
            <div class="wk-grid-2" style="margin-bottom:14px;">
                <div class="wk-field">
                    <label class="wk-label">Prénom <span class="req">*</span></label>
                    <input type="text" name="client_prenom" class="wk-input"
                           value="{{ old('client_prenom') }}"
                           placeholder="Ex: Kouassi">
                </div>
                <div class="wk-field">
                    <label class="wk-label">Nom <span class="req">*</span></label>
                    <input type="text" name="client_nom" class="wk-input"
                           value="{{ old('client_nom') }}"
                           placeholder="Ex: Yao">
                </div>
            </div>
            <div class="wk-grid-2">
                <div class="wk-field">
                    <label class="wk-label">Contact (téléphone) <span class="req">*</span></label>
                    <input type="tel" name="client_contact" class="wk-input"
                           value="{{ old('client_contact') }}"
                           placeholder="Ex: 0708325027" maxlength="20">
                </div>
                <div class="wk-field">
                    <label class="wk-label">E-mail <span style="font-size:9px;color:#94a3b8;">(optionnel)</span></label>
                    <input type="email" name="client_email" class="wk-input"
                           value="{{ old('client_email') }}"
                           placeholder="Ex: kouassi@email.com">
                </div>
            </div>
        </div>

        <!-- ── 2. Trajet ── -->
        <div class="wk-card">
            <div class="wk-card-title">
                <i class="fas fa-route icon-blue"></i>
                Trajet
            </div>

            <!-- Toggle itinéraire prédéfini / libre -->
            <div class="trajet-toggle">
                <button type="button" id="btn-itineraire" class="trajet-btn {{ old('mode_trajet', 'itineraire') === 'itineraire' ? 'active' : '' }}"
                        onclick="setTrajetMode('itineraire')">
                    <i class="fas fa-list mr-1"></i> Itinéraire existant
                </button>
                <button type="button" id="btn-libre" class="trajet-btn {{ old('mode_trajet') === 'libre' ? 'active' : '' }}"
                        onclick="setTrajetMode('libre')">
                    <i class="fas fa-pen mr-1"></i> Trajet personnalisé
                </button>
            </div>
            <input type="hidden" name="mode_trajet" id="mode_trajet" value="{{ old('mode_trajet', 'itineraire') }}">

            <!-- Itinéraire prédéfini -->
            <div id="block-itineraire" class="{{ old('mode_trajet') === 'libre' ? 'hidden' : '' }}">
                <div class="wk-field">
                    <label class="wk-label">Itinéraire</label>
                    <select name="itineraire_id" class="wk-select" id="itineraireSelect">
                        <option value="">— Choisir un itinéraire —</option>
                        @foreach($itineraires as $itin)
                            <option value="{{ $itin->id }}"
                                {{ old('itineraire_id') == $itin->id ? 'selected' : '' }}>
                                {{ $itin->point_depart }} → {{ $itin->point_arrive }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if($itineraires->isEmpty())
                    <p style="font-size:12px;color:#f59e0b;margin-top:8px;font-weight:700;">
                        <i class="fas fa-info-circle mr-1"></i>
                        Aucun itinéraire disponible. Utilisez le trajet personnalisé.
                    </p>
                @endif
            </div>

            <!-- Trajet libre -->
            <div id="block-libre" class="{{ old('mode_trajet') !== 'libre' ? 'hidden' : '' }}">
                <div class="wk-grid-2">
                    <div class="wk-field">
                        <label class="wk-label">Lieu de départ <span class="req">*</span></label>
                        <input type="text" name="lieu_depart" class="wk-input"
                               value="{{ old('lieu_depart') }}"
                               placeholder="Ex: Abidjan Plateau">
                    </div>
                    <div class="wk-field">
                        <label class="wk-label">Lieu d'arrivée <span class="req">*</span></label>
                        <input type="text" name="lieu_retour" class="wk-input"
                               value="{{ old('lieu_retour') }}"
                               placeholder="Ex: Bouaké Centre">
                    </div>
                </div>
            </div>
        </div>

        <!-- ── 3. Dates & horaires ── -->
        <div class="wk-card">
            <div class="wk-card-title">
                <i class="fas fa-calendar-alt icon-green"></i>
                Dates & Horaires
            </div>
            <div class="wk-grid-2" style="margin-bottom:14px;">
                <div class="wk-field">
                    <label class="wk-label">Date de départ <span class="req">*</span></label>
                    <input type="date" name="date_depart" class="wk-input"
                           value="{{ old('date_depart') }}"
                           min="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="wk-field">
                    <label class="wk-label">Heure de départ <span class="req">*</span></label>
                    <input type="time" name="heure_depart" class="wk-input"
                           value="{{ old('heure_depart') }}">
                </div>
            </div>
            <div class="wk-grid-2">
                <div class="wk-field">
                    <label class="wk-label">Date de retour <span style="font-size:9px;color:#94a3b8;">(optionnel)</span></label>
                    <input type="date" name="date_retour" class="wk-input"
                           value="{{ old('date_retour') }}"
                           min="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="wk-field">
                    <label class="wk-label">Heure de retour <span style="font-size:9px;color:#94a3b8;">(si retour)</span></label>
                    <input type="time" name="heure_retour" class="wk-input"
                           value="{{ old('heure_retour') }}">
                </div>
            </div>
        </div>

        <!-- ── 4. Convoi ── -->
        <div class="wk-card">
            <div class="wk-card-title">
                <i class="fas fa-users icon-purple"></i>
                Détails du convoi
            </div>
            <div class="wk-grid-3" style="margin-bottom:14px;">
                <div class="wk-field">
                    <label class="wk-label">Nombre de personnes <span class="req">*</span></label>
                    <input type="number" name="nombre_personnes" class="wk-input"
                           value="{{ old('nombre_personnes', 10) }}"
                           min="10" max="500">
                </div>
                <div class="wk-field" style="grid-column: span 2;">
                    <label class="wk-label">Montant payé (FCFA) <span class="req">*</span></label>
                    <div class="montant-wrap">
                        <input type="number" name="montant" class="wk-input"
                               value="{{ old('montant', 0) }}"
                               min="0" step="500">
                        <span class="currency">FCFA</span>
                    </div>
                </div>
            </div>
            <div class="wk-field" style="margin-bottom:14px;">
                <label class="wk-label">Lieu de rassemblement <span class="req">*</span></label>
                <input type="text" name="lieu_rassemblement" class="wk-input"
                       value="{{ old('lieu_rassemblement') }}"
                       placeholder="Ex: Devant la gare routière d'Adjamé">
            </div>
            <div class="wk-field">
                <label class="wk-label">Motif / observations <span style="font-size:9px;color:#94a3b8;">(optionnel)</span></label>
                <textarea name="motif" class="wk-textarea"
                          placeholder="Remarques ou informations complémentaires...">{{ old('motif') }}</textarea>
            </div>
        </div>

        <!-- ── Résumé + validation ── -->
        <div class="wk-card" style="background:linear-gradient(135deg,#fff7ed 0%,#fef3c7 100%);border-color:#fed7aa;">
            <div style="display:flex;align-items:flex-start;gap:14px;flex-wrap:wrap;">
                <div style="flex:1;min-width:220px;">
                    <p style="font-size:12px;font-weight:900;color:#9a3412;margin:0 0 4px;text-transform:uppercase;letter-spacing:0.5px;">
                        <i class="fas fa-info-circle mr-1"></i>Récapitulatif
                    </p>
                    <p style="font-size:12px;font-weight:700;color:#78350f;margin:0;line-height:1.6;">
                        Le convoi sera créé directement avec le statut <strong>Payé</strong>.<br>
                        Un SMS de confirmation sera envoyé au contact du client.<br>
                        La gare <strong>{{ $gare->nom_gare }}</strong> sera automatiquement affectée.
                    </p>
                </div>
                <div class="wk-submit-area" style="padding-top:0;">
                    <a href="{{ route('gare-espace.convois.index') }}" class="btn-cancel-wk">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    <button type="submit" class="btn-submit-wk">
                        <i class="fas fa-check"></i> Créer le convoi
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
    function setTrajetMode(mode) {
        document.getElementById('mode_trajet').value = mode;

        const btnItineraire = document.getElementById('btn-itineraire');
        const btnLibre      = document.getElementById('btn-libre');
        const blockItin     = document.getElementById('block-itineraire');
        const blockLibre    = document.getElementById('block-libre');

        if (mode === 'itineraire') {
            btnItineraire.classList.add('active');
            btnLibre.classList.remove('active');
            blockItin.classList.remove('hidden');
            blockLibre.classList.add('hidden');
        } else {
            btnLibre.classList.add('active');
            btnItineraire.classList.remove('active');
            blockLibre.classList.remove('hidden');
            blockItin.classList.add('hidden');
            // Clear itineraire_id when switching to libre
            document.getElementById('itineraireSelect').value = '';
        }
    }

    // Sync date retour min with date depart
    const dateDepart  = document.querySelector('[name="date_depart"]');
    const dateRetour  = document.querySelector('[name="date_retour"]');
    if (dateDepart && dateRetour) {
        dateDepart.addEventListener('change', function () {
            dateRetour.min = this.value;
            if (dateRetour.value && dateRetour.value < this.value) {
                dateRetour.value = this.value;
            }
        });
    }
</script>
@endpush
