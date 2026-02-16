@extends('compagnie.layouts.template')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#e94f1b] rounded-2xl shadow-lg mb-4">
                <i class="fas fa-building text-3xl text-white"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Gestion des Gares</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Gérez vos gares et points d'embarquement
            </p>
        </div>

        <!-- Carte principale -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <!-- En-tête de la carte -->
            <div class="px-8 py-6 bg-gradient-to-r from-[#e94f1b] to-[#e89116]">
                <div class="flex flex-col sm:flex-row justify-between items-center">
                    <h2 class="text-2xl font-bold text-white mb-4 sm:mb-0">Liste des Gares</h2>
                    <a href="{{ route('gare.create') }}" 
                       class="flex items-center px-6 py-3 bg-white text-[#e94f1b] font-bold rounded-xl hover:bg-gray-50 transform hover:-translate-y-1 transition-all duration-200 shadow-lg">
                        <i class="fas fa-plus mr-2"></i>
                        Nouvelle Gare
                    </a>
                </div>
            </div>

            <!-- Contenu -->
            <div class="p-8">
                <!-- Tableau des gares -->
                <div class="overflow-hidden rounded-2xl border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nom de la Gare</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Ville</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Adresse</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($gares as $gare)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $gare->nom_gare }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600">{{ $gare->ville }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600">{{ $gare->adresse }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('gare.edit', $gare->id) }}" 
                                           class="inline-flex items-center px-3 py-2 bg-[#e94f1b] text-white rounded-lg hover:bg-[#e89116] transition-colors duration-200"
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <button type="button" 
                                                class="inline-flex items-center px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-200 delete-gare"
                                                title="Supprimer"
                                                data-gare-id="{{ $gare->id }}"
                                                data-gare-nom="{{ $gare->nom_gare }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <i class="fas fa-building text-5xl mb-4 text-gray-300"></i>
                                        <p class="text-lg font-semibold mb-2">Aucune gare trouvée</p>
                                        <p class="text-sm">Commencez par ajouter votre première gare.</p>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-gare').forEach(button => {
        button.addEventListener('click', function() {
            const gareId = this.getAttribute('data-gare-id');
            const nom = this.getAttribute('data-gare-nom');

            Swal.fire({
                title: 'Supprimer la gare',
                html: `Êtes-vous sûr de vouloir supprimer la gare <strong>${nom}</strong> ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/company/gare/${gareId}`;
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    const method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'DELETE';
                    form.appendChild(csrf);
                    form.appendChild(method);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });

    @if(session('success'))
    Swal.fire({
        title: 'Succès!',
        text: '{{ session('success') }}',
        icon: 'success',
        confirmButtonColor: '#e94f1b'
    });
    @endif
});
</script>
@endsection
