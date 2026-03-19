@extends('compagnie.layouts.template')

@section('page-title', 'Détails du Signalement')
@section('page-subtitle', 'Analyse et prise de décision suite à un incident')

@section('styles')
<style>
    .details-container { max-width: 1100px; margin: 0 auto; }

    /* Header de l'incident */
    .incident-header {
        background: linear-gradient(135deg, var(--red) 0%, #9F1239 100%);
        border-radius: var(--radius); padding: 24px 32px; color: white; margin-bottom: 24px;
        position: relative; overflow: hidden; box-shadow: var(--shadow-md);
        display: flex; flex-direction: column; justify-content: center; min-height: 140px;
    }
    .incident-header .bg-icon {
        position: absolute; right: 20px; top: 50%; transform: translateY(-50%);
        font-size: 100px; color: rgba(255,255,255,0.1); pointer-events: none;
    }
    .incident-title { font-size: 22px; font-weight: 800; margin: 12px 0 8px; line-height: 1.2; }
    .incident-meta { font-size: 12px; font-weight: 600; opacity: 0.9; margin: 0; }

    .tag-white { background: white; color: var(--red); padding: 4px 12px; border-radius: 20px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
    .tag-trans { background: rgba(255,255,255,0.2); color: white; padding: 4px 12px; border-radius: 20px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
    .tag-green { background: var(--emerald); color: white; padding: 4px 12px; border-radius: 20px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }

    /* Blocs d'informations */
    .info-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 20px; margin-bottom: 20px; box-shadow: var(--shadow-sm);
    }
    .info-card-title {
        font-size: 11px; font-weight: 800; color: var(--text-3);
        text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px;
        display: flex; align-items: center; gap: 8px;
    }
    
    .desc-box { background: var(--surface-2); padding: 16px; border-radius: var(--radius-sm); border: 1px solid var(--border); font-size: 14px; color: var(--text-1); font-style: italic; line-height: 1.6; }
    
    .photo-box { border-radius: var(--radius-sm); overflow: hidden; border: 4px solid var(--surface-2); cursor: pointer; transition: 0.2s; display: block; text-align: center; background: #000; }
    .photo-box:hover { border-color: var(--red); }
    .photo-box img { max-width: 100%; max-height: 350px; object-fit: contain; }

    /* Sidebar Mini-cards */
    .mini-card { background: var(--surface-2); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 16px; margin-bottom: 16px; }
    .mini-card h4 { font-size: 10px; font-weight: 800; color: var(--text-3); text-transform: uppercase; margin-bottom: 12px; margin-top: 0; }
    
    .avatar-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 800; flex-shrink: 0; }
    
    /* Boutons logistiques */
    .btn-logistic {
        width: 100%; padding: 14px; border-radius: var(--radius-sm); font-weight: 800; font-size: 12px;
        text-transform: uppercase; letter-spacing: 0.5px; border: none; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-logistic.red { background: var(--red); color: white; box-shadow: 0 4px 15px rgba(239,68,68,0.3); }
    .btn-logistic.red:hover { background: #B91C1C; transform: translateY(-2px); }
    .btn-logistic.green { background: var(--emerald); color: white; box-shadow: 0 4px 15px rgba(16,185,129,0.3); }
    .btn-logistic.green:hover { background: #047857; transform: translateY(-2px); }
    .btn-logistic.blue { background: var(--blue); color: white; box-shadow: 0 4px 15px rgba(59,130,246,0.3); }
    .btn-logistic.blue:hover { background: #1D4ED8; transform: translateY(-2px); }
    .btn-logistic.dark { background: var(--text-1); color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
    .btn-logistic.dark:hover { background: black; transform: translateY(-2px); }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <div class="details-container">

        {{-- Messages --}}
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center" style="border-radius: var(--radius-sm); font-weight: 600; font-size: 13px;">
                <i class="fas fa-check-circle mr-2" style="font-size: 18px;"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center" style="border-radius: var(--radius-sm); font-weight: 600; font-size: 13px;">
                <i class="fas fa-exclamation-circle mr-2" style="font-size: 18px;"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Actions Top --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <a href="{{ route('compagnie.signalements.index') }}" class="btn btn-light btn-sm" style="font-weight: 700; font-size: 12px; border-radius: var(--radius-sm);">
                <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
            </a>
            @if($signalement->statut !== 'traite')
                <form action="{{ route('compagnie.signalements.mark-traite', $signalement->id) }}" method="POST" class="m-0">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success btn-sm" style="background: var(--emerald); border: none; font-weight: 700; border-radius: var(--radius-sm); padding: 8px 16px;">
                        <i class="fas fa-check-circle mr-1"></i> Marquer comme traité
                    </button>
                </form>
            @endif
        </div>

        {{-- HEADER INCIDENT --}}
        <div class="incident-header">
            <i class="fas fa-exclamation-triangle bg-icon"></i>
            <div style="position: relative; z-index: 2;">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <span class="tag-trans">ID #{{ $signalement->id }}</span>
                    <span class="tag-white">{{ $signalement->type }}</span>
                    @if($signalement->statut === 'traite')
                        <span class="tag-green"><i class="fas fa-check mr-1"></i> Traité</span>
                    @endif
                </div>
                <h1 class="incident-title">Détails du Signalement</h1>
                <p class="incident-meta"><i class="far fa-calendar-alt mr-1"></i> Signalé le {{ $signalement->created_at->format('d/m/Y à H:i') }}</p>
            </div>
        </div>

        <div class="row">
            {{-- COLONNE GAUCHE : DÉTAILS --}}
            <div class="col-lg-8">
                
                {{-- Description --}}
                <div class="info-card">
                    <h3 class="info-card-title"><i class="fas fa-align-left" style="color: var(--red);"></i> Description du problème</h3>
                    <div class="desc-box">
                        "{{ $signalement->description }}"
                    </div>
                </div>

                {{-- Photo --}}
                @if($signalement->photo_path)
                <div class="info-card">
                    <h3 class="info-card-title"><i class="fas fa-camera" style="color: var(--red);"></i> Preuve Visuelle</h3>
                    <a href="{{ asset($signalement->photo_path) }}" target="_blank" class="photo-box">
                        <img src="{{ asset($signalement->photo_path) }}" alt="Photo de l'incident" onerror="this.onerror=null; this.src='{{ $signalement->photo_path }}';">
                    </a>
                </div>
                @endif

                {{-- Localisation GPS --}}
                @if($signalement->latitude && $signalement->longitude)
                <div class="info-card">
                    <h3 class="info-card-title"><i class="fas fa-map-marker-alt" style="color: var(--red);"></i> Lieu de l'incident</h3>
                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between" style="background: #EFF6FF; border: 1px solid #BFDBFE; padding: 20px; border-radius: var(--radius-sm); gap: 16px;">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 40px; height: 40px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--blue); font-size: 16px; box-shadow: var(--shadow-sm); flex-shrink: 0;">
                                <i class="fas fa-location-arrow"></i>
                            </div>
                            <div>
                                <p style="font-size: 10px; font-weight: 800; color: var(--blue); text-transform: uppercase; margin: 0;">Adresse approximative</p>
                                <p id="address-display" style="font-size: 13px; font-weight: 800; color: var(--text-1); margin: 4px 0;"><i class="fas fa-spinner fa-spin mr-2"></i>Chargement...</p>
                                <p style="font-size: 11px; color: var(--text-3); font-family: monospace; margin: 0;">GPS: {{ $signalement->latitude }}, {{ $signalement->longitude }}</p>
                            </div>
                        </div>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $signalement->latitude }},{{ $signalement->longitude }}" target="_blank" class="btn btn-primary btn-sm" style="background: white; color: var(--blue); border: none; font-weight: 700; box-shadow: var(--shadow-sm); white-space: nowrap;">
                            <i class="fas fa-external-link-alt mr-1"></i> Ouvrir GPS
                        </a>
                    </div>
                </div>
                
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const lat = {{ $signalement->latitude }};
                        const lon = {{ $signalement->longitude }};
                        const display = document.getElementById('address-display');
                        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                            .then(res => res.json())
                            .then(data => { display.innerText = (data && data.display_name) ? data.display_name : "Adresse introuvable"; })
                            .catch(err => { display.innerText = "Erreur de récupération"; });
                    });
                </script>
                @endif

                {{-- LOGISTIQUE / DÉCISIONS (Seulement si c'est un signalement chauffeur) --}}
                @if($signalement->statut !== 'traite' && !$signalement->user_id)
                <div class="info-card" style="border: 2px solid var(--orange-mid);">
                    <h3 class="info-card-title" style="color: var(--text-1); font-size: 14px;"><i class="fas fa-gavel" style="color: var(--orange);"></i> Décision & Logistique</h3>
                    
                    {{-- ACCIDENT --}}
                    @if($signalement->type === 'accident')
                        <div style="background: #FEF2F2; border: 1px solid #FCA5A5; padding: 16px; border-radius: var(--radius-sm); margin-bottom: 16px;">
                            <h4 style="font-size: 12px; font-weight: 800; color: #B91C1C; text-transform: uppercase; margin-bottom: 8px;"><i class="fas fa-exclamation-circle"></i> Interruption requise</h4>
                            <p style="font-size: 12px; color: #DC2626; margin-bottom: 16px; font-weight: 600;">Cette action marquera le voyage comme interrompu. Le véhicule et le chauffeur seront immobilisés.</p>
                            <form action="{{ route('compagnie.signalements.interrupt', $signalement->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-logistic red" onclick="return confirm('Êtes-vous sûr de vouloir interrompre ce voyage ?')">
                                    <i class="fas fa-times-circle"></i> Confirmer l'interruption
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- PANNE --}}
                    @if($signalement->type === 'panne')
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div style="background: #ECFDF5; border: 1px solid #A7F3D0; padding: 16px; border-radius: var(--radius-sm); height: 100%;">
                                    <h4 style="font-size: 11px; font-weight: 800; color: #047857; text-transform: uppercase; margin-bottom: 8px;">Panne réparée</h4>
                                    <p style="font-size: 11px; color: #059669; margin-bottom: 16px; font-weight: 600;">Le car peut reprendre la route.</p>
                                    <form action="{{ route('compagnie.signalements.resume', $signalement->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-logistic green"><i class="fas fa-play"></i> Reprendre la route</button>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="background: #EFF6FF; border: 1px solid #BFDBFE; padding: 16px; border-radius: var(--radius-sm); height: 100%;">
                                    <h4 style="font-size: 11px; font-weight: 800; color: #1D4ED8; text-transform: uppercase; margin-bottom: 8px;">Transbordement</h4>
                                    <p style="font-size: 11px; color: #2563EB; margin-bottom: 12px; font-weight: 600;">Envoyez un autre véhicule.</p>
                                    <form action="{{ route('compagnie.signalements.transbordement', $signalement->id) }}" method="POST">
                                        @csrf
                                        <select name="new_vehicule_id" required class="form-control form-control-sm mb-3" style="font-size: 12px; font-weight: 700; height: 38px;">
                                            <option value="" disabled selected>Choisir un car...</option>
                                            @forelse($availableVehicles ?? [] as $v)
                                                <option value="{{ $v->id }}">{{ $v->immatriculation }} ({{ $v->nombre_place }} pl.)</option>
                                            @empty
                                                <option value="" disabled>Aucun car dispo</option>
                                            @endforelse
                                        </select>
                                        <button type="submit" class="btn-logistic blue"><i class="fas fa-exchange-alt"></i> Assigner</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 text-center">
                            <form action="{{ route('compagnie.signalements.interrupt', $signalement->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-link text-danger" style="font-size: 11px; font-weight: 800; text-transform: uppercase; text-decoration: underline;">
                                    Ou annuler définitivement le voyage
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- AUTRE (Default) --}}
                    @if(!in_array($signalement->type, ['accident', 'panne']))
                        <form action="{{ route('compagnie.signalements.mark-traite', $signalement->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-logistic dark"><i class="fas fa-check-double"></i> Marquer comme incident résolu</button>
                        </form>
                    @endif
                </div>
                @endif
            </div>

            {{-- COLONNE DROITE : SIDEBAR --}}
            <div class="col-lg-4">
                
                {{-- Signalé par --}}
                <div class="mini-card">
                    <h4>Signalé par</h4>
                    @php
                        $isChauffeur = $signalement->personnel_id && !$signalement->user_id;
                        if ($isChauffeur && $signalement->personnel) {
                            $rName = $signalement->personnel->name . ' ' . ($signalement->personnel->prenom ?? '');
                            $rContact = $signalement->personnel->contact ?? 'Sans numéro';
                            $rInitial = strtoupper(substr($signalement->personnel->name, 0, 1));
                            $rType = 'Chauffeur';
                            $avStyle = 'background: #DBEAFE; color: #1E40AF;';
                        } elseif ($signalement->user) {
                            $rName = $signalement->user->name . ' ' . ($signalement->user->prenom ?? '');
                            $rContact = $signalement->user->contact ?? $signalement->user->telephone ?? 'Sans numéro';
                            $rInitial = strtoupper(substr($signalement->user->name, 0, 1));
                            $rType = 'Passager';
                            $avStyle = 'background: #F3E8FF; color: #6B21A8;';
                        } else {
                            $rName = 'Inconnu'; $rContact = 'Sans numéro'; $rInitial = '?'; $rType = 'Inconnu';
                            $avStyle = 'background: var(--border-strong); color: var(--text-2);';
                        }
                    @endphp
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-icon" style="{{ $avStyle }}">{{ $rInitial }}</div>
                        <div>
                            <div style="font-weight: 800; font-size: 13px; color: var(--text-1);">{{ trim($rName) }}</div>
                            <div style="font-size: 11px; font-weight: 600; color: var(--text-3);">{{ $rContact }}</div>
                        </div>
                    </div>
                    <span style="font-size: 10px; font-weight: 800; text-transform: uppercase; border: 1px solid var(--border-strong); padding: 2px 8px; border-radius: 4px; display: inline-block;">{{ $rType }}</span>
                </div>

                {{-- Véhicule & Trajet --}}
                <div class="mini-card">
                    <h4>Véhicule & Trajet</h4>
                    <div class="d-flex align-items-start gap-2 mb-3">
                        <i class="fas fa-bus text-muted mt-1"></i>
                        <div>
                            <div style="font-weight: 800; font-size: 13px; color: var(--text-1);">{{ $signalement->vehicule?->immatriculation ?? $signalement->programme?->vehicule?->immatriculation ?? 'Non assigné' }}</div>
                            <div style="font-size: 11px; color: var(--text-3); font-weight: 600;">{{ $signalement->vehicule?->marque ?? $signalement->programme?->vehicule?->marque ?? '' }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-route text-muted mt-1"></i>
                        <div>
                            <div style="font-weight: 800; font-size: 12px; color: var(--text-1);">
                                {{ $signalement->programme?->point_depart ?? '?' }} <i class="fas fa-arrow-right text-muted mx-1" style="font-size: 9px;"></i> {{ $signalement->programme?->point_arrive ?? '?' }}
                            </div>
                            <div style="font-size: 10px; color: var(--text-3); font-weight: 600; margin-top: 2px;">Programme #{{ $signalement->programme_id }}</div>
                        </div>
                    </div>
                </div>

                {{-- Sapeur Pompier (Si accident) --}}
                @if($signalement->type === 'accident' && $signalement->sapeurPompier)
                <div class="mini-card" style="background: #FEF2F2; border-color: #FCA5A5;">
                    <h4 style="color: #B91C1C;"><i class="fas fa-fire-extinguisher"></i> Sapeur Pompier assigné</h4>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="avatar-icon" style="background: white; color: #DC2626; border: 1px solid #FCA5A5; width: 36px; height: 36px; font-size: 14px;"><i class="fas fa-fire-alt"></i></div>
                        <div>
                            <div style="font-weight: 800; font-size: 13px; color: var(--text-1);">{{ $signalement->sapeurPompier->name }}</div>
                            <div style="font-size: 11px; color: var(--text-3); font-weight: 600;">{{ $signalement->sapeurPompier->commune }}</div>
                        </div>
                    </div>
                    <div style="font-size: 11px; font-weight: 600; color: var(--text-2); margin-top: 8px;">
                        <i class="fas fa-phone mr-1"></i> {{ $signalement->sapeurPompier->contact ?? 'Non renseigné' }}
                    </div>
                </div>
                @endif

                {{-- Gare de départ --}}
                @if(isset($gareDepart) && $gareDepart)
                <div class="mini-card" style="background: #EFF6FF; border-color: #BFDBFE;">
                    <h4 style="color: #1D4ED8;"><i class="fas fa-warehouse"></i> Gare concernée</h4>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="avatar-icon" style="background: white; color: #2563EB; border: 1px solid #BFDBFE; width: 36px; height: 36px; font-size: 14px;"><i class="fas fa-building"></i></div>
                        <div>
                            <div style="font-weight: 800; font-size: 13px; color: var(--text-1);">{{ $gareDepart->nom_gare }}</div>
                            <div style="font-size: 11px; color: var(--text-3); font-weight: 600;">{{ $gareDepart->ville }}</div>
                        </div>
                    </div>
                    
                    @if($signalement->type === 'accident' || $signalement->user_id)
                    <form action="{{ route('compagnie.signalements.alert-gare', $signalement->id) }}" method="POST" class="mt-3">
                        @csrf
                        <textarea name="message" rows="2" class="form-control form-control-sm mb-2" style="font-size: 11px; border-color: #BFDBFE;" placeholder="Message à la gare (optionnel)"></textarea>
                        <button type="submit" class="btn btn-primary btn-sm w-100" style="font-size: 10px; font-weight: 800; text-transform: uppercase;">
                            <i class="fas fa-paper-plane mr-1"></i> Alerter la gare
                        </button>
                    </form>
                    @endif
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection