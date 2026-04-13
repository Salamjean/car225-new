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
            <a href="{{ route('user.convoi.index') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-gray-100 text-gray-700 text-xs font-black uppercase tracking-wider hover:bg-gray-200 transition-all">
                <i class="fas fa-arrow-left"></i>
                Retour
            </a>
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
                        <p class="text-xs text-gray-500 font-medium">La compagnie a fixé le montant. Lisez le règlement et procédez au paiement.</p>
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
                    <p><strong>1. Réservation :</strong> Toute demande de convoi est soumise à la validation préalable de la compagnie. Le montant fixé par la compagnie est définitif.</p>
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
                            <p class="text-xs text-amber-700 font-medium">La compagnie va assigner une gare à votre convoi. Vous pouvez d'ores et déjà renseigner vos passagers.</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-[28px] border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-wider">
                        <i class="fas fa-users text-[#e94f1b] mr-2"></i>
                        Passagers ({{ $convoi->passagers->count() }} / {{ $convoi->nombre_personnes }})
                    </h3>
                </div>

                @if ($convoi->statut === 'paye')
                @php
                    // Construire un tableau indexé des passagers existants
                    $passagersExistants = $convoi->passagers->keyBy(fn($p, $k) => $k)->values();
                @endphp
                <form action="{{ route('user.convoi.store-passengers', $convoi) }}" method="POST" id="passagersForm">
                    @csrf
                    <div class="p-6">
                        @if ($errors->has('passagers') || $errors->has('passagers.*') || $errors->has('passagers.*.nom') || $errors->has('passagers.*.contact'))
                            <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 mb-4 text-red-700 text-sm font-semibold">
                                Veuillez remplir tous les champs obligatoires (nom, prénoms, contact) pour chaque passager.
                            </div>
                        @endif
                        <div class="space-y-4">
                            @for ($i = 0; $i < $convoi->nombre_personnes; $i++)
                            @php $p = $passagersExistants[$i] ?? null; @endphp
                            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                <div class="mb-3">
                                    <span class="text-xs font-black text-gray-500 uppercase tracking-wider">
                                        Passager {{ $i + 1 }}
                                        <span class="text-[10px] font-semibold text-gray-400 normal-case tracking-normal ml-1">/ {{ $convoi->nombre_personnes }}</span>
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                    <div>
                                        <input type="text" name="passagers[{{ $i }}][nom]"
                                            value="{{ old("passagers.$i.nom", $p->nom ?? '') }}"
                                            placeholder="Nom *" required
                                            class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-[#e94f1b] outline-none">
                                    </div>
                                    <div>
                                        <input type="text" name="passagers[{{ $i }}][prenoms]"
                                            value="{{ old("passagers.$i.prenoms", $p->prenoms ?? '') }}"
                                            placeholder="Prénoms *" required
                                            class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-[#e94f1b] outline-none">
                                    </div>
                                    <div>
                                        <input type="tel" name="passagers[{{ $i }}][contact]"
                                            value="{{ old("passagers.$i.contact", $p->contact ?? '') }}"
                                            placeholder="Contact * (10 chiffres)"
                                            required maxlength="10" minlength="10" pattern="[0-9]{10}"
                                            inputmode="numeric"
                                            oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                            class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-[#e94f1b] outline-none">
                                    </div>
                                    <div>
                                        <input type="tel" name="passagers[{{ $i }}][contact_urgence]"
                                            value="{{ old("passagers.$i.contact_urgence", $p->contact_urgence ?? '') }}"
                                            placeholder="Contact d'urgence * (10 chiffres)"
                                            required maxlength="10" minlength="10" pattern="[0-9]{10}"
                                            inputmode="numeric"
                                            oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                            class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-[#e94f1b] outline-none">
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>
                        <div class="flex justify-end mt-5">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-8 py-3 rounded-2xl bg-[#e94f1b] text-white text-xs font-black uppercase tracking-widest shadow-lg shadow-[#e94f1b]/20 hover:bg-[#d44518] transition-all">
                                <i class="fas fa-save"></i>
                                Enregistrer les passagers
                            </button>
                        </div>
                    </div>
                </form>
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

    @push('scripts')
    <script>
    // Forcer uniquement des chiffres sur les champs contact
    document.querySelectorAll('input[name$="[contact]"]').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
        });
    });
    </script>
    @endpush
@endsection
