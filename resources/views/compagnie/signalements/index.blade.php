@extends('compagnie.layouts.template')

@section('page-title', 'Gestion des Signalements')
@section('page-subtitle', 'Suivez et traitez les incidents signalés')

@section('styles')
<style>
    .search-filter-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 20px; margin-bottom: 24px; box-shadow: var(--shadow-sm);
    }
    
    .input-modern {
        width: 100%; padding: 10px 14px; border: 1px solid var(--border-strong);
        border-radius: var(--radius-sm); font-size: 13px; font-weight: 600;
        background: var(--surface-2); color: var(--text-1); transition: 0.2s; height: 42px;
    }
    .input-modern:focus { outline: none; border-color: var(--orange); background: var(--surface); box-shadow: 0 0 0 3px var(--orange-light); }
    .filter-label { display: block; font-size: 10px; font-weight: 800; color: var(--text-3); text-transform: uppercase; margin-bottom: 8px; }

    .btn-action {
        width: 32px; height: 32px; border-radius: 8px; border: none; display: inline-flex; align-items: center; justify-content: center;
        font-size: 13px; transition: 0.2s; cursor: pointer; text-decoration: none;
    }
    .btn-action.view { background: #F0F9FF; color: #0EA5E9; }
    .btn-action.view:hover { background: #0EA5E9; color: white; }
    
    .pulse-alert { animation: soft-pulse 2s infinite; border: 1px solid #EF4444; color: #EF4444; background: #FEF2F2; }
    @keyframes soft-pulse {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    .unread-row { background: rgba(239, 68, 68, 0.03); }

    .badge-status { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; }
    .b-success { background: #DCFCE7; color: #16A34A; }
    .b-danger { background: #FEF2F2; color: #DC2626; }
    .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    .type-box { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: var(--text-1); }
    .reporter-box { display: flex; align-items: center; gap: 10px; }
    .avatar-mini { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; }
    .avatar-mini.blue { background: #DBEAFE; color: #1E40AF; }
    .avatar-mini.purple { background: #F3E8FF; color: #6B21A8; }
</style>
@endsection

@section('content')
<div class="dashboard-page">

    {{-- STATS ROW --}}
    <div class="metric-grid mb-4">
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-rose"><i class="fas fa-bell"></i></div>
                <span class="metric-tag mt-rose">Alertes</span>
            </div>
            <div class="metric-label">Nouveaux</div>
            <div class="metric-value" style="color: var(--red);">{{ $stats['nouveaux'] }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-green"><i class="fas fa-check-circle"></i></div>
                <span class="metric-tag mt-green">Résolus</span>
            </div>
            <div class="metric-label">Traités</div>
            <div class="metric-value">{{ $stats['traites'] }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-blue"><i class="fas fa-id-badge"></i></div>
                <span class="metric-tag mt-blue">Staff</span>
            </div>
            <div class="metric-label">Signalés par Chauffeurs</div>
            <div class="metric-value">{{ $stats['from_chauffeurs'] }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon" style="background: #F3E8FF; color: #7E22CE;"><i class="fas fa-user-friends"></i></div>
                <span class="metric-tag" style="background: #F3E8FF; color: #7E22CE;">Clients</span>
            </div>
            <div class="metric-label">Signalés par Passagers</div>
            <div class="metric-value">{{ $stats['from_users'] }}</div>
        </div>
    </div>

    {{-- FILTRES CORRIGÉS --}}
    <div class="search-filter-card">
        <form method="GET" action="{{ route('compagnie.signalements.index') }}">
            <div class="row align-items-end">
                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                    <label class="filter-label">Source du signalement</label>
                    <select name="source" class="input-modern" style="appearance: auto;">
                        <option value="">Toutes les sources</option>
                        <option value="chauffeur" {{ request('source') == 'chauffeur' ? 'selected' : '' }}>Chauffeurs</option>
                        <option value="utilisateur" {{ request('source') == 'utilisateur' ? 'selected' : '' }}>Passagers</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                    <label class="filter-label">Type d'incident</label>
                    <select name="type" class="input-modern" style="appearance: auto;">
                        <option value="">Tous les types</option>
                        <option value="accident" {{ request('type') == 'accident' ? 'selected' : '' }}>Accident</option>
                        <option value="panne" {{ request('type') == 'panne' ? 'selected' : '' }}>Panne</option>
                        <option value="retard" {{ request('type') == 'retard' ? 'selected' : '' }}>Retard</option>
                        <option value="comportement" {{ request('type') == 'comportement' ? 'selected' : '' }}>Comportement</option>
                        <option value="autre" {{ request('type') == 'autre' ? 'selected' : '' }}>Autre</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 mb-3 mb-md-0">
                    <label class="filter-label">Statut</label>
                    <select name="statut" class="input-modern" style="appearance: auto;">
                        <option value="">Tous les statuts</option>
                        <option value="nouveau" {{ request('statut') == 'nouveau' ? 'selected' : '' }}>Nouveau</option>
                        <option value="traite" {{ request('statut') == 'traite' ? 'selected' : '' }}>Traité</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="filter-label d-none d-lg-block">&nbsp;</label> <div style="display: flex; gap: 8px;">
                        <a href="{{ route('compagnie.signalements.index') }}" class="btn btn-light" style="flex: 1; font-weight: 700; font-size: 13px; height: 42px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border-strong);">Réinitialiser</a>
                        <button type="submit" class="btn btn-primary" style="flex: 1; background: var(--orange); border: none; font-weight: 700; font-size: 13px; height: 42px; display: flex; align-items: center; justify-content: center;">Filtrer</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="dash-card">
        <div class="dash-card-head" style="background: var(--surface-2);">
            <div class="dash-card-head-left">
                <div class="dash-card-icon" style="background: var(--red); color: white;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <span class="dash-card-title">Liste des signalements</span>
            </div>
        </div>

        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Source</th>
                        <th>Type</th>
                        <th>Date & Heure</th>
                        <th>Véhicule & Trajet</th>
                        <th>Signalé par</th>
                        <th class="text-center">Statut</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($signalements as $signalement)
                    @php
                        $isChauffeur = $signalement->personnel_id && !$signalement->user_id;
                        $isUser = (bool) $signalement->user_id;
                        
                        if ($isChauffeur && $signalement->personnel) {
                            $reporterName = $signalement->personnel->name . ' ' . ($signalement->personnel->prenom ?? '');
                            $reporterInitial = strtoupper(substr($signalement->personnel->name, 0, 1));
                        } elseif ($isUser && $signalement->user) {
                            $reporterName = $signalement->user->name . ' ' . ($signalement->user->prenom ?? '');
                            $reporterInitial = strtoupper(substr($signalement->user->name, 0, 1));
                        } else {
                            $reporterName = 'Inconnu'; $reporterInitial = '?';
                        }

                        $typeIcons = [
                            'accident' => ['i' => 'fa-car-crash', 'c' => 'color: var(--red);'],
                            'panne' => ['i' => 'fa-tools', 'c' => 'color: var(--orange);'],
                            'retard' => ['i' => 'fa-clock', 'c' => 'color: #D97706;'],
                            'comportement' => ['i' => 'fa-user-slash', 'c' => 'color: #7E22CE;'],
                            'autre' => ['i' => 'fa-info-circle', 'c' => 'color: var(--text-2);'],
                        ];
                        $ti = $typeIcons[$signalement->type] ?? $typeIcons['autre'];
                    @endphp
                    <tr class="{{ !$signalement->is_read_by_company ? 'unread-row' : '' }}">
                        <td>
                            @if($isChauffeur)
                                <span class="metric-tag mt-blue"><i class="fas fa-id-badge mr-1"></i> Chauffeur</span>
                            @else
                                <span class="metric-tag" style="background: #F3E8FF; color: #7E22CE;"><i class="fas fa-user mr-1"></i> Passager</span>
                            @endif
                        </td>
                        <td>
                            <div class="type-box">
                                <i class="fas {{ $ti['i'] }}" style="{{ $ti['c'] }}"></i>
                                <span>{{ ucfirst($signalement->type) }}</span>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 700; font-size: 13px; color: var(--text-1);">{{ $signalement->created_at->format('d/m/Y') }}</div>
                            <div style="font-size: 11px; font-weight: 600; color: var(--text-3);">{{ $signalement->created_at->format('H:i') }}</div>
                        </td>
                        <td>
                            <div style="font-family: monospace; font-size: 12px; font-weight: 800; color: var(--text-1); background: var(--surface-2); border: 1px solid var(--border-strong); padding: 2px 6px; border-radius: 4px; display: inline-block; margin-bottom: 4px;">
                                <i class="fas fa-bus mr-1 text-muted"></i> {{ $signalement->vehicule?->immatriculation ?? $signalement->programme?->vehicule?->immatriculation ?? '---' }}
                            </div>
                            <div style="font-size: 11px; font-weight: 600; color: var(--text-2);">
                                {{ $signalement->programme?->point_depart ?? '?' }} <i class="fas fa-arrow-right text-muted mx-1" style="font-size: 9px;"></i> {{ $signalement->programme?->point_arrive ?? '?' }}
                            </div>
                        </td>
                        <td>
                            <div class="reporter-box">
                                <div class="avatar-mini {{ $isChauffeur ? 'blue' : 'purple' }}">{{ $reporterInitial }}</div>
                                <span style="font-size: 12px; font-weight: 700; color: var(--text-1);">{{ $reporterName }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($signalement->statut === 'traite')
                                <span class="badge-status b-success"><span class="dot"></span> Traité</span>
                            @else
                                <span class="badge-status b-danger"><span class="dot"></span> Nouveau</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('compagnie.signalements.show', $signalement->id) }}" 
                               class="btn-action view {{ !$signalement->is_read_by_company ? 'pulse-alert' : '' }}" 
                               onclick="if(!{{ $signalement->is_read_by_company ? 'true' : 'false' }}) { markReadOptimistic(); }"
                               title="Détails">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="table-empty py-5">
                                <i class="fas fa-check-circle table-empty-icon mb-3" style="font-size: 40px; color: var(--emerald);"></i>
                                <h3 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin: 0;">Tout est calme</h3>
                                <p style="font-size: 12px; color: var(--text-3); font-weight: 600;">Aucun signalement en attente de traitement.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($signalements->hasPages())
        <div class="p-3 border-top">
            {{ $signalements->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</div>

<script>
    function markReadOptimistic() {
        const badge = document.querySelector('.topbar-notif-dot'); 
        if (badge) {
            badge.style.display = 'none';
        }
        return true; 
    }
</script>
@endsection