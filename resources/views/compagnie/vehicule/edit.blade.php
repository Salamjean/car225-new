@extends('compagnie.layouts.template')

@section('page-title', 'Modifier Véhicule')
@section('page-subtitle', 'Mise à jour des informations techniques et d\'affectation')

@section('styles')
<!-- On réutilise le même CSS parfait que pour le Create -->
<style>
    .form-wrapper { max-width: 800px; margin: 0 auto; }
    
    .btn-back { display: inline-flex; align-items: center; gap: 8px; color: var(--text-3); font-weight: 700; font-size: 13px; text-decoration: none; margin-bottom: 24px; transition: color 0.2s; }
    .btn-back:hover { color: var(--orange); text-decoration: none; }

    .dash-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; box-shadow: var(--shadow-sm); overflow: hidden; margin-bottom: 24px; }
    .card-header { padding: 16px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
    .card-icon { width: 32px; height: 32px; background: var(--text-1); color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 13px; }
    .card-title { font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-1); margin: 0; }
    .card-body { padding: 24px; }

    /* Inputs */
    .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-label { font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--text-3); letter-spacing: 0.5px; }
    .input-with-icon { position: relative; }
    .input-with-icon select { padding-right: 36px; }
    .input-with-icon .chevron { position: absolute; right: 16px; top: 50%; transform: translateY(-50%); font-size: 10px; color: var(--text-3); pointer-events: none; }
    .form-control { width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid var(--border); background: var(--surface-2); font-size: 13px; font-weight: 600; color: var(--text-1); outline: none; transition: all 0.2s; appearance: none; }
    .form-control:focus { background: var(--surface); border-color: var(--orange); box-shadow: 0 0 0 3px var(--orange-light); }
    .form-control option { background: var(--surface); color: var(--text-1); padding: 10px; }
    select.form-control { cursor: pointer; height: 48px; }
    .form-error { font-size: 10px; font-weight: 700; color: #DC2626; margin-top: 4px; text-transform: uppercase; }

    /* Action Footer */
    .form-actions { display: flex; justify-content: flex-end; gap: 12px; padding: 24px; border-top: 1px solid var(--border); background: var(--surface); border-radius: 0 0 16px 16px; }
    .btn-reset { padding: 14px 24px; border-radius: 12px; background: transparent; border: 1px solid transparent; font-size: 12px; font-weight: 800; text-transform: uppercase; color: var(--text-3); cursor: pointer; transition: 0.2s; text-decoration: none; display: inline-flex; align-items: center; }
    .btn-reset:hover { background: var(--surface-2); color: var(--text-2); text-decoration: none; }
    .btn-submit { padding: 14px 32px; border-radius: 12px; background: var(--orange); color: white; border: none; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 8px; }
    .btn-submit:hover { background: var(--orange-dark); box-shadow: 0 4px 12px rgba(249,115,22,0.25); }

    @media (max-width: 640px) {
        .form-grid-2 { grid-template-columns: 1fr; }
        .form-actions { flex-direction: column-reverse; }
        .btn-submit, .btn-reset { width: 100%; justify-content: center; text-align: center; }
    }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <div class="form-wrapper">
        <a href="{{ route('vehicule.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Retour à la flotte
        </a>

        <form action="{{ route('vehicule.update', $vehicule->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="dash-card">
                
                {{-- Section 0: Gare --}}
                @if(isset($gares) && $gares->count() > 0)
                <div class="card-header" style="background: #EFF6FF; border-bottom-color: #BFDBFE;">
                    <div class="card-icon" style="background: #2563EB;"><i class="fas fa-building"></i></div>
                    <h3 class="card-title" style="color: #1E3A8A;">Affectation à une Gare</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Sélection de la Gare *</label>
                        <div class="input-with-icon">
                            <select name="gare_id" required class="form-control">
                                <option value="" disabled>-- Sélectionnez une gare --</option>
                                @foreach($gares as $gare)
                                    <option value="{{ $gare->id }}" {{ old('gare_id', $vehicule->gare_id) == $gare->id ? 'selected' : '' }}>
                                        {{ $gare->nom_gare }} — {{ $gare->ville }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down chevron"></i>
                        </div>
                        @error('gare_id') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div style="height: 1px; background: var(--border);"></div>
                @endif

                {{-- Section 1: Infos Générales --}}
                <div class="card-header">
                    <div class="card-icon"><i class="fas fa-pen-nib"></i></div>
                    <h3 class="card-title">Informations Générales</h3>
                </div>
                <div class="card-body">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">Immatriculation *</label>
                            <input type="text" name="immatriculation" value="{{ old('immatriculation', $vehicule->immatriculation) }}" required class="form-control" placeholder="ex: 1234 AB 01" style="font-family: monospace; letter-spacing: 1px;">
                            @error('immatriculation') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Numéro de série</label>
                            <input type="text" name="numero_serie" value="{{ old('numero_serie', $vehicule->numero_serie) }}" class="form-control" placeholder="Numéro de châssis">
                            @error('numero_serie') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div style="height: 1px; background: var(--border);"></div>

                {{-- Section 2: Configuration --}}
                <div class="card-header" style="background: #ECFDF5; border-bottom-color: #A7F3D0;">
                    <div class="card-icon" style="background: #059669;"><i class="fas fa-chair"></i></div>
                    <h3 class="card-title" style="color: #065F46;">Configuration des places</h3>
                </div>
                <div class="card-body">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">Type de rangée *</label>
                            <div class="input-with-icon">
                                <select name="type_range" id="type_range" required class="form-control">
                                    <option value="2x2" {{ old('type_range', $vehicule->type_range) == '2x2' ? 'selected' : '' }}>2x2 (2 places par côté)</option>
                                    <option value="2x3" {{ old('type_range', $vehicule->type_range) == '2x3' ? 'selected' : '' }}>2x3 (2 à gauche, 3 à droite)</option>
                                    <option value="2x4" {{ old('type_range', $vehicule->type_range) == '2x4' ? 'selected' : '' }}>2x4 (2 à gauche, 4 à droite)</option>
                                </select>
                                <i class="fas fa-chevron-down chevron"></i>
                            </div>
                            @error('type_range') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nombre total de places *</label>
                            <input type="number" name="nombre_place" id="nombre_place" value="{{ old('nombre_place', $vehicule->nombre_place) }}" required class="form-control" placeholder="ex: 16" min="4" max="100">
                            @error('nombre_place') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="form-actions">
                    <a href="{{ route('vehicule.index') }}" class="btn-reset"><i class="fas fa-times" style="margin-right:8px;"></i> Annuler</a>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Mise à jour Réussie',
            text: "{{ session('success') }}",
            confirmButtonColor: '#F97316',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            customClass: { popup: 'rounded-[20px] border-0 shadow-lg' }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: "{{ session('error') }}",
            confirmButtonColor: '#EF4444',
            customClass: { popup: 'rounded-[20px] border-0 shadow-lg' }
        });
    @endif
</script>
@endsection