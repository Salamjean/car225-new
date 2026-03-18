@extends('compagnie.layouts.template')

@section('page-title', 'Mise à jour Profil')
@section('page-subtitle', 'Modifier les informations du collaborateur')

@section('styles')
<style>
    .form-wrapper { max-width: 1000px; margin: 0 auto; }
    
    .btn-back { display: inline-flex; align-items: center; gap: 8px; color: var(--text-3); font-weight: 700; font-size: 13px; text-decoration: none; margin-bottom: 24px; transition: color 0.2s; }
    .btn-back:hover { color: var(--orange); text-decoration: none; }

    .form-grid-layout { display: grid; grid-template-columns: 280px 1fr; gap: 24px; }

    .dash-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; box-shadow: var(--shadow-sm); overflow: hidden; }
    .card-header { padding: 16px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
    .card-step { width: 28px; height: 28px; background: var(--text-1); color: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; }
    .card-title { font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-1); margin: 0; }
    .card-body { padding: 24px; }

    /* Photo Upload */
    .photo-upload-container { display: flex; flex-direction: column; align-items: center; text-align: center; }
    .photo-label { font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--text-3); letter-spacing: 1px; margin-bottom: 16px; }
    .avatar-wrapper { position: relative; width: 160px; height: 160px; border-radius: 30px; background: var(--surface-2); border: 4px solid var(--surface); box-shadow: var(--shadow-md); display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .avatar-placeholder { font-size: 60px; color: var(--border-strong); }
    .avatar-preview { width: 100%; height: 100%; object-fit: cover; }
    .btn-upload { position: absolute; bottom: -10px; right: -10px; width: 44px; height: 44px; border-radius: 14px; background: var(--text-1); color: white; display: flex; align-items: center; justify-content: center; font-size: 16px; cursor: pointer; transition: all 0.2s; border: 4px solid var(--surface); z-index: 10; }
    .btn-upload:hover { background: var(--orange); transform: scale(1.05); }

    .photo-hints { margin-top: 32px; padding: 16px; background: var(--surface-2); border-radius: 12px; border: 1px solid var(--border); text-align: left; width: 100%; }
    .photo-hints h4 { font-size: 10px; font-weight: 800; color: var(--text-2); text-transform: uppercase; margin-bottom: 8px; }
    .photo-hints p { font-size: 11px; color: var(--text-3); font-weight: 600; font-style: italic; margin: 0; line-height: 1.5; }

    /* Inputs */
    .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-label { font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--text-3); letter-spacing: 0.5px; }
    .input-with-icon { position: relative; }
    .input-with-icon i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-3); }
    .input-with-icon select { padding-right: 36px; }
    .input-with-icon .chevron { position: absolute; right: 16px; left: auto; font-size: 10px; pointer-events: none; }
    .form-control { width: 100%; padding: 14px 16px 14px 44px; border-radius: 12px; border: 1px solid var(--border); background: var(--surface-2); font-size: 13px; font-weight: 600; color: var(--text-1); outline: none; transition: all 0.2s; appearance: none; }
    .form-control:focus { background: var(--surface); border-color: var(--orange); box-shadow: 0 0 0 3px var(--orange-light); }
    .form-error { font-size: 10px; font-weight: 700; color: #DC2626; margin-top: 4px; }

    /* Action Footer */
    .form-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; }
    .btn-reset { padding: 14px 24px; border-radius: 12px; background: transparent; border: 1px solid transparent; font-size: 12px; font-weight: 800; text-transform: uppercase; color: var(--text-3); cursor: pointer; transition: 0.2s; text-decoration: none; display: inline-flex; align-items: center; }
    .btn-reset:hover { background: var(--surface-2); color: var(--text-2); text-decoration: none; }
    .btn-submit { padding: 14px 32px; border-radius: 12px; background: var(--orange); color: white; border: none; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 8px; }
    .btn-submit:hover { background: var(--orange-dark); box-shadow: 0 4px 12px rgba(249,115,22,0.25); }

    @media (max-width: 992px) {
        .form-grid-layout { grid-template-columns: 1fr; }
        .photo-upload-container { padding-bottom: 24px; border-bottom: 1px solid var(--border); margin-bottom: 24px; }
    }
    @media (max-width: 640px) {
        .form-grid-2 { grid-template-columns: 1fr; }
        .form-actions { flex-direction: column-reverse; }
        .btn-submit, .btn-reset { width: 100%; text-align: center; justify-content: center; }
    }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <div class="form-wrapper">
        <a href="{{ route('compagnie.agents.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Retour au Répertoire
        </a>

        <form method="POST" action="{{ route('compagnie.agents.update', $agent->id) }}" enctype="multipart/form-data" id="editAgentForm">
            @csrf
            @method('PUT')

            <div class="form-grid-layout">
                
                {{-- ── LEFT: PHOTO ── --}}
                <div class="dash-card">
                    <div class="card-body photo-upload-container">
                        <label class="photo-label">Photo d'Identité</label>
                        
                        <div class="avatar-wrapper">
                            @if($agent->profile_picture)
                                <img id="imagePreview" src="{{ Storage::url($agent->profile_picture) }}" alt="Photo agent" class="avatar-preview" style="display: block;">
                                <div class="avatar-placeholder" id="avatarPlaceholder" style="display: none;">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                            @else
                                <div class="avatar-placeholder" id="avatarPlaceholder">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <img id="imagePreview" src="#" alt="Aperçu" class="avatar-preview">
                            @endif
                            
                            <label for="profile_picture" class="btn-upload">
                                <i class="fas fa-camera"></i>
                                <input type="file" id="profile_picture" name="profile_picture" class="d-none" accept="image/*">
                            </label>
                        </div>

                        <div class="photo-hints">
                            <h4><i class="fas fa-shield-alt"></i> Traçabilité</h4>
                            <p>Toute modification de profil est enregistrée dans les logs système pour garantir la sécurité.</p>
                        </div>
                    </div>
                </div>

                {{-- ── RIGHT: INFORMATIONS ── --}}
                <div style="display: flex; flex-direction: column; gap: 24px;">
                    
                    {{-- Section 1 --}}
                    <div class="dash-card">
                        <div class="card-header">
                            <div class="card-step">1</div>
                            <h3 class="card-title">Coordonnées de l'Agent</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label class="form-label">Nom de Famille <span class="text-danger">*</span></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-user"></i>
                                        <input type="text" name="name" value="{{ old('name', $agent->name) }}" required class="form-control">
                                    </div>
                                    @error('name') <span class="form-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Prénom(s) <span class="text-danger">*</span></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-id-card"></i>
                                        <input type="text" name="prenom" value="{{ old('prenom', $agent->prenom) }}" required class="form-control">
                                    </div>
                                    @error('prenom') <span class="form-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Email Professionnel <span class="text-danger">*</span></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-envelope"></i>
                                        <input type="email" name="email" value="{{ old('email', $agent->email) }}" required class="form-control">
                                    </div>
                                    @error('email') <span class="form-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Contact Téléphonique <span class="text-danger">*</span></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-phone-alt"></i>
                                        <input type="text" name="contact" value="{{ old('contact', $agent->contact) }}" required maxlength="10" class="form-control">
                                    </div>
                                    @error('contact') <span class="form-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Gare d'Affectation <span class="text-danger">*</span></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-building"></i>
                                        <select name="gare_id" required class="form-control">
                                            <option value="">Sélectionner une gare...</option>
                                            @foreach($gares as $gare)
                                                <option value="{{ $gare->id }}" {{ old('gare_id', $agent->gare_id) == $gare->id ? 'selected' : '' }}>{{ $gare->nom_gare }}</option>
                                            @endforeach
                                        </select>
                                        <i class="fas fa-chevron-down chevron"></i>
                                    </div>
                                    @error('gare_id') <span class="form-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Commune (Résidence) <span class="text-danger">*</span></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <input type="text" name="commune" value="{{ old('commune', $agent->commune) }}" required class="form-control">
                                    </div>
                                    @error('commune') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2 --}}
                    <div class="dash-card">
                        <div class="card-header" style="background: var(--surface-2);">
                            <div class="card-step" style="background: var(--text-1);">2</div>
                            <h3 class="card-title">Sécurité & Urgence</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label class="form-label">Contact d'Urgence (Nom complet) <span class="text-danger">*</span></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-user-shield"></i>
                                        <input type="text" name="nom_urgence" value="{{ old('nom_urgence', $agent->nom_urgence) }}" required class="form-control">
                                    </div>
                                    @error('nom_urgence') <span class="form-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Lien de Parenté <span class="text-danger">*</span></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-users"></i>
                                        <select name="lien_parente_urgence" required class="form-control">
                                            <option value="">Relation avec l'agent...</option>
                                            @foreach(['Conjoint(e)', 'Père', 'Mère', 'Frère', 'Sœur', 'Oncle', 'Tante', 'Ami(e)', 'Autre'] as $lien)
                                                <option value="{{ $lien }}" {{ old('lien_parente_urgence', $agent->lien_parente_urgence) == $lien ? 'selected' : '' }}>{{ $lien }}</option>
                                            @endforeach
                                        </select>
                                        <i class="fas fa-chevron-down chevron"></i>
                                    </div>
                                    @error('lien_parente_urgence') <span class="form-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Contact d'Urgence (Numéro) <span class="text-danger">*</span></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-phone-alt"></i>
                                        <input type="text" name="cas_urgence" value="{{ old('cas_urgence', $agent->cas_urgence) }}" required maxlength="10" class="form-control" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                                    </div>
                                    @error('cas_urgence') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="form-actions">
                        <a href="{{ route('compagnie.agents.index') }}" class="btn-reset">Annuler les changements</a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-sync-alt"></i> Appliquer les Modifications
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const profileInput = document.getElementById('profile_picture');
    const imagePreview = document.getElementById('imagePreview');
    const placeholder = document.getElementById('avatarPlaceholder');

    profileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error', title: 'Fichier trop lourd', text: 'La taille maximale autorisée est de 2 Mo.',
                    confirmButtonColor: '#F97316', customClass: { popup: 'rounded-lg border-0 shadow-sm' }
                });
                this.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
                placeholder.style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    });

    @if(session('success'))
        Swal.fire({
            icon: 'success', title: 'Opération Réussie', text: '{{ session('success') }}',
            timer: 3000, showConfirmButton: false, toast: true, position: 'top-end',
            customClass: { popup: 'rounded-lg shadow-sm' }
        });
    @endif
});
</script>
@endsection