<!DOCTYPE html>
<html>
<head>
    <title>CAR 225 - Confirmation d'inscription</title>
</head>
<body>
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <img src="{{ $logoUrl }}" alt="Logo Car 225" width="150">
            </td>
        </tr>
        <tr>
            <td>
                <h1>Votre compagnie a été inscrite.</h1>
                <p>Votre compte a été créé avec succès sur la plateforme.</p>
                <p>Cliquez sur le bouton ci-dessous pour valider votre compte.</p>
                <p>Saisissez le code <strong>{{ $code }}</strong> dans le formulaire qui apparaîtra.</p>
                <p><a href="{{ url('/validate-compagny-account/' . $email) }}" style="background-color:#e94f1b; border: none; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; cursor: pointer;">Valider mon compte</a></p>
                <p>Merci d'utiliser notre application Car 225.</p>
            </td>
        </tr>
    </table>
</body>
</html>