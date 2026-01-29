@extends('compagnie.layouts.template')
@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
        <div class="mx-auto" style="width: 10fr">
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
                    class="inline-flex items-center px-6 py-4 bg-[#e94f1b] text-white font-bold rounded-xl hover:bg-[#e94f1b] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nouveau Programme
                </a>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
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
                                {{ $programmes->where('date_depart', '>=', now()->format('Y-m-d'))->count() }}
                            </p>
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
                                {{ $programmes->where('staut_place', 'rempli')->count() }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Places disponibles -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Places libres</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">
                                {{ $programmes->sum(function ($programme) {
        return $programme->vehicule->nombre_place - $programme->nbre_siege_occupe;
    }) }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
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

                            <!-- Filtre par type -->
                            <select id="typeFilter"
                                class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300">
                                <option value="">Tous les types</option>
                                <option value="ponctuel">Ponctuel</option>
                                <option value="recurrent">Récurrent</option>
                            </select>

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

                    <!-- Tableau -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Trajet
                                    </th>
                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Date & Heure
                                    </th>
                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Véhicule
                                    </th>
                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Équipage
                                    </th>
                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Places
                                    </th>
                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Montant
                                    </th>
                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Statut
                                    </th>
                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="programmeTable">
                                @forelse($programmes as $programme)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 programme-row"
                                        data-search="{{ strtolower($programme->point_depart . ' ' . $programme->point_arrive . ' ' . $programme->vehicule->marque) }}"
                                        data-statut="{{ $programme->staut_place }}" data-date="{{ $programme->date_depart }}">
                                        <!-- Trajet -->
                                        <td class="px-6 py-4 whitespace-nowrap" style="display: flex; justify-content:center">
                                            <div class="flex items-center">
                                                <div class="ml-4">
                                                    <div class="text-sm font-semibold text-gray-900">
                                                        {{ $programme->point_depart }} → {{ $programme->point_arrive }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 text-center">
                                                        {{ $programme->durer_parcours }}
                                                        @if ($programme->is_aller_retour)
                                                            <span
                                                                class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                                <i class="fas fa-exchange-alt mr-1"></i> Aller-Retour
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Date & Heure -->
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="text-sm font-medium text-gray-900 text-center">
                                                {{ \Carbon\Carbon::parse($programme->date_depart)->format('d/m/Y') }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $programme->heure_depart }} - {{ $programme->heure_arrive }}
                                            </div>
                                        </td>

                                        <!-- Véhicule -->
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="text-sm font-medium text-gray-900"
                                                style="display: flex; justify-content:center">
                                                <div class="flex items-center gap-2">
                                                    <span>{{ $programme->vehicule->marque }}
                                                        {{ $programme->vehicule->modele }}</span>
                                                    <button type="button"
                                                        onclick="changerVehicule({{ $programme->id }}, '{{ $programme->vehicule->marque }} {{ $programme->vehicule->modele }}')"
                                                        class="text-blue-600 hover:text-blue-800 transition-colors duration-200"
                                                        title="Changer le véhicule">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $programme->vehicule->immatriculation }}
                                            </div>
                                        </td>

                                        <!-- Équipage -->
                                        <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex flex-col">
            <span class="text-sm font-medium text-gray-900">
                {{ $programme->point_depart }} → {{ $programme->point_arrive }}
            </span>
            <!-- BADGE ALLER-RETOUR -->
            @if($programme->is_aller_retour)
                <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800 w-fit">
                    <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    Aller-Retour
                </span>
            @endif
        </div>
        <div class="text-sm text-gray-500">{{ $programme->durer_parcours }}</div>
    </td>
                                        <!-- Places -->
                                       <td class="px-6 py-4 whitespace-nowrap" style="display: flex; justify-content:center">
    <div class="flex items-center">
        <!-- Barre de progression -->
        <div class="w-24 bg-gray-200 rounded-full h-2 mr-3">
            @php
                $placesTotales = $programme->vehicule->nombre_place;
                // On utilise la variable calculée dans le contrôleur ou 0 par défaut
                $placesOccupees = $programme->total_reserves ?? 0;
                $pourcentage = $placesTotales > 0 ? ($placesOccupees / $placesTotales) * 100 : 0;
                
                // Couleur dynamique
                $colorClass = 'bg-green-500';
                if($pourcentage >= 100) $colorClass = 'bg-red-500';
                elseif($pourcentage >= 80) $colorClass = 'bg-yellow-500';
            @endphp
            
            <div class="{{ $colorClass }} h-2 rounded-full"
                style="width: {{ min($pourcentage, 100) }}%">
            </div>
        </div>
        
        <!-- Texte (0/20) -->
        <span class="text-sm font-medium text-gray-900">
            {{ $placesOccupees }}/{{ $placesTotales }}
        </span>
    </div>
</td>

                                        <!-- Montant -->
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="text-sm font-semibold text-[#e94f1b]">
                                                {{ number_format($programme->montant_billet, 0, ',', ' ') }} FCFA
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Par passager
                                            </div>
                                        </td>

                                        <!-- Type de programmation -->
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if ($programme->type_programmation == 'ponctuel')
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    Ponctuel
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                    Récurrent
                                                </span>
                                                @if ($programme->date_fin_programmation)
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        Jusqu'au
                                                        {{ \Carbon\Carbon::parse($programme->date_fin_programmation)->format('d/m/Y') }}
                                                    </div>
                                                @endif
                                            @endif
                                        </td>

                                        <!-- Statut -->
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if ($programme->staut_place == 'vide')
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                                    Disponible
                                                </span>
                                            @elseif($programme->staut_place == 'presque_complet')
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

                                        <!-- Actions -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                            style="display: flex; justify-content:center">
                                            <div class="flex items-center space-x-3">
                                                <!-- Bouton Voir (SweetAlert) -->
                                                <button type="button" onclick="showProgrammeDetails({{ $programme->id }})"
                                                    class="text-blue-600 hover:text-blue-900 transition-colors duration-200 p-2 rounded-lg hover:bg-blue-50"
                                                    title="Voir les détails">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>

                                                <!-- Bouton Modifier -->
                                                <a href="{{ route('programme.edit', $programme->id) }}"
                                                    class="text-green-600 hover:text-green-900 transition-colors duration-200 p-2 rounded-lg hover:bg-green-50"
                                                    title="Modifier">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>

                                                <!-- Bouton Supprimer (SweetAlert) -->
                                                <button type="button"
                                                    onclick="confirmDelete({{ $programme->id }}, '{{ $programme->point_depart }} → {{ $programme->point_arrive }}')"
                                                    class="text-red-600 hover:text-red-900 transition-colors duration-200 p-2 rounded-lg hover:bg-red-50"
                                                    title="Supprimer">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center">
                                            <div class="flex flex-col items-center justify-center text-gray-500">
                                                <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <p class="text-lg font-medium mb-2">Aucun programme trouvé</p>
                                                <p class="text-sm mb-4">Commencez par créer un nouveau programme de
                                                    transport.
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
            document.addEventListener('DOMContentLoaded', function () {
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

                // Badge de type
                const typeBadge = programme.type_programmation === 'ponctuel' ?
                    '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>Ponctuel</span>' :
                    '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Récurrent</span>';

                // Badge Aller-Retour
                const allerRetourBadge = programme.is_aller_retour ?
                    '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"><i class="fas fa-exchange-alt mr-1"></i> Aller-Retour</span>' : '';

                // Informations sur la récurrence
                const recurrenceInfo = programme.type_programmation === 'recurrent' && programme.date_fin_programmation ?
                    `<div class="text-sm text-gray-600 mt-2">
                    <strong>Période :</strong> Du ${programme.date_depart_formatee} au ${programme.date_fin_programmation}
                </div>` : '';

                // Barre de progression des places
                const pourcentageOccupation = Math.round((programme.nbre_siege_occupe / programme.vehicule.nombre_place) * 100);
                const placesDisponibles = programme.vehicule.nombre_place - programme.nbre_siege_occupe;

                Swal.fire({
                    title: `<div class="flex items-center space-x-3">

                    <div>
                        <h2 class="text-xl font-bold text-gray-900 text-center"">${programme.point_depart} → ${programme.point_arrive}</h2>
                        <div class="flex space-x-2 mt-1 justify-center">
                            ${statutBadge}
                            ${typeBadge}
                            ${allerRetourBadge}
                        </div>
                    </div>
                </div>`,
                    html: `
                    <div class="text-left space-y-6">
                        <!-- Montant du billet -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Montant du billet</label>
                            <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-100">
                                <p class="text-2xl font-bold text-[#e94f1b]">
                                    ${new Intl.NumberFormat('fr-FR').format(programme.montant_billet)} FCFA
                                </p>
                                <p class="text-sm text-gray-600 mt-1">Prix par passager</p>
                            </div>
                        </div>

                        ${recurrenceInfo}

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


                        <!-- Informations Retour (Si Aller-Retour) -->
                        ${programme.retour_details ? `
                            <div class="space-y-2 border-t pt-4 mt-4 border-gray-200">
                                <label class="block text-sm font-bold text-indigo-700 flex items-center">
                                    <i class="fas fa-exchange-alt mr-2"></i> Détails du Retour
                                </label>
                                <div class="bg-indigo-50 p-3 rounded-lg border border-indigo-100">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase font-semibold">Départ Retour</p>
                                            <p class="text-sm font-medium text-gray-900">${programme.retour_details.heure_depart}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase font-semibold">Arrivée Retour</p>
                                            <p class="text-sm font-medium text-gray-900">${programme.retour_details.heure_arrive}</p>
                                        </div>
                                    </div>
                                    
                                    ${programme.retour_details.type_programmation === 'recurrent' ? `
                                        <div class="mt-3 pt-2 border-t border-indigo-100">
                                            <p class="text-xs text-gray-500 uppercase font-semibold">Récurrence Retour</p>
                                            <p class="text-sm text-indigo-800">
                                                ${programme.retour_details.jours_recurrence && programme.retour_details.jours_recurrence.length > 0 
                                                    ? 'Jours : ' + programme.retour_details.jours_recurrence.join(', ') 
                                                    : 'Récurrent'}
                                            </p>
                                        </div>
                                    ` : `
                                        <div class="mt-3 pt-2 border-t border-indigo-100">
                                            <p class="text-xs text-gray-500 uppercase font-semibold">Date Retour</p>
                                            <p class="text-sm text-indigo-800">${programme.retour_details.date_depart}</p>
                                        </div>
                                    `}
                                </div>
                            </div>
                        ` : ''}

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
                                    <span class="text-sm text-gray-600">${programme.chauffeur ? (programme.chauffeur.prenom + ' ' + programme.chauffeur.name) : 'Non défini'}</span>
                                </div>
                                ${programme.convoyeur ? `
                                                    <div class="flex justify-between items-center">
                                                        <span class="text-sm font-medium text-gray-900">Convoyeur:</span>
                                                        <span class="text-sm text-gray-600">${programme.convoyeur ? (programme.convoyeur.prenom + ' ' + programme.convoyeur.name) : 'Non défini'}</span>
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
                    width: 650,
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

            // Filtrage et recherche en temps réel
            document.addEventListener('DOMContentLoaded', function () {
                const searchInput = document.getElementById('searchInput');
                const typeFilter = document.getElementById('typeFilter');
                const statutFilter = document.getElementById('statutFilter');
                const dateFilter = document.getElementById('dateFilter');
                const programmeRows = document.querySelectorAll('.programme-row');

                function filterTable() {
                    const searchValue = searchInput.value.toLowerCase();
                    const typeValue = typeFilter.value;
                    const statutValue = statutFilter.value;
                    const dateValue = dateFilter.value;
                    const today = new Date().toISOString().split('T')[0];

                    programmeRows.forEach(row => {
                        const searchData = row.getAttribute('data-search');
                        const typeData = row.getAttribute('data-type');
                        const statutData = row.getAttribute('data-statut');
                        const programmeDate = row.getAttribute('data-date');

                        const matchesSearch = searchData.includes(searchValue);
                        const matchesType = !typeValue || typeData === typeValue;
                        const matchesStatut = !statutValue || statutData === statutValue;

                        let matchesDate = true;
                        if (dateValue === 'today') {
                            matchesDate = programmeDate === today;
                        } else if (dateValue === 'upcoming') {
                            matchesDate = programmeDate >= today;
                        } else if (dateValue === 'past') {
                            matchesDate = programmeDate < today;
                        }

                        if (matchesSearch && matchesType && matchesStatut && matchesDate) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                }

                searchInput.addEventListener('input', filterTable);
                typeFilter.addEventListener('change', filterTable);
                statutFilter.addEventListener('change', filterTable);
                dateFilter.addEventListener('change', filterTable);
            });

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