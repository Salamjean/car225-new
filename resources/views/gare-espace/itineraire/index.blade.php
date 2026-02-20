@extends('gare-espace.layouts.template')

@section('title', 'Itinéraires')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Itinéraires</h2>
                <p class="text-gray-500 text-lg">Liste des itinéraires de votre compagnie</p>
            </div>
            <a href="{{ route('gare-espace.itineraire.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-[#e94f1b] text-white font-bold rounded-xl hover:bg-[#d33d0f] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                <i class="fas fa-plus mr-2"></i>
                Nouvel Itinéraire
            </a>
        </div>

        <!-- Search -->
        <div class="mb-6">
            <form method="GET" action="{{ route('gare-espace.itineraire.index') }}" class="flex gap-4">
                <div class="flex-1 relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="w-full px-4 py-3 pl-10 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-white"
                           placeholder="Rechercher par départ, arrivée ou durée...">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
                <button type="submit" class="px-6 py-3 bg-[#e94f1b] text-white font-semibold rounded-xl hover:bg-[#d33d0f] transition-all duration-200">
                    Rechercher
                </button>
            </form>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium uppercase">Total Itinéraires</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $itineraires->total() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-route text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium uppercase">Page actuelle</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $itineraires->currentPage() }} / {{ $itineraires->lastPage() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-alt text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Itinéraires List -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Point de départ</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Point d'arrivée</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Durée</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Date de création</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($itineraires as $itineraire)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-green-500 ring-4 ring-green-100"></div>
                                        <span class="font-semibold text-gray-900">{{ $itineraire->point_depart }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-red-500 ring-4 ring-red-100"></div>
                                        <span class="font-semibold text-gray-900">{{ $itineraire->point_arrive }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-sm font-semibold">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $itineraire->durer_parcours }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $itineraire->created_at->format('d/m/Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-route text-gray-300 text-3xl"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">Aucun itinéraire trouvé</p>
                                    <a href="{{ route('gare-espace.itineraire.create') }}" class="text-[#e94f1b] font-semibold mt-2 inline-block hover:underline">
                                        Créer le premier itinéraire
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($itineraires->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $itineraires->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Succès!',
        text: "{{ session('success') }}",
        confirmButtonColor: '#e94f1b',
        timer: 5000,
        showConfirmButton: true
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: "{{ session('error') }}",
        confirmButtonColor: '#e94f1b'
    });
@endif
</script>
@endsection
