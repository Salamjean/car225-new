@extends('user.layouts.template')

@section('title', 'Modifier ma réservation')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">

    {{-- ===== EN-TÊTE ===== --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('user.reservation.group', $reservation->payment_transaction_id) }}"
           class="w-10 h-10 rounded-2xl bg-white border border-gray-100 shadow-sm flex items-center justify-center text-gray-400 hover:text-[#e94f1b] hover:border-[#e94f1b]/30 transition-all">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-[#1A1D1F] tracking-tight uppercase flex items-center gap-2">
                <i class="fas fa-pen text-[#e94f1b]"></i> Modifier ma réservation
            </h1>
            <p class="text-sm text-gray-400 font-medium">{{ $reservation->reference }}</p>
        </div>
    </div>

    {{-- ===== RÉSUMÉ BILLET ACTUEL ===== --}}
    <div class="bg-white rounded-[28px] border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500">
                <i class="fas fa-ticket-alt text-sm"></i>
            </div>
            <span class="text-xs font-black text-gray-600 uppercase tracking-widest">Billet actuel</span>
        </div>
        <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-base font-black text-[#1A1D1F]">{{ $reservation->programme->point_depart }}</span>
                        <i class="fas fa-arrow-right text-[#e94f1b] text-xs"></i>
                        <span class="text-base font-black text-[#1A1D1F]">{{ $reservation->programme->point_arrive }}</span>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-gray-400 font-medium">
                        <span><i class="far fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($reservation->date_voyage)->format('d/m/Y') }}</span>
                        <span><i class="far fa-clock mr-1"></i>{{ $currentTime ?? '—' }}</span>
                        <span><i class="fas fa-chair mr-1"></i>Place {{ $currentSeat ?? '—' }}</span>
                    </div>
                    @if($isRoundTrip && $returnDetails)
                    <div class="mt-2 flex items-center gap-3 text-xs text-orange-600 font-bold">
                        <span class="px-2 py-0.5 bg-orange-50 border border-orange-100 rounded-lg uppercase tracking-wide">Retour</span>
                        <span>{{ \Carbon\Carbon::parse($returnDetails['date'])->format('d/m/Y') }}</span>
                        @if($currentRetTime)<span>· {{ $currentRetTime }}</span>@endif
                        @if($currentRetSeat)<span>· Place {{ $currentRetSeat }}</span>@endif
                    </div>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-xl font-black text-[#1A1D1F]">{{ number_format($totalOldPrice, 0, ',', ' ') }}</p>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">FCFA</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== AVOIR & PÉNALITÉ ===== --}}
    <div class="bg-blue-50 border border-blue-100 rounded-[28px] p-6">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="flex-1">
                <p class="text-xs font-black text-blue-400 uppercase tracking-widest mb-3">Calcul de la valeur résiduelle</p>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-[10px] font-black text-blue-300 uppercase tracking-widest mb-1">Prix original</p>
                        <p class="text-base font-black text-blue-800">{{ number_format($totalOldPrice, 0, ',', ' ') }} <span class="text-xs font-bold">FCFA</span></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-red-400 uppercase tracking-widest mb-1">Pénalité</p>
                        <p class="text-base font-black text-red-600">- {{ number_format($penalty, 0, ',', ' ') }} <span class="text-xs font-bold">FCFA</span></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1">Avoir disponible</p>
                        <p class="text-base font-black text-blue-700">{{ number_format($residualValue, 0, ',', ' ') }} <span class="text-xs font-bold">FCFA</span></p>
                    </div>
                </div>
                <p class="text-[10px] text-blue-400 mt-3 font-medium flex items-center gap-1">
                    <i class="fas fa-clock"></i> {{ $penaltyInfo }}
                </p>
            </div>
        </div>
    </div>

    {{-- ===== FORMULAIRE NOUVEAU VOYAGE ===== --}}
    <div class="bg-white rounded-[28px] border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-[#e94f1b]/10 flex items-center justify-center text-[#e94f1b]">
                <i class="fas fa-exchange-alt text-sm"></i>
            </div>
            <span class="text-xs font-black text-gray-600 uppercase tracking-widest">Nouveau voyage</span>
        </div>
        <div class="p-6 space-y-6">

            {{-- Trajet --}}
            <div>
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Trajet</label>
                <select id="mod-route" class="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:ring-2 focus:ring-[#e94f1b] focus:bg-white transition-all {{ $isRoundTrip ? 'opacity-60 cursor-not-allowed' : '' }}"
                    {{ $isRoundTrip ? 'disabled' : '' }}>
                    @foreach($routes as $route)
                        <option value="{{ $route['id'] }}"
                            {{ $route['unique_key'] === $currentRouteKey ? 'selected' : '' }}
                            data-depart="{{ $route['depart'] }}"
                            data-arrive="{{ $route['arrive'] }}"
                            data-compagnie="{{ $route['compagnie_id'] }}"
                            data-prix="{{ $route['prix'] }}">
                            {{ $route['name'] }} — {{ $route['compagnie'] }}
                        </option>
                    @endforeach
                </select>
                @if($isRoundTrip)
                <p class="text-[10px] text-gray-400 mt-1 font-medium"><i class="fas fa-lock mr-1"></i>Le trajet est fixé pour les voyages aller-retour.</p>
                @endif
            </div>

            {{-- ===== ALLER ===== --}}
            <div class="p-5 bg-gray-50 rounded-2xl border border-gray-100 space-y-4">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 bg-[#e94f1b] text-white text-[10px] font-black rounded-lg uppercase tracking-wide">Aller</span>
                    <p class="text-sm font-black text-gray-700">{{ $reservation->programme->point_depart }} → {{ $reservation->programme->point_arrive }}</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Date</label>
                        <input type="date" id="mod-date" onkeydown="return false"
                            value="{{ $formattedDateAller }}"
                            min="{{ now()->format('Y-m-d') }}"
                            class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-2xl text-sm font-bold outline-none focus:ring-2 focus:ring-[#e94f1b] transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Heure de départ</label>
                        <select id="mod-time" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-2xl text-sm font-bold outline-none focus:ring-2 focus:ring-[#e94f1b] transition-all">
                            <option value="">Chargement...</option>
                        </select>
                    </div>
                </div>
                {{-- Seat picker button --}}
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Place sélectionnée</label>
                    <button type="button" id="aller-seat-btn" onclick="openSeatModal('aller')" disabled
                        class="w-full flex items-center justify-between px-5 py-4 bg-white border-2 border-dashed border-gray-200 rounded-2xl text-sm font-bold text-gray-400 hover:border-[#e94f1b] hover:text-[#e94f1b] transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:border-gray-200 disabled:hover:text-gray-400">
                        <span class="flex items-center gap-3">
                            <i class="fas fa-chair text-base"></i>
                            <span id="aller-seat-label">Sélectionnez d'abord une heure</span>
                        </span>
                        <i class="fas fa-chevron-right text-xs"></i>
                    </button>
                    <input type="hidden" id="mod-seat-input">
                </div>
            </div>

            {{-- ===== RETOUR (si aller-retour) ===== --}}
            @if($isRoundTrip)
            <div class="p-5 bg-orange-50 rounded-2xl border border-orange-100 space-y-4">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 bg-orange-500 text-white text-[10px] font-black rounded-lg uppercase tracking-wide">Retour</span>
                    <p class="text-sm font-black text-gray-700">{{ $reservation->programme->point_arrive }} → {{ $reservation->programme->point_depart }}</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Date retour</label>
                        <input type="date" id="mod-ret-date" onkeydown="return false"
                            value="{{ $returnDetails['date'] ?? '' }}"
                            min="{{ $formattedDateAller }}"
                            class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-2xl text-sm font-bold outline-none focus:ring-2 focus:ring-orange-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Heure retour</label>
                        <select id="mod-ret-time" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-2xl text-sm font-bold outline-none focus:ring-2 focus:ring-orange-400 transition-all">
                            <option value="">Chargement...</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Place sélectionnée — retour</label>
                    <button type="button" id="retour-seat-btn" onclick="openSeatModal('retour')" disabled
                        class="w-full flex items-center justify-between px-5 py-4 bg-white border-2 border-dashed border-orange-200 rounded-2xl text-sm font-bold text-gray-400 hover:border-orange-500 hover:text-orange-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:border-orange-200 disabled:hover:text-gray-400">
                        <span class="flex items-center gap-3">
                            <i class="fas fa-chair text-base"></i>
                            <span id="retour-seat-label">Sélectionnez d'abord une heure</span>
                        </span>
                        <i class="fas fa-chevron-right text-xs"></i>
                    </button>
                    <input type="hidden" id="mod-ret-seat-input">
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- ===== RÉCAPITULATIF COÛTS (dynamique) ===== --}}
    <div id="delta-box" class="hidden bg-[#1A1D1F] text-white rounded-[28px] p-6 shadow-xl shadow-gray-900/10">
        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Récapitulatif financier</p>
        <div class="space-y-3">
            <div class="flex justify-between items-center text-sm opacity-60">
                <span class="font-bold">Nouveau prix total</span>
                <span id="new-total-display" class="font-black">—</span>
            </div>
            <div class="flex justify-between items-center text-sm opacity-60">
                <span class="font-bold">Avoir après pénalité</span>
                <span id="residual-display" class="font-black">—</span>
            </div>
            <div class="pt-3 border-t border-white/10 flex justify-between items-center">
                <span id="delta-label" class="text-xs font-black uppercase tracking-widest text-[#e94f1b]">Total à payer</span>
                <span id="delta-amount" class="text-2xl font-black text-white">—</span>
            </div>
        </div>
        <p id="wallet-error" class="hidden mt-3 text-[10px] font-bold text-red-400 bg-red-400/10 px-3 py-2 rounded-xl text-center">
            <i class="fas fa-exclamation-triangle mr-1"></i> Solde insuffisant sur votre portefeuille ({{ number_format($userSolde, 0, ',', ' ') }} FCFA disponible)
        </p>
    </div>

    {{-- ===== BOUTON CONFIRMER ===== --}}
    <div class="pb-8">
        <button id="submit-btn" onclick="submitModification()" disabled
            class="w-full py-4 bg-[#1A1D1F] text-white font-black rounded-2xl uppercase tracking-widest text-sm shadow-lg shadow-gray-900/20 transition-all flex items-center justify-center gap-3 disabled:opacity-40 disabled:cursor-not-allowed">
            <i class="fas fa-check"></i>
            <span id="submit-label">Confirmez votre sélection ci-dessus</span>
        </button>
        <p class="text-center text-xs text-gray-400 mt-3 font-medium">
            <i class="fas fa-shield-alt mr-1"></i> La modification est soumise à nos conditions de remboursement.
        </p>
    </div>

</div>

{{-- ===== MODAL SÉLECTION DE PLACE (style bus comme create) ===== --}}
<div id="seat-modal" class="fixed inset-0 z-[9999] hidden">
    {{-- Overlay --}}
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeSeatModal()"></div>
    {{-- Panneau --}}
    <div class="absolute inset-0 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="relative bg-white w-full sm:max-w-lg rounded-t-[32px] sm:rounded-[32px] shadow-2xl flex flex-col max-h-[92vh]">

            {{-- Header orange (style create) --}}
            <div class="bg-gradient-to-r from-[#e94f1b] to-[#f97316] px-6 py-5 rounded-t-[32px] flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-bus text-white text-base"></i>
                        </div>
                        <div>
                            <p class="text-white font-black text-base leading-tight" id="seat-modal-title">Choisir une place</p>
                            <p class="text-white/70 text-xs font-medium" id="seat-modal-subtitle">Plan du véhicule</p>
                        </div>
                    </div>
                    <button onclick="closeSeatModal()" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center text-white transition-all">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>

            {{-- Légende --}}
            <div class="flex items-center justify-center gap-4 py-3 border-b border-gray-100 flex-shrink-0 bg-gray-50/50 flex-wrap px-4">
                <div class="flex items-center gap-1.5">
                    <div class="w-5 h-5 rounded-md" style="background: linear-gradient(135deg,#e94f1b,#e89116);"></div>
                    <span class="text-[11px] font-bold text-gray-500">Gauche</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-5 h-5 rounded-md" style="background: linear-gradient(135deg,#10b981,#059669);"></div>
                    <span class="text-[11px] font-bold text-gray-500">Droite</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-5 h-5 rounded-md" style="background: linear-gradient(135deg,#4f46e5,#6366f1);"></div>
                    <span class="text-[11px] font-bold text-gray-500">Sélectionnée</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-5 h-5 rounded-md bg-gray-200"></div>
                    <span class="text-[11px] font-bold text-gray-500">Occupée</span>
                </div>
            </div>

            {{-- Corps : plan bus --}}
            <div class="overflow-y-auto flex-1 p-4" id="seat-modal-body">
                <div class="flex items-center justify-center h-24">
                    <div class="animate-spin w-6 h-6 border-2 border-[#e94f1b] border-t-transparent rounded-full"></div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex-shrink-0 px-6 py-4 border-t border-gray-100 bg-white rounded-b-[32px]">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Place choisie</span>
                    <span id="modal-selected-seat-display" class="text-sm font-black text-gray-500">Aucune</span>
                </div>
                <button id="modal-confirm-btn" onclick="confirmSeatSelection()" disabled
                    class="w-full py-3.5 bg-[#e94f1b] text-white font-black rounded-2xl text-sm uppercase tracking-widest transition-all disabled:opacity-40 disabled:cursor-not-allowed hover:bg-[#d44518]">
                    <i class="fas fa-check mr-2"></i> Confirmer cette place
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const MOD_RES_ID       = {{ $reservation->id }};
const MOD_IS_RT        = {{ $isRoundTrip ? 'true' : 'false' }};
const MOD_CURRENT_DATE = "{{ $formattedDateAller }}";
const MOD_CURRENT_TIME = "{{ $currentTime ?? '' }}";
const MOD_CURRENT_SEAT = {{ $currentSeat ?? 'null' }};
const MOD_RET_DATE     = "{{ $returnDetails['date'] ?? '' }}";
const MOD_RET_TIME     = "{{ $currentRetTime ?? '' }}";
const MOD_RET_SEAT     = {{ $currentRetSeat ?? 'null' }};
const CSRF_TOKEN       = "{{ csrf_token() }}";
const RETURN_URL       = "{{ route('user.reservation.group', $reservation->payment_transaction_id) }}";

/* ── état global du modal ── */
let seatModalType = 'aller';   // 'aller' | 'retour'
let seatModalData = null;      // seats + vehicule réponse API
let seatModalSelected = null;  // numéro temporaire sélectionné dans le modal

/* ─── config rangées (identique create.blade) ─── */
const typeRangeConfig = {
    '2x2': { left: 2, right: 2 },
    '2x3': { left: 2, right: 3 },
    '2x4': { left: 2, right: 4 },
    'Gamme Prestige': { left: 2, right: 2 },
    'Gamme Standard': { left: 2, right: 3 },
};

/* ─── génère le plan bus ─── */
function generateBusLayout(seats, vehicule, preSelectedSeat = null) {
    const typeRange = vehicule?.type_range || '2x2';
    let cfg = typeRangeConfig[typeRange] || { left: 2, right: 2 };
    const placesLeft  = cfg.left;
    const placesRight = cfg.right;
    const perRow      = placesLeft + placesRight;
    const totalSeats  = vehicule?.nombre_place || seats.length;
    const rowCount    = Math.ceil(totalSeats / perRow);

    const seatMap = {};
    seats.forEach(s => seatMap[s.number] = s.available);

    let html = `<div style="background:white;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">`;
    // en-tête
    html += `<div style="display:grid;grid-template-columns:70px 1fr 50px 1fr;gap:8px;padding:12px 16px;background:#f9fafb;border-bottom:2px solid #e5e7eb;">
        <div style="text-align:center;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Rangée</div>
        <div style="text-align:center;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Gauche</div>
        <div></div>
        <div style="text-align:center;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Droite</div>
    </div>`;
    html += `<div style="max-height:420px;overflow-y:auto;">`;

    let num = 1;
    for (let row = 1; row <= rowCount; row++) {
        const remaining      = totalSeats - (num - 1);
        const seatsThisRow   = Math.min(perRow, remaining);
        const leftCount      = Math.min(placesLeft, seatsThisRow);
        const rightCount     = Math.min(placesRight, seatsThisRow - leftCount);
        const borderBottom   = row < rowCount ? 'border-bottom:1px solid #f3f4f6;' : '';

        html += `<div style="display:grid;grid-template-columns:70px 1fr 50px 1fr;gap:8px;padding:14px 16px;align-items:center;${borderBottom}">`;
        // numéro rangée
        html += `<div style="text-align:center;font-size:12px;font-weight:600;color:#9ca3af;">Rg ${row}</div>`;
        // gauche
        html += `<div style="display:flex;justify-content:center;gap:6px;flex-wrap:wrap;">`;
        for (let i = 0; i < leftCount; i++) {
            const n = num + i;
            const available = seatMap[n] !== undefined ? seatMap[n] : true;
            const isSelected = preSelectedSeat && n == preSelectedSeat;
            html += buildSeat(n, available, isSelected, 'left');
        }
        html += `</div>`;
        // allée
        html += `<div style="display:flex;justify-content:center;"><div style="width:8px;height:36px;background:#d1d5db;border-radius:4px;"></div></div>`;
        // droite
        html += `<div style="display:flex;justify-content:center;gap:6px;flex-wrap:wrap;">`;
        for (let i = 0; i < rightCount; i++) {
            const n = num + leftCount + i;
            const available = seatMap[n] !== undefined ? seatMap[n] : true;
            const isSelected = preSelectedSeat && n == preSelectedSeat;
            html += buildSeat(n, available, isSelected, 'right');
        }
        html += `</div>`;
        html += `</div>`;
        num += seatsThisRow;
    }
    html += `</div></div>`;
    return html;
}

function buildSeat(n, available, isSelected, side) {
    const isLeft = side === 'left';
    if (isSelected) {
        return `<div onclick="selectModalSeat(${n})" data-seat="${n}" class="modal-seat"
            style="width:44px;height:44px;border-radius:8px;display:flex;align-items:center;justify-content:center;
            color:white;font-weight:700;font-size:14px;cursor:pointer;box-shadow:0 0 0 3px rgba(99,102,241,.45);
            background:linear-gradient(135deg,#4f46e5,#6366f1);">${n}</div>`;
    }
    if (!available) {
        return `<div data-seat="${n}" class="modal-seat"
            style="width:44px;height:44px;border-radius:8px;display:flex;align-items:center;justify-content:center;
            background:#f3f4f6;color:#d1d5db;font-weight:700;font-size:14px;cursor:not-allowed;">${n}</div>`;
    }
    const grad = isLeft
        ? 'linear-gradient(135deg,#e94f1b,#e89116)'
        : 'linear-gradient(135deg,#10b981,#059669)';
    return `<div onclick="selectModalSeat(${n})" data-seat="${n}" class="modal-seat"
        style="width:44px;height:44px;border-radius:8px;display:flex;align-items:center;justify-content:center;
        color:white;font-weight:700;font-size:14px;cursor:pointer;background:${grad};
        box-shadow:0 2px 4px rgba(0,0,0,.15);transition:transform .1s;"
        onmouseover="this.style.transform='scale(1.1)'"
        onmouseout="this.style.transform='scale(1)'">${n}</div>`;
}

/* ─── sélection dans le modal ─── */
window.selectModalSeat = function(n) {
    seatModalSelected = n;
    // Reconstruire avec nouvelle sélection
    const pre = seatModalData?.preSelected; // garder ref
    document.getElementById('seat-modal-body').innerHTML =
        generateBusLayout(seatModalData.seats, seatModalData.vehicule, n);
    document.getElementById('modal-selected-seat-display').textContent = `Place n° ${n}`;
    document.getElementById('modal-confirm-btn').disabled = false;
};

/* ─── ouvrir modal ─── */
window.openSeatModal = function(type) {
    if (!seatModalData || seatModalData.type !== type) return; // data pas encore chargée
    seatModalType = type;
    seatModalSelected = null;

    const currentSeat = type === 'retour'
        ? parseInt($('#mod-ret-seat-input').val()) || null
        : parseInt($('#mod-seat-input').val()) || null;

    const isRetour = type === 'retour';
    document.getElementById('seat-modal-title').textContent = isRetour ? 'Place — Voyage Retour' : 'Place — Voyage Aller';
    document.getElementById('seat-modal-subtitle').textContent =
        isRetour ? 'Choisissez votre place pour le retour' : 'Choisissez votre place pour l\'aller';
    document.getElementById('modal-selected-seat-display').textContent = currentSeat ? `Place n° ${currentSeat}` : 'Aucune';
    document.getElementById('modal-confirm-btn').disabled = !currentSeat;

    document.getElementById('seat-modal-body').innerHTML =
        generateBusLayout(seatModalData.seats, seatModalData.vehicule, currentSeat);

    if (currentSeat) seatModalSelected = currentSeat;

    document.getElementById('seat-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
};

window.closeSeatModal = function() {
    document.getElementById('seat-modal').classList.add('hidden');
    document.body.style.overflow = '';
};

window.confirmSeatSelection = function() {
    if (!seatModalSelected) return;
    const type = seatModalType;
    if (type === 'retour') {
        $('#mod-ret-seat-input').val(seatModalSelected);
        document.getElementById('retour-seat-label').textContent = `Place n° ${seatModalSelected} sélectionnée ✓`;
        document.getElementById('retour-seat-btn').classList.add('border-orange-400', 'text-orange-600');
        document.getElementById('retour-seat-btn').classList.remove('border-dashed', 'text-gray-400');
    } else {
        $('#mod-seat-input').val(seatModalSelected);
        document.getElementById('aller-seat-label').textContent = `Place n° ${seatModalSelected} sélectionnée ✓`;
        document.getElementById('aller-seat-btn').classList.add('border-[#e94f1b]', 'text-[#e94f1b]');
        document.getElementById('aller-seat-btn').classList.remove('border-dashed', 'text-gray-400');
    }
    closeSeatModal();
    updateDeltaBox();
};

/* ─── load times ─── */
async function loadTimes(type, date, preSelectedTime = null) {
    const routeOpt  = $('#mod-route option:selected');
    const depart    = type === 'retour' ? routeOpt.data('arrive') : routeOpt.data('depart');
    const arrive    = type === 'retour' ? routeOpt.data('depart') : routeOpt.data('arrive');
    const compagnie = routeOpt.data('compagnie');
    const sel       = type === 'retour' ? '#mod-ret-time' : '#mod-time';
    const btn       = type === 'retour' ? 'retour-seat-btn' : 'aller-seat-btn';
    const lbl       = type === 'retour' ? 'retour-seat-label' : 'aller-seat-label';
    const inp       = type === 'retour' ? '#mod-ret-seat-input' : '#mod-seat-input';

    $(sel).html('<option value="">Chargement...</option>').prop('disabled', true);
    $(inp).val('');
    document.getElementById(btn).disabled = true;
    document.getElementById(lbl).textContent = 'Sélectionnez d\'abord une heure';
    document.getElementById(btn).className = document.getElementById(btn).className
        .replace('border-[#e94f1b] text-[#e94f1b]', '')
        .replace('border-orange-400 text-orange-600', '');
    if (type !== 'retour') seatModalData = null; else seatModalData = null;
    updateDeltaBox();

    try {
        const res = await $.get('/user/booking/api/route-schedules', { point_depart: depart, point_arrive: arrive, compagnie_id: compagnie, date });
        if (res.success && res.schedules.length > 0) {
            let opts = '<option value="">— Choisir une heure —</option>';
            res.schedules.forEach(sch => {
                const display     = (sch.heure_depart || '00:00').substring(0, 5);
                const isSelected  = preSelectedTime && display === preSelectedTime.substring(0, 5) ? 'selected' : '';
                opts += `<option value="${sch.heure_depart}" ${isSelected} data-prog-id="${sch.id}" data-prix="${sch.montant_billet}">${display}</option>`;
            });
            $(sel).html(opts).prop('disabled', false);
        } else {
            $(sel).html('<option value="">Aucun départ ce jour</option>');
        }
    } catch(e) {
        $(sel).html('<option value="">Erreur</option>');
    }
}

/* ─── load seats (stocke data + active bouton) ─── */
async function loadSeats(type, progId, date, time, preSelectedSeat = null) {
    const btn = type === 'retour' ? 'retour-seat-btn' : 'aller-seat-btn';
    const lbl = type === 'retour' ? 'retour-seat-label' : 'aller-seat-label';
    const inp = type === 'retour' ? '#mod-ret-seat-input' : '#mod-seat-input';

    document.getElementById(btn).disabled = true;
    document.getElementById(lbl).textContent = 'Chargement...';
    $(inp).val('');
    if (type === 'retour') seatModalData = null; else seatModalData = null;
    updateDeltaBox();

    try {
        const res = await $.get(`/user/booking/programmes/${progId}/seats`, { date, heure: time });
        if (!res.success) { document.getElementById(lbl).textContent = 'Erreur — réessayez'; return; }

        // stocker pour le modal
        const data = { type, seats: res.seats, vehicule: res.vehicule || null, preSelected: preSelectedSeat };
        if (type === 'retour') {
            seatModalData = data; // on écrase, mais on sépare avec la variable globale ci-dessous
            window._seatDataRetour = data;
        } else {
            window._seatDataAller = data;
            seatModalData = data;
        }

        document.getElementById(btn).disabled = false;

        if (preSelectedSeat) {
            document.getElementById(lbl).textContent = `Place n° ${preSelectedSeat} sélectionnée ✓`;
            $(inp).val(preSelectedSeat);
            updateDeltaBox();
        } else {
            document.getElementById(lbl).textContent = 'Cliquez pour choisir votre place';
        }
    } catch(e) {
        document.getElementById(lbl).textContent = 'Erreur chargement';
    }
}

/* Quand on ouvre le modal, assure qu'on utilise les bonnes données */
const _origOpenSeatModal = window.openSeatModal;
window.openSeatModal = function(type) {
    const data = type === 'retour' ? window._seatDataRetour : window._seatDataAller;
    if (!data) return;
    seatModalData = data;
    seatModalType = type;
    seatModalSelected = null;

    const currentSeat = parseInt(type === 'retour' ? $('#mod-ret-seat-input').val() : $('#mod-seat-input').val()) || null;

    const isRetour = type === 'retour';
    document.getElementById('seat-modal-title').textContent    = isRetour ? 'Place — Voyage Retour' : 'Place — Voyage Aller';
    document.getElementById('seat-modal-subtitle').textContent = isRetour ? 'Choisissez votre place pour le retour' : 'Choisissez votre place pour l\'aller';
    document.getElementById('modal-selected-seat-display').textContent = currentSeat ? `Place n° ${currentSeat}` : 'Aucune';
    document.getElementById('modal-confirm-btn').disabled = !currentSeat;

    document.getElementById('seat-modal-body').innerHTML = generateBusLayout(data.seats, data.vehicule, currentSeat);
    if (currentSeat) seatModalSelected = currentSeat;

    document.getElementById('seat-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
};

/* ─── delta / total ─── */
async function updateDeltaBox() {
    const progIdAller = $('#mod-time option:selected').data('prog-id');
    const seatAller   = $('#mod-seat-input').val();
    $('#submit-btn').prop('disabled', true);
    $('#submit-label').text('Confirmez votre sélection ci-dessus');

    if (!progIdAller || !seatAller) { $('#delta-box').addClass('hidden'); return; }
    if (MOD_IS_RT) {
        if (!$('#mod-ret-time option:selected').data('prog-id') || !$('#mod-ret-seat-input').val()) {
            $('#delta-box').addClass('hidden'); return;
        }
    }

    const payload = {
        new_programme_id: progIdAller,
        new_date_aller:   $('#mod-date').val(),
        _token: CSRF_TOKEN
    };
    if (MOD_IS_RT) {
        payload.new_return_programme_id = $('#mod-ret-time option:selected').data('prog-id');
        payload.new_return_date = $('#mod-ret-date').val();
    }

    try {
        const res = await $.post(`/user/booking/reservations/${MOD_RES_ID}/calculate-delta`, payload);
        $('#delta-box').removeClass('hidden');
        $('#new-total-display').text(Number(res.new_total).toLocaleString('fr-FR') + ' FCFA');
        $('#residual-display').text(Number(res.residual_value).toLocaleString('fr-FR') + ' FCFA');
        $('#delta-amount').text(Number(res.delta).toLocaleString('fr-FR') + ' FCFA');

        if (res.action === 'pay') {
            $('#delta-label').text('Reste à payer');
            $('#delta-amount').removeClass('text-green-400').addClass('text-[#e94f1b]');
            if (!res.can_afford) {
                $('#wallet-error').removeClass('hidden');
                $('#submit-btn').prop('disabled', true);
                $('#submit-label').text('Solde insuffisant');
            } else {
                $('#wallet-error').addClass('hidden');
                $('#submit-btn').prop('disabled', false);
                $('#submit-label').text('Confirmer la modification');
            }
        } else {
            $('#delta-label').text('Crédit à rembourser');
            $('#delta-amount').removeClass('text-[#e94f1b]').addClass('text-green-400');
            $('#wallet-error').addClass('hidden');
            $('#submit-btn').prop('disabled', false);
            $('#submit-label').text('Confirmer la modification');
        }
    } catch(e) { $('#delta-box').addClass('hidden'); }
}

/* ─── submit ─── */
async function submitModification() {
    const seatAller = $('#mod-seat-input').val();
    const progAller = $('#mod-time option:selected').data('prog-id');
    if (!seatAller || !progAller) {
        Swal.fire({ icon: 'warning', title: 'Incomplet', text: 'Veuillez sélectionner votre voyage aller complet.', confirmButtonColor: '#e94f1b', customClass: { popup: 'rounded-[28px]' } });
        return;
    }

    const payload = {
        programme_id: progAller,
        date_voyage:  $('#mod-date').val(),
        heure_depart: $('#mod-time').val(),
        seat_number:  seatAller,
        _token: CSRF_TOKEN
    };

    if (MOD_IS_RT) {
        payload.return_programme_id = $('#mod-ret-time option:selected').data('prog-id');
        payload.return_date_voyage  = $('#mod-ret-date').val();
        payload.return_heure_depart = $('#mod-ret-time').val();
        payload.return_seat_number  = $('#mod-ret-seat-input').val();
        if (!payload.return_seat_number || !payload.return_programme_id) {
            Swal.fire({ icon: 'warning', title: 'Incomplet', text: 'Veuillez aussi sélectionner votre voyage retour complet.', confirmButtonColor: '#e94f1b', customClass: { popup: 'rounded-[28px]' } });
            return;
        }
    }

    const confirmed = await Swal.fire({
        title: '<span class="text-lg font-black uppercase tracking-tight">Confirmer la modification ?</span>',
        html: '<p class="text-sm text-gray-500">Cette action remplacera votre réservation actuelle. La pénalité s\'applique.</p>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check mr-2"></i> Oui, confirmer',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#1A1D1F',
        cancelButtonColor: '#f3f4f6',
        customClass: { popup: 'rounded-[28px]', confirmButton: 'rounded-xl px-6 py-3 font-black uppercase text-xs tracking-widest', cancelButton: 'rounded-xl px-6 py-3 font-black text-gray-800 text-xs' }
    });
    if (!confirmed.isConfirmed) return;

    $('#submit-btn').prop('disabled', true).html('<div class="animate-spin w-5 h-5 border-2 border-white border-t-transparent rounded-full mx-auto"></div>');

    try {
        const res = await $.post(`/user/booking/reservations/${MOD_RES_ID}/modify`, payload);
        if (!res.success) {
            Swal.fire({ icon: 'error', title: 'Erreur', text: res.message || 'Erreur lors de la modification.', confirmButtonColor: '#e94f1b', customClass: { popup: 'rounded-[28px]' } });
            $('#submit-btn').prop('disabled', false).html('<i class="fas fa-check mr-3"></i><span>Confirmer la modification</span>');
            return;
        }
        await Swal.fire({
            icon: 'success',
            title: '<span class="text-lg font-black text-green-600 uppercase">Modification réussie !</span>',
            text: res.message || 'Votre réservation a bien été mise à jour.',
            confirmButtonText: 'Voir mes billets',
            confirmButtonColor: '#22c55e',
            customClass: { popup: 'rounded-[28px]', confirmButton: 'rounded-xl px-8 py-3 font-black uppercase tracking-widest text-xs' }
        });
        window.location.href = RETURN_URL;
    } catch(e) {
        Swal.fire({ icon: 'error', title: 'Erreur', text: e.responseJSON?.message || 'Erreur technique.', confirmButtonColor: '#e94f1b', customClass: { popup: 'rounded-[28px]' } });
        $('#submit-btn').prop('disabled', false).html('<i class="fas fa-check mr-3"></i><span>Confirmer la modification</span>');
    }
}

/* ─── events ─── */
$(function() {
    $('#mod-route').on('change', function() {
        $('#mod-time, #mod-ret-time').html('<option>Heure requise</option>').prop('disabled', true);
        $('#mod-seat-input, #mod-ret-seat-input').val('');
        $('#delta-box').addClass('hidden');
        document.getElementById('aller-seat-btn').disabled = true;
        document.getElementById('aller-seat-label').textContent = 'Sélectionnez d\'abord une heure';
        if (MOD_IS_RT) {
            document.getElementById('retour-seat-btn').disabled = true;
            document.getElementById('retour-seat-label').textContent = 'Sélectionnez d\'abord une heure';
        }
        window._seatDataAller = null;
        window._seatDataRetour = null;
        const date = $('#mod-date').val();
        if (date) loadTimes('aller', date);
        if (MOD_IS_RT) { const d = $('#mod-ret-date').val(); if (d) loadTimes('retour', d); }
    });

    $('#mod-date').on('change', function() {
        const val = $(this).val();
        loadTimes('aller', val);
        if (MOD_IS_RT) {
            $('#mod-ret-date').attr('min', val);
            if ($('#mod-ret-date').val() < val) { $('#mod-ret-date').val(val); loadTimes('retour', val); }
        }
    });
    $('#mod-ret-date').on('change', function() { loadTimes('retour', $(this).val()); });

    $('#mod-time').on('change', function() {
        const progId = $(this).find(':selected').data('prog-id');
        if (progId) loadSeats('aller', progId, $('#mod-date').val(), $(this).val());
    });
    $('#mod-ret-time').on('change', function() {
        const progId = $(this).find(':selected').data('prog-id');
        if (progId) loadSeats('retour', progId, $('#mod-ret-date').val(), $(this).val());
    });

    /* Escape key */
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSeatModal(); });

    /* Preload */
    if (MOD_CURRENT_DATE) {
        loadTimes('aller', MOD_CURRENT_DATE, MOD_CURRENT_TIME).then(() => {
            const progId = $('#mod-time option:selected').data('prog-id');
            if (progId) loadSeats('aller', progId, MOD_CURRENT_DATE, $('#mod-time').val(), MOD_CURRENT_SEAT);
        });
    }
    if (MOD_IS_RT && MOD_RET_DATE) {
        loadTimes('retour', MOD_RET_DATE, MOD_RET_TIME).then(() => {
            const progId = $('#mod-ret-time option:selected').data('prog-id');
            if (progId) loadSeats('retour', progId, MOD_RET_DATE, $('#mod-ret-time').val(), MOD_RET_SEAT);
        });
    }
});
</script>
@endpush

@if(session('error'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({ icon: 'error', title: 'Modification impossible', text: "{{ session('error') }}", confirmButtonColor: '#e94f1b', customClass: { popup: 'rounded-[28px]' } });
});
</script>
@endif
@endsection
