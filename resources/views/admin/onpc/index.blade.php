@extends('admin.layouts.template')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Page header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 font-bold flex items-center gap-3">
                    <i class="fas fa-shield-alt text-blue-700"></i>
                    Agents ONPC
                </h1>
                <p class="text-sm text-gray-500 mt-1">Office National de la Protection Civile — Superviseurs nationaux</p>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('admin.onpc.create') }}" class="btn bg-blue-700 hover:bg-blue-800 text-white">
                    <i class="fas fa-user-plus"></i>
                    <span class="hidden xs:block ml-2">Ajouter un agent ONPC</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <!-- Search -->
        <form method="GET" action="{{ route('admin.onpc.index') }}" class="mb-4">
            <div class="relative max-w-md">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Rechercher (nom, email, contact, localisation)..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </form>

        <!-- Table -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-200">
            <div class="p-3">
                <div class="overflow-x-auto">
                    <table class="table-auto w-full">
                        <thead class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="p-2 text-left">Agent</th>
                                <th class="p-2 text-left">Email</th>
                                <th class="p-2 text-left">Contact</th>
                                <th class="p-2 text-left">Localisation</th>
                                <th class="p-2 text-left">Statut</th>
                                <th class="p-2 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">
                            @forelse($onpcs as $onpc)
                                <tr>
                                    <td class="p-2">
                                        <div class="flex items-center">
                                            @if($onpc->photo_path)
                                                <div class="w-10 h-10 shrink-0 mr-3">
                                                    <img class="rounded-full object-cover w-10 h-10"
                                                        src="{{ Storage::url($onpc->photo_path) }}"
                                                        alt="{{ $onpc->name }}">
                                                </div>
                                            @else
                                                <div class="w-10 h-10 shrink-0 mr-3 bg-blue-100 rounded-full flex items-center justify-center text-blue-700">
                                                    <i class="fas fa-user-shield"></i>
                                                </div>
                                            @endif
                                            <div class="font-medium text-gray-800">{{ $onpc->name }}</div>
                                        </div>
                                    </td>
                                    <td class="p-2">{{ $onpc->email }}</td>
                                    <td class="p-2 font-medium text-green-600">{{ $onpc->contact ?? '—' }}</td>
                                    <td class="p-2 text-xs">{{ $onpc->localisation ?? '—' }}</td>
                                    <td class="p-2">
                                        @if($onpc->statut === 'actif')
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700 font-semibold">Actif</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700 font-semibold">Désactivé</span>
                                        @endif
                                    </td>
                                    <td class="p-2">
                                        <div class="flex items-center justify-center gap-2">
                                            <form action="{{ route('admin.onpc.resend-otp', $onpc->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" title="Renvoyer l'OTP par email"
                                                    class="text-amber-600 hover:text-amber-800 border border-amber-200 rounded p-1 hover:bg-amber-50">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('admin.onpc.edit', $onpc->id) }}"
                                                class="text-blue-600 hover:text-blue-900 border border-blue-200 rounded p-1 hover:bg-blue-50">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.onpc.destroy', $onpc->id) }}" method="POST"
                                                onsubmit="return confirm('Supprimer cet agent ONPC ?');" class="inline">
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
                                    <td colspan="6" class="p-6 text-center text-gray-500">
                                        Aucun agent ONPC enregistré.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4">{{ $onpcs->links() }}</div>
    </div>
@endsection
