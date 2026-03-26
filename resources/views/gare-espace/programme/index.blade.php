@extends('gare-espace.layouts.template')

@section('title', 'Programmes')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="width: 97%">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Programmes de transport</h1>
                <p class="text-gray-500 text-sm mt-1">
                    Lignes au départ de la <span class="font-semibold text-orange-600">{{ $gare->nom_gare }}</span>
                </p>
            </div>
            <a href="{{ route('gare-espace.programme.create') }}"
               class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2.5 rounded-xl shadow-md hover:shadow-lg transition-all">
                <i class="fas fa-plus"></i> Nouvelle ligne
            </a>
        </div>

        {{-- ALERTES --}}
        @if(session('success'))
            <div class="mb-6 flex items-start gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl">
                <i class="fas fa-check-circle text-green-500 mt-0.5 flex-shrink-0"></i>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl">
                <i class="fas fa-exclamation-circle text-red-500 mt-0.5 flex-shrink-0"></i>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif

        {{-- STATS --}}
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase">Routes actives</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $groupedProgrammes->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-route text-orange-500 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase">Total horaires</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $programmes->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-orange-500 col-span-2 lg:col-span-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase">Gare de départ</p>
                        <p class="text-lg font-bold text-orange-600 mt-1">{{ $gare->nom_gare }}</p>
                        <p class="text-xs text-gray-400">{{ $gare->ville }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- LISTE DES ROUTES --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gray-900 rounded-xl flex items-center justify-center">
                        <i class="fas fa-map-marked-alt text-white text-sm"></i>
                    </div>
                    <span class="font-bold text-gray-800">Routes au départ de {{ $gare->nom_gare }}</span>
                </div>
                <span class="bg-orange-100 text-orange-700 text-xs font-bold px-3 py-1 rounded-full">
                    {{ $groupedProgrammes->count() }} ROUTE(S)
                </span>
            </div>

            @if($groupedProgrammes->count() > 0)
                <div class="divide-y divide-gray-50">
                    @foreach($groupedProgrammes as $route)
                        <div class="px-6 py-5 hover:bg-gray-50 transition-colors">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                                {{-- Infos Route --}}
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 text-white flex items-center justify-center shadow-md flex-shrink-0">
                                        <i class="fas fa-arrow-right text-lg"></i>
                                    </div>
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2 mb-1">
                                            <h3 class="font-extrabold text-gray-900 text-sm">
                                                {{ $route->itineraire->point_depart }}
                                                <i class="fas fa-long-arrow-alt-right mx-1 text-gray-400 text-xs"></i>
                                                {{ $route->itineraire->point_arrive }}
                                            </h3>
                                            <span class="bg-amber-100 text-amber-700 text-xs font-bold px-2 py-0.5 rounded-full">
                                                {{ number_format($route->montant_billet, 0, ',', ' ') }} FCFA
                                            </span>
                                            @php
                                                $capacity = $route->horaires->first()?->capacity ?? 'N/A';
                                            @endphp
                                            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded-full">
                                                {{ $capacity }} places
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-400 font-semibold uppercase">
                                            {{ $route->gare_depart?->nom_gare ?? '—' }}
                                            <i class="fas fa-arrow-right mx-1 text-[9px]"></i>
                                            {{ $route->gare_arrivee?->nom_gare ?? '—' }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Compteur & Actions --}}
                                <div class="flex items-center gap-3 flex-wrap">
                                    <div class="text-center px-4 py-2 bg-green-50 border border-green-200 rounded-xl min-w-[80px]">
                                        <p class="text-xs font-bold text-green-600 uppercase mb-0.5">Horaires</p>
                                        <p class="text-2xl font-extrabold text-gray-800">{{ $route->horaires->count() }}</p>
                                    </div>
                                    <div class="flex gap-2 pl-3 border-l border-gray-200">
                                        <button type="button"
                                            onclick="showSchedulesPopup({{ json_encode($route) }})"
                                            class="w-9 h-9 rounded-lg bg-sky-50 text-sky-500 hover:bg-sky-100 flex items-center justify-center transition-colors"
                                            title="Voir les horaires">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>
                                        <button type="button"
                                            onclick="manageSchedules({{ json_encode($route) }})"
                                            class="w-9 h-9 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-100 flex items-center justify-center transition-colors"
                                            title="Ajouter / Supprimer des horaires">
                                            <i class="fas fa-pencil-alt text-sm"></i>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-20 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                        <i class="fas fa-route text-gray-300 text-4xl"></i>
                    </div>
                    <h3 class="font-extrabold text-gray-700 text-lg mb-2">Aucune ligne créée</h3>
                    <p class="text-gray-400 text-sm mb-6">Configurez votre première ligne de transport au départ de votre gare</p>
                    <a href="{{ route('gare-espace.programme.create') }}"
                       class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 py-3 rounded-xl shadow-md transition-all">
                        <i class="fas fa-plus"></i> Créer une ligne
                    </a>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
// --- CONSULTATION DES HORAIRES ---
function showSchedulesPopup(routeData) {
    const list = routeData.horaires || [];
    const rows = list.length === 0
        ? `<div style="text-align:center;padding:20px;border:1px dashed #e2e8f0;border-radius:8px;font-size:12px;color:#94a3b8;font-style:italic;">Aucun horaire configuré</div>`
        : list.map(h => `
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border:1px solid #e2e8f0;border-radius:8px;margin-bottom:8px;background:#f8fafc;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span style="font-weight:800;font-size:15px;color:#1e293b;">${h.heure_depart}</span>
                    <i class="fas fa-arrow-right" style="color:#94a3b8;font-size:10px;"></i>
                    <span style="font-weight:700;font-size:15px;color:#64748b;">${h.heure_arrive}</span>
                </div>
                <div style="text-align:center;">
                    <div style="font-weight:800;font-size:12px;color:#f97316;">${new Intl.NumberFormat('fr-FR').format(h.montant_billet)} FCFA</div>
                    <div style="font-size:9px;font-weight:700;color:#94a3b8;text-transform:uppercase;">${h.capacity || 'N/A'} places</div>
                </div>
            </div>`).join('');

    const gareDep = routeData.gare_depart ? routeData.gare_depart.nom_gare : routeData.itineraire.point_depart;
    const gareArr = routeData.gare_arrivee ? routeData.gare_arrivee.nom_gare : routeData.itineraire.point_arrive;

    Swal.fire({
        title: `<div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;">Horaires de départ</div>
                <div style="font-size:17px;font-weight:800;color:#1e293b;margin-top:5px;">${gareDep} → ${gareArr}</div>`,
        html: `<div style="text-align:left;margin-top:16px;max-height:350px;overflow-y:auto;">${rows}</div>`,
        width: '480px',
        showConfirmButton: false,
        showCloseButton: true,
    });
}

// --- GESTION COMPLÈTE (Modifier montant, capacité, horaires existants + nouveaux) ---
function manageSchedules(routeData) {
    window.currentDureeMinutes = parseDuration(routeData.durer_parcours);
    const currentCapacity      = routeData.horaires && routeData.horaires.length > 0 ? (routeData.horaires[0].capacity || 64) : 64;
    const gareArriveeId        = routeData.gare_arrivee ? routeData.gare_arrivee.id : '';

    const existingRows = (routeData.horaires || []).map(h => `
        <div style="display:flex;align-items:center;gap:10px;background:#f8fafc;border:1px solid #e2e8f0;padding:10px 12px;border-radius:10px;margin-bottom:8px;">
            <!-- Heure départ -->
            <div style="flex:1;">
                <label style="display:block;font-size:9px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:3px;">Départ</label>
                <input type="time" name="existing_horaires[${h.id}][heure_depart]"
                    value="${h.heure_depart}"
                    style="width:100%;padding:6px 10px;border:1px solid #e2e8f0;border-radius:8px;font-weight:700;font-size:13px;background:#fff;"
                    onchange="autoCalcExisting(this, ${h.id})">
            </div>
            <i class="fas fa-arrow-right" style="color:#94a3b8;font-size:10px;margin-top:16px;flex-shrink:0;"></i>
            <!-- Heure arrivée -->
            <div style="flex:1;">
                <label style="display:block;font-size:9px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:3px;">Arrivée</label>
                <input type="time" name="existing_horaires[${h.id}][heure_arrive]"
                    id="arr_existing_${h.id}" value="${h.heure_arrive}"
                    style="width:100%;padding:6px 10px;border:1px dashed #e2e8f0;border-radius:8px;font-weight:700;font-size:13px;background:#f1f5f9;cursor:not-allowed;" readonly>
            </div>
            <!-- Supprimer -->
            <button type="button" onclick="deleteScheduleGare(${h.id})"
                style="width:30px;height:30px;background:#fef2f2;color:#ef4444;border:none;border-radius:8px;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:14px;"
                title="Supprimer cet horaire"><i class="fas fa-trash"></i></button>
        </div>`).join('') || `<div style="text-align:center;padding:14px;border:1px dashed #e2e8f0;border-radius:8px;font-size:11px;color:#94a3b8;margin-bottom:8px;">Aucun horaire existant</div>`;

    Swal.fire({
        title: `<div style="font-size:17px;font-weight:800;color:#1e293b;">Modifier la ligne</div>
                <div style="font-size:12px;font-weight:500;color:#64748b;margin-top:4px;">
                    ${routeData.gare_depart ? routeData.gare_depart.nom_gare : '—'}
                    <i class="fas fa-arrow-right" style="margin:0 5px;"></i>
                    ${routeData.gare_arrivee ? routeData.gare_arrivee.nom_gare : '—'}
                </div>`,
        html: `
            <div style="text-align:left;padding-top:10px;">
                <form action="{{ route('gare-espace.programme.updateRoute') }}" method="POST" id="editRouteFormGare">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="itineraire_id" value="${routeData.itineraire_id}">
                    <input type="hidden" name="gare_arrivee_id" value="${gareArriveeId}">

                    {{-- Montant + Capacité --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:18px;padding:14px;background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;">
                        <div>
                            <label style="display:block;font-size:10px;font-weight:700;color:#9a3412;text-transform:uppercase;margin-bottom:5px;">
                                <i class="fas fa-coins" style="margin-right:4px;"></i>Prix du billet (FCFA)
                            </label>
                            <div style="position:relative;">
                                <input type="number" name="montant_billet" id="edit_montant"
                                    value="${routeData.montant_billet}" min="0" step="100" required
                                    style="width:100%;padding:8px 12px;border:1px solid #fdba74;border-radius:8px;font-weight:700;font-size:14px;text-align:center;background:#fff;">
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:10px;font-weight:700;color:#9a3412;text-transform:uppercase;margin-bottom:5px;">
                                <i class="fas fa-users" style="margin-right:4px;"></i>Capacité (places)
                            </label>
                            <input type="number" name="capacity" id="edit_capacity"
                                value="${currentCapacity}" min="1" max="200" required
                                style="width:100%;padding:8px 12px;border:1px solid #fdba74;border-radius:8px;font-weight:700;font-size:14px;text-align:center;background:#fff;">
                        </div>
                    </div>

                    {{-- Horaires existants --}}
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid #e2e8f0;">
                        <h4 style="font-size:10px;font-weight:800;color:#16a34a;text-transform:uppercase;margin:0;">
                            <i class="fas fa-clock" style="margin-right:4px;"></i> Horaires existants
                        </h4>
                        <button type="button" onclick="addNewRowGare()"
                            style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;padding:4px 12px;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;">
                            <i class="fas fa-plus"></i> Ajouter un horaire
                        </button>
                    </div>
                    <div style="max-height:240px;overflow-y:auto;padding-right:2px;">${existingRows}</div>

                    {{-- Nouveaux horaires --}}
                    <div id="new-aller-container-gare"></div>
                </form>
            </div>`,
        width: '600px',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-save" style="margin-right:6px;"></i> Enregistrer les modifications',
        cancelButtonText: 'Fermer',
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#94a3b8',
        preConfirm: () => {
            const montant  = document.getElementById('edit_montant')?.value;
            const capacity = document.getElementById('edit_capacity')?.value;
            if (!montant || !capacity) {
                Swal.showValidationMessage('Veuillez renseigner le montant et la capacité.');
                return false;
            }
            document.getElementById('editRouteFormGare').submit();
        }
    });
}

// Recalcul auto de l'heure d'arrivée sur les horaires existants
function autoCalcExisting(input, progId) {
    const arrInput = document.getElementById(`arr_existing_${progId}`);
    if (input.value && window.currentDureeMinutes && arrInput) {
        const [h, m] = input.value.split(':').map(Number);
        const total  = h * 60 + m + window.currentDureeMinutes;
        arrInput.value = `${String(Math.floor(total/60)%24).padStart(2,'0')}:${String(total%60).padStart(2,'0')}`;
    }
}

window.addNewRowGare = function () {
    const container = document.getElementById('new-aller-container-gare');
    const index     = Date.now();
    const depId     = `dep_${index}`;
    const arrId     = `arr_${index}`;
    container.insertAdjacentHTML('beforeend', `
        <div class="new-row-gare" style="background:#f0fdf4;padding:10px;border-radius:8px;border-left:3px solid #16a34a;margin-bottom:8px;position:relative;">
            <button type="button" onclick="this.parentElement.remove()"
                style="position:absolute;top:-8px;right:-8px;background:#ef4444;color:white;border:none;border-radius:50%;width:20px;height:20px;font-size:9px;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-times"></i>
            </button>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div>
                    <label style="font-size:9px;font-weight:700;color:#64748b;text-transform:uppercase;display:block;margin-bottom:3px;">Départ</label>
                    <input type="time" id="${depId}" name="aller_horaires[${index}][heure_depart]" required
                        style="width:100%;padding:6px 10px;border:1px solid #e2e8f0;border-radius:6px;font-weight:700;font-size:13px;"
                        onchange="autoCalcGare('${depId}','${arrId}')">
                </div>
                <div>
                    <label style="font-size:9px;font-weight:700;color:#64748b;text-transform:uppercase;display:block;margin-bottom:3px;">Arrivée</label>
                    <input type="time" id="${arrId}" name="aller_horaires[${index}][heure_arrive]" required readonly
                        style="width:100%;padding:6px 10px;border:1px dashed #e2e8f0;border-radius:6px;font-weight:700;font-size:13px;background:#f1f5f9;cursor:not-allowed;">
                </div>
            </div>
        </div>`);
};

function autoCalcGare(depId, arrId) {
    const dep = document.getElementById(depId);
    const arr = document.getElementById(arrId);
    if (dep.value && window.currentDureeMinutes) {
        const [h, m] = dep.value.split(':').map(Number);
        const total  = h * 60 + m + window.currentDureeMinutes;
        arr.value    = `${String(Math.floor(total/60)%24).padStart(2,'0')}:${String(total%60).padStart(2,'0')}`;
    }
}

function parseDuration(str) {
    if (!str) return 90;
    let h = 0, m = 0;
    const hm = str.match(/(\d+)\s*h/i); if (hm) h = parseInt(hm[1]);
    const mm = str.match(/(\d+)\s*m/i); if (mm) m = parseInt(mm[1]);
    return (h * 60 + m) || 90;
}

function deleteScheduleGare(programId) {
    Swal.fire({
        title: 'Supprimer cet horaire ?',
        text: 'Cette action est irréversible.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler',
    }).then(result => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/gare-espace/programme/${programId}`;
            form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">
                              <input type="hidden" name="_method" value="DELETE">`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection
