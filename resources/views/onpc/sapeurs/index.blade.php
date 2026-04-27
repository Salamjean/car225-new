@extends('onpc.layouts.app')

@section('title', 'Sapeurs Pompiers')

@section('content')
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-gray-900">Sapeurs Pompiers</h1>
            <p class="text-sm text-gray-500 mt-1">Toutes les casernes et leur activité</p>
        </div>
    </div>

    <form method="GET" action="{{ route('onpc.sapeurs.index') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 grid grid-cols-1 md:grid-cols-4 gap-3">
        <div class="md:col-span-3 relative">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher (nom, email, commune, contact)..."
                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
        <div class="flex gap-2">
            <select name="statut" class="flex-1 px-3 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500/30">
                <option value="">Tous statuts</option>
                <option value="actif" @selected(request('statut') === 'actif')>Actif</option>
                <option value="desactive" @selected(request('statut') === 'desactive')>Désactivé</option>
            </select>
            <button type="submit" class="px-4 py-2.5 rounded-lg bg-blue-700 hover:bg-blue-800 text-white font-bold">
                <i class="fas fa-filter"></i>
            </button>
        </div>
    </form>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-x-auto">
        <table class="w-full">
            <thead class="text-xs uppercase font-semibold text-gray-500 bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left p-3">Caserne</th>
                    <th class="text-left p-3">Commune</th>
                    <th class="text-left p-3">Contact</th>
                    <th class="text-center p-3">Total</th>
                    <th class="text-center p-3">Traités</th>
                    <th class="text-center p-3">Statut</th>
                    <th class="text-center p-3">Action</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse($casernes as $c)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3">
                            <div class="flex items-center gap-3">
                                @if($c->path_logo)
                                    <img src="{{ Storage::url($c->path_logo) }}" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-red-100 text-red-700 flex items-center justify-center">
                                        <i class="fas fa-fire-extinguisher"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="font-bold text-gray-900">{{ $c->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $c->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-3">{{ $c->commune ?? '—' }}<div class="text-xs text-gray-500">{{ $c->adresse }}</div></td>
                        <td class="p-3 font-medium text-green-600">{{ $c->contact ?? '—' }}</td>
                        <td class="p-3 text-center font-bold text-blue-700">{{ $c->signalements_assigned_count ?? 0 }}</td>
                        <td class="p-3 text-center font-bold text-emerald-600">{{ $c->signalements_traites_count ?? 0 }}</td>
                        <td class="p-3 text-center">
                            @if($c->statut === 'actif')
                                <span class="px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700 font-semibold">Actif</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700 font-semibold">Désactivé</span>
                            @endif
                        </td>
                        <td class="p-3 text-center">
                            <a href="{{ route('onpc.sapeurs.show', $c->id) }}" class="text-blue-700 hover:underline text-sm font-semibold">
                                Détails →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-center text-gray-500">Aucune caserne trouvée.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $casernes->links() }}</div>
@endsection
