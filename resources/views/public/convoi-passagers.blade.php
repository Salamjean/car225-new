<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passager — {{ $convoi->reference }}</title>
    <link rel="shortcut icon" href="{{ asset('assetsPoster/assets/images/logo_car225.png') }}" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f8fafc; }
        .hero-brand {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 70%, #1a1030 100%);
            padding: 24px 0 0;
            position: relative;
            overflow: hidden;
        }
        .hero-brand::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 280px; height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(249,115,22,.3) 0%, transparent 70%);
            pointer-events: none;
        }
        .field-label {
            display: block;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #64748b;
            margin-bottom: 6px;
        }
        .field-input {
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            background: #f8fafc;
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .field-input:focus {
            border-color: #f97316;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(249,115,22,.12);
        }
        .field-input::placeholder { color: #94a3b8; }
        .btn-submit {
            width: 100%;
            padding: 18px;
            border-radius: 16px;
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: #fff;
            font-size: 15px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border: none;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(249,115,22,.35);
            transition: all .2s;
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 8px 28px rgba(249,115,22,.45); }
        .btn-submit:disabled { opacity: .6; cursor: not-allowed; transform: none; }
    </style>
</head>
<body>

    {{-- Header brand --}}
    <div class="hero-brand">
        <div class="max-w-lg mx-auto px-5 relative z-10">
            <div class="flex items-center gap-3 mb-6">
                <img src="{{ asset('assetsPoster/assets/images/logo_car225.png') }}"
                     class="w-10 h-10 rounded-xl object-contain" alt="CAR225" onerror="this.style.display='none'">
                <div>
                    <div class="text-white font-black text-lg tracking-tight">CAR225</div>
                    <div class="text-orange-400 text-xs font-bold">Transport & Convois</div>
                </div>
            </div>

            {{-- Recap card --}}
            <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-5 mb-0">
                <div class="text-white/50 text-[10px] font-black uppercase tracking-widest mb-3">
                    <i class="fas fa-users mr-1"></i> Inscription passager — Convoi
                </div>
                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div>
                        <div class="text-white font-black text-lg tracking-tight">
                            {{ $convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? '—') }}
                            <span class="text-orange-400 mx-2">→</span>
                            {{ $convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? '—') }}
                        </div>
                        <div class="text-white/60 text-sm font-semibold mt-1">
                            <i class="far fa-calendar-alt mr-1 text-orange-400"></i>
                            Départ : {{ $convoi->date_depart ? \Carbon\Carbon::parse($convoi->date_depart)->format('d M Y') : '—' }}
                            @if($convoi->heure_depart) à {{ substr($convoi->heure_depart, 0, 5) }} @endif
                        </div>
                        @if($convoi->lieu_rassemblement)
                        <div class="text-white/60 text-sm font-semibold mt-1">
                            <i class="fas fa-map-pin mr-1 text-orange-400"></i>
                            {{ $convoi->lieu_rassemblement }}
                        </div>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="text-orange-300 text-xs font-black uppercase tracking-wider mb-1">Places</div>
                        <div class="bg-white/15 text-white font-black text-xl px-4 py-2 rounded-xl">{{ $placesLabel }}</div>
                        @if($convoi->gare)
                        <div class="text-white/50 text-xs font-semibold mt-2">
                            <i class="fas fa-building mr-1"></i>{{ $convoi->gare->nom_gare }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div style="margin-top:-1px">
            <svg viewBox="0 0 1440 40" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:block;width:100%;">
                <path d="M0 40 L0 20 Q360 0 720 20 Q1080 40 1440 20 L1440 40 Z" fill="#f8fafc"/>
            </svg>
        </div>
    </div>

    {{-- Content --}}
    <div class="max-w-lg mx-auto px-5 py-6">

        {{-- Status banner --}}
        @if($existingPassager)
        <div class="bg-green-50 border border-green-200 rounded-2xl px-5 py-4 mb-6 flex gap-3 items-start">
            <i class="fas fa-check-circle text-green-500 text-lg flex-shrink-0 mt-0.5"></i>
            <div>
                <p class="text-green-800 font-black text-sm">Vous êtes déjà inscrit(e) !</p>
                <p class="text-green-600 font-semibold text-xs mt-1">Vous pouvez mettre à jour vos informations ci-dessous si nécessaire, puis cliquer sur <strong>Enregistrer</strong>.</p>
            </div>
        </div>
        @else
        <div class="bg-blue-50 border border-blue-200 rounded-2xl px-5 py-4 mb-6 flex gap-3 items-start">
            <i class="fas fa-info-circle text-blue-500 text-lg flex-shrink-0 mt-0.5"></i>
            <div>
                <p class="text-blue-800 font-black text-sm">Renseignez vos informations personnelles</p>
                <p class="text-blue-600 font-semibold text-xs mt-1">Remplissez uniquement <strong>vos propres informations</strong>. Chaque passager reçoit ce lien individuellement.</p>
            </div>
        </div>
        @endif

        {{-- Errors --}}
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-2xl px-5 py-4 mb-5">
            <p class="text-red-700 font-black text-sm mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Corrigez les erreurs :</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-red-600 text-xs font-semibold">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('public.convoi.passagers.store', $token) }}" method="POST" id="passengerForm">
            @csrf
            <input type="hidden" name="device_id" value="{{ $deviceId }}">

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
                <h2 class="text-base font-black text-gray-900 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-orange-50 border border-orange-200 flex items-center justify-center text-orange-600 font-black text-sm flex-shrink-0">
                        <i class="fas fa-user text-xs"></i>
                    </span>
                    {{ $existingPassager ? 'Mettre à jour mes informations' : 'Mes informations' }}
                </h2>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="field-label">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="nom" class="field-input"
                               value="{{ old('nom', $existingPassager->nom ?? '') }}"
                               placeholder="Ex : KONAN" required autocomplete="family-name">
                        @error('nom')<p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="field-label">Prénoms <span class="text-red-500">*</span></label>
                        <input type="text" name="prenoms" class="field-input"
                               value="{{ old('prenoms', $existingPassager->prenoms ?? '') }}"
                               placeholder="Ex : Kouamé" required autocomplete="given-name">
                        @error('prenoms')<p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="field-label">Mon numéro de contact <span class="text-red-500">*</span></label>
                    <input type="tel" name="contact" class="field-input"
                           value="{{ old('contact', $existingPassager->contact ?? '') }}"
                           placeholder="07xxxxxxxx" required maxlength="10" inputmode="numeric"
                           oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)"
                           autocomplete="tel">
                    @error('contact')<p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>@enderror
                    <p class="text-xs text-gray-400 mt-1.5">10 chiffres — sera utilisé pour vous contacter en cas de besoin.</p>
                </div>

                <div>
                    <label class="field-label">Contact d'urgence <span class="text-gray-400 text-[10px] normal-case font-semibold">(optionnel)</span></label>
                    <input type="tel" name="contact_urgence" class="field-input"
                           value="{{ old('contact_urgence', $existingPassager->contact_urgence ?? '') }}"
                           placeholder="05xxxxxxxx" maxlength="10" inputmode="numeric"
                           oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)">
                    @error('contact_urgence')<p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>@enderror
                    <p class="text-xs text-gray-400 mt-1.5">Numéro d'un proche à prévenir en cas d'urgence.</p>
                </div>
            </div>

            <div class="mt-5 sticky bottom-4">
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-{{ $existingPassager ? 'save' : 'check-circle' }} mr-2"></i>
                    {{ $existingPassager ? 'Enregistrer les modifications' : 'Confirmer mon inscription' }}
                </button>
            </div>
        </form>

        <p class="text-center text-xs text-gray-400 font-semibold mt-6">
            <i class="fas fa-shield-alt mr-1 text-orange-400"></i>
            Formulaire sécurisé CAR225 — Vos données sont protégées
        </p>
    </div>

<script>
document.getElementById('passengerForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Enregistrement...';
});
</script>
</body>
</html>
