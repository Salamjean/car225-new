@extends('compagnie.layouts.template')

@section('page-title', 'Modifier la Gare')
@section('page-subtitle', 'Mettre à jour les informations de ' . $gare->nom_gare)

@section('styles')
<style>
    .form-container { width: 75%; margin: 0 auto; }
    .form-card { background: var(--surface); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); margin-bottom: 24px; padding: 24px; }
    
    .input-group-modern { position: relative; margin-bottom: 20px; }
    .input-group-modern .form-label { display: block; font-size: 10px; font-weight: 700; color: var(--text-3); text-transform: uppercase; margin-bottom: 6px; }
    .input-group-modern .input-modern { width: 100%; padding: 12px 16px; border: 1px solid var(--border-strong); border-radius: var(--radius-sm); background: var(--surface-2); color: var(--text-1); font-size: 13px; font-weight: 600; transition: 0.2s; }
    .input-group-modern .input-modern:focus { outline: none; border-color: var(--orange); background: var(--surface); box-shadow: 0 0 0 3px var(--orange-light); }
    
    .btn-submit { background: linear-gradient(135deg, var(--orange) 0%, var(--orange-dark) 100%); color: white; padding: 12px 24px; border-radius: var(--radius-sm); font-weight: 700; font-size: 13px; text-transform: uppercase; border: none; cursor: pointer; box-shadow: 0 4px 15px rgba(249,115,22,0.3); transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(249,115,22,0.4); color: white; text-decoration: none;}
    
    .current-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border); margin-top: 8px; display: block; }
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

        <form action="{{ route('gare.update', $gare->id) }}" method="POST" enctype="multipart/form-data" class="form-card">
            @csrf
            @method('PUT')
            
            <h4 style="font-size: 14px; font-weight: 800; color: var(--text-1); text-transform: uppercase; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid var(--border);">Informations de la Gare</h4>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group-modern">
                        <label class="form-label">Nom de la gare <span class="text-danger">*</span></label>
                        <input type="text" name="nom_gare" value="{{ old('nom_gare', $gare->nom_gare) }}" required class="input-modern">
                        @error('nom_gare') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group-modern">
                        <label class="form-label">Ville <span class="text-danger">*</span></label>
                        <select name="ville" required class="input-modern" style="appearance: auto;">
                            @php
                                $villes = ['Abidjan', 'Abengourou', 'Adzopé', 'Agboville', 'Anyama', 'Bondoukou', 'Bongouanou', 'Bouaflé', 'Bouaké', 'Boundiali', 'Bouna', 'Dabou', 'Daloa', 'Divo', 'Duékoué', 'Ferkessédougou', 'Gagnoa', 'Grand-Bassam', 'Guiglo', 'Issia', 'Katiola', 'Korhogo', 'Man', 'Odienné', 'Oumé', 'San-Pédro', 'Séguéla', 'Sinfra', 'Soubré', 'Tanda', 'Touba', 'Toumodi', 'Vavoua', 'Yamoussoukro', 'Zénoula'];
                                sort($villes);
                            @endphp
                            @foreach($villes as $ville)
                                <option value="{{ $ville }}" {{ old('ville', $gare->ville) == $ville ? 'selected' : '' }}>{{ $ville }}</option>
                            @endforeach
                        </select>
                        @error('ville') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group-modern">
                        <label class="form-label">Adresse / Situation Géographique <span class="text-danger">*</span></label>
                        <input type="text" name="adresse" value="{{ old('adresse', $gare->adresse) }}" required class="input-modern">
                        @error('adresse') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group-modern">
                        <label class="form-label">Commune</label>
                        <input type="text" name="commune" value="{{ old('commune', $gare->commune) }}" class="input-modern">
                        @error('commune') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <h4 style="font-size: 14px; font-weight: 800; color: var(--text-1); text-transform: uppercase; margin-top: 10px; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid var(--border);">Responsable</h4>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group-modern">
                        <label class="form-label">Nom du Responsable <span class="text-danger">*</span></label>
                        <input type="text" name="responsable_nom" value="{{ old('responsable_nom', $gare->responsable_nom) }}" required class="input-modern">
                        @error('responsable_nom') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group-modern">
                        <label class="form-label">Prénom du Responsable <span class="text-danger">*</span></label>
                        <input type="text" name="responsable_prenom" value="{{ old('responsable_prenom', $gare->responsable_prenom) }}" required class="input-modern">
                        @error('responsable_prenom') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group-modern">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $gare->email) }}" required class="input-modern">
                        <p style="font-size: 10px; color: var(--orange); margin-top: 4px; font-style: italic;"><i class="fas fa-info-circle"></i> La modification de l'email nécessitera une vérification OTP.</p>
                        @error('email') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group-modern">
                        <label class="form-label">Contact principal <span class="text-danger">*</span></label>
                        <input type="text" name="contact" value="{{ old('contact', $gare->contact) }}" required class="input-modern">
                        @error('contact') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group-modern">
                        <label class="form-label">Contact d'Urgence</label>
                        <input type="text" name="contact_urgence" value="{{ old('contact_urgence', $gare->contact_urgence) }}" class="input-modern">
                        @error('contact_urgence') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group-modern">
                        <label class="form-label">Nouvelle photo de profil (Optionnel)</label>
                        <input type="file" name="profile_image" accept="image/*" class="input-modern" style="padding: 9px 16px;">
                        @if($gare->profile_image)
                            <img src="{{ asset('storage/' . $gare->profile_image) }}" alt="Profile" class="current-avatar">
                        @endif
                        @error('profile_image') <span class="text-danger" style="font-size: 10px; font-weight: 700;">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4 pt-3" style="border-top: 1px solid var(--border);">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-check"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection