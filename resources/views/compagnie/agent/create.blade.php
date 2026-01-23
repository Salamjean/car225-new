@extends('compagnie.layouts.template')
@section('content')
<div class="container-fluid">
    <!-- En-tête de page -->
    <div class="row page-header-modern">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between p-4">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="header-icon-wrapper me-3">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div>
                        <h1 class="page-title mb-1">Ajouter un nouvel agent</h1>
                        <p class="page-subtitle text-muted mb-0">Créez un compte agent pour votre compagnie</p>
                    </div>
                </div>
                
                <nav aria-label="breadcrumb" class="modern-breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('compagnie.dashboard') }}" class="breadcrumb-link">
                                <i class="fas fa-home me-1"></i>Tableau de bord
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('compagnie.agents.index') }}" class="breadcrumb-link">
                                <i class="fas fa-users me-1"></i>Agents
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-plus-circle me-1"></i>Nouvel agent
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Formulaire principal -->
    <div class="row justify-content-center">
        <div class="col-xxl-9 col-xl-10 col-lg-11">
            <div class="modern-card">
                <!-- En-tête de la carte -->
                <div class="card-header-modern">
                    <div class="d-flex align-items-center">
                        <div class="step-indicator me-4">
                            <span class="step-number">1</span>
                            <div class="step-line"></div>
                            <span class="step-number">2</span>
                            <div class="step-line"></div>
                            <span class="step-number step-active">3</span>
                        </div>
                        <div class="header-content">
                            <h2 class="card-title mb-2">Informations de l'agent</h2>
                            <p class="card-subtitle mb-0">Complétez les informations requises pour créer un nouveau compte</p>
                        </div>
                    </div>
                </div>

                <!-- Corps de la carte -->
                <div class="card-body-modern">
                    <form method="POST" action="{{ route('compagnie.agents.store') }}" enctype="multipart/form-data" id="agentForm" class="modern-form">
                        @csrf

                        <!-- Section 1: Informations de base -->
                        <div class="form-section-modern mb-5">
                            <div class="section-header-modern mb-4">
                                <div class="section-icon">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <div>
                                    <h3 class="section-title mb-2">Informations personnelles</h3>
                                    <p class="section-subtitle">Renseignez les informations d'identité de l'agent</p>
                                </div>
                                <span class="section-badge">Obligatoire</span>
                            </div>
                            
                            <div class="row g-4">
                                <!-- Colonne 1: Nom -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-group-modern">
                                        <label for="name" class="form-label-modern">
                                            <span class="label-text">Nom</span>
                                            <span class="label-required">*</span>
                                        </label>
                                        <div class="input-group-modern">
                                            <span class="input-icon">
                                                <i class="fas fa-user"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control-modern @error('name') is-invalid @enderror" 
                                                   id="name" 
                                                   name="name" 
                                                   value="{{ old('name') }}" 
                                                   placeholder="Entrez le nom"
                                                   required>
                                            <div class="input-hint" data-hint="Le nom de famille de l'agent"></div>
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback-modern">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Colonne 2: Prénom -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-group-modern">
                                        <label for="prenom" class="form-label-modern">
                                            <span class="label-text">Prénom</span>
                                            <span class="label-required">*</span>
                                        </label>
                                        <div class="input-group-modern">
                                            <span class="input-icon">
                                                <i class="fas fa-user"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control-modern @error('prenom') is-invalid @enderror" 
                                                   id="prenom" 
                                                   name="prenom" 
                                                   value="{{ old('prenom') }}" 
                                                   placeholder="Entrez le prénom"
                                                   required>
                                            <div class="input-hint" data-hint="Le prénom de l'agent"></div>
                                        </div>
                                        @error('prenom')
                                            <div class="invalid-feedback-modern">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Colonne 3: Email -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-group-modern">
                                        <label for="email" class="form-label-modern">
                                            <span class="label-text">Email</span>
                                            <span class="label-required">*</span>
                                        </label>
                                        <div class="input-group-modern">
                                            <span class="input-icon">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                            <input type="email" 
                                                   class="form-control-modern @error('email') is-invalid @enderror" 
                                                   id="email" 
                                                   name="email" 
                                                   value="{{ old('email') }}" 
                                                   placeholder="exemple@email.com"
                                                   required>
                                            <div class="input-hint" data-hint="L'adresse email de connexion"></div>
                                        </div>
                                        @error('email')
                                            <div class="invalid-feedback-modern">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Colonne 4: Contact -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-group-modern">
                                        <label for="contact" class="form-label-modern">
                                            <span class="label-text">Téléphone</span>
                                            <span class="label-required">*</span>
                                        </label>
                                        <div class="input-group-modern">
                                            <span class="input-icon">
                                                <i class="fas fa-phone"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control-modern @error('contact') is-invalid @enderror" 
                                                   id="contact" 
                                                   name="contact" 
                                                   value="{{ old('contact') }}" 
                                                   placeholder="+XX XXX XXX XXX"
                                                   required>
                                            <div class="input-hint" data-hint="Numéro de téléphone principal"></div>
                                        </div>
                                        @error('contact')
                                            <div class="invalid-feedback-modern">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Colonne 5: Commune -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-group-modern">
                                        <label for="commune" class="form-label-modern">
                                            <span class="label-text">Commune</span>
                                        </label>
                                        <div class="input-group-modern">
                                            <span class="input-icon">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control-modern @error('commune') is-invalid @enderror" 
                                                   id="commune" 
                                                   name="commune" 
                                                   value="{{ old('commune') }}" 
                                                   placeholder="Entrez la commune">
                                            <div class="input-hint" data-hint="Localité de résidence"></div>
                                        </div>
                                        @error('commune')
                                            <div class="invalid-feedback-modern">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Colonne 6: Contact urgence -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-group-modern">
                                        <label for="cas_urgence" class="form-label-modern">
                                            <span class="label-text">Contact d'urgence</span>
                                        </label>
                                        <div class="input-group-modern">
                                            <span class="input-icon">
                                                <i class="fas fa-phone-alt"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control-modern @error('cas_urgence') is-invalid @enderror" 
                                                   id="cas_urgence" 
                                                   name="cas_urgence" 
                                                   value="{{ old('cas_urgence') }}" 
                                                   placeholder="+XX XXX XXX XXX">
                                            <div class="input-hint" data-hint="Contact en cas d'urgence"></div>
                                        </div>
                                        @error('cas_urgence')
                                            <div class="invalid-feedback-modern">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Photo de profil -->
                        <div class="form-section-modern mb-5">
                            <div class="section-header-modern mb-4">
                                <div class="section-icon">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <div>
                                    <h3 class="section-title mb-2">Photo de profil</h3>
                                    <p class="section-subtitle">Ajoutez une photo pour identifier facilement l'agent</p>
                                </div>
                                <span class="section-badge optional">Optionnel</span>
                            </div>
                            
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="file-upload-modern">
                                        <div class="upload-area-modern @error('profile_picture') upload-error @enderror" id="uploadArea">
                                            <div class="upload-icon">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                            </div>
                                            <div class="upload-content">
                                                <h4 class="upload-title">Glissez-déposez votre fichier ici</h4>
                                                <p class="upload-subtitle">ou</p>
                                                <button type="button" class="btn-upload-modern" onclick="document.getElementById('profile_picture').click()">
                                                    <i class="fas fa-folder-open me-2"></i>
                                                    Parcourir les fichiers
                                                </button>
                                                <p class="upload-info mt-3">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Formats supportés : JPEG, PNG, JPG, GIF • Taille max : 2MB
                                                </p>
                                            </div>
                                            <input type="file" 
                                                   class="d-none" 
                                                   id="profile_picture" 
                                                   name="profile_picture" 
                                                   accept="image/jpeg,image/png,image/jpg,image/gif">
                                        </div>
                                        
                                        <!-- Aperçu de l'image -->
                                        <div class="image-preview-modern mt-4" id="imagePreviewContainer" style="display: none;">
                                            <div class="preview-header">
                                                <h5 class="preview-title mb-0">
                                                    <i class="fas fa-image me-2"></i>
                                                    Aperçu de l'image
                                                </h5>
                                                <button type="button" class="btn-remove-modern" onclick="removeImage()">
                                                    <i class="fas fa-times me-1"></i>
                                                    Supprimer
                                                </button>
                                            </div>
                                            <div class="preview-body">
                                                <img id="imagePreview" src="#" alt="Aperçu de l'image" class="preview-image">
                                            </div>
                                        </div>
                                        
                                        @error('profile_picture')
                                            <div class="invalid-feedback-modern mt-2">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-lg-4">
                                    <div class="upload-guidelines">
                                        <h5 class="guidelines-title">
                                            <i class="fas fa-lightbulb me-2"></i>
                                            Recommandations
                                        </h5>
                                        <ul class="guidelines-list">
                                            <li>
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                Photo récente et claire
                                            </li>
                                            <li>
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                Visage bien visible
                                            </li>
                                            <li>
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                Fond neutre de préférence
                                            </li>
                                            <li>
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                Format carré recommandé
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Informations de la compagnie -->
                        <div class="company-info-modern mb-5">
                            <div class="company-header">
                                <h4 class="company-title mb-0">
                                    <i class="fas fa-building me-2"></i>
                                    Informations de la compagnie
                                </h4>
                                <span class="company-badge">Lecture seule</span>
                            </div>
                            <div class="company-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item-modern">
                                            <div class="info-icon">
                                                <i class="fas fa-building"></i>
                                            </div>
                                            <div class="info-content">
                                                <label class="info-label">Compagnie</label>
                                                <p class="info-value">{{ Auth::guard('compagnie')->user()->name ?? 'Non disponible' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item-modern">
                                            <div class="info-icon">
                                                <i class="fas fa-envelope"></i>
                                            </div>
                                            <div class="info-content">
                                                <label class="info-label">Email compagnie</label>
                                                <p class="info-value">{{ Auth::guard('compagnie')->user()->email ?? 'Non disponible' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="company-notice mt-4">
                                    <div class="notice-icon">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    <div class="notice-content">
                                        <h6 class="notice-title mb-1">Information importante</h6>
                                        <p class="notice-text mb-0">
                                            L'agent sera automatiquement associé à cette compagnie. 
                                            Un email d'activation lui sera envoyé pour définir son mot de passe.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="form-actions-modern">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center">
                                <a href="{{ route('compagnie.agents.index') }}" class="btn-back-modern mb-3 mb-sm-0">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Retour aux agents
                                </a>
                                <div class="d-flex flex-wrap gap-3">
                                    <button type="reset" class="btn-reset-modern">
                                        <i class="fas fa-redo me-2"></i>
                                        Réinitialiser
                                    </button>
                                    <button type="submit" class="btn-submit-modern">
                                        <span class="submit-text">
                                            <i class="fas fa-user-plus me-2"></i>
                                            Créer l'agent
                                        </span>
                                        <span class="submit-loader">
                                            <i class="fas fa-spinner fa-spin me-2"></i>
                                            Création...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inclure SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Variables de design */
    :root {
        --primary-gradient: linear-gradient(135deg, #e94f1b 0%, #e94f1b 100%);
        --primary-hover: linear-gradient(135deg, #e94f1b 0%, #e94f1b 100%);
        --secondary-color: #0a8c5f;
        --secondary-light: rgba(10, 140, 95, 0.1);
        --success-color: #28a745;
        --danger-color: #dc3545;
        --warning-color: #e94f1b;
        --light-bg: #f8f9fa;
        --border-color: #e9ecef;
        --border-hover: #dee2e6;
        --text-primary: #2c3e50;
        --text-secondary: #6c757d;
        --text-light: #adb5bd;
        --shadow-sm: 0 2px 8px rgba(0,0,0,0.04);
        --shadow-md: 0 4px 20px rgba(0,0,0,0.08);
        --shadow-lg: 0 8px 30px rgba(0,0,0,0.12);
        --radius-sm: 8px;
        --radius-md: 12px;
        --radius-lg: 16px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* En-tête de page moderne */
    .page-header-modern {
        background: white;
        border-radius: var(--radius-md);
        margin-bottom: 2rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
    }

    .header-icon-wrapper {
        width: 56px;
        height: 56px;
        background: var(--primary-gradient);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        box-shadow: 0 4px 15px rgba(254, 162, 25, 0.25);
    }

    .page-title {
        color: var(--text-primary);
        font-weight: 700;
        font-size: 1.75rem;
    }

    .page-subtitle {
        color: var(--text-secondary);
        font-size: 0.95rem;
    }

    /* Breadcrumb moderne */
    .modern-breadcrumb .breadcrumb {
        background: var(--light-bg);
        border-radius: 20px;
        padding: 0.75rem 1.25rem;
        margin: 0;
    }

    .breadcrumb-link {
        color: var(--text-secondary);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9rem;
        transition: var(--transition);
        display: flex;
        align-items: center;
    }

    .breadcrumb-link:hover {
        color: var(--primary-color);
    }

    .breadcrumb-item.active .breadcrumb-link {
        color: var(--primary-color);
        font-weight: 600;
    }

    /* Carte moderne */
    .modern-card {
        background: white;
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
    }

    .card-header-modern {
        background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
        padding: 2rem 2rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }

    .card-title {
        color: var(--text-primary);
        font-weight: 700;
        font-size: 1.5rem;
    }

    .card-subtitle {
        color: var(--text-secondary);
        font-size: 0.95rem;
    }

    /* Indicateur d'étapes */
    .step-indicator {
        display: flex;
        align-items: center;
        padding: 0.5rem 1rem;
        background: var(--light-bg);
        border-radius: 30px;
    }

    .step-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: white;
        border: 2px solid var(--border-color);
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .step-active {
        background: var(--primary-gradient);
        border-color: transparent;
        color: white;
        box-shadow: 0 4px 8px rgba(254, 162, 25, 0.3);
    }

    .step-line {
        width: 40px;
        height: 2px;
        background: var(--border-color);
        margin: 0 0.5rem;
    }

    /* Corps de carte */
    .card-body-modern {
        padding: 2.5rem;
    }

    @media (max-width: 768px) {
        .card-body-modern {
            padding: 1.5rem;
        }
    }

    /* Section de formulaire moderne */
    .form-section-modern {
        background: white;
        border-radius: var(--radius-md);
        padding: 2rem;
        border: 1px solid var(--border-color);
        transition: var(--transition);
    }

    .form-section-modern:hover {
        border-color: var(--border-hover);
        box-shadow: var(--shadow-sm);
    }

    .section-header-modern {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid var(--light-bg);
    }

    .section-icon {
        width: 48px;
        height: 48px;
        background: var(--secondary-light);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--secondary-color);
        font-size: 1.25rem;
    }

    .section-title {
        color: var(--text-primary);
        font-weight: 600;
        font-size: 1.25rem;
        margin: 0;
    }

    .section-subtitle {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin: 0;
    }

    .section-badge {
        margin-left: auto;
        padding: 0.25rem 0.75rem;
        background: var(--secondary-light);
        color: var(--secondary-color);
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .section-badge.optional {
        background: rgba(108, 117, 125, 0.1);
        color: var(--text-secondary);
    }

    /* Groupes de formulaires modernes */
    .form-group-modern {
        margin-bottom: 1.5rem;
    }

    .form-label-modern {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.95rem;
    }

    .label-text {
        margin-right: 0.25rem;
    }

    .label-required {
        color: var(--danger-color);
        font-size: 1.2rem;
        line-height: 1;
    }

    .input-group-modern {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        z-index: 2;
    }

    .form-control-modern {
        width: 100%;
        padding: 0.875rem 1rem 0.875rem 3rem;
        border: 2px solid var(--border-color);
        border-radius: var(--radius-sm);
        background: white;
        color: var(--text-primary);
        font-size: 0.95rem;
        transition: var(--transition);
        line-height: 1.5;
    }

    .form-control-modern:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(254, 162, 25, 0.15);
    }

    .form-control-modern.is-invalid {
        border-color: var(--danger-color);
    }

    .form-control-modern.is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15);
    }

    .input-hint {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-light);
        font-size: 0.8rem;
        opacity: 0;
        transition: var(--transition);
        pointer-events: none;
    }

    .input-group-modern:hover .input-hint {
        opacity: 1;
    }

    .invalid-feedback-modern {
        display: flex;
        align-items: center;
        color: var(--danger-color);
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    /* Zone de téléchargement moderne */
    .file-upload-modern {
        position: relative;
    }

    .upload-area-modern {
        border: 3px dashed var(--border-color);
        border-radius: var(--radius-md);
        padding: 3rem 2rem;
        text-align: center;
        background: var(--light-bg);
        cursor: pointer;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .upload-area-modern:hover {
        border-color: var(--primary-color);
        background: rgba(254, 162, 25, 0.02);
    }

    .upload-area-modern.upload-error {
        border-color: var(--danger-color);
        background: rgba(220, 53, 69, 0.02);
    }

    .upload-icon {
        font-size: 3rem;
        color: var(--text-light);
        margin-bottom: 1.5rem;
        transition: var(--transition);
    }

    .upload-area-modern:hover .upload-icon {
        color: var(--primary-color);
        transform: translateY(-5px);
    }

    .upload-title {
        color: var(--text-primary);
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 1.25rem;
    }

    .upload-subtitle {
        color: var(--text-secondary);
        margin: 0.5rem 0;
        font-size: 0.95rem;
    }

    .btn-upload-modern {
        background: white;
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-upload-modern:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(254, 162, 25, 0.3);
    }

    .upload-info {
        color: var(--text-secondary);
        font-size: 0.85rem;
        margin-top: 1rem;
    }

    /* Aperçu d'image moderne */
    .image-preview-modern {
        background: white;
        border-radius: var(--radius-md);
        overflow: hidden;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
    }

    .preview-header {
        background: var(--light-bg);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--border-color);
    }

    .preview-title {
        color: var(--text-primary);
        font-weight: 600;
        font-size: 1rem;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .btn-remove-modern {
        background: var(--danger-color);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: var(--radius-sm);
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
    }

    .btn-remove-modern:hover {
        background: #bb2d3b;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }

    .preview-body {
        padding: 2rem;
        text-align: center;
    }

    .preview-image {
        max-width: 200px;
        max-height: 200px;
        object-fit: cover;
        border-radius: var(--radius-sm);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    /* Lignes directrices */
    .upload-guidelines {
        background: var(--secondary-light);
        border-radius: var(--radius-md);
        padding: 1.5rem;
        height: 100%;
    }

    .guidelines-title {
        color: var(--secondary-color);
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }

    .guidelines-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .guidelines-list li {
        padding: 0.5rem 0;
        color: var(--text-secondary);
        font-size: 0.9rem;
        display: flex;
        align-items: center;
    }

    /* Informations sur l'entreprise */
    .company-info-modern {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: var(--radius-md);
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    .company-header {
        background: var(--secondary-color);
        color: white;
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .company-title {
        font-weight: 600;
        font-size: 1.1rem;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .company-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .company-body {
        padding: 1.5rem;
    }

    .info-item-modern {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .info-icon {
        width: 40px;
        height: 40px;
        background: var(--secondary-light);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--secondary-color);
    }

    .info-label {
        display: block;
        color: var(--text-secondary);
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    .info-value {
        color: var(--text-primary);
        font-weight: 600;
        font-size: 1rem;
        margin: 0;
    }

    .company-notice {
        background: rgba(13, 110, 253, 0.05);
        border: 1px solid rgba(13, 110, 253, 0.1);
        border-radius: var(--radius-sm);
        padding: 1rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }

    .notice-icon {
        color: #0d6efd;
        font-size: 1.25rem;
        margin-top: 0.25rem;
    }

    .notice-title {
        color: var(--text-primary);
        font-weight: 600;
        font-size: 0.95rem;
        margin: 0;
    }

    .notice-text {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin: 0;
        line-height: 1.5;
    }

    /* Actions de formulaire */
    .form-actions-modern {
        padding-top: 2rem;
        margin-top: 2rem;
        border-top: 1px solid var(--border-color);
    }

    .btn-back-modern {
        display: inline-flex;
        align-items: center;
        padding: 0.75rem 1.5rem;
        background: var(--light-bg);
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
        text-decoration: none;
        border-radius: var(--radius-sm);
        font-weight: 500;
        font-size: 0.95rem;
        transition: var(--transition);
    }

    .btn-back-modern:hover {
        background: white;
        border-color: var(--primary-color);
        color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: var(--shadow-sm);
    }

    .btn-reset-modern {
        display: inline-flex;
        align-items: center;
        padding: 0.875rem 1.75rem;
        background: white;
        border: 2px solid var(--border-color);
        color: var(--text-secondary);
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: var(--transition);
    }

    .btn-reset-modern:hover {
        border-color: var(--danger-color);
        color: var(--danger-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.15);
    }

    .btn-submit-modern {
        display: inline-flex;
        align-items: center;
        padding: 0.875rem 2rem;
        background: var(--primary-gradient);
        border: none;
        color: white;
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .btn-submit-modern:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(254, 162, 25, 0.4);
    }

    .btn-submit-modern:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none !important;
        box-shadow: none !important;
    }

    .submit-loader {
        display: none;
    }

    .btn-submit-modern.loading .submit-text {
        display: none;
    }

    .btn-submit-modern.loading .submit-loader {
        display: flex;
        align-items: center;
    }

    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .modern-card {
        animation: fadeIn 0.5s ease-out;
    }

    .form-section-modern {
        animation: fadeIn 0.6s ease-out 0.1s both;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .section-header-modern {
            flex-wrap: wrap;
        }
        
        .section-badge {
            margin-left: 0;
            margin-top: 0.5rem;
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .card-body-modern {
            padding: 1.5rem;
        }
        
        .form-section-modern {
            padding: 1.5rem;
        }
        
        .section-header-modern {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }
        
        .section-icon {
            align-self: flex-start;
        }
        
        .step-indicator {
            display: none;
        }
    }

    @media (max-width: 576px) {
        .page-title {
            font-size: 1.5rem;
        }
        
        .card-body-modern {
            padding: 1rem;
        }
        
        .form-section-modern {
            padding: 1.25rem;
        }
        
        .form-actions-modern {
            flex-direction: column;
            gap: 1rem;
        }
        
        .btn-reset-modern,
        .btn-submit-modern {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser SweetAlert2 avec un thème personnalisé
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // Afficher les messages de session
    @if(session('success'))
        Toast.fire({
            icon: 'success',
            title: '{{ session('success') }}',
            background: 'var(--light-bg)',
            color: 'var(--text-primary)',
            iconColor: 'var(--secondary-color)'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: '{{ session('error') }}',
            confirmButtonColor: '#e94f1b',
            background: 'var(--light-bg)',
            color: 'var(--text-primary)'
        });
    @endif

    // Gestion du téléchargement d'image
    const uploadArea = document.getElementById('uploadArea');
    const profilePictureInput = document.getElementById('profile_picture');
    const imagePreview = document.getElementById('imagePreview');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');

    // Click sur la zone de téléchargement
    uploadArea.addEventListener('click', function() {
        profilePictureInput.click();
    });

    // Glisser-déposer amélioré
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.style.borderColor = '#e94f1b';
        uploadArea.style.background = 'rgba(254, 162, 25, 0.05)';
        uploadArea.style.transform = 'scale(1.02)';
    }

    function unhighlight(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.style.borderColor = '#e9ecef';
        uploadArea.style.background = 'var(--light-bg)';
        uploadArea.style.transform = 'scale(1)';
    }

    // Gérer le drop de fichier
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            profilePictureInput.files = files;
            handleFile(files[0]);
        }
    });

    // Gérer le changement de fichier
    profilePictureInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            handleFile(this.files[0]);
        }
    });

    function handleFile(file) {
        // Validation du type de fichier
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Format invalide',
                text: 'Veuillez sélectionner une image au format JPEG, PNG, JPG ou GIF.',
                confirmButtonColor: '#e94f1b',
                background: 'var(--light-bg)'
            });
            return;
        }

        // Validation de la taille
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Fichier trop volumineux',
                text: 'La taille maximale autorisée est de 2MB.',
                confirmButtonColor: '#e94f1b',
                background: 'var(--light-bg)'
            });
            return;
        }

        // Afficher l'aperçu
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            imagePreviewContainer.style.display = 'block';
            
            // Animation d'apparition
            imagePreviewContainer.style.animation = 'fadeIn 0.5s ease-out';
            
            // Message de succès
            Toast.fire({
                icon: 'success',
                title: 'Image téléchargée avec succès',
                background: 'var(--light-bg)'
            });
        }
        reader.readAsDataURL(file);
    }

    // Supprimer l'image
    window.removeImage = function() {
        Swal.fire({
            title: 'Supprimer l\'image ?',
            text: 'Cette action est irréversible.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e94f1b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            background: 'var(--light-bg)'
        }).then((result) => {
            if (result.isConfirmed) {
                profilePictureInput.value = '';
                imagePreviewContainer.style.display = 'none';
                
                Toast.fire({
                    icon: 'success',
                    title: 'Image supprimée',
                    background: 'var(--light-bg)'
                });
            }
        });
    }

    // Validation du formulaire
    const form = document.getElementById('agentForm');
    const submitBtn = form.querySelector('.btn-submit-modern');
    
    // Validation en temps réel
    const inputs = form.querySelectorAll('input[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });

    function validateField(field) {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            return false;
        } else {
            field.classList.remove('is-invalid');
            return true;
        }
    }

    // Soumission du formulaire
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validation
        let isValid = true;
        const requiredFields = form.querySelectorAll('input[required]');
        requiredFields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
                if (!form.querySelector('.is-invalid:first-of-type')) {
                    field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    field.focus();
                }
            }
        });

        if (!isValid) {
            Swal.fire({
                icon: 'warning',
                title: 'Champs manquants',
                html: 'Veuillez remplir tous les champs obligatoires.<br>Les champs marqués d\'un <span style="color: var(--danger-color)">*</span> sont requis.',
                confirmButtonColor: '#e94f1b',
                background: 'var(--light-bg)'
            });
            return;
        }

        // Afficher l'indicateur de chargement
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;

        // Soumettre le formulaire après un court délai pour l'animation
        setTimeout(() => {
            form.submit();
        }, 500);
    });

    // Réinitialisation du formulaire
    const resetBtn = form.querySelector('.btn-reset-modern');
    resetBtn.addEventListener('click', function() {
        Swal.fire({
            title: 'Réinitialiser le formulaire ?',
            text: 'Toutes les données saisies seront perdues.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#e94f1b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, réinitialiser',
            cancelButtonText: 'Annuler',
            background: 'var(--light-bg)'
        }).then((result) => {
            if (result.isConfirmed) {
                form.reset();
                imagePreviewContainer.style.display = 'none';
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
                
                // Supprimer les messages d'erreur
                form.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });
                
                Toast.fire({
                    icon: 'success',
                    title: 'Formulaire réinitialisé',
                    background: 'var(--light-bg)'
                });
            }
        });
    });

    // Afficher les info-bulles
    const hints = form.querySelectorAll('.input-hint');
    hints.forEach(hint => {
        const input = hint.closest('.input-group-modern').querySelector('input');
        
        input.addEventListener('focus', function() {
            hint.style.opacity = '1';
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                hint.style.opacity = '0';
            }
        });
    });
});
</script>
@endsection