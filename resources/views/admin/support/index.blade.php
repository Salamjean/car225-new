@extends('admin.layouts.template')

@section('content')
<div class="mdc-layout-grid">
    <div class="mdc-layout-grid__inner">
        <div class="mdc-layout-grid__cell stretch-card mdc-layout-grid__cell--span-12">
            <div class="mdc-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Support Client - Préoccupations des utilisateurs</h4>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Catégorie</th>
                                <th>Objet</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle mr-2" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-user text-muted" style="font-size: 14px;"></i>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold text-dark">{{ $request->user->name }}</div>
                                                <small class="text-muted">{{ $request->user->telephone }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $badges = [
                                                'bagage_perdu' => ['label' => 'Bagage Perdu', 'class' => 'badge-soft-danger'],
                                                'objet_oublie' => ['label' => 'Objet Oublié', 'class' => 'badge-soft-warning'],
                                                'remboursement' => ['label' => 'Remboursement', 'class' => 'badge-soft-success'],
                                                'qualite' => ['label' => 'Qualité Service', 'class' => 'badge-soft-purple'],
                                                'compte' => ['label' => 'Mon Compte', 'class' => 'badge-soft-info'],
                                                'autre' => ['label' => 'Autre', 'class' => 'badge-soft-secondary'],
                                            ];
                                            $badge = $badges[$request->type] ?? ['label' => $request->type, 'class' => 'badge-secondary'];
                                        @endphp
                                        <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                                    </td>
                                    <td>
                                        <span class="text-dark font-weight-medium">{{ Str::limit($request->objet, 40) }}</span>
                                    </td>
                                    <td>
                                        <div class="text-muted small">
                                            {{ $request->created_at->format('d/m/Y') }}<br>
                                            {{ $request->created_at->format('H:i') }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($request->statut == 'ouvert')
                                            <span class="badge badge-danger">Ouvert</span>
                                        @elseif($request->statut == 'en_cours')
                                            <span class="badge badge-warning">En cours</span>
                                        @else
                                            <span class="badge badge-success">Fermé</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.support.show', $request->id) }}" class="mdc-button mdc-button--raised mdc-button--dense">
                                            <i class="material-icons mdc-button__icon">visibility</i>
                                            Voir
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                            Aucune demande au support pour le moment.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $requests->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .badge-soft-danger { background-color: #fee2e2; color: #dc2626; }
    .badge-soft-warning { background-color: #fef3c7; color: #d97706; }
    .badge-soft-success { background-color: #dcfce7; color: #16a34a; }
    .badge-soft-purple { background-color: #f3e8ff; color: #9333ea; }
    .badge-soft-info { background-color: #e0f2fe; color: #0284c7; }
    .badge-soft-secondary { background-color: #f3f4f6; color: #4b5563; }
    .badge { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; }
    .table thead th { border-top: none; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; color: #9ca3af; }
    .card-title { font-weight: 800; color: #1a1d1f; }
</style>
@endsection
