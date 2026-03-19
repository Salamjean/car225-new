@extends('compagnie.layouts.template')

@section('page-title', 'Gestion du Personnel')
@section('page-subtitle', 'Supervisez vos chauffeurs et convoyeurs (Équipage)')

@section('styles')
<style>
    .mi-purple { background: #F3E8FF; color: #9333EA; }
    .mt-purple { background: #F3E8FF; color: #6B21A8; }
    .mt-blue { background: #EFF6FF; color: #1E3A8A; }

    .dash-header-actions { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; padding: 16px 20px; }
    .filters-group { display: flex; flex-wrap: wrap; flex: 1; gap: 12px; align-items: center; }
    
    .search-wrapper { position: relative; flex: 1; min-width: 250px; max-width: 400px; }
    .search-wrapper i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-3); }
    .search-wrapper input { width: 100%; padding: 12px 16px 12px 40px; border-radius: 12px; border: 1px solid var(--border); background: var(--surface-2); font-size: 13px; font-weight: 600; color: var(--text-1); outline: none; transition: all 0.2s; }
    .search-wrapper input:focus { background: var(--surface); border-color: var(--orange); box-shadow: 0 0 0 3px var(--orange-light); }

    .select-wrapper { position: relative; min-width: 150px; }
    .select-wrapper select { width: 100%; padding: 12px 32px 12px 16px; border-radius: 12px; border: 1px solid var(--border); background: var(--surface-2); font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--text-2); outline: none; appearance: none; cursor: pointer; transition: all 0.2s; }
    .select-wrapper select:focus { background: var(--surface); border-color: var(--orange); }
    .select-wrapper i { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); font-size: 10px; color: var(--text-3); pointer-events: none; }

    .btn-primary { background: var(--orange); color: #fff; padding: 12px 24px; border-radius: 12px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(249,115,22,0.1); }
    .btn-primary:hover { background: var(--orange-dark); color: #fff; text-decoration: none; transform: translateY(-2px); box-shadow: 0 8px 16px rgba(249,115,22,0.25); }

    .personnel-row { transition: background 0.2s; }
    .personnel-row:hover { background: var(--surface-2); }
    
    .td-avatar-img { width: 46px; height: 46px; border-radius: 14px; object-fit: cover; border: 1px solid var(--border-strong); }
    .td-avatar-placeholder { width: 46px; height: 46px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 800; }
    .td-avatar-placeholder.chauffeur { background: #EFF6FF; color: #2563EB; border: 1px solid #BFDBFE; }
    .td-avatar-placeholder.convoyeur { background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; }

    .cell-stack { display: flex; flex-direction: column; gap: 4px; }
    .text-name { font-size: 13px; font-weight: 800; color: var(--text-1); text-transform: uppercase; }
    .text-email { font-size: 10px; font-weight: 700; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.5px; }
    
    .info-line { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--text-1); font-weight: 700; font-family: monospace; letter-spacing: 0.5px; }
    .info-line i { width: 14px; text-align: center; color: var(--text-3); font-size: 10px; }
    .info-line.urgent { color: #E11D48; }
    .info-line.urgent i { color: #FDA4AF; }

    .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
    .sp-libre { background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; }
    .sp-route { background: #FEF2F2; color: #DC2626; border: 1px solid #FECDD3; }
    
    .role-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 8px; font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
    .rb-chauffeur { background: #EFF6FF; color: #2563EB; }
    .rb-convoyeur { background: #F3E8FF; color: #9333EA; }

    .actions-group { display: flex; gap: 6px; justify-content: flex-end; }
    .btn-icon { width: 34px; height: 34px; border-radius: 10px; border: 1px solid var(--border); background: var(--surface); color: var(--text-3); display: flex; align-items: center; justify-content: center; font-size: 12px; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .btn-icon:hover { text-decoration: none; }
    .bi-view:hover { background: var(--orange); color: white; border-color: var(--orange); }
    .bi-edit:hover { background: var(--orange); color: white; border-color: var(--orange); }
    .bi-del:hover { background: #E11D48; color: white; border-color: #E11D48; }

    .code-id-badge { background: #F8FAFC; color: #64748B; padding: 4px 8px; border-radius: 6px; font-family: monospace; font-weight: 800; font-size: 11px; border: 1.5px solid #E2E8F0; display: inline-block; }

    .sa-detail-card { background: var(--surface-2); border-radius: 16px; padding: 20px; text-align: left; margin-top: 20px; border: 1px solid var(--border); }
    .sa-detail-header { display: flex; align-items: center; gap: 16px; margin-bottom: 24px; }
    .sa-detail-avatar { width: 60px; height: 60px; border-radius: 14px; background: var(--orange); color: white; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 800; }
    .sa-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; }
    .sa-box { background: var(--surface); padding: 16px; border-radius: 12px; text-align: center; border: 1px solid var(--border); }
    .sa-box-label { font-size: 9px; font-weight: 800; color: var(--text-3); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
    .sa-box-val { font-size: 12px; font-weight: 800; color: var(--text-1); text-transform: uppercase; }
    .sa-row { display: flex; justify-content: space-between; align-items: center; padding: 16px; background: var(--surface); border-radius: 12px; margin-bottom: 8px; border: 1px solid var(--border); }
    .sa-row.danger { background: #FEF2F2; border-color: #FECDD3; }
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
            <div class="metric-label">Membres d'Équipage</div>
            <div class="metric-value">{{ $personnels->count() }}</div>
        </div>

        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-blue"><i class="fas fa-id-card"></i></div>
                <span class="metric-tag mt-blue">Pilotes</span>
            </div>
            <div class="metric-label">Chauffeurs Actifs</div>
            <div class="metric-value">{{ $personnels->where('type_personnel', 'Chauffeur')->count() }}</div>
        </div>

        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-green"><i class="fas fa-people-carry"></i></div>
                <span class="metric-tag mt-green">Assistants</span>
            </div>
            <div class="metric-label">Convoyeurs Actifs</div>
            <div class="metric-value">{{ $personnels->where('type_personnel', 'Convoyeur')->count() }}</div>
        </div>

        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-purple"><i class="fas fa-user-check"></i></div>
                <span class="metric-tag mt-purple">Libres</span>
            </div>
            <div class="metric-label">Prêts pour Départ</div>
            <div class="metric-value">{{ $personnels->where('statut', 'disponible')->count() }}</div>
        </div>
    </div>

    {{-- ACTIONS HEADER --}}
    <div class="dash-card mb-4 mt-4">
        <div class="dash-header-actions">
            <div class="filters-group">
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Rechercher un membre...">
                </div>
                
                <div class="select-wrapper">
                    <select id="typeFilter">
                        <option value="">Tous les rôles</option>
                        <option value="Chauffeur">Chauffeurs</option>
                        <option value="Convoyeur">Convoyeurs</option>
                    </select>
                    <i class="fas fa-chevron-down"></i>
                </div>

                <div class="select-wrapper">
                    <select id="statutFilter">
                        <option value="">Tous les statuts</option>
                        <option value="disponible">Disponibles</option>
                        <option value="indisponible">En route</option>
                    </select>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
            
            <a href="{{ route('personnel.create') }}" class="btn-primary">
                <i class="fas fa-user-plus"></i> Nouvel Équipage
            </a>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="dash-card">
        <div class="dash-card-head">
            <h3 class="dash-card-title">Listing Équipage & Personnel</h3>
        </div>

        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Collaborateur</th>
                        <th>Fonction</th>
                        <th>CODE ID</th>
                        <th>Coordonnées</th>
                        <th class="text-center">Statut</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="personnelTable">
                    @forelse ($personnels as $personnel)
                    <tr class="personnel-row" 
                        data-type="{{ $personnel->type_personnel }}" 
                        data-statut="{{ $personnel->statut }}"
                        data-search="{{ strtolower($personnel->name . ' ' . $personnel->prenom . ' ' . $personnel->email . ' ' . $personnel->code_id) }}"
                        data-id="{{ $personnel->id }}"
                        data-code-id="{{ $personnel->code_id }}"
                        data-full-name="{{ addslashes($personnel->prenom . ' ' . $personnel->name) }}"
                        data-email="{{ $personnel->email }}"
                        data-phone="{{ $personnel->country_code }} {{ $personnel->contact }}"
                        data-emergency="{{ $personnel->country_code_urgence }} {{ $personnel->contact_urgence }}">
                        
                        <td>
                            <div class="td-user">
                                @if($personnel->profile_image)
                                    <img src="{{ Storage::url($personnel->profile_image) }}" alt="Photo" class="td-avatar-img">
                                @else
                                    <div class="td-avatar-placeholder {{ strtolower($personnel->type_personnel) }}">
                                        {{ strtoupper(substr($personnel->prenom, 0, 1)) }}{{ strtoupper(substr($personnel->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="cell-stack">
                                    <span class="text-name">{{ $personnel->prenom }} {{ $personnel->name }}</span>
                                    <span class="text-email">{{ $personnel->email }}</span>
                                </div>
                            </div>
                        </td>

                        <td>
                            @if($personnel->type_personnel == 'Chauffeur')
                                <span class="role-badge rb-chauffeur"><i class="fas fa-id-card"></i> Chauffeur</span>
                            @else
                                <span class="role-badge rb-convoyeur"><i class="fas fa-people-carry"></i> Convoyeur</span>
                            @endif
                        </td>

                        <td>
                            <span class="code-id-badge">{{ $personnel->code_id ?? 'N/A' }}</span>
                        </td>

                        <td>
                            <div class="cell-stack">
                                <span class="info-line"><i class="fas fa-phone-alt"></i> {{ $personnel->country_code }} {{ $personnel->contact }}</span>
                                <span class="info-line urgent" title="Contact d'urgence"><i class="fas fa-ambulance"></i> {{ $personnel->country_code_urgence }} {{ $personnel->contact_urgence }}</span>
                            </div>
                        </td>

                        <td class="text-center">
                            @if($personnel->statut == 'disponible')
                                <span class="status-pill sp-libre"><span class="dot" style="animation: pulseGreen 2s infinite;"></span> Libre</span>
                            @else
                                <span class="status-pill sp-route"><span class="dot"></span> En Route</span>
                            @endif
                        </td>

                        <td>
                            <div class="actions-group">
                                <button type="button" class="btn-icon bi-view show-details-btn" title="Fiche Profil">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('personnels.edit', $personnel->id) }}" class="btn-icon bi-edit" title="Modifier">
                                    <i class="fas fa-pen-nib"></i>
                                </a>
                                <button type="button" class="btn-icon bi-del delete-personnel-btn" data-name="{{ addslashes($personnel->prenom . ' ' . $personnel->name) }}" data-id="{{ $personnel->id }}" title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="table-empty">
                                <i class="fas fa-users-slash"></i>
                                <h4 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin: 0;">Aucun personnel enregistré</h4>
                                <p style="font-size: 13px; color: var(--text-3); margin-bottom: 16px;">Commencez par ajouter des chauffeurs et des convoyeurs.</p>
                                <a href="{{ route('personnel.create') }}" class="btn-primary">Enrôler maintenant</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($personnels->hasPages())
        <div class="dash-card-footer" style="padding: 16px 20px; border-top: 1px solid var(--border);">
            {{ $personnels->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const statutFilter = document.getElementById('statutFilter');
    const rows = document.querySelectorAll('.personnel-row');

    function filterTable() {
        const query = searchInput.value.toLowerCase().trim();
        const type = typeFilter.value;
        const statut = statutFilter.value;

        rows.forEach(row => {
            const searchData = row.getAttribute('data-search');
            const rowType = row.getAttribute('data-type');
            const rowStatut = row.getAttribute('data-statut');

            const matchesSearch = searchData.includes(query);
            const matchesType = !type || rowType === type;
            const matchesStatut = !statut || rowStatut === statut;

            row.style.display = (matchesSearch && matchesType && matchesStatut) ? '' : 'none';
        });
    }

    if(searchInput) searchInput.addEventListener('input', filterTable);
    if(typeFilter) typeFilter.addEventListener('change', filterTable);
    if(statutFilter) statutFilter.addEventListener('change', filterTable);

    document.querySelectorAll('.show-details-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const name = row.getAttribute('data-full-name');
            const codeId = row.getAttribute('data-code-id');
            const email = row.getAttribute('data-email');
            const type = row.getAttribute('data-type');
            const phone = row.getAttribute('data-phone');
            const emergency = row.getAttribute('data-emergency');
            const statut = row.getAttribute('data-statut');
            
            const statutColor = statut === 'disponible' ? '#059669' : '#DC2626';
            
            Swal.fire({
                title: `<span style="font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text-3); letter-spacing:1px;">Détails Collaborateur</span>`,
                html: `
                    <div class="sa-detail-card">
                        <div class="sa-detail-header">
                            <div class="sa-detail-avatar">${name.charAt(0)}</div>
                            <div>
                                <div style="font-size:16px; font-weight:800; color:var(--text-1); text-transform:uppercase;">${name}</div>
                                <div style="font-size:11px; font-weight:700; color:var(--orange); text-transform:uppercase; margin-top:4px;">${email}</div>
                            </div>
                        </div>
                        
                        <div class="sa-grid">
                            <div class="sa-box">
                                <div class="sa-box-label">CODE IDENTIFIANT</div>
                                <div class="sa-box-val" style="color:var(--orange);">${codeId || 'N/A'}</div>
                            </div>
                            <div class="sa-box">
                                <div class="sa-box-label">DIVISION</div>
                                <div class="sa-box-val">${type}</div>
                            </div>
                        </div>

                        <div class="sa-row">
                            <div>
                                <div class="sa-box-label">ÉTAT ACTUEL</div>
                                <div style="font-size:14px; font-weight:800; color:${statutColor}; text-transform:uppercase;">${statut}</div>
                            </div>
                            <div class="status-pill ${statut === 'disponible' ? 'sp-libre' : 'sp-route'}" style="padding:4px 8px;">${statut}</div>
                        </div>

                        <div class="sa-row">
                            <div>
                                <div class="sa-box-label">Ligne Directe</div>
                                <div style="font-size:14px; font-weight:800; color:var(--text-1); font-family:monospace;">${phone}</div>
                            </div>
                            <i class="fas fa-phone-alt" style="color:var(--text-3);"></i>
                        </div>
                        <div class="sa-row danger">
                            <div>
                                <div class="sa-box-label" style="color:#E11D48;">Contact d'Urgence</div>
                                <div style="font-size:14px; font-weight:800; color:#E11D48; font-family:monospace;">${emergency}</div>
                            </div>
                            <i class="fas fa-ambulance" style="color:#FDA4AF;"></i>
                        </div>
                    </div>
                `,
                showConfirmButton: false, showCloseButton: true, width: 450,
                customClass: { popup: 'rounded-[20px] shadow-lg border-0', closeButton: 'text-gray-400 hover:text-gray-900' }
            });
        });
    });

    document.querySelectorAll('.delete-personnel-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            
            Swal.fire({
                title: 'RAYER DES EFFECTIFS',
                html: `<p style="font-size:13px; color:#6B6560;">Le profil de <strong style="color:#EA580C;">${name}</strong> sera définitivement supprimé.</p>`,
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: 'var(--orange)', cancelButtonColor: '#F5F4F1',
                confirmButtonText: '<span style="color:white; font-size:12px; font-weight:700;">SUPPRIMER</span>',
                cancelButtonText: '<span style="color:#6B6560; font-size:12px; font-weight:700;">ANNULER</span>',
                reverseButtons: true,
                customClass: { popup: 'rounded-[20px] shadow-lg border-0', confirmButton: 'rounded-[10px] px-4 py-2', cancelButton: 'rounded-[10px] px-4 py-2' }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/company/personal/${id}`; 
                    form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });

    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Succès', text: '{{ session('success') }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, customClass: { popup: 'rounded-[14px] shadow-lg border-0' } });
    @endif
});
</script>
@endsection