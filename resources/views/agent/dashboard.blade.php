@extends('agent.layouts.template')

@section('content')
<div class="min-h-screen bg-gray-50 py-6 px-4 sm:px-6">
    <div class="mx-auto" style="max-width: 1400px;">

        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">
                    Bienvenue, {{ $agent->prenom ?? $agent->name }} 👋
                </h1>
                <p class="text-gray-500 mt-1">
                    Agent de <span class="font-semibold text-red-600">{{ $agent->compagnie->name ?? 'Compagnie' }}</span>
                    @if($agent->gare)
                        | <span class="font-semibold text-blue-600">{{ $agent->gare->nom_gare }}</span>
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-gray-400">{{ now()->format('d/m/Y H:i') }}</span>
                <button onclick="window.location.reload()"
                    class="p-2 bg-white rounded-lg shadow-sm border border-gray-200 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-sync-alt text-gray-600"></i>
                </button>
                <a href="{{ route('agent.reservations.index') }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition-all flex items-center gap-2">
                    <i class="fas fa-qrcode"></i>
                    Scanner QR
                </a>
            </div>
        </div>

        <!-- Row 1: Main Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-6">
            <!-- Scans Today Card -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-5 shadow-xl text-white transform hover:-translate-y-1 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-qrcode text-xl"></i>
                    </div>
                    <span class="px-2 py-1 bg-white/20 rounded-lg text-xs font-bold">
                        Moi aujourd'hui
                    </span>
                </div>
                <p class="text-sm font-bold text-white/80 uppercase tracking-wider">Mes scans effectués</p>
                <h3 class="text-3xl font-black mt-1">{{ $scansToday }}</h3>
                <p class="mt-3 text-xs text-white/70 font-medium">
                    Total: {{ $totalScans }} scans effectués par moi
                </p>
            </div>

            <!-- Passagers Embarqués Today Card (Station Scope) -->
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-5 shadow-xl text-white transform hover:-translate-y-1 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <span class="px-2 py-1 bg-white/20 rounded-lg text-xs font-bold uppercase tracking-tighter">
                        Gare aujourd'hui
                    </span>
                </div>
                <p class="text-sm font-bold text-white/80 uppercase tracking-wider">Passagers embarqués</p>
                <h3 class="text-3xl font-black mt-1">{{ $passagersEmbarquesToday }}</h3>
                <p class="mt-3 text-xs text-white/70 font-medium tracking-tight">
                    Confirmés au départ de cette gare
                </p>
            </div>

            <!-- Total Terminées Card -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-5 shadow-xl text-white transform hover:-translate-y-1 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                </div>
                <p class="text-sm font-bold text-white/80 uppercase tracking-wider">Voyages terminés</p>
                <h3 class="text-3xl font-black mt-1">{{ $reservationsTerminees }}</h3>
                <p class="mt-3 text-xs text-white/70 font-medium">
                    Historique total cumulé pour la gare
                </p>
            </div>
        </div>

        <!-- Row 2: Secondary Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
            <div class="bg-white rounded-xl p-4 shadow-md border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-route text-blue-600 text-xl"></i>
                </div>
                <div class="text-left">
                    <p class="text-2xl font-black text-gray-900 leading-none">{{ $programmesToday }}</p>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mt-1">Programmes aujourd'hui</p>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-md border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-orange-600 text-xl"></i>
                </div>
                <div class="text-left">
                    <p class="text-2xl font-black text-gray-900 leading-none">{{ $agent->gare->nom_gare ?? $agent->compagnie->name }}</p>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mt-1">Ma Gare de travail</p>
                </div>
            </div>
        </div>

        <!-- Row 3: Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Scans -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50 bg-gradient-to-r from-blue-50 to-white flex items-center justify-between">
                    <h3 class="text-lg font-black text-blue-900 uppercase tracking-tight">
                        <i class="fas fa-history mr-2"></i>Derniers scans
                    </h3>
                    <a href="{{ route('agent.reservations.index') }}" class="text-sm text-blue-600 hover:underline font-medium">
                        Voir tout →
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-xs uppercase font-bold text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Passager</th>
                                <th class="px-4 py-3">Trajet</th>
                                <th class="px-4 py-3">Scanné le</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">
                            @forelse($recentScans as $scan)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-900">{{ $scan->passager_prenom }} {{ $scan->passager_nom }}</div>
                                    <div class="text-xs text-gray-400">{{ $scan->reference }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-gray-700">
                                        {{ $scan->programme->point_depart ?? '?' }} → {{ $scan->programme->point_arrive ?? '?' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500">
                                    @if($scan->embarquement_scanned_at)
                                        {{ \Carbon\Carbon::parse($scan->embarquement_scanned_at)->format('d/m H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-400">
                                    <i class="fas fa-inbox text-3xl mb-2"></i>
                                    <p>Aucun scan effectué</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Programmes du jour -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50 bg-gradient-to-r from-orange-50 to-white">
                    <h3 class="text-lg font-black text-orange-900 uppercase tracking-tight">
                        <i class="fas fa-calendar-day mr-2"></i>Programmes du jour
                    </h3>
                </div>
                <div class="overflow-x-auto max-h-80">
                    @if($programmesDuJour->count() > 0)
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-xs uppercase font-bold text-gray-500 sticky top-0">
                            <tr>
                                <th class="px-4 py-3">Heure</th>
                                <th class="px-4 py-3">Trajet</th>
                                <th class="px-4 py-3">Véhicule</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">
                            @foreach($programmesDuJour as $prog)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-bold">
                                        {{ $prog->heure_depart }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-900">{{ $prog->point_depart }} → {{ $prog->point_arrive }}</div>
                                    <div class="text-xs text-gray-400">{{ number_format($prog->prix, 0, ',', ' ') }} FCFA</div>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $prog->vehicule->immatriculation ?? '-' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="p-8 text-center text-gray-400">
                        <i class="fas fa-calendar-times text-3xl mb-2"></i>
                        <p>Aucun programme prévu aujourd'hui</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Action Button (Mobile) -->
        <div class="fixed bottom-6 right-6 lg:hidden">
            <a href="{{ route('agent.reservations.index') }}" 
               class="w-16 h-16 bg-blue-600 text-white rounded-full shadow-2xl flex items-center justify-center hover:bg-blue-700 transition-all">
                <i class="fas fa-qrcode text-2xl"></i>
            </a>
        </div>

    </div>
</div>
@endsection