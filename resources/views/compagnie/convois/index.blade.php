@extends('compagnie.layouts.template')

@section('page-title', 'Convois Reçus')
@section('page-subtitle', 'Demandes de convois envoyées par les utilisateurs')

@section('content')
    <div class="dashboard-page">

        {{-- Solde portefeuille convois --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="dash-card p-4" style="border-left: 4px solid #10b981;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Solde portefeuille convois</span>
                        <div style="width:36px;height:36px;border-radius:10px;background:#ECFDF5;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-wallet" style="color:#10b981;font-size:14px;"></i>
                        </div>
                    </div>
                    <div style="font-size:26px;font-weight:900;color:#065f46;letter-spacing:-0.5px;">
                        {{ number_format($soldeConvoie, 0, ',', ' ') }}
                        <span style="font-size:13px;font-weight:700;color:#6b7280;">FCFA</span>
                    </div>
                    <div class="mt-1" style="font-size:11px;color:#6b7280;font-weight:600;">
                        Cumul encaissé depuis l'ouverture
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dash-card p-4" style="border-left: 4px solid #f97316;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Total paiements reçus</span>
                        <div style="width:36px;height:36px;border-radius:10px;background:#FFF7ED;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-money-bill-wave" style="color:#f97316;font-size:14px;"></i>
                        </div>
                    </div>
                    <div style="font-size:26px;font-weight:900;color:#9a3412;letter-spacing:-0.5px;">
                        {{ number_format($totalPaye, 0, ',', ' ') }}
                        <span style="font-size:13px;font-weight:700;color:#6b7280;">FCFA</span>
                    </div>
                    <div class="mt-1" style="font-size:11px;color:#6b7280;font-weight:600;">
                        Convois payés + en cours + terminés
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dash-card p-4" style="border-left: 4px solid #6366f1;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Convois en attente de paiement</span>
                        <div style="width:36px;height:36px;border-radius:10px;background:#EEF2FF;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-hourglass-half" style="color:#6366f1;font-size:14px;"></i>
                        </div>
                    </div>
                    <div style="font-size:26px;font-weight:900;color:#3730a3;letter-spacing:-0.5px;">
                        {{ number_format(\App\Models\Convoi::where('compagnie_id', Auth::guard('compagnie')->id())->where('statut', 'valide')->sum('montant'), 0, ',', ' ') }}
                        <span style="font-size:13px;font-weight:700;color:#6b7280;">FCFA</span>
                    </div>
                    <div class="mt-1" style="font-size:11px;color:#6b7280;font-weight:600;">
                        Convois validés non encore payés
                    </div>
                </div>
            </div>
        </div>

        <div class="premium-tabs mb-3 flex-wrap" style="display:inline-flex;gap:4px;">
            <a href="{{ route('compagnie.convois.index') }}" class="p-tab {{ ($statut ?? 'all') === 'all' ? 'active' : '' }}">Tous</a>
            <a href="{{ route('compagnie.convois.index', ['statut' => 'en_attente']) }}" class="p-tab {{ ($statut ?? 'all') === 'en_attente' ? 'active' : '' }}">
                En attente
                @if ($enAttenteCount > 0)
                    <span class="badge badge-danger ml-1" style="font-size:10px;padding:2px 6px;">{{ $enAttenteCount }}</span>
                @endif
            </a>
            <a href="{{ route('compagnie.convois.index', ['statut' => 'valide']) }}" class="p-tab {{ ($statut ?? 'all') === 'valide' ? 'active' : '' }}">Validés</a>
            <a href="{{ route('compagnie.convois.index', ['statut' => 'refuse']) }}" class="p-tab {{ ($statut ?? 'all') === 'refuse' ? 'active' : '' }}">Refusés</a>
            <a href="{{ route('compagnie.convois.index', ['statut' => 'paye']) }}" class="p-tab {{ ($statut ?? 'all') === 'paye' ? 'active' : '' }}">Payés</a>
            <a href="{{ route('compagnie.convois.index', ['statut' => 'en_cours']) }}" class="p-tab {{ ($statut ?? 'all') === 'en_cours' ? 'active' : '' }}">En cours</a>
            <a href="{{ route('compagnie.convois.index', ['statut' => 'termine']) }}" class="p-tab {{ ($statut ?? 'all') === 'termine' ? 'active' : '' }}">Terminés</a>
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
                            <th>Départ</th>
                            <th class="text-center">Statut</th>
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
                                    {{ $convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? '-') }}
                                    @if($convoi->lieu_retour ?? $convoi->itineraire->point_arrive ?? null)
                                        → {{ $convoi->lieu_retour ?? $convoi->itineraire->point_arrive }}
                                    @endif
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
                                    <span class="badge badge-warning px-3 py-2">{{ $convoi->nombre_personnes }}</span>
                                </td>
                                <td>
                                    @if($convoi->date_depart)
                                        {{ \Carbon\Carbon::parse($convoi->date_depart)->format('d/m/Y') }}
                                        @if($convoi->heure_depart)
                                            <small class="text-muted">{{ $convoi->heure_depart }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php
                                        $sm = [
                                            'en_attente' => ['En attente',  'badge-warning'],
                                            'valide'     => ['Validé',      'badge-primary'],
                                            'refuse'     => ['Refusé',      'badge-danger'],
                                            'paye'       => ['Payé',        'badge-success'],
                                            'en_cours'   => ['En cours',    'badge-info'],
                                            'termine'    => ['Terminé',     'badge-secondary'],
                                            'annule'     => ['Annulé',      'badge-danger'],
                                        ];
                                        [$slabel, $sclass] = $sm[$convoi->statut] ?? [ucfirst($convoi->statut), 'badge-secondary'];
                                    @endphp
                                    <span class="badge {{ $sclass }} px-3 py-2">{{ $slabel }}</span>
                                </td>
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
            white-space: nowrap;
        }
        .p-tab.active {
            background: #fff;
            color: #f97316;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
    </style>
@endsection
