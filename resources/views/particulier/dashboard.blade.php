@extends('particulier.layouts.template')

@section('page-title', 'Tableau de bord')

@section('content')
    <div class="space-y-8">
        
        <!-- Alerts -->
        @if (session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-r-xl shadow-sm font-semibold text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-r-xl shadow-sm font-semibold text-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Portefeuille Balance -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-emerald-500">
                <div>
                    <span class="text-[10px] font-black uppercase text-gray-400 tracking-wider">Solde portefeuille</span>
                    <h3 class="text-2xl font-black text-emerald-800 mt-1">
                        {{ number_format($soldeConvoie, 0, ',', ' ') }} <span class="text-xs font-bold text-gray-400">FCFA</span>
                    </h3>
                    <p class="text-xs text-gray-400 mt-1 font-semibold">Gains disponibles pour retrait</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500 text-xl shadow-inner">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>

            <!-- Total Earnings -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-orange-500">
                <div>
                    <span class="text-[10px] font-black uppercase text-gray-400 tracking-wider">Gains cumulés</span>
                    <h3 class="text-2xl font-black text-orange-800 mt-1">
                        {{ number_format($totalPaye, 0, ',', ' ') }} <span class="text-xs font-bold text-gray-400">FCFA</span>
                    </h3>
                    <p class="text-xs text-gray-400 mt-1 font-semibold">Chiffre d'affaires total payé</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-500 text-xl shadow-inner">
                    <i class="fas fa-coins"></i>
                </div>
            </div>

            <!-- Vehicle Stats -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-blue-500">
                <div>
                    <span class="text-[10px] font-black uppercase text-gray-400 tracking-wider">Mon Véhicule</span>
                    <h3 class="text-lg font-black text-gray-800 mt-1">
                        {{ $particulier->immatriculation }}
                    </h3>
                    <p class="text-xs text-gray-400 mt-1 font-semibold">{{ $particulier->nombre_place_car }} places · Agréé</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-xl shadow-inner">
                    <i class="fas fa-bus"></i>
                </div>
            </div>
        </div>

        <!-- Filter Navigation -->
        <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-4">
            @php
                $statusList = [
                    'all' => 'Tous',
                    'en_attente' => 'En attente',
                    'valide' => 'Validés (prix fixé)',
                    'paye' => 'Payés (À démarrer)',
                    'en_cours' => 'En cours',
                    'termine' => 'Terminés',
                    'annule' => 'Annulés',
                ];
            @endphp
            @foreach($statusList as $key => $label)
                <a href="{{ route('particulier.dashboard', ['statut' => $key]) }}"
                    class="px-4 py-2 text-xs font-black uppercase tracking-wider rounded-xl transition-all border {{ $statut === $key ? 'bg-[#e94f1b] text-white border-[#e94f1b] shadow-md shadow-[#e94f1b]/10' : 'bg-white text-gray-500 border-gray-200/80 hover:bg-gray-50 hover:text-gray-800' }}">
                    {{ $label }}
                    @if($key === 'en_attente' && $enAttenteCount > 0)
                        <span class="ml-1.5 px-1.5 py-0.5 rounded-full text-[9px] bg-white text-[#e94f1b] font-black">{{ $enAttenteCount }}</span>
                    @endif
                </a>
            @endforeach
        </div>

        <!-- Table Listing Convois -->
        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/80 border-b border-gray-200/60 text-xs text-gray-400 font-black uppercase">
                        <tr>
                            <th class="px-6 py-4">Référence</th>
                            <th class="px-6 py-4">Demandeur</th>
                            <th class="px-6 py-4">Trajet</th>
                            <th class="px-6 py-4 text-center">Places</th>
                            <th class="px-6 py-4 text-center">Date départ</th>
                            <th class="px-6 py-4 text-center">Statut</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        @forelse($convois as $convoi)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-black text-gray-700">{{ $convoi->reference }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800">{{ $convoi->demandeur_nom }}</div>
                                    <div class="text-xs text-gray-400 font-semibold">{{ $convoi->demandeur_contact }}</div>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-700">
                                    {{ $convoi->lieu_depart }} <i class="fas fa-arrow-right mx-1 text-gray-400 text-xs"></i> {{ $convoi->lieu_retour }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex px-2.5 py-1 rounded-lg bg-orange-50 text-[#e94f1b] text-xs font-black">
                                        {{ $convoi->nombre_personnes }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-500 font-semibold">
                                    {{ \Carbon\Carbon::parse($convoi->date_depart)->format('d/m/Y') }} à {{ substr($convoi->heure_depart, 0, 5) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $sm = [
                                            'en_attente' => ['En attente',  'bg-amber-50 text-amber-700 border-amber-100',   'bg-amber-500'],
                                            'valide'     => ['Validé',      'bg-blue-50 text-blue-700 border-blue-100',     'bg-blue-500'],
                                            'refuse'     => ['Refusé',      'bg-red-50 text-red-700 border-red-100',       'bg-red-500'],
                                            'paye'       => ['Payé',        'bg-green-50 text-green-700 border-green-100',   'bg-green-500'],
                                            'en_cours'   => ['En cours',    'bg-indigo-50 text-indigo-700 border-indigo-100', 'bg-indigo-500'],
                                            'termine'    => ['Terminé',     'bg-gray-100 text-gray-600 border-gray-200',    'bg-gray-400'],
                                            'annule'     => ['Annulé',      'bg-red-50 text-red-700 border-red-100',       'bg-red-500'],
                                        ];
                                        [$slabel, $sclass, $sdot] = $sm[$convoi->statut] ?? [$convoi->statut, 'bg-gray-100 text-gray-600 border-gray-200', 'bg-gray-400'];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full border {{ $sclass }} text-[10px] font-black uppercase">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $sdot }}"></span> {{ $slabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('particulier.convoi.show', $convoi) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-black uppercase transition-all shadow-sm">
                                        <i class="fas fa-eye"></i> Voir
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center text-gray-400 font-semibold">
                                    Aucune demande de convoi trouvée pour ce filtre.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($convois->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $convois->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
