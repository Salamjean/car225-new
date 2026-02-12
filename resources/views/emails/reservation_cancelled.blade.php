<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annulation de Réservation - CAR 225</title>
    <style>
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

        .email-header {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
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
            color: #fbbf24;
        }

        .email-subtitle {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 10px;
        }

        .email-content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 20px;
            color: #1a202c;
            margin-bottom: 30px;
        }

        .highlight-name {
            color: #e94f1b;
            font-weight: 600;
        }

        .cancellation-box {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            border-left: 5px solid #dc2626;
        }

        .cancellation-title {
            color: #991b1b;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

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

        .refund-box {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-radius: 12px;
            padding: 30px;
            margin: 30px 0;
            text-align: center;
            border: 2px solid #10b981;
        }

        .refund-title {
            color: #065f46;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 15px;
        }

        .refund-amount {
            font-size: 48px;
            font-weight: 800;
            color: #10b981;
            margin: 20px 0;
        }

        .refund-percentage {
            font-size: 18px;
            color: #059669;
            font-weight: 600;
        }

        .round-trip-notice {
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            border-left: 5px solid #ea580c;
        }

        .notice-title {
            color: #9a3412;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .reference-badge {
            display: inline-block;
            background: white;
            color: #e94f1b;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 700;
            margin: 5px;
            border: 2px solid #ea580c;
        }

        .email-footer {
            background: #1a202c;
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .copyright {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 20px;
        }

        @media (max-width: 640px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <div class="logo">CAR<span class="logo-accent">225</span></div>
            <h1>Réservation Annulée</h1>
            <p class="email-subtitle">Votre réservation a été annulée avec succès</p>
        </div>

        <div class="email-content">
            <div class="greeting">
                Bonjour <span class="highlight-name">{{ $reservation->passager_nom }} {{ $reservation->passager_prenom }}</span>,
            </div>

            <p>Nous vous confirmons l'annulation de votre réservation. Votre remboursement a été crédité sur votre wallet CAR 225.</p>

            <div class="cancellation-box">
                <div class="cancellation-title">
                    ❌ Réservation annulée
                </div>
                <p>L'annulation a été effectuée le <strong>{{ \Carbon\Carbon::parse($reservation->annulation_date)->format('d/m/Y à H:i') }}</strong>.</p>
            </div>

            @if($isRoundTrip)
            <div class="round-trip-notice">
                <div class="notice-title">🔄 Aller-Retour Annulé</div>
                <p style="margin-bottom: 15px;">Les deux billets suivants ont été annulés :</p>
                <div>
                    <span class="reference-badge">{{ $reservation->reference }}</span>
                    <span class="reference-badge">{{ $pairedReference }}</span>
                </div>
            </div>
            @endif

            <div class="info-grid">
                <div class="info-card">
                    <div class="info-label">Référence</div>
                    <div class="info-value">{{ $reservation->reference }}</div>
                </div>

                <div class="info-card">
                    <div class="info-label">Date du voyage</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($reservation->date_voyage)->format('d/m/Y') }}</div>
                </div>

                <div class="info-card">
                    <div class="info-label">Itinéraire</div>
                    <div class="info-value">{{ $reservation->programme->point_depart ?? 'N/A' }} → {{ $reservation->programme->point_arrive ?? 'N/A' }}</div>
                </div>

                <div class="info-card">
                    <div class="info-label">Montant original</div>
                    <div class="info-value">{{ number_format($reservation->montant, 0, ',', ' ') }} FCFA</div>
                </div>
            </div>

            <div class="refund-box">
                <div class="refund-title">💰 Remboursement</div>
                <div class="refund-amount">{{ number_format($refundAmount, 0, ',', ' ') }} <span style="font-size: 24px;">FCFA</span></div>
                <div class="refund-percentage">{{ $refundPercentage }}% de remboursement</div>
                <p style="margin-top: 20px; color: #065f46; font-size: 14px;">
                    Le montant a été crédité sur votre wallet CAR 225
                </p>
            </div>

            <p style="text-align: center; color: #6b7280; font-size: 14px; margin-top: 30px;">
                Merci d'avoir utilisé CAR 225. Nous espérons vous revoir bientôt !
            </p>
        </div>

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
