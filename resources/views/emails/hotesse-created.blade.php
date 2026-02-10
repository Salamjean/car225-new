<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte Hôtesse Créé</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #e94e1a 0%, #d33d0f 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .otp-box {
            background: #f8f9fa;
            border-left: 4px solid #e94e1a;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #e94e1a;
            text-align: center;
            letter-spacing: 5px;
            margin: 10px 0;
        }
        .info-box {
            background: #e8f4f8;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #e94e1a;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .warning {
            color: #856404;
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Bienvenue chez CAR 225</h1>
        </div>
        
        <div class="content">
            <p>Bonjour <strong>{{ $hotesseData['prenom'] }} {{ $hotesseData['name'] }}</strong>,</p>
            
            <p>Votre compte hôtesse a été créé avec succès par <strong>{{ $compagnieName }}</strong>.</p>
            
            <div class="info-box">
                <strong>📧 Votre email de connexion:</strong><br>
                {{ $hotesseData['email'] }}
            </div>
            
            <p>Pour activer votre compte et définir votre mot de passe, veuillez utiliser le code OTP ci-dessous:</p>
            
            <div class="otp-box">
                <p style="margin: 0; text-align: center; color: #666;">Votre code OTP</p>
                <div class="otp-code">{{ $otpCode }}</div>
                <p style="margin: 0; text-align: center; color: #666; font-size: 12px;">
                    Ce code est valide indéfiniment
                </p>
            </div>
            
            <div class="warning">
                ⚠️ <strong>Important:</strong> Ne partagez ce code avec personne. L'équipe de {{ $compagnieName }} ne vous demandera jamais ce code.
            </div>
            
            <p><strong>Prochaines étapes:</strong></p>
            <ol>
                <li>Accédez à la page de vérification OTP</li>
                <li>Entrez le code ci-dessus</li>
                <li>Créez votre mot de passe sécurisé</li>
                <li>Connectez-vous et commencez votre service</li>
            </ol>
            
            <div style="text-align: center; margin: 25px 0;">
                <a href="{{ route('hotesse.auth.verify-otp', ['email' => $hotesseData['email']]) }}" class="button">
                    Vérifier mon compte
                </a>
            </div>
            
            <p>Si vous rencontrez des problèmes, contactez votre administrateur chez <strong>{{ $compagnieName }}</strong>.</p>
            
            <p>Cordialement,<br>
            <strong>L'équipe {{ $compagnieName }}</strong></p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} {{ $compagnieName }}. Tous droits réservés.</p>
            <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
        </div>
    </div>
</body>
</html>
