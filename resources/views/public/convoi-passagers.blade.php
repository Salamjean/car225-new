<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Liste des passagers — {{ $convoi->reference }}</title>
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
        .hero-brand::after {
            content: '';
            position: absolute;
            bottom: 0; left: -40px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99,102,241,.2) 0%, transparent 70%);
            pointer-events: none;
        }

        .pass-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 20px;
            margin-bottom: 14px;
            transition: box-shadow .2s;
        }
        .pass-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.06); }

        .pass-num-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px; height: 30px;
            border-radius: 10px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #ea580c;
            font-size: 13px;
            font-weight: 900;
            flex-shrink: 0;
        }

        .field-label {
            display: block;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #64748b;
            margin-bottom: 6px;
        }
        .field-input {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 13px;
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
            padding: 16px;
            border-radius: 14px;
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: #fff;
            font-size: 14px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border: none;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(249,115,22,.35);
            transition: all .2s;
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 8px 28px rgba(249,115,22,.45); }
        .btn-submit:disabled { opacity: .6; cursor: not-allowed; transform: none; box-shadow: none; }

        .info-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 800;
        }

        .progress-bar-track {
            background: #f1f5f9;
            border-radius: 99px;
            height: 6px;
            overflow: hidden;
        }
        .progress-bar-fill {
            height: 100%;
            border-radius: 99px;
            background: linear-gradient(90deg, #f97316, #ea580c);
            transition: width .5s ease;
        }
    </style>
</head>
<body>

    {{-- ── Header brand ── --}}
    <div class="hero-brand">
        <div class="max-w-2xl mx-auto px-5 relative z-10">
            <div class="flex items-center gap-3 mb-6">
                <img src="{{ asset('assetsPoster/assets/images/logo_car225.png') }}"
                     class="w-10 h-10 rounded-xl object-contain" alt="CAR225" onerror="this.style.display='none'">
                <div>
                    <div class="text-white font-black text-lg tracking-tight">CAR225</div>
                    <div class="text-orange-400 text-xs font-bold">Transport & Convois</div>
                </div>
            </div>

            {{-- Convoi recap card --}}
            <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-5 mb-0">
                <div class="text-white/50 text-[10px] font-900 uppercase tracking-widest mb-3">
                    <i class="fas fa-users mr-1"></i> Formulaire passagers — Convoi
                </div>
                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div>
                        <div class="text-white font-black text-xl tracking-tight">
                            {{ $convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? '—') }}
                            <span class="text-orange-400 mx-2">→</span>
                            {{ $convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? '—') }}
                        </div>
                        <div class="text-white/60 text-sm font-semibold mt-1">
                            <i class="far fa-calendar-alt mr-1 text-orange-400"></i>
                            Départ : {{ $convoi->date_depart ? \Carbon\Carbon::parse($convoi->date_depart)->format('d M Y') : '—' }}
                            @if($convoi->heure_depart)
                                à {{ substr($convoi->heure_depart, 0, 5) }}
                            @endif
                        </div>
                        @if($convoi->lieu_rassemblement)
                        <div class="text-white/60 text-sm font-semibold mt-1">
                            <i class="fas fa-map-pin mr-1 text-orange-400"></i>
                            Rassemblement : {{ $convoi->lieu_rassemblement }}
                        </div>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="text-orange-300 text-xs font-black uppercase tracking-wider mb-1">Réf.</div>
                        <div class="bg-white/15 text-white font-black text-sm px-3 py-1.5 rounded-lg">{{ $convoi->reference }}</div>
                        <div class="text-white/50 text-xs font-semibold mt-2">
                            {{ $convoi->nombre_personnes }} passager(s)
                        </div>
                        @if($convoi->gare)
                        <div class="text-white/50 text-xs font-semibold mt-1">
                            <i class="fas fa-building mr-1"></i>{{ $convoi->gare->nom_gare }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Wave separator --}}
        <div style="margin-top:-1px">
            <svg viewBox="0 0 1440 40" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:block;width:100%;">
                <path d="M0 40 L0 20 Q360 0 720 20 Q1080 40 1440 20 L1440 40 Z" fill="#f8fafc"/>
            </svg>
        </div>
    </div>

    {{-- ── Content ── --}}
    <div class="max-w-2xl mx-auto px-5 py-6">

        {{-- Intro --}}
        <div class="bg-blue-50 border border-blue-200 rounded-2xl px-5 py-4 mb-6 flex gap-3">
            <i class="fas fa-info-circle text-blue-500 text-lg flex-shrink-0 mt-0.5"></i>
            <div>
                <p class="text-blue-800 font-black text-sm">Renseignez les informations de vos {{ $convoi->nombre_personnes }} passagers</p>
                <p class="text-blue-600 font-semibold text-xs mt-1">Tous les champs sont obligatoires. Ces informations seront transmises à la gare pour la préparation du convoi.</p>
            </div>
        </div>

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

        {{-- Progress --}}
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-black text-gray-500 uppercase tracking-wider">Passagers à renseigner</span>
            <span class="text-xs font-black text-orange-500" id="progressLabel">0 / {{ $convoi->nombre_personnes }}</span>
        </div>
        <div class="progress-bar-track mb-6">
            <div class="progress-bar-fill" id="progressBar" style="width:0%"></div>
        </div>

        <form action="{{ route('public.convoi.passagers.store', $token) }}" method="POST" id="passagersForm">
            @csrf

            <div id="passagersContainer">
            @php $existants = $convoi->passagers->values(); @endphp
            @for($i = 0; $i < $convoi->nombre_personnes; $i++)
            @php $p = $existants[$i] ?? null; @endphp
            <div class="pass-card" data-idx="{{ $i }}">
                <div class="flex items-center gap-3 mb-4">
                    <span class="pass-num-badge">{{ $i + 1 }}</span>
                    <span class="text-sm font-black text-gray-700 uppercase tracking-wider">Passager {{ $i + 1 }}</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="field-label">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="passagers[{{ $i }}][nom]" class="field-input pass-input"
                               value="{{ old("passagers.$i.nom", $p->nom ?? '') }}"
                               placeholder="Ex : KONAN" required>
                    </div>
                    <div>
                        <label class="field-label">Prénoms <span class="text-red-500">*</span></label>
                        <input type="text" name="passagers[{{ $i }}][prenoms]" class="field-input pass-input"
                               value="{{ old("passagers.$i.prenoms", $p->prenoms ?? '') }}"
                               placeholder="Ex : Kouamé" required>
                    </div>
                    <div>
                        <label class="field-label">Contact <span class="text-red-500">*</span></label>
                        <input type="tel" name="passagers[{{ $i }}][contact]" class="field-input pass-input"
                               value="{{ old("passagers.$i.contact", $p->contact ?? '') }}"
                               placeholder="07xxxxxxxx" required>
                    </div>
                    <div>
                        <label class="field-label">Contact urgence <span class="text-red-500">*</span></label>
                        <input type="tel" name="passagers[{{ $i }}][contact_urgence]" class="field-input pass-input"
                               value="{{ old("passagers.$i.contact_urgence", $p->contact_urgence ?? '') }}"
                               placeholder="05xxxxxxxx" required>
                    </div>
                </div>
            </div>
            @endfor
            </div>

            {{-- Submit --}}
            <div class="sticky bottom-4 mt-4">
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-check-circle mr-2"></i>
                    Valider et envoyer la liste des passagers
                </button>
            </div>
        </form>
    </div>

    {{-- ── Footer ── --}}
    <div class="text-center py-8 text-xs text-gray-400 font-semibold">
        <i class="fas fa-shield-alt mr-1 text-orange-400"></i>
        Formulaire sécurisé CAR225 — Vos données sont protégées
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const total    = {{ $convoi->nombre_personnes }};
    const inputs   = document.querySelectorAll('.pass-input');
    const progress = document.getElementById('progressBar');
    const label    = document.getElementById('progressLabel');
    const form     = document.getElementById('passagersForm');
    const btn      = document.getElementById('submitBtn');

    function updateProgress() {
        // Compte les cartes qui ont au moins nom + prénoms remplis
        let filled = 0;
        for (let i = 0; i < total; i++) {
            const nom     = form.querySelector(`input[name="passagers[${i}][nom]"]`);
            const prenoms = form.querySelector(`input[name="passagers[${i}][prenoms]"]`);
            if (nom && nom.value.trim() && prenoms && prenoms.value.trim()) filled++;
        }
        const pct = Math.round((filled / total) * 100);
        progress.style.width = pct + '%';
        label.textContent = filled + ' / ' + total;
    }

    inputs.forEach(function (input) {
        input.addEventListener('input', updateProgress);
    });

    updateProgress();

    form.addEventListener('submit', function () {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Envoi en cours...';
    });
});
</script>
</body>
</html>
