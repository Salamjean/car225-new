<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code OTP - Car225</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f5f7fa;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(to right, #e94f1b, #ffb74d);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .email-header p {
            margin: 10px 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .email-body {
            padding: 40px 30px;
        }
        .email-body p {
            color: #343a40;
            font-size: 16px;
            line-height: 1.6;
            margin: 0 0 20px;
        }
        .otp-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px dashed #e94f1b;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-label {
            color: #6c757d;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        .otp-code {
            font-size: 48px;
            font-weight: 700;
            color: #e94f1b;
            letter-spacing: 8px;
            margin: 10px 0;
        }
        .info-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info-box p {
            margin: 0;
            color: #856404;
            font-size: 14px;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #e9ecef;
        }
        .email-footer p {
            margin: 5px 0;
        }
        .email-footer a {
            color: #e94f1b;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🔑 Réinitialisation de mot de passe</h1>
            <p>Car225 - Votre plateforme de réservation</p>
        </div>
        
        <div class="email-body">
            <p>Bonjour,</p>
            
            <p>Vous avez demandé à réinitialiser votre mot de passe. Utilisez le code OTP ci-dessous pour continuer le processus :</p>
            
            <div class="otp-box">
                <div class="otp-label">Votre code OTP</div>
                <div class="otp-code">{{ $otp }}</div>
            </div>
            
            <div class="info-box">
                <p><strong>⏱ Important :</strong> Ce code est valable pendant 10 minutes seulement. Pour votre sécurité, ne partagez ce code avec personne.</p>
            </div>
            
            <p>Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet email. Votre compte reste sécurisé.</p>
        </div>
        
        <div class="email-footer">
            <p><strong>Car225</strong></p>
            <p>Votre plateforme de réservation de transport</p>
            <p style="margin-top: 15px;">Vous avez des questions ? <a href="mailto:support@car225.com">Contactez-nous</a></p>
        </div>
    </div>
</body>
</html>
