<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #e94f1b; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .header h1 { color: white; margin: 0; font-size: 24px; }
        .content { background: #fff; padding: 30px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 8px 8px; }
        .otp-box { background: #f8f9fa; border: 2px dashed #e94f1b; text-align: center; padding: 20px; margin: 20px 0; border-radius: 8px; font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #e94f1b; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CAR 225</h1>
        </div>
        <div class="content">
            <p>Bonjour <strong>{{ $userName }}</strong>,</p>
            <p>Vous avez demandé à modifier les informations de votre profil. Veuillez utiliser le code de sécurité ci-dessous pour confirmer cette opération :</p>
            <div class="otp-box">
                {{ $otp }}
            </div>
            <p>Ce code est valide pendant les 10 prochaines minutes.</p>
            <p>Si vous n'êtes pas à l'origine de cette demande, veuillez ignorer cet email.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} CAR 225. Tous droits réservés.
        </div>
    </div>
</body>
</html>
