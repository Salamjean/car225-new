@extends('compagnie.layouts.template')

@section('page-title', 'Mise à jour Profil')
@section('page-subtitle', 'Modification des accès et informations de ' . $caisse->prenom . ' ' . $caisse->name)

@section('styles')
<style>
    /* Réutilisation de nos classes standards pour l'édition */
    .form-card {
        background: var(--surface); border-radius: var(--radius);
        border: 1px solid var(--border); box-shadow: var(--shadow-sm);
        margin-bottom: 24px; overflow: hidden;
    }
    .form-card-header {
        padding: 16px 24px; background: var(--surface-2);
        border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px;
    }
    .form-step-badge {
        width: 32px; height: 32px; border-radius: 10px;
        background: var(--text-1); color: white; display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 13px;
    }
    
    .profile-upload-wrap { position: relative; width: 180px; height: 180px; margin: 0 auto 24px; }
    .profile-img-box {
        width: 100%; height: 100%; border-radius: 30px;
        background: var(--surface-2); border: 2px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        overflow: hidden;
    }
    .profile-img-box img { width: 100%; height: 100%; object-fit: cover; }
    .profile-icon-placeholder { font-size: 50px; color: var(--text-3); }
    
    .profile-upload-btn {
        position: absolute; bottom: -5px; right: -5px;
        width: 48px; height: 48px; border-radius: 14px;
        background: var(--text-1); color: white;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; box-shadow: var(--shadow-md); border: 4px solid var(--surface);
        transition: 0.2s;
    }
    .profile-upload-btn:hover { background: var(--orange); }

    .input-group-modern { position: relative; margin-bottom: 20px; }
    .input-group-modern .form-label {
        display: block; font-size: 10px; font-weight: 700; color: var(--text-3);
        text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;
    }
    .input-group-modern .input-icon {
        position: absolute; left: 16px; top: 35px; color: var(--text-3); font-size: 14px;
    }
    .input-group-modern .input-modern {
        width: 100%; padding: 12px 16px 12px 42px;
        border: 1px solid var(--border-strong); border-radius: var(--radius-sm);
        background: var(--surface-2); color: var(--text-1);
        font-size: 13px; font-weight: 600; transition: 0.2s;
    }
    .input-group-modern .input-modern:focus {
        outline: none; border-color: var(--orange); background: var(--surface);
        box-shadow: 0 0 0 3px var(--orange-light);
    }
    .input-group-modern .input-modern:focus + .input-icon,
    .input-group-modern:focus-within .input-icon { color: var(--orange); }
    
    select.input-modern { appearance: none; }
    .select-chevron { position: absolute; right: 16px; top: 35px; color: var(--text-3); font-size: 12px; pointer-events: none; }

    .btn-submit {
        background: var(--text-1); color: white; padding: 14px 28px;
        border-radius: var(--radius-sm); font-weight: 700; font-size: 13px;
        text-transform: uppercase; letter-spacing: 0.5px; border: none; cursor: pointer;
        box-shadow: var(--shadow-md); transition: 0.2s;
    }
    .btn-submit:hover { background: var(--orange); transform: translateY(-2px); color: white; }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <div style="max-width: 1100px; margin: 0 auto;">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <a href="{{ route('compagnie.caisses.index') }}" class="btn btn-light btn-sm" style="font-weight: 700; font-size: 12px; border-radius: var(--radius-sm);">
                <i class="fas fa-arrow-left mr-2"></i> Annuler & Retour
            </a>
        </div>

        <form action="{{ route('compagnie.caisses.update', $caisse->id) }}" method="POST" enctype="multipart/form-data" id="editCaisseForm">
            @csrf
            @method('PUT')

            <div class="row">
                {{-- COLONNE GAUCHE : PROFIL --}}
                <div class="col-lg-4 mb-4">
                    <div class="form-card text-center p-4">
                        <h4 style="font-size: 11px; font-weight: 800; color: var(--text-3); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px;">Photo actuelle</h4>
                        
                        <div class="profile-upload-wrap">
                            <div class="profile-img-box" id="imagePreviewContainer">
                                @if($caisse->profile_picture)
                                    <img id="imagePreview" src="{{ asset('storage/' . $caisse->profile_picture) }}" alt="Profil">
                                @else
                                    <div id="avatarPlaceholder" class="profile-icon-placeholder">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <img id="imagePreview" src="#" alt="Aperçu" class="d-none">
                                @endif
                            </div>
                            
                            <label for="profile_picture" class="profile-upload-btn">
                                <i class="fas fa-camera"></i>
                                <input type="file" id="profile_picture" name="profile_picture" class="d-none" accept="image/*">
                            </label>
                        </div>

                        <div style="background: var(--surface-2); border: 1px solid var(--border); border-radius: 12px; padding: 16px; text-align: left; margin-top: 20px;">
                            <h5 style="font-size: 11px; font-weight: 800; color: var(--text-1); text-transform: uppercase; margin-bottom: 12px;">Statut du Compte</h5>
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $caisse->isArchived() ? 'var(--red)' : 'var(--emerald)' }};"></span>
                                <span style="font-size: 11px; font-weight: 800; color: var(--text-2); text-transform: uppercase;">
                                    {{ $caisse->isArchived() ? 'Agent Archivé' : 'Agent Actif' }}
                                </span>
                            </div>
                            <p style="font-size: 10px; color: var(--text-3); margin: 0; font-style: italic;">
                                Modifié le : {{ $caisse->updated_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        @error('profile_picture') <span class="text-danger" style="font-size: 11px; font-weight: 700; margin-top: 10px; display: block;">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- COLONNE DROITE : FORMULAIRE --}}
                <div class="col-lg-8">

                    {{-- 01. Affectation (si gares dispo) --}}
                    @if(isset($gares) && $gares->count() > 0)
                    <div class="form-card">
                        <div class="form-card-header">
                            <div class="form-step-badge">01</div>
                            <h3 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">Affectation Poste</h3>
                        </div>
                        <div class="p-4">
                            <div class="input-group-modern mb-0">
                                <label class="form-label">Gare de Rattachement</label>
                                <i class="fas fa-building input-icon"></i>
                                <select name="gare_id" required class="input-modern">
                                    <option value="" disabled>-- Sélectionner --</option>
                                    @foreach($gares as $gare)
                                        <option value="{{ $gare->id }}" {{ old('gare_id', $caisse->gare_id) == $gare->id ? 'selected' : '' }}>
                                            {{ $gare->nom_gare }} — {{ $gare->ville }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down select-chevron"></i>
                                @error('gare_id') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- 02. Identité --}}
                    <div class="form-card">
                        <div class="form-card-header">
                            <div class="form-step-badge" style="background: var(--blue);">02</div>
                            <h3 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">Identité & Coordonnées</h3>
                        </div>
                        <div class="p-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group-modern">
                                        <label class="form-label">Nom de Famille</label>
                                        <i class="fas fa-id-card input-icon"></i>
                                        <input type="text" name="name" value="{{ old('name', $caisse->name) }}" required class="input-modern">
                                        @error('name') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group-modern">
                                        <label class="form-label">Prénom(s)</label>
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" name="prenom" value="{{ old('prenom', $caisse->prenom) }}" required class="input-modern">
                                        @error('prenom') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group-modern">
                                        <label class="form-label">Adresse Email Pro</label>
                                        <i class="fas fa-envelope input-icon"></i>
                                        <input type="email" name="email" value="{{ old('email', $caisse->email) }}" required class="input-modern">
                                        @error('email') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group-modern">
                                        <label class="form-label">Contact Mobile</label>
                                        <i class="fas fa-mobile-alt input-icon"></i>
                                        <input type="text" name="contact" value="{{ old('contact', $caisse->contact) }}" required maxlength="10" class="input-modern">
                                        @error('contact') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="input-group-modern mb-0">
                                        <label class="form-label">Commune de Résidence</label>
                                        <i class="fas fa-map-marker-alt input-icon"></i>
                                        <input type="text" name="commune" value="{{ old('commune', $caisse->commune) }}" class="input-modern" placeholder="Ex: Cocody, Angré">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 03. Sécurité --}}
                    <div class="form-card">
                        <div class="form-card-header">
                            <div class="form-step-badge" style="background: var(--orange);">03</div>
                            <h3 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">Contact d'Urgence</h3>
                        </div>
                        <div class="p-4">
                            <div class="input-group-modern mb-0">
                                <label class="form-label">Numéro Téléphonique de Sécurité</label>
                                <i class="fas fa-ambulance input-icon"></i>
                                <input type="text" name="cas_urgence" value="{{ old('cas_urgence', $caisse->cas_urgence) }}" class="input-modern" placeholder="01 00 00 00 00">
                                @error('cas_urgence') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex align-items-center justify-content-between mt-4 pt-3" style="border-top: 1px solid var(--border);">
                        <div>
                            <p style="font-size: 11px; font-weight: 600; color: var(--text-3); margin: 0; font-style: italic; max-width: 300px;">
                                "Toute modification des informations sera immédiatement répercutée sur les accès de l'agent."
                            </p>
                        </div>
                        <div>
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-save mr-2" style="color: var(--orange);"></i> Enregistrer les Modifications
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
                    title: 'Fichier volumineux',
                    text: 'La taille maximale autorisée pour les photos est de 2 Mo.',
                    confirmButtonColor: '#F97316',
                    customClass: { popup: 'rounded-lg border-0 shadow-sm' }
                });
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                if (imagePreview.classList.contains('d-none')) {
                    imagePreview.classList.remove('d-none');
                    if (placeholder) placeholder.classList.add('d-none');
                }
                imagePreview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Mise à jour réussie',
            text: "{{ session('success') }}",
            confirmButtonColor: '#F97316',
            customClass: { popup: 'rounded-lg shadow-sm' }
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Erreur de modification',
            text: "Veuillez vérifier les champs marqués en rouge.",
            confirmButtonColor: '#000',
            customClass: { popup: 'rounded-lg shadow-sm' }
        });
    @endif
});
</script>
@endsection