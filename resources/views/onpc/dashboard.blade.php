@extends('onpc.layouts.app')

@section('title', 'Tableau de bord')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-black text-gray-900">Tableau de bord</h1>
        <p class="text-sm text-gray-500 mt-1">Vue d'ensemble nationale des interventions des sapeurs-pompiers</p>
    </div>

    <!-- KPI cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
        @php
            $kpis = [
                ['Casernes',      $stats['casernes_total'],         'fa-fire-extinguisher', 'bg-blue-100 text-blue-700'],
                ['Casernes actives', $stats['casernes_actives'],    'fa-shield-alt',        'bg-emerald-100 text-emerald-700'],
                ['Signalements',  $stats['signalements_total'],     'fa-exclamation-triangle','bg-amber-100 text-amber-700'],
                ['Nouveaux',      $stats['signalements_nouveaux'],  'fa-bell',              'bg-red-100 text-red-700'],
                ['Traités',       $stats['signalements_traites'],   'fa-check-double',      'bg-indigo-100 text-indigo-700'],
                ['Accidents',     $stats['accidents_total'],        'fa-car-crash',         'bg-rose-100 text-rose-700'],
                ['Morts',         $stats['morts_total'],            'fa-skull',             'bg-slate-200 text-slate-800'],
                ['Blessés',       $stats['blesses_total'],          'fa-user-injured',      'bg-orange-100 text-orange-700'],
                ['Évacués',       $stats['evacues_total'],          'fa-procedures',        'bg-teal-100 text-teal-700'],
                ['Du jour',       $stats['signalements_du_jour'],   'fa-calendar-day',      'bg-purple-100 text-purple-700'],
            ];
        @endphp
        @foreach($kpis as [$label, $value, $icon, $color])
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs uppercase font-semibold text-gray-500">{{ $label }}</span>
                    <div class="w-9 h-9 rounded-full {{ $color }} flex items-center justify-center"><i class="fas {{ $icon }}"></i></div>
                </div>
                <div class="text-2xl font-black text-gray-900">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent signalements -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h2 class="font-bold text-gray-900"><i class="fas fa-clock-rotate-left text-blue-700 mr-2"></i>Derniers signalements</h2>
                <a href="{{ route('onpc.signalements.index') }}" class="text-sm text-blue-700 font-semibold hover:underline">Voir tout →</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentSignalements as $s)
                    <a href="{{ route('onpc.signalements.show', $s->id) }}" class="flex items-start gap-3 p-4 hover:bg-gray-50">
                        <div class="w-10 h-10 rounded-full bg-{{ $s->type === 'accident' ? 'red' : 'amber' }}-100 text-{{ $s->type === 'accident' ? 'red' : 'amber' }}-700 flex items-center justify-center">
                            <i class="fas {{ $s->type === 'accident' ? 'fa-car-crash' : 'fa-exclamation-triangle' }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-gray-900 truncate">
                                    {{ ucfirst($s->type) }} — #{{ $s->id }}
                                </p>
                                <span class="text-xs text-gray-500">{{ $s->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-600 truncate">{{ \Illuminate\Support\Str::limit($s->description, 100) }}</p>
                            <div class="text-xs text-gray-500 mt-1 flex flex-wrap gap-2">
                                @if($s->sapeurPompier)<span><i class="fas fa-fire-extinguisher mr-1"></i>{{ $s->sapeurPompier->name }}</span>@endif
                                @if($s->compagnie)<span>· <i class="fas fa-building mr-1"></i>{{ $s->compagnie->name }}</span>@endif
                                <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 ml-auto">{{ $s->statut }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-6 text-center text-gray-500">Aucun signalement enregistré.</div>
                @endforelse
            </div>
        </div>

        <!-- Top casernes -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h2 class="font-bold text-gray-900"><i class="fas fa-trophy text-amber-500 mr-2"></i>Top casernes</h2>
                <a href="{{ route('onpc.sapeurs.index') }}" class="text-sm text-blue-700 font-semibold hover:underline">Tous →</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($topCasernes as $caserne)
                    <a href="{{ route('onpc.sapeurs.show', $caserne->id) }}" class="flex items-center gap-3 p-4 hover:bg-gray-50">
                        @if($caserne->path_logo)
                            <img src="{{ Storage::url($caserne->path_logo) }}" class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 rounded-full bg-red-100 text-red-700 flex items-center justify-center">
                                <i class="fas fa-fire-extinguisher"></i>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 truncate">{{ $caserne->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $caserne->commune }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-black text-blue-700">{{ $caserne->signalements_assigned_count ?? 0 }}</div>
                            <div class="text-[10px] uppercase font-semibold text-gray-400">interventions</div>
                        </div>
                    </a>
                @empty
                    <div class="p-6 text-center text-gray-500">Aucune caserne enregistrée.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
