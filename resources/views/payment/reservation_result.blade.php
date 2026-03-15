<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Réservation - {{ config('app.name', 'CAR225') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');
        
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }
        .result-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }
        .icon-box {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            margin: -50px auto 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .btn-primary {
            background-color: #e94f1b;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #d44315;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(233, 79, 27, 0.4);
        }
    </style>
</head>
<body class="flex items-center justify-center p-4">

    <div class="max-w-md w-full result-card p-8 text-center animate__animated animate__zoomIn">
        @if($success)
            <div class="icon-box bg-green-500 text-white animate__animated animate__bounceIn animate__delay-1s">
                <i class="fas fa-check"></i>
            </div>
            <h1 class="text-3xl font-black text-gray-800 mb-3">Paiement Réussi !</h1>
            <p class="text-gray-600 mb-8 text-lg">
                Votre réservation a été validée avec succès. Vos billets seront disponibles dans l'application d'ici peu.
            </p>
        @else
            <div class="icon-box bg-red-500 text-white animate__animated animate__bounceIn animate__delay-1s">
                <i class="fas fa-times"></i>
            </div>
            <h1 class="text-3xl font-black text-gray-800 mb-3">Échec / Annulé</h1>
            <p class="text-gray-600 mb-8 text-lg">
                Le paiement de votre réservation a été annulé ou a échoué. Veuillez réessayer.
            </p>
        @endif

        <div class="bg-gray-50 rounded-2xl p-5 mb-8 text-left border border-gray-100">
            <div class="flex justify-between items-center mb-2">
                <span class="text-gray-500 text-sm font-medium">Référence</span>
                <span class="text-gray-800 font-bold text-sm">{{ $transactionId }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-500 text-sm font-medium">Type</span>
                <span class="text-gray-800 font-bold text-sm">Réservation Ticket</span>
            </div>
        </div>

        <div class="space-y-4">
            <!-- Bouton Deep Link Mobile -->
            <a href="car225://payment?success={{ $success ? 'true' : 'false' }}&transactionId={{ $transactionId }}&method=wave" 
               class="btn-primary block w-full text-white font-bold py-4 px-6 rounded-2xl shadow-lg flex items-center justify-center text-lg">
                <i class="fas fa-mobile-alt mr-3"></i> Retourner dans l'application
            </a>
            
            <!-- Lien Web -->
            <a href="{{ route('reservation.index') }}" 
               class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-4 px-6 rounded-2xl transition duration-200">
                <i class="fas fa-ticket-alt mr-2"></i> Mes réservations (Web)
            </a>
        </div>

        <p class="mt-8 text-gray-400 text-sm">
            Si la redirection vers l'application mobile ne se fait pas automatiquement, cliquez sur le bouton orange.
        </p>
    </div>

    <script>
        // Tentative de redirection automatique après 3 secondes vers l'app
        setTimeout(function() {
            window.location.href = "car225://payment?success={{ $success ? 'true' : 'false' }}&transactionId={{ $transactionId }}&method=wave";
        }, 3000);
    </script>

</body>
</html>
