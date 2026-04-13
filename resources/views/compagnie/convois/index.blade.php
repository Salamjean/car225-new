@extends('compagnie.layouts.template')

@section('page-title', 'Convois Reçus')
@section('page-subtitle', 'Demandes de convois envoyées par les utilisateurs')

@section('content')
    <div class="dashboard-page">
        <div class="premium-tabs mb-3">
            <a href="{{ route('compagnie.convois.index') }}" class="p-tab {{ ($statut ?? 'all') === 'all' ? 'active' : '' }}">Tous</a>
            <a href="{{ route('compagnie.convois.index', ['statut' => 'en_attente']) }}" class="p-tab {{ ($statut ?? 'all') === 'en_attente' ? 'active' : '' }}">En attente</a>
            <a href="{{ route('compagnie.convois.index', ['statut' => 'valide']) }}" class="p-tab {{ ($statut ?? 'all') === 'valide' ? 'active' : '' }}">Validés</a>
            <a href="{{ route('compagnie.convois.index', ['statut' => 'annule']) }}" class="p-tab {{ ($statut ?? 'all') === 'annule' ? 'active' : '' }}">Annulés</a>
        </div>

        <div class="dash-card">
            <div class="dash-card-head d-flex justify-content-between align-items-center">
                <h3 class="dash-card-title m-0">
                    <i class="fas fa-users text-orange mr-2"></i> Liste des convois
                </h3>
                <span class="badge badge-light px-3 py-2">{{ $convois->total() }} convoi(s)</span>
            </div>

            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Demandeur</th>
                            <th>Itinéraire</th>
                            <th>Gare</th>
                            <th class="text-center">Personnes</th>
                            <th class="text-center">Statut</th>
                            <th>Date</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($convois as $convoi)
                            <tr>
                                <td class="font-weight-bold">{{ $convoi->reference }}</td>
                                <td>
                                    {{ trim(($convoi->user->name ?? '') . ' ' . ($convoi->user->prenom ?? '')) ?: 'Utilisateur' }}
                                </td>
                                <td>
                                    {{ $convoi->itineraire ? ($convoi->itineraire->point_depart . ' -> ' . $convoi->itineraire->point_arrive) : '-' }}
                                </td>
                                <td>
                                    @if($convoi->gare)
                                        <span class="badge badge-info px-2 py-1" style="font-size:10px;">
                                            <i class="fas fa-building mr-1"></i>{{ $convoi->gare->nom_gare }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-warning px-3 py-2">{{ $convoi->passagers_count ?: $convoi->nombre_personnes }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($convoi->statut === 'en_attente')
                                        <span class="badge badge-secondary px-3 py-2">En attente</span>
                                    @elseif($convoi->statut === 'valide')
                                        <span class="badge badge-success px-3 py-2">Validé</span>
                                    @else
                                        <span class="badge badge-danger px-3 py-2">Annulé</span>
                                    @endif
                                </td>
                                <td>{{ $convoi->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-right">
                                    <a href="{{ route('compagnie.convois.show', $convoi->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-eye mr-1"></i> Voir
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">Aucun convoi reçu pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($convois->hasPages())
                <div class="p-3 border-top">
                    {{ $convois->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .premium-tabs {
            display: inline-flex;
            background: rgba(255, 255, 255, 0.6);
            padding: 5px;
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .p-tab {
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 800;
            color: #64748b;
            text-decoration: none !important;
        }
        .p-tab.active {
            background: #fff;
            color: #f97316;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
    </style>
@endsection

