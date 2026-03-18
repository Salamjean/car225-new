@extends('compagnie.layouts.template')

@section('page-title', 'Gestion des Caisses')
@section('page-subtitle', 'Supervisez vos encaissements et gérez votre équipe de caisse')

@section('styles')
<style>
    /* Avatars dans la table */
    .caisse-avatar {
        width: 36px; height: 36px; border-radius: 10px;
        background: var(--surface-2); border: 1px solid var(--border-strong);
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 12px; color: var(--orange);
        object-fit: cover; overflow: hidden;
    }
    
    /* Boutons d'action */
    .btn-action {
        width: 32px; height: 32px; border-radius: 8px; border: none;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 12px; transition: all 0.2s; cursor: pointer; text-decoration: none;
    }
    .btn-action.edit { background: var(--surface-2); color: var(--text-2); }
    .btn-action.edit:hover { background: var(--text-1); color: white; }
    .btn-action.archive { background: #FFF7ED; color: var(--orange-dark); }
    .btn-action.archive:hover { background: var(--orange); color: white; }
    .btn-action.delete { background: #FEF2F2; color: var(--red); }
    .btn-action.delete:hover { background: var(--red); color: white; }

    /* Barre de recherche personnalisée */
    .search-box { position: relative; max-width: 350px; width: 100%; }
    .search-box i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-3); font-size: 13px; }
    .search-box input {
        width: 100%; padding: 10px 16px 10px 40px;
        border: 1px solid var(--border); border-radius: var(--radius-sm);
        font-size: 13px; background: var(--surface); color: var(--text-1); transition: 0.2s;
    }
    .search-box input:focus { outline: none; border-color: var(--orange); box-shadow: 0 0 0 3px var(--orange-light); }
</style>
@endsection

@section('content')
<div class="dashboard-page">

    {{-- STATS ROW --}}
    <div class="metric-grid mb-4">
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-amber"><i class="fas fa-cash-register"></i></div>
                <span class="metric-tag mt-slate">Effectif</span>
            </div>
            <div class="metric-label">Caissières Enregistrées</div>
            <div class="metric-value">{{ $caisses->count() }}</div>
        </div>

        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-green"><i class="fas fa-user-check"></i></div>
                <span class="metric-tag mt-green">Actives</span>
            </div>
            <div class="metric-label">En service</div>
            <div class="metric-value">{{ $caisses->whereNull('archived_at')->count() }}</div>
        </div>

        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-rose"><i class="fas fa-user-clock"></i></div>
                <span class="metric-tag mt-rose">Archives</span>
            </div>
            <div class="metric-label">Profils Suspendus</div>
            <div class="metric-value">{{ $caisses->whereNotNull('archived_at')->count() }}</div>
        </div>
    </div>

    {{-- ACTION HEADER --}}
    <div class="dash-card mb-4 p-3 d-flex flex-wrap align-items-center justify-content-between" style="gap: 16px;">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Filtrer par nom, code ID, contact...">
        </div>
        
        <a href="{{ route('compagnie.caisse.create') }}" class="btn btn-primary" style="background: var(--orange); border: none; font-weight: 700; border-radius: var(--radius-sm); font-size: 13px;">
            <i class="fas fa-plus mr-2"></i> Enrôler une Caissière
        </a>
    </div>

    {{-- TABLE --}}
    <div class="dash-card">
        <div class="dash-card-head" style="background: var(--surface-2);">
            <div class="dash-card-head-left">
                <div class="dash-card-icon" style="background: var(--text-1); color: white;">
                    <i class="fas fa-users"></i>
                </div>
                <span class="dash-card-title">Répertoire de la Régie</span>
            </div>
        </div>

        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Identité & Code</th>
                        <th>Coordonnées</th>
                        <th>Affectation</th>
                        <th class="text-center">Statut</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="caisseTableBody">
                    @forelse ($caisses as $caisse)
                    <tr class="caisse-row">
                        <td>
                            <div class="d-flex align-items-center" style="gap: 12px;">
                                @if($caisse->profile_picture)
                                    <img src="{{ Storage::url($caisse->profile_picture) }}" alt="Avatar" class="caisse-avatar">
                                @else
                                    <div class="caisse-avatar">{{ strtoupper(substr($caisse->name, 0, 1)) }}{{ strtoupper(substr($caisse->prenom, 0, 1)) }}</div>
                                @endif
                                <div>
                                    <div style="font-weight: 800; font-size: 13px; color: var(--text-1); text-transform: uppercase;">{{ $caisse->name }} {{ $caisse->prenom }}</div>
                                    <div style="font-size: 10px; font-weight: 800; color: var(--text-3); margin-top: 4px; background: var(--surface-2); padding: 2px 6px; border-radius: 4px; display: inline-block;">
                                        ID: {{ $caisse->code_id ?? 'REG-CASH' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 700; font-size: 12px; color: var(--text-1); font-family: monospace; letter-spacing: 0.5px; margin-bottom: 2px;"><i class="fas fa-mobile-alt mr-1" style="color: var(--text-3);"></i> {{ $caisse->contact }}</div>
                            <div style="font-size: 11px; color: var(--text-3);"><i class="fas fa-envelope mr-1"></i> {{ $caisse->email }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 800; font-size: 11px; color: var(--blue); text-transform: uppercase; margin-bottom: 2px;"><i class="fas fa-map-marker-alt mr-1"></i> {{ $caisse->commune ?? 'Non définie' }}</div>
                            @if($caisse->gare)
                                <div style="font-size: 11px; font-weight: 700; color: var(--orange);"><i class="fas fa-building mr-1"></i> {{ $caisse->gare->nom_gare }}</div>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($caisse->isArchived())
                                <span class="metric-tag mt-rose"><i class="fas fa-circle mr-1" style="font-size: 6px;"></i> Archivée</span>
                            @else
                                <span class="metric-tag mt-green"><i class="fas fa-circle mr-1" style="font-size: 6px;"></i> Active</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="d-flex justify-content-end" style="gap: 8px;">
                                <a href="{{ route('compagnie.caisse.edit', $caisse->id) }}" class="btn-action edit" title="Modifier">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <button type="button" onclick="toggleArchive({{ $caisse->id }}, {{ $caisse->isArchived() ? 'true' : 'false' }})" class="btn-action archive" title="{{ $caisse->isArchived() ? 'Réactiver' : 'Archiver' }}">
                                    <i class="fas {{ $caisse->isArchived() ? 'fa-undo' : 'fa-archive' }}"></i>
                                </button>
                                <button type="button" onclick="confirmDelete({{ $caisse->id }}, '{{ $caisse->prenom }} {{ $caisse->name }}')" class="btn-action delete" title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="table-empty py-5">
                                <i class="fas fa-cash-register table-empty-icon mb-3" style="font-size: 40px; color: var(--border-strong);"></i>
                                <h3 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin: 0;">Aucune caissière enregistrée</h3>
                                <p style="font-size: 12px; color: var(--text-3); font-weight: 600; margin-bottom: 16px;">Votre équipe de vente n'a pas encore été configurée.</p>
                                <a href="{{ route('compagnie.caisse.create') }}" class="btn btn-primary btn-sm" style="background: var(--orange); border: none; font-weight: 700; border-radius: var(--radius-sm);">
                                    Recruter maintenant
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
    // Advanced Search Logic
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('.caisse-row');

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        tableRows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
});

function toggleArchive(id, isArchived) {
    const action = isArchived ? 'Restauration' : 'Archivage';
    const message = isArchived ? "Souhaitez-vous réactiver l'accès pour cette caissière ?" : "Cette caissière ne pourra plus se connecter aux terminaux de vente.";
    
    Swal.fire({
        title: action,
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Confirmer',
        cancelButtonText: 'Annuler',
        confirmButtonColor: 'var(--text-1)',
        cancelButtonColor: 'var(--text-3)',
        customClass: { popup: 'rounded-lg border-0 shadow-sm' }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/company/caisse/${id}/toggle-archive`, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ 
                        icon: 'success', 
                        title: 'Opération réussie', 
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-lg shadow-sm' }
                    }).then(() => location.reload());
                }
            });
        }
    });
}

function confirmDelete(id, name) {
    Swal.fire({
        title: 'Suppression définitive',
        html: `La caissière <strong>${name}</strong> sera définitivement rayée des effectifs.`,
        icon: 'error',
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
            form.action = `/company/caisse/${id}`;
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
        icon: 'success', 
        title: 'Opération Réussie', 
        text: '{{ session('success') }}',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        customClass: { popup: 'rounded-lg shadow-sm' }
    });
@endif
</script>
@endsection