@extends('compagnie.layouts.template')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Archives et Historique</h1>
                <p class="text-gray-600">
                    Consultez les programmes terminés et le journal des modifications.
                </p>
            </div>
            <a href="{{ route('programme.index') }}" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Retour aux programmes actifs
            </a>
        </div>

        <!-- SECTION 1 : Programmes Terminés / Expirés -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-10 border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
                    <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    </div>
                    Archives des Programmes
                </h2>
                <span class="bg-gray-200 text-gray-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                    {{ $programmesExpires->total() }} Terminés
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-4 font-medium">Trajet</th>
                            <th class="px-6 py-4 font-medium">Type</th>
                            <th class="px-6 py-4 font-medium">Dates & Horaires</th>
                            <th class="px-6 py-4 font-medium">Véhicule utilisé</th>
                            <th class="px-6 py-4 font-medium text-center">État</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($programmesExpires as $prog)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $prog->point_depart }}</div>
                                    <div class="text-gray-400 text-xs">vers</div>
                                    <div class="font-bold text-gray-900">{{ $prog->point_arrive }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($prog->type_programmation == 'ponctuel')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Ponctuel
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            Récurrent
                                        </span>
                                    @endif
                                    @if($prog->is_aller_retour)
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                Aller-Retour
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    @if($prog->type_programmation == 'ponctuel')
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            {{ \Carbon\Carbon::parse($prog->date_depart)->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1 pl-5">
                                            {{ $prog->heure_depart }} - {{ $prog->heure_arrive }}
                                        </div>
                                    @else
                                        <div class="text-xs">Du {{ \Carbon\Carbon::parse($prog->date_depart)->format('d/m/Y') }}</div>
                                        <div class="text-xs font-semibold">Au {{ \Carbon\Carbon::parse($prog->date_fin_programmation)->format('d/m/Y') }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($prog->vehicule)
                                        <div class="text-gray-900 font-medium">{{ $prog->vehicule->marque }} {{ $prog->vehicule->modele }}</div>
                                        <div class="text-xs text-gray-500">{{ $prog->vehicule->immatriculation }}</div>
                                    @else
                                        <span class="text-gray-400 italic">Véhicule supprimé</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-2"></span>
                                        Terminé
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        <p>Aucun programme archivé pour le moment.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination spécifique pour les programmes -->
            @if($programmesExpires->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $programmesExpires->appends(['logs_page' => request('logs_page')])->links() }}
                </div>
            @endif
        </div>

        <!-- SECTION 2 : Journal d'activité (Logs) -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-blue-100">
            <div class="px-6 py-5 border-b border-gray-200 bg-blue-50 flex justify-between items-center">
                <h2 class="text-xl font-bold text-blue-900 flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    Journal d'activité
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-blue-800 uppercase bg-blue-50/50 border-b border-blue-100">
                        <tr>
                            <th class="px-6 py-4 font-medium">Date de l'action</th>
                            <th class="px-6 py-4 font-medium">Programme concerné</th>
                            <th class="px-6 py-4 font-medium">Action</th>
                            <th class="px-6 py-4 font-medium">Détails</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                    {{ $log->created_at->format('d/m/Y') }}
                                    <span class="text-xs text-gray-400 ml-1">{{ $log->created_at->format('H:i') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $log->itineraire }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $log->date_depart }} ({{ $log->heure_depart }})
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($log->action == 'change_chauffeur')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            Changement Chauffeur
                                        </span>
                                    @elseif($log->action == 'change_vehicule')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                            Changement Véhicule
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                            {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-700 text-sm mb-1">{{ $log->raison }}</div>
                                    <div class="text-xs text-gray-500 bg-gray-50 p-2 rounded border border-gray-100">
                                        <span class="font-semibold">État après modif :</span><br>
                                        Véhicule: {{ Str::limit($log->vehicule, 30) }}<br>
                                        Chauffeur: {{ Str::limit($log->chauffeur, 30) }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    Aucune activité enregistrée récemment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination spécifique pour les logs -->
            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-blue-100 bg-blue-50/30">
                    {{ $logs->appends(['prog_page' => request('prog_page')])->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

    <!-- Inclure SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Filtrage et recherche en temps réel
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const statutFilter = document.getElementById('statutFilter');
            const dateFilter = document.getElementById('dateFilter');
            const programmeRows = document.querySelectorAll('.programme-row');

            function filterTable() {
                const searchValue = searchInput.value.toLowerCase();
                const statutValue = statutFilter.value;
                const dateValue = dateFilter.value;
                const today = new Date().toISOString().split('T')[0];

                programmeRows.forEach(row => {
                    const searchData = row.getAttribute('data-search');
                    const statutData = row.getAttribute('data-statut');
                    const programmeDate = row.getAttribute('data-date');

                    const matchesSearch = searchData.includes(searchValue);
                    const matchesStatut = !statutValue || statutData === statutValue;

                    let matchesDate = true;
                    if (dateValue === 'today') {
                        matchesDate = programmeDate === today;
                    } else if (dateValue === 'upcoming') {
                        matchesDate = programmeDate >= today;
                    } else if (dateValue === 'past') {
                        matchesDate = programmeDate < today;
                    }

                    if (matchesSearch && matchesStatut && matchesDate) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            searchInput.addEventListener('input', filterTable);
            statutFilter.addEventListener('change', filterTable);
            dateFilter.addEventListener('change', filterTable);
        });

        // Afficher les détails du programme avec SweetAlert2
        function showProgrammeDetails(programmeId) {
            // Afficher un loader pendant le chargement
            Swal.fire({
                title: 'Chargement...',
                text: 'Récupération des informations du programme',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Appel AJAX direct
            fetch(`/company/programme/${programmeId}/api`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors de la récupération des données');
                    }
                    return response.json();
                })
                .then(programme => {
                    Swal.close();
                    showProgrammeModal(programme);
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    Swal.fire({
                        title: 'Erreur',
                        text: 'Impossible de charger les détails du programme',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
        }

        function showProgrammeModal(programme) {
            // Badge de statut
            const statutBadge = programme.staut_place === 'vide' ?
                '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"><span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>Disponible</span>' :
                programme.staut_place === 'presque_complet' ?
                '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"><span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>Presque complet</span>' :
                '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"><span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>Complet</span>';

            // Barre de progression des places
            const pourcentageOccupation = Math.round((programme.nbre_siege_occupe / programme.vehicule.nombre_place) * 100);
            const placesDisponibles = programme.vehicule.nombre_place - programme.nbre_siege_occupe;

            Swal.fire({
                title: `<div class="flex items-center space-x-3">
                  <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                  <div>
                    <h2 class="text-xl font-bold text-gray-900">${programme.point_depart} → ${programme.point_arrive}</h2>
                    <div class="flex space-x-2 mt-1">
                      ${statutBadge}
                    </div>
                  </div>
                </div>`,
                html: `
            <div class="text-left space-y-6">
                <!-- Informations du trajet -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Date de départ</label>
                        <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded-lg">${programme.date_depart_formatee}</p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Durée</label>
                        <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded-lg">${programme.durer_parcours}</p>
                    </div>
                </div>

                <!-- Horaires -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Heure de départ</label>
                        <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded-lg">${programme.heure_depart}</p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Heure d'arrivée</label>
                        <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded-lg">${programme.heure_arrive}</p>
                    </div>
                </div>

                <!-- Véhicule -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Véhicule</label>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-sm font-medium text-gray-900">${programme.vehicule.marque} ${programme.vehicule.modele}</p>
                        <p class="text-sm text-gray-600">Immatriculation: ${programme.vehicule.immatriculation}</p>
                        <p class="text-sm text-gray-600">${programme.vehicule.nombre_place} places totales</p>
                    </div>
                </div>

                <!-- Équipage -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Équipage</label>
                    <div class="bg-gray-50 p-3 rounded-lg space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-900">Chauffeur:</span>
                            <span class="text-sm text-gray-600">${programme.chauffeur.prenom} ${programme.chauffeur.name}</span>
                        </div>
                        ${programme.convoyeur ? `
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-900">Convoyeur:</span>
                                        <span class="text-sm text-gray-600">${programme.convoyeur.prenom} ${programme.convoyeur.name}</span>
                                    </div>
                                    ` : ''}
                    </div>
                </div>

                <!-- Places -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Occupation des places</label>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-900">${programme.nbre_siege_occupe}/${programme.vehicule.nombre_place} places occupées</span>
                            <span class="text-sm font-medium ${programme.staut_place === 'rempli' ? 'text-red-600' : programme.staut_place === 'presque_complet' ? 'text-yellow-600' : 'text-green-600'}">
                                ${pourcentageOccupation}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full ${programme.staut_place === 'rempli' ? 'bg-red-500' : programme.staut_place === 'presque_complet' ? 'bg-yellow-500' : 'bg-green-500'}" 
                                 style="width: ${pourcentageOccupation}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">${placesDisponibles} places disponibles</p>
                    </div>
                </div>
            </div>
        `,
                width: 600,
                padding: '2rem',
                background: '#ffffff',
                showCloseButton: true,
                showConfirmButton: false,
                customClass: {
                    container: 'programme-details-modal',
                    popup: 'rounded-2xl shadow-2xl',
                    closeButton: 'text-gray-400 hover:text-gray-600 transition-colors duration-200'
                }
            });
        }

        // Suppression avec SweetAlert2
        function confirmDelete(programmeId, programmeTrajet) {
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                html: `
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <p class="text-lg font-medium text-gray-900 mb-2">Supprimer le programme</p>
                <p class="text-gray-600">"<strong>${programmeTrajet}</strong>" sera définitivement supprimé.</p>
                <p class="text-sm text-red-600 mt-2">Cette action est irréversible !</p>
            </div>
        `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'px-6 py-3 rounded-lg font-medium',
                    cancelButton: 'px-6 py-3 rounded-lg font-medium'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Soumettre le formulaire de suppression
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/company/programme/${programmeId}`;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Fonction pour changer le chauffeur
        function changerChauffeur(programmeId, chauffeurActuel) {
            // Afficher un loader pendant le chargement des chauffeurs
            Swal.fire({
                title: 'Chargement...',
                text: 'Récupération de la liste des chauffeurs',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Appel AJAX pour récupérer les chauffeurs
            fetch(`/company/programme/${programmeId}/chauffeurs-disponibles`)
                .then(response => response.json())
                .then(chauffeurs => {
                    Swal.close();

                    let optionsHTML = '';
                    chauffeurs.forEach(chauffeur => {
                        const selected = chauffeur.nom_complet === chauffeurActuel ? 'selected' : '';
                        optionsHTML +=
                            `<option value="${chauffeur.id}" ${selected}>${chauffeur.nom_complet} - ${chauffeur.contact}</option>`;
                    });

                    Swal.fire({
                        title: 'Changer le chauffeur',
                        html: `
                    <div class="text-left space-y-4">
                        <p class="text-sm text-gray-600">Chauffeur actuel: <strong>${chauffeurActuel}</strong></p>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Nouveau chauffeur</label>
                            <select id="nouveauChauffeur" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent">
                                ${optionsHTML}
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Raison du changement (optionnel)</label>
                            <textarea id="raisonChauffeur" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent" placeholder="Ex: Chauffeur malade, changement de planning..."></textarea>
                        </div>
                    </div>
                `,
                        showCancelButton: true,
                        confirmButtonText: 'Modifier',
                        cancelButtonText: 'Annuler',
                        confirmButtonColor: '#e94f1b',
                        preConfirm: () => {
                            const chauffeurId = document.getElementById('nouveauChauffeur').value;
                            const raison = document.getElementById('raisonChauffeur').value;

                            if (!chauffeurId) {
                                Swal.showValidationMessage('Veuillez sélectionner un chauffeur');
                                return false;
                            }

                            return {
                                chauffeurId,
                                raison
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Envoyer la modification
                            changerChauffeurSubmit(programmeId, result.value.chauffeurId, result.value.raison);
                        }
                    });
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    Swal.fire('Erreur', 'Impossible de charger la liste des chauffeurs', 'error');
                });
        }

        // Fonction pour changer le véhicule
        function changerVehicule(programmeId, vehiculeActuel) {
            // Afficher un loader pendant le chargement des véhicules
            Swal.fire({
                title: 'Chargement...',
                text: 'Récupération de la liste des véhicules',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Appel AJAX pour récupérer les véhicules
            fetch(`/company/programme/${programmeId}/vehicules-disponibles`)
                .then(response => response.json())
                .then(vehicules => {
                    Swal.close();

                    let optionsHTML = '';
                    vehicules.forEach(vehicule => {
                        const selected = vehicule.nom_complet === vehiculeActuel ? 'selected' : '';
                        optionsHTML +=
                            `<option value="${vehicule.id}" ${selected}>${vehicule.nom_complet} (${vehicule.places} places)</option>`;
                    });

                    Swal.fire({
                        title: 'Changer le véhicule',
                        html: `
                    <div class="text-left space-y-4">
                        <p class="text-sm text-gray-600">Véhicule actuel: <strong>${vehiculeActuel}</strong></p>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Nouveau véhicule</label>
                            <select id="nouveauVehicule" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent">
                                ${optionsHTML}
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Raison du changement (optionnel)</label>
                            <textarea id="raisonVehicule" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent" placeholder="Ex: Véhicule en panne, maintenance..."></textarea>
                        </div>
                    </div>
                `,
                        showCancelButton: true,
                        confirmButtonText: 'Modifier',
                        cancelButtonText: 'Annuler',
                        confirmButtonColor: '#e94f1b',
                        preConfirm: () => {
                            const vehiculeId = document.getElementById('nouveauVehicule').value;
                            const raison = document.getElementById('raisonVehicule').value;

                            if (!vehiculeId) {
                                Swal.showValidationMessage('Veuillez sélectionner un véhicule');
                                return false;
                            }

                            return {
                                vehiculeId,
                                raison
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Envoyer la modification
                            changerVehiculeSubmit(programmeId, result.value.vehiculeId, result.value.raison);
                        }
                    });
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    Swal.fire('Erreur', 'Impossible de charger la liste des véhicules', 'error');
                });
        }

        // Fonction pour soumettre le changement de chauffeur
        function changerChauffeurSubmit(programmeId, chauffeurId, raison) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('chauffeur_id', chauffeurId);
            formData.append('raison', raison);
            formData.append('_method', 'PATCH');

            fetch(`/company/programme/${programmeId}/changer-chauffeur`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Succès!', data.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Erreur!', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    Swal.fire('Erreur', 'Une erreur est survenue lors de la modification', 'error');
                });
        }

        // Fonction pour soumettre le changement de véhicule
        function changerVehiculeSubmit(programmeId, vehiculeId, raison) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('vehicule_id', vehiculeId);
            formData.append('raison', raison);
            formData.append('_method', 'PATCH');

            fetch(`/company/programme/${programmeId}/changer-vehicule`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Succès!', data.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Erreur!', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    Swal.fire('Erreur', 'Une erreur est survenue lors de la modification', 'error');
                });
        }

        // Gestion des messages flash avec SweetAlert2
        @if (session('success'))
            Swal.fire({
                title: 'Succès !',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#e94f1b',
                confirmButtonText: 'OK',
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if (session('error'))
            Swal.fire({
                title: 'Erreur !',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        @endif
    </script>
<script>
    @if (session('success'))
        Swal.fire({
            title: 'Succès !',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonColor: '#e94f1b',
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true
        });
    @endif

    @if (session('error'))
        Swal.fire({
            title: 'Action impossible',
            text: "{{ session('error') }}", // Le message du contrôleur s'affichera ici
            icon: 'warning', // Icône Warning est plus appropriée pour une restriction métier
            confirmButtonColor: '#d33',
            confirmButtonText: 'Compris'
        });
    @endif
</script>
    <style>
        .programme-row {
            transition: all 0.3s ease;
        }

        .programme-row:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Style pour la modal SweetAlert2 personnalisée */
        .programme-details-modal .swal2-popup {
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }

            .table-responsive th,
            .table-responsive td {
                padding: 0.5rem;
            }

            .programme-details-modal .swal2-popup {
                width: 95% !important;
                margin: 1rem;
            }
        }

        /* Animation pour les boutons d'action */
        .action-btn {
            transition: all 0.2s ease;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }
    </style>
@endsection
