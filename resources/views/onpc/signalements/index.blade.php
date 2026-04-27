@extends('onpc.layouts.app')

@section('title', 'Signalements')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-black text-gray-900">Signalements</h1>
        <p class="text-sm text-gray-500 mt-1">Tous les signalements remontés par les chauffeurs / passagers</p>
    </div>

    <form method="GET" action="{{ route('onpc.signalements.index') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 grid grid-cols-1 md:grid-cols-12 gap-3">
        <div class="md:col-span-4 relative">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher (description, caserne, compagnie)..."
                class="w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
        <select name="type" class="md:col-span-2 px-3 py-2.5 border border-gray-200 rounded-lg">
            <option value="">Tous types</option>
            @foreach(['accident','panne','retard','comportement','autre'] as $t)
                <option value="{{ $t }}" @selected(request('type') === $t)>{{ ucfirst($t) }}</option>
            @endforeach
        </select>
        <select name="statut" class="md:col-span-2 px-3 py-2.5 border border-gray-200 rounded-lg">
            <option value="">Tous statuts</option>
            <option value="nouveau" @selected(request('statut') === 'nouveau')>Nouveau</option>
            <option value="traite"  @selected(request('statut') === 'traite')>Traité</option>
        </select>
        <select name="sapeur_pompier_id" class="md:col-span-2 px-3 py-2.5 border border-gray-200 rounded-lg">
            <option value="">Toutes casernes</option>
            @foreach($casernes as $c)
                <option value="{{ $c->id }}" @selected((string) request('sapeur_pompier_id') === (string) $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="md:col-span-1 px-2 py-2.5 border border-gray-200 rounded-lg" title="Du">
        <input type="date" name="date_to"   value="{{ request('date_to')   }}" class="md:col-span-1 px-2 py-2.5 border border-gray-200 rounded-lg" title="Au">

        <div class="md:col-span-12 flex justify-end gap-2">
            <a href="{{ route('onpc.signalements.index') }}" class="px-4 py-2.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold">Réinitialiser</a>
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-blue-700 hover:bg-blue-800 text-white font-bold">
                <i class="fas fa-filter mr-1"></i> Filtrer
            </button>
        </div>
    </form>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-x-auto">
        <table class="w-full">
            <thead class="text-xs uppercase font-semibold text-gray-500 bg-gray-50 border-b">
                <tr>
                    <th class="p-3 text-left">#</th>
                    <th class="p-3 text-left">Date</th>
                    <th class="p-3 text-left">Type</th>
                    <th class="p-3 text-left">Caserne</th>
                    <th class="p-3 text-left">Compagnie</th>
                    <th class="p-3 text-left">Trajet</th>
                    <th class="p-3 text-center">Bilan</th>
                    <th class="p-3 text-center">Statut</th>
                    <th class="p-3 text-center"></th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse($signalements as $s)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-mono text-xs">#{{ $s->id }}</td>
                        <td class="p-3">{{ $s->created_at->format('d/m/Y H:i') }}</td>
                        <td class="p-3 capitalize">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-bold {{ $s->type === 'accident' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700' }}">
                                <i class="fas {{ $s->type === 'accident' ? 'fa-car-crash' : 'fa-exclamation-triangle' }}"></i>
                                {{ $s->type }}
                            </span>
                        </td>
                        <td class="p-3">
                            @if($s->sapeurPompier)
                                <a href="{{ route('onpc.sapeurs.show', $s->sapeurPompier->id) }}" class="text-blue-700 hover:underline">{{ $s->sapeurPompier->name }}</a>
                            @else <span class="text-gray-400">—</span> @endif
                        </td>
                        <td class="p-3">{{ optional($s->compagnie)->name ?? '—' }}</td>
                        <td class="p-3 text-xs text-gray-600">
                            @if($s->voyage && $s->voyage->programme)
                                {{ $s->voyage->programme->point_depart }} → {{ $s->voyage->programme->point_arrive }}
                            @elseif($s->convoi && $s->convoi->itineraire)
                                {{ $s->convoi->itineraire->point_depart }} → {{ $s->convoi->itineraire->point_arrive }}
                            @else — @endif
                        </td>
                        <td class="p-3 text-center text-xs">
                            @if($s->statut === 'traite')
                                <span class="text-rose-600 font-bold">{{ $s->nombre_morts }}M</span>
                                / <span class="text-orange-600 font-bold">{{ $s->nombre_blesses }}B</span>
                            @else — @endif
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-1 text-xs rounded-full {{ $s->statut === 'traite' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} font-semibold">
                                {{ ucfirst($s->statut) }}
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            <a href="{{ route('onpc.signalements.show', $s->id) }}" class="text-blue-700 hover:underline text-sm font-semibold">Détails</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="p-6 text-center text-gray-500">Aucun signalement trouvé.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $signalements->links() }}</div>
@endsection
