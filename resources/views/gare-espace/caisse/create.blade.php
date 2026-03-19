@extends('gare-espace.layouts.template')

@section('title', 'Nouvelle Caissière')

@section('content')
<!-- Google Fonts & Animate.css -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
    :root {
        --primary: #e94f1b;
        --primary-dark: #d33d0f;
        --primary-light: #fff7ed;
        --secondary: #10b981;
        --accent: #f59e0b;
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --gray-700: #334155;
        --gray-900: #0f172a;
        --font-jakarta: 'Plus Jakarta Sans', sans-serif;
    }

    .premium-container {
        font-family: var(--font-jakarta);
        background: var(--gray-50);
        min-height: 100vh;
        padding: 3rem 1.5rem;
    }

    .form-card-premium {
        background: white;
        border-radius: 2.5rem;
        border: 1px solid var(--gray-200);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .input-group-premium {
        position: relative;
        transition: all 0.3s ease;
    }

    .floating-label-input {
        width: 100%;
        padding: 1.25rem 1rem;
        border: 1.5px solid var(--gray-200);
        border-radius: 1.25rem;
        background: var(--gray-50);
        font-weight: 600;
        color: var(--gray-900);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 0.95rem;
    }

    .floating-label-input:focus {
        outline: none;
        border-color: var(--primary);
        background: white;
        box-shadow: 0 10px 15px -3px rgba(233, 79, 27, 0.1);
        transform: translateY(-2px);
    }

    .label-premium {
        display: block;
        font-size: 0.75rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
        margin-left: 0.5rem;
    }

    .section-header-premium {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .section-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--primary);
        box-shadow: 0 0 0 4px var(--primary-light);
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--gray-900);
        letter-spacing: -0.02em;
    }

    .btn-submit-premium {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 1.25rem 2.5rem;
        border-radius: 1.25rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s;
        box-shadow: 0 10px 15px -3px rgba(233, 79, 27, 0.3);
    }

    .btn-submit-premium:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 25px -5px rgba(233, 79, 27, 0.4);
        filter: brightness(1.1);
    }

    .btn-back-premium {
        background: white;
        color: var(--gray-700);
        padding: 1.25rem 2rem;
        border-radius: 1.25rem;
        font-weight: 700;
        border: 1.5px solid var(--gray-200);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s;
    }

    .btn-back-premium:hover {
        background: var(--gray-50);
        border-color: var(--gray-700);
        color: var(--gray-900);
    }

    .avatar-upload-premium {
        position: relative;
        width: 160px;
        height: 160px;
        margin: 0 auto;
    }

    .avatar-preview-box {
        width: 100%;
        height: 100%;
        border-radius: 2.5rem;
        background: var(--gray-100);
        border: 2px dashed var(--gray-200);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        transition: all 0.3s;
    }

    .avatar-preview-box:hover {
        border-color: var(--primary);
        background: var(--primary-light);
    }

    .avatar-upload-btn {
        position: absolute;
        bottom: -10px;
        right: -10px;
        width: 45px;
        height: 45px;
        border-radius: 1rem;
        background: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: all 0.2s;
        border: 4px solid white;
    }

    .avatar-upload-btn:hover {
        transform: scale(1.1);
        background: var(--primary-dark);
    }

    /* Custom Select */
    select.floating-label-input {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 1rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
    }
</style>

<div class="premium-container">
    <div class="max-w-5xl mx-auto">
        {{-- Header --}}
        <div class="text-center mb-12 animate__animated animate__fadeInDown">
            <span class="inline-block px-4 py-1.5 bg-orange-100 text-orange-600 rounded-full text-xs font-black uppercase tracking-widest mb-4">Financement</span>
            <h1 class="text-5xl font-black text-gray-900 tracking-tight mb-4">Nouvelle <span class="text-orange-600">Caissière</span></h1>
            <p class="text-gray-500 font-medium text-lg">Inscrivez une nouvelle collaboratrice de caisse pour votre gare.</p>
        </div>

        <form action="{{ route('gare-espace.caisse.store') }}" method="POST" enctype="multipart/form-data" class="animate__animated animate__fadeInUp">
            @csrf

            <div class="form-card-premium p-8 lg:p-12">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                    
                    {{-- Left Column: Avatar & Role Info --}}
                    <div class="lg:col-span-4 space-y-10">
                        <div class="text-center">
                            <label class="label-premium mb-4">Photo de profil</label>
                            <div class="avatar-upload-premium group">
                                <div class="avatar-preview-box">
                                    <img id="p-preview" src="{{ asset('assets/images/placeholder-avatar.png') }}" class="w-full h-full object-cover hidden">
                                    <div id="p-placeholder" class="text-gray-300">
                                        <i class="fas fa-user-circle text-8xl"></i>
                                    </div>
                                </div>
                                <label for="profile_picture" class="avatar-upload-btn">
                                    <i class="fas fa-camera"></i>
                                </label>
                                <input type="file" id="profile_picture" name="profile_picture" class="hidden" accept="image/*">
                            </div>
                            @error('profile_picture')
                                <p class="text-red-500 text-[10px] mt-3 font-bold uppercase">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-orange-50 p-6 rounded-[2rem] border border-orange-100 italic">
                            <p class="text-xs text-orange-700 font-medium">
                                <i class="fas fa-info-circle mr-2"></i>
                                Un code OTP sera envoyé à l'adresse email de la caissière pour sa première connexion.
                            </p>
                        </div>
                    </div>

                    {{-- Right Column: Detailed Info --}}
                    <div class="lg:col-span-8 space-y-12">
                        {{-- Section 1: Identité --}}
                        <div>
                            <div class="section-header-premium">
                                <div class="section-dot"></div>
                                <h2 class="section-title">Identité</h2>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="input-group-premium">
                                    <label class="label-premium">Nom</label>
                                    <input type="text" name="name" value="{{ old('name') }}" required class="floating-label-input" placeholder="Ex: Kouame">
                                    @error('name') <p class="text-red-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror
                                </div>
                                <div class="input-group-premium">
                                    <label class="label-premium">Prénom</label>
                                    <input type="text" name="prenom" value="{{ old('prenom') }}" required class="floating-label-input" placeholder="Ex: Amenan">
                                    @error('prenom') <p class="text-red-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Section 2: Contact --}}
                        <div>
                            <div class="section-header-premium">
                                <div class="section-dot" style="background: var(--secondary); box-shadow: 0 0 0 4px #d1fae5;"></div>
                                <h2 class="section-title">Coordonnées & Localisation</h2>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="input-group-premium">
                                    <label class="label-premium">Email de connexion</label>
                                    <input type="email" name="email" value="{{ old('email') }}" required class="floating-label-input" placeholder="k.amenan@gare.ci">
                                    @error('email') <p class="text-red-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror
                                </div>
                                <div class="input-group-premium">
                                    <label class="label-premium">Contact principal</label>
                                    <input type="text" name="contact" value="{{ old('contact') }}" required class="floating-label-input" placeholder="0102030405" maxlength="10">
                                    @error('contact') <p class="text-red-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror
                                </div>
                                <div class="input-group-premium md:col-span-2">
                                    <label class="label-premium">Commune de résidence</label>
                                    <input type="text" name="commune" value="{{ old('commune') }}" class="floating-label-input" placeholder="Ex: Cocody Angré">
                                    @error('commune') <p class="text-red-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Section 3: Urgence --}}
                        <div class="bg-red-50/50 p-6 lg:p-8 rounded-[2rem] border border-red-100">
                            <div class="section-header-premium">
                                <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shadow-sm">
                                    <i class="fas fa-heartbeat"></i>
                                </div>
                                <h2 class="section-title">Contact d'Urgence</h2>
                            </div>
                            <div class="space-y-6">
                                <div class="input-group-premium">
                                    <label class="label-premium">Nom Complet du contact</label>
                                    <input type="text" name="nom_urgence" value="{{ old('nom_urgence') }}" required class="floating-label-input" placeholder="Personne à prévenir">
                                    @error('nom_urgence') <p class="text-red-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="input-group-premium">
                                        <label class="label-premium">Lien de parenté</label>
                                        <select name="lien_parente_urgence" required class="floating-label-input">
                                            <option value="">Relation</option>
                                            <option value="Conjoint(e)" {{ old('lien_parente_urgence') == 'Conjoint(e)' ? 'selected' : '' }}>Conjoint(e)</option>
                                            <option value="Père" {{ old('lien_parente_urgence') == 'Père' ? 'selected' : '' }}>Père</option>
                                            <option value="Mère" {{ old('lien_parente_urgence') == 'Mère' ? 'selected' : '' }}>Mère</option>
                                            <option value="Frère" {{ old('lien_parente_urgence') == 'Frère' ? 'selected' : '' }}>Frère</option>
                                            <option value="Sœur" {{ old('lien_parente_urgence') == 'Sœur' ? 'selected' : '' }}>Sœur</option>
                                            <option value="Autre" {{ old('lien_parente_urgence') == 'Autre' ? 'selected' : '' }}>Autre</option>
                                        </select>
                                        @error('lien_parente_urgence') <p class="text-red-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="input-group-premium">
                                        <label class="label-premium">Numéro d'urgence</label>
                                        <input type="text" name="cas_urgence" value="{{ old('cas_urgence') }}" required class="floating-label-input" placeholder="0708091011" maxlength="10">
                                        @error('cas_urgence') <p class="text-red-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="mt-12 pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
                    <a href="{{ route('gare-espace.caisse.index') }}" class="btn-back-premium">
                        <i class="fas fa-arrow-left"></i>
                        <span>Retour à la liste</span>
                    </a>
                    
                    <button type="submit" class="btn-submit-premium">
                        <i class="fas fa-save"></i>
                        <span>Enregistrer la caissière</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('profile_picture');
        const preview = document.getElementById('p-preview');
        const placeholder = document.getElementById('p-placeholder');

        input.onchange = evt => {
            const [file] = input.files;
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
        }

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Opération réussie',
                text: '{{ session('success') }}',
                confirmButtonColor: '#e94f1b',
                customClass: { popup: 'rounded-[2rem]' }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Erreur détectée',
                text: '{{ session('error') }}',
                confirmButtonColor: '#e94f1b',
                customClass: { popup: 'rounded-[2rem]' }
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Vérifiez les champs',
                html: `<div class="text-left text-sm p-4 bg-red-50 rounded-2xl border border-red-100">
                        <ul class="space-y-1 text-red-700 font-medium">
                            @foreach($errors->all() as $error)
                                <li><i class="fas fa-exclamation-triangle mr-2"></i> {{ $error }}</li>
                            @endforeach
                        </ul>
                       </div>`,
                confirmButtonColor: '#e94f1b',
                customClass: { popup: 'rounded-[2.5rem] p-8' }
            });
        @endif
    });
</script>
@endsection
