<!DOCTYPE html>
<html>

<head>
    <title>CAR 225 - Confirmation d'inscription Sapeur Pompier</title>
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
                <h1>Votre compte Sapeur Pompier a été créé.</h1>
                <p>Vous avez été enregistré en tant que Sapeur Pompier sur la plateforme.</p>
                <p>Cliquez sur le bouton ci-dessous pour valider votre compte et définir votre mot de passe.</p>
                <p>Saisissez le code <strong>{{ $code }}</strong> dans le formulaire qui apparaîtra.</p>
                <p><a href="{{ route('sapeur-pompier.define-access', $email) }}"
                        style="background-color:#e94f1b; border: none; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; cursor: pointer;">Valider
                        mon compte</a></p>
                <p>Merci d'utiliser notre application Car 225.</p>
            </td>
        </tr>
    </table>
</body>

</html>