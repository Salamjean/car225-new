@extends('compagnie.layouts.template')

@section('page-title', 'Nouvelle Gare')
@section('page-subtitle', 'Configurez un nouveau point d\'embarquement stratégique pour votre réseau')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    .form-container { width: 75%; margin: 0 auto; }
    
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
        background: var(--orange); color: white; display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 13px; box-shadow: 0 4px 10px rgba(249,115,22,0.3);
    }
    
    .input-group-modern { position: relative; margin-bottom: 20px; }
    .input-group-modern .form-label {
        display: block; font-size: 10px; font-weight: 700; color: var(--text-3);
        text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;
    }
    .input-group-modern .input-icon {
        position: absolute; left: 16px; top: 35px; color: var(--text-3); font-size: 14px; transition: 0.2s; z-index: 5;
    }
    .input-group-modern .input-modern {
        width: 100%; padding: 12px 16px 12px 42px;
        border: 1px solid var(--border-strong); border-radius: var(--radius-sm);
        background: var(--surface-2); color: var(--text-1); font-size: 13px; font-weight: 600; transition: 0.2s;
    }
    .input-group-modern .input-modern:focus {
        outline: none; border-color: var(--orange); background: var(--surface); box-shadow: 0 0 0 3px var(--orange-light);
    }
    .input-group-modern .input-modern:focus + .input-icon, .input-group-modern:focus-within .input-icon { color: var(--orange); }
    
    .phone-group { display: flex; gap: 8px; }
    .phone-prefix { width: 90px; padding: 12px; border: 1px solid var(--border-strong); border-radius: var(--radius-sm); background: var(--surface-2); font-size: 12px; font-weight: 700; color: var(--text-2); text-align: center; }
    
    /* TomSelect overrides */
    .ts-wrapper .ts-control { border: 1px solid var(--border-strong) !important; border-radius: var(--radius-sm) !important; padding: 12px 16px 12px 42px !important; background: var(--surface-2) !important; font-size: 13px !important; font-weight: 600 !important; color: var(--text-1) !important; box-shadow: none !important; }
    .ts-wrapper.focus .ts-control { border-color: var(--orange) !important; background: var(--surface) !important; box-shadow: 0 0 0 3px var(--orange-light) !important; }
    .ts-dropdown { border-radius: var(--radius-sm) !important; font-size: 13px !important; }
    
    /* Upload Image */
    .profile-upload-wrap { position: relative; width: 140px; height: 140px; margin: 0 auto; }
    .profile-img-box { width: 100%; height: 100%; border-radius: 30px; background: var(--surface-2); border: 2px dashed var(--border-strong); display: flex; align-items: center; justify-content: center; overflow: hidden; transition: 0.2s; }
    .profile-img-box img { width: 100%; height: 100%; object-fit: cover; }
    .profile-icon-placeholder { font-size: 40px; color: var(--text-3); }
    .profile-upload-btn { position: absolute; bottom: -5px; right: -5px; width: 40px; height: 40px; border-radius: 12px; background: var(--orange); color: white; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 3px solid var(--surface); transition: 0.2s; }
    .profile-upload-btn:hover { transform: scale(1.1); }

    .btn-submit { background: linear-gradient(135deg, var(--orange) 0%, var(--orange-dark) 100%); color: white; padding: 14px 28px; border-radius: var(--radius-sm); font-weight: 700; font-size: 13px; text-transform: uppercase; border: none; cursor: pointer; box-shadow: 0 4px 15px rgba(249,115,22,0.3); transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(249,115,22,0.4); color: white; }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <div class="form-container">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <a href="{{ route('gare.index') }}" class="btn btn-light btn-sm" style="font-weight: 700; font-size: 12px; border-radius: var(--radius-sm);">
                <i class="fas fa-arrow-left mr-2"></i> Retour au réseau
            </a>
        </div>

        <form action="{{ route('gare.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-card">
                <div class="form-card-header">
                    <div class="form-step-badge">01</div>
                    <h3 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin: 0; text-transform: uppercase;">Informations Structurelles</h3>
                </div>
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group-modern">
                                <label class="form-label">Nom de la Gare <span class="text-danger">*</span></label>
                                <i class="fas fa-building input-icon"></i>
                                <input type="text" name="nom_gare" value="{{ old('nom_gare') }}" required class="input-modern" placeholder="Ex: Gare de Bassam Central">
                                @error('nom_gare') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group-modern">
                                <label class="form-label">Ville / Localité <span class="text-danger">*</span></label>
                                <i class="fas fa-city input-icon"></i>
                                <select name="ville" id="select-ville" required class="input-modern">
                                    <option value="">Sélectionnez une ville</option>
                                    @php
                                        $villes = ['Abidjan','Alepé', 'Abengourou', 'Adzopé', 'Agboville', 'Anyama', 'Bondoukou', 'Bongouanou', 'Bouaflé', 'Bouaké', 'Boundiali', 'Bouna', 'Dabou', 'Daloa', 'Divo', 'Duékoué', 'Ferkessédougou', 'Gagnoa', 'Grand-Bassam', 'Guiglo', 'Issia', 'Katiola', 'Korhogo', 'Man', 'Odienné', 'Oumé', 'San-Pédro', 'Séguéla', 'Sinfra', 'Soubré', 'Tanda', 'Touba', 'Toumodi', 'Vavoua', 'Yamoussoukro', 'Zénoula'];
                                        sort($villes);
                                    @endphp
                                    @foreach($villes as $ville)
                                        <option value="{{ $ville }}" {{ old('ville') == $ville ? 'selected' : '' }}>{{ $ville }}</option>
                                    @endforeach
                                </select>
                                @error('ville') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="input-group-modern mb-0">
                                <label class="form-label text-orange font-bold mb-2 block">LOCALISATION GPS (FACULTATIF MAIS RECOMMANDÉ POUR LE SUIVI)</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group-modern">
                                            <i class="fas fa-map-pin input-icon"></i>
                                            <input type="number" step="0.00000001" name="latitude" value="{{ old('latitude') }}" class="input-modern" placeholder="Latitude (ex: 5.3484)">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group-modern">
                                            <i class="fas fa-map-pin input-icon"></i>
                                            <input type="number" step="0.00000001" name="longitude" value="{{ old('longitude') }}" class="input-modern" placeholder="Longitude (ex: -4.0305)">
                                        </div>
                                    </div>
                                </div>
                                <p style="font-size: 10px; color: var(--text-3); margin-top: -10px; font-style: italic;">
                                    <i class="fas fa-info-circle"></i> Ces coordonnées permettent de calculer l'arrivée estimée en temps réel pour vos chauffeurs.
                                </p>
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <div class="input-group-modern mb-0">
                                <label class="form-label">Adresse Complète / Point de Repère <span class="text-danger">*</span></label>
                                <i class="fas fa-map-marker-alt input-icon"></i>
                                <input type="text" name="adresse" value="{{ old('adresse') }}" required class="input-modern" placeholder="Ex: Boulevard Lagunaire, face au grand marché">
                                @error('adresse') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="form-card h-100 mb-4 mb-lg-0">
                        <div class="form-card-header">
                            <div class="form-step-badge" style="background: var(--blue); box-shadow: 0 4px 10px rgba(59,130,246,0.3);">02</div>
                            <h3 style="font-size: 14px; font-weight: 800; color: var(--text-1); margin: 0; text-transform: uppercase;">Responsable de Site</h3>
                        </div>
                        <div class="p-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group-modern">
                                        <label class="form-label">Nom <span class="text-danger">*</span></label>
                                        <i class="fas fa-user-tie input-icon"></i>
                                        <input type="text" name="responsable_nom" value="{{ old('responsable_nom') }}" required class="input-modern" style="padding-left: 42px;">
                                        @error('responsable_nom') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group-modern">
                                        <label class="form-label">Prénom <span class="text-danger">*</span></label>
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" name="responsable_prenom" value="{{ old('responsable_prenom') }}" required class="input-modern" style="padding-left: 42px;">
                                        @error('responsable_prenom') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group-modern mb-md-0">
                                        <label class="form-label">Email Professionnel <span class="text-danger">*</span></label>
                                        <i class="fas fa-envelope input-icon"></i>
                                        <input type="email" name="email" value="{{ old('email') }}" required class="input-modern" placeholder="responsable@gare.com">
                                        @error('email') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group-modern mb-0">
                                        <label class="form-label">Contact Direct <span class="text-danger">*</span></label>
                                        <div class="phone-group">
                                            <select name="country_code" class="phone-prefix">
                                                <option value="+225" selected>🇨🇮 +225</option>
                                                <option value="+33">🇫🇷 +33</option>
                                            </select>
                                            <input type="text" name="contact" value="{{ old('contact') }}" required class="input-modern" placeholder="07 00 00 00 00" style="padding-left: 16px;">
                                        </div>
                                        @error('contact') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="form-card h-100 text-center p-4 d-flex flex-column align-items-center justify-content-center">
                        <h4 style="font-size: 10px; font-weight: 800; color: var(--text-3); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px;">Photo du Responsable</h4>
                        <div class="profile-upload-wrap">
                            <div class="profile-img-box" id="image-preview-box">
                                <div id="placeholder-icon" class="profile-icon-placeholder"><i class="fas fa-camera"></i></div>
                                <img id="preview" src="#" class="d-none">
                            </div>
                            <input type="file" name="profile_image" id="profile_image" class="d-none" accept="image/*">
                            <label for="profile_image" class="profile-upload-btn"><i class="fas fa-plus"></i></label>
                        </div>
                        <p style="font-size: 10px; color: var(--text-3); margin-top: 10px; font-style: italic;">Formats: JPG, PNG (Max 2Mo)</p>
                        @error('profile_image') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="form-card mt-4">
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group-modern mb-md-0">
                                <label class="form-label">Commune Spécifique</label>
                                <i class="fas fa-map-pin input-icon"></i>
                                <input type="text" name="commune" value="{{ old('commune') }}" class="input-modern" placeholder="Ex: Treichville Arras">
                                @error('commune') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group-modern mb-0">
                                <label class="form-label">Contact de Secours</label>
                                <div class="phone-group">
                                    <select name="country_code_urgence" class="phone-prefix">
                                        <option value="+225" selected>🇨🇮 +225</option>
                                    </select>
                                    <input type="text" name="contact_urgence" value="{{ old('contact_urgence') }}" class="input-modern" placeholder="Optionnel" style="padding-left: 16px;">
                                </div>
                                @error('contact_urgence') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4 pt-3" style="border-top: 1px solid var(--border);">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-check-circle"></i> Valider & Créer la Gare
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new TomSelect("#select-ville", {
        create: false, sortField: { field: "text", direction: "asc" }, placeholder: "Sélectionner la ville principale..."
    });

    const profileImageInput = document.getElementById('profile_image');
    const imagePreview = document.getElementById('preview');
    const placeholderIcon = document.getElementById('placeholder-icon');

    profileImageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.classList.remove('d-none');
                placeholderIcon.classList.add('d-none');
            }
            reader.readAsDataURL(file);
        }
    });

    @if(session('success'))
        Swal.fire({
            icon: 'success', title: 'GÉO-BASE MISE À JOUR', text: "{{ session('success') }}",
            timer: 3000, showConfirmButton: false, toast: true, position: 'top-end',
            customClass: { popup: 'rounded-lg shadow-sm border-left-success' }
        });
    @endif
});
</script>
@endsection