@extends('admin.layouts.template')
@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
        <div class=" mx-auto" style="width: 90%">
            <!-- En-tête avec bouton -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Gestion des Compagnies</h1>
                    <p class="text-gray-600">Liste de toutes les compagnies enregistrées dans le système</p>
                </div>
                <a href="{{ route('compagnie.create') }}"
                    class="flex items-center px-6 py-3 bg-[#e94f1b] text-white font-semibold rounded-xl hover:bg-[#e89116] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl mt-4 sm:mt-0">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nouvelle Compagnie
                </a>
            </div>

            <!-- Carte principale -->
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
                <!-- En-tête de la table -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center">
                            <div class="w-2 h-8 bg-[#e94f1b] rounded-full mr-3"></div>
                            <h2 class="text-xl font-bold text-gray-800">Liste des Compagnies</h2>
                            <span class="ml-3 px-3 py-1 bg-[#e94f1b] text-white text-sm font-medium rounded-full"
                                id="compagnies-count">
                                {{ $compagnies->count() }} compagnie(s)
                            </span>
                        </div>

                        <!-- Barre de recherche -->
                        <form method="GET" action="{{ route('compagnie.index') }}" class="relative w-full sm:w-64">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-200"
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
                                    Compagnie
                                </th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Contact
                                </th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Localisation
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
                        <tbody class="bg-white divide-y divide-gray-200" id="compagnies-table">
                            @forelse($compagnies as $compagnie)
                                <tr class="hover:bg-gray-50 transition-colors duration-150"
                                    data-compagnie-id="{{ $compagnie->id }}">
                                    <!-- Colonne Compagnie -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center" style="display: flex; justify-content:center">
                                            <div class="flex-shrink-0 h-12 w-12">
                                                @if ($compagnie->path_logo)
                                                    <img class="h-12 w-12 rounded-xl object-cover border-2 border-gray-200"
                                                        src="{{ asset('storage/' . $compagnie->path_logo) }}"
                                                        alt="{{ $compagnie->name }}">
                                                @else
                                                    <div
                                                        class="h-12 w-12 rounded-xl bg-[#e94f1b] flex items-center justify-center text-white font-bold text-sm">
                                                        {{ substr($compagnie->name, 0, 2) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-semibold text-gray-900">{{ $compagnie->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $compagnie->email }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Colonne Contact -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-sm text-gray-900">{{ $compagnie->prefix . ' ' . $compagnie->contact }}
                                        </div>
                                    </td>

                                    <!-- Colonne Localisation -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-sm font-medium text-gray-900">{{ $compagnie->commune }}</div>
                                        <div class="text-sm text-gray-500 truncate max-w-xs">{{ $compagnie->adresse }}
                                        </div>
                                    </td>

                                    <!-- Colonne Statut -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if ($compagnie->statut === 'actif')
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                                Actif
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                                Désactivé
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Colonne Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end items-center space-x-2"
                                            style="display: flex; justify-content:center">
                                            <!-- Bouton Voir avec SweetAlert -->
                                            <button type="button"
                                                class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition-colors duration-200 show-compagnie-btn"
                                                data-compagnie="{{ json_encode($compagnie) }}" title="Voir détails">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>

                                            <!-- Bouton Modifier -->
                                            <a href="{{ route('compagnie.edit', $compagnie) }}"
                                                class="text-green-600 hover:text-green-900 p-2 rounded-lg hover:bg-green-50 transition-colors duration-200"
                                                title="Modifier">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            <!-- Bouton Supprimer avec SweetAlert -->
                                            <button type="button"
                                                class="text-red-600 hover:text-red-900 p-2 rounded-lg hover:bg-red-50 transition-colors duration-200 delete-compagnie-btn"
                                                data-compagnie-id="{{ $compagnie->id }}"
                                                data-compagnie-name="{{ $compagnie->name }}" title="Supprimer">
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
                                <!-- État vide -->
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune compagnie trouvée
                                            </h3>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($compagnies->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Affichage de {{ $compagnies->firstItem() }} à {{ $compagnies->lastItem() }} sur
                                {{ $compagnies->total() }} résultats
                            </div>
                            <div class="flex space-x-2">
                                {{ $compagnies->links() }}
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
            const showButtons = document.querySelectorAll('.show-compagnie-btn');
            showButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const compagnie = JSON.parse(this.getAttribute('data-compagnie'));

                    // Formater les informations de la compagnie
                    const logoHtml = compagnie.path_logo ?
                        `<img src="{{ asset('storage') }}/${compagnie.path_logo}" alt="${compagnie.name}" class="w-32 h-32 rounded-2xl object-cover mx-auto mb-4 border-2 border-gray-200">` :
                        `<div class="w-32 h-32 rounded-2xl bg-[#e94f1b] flex items-center justify-center text-white font-bold text-2xl mx-auto mb-4">${compagnie.name.substring(0, 2)}</div>`;

                    const statusBadge = compagnie.statut === 'actif' ?
                        '<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">🟢 Actif</span>' :
                        '<span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">🔴 Désactivé</span>';

                    Swal.fire({
                        title: compagnie.name,
                        html: `
                    <div class="text-left">
                        ${logoHtml}
                        <div class="grid grid-cols-1 gap-3 text-xl text-center">
                            ${compagnie.sigle ? `<div><strong class="text-gray-700">Sigle:</strong> <span class="text-[#e94f1b] font-semibold">${compagnie.sigle}</span></div>` : ''}
                            ${compagnie.slogan ? `<div><strong class="text-gray-700">Slogan:</strong> "${compagnie.slogan}"</div>` : ''}
                            <div><strong class="text-gray-700">Email:</strong> ${compagnie.email}</div>
                            <div><strong class="text-gray-700">Contact:</strong> ${compagnie.contact}</div>
                            <div><strong class="text-gray-700">Préfixe:</strong> ${compagnie.prefix}</div>
                            <div><strong class="text-gray-700">Commune:</strong> ${compagnie.commune}</div>
                            <div><strong class="text-gray-700">Adresse:</strong> ${compagnie.adresse}</div>
                            <div><strong class="text-gray-700">Statut:</strong> ${statusBadge}</div>
                            ${compagnie.username ? `<div><strong class="text-gray-700">Username:</strong> ${compagnie.username}</div>` : ''}
                            <div><strong class="text-gray-700">Créé le:</strong> ${new Date(compagnie.created_at).toLocaleDateString('fr-FR')}</div>
                        </div>
                    </div>
                `,
                        width: 600,
                        padding: '2rem',
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
            const deleteButtons = document.querySelectorAll('.delete-compagnie-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const compagnieId = this.getAttribute('data-compagnie-id');
                    const compagnieName = this.getAttribute('data-compagnie-name');

                    Swal.fire({
                        title: 'Êtes-vous sûr ?',
                        html: `
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>
                        <p class="text-gray-700 mb-2">Vous êtes sur le point de supprimer la compagnie :</p>
                        <p class="font-semibold text-lg text-gray-900">${compagnieName}</p>
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
                            form.action = `{{ url('admin/company') }}/${compagnieId}`;
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
            background-color: #e94f1b;
            border-color: #e94f1b;
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
    </style>
@endsection
