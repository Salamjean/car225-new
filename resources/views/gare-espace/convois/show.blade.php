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

    /* ── Validate / Refuse panels ─────────────── */
    .validate-section { border-color: #bbf7d0; }
    .validate-section .section-head { background: #f0fdf4; }
    .validate-section .section-head h3 { color: #166534; }
    .validate-section .icon-circle { background: #dcfce7; color: #16a34a; }

    .refuse-section { border-color: #fecaca; }
    .refuse-section .section-head { background: #fef2f2; }
    .refuse-section .section-head h3 { color: #7f1d1d; }
    .refuse-section .icon-circle { background: #fee2e2; color: #dc2626; }

    .btn-validate {
        padding: 11px 24px; border-radius: 12px;
        background: linear-gradient(135deg, #22c55e, #16a34a); color: #fff;
        font-size: 12px; font-weight: 900; text-transform: uppercase;
        letter-spacing: 0.4px; border: none; cursor: pointer; transition: all .2s;
    }
    .btn-validate:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(34,197,94,.3); }

    .btn-refuse {
        padding: 11px 24px; border-radius: 12px;
        background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff;
        font-size: 12px; font-weight: 900; text-transform: uppercase;
        letter-spacing: 0.4px; border: none; cursor: pointer; transition: all .2s;
    }
    .btn-refuse:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(239,68,68,.3); }

    .form-field-label {
        display: block; font-size: 10px; font-weight: 900;
        color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 7px;
    }
    .form-input {
        width: 100%; padding: 11px 14px; border: 1px solid #e2e8f0; border-radius: 12px;
        font-size: 13px; font-weight: 700; color: #334155; background: #f8fafc;
        transition: border-color .2s;
    }
    .form-input:focus { outline: none; border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,.1); }
    .form-textarea { resize: vertical; min-height: 80px; }

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
            @if($convoi->created_by_gare && $convoi->statut === 'paye')
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
            'en_attente' => ['En attente', '#ea580c', '#fff7ed'],
            'valide'     => ['Validé',     '#059669', '#ecfdf5'],
            'confirme'   => ['Confirmé',   '#4f46e5', '#eef2ff'],
            'paye'       => ['Payé',       '#7c3aed', '#f5f3ff'],
            'en_cours'   => ['En cours',   '#0284c7', '#e0f2fe'],
            'termine'    => ['Terminé',    '#059669', '#ecfdf5'],
            'annule'     => ['Annulé',     '#dc2626', '#fef2f2'],
            'refuse'     => ['Refusé',     '#dc2626', '#fef2f2'],
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

    {{-- Lieu de rassemblement retour --}}
    @if($convoi->lieu_rassemblement_retour)
    <div class="alert-box" style="background:#F0FDF4;border:1px solid #bbf7d0;color:#166534;margin-bottom:20px;">
        <i class="fas fa-map-marker-alt" style="font-size:16px;color:#16a34a;"></i>
        <div>
            <strong style="display:block;font-size:13px;margin-bottom:4px;">Lieu de rassemblement (retour)</strong>
            <span style="font-size:14px;font-weight:800;">{{ $convoi->lieu_rassemblement_retour }}</span>
            <span style="display:block;font-size:11px;color:#15803d;margin-top:3px;">Le chauffeur devra passer à ce lieu au retour.</span>
        </div>
    </div>
    @endif

    {{-- ── Motif refus --}}
    @if($convoi->motif_refus && $convoi->statut === 'refuse')
    <div class="alert-box" style="background:#fef2f2;border:1px solid #fecaca;color:#7f1d1d;margin-bottom:20px;">
        <i class="fas fa-ban" style="color:#dc2626;font-size:16px;"></i>
        <div>
            <strong style="display:block;font-size:13px;margin-bottom:4px;">Motif du refus</strong>
            <span style="font-size:12px;">{{ $convoi->motif_refus }}</span>
        </div>
    </div>
    @endif

    {{-- ── Valider / Refuser (uniquement si en_attente) ───────────────── --}}
    @if($convoi->statut === 'en_attente')
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:18px;margin-bottom:24px;">
        {{-- Panel Valider --}}
        <div class="section-block validate-section" style="margin-bottom:0;">
            <div class="section-head">
                <div class="icon-circle"><i class="fas fa-check-circle"></i></div>
                <h3>Valider la demande</h3>
            </div>
            <div class="section-body">
                <p style="font-size:12px;color:#6b7280;font-weight:600;margin-bottom:14px;">
                    Fixez le montant — le demandeur sera notifié par SMS et peut procéder au paiement.
                </p>
                <form action="{{ route('gare-espace.convois.valider', $convoi) }}" method="POST">
                    @csrf
                    <div style="margin-bottom:14px;">
                        <label class="form-field-label">Montant (FCFA) <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="montant" class="form-input" min="100" step="1"
                               value="{{ old('montant') }}" placeholder="Ex : 150000" required
                               style="border-color:#22c55e20;">
                        @error('montant')<span style="font-size:11px;color:#dc2626;margin-top:4px;display:block;">{{ $message }}</span>@enderror
                    </div>
                    <button type="submit" class="btn-validate">
                        <i class="fas fa-paper-plane mr-1"></i> Valider et notifier
                    </button>
                </form>
            </div>
        </div>

        {{-- Panel Refuser --}}
        <div class="section-block refuse-section" style="margin-bottom:0;">
            <div class="section-head">
                <div class="icon-circle"><i class="fas fa-times-circle"></i></div>
                <h3>Refuser la demande</h3>
            </div>
            <div class="section-body">
                <p style="font-size:12px;color:#6b7280;font-weight:600;margin-bottom:14px;">
                    Indiquez le motif. Le demandeur sera notifié par SMS.
                </p>
                <form action="{{ route('gare-espace.convois.refuser', $convoi) }}" method="POST"
                      onsubmit="return confirm('Confirmer le refus de cette demande ?')">
                    @csrf
                    <div style="margin-bottom:14px;">
                        <label class="form-field-label">Raison du refus <span style="color:#ef4444;">*</span></label>
                        <textarea name="motif_refus" class="form-input form-textarea" maxlength="500" required
                                  placeholder="Expliquez la raison du refus...">{{ old('motif_refus') }}</textarea>
                        @error('motif_refus')<span style="font-size:11px;color:#dc2626;margin-top:4px;display:block;">{{ $message }}</span>@enderror
                    </div>
                    <button type="submit" class="btn-refuse">
                        <i class="fas fa-ban mr-1"></i> Confirmer le refus
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Paiement Walk-in (confirme + created_by_gare) ── --}}
    @if($convoi->statut === 'confirme' && $convoi->created_by_gare)
    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:14px;padding:14px 18px;margin-bottom:18px;display:flex;align-items:flex-start;gap:12px;">
        <i class="fas fa-cash-register" style="color:#d97706;font-size:16px;flex-shrink:0;margin-top:2px;"></i>
        <div>
            <div style="font-size:13px;font-weight:900;color:#92400e;margin-bottom:3px;">Convoi créé — Paiement client en attente</div>
            <div style="font-size:12px;font-weight:600;color:#b45309;">
                Le convoi a été enregistré. Cliquez sur <strong>Faire le paiement</strong> dès que le client règle les <strong>{{ number_format($convoi->montant, 0, ',', ' ') }} FCFA</strong> en caisse. Cela débloquera l'affectation et l'impression du ticket.
            </div>
        </div>
    </div>
  {{-- Bouton Faire le paiement --}}
    <div style="background:#ecfdf5;border:1px solid #bbf7d0;border-radius:16px;padding:20px 24px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;">
        <div>
            <div style="font-size:13px;font-weight:900;color:#166534;margin-bottom:4px;"><i class="fas fa-cash-register mr-2" style="color:#16a34a;"></i>Encaisser le paiement</div>
            <div style="font-size:12px;font-weight:600;color:#15803d;">Cliquez sur <strong>Faire le paiement</strong> dès que le client règle en caisse. L'affectation chauffeur/véhicule sera débloquée et le ticket sera imprimable.</div>
        </div>
        @php $montantPayerConfirm = number_format($convoi->montant, 0, ',', ' '); @endphp
        <form action="{{ route('gare-espace.convois.payer-walkin', $convoi) }}" method="POST"
              onsubmit="return confirm('Confirmer la réception du paiement de {{ $montantPayerConfirm }} FCFA pour ce convoi ?')">
            @csrf
            <button type="submit"
                    style="display:inline-flex;align-items:center;gap:8px;padding:13px 28px;border-radius:12px;background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;font-size:13px;font-weight:900;text-transform:uppercase;letter-spacing:0.4px;border:none;cursor:pointer;box-shadow:0 4px 14px rgba(249,115,22,.3);transition:all .2s;">
                <i class="fas fa-cash-register"></i> Faire le paiement — {{ number_format($convoi->montant, 0, ',', ' ') }} FCFA
            </button>
        </form>
    </div>
    {{-- Affectation grisée / verrouillée (walk-in avant paiement) --}}
    <div class="section-block assign-section" style="opacity:.45;pointer-events:none;user-select:none;margin-bottom:18px;">
        <div class="section-head">
            <div class="icon-circle"><i class="fas fa-user-cog"></i></div>
            <h3>Affecter chauffeur & véhicule</h3>
            <span style="margin-left:auto;font-size:10px;font-weight:900;background:#e0e7ff;color:#6366f1;padding:4px 10px;border-radius:8px;text-transform:uppercase;letter-spacing:0.4px;">
                <i class="fas fa-lock mr-1"></i> Disponible après paiement
            </span>
        </div>
        <div class="section-body">
            <div class="assign-row" style="grid-template-columns:1fr 1fr auto;">
                <div><label>Chauffeur</label><select disabled style="background:#f1f5f9;color:#94a3b8;cursor:not-allowed;"><option>-- Disponible après paiement --</option></select></div>
                <div><label>Véhicule</label><select disabled style="background:#f1f5f9;color:#94a3b8;cursor:not-allowed;"><option>-- Disponible après paiement --</option></select></div>
                <div><label>&nbsp;</label><button type="button" class="btn-assign" disabled style="opacity:.5;cursor:not-allowed;"><i class="fas fa-lock mr-1"></i> Affecter</button></div>
            </div>
        </div>
    </div>

  
    @endif

    {{-- ── Solder (uniquement si confirme, non walk-in) ── --}}
    @if($convoi->statut === 'confirme' && !$convoi->created_by_gare)

    {{-- Bandeau info + affectation grisée --}}
    <div style="background:#eef2ff;border:1px solid #c7d2fe;border-radius:14px;padding:14px 18px;margin-bottom:18px;display:flex;align-items:flex-start;gap:12px;">
        <i class="fas fa-user-check" style="color:#4f46e5;font-size:16px;flex-shrink:0;margin-top:2px;"></i>
        <div>
            <div style="font-size:13px;font-weight:900;color:#3730a3;margin-bottom:3px;">Client a accepté le montant — paiement physique en attente</div>
            <div style="font-size:12px;font-weight:600;color:#4f46e5;">
                <strong>{{ $convoi->demandeur_nom }}</strong> a confirmé le montant de <strong>{{ number_format($convoi->montant, 0, ',', ' ') }} FCFA</strong>.
                Cliquez sur <strong>Solder</strong> dès réception du paiement en gare. L'affectation du chauffeur et du véhicule sera disponible après encaissement.
            </div>
        </div>
    </div>
  {{-- Bouton Solder --}}
    <div style="background:#ecfdf5;border:1px solid #bbf7d0;border-radius:16px;padding:20px 24px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;">
        <div>
            <div style="font-size:13px;font-weight:900;color:#166534;margin-bottom:4px;"><i class="fas fa-money-bill-wave mr-2" style="color:#16a34a;"></i>Encaissement en gare</div>
            <div style="font-size:12px;font-weight:600;color:#15803d;">Cliquez sur <strong>Solder</strong> dès que vous avez reçu le paiement du client. L'affectation chauffeur/véhicule sera débloquée.</div>
        </div>
        @php $montantSolderConfirm = number_format($convoi->montant, 0, ',', ' '); @endphp
        <form action="{{ route('gare-espace.convois.solder', $convoi) }}" method="POST"
              onsubmit="return confirm('Confirmer la réception du paiement de {{ $montantSolderConfirm }} FCFA pour ce convoi ?')">
            @csrf
            <button type="submit"
                    style="display:inline-flex;align-items:center;gap:8px;padding:13px 28px;border-radius:12px;background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff;font-size:13px;font-weight:900;text-transform:uppercase;letter-spacing:0.4px;border:none;cursor:pointer;box-shadow:0 4px 14px rgba(34,197,94,.3);transition:all .2s;">
                <i class="fas fa-hand-holding-usd"></i> Solder — {{ number_format($convoi->montant, 0, ',', ' ') }} FCFA
            </button>
        </form>
    </div>
    {{-- Affectation grisée / verrouillée (lecture seule) --}}
    <div class="section-block assign-section" style="opacity:.45;pointer-events:none;user-select:none;margin-bottom:18px;">
        <div class="section-head">
            <div class="icon-circle"><i class="fas fa-user-cog"></i></div>
            <h3>Affecter chauffeur & véhicule</h3>
            <span style="margin-left:auto;font-size:10px;font-weight:900;background:#e0e7ff;color:#6366f1;padding:4px 10px;border-radius:8px;text-transform:uppercase;letter-spacing:0.4px;">
                <i class="fas fa-lock mr-1"></i> Disponible après encaissement
            </span>
        </div>
        <div class="section-body">
            <div class="assign-row" style="grid-template-columns:1fr 1fr auto;">
                <div>
                    <label>Chauffeur</label>
                    <select disabled style="background:#f1f5f9;color:#94a3b8;cursor:not-allowed;">
                        <option>-- Disponible après encaissement --</option>
                    </select>
                </div>
                <div>
                    <label>Véhicule</label>
                    <select disabled style="background:#f1f5f9;color:#94a3b8;cursor:not-allowed;">
                        <option>-- Disponible après encaissement --</option>
                    </select>
                </div>
                <div>
                    <label>&nbsp;</label>
                    <button type="button" class="btn-assign" disabled style="opacity:.5;cursor:not-allowed;">
                        <i class="fas fa-lock mr-1"></i> Affecter
                    </button>
                </div>
            </div>
        </div>
    </div>

  
    @endif

    {{-- ── Affectation + Passagers (paye) ──── --}}
    @if($convoi->statut === 'paye')
    @php
        $gareId = Auth::guard('gare')->id();

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

    @if($convoi->created_by_gare && !$convoi->passagers_soumis)
    {{-- ════════════════════════════════════════════════════════════════════
         WALK-IN + PASSAGERS NON SOUMIS : formulaire unifié — 1 seul bouton
         ════════════════════════════════════════════════════════════════════ --}}
    <form action="{{ route('gare-espace.convois.save-full', $convoi) }}" method="POST" id="saveFullForm">
        @csrf

        {{-- Affectation (sans bouton propre) --}}
        <div class="section-block assign-section" style="margin-bottom:16px;">
            <div class="section-head">
                <div class="icon-circle"><i class="fas fa-user-cog"></i></div>
                <h3>@if($convoi->personnel_id) Modifier l'affectation @else Affecter chauffeur & véhicule @endif</h3>
                @if($convoi->personnel_id)
                    <span style="margin-left:auto;font-size:10px;font-weight:900;background:#e0e7ff;color:#4338ca;padding:4px 10px;border-radius:8px;text-transform:uppercase;letter-spacing:0.4px;">
                        <i class="fas fa-check-circle mr-1"></i> Affecté
                    </span>
                @else
                    <span style="margin-left:auto;font-size:10px;font-weight:700;color:#94a3b8;">Optionnel — vous pouvez enregistrer sans affecter</span>
                @endif
            </div>
            <div class="section-body">
                <div class="assign-row" style="grid-template-columns:1fr 1fr;">
                    <div>
                        <label>Chauffeur</label>
                        <select name="personnel_id">
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
                        <label>Véhicule</label>
                        <select name="vehicule_id">
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
                </div>
            </div>
        </div>

        {{-- Passagers / Garant (sans bouton propre) --}}
        <div class="section-block pass-section" style="margin-bottom:16px;">
            <div class="section-head">
                <div class="icon-circle"><i class="fas fa-users"></i></div>
                <h3>Passagers du convoi</h3>
            </div>
            <div class="section-body">
                {{-- Toggle garant --}}
                <div class="garant-toggle-wrap" onclick="toggleGarant()">
                    <input type="checkbox" id="garantCheck" name="is_garant" value="1"
                           {{ $convoi->is_garant ? 'checked' : '' }}>
                    <div class="garant-slider {{ $convoi->is_garant ? 'on' : '' }}" id="garantSlider"></div>
                    <div>
                        <div class="garant-text-title">
                            <i class="fas fa-user-shield mr-1"></i> Le client se porte garant pour le groupe
                        </div>
                        <div class="garant-text-sub" id="garantSubText">
                            @if($convoi->is_garant)
                                Seules ses informations personnelles seront enregistrées.
                            @else
                                Un lien sécurisé sera envoyé au client pour saisir la liste des passagers.
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Bannière garant --}}
                <div class="garant-banner {{ $convoi->is_garant ? '' : 'hidden' }}" id="garantBanner">
                    <i class="fas fa-shield-alt" style="color:#7c3aed;font-size:16px;flex-shrink:0;"></i>
                    <span>Mode garant activé — le client représente l'ensemble du groupe. L'affectation peut être faite immédiatement.</span>
                </div>

                {{-- Bannière lien --}}
                <div id="linkBanner" class="{{ $convoi->is_garant ? 'hidden' : '' }}"
                     style="display:{{ $convoi->is_garant ? 'none' : 'flex' }};align-items:flex-start;gap:12px;padding:14px 18px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:14px;">
                    <i class="fas fa-paper-plane" style="color:#3b82f6;font-size:16px;flex-shrink:0;margin-top:2px;"></i>
                    <div>
                        <div style="font-size:13px;font-weight:900;color:#1e40af;margin-bottom:3px;">Envoi d'un lien au client</div>
                        <div style="font-size:12px;font-weight:600;color:#3b82f6;line-height:1.5;">
                            Un SMS (et email si disponible) sera envoyé à <strong>{{ $convoi->client_contact }}</strong> avec un lien sécurisé pour que le client renseigne lui-même la liste des {{ $convoi->nombre_personnes }} passagers.
                        </div>
                        @if($convoi->passenger_form_token)
                        <div style="margin-top:6px;font-size:11px;color:#94a3b8;font-weight:600;">
                            <i class="fas fa-redo mr-1 text-orange-400"></i> Un lien a déjà été envoyé — un nouveau lien sera généré et envoyé.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── BOUTON UNIQUE ─── --}}
        <div style="display:flex;justify-content:flex-end;margin-bottom:24px;">
            <button type="submit" id="saveFullBtn" class="btn-save-pass"
                    style="padding:14px 40px;font-size:14px;gap:10px;border-radius:14px;">
                <i class="fas fa-save" id="saveFullIcon"></i>
                <span id="saveFullLabel">Enregistrer</span>
            </button>
        </div>
    </form>

    {{-- Annuler l'affectation (en dehors du formulaire principal) --}}
    @if($convoi->personnel_id)
    <div style="margin-bottom:20px;">
        <form action="{{ route('gare-espace.convois.unassign', $convoi) }}" method="POST"
              onsubmit="return confirm('Annuler l\'affectation ? Le chauffeur et le véhicule seront libérés.')">
            @csrf
            <button type="submit" class="btn-unassign">
                <i class="fas fa-times-circle"></i> Annuler l'affectation et reprogrammer
            </button>
        </form>
    </div>
    @endif

    {{-- Lien déjà envoyé : copier / renvoyer (en dehors du formulaire principal) --}}
    @if(!$convoi->is_garant && $convoi->passenger_form_token)
    <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:14px;padding:16px 18px;margin-bottom:20px;display:flex;align-items:flex-start;gap:12px;">
        <i class="fas fa-clock" style="color:#f97316;font-size:16px;flex-shrink:0;margin-top:2px;"></i>
        <div style="flex:1;min-width:0;">
            <div style="font-size:13px;font-weight:900;color:#92400e;margin-bottom:4px;">En attente de la liste passagers</div>
            <div style="font-size:12px;font-weight:600;color:#b45309;margin-bottom:10px;">Le client n'a pas encore soumis la liste. Le lien a été envoyé au {{ $convoi->client_contact }}.</div>
            @php $lienPassager = route('public.convoi.passagers.form', $convoi->passenger_form_token); @endphp
            <div style="display:flex;align-items:center;gap:8px;background:#fff;border:1px solid #fed7aa;border-radius:10px;padding:8px 12px;margin-bottom:10px;">
                <i class="fas fa-link" style="color:#f97316;font-size:11px;flex-shrink:0;"></i>
                <span style="font-size:11px;color:#92400e;font-weight:700;word-break:break-all;flex:1;" id="lienPassagerText">{{ $lienPassager }}</span>
                <button type="button" onclick="copyLink()" style="flex-shrink:0;padding:5px 10px;border-radius:7px;background:#f97316;color:#fff;border:none;font-size:10px;font-weight:900;cursor:pointer;transition:background .2s;" id="copyBtn">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>
    </div>
    @endif

    @else
    {{-- ════════════════════════════════════════════════════════════════════
         CAS STANDARD (non walk-in ou passagers déjà soumis) : formulaires séparés
         ════════════════════════════════════════════════════════════════════ --}}

    {{-- Avertissement si 0 passager et pas garant (non walk-in) --}}
    @php $passagersCount = $convoi->passagers->count(); @endphp
    @if(!$convoi->is_garant && $passagersCount === 0 && !$convoi->personnel_id)
    <div class="alert-box" style="background:#fffbeb;border:1px solid #fde68a;color:#92400e;margin-bottom:16px;">
        <i class="fas fa-exclamation-triangle" style="color:#d97706;font-size:16px;flex-shrink:0;"></i>
        <div>
            <strong style="display:block;font-size:13px;margin-bottom:3px;">Aucun passager enregistré</strong>
            <span style="font-size:12px;font-weight:600;">Au moins 1 passager doit être enregistré (ou le client doit choisir le mode garant) avant de pouvoir affecter un chauffeur.</span>
        </div>
    </div>
    @elseif(!$convoi->is_garant && !$convoi->passagers_soumis && $passagersCount > 0 && !$convoi->personnel_id)
    <div class="alert-box" style="background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af;margin-bottom:16px;">
        <i class="fas fa-info-circle" style="color:#3b82f6;font-size:16px;flex-shrink:0;"></i>
        <div>
            <strong style="display:block;font-size:13px;margin-bottom:3px;">{{ $passagersCount }} / {{ $convoi->nombre_personnes }} passager(s) enregistré(s)</strong>
            <span style="font-size:12px;font-weight:600;">La liste n'est pas encore complète. Vous pouvez affecter dès maintenant ou attendre que tous les passagers soient enregistrés.</span>
        </div>
    </div>
    @endif

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
                        @php $assignBlocked = !$convoi->is_garant && $passagersCount === 0; @endphp
                        <button type="submit" class="btn-assign"
                            {{ $assignBlocked ? 'disabled' : '' }}
                            @if($assignBlocked) title="Au moins 1 passager doit être enregistré avant d'affecter" @endif
                            style="{{ $assignBlocked ? 'opacity:.45;cursor:not-allowed;pointer-events:none;filter:grayscale(.4);' : '' }}">
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
    @endif {{-- end created_by_gare && !passagers_soumis --}}

    @endif {{-- end statut=paye --}}

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

    {{-- Section passagers walk-in (paye, passagers déjà soumis) --}}
    @if($convoi->created_by_gare && $convoi->statut === 'paye' && $convoi->passagers_soumis)
    <div class="section-block pass-section">
        <div class="section-head">
            <div class="icon-circle"><i class="fas fa-users"></i></div>
            <h3>
                @if($convoi->is_garant)
                    Garant du groupe
                @else
                    Passagers <span style="font-size:11px;color:#059669;margin-left:6px;">✓ soumis par le client</span>
                @endif
            </h3>
        </div>
        <div class="section-body">
            {{-- Passagers soumis : afficher en lecture seule --}}
            @if($passagersExistants->count() > 0)
            <div style="background:#ecfdf5;border:1px solid #bbf7d0;border-radius:14px;padding:12px 16px;margin-bottom:14px;display:flex;align-items:center;gap:10px;">
                <i class="fas fa-check-circle" style="color:#059669;font-size:16px;"></i>
                <div style="font-size:13px;font-weight:700;color:#065f46;">
                    @if($convoi->is_garant)
                        <strong>{{ $passagersExistants->first()->prenoms ?? '' }} {{ $passagersExistants->first()->nom ?? '' }}</strong>
                        est le garant du groupe de {{ $convoi->nombre_personnes }} personnes.
                    @else
                        Le client a soumis la liste — {{ $passagersExistants->count() }} passager(s) enregistré(s).
                    @endif
                </div>
            </div>
            @if(!$convoi->is_garant)
            <div style="overflow-x:auto;">
                <table class="table-clean">
                    <thead>
                        <tr>
                            <th>#</th><th>Nom</th><th>Prénoms</th><th>Contact</th><th>Urgence</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($passagersExistants as $i => $p)
                        <tr>
                            <td><span class="num-pill">{{ $i + 1 }}</span></td>
                            <td>{{ $p->nom ?: '—' }}</td>
                            <td>{{ $p->prenoms ?: '—' }}</td>
                            <td>{{ $p->contact ?: '—' }}</td>
                            <td>{{ $p->contact_urgence ?: '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
            @endif
        </div>
    </div>

    @elseif(!$convoi->created_by_gare || $convoi->statut !== 'paye')
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
@if($convoi->created_by_gare && $convoi->statut === 'paye' && !$convoi->passagers_soumis)
<script>
(function() {
    const garantCheck  = document.getElementById('garantCheck');
    const garantSlider = document.getElementById('garantSlider');
    const garantBanner = document.getElementById('garantBanner');
    const linkBanner   = document.getElementById('linkBanner');
    const subText      = document.getElementById('garantSubText');

    function applyGarantState(isOn) {
        if (garantSlider) garantSlider.classList.toggle('on', isOn);
        if (garantBanner) garantBanner.classList.toggle('hidden', !isOn);
        if (linkBanner)   linkBanner.style.display = isOn ? 'none' : 'flex';
        if (subText) {
            subText.textContent = isOn
                ? 'Seules ses informations personnelles seront enregistrées.'
                : 'Un lien sécurisé sera envoyé au client pour saisir la liste des passagers.';
        }
    }

    window.toggleGarant = function() {
        const newState = !garantCheck.checked;
        garantCheck.checked = newState;
        applyGarantState(newState);
    };

    // Spinner sur le bouton unique lors de la soumission
    const form = document.getElementById('saveFullForm');
    const btn  = document.getElementById('saveFullBtn');
    const icon = document.getElementById('saveFullIcon');
    const lbl  = document.getElementById('saveFullLabel');
    if (form && btn) {
        form.addEventListener('submit', function() {
            btn.disabled = true;
            if (icon) icon.className = 'fas fa-spinner fa-spin';
            if (lbl)  lbl.textContent = 'Enregistrement…';
        });
    }

    applyGarantState(garantCheck ? garantCheck.checked : false);
})();

function copyLink() {
    const text = document.getElementById('lienPassagerText');
    const btn  = document.getElementById('copyBtn');
    if (!text) return;
    navigator.clipboard.writeText(text.textContent.trim()).then(function() {
        btn.innerHTML = '<i class="fas fa-check"></i>';
        btn.style.background = '#059669';
        setTimeout(function() {
            btn.innerHTML = '<i class="fas fa-copy"></i>';
            btn.style.background = '#f97316';
        }, 2000);
    }).catch(function() {
        const el = document.createElement('textarea');
        el.value = text.textContent.trim();
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        btn.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(function() { btn.innerHTML = '<i class="fas fa-copy"></i>'; }, 2000);
    });
}
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
