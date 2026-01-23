<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            border: 1px solid #eee;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .header {
            background-color: #051e23;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #e94f1b;
        }

        .content {
            padding: 30px;
            background-color: #ffffff;
        }

        .info-item {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f9f9f9;
        }

        .label {
            font-weight: bold;
            color: #e94f1b;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
            display: block;
            margin-bottom: 5px;
        }

        .value {
            font-size: 16px;
            color: #051e23;
        }

        .message-box {
            background-color: #f8fafb;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #e94f1b;
            margin-top: 10px;
        }

        .footer {
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Nouveau Message de Contact</h1>
            <p>Plateforme CAR 225</p>
        </div>
        <div class="content">
            <div class="info-item">
                <span class="label">Expéditeur</span>
                <span class="value">{{ $name }}</span>
            </div>
            <div class="info-item">
                <span class="label">Email</span>
                <span class="value">{{ $email }}</span>
            </div>
            <div class="info-item">
                <span class="label">Sujet</span>
                <span class="value">{{ $subject }}</span>
            </div>
            <div class="info-item">
                <span class="label">Message</span>
                <div class="message-box">
                    {!! nl2br(e($userMessage)) !!}
                </div>
            </div>
        </div>
        <div class="footer">
            <p>Cet email a été envoyé automatiquement depuis le site CAR 225.</p>
            <p>&copy; {{ date('Y') }} CAR 225 - Tous droits réservés.</p>
        </div>
    </div>
</body>

</html>