<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu Convoi — {{ $convoi->reference }}</title>
    <style>
        @page {
            margin: 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #334155;
            background: #ffffff;
            line-height: 1.4;
        }

        .container {
            padding: 30px;
        }

        /* ── HEADER ── */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 15px;
        }
        .header-left {
            display: table-cell;
            vertical-align: middle;
        }
        .header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }
        .logo-placeholder {
            font-size: 24px;
            font-weight: 800;
            color: #e94e1a;
            letter-spacing: -1px;
        }
        .ref-box {
            background: #fff7ed;
            border: 1px solid #ffedd5;
            padding: 8px 12px;
            border-radius: 8px;
            display: inline-block;
        }
        .ref-label {
            font-size: 9px;
            text-transform: uppercase;
            font-weight: 700;
            color: #9a3412;
            display: block;
        }
        .ref-value {
            font-size: 14px;
            font-weight: 800;
            color: #1e293b;
        }

        /* ── MAIN GRID ── */
        .main-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .grid-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .grid-col-left {
            padding-right: 15px;
        }
        .grid-col-right {
            padding-left: 15px;
        }

        .section-title {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 1px;
            margin-bottom: 10px;
            border-left: 3px solid #e94e1a;
            padding-left: 8px;
        }

        .card {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 15px;
        }

        .data-row {
            margin-bottom: 6px;
        }
        .data-row:last-child { margin-bottom: 0; }
        .data-label {
            font-size: 9px;
            color: #94a3b8;
            font-weight: 600;
            display: block;
            margin-bottom: 1px;
        }
        .data-value {
            font-size: 11px;
            font-weight: 700;
            color: #1e293b;
        }

        /* ── ITINERARY ── */
        .itinerary-card {
            background: linear-gradient(to right, #ffffff, #fff7ed);
            border: 1px solid #fed7aa;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .itinerary-cities {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
        }
        .city-arrow {
            color: #e94e1a;
            margin: 0 8px;
        }
        .itinerary-details {
            margin-top: 10px;
            display: table;
            width: 100%;
        }
        .itinerary-item {
            display: table-cell;
            width: 33%;
        }

        /* ── AMOUNT ── */
        .amount-section {
            text-align: right;
            margin-bottom: 20px;
        }
        .amount-label {
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
        }
        .amount-value {
            font-size: 28px;
            font-weight: 900;
            color: #e94e1a;
        }
        .amount-currency {
            font-size: 14px;
            font-weight: 700;
            color: #e94e1a;
        }

        /* ── QR / FOOTER ── */
        .footer-note {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #004a29;
            padding: 12px;
            border-radius: 4px;
            font-size: 10px;
            color: #475569;
            margin-bottom: 20px;
        }

        .footer {
            border-top: 1px solid #f1f5f9;
            padding-top: 15px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
        }

        .nb-passengers {
            background: #0f172a;
            color: #ffffff;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
        }
    </style>
</head>
<body>
<div class="container">

    {{-- HEADER --}}
    <div class="header">
        <div class="header-left">
            <div class="logo-placeholder">CAR 225</div>
            <div style="font-size: 9px; color: #94a3b8; font-weight: 600;">TRANS-URBAIN · CÔTE D'IVOIRE</div>
        </div>
        <div class="header-right">
            <div class="ref-box">
                <span class="ref-label">Référence Convoi</span>
                <span class="ref-value">{{ $convoi->reference }}</span>
            </div>
            <div style="margin-top: 5px; font-size: 9px; color: #64748b;">
                Généré le {{ now()->format('d/m/Y à H:i') }}
            </div>
        </div>
    </div>

    {{-- ITINERARY --}}
    <div class="itinerary-card">
        <div class="itinerary-cities">
            {{ $convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? '—') }}
            <span class="city-arrow">→</span>
            {{ $convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? '—') }}
        </div>
        <div class="itinerary-details">
            <div class="itinerary-item">
                <span class="data-label">Date Départ</span>
                <span class="data-value">{{ $convoi->date_depart ? \Carbon\Carbon::parse($convoi->date_depart)->format('d/m/Y') : '—' }}</span>
                @if($convoi->heure_depart)
                    <span style="color:#e94e1a; font-weight: 800;">· {{ substr($convoi->heure_depart, 0, 5) }}</span>
                @endif
            </div>
            <div class="itinerary-item">
                <span class="data-label">Passagers</span>
                <span class="nb-passengers">{{ $convoi->nombre_personnes }} PERS.</span>
            </div>
            <div class="itinerary-item" style="text-align: right;">
                <span class="data-label">Compagnie</span>
                <span class="data-value">{{ $convoi->compagnie->name ?? '—' }}</span>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="main-grid">
        {{-- Client Info --}}
        <div class="grid-col grid-col-left">
            <h3 class="section-title">Client</h3>
            <div class="card">
                <div class="data-row">
                    <span class="data-label">Nom complet</span>
                    <span class="data-value">{{ $convoi->demandeur_nom }}</span>
                </div>
                <div class="data-row">
                    <span class="data-label">Contact</span>
                    <span class="data-value">{{ $convoi->demandeur_contact ?? '—' }}</span>
                </div>
                @php
                    $demandeurEmail = $convoi->client_email ?? ($convoi->user->email ?? null);
                @endphp
                @if($demandeurEmail)
                <div class="data-row">
                    <span class="data-label">E-mail</span>
                    <span class="data-value">{{ $demandeurEmail }}</span>
                </div>
                @endif
            </div>

            @if($convoi->lieu_rassemblement)
            <h3 class="section-title">Rassemblement</h3>
            <div class="card" style="background: #fdf2f2; border-color: #fecaca;">
                <div class="data-row">
                    <span class="data-label">Lieu</span>
                    <span class="data-value" style="color: #b91c1c;">📍 {{ $convoi->lieu_rassemblement }}</span>
                </div>
            </div>
            @endif
        </div>

        {{-- Assignment Info --}}
        <div class="grid-col grid-col-right">
            <h3 class="section-title">Affectation</h3>
            <div class="card">
                @if($convoi->chauffeur)
                <div class="data-row">
                    <span class="data-label">Chauffeur</span>
                    <span class="data-value">{{ trim(($convoi->chauffeur->prenom ?? '') . ' ' . ($convoi->chauffeur->name ?? '')) }}</span>
                </div>
                <div class="data-row">
                    <span class="data-label">Contact Chauffeur</span>
                    <span class="data-value">{{ $convoi->chauffeur->contact ?? '—' }}</span>
                </div>
                @endif
                
                @if($convoi->vehicule)
                <div class="data-row">
                    <span class="data-label">Véhicule</span>
                    <span class="data-value">{{ $convoi->vehicule->immatriculation }}</span>
                </div>
                <div class="data-row">
                    <span class="data-label">Capacité</span>
                    <span class="data-value">{{ $convoi->vehicule->nombre_place }} places</span>
                </div>
                @endif

                @if(!$convoi->chauffeur && !$convoi->vehicule)
                <div style="text-align: center; padding: 10px 0;">
                    <span style="font-size: 9px; color: #94a3b8; font-style: italic;">Affectation en attente...</span>
                </div>
                @endif
            </div>

            <div class="amount-section">
                <span class="amount-label">Total Payé</span><br>
                <span class="amount-value">{{ number_format($convoi->montant, 0, ',', ' ') }}</span>
                <span class="amount-currency">FCFA</span>
                <div style="font-size: 8px; color: #94a3b8; margin-top: -5px;">Paiement enregistré à la gare</div>
            </div>
        </div>
    </div>

    {{-- FOOTER NOTE --}}
    <div class="footer-note">
        <strong>À SAVOIR :</strong> Ce document confirme votre réservation. En cas de modification d'affectation (Chauffeur/Véhicule), vous recevrez une notification par SMS. Veuillez vous présenter au lieu de rassemblement 15 minutes avant l'heure indiquée.
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        Délivré par CAR 225 · www.car225.com · Côte d'Ivoire<br>
        Merci de votre confiance. Bon voyage !
    </div>

</div>
</body>
</html>
