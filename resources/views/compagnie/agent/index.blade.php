@extends('compagnie.layouts.template')

@section('page-title', 'Gestion de l\'Équipe')
@section('page-subtitle', 'Supervisez les accès et suivez les performances de vos agents de gare')

@section('styles')
<style>
    .mi-purple { background: #F3E8FF; color: #9333EA; }
    
    .dash-header-actions { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; padding: 16px 20px; }
    .search-wrapper { position: relative; flex: 1; max-width: 400px; }
    .search-wrapper i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-3); }
    .search-wrapper input { width: 100%; padding: 12px 16px 12px 36px; border-radius: 12px; border: 1px solid var(--border); background: var(--surface-2); font-size: 13px; font-weight: 600; color: var(--text-1); outline: none; transition: all 0.2s; }
    .search-wrapper input:focus { background: var(--surface); border-color: var(--orange); box-shadow: 0 0 0 3px var(--orange-light); }

    .btn-primary { background: var(--text-1); color: #fff; padding: 12px 20px; border-radius: 12px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s; }
    .btn-primary:hover { background: var(--orange); color: #fff; text-decoration: none; transform: translateY(-1px); }

    .agent-row { transition: all 0.2s; }
    .agent-row:hover { background: var(--surface-2); }
    .td-avatar-img { width: 44px; height: 44px; border-radius: 12px; object-fit: cover; border: 1px solid var(--border-strong); }
    .td-avatar-placeholder { width: 44px; height: 44px; border-radius: 12px; background: var(--orange-light); color: var(--orange); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 800; border: 1px solid var(--orange-mid); }
    
    .cell-stack { display: flex; flex-direction: column; gap: 4px; }
    .text-name { font-size: 13px; font-weight: 800; color: var(--text-1); text-transform: uppercase; }
    .text-code { font-size: 9px; font-weight: 800; color: var(--orange-dark); background: var(--orange-light); padding: 2px 6px; border-radius: 4px; display: inline-block; width: fit-content; }
    
    .info-line { display: flex; align-items: center; gap: 8px; font-size: 11px; color: var(--text-2); font-weight: 600; }
    .info-line i { width: 14px; text-align: center; color: var(--text-3); }

    .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
    .sp-active { background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; }
    .sp-inactive { background: #FEF2F2; color: #DC2626; border: 1px solid #FECDD3; }
    .status-pill .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    .actions-group { display: flex; gap: 6px; justify-content: flex-end; }
    .btn-icon { width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border); background: var(--surface); color: var(--text-3); display: flex; align-items: center; justify-content: center; font-size: 12px; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .btn-icon.edit:hover { background: var(--text-1); color: white; border-color: var(--text-1); }
    .btn-icon.msg:hover { background: var(--orange); color: white; border-color: var(--orange); }
    .btn-icon.del:hover { background: #E11D48; color: white; border-color: #E11D48; }

    /* Modal Styling */
    .modal-content { border-radius: var(--radius); border: none; box-shadow: var(--shadow-md); }
    .modal-header { border-bottom: 1px solid var(--border); padding: 16px 24px; }
    .modal-title { font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-1); }
    .modal-body { padding: 24px; }
    .modal-footer { border-top: 1px solid var(--border); padding: 16px 24px; }
    .form-group-modal label { font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--text-3); letter-spacing: 0.5px; margin-bottom: 6px; display: block; }
    .form-control-modal { width: 100%; border: 1px solid var(--border); background: var(--surface-2); border-radius: var(--radius-sm); padding: 12px 14px; font-size: 13px; font-weight: 600; outline: none; transition: 0.2s; }
    .form-control-modal:focus { border-color: var(--orange); background: var(--surface); box-shadow: 0 0 0 3px var(--orange-light); }
</style>
@endsection

@section('content')
<div class="dashboard-page">

    {{-- TOP STATS --}}
    <div class="metric-grid mb-4">
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-amber"><i class="fas fa-users"></i></div>
                <span class="metric-tag mt-slate">Total</span>
            </div>
            <div class="metric-label">Agents Enregistrés</div>
            <div class="metric-value">{{ $totalAgents }}</div>
        </div>

        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-green"><i class="fas fa-user-check"></i></div>
                <span class="metric-tag mt-green">Actifs</span>
            </div>
            <div class="metric-label">Opérationnels</div>
            <div class="metric-value">{{ $activeAgents }}</div>
        </div>

        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-blue"><i class="fas fa-user-plus"></i></div>
                <span class="metric-tag mt-slate">Hebdo</span>
            </div>
            <div class="metric-label">Nouveaux (7j)</div>
            <div class="metric-value">+{{ $newAgents }}</div>
        </div>

        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-purple"><i class="fas fa-map-marked-alt"></i></div>
                <span class="metric-tag mt-slate">Secteurs</span>
            </div>
            <div class="metric-label">Zones de Service</div>
            <div class="metric-value">{{ count(array_unique(array_filter($agents->pluck('commune')->toArray()))) }}</div>
        </div>
    </div>

    {{-- ACTIONS HEADER --}}
    <div class="dash-card mb-4 mt-4">
        <div class="dash-header-actions">
            <div class="search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" id="agentSearch" placeholder="Filtrer par nom, téléphone, commune...">
            </div>
            
            <a href="{{ route('compagnie.agents.create') }}" class="btn-primary">
                <i class="fas fa-plus"></i> Enrôler un Agent
            </a>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="dash-card">
        <div class="dash-card-head" style="background: var(--surface-2);">
            <div class="dash-card-head-left">
                <div class="dash-card-icon" style="background: var(--text-1); color: white;"><i class="fas fa-users"></i></div>
                <span class="dash-card-title">Listing des Agents de Gare</span>
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
                <tbody>
                    @forelse ($agents as $agent)
                    <tr class="agent-row">
                        <td>
                            <div class="d-flex align-items-center" style="gap: 12px;">
                                @if($agent->profile_picture)
                                    <img src="{{ Storage::url($agent->profile_picture) }}" alt="{{ $agent->name }}" class="td-avatar-img">
                                @else
                                    <div class="td-avatar-placeholder">
                                        {{ strtoupper(substr($agent->name, 0, 1)) }}{{ strtoupper(substr($agent->prenom, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="cell-stack">
                                    <span class="text-name">{{ $agent->name }} {{ $agent->prenom }}</span>
                                    <span class="text-code">{{ $agent->code_id ?? 'PRO-AGENT' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="cell-stack">
                                <span class="info-line"><i class="fas fa-mobile-alt"></i> {{ $agent->contact }}</span>
                                <span class="info-line"><i class="fas fa-envelope"></i> {{ $agent->email }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="cell-stack">
                                <span class="info-line"><i class="fas fa-map-marker-alt"></i> {{ $agent->commune ?? 'Non définie' }}</span>
                                @if($agent->gare)
                                    <span class="info-line" style="color: var(--orange-dark);"><i class="fas fa-building" style="color: var(--orange);"></i> {{ $agent->gare->nom_gare }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            @if($agent->is_active)
                                <span class="status-pill sp-active"><span class="dot"></span> ACTIF</span>
                            @else
                                <span class="status-pill sp-inactive"><span class="dot"></span> INACTIF</span>
                            @endif
                        </td>
                        <td>
                            <div class="actions-group">
                                <a href="{{ route('compagnie.agents.edit', $agent->id) }}" class="btn-icon edit" title="Modifier">
                                    <i class="fas fa-user-edit"></i>
                                </a>
                                <button type="button" class="btn-icon msg" onclick="openMessageModal({{ $agent->id }}, '{{ addslashes($agent->name . ' ' . $agent->prenom) }}')" title="Envoyer Message">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                                <button type="button" class="btn-icon del" onclick="confirmDelete({{ $agent->id }}, '{{ addslashes($agent->name) }}')" title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="table-empty">
                                <i class="fas fa-user-slash mb-3" style="font-size: 40px;"></i>
                                <h4 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin: 0;">Aucun agent enregistré</h4>
                                <p style="font-size: 12px; color: var(--text-3); margin: 8px 0 16px;">Votre équipe d'accueil n'a pas encore été constituée.</p>
                                <a href="{{ route('compagnie.agents.create') }}" class="btn-primary">Enrôler maintenant</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($agents->hasPages())
        <div class="p-3 border-top">
            {{ $agents->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</div>

{{-- MESSAGE MODAL --}}
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouveau Message Interne</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline: none; background: transparent; border: none; font-size: 24px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('compagnie.messages.store') }}" method="POST">
                @csrf
                <input type="hidden" name="recipient_type" value="agent"> 
                <input type="hidden" name="recipient_id" id="modal_agent_id">
                
                <div class="modal-body">
                    <div class="form-group-modal mb-3">
                        <label>Destinataire</label>
                        <input type="text" id="modal_agent_name" class="form-control-modal" readonly style="pointer-events: none; opacity: 0.7;">
                    </div>
                    <div class="form-group-modal mb-3">
                        <label>Objet du message <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control-modal" required placeholder="Titre...">
                    </div>
                    <div class="form-group-modal">
                        <label>Contenu <span class="text-danger">*</span></label>
                        <textarea name="message" rows="4" class="form-control-modal" required placeholder="Votre message..."></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="display: flex; gap: 10px;">
                    <button type="button" class="btn btn-light" style="flex: 1; font-weight: 700; border-radius: var(--radius-sm);" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" style="flex: 1; background: var(--orange); border: none;">Envoyer <i class="fas fa-paper-plane"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('agentSearch');
    const tableRows = document.querySelectorAll('.agent-row');

    if(searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            tableRows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }
});

function openMessageModal(id, name) {
    document.getElementById('modal_agent_id').value = id;
    document.getElementById('modal_agent_name').value = name;
    $('#messageModal').modal('show');
}

function confirmDelete(id, name) {
    Swal.fire({
        title: 'Suppression définitive',
        html: `L'agent <strong>${name}</strong> sera définitivement supprimé des effectifs.`,
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
            form.action = `/company/agents/${id}`; 
            form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

@if(session('success'))
    Swal.fire({ icon: 'success', title: 'Succès', text: '{{ session('success') }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, customClass: { popup: 'rounded-lg shadow-sm' } });
@endif
</script>
@endsection