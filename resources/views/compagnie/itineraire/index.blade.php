@extends('compagnie.layouts.template')

@section('page-title', 'Gestion des Itinéraires')
@section('page-subtitle', 'Définissez vos routes et trajets interurbains')

@section('styles')
<style>
    .search-box { position: relative; max-width: 350px; width: 100%; }
    .search-box i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-3); font-size: 13px; }
    .search-box input {
        width: 100%; padding: 10px 16px 10px 40px;
        border: 1px solid var(--border); border-radius: var(--radius-sm);
        font-size: 13px; background: var(--surface); color: var(--text-1); transition: 0.2s;
    }
    .search-box input:focus { outline: none; border-color: var(--orange); box-shadow: 0 0 0 3px var(--orange-light); }

    .itinerary-main { display: flex; align-items: center; gap: 12px; }
    .itinerary-icon-box {
        width: 36px; height: 36px; border-radius: 10px; background: var(--orange-light); color: var(--orange);
        display: flex; align-items: center; justify-content: center; font-size: 16px; border: 1px solid var(--orange-mid);
    }
    .itinerary-path { display: flex; align-items: center; gap: 8px; }
    .path-start, .path-end { font-size: 13px; font-weight: 800; color: var(--text-1); }
    .path-arrow { color: var(--text-3); font-size: 12px; }

    .date-info { display: flex; flex-direction: column; gap: 2px; }
    .date-val { font-size: 13px; font-weight: 700; color: var(--text-1); }
    .time-val { font-size: 11px; color: var(--text-3); font-weight: 600; }

    .btn-action {
        width: 32px; height: 32px; border-radius: 8px; border: none; display: inline-flex; align-items: center; justify-content: center;
        font-size: 13px; transition: all 0.2s; cursor: pointer; text-decoration: none;
    }
    .btn-action.view { background: #F0F9FF; color: #0EA5E9; }
    .btn-action.edit { background: #EFF6FF; color: #2563EB; }
    .btn-action.delete { background: #FEF2F2; color: #DC2626; }
    .btn-action:hover { transform: translateY(-2px); filter: brightness(0.95); text-decoration: none; }
</style>
@endsection

@section('content')
<div class="dashboard-page">

    {{-- ACTION HEADER --}}
    <div class="dash-card mb-4 p-3 d-flex flex-wrap align-items-center justify-content-between" style="gap: 16px;">
        <form method="GET" action="{{ route('itineraire.index') }}" class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" name="search" value="{{ request('search') }}" id="searchInput" placeholder="Rechercher un trajet...">
        </form>
        
        <a href="{{ route('itineraire.create') }}" class="btn btn-primary" style="background: var(--orange); border: none; font-weight: 700; border-radius: var(--radius-sm); font-size: 13px;">
            <i class="fas fa-plus mr-2"></i> Nouvel Itinéraire
        </a>
    </div>

    {{-- TABLE --}}
    <div class="dash-card">
        <div class="dash-card-head" style="background: var(--surface-2);">
            <div class="dash-card-head-left">
                <div class="dash-card-icon" style="background: var(--text-1); color: white;">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <span class="dash-card-title">Catalogue des itinéraires</span>
            </div>
            <span class="dash-card-tag">{{ $itineraires->total() }} LIGNES</span>
        </div>

        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Itinéraire</th>
                        <th>Distance & Durée</th>
                        <th>Date de création</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="itinerairesTable">
                    @forelse($itineraires as $itineraire)
                    <tr>
                        <td>
                            <div class="itinerary-main">
                                <div class="itinerary-icon-box">
                                    <i class="fas fa-route"></i>
                                </div>
                                <div class="itinerary-path">
                                    <span class="path-start">{{ $itineraire->point_depart }}</span>
                                    <i class="fas fa-long-arrow-alt-right path-arrow"></i>
                                    <span class="path-end">{{ $itineraire->point_arrive }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="date-info">
                                <span class="date-val"><i class="far fa-clock mr-1" style="color: var(--orange);"></i> {{ $itineraire->durer_parcours }}</span>
                                <span class="time-val">Estimation du trajet</span>
                            </div>
                        </td>
                        <td>
                            <div class="date-info">
                                <span class="date-val">{{ $itineraire->created_at->format('d/m/Y') }}</span>
                                <span class="time-val">{{ $itineraire->created_at->format('H:i') }}</span>
                            </div>
                        </td>
                        <td class="text-right">
                            <div class="d-flex justify-content-end" style="gap: 8px;">
                                <button type="button" class="btn-action view show-itineraire-btn" data-itineraire="{{ json_encode($itineraire) }}" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('itineraire.edit', $itineraire) }}" class="btn-action edit" title="Modifier">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <button type="button" class="btn-action delete delete-itineraire-btn" data-itineraire-id="{{ $itineraire->id }}" data-itineraire-name="{{ $itineraire->point_depart }} → {{ $itineraire->point_arrive }}" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">
                            <div class="table-empty py-5">
                                <i class="fas fa-route table-empty-icon mb-3" style="font-size: 40px; color: var(--border-strong);"></i>
                                <h3 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin: 0;">Aucun itinéraire trouvé</h3>
                                <p style="font-size: 12px; color: var(--text-3); font-weight: 600; margin-bottom: 16px;">Votre catalogue de trajets est vide.</p>
                                <a href="{{ route('itineraire.create') }}" class="btn btn-primary btn-sm" style="background: var(--orange); border: none; font-weight: 700; border-radius: var(--radius-sm);">
                                    Définir un itinéraire
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($itineraires->hasPages())
        <div class="p-3 border-top">
            {{ $itineraires->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let timer;
        searchInput.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => searchInput.closest('form').submit(), 600);
        });
    }

    document.querySelectorAll('.show-itineraire-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const data = JSON.parse(this.getAttribute('data-itineraire'));
            const date = new Date(data.created_at).toLocaleDateString();
            
            Swal.fire({
                title: `<div style="font-size: 12px; font-weight: 700; color: var(--text-3); text-transform: uppercase;">Détails Itinéraire</div><div style="font-size: 18px; font-weight: 800; color: var(--text-1); margin-top: 5px;">Route #${data.id}</div>`,
                html: `
                    <div style="text-align: left; padding: 10px;">
                        <div style="background: linear-gradient(135deg, var(--orange) 0%, var(--orange-dark) 100%); padding: 24px; border-radius: 16px; color: white; text-align: center; margin-bottom: 20px; box-shadow: var(--shadow-sm);">
                            <div style="font-size: 18px; font-weight: 800;">${data.point_depart}</div>
                            <div style="margin: 12px 0; opacity: 0.7;"><i class="fas fa-long-arrow-alt-down" style="font-size: 24px;"></i></div>
                            <div style="font-size: 18px; font-weight: 800;">${data.point_arrive}</div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div style="background: var(--surface-2); border: 1px solid var(--border); padding: 16px; border-radius: 12px;">
                                    <div style="font-size: 10px; font-weight: 800; color: var(--text-3); text-transform: uppercase; margin-bottom: 4px;">Durée Estimée</div>
                                    <div style="font-size: 16px; font-weight: 800; color: var(--text-1);">${data.durer_parcours}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div style="background: var(--surface-2); border: 1px solid var(--border); padding: 16px; border-radius: 12px;">
                                    <div style="font-size: 10px; font-weight: 800; color: var(--text-3); text-transform: uppercase; margin-bottom: 4px;">Date Création</div>
                                    <div style="font-size: 16px; font-weight: 800; color: var(--text-1);">${date}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `,
                showCloseButton: true, showConfirmButton: false, width: 450,
                customClass: { popup: 'rounded-lg border-0 shadow-sm' }
            });
        });
    });

    document.querySelectorAll('.delete-itineraire-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-itineraire-id');
            const name = this.getAttribute('data-itineraire-name');

            Swal.fire({
                title: 'Supprimer cet itinéraire ?',
                html: `Le trajet <b>${name}</b> sera retiré de votre catalogue.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: 'var(--red)',
                cancelButtonColor: 'var(--text-3)',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                customClass: { popup: 'rounded-lg border-0 shadow-sm' }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/company/Itinerary/${id}`;
                    form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
});

@if(session('success'))
    Swal.fire({ icon: 'success', title: 'Succès', text: '{{ session('success') }}', confirmButtonColor: '#F97316', customClass: { popup: 'rounded-lg shadow-sm' } });
@endif
@if(session('error'))
    Swal.fire({ icon: 'error', title: 'Erreur', text: '{{ session('error') }}', confirmButtonColor: '#EF4444', customClass: { popup: 'rounded-lg shadow-sm' } });
@endif
</script>
@endsection