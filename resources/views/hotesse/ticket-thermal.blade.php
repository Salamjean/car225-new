<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket - {{ $reservation->reference }}</title>
    <style>
        @page {
            margin: 0;
            size: 80mm 200mm; /* Hauteur flexible */
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm;
            margin: 0;
            padding: 5mm;
            background: white;
            color: black;
            font-size: 12px;
            line-height: 1.2;
        }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .divider { border-top: 1px dashed black; margin: 5px 0; }
        .header { margin-bottom: 10px; }
        .logo { font-size: 18px; margin-bottom: 2px; }
        .reference { font-size: 14px; margin: 5px 0; }
        .qr-container { margin: 10px 0; display: flex; justify-content: center; }
        .qr-image { width: 45mm; height: 45mm; }
        .seat { font-size: 24px; border: 1px solid black; padding: 5px; margin: 5px 0; display: inline-block; }
        .footer { margin-top: 15px; font-size: 10px; }
        @media print {
            body { width: 80mm; padding: 2mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="text-center header">
        <div class="logo bold">CAR 225</div>
        <div>{{ $reservation->compagnie->name ?? 'Compagnie de Transport' }}</div>
        <div class="divider"></div>
        <div class="bold">BILLET DE VOYAGE</div>
        <div class="reference bold">{{ $reservation->reference }}</div>
    </div>

    <div class="divider"></div>

    <div class="info">
        <p><span class="bold">DE:</span> {{ $reservation->programme->point_depart }}</p>
        <p><span class="bold">A :</span> {{ $reservation->programme->point_arrive }}</p>
        <p><span class="bold">DATE:</span> {{ $reservation->date_voyage->format('d/m/Y') }}</p>
        <p><span class="bold">HEURE:</span> {{ date('H:i', strtotime($reservation->heure_depart)) }}</p>
    </div>

    <div class="divider"></div>

    <div class="text-center">
        <p class="bold">PASSAGER:</p>
        <p>{{ strtoupper($reservation->passager_nom) }} {{ strtoupper($reservation->passager_prenom) }}</p>
        <p>{{ $reservation->passager_telephone }}</p>
        
        <div class="seat bold">PLACE N° {{ $reservation->seat_number }}</div>
    </div>

    <div class="divider"></div>

    <div class="qr-container">
        @if($reservation->qr_code_path)
            <img src="{{ asset('storage/' . $reservation->qr_code_path) }}" alt="QR Code" class="qr-image">
        @endif
    </div>

    <div class="text-center">
        <p class="bold" style="font-size: 16px;">PRIX: {{ number_format($reservation->montant, 0, ',', ' ') }} FCFA</p>
        <p>Transaction validée (Hôtesse)</p>
    </div>

    <div class="divider"></div>

    <div class="text-center footer">
        <p>Servie par: {{ $reservation->hotesse->prenom ?? '' }}</p>
        <p>Billet non remboursable</p>
        <p>Bon Voyage !</p>
        <p>&copy; {{ date('Y') }} CAR 225</p>
    </div>

    <!-- Toolbar for mobile -->
    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #333; color: white; border: none; border-radius: 5px;">
            Impression Système
        </button>
        <br><br>
        <a href="intent://{{ request()->getHost() }}{{ route('hotesse.ticket.thermal', $reservation->id, false) }}#Intent;scheme=http;package=com.fourbarcode.print;end" 
           style="padding: 10px 20px; background: #e94e1a; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
            DIRECT 4BARCODE
        </a>
    </div>

    <script>
        window.onload = function() {
            // Tentative d'impression automatique si possible
            if (!navigator.userAgent.match(/Android/i)) {
                window.print();
            }
        }
    </script>
</body>
</html>
