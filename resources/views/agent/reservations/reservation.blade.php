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
            border: 2px solid #edf2f7;
            border-radius: 12px;
            transition: all 0.2s;
            background: white;
        }
        
        .programme-item:hover {
            border-color: #cbd5e0;
            background: #f8fafc;
        }
        
        .programme-item.selected {
            border-color: #007bff;
            background-color: #ebf8ff;
            position: relative;
        }
        
        .programme-item.selected::after {
            content: '✓';
            position: absolute;
            top: 10px;
            right: 10px;
            color: #007bff;
            font-weight: bold;
            font-size: 1.2rem;
        }

        /* Badge véhicule */
        .badge-immat {
            background: #2d3748;
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-family: monospace;
            letter-spacing: 1px;
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
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold pl-2">Sélectionner le trajet</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-4">
                <p class="text-muted pl-2 mb-4">Pour quel véhicule effectuez-vous le contrôle ?</p>
                
                <div id="programmesList" style="max-height: 400px; overflow-y: auto;">
                    @if(isset($programmesDuJour) && $programmesDuJour->count() > 0)
                        @foreach($programmesDuJour as $prog)
                            <div class="programme-item p-3 mb-3 cursor-pointer" 
                                 data-programme-id="{{ $prog->id }}"
                                 data-vehicule-id="{{ $prog->vehicule_id }}"
                                 data-vehicule-immat="{{ $prog->vehicule->immatriculation ?? 'N/A' }}"
                                 onclick="selectProgramme(this)">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="font-weight-bold text-dark mb-1" style="font-size: 1.1rem;">
                                            {{ $prog->point_depart }} <i class="material-icons text-muted mx-1" style="font-size: 14px;">arrow_forward</i> {{ $prog->point_arrive }}
                                        </div>
                                        <div class="text-muted small">
                                            <i class="material-icons mr-1" style="font-size: 14px; vertical-align: text-bottom;">schedule</i>
                                            Départ : <strong>{{ $prog->heure_depart }}</strong>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if($prog->vehicule)
                                            <div class="badge-immat mb-1">{{ $prog->vehicule->immatriculation }}</div>
                                            <div class="small text-muted">{{ $prog->vehicule->marque }}</div>
                                        @else
                                            <span class="badge badge-warning">Sans véhicule</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="material-icons text-muted mb-3" style="font-size: 48px;">event_busy</i>
                            <p class="text-muted">Aucun programme prévu aujourd'hui.</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary px-4 rounded-pill shadow-sm" id="continueToScanBtn" disabled>
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
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <div class="modal-body p-4" id="confirmModalBody">
                <!-- Le contenu sera injecté par JS -->
            </div>
            <div class="modal-footer border-0 justify-content-center bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" id="cancelConfirmBtn">Refuser</button>
                <button type="button" class="btn btn-success rounded-pill px-4 shadow" id="confirmEmbarquementBtn">
                    <i class="material-icons mr-1">check_circle</i> Confirmer l'embarquement
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
                    alert('Veuillez sélectionner un programme/véhicule');
                    return;
                }
                $('#vehicleSelectModal').modal('hide');
                setTimeout(function() {
                    $('#selectedVehicleText').text(selectedVehiculeImmat);
                    $('#qrScannerModal').modal('show');
                }, 300);
            });

            // --- Logique Caméra ---
            function startCamera() {
                if (isScanning) return;
                
                // Utiliser l'ID 'reader'
                html5Qrcode = new Html5Qrcode("reader");
                
                var config = { 
                    fps: 10, 
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0 
                };

                html5Qrcode.start(
                    { facingMode: "environment" }, 
                    config, 
                    onScanSuccess,
                    (errorMessage) => { /* ignore per-frame errors */ }
                ).then(function() {
                    isScanning = true;
                    console.log("Camera started");
                }).catch(function(err) {
                    console.log("Back camera failed, trying front:", err);
                    html5Qrcode.start(
                        { facingMode: "user" }, config, onScanSuccess
                    ).catch(function(err2) {
                        $('#reader').html('<div class="text-white text-center p-5">Impossible d\'accéder à la caméra.<br>Vérifiez vos permissions.</div>');
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
                var html = `
                    <div class="passenger-info-card">
                        <div class="passenger-avatar shadow-sm">
                            <i class="material-icons" style="font-size: 40px;">person</i>
                        </div>
                        <div class="text-muted small text-uppercase">Passager</div>
                        <div class="passenger-name">${reservation.passager_nom_complet}</div>
                        
                        <div class="card bg-light border-0 rounded-lg p-3 mb-3">
                            <table class="info-table">
                                <tr>
                                    <td>Référence</td>
                                    <td><span class="badge badge-dark px-2 py-1">${reservation.reference}</span></td>
                                </tr>
                                <tr>
                                    <td>Siège N°</td>
                                    <td><span class="badge badge-primary px-3 py-1" style="font-size: 1.1em">${reservation.seat_number}</span></td>
                                </tr>
                                <tr>
                                    <td>Destination</td>
                                    <td>${reservation.trajet || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td>Téléphone</td>
                                    <td>${reservation.passager_telephone}</td>
                                </tr>
                            </table>
                        </div>
                        
                        ${reservation.status === 'validated' ? 
                            '<div class="alert alert-warning"><i class="material-icons" style="font-size:16px;vertical-align:text-bottom">warning</i> Ce billet a déjà été scanné.</div>' : ''}
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
                        vehicule_id: selectedVehiculeId
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

        // Fonction sélection programme (externe au document.ready pour portée globale du onclick HTML)
        function selectProgramme(element) {
            var $el = $(element);
            $('.programme-item').removeClass('selected');
            $el.addClass('selected');
            
            selectedVehiculeId = $el.data('vehicule-id');
            selectedProgrammeId = $el.data('programme-id');
            selectedVehiculeImmat = $el.data('vehicule-immat');
            
            $('#continueToScanBtn').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
        }
    </script>
@endpush