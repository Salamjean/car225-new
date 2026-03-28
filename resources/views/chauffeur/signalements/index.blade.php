@extends('chauffeur.layouts.template')

@section('styles')
<style>
    .sig-page { background: linear-gradient(135deg, #f8fafc 0%, #fff1f2 100%); min-height: 80vh; }
    .sig-header { background: linear-gradient(135deg, #1e293b, #334155); border-radius: 1.25rem; padding: 1.25rem; color: white; position: relative; overflow: hidden; }
    @media (min-width: 768px) { .sig-header { padding: 2rem; } }
    .sig-header::before { content: ''; position: absolute; top: -50%; right: -20%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,75,43,0.15) 0%, transparent 70%); border-radius: 50%; }
    .sig-card { background: white; border-radius: 1.25rem; border: 1px solid #e2e8f0; transition: all 0.3s cubic-bezier(.4,0,.2,1); overflow: hidden; }
    .sig-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); border-color: #cbd5e1; }
    .type-pill { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 100px; font-size: 0.8rem; font-weight: 700; }
    .type-accident { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #991b1b; }
    .type-panne { background: linear-gradient(135deg, #ffedd5, #fed7aa); color: #9a3412; }
    .type-retard { background: linear-gradient(135deg, #fef9c3, #fde68a); color: #854d0e; }
    .type-comportement { background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #3730a3; }
    .type-autre { background: linear-gradient(135deg, #f3f4f6, #e5e7eb); color: #374151; }
    .stat-badge { background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 1rem; padding: 1rem; text-align: center; }
    .stat-badge .num { font-size: 1.75rem; font-weight: 800; line-height: 1; }
    .stat-badge .label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; margin-top: 4px; }
    .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
    .status-dot.nouveau { background: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.2); }
    .status-dot.en_cours { background: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.2); }
    .status-dot.traite { background: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,0.2); }
    .btn-create { background: linear-gradient(135deg, #FF4B2B, #FF416C); color: white; border: none; padding: 0.7rem 1.5rem; border-radius: 100px; font-weight: 700; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; font-size: 0.9rem; }
    .btn-create:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255,75,43,0.35); color: white; }
    .passengers-badge { cursor: pointer; transition: all 0.2s; }
    .passengers-badge:hover { background: #dbeafe !important; border-color: #93c5fd !important; }
    .empty-state { padding: 4rem 2rem; text-align: center; }
    .empty-state i { font-size: 3.5rem; color: #cbd5e1; margin-bottom: 1rem; }
</style>
@endsection

@section('content')
<div class="sig-page py-4">
    <div class="container-fluid">

        {{-- Header avec statistiques --}}
        <div class="sig-header mb-4">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <h2 class="fw-bold mb-1" style="font-size: 1.6rem;"><i class="fas fa-shield-alt me-2 opacity-75"></i>Mes Signalements</h2>
                    <p class="mb-0 opacity-75 small">Historique de vos rapports d'incidents en route</p>
                </div>
                <div class="col-md-6">
                    <div class="d-flex gap-2 justify-content-md-end">
                        @php
                            $totalCount = $signalements->count();
                            $nouveauCount = $signalements->where('statut', 'nouveau')->count();
                            $traiteCount = $signalements->where('statut', 'traite')->count();
                        @endphp
                        <div class="stat-badge flex-fill">
                            <div class="num">{{ $totalCount }}</div>
                            <div class="label">Total</div>
                        </div>
                        <div class="stat-badge flex-fill">
                            <div class="num text-info">{{ $nouveauCount }}</div>
                            <div class="label">En attente</div>
                        </div>
                        <div class="stat-badge flex-fill">
                            <div class="num text-success">{{ $traiteCount }}</div>
                            <div class="label">Traités</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bouton Nouveau --}}
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('chauffeur.signalements.create') }}" class="btn-create">
                <i class="fas fa-plus"></i> Nouveau Signalement
            </a>
        </div>

        @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center gap-2">
            <i class="fas fa-check-circle text-success"></i> {{ session('success') }}
        </div>
        @endif

        {{-- Liste des signalements --}}
        @forelse($signalements as $s)
        @php
            $passengersData = [];
            if($s->voyage) {
                foreach($s->voyage->scanned_passengers as $p) {
                    $passengersData[] = [
                        'name' => trim(($p->passager_nom ?? '') . ' ' . ($p->passager_prenom ?? '')) ?: ($p->user->name ?? 'Inconnu'),
                        'seat' => $p->seat_number ?? '?',
                        'contact' => $p->passager_telephone ?? '-'
                    ];
                }
            }
        @endphp
        <div class="sig-card mb-3">
            <div class="row g-0 align-items-start align-items-md-center">
                {{-- Type + Description --}}
                <div class="col-12 col-md-5 p-3 ps-3 ps-md-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="mt-1">
                            <span class="type-pill type-{{ $s->type }}">
                                @if($s->type == 'accident') <i class="fas fa-car-crash"></i>
                                @elseif($s->type == 'panne') <i class="fas fa-tools"></i>
                                @elseif($s->type == 'retard') <i class="fas fa-clock"></i>
                                @elseif($s->type == 'comportement') <i class="fas fa-user-slash"></i>
                                @else <i class="fas fa-exclamation-triangle"></i> @endif
                                {{ ucfirst($s->type) }}
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark small text-truncate" style="max-width: 250px;" title="{{ $s->description }}">{{ Str::limit($s->description, 50) }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                <i class="far fa-clock me-1"></i>{{ $s->created_at->format('d/m/Y à H:i') }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Trajet + Gare --}}
                <div class="col-12 col-md-3 p-3 border-top border-md-top-0 border-start-md border-end-md">
                    @if($s->voyage && $s->voyage->programme)
                    <div class="fw-bold text-dark small">{{ $s->voyage->programme->point_depart ?? '' }} → {{ $s->voyage->programme->point_arrive ?? '' }}</div>
                    <div class="text-muted" style="font-size: 0.7rem;">
                        <i class="fas fa-building me-1"></i>{{ $s->voyage->gareDepart->nom_gare ?? 'N/A' }}
                    </div>
                    <div class="d-flex gap-1 mt-1 flex-wrap">
                        <span class="badge bg-light text-dark border passengers-badge" style="font-size: 0.65rem;" onclick="showPassengers(this)" data-passengers="{{ htmlspecialchars(json_encode($passengersData)) }}">
                            <i class="fas fa-users text-primary me-1"></i>{{ count($passengersData) }} passagers
                        </span>
                        @if($s->vehicule)
                        <span class="badge bg-light text-dark border" style="font-size: 0.65rem;">
                            <i class="fas fa-bus text-secondary me-1"></i>{{ $s->vehicule->immatriculation }}
                        </span>
                        @endif
                    </div>
                    @else
                    <span class="text-muted small">N/A</span>
                    @endif
                </div>

                {{-- Statut --}}
                <div class="col-6 col-md-2 p-3 text-center">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <span class="status-dot {{ $s->statut }}"></span>
                        <span class="fw-bold small text-uppercase" style="letter-spacing: 0.5px;">{{ str_replace('_', ' ', $s->statut) }}</span>
                    </div>
                </div>

                {{-- Action --}}
                <div class="col-6 col-md-2 p-3 text-end pe-3 pe-md-4">
                    <a href="{{ route('chauffeur.signalements.show', $s) }}" class="btn btn-sm btn-dark rounded-pill px-3">
                        <i class="fas fa-eye me-1"></i> Voir
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="sig-card">
            <div class="empty-state">
                <i class="fas fa-clipboard-check d-block"></i>
                <h5 class="fw-bold text-muted mb-2">Aucun signalement</h5>
                <p class="text-muted small mb-3">Vous n'avez fait aucun signalement pour le moment.</p>
                <a href="{{ route('chauffeur.signalements.create') }}" class="btn-create btn-sm">
                    <i class="fas fa-plus"></i> Créer un signalement
                </a>
            </div>
        </div>
        @endforelse

    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function showPassengers(btn) {
    const data = JSON.parse(btn.getAttribute('data-passengers') || '[]');
    if (data.length === 0) {
        Swal.fire({ icon: 'info', title: 'Aucun passager', text: 'Aucun passager scanné pour ce voyage.', confirmButtonColor: '#1e293b' });
        return;
    }
    let rows = '';
    data.forEach((p, i) => {
        rows += `<tr><td class="text-center fw-bold">${p.seat}</td><td>${p.name}</td><td class="text-muted">${p.contact}</td></tr>`;
    });
    Swal.fire({
        title: '<i class="fas fa-users text-primary me-2"></i>Passagers à bord',
        html: `<div class="table-responsive"><table class="table table-sm table-bordered text-start align-middle mt-3"><thead class="table-light"><tr><th class="text-center" style="width:60px">Siège</th><th>Nom</th><th>Contact</th></tr></thead><tbody>${rows}</tbody></table></div>`,
        width: '550px',
        confirmButtonColor: '#1e293b',
        confirmButtonText: 'Fermer',
        customClass: { htmlContainer: 'text-start m-0' }
    });
}
</script>
@endsection
