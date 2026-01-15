<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de Réservation - CAR 225</title>
    <style>
        /* Reset et base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8fafc;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        /* Header */
        .email-header {
            background: linear-gradient(135deg, #000000 0%, #333333 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .logo {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }

        .logo-accent {
            color: #fea219;
        }

        .email-subtitle {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 10px;
        }

        /* Content */
        .email-content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 20px;
            color: #1a202c;
            margin-bottom: 30px;
        }

        .highlight-name {
            color: #fea219;
            font-weight: 600;
        }

        /* Confirmation Box */
        .confirmation-box {
            background: linear-gradient(135deg, #f0fff4 0%, #e6fffa 100%);
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            border-left: 5px solid #10b981;
        }

        .confirmation-title {
            color: #065f46;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin: 30px 0;
        }

        .info-card {
            background: #f8fafc;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #e2e8f0;
        }

        .info-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #1a202c;
        }

        /* Places */
        .places-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            border: 2px solid #e2e8f0;
        }

        .places-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #3b82f6;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            margin: 5px;
            min-width: 40px;
        }

        /* QR Code Preview */
        .qr-preview {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        .qr-image {
            max-width: 150px;
            height: auto;
            margin: 15px 0;
            border-radius: 8px;
            padding: 10px;
            background: white;
            border: 1px solid #e2e8f0;
        }

        /* Actions */
        .action-buttons {
            display: flex;
            gap: 15px;
            margin: 30px 0;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 14px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            flex: 1;
            min-width: 180px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #fea219 0%, #e89116 100%);
            color: white;
        }

        .btn-secondary {
            background: white;
            color: #4b5563;
            border: 2px solid #e5e7eb;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(254, 162, 25, 0.3);
        }

        /* Important Notice */
        .notice-box {
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            border-left: 5px solid #fea219;
        }

        .notice-title {
            color: #9a3412;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .notice-list {
            list-style: none;
        }

        .notice-list li {
            margin-bottom: 10px;
            padding-left: 24px;
            position: relative;
        }

        .notice-list li:before {
            content: "•";
            color: #fea219;
            font-size: 20px;
            position: absolute;
            left: 0;
            top: -2px;
        }

        /* Footer */
        .email-footer {
            background: #1a202c;
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 20px 0;
        }

        .footer-link {
            color: #cbd5e0;
            text-decoration: none;
            font-size: 14px;
        }

        .footer-link:hover {
            color: #fea219;
        }

        .copyright {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 20px;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .info-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                min-width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="logo">CAR<span class="logo-accent">225</span></div>
            <h1>Réservation Confirmée !</h1>
            <p class="email-subtitle">Votre voyage est programmé avec succès</p>
        </div>

        <!-- Content -->
        <div class="email-content">
            <div class="greeting">
                Bonjour <span class="highlight-name">{{ $displayName }}</span>,
            </div>

            <p>Nous avons le plaisir de vous confirmer votre réservation auprès de
                <strong>{{ $programme->compagnie->name ?? 'notre compagnie' }}</strong>. Votre voyage est maintenant
                confirmé et votre billet est prêt.</p>

            <!-- Confirmation Box -->
            <div class="confirmation-box">
                <div class="confirmation-title">
                    ✅ Réservation confirmée
                </div>
                <p>Votre réservation a été validée avec succès. Vous trouverez ci-dessous les détails de votre voyage et
                    votre billet en pièce jointe.</p>
            </div>

            <!-- Informations Principales -->
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-label">Référence</div>
                    <div class="info-value">{{ $reservation->reference }}</div>
                </div>

                <div class="info-card">
                    <div class="info-label">Date du voyage</div>
                    <div class="info-value">{{ $dateVoyage }}</div>
                </div>

                <div class="info-card">
                    <div class="info-label">Itinéraire</div>
                    <div class="info-value">{{ $programme->point_depart }} → {{ $programme->point_arrive }}</div>
                </div>

                <div class="info-card">
                    <div class="info-label">Heure de départ</div>
                    <div class="info-value">{{ $heureDepart }}</div>
                </div>
            </div>

            <!-- Important Notice -->
            <div class="notice-box">
                <div class="notice-title">
                    📌 Informations importantes
                </div>
                <ul class="notice-list">
                    <li>Votre billet électronique est disponible en pièce jointe (format PDF)</li>
                    <li>Présentez votre billet (format papier ou numérique) à l'embarquement</li>
                    <li>Le QR Code sera scanné pour validation</li>
                    <li>Arrivez au minimum 30 minutes avant le départ</li>
                    <li>Ayez une pièce d'identité valide avec vous</li>
                </ul>
            </div>

            <p style="margin-top: 30px; text-align: center; color: #4b5563;">
                <em>Nous vous remercions de votre confiance et vous souhaitons un excellent voyage !</em>
            </p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p>CAR 225 - Plateforme de Réservation de Transport</p>
            <p class="copyright">
                © {{ date('Y') }} CAR 225. Tous droits réservés.<br>
                Cet email est envoyé automatiquement, merci de ne pas y répondre.
            </p>
        </div>
    </div>
</body>

</html>