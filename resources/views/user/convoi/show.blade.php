@extends('user.layouts.template')

@section('title', 'Détail Convoi')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="inline-flex bg-white border border-gray-100 rounded-2xl p-1">
            <a href="{{ route('user.convoi.create') }}" class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider text-gray-600">
                Nouveau convoi
            </a>
            <a href="{{ route('user.convoi.index') }}" class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider bg-[#e94f1b] text-white">
                Mes convois
            </a>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-[#1A1D1F] tracking-tight">
                    Détail <span class="text-[#e94f1b]">Convoi</span>
                </h1>
                <p class="text-sm text-gray-500 font-medium">Référence : {{ $convoi->reference }}</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                @if(in_array($convoi->statut, ['paye', 'en_cours', 'termine']))
                <a href="{{ route('user.convoi.recu-pdf', $convoi) }}" target="_blank"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl text-xs font-black uppercase tracking-wider transition-all"
                   style="background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;box-shadow:0 4px 14px rgba(249,115,22,.35);">
                    <i class="fas fa-print"></i>
                    Imprimer le reçu
                </a>
                @endif
                <a href="{{ route('user.convoi.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-gray-100 text-gray-700 text-xs font-black uppercase tracking-wider hover:bg-gray-200 transition-all">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-700 font-semibold text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700 font-semibold text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Infos générales --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1">Compagnie</p>
                <p class="text-sm font-bold text-gray-900">{{ $convoi->compagnie->name ?? '-' }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1">Personnes</p>
                <p class="text-sm font-bold text-gray-900">{{ $convoi->nombre_personnes }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1">Statut</p>
                @php
                    $statusMap = [
                        'en_attente' => ['label' => 'En attente',   'class' => 'bg-amber-50 text-amber-700'],
                        'valide'     => ['label' => 'Validé',       'class' => 'bg-blue-50 text-blue-700'],
                        'refuse'     => ['label' => 'Refusé',       'class' => 'bg-red-50 text-red-700'],
                        'paye'       => ['label' => 'Payé',         'class' => 'bg-green-50 text-green-700'],
                        'en_cours'   => ['label' => 'En cours',     'class' => 'bg-indigo-50 text-indigo-700'],
                        'termine'    => ['label' => 'Terminé',      'class' => 'bg-gray-50 text-gray-700'],
                        'annule'     => ['label' => 'Annulé',       'class' => 'bg-red-50 text-red-700'],
                    ];
                    $s = $statusMap[$convoi->statut] ?? ['label' => ucfirst($convoi->statut), 'class' => 'bg-gray-50 text-gray-700'];
                @endphp
                <span class="inline-flex px-3 py-1 rounded-lg text-[10px] font-black uppercase {{ $s['class'] }}">{{ $s['label'] }}</span>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1">Date départ</p>
                <p class="text-sm font-bold text-gray-900">
                    {{ $convoi->date_depart ? \Carbon\Carbon::parse($convoi->date_depart)->format('d/m/Y') : '-' }}
                    @if($convoi->heure_depart)
                        <span class="text-gray-500">à {{ $convoi->heure_depart }}</span>
                    @endif
                </p>
            </div>
            <div class="col-span-2 bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1">Itinéraire</p>
                <p class="text-sm font-bold text-gray-900">
                    {{ $convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? '-') }}
                    <span class="text-[#e94f1b] mx-2">→</span>
                    {{ $convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? '-') }}
                </p>
            </div>
            @if($convoi->date_retour)
            <div class="col-span-2 bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1">Date de retour</p>
                <p class="text-sm font-bold text-gray-900">
                    {{ \Carbon\Carbon::parse($convoi->date_retour)->format('d/m/Y') }}
                    @if($convoi->heure_retour)
                        <span class="text-gray-500">à {{ $convoi->heure_retour }}</span>
                    @endif
                </p>
            </div>
            @endif
        </div>

        {{-- STATUT: EN_ATTENTE → info gare --}}
        @if ($convoi->statut === 'en_attente')
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-hourglass-half text-amber-600"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-amber-800 uppercase tracking-wider mb-1">Demande envoyée à la gare</h3>
                        <p class="text-sm text-amber-700 font-medium">
                            Votre demande a bien été transmise à
                            @if($convoi->gare)
                                <strong>{{ $convoi->gare->nom_gare }}</strong>.
                            @else
                                la gare sélectionnée.
                            @endif
                            La gare examine votre demande et vous contactera rapidement pour vous communiquer le montant.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- STATUT: REFUSE → afficher motif --}}
        @if ($convoi->statut === 'refuse')
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-red-800 uppercase tracking-wider mb-1">Demande refusée</h3>
                        <p class="text-sm text-red-700 font-medium">{{ $convoi->motif_refus }}</p>
                        <a href="{{ route('user.convoi.create') }}"
                            class="mt-3 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#e94f1b] text-white text-xs font-black uppercase tracking-wider hover:bg-[#d44518] transition-all">
                            <i class="fas fa-plus"></i>
                            Nouvelle demande
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- STATUT: VALIDE → paiement --}}
        @if ($convoi->statut === 'valide')
            <div class="bg-white rounded-[28px] border border-blue-100 shadow-sm p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                        <i class="fas fa-check-circle text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">Convoi validé — Paiement requis</h3>
                        <p class="text-xs text-gray-500 font-medium">La gare a validé votre demande et fixé le montant. Lisez le règlement et procédez au paiement.</p>
                    </div>
                </div>

                <div class="bg-blue-50 rounded-2xl p-5 mb-5">
                    <p class="text-[10px] font-black uppercase tracking-wider text-blue-500 mb-1">Montant à régler</p>
                    <p class="text-3xl font-black text-blue-800">
                        {{ number_format($convoi->montant, 0, ',', ' ') }} <span class="text-lg">FCFA</span>
                    </p>
                </div>

                {{-- Règlement --}}
                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5 mb-5 max-h-48 overflow-y-auto text-sm text-gray-700 leading-relaxed space-y-2">
                    <p class="font-black text-gray-900 text-xs uppercase tracking-wider mb-3">Règlement des convois CAR225</p>
                    <p><strong>1. Réservation :</strong> Toute demande de convoi est soumise à la validation par la gare. Le montant fixé est définitif.</p>
                    <p><strong>2. Paiement :</strong> Le paiement doit être effectué en totalité avant la mise à disposition du véhicule et du chauffeur. Aucun remboursement ne sera effectué après le départ.</p>
                    <p><strong>3. Passagers :</strong> La liste des passagers doit être complète avant la date de départ. La compagnie se réserve le droit de refuser tout passager non enregistré.</p>
                    <p><strong>4. Annulation :</strong> Toute annulation doit être notifiée à la compagnie au moins 48h avant la date de départ. Au-delà, aucun remboursement ne sera possible.</p>
                    <p><strong>5. Responsabilité :</strong> CAR225 et la compagnie ne sauraient être tenus responsables de tout incident imputable au non-respect de ce règlement par le demandeur.</p>
                </div>

                @if ($errors->has('reglement_accepte'))
                    <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 mb-4 text-red-700 text-sm font-semibold">
                        {{ $errors->first('reglement_accepte') }}
                    </div>
                @endif

                <form action="{{ route('user.convoi.pay', $convoi) }}" method="POST">
                    @csrf
                    <label class="flex items-start gap-3 cursor-pointer mb-5">
                        <input type="checkbox" name="reglement_accepte" value="1" class="mt-1 rounded accent-[#e94f1b]" @checked(old('reglement_accepte'))>
                        <span class="text-sm text-gray-700 font-semibold">J'ai lu et j'accepte le règlement des convois CAR225.</span>
                    </label>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl bg-[#e94f1b] text-white text-xs font-black uppercase tracking-widest shadow-lg shadow-[#e94f1b]/20 hover:bg-[#d44518] transition-all">
                        <i class="fas fa-credit-card"></i>
                        Payer {{ number_format($convoi->montant, 0, ',', ' ') }} FCFA
                    </button>
                </form>
            </div>
        @endif

        {{-- STATUT: PAYE → formulaire passagers --}}
        @if ($convoi->statut === 'paye' || $convoi->statut === 'en_cours' || $convoi->statut === 'termine')
            @if ($convoi->statut === 'paye' && !$convoi->gare_id)
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-clock text-amber-600"></i>
                        <div>
                            <p class="text-sm font-black text-amber-800">Paiement confirmé</p>
                            <p class="text-xs text-amber-700 font-medium">Paiement confirmé ! La gare va affecter un chauffeur et un véhicule. Vous pouvez dès maintenant renseigner vos passagers.</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Lieu de rassemblement + garant + passagers — FORMULAIRE UNIQUE --}}
            @if ($convoi->statut === 'paye')
            <form action="{{ route('user.convoi.store-passengers', $convoi) }}" method="POST" id="mainConvoiForm">
            @csrf
            {{-- Hidden is_garant (synced par JS avec le toggle) --}}
            <input type="hidden" name="is_garant" id="hiddenIsGarant" value="{{ $convoi->is_garant ? '1' : '0' }}">

            {{-- Erreurs serveur --}}
            @if ($errors->any())
            <div class="rounded-2xl bg-red-50 border border-red-200 px-5 py-4 text-red-700 text-sm font-semibold">
                <i class="fas fa-exclamation-circle mr-2"></i>
                @if ($errors->has('lieu_rassemblement'))
                    {{ $errors->first('lieu_rassemblement') }}
                @else
                    Veuillez corriger les erreurs ci-dessous.
                @endif
            </div>
            @endif

            {{-- Section: Lieu de rassemblement --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-map-pin text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">Lieu de rassemblement & options</h3>
                        <p class="text-xs text-gray-500 font-medium">Indiquez où le car doit venir vous chercher, et choisissez votre mode d'inscription.</p>
                    </div>
                </div>
                <div class="space-y-5">
                    {{-- Lieu de rassemblement --}}
                    <div>
                        <label class="block text-xs font-black text-gray-600 uppercase tracking-wider mb-2">
                            <i class="fas fa-map-marker-alt mr-1 text-[#e94f1b]"></i> Lieu de rassemblement <span class="text-[#e94f1b]">*</span>
                        </label>
                        <input type="text" name="lieu_rassemblement" id="lieuRassemblementInput"
                            value="{{ old('lieu_rassemblement', $convoi->lieu_rassemblement) }}"
                            placeholder="Ex: Devant la pharmacie centrale, Carrefour Akwaba..."
                            required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#e94f1b]/30 focus:border-[#e94f1b]">
                        <p class="text-xs text-gray-400 mt-1.5">Obligatoire — le chauffeur quittera la gare pour venir vous chercher à ce lieu.</p>
                    </div>

                    {{-- Toggle garant --}}
                    <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                        <label class="flex items-start gap-4 cursor-pointer">
                            <div class="relative flex-shrink-0 mt-0.5">
                                <input type="checkbox" id="toggleGarant"
                                    {{ $convoi->is_garant ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-10 h-6 bg-gray-200 rounded-full peer-checked:bg-[#e94f1b] transition-colors duration-200 peer-focus:ring-2 peer-focus:ring-[#e94f1b]/30"></div>
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-4"></div>
                            </div>
                            <div>
                                <p class="text-sm font-black text-indigo-800">Je me porte garant pour le groupe</p>
                                <p class="text-xs text-indigo-600 font-medium mt-1">
                                    Activez cette option si vous êtes le seul responsable du groupe (famille, collègues…).
                                    Seules <strong>vos informations</strong> seront demandées — vous êtes le point de contact en cas de problème.
                                    Les autres passagers n'auront pas besoin d'être enregistrés individuellement.
                                </p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            @elseif($convoi->lieu_rassemblement || $convoi->is_garant)
            {{-- Affichage en lecture seule si plus modifiable --}}
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 flex flex-wrap gap-6">
                @if($convoi->lieu_rassemblement)
                <div>
                    <p class="text-xs font-black text-blue-700 uppercase tracking-wider mb-1"><i class="fas fa-map-pin mr-1"></i> Lieu de rassemblement</p>
                    <p class="text-sm font-bold text-blue-900">{{ $convoi->lieu_rassemblement }}</p>
                </div>
                @endif
                @if($convoi->is_garant)
                <div>
                    <p class="text-xs font-black text-blue-700 uppercase tracking-wider mb-1"><i class="fas fa-user-shield mr-1"></i> Mode garant</p>
                    <p class="text-sm font-bold text-blue-900">Vous êtes le garant du groupe</p>
                </div>
                @endif
            </div>
            @endif

            <div class="bg-white rounded-[28px] border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-wider">
                        <i class="fas fa-users text-[#e94f1b] mr-2"></i>
                        @if($convoi->is_garant)
                            Passagers — Mode Garant
                            <span class="ml-2 text-[10px] px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-full font-black normal-case tracking-normal">
                                <i class="fas fa-user-shield mr-1"></i>Garant
                            </span>
                        @else
                            Passagers ({{ $convoi->passagers->count() }} / {{ $convoi->nombre_personnes }})
                        @endif
                    </h3>
                </div>

                @if ($convoi->statut === 'paye')
                @php
                    $passagersExistants = $convoi->passagers->keyBy(fn($p, $k) => $k)->values();
                @endphp
                @if($convoi->is_garant)
                <div class="mx-6 mt-4 mb-0 px-4 py-3 bg-indigo-50 border border-indigo-100 rounded-xl text-xs font-semibold text-indigo-700" id="garantBanner">
                    <i class="fas fa-user-shield mr-1"></i> Mode garant activé — renseignez uniquement vos informations personnelles.
                </div>
                @else
                <div class="mx-6 mt-4 mb-0 px-4 py-3 bg-indigo-50 border border-indigo-100 rounded-xl text-xs font-semibold text-indigo-700 hidden" id="garantBanner">
                    <i class="fas fa-user-shield mr-1"></i> Mode garant activé — renseignez uniquement vos informations personnelles.
                </div>
                @endif
                    <div class="p-6">
                        <div class="space-y-4" id="passagersContainer">
                            @for ($i = 0; $i < $convoi->nombre_personnes; $i++)
                            @php $p = $passagersExistants[$i] ?? null; @endphp
                            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 passenger-row" data-index="{{ $i }}"
                                 style="{{ $convoi->is_garant && $i > 0 ? 'display:none;' : '' }}">
                                <div class="mb-3">
                                    <span class="text-xs font-black text-gray-500 uppercase tracking-wider passenger-row-label" data-index="{{ $i }}">
                                        {{ $convoi->is_garant && $i === 0 ? 'Vos informations (Garant)' : 'Passager ' . ($i + 1) }}
                                        @if(!$convoi->is_garant)
                                        <span class="text-[10px] font-semibold text-gray-400 normal-case tracking-normal ml-1">/ {{ $convoi->nombre_personnes }}</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                    <div>
                                        <input type="text" name="passagers[{{ $i }}][nom]"
                                            value="{{ old("passagers.$i.nom", $p->nom ?? '') }}"
                                            placeholder="Nom"
                                            class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-[#e94f1b] outline-none">
                                    </div>
                                    <div>
                                        <input type="text" name="passagers[{{ $i }}][prenoms]"
                                            value="{{ old("passagers.$i.prenoms", $p->prenoms ?? '') }}"
                                            placeholder="Prénoms"
                                            class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-[#e94f1b] outline-none">
                                    </div>
                                    <div>
                                        <input type="tel" name="passagers[{{ $i }}][contact]"
                                            value="{{ old("passagers.$i.contact", $p->contact ?? '') }}"
                                            placeholder="Contact (10 chiffres)"
                                            maxlength="10" pattern="[0-9]{10}"
                                            inputmode="numeric"
                                            oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                            class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-[#e94f1b] outline-none">
                                    </div>
                                    <div>
                                        <input type="tel" name="passagers[{{ $i }}][contact_urgence]"
                                            value="{{ old("passagers.$i.contact_urgence", $p->contact_urgence ?? '') }}"
                                            placeholder="Contact d'urgence (10 chiffres)"
                                            maxlength="10" pattern="[0-9]{10}"
                                            inputmode="numeric"
                                            oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                            class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-[#e94f1b] outline-none">
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>
                        <div class="flex justify-end mt-5">
                            <button type="submit" form="mainConvoiForm"
                                class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl bg-[#e94f1b] text-white text-xs font-black uppercase tracking-widest shadow-lg shadow-[#e94f1b]/20 hover:bg-[#d44518] transition-all">
                                <i class="fas fa-save"></i>
                                Enregistrer
                            </button>
                        </div>
                    </div>
                </form>{{-- fin mainConvoiForm --}}
                @else
                {{-- Lecture seule quand en_cours ou terminé --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-5 py-4 text-[10px] font-black uppercase tracking-wider text-gray-500">#</th>
                                <th class="px-5 py-4 text-[10px] font-black uppercase tracking-wider text-gray-500">Nom</th>
                                <th class="px-5 py-4 text-[10px] font-black uppercase tracking-wider text-gray-500">Prénoms</th>
                                <th class="px-5 py-4 text-[10px] font-black uppercase tracking-wider text-gray-500">Contact</th>
                                <th class="px-5 py-4 text-[10px] font-black uppercase tracking-wider text-gray-500">Contact d'urgence</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($convoi->passagers as $i => $passager)
                            <tr>
                                <td class="px-5 py-4 text-xs font-black text-gray-700">{{ $i + 1 }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-gray-900">{{ $passager->nom }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-gray-900">{{ $passager->prenoms }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-gray-700">{{ $passager->contact }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-gray-500">{{ $passager->contact_urgence ?: '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-400 text-sm font-semibold">Aucun passager enregistré.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        @endif
    </div>

    @php
        $authUserData = [
            'nom'             => $authUser->name ?? '',
            'prenoms'         => $authUser->prenom ?? '',
            'contact'         => $authUser->contact ?? '',
            'contact_urgence' => $authUser->contact_urgence ?? '',
        ];
    @endphp

    {{-- Modal confirmation mode garant --}}
    <div id="confirmGarantModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="cancelGarant()"></div>
        {{-- Card --}}
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">
            {{-- Top accent --}}
            <div class="h-1.5 w-full bg-gradient-to-r from-indigo-500 to-[#e94f1b]"></div>
            <div class="p-8">
                {{-- Icon --}}
                <div class="flex justify-center mb-5">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-50 flex items-center justify-center">
                        <i class="fas fa-user-shield text-2xl text-indigo-600"></i>
                    </div>
                </div>
                {{-- Title --}}
                <h2 class="text-center text-lg font-black text-gray-900 mb-2">Confirmer le mode Garant</h2>
                <p class="text-center text-sm text-gray-500 font-medium mb-6 leading-relaxed">
                    En activant ce mode, <strong class="text-gray-800">vous serez le seul responsable</strong> du groupe.<br>
                    Seules vos informations personnelles seront enregistrées — les autres passagers ne seront pas nominativement déclarés.
                </p>
                {{-- Callout --}}
                <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-6 text-xs font-semibold text-amber-800">
                    <i class="fas fa-triangle-exclamation mr-2 text-amber-600"></i>
                    Cette décision engage votre responsabilité envers la compagnie et les passagers que vous représentez.
                </div>
                {{-- Buttons --}}
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="button" onclick="cancelGarant()"
                        class="flex-1 px-5 py-3 rounded-2xl border border-gray-200 bg-white text-gray-700 text-xs font-black uppercase tracking-wider hover:bg-gray-50 transition-all">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </button>
                    <button type="button" onclick="confirmGarant()"
                        class="flex-1 px-5 py-3 rounded-2xl bg-[#e94f1b] text-white text-xs font-black uppercase tracking-wider shadow-lg shadow-[#e94f1b]/25 hover:bg-[#d44518] transition-all">
                        <i class="fas fa-check mr-2"></i>Oui, je confirme
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    // ── 1. Forcer uniquement des chiffres sur les champs contact ─────────────
    document.querySelectorAll('input[name$="[contact]"], input[name$="[contact_urgence]"]').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
        });
    });

    // ── 2. Toggle garant : afficher/masquer les lignes passagers ─────────────
    const toggleGarant   = document.getElementById('toggleGarant');
    const hiddenIsGarant = document.getElementById('hiddenIsGarant');
    const passengerRows  = document.querySelectorAll('.passenger-row');

    // Infos de l'utilisateur connecté (pour pré-remplir passager 0)
    const authUser = @json($authUserData);

    // Sauvegarde des valeurs originales de la ligne 0 avant pré-remplissage
    let row0OriginalValues = null;

    function getRow0Fields() {
        const row0 = document.querySelector('.passenger-row[data-index="0"]');
        if (!row0) return null;
        return {
            nom:             row0.querySelector('input[name="passagers[0][nom]"]'),
            prenoms:         row0.querySelector('input[name="passagers[0][prenoms]"]'),
            contact:         row0.querySelector('input[name="passagers[0][contact]"]'),
            contact_urgence: row0.querySelector('input[name="passagers[0][contact_urgence]"]'),
        };
    }

    function prefillRow0WithUser() {
        const fields = getRow0Fields();
        if (!fields) return;
        // Sauvegarder les valeurs actuelles avant de pré-remplir
        row0OriginalValues = {
            nom:             fields.nom?.value ?? '',
            prenoms:         fields.prenoms?.value ?? '',
            contact:         fields.contact?.value ?? '',
            contact_urgence: fields.contact_urgence?.value ?? '',
        };
        if (fields.nom)             fields.nom.value             = authUser.nom;
        if (fields.prenoms)         fields.prenoms.value         = authUser.prenoms;
        if (fields.contact)         fields.contact.value         = authUser.contact;
        if (fields.contact_urgence) fields.contact_urgence.value = authUser.contact_urgence;
    }

    function restoreRow0() {
        if (!row0OriginalValues) return;
        const fields = getRow0Fields();
        if (!fields) return;
        if (fields.nom)             fields.nom.value             = row0OriginalValues.nom;
        if (fields.prenoms)         fields.prenoms.value         = row0OriginalValues.prenoms;
        if (fields.contact)         fields.contact.value         = row0OriginalValues.contact;
        if (fields.contact_urgence) fields.contact_urgence.value = row0OriginalValues.contact_urgence;
        row0OriginalValues = null;
    }

    function applyGarantMode(isGarant, prefill) {
        passengerRows.forEach(function(row) {
            const idx = parseInt(row.dataset.index, 10);
            if (idx > 0) {
                row.style.display = isGarant ? 'none' : '';
            }
        });
        if (hiddenIsGarant) hiddenIsGarant.value = isGarant ? '1' : '0';

        // Mettre à jour le label du passager 0
        const label0 = document.querySelector('.passenger-row-label[data-index="0"]');
        if (label0) {
            label0.innerHTML = isGarant
                ? 'Vos informations (Garant)'
                : 'Passager 1 <span class="text-[10px] font-semibold text-gray-400 normal-case tracking-normal ml-1">/ {{ $convoi->nombre_personnes }}</span>';
        }

        // Afficher/masquer le bandeau garant
        const garantBanner = document.getElementById('garantBanner');
        if (garantBanner) {
            garantBanner.classList.toggle('hidden', !isGarant);
        }

        if (isGarant && prefill) {
            prefillRow0WithUser();
        } else if (!isGarant && row0OriginalValues !== null) {
            restoreRow0();
        }
    }

    if (toggleGarant) {
        toggleGarant.addEventListener('change', function() {
            applyGarantMode(this.checked, true);
        });
        // Appliquer l'état initial au chargement (sans écraser les données déjà saisies)
        applyGarantMode(toggleGarant.checked, false);
    }

    // ── 3. Confirmation modale avant enregistrement quand garant ON ──────────
    const mainForm     = document.getElementById('mainConvoiForm');
    const confirmModal = document.getElementById('confirmGarantModal');
    let   garantConfirmed = false;

    if (mainForm) {
        mainForm.addEventListener('submit', function(e) {
            if (toggleGarant && toggleGarant.checked && !garantConfirmed) {
                e.preventDefault();
                confirmModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        });
    }

    function confirmGarant() {
        garantConfirmed = true;
        confirmModal.classList.add('hidden');
        document.body.style.overflow = '';
        if (mainForm) mainForm.submit();
    }

    function cancelGarant() {
        confirmModal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Fermer avec Echap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') cancelGarant();
    });
    </script>
    @endpush
@endsection
