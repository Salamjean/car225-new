@extends('gare-espace.layouts.template')

@section('title', 'Gestion des Réservations')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-gray-900 flex items-center gap-3 uppercase">
                <i class="fas fa-ticket-alt text-orange-500"></i>
                Gestion des <span class="text-orange-500">Réservations</span>
            </h1>
            <p class="text-gray-500 text-sm font-medium">Liste complète et filtrage des réservations liées à votre gare</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <div class="px-4 py-2 bg-orange-50 text-orange-600 rounded-xl border border-orange-100 flex flex-col items-center">
                <span class="text-[10px] font-black uppercase tracking-widest leading-none">Total</span>
                <span class="text-lg font-black leading-none mt-1">{{ $reservations->total() }}</span>
            </div>
            <div class="px-4 py-2 bg-green-50 text-green-600 rounded-xl border border-green-100 flex flex-col items-center">
                <span class="text-[10px] font-black uppercase tracking-widest leading-none">Aujourd'hui</span>
                <span class="text-lg font-black leading-none mt-1">{{ \App\Models\Reservation::where('gare_depart_id', auth('gare')->id())->whereDate('date_voyage', now())->count() }}</span>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('gare-espace.reservations.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <!-- Référence -->
            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Référence / Commande</label>
                <div class="relative group">
                    <i class="fas fa-barcode absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-orange-500 transition-colors"></i>
                    <input type="text" name="reference" value="{{ request('reference') }}" placeholder="Ex: RES-2026..." 
                        class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 focus:bg-white outline-none transition-all text-sm font-bold">
                </div>
            </div>

            <!-- Passager -->
            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nom du Passager</label>
                <div class="relative group">
                    <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-orange-500 transition-colors"></i>
                    <input type="text" name="passager" value="{{ request('passager') }}" placeholder="Nom ou Prénom" 
                        class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 focus:bg-white outline-none transition-all text-sm font-bold">
                </div>
            </div>

            <!-- Date -->
            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Date de Voyage</label>
                <div class="relative group">
                    <i class="fas fa-calendar absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-orange-500 transition-colors"></i>
                    <input type="date" name="date_voyage" value="{{ request('date_voyage') }}"
                        class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 focus:bg-white outline-none transition-all text-sm font-bold uppercase">
                </div>
            </div>

            <!-- Statut -->
            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Statut</label>
                <div class="relative group">
                    <i class="fas fa-info-circle absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-orange-500 transition-colors"></i>
                    <select name="statut" class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 focus:bg-white outline-none transition-all text-sm font-bold">
                        <option value="">Tous les statuts</option>
                        <option value="confirmee" {{ request('statut') == 'confirmee' ? 'selected' : '' }}>Confirmée</option>
                        <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="terminee" {{ request('statut') == 'terminee' ? 'selected' : '' }}>Terminée</option>
                        <option value="annulee" {{ request('statut') == 'annulee' ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-3 bg-orange-500 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-orange-600 transition-all shadow-lg shadow-orange-500/20 focus:ring-4 focus:ring-orange-500/30 active:scale-95">
                    <i class="fas fa-search mr-2"></i> Filtrer
                </button>
                @if(request()->anyFilled(['reference', 'passager', 'date_voyage', 'statut']))
                <a href="{{ route('gare-espace.reservations.index') }}" class="w-12 py-3 bg-gray-100 text-gray-500 rounded-xl flex items-center justify-center hover:bg-gray-200 transition-all active:scale-95" title="Réinitialiser">
                    <i class="fas fa-redo-alt text-xs"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 font-black text-gray-400 text-[10px] uppercase tracking-widest">
                        <th class="px-6 py-5 pl-8">Référence</th>
                        <th class="px-6 py-5">Passager</th>
                        <th class="px-6 py-5">Itinéraire</th>
                        <th class="px-6 py-5">Voyage</th>
                        <th class="px-6 py-5 text-right">Montant</th>
                        <th class="px-6 py-5 text-center">Statut</th>
                        <th class="px-6 py-5 text-center pr-8">Détails</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($reservations as $reservation)
                    <tr class="group hover:bg-orange-50/10 transition-colors duration-300">
                        <td class="px-6 py-5 pl-8">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-300 group-hover:bg-orange-500 group-hover:text-white group-hover:border-orange-500 transition-all">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                                <div>
                                    <span class="block text-xs font-black text-gray-900 leading-none mb-1 group-hover:text-orange-600 transition-colors">{{ $reservation->reference }}</span>
                                    <span class="text-[10px] text-gray-400 font-mono tracking-tighter">{{ $reservation->payment_transaction_id }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-5">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-900">{{ $reservation->passager_nom }} {{ $reservation->passager_prenom }}</span>
                                <span class="text-[10px] text-gray-400 font-medium flex items-center gap-1.5">
                                    <i class="fas fa-phone-alt text-[8px] text-orange-400"></i> {{ $reservation->passager_telephone }}
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-5">
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-bold text-gray-800">{{ $reservation->programme->point_depart }}</span>
                                    <i class="fas fa-long-arrow-alt-right text-[10px] text-orange-400"></i>
                                    <span class="text-xs font-bold text-gray-800">{{ $reservation->programme->point_arrive }}</span>
                                </div>
                                <span class="text-[10px] font-black text-orange-500 uppercase tracking-widest">Siège #{{ $reservation->seat_number }}</span>
                            </div>
                        </td>

                        <td class="px-6 py-5">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-900">{{ $reservation->date_voyage->format('d/m/Y') }}</span>
                                <span class="text-[10px] text-gray-400 font-medium flex items-center gap-1.5">
                                    <i class="far fa-clock text-[8px] text-orange-400"></i> {{ $reservation->heure_depart }}
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-5 text-right">
                            <span class="text-sm font-black text-gray-900">{{ number_format($reservation->montant, 0, ',', ' ') }}</span>
                            <span class="text-[9px] font-black text-gray-400 uppercase ml-1">FCFA</span>
                        </td>

                        <td class="px-6 py-5 text-center">
                            @if($reservation->statut == 'confirmee')
                                <span class="px-3 py-1 bg-green-50 text-green-600 text-[10px] font-black rounded-lg border border-green-100 uppercase tracking-widest">Confirmé</span>
                            @elseif($reservation->statut == 'en_attente')
                                <span class="px-3 py-1 bg-yellow-50 text-yellow-600 text-[10px] font-black rounded-lg border border-yellow-100 uppercase tracking-widest">En attente</span>
                            @elseif($reservation->statut == 'terminee')
                                <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-black rounded-lg border border-blue-100 uppercase tracking-widest">Terminé</span>
                            @else
                                <span class="px-3 py-1 bg-red-50 text-red-600 text-[10px] font-black rounded-lg border border-red-100 uppercase tracking-widest">{{ $reservation->statut }}</span>
                            @endif
                        </td>

                        <td class="px-6 py-5 text-center pr-8">
                            <button onclick="showDetails({{ $reservation->id }})" class="w-8 h-8 rounded-lg bg-orange-50 text-orange-500 flex items-center justify-center hover:bg-orange-500 hover:text-white transition-all transform hover:scale-110 shadow-sm border border-orange-100">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-24 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mb-6 border border-gray-100">
                                    <i class="fas fa-search-minus text-3xl"></i>
                                </div>
                                <h3 class="text-xl font-black text-gray-900 mb-2 uppercase tracking-tighter">Aucune réservation trouvée</h3>
                                <p class="text-gray-400 font-medium max-w-sm mx-auto text-sm">Nous n'avons trouvé aucune réservation correspondant à vos critères de recherche.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reservations->hasPages())
        <div class="px-8 py-6 bg-gray-50 border-t border-gray-100 flex items-center justify-between gap-4">
            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                Affichage de {{ $reservations->firstItem() }} à {{ $reservations->lastItem() }} sur {{ $reservations->total() }} réservations
            </div>
            <div class="flex items-center gap-2">
                {{ $reservations->onEachSide(1)->links('pagination::tailwind') }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Détails -->
<div id="modalDetails" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="relative bg-white w-full max-w-2xl mx-4 rounded-3xl shadow-2xl overflow-hidden transform transition-all scale-95 opacity-0 duration-300" id="modalContent">
        <!-- Modal Header -->
        <div class="bg-orange-500 px-8 py-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-white">
                    <i class="fas fa-user-tag text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-white uppercase tracking-tight leading-none mb-1">Détails Passager</h2>
                    <p class="text-orange-100 text-xs font-medium" id="modalRef"></p>
                </div>
            </div>
            <button onclick="closeModal()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Info Passager -->
                <div class="space-y-4">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                        <span class="w-4 h-[1px] bg-orange-200"></span> Informations Voyageur
                    </h3>
                    <div class="space-y-3">
                        <div class="flex flex-col bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            <span class="text-[10px] font-black text-orange-500 uppercase mb-1">Nom complet</span>
                            <p class="font-bold text-gray-900" id="passagerNom"></p>
                        </div>
                        <div class="flex flex-col bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            <span class="text-[10px] font-black text-orange-500 uppercase mb-1">Téléphone</span>
                            <p class="font-bold text-gray-900" id="passagerTel"></p>
                        </div>
                        <div class="flex flex-col bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            <span class="text-[10px] font-black text-orange-500 uppercase mb-1">Personne à contacter en cas d'urgence</span>
                            <p class="font-bold text-gray-900" id="passagerUrgence"></p>
                        </div>
                    </div>
                </div>

                <!-- Info Trajet -->
                <div class="space-y-4">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                        <span class="w-4 h-[1px] bg-orange-200"></span> Détails du Trajet
                    </h3>
                    <div class="bg-gray-900 rounded-3xl p-6 text-white space-y-6 shadow-xl shadow-gray-200">
                        <div class="flex items-center justify-between gap-4">
                            <div class="text-center flex-1">
                                <p class="text-[9px] font-black text-orange-400 uppercase mb-1">Départ</p>
                                <p class="text-sm font-black uppercase" id="trajetDepart"></p>
                            </div>
                            <div class="flex flex-col items-center">
                                <i class="fas fa-bus text-orange-500"></i>
                                <div class="w-8 h-[1px] bg-gray-700 my-1"></div>
                            </div>
                            <div class="text-center flex-1">
                                <p class="text-[9px] font-black text-orange-400 uppercase mb-1">Arrivée</p>
                                <p class="text-sm font-black uppercase" id="trajetArrivee"></p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 border-t border-gray-800 pt-6">
                            <div>
                                <p class="text-[9px] font-black text-gray-500 uppercase mb-1">Date & Heure</p>
                                <p class="text-xs font-bold" id="trajetDate"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] font-black text-gray-500 uppercase mb-1">Siège attribué</p>
                                <p class="text-lg font-black text-orange-500" id="trajetSiege"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Voyage/Mission Info -->
            <div id="voyageInfo" class="hidden animate-fade-in">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2 mb-4">
                    <span class="w-4 h-[1px] bg-orange-200"></span> Véhicule & Chauffeur
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center gap-4 bg-orange-50/50 p-4 rounded-2xl border border-orange-100">
                        <div class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center">
                            <i class="fas fa-bus"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-orange-500 uppercase">Véhicule</p>
                            <p class="text-sm font-bold text-gray-900" id="voyageVehicule"></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 bg-orange-50/50 p-4 rounded-2xl border border-orange-100">
                        <div class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-orange-500 uppercase">Chauffeur</p>
                            <p class="text-sm font-bold text-gray-900" id="voyageChauffeur"></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-orange-500">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase leading-none mb-1">Montant Payé</p>
                        <p class="text-sm font-black text-gray-900" id="paiementMontant"></p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-[9px] font-black text-gray-400 uppercase leading-none mb-1">Méthode</p>
                    <p class="text-xs font-bold text-gray-900 uppercase" id="paiementMethode"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showDetails(id) {
        const modal = document.getElementById('modalDetails');
        const content = document.getElementById('modalContent');
        
        // Show loading or just open modal
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);

        // Fetch data
        fetch(`/gare-espace/reservations/${id}`)
            .then(response => response.json())
            .then(res => {
                if(res.success) {
                    const data = res.data;
                    document.getElementById('modalRef').textContent = `Référence: ${data.reference}`;
                    document.getElementById('passagerNom').textContent = data.passager.nom;
                    document.getElementById('passagerTel').textContent = data.passager.telephone;
                    document.getElementById('passagerUrgence').textContent = data.passager.urgence;
                    
                    document.getElementById('trajetDepart').textContent = data.trajet.depart;
                    document.getElementById('trajetArrivee').textContent = data.trajet.arrivee;
                    document.getElementById('trajetDate').textContent = `${data.trajet.date} à ${data.trajet.heure}`;
                    document.getElementById('trajetSiege').textContent = data.trajet.siege;

                    document.getElementById('paiementMontant').textContent = data.paiement.montant;
                    document.getElementById('paiementMethode').textContent = data.paiement.methode;

                    const vInfo = document.getElementById('voyageInfo');
                    if (data.voyage) {
                        vInfo.classList.remove('hidden');
                        document.getElementById('voyageVehicule').textContent = data.voyage.vehicule;
                        document.getElementById('voyageChauffeur').textContent = data.voyage.chauffeur;
                    } else {
                        vInfo.classList.add('hidden');
                    }
                }
            });
    }

    function closeModal() {
        const modal = document.getElementById('modalDetails');
        const content = document.getElementById('modalContent');
        
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>

<style>
    /* Custom Orange Accents */
    input:focus, select:focus {
        border-color: #f97316 !important;
        box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1) !important;
    }
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
