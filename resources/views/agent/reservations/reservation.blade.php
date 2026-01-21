@extends('agent.layouts.template')

@section('content')
    <div class="mdc-layout-grid">
        <div class="mdc-layout-grid__inner">
            <div class="mdc-layout-grid__cell stretch-card mdc-layout-grid__cell--span-12">
                <div class="mdc-card p-4">
                    
                    <!-- En-tête avec bouton Scanner -->
                    <div class="mdc-layout-grid">
        <div class="mdc-layout-grid__inner justify-content-center">
            <div class="mdc-layout-grid__cell stretch-card mdc-layout-grid__cell--span-8-desktop mdc-layout-grid__cell--span-12-tablet">
                <div class="mdc-card p-0" style="border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); overflow: hidden;">
                    
                    <div class="text-center py-5 text-white" style="background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset('assets/images/scan-header-bg.png') }}'); background-size: cover; background-position: center; position: relative;">
                        

                        <h3 class="font-weight-bold mb-1">Espace Embarquement</h3>
                        <p class="mb-0" style="opacity: 0.9;">Gérez les montées dans les cars en toute simplicité</p>
                    </div>

                    <div class="p-5 text-center">
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <h5 class="text-muted mb-4 font-weight-normal">
                                    Scannez le ticket du passager pour valider son accès et l'assigner à un véhicule.
                                </h5>

                                <button type="button" class="btn btn-primary btn-lg btn-rounded shadow-lg" id="openVehicleSelectBtn" 
                                    style="padding: 18px 40px; font-size: 1.2rem; transition: transform 0.2s;">
                                    <i class="material-icons mr-2" style="font-size: 28px; vertical-align: middle;">camera_alt</i>
                                    COMMENCER LE SCAN
                                </button>
                                
                                <div class="mt-4 text-muted small">
                                    <i class="material-icons" style="font-size: 14px; vertical-align: middle;">info</i>
                                    Assurez-vous d'avoir autorisé l'accès à la caméra
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stats rapides (optionnel) -->
                    <div class="bg-light p-3 border-top">
                        <div class="d-flex justify-content-around text-center">
                            <div>
                                <h4 class="mb-0 font-weight-bold text-primary">{{ $terminees->count() }}</h4>
                                <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.7rem;">Aujourd'hui</small>
                            </div>
                            <div>
                                <h4 class="mb-0 font-weight-bold text-success">{{ $programmesDuJour->count() }}</h4>
                                <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.7rem;">Départs</small>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

                    <!-- Formulaire caché pour la soumission du scan -->
                    <form id="scan-form" action="{{ route('agent.reservations.scan') }}" method="POST" style="display: none;">
                        @csrf
                        <input type="hidden" id="scan-input" name="reference">
                    </form>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="material-icons mr-2" style="vertical-align: middle;">check_circle</i>
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="material-icons mr-2" style="vertical-align: middle;">error</i>
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="material-icons mr-2" style="vertical-align: middle;">warning</i>
                            {{ session('warning') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Sélection Véhicule/Programme -->
    <div class="modal fade" id="vehicleSelectModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #28a745; color: white;">
                    <h5 class="modal-title">
                        <i class="material-icons mr-2" style="vertical-align: middle;">directions_bus</i>
                        Sélectionner le véhicule
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Choisissez le programme/véhicule pour lequel vous allez scanner les passagers:</p>
                    
                    <div id="programmesList">
                        @if(isset($programmesDuJour) && $programmesDuJour->count() > 0)
                            @foreach($programmesDuJour as $prog)
                                <div class="programme-card p-3 mb-2 border rounded cursor-pointer" 
                                     data-programme-id="{{ $prog->id }}"
                                     data-vehicule-id="{{ $prog->vehicule_id }}"
                                     data-vehicule-immat="{{ $prog->vehicule->immatriculation ?? 'N/A' }}"
                                     style="cursor: pointer; transition: all 0.2s;"
                                     onclick="selectProgramme(this)">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $prog->point_depart }} → {{ $prog->point_arrive }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="material-icons" style="font-size: 14px; vertical-align: middle;">schedule</i>
                                                {{ $prog->heure_depart }}
                                            </small>
                                        </div>
                                        <div class="text-right">
                                            @if($prog->vehicule)
                                                <span class="badge badge-primary">{{ $prog->vehicule->immatriculation }}</span>
                                                <br>
                                                <small class="text-muted">{{ $prog->vehicule->marque }} {{ $prog->vehicule->modele }}</small>
                                            @else
                                                <span class="badge badge-secondary">Pas de véhicule</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-warning text-center">
                                <i class="material-icons" style="font-size: 36px;">event_busy</i>
                                <p class="mb-0 mt-2">Aucun programme prévu aujourd'hui</p>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-success" id="continueToScanBtn" disabled>
                        <i class="material-icons mr-1" style="vertical-align: middle;">qr_code_scanner</i>
                        Continuer vers le scan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Scanner QR -->
    <div class="modal fade" id="qrScannerModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #007bff; color: white;">
                    <h5 class="modal-title">
                        <i class="material-icons mr-2" style="vertical-align: middle;">qr_code_scanner</i>
                        Scanner le QR Code
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Info véhicule sélectionné -->
                    <div id="selectedVehicleInfo" class="alert alert-info mb-3" style="display: none;">
                        <i class="material-icons mr-2" style="vertical-align: middle;">directions_bus</i>
                        Véhicule: <strong id="selectedVehicleText"></strong>
                    </div>
                    
                    <div id="reader" style="width: 100%; min-height: 350px;"></div>
                    <p class="text-center mt-3 text-muted">
                        <i class="material-icons" style="vertical-align: middle;">camera_alt</i>
                        Placez le QR Code du passager devant la caméra
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Inclure le modal de confirmation -->
    @include('agent.reservations.confirmation')
@endsection

@push('scripts')
    <!-- HTML5-QRCode Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <style>
        .programme-card:hover {
            background-color: #e8f5e9;
            border-color: #28a745 !important;
        }
        .programme-card.selected {
            background-color: #c8e6c9;
            border-color: #28a745 !important;
            box-shadow: 0 0 0 2px #28a745;
        }
    </style>

    <script>
        // Variables globales pour la sélection
        var selectedVehiculeId = null;
        var selectedProgrammeId = null;
        var selectedVehiculeImmat = null;
        var currentReference = null;

        $(document).ready(function() {
            var html5Qrcode = null;
            var isScanning = false;

            // Handle hash for tabs
            var hash = window.location.hash;
            if (hash) {
                $('a[href="' + hash + '"]').tab('show');
            }

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
                    $('#selectedVehicleInfo').show();
                    $('#qrScannerModal').modal('show');
                }, 300);
            });

            // Function to start camera
            function startCamera() {
                if (isScanning) return;
                
                html5Qrcode = new Html5Qrcode("reader");
                
                var config = { fps: 10, qrbox: { width: 250, height: 250 } };

                html5Qrcode.start(
                    { facingMode: "environment" }, 
                    config, 
                    onScanSuccess
                ).then(function() {
                    isScanning = true;
                    console.log("Camera started");
                }).catch(function(err) {
                    console.log("Back camera failed, trying front:", err);
                    html5Qrcode.start(
                        { facingMode: "user" }, 
                        config, 
                        onScanSuccess
                    ).then(function() {
                        isScanning = true;
                    }).catch(function(err2) {
                        console.error("All cameras failed:", err2);
                        $('#reader').html('<div class="alert alert-danger text-center p-4">' +
                            '<i class="material-icons" style="font-size: 48px;">videocam_off</i>' +
                            '<h5 class="mt-2">Impossible d\'accéder à la caméra</h5>' +
                            '</div>');
                    });
                });
            }

            function stopCamera() {
                if (html5Qrcode && isScanning) {
                    html5Qrcode.stop().then(function() {
                        isScanning = false;
                    }).catch(function(err) {
                        isScanning = false;
                    });
                }
            }

            function onScanSuccess(decodedText) {
                console.log('QR Code scanned:', decodedText);
                
                var reference = decodedText;

                // Parse JSON si nécessaire
                try {
                    var data = JSON.parse(decodedText);
                    if (data.reference) {
                        reference = data.reference;
                    }
                } catch (e) {
                    // Pas JSON, utiliser tel quel
                }

                // Arrêter le scanner
                stopCamera();
                $('#qrScannerModal').modal('hide');

                // Rechercher les infos du passager
                searchReservation(reference);
            }

            // Rechercher la réservation et afficher les infos
            function searchReservation(reference) {
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
                        }
                    },
                    error: function(xhr) {
                        var message = 'Erreur lors de la recherche';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        alert('❌ ' + message);
                    }
                });
            }

            // Afficher le modal de confirmation avec les infos du passager
            function showConfirmModal(reservation) {
                var html = `
                    <div class="passenger-info-card">
                        <div class="passenger-avatar">
                            <i class="material-icons">person</i>
                        </div>
                        <div class="passenger-name">${reservation.passager_nom_complet}</div>
                        <table class="info-table">
                            <tr>
                                <td>Référence:</td>
                                <td><span class="reference-badge">${reservation.reference}</span></td>
                            </tr>
                            <tr>
                                <td>Place:</td>
                                <td><span class="seat-badge">${reservation.seat_number}</span></td>
                            </tr>
                            <tr>
                                <td>Trajet:</td>
                                <td>${reservation.trajet}</td>
                            </tr>
                            <tr>
                                <td>Date:</td>
                                <td>${reservation.date_voyage}</td>
                            </tr>
                            <tr>
                                <td>Heure:</td>
                                <td>${reservation.heure_depart}</td>
                            </tr>
                            <tr>
                                <td>Téléphone:</td>
                                <td>${reservation.passager_telephone}</td>
                            </tr>
                            <tr>
                                <td>Véhicule:</td>
                                <td><span class="badge badge-success">${selectedVehiculeImmat}</span></td>
                            </tr>
                        </table>
                    </div>
                `;
                
                $('#confirmModalBody').html(html);
                $('#confirmModal').modal('show');
            }

            // Confirmer l'embarquement
            $('#confirmEmbarquementBtn').click(function() {
                console.log('currentReference:', currentReference);
                console.log('selectedVehiculeId:', selectedVehiculeId);
                
                if (!currentReference) {
                    alert('Référence manquante pour la confirmation. currentReference=' + currentReference);
                    return;
                }
                if (!selectedVehiculeId) {
                    alert('Véhicule non sélectionné. selectedVehiculeId=' + selectedVehiculeId);
                    return;
                }

                var btn = $(this);
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2"></span>Confirmation...');

                $.ajax({
                    url: '{{ route("agent.reservations.confirm") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        reference: currentReference,
                        vehicule_id: selectedVehiculeId
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#confirmModal').modal('hide');
                            alert('✅ ' + response.message);
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        var message = 'Erreur lors de la confirmation';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        alert('❌ ' + message);
                        btn.prop('disabled', false).html('<i class="material-icons mr-1" style="vertical-align: middle;">check</i>Confirmer l\'embarquement');
                    }
                });
            });

            // Fermer le modal de confirmation
            $('#closeConfirmModal, #cancelConfirmBtn').click(function() {
                $('#confirmModal').modal('hide');
                currentReference = null;
            });

            // Événements du modal scanner
            $('#qrScannerModal').on('shown.bs.modal', function () {
                $('#reader').html('<div class="text-center p-4"><div class="spinner-border text-primary"></div><p class="mt-2">Démarrage de la caméra...</p></div>');
                setTimeout(startCamera, 500);
            });

            $('#qrScannerModal').on('hidden.bs.modal', function () {
                stopCamera();
                $('#reader').html('');
            });

            // Reset sélection visuelle quand on ferme le modal véhicule (mais garder les valeurs!)
            $('#vehicleSelectModal').on('hidden.bs.modal', function () {
                // Ne reset que l'affichage visuel, pas les variables
                $('.programme-card').removeClass('selected');
                $('#continueToScanBtn').prop('disabled', true);
            });
        });

        // Fonction pour sélectionner un programme
        function selectProgramme(element) {
            var $el = $(element);
            var vehiculeId = $el.data('vehicule-id');
            
            if (!vehiculeId) {
                alert('Ce programme n\'a pas de véhicule assigné');
                return;
            }
            
            $('.programme-card').removeClass('selected');
            $el.addClass('selected');
            
            // Mettre à jour les variables globales
            selectedVehiculeId = vehiculeId;
            selectedProgrammeId = $el.data('programme-id');
            selectedVehiculeImmat = $el.data('vehicule-immat');
            
            $('#continueToScanBtn').prop('disabled', false);
        }
    </script>
@endpush
