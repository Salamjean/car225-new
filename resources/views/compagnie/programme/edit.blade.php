@extends('compagnie.layouts.template')

@section('page-title', 'Modifier le programme')
@section('page-subtitle', 'Mise à jour des paramètres et horaires')

@section('styles')
<style>
    .form-card {
        background: var(--surface);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .form-header {
        padding: 18px 24px;
        background: var(--surface-2);
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .form-body { padding: 24px; }
    
    .input-modern {
        width: 100%; padding: 10px 14px;
        border: 1px solid var(--border-strong);
        border-radius: var(--radius-sm);
        font-size: 14px; font-weight: 700;
        background: var(--surface-2); color: var(--text-1);
        transition: all 0.2s;
    }
    .input-modern:focus {
        outline: none; border-color: var(--orange);
        background: var(--surface); box-shadow: 0 0 0 3px var(--orange-light);
    }
    .form-label {
        display: block; font-size: 11px; font-weight: 700;
        color: var(--text-2); text-transform: uppercase; margin-bottom: 6px;
    }

    /* Radio Cards pour le Statut */
    .radio-card-wrapper { display: flex; gap: 16px; }
    .radio-card-label { flex: 1; cursor: pointer; position: relative; }
    .radio-card-input { position: absolute; opacity: 0; width: 0; height: 0; }
    .radio-card-content {
        padding: 16px; border: 2px solid var(--border-strong);
        border-radius: var(--radius-sm); text-align: center;
        transition: all 0.2s; background: var(--surface);
    }
    .radio-card-content i { font-size: 24px; margin-bottom: 8px; }
    .radio-card-content .title { font-size: 14px; font-weight: 800; color: var(--text-1); }
    .radio-card-content .desc { font-size: 11px; color: var(--text-3); }

    .radio-card-input:checked + .radio-card-content.actif {
        border-color: var(--emerald); background: #ECFDF5;
    }
    .radio-card-input:checked + .radio-card-content.actif i { color: var(--emerald); }

    .radio-card-input:checked + .radio-card-content.annule {
        border-color: var(--red); background: #FEF2F2;
    }
    .radio-card-input:checked + .radio-card-content.annule i { color: var(--red); }

    .btn-submit {
        background: linear-gradient(135deg, var(--orange) 0%, var(--orange-dark) 100%);
        color: white; padding: 12px 24px; border-radius: var(--radius-sm);
        font-weight: 700; border: none; cursor: pointer;
        box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3); transition: transform 0.2s;
    }
    .btn-submit:hover { transform: translateY(-2px); color: white; text-decoration: none; }
</style>
@endsection

@section('content')
<div class="dashboard-page" style="max-width: 900px; margin: 0 auto;">
    
    <a href="{{ route('programme.index') }}" class="btn btn-link px-0 mb-3" style="color: var(--text-3); font-size: 13px; font-weight: 600; text-decoration: none;">
        <i class="fas fa-arrow-left mr-1"></i> Retour aux lignes
    </a>

    {{-- Infos route (lecture seule) --}}
    <div class="form-card p-4 d-flex align-items-center mb-4" style="gap: 16px;">
        <div style="width: 50px; height: 50px; border-radius: 12px; background: var(--orange-light); color: var(--orange); display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fas fa-route"></i>
        </div>
        <div>
            <h2 style="font-size: 18px; font-weight: 800; color: var(--text-1); margin: 0;">
                {{ $programme->point_depart }} <i class="fas fa-arrow-right mx-2 text-muted" style="font-size: 14px;"></i> {{ $programme->point_arrive }}
            </h2>
            <div style="font-size: 12px; color: var(--text-3); font-weight: 600; margin-top: 4px;">
                @if($programme->gareDepart)
                    <i class="fas fa-map-marker-alt" style="color: var(--emerald);"></i> {{ $programme->gareDepart->nom_gare }}
                    <span class="mx-1">&bull;</span>
                    <i class="fas fa-map-marker-alt" style="color: var(--blue);"></i> {{ $programme->gareArrivee->nom_gare ?? 'N/A' }}
                @endif
                @if($programme->durer_parcours)
                    <span class="mx-2">|</span>
                    <i class="fas fa-clock"></i> {{ $programme->durer_parcours }}
                @endif
            </div>
        </div>
    </div>

    {{-- Formulaire --}}
    <div class="form-card">
        <div class="form-header">
            <h3 style="font-size: 15px; font-weight: 800; color: var(--text-1); margin: 0;">
                <i class="fas fa-edit mr-2" style="color: var(--orange);"></i> Configuration de l'horaire
            </h3>
        </div>

        <form action="{{ route('programme.update', $programme->id) }}" method="POST" class="form-body">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="alert alert-danger" style="border-radius: var(--radius-sm); font-size: 13px;">
                    <ul class="mb-0 pl-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label for="heure_depart" class="form-label"><i class="fas fa-clock text-success mr-1"></i> Heure de départ</label>
                    <input type="time" name="heure_depart" id="heure_depart" 
                        value="{{ old('heure_depart', \Carbon\Carbon::parse($programme->heure_depart)->format('H:i')) }}" required
                        class="input-modern">
                </div>

                <div class="col-md-6 mb-4">
                    <label for="heure_arrive" class="form-label"><i class="fas fa-flag-checkered text-danger mr-1"></i> Heure d'arrivée estimée</label>
                    <input type="time" name="heure_arrive" id="heure_arrive" 
                        value="{{ old('heure_arrive', \Carbon\Carbon::parse($programme->heure_arrive)->format('H:i')) }}" required
                        class="input-modern" style="background: #F1F5F9; border-style: dashed;" readonly>
                </div>

                <div class="col-md-6 mb-4">
                    <label for="montant_billet" class="form-label"><i class="fas fa-money-bill-wave text-warning mr-1"></i> Montant du billet (FCFA)</label>
                    <input type="number" name="montant_billet" id="montant_billet" 
                        value="{{ old('montant_billet', intval($programme->montant_billet)) }}" required min="0" step="100"
                        class="input-modern">
                </div>

                <div class="col-md-6 mb-4">
                    <label for="capacity" class="form-label"><i class="fas fa-chair text-primary mr-1"></i> Nombre de places max</label>
                    <input type="number" name="capacity" id="capacity" 
                        value="{{ old('capacity', $programme->capacity ?? 50) }}" required min="1"
                        class="input-modern">
                </div>

                <div class="col-12 mb-4">
                    <label class="form-label"><i class="fas fa-toggle-on text-purple mr-1"></i> Statut du programme</label>
                    <div class="radio-card-wrapper">
                        <label class="radio-card-label">
                            <input type="radio" name="statut" value="actif" class="radio-card-input" {{ old('statut', $programme->statut) == 'actif' ? 'checked' : '' }}>
                            <div class="radio-card-content actif">
                                <i class="fas fa-check-circle text-muted"></i>
                                <div class="title">Actif</div>
                                <div class="desc">Visible et réservable</div>
                            </div>
                        </label>
                        <label class="radio-card-label">
                            <input type="radio" name="statut" value="annule" class="radio-card-input" {{ old('statut', $programme->statut) == 'annule' ? 'checked' : '' }}>
                            <div class="radio-card-content annule">
                                <i class="fas fa-times-circle text-muted"></i>
                                <div class="title">Annulé</div>
                                <div class="desc">Désactivé temporairement</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end pt-3 mt-2" style="border-top: 1px solid var(--border); gap: 12px;">
                <a href="{{ route('programme.index') }}" class="btn btn-light" style="font-weight: 700; font-size: 13px; border-radius: var(--radius-sm);">Annuler</a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save mr-2"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function parseDuration(str) {
        if (!str) return 90;
        let h = 0, m = 0;
        const hM = str.match(/(\d+)\s*h/i);
        if (hM) h = parseInt(hM[1]);
        const mM = str.match(/(\d+)\s*m/i);
        if (mM) m = parseInt(mM[1]);
        return (h * 60) + m || 90;
    }

    const dureeMinutes = parseDuration(@json($programme->durer_parcours));
    const departInput = document.getElementById('heure_depart');
    const arriveeInput = document.getElementById('heure_arrive');

    departInput.addEventListener('change', function() {
        if (this.value) {
            const [h, m] = this.value.split(':').map(Number);
            const total = h * 60 + m + dureeMinutes;
            const arrH = Math.floor(total / 60) % 24;
            const arrM = total % 60;
            arriveeInput.value = `${String(arrH).padStart(2, '0')}:${String(arrM).padStart(2, '0')}`;
        }
    });
</script>
@endsection