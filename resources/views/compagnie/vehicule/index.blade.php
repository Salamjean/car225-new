@extends('compagnie.layouts.template')

@section('page-title', 'Flotte & Matériel')
@section('page-subtitle', 'Supervisez l\'état technique et la disponibilité de votre parc automobile')

@section('styles')
<style>
    /* Metric Cards Additions */
    .mi-red { background: #FEF2F2; color: #E11D48; }
    .mt-red { background: #FEF2F2; color: #9F1239; }

    /* Search & Actions */
    .dash-header-actions { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; padding: 16px 20px; }
    .search-wrapper { position: relative; flex: 1; max-width: 400px; }
    .search-wrapper i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-3); }
    .search-wrapper input { width: 100%; padding: 12px 16px 12px 40px; border-radius: 12px; border: 1px solid var(--border); background: var(--surface-2); font-size: 13px; font-weight: 600; color: var(--text-1); outline: none; transition: all 0.2s; }
    .search-wrapper input:focus { background: var(--surface); border-color: var(--orange); box-shadow: 0 0 0 3px var(--orange-light); }

    .btn-primary { background: var(--text-1); color: #fff; padding: 12px 20px; border-radius: 12px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s; }
    .btn-primary:hover { background: var(--orange); color: #fff; text-decoration: none; transform: translateY(-1px); }

    /* Table & License Plate */
    .vehicule-row { transition: background 0.2s; }
    .vehicule-row:hover { background: var(--surface-2); }
    
    .license-plate { display: inline-flex; border: 2px solid var(--text-1); border-radius: 8px; overflow: hidden; background: white; align-items: stretch; transition: transform 0.2s; box-shadow: var(--shadow-sm); }
    .vehicule-row:hover .license-plate { transform: scale(1.05); }
    .lp-country { background: #2563EB; color: white; font-size: 8px; font-weight: 900; padding: 6px 6px; display: flex; flex-direction: column; align-items: center; justify-content: center; border-right: 1px solid var(--text-1); }
    .lp-country img { width: 12px; height: 8px; margin-bottom: 2px; }
    .lp-number { padding: 6px 12px; font-size: 14px; font-weight: 900; font-family: monospace; letter-spacing: 1px; color: var(--text-1); display: flex; align-items: center; justify-content: center; }

    /* Type Pill & Serial */
    .cell-stack { display: flex; flex-direction: column; gap: 6px; }
    .type-pill { display: inline-block; width: fit-content; padding: 3px 8px; background: #EFF6FF; color: #2563EB; font-size: 9px; font-weight: 900; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px; border: 1px solid #BFDBFE; }
    .serial-number { font-size: 10px; font-weight: 700; color: var(--text-3); text-transform: uppercase; font-family: monospace; }

    /* Seats Tag */
    .seats-tag { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: var(--surface-2); border-radius: 10px; border: 1px solid var(--border); }
    .seats-tag i { font-size: 10px; color: var(--text-3); }
    .seats-tag span { font-size: 12px; font-weight: 900; color: var(--text-1); }

    /* Status Pills & Tooltip */
    .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
    .sp-active { background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; }
    .sp-inactive { background: #FEF2F2; color: #DC2626; border: 1px solid #FECDD3; }
    .status-pill .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .sp-active .dot { animation: pulseGreen 2s infinite; }

    .status-tooltip-wrap { position: relative; display: inline-flex; cursor: help; }
    .status-tooltip { position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%); margin-bottom: 8px; width: 180px; padding: 10px; background: var(--text-1); color: white; font-size: 9px; font-weight: 700; border-radius: 8px; text-align: center; opacity: 0; visibility: hidden; transition: 0.2s; z-index: 20; box-shadow: var(--shadow-md); }
    .status-tooltip::after { content: ''; position: absolute; top: 100%; left: 50%; transform: translateX(-50%); border: 6px solid transparent; border-top-color: var(--text-1); }
    .status-tooltip-wrap:hover .status-tooltip { opacity: 1; visibility: visible; }

    /* Action Buttons */
    .actions-group { display: flex; gap: 6px; justify-content: flex-end; }
    .btn-icon { width: 34px; height: 34px; border-radius: 10px; border: 1px solid var(--border); background: var(--surface); color: var(--text-3); display: flex; align-items: center; justify-content: center; font-size: 12px; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .btn-icon:hover { text-decoration: none; }
    .bi-view:hover { background: var(--orange); color: white; border-color: var(--orange); }
    .bi-edit:hover { background: var(--text-1); color: white; border-color: var(--text-1); }
    .bi-pause:hover { background: #F59E0B; color: white; border-color: #F59E0B; }
    .bi-play:hover { background: #10B981; color: white; border-color: #10B981; }
    .bi-del:hover { background: #E11D48; color: white; border-color: #E11D48; }

    /* Empty State */
    .table-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 20px; text-align: center; }
    .table-empty i { font-size: 48px; color: var(--border-strong); margin-bottom: 16px; }

    /* SweetAlert details overrides */
    .sa-card { padding: 0; text-align: left; }
    .sa-plate-wrap { display: flex; justify-content: center; margin-bottom: 24px; }
    .sa-plate { display: inline-flex; border: 2px solid var(--text-1); border-radius: 8px; overflow: hidden; background: white; transform: scale(1.3); transform-origin: center; box-shadow: var(--shadow-sm); }
    .sa-plate .country { background: #2563EB; color: white; font-size: 8px; font-weight: 900; padding: 6px 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; border-right: 1px solid var(--text-1); }
    .sa-plate .number { padding: 6px 14px; font-size: 16px; font-weight: 900; font-family: monospace; letter-spacing: 1px; color: var(--text-1); display: flex; align-items: center; justify-content: center; }
    
    .sa-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; }
    .sa-box { background: var(--surface-2); padding: 12px 16px; border-radius: 12px; border: 1px solid var(--border); }
    .sa-box-label { font-size: 9px; font-weight: 800; color: var(--text-3); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
    .sa-box-val { font-size: 12px; font-weight: 800; color: var(--text-1); text-transform: uppercase; font-family: monospace; }
    
    .sa-status-box { background: var(--surface); border: 1px solid var(--border); padding: 16px; border-radius: 12px; }
    .sa-status-box.active { background: #ECFDF5; border-color: #A7F3D0; }
    .sa-status-box.inactive { background: #FEF2F2; border-color: #FECDD3; }
</style>
@endsection

@section('content')
<div class="dashboard-page">

    {{-- ── TOP STATS ── --}}
    <div class="metric-grid mb-4">
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-amber"><i class="fas fa-bus"></i></div>
                <span class="metric-tag mt-slate">Global</span>
            </div>
            <div class="metric-label">Véhicules Enregistrés</div>
            <div class="metric-value">{{ $vehicules->total() }}</div>
        </div>

        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-green"><i class="fas fa-check-double"></i></div>
                <span class="metric-tag mt-green">Opérationnel</span>
            </div>
            <div class="metric-label">En Circulation</div>
            <div class="metric-value">{{ $vehicules->where('is_active', true)->count() }}</div>
        </div>

        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-red"><i class="fas fa-tools"></i></div>
                <span class="metric-tag mt-red">Maintenance</span>
            </div>
            <div class="metric-label">Véhicules Indisponibles</div>
            <div class="metric-value">{{ $vehicules->where('is_active', false)->count() }}</div>
        </div>

        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-blue"><i class="fas fa-users-cog"></i></div>
                <span class="metric-tag mt-slate">Capacité</span>
            </div>
            <div class="metric-label">Sièges Disponibles</div>
            <div class="metric-value">{{ $vehicules->sum('nombre_place') }}</div>
        </div>
    </div>

    {{-- ── ACTIONS HEADER ── --}}
    <div class="dash-card mb-4 mt-4">
        <div class="dash-header-actions">
            <div class="search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" id="vehiculeSearch" placeholder="Chercher une plaque ou un numéro de série...">
            </div>
            
            <a href="{{ route('vehicule.create') }}" class="btn-primary">
                <i class="fas fa-plus-circle"></i> Enregistrer un Véhicule
            </a>
        </div>
    </div>

    {{-- ── TABLE CARD ── --}}
    <div class="dash-card">
        <div class="dash-card-head">
            <h3 class="dash-card-title">Inventaire Technique</h3>
        </div>

        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Immatriculation</th>
                        <th>Type / Série</th>
                        <th class="text-center">Places</th>
                        <th class="text-center">Disponibilité</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicules as $vehicule)
                    <tr class="vehicule-row">
                        <td>
                            <div class="license-plate">
                                <div class="lp-country">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/bc/Flag_of_Cote_d%27Ivoire.svg" alt="CI">
                                    CI
                                </div>
                                <div class="lp-number">{{ $vehicule->immatriculation }}</div>
                            </div>
                        </td>

                        <td>
                            <div class="cell-stack">
                                <span class="type-pill">{{ $vehicule->type_range }}</span>
                                <span class="serial-number">S/N: {{ $vehicule->numero_serie ?? '---' }}</span>
                            </div>
                        </td>

                        <td class="text-center">
                            <div class="seats-tag">
                                <i class="fas fa-user-friends"></i>
                                <span>{{ $vehicule->nombre_place }}</span>
                            </div>
                        </td>

                        <td class="text-center">
                            @if($vehicule->is_active)
                                <span class="status-pill sp-active"><span class="dot"></span> OPÉRATIONNEL</span>
                            @else
                                <div class="status-tooltip-wrap">
                                    <span class="status-pill sp-inactive"><span class="dot"></span> INACTIF</span>
                                    @if($vehicule->motif)
                                        <div class="status-tooltip">{{ $vehicule->motif }}</div>
                                    @endif
                                </div>
                            @endif
                        </td>

                        <td>
                            <div class="actions-group">
                                <button type="button" class="btn-icon bi-view show-vehicule-details"
                                    data-id="{{ $vehicule->id }}" 
                                    data-immat="{{ $vehicule->immatriculation }}"
                                    data-serie="{{ $vehicule->numero_serie ?? 'N/A' }}"
                                    data-type="{{ $vehicule->type_range }}"
                                    data-places="{{ $vehicule->nombre_place }}"
                                    data-status="{{ $vehicule->is_active ? 'Actif' : 'Inactif' }}"
                                    data-motif="{{ $vehicule->motif ?? 'Aucun motif' }}"
                                    data-created="{{ $vehicule->created_at->format('d/m/Y') }}"
                                    title="Détails techniques">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('vehicule.edit', $vehicule->id) }}" class="btn-icon bi-edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>

                                @if($vehicule->is_active)
                                    <button type="button" onclick="confirmAction({{ $vehicule->id }}, '{{ $vehicule->immatriculation }}', 'deactivate')" class="btn-icon bi-pause" title="Retirer du service">
                                        <i class="fas fa-pause"></i>
                                    </button>
                                @else
                                    <button type="button" onclick="confirmAction({{ $vehicule->id }}, '{{ $vehicule->immatriculation }}', 'activate')" class="btn-icon bi-play" title="Remettre en service">
                                        <i class="fas fa-play"></i>
                                    </button>
                                @endif

                                <button type="button" onclick="confirmAction({{ $vehicule->id }}, '{{ $vehicule->immatriculation }}', 'delete')" class="btn-icon bi-del" title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="table-empty">
                                <i class="fas fa-bus"></i>
                                <h4>Flotte vide</h4>
                                <p style="font-size: 13px; color: var(--text-3); margin-bottom: 16px;">Aucun véhicule n'est encore enregistré dans votre système.</p>
                                <a href="{{ route('vehicule.create') }}" class="btn-primary">Enregistrer le Premier Véhicule</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vehicules->hasPages())
        <div class="dash-card-footer" style="padding: 16px 20px; border-top: 1px solid var(--border);">
            {{ $vehicules->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Advanced Search
    const searchInput = document.getElementById('vehiculeSearch');
    const tableRows = document.querySelectorAll('.vehicule-row');

    if(searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            tableRows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }

    // Details Modal
    document.querySelectorAll('.show-vehicule-details').forEach(btn => {
        btn.addEventListener('click', function() {
            const d = this.dataset;
            const statusClass = d.status === 'Actif' ? 'active' : 'inactive';
            const statusColor = d.status === 'Actif' ? '#059669' : '#DC2626';
            const motifHtml = d.status !== 'Actif' ? `<p style="margin-top:8px; font-size:11px; font-weight:700; color:#DC2626; font-style:italic;">"${d.motif}"</p>` : '';

            Swal.fire({
                title: `<span style="font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:1px; color:var(--text-1);">FICHE TECHNIQUE</span>`,
                html: `
                    <div class="sa-card">
                        <div class="sa-plate-wrap">
                            <div class="sa-plate">
                                <div class="country">
                                    CI
                                </div>
                                <div class="number">${d.immat}</div>
                            </div>
                        </div>

                        <div class="sa-grid">
                            <div class="sa-box">
                                <div class="sa-box-label">Type de véhicule</div>
                                <div class="sa-box-val">${d.type}</div>
                            </div>
                            <div class="sa-box">
                                <div class="sa-box-label">Capacité</div>
                                <div class="sa-box-val" style="color:var(--orange);">${d.places} PLACES</div>
                            </div>
                            <div class="sa-box">
                                <div class="sa-box-label">Numéro de série</div>
                                <div class="sa-box-val" style="font-size:10px;">${d.serie}</div>
                            </div>
                            <div class="sa-box">
                                <div class="sa-box-label">Date Signature</div>
                                <div class="sa-box-val">${d.created}</div>
                            </div>
                        </div>

                        <div class="sa-status-box ${statusClass}">
                            <div class="sa-box-label">Disponibilité Actuelle</div>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span style="width:8px; height:8px; border-radius:50%; background:${statusColor};"></span>
                                <span style="font-size:11px; font-weight:800; color:${statusColor}; text-transform:uppercase;">${d.status}</span>
                            </div>
                            ${motifHtml}
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCloseButton: true,
                customClass: {
                    popup: 'rounded-[24px] border-0 shadow-lg',
                    closeButton: 'bg-gray-100 rounded-xl m-4'
                }
            });
        });
    });

    // Confirmation Logic
    window.confirmAction = function(id, immat, action) {
        let config = {};
        switch(action) {
            case 'activate':
                config = { title: 'RÉACTIVER LE VÉHICULE', text: `Remettre le véhicule ${immat} en circulation ?`, icon: 'question', btn: 'RÉACTIVER', btnColor: '#10B981', method: 'PATCH', url: `/company/car/${id}/activate` };
                break;
            case 'deactivate':
                config = { title: 'RETRAIT DE CIRCULATION', text: `Pourquoi le véhicule ${immat} est-il inactif ?`, icon: 'warning', input: 'text', placeholder: 'Ex: Maintenance freins...', btn: 'DÉSACTIVER', btnColor: '#F59E0B', method: 'PATCH', url: `/company/car/${id}/deactivate` };
                break;
            case 'delete':
                config = { title: 'SUPPRESSION DÉFINITIVE', text: `Supprimer ${immat} de la base de données ?`, icon: 'error', btn: 'SUPPRIMER', btnColor: '#E11D48', method: 'DELETE', url: `/company/car/${id}` };
                break;
        }

        Swal.fire({
            title: `<span style="font-size:14px; font-weight:800; text-transform:uppercase;">${config.title}</span>`,
            html: `<p style="font-size:12px; color:var(--text-2); font-weight:600;">${config.text}</p>`,
            icon: config.icon,
            input: config.input ? 'text' : false,
            inputPlaceholder: config.placeholder,
            showCancelButton: true,
            confirmButtonColor: config.btnColor,
            cancelButtonColor: '#1A1714',
            confirmButtonText: `<span style="font-size:10px; font-weight:800;">${config.btn}</span>`,
            cancelButtonText: `<span style="font-size:10px; font-weight:800;">ANNULER</span>`,
            reverseButtons: true,
            customClass: {
                popup: 'rounded-[24px] border-0 shadow-lg',
                input: 'border-1 border-gray-200 rounded-xl px-4 py-3 text-sm font-semibold outline-none',
                confirmButton: 'rounded-xl px-6 py-3',
                cancelButton: 'rounded-xl px-6 py-3'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = config.url;
                form.innerHTML = `
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="${config.method}">
                    ${action === 'deactivate' ? `<input type="hidden" name="motif" value="${result.value}">` : ''}
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Succès',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            customClass: { popup: 'rounded-[14px] shadow-lg' }
        });
    @endif
});
</script>
@endsection