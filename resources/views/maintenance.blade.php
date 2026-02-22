<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CAR225 - Maintenance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('assetsPoster/assets/images/Car225_favicon.png') }}" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            overflow: hidden;
            position: relative;
        }

        /* Animated background particles */
        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .particles::before,
        .particles::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            opacity: 0.1;
            animation: float 8s ease-in-out infinite;
        }

        .particles::before {
            background: #e94f1b;
            top: -100px;
            right: -100px;
            animation-delay: 0s;
        }

        .particles::after {
            background: #e89116;
            bottom: -100px;
            left: -100px;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, -30px) scale(1.1); }
        }

        .container {
            text-align: center;
            z-index: 10;
            padding: 2rem;
            max-width: 600px;
        }

        .logo {
            width: 140px;
            margin-bottom: 2rem;
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }

        .icon-wrapper {
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
            background: rgba(233, 79, 27, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .icon-wrapper::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid rgba(233, 79, 27, 0.3);
            animation: pulse-ring 2s ease-in-out infinite;
        }

        @keyframes pulse-ring {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0; }
        }

        .icon-wrapper i {
            font-size: 3rem;
            color: #e94f1b;
            animation: gear-spin 6s linear infinite;
        }

        @keyframes gear-spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        h1 {
            font-size: 2.2rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 1rem;
            letter-spacing: -0.5px;
        }

        h1 span {
            background: linear-gradient(135deg, #e94f1b, #e89116);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .message {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.8;
            margin-bottom: 2.5rem;
            font-weight: 300;
        }

        .progress-bar {
            width: 200px;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            margin: 0 auto 2rem;
            overflow: hidden;
        }

        .progress-bar::after {
            content: '';
            display: block;
            width: 40%;
            height: 100%;
            background: linear-gradient(90deg, #e94f1b, #e89116);
            border-radius: 4px;
            animation: loading 2s ease-in-out infinite;
        }

        @keyframes loading {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(350%); }
        }

        .contact-info {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.85rem;
            font-weight: 400;
        }

        .contact-info i {
            color: #e94f1b;
        }
    </style>
</head>
<body>
    <div class="particles"></div>

    <div class="container">
        <img src="{{ asset('assetsPoster/assets/images/logo_car225.png') }}" alt="CAR225" class="logo">

        <div class="icon-wrapper">
            <i class="fas fa-cog"></i>
        </div>

        <h1>Site en <span>Maintenance</span></h1>

        <p class="message">{{ $message }}</p>

        <div class="progress-bar"></div>

        <div class="contact-info">
            <i class="fas fa-envelope"></i>
            <span>support@car225.com</span>
        </div>
    </div>
</body>
</html>
