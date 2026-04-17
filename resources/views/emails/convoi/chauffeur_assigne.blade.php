<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Convoi CAR225 — Chauffeur assigné</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,.08); }
        .header { background: linear-gradient(135deg, #f97316, #ea580c); padding: 32px 24px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; }
        .header p { color: rgba(255,255,255,.85); margin: 6px 0 0; font-size: 14px; }
        .body { padding: 28px 24px; }
        .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .info-row .label { color: #64748b; font-weight: 600; }
        .info-row .value { color: #0f172a; font-weight: 800; }
        .highlight { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 14px 18px; margin: 20px 0; }
        .highlight .title { font-size: 13px; font-weight: 900; color: #166534; margin-bottom: 6px; }
        .highlight .val { font-size: 16px; font-weight: 900; color: #15803d; }
        .footer { background: #f8fafc; padding: 18px 24px; text-align: center; font-size: 11px; color: #94a3b8; }
        .btn { display: inline-block; margin: 18px auto; padding: 12px 28px; background: #f97316; color: #fff; text-decoration: none; border-radius: 10px; font-weight: 900; font-size: 13px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🚌 Votre convoi est prêt !</h1>
        <p>Un chauffeur a été assigné à votre convoi CAR225</p>
    </div>
    <div class="body">
        <p style="font-size:15px;color:#0f172a;font-weight:700;margin-bottom:20px;">
            Bonjour {{ $convoi->created_by_gare ? ($convoi->client_prenom . ' ' . $convoi->client_nom) : ($convoi->user->prenom ?? $convoi->user->name ?? 'Client') }},
        </p>
        <p style="font-size:14px;color:#475569;margin-bottom:20px;">Un chauffeur et un véhicule ont été affectés à votre convoi. Voici les détails de votre voyage :</p>

        <div class="info-row"><span class="label">Référence</span><span class="value">{{ $convoi->reference }}</span></div>
        <div class="info-row"><span class="label">Trajet</span><span class="value">{{ $convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? '-') }} → {{ $convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? '-') }}</span></div>
        <div class="info-row"><span class="label">Date de départ</span><span class="value">{{ $convoi->date_depart ? \Carbon\Carbon::parse($convoi->date_depart)->format('d/m/Y') : '-' }}{{ $convoi->heure_depart ? ' à ' . substr($convoi->heure_depart, 0, 5) : '' }}</span></div>
        <div class="info-row"><span class="label">Nombre de passagers</span><span class="value">{{ $convoi->nombre_personnes }}</span></div>

        @if($convoi->chauffeur)
        <div class="highlight">
            <div class="title">🧑‍✈️ Chauffeur assigné</div>
            <div class="val">{{ trim(($convoi->chauffeur->prenom ?? '') . ' ' . ($convoi->chauffeur->name ?? '')) }}</div>
            @if($convoi->chauffeur->contact)
            <div style="font-size:13px;color:#15803d;margin-top:4px;">📞 {{ $convoi->chauffeur->contact }}</div>
            @endif
        </div>
        @endif

        @if($convoi->vehicule)
        <div class="info-row"><span class="label">Véhicule</span><span class="value">{{ $convoi->vehicule->immatriculation }}{{ $convoi->vehicule->modele ? ' — ' . $convoi->vehicule->modele : '' }}</span></div>
        @endif

        @if($convoi->lieu_rassemblement)
        <div class="info-row"><span class="label">Lieu de rassemblement</span><span class="value">{{ $convoi->lieu_rassemblement }}</span></div>
        @endif

        @if($convoi->passagers->count() > 0)
        <div style="margin-top:20px;">
            <p style="font-size:13px;font-weight:900;color:#475569;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Liste des passagers</p>
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:#f8fafc;">
                        <th style="padding:8px 12px;text-align:left;color:#64748b;font-weight:700;">#</th>
                        <th style="padding:8px 12px;text-align:left;color:#64748b;font-weight:700;">Nom</th>
                        <th style="padding:8px 12px;text-align:left;color:#64748b;font-weight:700;">Prénoms</th>
                        <th style="padding:8px 12px;text-align:left;color:#64748b;font-weight:700;">Contact</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($convoi->passagers as $i => $p)
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:8px 12px;color:#94a3b8;">{{ $i + 1 }}</td>
                        <td style="padding:8px 12px;font-weight:700;">{{ $p->nom ?: '—' }}</td>
                        <td style="padding:8px 12px;font-weight:700;">{{ $p->prenoms ?: '—' }}</td>
                        <td style="padding:8px 12px;">{{ $p->contact ?: '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <p style="font-size:13px;color:#64748b;margin-top:24px;line-height:1.6;">
            Merci de vous présenter au lieu de rassemblement à l'heure indiquée. En cas de problème, contactez directement la gare.
        </p>
    </div>
    <div class="footer">
        © {{ date('Y') }} CAR225 — Ne pas répondre à cet email.
    </div>
</div>
</body>
</html>
