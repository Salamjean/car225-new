@extends('agent.layouts.template')

@section('content')
    <div class="mdc-layout-grid">
        <div class="mdc-layout-grid__inner">
            <div class="mdc-layout-grid__cell stretch-card mdc-layout-grid__cell--span-12">
                <div class="mdc-card p-4">
                    
                    <!-- En-tête avec bouton Scanner -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="card-title mb-0">Gestion des Réservations</h6>
                        <button type="button" class="btn btn-primary btn-lg" id="openScannerBtn" style="font-size: 1.2rem; padding: 15px 30px;">
                            <i class="material-icons mr-2" style="font-size: 28px; vertical-align: middle;">qr_code_scanner</i>
                            Scanner un QR Code
                        </button>
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

                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs" id="reservationTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="en-cours-tab" data-toggle="tab" href="#en-cours" role="tab">
                                <i class="material-icons mr-1" style="font-size: 18px; vertical-align: middle;">schedule</i>
                                En cours ({{ $enCours->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="terminees-tab" data-toggle="tab" href="#terminees" role="tab">
                                <i class="material-icons mr-1" style="font-size: 18px; vertical-align: middle;">check_circle</i>
                                Terminées ({{ $terminees->count() }})
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="reservationTabsContent">
                        <!-- Onglet En cours -->
                        <div class="tab-pane fade show active" id="en-cours" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hoverable">
                                    <thead>
                                        <tr>
                                            <th>Réf</th>
                                            <th>Passager</th>
                                            <th>Place</th>
                                            <th>Trajet</th>
                                            <th>Date Voyage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($enCours as $reservation)
                                            <tr>
                                                <td><span class="badge badge-info">{{ $reservation->reference }}</span></td>
                                                <td>
                                                    <strong>{{ $reservation->passager_prenom }} {{ $reservation->passager_nom }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $reservation->passager_email }}</small>
                                                </td>
                                                <td><span class="badge badge-primary">{{ $reservation->seat_number }}</span></td>
                                                <td>{{ $reservation->programme->point_depart ?? '?' }} → {{ $reservation->programme->point_arrive ?? '?' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($reservation->date_voyage)->format('d/m/Y') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <i class="material-icons" style="font-size: 48px; color: #ccc;">inbox</i>
                                                    <p class="text-muted mt-2">Aucune réservation en attente de scan.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Onglet Terminées -->
                        <div class="tab-pane fade" id="terminees" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hoverable">
                                    <thead>
                                        <tr>
                                            <th>Réf</th>
                                            <th>Passager</th>
                                            <th>Place</th>
                                            <th>Trajet</th>
                                            <th>Scanné le</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($terminees as $reservation)
                                            <tr>
                                                <td><span class="badge badge-secondary">{{ $reservation->reference }}</span></td>
                                                <td>{{ $reservation->passager_prenom }} {{ $reservation->passager_nom }}</td>
                                                <td><span class="badge badge-success">{{ $reservation->seat_number }}</span></td>
                                                <td>{{ $reservation->programme->point_depart ?? '?' }} → {{ $reservation->programme->point_arrive ?? '?' }}</td>
                                                <td>
                                                    @if($reservation->embarquement_scanned_at)
                                                        {{ \Carbon\Carbon::parse($reservation->embarquement_scanned_at)->format('d/m/Y H:i') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <i class="material-icons" style="font-size: 48px; color: #ccc;">inventory_2</i>
                                                    <p class="text-muted mt-2">Aucune réservation scannée.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

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

    <script>
        $(document).ready(function() {
            var html5Qrcode = null;
            var isScanning = false;
            var currentReference = null;

            // Handle hash for tabs
            var hash = window.location.hash;
            if (hash) {
                $('a[href="' + hash + '"]').tab('show');
            }

            // Ouvrir le scanner
            $('#openScannerBtn').click(function() {
                $('#qrScannerModal').modal('show');
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
                        reference: reference
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
                        </table>
                    </div>
                `;
                
                $('#confirmModalBody').html(html);
                $('#confirmModal').modal('show');
            }

            // Confirmer l'embarquement
            $('#confirmEmbarquementBtn').click(function() {
                if (!currentReference) return;

                var btn = $(this);
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2"></span>Confirmation...');

                $.ajax({
                    url: '{{ route("agent.reservations.confirm") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        reference: currentReference
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
        });
    </script>
@endpush
