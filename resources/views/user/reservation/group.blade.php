@extends('user.layouts.template')

@section('title', 'Détails de la Transaction')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('reservation.index') }}" class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-[#e94f1b] hover:text-white transition-all">
                    <i class="fas fa-arrow-left text-xs"></i>
                </a>
                <h1 class="text-3xl font-black text-[#1A1D1F] tracking-tight uppercase">
                    Billets de la <span class="text-[#e94f1b]">Transaction</span>
                </h1>
            </div>
            <p class="text-gray-500 font-medium ml-11">Référence Transaction : <span class="font-mono font-bold text-gray-700">{{ $transaction_id }}</span></p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-5 py-2.5 bg-gray-900 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg">
                {{ $reservations->count() }} billet(s)
            </span>
        </div>
    </div>

    <!-- Reservations Table -->
    <div class="bg-white rounded-[32px] border border-gray-100 shadow-xl shadow-gray-200/40 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest pl-8">Billet</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Itinéraire & Compagnie</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Départ</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Siège</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Info Passager</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Tarif</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Statut</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center pr-8">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($reservations as $reservation)
                    <tr class="group hover:bg-[#fff5f2] transition-all duration-300">
                        <td class="px-6 py-5 pl-8">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white border-2 border-dashed border-gray-200 flex items-center justify-center text-gray-300 group-hover:border-[#e94f1b] group-hover:text-[#e94f1b] transition-colors">
                                    <i class="fas fa-barcode text-lg"></i>
                                </div>
                                <div>
                                    <span class="block text-xs font-mono font-semibold text-gray-600 bg-gray-100 px-2 py-1 rounded-md mb-1 group-hover:bg-white group-hover:shadow-sm transition-all">
                                        {{ $reservation->reference }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-5">
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2 mb-1">
                                    <div>
                                        <span class="text-sm font-medium text-[#1A1D1F] block">{{ $reservation->programme->point_depart }}</span>
                                    </div>
                                    <div class="w-12 h-[2px] bg-gray-200 relative flex items-center justify-center mx-2">
                                        <i class="fas fa-bus text-[8px] text-gray-400 absolute bg-white px-1"></i>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-[#1A1D1F] block">{{ $reservation->programme->point_arrive }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-bold text-gray-900 tracking-wide">{{ $reservation->programme->compagnie->sigle ?? '' }}</span>
                                    <span class="text-[10px] font-light text-gray-500 tracking-wide ml-1">{{ $reservation->programme->compagnie->name }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col items-center justify-center bg-gray-50 border border-gray-100 rounded-xl w-12 h-12">
                                    <span class="text-[8px] font-semibold text-[#e94f1b] uppercase leading-none mt-1">
                                        {{ Str::upper(\Carbon\Carbon::parse($reservation->date_voyage)->locale('fr')->translatedFormat('M')) }}
                                    </span>
                                    <span class="text-lg font-bold text-gray-800 leading-none">
                                        {{ \Carbon\Carbon::parse($reservation->date_voyage)->format('d') }}
                                    </span>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($reservation->heure_depart ?? $reservation->programme->heure_depart)->format('H:i') }}</p>
                            </div>
                        </td>

                        <td class="px-6 py-5 text-center">
                            <div class="inline-block relative">
                                <svg class="w-8 h-8 text-gray-200 group-hover:text-[#1A1D1F] transition-colors" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/></svg>
                                <span class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-[10px] font-bold text-gray-600 group-hover:text-white">{{ $reservation->seat_number }}</span>
                            </div>
                        </td>

                        <td class="px-6 py-5 text-center">
                            <button type="button" 
                                class="view-passenger-details-btn relative inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-100 text-gray-500 hover:bg-[#e94f1b] hover:text-white hover:shadow-lg hover:shadow-[#e94f1b]/30 transition-all duration-300"
                                data-nom="{{ $reservation->passager_nom }}"
                                data-prenom="{{ $reservation->passager_prenom }}"
                                data-email="{{ $reservation->passager_email ?? 'Non renseigné' }}"
                                data-telephone="{{ $reservation->passager_telephone ?? 'Non renseigné' }}"
                                data-urgence="{{ $reservation->passager_urgence ?? 'Non renseigné' }}">
                                <i class="far fa-user"></i>
                            </button>
                        </td>

                        <td class="px-6 py-5 text-right">
                            <p class="text-sm font-bold text-[#1A1D1F]">{{ number_format($reservation->montant, 0, ',', ' ') }}</p>
                            <p class="text-[9px] font-medium text-gray-400 uppercase tracking-wider">FCFA</p>
                        </td>

                        <td class="px-6 py-5 text-center">
                            @if(in_array($reservation->statut, ['confirmee', 'terminee']) && $reservation->mission && $reservation->mission->statut == 'en_cours')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-50 text-purple-600 text-[10px] font-bold rounded-lg uppercase tracking-widest border border-purple-100 italic">
                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-pulse"></span> En voyage
                                </span>
                            @elseif($reservation->statut == 'confirmee')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-600 text-[10px] font-bold rounded-lg uppercase tracking-widest border border-green-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Confirmé
                                </span>
                            @elseif($reservation->statut == 'terminee')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 text-[10px] font-bold rounded-lg uppercase tracking-widest border border-blue-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Terminé
                                </span>
                            @elseif($reservation->statut == 'annulee')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 text-[10px] font-bold rounded-lg uppercase tracking-widest border border-red-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Annulé
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-yellow-50 text-yellow-600 text-[10px] font-bold rounded-lg uppercase tracking-widest border border-yellow-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> {{ ucfirst($reservation->statut) }}
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-5 text-center pr-8">
                            <div class="flex items-center justify-center gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                @if($reservation->statut == 'confirmee')
                                    @php
                                        $heureDepart = $reservation->heure_depart ?? $reservation->programme->heure_depart ?? '00:00';
                                        $dateVoyage = \Carbon\Carbon::parse($reservation->date_voyage)->format('Y-m-d');
                                        $departureDateTime = \Carbon\Carbon::parse("{$dateVoyage} {$heureDepart}");
                                        $canAct = $departureDateTime->diffInMinutes(now(), false) < -15;
                                        if ($reservation->embarquement_scanned_at) { $canAct = false; }
                                    @endphp
                                    
                                    <button type="button" class="modify-btn w-8 h-8 rounded-lg flex items-center justify-center transition-all {{ $canAct ? 'bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}"
                                        data-id="{{ $reservation->id }}"
                                        data-reference="{{ $reservation->reference }}"
                                        data-departure="{{ $departureDateTime->toISOString() }}"
                                        title="Modifier" {{ !$canAct ? 'disabled' : '' }}>
                                        <i class="fas fa-pen text-xs"></i>
                                    </button>
                                    
                                    <button type="button" class="cancel-btn w-8 h-8 rounded-lg flex items-center justify-center transition-all {{ $canAct ? 'bg-red-50 text-red-600 hover:bg-red-600 hover:text-white' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}"
                                        data-id="{{ $reservation->id }}"
                                        data-reference="{{ $reservation->reference }}"
                                        data-montant="{{ $reservation->montant }}"
                                        data-departure="{{ $departureDateTime->toISOString() }}"
                                        title="Annuler" {{ !$canAct ? 'disabled' : '' }}>
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>

                                    <a href="{{ route('reservations.download', $reservation) }}" class="w-8 h-8 bg-gray-900 text-white rounded-lg flex items-center justify-center hover:scale-110 transition-transform shadow-lg shadow-gray-900/20" title="Télécharger">
                                        <i class="fas fa-download text-xs"></i>
                                    </a>
                                @elseif($reservation->statut == 'terminee')
                                    <a href="{{ route('reservations.download', $reservation) }}" class="w-8 h-8 bg-gray-900 text-white rounded-lg flex items-center justify-center hover:scale-110 transition-transform shadow-lg shadow-gray-900/20" title="Télécharger">
                                        <i class="fas fa-download text-xs"></i>
                                    </a>
                                @endif
                                <a href="{{ route('reservations.show', $reservation) }}" class="w-8 h-8 bg-white border border-gray-200 text-gray-400 rounded-lg flex items-center justify-center hover:border-[#e94f1b] hover:text-[#e94f1b] transition-all" title="Détails">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')

@include('user.reservation.scripts')
@endpush
@endsection
