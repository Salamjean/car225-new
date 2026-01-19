<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .header {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        .header h2 {
            margin: 0;
            color: #dc3545;
        }

        .content {
            padding: 20px 0;
        }

        .info-block {
            background-color: #f0f4f8;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .info-label {
            font-weight: bold;
            color: #555;
        }

        .footer {
            text-align: center;
            font-size: 0.8em;
            color: #888;
            margin-top: 20px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Nouveau Signalement Important</h2>
        </div>

        <div class="content">
            <p>Bonjour,</p>
            <p>Un nouveau problème a été signalé concernant l'un de vos voyages. Voici les détails :</p>

            <div class="info-block">
                <p><span class="info-label">Type d'incident :</span> {{ ucfirst($signalement->type) }}</p>
                <p><span class="info-label">Date :</span> {{ $signalement->created_at->format('d/m/Y H:i') }}</p>
                <p><span class="info-label">Véhicule :</span>
                    @if($signalement->programme && $signalement->programme->vehicule)
                        {{ $signalement->programme->vehicule->immatriculation }} -
                        {{ $signalement->programme->vehicule->marque }}
                    @elseif($signalement->vehicule_id)
                        {{ \App\Models\Vehicule::find($signalement->vehicule_id)->immatriculation ?? 'Non défini' }}
                    @else
                        Non spécifié
                    @endif
                </p>
                <p><span class="info-label">Trajet :</span>
                    @if($signalement->programme)
                        {{ $signalement->programme->point_depart }} -> {{ $signalement->programme->point_arrive }}
                    @else
                        -
                    @endif
                </p>
            </div>

            <p><span class="info-label">Description du problème :</span></p>
            <blockquote style="border-left: 4px solid #dc3545; padding-left: 10px; color: #555; font-style: italic;">
                {{ $signalement->description }}
            </blockquote>

            @if($signalement->type == 'accident')
                <p style="color: red; font-weight: bold;">
                    ⚠️ Cet incident est signalé comme un ACCIDENT. Les services de secours ont été notifiés si nécessaire.
                </p>
            @endif

            <p>Veuillez vous connecter à votre espace compagnie pour traiter ce signalement.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Sécurité Transport. Ceci est un message automatique.</p>
        </div>
    </div>
</body>

</html>