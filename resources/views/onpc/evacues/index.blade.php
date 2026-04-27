@extends('onpc.layouts.app')

@section('title', 'Passagers évacués')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-black text-gray-900">Passagers évacués</h1>
        <p class="text-sm text-gray-500 mt-1">Synthèse de tous les passagers évacués vers un hôpital, issus des bilans clôturés.</p>
    </div>

    <form method="GET" action="{{ route('onpc.evacues.index') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="relative max-w-xl">
            <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Rechercher (caserne, compagnie)..."
                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
    </form>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-x-auto">
        <table class="w-full">
            <thead class="text-xs uppercase font-semibold text-gray-500 bg-gray-50 border-b">
                <tr>
                    <th class="p-3 text-left">Date</th>
                    <th class="p-3 text-left">Passager</th>
                    <th class="p-3 text-left">Identité</th>
                    <th class="p-3 text-left">Trajet</th>
                    <th class="p-3 text-left">Caserne</th>
                    <th class="p-3 text-left">Compagnie</th>
                    <th class="p-3 text-left">Hôpital</th>
                    <th class="p-3 text-left">Contact urgence</th>
                    <th class="p-3 text-center"></th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse($rows as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 text-xs whitespace-nowrap">{{ $r['date']->format('d/m/Y H:i') }}</td>

                        {{-- Avatar + nom --}}
                        <td class="p-3">
                            <div class="flex items-center gap-3">
                                @if(!empty($r['photo_url']))
                                    <img src="{{ $r['photo_url'] }}" alt="{{ $r['passager'] }}"
                                        class="w-10 h-10 rounded-full object-cover ring-2 ring-blue-100">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center font-black text-sm shrink-0">
                                        {{ $r['photo_initials'] ?? '?' }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="font-bold text-gray-900 truncate">{{ $r['passager'] }}</div>
                                    @if(!empty($r['email']))
                                        <div class="text-[11px] text-gray-500 truncate"><i class="fas fa-envelope mr-1"></i>{{ $r['email'] }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Identité (âge, genre, contact) --}}
                        <td class="p-3">
                            <div class="flex flex-col gap-0.5 text-xs">
                                @if(!empty($r['age']))
                                    <span class="font-semibold text-gray-700">
                                        <i class="fas fa-birthday-cake text-pink-500 mr-1"></i>{{ $r['age'] }} ans
                                        @if(!empty($r['date_naissance']))
                                            <span class="text-gray-400">({{ \Carbon\Carbon::parse($r['date_naissance'])->format('d/m/Y') }})</span>
                                        @endif
                                    </span>
                                @endif
                                @if(!empty($r['genre']))
                                    <span class="text-gray-500 capitalize"><i class="fas {{ $r['genre'] === 'femme' ? 'fa-venus' : ($r['genre'] === 'homme' ? 'fa-mars' : 'fa-genderless') }} mr-1"></i>{{ $r['genre'] }}</span>
                                @endif
                                @if(!empty($r['contact']))
                                    <span class="text-gray-500"><i class="fas fa-phone mr-1"></i>{{ $r['contact'] }}</span>
                                @endif
                                @if(empty($r['age']) && empty($r['genre']) && empty($r['contact']))
                                    <span class="text-gray-400">—</span>
                                @endif
                            </div>
                        </td>

                        <td class="p-3 text-xs text-gray-600">{{ $r['trajet'] }}</td>
                        <td class="p-3 text-xs">{{ $r['caserne'] ?? '—' }}</td>
                        <td class="p-3 text-xs">{{ $r['compagnie'] ?? '—' }}</td>
                        <td class="p-3 text-xs">
                            <strong>{{ $r['hopital'] }}</strong>
                            @if(!empty($r['hopital_adresse']))
                                <div class="text-gray-500">{{ $r['hopital_adresse'] }}</div>
                            @endif
                        </td>
                        <td class="p-3 text-xs">{{ $r['contact_urgence'] ?? '—' }}</td>
                        <td class="p-3 text-center">
                            <a href="{{ route('onpc.signalements.show', $r['signalement_id']) }}" class="text-blue-700 hover:underline text-sm font-semibold whitespace-nowrap">Détails →</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="p-6 text-center text-gray-500">Aucun passager évacué pour le moment.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($lastPage > 1)
        <div class="mt-4 flex items-center justify-center gap-2">
            @for($p = 1; $p <= $lastPage; $p++)
                <a href="{{ route('onpc.evacues.index', array_merge(request()->query(), ['page' => $p])) }}"
                    class="px-3 py-1.5 rounded-lg text-sm border {{ $p === $page ? 'bg-blue-700 text-white border-blue-700' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                    {{ $p }}
                </a>
            @endfor
        </div>
    @endif
@endsection
