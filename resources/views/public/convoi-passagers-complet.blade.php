<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convoi complet — CAR225</title>
    <link rel="shortcut icon" href="{{ asset('assetsPoster/assets/images/logo_car225.png') }}" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>* { font-family: 'Inter', sans-serif; } body { background: #f8fafc; }</style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center px-5 py-12">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="h-2 bg-gradient-to-r from-red-500 to-orange-500"></div>
                <div class="p-8 text-center">
                    <div class="w-20 h-20 rounded-2xl bg-red-50 flex items-center justify-center mx-auto mb-5">
                        <i class="fas fa-users-slash text-3xl text-red-500"></i>
                    </div>
                    <h1 class="text-xl font-black text-gray-900 mb-2">Convoi complet</h1>
                    <p class="text-gray-500 font-semibold text-sm mb-6 leading-relaxed">
                        Toutes les <strong class="text-gray-800">{{ $convoi->nombre_personnes }} places</strong> de ce convoi sont déjà réservées.<br>
                        Il n'est plus possible de s'inscrire.
                    </p>
                    <div class="bg-gray-50 rounded-2xl p-4 text-left mb-6">
                        <div class="text-xs font-black text-gray-500 uppercase tracking-wider mb-3">Détails du convoi</div>
                        <div class="space-y-2 text-sm font-semibold text-gray-700">
                            <div><i class="fas fa-route text-orange-400 mr-2 w-4"></i>
                                {{ $convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? '—') }} → {{ $convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? '—') }}
                            </div>
                            <div><i class="far fa-calendar-alt text-orange-400 mr-2 w-4"></i>
                                {{ $convoi->date_depart ? \Carbon\Carbon::parse($convoi->date_depart)->format('d M Y') : '—' }}
                                @if($convoi->heure_depart) à {{ substr($convoi->heure_depart, 0, 5) }} @endif
                            </div>
                            @if($convoi->gare)
                            <div><i class="fas fa-building text-orange-400 mr-2 w-4"></i>{{ $convoi->gare->nom_gare }}</div>
                            @endif
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 font-semibold">
                        <i class="fas fa-shield-alt mr-1 text-orange-400"></i> CAR225 — Transport & Convois
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
