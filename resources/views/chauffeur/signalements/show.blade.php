@extends('chauffeur.layouts.template')

@section('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 1.5rem;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
    }
    
    .status-badge {
        padding: 0.5rem 1.5rem;
        border-radius: 2rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .status-nouveau { background: #e0f2fe; color: #0369a1; }
    .status-en_cours { background: #fef3c7; color: #92400e; }
    .status-traite { background: #dcfce7; color: #166534; }

    .info-box {
        background: #f8fafc;
        border-radius: 1rem;
        padding: 1.5rem;
        height: 100%;
    }

    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-weight: 600;
        color: #1e293b;
    }

    .accident-image {
        width: 100%;
        border-radius: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4 text-dark">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <div>
                    <a href="{{ route('chauffeur.signalements.index') }}" class="text-decoration-none text-muted">
                        <i class="fas fa-arrow-left me-2"></i> Retour à l'historique
                    </a>
                    <h2 class="fw-bold mt-3 mb-0">Détails du Signalement #{{ $signalement->id }}</h2>
                </div>
                <span class="status-badge status-{{ $signalement->statut }}">
                    {{ str_replace('_', ' ', $signalement->statut) }}
                </span>
            </div>

            <div class="row">
                <div class="col-md-7 mb-4">
                    <div class="glass-card p-4 h-100">
                        <h5 class="fw-bold mb-4">Description du Problème</h5>
                        <div class="mb-4">
                            <div class="type-badge mb-3 d-inline-block">
                                @if($signalement->type == 'accident') <span class="badge bg-danger p-2 px-3 rounded-pill"><i class="fas fa-car-crash me-2"></i> ACCIDENT</span>
                                @elseif($signalement->type == 'panne') <span class="badge bg-warning text-dark p-2 px-3 rounded-pill"><i class="fas fa-tools me-2"></i> PANNE</span>
                                @elseif($signalement->type == 'retard') <span class="badge bg-info p-2 px-3 rounded-pill"><i class="fas fa-clock me-2"></i> RETARD</span>
                                @else <span class="badge bg-secondary p-2 px-3 rounded-pill"><i class="fas fa-exclamation-triangle me-2"></i> AUTRE</span> @endif
                            </div>
                            <p class="fs-5 lh-base">{{ $signalement->description }}</p>
                        </div>

                        @if($signalement->photo_path)
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 font-semibold">Photo jointe</h6>
                            <img src="{{ asset($signalement->photo_path) }}" alt="Photo du signalement" class="accident-image">
                        </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="glass-card p-4 mb-4">
                        <h5 class="fw-bold mb-4">Informations Voyage</h5>
                        @if($signalement->voyage)
                        <div class="mb-3">
                            <div class="info-label">Trajet</div>
                            <div class="info-value">{{ $signalement->voyage->programme->point_depart }} → {{ $signalement->voyage->programme->point_arrive }}</div>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="info-label">Date</div>
                                <div class="info-value">{{ \Carbon\Carbon::parse($signalement->voyage->date_voyage)->format('d/m/Y') }}</div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="info-label">Départ</div>
                                <div class="info-value">{{ $signalement->voyage->programme->heure_depart }}</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="info-label">Véhicule</div>
                            <div class="info-value">{{ $signalement->vehicule->immatriculation ?? 'N/A' }} ({{ $signalement->vehicule->marque ?? '' }})</div>
                        </div>
                        @endif
                        <div class="mb-0">
                            <div class="info-label">Signalé le</div>
                            <div class="info-value">{{ $signalement->created_at->format('d/m/Y à H:i') }}</div>
                        </div>
                    </div>

                    {{-- Section Passagers à Bord --}}
                    @if($signalement->voyage)
                    @php
                        $scannedPassengers = $signalement->voyage->scanned_passengers;
                    @endphp
                    <div class="glass-card p-4 mb-4">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-users text-primary me-2"></i>Passagers à Bord 
                            <span class="badge bg-primary rounded-pill ms-1">{{ $scannedPassengers->count() }}</span>
                        </h5>
                        @if($scannedPassengers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="small text-center" style="width: 50px;"><i class="fas fa-chair text-muted"></i> Siège</th>
                                        <th class="small">Nom & Prénom</th>
                                        <th class="small">Téléphone</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($scannedPassengers as $passenger)
                                    <tr>
                                        <td class="fw-bold text-center">{{ $passenger->seat_number ?? '?' }}</td>
                                        <td class="small fw-medium">
                                            {{ trim(($passenger->passager_nom ?? '') . ' ' . ($passenger->passager_prenom ?? '')) ?: ($passenger->user->name ?? 'Inconnu') }}
                                        </td>
                                        <td class="small text-muted">{{ $passenger->passager_telephone ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-user-slash fa-2x mb-2 opacity-50"></i>
                            <p class="small mb-0">Aucun passager scanné pour ce voyage.</p>
                        </div>
                        @endif
                    </div>
                    @endif
                    @if($signalement->latitude && $signalement->longitude)
                    <div class="glass-card p-4">
                        <h5 class="fw-bold mb-3">Localisation</h5>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-danger-subtle text-danger p-3 rounded-circle me-3">
                                <i class="fas fa-map-marker-alt fa-lg"></i>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $signalement->latitude }}, {{ $signalement->longitude }}</div>
                                <div class="text-muted small">Coordonnées GPS transmises</div>
                            </div>
                        </div>
                        <a href="https://www.google.com/maps?q={{ $signalement->latitude }},{{ $signalement->longitude }}" target="_blank" class="btn btn-outline-dark w-100 rounded-pill">
                            <i class="fas fa-external-link-alt me-2"></i> Voir sur Google Maps
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
