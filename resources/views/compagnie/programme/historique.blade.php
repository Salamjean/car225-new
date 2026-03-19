@extends('compagnie.layouts.template')

@section('page-title', 'Archives & Historique')
@section('page-subtitle', 'Consultez les programmes terminés et le journal des modifications')

@section('content')
<div class="dashboard-page">
    
    {{-- SECTION 1 : Programmes Terminés --}}
    <div class="dash-header" style="margin-bottom: 16px;">
        <div class="d-flex align-items-center" style="gap: 12px;">
            <div class="dash-card-icon" style="background: var(--surface-2); border: 1px solid var(--border-strong);">
                <i class="fas fa-archive text-muted"></i>
            </div>
            <div>
                <h2 class="dash-title" style="font-size: 18px;">Archives des Programmes</h2>
                <p class="dash-subtitle mt-0">{{ $programmesExpires->total() }} Programme(s) terminé(s)</p>
            </div>
        </div>
        <a href="{{ route('programme.index') }}" class="btn btn-light btn-sm" style="font-weight: 700; border-radius: var(--radius-sm);">
            <i class="fas fa-arrow-left mr-1"></i> Retour aux actifs
        </a>
    </div>

    <div class="dash-card mb-5">
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Trajet</th>
                        <th>Type d'opération</th>
                        <th>Dates & Horaires</th>
                        <th>Véhicule</th>
                        <th class="text-right">État</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programmesExpires as $prog)
                    <tr>
                        <td>
                            <div style="font-weight: 800; color: var(--text-1); font-size: 13px;">{{ $prog->point_depart }}</div>
                            <div style="font-size: 10px; color: var(--text-3); margin: 2px 0;"><i class="fas fa-arrow-down"></i></div>
                            <div style="font-weight: 800; color: var(--text-1); font-size: 13px;">{{ $prog->point_arrive }}</div>
                        </td>
                        <td>
                            @if($prog->type_programmation == 'ponctuel')
                                <span class="metric-tag mt-slate"><i class="fas fa-calendar-day mr-1"></i> Ponctuel</span>
                            @else
                                <span class="metric-tag" style="background: #F3E8FF; color: #7E22CE;"><i class="fas fa-redo mr-1"></i> Récurrent</span>
                            @endif
                            @if($prog->is_aller_retour)
                                <span class="metric-tag" style="background: #E0E7FF; color: #4338CA; display: block; width: max-content; margin-top: 4px;">Aller-Retour</span>
                            @endif
                        </td>
                        <td>
                            @if($prog->type_programmation == 'ponctuel')
                                <div style="font-weight: 700; color: var(--text-2); font-size: 12px;">{{ \Carbon\Carbon::parse($prog->date_depart)->format('d/m/Y') }}</div>
                                <div style="font-size: 11px; font-weight: 600; color: var(--text-3);">{{ $prog->heure_depart }} — {{ $prog->heure_arrive }}</div>
                            @else
                                <div style="font-weight: 800; color: var(--text-3); font-size: 10px; text-transform: uppercase;">Du {{ \Carbon\Carbon::parse($prog->date_depart)->format('d/m/Y') }}</div>
                                <div style="font-weight: 800; color: var(--orange); font-size: 10px; text-transform: uppercase;">Au {{ \Carbon\Carbon::parse($prog->date_fin_programmation)->format('d/m/Y') }}</div>
                            @endif
                        </td>
                        <td>
                            @if($prog->vehicule)
                                <div style="font-weight: 800; font-size: 12px; color: var(--text-1);">{{ $prog->vehicule->marque }} {{ $prog->vehicule->modele }}</div>
                                <div style="font-size: 10px; font-weight: 700; color: var(--text-3); text-transform: uppercase;">{{ $prog->vehicule->immatriculation }}</div>
                            @else
                                <span style="font-size: 11px; font-weight: 600; color: var(--text-3); font-style: italic;">Véhicule supprimé</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <span class="metric-tag mt-slate"><i class="fas fa-circle mr-1" style="font-size: 6px;"></i> Archive</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="table-empty">
                                <i class="fas fa-box-open table-empty-icon opacity-50"></i>
                                <p>Aucun programme archivé</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($programmesExpires->hasPages())
        <div class="p-3 border-top">
            {{ $programmesExpires->appends(['logs_page' => request('logs_page')])->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>

    {{-- SECTION 2 : Journal d'activité --}}
    <div class="dash-header" style="margin-bottom: 16px;">
        <div class="d-flex align-items-center" style="gap: 12px;">
            <div class="dash-card-icon dci-blue">
                <i class="fas fa-history"></i>
            </div>
            <div>
                <h2 class="dash-title" style="font-size: 18px;">Journal d'activité</h2>
                <p class="dash-subtitle mt-0">Suivi des modifications en temps réel</p>
            </div>
        </div>
    </div>

    <div class="dash-card border-primary">
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead style="background: #EFF6FF;">
                    <tr>
                        <th style="color: #1D4ED8;">Date & Heure</th>
                        <th style="color: #1D4ED8;">Programme Concerné</th>
                        <th style="color: #1D4ED8;">Nature de l'Action</th>
                        <th style="color: #1D4ED8; min-width: 300px;">Détails & Justification</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>
                            <div style="font-weight: 800; font-size: 12px; color: var(--text-1);">{{ $log->created_at->format('d/m/Y') }}</div>
                            <div style="font-weight: 700; font-size: 11px; color: var(--blue);">{{ $log->created_at->format('H:i') }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 800; font-size: 12px; color: var(--text-1);">{{ $log->itineraire }}</div>
                            <div style="font-weight: 700; font-size: 10px; color: var(--text-3); text-transform: uppercase;">Départ le {{ $log->date_depart }} ({{ $log->heure_depart }})</div>
                        </td>
                        <td>
                            @if($log->action == 'change_chauffeur')
                                <span class="metric-tag mt-amber">Chauffeur</span>
                            @elseif($log->action == 'change_vehicule')
                                <span class="metric-tag mt-green">Véhicule</span>
                            @else
                                <span class="metric-tag mt-slate">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="max-width: 400px;">
                                <p style="font-size: 12px; font-weight: 600; color: var(--text-2); margin-bottom: 8px;">{{ $log->raison }}</p>
                                <div style="background: var(--surface-2); padding: 10px; border-radius: 8px; border: 1px solid var(--border); display: flex; gap: 16px;">
                                    <div style="flex: 1;">
                                        <span style="display: block; font-size: 9px; font-weight: 800; color: var(--text-3); text-transform: uppercase;">Nouv. Véhicule</span>
                                        <span style="font-size: 11px; font-weight: 700; color: var(--text-1);">{{ Str::limit($log->vehicule, 20) }}</span>
                                    </div>
                                    <div style="width: 1px; background: var(--border-strong);"></div>
                                    <div style="flex: 1;">
                                        <span style="display: block; font-size: 9px; font-weight: 800; color: var(--text-3); text-transform: uppercase;">Nouv. Chauffeur</span>
                                        <span style="font-size: 11px; font-weight: 700; color: var(--text-1);">{{ Str::limit($log->chauffeur, 20) }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">
                            <div class="table-empty">
                                <i class="fas fa-stream table-empty-icon opacity-50"></i>
                                <p>Aucune activité enregistrée</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="p-3 border-top" style="background: #EFF6FF;">
            {{ $logs->appends(['prog_page' => request('prog_page')])->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>

</div>

@if (session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Succès!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#F97316',
        timer: 3000,
        customClass: { popup: 'rounded-3xl border-0 shadow-sm' }
    });
</script>
@endif

@if (session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: '{{ session('error') }}',
        confirmButtonColor: '#EF4444',
        customClass: { popup: 'rounded-3xl border-0 shadow-sm' }
    });
</script>
@endif
@endsection