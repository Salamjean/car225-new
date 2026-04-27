@extends('onpc.layouts.app')

@section('title', $sapeurPompier->name)

@section('content')
    <a href="{{ route('onpc.sapeurs.index') }}" class="text-sm text-blue-700 hover:underline mb-4 inline-flex items-center gap-1">
        <i class="fas fa-arrow-left"></i> Retour à la liste
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 flex flex-col md:flex-row md:items-center gap-6">
        @if($sapeurPompier->path_logo)
            <img src="{{ Storage::url($sapeurPompier->path_logo) }}" class="w-24 h-24 rounded-full object-cover">
        @else
            <div class="w-24 h-24 rounded-full bg-red-100 text-red-700 flex items-center justify-center text-4xl">
                <i class="fas fa-fire-extinguisher"></i>
            </div>
        @endif
        <div class="flex-1">
            <h1 class="text-2xl font-black text-gray-900">{{ $sapeurPompier->name }}</h1>
            <p class="text-gray-500"><i class="fas fa-envelope mr-1"></i>{{ $sapeurPompier->email }}</p>
            <p class="text-gray-500"><i class="fas fa-phone mr-1"></i>{{ $sapeurPompier->contact }}</p>
            <p class="text-gray-500"><i class="fas fa-map-marker-alt mr-1"></i>{{ $sapeurPompier->commune }} — {{ $sapeurPompier->adresse }}</p>
        </div>
        <div class="grid grid-cols-3 gap-2 text-center">
            <div class="bg-blue-50 rounded-xl p-3">
                <div class="text-2xl font-black text-blue-700">{{ $sapeurPompier->signalements_assigned_count ?? 0 }}</div>
                <div class="text-[10px] uppercase text-gray-500 font-semibold">Total</div>
            </div>
            <div class="bg-amber-50 rounded-xl p-3">
                <div class="text-2xl font-black text-amber-700">{{ $sapeurPompier->signalements_nouveaux_count ?? 0 }}</div>
                <div class="text-[10px] uppercase text-gray-500 font-semibold">Nouveaux</div>
            </div>
            <div class="bg-emerald-50 rounded-xl p-3">
                <div class="text-2xl font-black text-emerald-700">{{ $sapeurPompier->signalements_traites_count ?? 0 }}</div>
                <div class="text-[10px] uppercase text-gray-500 font-semibold">Traités</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b">
            <h2 class="font-bold text-gray-900"><i class="fas fa-list text-blue-700 mr-2"></i>Historique des interventions</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="text-xs uppercase font-semibold text-gray-500 bg-gray-50 border-b">
                    <tr>
                        <th class="text-left p-3">Date</th>
                        <th class="text-left p-3">Type</th>
                        <th class="text-left p-3">Compagnie</th>
                        <th class="text-left p-3">Trajet</th>
                        <th class="text-center p-3">Bilan</th>
                        <th class="text-center p-3">Statut</th>
                        <th class="text-center p-3"></th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    @forelse($signalements as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3">{{ $s->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3 capitalize">{{ $s->type }}</td>
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
                        <tr><td colspan="7" class="p-6 text-center text-gray-500">Aucune intervention pour cette caserne.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $signalements->links() }}</div>
@endsection
