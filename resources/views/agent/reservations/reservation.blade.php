@extends('agent.layouts.template')

@section('content')
    <div class="container-fluid content-wrapper">
        <!-- Style personnalisé pour cette vue uniquement -->
        <style>
            :root {
                --primary-brand: #ff5a1f;
                --primary-gradient: linear-gradient(135deg, #001a41 0%, #003380 100%);
                --secondary-brand: #001a41;
                --orange-brand: #ff5a1f;
                --bg-soft: #f8fafc;
                --success-accent: #22c55e;
                --danger-accent: #ef4444;
                --info-accent: #3b82f6;
            }

            /* Fond global plus doux */
            .content-wrapper {
                background-color: var(--bg-soft);
                min-height: calc(100vh - 64px);
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem;
            }

            /* La carte principale */
            .scan-card {
                background: white;
                border-radius: 32px;
                border: 1px solid rgba(249, 115, 22, 0.1);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
                padding: 3rem;
                max-width: 600px;
                width: 100%;
                margin: auto;
                text-align: center;
                position: relative;
                overflow: hidden;
            }

            .scan-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 6px;
                background: var(--primary-gradient);
            }

            /* Animation de pulsation autour de l'icône */
            .icon-container {
                position: relative;
                width: 140px;
                height: 140px;
                background: rgba(255, 90, 31, 0.08);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 2.5rem;
            }

            .pulse-ring {
                position: absolute;
                width: 100%;
                height: 100%;
                border-radius: 50%;
                border: 2px solid var(--primary-brand);
                opacity: 0.5;
                animation: pulse-brand 2s infinite ease-out;
            }

            @keyframes pulse-brand {
                0% {
                    transform: scale(1);
                    opacity: 0.8;
                }

                100% {
                    transform: scale(1.6);
                    opacity: 0;
                }
            }

            /* Typographie */
            .scan-title {
                font-family: 'Inter', sans-serif;
                font-weight: 800;
                color: var(--secondary-brand);
                font-size: 2.25rem;
                letter-spacing: -0.025em;
                margin-bottom: 1rem;
            }

            .scan-subtitle {
                color: #64748b;
                font-size: 1.1rem;
                line-height: 1.6;
                max-width: 400px;
                margin-left: auto;
                margin-right: auto;
            }

            /* Bouton d'action principal */
            .btn-scanner-hero {
                background: linear-gradient(135deg, var(--orange-brand) 0%, #e64e16 100%);
                border: none;
                color: white !important;
                padding: 1.25rem 2.5rem;
                border-radius: 100px;
                font-weight: 700;
                font-size: 1.25rem;
                box-shadow: 0 10px 25px -5px rgba(255, 90, 31, 0.5);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.75rem;
                text-transform: none;
                margin-top: 1rem;
            }

            .btn-scanner-hero:hover {
                transform: translateY(-4px);
                box-shadow: 0 20px 30px -10px rgba(255, 90, 31, 0.6);
            }

            .btn-scanner-hero:active {
                transform: translateY(0);
            }

            /* Styles pour la liste des véhicules */
            .programme-item {
                border: 1px solid #e2e8f0;
                border-radius: 20px;
                transition: all 0.3s ease;
                background: white;
                cursor: pointer;
                overflow: hidden;
            }

            .programme-item:hover {
                border-color: var(--primary-brand);
                box-shadow: 0 10px 20px rgba(249, 115, 22, 0.1);
                transform: scale(1.01);
            }

            .programme-item.selected {
                border-color: var(--primary-brand) !important;
                background-color: rgba(249, 115, 22, 0.02) !important;
                box-shadow: 0 10px 25px rgba(249, 115, 22, 0.15);
            }

            .selected-check {
                position: absolute;
                top: 1rem;
                right: 1rem;
                background: var(--primary-brand);
                color: white;
                width: 28px;
                height: 28px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                z-index: 10;
                transform: scale(0);
                transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }

            .programme-item.selected .selected-check {
                transform: scale(1);
            }

            /* Badge véhicule */
            .badge-immat {
                background: var(--secondary-brand);
                color: white;
                padding: 6px 12px;
                border-radius: 8px;
                font-family: 'JetBrains Mono', monospace;
                letter-spacing: 1px;
                font-weight: 700;
                font-size: 0.8rem;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .info-label {
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #94a3b8;
                font-weight: 600;
            }

            .info-value {
                font-weight: 700;
                color: var(--secondary-brand);
            }

            /* Modal Styling */
            .modal-content {
                border-radius: 24px;
                border: none;
            }

            .modal-header-premium {
                background: var(--secondary-brand);
                color: white;
                padding: 2rem;
                border-bottom: none;
            }

            /* Animations */
            .fade-in-up {
                animation: fadeInUp 0.6s ease-out forwards;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Nouveau design du scanner */
            .scanner-container {
                position: relative;
                width: 100%;
                max-width: 580px;
                aspect-ratio: 4/3; /* Changé de 1/1 pour mieux s'adapter aux écrans horizontaux */
                max-height: 50vh; /* Limite la hauteur pour éviter les coupures sur petit écran */
                margin: 0 auto;
                border-radius: 30px;
                overflow: hidden;
                border: 2px solid rgba(255, 255, 255, 0.1);
                background: #000;
                box-shadow: 0 0 50px rgba(0, 0, 0, 0.5);
            }

            @media (max-width: 768px) {
                .scanner-container {
                    aspect-ratio: 1/1;
                    max-height: 40vh;
                }
            }

            #reader {
                width: 100% !important;
                height: 100% !important;
                border: none !important;
            }

            #reader video {
                width: 100% !important;
                height: 100% !important;
                object-fit: cover !important;
            }

            .scanner-overlay {
                position: absolute;
                inset: 0;
                pointer-events: none;
                z-index: 10;
                background: radial-gradient(circle, transparent 70%, rgba(0, 6, 23, 0.6) 100%);
            }

            .scanner-guide {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 250px;
                height: 250px;
                border: 2px solid rgba(255, 255, 255, 0.1);
                border-radius: 30px;
            }

            @media (max-width: 576px) {
                .scanner-guide {
                    width: 200px;
                    height: 200px;
                }
            }

            .scanner-corner {
                position: absolute;
                width: 40px;
                height: 40px;
                border: 4px solid var(--primary-brand);
                filter: drop-shadow(0 0 10px rgba(249, 115, 22, 0.5));
            }

            .corner-tl {
                top: -2px;
                left: -2px;
                border-right: 0;
                border-bottom: 0;
                border-radius: 15px 0 0 0;
            }

            .corner-tr {
                top: -2px;
                right: -2px;
                border-left: 0;
                border-bottom: 0;
                border-radius: 0 15px 0 0;
            }

            .corner-bl {
                bottom: -2px;
                left: -2px;
                border-right: 0;
                border-top: 0;
                border-radius: 0 0 0 15px;
            }

            .corner-br {
                bottom: -2px;
                right: -2px;
                border-left: 0;
                border-top: 0;
                border-radius: 0 0 15px 0;
            }

            .scanner-line {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 4px;
                background: linear-gradient(90deg, transparent, var(--primary-brand), transparent);
                box-shadow: 0 0 15px var(--primary-brand);
                animation: scan-move 3s linear infinite;
            }

            @keyframes scan-move {
                0% {
                    top: 10%;
                    opacity: 0;
                }

                10% {
                    opacity: 1;
                }

                90% {
                    opacity: 1;
                }

                100% {
                    top: 90%;
                    opacity: 0;
                }
            }
            
            /* Fix pour la sidebar sur tablette et z-index global */
            #qrScannerModal, #vehicleSelectModal, #confirmModal {
                z-index: 99999 !important;
            }
            .modal-backdrop {
                z-index: 99998 !important;
            }
            
            /* Amélioration de la réactivité pour éviter les coupures */
            @media (max-width: 991px) {
                .modal-dialog {
                    margin: 0.5rem auto;
                    max-width: calc(100% - 1rem);
                }
            }
        </style>

        <div class="row w-100 max-w-4xl">
            <div class="col-12">
                <!-- Carte Principale -->
                <div class="scan-card fade-in-up">
                    <!-- En-tête décoratif -->
                    <div class="mb-5">
                        <span
                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-orange-50 text-orange-600 border border-orange-100">
                            <i class="material-icons mr-2 text-base">verified</i>
                            Contrôleur de Billets Digital
                        </span>
                    </div>

                    <!-- Icone animée -->
                    <div class="icon-container">
                        <div class="pulse-ring"></div>
                        <div class="z-10 bg-white p-5 rounded-full shadow-lg">
                            <i class="material-icons text-orange-500" style="font-size: 64px;">qr_code_scanner</i>
                        </div>
                    </div>

                    <!-- Textes -->
                    <h2 class="scan-title">Scanner un Billet</h2>
                    <p class="scan-subtitle mb-8">
                        Validez instantanément les réservations en scannant le QR code sur le billet du passager.
                    </p>

                    <!-- Gros bouton d'action -->
                    <div class="px-4">
                        <button type="button" class="btn-scanner-hero" id="openVehicleSelectBtn">
                            <i class="material-icons">sensors</i>
                            Démarrer le contrôle
                        </button>
                    </div>

                    <div class="mt-8 flex items-center justify-center gap-4 text-slate-400">
                        <span class="flex items-center gap-1 text-xs font-medium uppercase tracking-wider">
                            <i class="material-icons text-sm">flash_on</i> Rapide
                        </span>
                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                        <span class="flex items-center gap-1 text-xs font-medium uppercase tracking-wider">
                            <i class="material-icons text-sm">shield</i> Sécurisé
                        </span>
                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                        <span class="flex items-center gap-1 text-xs font-medium uppercase tracking-wider">
                            <i class="material-icons text-sm">sync</i> Temps réel
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= MODALES (Design Amélioré) ================= -->

    <!-- Modal Sélection Véhicule -->
    <div class="modal fade" id="vehicleSelectModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 680px;">
            <div class="modal-content overflow-hidden border-0 shadow-2xl rounded-[22px]">
                <div class="modal-header-premium relative">
                    <div class="relative z-100">
                        <h5 class="modal-title flex items-center gap-3 font-bold text-2xl tracking-tight">
                            <span class="p-2 bg-[#ff5a1f]/10 rounded-lg">
                                <i class="material-icons opacity-100 text-[#ff5a1f]">directions_bus</i>
                            </span>
                            Sélectionner le Trajet
                        </h5>
                        <p class="text-slate-400 mt-2 text-sm">Pour quel voyage effectuez-vous le contrôle ?</p>
                    </div>
                </div>
                <div class="modal-body p-6 bg-slate-50">
                    <div id="programmesList" style="max-height: 350px; overflow-y: auto;" class="pr-2 custom-scrollbar">
                        @if (isset($programmesDuJour) && $programmesDuJour->count() > 0)
                            @foreach ($programmesDuJour as $prog)
                                @php
                                    $voyage = $prog->voyages->first();
                                    $v = $voyage ? $voyage->vehicule : null;
                                    $isAssigned = $v ? true : false;
                                @endphp
                                <div class="programme-item relative mb-4 {{ $isAssigned ? '' : 'opacity-60 grayscale cursor-not-allowed' }}"
                                    data-programme-id="{{ $prog->id }}" data-vehicule-id="{{ $v->id ?? '' }}"
                                    data-vehicule-immat="{{ $v->immatriculation ?? '' }}"
                                    onclick="{{ $isAssigned ? 'selectProgramme(this)' : '' }}">

                                    <div class="selected-check">
                                        <i class="material-icons text-sm">check</i>
                                    </div>

                                    <div class="p-5">
                                        <!-- Route info -->
                                        <div class="flex items-center justify-between gap-4 mb-4">
                                            <div class="flex-1">
                                                <div class="info-label mb-1">Départ</div>
                                                <div class="text-lg font-extrabold text-slate-900 leading-tight">
                                                    {{ $prog->point_depart }}
                                                </div>
                                                @if ($prog->gareDepart)
                                                    <div
                                                        class="text-xs font-bold text-orange-600 flex items-center gap-1 mt-1 uppercase tracking-wide">
                                                        <i class="material-icons text-xs">location_on</i>
                                                        {{ $prog->gareDepart->nom_gare }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="flex flex-col items-center gap-1">
                                                <div
                                                    class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-orange-500 shadow-sm border border-slate-200">
                                                    <i class="material-icons text-xl">east</i>
                                                </div>
                                            </div>

                                            <div class="flex-1 text-right">
                                                <div class="info-label mb-1 text-right">Arrivée</div>
                                                <div class="text-lg font-extrabold text-slate-900 leading-tight">
                                                    {{ $prog->point_arrive }}
                                                </div>
                                                @if ($prog->gareArrivee)
                                                    <div
                                                        class="text-xs font-bold text-slate-500 flex items-center justify-end gap-1 mt-1 uppercase tracking-wide">
                                                        <i class="material-icons text-xs">flag</i>
                                                        {{ $prog->gareArrivee->nom_gare }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Meta info bar -->
                                        <div class="flex items-center justify-between border-t border-slate-100 pt-4 mt-4">
                                            <div class="flex items-center gap-4">
                                                <div class="flex items-center gap-2">
                                                    <i class="material-icons text-orange-500 text-lg">schedule</i>
                                                    <span
                                                        class="text-sm font-bold text-slate-700">{{ $prog->heure_depart }}</span>
                                                </div>
                                                @if ($v)
                                                    <div class="px-3 py-1 bg-slate-100 rounded-lg border border-slate-200">
                                                        <span
                                                            class="text-[11px] font-black text-slate-600 tracking-wider font-mono">{{ $v->immatriculation }}</span>
                                                    </div>
                                                @endif
                                            </div>

                                            @if (!$v)
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-wider">
                                                    <i class="material-icons text-sm">warning_amber</i> Non assigné
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-12 bg-white rounded-3xl border border-dashed border-slate-300">
                                <i class="material-icons text-slate-300" style="font-size: 64px;">event_busy</i>
                                <h4 class="mt-4 font-bold text-slate-900">Aucun voyage</h4>
                                <p class="text-slate-500 text-sm">Aucun programme n'est prévu pour aujourd'hui.</p>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer p-6 border-0 bg-white flex gap-3">
                    <button type="button"
                        class="flex-1 py-3 px-4 rounded-2xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors"
                        data-dismiss="modal">Annuler</button>
                    <button type="button"
                        class="flex-1 py-3 px-4 rounded-xl font-bold text-white bg-[#ff5a1f] shadow-xl shadow-[#ff5a1f]/20 disabled:opacity-60 disabled:shadow-none transition-all flex items-center justify-center gap-2"
                        id="continueToScanBtn" disabled>
                        <span>Valider et Scanner</span>
                        <i class="material-icons">arrow_forward</i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Scanner QR -->
    <div class="modal fade" id="qrScannerModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down" role="document" style="max-width: 500px;">
            <div class="modal-content bg-[#020617] border-0 shadow-3xl overflow-y-auto max-h-[95vh] sm:rounded-[40px]">
                <div class="modal-header border-0 p-6 flex items-center justify-between">
                    <div>
                        <h5 class="modal-title text-white font-bold text-xl tracking-tight flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                            Scanner un Billet
                        </h5>
                        <p class="text-slate-500 text-xs mt-1">Placez le QR code au centre du cadre</p>
                    </div>
                    <button type="button"
                        class="w-10 h-10 rounded-full bg-white/10 text-white flex items-center justify-center hover:bg-white/20 transition-all"
                        data-dismiss="modal" aria-label="Close">
                        <i class="material-icons">close</i>
                    </button>
                </div>

                <div class="modal-body p-4 pt-0 relative">
                    <!-- Zone Caméra Immersive -->
                    <div class="scanner-container">
                        <div id="reader"></div>

                        <!-- Overlay Holographique -->
                        <div class="scanner-overlay"></div>

                        <div class="scanner-guide">
                            <div class="scanner-corner corner-tl"></div>
                            <div class="scanner-corner corner-tr"></div>
                            <div class="scanner-corner corner-bl"></div>
                            <div class="scanner-corner corner-br"></div>
                            <div class="scanner-line"></div>
                        </div>

                        <!-- Vehicle info badge floattant -->
                        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 w-max">
                            <div
                                class="px-5 py-2.5 bg-black/60 backdrop-blur-xl rounded-2xl border border-white/10 flex items-center gap-3 shadow-2xl">
                                <i class="material-icons text-orange-500 text-lg">directions_bus</i>
                                <span class="text-white text-sm font-bold tracking-wider font-mono"
                                    id="selectedVehicleText">---</span>
                            </div>
                        </div>

                        <!-- Contrôles additionnels (Flash) -->
                        <div class="absolute top-6 right-6 z-20">
                            <button type="button" id="toggleFlash"
                                class="w-12 h-12 rounded-2xl bg-black/40 backdrop-blur-md border border-white/10 text-white flex items-center justify-center hover:bg-black/60 transition-all hidden">
                                <i class="material-icons">flashlight_on</i>
                            </button>
                        </div>
                    </div>

                    <!-- Section Saisie Manuelle (Plus Compacte et Élégante) -->
                    <div class="mt-6 px-4">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="h-px flex-1 bg-white/5"></div>
                            <span class="text-[9px] uppercase font-bold tracking-[0.2em] text-slate-500">ou par
                                référence</span>
                            <div class="h-px flex-1 bg-white/5"></div>
                        </div>

                        <div class="flex gap-3 bg-white/5 p-2 rounded-3xl border border-white/5">
                            <div class="relative flex-1">
                                <i
                                    class="material-icons absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-lg">local_offer</i>
                                <input type="text" id="manualReferenceInput"
                                    class="w-full bg-transparent border-0 rounded-2xl py-3 pl-12 pr-4 text-white font-mono font-bold text-sm placeholder:text-slate-600 focus:outline-none"
                                    placeholder="RÉFÉRENCE BILLET" autocomplete="off">
                            </div>
                            <button type="button" id="manualSearchBtn"
                                class="w-12 h-12 bg-[#ff5a1f] hover:bg-[#e64e16] text-white rounded-2xl transition-all shadow-lg flex items-center justify-center">
                                <i class="material-icons">search</i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-6 pt-0 justify-center">
                    <p class="text-slate-600 text-[10px] text-center font-medium leading-relaxed max-w-[280px]">
                        Assurez-vous que l'éclairage est suffisant pour une lecture optimale du code.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirmation Passager (Premium Redesign) -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px;">
            <div class="modal-content overflow-hidden border-0 shadow-[0_35px_60px_-15px_rgba(0,0,0,0.3)] rounded-[32px]">
                <div class="modal-body p-0" id="confirmModalBody">
                    <!-- Contenu injecté dynamiquement -->
                </div>
                <div class="modal-footer p-6 border-0 bg-slate-50 flex items-center gap-3">
                    <button type="button"
                        class="flex-1 py-4 font-bold text-slate-500 bg-white border border-slate-200 rounded-2xl hover:bg-slate-100 transition-colors"
                        id="cancelConfirmBtn">
                        Fermer
                    </button>
                    <button type="button"
                        class="flex-[2] py-4 px-8 font-bold text-white bg-[#ff5a1f] rounded-2xl shadow-xl shadow-[#ff5a1f]/20 hover:bg-[#e64e16] hover:-translate-y-1 transition-all flex items-center justify-center gap-3"
                        id="confirmEmbarquementBtn">
                        <i class="material-icons">task_alt</i>
                        Confirmer l'embarquement
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
    </style>

@endsection

@push('scripts')
    <!-- HTML5-QRCode Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <script>
        // Variables globales
        var selectedVehiculeId = null;
        var selectedProgrammeId = null;
        var selectedVehiculeImmat = null;
        var currentReference = null;
        var html5Qrcode = null;
        var isScanning = false;

        $(document).ready(function() {
            // Ouvrir le modal de sélection
            $('#openVehicleSelectBtn').click(function() {
                $('#vehicleSelectModal').modal('show');
            });

            // Continuer vers le scan
            $('#continueToScanBtn').click(function() {
                if (!selectedVehiculeId) {
                    alert('Veuillez sélectionner un voyage.');
                    return;
                }
                $('#vehicleSelectModal').modal('hide');
                setTimeout(() => {
                    $('#selectedVehicleText').text(selectedVehiculeImmat);
                    $('#qrScannerModal').modal('show');
                }, 300);
            });

            // --- Logique Caméra ---
            function startCamera() {
                if (isScanning) return;

                const containerWidth = $('.scanner-container').width() || 350;
                let boxSize = Math.min(containerWidth * 0.75, 280);
                if (boxSize < 200) boxSize = 200;

                // Synchroniser le guide visuel
                $('.scanner-guide').css({
                    width: boxSize + 'px',
                    height: boxSize + 'px'
                });

                if (html5Qrcode === null) {
                    html5Qrcode = new Html5Qrcode("reader");
                }

                var config = {
                    fps: 30,
                    qrbox: {
                        width: boxSize,
                        height: boxSize
                    },
                    aspectRatio: 1.0,
                    disableFlip: false,
                    rememberLastUsedCamera: true
                };

                // Vérification du contexte sécurisé (HTTPS)
                if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                    showCameraError("Le scanner nécessite une connexion sécurisée (HTTPS) pour accéder à la caméra.");
                    return;
                }

                html5Qrcode.start({
                            facingMode: "environment"
                        },
                        config,
                        onScanSuccess
                    ).then(handleCameraStarted)
                    .catch(err => {
                        console.warn("Échec caméra arrière, tentative caméra frontale...", err);
                        html5Qrcode.start({
                                facingMode: "user"
                            }, config, onScanSuccess)
                            .then(handleCameraStarted)
                            .catch(err2 => {
                                console.error("Erreur critique caméra:", err2);
                                showCameraError("L'accès à la caméra a été refusé. Veuillez vérifier les permissions dans les paramètres de votre navigateur.");
                            });
                    });
            }

            function showCameraError(msg) {
                $('#reader').html(`
                    <div class="h-full flex flex-col items-center justify-center text-white p-6 text-center">
                        <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center mb-4">
                            <i class="material-icons text-3xl text-red-500">videocam_off</i>
                        </div>
                        <h6 class="font-bold text-sm">Accès caméra impossible</h6>
                        <p class="text-[11px] text-slate-400 mt-3 mb-6 leading-relaxed max-w-[240px] mx-auto">${msg}</p>
                        <button type="button" onclick="location.reload()" class="px-5 py-2.5 bg-white/10 hover:bg-white/20 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all inline-flex items-center gap-2">
                           <i class="material-icons text-base">refresh</i> Actualiser la page
                        </button>
                    </div>
                `);
            }

            function handleCameraStarted() {
                isScanning = true;
                // Vérifier si le flash est disponible
                try {
                    const cameras = html5Qrcode.getRunningTrack();
                    if (cameras && cameras.clone && cameras.applyConstraints) {
                        const capabilities = cameras.getCapabilities();
                        if (capabilities && capabilities.torch) {
                            $('#toggleFlash').removeClass('hidden').off('click').on('click', function() {
                                const isTorchOn = cameras.getSettings().torch;
                                cameras.applyConstraints({
                                    advanced: [{
                                        torch: !isTorchOn
                                    }]
                                });
                                $(this).find('i').text(!isTorchOn ? 'flashlight_off' : 'flashlight_on');
                                $(this).toggleClass('bg-orange-600 bg-black/40');
                            });
                        }
                    }
                } catch (e) {
                    console.log("Flash non supporté");
                }
            }

            function stopCamera() {
                if (html5Qrcode && isScanning) {
                    html5Qrcode.stop().then(() => {
                        html5Qrcode.clear();
                        isScanning = false;
                    }).catch(err => {
                        console.warn("Erreur lors de l'arrêt de la caméra:", err);
                        isScanning = false;
                    });
                }
            }

            function onScanSuccess(decodedText) {
                var reference = decodedText;
                try {
                    var data = JSON.parse(decodedText);
                    if (data.reference) reference = data.reference;
                } catch (e) {}

                stopCamera();
                $('#qrScannerModal').modal('hide');
                searchReservation(reference);
            }

            // --- Manuel Search ---
            $(document).on('click', '#manualSearchBtn', function() {
                var ref = $('#manualReferenceInput').val().trim();
                if (!ref) return;
                stopCamera();
                $('#qrScannerModal').modal('hide');
                searchReservation(ref);
            });

            $(document).on('keypress', '#manualReferenceInput', function(e) {
                if (e.key === 'Enter') $('#manualSearchBtn').click();
            });

            // Modals events
            $('#qrScannerModal').on('shown.bs.modal', startCamera);
            $('#qrScannerModal').on('hidden.bs.modal', stopCamera);
            $('#qrScannerModal').on('show.bs.modal', () => $('#manualReferenceInput').val(''));

            $('#cancelConfirmBtn').click(() => $('#confirmModal').modal('hide'));
        });

        // --- Fonctions Globales ---

        function selectProgramme(element) {
            var $el = $(element);
            $('.programme-item').removeClass('selected');
            $el.addClass('selected');

            selectedVehiculeId = $el.data('vehicule-id');
            selectedProgrammeId = $el.data('programme-id');
            selectedVehiculeImmat = $el.data('vehicule-immat');

            $('#continueToScanBtn').prop('disabled', !selectedVehiculeId);
        }

        function searchReservation(reference) {
            $('#confirmModalBody').html(`
                <div class="px-12 py-20 text-center">
                    <div class="relative w-24 h-24 mx-auto mb-8">
                        <div class="absolute inset-0 border-8 border-slate-100 rounded-full"></div>
                        <div class="absolute inset-0 border-8 border-orange-500 rounded-full border-t-transparent animate-spin"></div>
                        <div class="absolute inset-4 bg-white rounded-full flex items-center justify-center shadow-inner">
                            <i class="material-icons text-orange-200">search</i>
                        </div>
                    </div>
                    <h4 class="font-extrabold text-slate-900 text-xl tracking-tight">Vérification du billet</h4>
                    <p class="text-slate-500 mt-2">Nous interrogeons la base de données...</p>
                </div>
            `);

            $('#confirmEmbarquementBtn').hide();
            $('#confirmModal').modal('show');

            $.ajax({
                url: "{{ route('agent.reservations.search') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    reference: reference,
                    vehicule_id: selectedVehiculeId,
                    programme_id: selectedProgrammeId
                },
                success: function(response) {
                    if (response.success) {
                        currentReference = reference;
                        showConfirmModal(response.reservation);
                        $('#confirmEmbarquementBtn').show();
                    } else {
                        $('#confirmModalBody').html(`
                            <div class="px-10 py-16 text-center">
                                <div class="w-20 h-20 bg-red-50 text-red-500 rounded-3xl flex items-center justify-center mx-auto mb-6">
                                    <i class="material-icons" style="font-size: 40px;">error_outline</i>
                                </div>
                                <h4 class="font-black text-slate-900 text-xl mb-2">Billet Invalide</h4>
                                <p class="text-slate-500 leading-relaxed">${response.message || 'Ce billet ne semble pas valide.'}</p>
                            </div>
                        `);
                    }
                },
                error: function() {
                    $('#confirmModalBody').html(`
                        <div class="p-12 text-center text-red-500">
                            <i class="material-icons mb-3" style="font-size: 48px;">cloud_off</i>
                            <h5 class="font-bold">Erreur réseau</h5>
                            <p class="text-sm">Impossible de contacter le serveur.</p>
                        </div>
                    `);
                }
            });
        }

        function showConfirmModal(res) {
            var passengerName = res.passager_nom_complet || 'Passager Inconnu';
            var phone = res.passager_telephone || 'N/A';
            var type = res.type_scan || 'ALLER';
            var bgClass = (type === 'RETOUR') ? 'bg-orange-500' : 'bg-slate-800';

            // Initials logic
            var names = passengerName.trim().split(' ');
            var initials = names.length > 1 ?
                (names[0][0] + names[names.length - 1][0]).toUpperCase() :
                names[0][0].toUpperCase();

            var html = `
                <div class="relative">
                    <div class="p-8 bg-slate-900 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-orange-500/10 rounded-full translate-x-10 -translate-y-10"></div>
                        <div class="relative z-10 flex items-center gap-5">
                            <div class="w-16 h-16 bg-orange-500 rounded-2xl flex items-center justify-center text-2xl font-black shadow-lg">
                                ${initials}
                            </div>
                            <div>
                                <div class="text-[10px] uppercase font-black tracking-[0.2em] text-orange-400 mb-1">Passager Confirmé</div>
                                <h3 class="text-xl font-bold leading-tight">${passengerName}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <!-- Route Details -->
                        <div class="bg-slate-50 rounded-3xl p-6 border border-slate-100 mb-8">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex-1">
                                    <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Départ</div>
                                    <div class="text-lg font-black text-slate-900 leading-tight">${res.gare_depart_ville || res.trajet.split(' → ')[0]}</div>
                                    <div class="text-[11px] font-bold text-orange-600 mt-1">${res.gare_depart || ''}</div>
                                </div>
                                <div class="flex flex-col items-center">
                                    <div class="w-10 h-[2px] bg-slate-200"></div>
                                    <div class="w-8 h-8 rounded-full bg-white border-2 border-slate-200 flex items-center justify-center -my-4 z-10">
                                        <i class="material-icons text-slate-400" style="font-size: 14px;">directions_bus</i>
                                    </div>
                                    <div class="w-10 h-[2px] bg-slate-200"></div>
                                </div>
                                <div class="flex-1 text-right">
                                    <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Arrivée</div>
                                    <div class="text-lg font-black text-slate-900 leading-tight">${res.gare_arrivee_ville || res.trajet.split(' → ')[1]}</div>
                                    <div class="text-[11px] font-bold text-orange-600 mt-1">${res.gare_arrivee || ''}</div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6 mb-8">
                            <div>
                                <div class="info-label mb-1">Référence</div>
                                <div class="font-mono font-bold text-slate-900">${res.reference}</div>
                            </div>
                            <div class="text-right">
                                <div class="info-label mb-1">Type de billet</div>
                                <div class="${bgClass} text-white text-[10px] font-black uppercase px-3 py-1 rounded-full inline-block tracking-wider">
                                    ${type}
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600">
                                        <i class="material-icons text-base">phone</i>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-600">Téléphone</span>
                                </div>
                                <span class="text-sm font-bold text-slate-900">${phone}</span>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600">
                                        <i class="material-icons text-base">airline_seat_recline_normal</i>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-600">Siège</span>
                                </div>
                                <span class="text-sm font-bold text-slate-900">${res.seat_number || '1'}</span>
                            </div>
                        </div>
                    </div>

                    <div class="px-8 pb-4">
                        ${res.statut === 'terminee' ? `
                                                            <div class="py-3 px-4 bg-amber-50 text-amber-700 rounded-xl flex items-center gap-3 text-xs font-bold border border-amber-100">
                                                                <i class="material-icons text-base">warning</i>
                                                                Déjà scanné précédemment
                                                            </div>
                                                        ` : `
                                                            <div class="py-3 px-4 bg-emerald-50 text-emerald-700 rounded-xl flex items-center gap-3 text-xs font-bold border border-emerald-100">
                                                                <i class="material-icons text-base">verified_user</i>
                                                                Prêt pour l'embarquement
                                                            </div>
                                                        `}
                    </div>
                </div>
            `;
            $('#confirmModalBody').html(html);
        }

        $('#confirmEmbarquementBtn').click(function() {
            var btn = $(this);
            btn.prop('disabled', true).html(`
                <div class="w-5 h-5 border-2 border-white rounded-full border-t-transparent animate-spin mr-2"></div>
                Confirmation...
            `);

            $.ajax({
                url: "{{ route('agent.reservations.confirm') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    reference: currentReference,
                    vehicule_id: selectedVehiculeId,
                    programme_id: selectedProgrammeId
                },
                success: function(response) {
                    if (response.success) {
                        $('#confirmModalBody').html(`
                            <div class="px-10 py-20 text-center">
                                <div class="w-24 h-24 bg-emerald-500 text-white rounded-[40px] flex items-center justify-center mx-auto mb-8 shadow-2xl shadow-emerald-200 animate-bounce">
                                    <i class="material-icons" style="font-size: 48px;">done_all</i>
                                </div>
                                <h4 class="font-black text-slate-900 text-2xl mb-2">Embarquement Validé</h4>
                                <p class="text-slate-500">Le passager peut monter à bord.</p>
                            </div>
                        `);
                        btn.hide();
                        $('#cancelConfirmBtn').text('Terminer').addClass(
                            'bg-slate-900 text-white border-0');

                        setTimeout(() => {
                            $('#confirmModal').modal('hide');
                            $('#qrScannerModal').modal('show');
                        }, 2500);
                    } else {
                        alert(response.message || 'Erreur');
                        btn.prop('disabled', false).html(
                            '<i class="material-icons mr-2">task_alt</i> Confirmer');
                    }
                },
                error: function() {
                    alert('Erreur serveur.');
                    btn.prop('disabled', false).html(
                        '<i class="material-icons mr-2">task_alt</i> Confirmer');
                }
            });
        });
    </script>
@endpush
