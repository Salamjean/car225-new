<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu Convoi — CAR225</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; color: #334155; }
        .wrap { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.1); }

        /* Header */
        .hd { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 36px 32px; text-align: center; }
        .hd-logo { display: inline-block; background: #f97316; color: #fff; font-size: 24px; font-weight: 900; padding: 10px 20px; border-radius: 10px; letter-spacing: 1px; margin-bottom: 10px; }
        .hd-sub { color: rgba(255,255,255,.6); font-size: 12px; font-weight: 600; }

        /* Hero */
        .hero { background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); padding: 24px 32px; text-align: center; }
        .hero-title { color: #fff; font-size: 20px; font-weight: 900; margin-bottom: 4px; }
        .hero-ref { color: rgba(255,255,255,.8); font-size: 13px; font-weight: 700; }

        /* Content */
        .content { padding: 32px; }

        .greeting { font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 12px; }
        .intro { font-size: 14px; color: #475569; line-height: 1.7; margin-bottom: 24px; }

        /* Route */
        .route-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 20px; }
        .route-text { font-size: 18px; font-weight: 900; color: #0f172a; }
        .route-arrow { color: #f97316; margin: 0 10px; }
        .route-date { font-size: 13px; color: #64748b; font-weight: 600; margin-top: 8px; }

        /* Info table */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table tr td { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; font-size: 13px; }
        .info-table tr td:first-child { color: #94a3b8; font-weight: 700; width: 40%; }
        .info-table tr td:last-child { color: #1e293b; font-weight: 800; }

        /* Montant */
        .amount-box { background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 20px; }
        .amount-label { font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: #16a34a; margin-bottom: 6px; }
        .amount-value { font-size: 32px; font-weight: 900; color: #15803d; }
        .amount-unit { font-size: 14px; color: #22c55e; font-weight: 700; }

        /* Lieu */
        .lieu-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 16px; margin-bottom: 20px; }
        .lieu-label { font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.8px; color: #92400e; margin-bottom: 6px; }
        .lieu-value { font-size: 14px; font-weight: 800; color: #78350f; }

        /* Note */
        .note { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; font-size: 13px; color: #1d4ed8; font-weight: 600; line-height: 1.6; }

        /* Attachment note */
        .attach-note { background: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; font-size: 13px; color: #5b21b6; font-weight: 600; }
        .attach-note strong { display: block; margin-bottom: 3px; font-size: 13px; color: #3b0764; }

        /* Footer */
        .footer { background: #f8fafc; padding: 22px 32px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer-text { font-size: 12px; color: #94a3b8; font-weight: 600; margin-bottom: 4px; }
        .footer-brand { font-size: 13px; color: #f97316; font-weight: 900; }
    </style>
</head>
<body>
<div class="wrap">

    <!-- Header -->
    <div class="hd">
        <div class="hd-logo">CAR 225</div>
        <div class="hd-sub">Plateforme de transport — Côte d'Ivoire</div>
    </div>

    <!-- Hero -->
    <div class="hero">
        <div class="hero-title">✅ Convoi enregistré avec succès</div>
        <div class="hero-ref">Référence : {{ $convoi->reference }}</div>
    </div>

    <!-- Content -->
    <div class="content">

        <div class="greeting">Bonjour {{ $convoi->client_prenom }} {{ $convoi->client_nom }},</div>
        <div class="intro">
            Votre convoi a bien été enregistré auprès de la gare <strong>{{ $convoi->gare->nom_gare ?? 'CAR225' }}</strong>.
            Vous trouverez ci-dessous le récapitulatif ainsi que votre reçu en pièce jointe (PDF).
        </div>

        <!-- Route -->
        <div class="route-box">
            <div class="route-text">
                {{ $convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? '—') }}
                <span class="route-arrow">→</span>
                {{ $convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? '—') }}
            </div>
            @if($convoi->date_depart)
            <div class="route-date">
                Départ : {{ \Carbon\Carbon::parse($convoi->date_depart)->format('d/m/Y') }}
                @if($convoi->heure_depart) à {{ substr($convoi->heure_depart, 0, 5) }}@endif
                @if($convoi->date_retour)
                &nbsp;·&nbsp; Retour : {{ \Carbon\Carbon::parse($convoi->date_retour)->format('d/m/Y') }}
                @endif
            </div>
            @endif
        </div>

        <!-- Détails -->
        <table class="info-table">
            <tr>
                <td>Référence</td>
                <td>{{ $convoi->reference }}</td>
            </tr>
            <tr>
                <td>Nombre de passagers</td>
                <td>{{ $convoi->nombre_personnes }} personne(s)</td>
            </tr>
            <tr>
                <td>Compagnie</td>
                <td>{{ $convoi->compagnie->name ?? '—' }}</td>
            </tr>
            <tr>
                <td>Gare</td>
                <td>{{ $convoi->gare->nom_gare ?? '—' }}</td>
            </tr>
            <tr>
                <td>Statut</td>
                <td><span style="color:#16a34a;font-weight:900;">✅ Payé</span></td>
            </tr>
        </table>

        <!-- Montant -->
        <div class="amount-box">
            <div class="amount-label">Montant total payé</div>
            <div class="amount-value">{{ number_format($convoi->montant, 0, ',', ' ') }}</div>
            <div class="amount-unit">FCFA</div>
        </div>

        <!-- Lieu rassemblement -->
        @if($convoi->lieu_rassemblement)
        <div class="lieu-box">
            <div class="lieu-label">📍 Lieu de rassemblement</div>
            <div class="lieu-value">{{ $convoi->lieu_rassemblement }}</div>
        </div>
        @endif

        <!-- Pièce jointe -->
        <div class="attach-note">
            <strong>📎 Reçu PDF joint à cet e-mail</strong>
            Votre reçu officiel est disponible en pièce jointe. Conservez-le et présentez-le le jour du départ.
        </div>

        <!-- Note -->
        <div class="note">
            ℹ️ Le chauffeur et le véhicule vous seront communiqués prochainement par SMS au
            <strong>{{ $convoi->client_contact }}</strong>. Pour toute question, contactez directement la gare.
        </div>

    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-brand">CAR 225</div>
        <div class="footer-text">Plateforme de transport — Côte d'Ivoire</div>
        <div class="footer-text" style="margin-top:8px;">Cet e-mail a été envoyé automatiquement, merci de ne pas y répondre.</div>
    </div>

</div>
</body>
</html>
