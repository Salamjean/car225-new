@extends('gare-espace.layouts.template')

@section('title', 'Personnel')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Personnel</h2>
                <p class="text-gray-500 text-lg">Liste des chauffeurs et convoyeurs de votre compagnie</p>
            </div>
            <a href="{{ route('gare-espace.personnel.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-[#e94f1b] text-white font-bold rounded-xl hover:bg-[#d33d0f] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                <i class="fas fa-plus mr-2"></i>
                Créer un Personnel
            </a>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium uppercase">Total Personnel</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $personnels->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium uppercase">Chauffeurs</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $personnels->where('type_personnel', 'Chauffeur')->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-id-card text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium uppercase">Disponibles</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $personnels->where('statut', 'disponible')->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-check text-orange-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personnel List -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Photo</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Nom & Prénom</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Code ID</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Contact</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($personnels as $personnel)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    @if($personnel->profile_image)
                                        <img src="{{ asset('storage/' . $personnel->profile_image) }}" class="w-10 h-10 rounded-full object-cover" alt="">
                                    @else
                                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900">{{ $personnel->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $personnel->prenom }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-md font-mono text-xs">{{ $personnel->code_id ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($personnel->type_personnel === 'Chauffeur')
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-sm font-semibold">Chauffeur</span>
                                    @else
                                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-lg text-sm font-semibold">Convoyeur</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $personnel->contact }}</td>
                                <td class="px-6 py-4">
                                    @if($personnel->statut === 'disponible')
                                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-sm font-semibold">Disponible</span>
                                    @else
                                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-sm font-semibold">Indisponible</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-users text-gray-300 text-3xl"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">Aucun personnel trouvé</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Succès!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#e94f1b',
                timer: 3000
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
