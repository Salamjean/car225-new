<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Billet de voyage - {{ $reservation->reference }}</title>
    <style>
        @page {
            margin: 30px;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            line-height: 1.4;
            color: #1f2937;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            font-size: 13px;
        }

        .ticket-container {
            width: 100%;
            background: #ffffff;
            position: relative;
        }

        /* En-tête (Header) */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .header-title-section {
            vertical-align: top;
            text-align: left;
        }

        .ticket-main-title {
            font-size: 22px;
            font-weight: 800;
            color: #02245b;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ticket-warning-sub {
            font-size: 10px;
            font-weight: 700;
            color: #dc2626;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .header-logo {
            text-align: right;
            vertical-align: top;
        }

        .header-logo-text {
            font-size: 24px;
            font-weight: 800;
            color: #02245b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }

        .header-subtitle {
            font-size: 11px;
            color: #6b7280;
            margin-top: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Section Métadonnées Réservation */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 15px;
        }

        .meta-cell {
            padding: 8px 0;
            width: 25%;
            vertical-align: top;
        }

        .meta-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 3px;
            letter-spacing: 0.5px;
        }

        .meta-value {
            font-size: 13px;
            font-weight: 700;
            color: #1f2937;
        }

        .reference-badge {
            background-color: #10b981;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 700;
        }

        /* Section Itinéraire & Trajet */
        .journey-card {
            border-top: 2px solid #02245b;
            border-bottom: 2px solid #02245b;
            padding: 20px 0;
            margin-bottom: 30px;
        }

        .journey-table {
            width: 100%;
            border-collapse: collapse;
        }

        .city-cell {
            width: 35%;
            vertical-align: middle;
        }

        .city-title {
            font-size: 12px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .city-code {
            font-size: 32px;
            font-weight: 800;
            color: #02245b;
            margin: 0;
            line-height: 1.1;
        }

        .arrow-cell {
            width: 30%;
            text-align: center;
            vertical-align: middle;
        }

        /* Grille des détails du voyage */
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .grid-cell {
            text-align: left;
            vertical-align: top;
            width: 20%;
        }

        .grid-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .grid-value {
            font-size: 13px;
            font-weight: 700;
            color: #1f2937;
        }

        .grid-value.highlight-seat {
            color: #e94f1b;
            font-size: 15px;
        }

        .grid-value.highlight-time {
            color: #e94f1b;
        }

        /* Section Basse en Double Colonne */
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-column {
            width: 55%;
            vertical-align: top;
            padding-right: 30px;
        }

        .card-column {
            width: 45%;
            vertical-align: top;
        }

        /* Colonne Gauche: Instructions importantes */
        .info-title {
            font-size: 13px;
            font-weight: 700;
            color: #02245b;
            margin: 0 0 12px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
        }

        .info-subtitle {
            font-size: 11px;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 6px 0;
            text-transform: uppercase;
        }

        .info-text {
            font-size: 11px;
            color: #4b5563;
            line-height: 1.5;
            margin: 0 0 15px 0;
        }

        .info-list {
            margin: 0 0 20px 0;
            padding-left: 15px;
        }

        .info-list li {
            font-size: 11px;
            color: #4b5563;
            margin-bottom: 6px;
            line-height: 1.4;
        }

        .warning-box {
            background-color: #fee2e2;
            border: 1px solid #fca5a5;
            border-radius: 6px;
            padding: 10px 12px;
            margin-top: 10px;
        }

        .warning-title {
            font-weight: 700;
            color: #dc2626;
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .warning-text {
            font-size: 10px;
            color: #991b1b;
            margin: 0;
            line-height: 1.3;
        }

        /* Colonne Droite: QR Code & Montant */
        .qr-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 18px;
            text-align: center;
        }

        .qr-card-header {
            background-color: #02245b;
            color: #ffffff;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
        }

        .qr-image-container {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 8px;
            border-radius: 6px;
            display: inline-block;
            width: 130px;
            height: 130px;
        }

        .qr-img {
            width: 130px;
            height: 130px;
            display: block;
        }

        .qr-placeholder {
            width: 130px;
            height: 130px;
            background: #f3f4f6;
            border: 2px dashed #d1d5db;
            border-radius: 6px;
            font-size: 11px;
            color: #6b7280;
            text-align: center;
            line-height: 130px;
        }

        .qr-text {
            font-size: 10px;
            color: #6b7280;
            margin: 10px 0 15px 0;
        }

        /* Section Montant */
        .amount-container {
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
            text-align: center;
        }

        .amount-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .amount-val {
            font-size: 22px;
            font-weight: 800;
            color: #10b981;
        }

        .amount-sub {
            font-size: 9px;
            color: #6b7280;
            margin-top: 2px;
            line-height: 1.2;
        }

        /* Pied de page (Footer) */
        .footer-section {
            margin-top: 40px;
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
            text-align: center;
            color: #9ca3af;
            font-size: 9px;
        }

        .footer-section p {
            margin: 2px 0;
        }
    </style>
</head>

<body>
    @php
        // Génération automatique des codes de ville de style aéroport
        $villesCodes = [
            'abidjan' => 'ABJ',
            'bouake' => 'BKE',
            'bouaké' => 'BKE',
            'yamoussoukro' => 'YAK',
            'san-pedro' => 'SPY',
            'san pedro' => 'SPY',
            'korhogo' => 'KGO',
            'daloa' => 'DLO',
            'man' => 'MXX',
            'gagnoa' => 'GGN',
            'odienne' => 'ODN',
            'odienné' => 'ODN',
        ];
        
        $depNormalized = strtolower(preg_replace('/[^a-zA-Z]/', '', $programme->point_depart));
        $arrNormalized = strtolower(preg_replace('/[^a-zA-Z]/', '', $programme->point_arrive));
        
        $codeDepart = $villesCodes[$depNormalized] ?? strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $programme->point_depart), 0, 3));
        $codeArrive = $villesCodes[$arrNormalized] ?? strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $programme->point_arrive), 0, 3));

        // Chargement du logo en Base64 — seule méthode fiable avec DomPDF
        // (les chemins locaux et URLs distantes sont bloqués selon le contexte serveur)
        $logoBase64 = null;
        if (!empty($compagnie->path_logo)) {
            $pathsToTry = [
                storage_path('app/public/' . $compagnie->path_logo),
                public_path('storage/' . $compagnie->path_logo),
                public_path($compagnie->path_logo),
            ];
            foreach ($pathsToTry as $path) {
                if (file_exists($path)) {
                    try {
                        $logoData = file_get_contents($path);
                        if ($logoData !== false) {
                            $logoMime = mime_content_type($path) ?: 'image/png';
                            $logoBase64 = 'data:' . $logoMime . ';base64,' . base64_encode($logoData);
                        }
                    } catch (\Exception $e) {
                        // Continuer avec le prochain chemin
                    }
                    break;
                }
            }
        }
    @endphp

    <div class="ticket-container">
        <!-- En-tête -->
        <table class="header-table">
            <tr>
                <td class="header-title-section">
                    <h1 class="ticket-main-title">Billet de Voyage</h1>
                    <p class="ticket-warning-sub">Attention, conservez ce document pour l'embarquement.</p>
                </td>
                <td class="header-logo">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" style="max-height: 55px; max-width: 180px; display: block; margin-left: auto;" alt="{{ $compagnie->name ?? '' }}">
                        @if($compagnie->name)
                            <div class="header-subtitle" style="margin-top: 5px;">{{ $compagnie->name }}</div>
                        @endif
                    @else
                        <div class="header-logo-text">
                            {{ $compagnie->name ?? 'CAR 225' }}
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Métadonnées Réservation -->
        <table class="meta-table">
            <tr>
                <td class="meta-cell">
                    <div class="meta-label">Nom du passager</div>
                    <div class="meta-value">
                        {{ strtoupper($reservation->passager_nom ?? $reservation->user->name ?? 'PASSAGER') }} {{ strtoupper($reservation->passager_prenom ?? '') }}
                    </div>
                </td>
                <td class="meta-cell">
                    <div class="meta-label">Référence réservation</div>
                    <div class="meta-value">
                        <span class="reference-badge">{{ $reservation->reference }}</span>
                    </div>
                </td>
                <td class="meta-cell">
                    <div class="meta-label">N° de Téléphone</div>
                    <div class="meta-value">
                        {{ $reservation->passager_telephone ?? $reservation->user->telephone ?? 'Non renseigné' }}
                    </div>
                </td>
                <td class="meta-cell">
                    <div class="meta-label">Date d'émission</div>
                    <div class="meta-value">
                        {{ date('d/m/Y') }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- Section Itinéraire & Trajet (Axe Central) -->
        <div class="journey-card">
            <table class="journey-table">
                <tr>
                    <td class="city-cell">
                        <div class="city-title">Départ</div>
                        <div class="city-code">{{ $codeDepart }}</div>
                        <div style="font-size: 13px; font-weight: 700; color: #1f2937; margin-top: 3px;">
                            {{ $programme->point_depart }}
                        </div>
                    </td>
                    <td class="arrow-cell">
                        <!-- Table imbriquée pour simuler parfaitement les lignes et le car pure SVG -->
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 42%; vertical-align: middle;">
                                    <div style="border-bottom: 2px solid #cbd5e1; height: 1px; line-height: 1px; font-size: 1px; overflow: hidden;">&nbsp;</div>
                                </td>
                                <td style="width: 16%; text-align: center; vertical-align: middle; padding: 0 4px;">
                                    <!-- A highly-compatible base64-encoded side-profile SVG bus icon that renders flawlessly on Dompdf -->
                                    <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZlYTIxOSIgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIj48cGF0aCBkPSJNNCAxNmMwIC41NS40NSAxIDEgMWgxYzAgMS4xLjkgMiAyIDJzMi0uOSAyLTJoNGMwIDEuMS45IDIgMiAyczItLjkgMi0yaDFjLjU1IDAgMS0uNDUgMS0xdi02YzAtMi41LTItNC41LTQuNS00LjVINS41QzQuNjcgMy41IDQgNC4xNyA0IDV2MTF6bTMuNSAxYy0uMjggMC0uNS0uMjItLjUtLjVzLjIyLS41LjUtLjUuNS4yMi41LjUtLjIyLjUtLjUuNXptOCAwYy0uMjggMC0uNS0uMjItLjUtLjVzLjIyLS41LjUtLjUuNS4yMi41LjUtLjIyLjUtLjUuNXpNNiA2aDN2M0g2VjZ6bTUgMGgzdjNoLTNWNnptNSAwaDJ2M2gtMlY2eiIvPjwvc3ZnPg==" style="width: 46px; height: 46px; display: block; margin: 0 auto; vertical-align: middle;" alt="Bus">
                                </td>
                                <td style="width: 42%; vertical-align: middle;">
                                    <div style="border-bottom: 2px solid #cbd5e1; height: 1px; line-height: 1px; font-size: 1px; overflow: hidden;">&nbsp;</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="city-cell" style="text-align: right;">
                        <div class="city-title">Arrivée</div>
                        <div class="city-code">{{ $codeArrive }}</div>
                        <div style="font-size: 13px; font-weight: 700; color: #1f2937; margin-top: 3px;">
                            {{ $programme->point_arrive }}
                        </div>
                    </td>
                </tr>
            </table>

            <!-- Grille des détails -->
            <table class="grid-table">
                <tr>
                    <td class="grid-cell">
                        <div class="grid-label">Date de voyage</div>
                        <div class="grid-value">{{ date('d/m/Y', strtotime($dateVoyage)) }}</div>
                    </td>
                    <td class="grid-cell">
                        <div class="grid-label">Heure de départ</div>
                        <div class="grid-value highlight-time">{{ $heureDepart }}</div>
                    </td>
                    <td class="grid-cell">
                        <div class="grid-label">Type de voyage</div>
                        <div class="grid-value">{{ $tripType }}</div>
                    </td>
                    <td class="grid-cell">
                        <div class="grid-label">Siège / Place</div>
                        <div class="grid-value highlight-seat">
                            @if($seatNumber)
                                Place N° {{ $seatNumber }}
                            @else
                                Non assigné
                            @endif
                        </div>
                    </td>
                    <td class="grid-cell">
                        <div class="grid-label">Compagnie</div>
                        <div class="grid-value">{{ $compagnie->name ?? 'CAR 225' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Section Basse (Double Colonne) -->
        <table class="bottom-table">
            <tr>
                <!-- Colonne Gauche: Instructions -->
                <td class="info-column">
                    <h3 class="info-title">Comment effectuer votre voyage ?</h3>
                    
                    <div class="info-subtitle">1. Présentation à la gare</div>
                    <p class="info-text">
                        Présentez-vous à la gare de départ au moins <strong>30 minutes</strong> avant l'heure de départ mentionnée sur ce billet afin de procéder à l'enregistrement de vos bagages et à l'embarquement.
                    </p>
                    
                    <div class="info-subtitle">2. Pièces d'identité requises</div>
                    <p class="info-text">
                        Une pièce d'identité en cours de validité (CNI, Passeport, Attestation d'identité ou Carte consulaire) correspondant au nom inscrit sur le billet est obligatoire pour l'embarquement.
                    </p>

                    <!-- Avertissement réglementaire -->
                    <div class="warning-box">
                        <div class="warning-title">Avertissement important</div>
                        <p class="warning-text">
                            Ce billet est nominatif, individuel, non échangeable et non remboursable. Toute falsification ou reproduction illicite de ce document est passible de poursuites judiciaires.
                        </p>
                    </div>
                </td>

                <!-- Colonne Droite: QR Code & Montant -->
                <td class="card-column">
                    <div class="qr-card">
                        <!-- Conteneur block centré pour le badge titre -->
                        <div style="text-align: center; margin-bottom: 15px; width: 100%;">
                            <span class="qr-card-header">
                                Validation du billet - {{ $ticketType }}
                            </span>
                        </div>
                        
                        <!-- Conteneur block centré pour le QR code -->
                        <div style="text-align: center; margin-bottom: 5px; width: 100%;">
                            <span class="qr-image-container">
                                @if($qrCodeBase64)
                                    <img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="QR Code" class="qr-img">
                                @else
                                    <div class="qr-placeholder">QR Code non disponible</div>
                                @endif
                            </span>
                        </div>
                        <p class="qr-text">Scannez ce QR Code en gare pour valider votre embarquement</p>

                        <!-- Montant de la transaction -->
                        <div class="amount-container">
                            <div class="amount-label">Montant du billet</div>
                            <div class="amount-val">
                                {{ number_format($prixTotalIndividuel, 0, ',', ' ') }} FCFA
                            </div>
                            @if($isAllerRetour)
                                <div class="amount-sub">
                                    (Aller : {{ number_format($prixUnitaire, 0, ',', ' ') }} FCFA + Retour : {{ number_format($prixUnitaire, 0, ',', ' ') }} FCFA)
                                </div>
                            @endif
                            <div class="amount-sub" style="color: #10b981; font-weight: 700; margin-top: 5px;">
                                Transaction sécurisée
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Pied de page -->
        <div class="footer-section">
            <p>Pour toute assistance : contact@edemarchee-ci.com | +225 XX XX XX XX</p>
            <p>&copy; {{ date('Y') }} {{ $compagnie->name ?? 'CAR 225' }} - Tous droits réservés. Propulsé par E-Démarche CI.</p>
        </div>
    </div>
</body>

</html>