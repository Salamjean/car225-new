@extends('compagnie.layouts.template')

@section('page-title', 'Détail Convoi')
@section('page-subtitle', 'Gestion de la demande de convoi')

@section('content')
    <div class="dashboard-page">
        <div class="mb-3">
            <a href="{{ route('compagnie.convois.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left mr-2"></i> Retour aux convois
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success rounded-xl mb-3">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger rounded-xl mb-3">{{ session('error') }}</div>
        @endif

        {{-- Infos principales --}}
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="dash-card p-3">
                    <div class="text-muted small">Référence</div>
                    <div class="font-weight-bold">{{ $convoi->reference }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dash-card p-3">
                    <div class="text-muted small">Demandeur</div>
                    <div class="font-weight-bold">{{ trim(($convoi->user->name ?? '') . ' ' . ($convoi->user->prenom ?? '')) ?: 'Utilisateur' }}</div>
                    @if($convoi->user->email ?? null)
                        <div class="text-muted small">{{ $convoi->user->email }}</div>
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <div class="dash-card p-3">
                    <div class="text-muted small">Nombre de personnes</div>
                    <div class="font-weight-bold">{{ $convoi->nombre_personnes }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dash-card p-3">
                    <div class="text-muted small">Statut</div>
                    @php
                        $sm = [
                            'en_attente' => ['En attente',  'badge-warning'],
                            'valide'     => ['Validé',      'badge-primary'],
                            'refuse'     => ['Refusé',      'badge-danger'],
                            'paye'       => ['Payé',        'badge-success'],
                            'en_cours'   => ['En cours',    'badge-info'],
                            'termine'    => ['Terminé',     'badge-secondary'],
                            'annule'     => ['Annulé',      'badge-danger'],
                        ];
                        [$slabel, $sclass] = $sm[$convoi->statut] ?? [ucfirst($convoi->statut), 'badge-secondary'];
                    @endphp
                    <span class="badge {{ $sclass }} px-3 py-2 mt-1">{{ $slabel }}</span>
                </div>
            </div>
            <div class="col-md-6 mt-2">
                <div class="dash-card p-3">
                    <div class="text-muted small">Itinéraire</div>
                    <div class="font-weight-bold">
                        {{ $convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? '-') }}
                        <span class="text-orange mx-1">→</span>
                        {{ $convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? '-') }}
                    </div>
                </div>
            </div>
            <div class="col-md-3 mt-2">
                <div class="dash-card p-3">
                    <div class="text-muted small">Date de départ</div>
                    <div class="font-weight-bold">
                        @if($convoi->date_depart)
                            {{ \Carbon\Carbon::parse($convoi->date_depart)->format('d/m/Y') }}
                            @if($convoi->heure_depart) à {{ $convoi->heure_depart }} @endif
                        @else — @endif
                    </div>
                </div>
            </div>
            <div class="col-md-3 mt-2">
                <div class="dash-card p-3">
                    <div class="text-muted small">Date de retour</div>
                    <div class="font-weight-bold">
                        @if($convoi->date_retour)
                            {{ \Carbon\Carbon::parse($convoi->date_retour)->format('d/m/Y') }}
                            @if($convoi->heure_retour) à {{ $convoi->heure_retour }} @endif
                        @else — @endif
                    </div>
                </div>
            </div>
            @if($convoi->montant)
            <div class="col-md-3 mt-2">
                <div class="dash-card p-3">
                    <div class="text-muted small">Montant facturé</div>
                    <div class="font-weight-bold text-success">{{ number_format($convoi->montant, 0, ',', ' ') }} FCFA</div>
                </div>
            </div>
            @endif
            @if($convoi->motif_refus)
            <div class="col-md-9 mt-2">
                <div class="dash-card p-3 border-left border-danger">
                    <div class="text-muted small">Motif de refus</div>
                    <div class="font-weight-bold text-danger">{{ $convoi->motif_refus }}</div>
                </div>
            </div>
            @endif
            @if($convoi->gare)
            <div class="col-md-4 mt-2">
                <div class="dash-card p-3">
                    <div class="text-muted small">Gare assignée</div>
                    <div class="font-weight-bold"><i class="fas fa-building mr-1 text-orange"></i>{{ $convoi->gare->nom_gare }}</div>
                </div>
            </div>
            @endif
        </div>

        {{-- ACTION: VALIDER ou REFUSER (quand en_attente) --}}
        @if ($convoi->statut === 'en_attente')
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="dash-card p-4">
                    <h5 class="font-weight-bold text-success mb-3"><i class="fas fa-check-circle mr-2"></i> Valider le convoi</h5>
                    <p class="text-muted small mb-3">Saisissez le montant à facturer à l'utilisateur. Il recevra une notification par email.</p>
                    @error('montant')
                        <div class="alert alert-danger py-2 small">{{ $message }}</div>
                    @enderror
                    <form action="{{ route('compagnie.convois.valider', $convoi) }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-muted text-uppercase">Montant (FCFA) <span class="text-danger">*</span></label>
                            <input type="number" name="montant" min="100" step="1"
                                value="{{ old('montant') }}"
                                placeholder="Ex: 150000"
                                class="form-control rounded-xl font-weight-bold">
                        </div>
                        <button type="submit" class="btn btn-success btn-block rounded-xl font-weight-bold">
                            <i class="fas fa-check mr-2"></i> Valider et notifier l'utilisateur
                        </button>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="dash-card p-4">
                    <h5 class="font-weight-bold text-danger mb-3"><i class="fas fa-times-circle mr-2"></i> Refuser le convoi</h5>
                    <p class="text-muted small mb-3">Indiquez le motif du refus. L'utilisateur sera notifié par email.</p>
                    @error('motif_refus')
                        <div class="alert alert-danger py-2 small">{{ $message }}</div>
                    @enderror
                    <form action="{{ route('compagnie.convois.refuser', $convoi) }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-muted text-uppercase">Motif du refus <span class="text-danger">*</span></label>
                            <textarea name="motif_refus" rows="3" maxlength="500"
                                placeholder="Expliquez pourquoi vous refusez cette demande..."
                                class="form-control rounded-xl font-weight-bold">{{ old('motif_refus') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-danger btn-block rounded-xl font-weight-bold"
                            onclick="return confirm('Confirmer le refus de cette demande ?')">
                            <i class="fas fa-times mr-2"></i> Refuser et notifier l'utilisateur
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- ACTION: ASSIGNER UNE GARE (quand paye, sans gare encore) --}}
        @if ($convoi->statut === 'paye' && !$convoi->gare_id)
        @php
            $passagersEnregistres = $convoi->passagers->count();
            $passagersComplets    = $passagersEnregistres >= $convoi->nombre_personnes;
        @endphp
        <div class="dash-card p-4 mb-3">
            <h5 class="font-weight-bold text-primary mb-3"><i class="fas fa-building mr-2"></i> Assigner une gare</h5>

            @if (!$passagersComplets)
            <div class="alert mb-3 d-flex align-items-start gap-3"
                 style="background:#FFF7ED;border:1px solid #fed7aa;border-radius:12px;padding:14px 16px;">
                <i class="fas fa-exclamation-triangle mt-1" style="color:#f97316;font-size:16px;flex-shrink:0;"></i>
                <div>
                    <div style="font-weight:800;font-size:13px;color:#9a3412;">Passagers incomplets</div>
                    <div style="font-size:12px;color:#78350f;margin-top:2px;">
                        L'utilisateur n'a renseigné que <strong>{{ $passagersEnregistres }}</strong> passager(s) sur
                        <strong>{{ $convoi->nombre_personnes }}</strong> attendus.
                        L'assignation à une gare sera possible dès que tous les passagers seront enregistrés.
                    </div>
                </div>
            </div>
            @else
            <div class="alert mb-3 d-flex align-items-start gap-3"
                 style="background:#F0FDF4;border:1px solid #bbf7d0;border-radius:12px;padding:14px 16px;">
                <i class="fas fa-check-circle mt-1" style="color:#16a34a;font-size:16px;flex-shrink:0;"></i>
                <div style="font-size:12px;color:#14532d;font-weight:600;">
                    Tous les <strong>{{ $convoi->nombre_personnes }}</strong> passagers ont été enregistrés. Vous pouvez assigner une gare.
                </div>
            </div>
            @endif

            @error('gare_id')
                <div class="alert alert-danger py-2 small">{{ $message }}</div>
            @enderror

            <form action="{{ route('compagnie.convois.assigner-gare', $convoi) }}" method="POST" class="row align-items-end">
                @csrf
                <div class="col-md-6">
                    <label class="small font-weight-bold text-muted text-uppercase">Gare <span class="text-danger">*</span></label>
                    <select name="gare_id" class="form-control rounded-xl font-weight-bold" {{ !$passagersComplets ? 'disabled' : '' }}>
                        <option value="">Choisir une gare</option>
                        @foreach ($gares as $gare)
                            <option value="{{ $gare->id }}" @selected(old('gare_id') == $gare->id)>{{ $gare->nom_gare }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mt-2 mt-md-0">
                    <button type="submit" class="btn btn-primary btn-block rounded-xl font-weight-bold"
                        {{ !$passagersComplets ? 'disabled' : '' }}
                        @if(!$passagersComplets) title="En attente des passagers" @endif>
                        <i class="fas fa-paper-plane mr-2"></i> Assigner la gare
                    </button>
                </div>
            </form>
        </div>
        @endif

        {{-- Suivi GPS (visible si en_cours) --}}
        @if (in_array($convoi->statut, ['en_cours', 'termine']))
        <div class="dash-card p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="text-muted small">Suivi GPS temps réel</div>
                <span class="badge badge-light" id="trackingStatusBadge">--</span>
            </div>
            <div class="font-weight-bold mb-1" id="trackingCoords">Position: --</div>
            <div class="small text-muted" id="trackingMeta">Dernière mise à jour: --</div>
            <a href="#" id="trackingMapLink" target="_blank" class="btn btn-sm btn-outline-primary mt-2 d-none">
                <i class="fas fa-map-marker-alt mr-1"></i> Ouvrir sur Google Maps
            </a>
        </div>
        @endif

        {{-- Liste passagers --}}
        <div class="dash-card">
            <div class="dash-card-head">
                <h3 class="dash-card-title m-0">
                    <i class="fas fa-list mr-2 text-orange"></i>
                    Passagers ({{ $convoi->passagers->count() }})
                </h3>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Prénoms</th>
                            <th>Contact</th>
                            <th>Contact d'urgence</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($convoi->passagers as $index => $passager)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="font-weight-bold">{{ $passager->nom }}</td>
                                <td>{{ $passager->prenoms }}</td>
                                <td>{{ $passager->contact }}</td>
                                <td>{{ $passager->contact_urgence ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Aucun passager enregistré pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@if (in_array($convoi->statut, ['en_cours', 'termine']))
<script>
document.addEventListener('DOMContentLoaded', function () {
    const endpoint = "{{ route('compagnie.convois.location', $convoi->id) }}";
    const coordsEl = document.getElementById('trackingCoords');
    const metaEl = document.getElementById('trackingMeta');
    const badgeEl = document.getElementById('trackingStatusBadge');
    const mapLinkEl = document.getElementById('trackingMapLink');

    function updateTracking() {
        fetch(endpoint, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                badgeEl.textContent = (data.statut || '--').replace('_', ' ');
                if (data.latitude !== null && data.longitude !== null) {
                    coordsEl.textContent = `Position: ${data.latitude}, ${data.longitude}`;
                    metaEl.textContent = `Mise à jour: ${data.last_update} • Chauffeur: ${data.chauffeur} • Véhicule: ${data.vehicule}`;
                    mapLinkEl.href = `https://www.google.com/maps?q=${data.latitude},${data.longitude}`;
                    mapLinkEl.classList.remove('d-none');
                } else {
                    coordsEl.textContent = 'Position: en attente du GPS chauffeur';
                    metaEl.textContent = `Mise à jour: ${data.last_update}`;
                    mapLinkEl.classList.add('d-none');
                }
            }).catch(() => {});
    }

    updateTracking();
    setInterval(updateTracking, 7000);
});
</script>
@endif
@endsection
