@extends('compagnie.layouts.template')

@section('page-title', 'Réseau des Gares')
@section('page-subtitle', 'Gérez vos points d\'embarquement et supervisez vos responsables de gare')

@section('styles')
<style>
    .gare-avatar-box {
        width: 40px; height: 40px; border-radius: 12px; background: var(--orange-light); color: var(--orange);
        display: flex; align-items: center; justify-content: center; font-size: 16px; border: 1px solid var(--orange-mid);
    }
    
    .search-box { position: relative; max-width: 350px; width: 100%; }
    .search-box i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-3); font-size: 13px; }
    .search-box input {
        width: 100%; padding: 10px 16px 10px 40px;
        border: 1px solid var(--border); border-radius: var(--radius-sm);
        font-size: 13px; background: var(--surface); color: var(--text-1); transition: 0.2s;
    }
    .search-box input:focus { outline: none; border-color: var(--orange); box-shadow: 0 0 0 3px var(--orange-light); }

    .btn-action {
        width: 32px; height: 32px; border-radius: 8px; border: none; display: inline-flex; align-items: center; justify-content: center;
        font-size: 13px; transition: 0.2s; cursor: pointer; text-decoration: none;
    }
    .btn-action.edit { background: #EFF6FF; color: #2563EB; }
    .btn-action.delete { background: #FEF2F2; color: #DC2626; }
    .btn-action:hover { transform: translateY(-2px); filter: brightness(0.95); text-decoration: none; }
</style>
@endsection

@section('content')
<div class="dashboard-page">

    {{-- STATS ROW --}}
    <div class="mb-4" style="display:flex; justify-content: space-between; gap: 10px;">
        <div class="metric-card" style="width: 100%;">
            <div class="metric-top">
                <div class="metric-icon mi-amber"><i class="fas fa-warehouse"></i></div>
                <span class="metric-tag mt-slate">Réseau</span>
            </div>
            <div class="metric-label">Gares Opérationnelles</div>
            <div class="metric-value">{{ $gares->count() }}</div>
        </div>

        <div class="metric-card" style="width: 100%;">
            <div class="metric-top">
                <div class="metric-icon mi-blue"><i class="fas fa-city"></i></div>
                <span class="metric-tag mt-blue">Couverture</span>
            </div>
            <div class="metric-label">Villes Desservies</div>
            <div class="metric-value">{{ $gares->pluck('ville')->unique()->count() }}</div>
        </div>

        <div class="metric-card" style="width: 100%;">
            <div class="metric-top">
                <div class="metric-icon" style="background: #F3E8FF; color: #7E22CE;"><i class="fas fa-user-tie"></i></div>
                <span class="metric-tag" style="background: #F3E8FF; color: #7E22CE;">Encadrement</span>
            </div>
            <div class="metric-label">Chefs de Gare</div>
            <div class="metric-value">{{ $gares->count() }}</div>
        </div>
    </div>

    {{-- ACTION HEADER --}}
    <div class="dash-card mb-4 p-3 d-flex flex-wrap align-items-center justify-content-between" style="gap: 16px;">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="gareSearch" placeholder="Chercher une gare, ville, adresse...">
        </div>
        
        <a href="{{ route('gare.create') }}" class="btn btn-primary" style="background: var(--orange); border: none; font-weight: 700; border-radius: var(--radius-sm); font-size: 13px;">
            <i class="fas fa-plus mr-2"></i> Ajouter une Gare
        </a>
    </div>

    {{-- TABLE --}}
    <div class="dash-card">
        <div class="dash-card-head" style="background: var(--surface-2);">
            <div class="dash-card-head-left">
                <div class="dash-card-icon" style="background: var(--text-1); color: white;">
                    <i class="fas fa-network-wired"></i>
                </div>
                <span class="dash-card-title">Répertoire Stratégique</span>
            </div>
        </div>

        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Nom de la Gare</th>
                        <th>Localisation</th>
                        <th>Adresse</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gares as $gare)
                    <tr class="gare-row">
                        <td>
                            <div class="d-flex align-items-center" style="gap: 12px;">
                                <div class="gare-avatar-box">
                                    <i class="fas fa-warehouse"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 800; font-size: 13px; color: var(--text-1); text-transform: uppercase;">{{ $gare->nom_gare }}</div>
                                    <div style="font-size: 10px; font-weight: 800; color: var(--text-3); margin-top: 4px; background: var(--surface-2); padding: 2px 6px; border-radius: 4px; display: inline-block;">
                                        ID: #{{ str_pad($gare->id, 3, '0', STR_PAD_LEFT) }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 800; font-size: 12px; color: var(--text-1); text-transform: uppercase;"><i class="fas fa-city mr-1 text-muted"></i> {{ $gare->ville }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 600; font-size: 12px; color: var(--text-2);"><i class="fas fa-map-marker-alt mr-1" style="color: var(--orange);"></i> {{ Str::limit($gare->adresse, 40) }}</div>
                        </td>
                        <td class="text-right">
                            <div class="d-flex justify-content-end" style="gap: 8px;">
                                <a href="{{ route('gare.edit', $gare->id) }}" class="btn-action edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" onclick="confirmDeleteGare({{ $gare->id }}, '{{ addslashes($gare->nom_gare) }}')" class="btn-action delete" title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">
                            <div class="table-empty py-5">
                                <i class="fas fa-university table-empty-icon mb-3" style="font-size: 40px; color: var(--border-strong);"></i>
                                <h3 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin: 0;">Aucune gare enregistrée</h3>
                                <p style="font-size: 12px; color: var(--text-3); font-weight: 600; margin-bottom: 16px;">Votre réseau de gares est vide pour le moment.</p>
                                <a href="{{ route('gare.create') }}" class="btn btn-primary btn-sm" style="background: var(--orange); border: none; font-weight: 700; border-radius: var(--radius-sm);">
                                    Créer la Première Gare
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('gareSearch');
    const tableRows = document.querySelectorAll('.gare-row');

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        tableRows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });

    window.confirmDeleteGare = function(id, name) {
        Swal.fire({
            title: 'Suppression de Gare',
            html: `Voulez-vous vraiment supprimer la gare <strong>${name}</strong> ?<br>Cette action affectera les itinéraires liés.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'var(--red)',
            cancelButtonColor: 'var(--text-3)',
            confirmButtonText: 'Supprimer',
            cancelButtonText: 'Annuler',
            customClass: { popup: 'rounded-lg border-0 shadow-sm' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/company/gare/${id}`;
                form.innerHTML = `
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    @if(session('success'))
        Swal.fire({
            icon: 'success', title: 'Opération réussie', text: "{{ session('success') }}",
            timer: 3000, showConfirmButton: false, toast: true, position: 'top-end',
            customClass: { popup: 'rounded-lg shadow-sm border-left-success' }
        });
    @endif
});
</script>
@endsection