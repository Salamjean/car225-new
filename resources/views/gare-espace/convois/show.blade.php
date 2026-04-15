@extends('gare-espace.layouts.template')

@section('title', 'Détail convoi')

@section('styles')
<style>
    .convoi-shell {
        padding: 28px;
    }

    /* ── Header ────────────────────────────────── */
    .convoi-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 28px;
    }
    .convoi-header h1 {
        font-size: 26px;
        font-weight: 900;
        color: #0f172a;
        margin: 0;
        letter-spacing: -0.4px;
    }
    .convoi-header h1 span { color: #f97316; }
    .convoi-ref {
        font-size: 12px;
        font-weight: 800;
        color: #94a3b8;
        margin-top: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .btn-back-show {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 12px;
        background: #fff;
        border: 1px solid #e5e7eb;
        color: #475569;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all .2s;
    }
    .btn-back-show:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }

    /* ── Status badge ──────────────────────────── */
    .status-chip {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 7px 14px;
        border-radius: 999px;
    }
    .status-chip::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
    }

    /* ── Cards grid ────────────────────────────── */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }
    .info-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 18px 20px;
        transition: box-shadow .2s;
    }
    .info-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.04); }
    .info-card .label {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        color: #94a3b8;
        font-weight: 900;
        margin-bottom: 8px;
    }
    .info-card .value {
        font-size: 15px;
        font-weight: 800;
        color: #1e293b;
        line-height: 1.3;
    }
    .info-card .value.small-text { font-size: 13px; }

    /* ── Full-width route card ─────────────────── */
    .route-card {
        background: linear-gradient(135deg, #fff7ed 0%, #fef3c7 100%);
        border: 1px solid #fed7aa;
        border-radius: 16px;
        padding: 20px 24px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .route-card .route-icon {
        width: 48px;
        height: 48px;
        background: #fff;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #f97316;
        font-size: 20px;
        border: 1px solid #fed7aa;
        flex-shrink: 0;
    }
    .route-card .route-text {
        font-size: 16px;
        font-weight: 900;
        color: #92400e;
    }
    .route-card .route-text .arrow {
        color: #f97316;
        margin: 0 10px;
    }
    .route-dates {
        margin-left: auto;
        text-align: right;
        font-size: 12px;
        font-weight: 700;
        color: #92400e;
    }
    .route-dates span { display: block; margin-bottom: 2px; }

    /* ── Section block ─────────────────────────── */
    .section-block {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        overflow: hidden;
        margin-bottom: 24px;
    }
    .section-head {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-head h3 {
        margin: 0;
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #334155;
    }
    .section-head .icon-circle {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }
    .section-body { padding: 20px 24px; }

    /* ── Assign section ────────────────────────── */
    .assign-section { border-color: #c7d2fe; }
    .assign-section .section-head { background: #eef2ff; }
    .assign-section .section-head h3 { color: #3730a3; }
    .assign-section .icon-circle { background: #c7d2fe; color: #4338ca; }

    .assign-row {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 16px;
        align-items: end;
    }
    .assign-row label {
        display: block;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6b7280;
        margin-bottom: 7px;
    }
    .assign-row select {
        width: 100%;
        padding: 11px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        background: #f8fafc;
        transition: border-color .2s;
    }
    .assign-row select:focus { outline: none; border-color: #818cf8; box-shadow: 0 0 0 3px rgba(129,140,248,.15); }

    .btn-assign {
        padding: 11px 24px;
        border-radius: 12px;
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        color: #fff;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        border: none;
        cursor: pointer;
        transition: all .2s;
        white-space: nowrap;
    }
    .btn-assign:hover { background: linear-gradient(135deg, #6d28d9, #5b21b6); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(109,40,217,.25); }

    .btn-unassign {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 12px;
        background: #fef2f2;
        color: #dc2626;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        border: 1px solid #fecaca;
        cursor: pointer;
        transition: all .2s;
    }
    .btn-unassign:hover { background: #dc2626; color: #fff; border-color: #dc2626; }

    /* ── GPS section ───────────────────────────── */
    .gps-section { border-color: #bae6fd; }
    .gps-section .section-head { background: #e0f2fe; }
    .gps-section .section-head h3 { color: #075985; }
    .gps-section .icon-circle { background: #bae6fd; color: #0284c7; }

    /* ── Passengers table ──────────────────────── */
    .pass-section .section-head { background: #f8fafc; }
    .pass-section .icon-circle { background: #fff7ed; color: #ea580c; }

    .table-clean { width: 100%; border-collapse: collapse; }
    .table-clean thead th {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        font-weight: 900;
        color: #94a3b8;
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
        text-align: left;
    }
    .table-clean tbody td {
        padding: 14px 20px;
        border-bottom: 1px solid #f8fafc;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
    }
    .table-clean tbody tr:hover td { background: #fffbeb; }
    .num-pill {
        display: inline-flex;
        min-width: 28px;
        justify-content: center;
        border-radius: 8px;
        background: #fff7ed;
        color: #ea580c;
        font-size: 11px;
        font-weight: 900;
        padding: 4px 8px;
    }

    /* ── Passagers form ────────────────────────── */
    .pass-section { border-color: #e2e8f0; }
    .pass-section .section-head { background: #f8fafc; }
    .pass-section .icon-circle  { background: #fff7ed; color: #ea580c; }

    /* Toggle garant */
    .garant-toggle-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 20px;
        background: #f5f3ff;
        border: 1.5px solid #c4b5fd;
        border-radius: 14px;
        margin-bottom: 18px;
        cursor: pointer;
    }
    .garant-toggle-wrap input[type="checkbox"] { display: none; }
    .garant-slider {
        position: relative;
        width: 48px;
        height: 26px;
        background: #cbd5e1;
        border-radius: 99px;
        flex-shrink: 0;
        transition: background .2s;
        cursor: pointer;
    }
    .garant-slider::after {
        content: '';
        position: absolute;
        top: 3px; left: 3px;
        width: 20px; height: 20px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 1px 4px rgba(0,0,0,.2);
        transition: transform .2s;
    }
    .garant-slider.on { background: #7c3aed; }
    .garant-slider.on::after { transform: translateX(22px); }

    .garant-text-title {
        font-size: 13px;
        font-weight: 900;
        color: #3b0764;
    }
    .garant-text-sub {
        font-size: 11px;
        font-weight: 600;
        color: #7c3aed;
        margin-top: 2px;
    }

    /* Garant banner */
    .garant-banner {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        background: #f5f3ff;
        border: 1px solid #ddd6fe;
        border-radius: 12px;
        margin-bottom: 16px;
        font-size: 12px;
        font-weight: 700;
        color: #5b21b6;
    }

    /* Passager row card */
    .passager-row {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 18px;
        margin-bottom: 12px;
    }
    .passager-row-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }
    .passager-num {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 11px;
        font-weight: 900;
        color: #ea580c;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .passager-num-dot {
        width: 24px;
        height: 24px;
        border-radius: 8px;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 900;
        color: #ea580c;
        flex-shrink: 0;
    }
    .pass-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .pass-grid-4 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr;
        gap: 12px;
    }
    .pass-field label {
        display: block;
        font-size: 10px;
        font-weight: 900;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }
    .pass-input {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 9px;
        padding: 9px 12px;
        font-size: 12px;
        font-weight: 700;
        color: #1e293b;
        background: #fff;
        transition: border-color .2s, box-shadow .2s;
        outline: none;
    }
    .pass-input:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249,115,22,.1);
    }

    /* Save button */
    .btn-save-pass {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 28px;
        border-radius: 12px;
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: #fff;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(249,115,22,.3);
        transition: all .2s;
    }
    .btn-save-pass:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(249,115,22,.4); }

    @media (max-width: 600px) {
        .pass-grid-4 { grid-template-columns: 1fr 1fr; }
        .pass-grid   { grid-template-columns: 1fr; }
    }

    /* ── Alerts ─────────────────────────────────── */
    .alert-box {
        padding: 14px 18px;
        border-radius: 14px;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .alert-success-box { background: #ecfdf5; border: 1px solid #bbf7d0; color: #065f46; }
    .alert-error-box { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

    /* ── Demandeur card ─────────────────────────── */
    .demandeur-card {
        background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
        border: 1px solid #bbf7d0;
    }
    .demandeur-card .label { color: #15803d; }
    .demandeur-card .value { color: #166534; }

    @media (max-width: 768px) {
        .convoi-shell { padding: 16px; }
        .info-grid { grid-template-columns: repeat(2, 1fr); }
        .assign-row { grid-template-columns: 1fr; }
        .route-card { flex-direction: column; align-items: flex-start; }
        .route-dates { margin-left: 0; text-align: left; }
    }
</style>
@endsection

@section('content')
<div class="convoi-shell">
    {{-- ── Header ────────────────────────────── --}}
    <div class="convoi-header">
        <div>
            <h1>Détail <span>Convoi</span></h1>
            <div class="convoi-ref"><i class="fas fa-hashtag mr-1"></i> {{ $convoi->reference }}</div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            @if($convoi->created_by_gare)
            <a href="{{ route('gare-espace.convois.recu-pdf', $convoi) }}" target="_blank"
               style="display:inline-flex;align-items:center;gap:7px;padding:10px 18px;border-radius:12px;background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:0.4px;text-decoration:none;box-shadow:0 3px 10px rgba(249,115,22,.3);transition:all .2s;">
                <i class="fas fa-print"></i> Imprimer le reçu
            </a>
            @endif
            <a href="{{ route('gare-espace.convois.index') }}" class="btn-back-show">
                <i class="fas fa-arrow-left"></i> Retour aux convois
            </a>
        </div>
    </div>

    {{-- ── Alerts ────────────────────────────── --}}
    @if(session('success'))
        <div class="alert-box alert-success-box"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-box alert-error-box"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    @php
        $trajetDepart  = $convoi->lieu_depart  ?? ($convoi->itineraire->point_depart ?? '-');
        $trajetArrivee = $convoi->lieu_retour   ?? ($convoi->itineraire->point_arrive ?? '-');

        $sm = [
            'paye'     => ['Payé',    '#7c3aed', '#f5f3ff'],
            'en_cours' => ['En cours','#0284c7', '#e0f2fe'],
            'termine'  => ['Terminé', '#059669', '#ecfdf5'],
            'annule'   => ['Annulé',  '#dc2626', '#fef2f2'],
            'valide'   => ['Validé',  '#059669', '#ecfdf5'],
            'refuse'   => ['Refusé',  '#dc2626', '#fef2f2'],
        ];
        [$sLabel, $sColor, $sBg] = $sm[$convoi->statut] ?? [ucfirst(str_replace('_',' ',$convoi->statut)), '#475569', '#f1f5f9'];
    @endphp

    {{-- ── Route card (full width) ───────────── --}}
    <div class="route-card">
        <div class="route-icon"><i class="fas fa-route"></i></div>
        <div class="route-text">
            {{ $trajetDepart }} <span class="arrow">&rarr;</span> {{ $trajetArrivee }}
        </div>
        <div class="route-dates">
            @if($convoi->date_depart)
                <span><i class="far fa-calendar-alt mr-1"></i> Départ : {{ \Carbon\Carbon::parse($convoi->date_depart)->format('d/m/Y') }}@if($convoi->heure_depart) à {{ $convoi->heure_depart }}@endif</span>
            @endif
            @if($convoi->date_retour)
                <span><i class="far fa-calendar-check mr-1"></i> Retour : {{ \Carbon\Carbon::parse($convoi->date_retour)->format('d/m/Y') }}@if($convoi->heure_retour) à {{ $convoi->heure_retour }}@endif</span>
            @endif
        </div>
    </div>

    {{-- ── Info grid ─────────────────────────── --}}
    <div class="info-grid">
        <div class="info-card">
            <div class="label">Statut</div>
            <span class="status-chip" style="background:{{ $sBg }};color:{{ $sColor }};">{{ $sLabel }}</span>
        </div>
        <div class="info-card">
            <div class="label">Compagnie</div>
            <div class="value">{{ $convoi->compagnie->name ?? '-' }}</div>
        </div>
        <div class="info-card">
            <div class="label">Nombre de personnes</div>
            <div class="value">{{ $convoi->nombre_personnes }} <span style="font-size:11px;color:#94a3b8;">passagers</span></div>
        </div>
        <div class="info-card demandeur-card">
            <div class="label">
                <i class="fas fa-user mr-1"></i>
                @if($convoi->created_by_gare) Client sur place @else Demandeur @endif
            </div>
            <div class="value">{{ $convoi->demandeur_nom }}</div>
            @if($convoi->demandeur_contact)
                <div style="font-size:11px;color:#16a34a;margin-top:4px;font-weight:700;"><i class="fas fa-phone mr-1"></i>{{ $convoi->demandeur_contact }}</div>
            @endif
            @if($convoi->created_by_gare)
                <div style="margin-top:5px;">
                    <span style="display:inline-flex;align-items:center;gap:4px;font-size:9px;font-weight:900;background:#fff7ed;color:#ea580c;padding:3px 8px;border-radius:6px;text-transform:uppercase;letter-spacing:0.4px;">
                        <i class="fas fa-store"></i> Créé en gare
                    </span>
                </div>
            @endif
        </div>
        @if($convoi->montant)
        <div class="info-card">
            <div class="label">Montant</div>
            <div class="value" style="color:#059669;">{{ number_format($convoi->montant, 0, ',', ' ') }} <span style="font-size:11px;">FCFA</span></div>
        </div>
        @endif
        @if($convoi->chauffeur)
        <div class="info-card">
            <div class="label"><i class="fas fa-id-badge mr-1"></i> Chauffeur assigné</div>
            <div class="value small-text">{{ trim(($convoi->chauffeur->prenom ?? '') . ' ' . ($convoi->chauffeur->name ?? '')) }}</div>
            @if($convoi->chauffeur->contact ?? null)
                <div style="font-size:11px;color:#6b7280;margin-top:3px;font-weight:600;"><i class="fas fa-phone mr-1"></i>{{ $convoi->chauffeur->contact }}</div>
            @endif
        </div>
        @endif
        @if($convoi->vehicule)
        <div class="info-card">
            <div class="label"><i class="fas fa-bus mr-1"></i> Véhicule assigné</div>
            <div class="value small-text">{{ $convoi->vehicule->immatriculation }}</div>
            @if($convoi->vehicule->modele)
                <div style="font-size:11px;color:#6b7280;margin-top:3px;font-weight:600;">{{ $convoi->vehicule->modele }} &bull; {{ $convoi->vehicule->nombre_place }} places</div>
            @endif
        </div>
        @endif
    </div>

    {{-- Alerte désistement chauffeur --}}
    @if($convoi->motif_annulation_chauffeur && $convoi->statut === 'paye' && !$convoi->personnel_id)
    <div class="alert-box" style="background:#FFF7ED;border:1px solid #fed7aa;color:#92400e;margin-bottom:20px;">
        <i class="fas fa-exclamation-triangle" style="color:#f97316;font-size:16px;"></i>
        <div>
            <strong style="display:block;font-size:13px;margin-bottom:4px;">Désistement du chauffeur précédent</strong>
            <span style="font-size:12px;">{{ $convoi->motif_annulation_chauffeur }}</span>
            <span style="display:block;font-size:11px;color:#b45309;margin-top:4px;">Veuillez affecter un nouveau chauffeur et un véhicule de remplacement.</span>
        </div>
    </div>
    @endif

    {{-- Lieu de rassemblement --}}
    @if($convoi->lieu_rassemblement)
    <div class="alert-box" style="background:#EFF6FF;border:1px solid #bfdbfe;color:#1e40af;margin-bottom:20px;">
        <i class="fas fa-map-pin" style="font-size:16px;"></i>
        <div>
            <strong style="display:block;font-size:13px;margin-bottom:4px;">Lieu de rassemblement</strong>
            <span style="font-size:14px;font-weight:800;">{{ $convoi->lieu_rassemblement }}</span>
            <span style="display:block;font-size:11px;color:#3b82f6;margin-top:3px;">Le chauffeur devra passer à ce lieu avant le départ.</span>
        </div>
    </div>
    @endif

    {{-- ── Affectation (uniquement si paye) ──── --}}
    @if($convoi->statut === 'paye')
    @php
        $gareId = Auth::guard('gare')->id();

        // Chauffeurs disponibles : statut=disponible + pas en voyage en_cours + pas sur un convoi chevauchant
        $busyVoyPersonnel = \App\Models\Voyage::where('statut', 'en_cours')->whereNotNull('personnel_id')->pluck('personnel_id');
        $busyConvoiPersonnel = \App\Models\Convoi::whereNotNull('personnel_id')
            ->whereIn('statut', ['paye', 'en_cours'])
            ->when($convoi->id, fn($q) => $q->where('id', '!=', $convoi->id))
            ->when($convoi->date_depart && $convoi->date_retour, fn($q) =>
                $q->where('date_depart', '<=', $convoi->date_retour)->where('date_retour', '>=', $convoi->date_depart)
            )->pluck('personnel_id');
        $excludePersonnel = $busyVoyPersonnel->merge($busyConvoiPersonnel)->unique();

        $chauffeursDispo = \App\Models\Personnel::where('gare_id', $gareId)
            ->where('type_personnel', 'chauffeur')->where('statut', 'disponible')
            ->whereNull('archived_at')->whereNotIn('id', $excludePersonnel)
            ->orderBy('prenom')->get(['id', 'name', 'prenom']);

        // Véhicules disponibles
        $busyVoyVehicule = \App\Models\Voyage::where('statut', 'en_cours')->whereNotNull('vehicule_id')->pluck('vehicule_id');
        $busyConvoiVehicule = \App\Models\Convoi::whereNotNull('vehicule_id')
            ->whereIn('statut', ['paye', 'en_cours'])
            ->when($convoi->id, fn($q) => $q->where('id', '!=', $convoi->id))
            ->when($convoi->date_depart && $convoi->date_retour, fn($q) =>
                $q->where('date_depart', '<=', $convoi->date_retour)->where('date_retour', '>=', $convoi->date_depart)
            )->pluck('vehicule_id');
        $excludeVehicule = $busyVoyVehicule->merge($busyConvoiVehicule)->unique();

        $vehiculesDispo = \App\Models\Vehicule::where('gare_id', $gareId)
            ->where('is_active', true)->where('statut', 'disponible')
            ->whereNotIn('id', $excludeVehicule)
            ->orderBy('immatriculation')->get(['id', 'immatriculation', 'modele', 'nombre_place']);
    @endphp

    <div class="section-block assign-section">
        <div class="section-head">
            <div class="icon-circle"><i class="fas fa-user-cog"></i></div>
            <h3>@if($convoi->personnel_id) Gestion de l'affectation @else Affecter chauffeur & véhicule @endif</h3>
        </div>
        <div class="section-body">
            <form action="{{ $convoi->personnel_id ? route('gare-espace.convois.reassign', $convoi) : route('gare-espace.convois.assign', $convoi) }}"
                  method="POST">
                @csrf
                <div class="assign-row">
                    <div>
                        <label>Chauffeur <span style="color:#ef4444;">*</span></label>
                        <select name="personnel_id" required>
                            <option value="">-- Choisir un chauffeur --</option>
                            @foreach($chauffeursDispo as $ch)
                                <option value="{{ $ch->id }}" {{ $convoi->personnel_id == $ch->id ? 'selected' : '' }}>
                                    {{ trim(($ch->prenom ?? '') . ' ' . ($ch->name ?? '')) }}
                                </option>
                            @endforeach
                            @if($convoi->chauffeur && !$chauffeursDispo->contains('id', $convoi->personnel_id))
                                <option value="{{ $convoi->personnel_id }}" selected>
                                    {{ trim(($convoi->chauffeur->prenom ?? '') . ' ' . ($convoi->chauffeur->name ?? '')) }} (actuel)
                                </option>
                            @endif
                        </select>
                    </div>
                    <div>
                        <label>Véhicule <span style="color:#ef4444;">*</span></label>
                        <select name="vehicule_id" required>
                            <option value="">-- Choisir un véhicule --</option>
                            @foreach($vehiculesDispo as $v)
                                <option value="{{ $v->id }}" {{ $convoi->vehicule_id == $v->id ? 'selected' : '' }}>
                                    {{ $v->immatriculation }}{{ $v->modele ? ' — ' . $v->modele : '' }} ({{ $v->nombre_place }} pl.)
                                </option>
                            @endforeach
                            @if($convoi->vehicule && !$vehiculesDispo->contains('id', $convoi->vehicule_id))
                                <option value="{{ $convoi->vehicule_id }}" selected>
                                    {{ $convoi->vehicule->immatriculation }} (actuel)
                                </option>
                            @endif
                        </select>
                    </div>
                    <div>
                        <label>&nbsp;</label>
                        <button type="submit" class="btn-assign">
                            <i class="fas fa-{{ $convoi->personnel_id ? 'sync-alt' : 'user-check' }} mr-1"></i>
                            {{ $convoi->personnel_id ? 'Modifier' : 'Affecter' }}
                        </button>
                    </div>
                </div>
            </form>

            @if($convoi->personnel_id)
            <div style="margin-top:18px;padding-top:18px;border-top:1px dashed #e2e8f0;">
                <form action="{{ route('gare-espace.convois.unassign', $convoi) }}" method="POST"
                      onsubmit="return confirm('Annuler l\'affectation ? Le chauffeur et le véhicule seront libérés.')">
                    @csrf
                    <button type="submit" class="btn-unassign">
                        <i class="fas fa-times-circle"></i> Annuler l'affectation et reprogrammer
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ── GPS temps réel (en_cours / terminé) ── --}}
    @if(in_array($convoi->statut, ['en_cours', 'termine']))
    <div class="section-block gps-section">
        <div class="section-head">
            <div class="icon-circle"><i class="fas fa-satellite-dish"></i></div>
            <h3>Suivi GPS temps réel</h3>
            <span class="status-chip" style="background:#e0f2fe;color:#0284c7;margin-left:auto;font-size:10px;" id="trackingStatusBadge">--</span>
        </div>
        <div class="section-body">
            <div style="font-weight:800;color:#1e293b;margin-bottom:6px;" id="trackingCoords">Position : --</div>
            <div style="font-size:12px;color:#6b7280;font-weight:600;" id="trackingMeta">Dernière mise à jour : --</div>
            <a href="#" id="trackingMapLink" target="_blank"
               style="display:none;margin-top:12px;padding:9px 16px;border-radius:10px;background:#0284c7;color:#fff;font-size:12px;font-weight:800;text-decoration:none;text-transform:uppercase;letter-spacing:.3px;">
                <i class="fas fa-map-marker-alt mr-1"></i> Voir sur Google Maps
            </a>
        </div>
    </div>
    @endif

    {{-- ── Passagers ─────────────────────────── --}}
    @php
        $passagersExistants = $convoi->passagers->values();
    @endphp

    {{-- Formulaire interactif — uniquement pour les convois walk-in payés --}}
    @if($convoi->created_by_gare && $convoi->statut === 'paye')
    <div class="section-block pass-section">
        <div class="section-head">
            <div class="icon-circle"><i class="fas fa-users"></i></div>
            <h3>
                @if($convoi->is_garant)
                    Garant du groupe
                @else
                    Passagers
                    <span style="font-size:11px;color:#94a3b8;margin-left:6px;font-weight:700;">
                        {{ $passagersExistants->count() }} / {{ $convoi->nombre_personnes }} enregistrés
                    </span>
                @endif
            </h3>
        </div>
        <div class="section-body">

            @php
                // Infos client walk-in pour pré-remplissage
                $clientNom     = $convoi->client_nom    ?? '';
                $clientPrenom  = $convoi->client_prenom ?? '';
                $clientContact = $convoi->client_contact ?? '';
            @endphp

            <form action="{{ route('gare-espace.convois.store-passagers', $convoi) }}" method="POST" id="passForm">
                @csrf

                {{-- Toggle garant --}}
                <div class="garant-toggle-wrap" onclick="toggleGarant()">
                    <input type="checkbox" id="garantCheck" name="is_garant" value="1"
                           {{ $convoi->is_garant ? 'checked' : '' }}>
                    <div class="garant-slider {{ $convoi->is_garant ? 'on' : '' }}" id="garantSlider"></div>
                    <div>
                        <div class="garant-text-title">
                            <i class="fas fa-user-shield mr-1"></i> Le client se porte garant pour le groupe
                        </div>
                        <div class="garant-text-sub">
                            Seules ses informations personnelles seront enregistrées.
                        </div>
                    </div>
                </div>

                {{-- Bannière garant --}}
                <div class="garant-banner {{ $convoi->is_garant ? '' : 'hidden' }}" id="garantBanner">
                    <i class="fas fa-shield-alt" style="color:#7c3aed;font-size:16px;flex-shrink:0;"></i>
                    <span>Mode garant activé — seules les informations du responsable du groupe sont enregistrées.</span>
                </div>

                {{-- Lignes passagers --}}
                <div id="passagersContainer">
                    @for($i = 0; $i < $convoi->nombre_personnes; $i++)
                    @php $p = $passagersExistants[$i] ?? null; @endphp
                    <div class="passager-row {{ $convoi->is_garant && $i > 0 ? 'hidden' : '' }}"
                         data-row="{{ $i }}">
                        <div class="passager-row-head">
                            <span class="passager-num">
                                <span class="passager-num-dot">{{ $i + 1 }}</span>
                                <span id="rowLabel{{ $i }}">
                                    @if($convoi->is_garant && $i === 0)
                                        Responsable du groupe (Garant)
                                    @else
                                        Passager {{ $i + 1 }}
                                    @endif
                                </span>
                            </span>
                        </div>
                        <div class="pass-grid-4">
                            <div class="pass-field">
                                <label>Nom <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="passagers[{{ $i }}][nom]" class="pass-input"
                                       value="{{ old("passagers.$i.nom", $p->nom ?? '') }}"
                                       placeholder="Yao" required>
                            </div>
                            <div class="pass-field">
                                <label>Prénoms <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="passagers[{{ $i }}][prenoms]" class="pass-input"
                                       value="{{ old("passagers.$i.prenoms", $p->prenoms ?? '') }}"
                                       placeholder="Kouassi" required>
                            </div>
                            <div class="pass-field">
                                <label>Contact <span style="color:#ef4444;">*</span></label>
                                <input type="tel" name="passagers[{{ $i }}][contact]" class="pass-input"
                                       value="{{ old("passagers.$i.contact", $p->contact ?? '') }}"
                                       placeholder="07xxxxxxxx" required>
                            </div>
                            <div class="pass-field">
                                <label>Contact urgence <span style="color:#ef4444;">*</span></label>
                                <input type="tel" name="passagers[{{ $i }}][contact_urgence]" class="pass-input"
                                       value="{{ old("passagers.$i.contact_urgence", $p->contact_urgence ?? '') }}"
                                       placeholder="05xxxxxxxx" required>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>

                <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                    <button type="submit" class="btn-save-pass">
                        <i class="fas fa-save"></i> Enregistrer les passagers
                    </button>
                </div>
            </form>
        </div>
    </div>

    @else
    {{-- Table lecture seule pour les autres convois ou statuts non-paye --}}
    <div class="section-block pass-section">
        <div class="section-head">
            <div class="icon-circle"><i class="fas fa-users"></i></div>
            <h3>
                @if($convoi->is_garant)
                    Garant du groupe
                @else
                    Passagers ({{ $passagersExistants->count() }} / {{ $convoi->nombre_personnes }})
                @endif
            </h3>
        </div>
        @if($convoi->is_garant && $passagersExistants->count() > 0)
        <div style="padding:14px 20px;">
            <div class="garant-banner" style="display:flex;">
                <i class="fas fa-shield-alt" style="color:#7c3aed;font-size:16px;flex-shrink:0;"></i>
                <span>
                    <strong>{{ $passagersExistants->first()->prenoms ?? '' }} {{ $passagersExistants->first()->nom ?? '' }}</strong>
                    est le garant du groupe de {{ $convoi->nombre_personnes }} personnes.
                </span>
            </div>
        </div>
        @endif
        <div style="overflow-x:auto;">
            <table class="table-clean">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nom</th>
                        <th>Prénoms</th>
                        <th>Contact</th>
                        <th>Contact urgence</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($passagersExistants as $i => $p)
                        <tr>
                            <td><span class="num-pill">{{ $i + 1 }}</span></td>
                            <td>{{ $p->nom ?: '—' }}</td>
                            <td>{{ $p->prenoms ?: '—' }}</td>
                            <td>{{ $p->contact ?: '—' }}</td>
                            <td>{{ $p->contact_urgence ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;padding:36px 20px;color:#94a3b8;font-weight:600;">
                                <i class="fas fa-user-slash mr-2"></i> Aucun passager enregistré.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection

@section('scripts')
@if($convoi->created_by_gare && $convoi->statut === 'paye')
<script>
(function() {
    // Infos du client walk-in pour pré-remplir la première ligne
    const clientInfo = {
        nom:     @json($convoi->client_nom ?? ''),
        prenoms: @json($convoi->client_prenom ?? ''),
        contact: @json($convoi->client_contact ?? ''),
    };

    const garantCheck  = document.getElementById('garantCheck');
    const garantSlider = document.getElementById('garantSlider');
    const garantBanner = document.getElementById('garantBanner');
    const container    = document.getElementById('passagersContainer');

    function getRow(i) {
        return container.querySelector('[data-row="' + i + '"]');
    }
    function getInput(row, field) {
        return row.querySelector('input[name*="[' + field + ']"]');
    }

    function setRowInputsDisabled(row, disabled) {
        row.querySelectorAll('input').forEach(function(input) {
            input.disabled = disabled;
        });
    }

    function applyGarantState(isOn) {
        // Slider visuel
        garantSlider.classList.toggle('on', isOn);
        garantBanner.classList.toggle('hidden', !isOn);

        const rows = container.querySelectorAll('.passager-row');
        rows.forEach(function(row, i) {
            const label = document.getElementById('rowLabel' + i);
            if (isOn) {
                if (i === 0) {
                    row.classList.remove('hidden');
                    setRowInputsDisabled(row, false);
                    if (label) label.textContent = 'Responsable du groupe (Garant)';
                    // Pré-remplir avec infos client si les champs sont vides
                    const nomField     = getInput(row, 'nom');
                    const prenomsField = getInput(row, 'prenoms');
                    const contactField = getInput(row, 'contact');
                    if (nomField && !nomField.value)          nomField.value     = clientInfo.nom;
                    if (prenomsField && !prenomsField.value)  prenomsField.value = clientInfo.prenoms;
                    if (contactField && !contactField.value)  contactField.value = clientInfo.contact;
                } else {
                    row.classList.add('hidden');
                    setRowInputsDisabled(row, true); // désactivés = pas soumis, pas validés
                }
            } else {
                row.classList.remove('hidden');
                setRowInputsDisabled(row, false);
                if (label) label.textContent = 'Passager ' + (i + 1);
            }
        });
    }

    window.toggleGarant = function() {
        const newState = !garantCheck.checked;
        garantCheck.checked = newState;
        applyGarantState(newState);
    };

    // Init au chargement
    applyGarantState(garantCheck.checked);
})();
</script>
@endif

@if(in_array($convoi->statut, ['en_cours', 'termine']))
<script>
document.addEventListener('DOMContentLoaded', function () {
    const endpoint = "{{ route('gare-espace.convois.location', $convoi->id) }}";
    const coordsEl  = document.getElementById('trackingCoords');
    const metaEl    = document.getElementById('trackingMeta');
    const badgeEl   = document.getElementById('trackingStatusBadge');
    const mapLinkEl = document.getElementById('trackingMapLink');

    function updateTracking() {
        fetch(endpoint, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                badgeEl.textContent = (data.statut || '').replace('_', ' ') || '--';
                if (data.latitude !== null && data.longitude !== null) {
                    coordsEl.textContent = 'Position : ' + data.latitude + ', ' + data.longitude;
                    metaEl.textContent = 'Mise à jour : ' + data.last_update + ' \u2022 Chauffeur : ' + data.chauffeur + ' \u2022 Véhicule : ' + data.vehicule;
                    mapLinkEl.href = 'https://www.google.com/maps?q=' + data.latitude + ',' + data.longitude;
                    mapLinkEl.style.display = 'inline-block';
                } else {
                    coordsEl.textContent = 'Position : en attente du GPS chauffeur';
                    metaEl.textContent = 'Mise à jour : ' + data.last_update;
                    mapLinkEl.style.display = 'none';
                }
            }).catch(function(){});
    }

    updateTracking();
    setInterval(updateTracking, 7000);
});
</script>
@endif
@endsection
