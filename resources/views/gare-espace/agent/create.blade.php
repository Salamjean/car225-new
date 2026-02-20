@extends('gare-espace.layouts.template')

@section('content')
<!-- Import Google Fonts & Animate.css -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<div class="content-wrapper-modern">
    <!-- Header Actions -->
    <div class="action-header animate__animated animate__fadeIn">
        <div class="header-left">
            <h1 class="main-title">Nouvel Agent</h1>
            <p class="main-subtitle">Créez un profil complet pour votre futur collaborateur</p>
        </div>
        <div class="header-right">
            <a href="{{ route('gare-espace.agents.index') }}" class="btn btn-filter">
                <i class="fas fa-arrow-left"></i>
                <span>Retour à la liste</span>
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xxl-10">
            <div class="main-card-modern animate__animated animate__fadeInUp">
                <!-- Inner Header with Step Indicators -->
                <div class="card-header-inner">
                    <div class="step-indicator">
                        <div class="step active">
                            <span class="step-num">1</span>
                            <span class="step-text">Profil & Identité</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="step">
                            <span class="step-num">2</span>
                            <span class="step-text">Accès & Sécurité</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('gare-espace.agents.store') }}" enctype="multipart/form-data" class="premium-form p-4 p-md-5" id="createAgentForm">
                    @csrf

                    <div class="row g-4">
                        <!-- Profil Side -->
                        <div class="col-lg-4">
                            <div class="profile-upload-section">
                                <label class="section-label">Photo d'identité</label>
                                <div class="avatar-preview-wrapper shadow-sm">
                                    <div class="avatar-current" id="imagePreviewContainer">
                                        <div id="avatarPlaceholder" class="avatar-placeholder-big">
                                            <i class="fas fa-user-plus"></i>
                                        </div>
                                        <img id="imagePreview" src="#" alt="Aperçu" style="display: none;">
                                    </div>
                                    <label for="profile_picture" class="btn-change-avatar">
                                        <i class="fas fa-camera"></i>
                                        <input type="file" id="profile_picture" name="profile_picture" class="d-none" accept="image/*">
                                    </label>
                                </div>
                                <div class="upload-guidelines">
                                    <p class="upload-hint"><i class="fas fa-info-circle me-1"></i> Formats acceptés : JPG, PNG (Max. 2Mo)</p>
                                    <ul class="mini-checklist">
                                        <li><i class="fas fa-check"></i> Fond neutre</li>
                                        <li><i class="fas fa-check"></i> Visage centré</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Form Side -->
                        <div class="col-lg-8">
                            <div class="form-section-header mb-4">
                                <h4 class="section-inner-title">Coordonnées de l'agent</h4>
                                <div class="title-accent"></div>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label>Nom de famille <span class="required">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-user"></i>
                                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ex: Bakayoko">
                                        </div>
                                        @error('name') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label>Prénom(s) <span class="required">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-user-tag"></i>
                                            <input type="text" name="prenom" value="{{ old('prenom') }}" required placeholder="Ex: Jean-Marc">
                                        </div>
                                        @error('prenom') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label>Adresse Email <span class="required">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-envelope"></i>
                                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="agent@compagnie.com">
                                        </div>
                                        @error('email') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label>Numéro de téléphone <span class="required">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-phone-alt"></i>
                                            <input type="text" name="contact" value="{{ old('contact') }}" required placeholder="Ex: 0700000000" maxlength="10" minlength="10" pattern="[0-9]{10}">
                                        </div>
                                        @error('contact') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label>Commune de résidence <span class="required">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <input type="text" name="commune" value="{{ old('commune') }}" required placeholder="Ex: Cocody">
                                        </div>
                                        @error('commune') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label>Contact d'urgence <span class="required">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-ambulance"></i>
                                            <input type="text" name="cas_urgence" value="{{ old('cas_urgence') }}" required placeholder="Ex: 0500000000" maxlength="10" minlength="10" pattern="[0-9]{10}">
                                        </div>
                                        <small class="form-hint">Important pour la sécurité de l'agent en déplacement.</small>
                                        @error('cas_urgence') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Form Footer Actions -->
                            <div class="form-footer-actions mt-5 pt-4 border-top">
                                <div class="d-flex justify-content-end gap-3 align-items-center">
                                    <button type="reset" class="btn btn-reset-premium">
                                        <i class="fas fa-redo"></i> Réinitialiser
                                    </button>
                                    <button type="submit" class="btn btn-primary-modern">
                                        <i class="fas fa-user-plus"></i>
                                        <span>Créer le profil agent</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* CSS Variables & Design System */
:root {
    --primary: #e94f1b;
    --primary-light: #ff6b3d;
    --primary-dark: #c13e13;
    --secondary-green: #10b981;
    --gray-bg: #f8fafc;
    --card-bg: #ffffff;
    --text-main: #1e293b;
    --text-muted: #64748b;
    --border-color: #e2e8f0;
    --font-family: 'Plus Jakarta Sans', sans-serif;
    --radius-xl: 1.5rem;
}

body {
    background-color: var(--gray-bg);
}

.content-wrapper-modern {
    padding: 2rem;
    font-family: var(--font-family);
    max-width: 1400px;
    margin: 0 auto;
}

/* Header */
.action-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2.5rem;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.main-title {
    font-size: 2.25rem;
    font-weight: 800;
    color: var(--text-main);
    letter-spacing: -0.02em;
    margin-bottom: 0.25rem;
}

.main-subtitle {
    color: var(--text-muted);
    margin: 0;
}

.btn-filter {
    background: white;
    border: 1px solid var(--border-color);
    padding: 0.75rem 1.25rem;
    border-radius: 0.75rem;
    font-weight: 600;
    color: var(--text-main);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
    text-decoration: none !important;
}

.btn-filter:hover {
    background: #f1f5f9;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}

/* Main Card */
.main-card-modern {
    background: white;
    border-radius: var(--radius-xl);
    border: 1px solid var(--border-color);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.card-header-inner {
    padding: 1.5rem 3rem;
    background: #f8fafc;
    border-bottom: 1px solid var(--border-color);
}

/* Step Indicator */
.step-indicator {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.step {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: var(--text-muted);
    opacity: 0.6;
}

.step.active {
    color: var(--primary);
    opacity: 1;
    font-weight: 700;
}

.step-num {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    transition: all 0.3s;
}

.active .step-num {
    background: var(--primary);
    color: white;
    box-shadow: 0 4px 10px rgba(233, 79, 27, 0.2);
}

.step-text {
    font-size: 0.9rem;
}

.step-line {
    flex-grow: 0;
    width: 40px;
    height: 2px;
    background: #e2e8f0;
}

/* Profile Section */
.profile-upload-section {
    text-align: center;
    padding: 1rem;
}

.section-label {
    display: block;
    font-weight: 700;
    font-size: 0.875rem;
    color: var(--text-main);
    margin-bottom: 1.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.avatar-preview-wrapper {
    position: relative;
    width: 180px;
    height: 180px;
    margin: 0 auto 1.5rem;
}

.avatar-current {
    width: 100%;
    height: 100%;
    border-radius: 2rem;
    overflow: hidden;
    background: #f1f5f9;
    border: 4px solid white;
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
}

.avatar-current img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder-big {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3.5rem;
}

.btn-change-avatar {
    position: absolute;
    bottom: 8px;
    right: 8px;
    width: 44px;
    height: 44px;
    background: var(--primary);
    border-radius: 1rem;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(233, 79, 27, 0.3);
    transition: all 0.2s;
}

.btn-change-avatar:hover {
    transform: scale(1.1) rotate(5deg);
    background: var(--primary-dark);
}

.upload-guidelines {
    padding: 1rem;
    background: #fcfdfe;
    border-radius: 1rem;
    border: 1px solid #f1f5f9;
}

.upload-hint {
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-bottom: 0.75rem;
}

.mini-checklist {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    justify-content: center;
    gap: 1rem;
}

.mini-checklist li {
    font-size: 0.75rem;
    color: #10b981;
    font-weight: 600;
}

.mini-checklist i {
    font-size: 0.6rem;
    margin-right: 0.25rem;
}

/* Form Styles */
.form-section-header {
    position: relative;
    padding-bottom: 0.75rem;
}

.section-inner-title {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--text-main);
    margin: 0;
}

.title-accent {
    width: 40px;
    height: 4px;
    background: var(--primary);
    border-radius: 2px;
    margin-top: 0.5rem;
}

.form-group-modern {
    margin-bottom: 0.5rem;
}

.form-group-modern label {
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--text-main);
    margin-bottom: 0.5rem;
    display: block;
}

.required {
    color: var(--primary);
}

.input-with-icon {
    position: relative;
}

.input-with-icon i {
    position: absolute;
    left: 1.25rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 0.9rem;
    transition: color 0.2s;
}

.input-with-icon input, 
.input-with-icon select {
    width: 100%;
    padding: 0.85rem 1.25rem 0.85rem 3rem;
    border-radius: 1rem;
    border: 1.5px solid var(--border-color);
    background: #fdfdfd;
    font-weight: 600;
    color: var(--text-main);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.input-with-icon input:focus, 
.input-with-icon select:focus {
    border-color: var(--primary);
    background: white;
    box-shadow: 0 0 0 4px rgba(233, 79, 27, 0.1);
    outline: none;
}

.form-hint {
    font-size: 0.75rem;
    color: var(--text-muted);
    display: block;
    margin-top: 0.4rem;
}

.error-msg {
    color: #ef4444;
    font-size: 0.8rem;
    font-weight: 600;
    margin-top: 0.4rem;
    display: block;
}

/* Footer Actions */
.btn-primary-modern {
    background: var(--primary);
    color: white;
    padding: 0.85rem 2.25rem;
    border-radius: 1rem;
    font-weight: 700;
    border: none;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(233, 79, 27, 0.2);
}

.btn-primary-modern:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    color: white;
    box-shadow: 0 6px 18px rgba(233, 79, 27, 0.3);
}

.btn-reset-premium {
    background: #f1f5f9;
    color: #475569;
    padding: 0.85rem 1.5rem;
    border-radius: 1rem;
    border: none;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
}

.btn-reset-premium:hover {
    background: #e2e8f0;
    color: #1e293b;
}

/* Responsive Fixes */
@media (max-width: 992px) {
    .content-wrapper-modern { padding: 1.25rem; }
    .action-header { margin-bottom: 1.5rem; }
    .card-header-inner { padding: 1.25rem; }
    .main-title { font-size: 1.75rem; }
}

@media (max-width: 576px) {
    .step-text { display: none; }
    .btn-primary-modern { width: 100%; justify-content: center; }
    .form-footer-actions .d-flex { flex-direction: column-reverse; }
    .btn-reset-premium { width: 100%; justify-content: center; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Real-time Image Preview
    const profileInput = document.getElementById('profile_picture');
    const imagePreview = document.getElementById('imagePreview');
    const placeholder = document.getElementById('avatarPlaceholder');

    profileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'Fichier trop volumineux',
                    text: 'La taille maximale est de 2 Mo.',
                    confirmButtonColor: '#e94f1b'
                });
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
                imagePreview.classList.add('animate__animated', 'animate__fadeIn');
            }
            reader.readAsDataURL(file);
        }
    });

    // Form Reset Logic
    const createForm = document.getElementById('createAgentForm');
    createForm.addEventListener('reset', function() {
        setTimeout(() => {
            imagePreview.src = '#';
            imagePreview.style.display = 'none';
            if (placeholder) placeholder.style.display = 'flex';
        }, 10);
    });

    // Success Message Handling
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Agent créé !',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    @endif

    // Error Message Handling
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Erreur de saisie',
            text: 'Veuillez vérifier les informations du formulaire.',
            confirmButtonColor: '#e94f1b'
        });
    @endif
});
</script>
@endsection
