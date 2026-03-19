@extends('compagnie.layouts.template')

@section('page-title', 'Enrôlement Caisse')
@section('page-subtitle', 'Ajouter un nouveau gestionnaire de caisse à votre équipe')

@section('styles')
<style>
    /* Structure de la page */
    .enrolment-container {
        max-width: 1100px;
        margin: 0 auto;
    }
    
    .form-card {
        background: var(--surface);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .form-card-header {
        padding: 16px 24px;
        background: var(--surface-2);
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center; gap: 12px;
    }
    .form-step-badge {
        width: 32px; height: 32px; border-radius: 10px;
        background: var(--orange); color: white;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 13px;
        box-shadow: 0 4px 10px rgba(249,115,22,0.3);
    }
    
    /* Upload de profil */
    .profile-upload-wrap {
        position: relative; width: 160px; height: 160px; margin: 0 auto 24px;
    }
    .profile-img-box {
        width: 100%; height: 100%; border-radius: 30px;
        background: var(--surface-2); border: 2px dashed var(--border-strong);
        display: flex; align-items: center; justify-content: center;
        overflow: hidden; transition: all 0.3s;
    }
    .profile-img-box:hover { border-color: var(--orange); }
    .profile-img-box img { width: 100%; height: 100%; object-fit: cover; }
    .profile-icon-placeholder { font-size: 40px; color: var(--text-3); }
    
    .profile-upload-btn {
        position: absolute; bottom: -10px; right: -10px;
        width: 44px; height: 44px; border-radius: 12px;
        background: var(--orange); color: white;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; box-shadow: var(--shadow-md); border: 4px solid var(--surface);
        transition: transform 0.2s, background 0.2s;
    }
    .profile-upload-btn:hover { background: var(--orange); transform: scale(1.05); }

    /* Champs de formulaire modernes avec icônes */
    .input-group-modern { position: relative; margin-bottom: 20px; }
    .input-group-modern .form-label {
        display: block; font-size: 10px; font-weight: 700; color: var(--text-3);
        text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;
    }
    .input-group-modern .input-icon {
        position: absolute; left: 16px; top: 35px;
        color: var(--text-3); font-size: 14px; transition: color 0.2s;
    }
    .input-group-modern .input-modern {
        width: 100%; padding: 12px 16px 12px 42px;
        border: 1px solid var(--border-strong); border-radius: var(--radius-sm);
        background: var(--surface-2); color: var(--text-1);
        font-size: 13px; font-weight: 600; transition: all 0.2s;
    }
    .input-group-modern .input-modern:focus {
        outline: none; border-color: var(--orange); background: var(--surface);
        box-shadow: 0 0 0 3px var(--orange-light);
    }
    .input-group-modern .input-modern:focus + .input-icon,
    .input-group-modern:focus-within .input-icon { color: var(--orange); }
    
    select.input-modern { appearance: none; cursor: pointer; height: 48px; }
    .input-modern option { background: var(--surface); color: var(--text-1); padding: 10px; }
    .select-chevron { position: absolute; right: 16px; top: 35px; color: var(--text-3); font-size: 12px; pointer-events: none; }

    .btn-submit {
        background: linear-gradient(135deg, var(--orange) 0%, var(--orange-dark) 100%);
        color: white; padding: 14px 28px; border-radius: var(--radius-sm);
        font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;
        border: none; cursor: pointer; box-shadow: 0 4px 15px rgba(249,115,22,0.3); transition: 0.2s;
    }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(249,115,22,0.4); color: white; }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <div class="enrolment-container">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <a href="{{ route('compagnie.caisse.index') }}" class="btn btn-light btn-sm" style="font-weight: 700; font-size: 12px; border-radius: var(--radius-sm);">
                <i class="fas fa-arrow-left mr-2"></i> Retour au répertoire
            </a>
        </div>

        <form method="POST" action="{{ route('compagnie.caisse.store') }}" enctype="multipart/form-data" id="createCaisseForm">
            @csrf

            <div class="row">
                {{-- COLONNE GAUCHE : PROFIL --}}
                <div class="col-lg-4 mb-4">
                    <div class="form-card text-center p-4" style="position: sticky; top: 80px;">
                        <h4 style="font-size: 11px; font-weight: 800; color: var(--text-3); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px;">Photo de profil</h4>
                        
                        <div class="profile-upload-wrap">
                            <div class="profile-img-box" id="imagePreviewContainer">
                                <div id="avatarPlaceholder" class="profile-icon-placeholder">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <img id="imagePreview" src="#" alt="Aperçu" class="d-none">
                            </div>
                            <label for="profile_picture" class="profile-upload-btn">
                                <i class="fas fa-camera"></i>
                                <input type="file" id="profile_picture" name="profile_picture" class="d-none" accept="image/*">
                            </label>
                        </div>

                        <div style="background: var(--orange-light); border: 1px solid var(--orange-mid); border-radius: 12px; padding: 16px; text-align: left; margin-top: 20px;">
                            <h5 style="font-size: 11px; font-weight: 800; color: var(--orange-dark); text-transform: uppercase; margin-bottom: 8px;">
                                <i class="fas fa-shield-alt mr-1"></i> Information Sécurité
                            </h5>
                            <p style="font-size: 11px; color: var(--orange-dark); opacity: 0.8; margin: 0; font-style: italic; line-height: 1.4;">
                                "Les accès caisse sont strictement personnels. Chaque agent est responsable des opérations effectuées sous son identifiant."
                            </p>
                        </div>
                        @error('profile_picture') <span class="text-danger" style="font-size: 11px; font-weight: 700; margin-top: 10px; display: block;">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- COLONNE DROITE : FORMULAIRE --}}
                <div class="col-lg-8">
                    
                    {{-- 01. Affectation --}}
                    <div class="form-card">
                        <div class="form-card-header">
                            <div class="form-step-badge">01</div>
                            <h3 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">Affectation Poste</h3>
                        </div>
                        <div class="p-4">
                            <div class="input-group-modern mb-0">
                                <label class="form-label">Gare de Rattachement</label>
                                <i class="fas fa-university input-icon"></i>
                                <select name="gare_id" required class="input-modern">
                                    <option value="" disabled selected>-- Sélectionner la gare d'affectation --</option>
                                    @foreach($gares as $gare)
                                        <option value="{{ $gare->id }}" {{ old('gare_id') == $gare->id ? 'selected' : '' }}>
                                            {{ $gare->nom_gare }} — {{ $gare->ville }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down select-chevron"></i>
                                @error('gare_id') <span class="text-danger" style="font-size: 10px; font-weight: 700; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- 02. Identité --}}
                    <div class="form-card">
                        <div class="form-card-header">
                            <div class="form-step-badge" style="background: var(--blue); box-shadow: 0 4px 10px rgba(59,130,246,0.3);">02</div>
                            <h3 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">Identité & Coordonnées</h3>
                        </div>
                        <div class="p-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group-modern">
                                        <label class="form-label">Nom de Famille</label>
                                        <i class="fas fa-id-card input-icon"></i>
                                        <input type="text" name="name" value="{{ old('name') }}" required class="input-modern" placeholder="Ex: Bakayoko">
                                        @error('name') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group-modern">
                                        <label class="form-label">Prénom(s)</label>
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" name="prenom" value="{{ old('prenom') }}" required class="input-modern" placeholder="Ex: Jean-Marc">
                                        @error('prenom') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group-modern">
                                        <label class="form-label">Adresse Email Pro</label>
                                        <i class="fas fa-envelope input-icon"></i>
                                        <input type="email" name="email" value="{{ old('email') }}" required class="input-modern" placeholder="agent.caisse@cie.com">
                                        @error('email') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group-modern">
                                        <label class="form-label">Contact Mobile</label>
                                        <i class="fas fa-phone-alt input-icon"></i>
                                        <input type="text" name="contact" value="{{ old('contact') }}" required maxlength="10" class="input-modern" placeholder="07 00 00 00 00" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                                        @error('contact') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 03. Accès --}}
                    <div class="form-card">
                        <div class="form-card-header">
                            <div class="form-step-badge" style="background: var(--orange); box-shadow: 0 4px 10px rgba(249,115,22,0.3);">03</div>
                            <h3 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">Paramètres d'Accès</h3>
                        </div>
                        <div class="p-4" style="background: #FAFAF9;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="input-group-modern">
                                        <label class="form-label">Code ID Caisse (Identifiant)</label>
                                        <i class="fas fa-id-badge input-icon"></i>
                                        <input type="text" name="code_id" value="{{ old('code_id') }}" required class="input-modern" style="background: white;" placeholder="Ex: CAI-2024-001">
                                        @error('code_id') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group-modern mb-md-0">
                                        <label class="form-label">Mot de Passe</label>
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password" name="password" required class="input-modern" style="background: white;" placeholder="••••••••">
                                        @error('password') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group-modern mb-0">
                                        <label class="form-label">Confirmer Mot de Passe</label>
                                        <i class="fas fa-check-double input-icon"></i>
                                        <input type="password" name="password_confirmation" required class="input-modern" style="background: white;" placeholder="••••••••">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex align-items-center justify-content-between mt-4 pt-3" style="border-top: 1px solid var(--border);">
                        <div>
                            <p style="font-size: 11px; font-weight: 600; color: var(--text-3); margin: 0; font-style: italic; max-width: 300px;">En validant, vous permettez à ce nouveau caissier d'effectuer des transactions au nom de la compagnie.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="reset" class="btn btn-light" style="font-weight: 700; font-size: 13px; border-radius: var(--radius-sm);">Réinitialiser</button>
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-check-circle mr-2"></i> Valider l'Ouverture
                            </button>
                        </div>
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
                    icon: 'error',
                    title: 'Fichier trop lourd',
                    text: 'La taille maximale autorisée est de 2 Mo.',
                    confirmButtonColor: '#F97316',
                    customClass: { popup: 'rounded-lg border-0 shadow-sm' }
                });
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.classList.remove('d-none');
                placeholder.classList.add('d-none');
            }
            reader.readAsDataURL(file);
        }
    });

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Opération réussie',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            customClass: { popup: 'rounded-lg shadow-sm' }
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Erreur de validation',
            text: 'Veuillez vérifier les champs signalés en rouge.',
            confirmButtonColor: '#F97316',
            customClass: { popup: 'rounded-lg border-0 shadow-sm' }
        });
    @endif
});
</script>
@endsection