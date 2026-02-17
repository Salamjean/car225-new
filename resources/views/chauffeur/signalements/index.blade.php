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
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-nouveau { background: #e0f2fe; color: #0369a1; }
    .status-en_cours { background: #fef3c7; color: #92400e; }
    .status-traite { background: #dcfce7; color: #166534; }
    
    .type-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        margin-right: 15px;
    }
    
    .type-accident { background: #fee2e2; color: #dc2626; }
    .type-panne { background: #ffedd5; color: #ea580c; }
    .type-retard { background: #fef9c3; color: #ca8a04; }
    .type-autre { background: #f3f4f6; color: #4b5563; }
    
    .signalement-row {
        transition: all 0.3s ease;
        border-radius: 1rem;
        margin-bottom: 0.75rem;
    }
    
    .signalement-row:hover {
        transform: translateY(-2px);
        background: #f8fafc;
    }

    .btn-premium {
        background: linear-gradient(135deg, #FF4B2B, #FF416C);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(255, 75, 43, 0.3);
        color: white;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold mb-1">Mes Signalements</h2>
            <p class="text-muted small">Historique de vos rapports de problèmes en route</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('chauffeur.signalements.create') }}" class="btn btn-premium">
                <i class="fas fa-plus"></i> Nouveau Signalement
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 1rem;">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
    </div>
    @endif

    <div class="glass-card p-4">
        <div class="table-responsive">
            <table class="table table-borderless align-middle">
                <thead>
                    <tr class="text-muted small">
                        <th>TYPE</th>
                        <th>VOYAGE / TRAJET</th>
                        <th>DATE & HEURE</th>
                        <th>STATUT</th>
                        <th class="text-end">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($signalements as $s)
                    <tr class="signalement-row">
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="type-icon type-{{ $s->type }}">
                                    @if($s->type == 'accident') <i class="fas fa-car-crash"></i>
                                    @elseif($s->type == 'panne') <i class="fas fa-tools"></i>
                                    @elseif($s->type == 'retard') <i class="fas fa-clock"></i>
                                    @else <i class="fas fa-exclamation-triangle"></i> @endif
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ ucfirst($s->type) }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($s->voyage)
                            <div class="fw-medium text-dark">
                                {{ $s->voyage->programme->point_depart }} → {{ $s->voyage->programme->point_arrive }}
                            </div>
                            <div class="text-muted small">Passagers: {{ $s->voyage->programme->nbre_siege_occupe ?? 'N/A' }}</div>
                            @else
                            <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-medium">{{ $s->created_at->format('d/m/Y') }}</div>
                            <div class="text-muted small">{{ $s->created_at->format('H:i') }}</div>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $s->statut }}">
                                {{ str_replace('_', ' ', $s->statut) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('chauffeur.signalements.show', $s) }}" class="btn btn-light btn-sm rounded-pill px-3">
                                <i class="fas fa-eye"></i> Détails
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="opacity-50 mb-3">
                                <i class="fas fa-clipboard-list fa-3x"></i>
                            </div>
                            <h5 class="text-muted">Aucun signalement enregistré</h5>
                            <p class="text-muted small">Utilisez le bouton ci-dessus pour signaler un problème.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
