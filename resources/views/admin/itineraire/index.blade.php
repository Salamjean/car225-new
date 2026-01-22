@extends('admin.layouts.template')
@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
       <div class=" mx-auto" style="width: 90%">
            <!-- En-tête avec bouton -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Gestion des Itinéraires</h1>
                    <p class="text-gray-600">Liste de tous vos itinéraires enregistrés</p>
                </div>
            </div>

            <!-- Carte principale -->
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
                <!-- En-tête de la table -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center">
                            <div class="w-2 h-8 bg-[#e94e1a] rounded-full mr-3"></div>
                            <h2 class="text-xl font-bold text-gray-800">Liste des Itinéraires</h2>
                            <span class="ml-3 px-3 py-1 bg-[#e94e1a] text-white text-sm font-medium rounded-full"
                                id="itineraires-count">
                                {{ $itineraires->count() }} itinéraire(s)
                            </span>
                        </div>

                        <!-- Barre de recherche -->
                        <form method="GET" action="{{ route('admin.itineraire.index') }}" class="relative w-full sm:w-64">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Rechercher un itinéraire..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-200"
                                id="search-input">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </form>
                    </div>
                </div>

                <!-- Tableau -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Itinéraire
                                </th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Distance & Durée
                                </th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Date de création
                                </th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="itineraires-table">
                            @forelse($itineraires as $itineraire)
                                <tr class="hover:bg-gray-50 transition-colors duration-150"
                                    data-itineraire-id="{{ $itineraire->id }}">
                                    <!-- Colonne Itinéraire -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center" style="display: flex; justify-content:center">
                                            <div
                                                class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-[#e94e1a] to-orange-500 rounded-xl flex items-center justify-center text-white font-bold text-sm">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-semibold text-gray-900 flex items-center">
                                                    {{ $itineraire->point_depart }}
                                                    <svg class="w-4 h-4 mx-2 text-[#e94e1a]" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                                    </svg>
                                                    {{ $itineraire->point_arrive }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Colonne Distance & Durée -->
                                    <td class="px-6 py-4 whitespace-nowrap" style="display: flex; justify-content:center">
                                        <div class="flex flex-col space-y-1">
                                            <div class="flex items-center text-sm text-gray-900"
                                                style="display: flex; justify-content:center">
                                                <svg class="w-4 h-4 mr-1 text-green-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="font-medium">{{ $itineraire->durer_parcours }}</span>
                                            </div>
                                            <div class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full inline-block w-40"
                                                style="display: flex; justify-content:center">
                                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                </svg>
                                                Itinéraire calculé
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Colonne Date -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-sm text-gray-900">
                                            {{ $itineraire->created_at->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $itineraire->created_at->format('H:i') }}
                                        </div>
                                    </td>

                                    <!-- Colonne Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end items-center space-x-2"
                                            style="display: flex; justify-content:center">
                                            <!-- Bouton Voir avec SweetAlert -->
                                            <button type="button"
                                                class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition-colors duration-200 show-itineraire-btn"
                                                data-itineraire="{{ json_encode($itineraire) }}" title="Voir détails">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>

                                            <!-- Bouton Modifier -->
                                            {{-- <a href="{{ route('itineraire.edit', $itineraire) }}"
                                                class="text-green-600 hover:text-green-900 p-2 rounded-lg hover:bg-green-50 transition-colors duration-200"
                                                title="Modifier">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a> --}}

                                            <!-- Bouton Supprimer avec SweetAlert -->
                                            {{-- <button type="button"
                                                class="text-red-600 hover:text-red-900 p-2 rounded-lg hover:bg-red-50 transition-colors duration-200 delete-itineraire-btn"
                                                data-itineraire-id="{{ $itineraire->id }}"
                                                data-itineraire-name="{{ $itineraire->point_depart }} → {{ $itineraire->point_arrive }}"
                                                title="Supprimer">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button> --}}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <!-- État vide -->
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun itinéraire trouvé</h3>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($itineraires->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Affichage de {{ $itineraires->firstItem() }} à {{ $itineraires->lastItem() }} sur
                                {{ $itineraires->total() }} résultats
                            </div>
                            <div class="flex space-x-2">
                                {{ $itineraires->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Inclure SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Recherche en temps réel
            const searchInput = document.getElementById('search-input');
            if (searchInput) {
                let searchTimeout;

                searchInput.addEventListener('input', function(e) {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        this.closest('form').submit();
                    }, 500);
                });
            }

            // Gestion du bouton Voir avec SweetAlert
            const showButtons = document.querySelectorAll('.show-itineraire-btn');
            showButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const itineraire = JSON.parse(this.getAttribute('data-itineraire'));
                    const createdDate = new Date(itineraire.created_at).toLocaleDateString(
                    'fr-FR', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    const updatedDate = new Date(itineraire.updated_at).toLocaleDateString(
                    'fr-FR', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    Swal.fire({
                        title: `Itinéraire #${String(itineraire.id).padStart(4, '0')}`,
                        html: `
                    <div class="text-left space-y-4">
                        <div class="bg-gradient-to-r from-[#e94e1a] to-orange-500 p-4 rounded-xl text-white text-center">
                            <div class="text-lg font-bold">${itineraire.point_depart}</div>
                            <div class="my-2">
                                <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </div>
                            <div class="text-lg font-bold">${itineraire.point_arrive}</div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="text-center p-3 bg-blue-50 rounded-lg">
                                <div class="font-semibold text-blue-700">Durée</div>
                                <div class="text-lg font-bold text-gray-900 mt-1">${itineraire.durer_parcours}</div>
                            </div>
                            <div class="text-center p-3 bg-green-50 rounded-lg">
                                <div class="font-semibold text-green-700">ID</div>
                                <div class="text-lg font-bold text-gray-900 mt-1">#${String(itineraire.id).padStart(4, '0')}</div>
                            </div>
                        </div>
                        
                        <div class="border-t pt-3 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="font-semibold text-gray-700">Créé le:</span>
                                <span class="text-gray-900">${createdDate}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-semibold text-gray-700">Modifié le:</span>
                                <span class="text-gray-900">${updatedDate}</span>
                            </div>
                        </div>
                    </div>
                `,
                        width: 500,
                        padding: '1.5rem',
                        background: '#fff',
                        showCloseButton: true,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-3xl shadow-2xl'
                        }
                    });
                });
            });

            // Gestion du bouton Supprimer avec SweetAlert
            const deleteButtons = document.querySelectorAll('.delete-itineraire-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const itineraireId = this.getAttribute('data-itineraire-id');
                    const itineraireName = this.getAttribute('data-itineraire-name');

                    Swal.fire({
                        title: 'Êtes-vous sûr ?',
                        html: `
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>
                        <p class="text-gray-700 mb-2">Vous êtes sur le point de supprimer l'itinéraire :</p>
                        <p class="font-semibold text-lg text-gray-900">${itineraireName}</p>
                        <p class="text-red-600 text-sm mt-2">Cette action est irréversible !</p>
                    </div>
                `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Oui, supprimer !',
                        cancelButtonText: 'Annuler',
                        reverseButtons: true,
                        customClass: {
                            popup: 'rounded-3xl'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Créer un formulaire de suppression dynamique
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/company/Itinerary/${itineraireId}`;
                            form.style.display = 'none';

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
                });
            });

            // Animation au survol des lignes
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
                });

                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                });
            });
        });

        // SweetAlert notifications
        @if (Session::has('success'))
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: '{{ Session::get('success') }}',
                confirmButtonText: 'OK',
                background: 'white',
            });
        @endif

        @if (Session::has('error'))
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: '{{ Session::get('error') }}',
                confirmButtonText: 'OK',
                background: 'white',

            });
        @endif
    </script>

    <style>
        /* Styles personnalisés pour la pagination */
        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .page-item .page-link {
            padding: 0.5rem 0.75rem;
            margin: 0 0.125rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            color: #6b7280;
            text-decoration: none;
            transition: all 0.2s;
        }

        .page-item.active .page-link {
            background-color: #e94e1a;
            border-color: #e94e1a;
            color: white;
        }

        .page-item .page-link:hover {
            background-color: #f3f4f6;
            border-color: #d1d5db;
        }

        .page-item.active .page-link:hover {
            background-color: #e89116;
            border-color: #e89116;
        }

        /* Styles pour SweetAlert */
        .swal2-popup {
            border-radius: 1.5rem !important;
        }

        /* Animation des lignes du tableau */
        tbody tr {
            transition: all 0.3s ease;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }

            .px-6 {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .py-4 {
                padding-top: 1rem;
                padding-bottom: 1rem;
            }
        }
    </style>
@endsection
