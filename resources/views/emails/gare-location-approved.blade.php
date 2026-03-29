<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .container { background: linear-gradient(135deg, #92400e 0%, #f97316 100%); padding: 30px; border-radius: 10px; }
        .content { background: white; padding: 30px; border-radius: 8px; }
        .badge { display: inline-block; background: #dcfce7; color: #15803d; padding: 6px 16px; border-radius: 999px; font-weight: bold; font-size: 14px; margin-bottom: 16px; }
        .coords-box { background: #f0fdf4; border-left: 4px solid #16a34a; padding: 14px 18px; border-radius: 0 8px 8px 0; margin: 20px 0; font-family: monospace; font-size: 15px; color: #166534; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: rgba(255,255,255,0.75); }
        h2 { color: #111827; margin-top: 0; }
        p { color: #4b5563; }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <div class="badge">✅ Localisation mise à jour</div>
            <h2>Bonne nouvelle, {{ $gare->nom_gare }} !</h2>
            @if($context === 'approved')
            <p>Votre demande de mise à jour de la position GPS de votre gare a été <strong>approuvée</strong> par votre compagnie. La nouvelle localisation est désormais active sur la carte de suivi en temps réel.</p>
            @else
            <p>La position GPS de votre gare a été <strong>mise à jour manuellement</strong> par votre compagnie. La nouvelle localisation est désormais active sur la carte de suivi en temps réel.</p>
            @endif
            <div class="coords-box">
                📍 Latitude : {{ number_format($latitude, 6) }}<br>
                📍 Longitude : {{ number_format($longitude, 6) }}
            </div>
            <p>Connectez-vous à votre espace gare pour consulter votre tableau de bord mis à jour.</p>
            <p style="font-size:12px;color:#9ca3af;">Si vous n'êtes pas à l'origine de ce changement, contactez immédiatement votre compagnie.</p>
        </div>
        <div class="footer">CAR225 — Plateforme de gestion de transport<br>Cet email est envoyé automatiquement, ne pas répondre.</div>
    </div>
</body>
</html>
