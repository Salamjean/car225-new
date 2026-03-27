@extends('agent.layouts.template')

@section('content')
<div class="bg-gray-50 py-6 px-4 sm:px-6">
    <div class="mx-auto" style="width: 100%;">

        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
                    Bienvenue, {{ $agent->name . ' ' . $agent->prenom ?? ' Non défini' }} 👋
                    <span class="px-2 py-0.5 bg-[#001a41]/10 text-[#001a41] text-xs font-bold rounded-lg border border-[#001a41]/20 uppercase tracking-wider">
                        Identifiant: {{ $agent->code_id ?? 'N/A' }}
                    </span>
                </h1>
                <p class="text-gray-500 mt-1">
                    Agent de <span class="font-semibold text-[#ff5a1f]">{{ $agent->compagnie->name ?? 'Compagnie' }}</span>
                    @if($agent->gare)
                        | <span class="font-semibold text-[#001a41]">{{ $agent->gare->nom_gare }}</span>
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
                   class="px-4 py-2 bg-[#ff5a1f] text-white rounded-lg shadow-md hover:bg-[#e64e16] transition-all flex items-center gap-2 font-bold">
                    <i class="fas fa-qrcode"></i>
                    Scanner QR
                </a>
            </div>
        </div>

        <!-- Row 1: Main Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-5 mb-6">
            <!-- Scans Today Card -->
            <div class="bg-gradient-to-br from-[#001a41] to-[#003380] rounded-2xl p-5 shadow-xl text-white transform hover:-translate-y-1 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-qrcode text-xl text-[#ff5a1f]"></i>
                    </div>
                    <span class="px-2 py-1 bg-[#ff5a1f] rounded-lg text-xs font-bold">
                        Aujourd'hui
                    </span>
                </div>
                <p class="text-sm font-bold text-white/80 uppercase tracking-wider">Mes embarquements effectués</p>
                <h3 class="text-3xl font-black mt-1">{{ $scansToday }}</h3>
                <p class="mt-3 text-xs text-white/60 font-medium italic border-t border-white/10 pt-3">
                    Total cumulé : <span class="text-[#ff5a1f] font-bold">{{ $totalScans }}</span> embarquements
                </p>
            </div>

            <!-- Total Terminées Card -->
            <div class="bg-gradient-to-br from-[#ff5a1f] to-[#e64e16] rounded-2xl p-5 shadow-xl text-white transform hover:-translate-y-1 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                </div>
                <p class="text-sm font-bold text-white/90 uppercase tracking-wider">Voyages terminés</p>
                <h3 class="text-3xl font-black mt-1">{{ $reservationsTerminees }}</h3>
                <p class="mt-3 text-xs text-white/80 font-medium italic border-t border-white/10 pt-3">
                    Historique total cumulé pour la gare
                </p>
            </div>
        </div>

        <!-- Row 2: Secondary Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
            <div class="bg-white rounded-xl p-4 shadow-md border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 bg-[#001a41]/5 rounded-lg flex items-center justify-center">
                    <i class="fas fa-route text-[#001a41] text-xl"></i>
                </div>
                <div class="text-left">
                    <p class="text-2xl font-black text-[#001a41] leading-none">{{ $programmesToday }}</p>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mt-1">Programmes aujourd'hui</p>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-md border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 bg-[#ff5a1f]/5 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-[#ff5a1f] text-xl"></i>
                </div>
                <div class="text-left">
                    <p class="text-2xl font-black text-[#ff5a1f] leading-none truncate max-w-[200px]">{{ $agent->gare->nom_gare ?? $agent->compagnie->name }}</p>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mt-1">Ma Gare de travail</p>
                </div>
            </div>
        </div>

        <!-- Row 3: Tables -->
        <div class="grid grid-cols-1 gap-6">
            <!-- Recent Scans -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-[#001a41]/5 to-white flex items-center justify-between">
                    <h3 class="text-lg font-black text-[#001a41] uppercase tracking-tight">
                        <i class="fas fa-history mr-2 text-[#ff5a1f]"></i>Derniers scans
                    </h3>
                    <a href="{{ route('agent.reservations.index') }}" class="text-sm text-[#ff5a1f] hover:underline font-bold">
                        Voir tout →
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-[#001a41] text-xs uppercase font-bold text-white">
                            <tr>
                                <th class="px-6 py-4 border-b border-[#001a41]/10">Passager</th>
                                <th class="px-6 py-4 border-b border-[#001a41]/10">Trajet</th>
                                <th class="px-6 py-4 border-b border-[#001a41]/10">Scanné le</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">
                            @forelse($recentScans as $scan)
                            <tr class="hover:bg-[#ff5a1f]/5 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-[#001a41]">{{ $scan->passager_prenom }} {{ $scan->passager_nom }}</div>
                                    <div class="text-xs font-medium text-gray-400">{{ $scan->reference }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-gray-700 font-medium">
                                        {{ $scan->programme->point_depart ?? '?' }} → {{ $scan->programme->point_arrive ?? '?' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 font-semibold italic">
                                    @if($scan->embarquement_scanned_at)
                                        {{ \Carbon\Carbon::parse($scan->embarquement_scanned_at)->format('d/m H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-gray-300">
                                    <i class="fas fa-inbox text-5xl mb-4 opacity-20"></i>
                                    <p class="text-lg font-bold">Aucun scan effectué</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>


        </div>

        <!-- Quick Action Button (Mobile) -->
        <div class="fixed bottom-6 right-6 lg:hidden">
            <a href="{{ route('agent.reservations.index') }}" 
               class="w-16 h-16 bg-[#ff5a1f] text-white rounded-full shadow-2xl flex items-center justify-center hover:scale-110 transition-all active:scale-95">
                <i class="fas fa-qrcode text-2xl"></i>
            </a>
        </div>

    </div>
</div>
@endsection