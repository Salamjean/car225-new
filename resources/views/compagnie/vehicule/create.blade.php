@extends('compagnie.layouts.template')

@section('page-title', 'Nouveau Véhicule')
@section('page-subtitle', 'Ajoutez un nouveau véhicule à votre flotte de transport')

@section('styles')
<style>
    .form-wrapper { max-width: 800px; margin: 0 auto; }
    
    .btn-back { display: inline-flex; align-items: center; gap: 8px; color: var(--text-3); font-weight: 700; font-size: 13px; text-decoration: none; margin-bottom: 24px; transition: color 0.2s; }
    .btn-back:hover { color: var(--orange); text-decoration: none; }

    .dash-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; box-shadow: var(--shadow-sm); overflow: hidden; margin-bottom: 24px; }
    .card-header { padding: 16px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
    .card-icon { width: 32px; height: 32px; background: var(--orange); color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 13px; }
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

    /* Visualisation */
    .visual-wrapper { margin-top: 24px; padding: 24px; background: var(--surface-2); border-radius: 16px; border: 1px solid var(--border); display: none; flex-direction: column; align-items: center; }
    .visual-header { width: 100%; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .visual-title { font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--text-3); margin: 0; }
    .visual-badges { display: flex; gap: 8px; }
    .visual-badge { padding: 4px 10px; background: var(--surface); border: 1px solid var(--border); border-radius: 8px; font-size: 9px; font-weight: 800; text-transform: uppercase; }
    .badge-orange { color: var(--orange-dark); border-color: var(--orange-mid); }
    .badge-green { color: #059669; border-color: #A7F3D0; }

    .viz-row { display: flex; align-items: center; justify-content: space-between; width: 100%; max-width: 320px; padding: 12px 16px; background: var(--surface); border: 1px solid var(--border); border-radius: 12px; margin-bottom: 6px; box-shadow: var(--shadow-sm); }
    .viz-label { font-size: 10px; font-weight: 900; color: var(--text-3); width: 24px; }
    .viz-group { display: flex; gap: 6px; flex: 1; justify-content: center; }
    .viz-aisle { width: 16px; height: 36px; background: var(--surface-2); border-radius: 20px; margin: 0 16px; border: 1px solid var(--border); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); }
    .viz-seat { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; border-radius: 8px; color: white; transition: 0.2s; }
    .viz-seat:hover { transform: scale(1.1); }
    .seat-orange { background: var(--orange); box-shadow: 0 4px 8px rgba(249,115,22,0.3); }
    .seat-green { background: #10B981; box-shadow: 0 4px 8px rgba(16,185,129,0.3); }

    /* Action Footer */
    .form-actions { display: flex; justify-content: flex-end; gap: 12px; padding: 24px; border-top: 1px solid var(--border); background: var(--surface); border-radius: 0 0 16px 16px; }
    .btn-reset { padding: 14px 24px; border-radius: 12px; background: transparent; border: 1px solid transparent; font-size: 12px; font-weight: 800; text-transform: uppercase; color: var(--text-3); cursor: pointer; transition: 0.2s; }
    .btn-reset:hover { background: var(--surface-2); color: var(--text-2); }
    .btn-submit { padding: 14px 32px; border-radius: 12px; background: var(--orange); color: white; border: none; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 8px; }
    .btn-submit:hover { background: var(--orange-dark); box-shadow: 0 4px 12px rgba(249,115,22,0.25); }

    @media (max-width: 640px) {
        .form-grid-2 { grid-template-columns: 1fr; }
        .form-actions { flex-direction: column-reverse; }
        .btn-submit, .btn-reset { width: 100%; justify-content: center; text-align: center; }
        .viz-row { padding: 8px; }
        .viz-seat { width: 30px; height: 30px; font-size: 10px; }
        .viz-aisle { width: 10px; margin: 0 8px; }
    }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <div class="form-wrapper">
        <a href="{{ route('vehicule.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Retour à la flotte
        </a>

        <form action="{{ route('vehicule.store') }}" method="POST">
            @csrf

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
                                <option value="" disabled {{ old('gare_id') ? '' : 'selected' }}>-- Sélectionnez une gare --</option>
                                @foreach($gares as $gare)
                                    <option value="{{ $gare->id }}" {{ old('gare_id') == $gare->id ? 'selected' : '' }}>
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
                    <div class="card-icon"><i class="fas fa-bus"></i></div>
                    <h3 class="card-title">Informations Générales</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Immatriculation *</label>
                        <input type="text" name="immatriculation" value="{{ old('immatriculation') }}" required class="form-control" placeholder="ex: 1234 AB 01" style="font-family: monospace; letter-spacing: 1px;">
                        @error('immatriculation') <span class="form-error">{{ $message }}</span> @enderror
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
                                    <option value="" disabled selected>Sélectionnez le type</option>
                                    <option value="2x2" {{ old('type_range') == '2x2' ? 'selected' : '' }}>2x2 (2 places de chaque côté)</option>
                                    <option value="2x3" {{ old('type_range') == '2x3' ? 'selected' : '' }}>2x3 (2 à gauche, 3 à droite)</option>
                                    <option value="2x4" {{ old('type_range') == '2x4' ? 'selected' : '' }}>2x4 (2 à gauche, 4 à droite)</option>
                                </select>
                                <i class="fas fa-chevron-down chevron"></i>
                            </div>
                            @error('type_range') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nombre total de places *</label>
                            <input type="number" name="nombre_place" id="nombre_place" value="{{ old('nombre_place') }}" required class="form-control" placeholder="ex: 16" min="4" max="100">
                            @error('nombre_place') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Visualisation --}}
                    <div id="configuration_visuelle" class="visual-wrapper">
                        <div class="visual-header">
                            <h3 class="visual-title">Visualisation en temps réel</h3>
                            <div class="visual-badges">
                                <span id="config_type_badge" class="visual-badge badge-orange">-</span>
                                <span id="config_ranger_badge" class="visual-badge badge-green">-</span>
                            </div>
                        </div>
                        <div id="rangees_container" style="display: flex; flex-direction: column; align-items: center; width: 100%;">
                            {{-- Dynamically generated --}}
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="form-actions">
                    <button type="reset" class="btn-reset"><i class="fas fa-undo" style="margin-right:8px;"></i> Réinitialiser</button>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-plus"></i> Créer le véhicule
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeRangeSelect = document.getElementById('type_range');
    const configurationVisuelle = document.getElementById('configuration_visuelle');
    const rangeesContainer = document.getElementById('rangees_container');
    const configTypeBadge = document.getElementById('config_type_badge');
    const configRangerBadge = document.getElementById('config_ranger_badge');
    const nombrePlaceInput = document.getElementById('nombre_place');

    const typeRangeConfig = {
        '2x2': { g: 2, d: 2 },
        '2x3': { g: 2, d: 3 },
        '2x4': { g: 2, d: 4 }
    };

    typeRangeSelect.addEventListener('change', updateVisualisation);
    nombrePlaceInput.addEventListener('input', updateVisualisation);

    // Load visualisation if old data exists
    if(typeRangeSelect.value && nombrePlaceInput.value) {
        updateVisualisation();
    }

    function updateVisualisation() {
        const type = typeRangeSelect.value;
        const total = parseInt(nombrePlaceInput.value) || 0;

        if (!type || !total || !typeRangeConfig[type]) {
            configurationVisuelle.style.display = 'none';
            return;
        }

        const config = typeRangeConfig[type];
        const perRow = config.g + config.d;
        const rows = Math.ceil(total / perRow);

        configTypeBadge.textContent = type + ' (' + perRow + ' PL/RANG)';
        configRangerBadge.textContent = rows + ' RANGÉES';
        configurationVisuelle.style.display = 'flex';

        rangeesContainer.innerHTML = '';
        let num = 1;

        for (let r = 1; r <= rows; r++) {
            const leftCount = Math.min(config.g, total - (num - 1));
            const rightCount = Math.min(config.d, total - (num - 1 + leftCount));

            const rowDiv = document.createElement('div');
            rowDiv.className = 'viz-row';
            
            let rowHtml = `
                <div class="viz-label">R${r}</div>
                <div class="viz-group">
                    ${Array.from({length: leftCount}, (_, i) => `<div class="viz-seat seat-orange">${num + i}</div>`).join('')}
                </div>
                <div class="viz-aisle"></div>
                <div class="viz-group">
                    ${Array.from({length: rightCount}, (_, i) => `<div class="viz-seat seat-green">${num + leftCount + i}</div>`).join('')}
                </div>
            `;
            rowDiv.innerHTML = rowHtml;
            rangeesContainer.appendChild(rowDiv);
            num += (leftCount + rightCount);
        }
    }
});

@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Succès!',
        text: "{{ session('success') }}",
        confirmButtonColor: '#F97316',
        timer: 3000,
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