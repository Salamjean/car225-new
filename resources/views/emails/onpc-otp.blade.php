<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: Arial, sans-serif; background:#f4f6f8; margin:0; padding:24px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,.08);">
        <tr>
            <td align="center" style="background:linear-gradient(135deg,#0f172a,#1e3a8a);padding:30px;color:#fff;">
                <h1 style="margin:0;font-size:20px;">Réinitialisation du mot de passe ONPC</h1>
            </td>
        </tr>
        <tr>
            <td style="padding:32px;">
                <p style="color:#334155;line-height:1.6;">Bonjour {{ $name ?? 'Agent' }},</p>
                <p style="color:#334155;line-height:1.6;">
                    Vous avez demandé la réinitialisation de votre mot de passe sur l'espace ONPC de CAR 225.
                    Saisissez le code suivant dans le formulaire :
                </p>

                <div style="background:#f1f5f9;border-left:4px solid #1e3a8a;padding:18px;text-align:center;font-size:26px;font-weight:bold;letter-spacing:8px;color:#1e3a8a;border-radius:8px;margin:20px 0;">
                    {{ $otp }}
                </div>

                <p style="color:#64748b;font-size:13px;line-height:1.6;">
                    Ce code expire dans 10 minutes. Si vous n'êtes pas à l'origine de cette demande, ignorez ce message.
                </p>
            </td>
        </tr>
        <tr>
            <td style="background:#0f172a;color:#94a3b8;text-align:center;padding:14px;font-size:12px;">
                © {{ date('Y') }} CAR 225 — ONPC
            </td>
        </tr>
    </table>
</body>
</html>
