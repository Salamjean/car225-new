@extends('compagnie.layouts.template')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
        <div class="mx-auto" style="width: 90%">
            <!-- En-tête -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
                <div class="mb-6 lg:mb-0">
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-3">Gestion du Personnel</h1>
                    <p class="text-lg text-gray-600">
                        Gérez votre équipe de chauffeurs et convoyeurs
                    </p>
                </div>

                <!-- Bouton d'ajout -->
                <a href="{{ route('personnel.create') }}"
                    class="inline-flex items-center px-6 py-4 bg-[#e94e1a] text-white font-bold rounded-xl hover:bg-[#d33d0f] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nouveau Personnel
                </a>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Personnel -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-[#e94e1a]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Personnel</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $personnels->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#e94e1a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Chauffeurs -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Chauffeurs</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">
                                {{ $personnels->where('type_personnel', 'Chauffeur')->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Convoyeurs -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Convoyeurs</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">
                                {{ $personnels->where('type_personnel', 'Convoyeur')->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Disponibles -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Disponibles</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">
                                {{ $personnels->where('statut', 'disponible')->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <h2 class="text-xl font-bold text-gray-900 mb-4 lg:mb-0">Liste du Personnel</h2>

                        <!-- Filtres et recherche -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <!-- Barre de recherche -->
                            <div class="relative">
                                <input type="text" id="searchInput" placeholder="Rechercher un personnel..."
                                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Filtre par type -->
                            <select id="typeFilter"
                                class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300">
                                <option value="">Tous les types</option>
                                <option value="Chauffeur">Chauffeurs</option>
                                <option value="Convoyeur">Convoyeurs</option>
                            </select>

                            <!-- Filtre par statut -->
                            <select id="statutFilter"
                                class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300">
                                <option value="">Tous les statuts</option>
                                <option value="disponible">Disponibles</option>
                                <option value="indisponible">Indisponibles</option>
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
                                    Personnel</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Type</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Contact</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Contact Urgence</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Statut</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="personnelTable">
                            @forelse($personnels as $personnel)
                                <tr class="hover:bg-gray-50 transition-colors duration-200 personnel-row"
                                    data-type="{{ $personnel->type_personnel }}" data-statut="{{ $personnel->statut }}"
                                    data-search="{{ strtolower($personnel->name . ' ' . $personnel->prenom . ' ' . $personnel->email) }}">
                                    <!-- Photo et informations -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center"
                                        style="display: flex; justify-content:center">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12">
                                                @if ($personnel->profile_image)
                                                    <img class="h-12 w-12 rounded-full object-cover border-2 border-gray-200"
                                                        src="{{ asset('storage/' . $personnel->profile_image) }}"
                                                        alt="{{ $personnel->prenom }} {{ $personnel->name }}"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div
                                                        class="h-12 w-12 rounded-full bg-[#e94e1a] flex items-center justify-center text-white font-bold text-sm hidden">
                                                        {{ substr($personnel->prenom, 0, 1) }}{{ substr($personnel->name, 0, 1) }}
                                                    </div>
                                                @else
                                                    <div
                                                        class="h-12 w-12 rounded-full bg-[#e94e1a] flex items-center justify-center text-white font-bold text-sm">
                                                        {{ substr($personnel->prenom, 0, 1) }}{{ substr($personnel->name, 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ $personnel->prenom }} {{ $personnel->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $personnel->email }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Type de personnel -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center text-center"
                                            style="display: flex; justify-content:center">
                                            @if ($personnel->type_personnel == 'Chauffeur')
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    Chauffeur
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                                    </svg>
                                                    Convoyeur
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Contact -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-sm text-gray-900">{{ $personnel->country_code }}
                                            {{ $personnel->contact }}</div>
                                    </td>

                                    <!-- Contact d'urgence -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-sm text-gray-900">{{ $personnel->country_code_urgence }}
                                            {{ $personnel->contact_urgence }}</div>
                                    </td>

                                    <!-- Statut -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if ($personnel->statut == 'disponible')
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                                Disponible
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                                Indisponible
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center"
                                        style="display: flex; justify-content:center">
                                        <div class="flex items-center space-x-3">
                                            <!-- Bouton Voir (SweetAlert) -->
                                            <button type="button" onclick="showPersonnelDetails({{ $personnel->id }})"
                                                class="text-blue-600 hover:text-blue-900 transition-colors duration-200 p-2 rounded-lg hover:bg-blue-50"
                                                title="Voir les détails">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>

                                            <!-- Bouton Modifier -->
                                            <a href="{{ route('personnels.edit', $personnel->id) }}"
                                                class="text-green-600 hover:text-green-900 transition-colors duration-200 p-2 rounded-lg hover:bg-green-50"
                                                title="Modifier">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            <!-- Bouton Supprimer (SweetAlert) -->
                                            <button type="button"
                                                onclick="confirmDelete({{ $personnel->id }}, '{{ $personnel->prenom }} {{ $personnel->name }}')"
                                                class="text-red-600 hover:text-red-900 transition-colors duration-200 p-2 rounded-lg hover:bg-red-50"
                                                title="Supprimer">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-500">
                                            <svg class="w-16 h-16 mb-4 text-gray-300" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                            </svg>
                                            <p class="text-lg font-medium mb-2">Aucun personnel trouvé</p>
                                            <p class="text-sm mb-4">Commencez par ajouter un nouveau membre à votre équipe.
                                            </p>
                                            <a href="{{ route('personnels.create') }}"
                                                class="inline-flex items-center px-4 py-2 bg-[#e94e1a] text-white font-semibold rounded-lg hover:bg-[#d33d0f] transition-colors duration-200">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4" />
                                                </svg>
                                                Ajouter du personnel
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($personnels->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        {{ $personnels->links() }}
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
            const typeFilter = document.getElementById('typeFilter');
            const statutFilter = document.getElementById('statutFilter');
            const personnelRows = document.querySelectorAll('.personnel-row');

            function filterTable() {
                const searchValue = searchInput.value.toLowerCase();
                const typeValue = typeFilter.value;
                const statutValue = statutFilter.value;

                personnelRows.forEach(row => {
                    const searchData = row.getAttribute('data-search');
                    const typeData = row.getAttribute('data-type');
                    const statutData = row.getAttribute('data-statut');

                    const matchesSearch = searchData.includes(searchValue);
                    const matchesType = !typeValue || typeData === typeValue;
                    const matchesStatut = !statutValue || statutData === statutValue;

                    if (matchesSearch && matchesType && matchesStatut) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            searchInput.addEventListener('input', filterTable);
            typeFilter.addEventListener('change', filterTable);
            statutFilter.addEventListener('change', filterTable);
        });

        // Afficher les détails du personnel avec SweetAlert2
        function showPersonnelDetails(personnelId) {
            // Simuler les données (remplacez par un appel AJAX réel)
            const personnelData = {
                id: personnelId,
                name: document.querySelector(`[data-personnel-id="${personnelId}"]`).getAttribute('data-name'),
                prenom: document.querySelector(`[data-personnel-id="${personnelId}"]`).getAttribute('data-prenom'),
                email: document.querySelector(`[data-personnel-id="${personnelId}"]`).getAttribute('data-email'),
                type_personnel: document.querySelector(`[data-personnel-id="${personnelId}"]`).getAttribute(
                    'data-type'),
                contact: document.querySelector(`[data-personnel-id="${personnelId}"]`).getAttribute('data-contact'),
                contact_urgence: document.querySelector(`[data-personnel-id="${personnelId}"]`).getAttribute(
                    'data-contact-urgence'),
                statut: document.querySelector(`[data-personnel-id="${personnelId}"]`).getAttribute('data-statut'),
                profile_image: document.querySelector(`[data-personnel-id="${personnelId}"]`).getAttribute(
                    'data-profile-image')
            };

            // En production, utilisez un appel AJAX pour récupérer les données
            fetch(`/compagnie/personnels/${personnelId}`)
                .then(response => response.json())
                .then(personnel => {
                    showPersonnelModal(personnel);
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    // Fallback avec les données de l'attribut data
                    showPersonnelModal(personnelData);
                });
        }

        function showPersonnelModal(personnel) {
            const statutBadge = personnel.statut === 'disponible' ?
                '<span style="display:flex; justify-content:center" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 text-center"><span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>Disponible</span>' :
                '<span style="display:flex; justify-content:center" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 text-center"><span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>Indisponible</span>';

            const typeBadge = personnel.type_personnel === 'Chauffeur' ?
                '<span style="display:flex; justify-content:center" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 text-center">Chauffeur</span>' :
                '<span  style="display:flex; justify-content:center" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 text-center">Convoyeur</span>';

            // Gestion de l'image de profil - VERSION CORRIGÉE
            let profileImageHtml;
            Swal.fire({
                html: `
            <div class="text-left space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded-lg">${personnel.email}</p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Type</label>
                        <div class="text-sm">${typeBadge}</div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Contact Personnel</label>
                        <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded-lg">${personnel.contact}</p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Contact Urgence</label>
                        <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded-lg">${personnel.contact_urgence}</p>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Statut</label>
                    <div class="text-sm">${statutBadge}</div>
                </div>
            </div>
        `,
                width: 600,
                padding: '2rem',
                background: '#ffffff',
                showCloseButton: true,
                showConfirmButton: false,
                customClass: {
                    container: 'personnel-details-modal',
                    popup: 'rounded-2xl shadow-2xl',
                    closeButton: 'text-gray-400 hover:text-gray-600 transition-colors duration-200'
                }
            });
        }

        // Suppression avec SweetAlert2
        function confirmDelete(personnelId, personnelName) {
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                html: `
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <p class="text-lg font-medium text-gray-900 mb-2">Supprimer le personnel</p>
                <p class="text-gray-600">"<strong>${personnelName}</strong>" sera définitivement supprimé.</p>
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
                    form.action = `/compagnie/personnels/${personnelId}`;

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

        // Gestion des messages flash avec SweetAlert2
        @if (session('success'))
            Swal.fire({
                title: 'Succès !',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#e94e1a',
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
        .personnel-row {
            transition: all 0.3s ease;
        }

        .personnel-row:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Animation pour les badges */
        .badge {
            transition: all 0.3s ease;
        }

        .badge:hover {
            transform: scale(1.05);
        }

        /* Style pour la modal SweetAlert2 personnalisée */
        .personnel-details-modal .swal2-popup {
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

            .personnel-details-modal .swal2-popup {
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

    <!-- Ajouter les données personnel aux lignes du tableau pour l'AJAX -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ajouter les attributs data pour chaque ligne de personnel
            document.querySelectorAll('.personnel-row').forEach(row => {
                const personnelId = row.querySelector('button[onclick*="showPersonnelDetails"]')?.onclick
                    .toString().match(/\d+/)?.[0];
                if (personnelId) {
                    row.setAttribute('data-personnel-id', personnelId);
                    const name = row.querySelector('.text-sm.font-semibold').textContent.split(' ')[1];
                    const prenom = row.querySelector('.text-sm.font-semibold').textContent.split(' ')[0];
                    const email = row.querySelector('.text-gray-500').textContent;
                    const type = row.getAttribute('data-type');
                    const statut = row.getAttribute('data-statut');
                    const contact = row.children[2].textContent.trim();
                    const contactUrgence = row.children[3].textContent.trim();

                    row.setAttribute('data-name', name);
                    row.setAttribute('data-prenom', prenom);
                    row.setAttribute('data-email', email);
                    row.setAttribute('data-type', type);
                    row.setAttribute('data-statut', statut);
                    row.setAttribute('data-contact', contact);
                    row.setAttribute('data-contact-urgence', contactUrgence);
                }
            });
        });
    </script>
@endsection
