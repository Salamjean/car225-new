@extends('compagnie.layouts.template')

@section('page-title', 'Lignes de Transport')
@section('page-subtitle', 'Gérez vos routes et horaires Aller/Retour')

@section('styles')
<style>
    .btn-action {
        width: 32px; height: 32px; border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 13px; border: none; transition: transform 0.2s; text-decoration: none;
    }
    .btn-action.view { background: #F0F9FF; color: #0EA5E9; }
    .btn-action.edit { background: #EFF6FF; color: #2563EB; }
    .btn-action.link { background: #FFF7ED; color: var(--orange-dark); border: 1px solid var(--orange-mid); }
    .btn-action:hover { transform: translateY(-2px); filter: brightness(0.95); text-decoration: none; }

    .route-item {
        padding: 20px;
        border-bottom: 1px solid var(--border);
        transition: background 0.2s;
    }
    .route-item:last-child { border-bottom: none; }
    .route-item:hover { background: var(--surface-2); }
    
    .route-icon-box {
        width: 50px; height: 50px; border-radius: 12px;
        background: linear-gradient(135deg, var(--orange) 0%, var(--orange-dark) 100%);
        color: white; display: flex; align-items: center; justify-content: center;
        font-size: 20px; box-shadow: 0 4px 10px rgba(249,115,22,0.3);
    }

    .counter-box {
        padding: 10px 16px; background: var(--surface);
        border: 1px solid var(--border-strong); border-radius: 12px;
        text-align: center; min-width: 80px; box-shadow: var(--shadow-sm);
    }
    .counter-label { font-size: 10px; font-weight: 800; text-transform: uppercase; margin-bottom: 4px; }
    .counter-val { font-size: 18px; font-weight: 800; color: var(--text-1); line-height: 1; }
</style>
@endsection

@section('content')
<div class="dashboard-page">

    {{-- STATS ROW --}}
    <div class="metric-grid">
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-amber"><i class="fas fa-route"></i></div>
            </div>
            <div class="metric-label">Routes actives</div>
            <div class="metric-value">{{ $groupedProgrammes->count() }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-green"><i class="fas fa-arrow-right"></i></div>
            </div>
            <div class="metric-label">Horaires Aller</div>
            <div class="metric-value">{{ $groupedProgrammes->sum(fn($g) => $g->aller->count()) }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-blue"><i class="fas fa-arrow-left"></i></div>
            </div>
            <div class="metric-label">Horaires Retour</div>
            <div class="metric-value">{{ $groupedProgrammes->sum(fn($g) => $g->retour->count()) }}</div>
        </div>
        <div class="metric-card metric-featured">
            <div class="metric-top">
                <div class="metric-icon mi-white"><i class="fas fa-calendar-alt"></i></div>
            </div>
            <div class="metric-label">Total Programmes</div>
            <div class="metric-value">{{ $programmes->count() }}</div>
        </div>
    </div>

    {{-- HEADER ACTION --}}
    <div class="dash-header mt-4">
        <div>
            <h2 class="dash-title" style="font-size: 20px;">Configuration des lignes</h2>
        </div>
        <a href="{{ route('programme.create') }}" class="btn btn-primary" style="background: var(--orange); border: none; font-weight: 700; border-radius: var(--radius-sm); padding: 10px 20px;">
            <i class="fas fa-plus mr-2"></i> Nouvelle Route
        </a>
    </div>

    {{-- LISTE DES ROUTES --}}
    <div class="dash-card">
        <div class="dash-card-head" style="background: var(--surface-2);">
            <div class="dash-card-head-left">
                <div class="dash-card-icon" style="background: var(--text-1); color: white;">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <span class="dash-card-title">Routes configurées</span>
            </div>
            <span class="dash-card-tag">{{ $groupedProgrammes->count() }} ROUTES</span>
        </div>

        @if($groupedProgrammes->count() > 0)
            <div>
                @foreach($groupedProgrammes as $route)
                    <div class="route-item d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                        
                        {{-- Infos Route --}}
                        <div class="d-flex align-items-center gap-3">
                            <div class="route-icon-box">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center mb-1" style="gap: 8px;">
                                    <h3 style="font-size: 15px; font-weight: 800; color: var(--text-1); margin: 0;">
                                        {{ $route->itineraire->point_depart }} 
                                        <i class="fas fa-long-arrow-alt-right mx-1 text-muted" style="font-size: 12px;"></i> 
                                        {{ $route->itineraire->point_arrive }}
                                    </h3>
                                    <span class="metric-tag mt-amber">{{ number_format($route->montant_billet, 0, ',', ' ') }} FCFA</span>
                                    @php 
                                        $capacity = $route->aller->first()?->capacity ?? $route->retour->first()?->capacity ?? 'N/A';
                                    @endphp
                                    <span class="metric-tag mt-blue" style="background: #EFF6FF; color: #2563EB;">{{ $capacity }} PLACES</span>
                                </div>
                                <div style="font-size: 11px; font-weight: 700; color: var(--text-3); text-transform: uppercase;">
                                    {{ $route->gare_depart?->nom_gare ?? $route->itineraire?->point_depart ?? 'N/A' }}
                                    <span class="mx-1">&bull;</span>
                                    {{ $route->gare_arrivee?->nom_gare ?? $route->itineraire?->point_arrive ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        {{-- Stats & Actions --}}
                        <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                            <div class="d-flex gap-2">
                                <div class="counter-box">
                                    <div class="counter-label" style="color: var(--emerald);">ALLER</div>
                                    <div class="counter-val">{{ $route->aller->count() }}</div>
                                </div>
                                <div class="counter-box">
                                    <div class="counter-label" style="color: var(--blue);">RETOUR</div>
                                    <div class="counter-val">{{ $route->retour->count() }}</div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2 border-left pl-3 ml-2" style="border-color: var(--border) !important;">
                                <button type="button" onclick="showSchedulesPopup({{ json_encode($route) }})" class="btn-action view" title="Voir les horaires">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" onclick="manageSchedules({{ json_encode($route) }})" class="btn-action edit" title="Modifier les horaires">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        @else
            <div class="table-empty py-5">
                <i class="fas fa-route table-empty-icon mb-3" style="font-size: 40px; color: var(--border-strong);"></i>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--text-1); margin: 0;">Aucune ligne créée</h3>
                <p style="font-size: 12px; color: var(--text-3); font-weight: 600; margin-bottom: 20px;">Commencez par configurer votre première ligne de transport</p>
                <a href="{{ route('programme.create') }}" class="btn btn-primary" style="background: var(--orange); border: none; font-weight: 700; border-radius: var(--radius-sm);">
                    <i class="fas fa-plus mr-2"></i> Créer une ligne
                </a>
            </div>
        @endif
    </div>
</div>

<script>
// --- FONCTION 1 : CONSULTATION SIMPLE ---
function showSchedulesPopup(routeData) {
    const formatList = (list, colorHex, label) => {
        if (!list || list.length === 0) return `<div style="text-align: center; padding: 20px; border: 1px dashed var(--border-strong); border-radius: 8px; font-size: 12px; color: var(--text-3); font-weight: 600; font-style: italic;">Aucun horaire configuré</div>`;
        return list.map((h) => `
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; border: 1px solid var(--border); border-radius: 8px; margin-bottom: 8px; background: var(--surface-2);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="font-weight: 800; font-size: 14px; color: var(--text-1);">${h.heure_depart}</div>
                    <i class="fas fa-arrow-right" style="color: var(--text-3); font-size: 10px;"></i>
                    <div style="font-weight: 700; font-size: 14px; color: var(--text-3);">${h.heure_arrive}</div>
                </div>
                
                <div style="text-align: center; flex: 1; padding: 0 15px;">
                    <div style="font-weight: 800; font-size: 12px; color: var(--orange-dark);">${new Intl.NumberFormat('fr-FR').format(h.montant_billet)} FCFA</div>
                    <div style="font-size: 9px; font-weight: 700; color: var(--text-3); text-transform: uppercase;">${h.capacity || 'N/A'} PLACES</div>
                </div>

                <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: ${colorHex};">${label}</div>
            </div>
        `).join('');
    };

    const gareDep = routeData.gare_depart ? routeData.gare_depart.nom_gare : routeData.itineraire.point_depart;
    const gareArr = routeData.gare_arrivee ? routeData.gare_arrivee.nom_gare : routeData.itineraire.point_arrive;

    Swal.fire({
        title: `<div style="font-size: 12px; font-weight: 700; color: var(--text-3); text-transform: uppercase;">Consultation des horaires</div><div style="font-size: 18px; font-weight: 800; color: var(--text-1); margin-top: 5px;">${gareDep} - ${gareArr}</div>`,
        html: `
            <div class="row text-left mt-3">
                <div class="col-6">
                    <h4 style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--emerald); margin-bottom: 12px;"><i class="fas fa-circle mr-1" style="font-size: 6px;"></i> Trajets ALLER</h4>
                    <div style="max-height: 300px; overflow-y: auto;">
                        ${formatList(routeData.aller, 'var(--emerald)', 'Aller')}
                    </div>
                </div>
                <div class="col-6 border-left">
                    <h4 style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--blue); margin-bottom: 12px;"><i class="fas fa-circle mr-1" style="font-size: 6px;"></i> Trajets RETOUR</h4>
                    <div style="max-height: 300px; overflow-y: auto;">
                        ${formatList(routeData.retour, 'var(--blue)', 'Retour')}
                    </div>
                </div>
            </div>
        `,
        width: '700px',
        showConfirmButton: false,
        showCloseButton: true,
        customClass: { popup: 'rounded-lg border-0 shadow-sm' }
    });
}

// --- FONCTION 2 : GESTION COMPLETE (Ajout/Modification/Suppression) ---
function manageSchedules(routeData) {
    window.currentDureeMinutes = parseDuration(routeData.durer_parcours);

    const generateExistingList = (list) => {
        if (!list || list.length === 0) return `<div style="text-align: center; padding: 15px; border: 1px dashed var(--border-strong); border-radius: 8px; font-size: 11px; color: var(--text-3); font-weight: 600; margin-bottom: 10px;">Aucun horaire existant</div>`;
        
        return list.map(h => `
            <div style="display: flex; align-items: center; justify-content: space-between; background: var(--surface); border: 1px solid var(--border); padding: 10px 14px; border-radius: 8px; margin-bottom: 8px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-weight: 800; font-size: 13px; color: var(--text-1);">${h.heure_depart}</span>
                    <i class="fas fa-arrow-right" style="color: var(--text-3); font-size: 10px;"></i>
                    <span style="font-weight: 700; font-size: 13px; color: var(--text-3);">${h.heure_arrive}</span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <a href="/company/programme/${h.id}/edit" class="btn-action link" title="Modifier ce programme complet" style="width: 28px; height: 28px;">
                        <i class="fas fa-pen" style="font-size: 10px;"></i>
                    </a>
                    <button type="button" onclick="deleteSchedule(${h.id})" class="btn-action" style="width: 28px; height: 28px; background: #FEF2F2; color: var(--red);" title="Supprimer">
                        <i class="fas fa-trash" style="font-size: 10px;"></i>
                    </button>
                </div>
            </div>
        `).join('');
    };

    const content = `
        <div style="text-align: left; padding-top: 10px;">
            <form action="{{ route('programme.store') }}" method="POST" id="addScheduleForm">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="itineraire_id" value="${routeData.itineraire_id}">
                <input type="hidden" name="montant_billet" value="${routeData.montant_billet}">
                <input type="hidden" name="gare_depart_id" value="${routeData.gare_depart.id}">
                <input type="hidden" name="gare_arrivee_id" value="${routeData.gare_arrivee.id}">
                <input type="hidden" name="capacity" value="${routeData.aller && routeData.aller.length > 0 ? (routeData.aller[0].capacity || 50) : 50}">
                
                <div class="row">
                    <div class="col-md-6 mb-4 mb-md-0">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid var(--border);">
                            <h4 style="font-size: 11px; font-weight: 800; color: var(--emerald); text-transform: uppercase; margin: 0;">Horaires Aller</h4>
                            <button type="button" onclick="addNewRow('aller')" class="btn btn-sm" style="background: #ECFDF5; color: var(--emerald); font-weight: 700; font-size: 10px; padding: 4px 10px; border-radius: 6px;">
                                <i class="fas fa-plus"></i> Ajouter
                            </button>
                        </div>
                        <div style="max-height: 200px; overflow-y: auto;">${generateExistingList(routeData.aller)}</div>
                        <div id="new-aller-container"></div>
                    </div>

                    <div class="col-md-6 border-md-left" style="border-color: var(--border) !important;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid var(--border);">
                            <h4 style="font-size: 11px; font-weight: 800; color: var(--blue); text-transform: uppercase; margin: 0;">Horaires Retour</h4>
                            <button type="button" onclick="addNewRow('retour')" class="btn btn-sm" style="background: #EFF6FF; color: var(--blue); font-weight: 700; font-size: 10px; padding: 4px 10px; border-radius: 6px;">
                                <i class="fas fa-plus"></i> Ajouter
                            </button>
                        </div>
                        <div style="max-height: 200px; overflow-y: auto;">${generateExistingList(routeData.retour)}</div>
                        <div id="new-retour-container"></div>
                    </div>
                </div>
            </form>
        </div>
    `;

    Swal.fire({
        title: `<div style="font-size: 18px; font-weight: 800; color: var(--text-1);">Gestion des horaires</div>`,
        html: content,
        width: '850px',
        showCancelButton: true,
        confirmButtonText: 'Enregistrer les ajouts',
        cancelButtonText: 'Fermer',
        confirmButtonColor: 'var(--orange)',
        cancelButtonColor: 'var(--text-3)',
        customClass: { popup: 'rounded-lg border-0 shadow-sm' },
        preConfirm: () => {
            const allerRows = document.querySelectorAll('#new-aller-container .new-row');
            const retourRows = document.querySelectorAll('#new-retour-container .new-row');
            if (allerRows.length === 0 && retourRows.length === 0) {
                Swal.showValidationMessage('Veuillez ajouter au moins un nouvel horaire via le bouton Ajouter.');
                return false;
            }
            document.getElementById('addScheduleForm').submit();
        }
    });
}

function parseDuration(durationStr) {
    if (!durationStr) return 90;
    let hours = 0, minutes = 0;
    const hMatch = durationStr.match(/(\d+)\s*h/i);
    if (hMatch) hours = parseInt(hMatch[1]);
    const mMatch = durationStr.match(/(\d+)\s*m/i);
    if (mMatch) minutes = parseInt(mMatch[1]);
    return (hours * 60) + minutes || 90;
}

function calculateArrivalTime(departureTime, durationMinutes) {
    const [h, m] = departureTime.split(':').map(Number);
    const totalMinutes = h * 60 + m + durationMinutes;
    const arrH = Math.floor(totalMinutes / 60) % 24;
    const arrM = totalMinutes % 60;
    return `${String(arrH).padStart(2, '0')}:${String(arrM).padStart(2, '0')}`;
}

window.addNewRow = function(type) {
    const container = document.getElementById(`new-${type}-container`);
    const index = Date.now();
    const borderColor = type === 'aller' ? 'var(--emerald)' : 'var(--blue)';
    const inputDepartId = `depart_${type}_${index}`;
    const inputArriveeId = `arrivee_${type}_${index}`;

    const rowHtml = `
        <div class="new-row" style="background: var(--surface-2); padding: 12px; border-radius: 8px; border-left: 3px solid ${borderColor}; margin-bottom: 10px; position: relative;">
            <button type="button" onclick="this.parentElement.remove()" style="position: absolute; top: -8px; right: -8px; background: var(--red); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 10px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                <i class="fas fa-times"></i>
            </button>
            <div class="row">
                <div class="col-6">
                    <label style="font-size: 9px; font-weight: 700; color: var(--text-2); text-transform: uppercase;">Départ</label>
                    <input type="time" id="${inputDepartId}" name="${type}_horaires[${index}][heure_depart]" required
                        class="form-control form-control-sm" style="font-weight: 700; font-size: 13px;"
                        onchange="autoCalcArrivee('${inputDepartId}', '${inputArriveeId}')">
                </div>
                <div class="col-6">
                    <label style="font-size: 9px; font-weight: 700; color: var(--text-2); text-transform: uppercase;">Arrivée</label>
                    <input type="time" id="${inputArriveeId}" name="${type}_horaires[${index}][heure_arrive]" required
                        class="form-control form-control-sm" style="background: #F1F5F9; font-weight: 700; font-size: 13px;" readonly>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', rowHtml);
};

function autoCalcArrivee(departId, arriveeId) {
    const departInput = document.getElementById(departId);
    const arriveeInput = document.getElementById(arriveeId);
    if (departInput.value && window.currentDureeMinutes) {
        arriveeInput.value = calculateArrivalTime(departInput.value, window.currentDureeMinutes);
    }
}

function deleteSchedule(programId) {
    Swal.fire({
        title: 'Supprimer cet horaire ?',
        text: 'Cette action est irréversible et annulera les trajets liés.',
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
            form.action = `/company/programme/${programId}`;
            form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection