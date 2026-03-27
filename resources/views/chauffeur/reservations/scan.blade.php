@extends('chauffeur.layouts.template')

@section('title', 'Scanner un QR Code')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="width: 100%;">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-orange-100 flex items-center justify-center">
                        <i class="fas fa-qrcode text-orange-600"></i>
                    </div>
                    Scanner les billets
                </h2>
                <p class="text-gray-500 text-sm mt-1">Scannez ou saisissez la référence d'une réservation</p>
            </div>
            <a href="{{ route('chauffeur.dashboard') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition text-sm font-medium shadow-sm">
                <i class="fas fa-arrow-left text-xs"></i>
                Retour
            </a>
        </div>

        @if($voyageActif)
        {{-- Voyage actif info --}}
        <div class="bg-gradient-to-r from-orange-600 to-red-600 rounded-2xl p-4 text-white shadow-lg mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-bus text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-white/70 uppercase font-bold tracking-wider">Voyage actif aujourd'hui</p>
                    <p class="font-bold text-lg">
                        {{ $voyageActif->programme->point_depart }} → {{ $voyageActif->programme->point_arrive }}
                    </p>
                    <p class="text-xs text-white/80">
                        {{ \Carbon\Carbon::parse($voyageActif->programme->heure_depart)->format('H:i') }}
                        &nbsp;|&nbsp;
                        {{ $voyageActif->vehicule->immatriculation ?? 'N/A' }}
                    </p>
                </div>
            </div>
        </div>
        @else
        {{-- Aucun voyage --}}
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-6 flex items-start gap-4">
            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-amber-500"></i>
            </div>
            <div>
                <p class="font-bold text-amber-800">Aucun voyage actif aujourd'hui</p>
                <p class="text-sm text-amber-600 mt-0.5">Vous devez avoir un voyage assigné pour scanner des billets. Contactez votre responsable.</p>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Zone de scan --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-search text-orange-500"></i>
                    Rechercher un billet
                </h3>

                <div class="flex gap-2 mb-4">
                    <input type="text"
                           id="referenceInput"
                           class="flex-1 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-transparent"
                           placeholder="RES-20260225-XXXXXX-1"
                           {{ !$voyageActif ? 'disabled' : '' }}>
                    <button id="searchBtn"
                            class="px-5 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-medium text-sm transition flex items-center gap-2 {{ !$voyageActif ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ !$voyageActif ? 'disabled' : '' }}>
                        <i class="fas fa-search text-xs"></i>
                        Chercher
                    </button>
                </div>

                {{-- Scan caméra QR --}}
                <div class="mb-4">
                    <button id="toggleCameraBtn"
                            class="w-full flex items-center justify-center gap-2 py-3 border-2 border-dashed border-orange-200 text-orange-600 rounded-xl hover:bg-orange-50 transition text-sm font-medium {{ !$voyageActif ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ !$voyageActif ? 'disabled' : '' }}>
                        <i class="fas fa-camera"></i>
                        Scanner avec la caméra
                    </button>
                    <div id="cameraContainer" class="hidden mt-3">
                        <div class="relative rounded-xl overflow-hidden bg-black" style="height: 450px;">
                            <div id="qr-reader" style="width: 100%; height: 100%;"></div>
                        </div>
                        <button id="stopCameraBtn" class="mt-2 w-full text-center text-xs text-gray-400 hover:text-red-500 transition py-2">
                            <i class="fas fa-times-circle mr-1"></i>Fermer la caméra
                        </button>
                    </div>
                </div>

                {{-- Résultat --}}
                <div id="resultArea" class="hidden">
                    {{-- Carte résultat --}}
                    <div id="resultCard" class="border border-gray-100 rounded-xl overflow-hidden">
                        {{-- Contenu injecté par JS --}}
                    </div>
                </div>

                <div id="noResult" class="hidden text-center py-6">
                    <i class="fas fa-search-minus text-4xl text-red-300"></i>
                    <p class="text-red-500 font-medium mt-2" id="noResultMsg">Aucune réservation trouvée</p>
                </div>

                <div id="initialState" class="text-center py-6">
                    <i class="fas fa-ticket-alt text-4xl text-gray-200"></i>
                    <p class="text-gray-400 text-sm mt-2">Entrez une référence ou utilisez la caméra</p>
                </div>
            </div>

            {{-- Derniers scans --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-history text-orange-500"></i>
                    Billets scannés aujourd'hui
                    <span class="ml-auto bg-orange-100 text-orange-600 text-xs font-bold px-2 py-0.5 rounded-full">
                        {{ $derniersScans->count() }}
                    </span>
                </h3>

                @if($derniersScans->count() > 0)
                <div class="space-y-3" id="scanHistoryList">
                    @foreach($derniersScans as $scan)
                    <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-100 rounded-xl">
                        <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center text-green-600 font-bold text-sm flex-shrink-0">
                            {{ $scan->seat_number }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 text-sm truncate">
                                {{ $scan->passager_prenom }} {{ $scan->passager_nom }}
                            </p>
                            <p class="text-xs text-gray-500">
                                <span class="text-orange-500 font-mono">{{ $scan->reference }}</span>
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="inline-flex items-center gap-1 text-xs text-green-600 font-bold">
                                <i class="fas fa-check-circle"></i>
                                {{ \Carbon\Carbon::parse($scan->embarquement_scanned_at)->format('H:i') }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-4xl text-gray-200"></i>
                    <p class="text-gray-400 text-sm mt-2">Aucun scan effectué aujourd'hui</p>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

{{-- Modal de confirmation --}}
<div id="confirmModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="bg-gradient-to-r from-orange-600 to-red-600 p-5 text-white">
            <div class="flex items-center justify-between">
                <h4 class="font-bold text-lg flex items-center gap-2">
                    <i class="fas fa-user-check"></i>
                    Confirmer l'embarquement
                </h4>
                <button id="closeModalBtn" class="text-white/70 hover:text-white transition">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
        <div class="p-6" id="modalBody">
            {{-- Contenu injecté --}}
        </div>
        <div class="border-t border-gray-100 p-4 flex gap-3">
            <button id="cancelBtn" class="flex-1 py-3 border border-gray-200 rounded-xl text-gray-600 font-medium hover:bg-gray-50 transition text-sm">
                Annuler
            </button>
            <button id="confirmBtn" class="flex-1 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-bold transition text-sm flex items-center justify-center gap-2">
                <i class="fas fa-check"></i>
                Valider l'embarquement
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const referenceInput = document.getElementById('referenceInput');
    const searchBtn      = document.getElementById('searchBtn');
    const resultArea     = document.getElementById('resultArea');
    const resultCard     = document.getElementById('resultCard');
    const noResult       = document.getElementById('noResult');
    const noResultMsg    = document.getElementById('noResultMsg');
    const initialState   = document.getElementById('initialState');
    const confirmModal   = document.getElementById('confirmModal');
    const modalBody      = document.getElementById('modalBody');
    const confirmBtn     = document.getElementById('confirmBtn');
    const cancelBtn      = document.getElementById('cancelBtn');
    const closeModalBtn  = document.getElementById('closeModalBtn');
    const toggleCameraBtn = document.getElementById('toggleCameraBtn');
    const stopCameraBtn  = document.getElementById('stopCameraBtn');
    const cameraContainer = document.getElementById('cameraContainer');

    let currentReservation = null;
    let html5QrCode = null;
    let isScanning = false;

    // ========================
    // Recherche
    // ========================
    searchBtn && searchBtn.addEventListener('click', doSearch);
    referenceInput && referenceInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') doSearch();
    });

    function doSearch() {
        const ref = referenceInput.value.trim();
        if (!ref) { referenceInput.focus(); return; }

        setLoading(true);
        hideAll();

        fetch('{{ route("chauffeur.reservations.search") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ reference: ref })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showResult(data.reservation);
            } else {
                showError(data.message || 'Réservation introuvable.');
            }
        })
        .catch(() => showError('Erreur réseau. Réessayez.'))
        .finally(() => setLoading(false));
    }

    function setLoading(state) {
        if (!searchBtn) return;
        searchBtn.disabled = state;
        searchBtn.innerHTML = state
            ? '<i class="fas fa-spinner fa-spin text-xs"></i> Recherche...'
            : '<i class="fas fa-search text-xs"></i> Chercher';
    }

    function hideAll() {
        resultArea.classList.add('hidden');
        noResult.classList.add('hidden');
        initialState.classList.add('hidden');
    }

    function showError(msg) {
        noResultMsg.textContent = msg;
        noResult.classList.remove('hidden');
    }

    function showResult(r) {
        currentReservation = r;

        const typeBadge = r.is_aller_retour
            ? `<span class="inline-block px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">${r.type_scan}</span>`
            : `<span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-bold rounded-full">Aller simple</span>`;

        resultCard.innerHTML = `
            <div class="bg-gradient-to-r from-orange-500 to-red-500 p-4 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs text-white/70 uppercase font-bold tracking-wider">Réservation</p>
                        <p class="font-mono font-bold">${r.reference}</p>
                    </div>
                    ${typeBadge}
                </div>
            </div>
            <div class="p-4 space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600">
                        <i class="fas fa-user text-lg"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900">${r.passager_nom_complet}</p>
                        <p class="text-sm text-gray-500">${r.passager_telephone || ''}</p>
                    </div>
                    <div class="ml-auto text-right">
                        <p class="text-xs text-gray-400">Siège</p>
                        <p class="text-2xl font-black text-orange-600">${r.seat_number}</p>
                    </div>
                </div>
                <div class="border-t border-gray-100 pt-3 grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <p class="text-xs text-gray-400">Départ</p>
                        <p class="font-semibold text-gray-800">${r.gare_depart || ''}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Arrivée</p>
                        <p class="font-semibold text-gray-800">${r.gare_arrivee || ''}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Heure départ</p>
                        <p class="font-semibold text-gray-800">${r.heure_depart || '--'}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Montant</p>
                        <p class="font-semibold text-green-600">${r.montant}</p>
                    </div>
                </div>
                <button id="openConfirmBtn"
                    class="w-full mt-2 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-bold transition flex items-center justify-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    Valider l'embarquement
                </button>
            </div>
        `;

        resultArea.classList.remove('hidden');

        document.getElementById('openConfirmBtn').addEventListener('click', openConfirmModal);
    }

    // ========================
    // Modal de confirmation
    // ========================
    function openConfirmModal() {
        if (!currentReservation) return;
        const r = currentReservation;
        modalBody.innerHTML = `
            <div class="text-center mb-4">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-user text-orange-500 text-2xl"></i>
                </div>
                <p class="text-xl font-bold text-gray-900">${r.passager_nom_complet}</p>
                <p class="text-gray-500 text-sm">${r.passager_telephone || ''}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 text-sm space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-500">Référence</span>
                    <span class="font-mono font-bold text-orange-600">${r.reference}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Siège</span>
                    <span class="font-bold text-2xl text-gray-900">${r.seat_number}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Trajet</span>
                    <span class="font-semibold text-gray-800">${r.trajet}</span>
                </div>
                ${r.is_aller_retour ? `<div class="flex justify-between"><span class="text-gray-500">Type scan</span><span class="font-bold text-blue-600">${r.type_scan}</span></div>` : ''}
            </div>
        `;
        confirmModal.classList.remove('hidden');
    }

    closeModalBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    confirmModal.addEventListener('click', function (e) {
        if (e.target === confirmModal) closeModal();
    });

    function closeModal() {
        confirmModal.classList.add('hidden');
    }

    confirmBtn.addEventListener('click', function () {
        if (!currentReservation) return;

        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Validation...';

        fetch('{{ route("chauffeur.reservations.confirm") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ reference: currentReservation.reference })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                closeModal();
                showSuccessToast(data.message);
                addToHistory(currentReservation);
                hideAll();
                initialState.classList.remove('hidden');
                referenceInput.value = '';
                currentReservation = null;
            } else {
                showErrorToast(data.message || 'Erreur lors de la validation.');
            }
        })
        .catch(() => showErrorToast('Erreur réseau.'))
        .finally(() => {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="fas fa-check"></i> Valider l\'embarquement';
        });
    });

    // ========================
    // Caméra QR (html5-qrcode)
    // ========================
    toggleCameraBtn && toggleCameraBtn.addEventListener('click', startCamera);
    stopCameraBtn && stopCameraBtn.addEventListener('click', stopCamera);

    function startCamera() {
        cameraContainer.classList.remove('hidden');
        
        if (html5QrCode) {
            try { html5QrCode.clear(); } catch(e) {}
        }
        
        html5QrCode = new Html5Qrcode("qr-reader");

        var config = {
            fps: 15,
            qrbox: { width: 280, height: 280 },
            aspectRatio: 1.333
        };

        html5QrCode.start(
            { facingMode: "environment" },
            config,
            function onScanSuccess(decodedText) {
                // Extraire la référence
                var reference = decodedText;
                try {
                    var data = JSON.parse(decodedText);
                    if (data.reference) reference = data.reference;
                } catch (e) {}

                stopCamera();
                referenceInput.value = reference;
                doSearch();
            },
            function onScanFailure(errorMessage) {
                // Ignore frame-by-frame errors
            }
        ).then(function() {
            isScanning = true;
        }).catch(function(err) {
            console.error("Camera start error:", err);
            // Try front camera
            html5QrCode.start(
                { facingMode: "user" },
                config,
                function(text) {
                    var ref = text;
                    try { var d = JSON.parse(text); if (d.reference) ref = d.reference; } catch(e) {}
                    stopCamera();
                    referenceInput.value = ref;
                    doSearch();
                }
            ).catch(function(err2) {
                showErrorToast("Impossible d'accéder à la caméra. Vérifiez vos permissions.");
                cameraContainer.classList.add('hidden');
            });
        });
    }

    function stopCamera() {
        if (html5QrCode && isScanning) {
            html5QrCode.stop().then(function() {
                html5QrCode.clear();
                isScanning = false;
            }).catch(function(err) {
                console.error(err);
            });
        }
        cameraContainer.classList.add('hidden');
    }

    // ========================
    // Historique live
    // ========================
    function addToHistory(r) {
        const list = document.getElementById('scanHistoryList');
        const now = new Date();
        const hh  = String(now.getHours()).padStart(2, '0');
        const mm  = String(now.getMinutes()).padStart(2, '0');

        const item = document.createElement('div');
        item.className = 'flex items-center gap-3 p-3 bg-green-50 border border-green-100 rounded-xl';
        item.innerHTML = `
            <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center text-green-600 font-bold text-sm flex-shrink-0">
                ${r.seat_number}
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-900 text-sm truncate">${r.passager_nom_complet}</p>
                <p class="text-xs text-gray-500"><span class="text-orange-500 font-mono">${r.reference}</span></p>
            </div>
            <div class="text-right flex-shrink-0">
                <span class="inline-flex items-center gap-1 text-xs text-green-600 font-bold">
                    <i class="fas fa-check-circle"></i> ${hh}:${mm}
                </span>
            </div>
        `;

        if (list) {
            list.prepend(item);
        } else {
            // Replace empty state
            const emptyState = document.querySelector('.lg\\:grid-cols-2 > div:last-child .text-center');
            if (emptyState) {
                const newList = document.createElement('div');
                newList.id = 'scanHistoryList';
                newList.className = 'space-y-3';
                emptyState.replaceWith(newList);
                newList.appendChild(item);
            }
        }
    }

    // ========================
    // Toasts
    // ========================
    function showSuccessToast(msg) {
        showToast(msg, 'bg-green-500');
    }
    function showErrorToast(msg) {
        showToast(msg, 'bg-red-500');
    }
    function showToast(msg, cls) {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-6 right-6 z-50 ${cls} text-white px-5 py-3 rounded-xl shadow-xl text-sm font-semibold flex items-center gap-2 transform translate-y-0 transition-all`;
        toast.innerHTML = `<i class="fas fa-info-circle"></i> ${msg}`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
            setTimeout(() => toast.remove(), 400);
        }, 3500);
    }
});
</script>
@endsection
