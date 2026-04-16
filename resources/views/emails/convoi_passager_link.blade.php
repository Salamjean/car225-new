<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Renseignez vos passagers — CAR225</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:'Inter',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:32px 16px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">

    {{-- Header --}}
    <tr>
        <td style="background:linear-gradient(135deg,#0f172a,#1e293b);padding:32px 40px;text-align:center;">
            <div style="font-size:24px;font-weight:900;color:#fff;letter-spacing:-0.5px;">CAR225</div>
            <div style="font-size:12px;color:#f97316;font-weight:700;margin-top:4px;">Transport & Convois</div>
        </td>
    </tr>

    {{-- Body --}}
    <tr>
        <td style="padding:36px 40px;">
            <p style="font-size:16px;font-weight:800;color:#0f172a;margin:0 0 8px;">
                Bonjour {{ $convoi->client_prenom ?? 'Client' }},
            </p>
            <p style="font-size:14px;color:#475569;margin:0 0 24px;line-height:1.6;">
                Votre convoi <strong>{{ $convoi->reference }}</strong> a bien été enregistré par la gare
                @if($convoi->gare) <strong>{{ $convoi->gare->nom_gare }}</strong> @endif.
                Pour préparer votre voyage, veuillez renseigner la liste des <strong>{{ $convoi->nombre_personnes }} passagers</strong> en cliquant sur le bouton ci-dessous.
            </p>

            {{-- Convoi info --}}
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:14px;padding:20px;margin-bottom:28px;">
                <tr>
                    <td style="font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:0.6px;color:#94a3b8;padding-bottom:12px;">
                        Détails du convoi
                    </td>
                </tr>
                <tr>
                    <td style="font-size:13px;color:#334155;font-weight:700;padding:4px 0;">
                        🗺️ <strong>Trajet :</strong>
                        {{ $convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? '—') }}
                        →
                        {{ $convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? '—') }}
                    </td>
                </tr>
                <tr>
                    <td style="font-size:13px;color:#334155;font-weight:700;padding:4px 0;">
                        📅 <strong>Départ :</strong>
                        {{ $convoi->date_depart ? \Carbon\Carbon::parse($convoi->date_depart)->format('d M Y') : '—' }}
                        @if($convoi->heure_depart) à {{ substr($convoi->heure_depart, 0, 5) }} @endif
                    </td>
                </tr>
                <tr>
                    <td style="font-size:13px;color:#334155;font-weight:700;padding:4px 0;">
                        👥 <strong>Nombre de passagers :</strong> {{ $convoi->nombre_personnes }}
                    </td>
                </tr>
            </table>

            {{-- CTA Button --}}
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" style="padding-bottom:24px;">
                        <a href="{{ $lien }}"
                           style="display:inline-block;padding:16px 40px;border-radius:12px;background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;font-size:14px;font-weight:900;text-decoration:none;letter-spacing:0.5px;box-shadow:0 4px 16px rgba(249,115,22,.35);">
                            Renseigner la liste des passagers
                        </a>
                    </td>
                </tr>
            </table>

            <p style="font-size:12px;color:#94a3b8;text-align:center;margin:0;">
                Ou copiez ce lien dans votre navigateur :<br>
                <a href="{{ $lien }}" style="color:#f97316;font-weight:700;word-break:break-all;">{{ $lien }}</a>
            </p>
        </td>
    </tr>

    {{-- Footer --}}
    <tr>
        <td style="background:#f8fafc;padding:20px 40px;text-align:center;border-top:1px solid #e2e8f0;">
            <p style="font-size:11px;color:#94a3b8;margin:0;">
                🔒 Lien sécurisé CAR225 — Ne partagez pas ce lien avec des tiers.
            </p>
        </td>
    </tr>

</table>
</td></tr>
</table>
</body>
</html>
