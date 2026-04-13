@extends('user.layouts.template')

@section('title', 'Mes Convois')

@section('content')
    <div class="space-y-6">
        <div class="inline-flex bg-white border border-gray-100 rounded-2xl p-1">
            <a href="{{ route('user.convoi.create') }}" class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider text-gray-600">
                Nouveau convoi
            </a>
            <a href="{{ route('user.convoi.index') }}" class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider bg-[#e94f1b] text-white">
                Mes convois
            </a>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-[#1A1D1F] tracking-tight">
                    Mes <span class="text-[#e94f1b]">Convois</span>
                </h1>
                <p class="text-sm text-gray-500 font-medium">Retrouvez l'historique de vos demandes de convoi.</p>
            </div>
            <a href="{{ route('user.convoi.create') }}"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-[#e94f1b] text-white text-xs font-black uppercase tracking-widest hover:bg-[#d44518] transition-all">
                <i class="fas fa-plus"></i>
                Nouveau convoi
            </a>
        </div>

        <div class="bg-white rounded-[28px] border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-5 py-4 text-[10px] font-black uppercase tracking-wider text-gray-500">Référence</th>
                            <th class="px-5 py-4 text-[10px] font-black uppercase tracking-wider text-gray-500">Compagnie</th>
                            <th class="px-5 py-4 text-[10px] font-black uppercase tracking-wider text-gray-500">Itinéraire</th>
                            <th class="px-5 py-4 text-[10px] font-black uppercase tracking-wider text-gray-500 text-center">Personnes</th>
                            <th class="px-5 py-4 text-[10px] font-black uppercase tracking-wider text-gray-500 text-center">Statut</th>
                            <th class="px-5 py-4 text-[10px] font-black uppercase tracking-wider text-gray-500">Date</th>
                            <th class="px-5 py-4 text-[10px] font-black uppercase tracking-wider text-gray-500 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($convois as $convoi)
                            <tr>
                                <td class="px-5 py-4 text-xs font-black text-gray-700">{{ $convoi->reference }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-gray-900">
                                    {{ $convoi->compagnie->name ?? 'Compagnie' }}
                                </td>
                                <td class="px-5 py-4 text-sm font-semibold text-gray-700">
                                    {{ $convoi->itineraire ? ($convoi->itineraire->point_depart . ' -> ' . $convoi->itineraire->point_arrive) : '-' }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex px-3 py-1 rounded-lg bg-orange-50 text-[#e94f1b] text-xs font-black">
                                        {{ $convoi->passagers_count ?: $convoi->nombre_personnes }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if ($convoi->statut === 'en_attente')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-amber-50 text-amber-700 text-[10px] font-black uppercase">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> En attente
                                        </span>
                                    @elseif($convoi->statut === 'valide')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-green-50 text-green-700 text-[10px] font-black uppercase">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Validé
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-red-50 text-red-700 text-[10px] font-black uppercase">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Annulé
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-sm font-semibold text-gray-600">
                                    {{ $convoi->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('user.convoi.show', $convoi->id) }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-100 text-gray-700 text-[11px] font-black uppercase tracking-wider hover:bg-gray-200 transition-all">
                                        <i class="fas fa-eye"></i>
                                        Voir
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-14 text-center">
                                    <p class="text-gray-400 font-semibold text-sm">Aucun convoi enregistré pour le moment.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($convois->hasPages())
                <div class="px-5 py-4 border-t border-gray-50 bg-gray-50">
                    {{ $convois->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

