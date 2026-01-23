@extends('compagnie.layouts.template')
@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
        <div class="mx-auto" style="width: 90%">
            <!-- En-tête -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
                <div class="mb-6 lg:mb-0">
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-3">Gestion des Programmes</h1>
                    <p class="text-lg text-gray-600">
                        Gérez vos programmes de transport
                    </p>
                </div>

                <!-- Bouton d'ajout -->
                <a href="{{ route('programme.create') }}"
                    class="inline-flex items-center px-6 py-4 bg-[#e94f1b] text-white font-bold rounded-xl hover:bg-[#e89116] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nouveau Programme
                </a>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Total Programmes -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-[#e94f1b]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Programmes</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $programmes->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#e94f1b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Programmes à venir -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">À venir</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">
                                {{ $programmes->where('date_depart', '>=', now()->format('Y-m-d'))->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Programmes complets -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Complets</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">
                                {{ $programmes->where('staut_place', 'rempli')->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte principale -->
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
                <!-- En-tête de la table -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 lg:mb-0">Liste des Programmes</h2>

                        <!-- Filtres et recherche -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <!-- Barre de recherche -->
                            <div class="relative">
                                <input type="text" id="searchInput" placeholder="Rechercher un programme..."
                                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Filtre par statut -->
                            <select id="statutFilter"
                                class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300">
                                <option value="">Tous les statuts</option>
                                <option value="vide">Places disponibles</option>
                                <option value="presque_complet">Presque complet</option>
                                <option value="rempli">Complet</option>
                            </select>

                            <!-- Filtre par date -->
                            <select id="dateFilter"
                                class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300">
                                <option value="">Toutes les dates</option>
                                <option value="today">Aujourd'hui</option>
                                <option value="upcoming">À venir</option>
                                <option value="past">Passés</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tableau -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Trajet</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Date & Heure</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Véhicule</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Équipage</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Places</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="programmeTable">
                            @forelse($programmes as $programme)
                                <tr class="hover:bg-gray-50 transition-colors duration-200 programme-row"
                                    data-search="{{ strtolower($programme->point_depart . ' ' . $programme->point_arrive . ' ' . $programme->vehicule) }}"
                                    data-statut="{{ $programme->staut_place }}" data-date="{{ $programme->date_depart }}">
                                    <!-- Trajet -->
                                    <td class="px-6 py-4 whitespace-nowrap" style="display: flex; justify-content:center">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </div>
                                            <div class="ml-4" >
                                                <div class="text-sm font-semibold text-gray-900 text-center" style="display: flex; justify-content:center">
                                                    {{ $programme->point_depart }} → {{ $programme->point_arrive }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $programme->durer_parcours }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Date & Heure -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $programme->date_depart }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $programme->heure_depart }} - {{ $programme->heure_arrivee }}
                                        </div>
                                    </td>

                                    <!-- Véhicule -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <div class="flex items-center gap-2">
                                                <span>{{ $programme->vehicule }}</span>
                                                </button>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Équipage -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium">{{ $programme->chauffeur }}</span>
                                            </div>
                                            @if ($programme->convoyeur)
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-gray-500 text-xs">+
                                                        {{ $programme->convoyeur }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Places -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-900">
                                                {{ $programme->sieges_occupes }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Statut -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($programme->statut_places == 'vide')
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                                Disponible
                                            </span>
                                        @elseif($programme->statut_places == 'presque_complet')
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                                                Presque complet
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                                Complet
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-500">
                                            <svg class="w-16 h-16 mb-4 text-gray-300" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-lg font-medium mb-2">Aucun programme trouvé</p>
                                            <p class="text-sm mb-4">Commencez par créer un nouveau programme de transport.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($programmes->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        {{ $programmes->links() }}
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
