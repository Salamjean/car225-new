@extends('admin.layouts.template')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
            <div class="mb-6 lg:mb-0">
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-3">Gestion des Hotesses</h1>
                <p class="text-lg text-gray-600">
                   Gérez les hotesses et leur assignation aux compagnies
                </p>
            </div>

            <!-- Bouton d'ajout -->
            <a href="{{ route('admin.hotesse.create') }}"
                class="inline-flex items-center px-6 py-4 bg-[#e94e1a] text-white font-bold rounded-xl hover:bg-[#d33d0f] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouvelle Hotesse
            </a>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Total Hotesses --><div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-[#e94e1a]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Hotesses</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $hotesses->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#e94e1a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Hotesses actives -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Actives</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $hotesses->where('archived_at', null)->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                    <h2 class="text-xl font-bold text-gray-900 mb-4 lg:mb-0">Liste des Hotesses</h2>

                    <!-- Barre de recherche -->
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Rechercher une hotesse..."
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableau -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Hotesse</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Compagnie</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Commune</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="hotesseTable">
                        @forelse($hotesses as $hotesse)
                            <tr class="hover:bg-gray-50 transition-colors duration-200 hotesse-row"
                                data-search="{{ strtolower($hotesse->name . ' ' . $hotesse->prenom . ' ' . $hotesse->email . ' ' . $hotesse->compagnie->name) }}">
                                <!-- Photo et informations -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-center">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            @if($hotesse->profile_picture)
                                                <img class="h-12 w-12 rounded-full object-cover border-2 border-gray-200"
                                                    src="{{ asset('storage/' . $hotesse->profile_picture) }}"
                                                    alt="{{ $hotesse->prenom }} {{ $hotesse->name }}"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="h-12 w-12 rounded-full bg-[#e94e1a] flex items-center justify-center text-white font-bold text-sm hidden">
                                                    {{ substr($hotesse->prenom, 0, 1) }}{{ substr($hotesse->name, 0, 1) }}
                                                </div>
                                            @else
                                                <div class="h-12 w-12 rounded-full bg-[#e94e1a] flex items-center justify-center text-white font-bold text-sm">
                                                    {{ substr($hotesse->prenom, 0, 1) }}{{ substr($hotesse->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900">{{ $hotesse->prenom }} {{ $hotesse->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $hotesse->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Compagnie -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm font-medium text-gray-900">{{ $hotesse->compagnie->name }}</div>
                                </td>

                                <!-- Contact -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm text-gray-900">{{ $hotesse->contact }}</div>
                                    @if($hotesse->cas_urgence)
                                        <div class="text-xs text-gray-500">Urgence: {{ $hotesse->cas_urgence }}</div>
                                    @endif
                                </td>

                                <!-- Commune -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm text-gray-900">{{ $hotesse->commune ?? 'Non renseignée' }}</div>
                                </td>

                                <!-- Statut -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($hotesse->isArchived())
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                            Archivée
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                            Active
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-3">
                                        <!-- Bouton Recharger -->
                                        <button type="button" onclick="showRechargeModal({{ $hotesse->id }}, '{{ $hotesse->prenom }} {{ $hotesse->name }}', {{ $hotesse->tickets }})"
                                            class="text-blue-600 hover:text-blue-900 transition-colors duration-200 p-2 rounded-lg hover:bg-blue-50"
                                            title="Recharger tickets">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                        </button>

                                        <!-- Bouton Archive/Unarchive -->
                                        <button type="button" onclick="toggleArchive({{ $hotesse->id }}, {{ $hotesse->isArchived() ? 'true' : 'false' }})"
                                            class="text-yellow-600 hover:text-yellow-900 transition-colors duration-200 p-2 rounded-lg hover:bg-yellow-50"
                                            title="{{ $hotesse->isArchived() ? 'Réactiver' : 'Archiver' }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                            </svg>
                                        </button>

                                        <!-- Bouton Supprimer -->
                                        <button type="button" onclick="confirmDelete({{ $hotesse->id }}, '{{ $hotesse->prenom }} {{ $hotesse->name }}')"
                                            class="text-red-600 hover:text-red-900 transition-colors duration-200 p-2 rounded-lg hover:bg-red-50"
                                            title="Supprimer">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <p class="text-lg font-medium mb-2">Aucune hotesse trouvée</p>
                                        <p class="text-sm mb-4">Commencez par ajouter une nouvelle hotesse.</p>
                                        <a href="{{ route('admin.hotesse.create') }}"
                                            class="inline-flex items-center px-4 py-2 bg-[#e94e1a] text-white font-semibold rounded-lg hover:bg-[#d33d0f] transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Ajouter une hotesse
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Filtrage et recherche
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const hotesseRows = document.querySelectorAll('.hotesse-row');

    searchInput.addEventListener('input', function() {
        const searchValue = this.value.toLowerCase();
        
        hotesseRows.forEach(row => {
            const searchData = row.getAttribute('data-search');
            if (searchData.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});

// Modal de rechargement
function showRechargeModal(hotesseId, hotesseName, currentTickets) {
    Swal.fire({
        title: 'Recharger les tickets',
        html: `
            <div class="text-left space-y-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm font-medium text-gray-700">Hotesse: <span class="text-blue-600">${hotesseName}</span></p>
                    <p class="text-sm font-medium text-gray-700">Tickets actuels: <span class="text-blue-600">${currentTickets}</span></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de tickets à assigner</label>
                    <input type="number" id="recharge-tickets" min="1" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Entrez le nombre de tickets">
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Recharger',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#e94e1a',
        cancelButtonColor: '#6b7280',
        preConfirm: () => {
            const tickets = document.getElementById('recharge-tickets').value;
            if (!tickets || tickets < 1) {
                Swal.showValidationMessage('Veuillez entrer un nombre de tickets valide');
                return false;
            }
            return tickets;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            rechargeTickets(hotesseId, result.value);
        }
    });
}

// Recharger tickets via AJAX
function rechargeTickets(hotesseId, tickets) {
    fetch(`/admin/hotesse/${hotesseId}/recharge`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ tickets: tickets })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.error || 'Une erreur est survenue');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Succès!',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#e94e1a'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                title: 'Erreur!',
                text: data.error,
                icon: 'error',
                confirmButtonColor: '#d33'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Erreur!',
            text: error.message || 'Une erreur est survenue',
            icon: 'error',
            confirmButtonColor: '#d33'
        });
    });
}

// Archive/unarchive
function confirmDelete(hotesseId, hotesseName) {
    Swal.fire({
        title: 'Êtes-vous sûr ?',
        html: `<p>L'hotesse "<strong>${hotesseName}</strong>" sera définitivement supprimée.</p>
               <p class="text-sm text-red-600 mt-2">Cette action est irréversible !</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Oui, supprimer !',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/hotesse/${hotesseId}`;

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

// Messages flash
@if(session('success'))
    Swal.fire({
        title: 'Succès !',
        text: '{{ session('success') }}',
        icon: 'success',
        confirmButtonColor: '#e94e1a',
        timer: 3000,
        timerProgressBar: true
    });
@endif

@if(session('error'))
    Swal.fire({
        title: 'Erreur !',
        text: '{{ session('error') }}',
        icon: 'error',
        confirmButtonColor: '#d33'
    });
@endif
</script>

<style>
.hotesse-row {
    transition: all 0.3s ease;
}

.hotesse-row:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>
@endsection
