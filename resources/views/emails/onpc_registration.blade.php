<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>CAR 225 - Compte ONPC créé</title>
</head>

<body style="font-family: Arial, sans-serif; background:#f4f6f8; margin:0; padding:24px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,.08);">
        <tr>
            <td align="center" style="background:linear-gradient(135deg,#0f172a,#1e3a8a);padding:30px;">
                <img src="{{ $logoUrl }}" alt="Logo Car 225" width="120" style="margin-bottom:8px;">
                <h1 style="color:#fff;font-size:20px;margin:8px 0 0 0;">Office National de la Protection Civile</h1>
            </td>
        </tr>
        <tr>
            <td style="padding:32px 32px 24px 32px;">
                <h2 style="color:#0f172a;margin:0 0 12px 0;">Votre compte ONPC a été créé</h2>
                <p style="color:#334155;line-height:1.6;">
                    Bonjour, vous avez été enregistré(e) en tant qu'agent ONPC sur la plateforme CAR 225.
                </p>
                <p style="color:#334155;line-height:1.6;">
                    Cliquez sur le bouton ci-dessous pour valider votre compte et définir votre mot de passe.
                    Saisissez ensuite le code suivant dans le formulaire :
                </p>

                <div style="background:#f1f5f9;border-left:4px solid #1e3a8a;padding:18px;text-align:center;font-size:24px;font-weight:bold;letter-spacing:6px;color:#1e3a8a;border-radius:8px;margin:20px 0;">
                    {{ $code }}
                </div>

                <p style="text-align:center;margin:24px 0;">
                    <a href="{{ route('onpc.define-access', $email) }}"
                        style="background:#1e3a8a;color:#fff;text-decoration:none;padding:14px 28px;border-radius:8px;font-weight:bold;display:inline-block;">
                        Valider mon compte
                    </a>
                </p>

                <p style="color:#64748b;font-size:13px;line-height:1.6;margin-top:24px;">
                    Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer ce message.
                </p>
            </td>
        </tr>
        <tr>
            <td style="background:#0f172a;color:#94a3b8;text-align:center;padding:16px;font-size:12px;">
                © {{ date('Y') }} CAR 225 — Plateforme officielle
            </td>
        </tr>
    </table>
</body>

</html>
