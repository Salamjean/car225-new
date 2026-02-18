@extends('compagnie.layouts.template')

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
            <h1 class="main-title">Modifier le profil</h1>
            <p class="main-subtitle">Mise à jour des informations de l'agent <strong>{{ $agent->name }} {{ $agent->prenom }}</strong></p>
        </div>
        <div class="header-right">
            <a href="{{ route('compagnie.agents.index') }}" class="btn btn-filter">
                <i class="fas fa-arrow-left"></i>
                <span>Retour à la liste</span>
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xxl-10">
            <div class="main-card-modern animate__animated animate__fadeInUp">
                <div class="card-header-inner">
                    <div class="step-indicator">
                        <div class="step active">
                            <span class="step-num">1</span>
                            <span class="step-text">Informations Générales</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="step">
                            <span class="step-num">2</span>
                            <span class="step-text">Sécurité & Accès</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('compagnie.agents.update', $agent->id) }}" enctype="multipart/form-data" class="premium-form p-4 p-md-5">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <!-- Profil Side -->
                        <div class="col-lg-4">
                            <div class="profile-upload-section">
                                <label class="section-label">Photo de profil</label>
                                <div class="avatar-preview-wrapper shadow-sm">
                                    <div class="avatar-current" id="imagePreviewContainer">
                                        @if($agent->profile_picture)
                                            <img id="imagePreview" src="{{ Storage::url($agent->profile_picture) }}" alt="Photo agent">
                                        @else
                                            <div id="avatarPlaceholder" class="avatar-placeholder-big">
                                                {{ substr($agent->name, 0, 1) }}{{ substr($agent->prenom, 0, 1) }}
                                            </div>
                                            <img id="imagePreview" src="#" alt="Aperçu" style="display: none;">
                                        @endif
                                    </div>
                                    <label for="profile_picture" class="btn-change-avatar">
                                        <i class="fas fa-camera"></i>
                                        <input type="file" id="profile_picture" name="profile_picture" class="d-none" accept="image/*">
                                    </label>
                                </div>
                                <p class="upload-hint">Cliquez sur l'icône pour changer la photo. Formats acceptés : JPG, PNG (Max. 2Mo)</p>
                            </div>
                        </div>

                        <!-- Form Side -->
                        <div class="col-lg-8">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label>Nom de famille <span class="required">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-user"></i>
                                            <input type="text" name="name" value="{{ old('name', $agent->name) }}" required placeholder="Ex: Bakayoko">
                                        </div>
                                        @error('name') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label>Prénom(s) <span class="required">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-user-tag"></i>
                                            <input type="text" name="prenom" value="{{ old('prenom', $agent->prenom) }}" required placeholder="Ex: Jean-Marc">
                                        </div>
                                        @error('prenom') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label>Adresse Email <span class="required">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-envelope"></i>
                                            <input type="email" name="email" value="{{ old('email', $agent->email) }}" required placeholder="agent@compagnie.com">
                                        </div>
                                        @error('email') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label>Numéro de téléphone <span class="required">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-phone-alt"></i>
                                            <input type="text" name="contact" value="{{ old('contact', $agent->contact) }}" required placeholder="Ex: 07 00 00 00 00">
                                        </div>
                                        @error('contact') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label>Gare d'affectation <span class="required">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-building"></i>
                                            <select name="gare_id" required>
                                                <option value="">Sélectionner une gare</option>
                                                @foreach($gares as $gare)
                                                    <option value="{{ $gare->id }}" {{ (old('gare_id', $agent->gare_id) == $gare->id) ? 'selected' : '' }}>
                                                        {{ $gare->nom_gare }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('gare_id') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label>Commune de résidence <span class="required">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <input type="text" name="commune" value="{{ old('commune', $agent->commune) }}" required placeholder="Ex: Cocody">
                                        </div>
                                        @error('commune') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group-modern">
                                        <label>Contact d'urgence <span class="required">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-ambulance"></i>
                                            <input type="text" name="cas_urgence" value="{{ old('cas_urgence', $agent->cas_urgence) }}" required placeholder="Nom et numéro du proche">
                                        </div>
                                        <small class="form-hint">Indispensable pour la sécurité de vos agents.</small>
                                        @error('cas_urgence') <span class="error-msg">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-footer-actions mt-5 pt-4 border-top">
                                <div class="d-flex justify-content-end gap-3">
                                    <a href="{{ route('compagnie.agents.index') }}" class="btn btn-link-modern">Annuler les modifications</a>
                                    <button type="submit" class="btn btn-primary-modern">
                                        <i class="fas fa-save"></i>
                                        <span>Mettre à jour l'agent</span>
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
/* Modern CSS Reset & Variables (Same as Index for consistency) */
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
}

.main-title {
    font-size: 2.25rem;
    font-weight: 800;
    color: var(--text-main);
    letter-spacing: -0.02em;
}

.main-subtitle { color: var(--text-muted); margin: 0; }

.btn-filter {
    background: white; border: 1px solid var(--border-color); padding: 0.75rem 1.25rem;
    border-radius: 0.75rem; font-weight: 600; color: var(--text-main);
    display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s;
}

.btn-filter:hover { background: #f1f5f9; transform: translateY(-2px); }

/* Card */
.main-card-modern {
    background: white; border-radius: var(--radius-xl);
    border: 1px solid var(--border-color);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
}

.card-header-inner {
    padding: 1.5rem 3rem; background: #f8fafc; border-bottom: 1px solid var(--border-color);
}

/* Step Indicator */
.step-indicator { display: flex; align-items: center; gap: 1.5rem; }

.step { display: flex; align-items: center; gap: 0.75rem; color: var(--text-muted); opacity: 0.6; }
.step.active { color: var(--primary); opacity: 1; font-weight: 700; }
.step-num {
    width: 28px; height: 28px; border-radius: 50%; background: #e2e8f0;
    display: flex; align-items: center; justify-content: center; font-size: 0.8rem;
}
.active .step-num { background: var(--primary); color: white; }
.step-text { font-size: 0.9rem; }
.step-line { flex-grow: 0; width: 40px; height: 2px; background: #e2e8f0; }

/* Profile Upload */
.profile-upload-section { text-align: center; padding: 2rem; }

.avatar-preview-wrapper {
    position: relative; width: 180px; height: 180px; margin: 0 auto 1.5rem;
}

.avatar-current {
    width: 100%; height: 100%; border-radius: 2rem; overflow: hidden;
    background: #f1f5f9; border: 4px solid white;
}

.avatar-current img { width: 100%; height: 100%; object-fit: cover; }

.avatar-placeholder-big {
    width: 100%; height: 100%; background: linear-gradient(135deg, #e94f1b, #f97316);
    color: white; display: flex; align-items: center; justify-content: center;
    font-size: 3.5rem; font-weight: 800;
}

.btn-change-avatar {
    position: absolute; bottom: 8px; right: 8px; width: 44px; height: 44px;
    background: white; border-radius: 1rem; color: var(--primary);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: all 0.2s;
}

.btn-change-avatar:hover { transform: scale(1.1); background: var(--primary); color: white; }

.upload-hint { font-size: 0.8rem; color: var(--text-muted); line-height: 1.4; }

/* Form Fields */
.form-group-modern { margin-bottom: 0.5rem; }
.form-group-modern label { font-weight: 700; font-size: 0.9rem; color: var(--text-main); margin-bottom: 0.5rem; display: block; }
.required { color: var(--primary); }

.input-with-icon { position: relative; }
.input-with-icon i {
    position: absolute; left: 1rem; top: 50%; transform: translateY(-50%);
    color: var(--text-muted); font-size: 0.9rem;
}

.input-with-icon input, .input-with-icon select {
    width: 100%; padding: 0.8rem 1rem 0.8rem 2.75rem;
    border-radius: 1rem; border: 1px solid var(--border-color);
    background: #fdfdfd; font-weight: 600; color: var(--text-main);
    transition: all 0.2s;
}

.input-with-icon input:focus, .input-with-icon select:focus {
    border-color: var(--primary); background: white;
    box-shadow: 0 0 0 4px rgba(233, 79, 27, 0.1); outline: none;
}

.form-hint { font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 0.4rem; }
.error-msg { color: #ef4444; font-size: 0.8rem; font-weight: 600; margin-top: 0.4rem; display: block; }

/* Buttons */
.btn-primary-modern {
    background: var(--primary); color: white; padding: 0.8rem 2rem; border-radius: 1rem;
    font-weight: 700; border: none; display: flex; align-items: center; gap: 0.75rem;
    transition: all 0.2s; box-shadow: 0 4px 12px rgba(233, 79, 27, 0.2);
}

.btn-primary-modern:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 6px 15px rgba(233, 79, 27, 0.3); color: white; }

.btn-link-modern { font-weight: 700; color: var(--text-muted); border: none; background: none; transition: all 0.2s; text-decoration: none !important; }
.btn-link-modern:hover { color: var(--primary); }

@media (max-width: 992px) {
    .content-wrapper-modern { padding: 1rem; }
    .action-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
    .card-header-inner { display: none; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image Preview Logic
    const profileInput = document.getElementById('profile_picture');
    const imagePreview = document.getElementById('imagePreview');
    const placeholder = document.getElementById('avatarPlaceholder');

    profileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    });

    // Form success toast
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Mise à jour réussie',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    @endif
});
</script>
@endsection
