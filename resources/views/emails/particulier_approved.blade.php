<!DOCTYPE html>
<html>
<head>
    <title>CAR 225 - Inscription Validée</title>
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
                <h1 style="color: #e94f1b; font-size: 22px; margin-bottom: 20px;">Félicitations, votre inscription est validée !</h1>
                <p>Bonjour,</p>
                <p>Votre compte de transporteur particulier a été activé avec succès par l'administrateur de CAR 225.</p>
                <p>Voici vos identifiants pour vous connecter au portail unifié :</p>
                <div style="background-color: #fff; padding: 15px; border-radius: 8px; border: 1px solid #ddd; margin: 15px 0;">
                    <p style="margin: 5px 0;"><strong>Identifiant (Code ID) :</strong> <span style="font-family: monospace; font-size: 16px; color: #e94f1b;">{{ $code_id }}</span></p>
                    <p style="margin: 5px 0;"><strong>Mot de passe :</strong> <span style="font-family: monospace; font-size: 16px;">{{ $password }}</span></p>
                </div>
                <p>Cliquez sur le lien ci-dessous pour accéder au portail de connexion et recevoir vos premières demandes de convoi :</p>
                <p style="margin: 25px 0; text-align: center;">
                    <a href="{{ route('portail.login') }}" style="background-color:#e94f1b; border: none; color: white; padding: 12px 30px; text-align: center; text-decoration: none; display: inline-block; font-size: 14px; font-weight: bold; cursor: pointer; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Accéder au portail</a>
                </p>
                <p style="font-size: 12px; color: #666; margin-top: 20px;">Nous vous recommandons de modifier votre mot de passe depuis votre profil une fois connecté.</p>
                <p>Merci d'utiliser notre application Car 225.</p>
            </td>
        </tr>
    </table>
</body>
</html>
