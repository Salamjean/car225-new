@extends('admin.layouts.template')

@section('content')
<div class="mdc-layout-grid">
    <div class="mdc-layout-grid__inner">
        <div class="mdc-layout-grid__cell stretch-card mdc-layout-grid__cell--span-8">
            <div class="mdc-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Détails de la demande</h4>
                    <a href="{{ route('admin.support.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>

                <div class="mb-4 p-3 bg-light rounded">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted d-block uppercase font-weight-bold" style="font-size: 10px; letter-spacing: 1px;">Objet</small>
                            <h5 class="text-dark font-weight-bold">{{ $supportRequest->objet }}</h5>
                        </div>
                        <div class="col-md-6 text-right">
                            <small class="text-muted d-block uppercase font-weight-bold" style="font-size: 10px; letter-spacing: 1px;">Catégorie</small>
                            <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $supportRequest->type)) }}</span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <small class="text-muted d-block uppercase font-weight-bold mb-2" style="font-size: 10px; letter-spacing: 1px;">Description du problème</small>
                    <div class="p-4 border rounded bg-white text-dark shadow-sm" style="line-height: 1.6;">
                        {{ $supportRequest->description }}
                    </div>
                </div>

                @if($supportRequest->reponse)
                    <div class="mb-4">
                        <small class="text-muted d-block uppercase font-weight-bold mb-2 text-primary" style="font-size: 10px; letter-spacing: 1px;">Votre réponse précédente</small>
                        <div class="p-4 border border-primary rounded bg-primary-subtle text-dark shadow-sm" style="line-height: 1.6; background-color: #f0f7ff;">
                            {{ $supportRequest->reponse }}
                        </div>
                    </div>
                @endif

                @if($supportRequest->statut != 'ferme')
                    <div class="mt-4 pt-4 border-top">
                        <h6 class="font-weight-bold mb-3">Répondre à l'utilisateur</h6>
                        <form action="{{ route('admin.support.repondre', $supportRequest->id) }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <textarea name="reponse" class="form-control" rows="6" placeholder="Saisissez votre réponse ici..." required></textarea>
                            </div>
                            <button type="submit" class="mdc-button mdc-button--raised w-100">
                                <i class="material-icons mdc-button__icon">send</i>
                                Envoyer la réponse
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <div class="mdc-layout-grid__cell stretch-card mdc-layout-grid__cell--span-4">
            <div class="mdc-card">
                <h6 class="card-title mb-4">Informations Complémentaires</h6>
                
                <div class="mb-4 pb-3 border-bottom">
                    <small class="text-muted d-block mb-1">Utilisateur</small>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle fa-2x text-muted mr-3"></i>
                        <div>
                            <div class="font-weight-bold">{{ $supportRequest->user->name }}</div>
                            <div class="small text-muted">{{ $supportRequest->user->telephone }}</div>
                        </div>
                    </div>
                </div>

                @if($supportRequest->reservation)
                    <div class="mb-4 pb-3 border-bottom text-dark">
                        <small class="text-muted d-block mb-2">Voyage concerné</small>
                        <div class="p-2 bg-light rounded small">
                            <strong>{{ $supportRequest->reservation->programme->itineraire->point_depart }} &rarr; {{ $supportRequest->reservation->programme->itineraire->point_arrive }}</strong><br>
                            Date : {{ \Carbon\Carbon::parse($supportRequest->reservation->date_voyage)->format('d/m/Y') }}<br>
                            Réf : {{ $supportRequest->reservation->reference }}
                        </div>
                    </div>
                @endif

                <div class="mb-4">
                    <small class="text-muted d-block mb-2">Statut de la demande</small>
                    <form action="{{ route('admin.support.statut', $supportRequest->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <select name="statut" class="form-control mb-3" onchange="this.form.submit()">
                            <option value="ouvert" {{ $supportRequest->statut == 'ouvert' ? 'selected' : '' }}>Ouvert / Nouveau</option>
                            <option value="en_cours" {{ $supportRequest->statut == 'en_cours' ? 'selected' : '' }}>En cours de traitement</option>
                            <option value="ferme" {{ $supportRequest->statut == 'ferme' ? 'selected' : '' }}>Fermé / Résolu</option>
                        </select>
                    </form>
                </div>

                <div class="text-muted small">
                    Créé le : {{ $supportRequest->created_at->format('d/m/Y à H:i') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
