@extends('chauffeur.layouts.template')

@section('styles')
<style>
    .sig-page { background: linear-gradient(135deg, #f8fafc 0%, #fff1f2 100%); min-height: 80vh; }
    .form-card { background: white; border-radius: 1.5rem; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px rgba(0,0,0,0.06); overflow: hidden; }
    .form-card .top-bar { height: 5px; background: linear-gradient(135deg, #FF4B2B, #FF416C); }
    .step-num { width: 32px; height: 32px; background: linear-gradient(135deg, #FF4B2B, #FF416C); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 800; flex-shrink: 0; }
    .form-control, .form-select { border-radius: 0.75rem; padding: 0.75rem 1rem; border: 1.5px solid #e2e8f0; background: #f8fafc; transition: all 0.2s; }
    .form-control:focus, .form-select:focus { background: white; box-shadow: 0 0 0 4px rgba(255,75,43,0.1); border-color: #FF4B2B; }
    .type-selector { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
    @media(min-width: 576px) { .type-selector { grid-template-columns: repeat(4, 1fr); } }
    .type-option input { display: none; }
    .type-card { padding: 1.25rem 0.75rem; text-align: center; border: 2px solid #f1f5f9; border-radius: 1rem; transition: all 0.25s cubic-bezier(.4,0,.2,1); background: white; cursor: pointer; }
    .type-card:hover { border-color: #fda4af; background: #fff5f5; transform: translateY(-2px); }
    .type-option input:checked + .type-card { border-color: #FF4B2B; background: linear-gradient(135deg, #fff5f2, #ffe4e6); box-shadow: 0 4px 15px rgba(255,75,43,0.15); transform: translateY(-2px); }
    .type-icon { font-size: 1.75rem; margin-bottom: 0.5rem; display: block; }
    .type-accident .type-icon { color: #dc2626; } .type-panne .type-icon { color: #ea580c; }
    .type-retard .type-icon { color: #ca8a04; } .type-autre .type-icon { color: #6b7280; }
    .btn-submit { background: linear-gradient(135deg, #FF4B2B, #FF416C); color: white; border: none; padding: 1rem 2rem; border-radius: 100px; font-weight: 700; font-size: 1rem; transition: all 0.3s; width: 100%; }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255,75,43,0.35); color: white; }
    .active-voyage-card { background: linear-gradient(135deg, #ecfdf5, #d1fae5); border: 2px solid #6ee7b7; border-radius: 1rem; padding: 1rem 1.25rem; }
    .active-voyage-card .pulse-dot { width: 10px; height: 10px; background: #22c55e; border-radius: 50%; display: inline-block; animation: pulse-green 1.5s infinite; }
    @keyframes pulse-green { 0%,100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.5); } 50% { box-shadow: 0 0 0 8px rgba(34,197,94,0); } }
    .file-upload { border: 2px dashed #e2e8f0; border-radius: 1rem; padding: 1.5rem; text-align: center; transition: all 0.2s; cursor: pointer; position: relative; }
    .file-upload:hover { border-color: #FF4B2B; background: #fff5f2; }
    .file-upload input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
    .gps-box { background: #f0f9ff; border: 1.5px solid #bae6fd; border-radius: 1rem; padding: 1rem; }
</style>
@endsection

@section('content')
<div class="sig-page py-4">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">

                {{-- Back Link --}}
                <a href="{{ route('chauffeur.signalements.index') }}" class="text-decoration-none text-muted d-inline-flex align-items-center gap-2 mb-3 small fw-medium">
                    <i class="fas fa-arrow-left"></i> Retour à l'historique
                </a>

                {{-- Page Header --}}
                <div class="mb-4">
                    <h2 class="fw-bold mb-1" style="font-size: 1.6rem;">
                        <i class="fas fa-exclamation-circle text-danger me-2"></i>Signaler un Incident
                    </h2>
                    <p class="text-muted small mb-0">Informez la compagnie en temps réel de tout problème rencontré sur la route.</p>
                </div>

                <div class="form-card">
                    <div class="top-bar"></div>
                    <form action="{{ route('chauffeur.signalements.store') }}" method="POST" enctype="multipart/form-data" id="signalementForm" class="p-4 p-md-5">
                        @csrf

                        @if(session('error'))
                        <div class="alert alert-danger border-0 rounded-3 d-flex align-items-center gap-2 mb-4">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        </div>
                        @endif

                        {{-- STEP 1: Voyage --}}
                        <div class="mb-4">
                            <h6 class="fw-bold d-flex align-items-center gap-2 mb-3">
                                <span class="step-num">1</span> Voyage concerné
                            </h6>

                            @if(isset($activeVoyage) && $activeVoyage)
                                {{-- Active Voyage Auto-Selected --}}
                                <div class="active-voyage-card">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-3">
                                            <div>
                                                <span class="pulse-dot"></span>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">
                                                    {{ $activeVoyage->programme->point_depart ?? '' }} → {{ $activeVoyage->programme->point_arrive ?? '' }}
                                                </div>
                                                <div class="text-muted" style="font-size: 0.8rem;">
                                                    {{ \Carbon\Carbon::parse($activeVoyage->date_voyage)->format('d/m/Y') }} à {{ $activeVoyage->programme->heure_depart ?? '' }}
                                                    @if($activeVoyage->vehicule)
                                                     · <i class="fas fa-bus"></i> {{ $activeVoyage->vehicule->immatriculation }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <span class="badge bg-success text-white rounded-pill px-3 py-2 fw-bold" style="font-size: 0.7rem;">
                                            <i class="fas fa-road me-1"></i> EN COURS
                                        </span>
                                    </div>
                                </div>
                                <input type="hidden" name="voyage_id" value="{{ $activeVoyage->id }}">
                            @else
                                <select name="voyage_id" class="form-select @error('voyage_id') is-invalid @enderror" required>
                                    <option value="">-- Choisir un voyage --</option>
                                    @foreach($voyages as $v)
                                    <option value="{{ $v->id }}" {{ (old('voyage_id') == $v->id || $preselectedVoyageId == $v->id) ? 'selected' : '' }}>
                                        {{ $v->programme->point_depart ?? '' }} → {{ $v->programme->point_arrive ?? '' }}
                                        ({{ \Carbon\Carbon::parse($v->date_voyage)->format('d/m/Y') }} à {{ $v->programme->heure_depart ?? '' }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('voyage_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @endif
                        </div>

                        {{-- STEP 2: Type --}}
                        <div class="mb-4">
                            <h6 class="fw-bold d-flex align-items-center gap-2 mb-3">
                                <span class="step-num">2</span> Type d'incident
                            </h6>
                            <div class="type-selector">
                                <label class="type-option type-accident">
                                    <input type="radio" name="type" value="accident" required {{ old('type') == 'accident' ? 'checked' : '' }}>
                                    <div class="type-card">
                                        <i class="fas fa-car-crash type-icon"></i>
                                        <span class="small fw-bold d-block">Accident</span>
                                    </div>
                                </label>
                                <label class="type-option type-panne">
                                    <input type="radio" name="type" value="panne" {{ old('type') == 'panne' ? 'checked' : '' }}>
                                    <div class="type-card">
                                        <i class="fas fa-tools type-icon"></i>
                                        <span class="small fw-bold d-block">Panne</span>
                                    </div>
                                </label>
                                <label class="type-option type-retard">
                                    <input type="radio" name="type" value="retard" {{ old('type') == 'retard' ? 'checked' : '' }}>
                                    <div class="type-card">
                                        <i class="fas fa-clock type-icon"></i>
                                        <span class="small fw-bold d-block">Retard</span>
                                    </div>
                                </label>
                                <label class="type-option type-autre">
                                    <input type="radio" name="type" value="autre" {{ old('type') == 'autre' ? 'checked' : '' }}>
                                    <div class="type-card">
                                        <i class="fas fa-exclamation-triangle type-icon"></i>
                                        <span class="small fw-bold d-block">Autre</span>
                                    </div>
                                </label>
                            </div>
                            @error('type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        {{-- STEP 3: Description --}}
                        <div class="mb-4">
                            <h6 class="fw-bold d-flex align-items-center gap-2 mb-3">
                                <span class="step-num">3</span> Décrivez la situation
                            </h6>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4"
                                placeholder="Ex: Le véhicule a une crevaison sur l'autoroute du nord à 20km d'Abidjan..." required>{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- STEP 4: Photo & GPS --}}
                        <div class="mb-4">
                            <h6 class="fw-bold d-flex align-items-center gap-2 mb-3">
                                <span class="step-num">4</span> Preuves & Localisation
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Photo de l'incident</label>
                                    <div class="file-upload" id="fileUploadZone">
                                        <input type="file" name="photo" class="@error('photo') is-invalid @enderror" accept="image/*" id="photoInput">
                                        <div id="uploadPlaceholder">
                                            <i class="fas fa-camera fa-2x text-muted opacity-50 d-block mb-2"></i>
                                            <span class="small text-muted">Appuyez pour prendre une photo</span>
                                            <div class="text-muted" style="font-size: 0.65rem;">Optionnelle</div>
                                        </div>
                                        <div id="uploadPreview" class="d-none">
                                            <i class="fas fa-check-circle text-success fa-2x mb-1"></i>
                                            <div class="small fw-bold text-success" id="fileName">Photo sélectionnée</div>
                                        </div>
                                    </div>
                                    @error('photo') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Position GPS</label>
                                    <div class="gps-box">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width:36px; height:36px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                                <i class="fas fa-map-marker-alt text-danger"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold small" id="gps-status">Récupération...</div>
                                                <div class="text-muted" style="font-size: 0.7rem;" id="gps-coords">En attente du GPS</div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-dark rounded-pill px-3" onclick="getLocation()">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="latitude" id="lat-input">
                                    <input type="hidden" name="longitude" id="lng-input">
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Submit --}}
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane me-2"></i> Envoyer le Signalement
                        </button>
                        <p class="text-muted text-center mt-3" style="font-size: 0.75rem;">
                            <i class="fas fa-shield-alt me-1"></i> Ce signalement sera immédiatement visible par la compagnie et les secours si nécessaire.
                        </p>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function getLocation() {
        const status = document.getElementById('gps-status');
        const coords = document.getElementById('gps-coords');
        const latInput = document.getElementById('lat-input');
        const lngInput = document.getElementById('lng-input');

        status.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Localisation...';
        coords.textContent = 'Recherche en cours...';

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    latInput.value = lat;
                    lngInput.value = lng;
                    status.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i>Position trouvée';
                    coords.textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);
                },
                (error) => {
                    let msg = 'Erreur GPS';
                    if (error.code === error.PERMISSION_DENIED) msg = 'Permission refusée';
                    else if (error.code === error.POSITION_UNAVAILABLE) msg = 'Position indisponible';
                    else if (error.code === error.TIMEOUT) msg = 'Délai expiré';
                    status.innerHTML = '<i class="fas fa-times-circle text-danger me-1"></i>' + msg;
                    coords.textContent = 'Réessayez avec le bouton';
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        } else {
            status.textContent = 'GPS non supporté';
        }
    }

    // Photo preview
    document.getElementById('photoInput').addEventListener('change', function(e) {
        if(this.files && this.files[0]) {
            document.getElementById('uploadPlaceholder').classList.add('d-none');
            document.getElementById('uploadPreview').classList.remove('d-none');
            document.getElementById('fileName').textContent = this.files[0].name;
        }
    });

    // Get GPS on page load
    document.addEventListener('DOMContentLoaded', getLocation);
</script>
@endsection
