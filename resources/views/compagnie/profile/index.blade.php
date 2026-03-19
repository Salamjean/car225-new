@extends('compagnie.layouts.template')

@section('page-title', 'Profil Compagnie')
@section('page-subtitle', 'Gérez les informations et la sécurité de votre structure')

@section('styles')
<style>
    .profile-container { max-width: 1200px; margin: 0 auto; }
    
    .dash-card {
        background: var(--surface); border-radius: var(--radius);
        border: 1px solid var(--border); box-shadow: var(--shadow-sm);
        overflow: hidden; margin-bottom: 24px;
    }

    /* Left Column - Avatar & Stats */
    .company-avatar-wrapper { position: relative; width: 140px; height: 140px; margin: 0 auto 20px; cursor: pointer; border-radius: 30px; background: var(--surface-2); border: 4px solid var(--orange); box-shadow: var(--shadow-md); display: flex; align-items: center; justify-content: center; overflow: hidden; transition: 0.3s; }
    .company-avatar-wrapper:hover { transform: scale(1.05); }
    .company-avatar-placeholder { font-size: 48px; font-weight: 900; color: white; background: linear-gradient(135deg, var(--orange) 0%, var(--orange-dark) 100%); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
    .company-avatar-img { width: 100%; height: 100%; object-fit: contain; background: white; }
    .company-avatar-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; opacity: 0; transition: 0.3s; color: white; font-size: 32px; }
    .company-avatar-wrapper:hover .company-avatar-overlay { opacity: 1; }

    .company-name { font-size: 22px; font-weight: 900; color: var(--text-1); margin: 0 0 8px; line-height: 1.2; }
    .company-sigle { display: inline-block; padding: 4px 12px; background: var(--orange-light); border: 1px solid var(--orange-mid); color: var(--orange-dark); font-size: 11px; font-weight: 800; border-radius: 8px; text-transform: uppercase; letter-spacing: 0.5px; }

    .stat-row { display: flex; align-items: center; justify-content: space-between; padding: 16px; background: var(--surface-2); border-radius: 16px; border: 1px solid var(--border); margin-bottom: 12px; }
    .stat-label { display: flex; align-items: center; gap: 12px; font-size: 11px; font-weight: 800; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; }
    .stat-icon.orange { background: var(--orange-light); color: var(--orange); }
    .stat-icon.blue { background: #EFF6FF; color: #2563EB; }
    .stat-value { font-size: 16px; font-weight: 900; color: var(--text-1); }

    .slogan-box { margin-top: 24px; padding: 16px; text-align: center; border-top: 1px solid var(--border); }
    .slogan-text { font-size: 12px; font-style: italic; color: var(--text-2); font-weight: 600; margin: 0; }

    /* Right Column - Tabs & Forms */
    .msg-tabs { display: flex; gap: 8px; margin-bottom: 0; overflow-x: auto; scrollbar-width: none; padding-bottom: 1px; }
    .msg-tab-btn { padding: 14px 24px; font-weight: 800; font-size: 13px; color: var(--text-3); background: transparent; border: 1px solid transparent; border-bottom: none; border-radius: 16px 16px 0 0; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: 0.2s; white-space: nowrap; }
    .msg-tab-btn:hover { color: var(--text-2); background: rgba(255,255,255,0.5); }
    .msg-tab-btn.active { background: var(--surface); color: var(--orange); border-color: var(--border); box-shadow: 0 -4px 15px rgba(0,0,0,0.02); }
    
    .msg-panel { background: var(--surface); border-radius: 0 16px 16px 16px; border: 1px solid var(--border); box-shadow: var(--shadow-sm); padding: 32px; }
    
    .panel-header { display: flex; align-items: center; gap: 16px; margin-bottom: 32px; }
    .panel-marker { width: 6px; height: 32px; border-radius: 4px; }
    .panel-marker.orange { background: var(--orange); box-shadow: 0 0 10px rgba(249,115,22,0.4); }
    .panel-marker.blue { background: #2563EB; box-shadow: 0 0 10px rgba(37,99,235,0.4); }
    .panel-title { font-size: 18px; font-weight: 900; color: var(--text-1); margin: 0 0 4px; line-height: 1.2; }
    .panel-subtitle { font-size: 11px; font-weight: 800; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.5px; margin: 0; }

    /* Inputs Modernes */
    .input-group-modern { margin-bottom: 24px; }
    .form-label { display: block; font-size: 10px; font-weight: 800; color: var(--text-3); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; margin-left: 4px; }
    .input-modern {
        width: 100%; padding: 14px 20px; border: 1px solid var(--border-strong); border-radius: 12px;
        background: var(--surface-2); color: var(--text-1); font-size: 14px; font-weight: 700; transition: 0.2s;
    }
    .input-modern:focus { outline: none; border-color: var(--orange); background: var(--surface); box-shadow: 0 0 0 4px var(--orange-light); }
    .input-modern.focus-blue:focus { border-color: #2563EB; box-shadow: 0 0 0 4px rgba(37,99,235,0.1); }
    .form-error { font-size: 10px; font-weight: 800; color: #DC2626; margin-top: 6px; margin-left: 4px; display: block; text-transform: uppercase; }

    .btn-submit {
        padding: 16px 32px; border-radius: 16px; font-weight: 900; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; border: none; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 10px; position: relative; overflow: hidden; color: white;
    }
    .btn-submit.orange { background: var(--orange); box-shadow: 0 4px 15px rgba(249,115,22,0.3); }
    .btn-submit.orange:hover { background: var(--orange-dark); transform: translateY(-2px); box-shadow: 0 8px 25px rgba(249,115,22,0.4); }
    .btn-submit.blue { background: #2563EB; box-shadow: 0 4px 15px rgba(37,99,235,0.3); }
    .btn-submit.blue:hover { background: #1D4ED8; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(37,99,235,0.4); }
</style>
@endsection

@section('content')
@php
    $isSecurityActive = $errors->hasAny(['current_password', 'password', 'password_confirmation']) || request('tab') == 'security';
@endphp

<div class="dashboard-page">
    <div class="profile-container">
        
        <div class="row">
            {{-- ── COLONNE GAUCHE: LOGO & STATS ── --}}
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="dash-card p-4 text-center">
                    <div class="company-avatar-wrapper" onclick="document.getElementById('logoInput').click()">
                        @if($compagnie->path_logo)
                            <img src="{{ asset('storage/' . $compagnie->path_logo) }}" class="company-avatar-img" id="logoPreview" alt="Logo {{ $compagnie->name }}">
                        @else
                            <div id="logoPlaceholder" class="company-avatar-placeholder">
                                {{ substr($compagnie->name, 0, 2) }}
                            </div>
                            <img src="" id="logoPreview" class="company-avatar-img d-none" alt="Logo">
                        @endif
                        <div class="company-avatar-overlay">
                            <i class="fas fa-camera"></i>
                        </div>
                    </div>
                    
                    <h3 class="company-name">{{ $compagnie->name }}</h3>
                    <div class="company-sigle">{{ $compagnie->sigle ?? 'PAS DE SIGLE' }}</div>
                    
                    <div class="mt-4 text-left">
                        <div class="stat-row">
                            <div class="stat-label">
                                <div class="stat-icon orange"><i class="fas fa-ticket-alt"></i></div>
                                Solde Tickets
                            </div>
                            <span class="stat-value">{{ number_format($compagnie->tickets, 0, ',', ' ') }}</span>
                        </div>
                        
                        <div class="stat-row">
                            <div class="stat-label">
                                <div class="stat-icon blue"><i class="fas fa-id-badge"></i></div>
                                Identifiant
                            </div>
                            <span class="stat-value" style="font-size: 14px;">{{ $compagnie->username }}</span>
                        </div>
                    </div>

                    <div class="slogan-box">
                        <p class="slogan-text">
                            <i class="fas fa-quote-left mr-1 opacity-50"></i>
                            {{ $compagnie->slogan ?? 'Ajoutez un slogan pour votre compagnie.' }}
                            <i class="fas fa-quote-right ml-1 opacity-50"></i>
                        </p>
                    </div>
                </div>
            </div>

            {{-- ── COLONNE DROITE: TABS & FORMS ── --}}
            <div class="col-lg-8">
                
                <div class="msg-tabs">
                    <button type="button" class="msg-tab-btn {{ !$isSecurityActive ? 'active' : '' }}" onclick="switchProfileTab('info', this)">
                        <i class="fas fa-building"></i> Informations Générales
                    </button>
                    <button type="button" class="msg-tab-btn {{ $isSecurityActive ? 'active' : '' }}" onclick="switchProfileTab('security', this)">
                        <i class="fas fa-shield-alt"></i> Sécurité du Compte
                    </button>
                </div>

                {{-- PANEL: INFOS GENERALES --}}
                <div id="panel-info" class="msg-panel" style="display: {{ !$isSecurityActive ? 'block' : 'none' }}; border-top-left-radius: 0;">
                    <div class="panel-header">
                        <div class="panel-marker orange"></div>
                        <div>
                            <h2 class="panel-title">Informations Générales</h2>
                            <p class="panel-subtitle">Identité de votre structure</p>
                        </div>
                    </div>

                    <form action="{{ route('compagnie.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" id="logoInput" name="path_logo" accept="image/*" class="d-none" onchange="previewImage(this)">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group-modern">
                                    <label class="form-label">Nom complet *</label>
                                    <input type="text" name="name" value="{{ old('name', $compagnie->name) }}" required class="input-modern">
                                    @error('name') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group-modern">
                                    <label class="form-label">Email professionnel *</label>
                                    <input type="email" name="email" value="{{ old('email', $compagnie->email) }}" required class="input-modern">
                                    @error('email') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group-modern">
                                    <label class="form-label">Contact / Téléphone *</label>
                                    <input type="text" name="contact" value="{{ old('contact', $compagnie->contact) }}" required class="input-modern">
                                    @error('contact') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group-modern">
                                    <label class="form-label">Sigle (ex: UTB)</label>
                                    <input type="text" name="sigle" value="{{ old('sigle', $compagnie->sigle) }}" class="input-modern">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-group-modern">
                                    <label class="form-label">Slogan de la compagnie</label>
                                    <input type="text" name="slogan" value="{{ old('slogan', $compagnie->slogan) }}" class="input-modern">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group-modern">
                                    <label class="form-label">Commune *</label>
                                    <input type="text" name="commune" value="{{ old('commune', $compagnie->commune) }}" required class="input-modern">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group-modern">
                                    <label class="form-label">Adresse Géographique *</label>
                                    <input type="text" name="adresse" value="{{ old('adresse', $compagnie->adresse) }}" required class="input-modern">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-2">
                            <button type="submit" class="btn-submit orange">
                                <i class="fas fa-save"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>

                {{-- PANEL: SECURITE --}}
                <div id="panel-security" class="msg-panel" style="display: {{ $isSecurityActive ? 'block' : 'none' }}; border-top-left-radius: 0;">
                    <div class="panel-header">
                        <div class="panel-marker blue"></div>
                        <div>
                            <h2 class="panel-title">Sécurité du Compte</h2>
                            <p class="panel-subtitle">Gérez vos accès confidentiels</p>
                        </div>
                    </div>

                    <form action="{{ route('compagnie.profile.password') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="input-group-modern">
                                    <label class="form-label">Mot de passe actuel</label>
                                    <input type="password" name="current_password" required class="input-modern focus-blue">
                                    @error('current_password') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group-modern">
                                    <label class="form-label">Nouveau mot de passe</label>
                                    <input type="password" name="password" required class="input-modern focus-blue">
                                    @error('password') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group-modern">
                                    <label class="form-label">Confirmation</label>
                                    <input type="password" name="password_confirmation" required class="input-modern focus-blue">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-2">
                            <button type="submit" class="btn-submit blue">
                                <i class="fas fa-lock"></i> Mettre à jour le mot de passe
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function switchProfileTab(tab, btn) {
        document.getElementById('panel-info').style.display = tab === 'info' ? 'block' : 'none';
        document.getElementById('panel-security').style.display = tab === 'security' ? 'block' : 'none';
        
        document.querySelectorAll('.msg-tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('logoPreview');
                const placeholder = document.getElementById('logoPlaceholder');
                
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                if (placeholder) placeholder.classList.add('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Opération Réussie',
            text: '{!! addslashes(session("success")) !!}',
            confirmButtonColor: '#F97316',
            timer: 3000,
            customClass: { popup: 'rounded-lg border-0 shadow-sm' }
        });
    @endif
</script>
@endsection