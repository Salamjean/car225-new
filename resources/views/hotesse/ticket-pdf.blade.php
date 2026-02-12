<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Billet de voyage - {{ $reservation->reference }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital@1&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #1f2937;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
        }

        .ticket-container {
            max-width: 297mm;
            /* Format A4 paysage */
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            position: relative;
            border: 5px solid #e94e1a;
            /* Couleur de la bordure */
        }

        .ticket-content {
            padding: 30px;
        }

        .header-section {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #e94e1a;
            padding-bottom: 20px;
        }

        .ticket-title {
            font-size: 28px;
            font-weight: 800;
            color: #e94e1a;
            margin-bottom: 10px;
        }

        .reference-container {
            display: inline-flex;
            align-items: center;
            gap: 15px;
            margin-top: 15px;
            background: #10b981;
            /* Vert */
            color: white;
            padding: 12px;
            border-radius: 12px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #02245b;
            margin: 20px 0;
            padding: 10px 0;
            border-bottom: 0px solid #f3f4f6;
        }

        /* QR Code section */
        .qr-container {
            text-align: center;
            margin: 40px 0;
        }

        .qr-image {
            width: 180px;
            height: 180px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
        }

        .amount-section {
            text-align: center;
            margin: 40px 0;
            padding: 30px;
            background: #10b981;
            /* Vert */
            color: white;
            border-radius: 10px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .ticket-container {
                max-width: 100%;
                box-shadow: none;
                border-radius: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="ticket-container">
        <div class="ticket-content">
            <!-- Header -->
            <div class="header-section">
                <div class="ticket-title">BILLET DE VOYAGE ÉLECTRONIQUE <br><span style="font-size: 20px; color: #02245b;">(STANDARD)</span></div>
                <div class="reference-container">
                    <span>Référence:</span>
                    <strong>{{ $reservation->reference }}</strong>
                </div>
            </div>

            <!-- Itinéraire -->
            <div class="section-title">Itinéraire du voyage</div>
            <div>
                <p>Départ: {{ $reservation->programme->point_depart }} - Arrivée: {{ $reservation->programme->point_arrive }}</p>
                <p>Date: {{ $reservation->date_voyage->format('d/m/Y') }} - Heure: {{ date('H:i', strtotime($reservation->heure_depart)) }}</p>
                <p>Passager: {{ $reservation->passager_nom }} {{ $reservation->passager_prenom }} ({{ $reservation->passager_telephone }})</p>
                @if($reservation->seat_number)
                    <p
                        style="background: #e94e1a; color: white; padding: 10px; border-radius: 8px; font-weight: bold; font-size: 25px; text-align: center; margin-top: 15px;">
                        <i class="fas fa-chair"></i> Place N° {{ $reservation->seat_number }}
                    </p>
                @endif
            </div>

            <!-- QR Code -->
            <div class="qr-container">
                <div
                    style="font-weight: 700; color: #1e40af; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px;">
                    Type de voyage : {{ $reservation->programme->itineraire->type ?? 'Standard' }}
                </div>
                <h3>Validation du billet</h3>
                @if($reservation->qr_code_path)
                    <img src="{{ asset('storage/' . $reservation->qr_code_path) }}" alt="QR Code" class="qr-image">
                @else
                    <div
                        style="width: 180px; height: 180px; background: #f3f4f6; border: 2px dashed #d1d5db; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #6b7280;">
                        QR Code non disponible
                    </div>
                @endif
                <div>Scannez pour vérification</div>
            </div>

            <!-- Montant -->
            <div class="amount-section">
                <div>Montant du billet :</div>
                <div style="font-size: 28px; font-weight: 800;">
                    {{ number_format($reservation->montant, 0, ',', ' ') }} FCFA
                </div>
                <div style="font-size: 14px; margin-top: 10px;">Transaction validée (Hôtesse)</div>
            </div>

            <!-- Avertissement -->
            <div
                style="background: #fee2e2; border: 1px solid #dc2626; border-radius: 8px; padding: 15px; margin: 20px 0;">
                <strong>Avertissement:</strong>
                <p>Ce billet est nominatif, non échangeable et non remboursable. Toute falsification est passible de
                    poursuites.</p>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>Servie par: {{ Auth::guard('hotesse')->user()->prenom }} {{ Auth::guard('hotesse')->user()->name }}</p>
                <p>Pour toute assistance : contact@edemarchee-ci.com</p>
                <p>&copy; {{ date('Y') }} CAR 225 - Tous droits réservés</p>
            </div>
        </div>
    </div>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
