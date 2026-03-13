<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Réussi - {{ config('app.name', 'CAR225') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        body {
            background: linear-gradient(135deg, #fff3cd 0%, #fffbf0 50%, #fff3cd 100%);
            min-height: 100vh;
        }
        .success-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.08);
        }
        .check-icon {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            margin: -45px auto 20px;
            box-shadow: 0 8px 25px rgba(34, 197, 94, 0.4);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full success-card p-8 text-center animate__animated animate__zoomIn">
        <div class="check-icon animate__animated animate__bounceIn animate__delay-1s">
            <i class="fas fa-check"></i>
        </div>
        
        <h1 class="text-2xl font-bold text-slate-800 mb-2">Paiement Enregistré !</h1>
        <p class="text-slate-600 mb-6">
            Votre paiement pour la <strong>Réservation</strong> via Wave est en cours de validation. Vous recevrez vos billets d'ici peu.
        </p>

        <div class="bg-slate-50 rounded-xl p-5 mb-8 text-left border border-slate-100">
            <div class="flex justify-between mb-3">
                <span class="text-slate-500 text-sm">Session ID :</span>
                <span class="text-slate-800 font-mono text-xs font-bold">{{ substr($sessionId ?? 'N/A', 0, 15) }}...</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500 text-sm">Statut :</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <i class="fas fa-clock mr-1"></i> En Traitement
                </span>
            </div>
        </div>

        <div class="space-y-3">
            <a href="{{ route('reservation.index') }}" class="block w-full bg-[#e94f1b] hover:bg-[#d44315] text-white font-semibold py-3 px-4 rounded-xl transition duration-200 shadow-lg shadow-orange-200">
                <i class="fas fa-ticket-alt mr-2"></i> Consulter mes réservations
            </a>
            <a href="{{ route('home') }}" class="block w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-3 px-4 rounded-xl transition duration-200">
                <i class="fas fa-home mr-2"></i> Retour à l'accueil
            </a>
        </div>

        <p class="mt-8 text-slate-400 text-xs text-center">
            <i class="fas fa-envelope mr-1"></i> Un email et un SMS de confirmation contenant vos billets vous seront envoyés sous peu dès confirmation par Wave.
        </p>
    </div>

    <script>
        // Auto-redirect vers la liste après 10 secondes (cas classique)
        setTimeout(function() {
            window.location.href = '{{ route("reservation.index") }}';
        }, 10000);
    </script>

</body>
</html>
