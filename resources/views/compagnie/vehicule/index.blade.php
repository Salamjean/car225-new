@extends('compagnie.layouts.template')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#e94f1b] rounded-2xl shadow-lg mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Gestion des Véhicules</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Gérez votre flotte de véhicules
            </p>
        </div>

        <!-- Carte principale -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <!-- En-tête de la carte -->
            <div class="px-8 py-6 bg-gradient-to-r from-[#e94f1b] to-[#e94f1b]">
                <div class="flex flex-col sm:flex-row justify-between items-center">
                    <h2 class="text-2xl font-bold text-white mb-4 sm:mb-0">Liste des Véhicules</h2>
                    <a href="{{ route('vehicule.create') }}" 
                       class="flex items-center px-6 py-3 bg-white text-[#e94f1b] font-bold rounded-xl hover:bg-gray-50 transform hover:-translate-y-1 transition-all duration-200 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nouveau Véhicule
                    </a>
                </div>
            </div>

            <!-- Contenu -->
            <div class="p-8">
                <!-- Messages de statut -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Tableau des véhicules -->
                <div class="overflow-hidden rounded-2xl border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Immatriculation</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Numéro de Série</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Places</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($vehicules as $vehicule)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-mono font-bold text-gray-900 text-center">{{ $vehicule->immatriculation }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm text-gray-600">{{ $vehicule->numero_serie ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $vehicule->type_range }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm text-gray-900">{{ $vehicule->nombre_place }} places</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($vehicule->is_active)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Actif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                            Inactif
                                        </span>
                                        @if($vehicule->motif)
                                            <div class="text-xs text-gray-500 mt-1">{{ $vehicule->motif }}</div>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center" style="display: flex; justify-content:center">
                                    <div class="flex items-center space-x-2">
                                        <!-- Bouton Voir (SweetAlert) -->
                                        <button type="button" 
                                                class="inline-flex items-center px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 view-details"
                                                title="Voir les détails"
                                                data-vehicule-id="{{ $vehicule->id }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>

                                        <!-- Bouton Modifier -->
                                        <a href="{{ route('vehicule.edit', $vehicule->id) }}" 
                                           class="inline-flex items-center px-3 py-2 bg-[#e94f1b] text-white rounded-lg hover:bg-[#e89116] transition-colors duration-200"
                                           title="Modifier">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        <!-- Bouton Activer/Désactiver -->
                                        @if($vehicule->is_active)
                                            <button type="button" 
                                                    class="inline-flex items-center px-3 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors duration-200 deactivate-vehicule"
                                                    title="Désactiver le véhicule"
                                                    data-vehicule-id="{{ $vehicule->id }}"
                                                    data-vehicule-immatriculation="{{ $vehicule->immatriculation }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button" 
                                                    class="inline-flex items-center px-3 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200 activate-vehicule"
                                                    title="Activer le véhicule"
                                                    data-vehicule-id="{{ $vehicule->id }}"
                                                    data-vehicule-immatriculation="{{ $vehicule->immatriculation }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                                </svg>
                                            </button>
                                        @endif

                                        <!-- Bouton Supprimer -->
                                        <button type="button" 
                                                class="inline-flex items-center px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-200 delete-vehicule"
                                                title="Supprimer le véhicule"
                                                data-vehicule-id="{{ $vehicule->id }}"
                                                data-vehicule-immatriculation="{{ $vehicule->immatriculation }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                        <p class="text-lg font-semibold mb-2">Aucun véhicule trouvé</p>
                                        <p class="text-sm">Commencez par ajouter votre premier véhicule.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($vehicules->hasPages())
                <div class="mt-6">
                    {{ $vehicules->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Inclure SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration SweetAlert2
    const Swal = window.Swal;

    // Données des véhicules (serait normalement récupérées via API)
    const vehiculesData = {
        @foreach($vehicules as $vehicule)
        {{ $vehicule->id }}: {
            id: {{ $vehicule->id }},
            immatriculation: "{{ $vehicule->immatriculation }}",
            numero_serie: "{{ $vehicule->numero_serie ?? 'N/A' }}",
            type_range: "{{ $vehicule->type_range }}",
            nombre_place: {{ $vehicule->nombre_place }},
            is_active: {{ $vehicule->is_active }},
            motif: "{{ $vehicule->motif ?? 'Aucun motif' }}",
            created_at: "{{ $vehicule->created_at->format('d/m/Y H:i') }}",
            updated_at: "{{ $vehicule->updated_at->format('d/m/Y H:i') }}"
        },
        @endforeach
    };

    // Afficher les détails d'un véhicule
    document.querySelectorAll('.view-details').forEach(button => {
        button.addEventListener('click', function() {
            const vehiculeId = this.getAttribute('data-vehicule-id');
            const vehicule = vehiculesData[vehiculeId];

            Swal.fire({
                title: `Détails du Véhicule`,
                html: `
                    <div class="text-left space-y-3">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <strong>Immatriculation:</strong><br>
                                <span class="font-mono">${vehicule.immatriculation}</span>
                            </div>
                            <div>
                                <strong>Numéro de série:</strong><br>
                                ${vehicule.numero_serie}
                            </div>
                            <div>
                                <strong>Type de rangée:</strong><br>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    ${vehicule.type_range}
                                </span>
                            </div>
                            <div>
                                <strong>Nombre de places:</strong><br>
                                ${vehicule.nombre_place} places
                            </div>
                            <div>
                                <strong>Statut:</strong><br>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${vehicule.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${vehicule.is_active ? 'Actif' : 'Inactif'}
                                </span>
                            </div>
                        </div>
                        ${!vehicule.is_active ? `
                        <div>
                            <strong>Motif d'inactivité:</strong><br>
                            <span class="text-red-600">${vehicule.motif}</span>
                        </div>
                        ` : ''}
                        <div class="border-t pt-3 text-sm text-gray-500">
                            <div>Créé le: ${vehicule.created_at}</div>
                            <div>Modifié le: ${vehicule.updated_at}</div>
                        </div>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Fermer',
                confirmButtonColor: '#e94f1b',
                width: '600px'
            });
        });
    });

    // Activer un véhicule
    document.querySelectorAll('.activate-vehicule').forEach(button => {
        button.addEventListener('click', function() {
            const vehiculeId = this.getAttribute('data-vehicule-id');
            const immatriculation = this.getAttribute('data-vehicule-immatriculation');

            Swal.fire({
                title: 'Activer le véhicule',
                text: `Êtes-vous sûr de vouloir activer le véhicule ${immatriculation} ?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Oui, activer',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6B7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Soumettre le formulaire d'activation
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/company/car/${vehiculeId}/activate`;
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'PATCH';
                    
                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });

    // Désactiver un véhicule
    document.querySelectorAll('.deactivate-vehicule').forEach(button => {
        button.addEventListener('click', function() {
            const vehiculeId = this.getAttribute('data-vehicule-id');
            const immatriculation = this.getAttribute('data-vehicule-immatriculation');

            Swal.fire({
                title: 'Désactiver le véhicule',
                text: `Êtes-vous sûr de vouloir désactiver le véhicule ${immatriculation} ?`,
                icon: 'warning',
                input: 'text',
                inputLabel: 'Motif de désactivation',
                inputPlaceholder: 'Entrez le motif de désactivation...',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Le motif est obligatoire!';
                    }
                },
                showCancelButton: true,
                confirmButtonText: 'Oui, désactiver',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#F59E0B',
                cancelButtonColor: '#6B7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Soumettre le formulaire de désactivation avec le motif
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/company/car/${vehiculeId}/deactivate`;
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'PATCH';
                    
                    const motifField = document.createElement('input');
                    motifField.type = 'hidden';
                    motifField.name = 'motif';
                    motifField.value = result.value;
                    
                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    form.appendChild(motifField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });

    // Supprimer un véhicule
    document.querySelectorAll('.delete-vehicule').forEach(button => {
        button.addEventListener('click', function() {
            const vehiculeId = this.getAttribute('data-vehicule-id');
            const immatriculation = this.getAttribute('data-vehicule-immatriculation');

            Swal.fire({
                title: 'Supprimer le véhicule',
                html: `Êtes-vous sûr de vouloir supprimer définitivement le véhicule <strong>${immatriculation}</strong> ?<br><span class="text-red-600">Cette action est irréversible!</span>`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Soumettre le formulaire de suppression
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/company/car/${vehiculeId}`;
                    
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

    // Afficher les messages de session avec SweetAlert
    @if(session('success'))
    Swal.fire({
        title: 'Succès!',
        text: '{{ session('success') }}',
        icon: 'success',
        confirmButtonText: 'OK',
        confirmButtonColor: '#e94f1b'
    });
    @endif

    @if(session('error'))
    Swal.fire({
        title: 'Erreur!',
        text: '{{ session('error') }}',
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#EF4444'
    });
    @endif
});
</script>

<style>
    .pagination {
        display: flex;
        justify-content: center;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .pagination li {
        margin: 0 4px;
    }
    
    .pagination li a,
    .pagination li span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 12px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .pagination li a {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #4a5568;
    }
    
    .pagination li a:hover {
        background-color: #e94f1b;
        border-color: #e94f1b;
        color: white;
        transform: translateY(-1px);
    }
    
    .pagination li span {
        background-color: #e94f1b;
        border: 1px solid #e94f1b;
        color: white;
    }
    
    .pagination li.active span {
        background-color: #e89116;
        border-color: #e89116;
    }
</style>
@endsection