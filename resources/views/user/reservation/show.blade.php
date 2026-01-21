@extends('user.layouts.template')

@section('title', 'Détails de la Réservation - ' . $reservation->reference)

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="row mb-6">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('reservation.index') }}">Mes Réservations</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $reservation->reference }}</li>
                </ol>
            </nav>
            
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Détails de la Réservation</h1>
                    <p class="text-muted mb-0">Référence : {{ $reservation->reference }}</p>
                </div>
                <div class="d-flex gap-2">
                    @if($reservation->statut == 'confirmee')
                        <a href="{{ route('reservations.ticket', $reservation->id) }}" 
                           class="btn btn-primary" style="background-color: #e94e1a; border-color: #e94e1a;">
                            <i class="fas fa-file-pdf me-2"></i> Télécharger le billet
                        </a>
                    @endif
                    <a href="{{ route('reservation.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Colonne principale -->
        <div class="col-lg-8">
            <!-- Carte du voyage -->
            <div class="card shadow mb-4">
                <div class="card-header py-3" style="border-bottom: 3px solid #e94e1a;">
                    <h6 class="m-0 font-weight-bold" style="color: #e94e1a;">
                        <i class="fas fa-route me-2"></i> Détails du Voyage
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="info-card">
                                <div class="info-label">Itinéraire</div>
                                <div class="info-value h5">
                                    {{ $reservation->programme->point_depart }} → {{ $reservation->programme->point_arrive }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="info-card">
                                <div class="info-label">Date du voyage</div>
                                <div class="info-value h5">
                                    {{ $reservation->date_voyage }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="info-card">
                                <div class="info-label">Heure de départ</div>
                                <div class="info-value h5">
                                    {{ date('H:i', strtotime($reservation->programme->heure_depart)) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="info-card">
                                <div class="info-label">Heure d'arrivée estimée</div>
                                <div class="info-value h5">
                                    {{ date('H:i', strtotime($reservation->programme->heure_arrive)) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="info-card">
                                <div class="info-label">Durée du trajet</div>
                                <div class="info-value h5">
                                    {{ $reservation->programme->durer_parcours }} minutes
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="info-card">
                                <div class="info-label">Type de programme</div>
                                <div class="info-value h5">
                                    {{ ucfirst($reservation->programme->type_programme) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Places réservées -->
            <div class="card shadow mb-4">
                <div class="card-header py-3" style="border-bottom: 3px solid #10b981;">
                    <h6 class="m-0 font-weight-bold" style="color: #10b981;">
                        <i class="fas fa-chair me-2"></i> Places Réservées
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-3">
                        <div class="place-card">
                            <div class="place-number">{{ $reservation->seat_number }}</div>
                            <div class="place-label">Siège</div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="info-label">Nombre total de places</div>
                        <div class="info-value h4">1 place</div>
                    </div>
                </div>
            </div>

            <!-- Informations paiement -->
            <div class="card shadow mb-4">
                <div class="card-header py-3" style="border-bottom: 3px solid #3b82f6;">
                    <h6 class="m-0 font-weight-bold" style="color: #3b82f6;">
                        <i class="fas fa-money-bill-wave me-2"></i> Informations de Paiement
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="info-card">
                                <div class="info-label">Montant total</div>
                                <div class="info-value h3" style="color: #10b981;">
                                    {{ number_format($reservation->montant, 0, ',', ' ') }} FCFA
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="info-card">
                                <div class="info-label">Mode de paiement</div>
                                <div class="info-value h5">
                                    {{ $reservation->methode_paiement ?? 'Non spécifié' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne latérale -->
        <div class="col-lg-4">
            <!-- Carte statut -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold" style="color: #e94e1a;">
                        <i class="fas fa-info-circle me-2"></i> Statut de la Réservation
                    </h6>
                </div>
                <div class="card-body text-center">
                    @if($reservation->statut == 'confirmee')
                        <div class="status-confirmed mb-3">
                            <i class="fas fa-check-circle fa-4x text-warning"></i>
                        </div>
                        <h4 class="text-warning mb-2">Réservation Confirmée</h4>
                        <p class="text-muted">Votre réservation a été confirmée avec succès.</p>
                    @elseif($reservation->statut == 'terminee')
                        <div class="status-pending mb-3">
                            <i class="fas fa-clock fa-4x text-success"></i>
                        </div>
                        <h4 class="text-success mb-2">Réservation Terminée</h4>
                        <p class="text-muted">Votre réservation est terminée.</p>
                    @else
                        <div class="status-confirmed mb-3">
                            <i class="fas fa-check-circle fa-4x text-warning"></i>
                        </div>
                        <h4 class="text-warning mb-2">Réservation Confirmée</h4>
                        <p class="text-muted">Votre réservation a été confirmée avec succès.</p>
                    @endif

                    <div class="mt-4">
                        <div class="info-label mb-2">Date de création</div>
                        <div class="info-value">{{ $reservation->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    
                    @if($reservation->statut == 'annulee')
                        <div class="mt-3">
                            <div class="info-label mb-2">Date d'annulation</div>
                            <div class="info-value">{{ $reservation->annulation_date->format('d/m/Y H:i') ?? 'N/A' }}</div>
                        </div>
                        <div class="mt-3">
                            <div class="info-label mb-2">Raison d'annulation</div>
                            <div class="info-value">{{ $reservation->annulation_reason ?? 'Non spécifiée' }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Carte compagnie -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold" style="color: #e94e1a;">
                        <i class="fas fa-bus me-2"></i> Compagnie de Transport
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($reservation->programme->compagnie->logo)
                            <img src="{{ asset('storage/' . $reservation->programme->compagnie->logo) }}" 
                                 alt="{{ $reservation->programme->compagnie->name }}" 
                                 class="img-fluid mb-3" style="max-height: 80px;">
                        @else
                            <div class="company-logo-placeholder mb-3">
                                <i class="fas fa-bus fa-3x" style="color: #e94e1a;"></i>
                            </div>
                        @endif
                        <h5 class="mb-1">{{ $reservation->programme->compagnie->name }}</h5>
                        <p class="text-muted small">{{ $reservation->programme->compagnie->description ?? 'Transport routier' }}</p>
                    </div>
                    
                    <div class="mt-3">
                        <div class="info-label">Contact</div>
                        <div class="info-value">{{ $reservation->programme->compagnie->contact }}</div>
                    </div>
                    
                    <div class="mt-3">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $reservation->programme->compagnie->email }}</div>
                    </div>
                    
                    <div class="mt-3">
                        <div class="info-label">Agrément</div>
                        <div class="info-value">TR-{{ date('Y') }}-{{ $reservation->programme->compagnie->id }}</div>
                    </div>
                </div>
            </div>

            <!-- QR Code -->
            @if($reservation->qr_code_path)
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold" style="color: #e94e1a;">
                        <i class="fas fa-qrcode me-2"></i> QR Code d'Embarquement
                    </h6>
                </div>
                <div class="card-body text-center">
                    <img src="{{ asset('storage/' . $reservation->qr_code_path) }}" 
                         alt="QR Code" 
                         class="img-fluid mb-3"
                         style="max-width: 200px;">
                    
                    @if($reservation->embarquement_status == 'scanned')
                        <div class="alert alert-success py-2 small">
                            <i class="fas fa-check-circle me-1"></i>
                            Validé le {{ $reservation->embarquement_scanned_at->format('d/m/Y H:i') }}
                        </div>
                    @else
                        <p class="text-muted small">
                            Présentez ce code à l'embarquement pour validation
                        </p>
                    @endif
                    
                    <div class="mt-3">
                        <a href="{{ route('reservations.download', $reservation->id) }}" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download me-1"></i> Télécharger le QR Code
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
<style>
    .info-card {
        padding: 1rem;
        background: #f8fafc;
        border-radius: 8px;
        border-left: 4px solid #e94e1a;
    }

    .info-label {
        font-size: 0.75rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-weight: 600;
        color: #1e293b;
    }

    .place-card {
        width: 70px;
        height: 70px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
    }

    .place-number {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .place-label {
        font-size: 0.7rem;
        opacity: 0.9;
    }

    .company-logo-placeholder {
        width: 80px;
        height: 80px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(233, 78, 26, 0.1);
        border-radius: 10px;
    }

    .status-confirmed,
    .status-pending,
    .status-cancelled {
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection