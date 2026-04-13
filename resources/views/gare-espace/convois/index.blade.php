@extends('gare-espace.layouts.template')

@section('title', 'Convois reçus')

@section('styles')
<style>
    .convoi-shell {
        background: linear-gradient(180deg, #f9fafb 0%, #f3f4f6 100%);
        border-radius: 28px;
        padding: 24px;
        border: 1px solid #f1f5f9;
    }

    .convoi-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 18px;
    }

    .convoi-head h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 900;
        color: #0f172a;
        letter-spacing: -0.5px;
    }

    .convoi-head p {
        margin: 4px 0 0;
        font-size: 13px;
        font-weight: 600;
        color: #6b7280;
    }

    .text-orange {
        color: #f97316;
    }

    .metric-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }

    .metric-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 14px 16px;
    }

    .metric-label {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #9ca3af;
        font-weight: 900;
        margin-bottom: 6px;
    }

    .metric-value {
        font-size: 24px;
        line-height: 1;
        font-weight: 900;
        color: #111827;
    }

    .tab-wrap {
        display: inline-flex;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 4px;
        gap: 4px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }

    .tab-link {
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        text-decoration: none !important;
        padding: 8px 12px;
        border-radius: 10px;
        transition: .2s ease;
    }

    .tab-link.active {
        background: #f97316;
        color: #fff;
        box-shadow: 0 6px 18px rgba(249, 115, 22, 0.25);
    }

    .table-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        overflow: hidden;
    }

    .table-modern thead th {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        font-weight: 900;
        color: #94a3b8;
        background: #f8fafc;
        padding: 14px 16px;
        border-bottom: 1px solid #eef2f7;
    }

    .table-modern tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        vertical-align: middle;
    }

    .table-modern tbody tr:hover td {
        background: #fff7ed;
    }

    .ref-badge {
        display: inline-flex;
        padding: 6px 10px;
        border-radius: 10px;
        background: #fff7ed;
        color: #ea580c;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: 0.3px;
    }

    .count-badge {
        display: inline-flex;
        padding: 5px 10px;
        border-radius: 10px;
        background: #fff7ed;
        color: #ea580c;
        font-size: 11px;
        font-weight: 900;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        padding: 6px 10px;
        border-radius: 999px;
    }

    .status-pill::before {
        content: "";
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
    }

    .status-wait { background: #fff7ed; color: #ea580c; }
    .status-ok { background: #ecfdf5; color: #059669; }
    .status-cancel { background: #fef2f2; color: #dc2626; }

    .btn-see {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        border-radius: 10px;
        background: #fff7ed;
        color: #ea580c;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        text-decoration: none !important;
        border: 1px solid #fed7aa;
        transition: .2s ease;
    }

    .btn-see:hover {
        background: #f97316;
        color: #fff;
    }

    .btn-assign {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        border-radius: 10px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        text-decoration: none !important;
        border: 1px solid #bfdbfe;
        transition: .2s ease;
        cursor: pointer;
    }

    .btn-assign:hover {
        background: #1d4ed8;
        color: #fff;
    }

    .actions-wrap {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .modal-overlay-convoi {
        position: fixed;
        inset: 0;
        background: rgba(2, 6, 23, 0.6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 12px;
    }

    .modal-overlay-convoi.open {
        display: flex;
    }

    .modal-card-convoi {
        width: 100%;
        max-width: 560px;
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }

    .modal-head-convoi {
        padding: 14px 16px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title-convoi {
        font-size: 14px;
        font-weight: 900;
        color: #0f172a;
        margin: 0;
    }

    .modal-body-convoi {
        padding: 16px;
    }

    .modal-label-convoi {
        display: block;
        margin-bottom: 6px;
        font-size: 11px;
        font-weight: 900;
        color: #64748b;
        text-transform: uppercase;
    }

    .modal-input-convoi {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        background: #f8fafc;
    }

    .modal-foot-convoi {
        padding: 12px 16px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    @media (max-width: 991px) {
        .metric-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
</style>
@endsection

@section('content')
@php
    $allCount    = $convois->total();
    $current     = $convois->getCollection();
    $payeCount   = $current->where('statut', 'paye')->count();
    $coursCount  = $current->where('statut', 'en_cours')->count();
    $termineCount = $current->where('statut', 'termine')->count();
@endphp
<div class="convoi-shell">
    @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
    @endif

    <div class="convoi-head">
        <div>
            <h1>Convois <span class="text-orange">reçus</span></h1>
            <p>Convois rattachés à votre gare via l'itinéraire choisi.</p>
        </div>
    </div>

    <div class="metric-grid">
        <div class="metric-card">
            <div class="metric-label">Total</div>
            <div class="metric-value">{{ $allCount }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Payés — à affecter</div>
            <div class="metric-value" style="color:#7c3aed;">{{ $payeCount }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">En cours</div>
            <div class="metric-value" style="color:#0284c7;">{{ $coursCount }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Terminés</div>
            <div class="metric-value" style="color:#059669;">{{ $termineCount }}</div>
        </div>
    </div>

    <div class="tab-wrap">
        <a href="{{ route('gare-espace.convois.index') }}" class="tab-link {{ ($statut ?? 'all') === 'all' ? 'active' : '' }}">Tous</a>
        <a href="{{ route('gare-espace.convois.index', ['statut' => 'paye']) }}" class="tab-link {{ ($statut ?? 'all') === 'paye' ? 'active' : '' }}">Payés</a>
        <a href="{{ route('gare-espace.convois.index', ['statut' => 'en_cours']) }}" class="tab-link {{ ($statut ?? 'all') === 'en_cours' ? 'active' : '' }}">En cours</a>
        <a href="{{ route('gare-espace.convois.index', ['statut' => 'termine']) }}" class="tab-link {{ ($statut ?? 'all') === 'termine' ? 'active' : '' }}">Terminés</a>
        <a href="{{ route('gare-espace.convois.index', ['statut' => 'annule']) }}" class="tab-link {{ ($statut ?? 'all') === 'annule' ? 'active' : '' }}">Annulés</a>
    </div>

    <div class="table-card">
        <div class="overflow-x-auto">
            <table class="w-full text-left table-modern">
                <thead>
                    <tr>
                        <th style="text-align: center;">Référence</th>
                        <th style="text-align: center;">Compagnie</th>
                        <th style="text-align: center;">Itinéraire</th>
                        <th style="text-align: center;">Personnes</th>
                        <th style="text-align: center;">Statut</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($convois as $convoi)
                    @php
                        $trajet = $convoi->lieu_depart
                            ? ($convoi->lieu_depart . ' → ' . $convoi->lieu_retour)
                            : ($convoi->itineraire ? ($convoi->itineraire->point_depart . ' → ' . $convoi->itineraire->point_arrive) : '-');

                        $sm = [
                            'paye'     => ['Payé',      'background:#f5f3ff;color:#7c3aed;'],
                            'en_cours' => ['En cours',  'background:#e0f2fe;color:#0284c7;'],
                            'termine'  => ['Terminé',   'background:#ecfdf5;color:#059669;'],
                            'annule'   => ['Annulé',    'background:#fef2f2;color:#dc2626;'],
                            'refuse'   => ['Refusé',    'background:#fef2f2;color:#dc2626;'],
                            'valide'   => ['Validé',    'background:#ecfdf5;color:#059669;'],
                        ];
                        [$slabel, $sstyle] = $sm[$convoi->statut] ?? [ucfirst(str_replace('_',' ',$convoi->statut)), 'background:#f1f5f9;color:#475569;'];

                        // Peut affecter si paye ET pas encore de chauffeur
                        $canAssign = $convoi->statut === 'paye' && !$convoi->personnel_id;
                    @endphp
                    <tr>
                        <td><span class="ref-badge">{{ $convoi->reference }}</span></td>
                        <td>{{ $convoi->compagnie->name ?? '-' }}</td>
                        <td style="font-size:12px;">{{ $trajet }}</td>
                        <td class="text-center">
                            <span class="count-badge">{{ $convoi->nombre_personnes }}</span>
                        </td>
                        <td class="text-center">
                            <span class="status-pill" style="{{ $sstyle }}">{{ $slabel }}</span>
                        </td>
                        <td class="text-right">
                            <div class="actions-wrap">
                                @if($canAssign)
                                    <button type="button"
                                            class="btn-assign"
                                            onclick="openAssignModal('{{ $convoi->id }}', '{{ $convoi->reference }}')">
                                        <i class="fas fa-user-check"></i> Affecter
                                    </button>
                                @elseif($convoi->statut === 'paye' && $convoi->personnel_id)
                                    <span style="font-size:11px;font-weight:700;color:#059669;">
                                        <i class="fas fa-check-circle mr-1"></i>Affecté
                                    </span>
                                @endif
                                <a href="{{ route('gare-espace.convois.show', $convoi->id) }}" class="btn-see">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-sm text-gray-500 py-10">
                            Aucun convoi reçu pour votre gare.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($convois->hasPages())
            <div class="px-4 py-3 border-top border-gray-100 bg-white">
                {{ $convois->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    const availableChauffeurs = @json(
        $chauffeurs->map(function ($chauffeur) {
            return [
                'id' => $chauffeur->id,
                'label' => trim(($chauffeur->prenom ?? '') . ' ' . ($chauffeur->name ?? '')),
            ];
        })->values()
    );

    const availableVehicules = @json(
        $vehicules->map(function ($vehicule) {
            return [
                'id' => $vehicule->id,
                'label' => $vehicule->immatriculation . ' - ' . ($vehicule->modele ?: 'Vehicule') . ' (' . $vehicule->nombre_place . ' places)',
            ];
        })->values()
    );

    function openAssignModal(convoiId, convoiRef) {
        const chauffeurOptions = ['<option value="">Choisir un chauffeur</option>']
            .concat(availableChauffeurs.map(item => `<option value="${item.id}">${item.label}</option>`))
            .join('');

        const vehiculeOptions = ['<option value="">Choisir un véhicule</option>']
            .concat(availableVehicules.map(item => `<option value="${item.id}">${item.label}</option>`))
            .join('');

        Swal.fire({
            title: 'Affecter chauffeur et véhicule',
            html: `
                <p class="text-muted mb-3" style="font-size:12px;">Convoi: <strong>${convoiRef || '-'}</strong></p>
                <div style="text-align:left;margin-bottom:10px;">
                    <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:700;color:#475569;">Chauffeur disponible</label>
                    <select id="swalPersonnelId" class="swal2-input" style="margin:0;width:100%;">
                        ${chauffeurOptions}
                    </select>
                </div>
                <div style="text-align:left;">
                    <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:700;color:#475569;">Véhicule disponible</label>
                    <select id="swalVehiculeId" class="swal2-input" style="margin:0;width:100%;">
                        ${vehiculeOptions}
                    </select>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Valider l\'affectation',
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#f59e0b',
            focusConfirm: false,
            preConfirm: () => {
                const personnelId = document.getElementById('swalPersonnelId').value;
                const vehiculeId = document.getElementById('swalVehiculeId').value;

                if (!personnelId || !vehiculeId) {
                    Swal.showValidationMessage('Choisis un chauffeur et un véhicule.');
                    return false;
                }

                return { personnelId, vehiculeId };
            }
        }).then((result) => {
            if (!result.isConfirmed) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/gare-espace/convois/${convoiId}/assign`;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            const personnel = document.createElement('input');
            personnel.type = 'hidden';
            personnel.name = 'personnel_id';
            personnel.value = result.value.personnelId;
            form.appendChild(personnel);

            const vehicule = document.createElement('input');
            vehicule.type = 'hidden';
            vehicule.name = 'vehicule_id';
            vehicule.value = result.value.vehiculeId;
            form.appendChild(vehicule);

            document.body.appendChild(form);
            form.submit();
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && Swal.isVisible()) {
            Swal.close();
        }
    });
</script>
@endpush
@endsection

