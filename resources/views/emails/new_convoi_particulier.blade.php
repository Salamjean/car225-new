<!DOCTYPE html>
<html>
<head>
    <title>CAR 225 - Nouvelle demande de convoi</title>
</head>
<body>
    <table width="100%" cellpadding="0" cellspacing="0" style="font-family: sans-serif; color: #333;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <img src="{{ $logoUrl }}" alt="Logo Car 225" width="150">
            </td>
        </tr>
        <tr>
            <td style="padding: 20px; background-color: #f9f9f9; border-radius: 10px; border: 1px solid #eee;">
                <h1 style="color: #e94f1b; font-size: 22px; margin-bottom: 20px;">Nouvelle demande de convoi disponible !</h1>
                <p>Bonjour,</p>
                <p>Vous avez reçu une nouvelle demande de convoi de la part de l'utilisateur <strong>{{ $convoi->demandeur_nom }}</strong>.</p>
                <p>Voici les détails du trajet proposé :</p>
                <div style="background-color: #fff; padding: 15px; border-radius: 8px; border: 1px solid #ddd; margin: 15px 0;">
                    <p style="margin: 5px 0;"><strong>Référence :</strong> {{ $convoi->reference }}</p>
                    <p style="margin: 5px 0;"><strong>Trajet :</strong> {{ $convoi->lieu_depart }} &rarr; {{ $convoi->lieu_retour }}</p>
                    <p style="margin: 5px 0;"><strong>Départ :</strong> {{ \Carbon\Carbon::parse($convoi->date_depart)->format('d/m/Y') }} à {{ substr($convoi->heure_depart, 0, 5) }}</p>
                    @if($convoi->date_retour)
                        <p style="margin: 5px 0;"><strong>Retour :</strong> {{ \Carbon\Carbon::parse($convoi->date_retour)->format('d/m/Y') }} à {{ substr($convoi->heure_retour, 0, 5) }}</p>
                    @endif
                    <p style="margin: 5px 0;"><strong>Nombre de places :</strong> {{ $convoi->nombre_personnes }} places</p>
                </div>
                <p>Veuillez vous connecter sur votre espace particulier pour étudier cette demande, fixer votre prix (montant) ou la refuser.</p>
                <p style="margin: 25px 0; text-align: center;">
                    <a href="{{ route('portail.login') }}" style="background-color:#e94f1b; border: none; color: white; padding: 12px 30px; text-align: center; text-decoration: none; display: inline-block; font-size: 14px; font-weight: bold; cursor: pointer; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Accéder à mon espace</a>
                </p>
                <p>Merci d'utiliser notre application Car 225.</p>
            </td>
        </tr>
    </table>
</body>
</html>
