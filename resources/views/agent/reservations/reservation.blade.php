@extends('agent.layouts.template')

@section('content')
<div class="container-fluid content-wrapper">
    <!-- Style personnalisé pour cette vue uniquement -->
    <style>
        /* Fond global plus doux */
        .content-wrapper {
            background-color: #f4f6f8;
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* La carte principale */
        .scan-card {
            background: white;
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: transform 0.3s ease;
            max-width: 600px;
            width: 100%;
            margin: auto;
        }

        /* Animation de pulsation autour de l'icône */
        .icon-container {
            position: relative;
            width: 120px;
            height: 120px;
            background: rgba(0, 123, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }

        .pulse-ring {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid #007bff;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(1.5); opacity: 0; }
        }

        /* Typographie */
        .scan-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            color: #2c3e50;
            font-size: 1.8rem;
        }
        
        .scan-subtitle {
            color: #7f8c8d;
            font-size: 1rem;
            line-height: 1.6;
        }

        /* Bouton d'action principal */
        .btn-scanner-hero {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            color: white;
            padding: 18px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.2rem;
            box-shadow: 0 8px 20px rgba(0, 123, 255, 0.3);
            transition: all 0.3s ease;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-scanner-hero:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(0, 123, 255, 0.4);
            color: white;
        }
        
        .btn-scanner-hero:active {
            transform: translateY(1px);
        }

        /* Styles pour la liste des véhicules */
        .programme-item {
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            transition: all 0.2s;
            background: white;
            cursor: pointer;
        }
        
        .programme-item:hover {
            border-color: #90cdf4;
            box-shadow: 0 4px 12px rgba(49, 130, 206, 0.1);
        }
        
        .programme-item.selected {
            border-color: #3182ce !important;
            background-color: white !important;
            box-shadow: 0 4px 16px rgba(49, 130, 206, 0.2);
            position: relative;
        }
        
        .programme-item.selected::after {
            content: '✓';
            position: absolute;
            top: 12px;
            right: 14px;
            background: #3182ce;
            color: white;
            font-weight: bold;
            font-size: 0.8rem;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        /* Badge véhicule */
        .badge-immat {
            background: #2d3748;
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-family: monospace;
            letter-spacing: 1px;
            font-weight: 700;
        }
        
        /* Modal Passenger Info */
        .passenger-info-card {
            text-align: center;
        }
        .passenger-avatar {
            width: 80px;
            height: 80px;
            background: #e2e8f0;
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #718096;
        }
        .passenger-name {
            font-size: 1.4rem;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            text-align: left;
        }
        .info-table td {
            padding: 8px 0;
            border-bottom: 1px solid #edf2f7;
        }
        .info-table tr:last-child td {
            border-bottom: none;
        }
        .info-table td:first-child {
            color: #718096;
            font-weight: 500;
            width: 40%;
        }
        .info-table td:last-child {
            color: #2d3748;
            font-weight: 600;
            text-align: right;
        }
    </style>

    <div class="row w-100">
        <div class="col-12">
            
            <!-- Carte Principale -->
            <div class="scan-card p-5 text-center">
                <!-- En-tête décoratif -->
                <div class="mb-4">
                    <span class="badge badge-pill badge-light text-muted px-3 py-2">
                        <i class="material-icons" style="font-size: 14px; vertical-align: middle;">verified_user</i> 
                        Zone Contrôleur
                    </span>
                </div>

                <!-- Icone animée -->
                <div class="icon-container">
                    <div class="pulse-ring"></div>
                    <i class="material-icons text-primary" style="font-size: 50px;">qr_code_scanner</i>
                </div>

                <!-- Textes -->
                <h2 class="scan-title mb-3">Validation des Billets</h2>
                <p class="scan-subtitle mb-5">
                    Utilisez votre caméra pour scanner le QR Code du passager.<br>
                    Le système vérifiera automatiquement la validité.
                </p>

                <!-- Gros bouton d'action -->
                <button type="button" class="btn-scanner-hero" id="openVehicleSelectBtn">
                    <i class="material-icons mr-3">photo_camera</i>
                    Commencer le Scan
                </button>
                
                <p class="mt-4 text-muted small">
                    <i class="material-icons" style="font-size: 14px; vertical-align: middle;">info</i>
                    Assurez-vous d'avoir une bonne luminosité
                </p>
            </div>

        </div>
    </div>
</div>

<!-- ================= MODALES (Design Amélioré) ================= -->

<!-- Modal Sélection Véhicule -->
<div class="modal fade" id="vehicleSelectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 520px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%); padding: 24px 28px 20px;">
                <div>
                    <h5 class="modal-title font-weight-bold text-white" style="font-size: 1.2rem;">
                        <i class="material-icons mr-2" style="vertical-align: middle; font-size: 22px;">directions_bus</i>
                        Sélectionner le trajet
                    </h5>
                    <p class="text-white-50 mb-0 mt-1" style="font-size: 0.82rem;">Pour quel voyage effectuez-vous le contrôle ?</p>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 20px 24px; background: #f8fafc;">
                
                <div id="programmesList" style="max-height: 350px; overflow-y: auto;" class="mb-2">
                    @if(isset($programmesDuJour) && $programmesDuJour->count() > 0)
                        @foreach($programmesDuJour as $prog)
                            @php
                                $voyage = $prog->voyages->first();
                                $v = $voyage ? $voyage->vehicule : null;
                                $isAssigned = $v ? true : false;
                            @endphp
                            <div class="programme-item p-0 mb-3 {{ $isAssigned ? 'cursor-pointer' : '' }}" 
                                 data-programme-id="{{ $prog->id }}"
                                 data-vehicule-id="{{ $v->id ?? '' }}"
                                 data-vehicule-immat="{{ $v->immatriculation ?? '' }}"
                                 onclick="{{ $isAssigned ? 'selectProgramme(this)' : '' }}"
                                 style="border-radius: 16px; border: 2px solid #e2e8f0; background: {{ $isAssigned ? 'white' : '#f1f5f9' }}; transition: all 0.2s; overflow: hidden; {{ !$isAssigned ? 'opacity: 0.7; cursor: not-allowed;' : '' }}">
                                 
                                 <!-- Route Header -->
                                 <div style="padding: 16px 18px 12px;">
                                     <div class="d-flex align-items-start justify-content-between">
                                         <div class="flex-grow-1">
                                             <!-- Route with gares -->
                                             <div class="d-flex align-items-center" style="gap: 10px;">
                                                 <div style="flex: 1;">
                                                     <div style="font-weight: 800; font-size: 1rem; color: #1a202c;">
                                                         {{ $prog->point_depart }}
                                                     </div>
                                                     @if($prog->gareDepart)
                                                         <div style="font-size: 0.72rem; color: #10b981; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px;">
                                                             <i class="material-icons" style="font-size: 11px; vertical-align: middle;">location_on</i>
                                                             {{ $prog->gareDepart->nom_gare }}
                                                         </div>
                                                     @endif
                                                 </div>
                                                 <div style="display: flex; flex-direction: column; align-items: center; padding: 0 4px;">
                                                     <div style="width: 28px; height: 28px; background: linear-gradient(135deg, #3182ce, #2b6cb0); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                         <i class="material-icons text-white" style="font-size: 14px;">arrow_forward</i>
                                                     </div>
                                                 </div>
                                                 <div style="flex: 1; text-align: right;">
                                                     <div style="font-weight: 800; font-size: 1rem; color: #1a202c;">
                                                         {{ $prog->point_arrive }}
                                                     </div>
                                                     @if($prog->gareArrivee)
                                                         <div style="font-size: 0.72rem; color: #e53e3e; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px;">
                                                             <i class="material-icons" style="font-size: 11px; vertical-align: middle;">flag</i>
                                                             {{ $prog->gareArrivee->nom_gare }}
                                                         </div>
                                                     @endif
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                                 
                                 <!-- Footer info bar -->
                                 <div style="background: #f7fafc; padding: 10px 18px; border-top: 1px solid #edf2f7; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                                     <div class="d-flex align-items-center" style="gap: 12px;">
                                         <span style="font-size: 0.78rem; color: #718096; display: inline-flex; align-items: center; gap: 4px;">
                                             <i class="material-icons" style="font-size: 15px; color: #3182ce;">schedule</i>
                                             Départ : <strong style="color: #2d3748;">{{ $prog->heure_depart }}</strong>
                                         </span>
                                         @if($prog->heure_arrive)
                                         <span style="font-size: 0.78rem; color: #718096; display: inline-flex; align-items: center; gap: 4px;">
                                             <i class="material-icons" style="font-size: 15px; color: #e53e3e;">schedule</i>
                                             Arrivée : <strong style="color: #2d3748;">{{ $prog->heure_arrive }}</strong>
                                         </span>
                                         @endif
                                     </div>
                                     <div>
                                         @if($v)
                                             <span class="badge-immat" style="font-size: 0.75rem;">{{ $v->immatriculation }}</span>
                                         @else
                                             <span style="background: #e2e8f0; color: #4a5568; padding: 4px 10px; border-radius: 6px; font-size: 0.72rem; font-weight: 700;">
                                                 <i class="material-icons" style="font-size: 13px; vertical-align: middle;">directions_bus</i> Aucun véhicule attribué
                                             </span>
                                         @endif
                                     </div>
                                 </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="material-icons text-muted mb-3" style="font-size: 48px;">event_busy</i>
                            <p class="text-muted font-weight-bold">Aucun programme prévu aujourd'hui.</p>
                            <p class="text-muted small">Les voyages disponibles apparaîtront ici</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer border-0" style="padding: 16px 24px; background: white;">
                <button type="button" class="btn btn-light px-4" data-dismiss="modal" style="border-radius: 10px;">Annuler</button>
                <button type="button" class="btn btn-primary px-4 shadow-sm" id="continueToScanBtn" disabled style="border-radius: 10px; font-weight: 700;">
                    Valider et Scanner <i class="material-icons ml-2" style="font-size: 16px; vertical-align: middle;">arrow_forward</i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Scanner QR (Style Camera) -->
<div class="modal fade" id="qrScannerModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header border-0 bg-transparent text-white">
                <h5 class="modal-title">Scanner un billet</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0 position-relative">
                <!-- Info Overlay -->
                <div class="position-absolute w-100 text-center text-white" style="top: 20px; z-index: 10; pointer-events: none;">
                    <span class="badge badge-dark p-2" style="background: rgba(0,0,0,0.6);">
                        <i class="material-icons mr-1" style="font-size: 14px; vertical-align: text-bottom;">directions_bus</i>
                        <span id="selectedVehicleText">Véhicule non défini</span>
                    </span>
                </div>
                
                <!-- Zone Caméra -->
                <div id="reader" style="width: 100%; min-height: 400px; background: black;"></div>
                
                <!-- Overlay cadre de visée -->
                <div class="position-absolute w-100 h-100 d-flex align-items-center justify-content-center" style="top: 0; left: 0; pointer-events: none;">
                    <div style="width: 250px; height: 250px; border: 2px solid rgba(255,255,255,0.5); border-radius: 20px; box-shadow: 0 0 0 9999px rgba(0,0,0,0.5);"></div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-dark justify-content-center">
                <button type="button" class="btn btn-outline-light rounded-pill px-4" data-dismiss="modal">
                    <i class="material-icons mr-2" style="font-size: 16px; vertical-align: middle;">close</i> Arrêter
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmation Passager (Inclus depuis fichier externe ou refait ici pour le style) -->
<!-- On suppose que tu as un fichier confirmation.blade.php, sinon voici la structure modale ID "confirmModal" -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 480px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-body p-4" id="confirmModalBody">
                <!-- Le contenu sera injecté par JS -->
            </div>
            <div class="modal-footer border-0 justify-content-center" style="background: #f8fafc; padding: 16px 24px;">
                <button type="button" class="btn btn-light px-4" id="cancelConfirmBtn" style="border-radius: 10px; font-weight: 600;">Refuser</button>
                <button type="button" class="btn btn-success px-4 shadow" id="confirmEmbarquementBtn" style="border-radius: 10px; font-weight: 700;">
                    <i class="material-icons mr-1" style="font-size: 18px; vertical-align: middle;">check_circle</i> Confirmer l'embarquement
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <!-- HTML5-QRCode Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <script>
        // Variables globales pour la sélection
        var selectedVehiculeId = null;
        var selectedProgrammeId = null;
        var selectedVehiculeImmat = null;
        var currentReference = null;

        $(document).ready(function() {
            var html5Qrcode = null;
            var isScanning = false;

            // Ouvrir le modal de sélection du véhicule
            $('#openVehicleSelectBtn').click(function() {
                $('#vehicleSelectModal').modal('show');
            });

            // Continuer vers le scan après sélection
            $('#continueToScanBtn').click(function() {
                if (!selectedVehiculeId) {
                    alert('Veuillez sélectionner un voyage.');
                    return;
                }

                var btn = $(this);
                
                $('#vehicleSelectModal').modal('hide');
                btn.prop('disabled', false).html('Valider et Scanner <i class="material-icons ml-2" style="font-size: 16px; vertical-align: middle;">arrow_forward</i>');
                
                setTimeout(function() {
                    $('#selectedVehicleText').text(selectedVehiculeImmat);
                    $('#qrScannerModal').modal('show');
                }, 300);
            });

            // --- Logique Caméra ---
            function startCamera() {
                if (isScanning) return;
                
                // --- VERSION STABLE (Restaurée) ---
                // On recrée l'instance pour éviter les conflits
                if (html5Qrcode) {
                    try { html5Qrcode.clear(); } catch(e) {}
                }
                html5Qrcode = new Html5Qrcode("reader");
                
                var config = { 
                    fps: 20, // Une valeur moyenne stable
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                };

                // Configuration standard de la caméra (arrière)
                var cameraConfig = { facingMode: "environment" };

                html5Qrcode.start(
                    cameraConfig, 
                    config, 
                    onScanSuccess,
                    (errorMessage) => { 
                        // On ignore les erreurs de scan par frame pour ne pas spammer la console
                    }
                ).then(function() {
                    isScanning = true;
                    console.log("Camera started in stable mode");
                }).catch(function(err) {
                    console.log("Back camera failed, trying user camera:", err);
                    // Tentative caméra frontale si l'arrière échoue
                    html5Qrcode.start(
                        { facingMode: "user" }, config, onScanSuccess
                    ).catch(function(err2) {
                        $('#reader').html('<div class="text-white text-center p-5"><i class="material-icons" style="font-size:48px">videocam_off</i><br><br>Impossible d\'accéder à la caméra.<br>Veuillez vérifier les permissions du navigateur.</div>');
                    });
                });
            }

            function stopCamera() {
                if (html5Qrcode && isScanning) {
                    html5Qrcode.stop().then(function() {
                        html5Qrcode.clear();
                        isScanning = false;
                    }).catch(function(err) { console.log(err); });
                }
            }

            function onScanSuccess(decodedText) {
                // Bip sonore (optionnel)
                // var audio = new Audio('beep.mp3'); audio.play();

                console.log('QR Code scanned:', decodedText);
                var reference = decodedText;
                try {
                    var data = JSON.parse(decodedText);
                    if (data.reference) reference = data.reference;
                } catch (e) {}

                stopCamera();
                $('#qrScannerModal').modal('hide');
                searchReservation(reference);
            }

            // --- Logique Backend ---
            function searchReservation(reference) {
                // Afficher un loader le temps de la recherche
                $('#confirmModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-3">Recherche du billet...</p></div>');
                $('#confirmModal').modal('show');

                $.ajax({
                    url: '{{ route("agent.reservations.search") }}',
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
                            $('#confirmEmbarquementBtn').show(); // Montrer le bouton confirmer
                        } else {
                             // Afficher l'erreur dans le modal joliment
                             $('#confirmModalBody').html(`
                                <div class="text-center text-danger py-4">
                                    <i class="material-icons" style="font-size: 60px;">error_outline</i>
                                    <h4 class="mt-3">Erreur</h4>
                                    <p>${response.message || 'Billet non trouvé'}</p>
                                </div>
                             `);
                             $('#confirmEmbarquementBtn').hide(); // Cacher le bouton confirmer
                        }
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erreur serveur';
                        $('#confirmModalBody').html(`
                            <div class="text-center text-danger py-4">
                                <i class="material-icons" style="font-size: 60px;">wifi_off</i>
                                <h4 class="mt-3">Erreur de connexion</h4>
                                <p>${msg}</p>
                            </div>
                         `);
                        $('#confirmEmbarquementBtn').hide();
                    }
                });
            }

            function showConfirmModal(reservation) {
                // Type badge color
                var typeBadge = '';
                if (reservation.type_scan === 'ALLER') {
                    typeBadge = '<span style="background: linear-gradient(135deg, #3182ce, #2b6cb0); color: white; padding: 4px 14px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.5px;">ALLER</span>';
                } else if (reservation.type_scan === 'RETOUR') {
                    typeBadge = '<span style="background: linear-gradient(135deg, #d69e2e, #b7791f); color: white; padding: 4px 14px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.5px;">RETOUR</span>';
                }

                var arBadge = reservation.is_aller_retour
                    ? '<span style="background: #ebf8ff; color: #2b6cb0; padding: 3px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 700;">Aller-Retour</span>'
                    : '<span style="background: #f0fff4; color: #276749; padding: 3px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 700;">Aller Simple</span>';

                var html = `
                    <div style="text-align: center; padding: 8px 0;">
                        <!-- Passenger Header -->
                        <div style="width: 72px; height: 72px; background: linear-gradient(135deg, #3182ce, #2b6cb0); border-radius: 50%; margin: 0 auto 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 20px rgba(49,130,206,0.3);">
                            <i class="material-icons text-white" style="font-size: 36px;">person</i>
                        </div>
                        <div style="font-size: 0.72rem; color: #a0aec0; text-transform: uppercase; font-weight: 600; letter-spacing: 1px;">Passager</div>
                        <div style="font-size: 1.4rem; font-weight: 800; color: #1a202c; margin: 4px 0 16px;">${reservation.passager_nom_complet}</div>

                        <!-- Route Visualization -->
                        <div style="background: #f7fafc; border-radius: 16px; padding: 18px 20px; margin-bottom: 16px; border: 1px solid #e2e8f0;">
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                                <div style="flex: 1; text-align: center;">
                                    <div style="font-weight: 800; font-size: 1rem; color: #1a202c;">${reservation.trajet.split(' → ')[0] || ''}</div>
                                    ${reservation.gare_depart ? '<div style="font-size: 0.7rem; color: #10b981; font-weight: 700; text-transform: uppercase; margin-top: 2px;"><i class="material-icons" style="font-size:11px;vertical-align:middle;">location_on</i> ' + reservation.gare_depart + '</div>' : ''}
                                    <div style="font-size: 0.78rem; color: #4a5568; margin-top: 4px; font-weight: 600;">
                                        <i class="material-icons" style="font-size:14px;vertical-align:middle;color:#3182ce;">schedule</i> ${reservation.heure_depart}
                                    </div>
                                </div>
                                <div style="display: flex; flex-direction: column; align-items: center; padding: 0 2px;">
                                    <div style="width: 40px; height: 2px; background: linear-gradient(to right, #3182ce, #e53e3e); border-radius: 2px;"></div>
                                    <div style="width: 30px; height: 30px; background: linear-gradient(135deg, #3182ce, #2b6cb0); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 4px 0;">
                                        <i class="material-icons text-white" style="font-size: 14px;">directions_bus</i>
                                    </div>
                                    <div style="width: 40px; height: 2px; background: linear-gradient(to right, #3182ce, #e53e3e); border-radius: 2px;"></div>
                                </div>
                                <div style="flex: 1; text-align: center;">
                                    <div style="font-weight: 800; font-size: 1rem; color: #1a202c;">${reservation.trajet.split(' → ')[1] || ''}</div>
                                    ${reservation.gare_arrivee ? '<div style="font-size: 0.7rem; color: #e53e3e; font-weight: 700; text-transform: uppercase; margin-top: 2px;"><i class="material-icons" style="font-size:11px;vertical-align:middle;">flag</i> ' + reservation.gare_arrivee + '</div>' : ''}
                                    <div style="font-size: 0.78rem; color: #4a5568; margin-top: 4px; font-weight: 600;">
                                        <i class="material-icons" style="font-size:14px;vertical-align:middle;color:#e53e3e;">schedule</i> ${reservation.heure_arrivee || '--:--'}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Cards Grid -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 16px;">
                            <!-- Seat -->
                            <div style="background: #edf2f7; border-radius: 12px; padding: 14px; text-align: center;">
                                <div style="font-size: 0.68rem; color: #a0aec0; text-transform: uppercase; font-weight: 600;">Siège</div>
                                <div style="font-size: 1.6rem; font-weight: 900; color: #2d3748;">${reservation.seat_number}</div>
                            </div>
                            <!-- Reference -->
                            <div style="background: #edf2f7; border-radius: 12px; padding: 14px; text-align: center;">
                                <div style="font-size: 0.68rem; color: #a0aec0; text-transform: uppercase; font-weight: 600;">Référence</div>
                                <div style="font-size: 0.85rem; font-weight: 800; color: #2d3748; font-family: monospace; letter-spacing: 1px; margin-top: 6px;">${reservation.reference}</div>
                            </div>
                            <!-- Price -->
                            <div style="background: #f0fff4; border-radius: 12px; padding: 14px; text-align: center;">
                                <div style="font-size: 0.68rem; color: #a0aec0; text-transform: uppercase; font-weight: 600;">Montant</div>
                                <div style="font-size: 1.1rem; font-weight: 900; color: #276749;">${reservation.montant}</div>
                            </div>
                            <!-- Date -->
                            <div style="background: #ebf8ff; border-radius: 12px; padding: 14px; text-align: center;">
                                <div style="font-size: 0.68rem; color: #a0aec0; text-transform: uppercase; font-weight: 600;">Date</div>
                                <div style="font-size: 1rem; font-weight: 800; color: #2b6cb0;">${reservation.date_voyage}</div>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 16px; text-align: left;">
                            <div style="padding: 10px 16px; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #f0f0f0;">
                                <i class="material-icons" style="font-size: 18px; color: #3182ce;">phone</i>
                                <div>
                                    <div style="font-size: 0.68rem; color: #a0aec0;">Téléphone</div>
                                    <div style="font-weight: 700; color: #2d3748; font-size: 0.9rem;">${reservation.passager_telephone}</div>
                                </div>
                            </div>
                            ${reservation.passager_email ? '<div style="padding: 10px 16px; display: flex; align-items: center; gap: 10px;"><i class="material-icons" style="font-size: 18px; color: #3182ce;">email</i><div><div style="font-size: 0.68rem; color: #a0aec0;">Email</div><div style="font-weight: 700; color: #2d3748; font-size: 0.9rem;">' + reservation.passager_email + '</div></div></div>' : ''}
                        </div>

                        <!-- Badges -->
                        <div style="display: flex; align-items: center; justify-content: center; gap: 8px; flex-wrap: wrap;">
                            ${typeBadge}
                            ${arBadge}
                        </div>

                        ${reservation.statut === 'terminee' ? 
                            '<div style="background: #fffbeb; border: 1px solid #fbbf24; border-radius: 12px; padding: 12px 16px; margin-top: 14px; display: flex; align-items: center; gap: 8px; justify-content: center;"><i class="material-icons" style="font-size:18px;color:#d97706;">warning</i><span style="color: #92400e; font-weight: 700; font-size: 0.85rem;">Ce billet a déjà été scanné</span></div>' : ''}
                    </div>
                `;
                $('#confirmModalBody').html(html);
            }

            // Confirmer l'embarquement
            $('#confirmEmbarquementBtn').click(function() {
                var btn = $(this);
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2"></span>Validation...');

                $.ajax({
                    url: '{{ route("agent.reservations.confirm") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        reference: currentReference,
                        vehicule_id: selectedVehiculeId,
                        programme_id: selectedProgrammeId  // AJOUT pour détection aller/retour
                    },
                    success: function(response) {
                        $('#confirmModal').modal('hide');
                        // Réinitialiser le bouton
                        btn.prop('disabled', false).html('<i class="material-icons mr-1">check_circle</i> Confirmer l\'embarquement');
                        
                        // Petit feedback visuel de succès (Toast ou Alert)
                        alert('✅ ' + response.message); 
                        
                        // Rouvrir le scanner automatiquement ? (Optionnel)
                        // setTimeout(function(){ $('#qrScannerModal').modal('show'); }, 1000);
                    },
                    error: function(xhr) {
                        alert('❌ ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Erreur'));
                        btn.prop('disabled', false).html('<i class="material-icons mr-1">check_circle</i> Confirmer l\'embarquement');
                    }
                });
            });

            // Fermer les modales reset
            $('#cancelConfirmBtn').click(function() { $('#confirmModal').modal('hide'); });

            // Événements du modal scanner
            $('#qrScannerModal').on('shown.bs.modal', function () {
                setTimeout(startCamera, 300);
            });
            $('#qrScannerModal').on('hidden.bs.modal', function () {
                stopCamera();
            });
        });

        // Fonction sélection programme simplified
        function selectProgramme(element) {
            var $el = $(element);
            $('.programme-item').removeClass('selected');
            $el.addClass('selected');
            
            selectedVehiculeId = $el.data('vehicule-id');
            selectedProgrammeId = $el.data('programme-id');
            selectedVehiculeImmat = $el.data('vehicule-immat');
            
            // Si pas de véhicule assigné, on désactive le bouton
            if (!selectedVehiculeId || selectedVehiculeId === '') {
                $('#continueToScanBtn').prop('disabled', true).addClass('btn-secondary').removeClass('btn-primary')
                    .html('Voyage non assigné <i class="material-icons ml-2" style="font-size: 16px; vertical-align: middle;">warning</i>');
            } else {
                $('#continueToScanBtn').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary')
                    .html('Valider et Scanner <i class="material-icons ml-2" style="font-size: 16px; vertical-align: middle;">arrow_forward</i>');
            }
        }

        // Event listeners pour sélection manuelle supprimés
    </script>
@endpush