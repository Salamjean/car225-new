@extends('admin.layouts.template')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Page header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Sapeurs Pompiers</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('sapeur-pompier.create') }}" class="btn bg-red-600 hover:bg-red-700 text-white">
                    <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                        <path
                            d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                    </svg>
                    <span class="hidden xs:block ml-2">Ajouter un Sapeur Pompier</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- Table -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-200">
            <div class="p-3">
                <div class="overflow-x-auto">
                    <table class="table-auto w-full">
                        <thead class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-left">Groupe</div>
                                </th>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-left">Email</div>
                                </th>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-left">Contact</div>
                                </th>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-left">Localisation</div>
                                </th>
                                <th class="p-2 whitespace-nowrap">
                                    <div class="font-semibold text-center">Actions</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">
                            @forelse($sapeurPompiers as $pompier)
                                <tr>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($pompier->path_logo)
                                                <div class="w-10 h-10 shrink-0 mr-2 sm:mr-3">
                                                    <img class="rounded-full" src="{{ Storage::url($pompier->path_logo) }}"
                                                        width="40" height="40" alt="{{ $pompier->name }}">
                                                </div>
                                            @else
                                                <div
                                                    class="w-10 h-10 shrink-0 mr-2 sm:mr-3 bg-red-100 rounded-full flex items-center justify-center text-red-500">
                                                    <i class="fas fa-fire-extinguisher"></i>
                                                </div>
                                            @endif
                                            <div class="font-medium text-gray-800">{{ $pompier->name }}</div>
                                        </div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-left">{{ $pompier->email }}</div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-left font-medium text-green-600">{{ $pompier->contact }}</div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="text-left">
                                            <div class="text-xs text-gray-500">{{ $pompier->commune }}</div>
                                            <div class="text-xs">{{ $pompier->adresse }}</div>
                                        </div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div
                                            class="text-center font-medium text-gray-800 flex items-center justify-center gap-2">
                                            <a href="{{ route('sapeur-pompier.edit', $pompier->id) }}"
                                                class="text-blue-600 hover:text-blue-900 border border-blue-200 rounded p-1 hover:bg-blue-50">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('sapeur-pompier.destroy', $pompier->id) }}" method="POST"
                                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce groupe ?');"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900 border border-red-200 rounded p-1 hover:bg-red-50">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-gray-500">
                                        Aucun sapeur pompier enregistré.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-4">
            {{ $sapeurPompiers->links() }}
        </div>
    </div>
@endsection