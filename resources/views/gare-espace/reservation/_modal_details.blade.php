<!-- Modal Détails -->
<div id="modalDetails" class="fixed inset-0 z-[100] flex items-center justify-center hidden pt-10 pb-10">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="relative bg-white w-full max-w-2xl mx-4 rounded-[2rem] shadow-2xl overflow-hidden transform transition-all scale-95 opacity-0 duration-300" id="modalContent">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-400 px-8 py-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-white shadow-inner">
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
        <div class="p-8 space-y-8 bg-gray-50/50 max-h-[85vh] overflow-y-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Info Passager -->
                <div class="space-y-4">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                        <span class="w-4 h-[2px] rounded bg-orange-300"></span> Informations Voyageur
                    </h3>
                    
                    <div class="flex items-center gap-4 mb-6 bg-white p-4 rounded-3xl border border-gray-100 shadow-sm relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-orange-500/5 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                        <div class="relative">
                            <img id="passagerPhoto" src="" alt="Photo" class="w-20 h-20 rounded-2xl object-cover border-2 border-white shadow-md hidden">
                            <div id="passagerInitial" class="w-20 h-20 rounded-2xl bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white text-2xl font-black shadow-lg">
                                ?
                            </div>
                        </div>
                        <div class="flex-1">
                            <span class="text-[9px] font-black text-orange-500 uppercase tracking-wider block mb-0.5">Identité du voyageur</span>
                            <p class="font-black text-gray-900 text-lg leading-tight" id="passagerNom"></p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 bg-green-50 text-green-600 text-[10px] font-bold rounded-md border border-green-100 uppercase">Vérifié</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex flex-col bg-white p-4 rounded-2xl border border-gray-100 shadow-sm transition-all hover:border-orange-200">
                            <span class="text-[10px] font-black text-orange-500 uppercase mb-1 flex items-center gap-2">
                                <i class="fas fa-phone-alt text-[9px]"></i> Téléphone
                            </span>
                            <p class="font-bold text-gray-900" id="passagerTel"></p>
                        </div>
                        <div class="flex flex-col bg-white p-4 rounded-2xl border border-gray-100 shadow-sm transition-all hover:border-orange-200">
                            <span class="text-[10px] font-black text-orange-500 uppercase mb-1 flex items-center gap-2">
                                <i class="fas fa-ambulance text-[9px]"></i> Contact d'Urgence
                            </span>
                            <p class="font-bold text-gray-900 text-sm" id="passagerUrgence"></p>
                        </div>
                    </div>
                </div>

                <!-- Info Trajet -->
                <div class="space-y-4">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                        <span class="w-4 h-[2px] rounded bg-orange-300"></span> Détails du Trajet
                    </h3>
                    <div class="bg-[#1e293b] rounded-3xl p-6 text-white space-y-6 shadow-xl">
                        <div class="flex items-center justify-between gap-4">
                            <div class="text-center flex-1">
                                <p class="text-[9px] font-black text-orange-400 uppercase mb-1">Départ</p>
                                <p class="text-[13px] font-black uppercase" id="trajetDepart"></p>
                            </div>
                            <div class="flex flex-col items-center">
                                <i class="fas fa-bus text-orange-500"></i>
                                <div class="w-8 h-[1px] bg-slate-600 my-1"></div>
                            </div>
                            <div class="text-center flex-1">
                                <p class="text-[9px] font-black text-orange-400 uppercase mb-1">Arrivée</p>
                                <p class="text-[13px] font-black uppercase" id="trajetArrivee"></p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 border-t border-slate-700 pt-6">
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Date & Heure</p>
                                <p class="text-xs font-bold" id="trajetDate"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Siège</p>
                                <p class="text-xl font-black text-orange-500" id="trajetSiege"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Voyage/Mission Info -->
            <div id="voyageInfo" class="hidden">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2 mb-4">
                    <span class="w-4 h-[2px] rounded bg-orange-300"></span> Véhicule & Chauffeur
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center gap-4 bg-orange-50 p-4 rounded-2xl border border-orange-100">
                        <div class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center shadow-lg shadow-orange-500/30">
                            <i class="fas fa-bus"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-orange-500 uppercase">Véhicule</p>
                            <p class="text-sm font-bold text-gray-900" id="voyageVehicule"></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 bg-orange-50 p-4 rounded-2xl border border-orange-100">
                        <div class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center shadow-lg shadow-orange-500/30">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-orange-500 uppercase">Chauffeur</p>
                            <p class="text-sm font-bold text-gray-900" id="voyageChauffeur"></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-between p-5 bg-white rounded-2xl border border-dashed border-gray-300 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-orange-500 border border-gray-100">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase leading-none mb-1">Montant Payé</p>
                        <p class="text-base font-black text-gray-900" id="paiementMontant"></p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black text-gray-400 uppercase leading-none mb-1">Méthode</p>
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg uppercase tracking-wide inline-block mt-1" id="paiementMethode"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showDetails(id) {
        const modal = document.getElementById('modalDetails');
        const content = document.getElementById('modalContent');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);

        // Fetch data
        fetch(`/gare-espace/reservations/${id}`)
            .then(response => response.json())
            .then(res => {
                console.log("=== DEBUG: MODAL DETAILS DATA ===");
                console.log("API Response details for ID " + id + ":", res);
                if(res.success) {
                    const data = res.data;
                    console.log("Trajet details:", data.trajet);
                    
                    document.getElementById('modalRef').textContent = `Référence: ${data.reference}`;
                    document.getElementById('passagerNom').textContent = data.passager.nom;
                    document.getElementById('passagerTel').textContent = data.passager.telephone;
                    document.getElementById('passagerUrgence').textContent = data.passager.urgence;

                    // Gérer la photo du passager
                    const photoImg = document.getElementById('passagerPhoto');
                    const initialBox = document.getElementById('passagerInitial');
                    if (data.passager.photo) {
                        photoImg.src = data.passager.photo;
                        photoImg.classList.remove('hidden');
                        initialBox.classList.add('hidden');
                    } else {
                        photoImg.classList.add('hidden');
                        initialBox.classList.remove('hidden');
                        initialBox.textContent = data.passager.nom.charAt(0).toUpperCase();
                    }
                    
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

     // Listen for Escape key to close modal
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            closeModal();
        }
    });
</script>
