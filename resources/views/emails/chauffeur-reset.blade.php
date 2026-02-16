<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .container { background: linear-gradient(135deg, #e94e1a 0%, #d33d0f 100%); padding: 30px; border-radius: 10px; }
        .content { background: white; padding: 30px; border-radius: 8px; }
        .otp-code { background: #f7fafc; border-left: 4px solid #e94e1a; padding: 15px; margin: 20px 0; font-size: 24px; font-weight: bold; text-align: center; letter-spacing: 8px; color: #e94e1a; }
        .button { display: inline-block; background: #e94e1a; color: white !important; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #718096; }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <h2 style="color: #2d3748;">Bonjour {{ $chauffeurName }},</h2>
            <p>Vous avez demandé la réinitialisation de votre mot de passe pour votre compte chauffeur <strong>CAR225</strong>.</p>
            <p>Voici votre code de vérification :</p>
            <div class="otp-code">{{ $otp }}</div>
            <p>Ce code est nécessaire pour définir votre nouveau mot de passe.</p>
            <div style="text-align: center;">
                <a href="{{ url('/chauffeur/password/reset?email=' . urlencode($chauffeurEmail)) }}" class="button">
                    Réinitialiser mon mot de passe
                </a>
            </div>
            <p style="color: #718096; font-size: 13px; margin-top: 30px;">Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet email en toute sécurité.</p>
            <div class="footer">
                <p>Cordialement,<br><strong>L'équipe CAR225</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
