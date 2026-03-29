@extends('compagnie.layouts.template')

@section('page-title', 'Localisation GPS des Gares')
@section('page-subtitle', 'Gérez les positions GPS de vos gares')

@section('styles')
<style>
    /* ── Cards demandes ── */
    .req-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px 22px;
        transition: box-shadow 0.2s, transform 0.2s;
        display: flex;
        align-items: center;
        gap: 18px;
        flex-wrap: wrap;
    }
    .req-card:hover { box-shadow: var(--shadow-md); transform: translateY(-1px); }
    .req-card.pending { border-left: 4px solid var(--blue); }

    .req-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .req-info { flex: 1; min-width: 0; }
    .req-name { font-size: 15px; font-weight: 800; color: var(--text-1); }
    .req-meta { font-size: 12px; color: var(--text-3); margin-top: 2px; }

    .coords-pill {
        display: inline-flex; align-items: center; gap: 6px;
        background: var(--surface-2); border: 1px solid var(--border-strong);
        border-radius: 8px; padding: 5px 10px;
        font-family: 'Courier New', monospace;
        font-size: 12px; font-weight: 700; color: var(--text-2);
    }
    .time-pill {
        display: inline-flex; align-items: center; gap: 5px;
        background: var(--surface-2); border: 1px solid var(--border);
        border-radius: 8px; padding: 5px 10px;
        font-size: 12px; color: var(--text-3);
    }

    .req-actions { display: flex; gap: 8px; flex-shrink: 0; }

    /* Buttons */
    .btn-approve {
        display: inline-flex; align-items: center; gap: 7px;
        background: #059669; color: white;
        padding: 9px 18px; border-radius: 9px;
        font-size: 13px; font-weight: 700;
        border: none; cursor: pointer;
        transition: background 0.15s, box-shadow 0.15s;
    }
    .btn-approve:hover { background: #047857; box-shadow: 0 4px 12px rgba(5,150,105,0.25); }

    .btn-reject {
        display: inline-flex; align-items: center; gap: 7px;
        background: #FFF1F2; color: #E11D48;
        padding: 9px 18px; border-radius: 9px;
        font-size: 13px; font-weight: 700;
        border: 1px solid #FECDD3; cursor: pointer;
        transition: background 0.15s;
    }
    .btn-reject:hover { background: #FFE4E6; }

    .btn-manual {
        display: inline-flex; align-items: center; gap: 7px;
        background: var(--orange-light); color: var(--orange-dark);
        padding: 7px 14px; border-radius: 8px;
        font-size: 12px; font-weight: 700;
        border: 1px solid var(--orange-mid); cursor: pointer;
        transition: background 0.15s;
    }
    .btn-manual:hover { background: var(--orange-mid); }

    /* Empty state */
    .empty-state {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 48px 24px;
        text-align: center; color: var(--text-3);
    }
    .empty-state i { font-size: 40px; margin-bottom: 12px; display: block; }
    .empty-state p { font-size: 14px; font-weight: 600; }

    /* Section headers */
    .section-head {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 14px;
    }
    .section-label {
        font-size: 11px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.6px;
        color: var(--text-3);
        display: flex; align-items: center; gap: 8px;
    }
    .section-label .dot-blue { width: 7px; height: 7px; background: var(--blue); border-radius: 50%; animation: pulseBlue 1.8s ease-in-out infinite; }
    @keyframes pulseBlue {
        0%,100% { box-shadow: 0 0 0 0 rgba(59,130,246,0.4); }
        50% { box-shadow: 0 0 0 6px rgba(59,130,246,0); }
    }
    .count-badge {
        background: var(--blue); color: white;
        font-size: 11px; font-weight: 800;
        padding: 2px 9px; border-radius: 999px;
    }
    .count-badge.zero { background: var(--surface-2); color: var(--text-3); }

    /* History table */
    .hist-table { width: 100%; border-collapse: collapse; }
    .hist-table th {
        font-size: 10px; font-weight: 800; text-transform: uppercase;
        letter-spacing: 0.5px; color: var(--text-3);
        padding: 10px 16px; text-align: left;
        border-bottom: 1px solid var(--border);
        background: var(--surface-2);
    }
    .hist-table td {
        padding: 12px 16px; border-bottom: 1px solid var(--border);
        font-size: 13px; color: var(--text-1);
    }
    .hist-table tr:last-child td { border-bottom: none; }
    .hist-table tr:hover td { background: var(--surface-2); }

    .badge-approved { background: #ECFDF5; color: #059669; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 6px; }
    .badge-rejected { background: #FFF1F2; color: #DC2626; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 6px; }

    /* Gare GPS cards */
    .gare-gps-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 14px; }
    .gare-gps-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 18px;
        display: flex; flex-direction: column; gap: 12px;
    }
    .gare-gps-card-top { display: flex; align-items: center; gap: 12px; }
    .gare-gps-avatar {
        width: 38px; height: 38px; border-radius: 10px;
        background: var(--orange-light); color: var(--orange-dark);
        display: flex; align-items: center; justify-content: center;
        font-size: 15px; font-weight: 800; flex-shrink: 0;
    }
    .gare-gps-status {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px;
    }
    .gps-ok { background: #ECFDF5; color: #059669; }
    .gps-missing { background: #FFFBEB; color: #B45309; }

    /* Modal */
    .modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.45); z-index: 9999;
        align-items: center; justify-content: center;
        backdrop-filter: blur(4px);
    }
    .modal-overlay.open { display: flex; }
    .modal-box {
        background: var(--surface); border-radius: 20px;
        padding: 32px; max-width: 440px; width: 90%;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        animation: modalIn 0.2s ease;
    }
    @keyframes modalIn { from { transform: translateY(-12px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .modal-title { font-size: 18px; font-weight: 800; color: var(--text-1); margin-bottom: 6px; }
    .modal-subtitle { font-size: 13px; color: var(--text-3); margin-bottom: 24px; }
    .modal-input-label { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-3); margin-bottom: 8px; display: block; }
    .modal-input {
        width: 100%; padding: 11px 14px; border: 1.5px solid var(--border-strong);
        border-radius: 10px; font-size: 14px; font-weight: 600;
        background: var(--surface-2); color: var(--text-1); outline: none;
        transition: border-color 0.15s, background 0.15s;
    }
    .modal-input:focus { border-color: var(--orange); background: white; box-shadow: 0 0 0 3px var(--orange-light); }
    .modal-footer { display: flex; gap: 10px; margin-top: 24px; }
    .modal-btn-primary {
        flex: 1; padding: 11px; background: var(--orange); color: white;
        border: none; border-radius: 10px; font-size: 14px; font-weight: 700;
        cursor: pointer; transition: background 0.15s;
    }
    .modal-btn-primary:hover { background: var(--orange-dark); }
    .modal-btn-cancel {
        padding: 11px 20px; background: var(--surface-2); color: var(--text-2);
        border: 1px solid var(--border-strong); border-radius: 10px; font-size: 14px; font-weight: 700;
        cursor: pointer; transition: background 0.15s;
    }
    .modal-btn-cancel:hover { background: var(--border); }
</style>
@endsection

@section('content')
<div class="dashboard-page">

    {{-- ── HEADER ── --}}
    <div class="dash-header">
        <div>
            <div class="dash-title">
                <i class="fas fa-map-marked-alt" style="color:var(--orange);font-size:22px;margin-right:10px;"></i>
                Localisation GPS des Gares
            </div>
            <div class="dash-subtitle">Approuvez les demandes et gérez les coordonnées de vos gares</div>
        </div>
        <a href="{{ route('gare.index') }}" style="display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:var(--surface);border:1px solid var(--border-strong);border-radius:10px;font-size:13px;font-weight:700;color:var(--text-2);text-decoration:none;transition:background 0.15s;"
           onmouseover="this.style.background='var(--surface-2)'" onmouseout="this.style.background='var(--surface)'">
            <i class="fas fa-building"></i> Gestion des gares
        </a>
    </div>

    {{-- ── STAT PILLS ── --}}
    <div class="metric-grid" style="grid-template-columns:repeat(3,1fr);max-width:600px;margin-bottom:28px;">
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-blue"><i class="fas fa-clock"></i></div>
                <span class="metric-tag mt-slate">En attente</span>
            </div>
            <div class="metric-label">Demandes</div>
            <div class="metric-value">{{ $pendingRequests->count() }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-green"><i class="fas fa-check-circle"></i></div>
                <span class="metric-tag mt-green">Actives</span>
            </div>
            <div class="metric-label">Gares géolocalisées</div>
            <div class="metric-value">{{ $allGares->whereNotNull('latitude')->count() }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-icon mi-amber"><i class="fas fa-exclamation-triangle"></i></div>
                <span class="metric-tag mt-amber">Manquant</span>
            </div>
            <div class="metric-label">Sans coordonnées</div>
            <div class="metric-value">{{ $allGares->whereNull('latitude')->count() }}</div>
        </div>
    </div>

    {{-- ── DEMANDES EN ATTENTE ── --}}
    <div style="margin-bottom: 32px;">
        <div class="section-head">
            <div class="section-label">
                <span class="dot-blue"></span>
                Demandes en attente
                <span class="count-badge {{ $pendingRequests->isEmpty() ? 'zero' : '' }}">{{ $pendingRequests->count() }}</span>
            </div>
        </div>

        @if($pendingRequests->isEmpty())
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>Aucune demande en attente</p>
            <span style="font-size:12px;margin-top:4px;display:block;">Vos gares n'ont pas soumis de demande de mise à jour GPS.</span>
        </div>
        @else
        <div style="display:flex;flex-direction:column;gap:10px;">
            @foreach($pendingRequests as $req)
            <div class="req-card pending" id="card-{{ $req->id }}">
                <div class="req-icon" style="background:#EFF6FF;color:#2563EB;">
                    <i class="fas fa-warehouse"></i>
                </div>
                <div class="req-info">
                    <div class="req-name">{{ $req->gare->nom_gare }}</div>
                    <div class="req-meta">{{ $req->gare->ville }} &nbsp;·&nbsp; {{ $req->gare->email }}</div>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;">
                        <span class="coords-pill">
                            <i class="fas fa-map-pin" style="color:var(--orange);"></i>
                            {{ number_format($req->latitude, 6) }}, {{ number_format($req->longitude, 6) }}
                        </span>
                        <span class="time-pill">
                            <i class="fas fa-clock"></i>
                            {{ $req->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
                <div class="req-actions">
                    <button class="btn-approve" onclick="approveRequest({{ $req->id }}, '{{ addslashes($req->gare->nom_gare) }}')">
                        <i class="fas fa-check"></i> Approuver
                    </button>
                    <button class="btn-reject" onclick="rejectRequest({{ $req->id }})">
                        <i class="fas fa-times"></i> Rejeter
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ── GESTION MANUELLE ── --}}
    <div style="margin-bottom: 32px;">
        <div class="section-head">
            <div class="section-label">
                <i class="fas fa-pencil-alt" style="color:var(--orange);"></i>
                Modifier manuellement les coordonnées
            </div>
        </div>
        <div class="gare-gps-grid">
            @foreach($allGares as $gare)
            <div class="gare-gps-card">
                <div class="gare-gps-card-top">
                    <div class="gare-gps-avatar">{{ strtoupper(substr($gare->nom_gare, 0, 2)) }}</div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:14px;font-weight:800;color:var(--text-1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $gare->nom_gare }}</div>
                        <div style="font-size:12px;color:var(--text-3);">{{ $gare->ville }}</div>
                    </div>
                </div>

                @if($gare->latitude && $gare->longitude)
                <div style="display:flex;align-items:center;gap:6px;">
                    <span class="gare-gps-status gps-ok"><i class="fas fa-map-pin"></i> GPS défini</span>
                </div>
                <div class="coords-pill" style="font-size:11px;">
                    {{ number_format($gare->latitude, 5) }}, {{ number_format($gare->longitude, 5) }}
                </div>
                @else
                <div>
                    <span class="gare-gps-status gps-missing"><i class="fas fa-exclamation-triangle"></i> Non défini</span>
                </div>
                @endif

                <button class="btn-manual" onclick="openManualModal({{ $gare->id }}, '{{ addslashes($gare->nom_gare) }}', '{{ $gare->latitude ?? '' }}', '{{ $gare->longitude ?? '' }}')">
                    <i class="fas fa-crosshairs"></i> Modifier les coordonnées
                </button>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── HISTORIQUE ── --}}
    @if($historyRequests->isNotEmpty())
    <div>
        <div class="section-head">
            <div class="section-label">
                <i class="fas fa-history" style="color:var(--text-3);"></i>
                Historique des demandes
            </div>
            <span style="font-size:12px;color:var(--text-3);">{{ $historyRequests->count() }} dernière(s)</span>
        </div>

        <div class="dash-card">
            <table class="hist-table">
                <thead>
                    <tr>
                        <th>Gare</th>
                        <th>Coordonnées demandées</th>
                        <th>Statut</th>
                        <th>Date de traitement</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historyRequests as $req)
                    <tr>
                        <td>
                            <div style="font-weight:700;">{{ $req->gare->nom_gare }}</div>
                            <div style="font-size:11px;color:var(--text-3);">{{ $req->gare->ville }}</div>
                        </td>
                        <td>
                            <span class="coords-pill">{{ number_format($req->latitude, 5) }}, {{ number_format($req->longitude, 5) }}</span>
                        </td>
                        <td>
                            @if($req->statut === 'approved')
                                <span class="badge-approved"><i class="fas fa-check" style="margin-right:4px;"></i>Approuvée</span>
                            @else
                                <span class="badge-rejected"><i class="fas fa-times" style="margin-right:4px;"></i>Rejetée</span>
                                @if($req->rejected_reason)
                                    <div style="font-size:11px;color:var(--text-3);margin-top:3px;font-style:italic;">{{ $req->rejected_reason }}</div>
                                @endif
                            @endif
                        </td>
                        <td style="font-size:12px;color:var(--text-3);">{{ $req->updated_at->format('d/m/Y à H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

{{-- ── MODAL MODIFICATION MANUELLE ── --}}
<div class="modal-overlay" id="manualModal">
    <div class="modal-box">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
            <div style="width:40px;height:40px;border-radius:10px;background:var(--orange-light);color:var(--orange-dark);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div>
                <div class="modal-title" id="modalGareName">Modifier les coordonnées</div>
                <div class="modal-subtitle" style="margin-bottom:0;">Entrez les coordonnées GPS exactes de la gare</div>
            </div>
        </div>
        <hr style="border:none;border-top:1px solid var(--border);margin:20px 0;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:4px;">
            <div>
                <label class="modal-input-label">Latitude</label>
                <input class="modal-input" id="manualLat" type="number" step="0.000001" placeholder="ex: 5.348441">
            </div>
            <div>
                <label class="modal-input-label">Longitude</label>
                <input class="modal-input" id="manualLng" type="number" step="0.000001" placeholder="ex: -4.030500">
            </div>
        </div>
        <div style="font-size:11px;color:var(--text-3);margin-top:10px;">
            <i class="fas fa-info-circle" style="color:var(--orange);"></i>
            Astuce : copiez les coordonnées depuis Google Maps en faisant un clic droit sur l'emplacement de la gare.
        </div>
        <div class="modal-footer">
            <button class="modal-btn-cancel" onclick="closeManualModal()">Annuler</button>
            <button class="modal-btn-primary" onclick="submitManualUpdate()">
                <i class="fas fa-save" style="margin-right:6px;"></i>Enregistrer
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const CSRF = '{{ csrf_token() }}';
let currentGareId = null;

/* ── Demandes ── */
function approveRequest(id, gareName) {
    Swal.fire({
        icon: 'question',
        title: 'Approuver la demande ?',
        html: `<p style="font-size:14px;color:#6b7280;">La position GPS de <strong>${gareName}</strong> sera mise à jour et la gare recevra un email de confirmation.</p>`,
        confirmButtonText: 'Oui, approuver',
        confirmButtonColor: '#059669',
        showCancelButton: true,
        cancelButtonText: 'Annuler',
        customClass: { popup: 'rounded-3xl' }
    }).then(result => {
        if (!result.isConfirmed) return;
        Swal.fire({ title: 'Traitement...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        fetch(`/company/gare-location-requests/${id}/approve`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF }
        }).then(r => r.json()).then(data => {
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Approuvée !', text: data.message, confirmButtonColor: '#059669' })
                    .then(() => window.location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Erreur', text: data.message, confirmButtonColor: '#d33' });
            }
        }).catch(err => {
            console.error(err);
            Swal.fire({ icon: 'error', title: 'Erreur réseau', text: 'Impossible de contacter le serveur.', confirmButtonColor: '#d33' });
        });
    });
}

function rejectRequest(id) {
    Swal.fire({
        icon: 'warning',
        title: 'Rejeter la demande ?',
        html: `<p style="font-size:13px;color:#6b7280;margin-bottom:10px;">Vous pouvez indiquer une raison (optionnel) :</p>
               <input id="swal-reason" class="swal2-input" placeholder="Raison du rejet...">`,
        confirmButtonText: 'Rejeter',
        confirmButtonColor: '#dc2626',
        showCancelButton: true,
        cancelButtonText: 'Annuler',
    }).then(result => {
        if (!result.isConfirmed) return;
        const reason = document.getElementById('swal-reason')?.value || '';
        Swal.fire({ title: 'Traitement...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        fetch(`/company/gare-location-requests/${id}/reject`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ reason })
        }).then(r => r.json()).then(data => {
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Rejetée', text: data.message, confirmButtonColor: '#ea580c' })
                    .then(() => window.location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Erreur', text: data.message, confirmButtonColor: '#d33' });
            }
        }).catch(err => {
            console.error(err);
            Swal.fire({ icon: 'error', title: 'Erreur réseau', text: 'Impossible de contacter le serveur.', confirmButtonColor: '#d33' });
        });
    });
}

/* ── Modification manuelle ── */
function openManualModal(gareId, gareName, currentLat, currentLng) {
    currentGareId = gareId;
    document.getElementById('modalGareName').textContent = gareName;
    document.getElementById('manualLat').value = currentLat || '';
    document.getElementById('manualLng').value = currentLng || '';
    document.getElementById('manualModal').classList.add('open');
}

function closeManualModal() {
    document.getElementById('manualModal').classList.remove('open');
    currentGareId = null;
}

function submitManualUpdate() {
    const lat = parseFloat(document.getElementById('manualLat').value);
    const lng = parseFloat(document.getElementById('manualLng').value);

    if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
        Swal.fire({ icon: 'error', title: 'Coordonnées invalides', text: 'Vérifiez que la latitude est entre -90 et 90, et la longitude entre -180 et 180.', confirmButtonColor: '#ea580c' });
        return;
    }

    fetch(`/company/gare-location-requests/gare/${currentGareId}/update-location`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ latitude: lat, longitude: lng })
    }).then(r => r.json()).then(data => {
        if (data.success) {
            closeManualModal();
            Swal.fire({ icon: 'success', title: 'Coordonnées mises à jour !', text: data.message, confirmButtonColor: '#059669', timer: 2500, timerProgressBar: true })
                .then(() => window.location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Erreur', text: data.message || 'Une erreur est survenue.', confirmButtonColor: '#d33' });
        }
    }).catch(() => {
        Swal.fire({ icon: 'error', title: 'Erreur réseau', text: 'Impossible de contacter le serveur.', confirmButtonColor: '#d33' });
    });
}

// Fermer en cliquant en dehors
document.getElementById('manualModal').addEventListener('click', function(e) {
    if (e.target === this) closeManualModal();
});
</script>
@endsection
