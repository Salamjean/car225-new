@extends('particulier.layouts.template')

@section('page-title')
    Détails du Convoi · {{ $convoi->reference }}
@endsection

@section('content')
    <div class="space-y-6">

        <!-- Back Button & Breadcrumbs -->
        <div class="flex items-center justify-between">
            <a href="{{ route('particulier.dashboard') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold text-xs shadow-sm transition-all">
                <i class="fas fa-arrow-left"></i> Retour au tableau de bord
            </a>
            
            @php
                $sm = [
                    'en_attente' => ['En attente',  'bg-amber-50 text-amber-700 border-amber-100',   'bg-amber-500'],
                    'valide'     => ['Validé',      'bg-blue-50 text-blue-700 border-blue-100',     'bg-blue-500'],
                    'refuse'     => ['Refusé',      'bg-red-50 text-red-700 border-red-100',       'bg-red-500'],
                    'paye'       => ['Payé',        'bg-green-50 text-green-700 border-green-100',   'bg-green-500'],
                    'en_cours'   => ['En cours',    'bg-indigo-50 text-indigo-700 border-indigo-100', 'bg-indigo-500'],
                    'termine'    => ['Terminé',     'bg-gray-100 text-gray-600 border-gray-200',    'bg-gray-400'],
                    'annule'     => ['Annulé',      'bg-red-50 text-red-700 border-red-100',       'bg-red-500'],
                ];
                [$slabel, $sclass, $sdot] = $sm[$convoi->statut] ?? [$convoi->statut, 'bg-gray-100 text-gray-600 border-gray-200', 'bg-gray-400'];
            @endphp
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full border {{ $sclass }} text-xs font-black uppercase">
                <span class="w-2 h-2 rounded-full {{ $sdot }}"></span> {{ $slabel }}
            </span>
        </div>

        @if (session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-r-xl shadow-sm font-semibold text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-r-xl shadow-sm font-semibold text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left 2 columns: Convoi details & Passengers -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Trajectory and dates -->
                <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm space-y-4">
                    <h3 class="font-black text-gray-800 text-base border-b border-gray-100 pb-3">
                        <i class="fas fa-map-marked-alt text-[#e94f1b] me-2"></i> Informations du Voyage
                    </h3>

                    <div class="flex items-start gap-4">
                        <div class="flex flex-col items-center mt-1">
                            <span class="w-4 h-4 rounded-full bg-emerald-500 border-4 border-emerald-100"></span>
                            <span class="w-0.5 h-12 bg-gray-200 border-dashed border-l my-1"></span>
                            <span class="w-4 h-4 rounded-full bg-red-500 border-4 border-red-100"></span>
                        </div>
                        <div class="flex-1 space-y-6">
                            <div>
                                <span class="text-[10px] font-black uppercase text-gray-400 block">Lieu de départ</span>
                                <span class="text-base font-bold text-gray-800">{{ $convoi->lieu_depart }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-black uppercase text-gray-400 block">Lieu d'arrivée</span>
                                <span class="text-base font-bold text-gray-800">{{ $convoi->lieu_retour }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-gray-100 pt-4">
                        <div class="space-y-2">
                            <div class="text-xs text-gray-500">
                                <i class="fas fa-calendar-alt text-[#e94f1b] me-2"></i> Date et Heure Aller :
                            </div>
                            <p class="font-bold text-gray-800">
                                {{ \Carbon\Carbon::parse($convoi->date_depart)->format('d/m/Y') }} à {{ substr($convoi->heure_depart, 0, 5) }}
                            </p>
                        </div>

                        @if($convoi->date_retour)
                            <div class="space-y-2">
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-history text-[#e94f1b] me-2"></i> Date et Heure Retour :
                                </div>
                                <p class="font-bold text-gray-800">
                                    {{ \Carbon\Carbon::parse($convoi->date_retour)->format('d/m/Y') }} à {{ substr($convoi->heure_retour, 0, 5) }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Passengers List (Visible from confirme status) -->
                @if(in_array($convoi->statut, ['confirme', 'paye', 'en_cours', 'termine']))
                    <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm space-y-4">
                        <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                            <h3 class="font-black text-gray-800 text-base">
                                <i class="fas fa-users text-[#e94f1b] me-2"></i> Liste des Passagers
                            </h3>
                            <span class="px-2.5 py-1 rounded-lg bg-orange-50 text-[#e94f1b] text-xs font-black">
                                {{ $convoi->passagers->count() }} / {{ $convoi->nombre_personnes }} passager(s)
                            </span>
                        </div>

                        {{-- Lieux de rassemblement --}}
                        @if($convoi->lieu_rassemblement)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-100">
                                    <span class="text-[10px] font-black uppercase text-emerald-700 block mb-0.5"><i class="fas fa-map-pin me-1"></i> Lieu rassemblement aller</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $convoi->lieu_rassemblement }}</span>
                                </div>
                                @if($convoi->lieu_rassemblement_retour)
                                    <div class="p-3 rounded-xl bg-blue-50 border border-blue-100">
                                        <span class="text-[10px] font-black uppercase text-blue-700 block mb-0.5"><i class="fas fa-map-pin me-1"></i> Lieu rassemblement retour</span>
                                        <span class="text-sm font-bold text-gray-800">{{ $convoi->lieu_rassemblement_retour }}</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="p-3 rounded-xl bg-amber-50 border border-amber-100">
                                <p class="text-xs font-semibold text-amber-700"><i class="fas fa-hourglass-half me-1"></i> L'utilisateur n'a pas encore renseigné le lieu de rassemblement.</p>
                            </div>
                        @endif

                        @if($convoi->passagers->isEmpty())
                            <p class="text-center py-6 text-gray-400 font-semibold">L'utilisateur n'a pas encore renseigné la liste des passagers.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase border-b border-gray-100">
                                        <tr>
                                            <th class="px-4 py-3">Nom &amp; Prénoms</th>
                                            <th class="px-4 py-3">Contact</th>
                                            <th class="px-4 py-3">Contact d'urgence</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 text-sm">
                                        @foreach($convoi->passagers as $index => $passager)
                                            <tr>
                                                <td class="px-4 py-3 font-bold text-gray-800">
                                                    {{ $index + 1 }}. {{ $passager->nom }} {{ $passager->prenoms }}
                                                </td>
                                                <td class="px-4 py-3 font-mono font-semibold text-gray-600">
                                                    {{ $passager->contact ?: 'Non précisé' }}
                                                </td>
                                                <td class="px-4 py-3 font-mono font-semibold text-gray-600">
                                                    {{ $passager->contact_urgence ?: 'Non précisé' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm text-center py-8">
                        <i class="fas fa-users-slash text-gray-300 text-4xl mb-2"></i>
                        <p class="text-sm text-gray-500 font-semibold">La liste des passagers sera visible après que le client ait confirmé le convoi.</p>
                    </div>
                @endif
            </div>

            <!-- Right column: Pricing, Client details & Actions -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Client Details -->
                <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm space-y-4">
                    <h3 class="font-black text-gray-800 text-base border-b border-gray-100 pb-2">
                        <i class="fas fa-user-circle text-[#e94f1b] me-2"></i> Coordonnées Client
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-[10px] font-black uppercase text-gray-400 block">Nom complet</span>
                            <span class="text-sm font-bold text-gray-800">{{ $convoi->demandeur_nom }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase text-gray-400 block">Téléphone</span>
                            <span class="text-sm font-bold text-gray-800">{{ $convoi->demandeur_contact }}</span>
                        </div>
                        @if($convoi->user && $convoi->user->email)
                            <div>
                                <span class="text-[10px] font-black uppercase text-gray-400 block">Email</span>
                                <span class="text-sm font-semibold text-gray-700 break-all">{{ $convoi->user->email }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Price and Operations -->
                <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm space-y-4">
                    <h3 class="font-black text-gray-800 text-base border-b border-gray-100 pb-2">
                        <i class="fas fa-hand-holding-usd text-[#e94f1b] me-2"></i> Facturation
                    </h3>

                    @if($convoi->montant)
                        <div class="p-4 rounded-xl bg-orange-50/50 border border-orange-100 text-center">
                            <span class="text-[10px] font-black uppercase text-orange-600 block mb-1">Montant fixé</span>
                            <span class="text-3xl font-black text-[#e94f1b]">
                                {{ number_format($convoi->montant, 0, ',', ' ') }}
                                <span class="text-sm font-bold">FCFA</span>
                            </span>
                        </div>
                    @else
                        <div class="p-4 rounded-xl bg-amber-50 border border-amber-100 text-center">
                            <span class="text-xs font-semibold text-amber-800"><i class="fas fa-exclamation-triangle me-1"></i> Prix non défini.</span>
                        </div>
                    @endif

                    <!-- Warning banner if claimed by another carrier -->
                    <div id="alreadyClaimedWarning" class="hidden p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs font-bold leading-normal">
                        <i class="fas fa-exclamation-circle me-1 text-red-600"></i> Ce convoi vient d'être récupéré par un autre transporteur particulier. Vous ne pouvez plus y répondre.
                    </div>

                    <!-- ACTIONS FOR EN_ATTENTE -->
                    @if($convoi->statut === 'en_attente')

                        {{-- Si le client a fait une contre-offre, proposer d'accepter ou de contre-proposer --}}
                        @if($convoi->dernier_offreur === 'client' && $convoi->montant_propose_client)
                            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 space-y-3">
                                <div class="text-center">
                                    <span class="text-[10px] font-black uppercase text-blue-600 block mb-1"><i class="fas fa-handshake me-1"></i> Offre du client</span>
                                    <span class="text-2xl font-black text-blue-700">{{ number_format($convoi->montant_propose_client, 0, ',', ' ') }} <span class="text-sm">FCFA</span></span>
                                </div>
                                {{-- Accepter l'offre du client --}}
                                <form action="{{ route('particulier.convoi.accepter-offre-client', $convoi) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition-all shadow-md">
                                        <i class="fas fa-check-circle"></i> Accepter cette offre
                                    </button>
                                </form>
                            </div>

                            {{-- Faire une contre-proposition --}}
                            <form action="{{ route('particulier.convoi.contre-proposer', $convoi) }}" method="POST" class="space-y-2 border-t border-gray-100 pt-3">
                                @csrf
                                <label class="text-[10px] font-black uppercase text-gray-400 block mb-1.5">Proposer un autre montant (FCFA)</label>
                                <input type="number" name="montant" placeholder="Ex: 135000" min="100" required
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none font-bold text-sm">
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition-all shadow-md">
                                    <i class="fas fa-paper-plane"></i> Envoyer ma contre-offre
                                </button>
                            </form>

                        @else
                            {{-- Cas normal : fixer le prix et valider --}}
                            <form action="{{ route('particulier.convoi.valider', $convoi) }}" method="POST" class="space-y-3 pt-2">
                                @csrf
                                <div>
                                    <label class="text-[10px] font-black uppercase text-gray-400 block mb-1.5">Saisir le montant de la prestation (FCFA)</label>
                                    <input type="number" name="montant" placeholder="Ex: 150000" min="100" required
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none font-bold text-sm">
                                </div>
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-[#e94f1b] hover:bg-[#d44518] text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition-all shadow-md">
                                    <i class="fas fa-check"></i> Valider &amp; Fixer le prix
                                </button>
                            </form>

                            <!-- Refuser convoi -->
                            <form action="{{ route('particulier.convoi.refuser', $convoi) }}" method="POST" class="space-y-3 pt-2">
                                @csrf
                                <div>
                                    <label class="text-[10px] font-black uppercase text-gray-400 block mb-1.5">Motif du refus</label>
                                    <textarea name="motif_refus" placeholder="Expliquez la raison du refus (indisponibilité, capacité insuffisante, etc.)" required rows="3"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none font-semibold text-xs"></textarea>
                                </div>
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                                    <i class="fas fa-ban"></i> Refuser la demande
                                </button>
                            </form>
                        @endif
                    @endif

                    <!-- ACTIONS FOR PAID / IN_PROGRESS -->
                    @if(in_array($convoi->statut, ['paye', 'en_cours']))
                        <div class="space-y-3 pt-2">
                            <!-- Démarrer Voyage (if paid) -->
                            @if($convoi->statut === 'paye')
                                <form action="{{ route('particulier.convoi.annuler', $convoi) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="demarrer">
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition-all shadow-md">
                                        <i class="fas fa-play"></i> Démarrer le trajet (Aller)
                                    </button>
                                </form>
                            @endif

                            <!-- Clôturer Voyage (if in progress) -->
                            @if($convoi->statut === 'en_cours')
                                <form action="{{ route('particulier.convoi.annuler', $convoi) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="terminer">
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition-all shadow-md">
                                        <i class="fas fa-flag-checkered"></i> Marquer comme terminé
                                    </button>
                                </form>
                            @endif

                            <!-- Annuler convoi -->
                            <form action="{{ route('particulier.convoi.annuler', $convoi) }}" method="POST" class="space-y-3 border-t border-gray-100 pt-3">
                                @csrf
                                <input type="hidden" name="action" value="annuler">
                                <div>
                                    <label class="text-[10px] font-black uppercase text-gray-400 block mb-1.5">Raison de l'annulation d'urgence</label>
                                    <textarea name="motif_annulation" placeholder="Raison de l'annulation (panne technique, incident majeur, etc.)" required rows="2"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none font-semibold text-xs"></textarea>
                                </div>
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                                    <i class="fas fa-times-circle"></i> Annuler le convoi
                                </button>
                            </form>
                        </div>
                    @endif

                    @if($convoi->statut === 'valide')
                        <div class="p-3 bg-blue-50 border border-blue-100 rounded-xl text-center space-y-1">
                            <p class="text-xs font-semibold text-blue-700"><i class="fas fa-clock me-1"></i> En attente de confirmation par le client.</p>
                            @if($convoi->dernier_offreur === 'particulier')
                                <p class="text-[10px] text-blue-500 font-medium">Vous avez proposé <strong>{{ number_format($convoi->montant, 0, ',', ' ') }} FCFA</strong>. Le client examine votre offre.</p>
                            @endif
                        </div>
                        {{-- Option contre-proposer quand c'est le chauffeur qui a fait la dernière offre --}}
                        <form action="{{ route('particulier.convoi.contre-proposer', $convoi) }}" method="POST" class="space-y-2 pt-2">
                            @csrf
                            <label class="text-[10px] font-black uppercase text-gray-400 block mb-1.5">Modifier votre offre (FCFA)</label>
                            <input type="number" name="montant" placeholder="Ex: 130000" min="100" value="{{ $convoi->montant }}"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none font-bold text-sm">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition-all shadow-md">
                                <i class="fas fa-sync-alt"></i> Mettre à jour l'offre
                            </button>
                        </form>
                    @endif

                    @if($convoi->statut === 'termine')
                        <div class="p-3 bg-emerald-50 border border-emerald-100 rounded-xl text-center">
                            <p class="text-xs font-bold text-emerald-800"><i class="fas fa-check-double me-1"></i> Prestation terminée et archivée.</p>
                        </div>
                    @endif

                    @if($convoi->statut === 'annule')
                        <div class="p-3 bg-red-50 border border-red-100 rounded-xl text-center">
                            <p class="text-xs font-semibold text-red-700 block mb-1">
                                <i class="fas fa-times-circle me-1"></i> Convoi annulé.
                            </p>
                            @if($convoi->motif_refus)
                                <p class="text-[10px] text-red-500 italic mt-1 font-semibold">Motif : {{ $convoi->motif_refus }}</p>
                            @endif
                        </div>
                    @endif

                    @if($convoi->statut === 'refuse')
                        <div class="p-3 bg-red-50 border border-red-100 rounded-xl text-center">
                            <p class="text-xs font-semibold text-red-700 block mb-1">
                                <i class="fas fa-ban me-1"></i> Demande refusée.
                            </p>
                            @if($convoi->motif_refus)
                                <p class="text-[10px] text-red-500 italic mt-1 font-semibold">Raison : {{ $convoi->motif_refus }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
    @if ($convoi->statut === 'en_attente' && $convoi->particulier_id === null)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const checkUrl = "{{ route('particulier.convoi.check-claim', $convoi->id) }}";
                
                const checkInterval = setInterval(async function() {
                    try {
                        const res = await fetch(checkUrl);
                        if (!res.ok) return;
                        const data = await res.json();
                        
                        if (data.claimed && !data.claimed_by_me) {
                            // Show warning banner
                            const warningEl = document.getElementById('alreadyClaimedWarning');
                            if (warningEl) {
                                warningEl.classList.remove('hidden');
                            }
                            
                            // Disable forms & inputs
                            document.querySelectorAll('form[action*="valider"], form[action*="refuser"]').forEach(form => {
                                form.querySelectorAll('input, textarea, button').forEach(el => {
                                    el.disabled = true;
                                    if (el.tagName === 'BUTTON') {
                                        el.classList.add('opacity-50', 'cursor-not-allowed');
                                    }
                                });
                            });
                            
                            // Stop polling
                            clearInterval(checkInterval);
                        }
                    } catch (err) {
                        console.error('Error checking claim status:', err);
                    }
                }, 5000); // Poll every 5 seconds
            });
        </script>
    @endif
@endsection
