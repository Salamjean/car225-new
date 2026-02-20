@extends('chauffeur.layouts.template')

@section('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 1.5rem;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
    }

    .form-control, .form-select {
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .form-control:focus, .form-select:focus {
        background: white;
        box-shadow: 0 0 0 4px rgba(255, 75, 43, 0.1);
        border-color: #FF4B2B;
    }

    .btn-premium {
        background: linear-gradient(135deg, #FF4B2B, #FF416C);
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(255, 75, 43, 0.3);
        color: white;
    }

    .type-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .type-option {
        cursor: pointer;
    }

    .type-option input {
        display: none;
    }

    .type-card {
        padding: 1rem;
        text-align: center;
        border: 2px solid #f1f5f9;
        border-radius: 1rem;
        transition: all 0.2s ease;
        background: white;
    }

    .type-option input:checked + .type-card {
        border-color: #FF4B2B;
        background: #fff5f2;
    }

    .type-icon {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .type-accident .type-icon { color: #dc2626; }
    .type-panne .type-icon { color: #ea580c; }
    .type-retard .type-icon { color: #ca8a04; }
    .type-autre .type-icon { color: #4b5563; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4 text-dark">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-4">
                <a href="{{ route('chauffeur.signalements.index') }}" class="text-decoration-none text-muted">
                    <i class="fas fa-arrow-left me-2"></i> Retour à la liste
                </a>
                <h2 class="fw-bold mt-3 mb-1">Signaler un problème</h2>
                <p class="text-muted">Informez la compagnie en temps réel de tout incident</p>
            </div>

            <div class="glass-card p-4 p-md-5">
                <form action="{{ route('chauffeur.signalements.store') }}" method="POST" enctype="multipart/form-data" id="signalementForm">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-bold">Voyage concerné</label>
                        @if(isset($preselectedVoyageId) && $preselectedVoyageId)
                            @php $v = $voyages->firstWhere('id', $preselectedVoyageId); @endphp
                            @if($v)
                                <div class="p-3 bg-light border rounded-3 d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fw-bold">{{ $v->programme->point_depart }} → {{ $v->programme->point_arrive }}</div>
                                        <div class="text-muted small">
                                            {{ Carbon\Carbon::parse($v->date_voyage)->format('d/m/Y') }} à {{ $v->programme->heure_depart }}
                                        </div>
                                    </div>
                                    <span class="badge bg-purple-100 text-purple-700 p-2 px-3 rounded-pill">
                                        <i class="fas fa-spinner fa-spin me-2"></i> En cours
                                    </span>
                                </div>
                                <input type="hidden" name="voyage_id" value="{{ $preselectedVoyageId }}">
                            @endif
                        @else
                            <select name="voyage_id" class="form-select @error('voyage_id') is-invalid @enderror" required>
                                <option value="">-- Choisir un voyage --</option>
                                @foreach($voyages as $v)
                                <option value="{{ $v->id }}" {{ old('voyage_id') == $v->id ? 'selected' : '' }}>
                                    {{ $v->programme->point_depart }} → {{ $v->programme->point_arrive }} 
                                    ({{ Carbon\Carbon::parse($v->date_voyage)->format('d/m/Y') }} à {{ $v->programme->heure_depart }})
                                </option>
                                @endforeach
                            </select>
                            @error('voyage_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @endif
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Type d'incident</label>
                        <div class="type-selector">
                            <label class="type-option type-accident">
                                <input type="radio" name="type" value="accident" required {{ old('type') == 'accident' ? 'checked' : '' }}>
                                <div class="type-card">
                                    <i class="fas fa-car-crash type-icon"></i>
                                    <span class="small fw-bold">Accident</span>
                                </div>
                            </label>
                            <label class="type-option type-panne">
                                <input type="radio" name="type" value="panne" {{ old('type') == 'panne' ? 'checked' : '' }}>
                                <div class="type-card">
                                    <i class="fas fa-tools type-icon"></i>
                                    <span class="small fw-bold">Panne</span>
                                </div>
                            </label>
                            <label class="type-option type-retard">
                                <input type="radio" name="type" value="retard" {{ old('type') == 'retard' ? 'checked' : '' }}>
                                <div class="type-card">
                                    <i class="fas fa-clock type-icon"></i>
                                    <span class="small fw-bold">Retard</span>
                                </div>
                            </label>
                            <label class="type-option type-autre">
                                <input type="radio" name="type" value="autre" {{ old('type') == 'autre' ? 'checked' : '' }}>
                                <div class="type-card">
                                    <i class="fas fa-exclamation-triangle type-icon"></i>
                                    <span class="small fw-bold">Autre</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Description détaillée</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Décrivez précisément la situation..." required>{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label fw-bold">Photo (Optionnel)</label>
                            <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
                            <p class="text-muted small mt-1">Obligatoire en cas d'accident.</p>
                            @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Localisation GPS</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-map-marker-alt text-danger"></i></span>
                                <input type="text" id="location-display" class="form-control border-start-0" placeholder="Position non détectée" readonly>
                                <button type="button" class="btn btn-dark" onclick="getLocation()">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                            <input type="hidden" name="latitude" id="lat-input">
                            <input type="hidden" name="longitude" id="lng-input">
                            <p class="text-xs text-muted mt-1">Fortement recommandé pour une intervention rapide.</p>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-premium btn-lg w-100 mb-3">
                            <i class="fas fa-paper-plane me-2"></i> Envoyer le signalement
                        </button>
                        <p class="text-muted small">Ce signalement sera immédiatement visible par la compagnie et les secours si nécessaire.</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function getLocation() {
        const display = document.getElementById('location-display');
        const latInput = document.getElementById('lat-input');
        const lngInput = document.getElementById('lng-input');
        
        display.value = "Localisation en cours...";
        
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    latInput.value = lat;
                    lngInput.value = lng;
                    display.value = lat.toFixed(6) + ", " + lng.toFixed(6);
                    console.log("Location found:", lat, lng);
                },
                (error) => {
                    let msg = "Erreur de géolocalisation";
                    switch(error.code) {
                        case error.PERMISSION_DENIED: msg = "Permission refusée"; break;
                        case error.POSITION_UNAVAILABLE: msg = "Position indisponible"; break;
                        case error.TIMEOUT: msg = "Délai expiré"; break;
                    }
                    display.value = msg;
                    console.error("Geolocation error:", error);
                }
            );
        } else {
            display.value = "Navigateur non compatible";
        }
    }

    // Get location on page load
    document.addEventListener('DOMContentLoaded', getLocation);
</script>
@endsection
