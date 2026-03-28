@extends('gare-espace.layouts.template')

@section('title', 'Passagers - ' . ($programme->gareArrivee?->nom_gare ?? $programme->point_arrive))

@section('styles')
<style>
    .program-page {
        min-height: 80vh;
        border-radius: 30px;
        padding: 30px;
        background: #F8F9FB;
        position: relative;
        z-index: 1;
    }
    .header-card {
        background: white;
        border-radius: 24px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        border: 1px solid rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    .route-display {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .route-icon {
        width: 60px; height: 60px;
        background: #f97316;
        color: white;
        border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px;
        box-shadow: 0 10px 20px rgba(249, 115, 22, 0.2);
    }
    .route-info h2 {
        font-size: 24px; font-weight: 900; color: #1e293b; margin: 0;
        text-transform: uppercase; letter-spacing: -0.5px;
    }
    .route-info p {
        font-size: 14px; color: #64748B; font-weight: 600; margin-top: 2px;
    }
    .back-btn {
        padding: 12px 24px;
        border-radius: 14px;
        background: #f1f5f9;
        color: #64748b;
        font-weight: 800;
        font-size: 13px;
        text-transform: uppercase;
        display: flex; align-items: center; gap: 10px;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
    }
    .back-btn:hover { background: #e2e8f0; color: #1e293b; transform: translateX(-5px); }

    .dash-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.03);
        border: 1px solid rgba(0,0,0,0.04);
        overflow: hidden;
    }
    .dash-table { width: 100%; border-collapse: separate; border-spacing: 0; text-align: left; }
    .dash-table th {
        background: #F8F9FB;
        padding: 18px 24px;
        font-size: 11px; font-weight: 800; color: #94A3B8; text-transform: uppercase; letter-spacing: 1px;
    }
    .dash-table td {
        padding: 16px 24px;
        border-bottom: 1px solid rgba(0,0,0,0.02);
    }
    .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 10px; font-weight: 800; }
    .sp-success { background: #ECFDF5; color: #059669; }
    .sp-pending { background: #FEF3C7; color: #d97706; }
    .sp-done { background: #EFF6FF; color: #1d4ed8; }
</style>
@endsection

@section('content')
<div class="program-page">
    <div class="header-card">
        <div class="route-display">
            <div class="route-icon">
                <i class="fas fa-bus"></i>
            </div>
            <div class="route-info">
                <h2>{{ $programme->gareDepart?->nom_gare ?? $programme->point_depart }} → {{ $programme->gareArrivee?->nom_gare ?? $programme->point_arrive }}</h2>
                <p>Départ le <span class="text-orange-600">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</span> à <span class="text-orange-600 font-black">{{ \Carbon\Carbon::parse($programme->heure_depart)->format('H:i') }}</span></p>
                <p class="text-[11px] uppercase tracking-widest mt-1">
                    {{ $programme->getPlacesReserveesForDate($date) }}/{{ $programme->getTotalSeats($date) }} Passagers réservés
                </p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('gare-espace.reservations.index', ['date_voyage' => $date, 'tab' => $tab]) }}" class="back-btn">
                <i class="fas fa-arrow-left"></i> Retour à la sélection
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white/60 p-6 rounded-3xl border border-white mb-8 shadow-sm backdrop-blur-md">
        <form action="{{ route('gare-espace.reservations.program', $programme->id) }}" method="GET" class="flex flex-wrap items-center gap-4">
            <input type="hidden" name="date" value="{{ $date }}">
            <input type="hidden" name="tab" value="{{ $tab }}">
            
            <div class="flex-1 min-w-[200px] relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="reference" value="{{ request('reference') }}" placeholder="Référence ou Passager..." class="w-full pl-12 pr-4 py-3 bg-white border border-gray-100 rounded-xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none font-medium transition-all">
            </div>

            <button type="submit" class="p-3 bg-orange-500 text-white rounded-xl shadow-lg shadow-orange-500/20 hover:bg-orange-600 transition-all">
                <i class="fas fa-search px-4"></i>
            </button>
        </form>
    </div>

    <div class="dash-card">
        <div class="overflow-x-auto">
            <table class="dash-table w-full">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Passager</th>
                        <th>Itinéraire</th>
                        <th class="text-center">Siège</th>
                        <th class="text-right">Montant</th>
                        <th class="text-center">Statut</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reservations as $reservation)
                        @if($tab === 'en-cours' && $reservation->statut === 'terminee')
                            @continue
                        @endif
                        @if($tab === 'terminees' && $reservation->statut !== 'terminee')
                            @continue
                        @endif
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td>
                            <span class="text-xs font-black text-orange-500 uppercase">{{ $reservation->reference }}</span>
                        </td>
                        <td>
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900">{{ $reservation->passager_nom }} {{ $reservation->passager_prenom }}</span>
                                <span class="text-[11px] text-gray-500">{{ $reservation->passager_telephone }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-gray-700 uppercase">{{ $reservation->programme->point_depart }}</span>
                                <i class="fas fa-arrow-right text-[8px] text-orange-500 my-0.5"></i>
                                <span class="text-[10px] font-black text-gray-700 uppercase">{{ $reservation->programme->point_arrive }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="px-3 py-1 bg-gray-100 rounded-lg font-black text-gray-700">#{{ $reservation->seat_number }}</span>
                        </td>
                        <td class="text-right">
                            <span class="font-black text-gray-900">{{ number_format($reservation->montant, 0, ',', ' ') }}</span>
                            <span class="text-[10px] text-gray-400 font-bold ml-1">FCFA</span>
                        </td>
                        <td class="text-center">
                             @if($reservation->statut == 'confirmee')
                                <span class="status-pill sp-success">Réservée</span>
                            @elseif($reservation->statut == 'terminee')
                                <span class="status-pill sp-done">Embarqué</span>
                            @elseif($reservation->statut == 'annulee')
                                <span class="status-pill bg-red-50 text-red-600">Annulée</span>
                            @else
                                <span class="status-pill sp-pending">{{ $reservation->statut }}</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <button onclick="showDetails({{ $reservation->id }})" class="w-10 h-10 border border-orange-100 text-orange-500 rounded-xl bg-orange-50 hover:bg-orange-500 hover:text-white transition-all">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-20 text-center text-gray-400 font-bold">
                            Aucune réservation trouvée pour ce programme.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($reservations->hasPages())
        <div class="p-6 bg-gray-50/50 border-t border-gray-100">
            {{ $reservations->links() }}
        </div>
        @endif
    </div>
</div>

@include('gare-espace.reservation._modal_details')

@endsection

@section('scripts')
<script>
    // Javascript logic is included via the modal partial
</script>
@endsection

