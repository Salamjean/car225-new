<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            border-radius: 10px;
        }
        .content {
            background: white;
            padding: 30px;
            border-radius: 8px;
        }
        .otp-code {
            background: #f7fafc;
            border-left: 4px solid #e94e1a;
            padding: 15px;
            margin: 20px 0;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 8px;
            color: #e94e1a;
        }
        .button {
            display: inline-block;
            background: #e94e1a;
            color: white !important;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #718096;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <h2 style="color: #2d3748;">Bonjour {{ $chauffeurName }},</h2>
            
            <p>Bienvenue chez <strong>CAR225</strong> ! Votre compte chauffeur a été créé avec succès.</p>
            
            <p>Voici votre code de vérification pour activer votre compte :</p>
            
            <div class="otp-code">
                {{ $otp }}
            </div>
            
            <p style="color: #718096; font-size: 14px;">
                <strong>Veuillez utiliser ce code pour vérifier votre adresse email.</strong>
            </p>
            
            <p>Veuillez utiliser ce code pour vérifier votre adresse email et accéder à votre espace chauffeur.</p>
            
            <div style="text-align: center;">
                <a href="{{ url('/chauffeur/verify-otp?email=' . urlencode($chauffeurEmail)) }}" class="button">
                    Vérifier mon compte
                </a>
            </div>
            
            <p style="color: #718096; font-size: 13px; margin-top: 30px;">
                Si vous n'avez pas demandé ce code, veuillez ignorer cet email.
            </p>
            
            <div class="footer">
                <p>Cordialement,<br><strong>L'équipe CAR225</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
