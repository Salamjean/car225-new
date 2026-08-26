@extends('admin.layouts.template')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Page header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Liste des Transporteurs Particuliers</h1>
                <p class="text-sm text-gray-500 mt-1">Historique complet des transporteurs particuliers agréés ou rejetés.</p>
            </div>
            
            <!-- Search bar -->
            <form action="{{ route('admin.particulier.index') }}" method="GET" class="relative">
                <input type="text" name="search" placeholder="Rechercher un particulier..." value="{{ request('search') }}"
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

        <!-- Table -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="p-4 whitespace-nowrap"><div class="font-semibold text-left">Code ID</div></th>
                            <th class="p-4 whitespace-nowrap"><div class="font-semibold text-left">Propriétaire</div></th>
                            <th class="p-4 whitespace-nowrap"><div class="font-semibold text-left">Email</div></th>
                            <th class="p-4 whitespace-nowrap"><div class="font-semibold text-left">Contact</div></th>
                            <th class="p-4 whitespace-nowrap"><div class="font-semibold text-left">Immatriculation</div></th>
                            <th class="p-4 whitespace-nowrap"><div class="font-semibold text-center">Places</div></th>
                            <th class="p-4 whitespace-nowrap"><div class="font-semibold text-center">Statut</div></th>
                            <th class="p-4 whitespace-nowrap"><div class="font-semibold text-center">Actions</div></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        @forelse($particuliers as $particulier)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 rounded bg-[#fff7ed] text-[#ea580c] font-mono text-xs font-bold border border-[#fed7aa]">
                                        {{ $particulier->code_id ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full overflow-hidden mr-3 bg-gray-100 flex-shrink-0">
                                            @if($particulier->photo_proprietaire)
                                                <img src="{{ $particulier->photo_proprietaire_url }}" alt="avatar" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center font-bold text-gray-500 bg-gray-200">
                                                    {{ substr($particulier->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-800">{{ $particulier->full_name }}</div>
                                            <div class="text-xs text-gray-400 font-semibold uppercase">Propriétaire</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="text-left font-medium text-gray-700">{{ $particulier->email }}</div>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="text-left font-semibold text-gray-600">{{ $particulier->contact }}</div>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 rounded bg-gray-100 text-gray-800 font-mono text-xs font-bold border border-gray-200">
                                        {{ $particulier->immatriculation }}
                                    </span>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="text-center font-bold text-gray-700">{{ $particulier->nombre_place_car }} places</div>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="text-center">
                                        @if($particulier->statut === 'valide')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold uppercase border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Agréé
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-50 text-red-700 text-xs font-bold uppercase border border-red-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Rejeté
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="flex justify-center">
                                        <a href="{{ route('admin.particulier.show', $particulier) }}"
                                            class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition-colors">
                                            <i class="fas fa-eye"></i> Consulter
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-gray-400 font-semibold">
                                    Aucun transporteur particulier enregistré.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($particuliers->hasPages())
                <div class="p-4 bg-gray-50 border-t border-gray-200">
                    {{ $particuliers->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
