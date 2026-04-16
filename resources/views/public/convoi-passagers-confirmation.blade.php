<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passagers enregistrés — CAR225</title>
    <link rel="shortcut icon" href="{{ asset('assetsPoster/assets/images/logo_car225.png') }}" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>* { font-family: 'Inter', sans-serif; } body { background: #f8fafc; }</style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center px-5">

    <div class="max-w-md w-full text-center">

        {{-- Logo --}}
        <div class="flex items-center justify-center gap-2 mb-8">
            <img src="{{ asset('assetsPoster/assets/images/logo_car225.png') }}"
                 class="w-10 h-10 rounded-xl object-contain" alt="CAR225" onerror="this.style.display='none'">
            <div class="font-black text-xl text-gray-900">CAR225</div>
        </div>

        {{-- Checkmark animation --}}
        <div class="w-24 h-24 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6"
             style="box-shadow: 0 0 0 12px rgba(34,197,94,.1);">
            <i class="fas fa-check text-green-500 text-4xl"></i>
        </div>

        <h1 class="text-2xl font-black text-gray-900 mb-2">Liste envoyée !</h1>
        <p class="text-gray-500 font-semibold text-sm mb-6">
            Les informations de vos passagers ont bien été transmises à la gare
            @if($convoi->gare)
                <strong class="text-gray-700">{{ $convoi->gare->nom_gare }}</strong>.
            @endif
            Vous serez contacté prochainement pour la suite.
        </p>

        {{-- Recap --}}
        <div class="bg-white border border-gray-100 rounded-2xl p-5 text-left mb-6 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Récapitulatif</p>
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500">Référence</span>
                    <span class="text-xs font-black text-orange-500">{{ $convoi->reference }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500">Trajet</span>
                    <span class="text-xs font-black text-gray-800">
                        {{ $convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? '—') }}
                        →
                        {{ $convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? '—') }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500">Date de départ</span>
                    <span class="text-xs font-black text-gray-800">
                        {{ $convoi->date_depart ? \Carbon\Carbon::parse($convoi->date_depart)->format('d M Y') : '—' }}
                        @if($convoi->heure_depart) à {{ substr($convoi->heure_depart, 0, 5) }} @endif
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500">Passagers enregistrés</span>
                    <span class="text-xs font-black text-green-600">{{ $convoi->passagers->count() }} / {{ $convoi->nombre_personnes }}</span>
                </div>
            </div>
        </div>

        <p class="text-xs text-gray-400 font-semibold">
            <i class="fas fa-shield-alt mr-1 text-orange-400"></i>
            Formulaire sécurisé CAR225 — Merci de votre confiance.
        </p>
    </div>

</body>
</html>
