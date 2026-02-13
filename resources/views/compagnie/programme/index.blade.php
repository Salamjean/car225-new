@extends('compagnie.layouts.template')

@section('content')
<!-- Initialisation des données pour le JS -->
<script>
    // Les données des gares et véhicules ne sont plus nécessaires globalement ici pour la création de programme
</script>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- En-tête -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
            <div class="mb-6 lg:mb-0">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Lignes de Transport</h1>
                <p class="text-gray-600">Gérez vos routes et horaires Aller/Retour</p>
            </div>
            <!-- Bouton pour créer une toute nouvelle route -->
            <a href="{{ route('programme.create') }}"
                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white font-bold rounded-xl hover:from-orange-600 hover:to-red-600 transform hover:-translate-y-1 transition-all duration-200 shadow-lg">
                <i class="fas fa-plus mr-2"></i>
                Nouvelle Route
            </a>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-md p-5 border-l-4 border-orange-500">
                <p class="text-sm text-gray-600">Routes</p>
                <p class="text-2xl font-bold text-gray-900">{{ $groupedProgrammes->count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-5 border-l-4 border-green-500">
                <p class="text-sm text-gray-600">Horaires Aller</p>
                <p class="text-2xl font-bold text-gray-900">{{ $groupedProgrammes->sum(fn($g) => $g->aller->count()) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-5 border-l-4 border-blue-500">
                <p class="text-sm text-gray-600">Horaires Retour</p>
                <p class="text-2xl font-bold text-gray-900">{{ $groupedProgrammes->sum(fn($g) => $g->retour->count()) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-5 border-l-4 border-purple-500">
                <p class="text-sm text-gray-600">Total Programmes</p>
                <p class="text-2xl font-bold text-gray-900">{{ $programmes->count() }}</p>
            </div>
        </div>

        <!-- Messages Flash -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-r-xl">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-r-xl">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        <!-- Liste des lignes groupées -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-800 to-gray-900 text-white">
                <h2 class="text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-route"></i>
                    Routes configurées
                </h2>
            </div>

            @if($groupedProgrammes->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($groupedProgrammes as $route)
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <!-- Route Info -->
                                <div class="flex items-center gap-4 flex-1">
                                    <div class="w-14 h-14 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center text-white shadow-lg">
                                        <i class="fas fa-exchange-alt text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">
                                            {{ $route->itineraire->point_depart }} <i class="fas fa-arrow-right mx-1"></i> {{ $route->itineraire->point_arrive }}
                                            <span class="mx-2">|</span>
                                            <i class="fas fa-tag mr-1"></i>{{ number_format($route->montant_billet, 0, ',', ' ') }} FCFA
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            {{ $route->gare_depart?->nom_gare ?? $route->itineraire?->point_depart ?? 'N/A' }} → {{ $route->gare_arrivee?->nom_gare ?? $route->itineraire?->point_arrive ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Compteurs Aller/Retour -->
                                <div class="flex items-center gap-3">
                                    <div class="text-center px-4 py-2 bg-green-50 rounded-lg">
                                        <p class="text-xs text-green-600 uppercase font-semibold">Aller</p>
                                        <p class="text-xl font-bold text-green-700">{{ $route->aller->count() }}</p>
                                    </div>
                                    <div class="text-center px-4 py-2 bg-blue-50 rounded-lg">
                                        <p class="text-xs text-blue-600 uppercase font-semibold">Retour</p>
                                        <p class="text-xl font-bold text-blue-700">{{ $route->retour->count() }}</p>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-2">
                                    <!-- Bouton Voir (Consultation simple) -->
                                    <button onclick="showSchedulesPopup({{ json_encode($route) }})" 
                                       class="p-3 bg-purple-100 text-purple-600 rounded-lg hover:bg-purple-200 transition" title="Voir les horaires">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    <!-- BOUTON MODIFIÉ : Ouvre le popup de gestion au lieu d'aller sur create -->
                                    <button onclick="manageSchedules({{ json_encode($route) }})" 
                                       class="p-3 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition" title="Ajouter / Gérer horaires">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-route text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Aucune ligne créée</h3>
                    <p class="text-gray-500 mb-6">Commencez par créer votre première ligne de transport</p>
                    <a href="{{ route('programme.create') }}" 
                       class="inline-flex items-center px-6 py-3 bg-orange-500 text-white font-bold rounded-xl hover:bg-orange-600 transition">
                        <i class="fas fa-plus mr-2"></i>
                        Créer une ligne
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* Animation pour l'ajout de lignes */
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-slide-down {
        animation: slideDown 0.3s ease-out;
    }
</style>

<script>
// --- FONCTION 1 : CONSULTATION SIMPLE (Votre ancienne fonction améliorée) ---
function showSchedulesPopup(routeData) {
    const formatList = (list, colorClass, iconClass) => {
        if (!list || list.length === 0) return '<p class="text-gray-400 text-center py-2 text-sm">Aucun horaire</p>';
        return list.map((h, idx) => `
            <div class="flex items-center justify-between p-2 ${colorClass} rounded mb-1">
                <div class="flex items-center gap-2">
                    <span class="font-bold">${h.heure_depart}</span>
                    <i class="fas fa-arrow-right text-xs opacity-50"></i>
                    <span>${h.heure_arrive}</span>
                </div>
                <!-- Ici on peut ajouter suppression si besoin, mais on le garde pour le mode "Manage" -->
            </div>
        `).join('');
    };

    Swal.fire({
        title: `Horaires : ${routeData.gare_depart.nom_gare} - ${routeData.gare_arrivee.nom_gare}`,
        html: `
            <div class="grid grid-cols-2 gap-4 text-left">
                <div>
                    <h4 class="font-bold text-green-700 mb-2 border-b border-green-200 pb-1">ALLER</h4>
                    <div class="max-h-60 overflow-y-auto">${formatList(routeData.aller, 'bg-green-50 text-green-800')}</div>
                </div>
                <div>
                    <h4 class="font-bold text-blue-700 mb-2 border-b border-blue-200 pb-1">RETOUR</h4>
                    <div class="max-h-60 overflow-y-auto">${formatList(routeData.retour, 'bg-blue-50 text-blue-800')}</div>
                </div>
            </div>
        `,
        width: '600px',
        showConfirmButton: false,
        showCloseButton: true
    });
}

// --- FONCTION 2 : GESTION COMPLETE (Ajout + Visualisation de l'existant) ---
function manageSchedules(routeData) {
    // On n'a plus besoin des véhicules et chauffeurs ici pour la création de programme
    window.currentGareDepartId = routeData.gare_depart.id;
    window.currentGareArriveeId = routeData.gare_arrivee.id;

    // 2. Générer le HTML de la liste EXISTANTE (Lecture seule + boutons edit/delete)
    const generateExistingList = (list, type) => {
        if (!list || list.length === 0) return `<p class="text-xs text-gray-400 italic mb-2">Aucun horaire existant.</p>`;
        
        return list.map(h => `
            <div class="flex items-center justify-between bg-white border border-gray-200 p-2 rounded mb-1 shadow-sm">
                <div class="text-sm">
                    <span class="font-bold text-gray-700">${h.heure_depart}</span>
                    <i class="fas fa-long-arrow-alt-right text-gray-400 mx-1"></i>
                    <span class="text-gray-600">${h.heure_arrive}</span>
                </div>
                <div class="flex gap-2">
                    <a href="/company/programme/${h.id}/edit" target="_blank" class="text-blue-500 hover:text-blue-700" title="Modifier cet horaire">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button type="button" onclick="deleteSchedule(${h.id})" class="text-red-400 hover:text-red-600" title="Supprimer">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        `).join('');
    };

    // 3. Construction de la Modale HTML
    const content = `
        <div class="text-left">
            <div class="bg-gray-100 p-3 rounded-lg mb-4 flex justify-between items-center">
                <div>
                    <span class="font-bold text-gray-800">${routeData.gare_depart.nom_gare}</span>
                    <i class="fas fa-arrow-right text-orange-500 mx-2"></i>
                    <span class="font-bold text-gray-800">${routeData.gare_arrivee.nom_gare}</span>
                </div>
                <div class="text-sm text-gray-600">
                    <i class="fas fa-money-bill-wave mr-1"></i> ${Number(routeData.montant_billet).toLocaleString()} FCFA
                </div>
            </div>

            <form action="{{ route('programme.store') }}" method="POST" id="addScheduleForm">
                @csrf
                <input type="hidden" name="itineraire_id" value="${routeData.itineraire_id}">
                <input type="hidden" name="montant_billet" value="${routeData.montant_billet}">
                <input type="hidden" name="gare_depart_id" value="${routeData.gare_depart.id}">
                <input type="hidden" name="gare_arrivee_id" value="${routeData.gare_arrivee.id}">
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- COLONNE ALLER -->
                    <div class="bg-green-50/50 p-3 rounded-xl border border-green-100">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-bold text-green-800"><i class="fas fa-arrow-right mr-2"></i>ALLER</h4>
                            <button type="button" onclick="addNewRow('aller')" class="bg-green-600 text-white text-xs px-2 py-1 rounded hover:bg-green-700 transition shadow">
                                <i class="fas fa-plus"></i> Ajouter
                            </button>
                        </div>
                        
                        <!-- Liste Existante -->
                        <div class="mb-4 max-h-40 overflow-y-auto pr-1">
                            <p class="text-[10px] uppercase text-gray-500 font-bold mb-1">Existants</p>
                            ${generateExistingList(routeData.aller, 'aller')}
                        </div>

                        <!-- Zone d'ajout dynamique -->
                        <div id="new-aller-container" class="space-y-2">
                            <!-- Les nouvelles lignes s'ajouteront ici -->
                        </div>
                    </div>

                    <!-- COLONNE RETOUR -->
                    <div class="bg-blue-50/50 p-3 rounded-xl border border-blue-100">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-bold text-blue-800"><i class="fas fa-arrow-left mr-2"></i>RETOUR</h4>
                            <button type="button" onclick="addNewRow('retour')" class="bg-blue-600 text-white text-xs px-2 py-1 rounded hover:bg-blue-700 transition shadow">
                                <i class="fas fa-plus"></i> Ajouter
                            </button>
                        </div>

                        <!-- Liste Existante -->
                        <div class="mb-4 max-h-40 overflow-y-auto pr-1">
                            <p class="text-[10px] uppercase text-gray-500 font-bold mb-1">Existants</p>
                            ${generateExistingList(routeData.retour, 'retour')}
                        </div>

                        <!-- Zone d'ajout dynamique -->
                        <div id="new-retour-container" class="space-y-2">
                            <!-- Les nouvelles lignes s'ajouteront ici -->
                        </div>
                    </div>
                </div>
            </form>
        </div>
    `;

    Swal.fire({
        title: 'Gérer les Horaires',
        html: content,
        width: '950px',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-save mr-2"></i>Enregistrer les ajouts',
        cancelButtonText: 'Fermer',
        confirmButtonColor: '#ea580c', // Orange
        cancelButtonColor: '#6b7280',
        focusConfirm: false,
        preConfirm: () => {
            // Validation simple : vérifier s'il y a des champs ajoutés
            const allerRows = document.querySelectorAll('#new-aller-container .schedule-row');
            const retourRows = document.querySelectorAll('#new-retour-container .schedule-row');
            
            if (allerRows.length === 0 && retourRows.length === 0) {
                Swal.showValidationMessage('Veuillez ajouter au moins un nouvel horaire ou fermer la fenêtre.');
                return false;
            }
            // Soumettre le formulaire
            document.getElementById('addScheduleForm').submit();
        }
    });
}

// Fonction utilitaire pour ajouter une ligne de formulaire
window.addNewRow = function(type) {
    const container = document.getElementById(`new-${type}-container`);
    const index = Date.now(); // ID unique pour l'index du tableau
    const borderColor = type === 'aller' ? 'border-green-400' : 'border-blue-400';
    const bgColor = type === 'aller' ? 'bg-green-100' : 'bg-blue-100';

    const rowHtml = `
        <div class="schedule-row bg-white p-2 rounded shadow-md border-l-4 ${borderColor} relative animate-slide-down">
            <button type="button" onclick="this.parentElement.remove()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600 z-10">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="grid grid-cols-2 gap-2 mb-2">
                <div>
                    <label class="text-[10px] uppercase text-gray-500 font-bold">Départ</label>
                    <input type="time" name="${type}_horaires[${index}][heure_depart]" required
                        class="w-full text-sm border-gray-300 rounded focus:ring-1 focus:ring-orange-500 p-1">
                </div>
                <div>
                    <label class="text-[10px] uppercase text-gray-500 font-bold">Arrivée</label>
                    <input type="time" name="${type}_horaires[${index}][heure_arrive]" required
                        class="w-full text-sm border-gray-300 rounded focus:ring-1 focus:ring-orange-500 p-1">
                </div>
            </div>
            
            <div class="mt-1 text-center">
                <span class="text-[10px] ${bgColor} text-gray-600 px-2 rounded-full">Nouveau</span>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', rowHtml);
};

// Fonction de suppression (inchangée)
function deleteSchedule(programId) {
    Swal.fire({
        title: 'Supprimer cet horaire ?',
        text: 'Action irréversible',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/company/programme/${programId}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection