@extends('compagnie.layouts.template')

@section('page-title', 'Nouveau Personnel')
@section('page-subtitle', 'Ajoutez un nouveau chauffeur ou convoyeur à votre équipe')

@section('styles')
<style>
    /* Utilisation du même CSS que pour Agent Create */
    .form-wrapper { max-width: 1000px; margin: 0 auto; }
    
    .btn-back { display: inline-flex; align-items: center; gap: 8px; color: var(--text-3); font-weight: 700; font-size: 13px; text-decoration: none; margin-bottom: 24px; transition: color 0.2s; }
    .btn-back:hover { color: var(--orange); text-decoration: none; }

    .form-grid-layout { display: grid; grid-template-columns: 280px 1fr; gap: 24px; }

    .dash-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; box-shadow: var(--shadow-sm); overflow: hidden; }
    .card-header { padding: 16px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
    .card-step { width: 28px; height: 28px; background: var(--orange); color: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; }
    .card-title { font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-1); margin: 0; }
    .card-body { padding: 24px; }

    /* Photo Upload */
    .photo-upload-container { display: flex; flex-direction: column; align-items: center; text-align: center; }
    .photo-label { font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--text-3); letter-spacing: 1px; margin-bottom: 16px; }
    .avatar-wrapper { position: relative; width: 160px; height: 160px; border-radius: 30px; background: var(--surface-2); border: 4px solid var(--surface); box-shadow: var(--shadow-md); display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .avatar-placeholder { font-size: 60px; color: var(--border-strong); }
    .avatar-preview { width: 100%; height: 100%; object-fit: cover; display: none; }
    .btn-upload { position: absolute; bottom: -10px; right: -10px; width: 44px; height: 44px; border-radius: 14px; background: var(--text-1); color: white; display: flex; align-items: center; justify-content: center; font-size: 16px; cursor: pointer; transition: all 0.2s; border: 4px solid var(--surface); z-index: 10; }
    .btn-upload:hover { background: var(--orange); transform: scale(1.05); }

    .photo-hints { margin-top: 32px; padding: 16px; background: var(--orange-light); border-radius: 12px; border: 1px solid var(--orange-mid); text-align: left; width: 100%; }
    .photo-hints h4 { font-size: 10px; font-weight: 800; color: var(--orange-dark); text-transform: uppercase; margin-bottom: 8px; }
    .photo-hints ul { list-style: none; padding: 0; margin: 0; }
    .photo-hints li { font-size: 11px; color: var(--text-2); font-weight: 600; margin-bottom: 4px; display: flex; align-items: center; gap: 6px; }
    .photo-hints li::before { content: ""; width: 4px; height: 4px; border-radius: 50%; background: var(--orange); }

    /* Inputs */
    .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-label { font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--text-3); letter-spacing: 0.5px; }
    .input-with-icon { position: relative; }
    .input-with-icon i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-3); }
    .input-with-icon select { padding-right: 36px; }
    .input-with-icon .chevron { position: absolute; right: 16px; left: auto; font-size: 10px; pointer-events: none; }
    .form-control { width: 100%; padding: 12px 16px 12px 44px; border-radius: 12px; border: 1px solid var(--border); background: var(--surface-2); font-size: 13px; font-weight: 600; color: var(--text-1); outline: none; transition: all 0.2s; appearance: none; }
    .form-control:focus { background: var(--surface); border-color: var(--orange); box-shadow: 0 0 0 3px var(--orange-light); }
    .form-control option { background: var(--surface); color: var(--text-1); padding: 10px; }
    select.form-control { cursor: pointer; height: 48px; }
    .form-control.is-invalid { border-color: #EF4444; box-shadow: 0 0 0 3px #FEE2E2; }
    .form-error { font-size: 10px; font-weight: 700; color: #DC2626; margin-top: 4px; }

    .form-row-phone { display: flex; gap: 12px; }
    .form-row-phone > div:first-child { width: 120px; flex-shrink: 0; }
    .form-row-phone > div:last-child { flex: 1; }
    .form-row-phone .form-control { padding-left: 16px; }

    /* Action Footer */
    .form-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; }
    .btn-reset { padding: 14px 24px; border-radius: 12px; background: transparent; border: 1px solid transparent; font-size: 12px; font-weight: 800; text-transform: uppercase; color: var(--text-3); cursor: pointer; transition: 0.2s; }
    .btn-reset:hover { background: var(--surface-2); color: var(--text-2); }
    .btn-submit { padding: 14px 32px; border-radius: 12px; background: var(--text-1); color: white; border: none; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 8px; }
    .btn-submit:hover { background: var(--orange); }

    @media (max-width: 992px) {
        .form-grid-layout { grid-template-columns: 1fr; }
        .photo-upload-container { padding-bottom: 24px; border-bottom: 1px solid var(--border); margin-bottom: 24px; }
    }
    @media (max-width: 640px) {
        .form-grid-2 { grid-template-columns: 1fr; }
        .form-actions { flex-direction: column-reverse; }
        .btn-submit, .btn-reset { width: 100%; text-align: center; justify-content: center; }
        .form-row-phone { flex-direction: column; }
        .form-row-phone > div:first-child { width: 100%; }
    }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <div class="form-wrapper">
        <a href="{{ route('personnel.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>

        <form action="{{ route('personnel.store') }}" method="POST" enctype="multipart/form-data" id="createPersonnelForm">
            @csrf

            <div class="form-grid-layout">
                
                {{-- ── LEFT: PHOTO ── --}}
                <div class="dash-card">
                    <div class="card-body photo-upload-container">
                        <label class="photo-label">Photo de Profil</label>
                        
                        <div class="avatar-wrapper">
                            <div class="avatar-placeholder" id="avatarPlaceholder">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <img id="imagePreview" src="#" alt="Aperçu" class="avatar-preview">
                            
                            <label for="profile_image" class="btn-upload">
                                <i class="fas fa-camera"></i>
                                <input type="file" id="profile_image" name="profile_image" class="d-none" accept="image/*">
                            </label>
                        </div>
                        @error('profile_image') <span class="form-error mt-2">{{ $message }}</span> @enderror

                        <div class="photo-hints">
                            <h4><i class="fas fa-info-circle"></i> Conseils Photo</h4>
                            <ul>
                                <li>Format carré recommandé</li>
                                <li>Taille maximale : 2 Mo</li>
                                <li>Visage bien visible</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- ── RIGHT: INFORMATIONS ── --}}
                <div style="display: flex; flex-direction: column; gap: 24px;">
                    
                    {{-- Section 1: Affectation --}}
                    @if(isset($gares) && $gares->count() > 0)
                    <div class="dash-card">
                        <div class="card-header">
                            <div class="card-step">1</div>
                            <h3 class="card-title">Affectation & Rôle</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label class="form-label">Gare de Rattachement <span class="text-danger">*</span></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-university"></i>
                                        <select name="gare_id" required class="form-control">
                                            <option value="" disabled selected>-- Choisir une gare --</option>
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

                                <div class="form-group">
                                    <label class="form-label">Type de Fonction <span class="text-danger">*</span></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-id-badge"></i>
                                        <select name="type_personnel" required class="form-control">
                                            <option value="" disabled selected>-- Sélectionner rôle --</option>
                                            <option value="Chauffeur" {{ old('type_personnel') == 'Chauffeur' ? 'selected' : '' }}>Chauffeur</option>
                                            <option value="Convoyeur" {{ old('type_personnel') == 'Convoyeur' ? 'selected' : '' }}>Convoyeur</option>
                                        </select>
                                        <i class="fas fa-chevron-down chevron"></i>
                                    </div>
                                    @error('type_personnel') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Section 2: Identité --}}
                    <div class="dash-card">
                        <div class="card-header">
                            <div class="card-step" style="background: var(--blue);">2</div>
                            <h3 class="card-title">Identité & Contact</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label class="form-label">Nom de Famille <span class="text-danger">*</span></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-id-card"></i>
                                        <input type="text" name="name" value="{{ old('name') }}" required class="form-control" placeholder="Bakayoko">
                                    </div>
                                    @error('name') <span class="form-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Prénom(s) <span class="text-danger">*</span></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-user"></i>
                                        <input type="text" name="prenom" value="{{ old('prenom') }}" required class="form-control" placeholder="Jean-Marc">
                                    </div>
                                    @error('prenom') <span class="form-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group" style="grid-column: 1 / -1;">
                                    <label class="form-label">Adresse Email Pro <span class="text-danger">*</span></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-envelope"></i>
                                        <input type="email" name="email" value="{{ old('email') }}" required class="form-control" placeholder="exemple@compagnie.com">
                                    </div>
                                    @error('email') <span class="form-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group" style="grid-column: 1 / -1;">
                                    <label class="form-label">Numéro Mobile Personnel <span class="text-danger">*</span></label>
                                    <div class="form-row-phone">
                                        <div class="input-with-icon">
                                            <select name="country_code" required class="form-control" style="padding-left:16px;">
                                                <option value="+225" selected>🇨🇮 +225</option>
                                                <option value="+33">🇫🇷 +33</option>
                                                <option value="+1">🇺🇸 +1</option>
                                            </select>
                                            <i class="fas fa-chevron-down chevron"></i>
                                        </div>
                                        <div class="input-with-icon">
                                            <i class="fas fa-phone-alt"></i>
                                            <input type="text" name="contact" value="{{ old('contact') }}" required maxlength="10" class="form-control" placeholder="07 00 00 00 00" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                                        </div>
                                    </div>
                                    @error('contact') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Urgence --}}
                    <div class="dash-card">
                        <div class="card-header" style="background: var(--surface-2);">
                            <div class="card-step" style="background: var(--text-1);">3</div>
                            <h3 class="card-title">Données de Secours</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-grid-2">
                                <div class="form-group" style="grid-column: 1 / -1;">
                                    <label class="form-label">Numéro Mobile Urgence <span class="text-danger">*</span></label>
                                    <div class="form-row-phone">
                                        <div class="input-with-icon">
                                            <select name="country_code_urgence" required class="form-control" style="padding-left:16px;">
                                                <option value="+225" selected>🇨🇮 +225</option>
                                                <option value="+33">🇫🇷 +33</option>
                                            </select>
                                            <i class="fas fa-chevron-down chevron"></i>
                                        </div>
                                        <div class="input-with-icon">
                                            <i class="fas fa-ambulance"></i>
                                            <input type="text" name="contact_urgence" id="contact_urgence" value="{{ old('contact_urgence') }}" required maxlength="10" class="form-control" placeholder="01 00 00 00 00" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                                        </div>
                                    </div>
                                    <span id="error-contact-same" class="form-error" style="display: none;">Le contact d'urgence doit être différent du contact principal.</span>
                                    @error('contact_urgence') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="form-actions">
                        <button type="reset" class="btn-reset">Réinitialiser</button>
                        <button type="submit" class="btn-submit">
                            Enregistrer le Membre <i class="fas fa-user-plus"></i>
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
    const profileInput = document.getElementById('profile_image');
    const imagePreview = document.getElementById('imagePreview');
    const placeholder = document.getElementById('avatarPlaceholder');

    profileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error', title: 'Fichier volumineux', text: 'La taille maximale autorisée est de 2 Mo.',
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

    const createForm = document.getElementById('createPersonnelForm');
    createForm.addEventListener('reset', function() {
        setTimeout(() => {
            imagePreview.src = '#';
            imagePreview.style.display = 'none';
            placeholder.style.display = 'flex';
        }, 10);
    });

    const contactInput = document.querySelector('input[name="contact"]');
    const contactUrgenceInput = document.getElementById('contact_urgence');
    const errorSameContact = document.getElementById('error-contact-same');

    function validateContacts() {
        if (contactInput.value && contactUrgenceInput.value && contactInput.value === contactUrgenceInput.value) {
            errorSameContact.style.display = 'block';
            contactUrgenceInput.classList.add('is-invalid');
            return false;
        } else {
            errorSameContact.style.display = 'none';
            contactUrgenceInput.classList.remove('is-invalid');
            return true;
        }
    }

    contactInput.addEventListener('input', validateContacts);
    contactUrgenceInput.addEventListener('input', validateContacts);

    createForm.addEventListener('submit', function(e) {
        if (!validateContacts()) {
            e.preventDefault();
            Swal.fire({
                icon: 'error', title: 'Conflit de numéros', text: "Le numéro d'urgence ne peut pas être identique au numéro principal.",
                confirmButtonColor: '#F97316', customClass: { popup: 'rounded-lg border-0 shadow-sm' }
            });
        }
    });

    @if(session('success'))
        Swal.fire({
            icon: 'success', title: 'Enregistrement Réussi', text: "{{ session('success') }}",
            confirmButtonColor: '#F97316', customClass: { popup: 'rounded-lg border-0 shadow-sm' }
        });
    @endif
});
</script>
@endsection