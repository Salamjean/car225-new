<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Annulé - {{ config('app.name', 'CAR225') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        body {
            background: #f8fafc;
        }
        .cancel-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .error-icon {
            background: #ef4444;
            color: white;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: -40px auto 20px;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full cancel-card p-8 text-center animate__animated animate__zoomIn">
        <div class="error-icon animate__animated animate__shakeX animate__delay-1s">
            <i class="fas fa-times"></i>
        </div>
        
        <h1 class="text-2xl font-bold text-slate-800 mb-2">Paiement Annulé</h1>
        <p class="text-slate-600 mb-6">
            Votre transaction a été annulée ou une erreur est survenue pendant le processus de paiement Wave.
        </p>

        <div class="space-y-3">
            <a href="{{ route('reservation.index') }}" class="block w-full bg-slate-800 hover:bg-slate-900 text-white font-semibold py-3 px-4 rounded-xl transition duration-200">
                Retour à mes réservations
            </a>
            <p class="text-slate-500 text-sm pt-4">
                La réservation a été initialisée. Vous pourrez retenter le paiement depuis la section "Mes Réservations".
            </p>
        </div>
    </div>
</body>
</html>
