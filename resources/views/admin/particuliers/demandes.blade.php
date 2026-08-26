@extends('admin.layouts.template')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Page header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Demandes d'inscription Particuliers</h1>
                <p class="text-sm text-gray-500 mt-1">Candidatures en attente de validation par l'administration.</p>
            </div>
            
            <!-- Search bar -->
            <form action="{{ route('admin.particulier.demandes') }}" method="GET" class="relative">
                <input type="text" name="search" placeholder="Rechercher une demande..." value="{{ request('search') }}"
                    class="pl-9 pr-4 py-2 border border-gray-200 bg-white rounded-lg shadow-sm focus:ring-2 focus:ring-[#e94f1b] outline-none text-sm w-64 transition-all">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <i class="fas fa-search"></i>
                </span>
            </form>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
                <p class="font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
                <p class="font-semibold">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Table -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="p-4 whitespace-nowrap"><div class="font-semibold text-left">Propriétaire</div></th>
                            <th class="p-4 whitespace-nowrap"><div class="font-semibold text-left">Email</div></th>
                            <th class="p-4 whitespace-nowrap"><div class="font-semibold text-left">Contact</div></th>
                            <th class="p-4 whitespace-nowrap"><div class="font-semibold text-left">Immatriculation</div></th>
                            <th class="p-4 whitespace-nowrap"><div class="font-semibold text-center">Places</div></th>
                            <th class="p-4 whitespace-nowrap"><div class="font-semibold text-center">Date Demande</div></th>
                            <th class="p-4 whitespace-nowrap"><div class="font-semibold text-center">Actions</div></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        @forelse($demandes as $demande)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full overflow-hidden mr-3 bg-gray-100 flex-shrink-0">
                                            @if($demande->photo_proprietaire)
                                                <img src="{{ $demande->photo_proprietaire_url }}" alt="avatar" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center font-bold text-gray-500 bg-gray-200">
                                                    {{ substr($demande->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-800">{{ $demande->full_name }}</div>
                                            <div class="text-xs text-gray-400 font-semibold uppercase">Propriétaire</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="text-left font-medium text-gray-700">{{ $demande->email }}</div>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="text-left font-semibold text-gray-600">{{ $demande->contact }}</div>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 rounded bg-gray-100 text-gray-800 font-mono text-xs font-bold border border-gray-200">
                                        {{ $demande->immatriculation }}
                                    </span>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="text-center font-bold text-gray-700">{{ $demande->nombre_place_car }} places</div>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="text-center font-medium text-gray-500">{{ $demande->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('admin.particulier.show', $demande) }}"
                                            class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition-colors">
                                            <i class="fas fa-eye"></i> Étudier
                                        </a>
                                        <form action="{{ route('admin.particulier.valider', $demande) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment valider cette demande d\'inscription ?');">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-colors shadow-sm">
                                                <i class="fas fa-check"></i> Valider
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-gray-400 font-semibold">
                                    Aucune demande d'inscription en attente.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($demandes->hasPages())
                <div class="p-4 bg-gray-50 border-t border-gray-200">
                    {{ $demandes->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
