<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu Convoi — {{ $convoi->reference }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #1e293b;
            background: #f1f5f9;
        }

        /* ── PAGE ── */
        .page {
            background: #fff;
            margin: 0 auto;
            max-width: 760px;
        }

        /* ─────────────────────────────────────────
           TOP BAND : dark header + orange accent
        ───────────────────────────────────────── */
        .top-band {
            background: #0f172a;
            padding: 0;
        }
        .top-inner {
            display: table;
            width: 100%;
        }
        .top-left {
            display: table-cell;
            vertical-align: middle;
            padding: 22px 28px;
            width: 55%;
        }
        .top-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            padding: 22px 28px;
            background: #ea580c;
            width: 45%;
        }

        .brand-name {
            font-size: 28px;
            font-weight: 900;
            color: #f97316;
            letter-spacing: 2px;
            line-height: 1;
        }
        .brand-sub {
            font-size: 10px;
            color: rgba(255,255,255,0.5);
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .doc-type {
            font-size: 11px;
            font-weight: 900;
            color: rgba(255,255,255,0.7);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 6px;
        }
        .doc-ref {
            font-size: 18px;
            font-weight: 900;
            color: #fff;
            letter-spacing: 1px;
        }
        .doc-date {
            font-size: 10px;
            color: rgba(255,255,255,0.65);
            font-weight: 600;
            margin-top: 4px;
        }

        /* ── ORANGE SEPARATOR ── */
        .orange-sep {
            height: 5px;
            background: #f97316;
        }

        /* ─────────────────────────────────────────
           ROUTE BLOCK
        ───────────────────────────────────────── */
        .route-block {
            background: #fff7ed;
            border-bottom: 1px solid #fed7aa;
            padding: 20px 28px;
            display: table;
            width: 100%;
        }
        .route-left  { display: table-cell; vertical-align: middle; width: 65%; }
        .route-right { display: table-cell; vertical-align: middle; text-align: right; }

        .route-label {
            font-size: 9px;
            font-weight: 900;
            color: #9a3412;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 8px;
        }
        .route-cities {
            font-size: 20px;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.1;
        }
        .route-arrow { color: #f97316; margin: 0 10px; }

        .date-pill {
            display: inline-block;
            background: #0f172a;
            color: #f97316;
            font-size: 10px;
            font-weight: 900;
            padding: 5px 12px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .time-pill {
            display: inline-block;
            background: #fff;
            border: 1px solid #fed7aa;
            color: #92400e;
            font-size: 10px;
            font-weight: 900;
            padding: 4px 10px;
            border-radius: 20px;
            margin-left: 4px;
        }

        /* ─────────────────────────────────────────
           STATUS BAND
        ───────────────────────────────────────── */
        .status-band {
            background: #ecfdf5;
            border-bottom: 1px solid #a7f3d0;
            padding: 10px 28px;
            display: table;
            width: 100%;
        }
        .status-band-left  { display: table-cell; vertical-align: middle; }
        .status-band-right { display: table-cell; vertical-align: middle; text-align: right; }

        .status-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #16a34a;
            margin-right: 6px;
        }
        .status-text {
            font-size: 11px;
            font-weight: 900;
            color: #15803d;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .status-compagnie {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
        }

        /* ─────────────────────────────────────────
           MAIN CONTENT : 2-column table layout
        ───────────────────────────────────────── */
        .main-content {
            padding: 24px 28px;
            display: table;
            width: 100%;
        }
        .col-left  { display: table-cell; vertical-align: top; width: 50%; padding-right: 14px; }
        .col-right { display: table-cell; vertical-align: top; padding-left: 14px; }

        /* Section card */
        .section-card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 14px;
        }
        .section-card-head {
            background: #0f172a;
            padding: 9px 14px;
            display: table;
            width: 100%;
        }
        .section-card-head-title {
            font-size: 9px;
            font-weight: 900;
            color: #f97316;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            display: table-cell;
        }
        .section-card-head-icon {
            display: table-cell;
            text-align: right;
            color: rgba(249,115,22,0.4);
            font-size: 12px;
        }
        .section-card-body { padding: 14px; }

        /* Row inside card */
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .info-row:last-child { margin-bottom: 0; }
        .info-label {
            display: table-cell;
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            width: 38%;
            padding-right: 6px;
            vertical-align: top;
            padding-top: 1px;
        }
        .info-value {
            display: table-cell;
            font-size: 11px;
            font-weight: 900;
            color: #0f172a;
        }

        /* Amount card */
        .amount-card {
            border: 2px solid #f97316;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 14px;
        }
        .amount-card-head {
            background: #f97316;
            padding: 8px 14px;
            font-size: 9px;
            font-weight: 900;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .amount-card-body {
            padding: 18px 14px;
            text-align: center;
            background: #fff7ed;
        }
        .amount-big {
            font-size: 32px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -1px;
            line-height: 1;
        }
        .amount-fcfa {
            font-size: 13px;
            font-weight: 900;
            color: #f97316;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
        }
        .amount-sub {
            font-size: 10px;
            color: #94a3b8;
            font-weight: 700;
            margin-top: 4px;
        }

        /* Walk-in badge */
        .walkin-tag {
            display: inline-block;
            background: #0f172a;
            color: #f97316;
            font-size: 8px;
            font-weight: 900;
            padding: 3px 9px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-top: 6px;
        }

        /* ─────────────────────────────────────────
           LIEU RASSEMBLEMENT — full width
        ───────────────────────────────────────── */
        .lieu-block {
            margin: 0 28px 20px;
            border: 2px solid #f97316;
            border-radius: 10px;
            overflow: hidden;
        }
        .lieu-block-head {
            background: #f97316;
            padding: 9px 16px;
            display: table;
            width: 100%;
        }
        .lieu-block-head-title {
            display: table-cell;
            font-size: 9px;
            font-weight: 900;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .lieu-block-body {
            background: #fff7ed;
            padding: 14px 16px;
            font-size: 13px;
            font-weight: 900;
            color: #0f172a;
        }

        /* ─────────────────────────────────────────
           NOTICE — full width
        ───────────────────────────────────────── */
        .notice {
            margin: 0 28px 20px;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #f97316;
            border-radius: 0 8px 8px 0;
            background: #f8fafc;
            padding: 12px 16px;
            font-size: 11px;
            color: #475569;
            font-weight: 600;
            line-height: 1.7;
        }

        /* ─────────────────────────────────────────
           TICKET TEAR LINE (decorative)
        ───────────────────────────────────────── */
        .tear-line {
            margin: 0 28px 20px;
            border-top: 2px dashed #e2e8f0;
            position: relative;
        }
        .tear-circle-left {
            position: absolute;
            left: -36px;
            top: -9px;
            width: 18px;
            height: 18px;
            background: #f1f5f9;
            border-radius: 50%;
            border: 2px solid #e2e8f0;
        }
        .tear-circle-right {
            position: absolute;
            right: -36px;
            top: -9px;
            width: 18px;
            height: 18px;
            background: #f1f5f9;
            border-radius: 50%;
            border: 2px solid #e2e8f0;
        }

        /* ─────────────────────────────────────────
           FOOTER BAND
        ───────────────────────────────────────── */
        .footer-band {
            background: #0f172a;
            padding: 14px 28px;
            display: table;
            width: 100%;
        }
        .footer-left  { display: table-cell; vertical-align: middle; }
        .footer-right { display: table-cell; vertical-align: middle; text-align: right; }

        .footer-brand {
            font-size: 14px;
            font-weight: 900;
            color: #f97316;
            letter-spacing: 1px;
        }
        .footer-sub {
            font-size: 9px;
            color: rgba(255,255,255,0.4);
            font-weight: 600;
            margin-top: 2px;
        }
        .footer-gen {
            font-size: 9px;
            color: rgba(255,255,255,0.35);
            font-weight: 600;
        }

        /* ── Nb personnes big ── */
        .nb-big {
            font-size: 36px;
            font-weight: 900;
            color: #f97316;
            line-height: 1;
        }
        .nb-sub {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-top: 3px;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- ═══ TOP HEADER ═══ --}}
    <div class="top-band">
        <div class="top-inner">
            <div class="top-left">
                <div class="brand-name">CAR 225</div>
                <div class="brand-sub">Transport inter-urbain · Côte d'Ivoire</div>
            </div>
            <div class="top-right">
                <div class="doc-type">Reçu de Convoi</div>
                <div class="doc-ref">{{ $convoi->reference }}</div>
                <div class="doc-date">Émis le {{ now()->format('d/m/Y') }} à {{ now()->format('H:i') }}</div>
            </div>
        </div>
    </div>
    <div class="orange-sep"></div>

    {{-- ═══ ROUTE ═══ --}}
    <div class="route-block">
        <div class="route-left">
            <div class="route-label">Itinéraire du convoi</div>
            <div class="route-cities">
                {{ $convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? '—') }}
                <span class="route-arrow">→</span>
                {{ $convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? '—') }}
            </div>
        </div>
        <div class="route-right">
            @if($convoi->date_depart)
                <div>
                    <span class="date-pill">Départ · {{ \Carbon\Carbon::parse($convoi->date_depart)->format('d M Y') }}</span>
                    @if($convoi->heure_depart)
                        <span class="time-pill">{{ substr($convoi->heure_depart, 0, 5) }}</span>
                    @endif
                </div>
            @endif
            @if($convoi->date_retour)
                <div style="margin-top:5px;">
                    <span class="date-pill" style="background:#f97316;color:#fff;">Retour · {{ \Carbon\Carbon::parse($convoi->date_retour)->format('d M Y') }}</span>
                    @if($convoi->heure_retour)
                        <span class="time-pill">{{ substr($convoi->heure_retour, 0, 5) }}</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- ═══ STATUS BAND ═══ --}}
    <div class="status-band">
        <div class="status-band-left">
            <span class="status-dot"></span>
            <span class="status-text">Convoi Payé — Confirmé</span>
        </div>
        <div class="status-band-right">
            <span class="status-compagnie">
                Compagnie : <strong>{{ $convoi->compagnie->name ?? '—' }}</strong>
                &nbsp;·&nbsp;
                Gare : <strong>{{ $convoi->gare->nom_gare ?? '—' }}</strong>
            </span>
        </div>
    </div>

    {{-- ═══ MAIN CONTENT (2 colonnes) ═══ --}}
    <div class="main-content">

        {{-- Colonne gauche --}}
        <div class="col-left">

            {{-- Client --}}
            <div class="section-card">
                <div class="section-card-head">
                    <div class="section-card-head-title">Informations Client</div>
                </div>
                <div class="section-card-body">
                    <div class="info-row">
                        <div class="info-label">Nom complet</div>
                        <div class="info-value">{{ $convoi->demandeur_nom }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Téléphone</div>
                        <div class="info-value">{{ $convoi->demandeur_contact ?? '—' }}</div>
                    </div>
                    @php
                        $demandeurEmail = $convoi->client_email ?? ($convoi->user->email ?? null);
                    @endphp
                    @if($demandeurEmail)
                    <div class="info-row">
                        <div class="info-label">E-mail</div>
                        <div class="info-value">{{ $demandeurEmail }}</div>
                    </div>
                    @endif
                    @if($convoi->created_by_gare)
                    <div class="walkin-tag">Enregistrement en gare</div>
                    @endif
                </div>
            </div>

            {{-- Affectation --}}
            <div class="section-card">
                <div class="section-card-head">
                    <div class="section-card-head-title">Affectation</div>
                </div>
                <div class="section-card-body">
                    @if($convoi->chauffeur)
                    <div class="info-row">
                        <div class="info-label">Chauffeur</div>
                        <div class="info-value">{{ trim(($convoi->chauffeur->prenom ?? '') . ' ' . ($convoi->chauffeur->name ?? '')) }}</div>
                    </div>
                    @endif
                    @if($convoi->vehicule)
                    <div class="info-row">
                        <div class="info-label">Véhicule</div>
                        <div class="info-value">{{ $convoi->vehicule->immatriculation }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Capacité</div>
                        <div class="info-value">{{ $convoi->vehicule->nombre_place }} places</div>
                    </div>
                    @endif
                    @if(!$convoi->chauffeur && !$convoi->vehicule)
                    <div style="font-size:10px;color:#94a3b8;font-weight:700;font-style:italic;padding:4px 0;">
                        Affectation en cours — vous serez notifié par SMS
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Colonne droite --}}
        <div class="col-right">

            {{-- Détails du convoi --}}
            <div class="section-card">
                <div class="section-card-head">
                    <div class="section-card-head-title">Détails du Convoi</div>
                </div>
                <div class="section-card-body">
                    <div class="info-row">
                        <div class="info-label">Référence</div>
                        <div class="info-value" style="color:#f97316;">{{ $convoi->reference }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Passagers</div>
                        <div class="info-value">
                            <span class="nb-big">{{ $convoi->nombre_personnes }}</span>
                            <span class="nb-sub">personne(s)</span>
                        </div>
                    </div>
                    @if($convoi->itineraire)
                    <div class="info-row" style="margin-top:8px;">
                        <div class="info-label">Itinéraire</div>
                        <div class="info-value">{{ $convoi->itineraire->point_depart }} → {{ $convoi->itineraire->point_arrive }}</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Montant --}}
            <div class="amount-card">
                <div class="amount-card-head">Montant Total Payé</div>
                <div class="amount-card-body">
                    <div class="amount-big">{{ number_format($convoi->montant, 0, ',', ' ') }}</div>
                    <div class="amount-fcfa">FCFA</div>
                    <div class="amount-sub">Paiement en espèces — Gare {{ $convoi->gare->nom_gare ?? '' }}</div>
                </div>
            </div>

        </div>
    </div>

    {{-- ═══ LIEU DE RASSEMBLEMENT ═══ --}}
    @if($convoi->lieu_rassemblement)
    <div class="lieu-block">
        <div class="lieu-block-head">
            <div class="lieu-block-head-title">📍  Lieu de rassemblement</div>
        </div>
        <div class="lieu-block-body">{{ $convoi->lieu_rassemblement }}</div>
    </div>
    @endif

    {{-- ═══ TEAR LINE ═══ --}}
    <div class="tear-line">
        <div class="tear-circle-left"></div>
        <div class="tear-circle-right"></div>
    </div>

    {{-- ═══ NOTICE ═══ --}}
    <div class="notice">
        <strong style="color:#f97316;">Important :</strong>
        Ce reçu atteste du paiement et de l'enregistrement de votre convoi. Conservez-le et présentez-le
        le jour du départ. Le chauffeur et le véhicule vous seront communiqués
        @if($convoi->demandeur_contact) par SMS au <strong>{{ $convoi->demandeur_contact }}</strong>@endif
        dès l'affectation confirmée.
    </div>

    {{-- ═══ FOOTER ═══ --}}
    <div class="footer-band">
        <div class="footer-left">
            <div class="footer-brand">CAR 225</div>
            <div class="footer-sub">Plateforme de transport inter-urbain — Côte d'Ivoire</div>
        </div>
        <div class="footer-right">
            <div class="footer-gen">Document généré le {{ now()->format('d/m/Y à H:i') }}</div>
            <div class="footer-gen" style="color:rgba(249,115,22,0.6);margin-top:2px;">www.car225.com</div>
        </div>
    </div>

</div>
</body>
</html>
