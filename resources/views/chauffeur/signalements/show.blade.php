@extends('chauffeur.layouts.template')

@section('styles')
<style>
    .sig-page { background: linear-gradient(135deg, #f8fafc 0%, #fff1f2 100%); min-height: 80vh; }

    .detail-card { background: white; border-radius: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden; }

    .detail-header { background: linear-gradient(135deg, #1e293b, #334155); color: white; padding: 1.5rem 2rem; position: relative; overflow: hidden; }
    .detail-header::before { content: ''; position: absolute; top: -40%; right: -15%; width: 200px; height: 200px; background: radial-gradient(circle, rgba(255,75,43,0.2) 0%, transparent 70%); border-radius: 50%; }

    .type-hero { display: inline-flex; align-items: center; gap: 8px; padding: 8px 20px; border-radius: 100px; font-weight: 700; font-size: 0.85rem; }
    .type-hero.accident { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #991b1b; }
    .type-hero.panne { background: linear-gradient(135deg, #ffedd5, #fed7aa); color: #9a3412; }
    .type-hero.retard { background: linear-gradient(135deg, #fef9c3, #fde68a); color: #854d0e; }
    .type-hero.comportement { background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #3730a3; }
    .type-hero.autre { background: linear-gradient(135deg, #f3f4f6, #e5e7eb); color: #374151; }

    .status-pill { padding: 6px 16px; border-radius: 100px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .status-pill.nouveau { background: #dbeafe; color: #1e40af; }
    .status-pill.en_cours { background: #fef3c7; color: #92400e; }
    .status-pill.traite { background: #dcfce7; color: #166534; }

    .info-tile { background: #f8fafc; border-radius: 1rem; padding: 1rem; border: 1px solid #f1f5f9; transition: all 0.2s; }
    .info-tile:hover { background: #f1f5f9; }
    .info-tile .label { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8; font-weight: 700; margin-bottom: 4px; }
    .info-tile .value { font-weight: 700; color: #1e293b; font-size: 0.9rem; }

    .section-title { font-size: 0.95rem; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 8px; margin-bottom: 1rem; }
    .section-title i { color: #FF4B2B; }

    .photo-frame { border-radius: 1rem; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
    .photo-frame img { width: 100%; display: block; object-fit: cover; max-height: 350px; }

    .passenger-table { border-radius: 0.75rem; overflow: hidden; }
    .passenger-table thead th { background: #f8fafc; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 700; border-bottom: 2px solid #e2e8f0; padding: 0.75rem; }
    .passenger-table tbody td { padding: 0.6rem 0.75rem; font-size: 0.85rem; border-bottom: 1px solid #f1f5f9; }
    .passenger-table tbody tr:hover { background: #f8fafc; }

    .gps-card { background: linear-gradient(135deg, #f0f9ff, #e0f2fe); border: 1px solid #bae6fd; border-radius: 1rem; }
    .map-btn { background: linear-gradient(135deg, #1e293b, #334155); color: white; border: none; border-radius: 100px; padding: 0.6rem 1.5rem; font-weight: 600; transition: all 0.3s; text-decoration: none; }
    .map-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.2); color:white; }
</style>
@endsection

@section('content')
<div class="sig-page py-4">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">

                {{-- Back Link --}}
                <a href="{{ route('chauffeur.signalements.index') }}" class="text-decoration-none text-muted d-inline-flex align-items-center gap-2 mb-3 small fw-medium">
                    <i class="fas fa-arrow-left"></i> Retour à l'historique
                </a>

                {{-- Header Card --}}
                <div class="detail-card mb-4">
                    <div class="detail-header">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <h4 class="fw-bold mb-1">Signalement #{{ $signalement->id }}</h4>
                                <div class="opacity-75 small">
                                    <i class="far fa-clock me-1"></i>Enregistré le {{ $signalement->created_at->format('d/m/Y à H:i') }}
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="type-hero {{ $signalement->type }}">
                                    @if($signalement->type == 'accident') <i class="fas fa-car-crash"></i>
                                    @elseif($signalement->type == 'panne') <i class="fas fa-tools"></i>
                                    @elseif($signalement->type == 'retard') <i class="fas fa-clock"></i>
                                    @elseif($signalement->type == 'comportement') <i class="fas fa-user-slash"></i>
                                    @else <i class="fas fa-exclamation-triangle"></i> @endif
                                    {{ ucfirst($signalement->type) }}
                                </span>
                                <span class="status-pill {{ $signalement->statut }}">{{ str_replace('_', ' ', $signalement->statut) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    {{-- Colonne Gauche --}}
                    <div class="col-lg-7">

                        {{-- Description --}}
                        <div class="detail-card p-4 mb-4">
                            <div class="section-title"><i class="fas fa-align-left"></i> Description de l'incident</div>
                            <p class="mb-0 lh-lg" style="font-size: 1rem; color: #334155;">{{ $signalement->description }}</p>
                        </div>

                        {{-- Photo --}}
                        @if($signalement->photo_path)
                        <div class="detail-card p-4 mb-4">
                            <div class="section-title"><i class="fas fa-camera"></i> Photo jointe</div>
                            <div class="photo-frame">
                                <img src="{{ asset($signalement->photo_path) }}" alt="Photo du signalement">
                            </div>
                        </div>
                        @endif

                        {{-- Passagers à Bord (Voyage) --}}
                        @if($signalement->voyage)
                        @php
                            $scannedPassengers = $signalement->voyage->scanned_passengers;
                        @endphp
                        <div class="detail-card p-4 mb-4">
                            <div class="section-title">
                                <i class="fas fa-users"></i> Passagers à Bord
                                <span class="badge bg-primary rounded-pill ms-1" style="font-size: 0.7rem;">{{ $scannedPassengers->count() }}</span>
                            </div>
                            @if($scannedPassengers->count() > 0)
                            <div class="table-responsive">
                                <table class="table passenger-table mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 60px;">Siège</th>
                                            <th>Nom & Prénom</th>
                                            <th>Téléphone</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($scannedPassengers as $passenger)
                                        <tr>
                                            <td class="text-center fw-bold">{{ $passenger->seat_number ?? '?' }}</td>
                                            <td class="fw-medium">
                                                {{ trim(($passenger->passager_nom ?? '') . ' ' . ($passenger->passager_prenom ?? '')) ?: ($passenger->user->name ?? 'Inconnu') }}
                                            </td>
                                            <td class="text-muted">{{ $passenger->passager_telephone ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-user-slash fa-2x mb-2 opacity-25"></i>
                                <p class="small mb-0">Aucun passager scanné pour ce voyage.</p>
                            </div>
                            @endif
                        </div>
                        @endif

                        {{-- Passagers Convoi (non-garant uniquement) --}}
                        @if($signalement->convoi && !$signalement->convoi->is_garant && $signalement->convoi->passagers->count() > 0)
                        <div class="detail-card p-4 mb-4">
                            <div class="section-title">
                                <i class="fas fa-users"></i> Passagers du Convoi
                                <span class="badge bg-primary rounded-pill ms-1" style="font-size: 0.7rem;">{{ $signalement->convoi->passagers->count() }}</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table passenger-table mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 50px;">#</th>
                                            <th>Nom & Prénom</th>
                                            <th>Téléphone</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($signalement->convoi->passagers as $i => $cp)
                                        <tr>
                                            <td class="text-center fw-bold">{{ $i + 1 }}</td>
                                            <td class="fw-medium">{{ trim(($cp->prenoms ?? '') . ' ' . ($cp->nom ?? '')) ?: 'Passager '.($i+1) }}</td>
                                            <td class="text-muted">{{ $cp->contact ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                    </div>

                    {{-- Colonne Droite --}}
                    <div class="col-lg-5">

                        {{-- Informations Voyage --}}
                        @if($signalement->voyage)
                        <div class="detail-card p-4 mb-4">
                            <div class="section-title"><i class="fas fa-route"></i> Informations Voyage</div>
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="info-tile">
                                        <div class="label">Trajet</div>
                                        <div class="value">{{ $signalement->voyage->programme->point_depart ?? '' }} → {{ $signalement->voyage->programme->point_arrive ?? '' }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="info-tile">
                                        <div class="label">Date</div>
                                        <div class="value">{{ \Carbon\Carbon::parse($signalement->voyage->date_voyage)->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="info-tile">
                                        <div class="label">Heure départ</div>
                                        <div class="value">{{ $signalement->voyage->programme->heure_depart ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="info-tile">
                                        <div class="label">Véhicule</div>
                                        <div class="value">{{ $signalement->vehicule->immatriculation ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="info-tile">
                                        <div class="label">Marque</div>
                                        <div class="value">{{ $signalement->vehicule->marque ?? '-' }}</div>
                                    </div>
                                </div>
                                @if($signalement->voyage->gareDepart)
                                <div class="col-12">
                                    <div class="info-tile">
                                        <div class="label">Gare de départ</div>
                                        <div class="value"><i class="fas fa-building text-primary me-1" style="font-size: 0.8rem;"></i>{{ $signalement->voyage->gareDepart->nom_gare }}</div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- Informations Convoi --}}
                        @if($signalement->convoi)
                        @php
                            $c   = $signalement->convoi;
                            $veh = $signalement->vehicule ?? $c->vehicule;
                        @endphp
                        <div class="detail-card p-4 mb-4">
                            <div class="section-title"><i class="fas fa-route"></i> Informations Convoi</div>
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="info-tile">
                                        <div class="label">Trajet</div>
                                        <div class="value">
                                            @if($c->itineraire)
                                                {{ $c->itineraire->point_depart }} → {{ $c->itineraire->point_arrive }}
                                            @elseif($c->lieu_depart)
                                                {{ $c->lieu_depart }} → {{ $c->lieu_retour ?? '...' }}
                                            @else
                                                Convoi
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="info-tile">
                                        <div class="label">Date départ</div>
                                        <div class="value">{{ $c->date_depart ? \Carbon\Carbon::parse($c->date_depart)->format('d/m/Y') : 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="info-tile">
                                        <div class="label">Heure départ</div>
                                        <div class="value">{{ $c->heure_depart ? substr($c->heure_depart, 0, 5) : 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="info-tile">
                                        <div class="label">Véhicule</div>
                                        <div class="value">{{ $veh->immatriculation ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="info-tile">
                                        <div class="label">Passagers</div>
                                        <div class="value">{{ $c->nombre_personnes ?? '-' }} pers.</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="info-tile">
                                        <div class="label">Référence</div>
                                        <div class="value" style="font-size: 0.82rem; color: #6b7280; font-family: monospace;">{{ $c->reference }}</div>
                                    </div>
                                </div>
                                @if($c->gare)
                                <div class="col-12">
                                    <div class="info-tile">
                                        <div class="label">Gare</div>
                                        <div class="value"><i class="fas fa-building text-primary me-1" style="font-size: 0.8rem;"></i>{{ $c->gare->nom_gare }}</div>
                                    </div>
                                </div>
                                @endif
                                @if($c->date_retour)
                                <div class="col-12">
                                    <div class="info-tile" style="background: #f5f3ff; border-color: #c4b5fd;">
                                        <div class="label" style="color: #7c3aed;">Retour prévu</div>
                                        <div class="value" style="color: #6d28d9;">
                                            {{ \Carbon\Carbon::parse($c->date_retour)->format('d/m/Y') }}
                                            @if($c->heure_retour) à {{ substr($c->heure_retour, 0, 5) }} @endif
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- Localisation GPS --}}
                        @if($signalement->latitude && $signalement->longitude)
                        <div class="detail-card mb-4">
                            <div class="gps-card p-4">
                                <div class="section-title"><i class="fas fa-map-marker-alt"></i> Localisation GPS</div>
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                        <i class="fas fa-crosshairs text-danger fa-lg"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="font-size: 0.9rem;">{{ number_format($signalement->latitude, 6) }}, {{ number_format($signalement->longitude, 6) }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Coordonnées GPS au moment du signalement</div>
                                    </div>
                                </div>
                                <a href="https://www.google.com/maps?q={{ $signalement->latitude }},{{ $signalement->longitude }}" target="_blank" class="map-btn d-flex align-items-center justify-content-center gap-2 w-100">
                                    <i class="fas fa-external-link-alt"></i> Ouvrir dans Google Maps
                                </a>
                            </div>
                        </div>
                        @endif

                        {{-- Compagnie --}}
                        @if($signalement->compagnie)
                        <div class="detail-card p-4">
                            <div class="section-title"><i class="fas fa-building"></i> Compagnie notifiée</div>
                            <div class="info-tile">
                                <div class="label">Nom</div>
                                <div class="value">{{ $signalement->compagnie->name ?? $signalement->compagnie->nom ?? 'N/A' }}</div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
