<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Car 225</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="shortcut icon" href="{{asset('assetsPoster/assets/images/logo_car225.png')}}" />
    <style>
        :root {
            --primary: #e94f1b;
            --primary-light: #ff6b3d;
            --primary-dark: #c43d10;
            --primary-glow: rgba(233, 79, 27, 0.15);
            --accent: #ff8c42;
            --dark: #0f172a;
            --dark-secondary: #1e293b;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border: #e2e8f0;
            --border-focus: #e94f1b;
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-input: #f1f5f9;
            --success: #10b981;
            --error: #ef4444;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -2px rgba(0,0,0,0.05);
            --shadow-lg: 0 20px 40px -12px rgba(0,0,0,0.12);
            --shadow-xl: 0 25px 50px -12px rgba(0,0,0,0.18);
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            background: var(--bg-secondary);
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* ─── LEFT PANEL ─── */
        .brand-panel {
            display: none;
            width: 50%;
            background: linear-gradient(145deg, var(--primary) 0%, var(--primary-dark) 50%, #1a1a2e 100%);
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            overflow: hidden;
            padding: 60px;
            flex-direction: column;
            justify-content: center;
        }

        .brand-video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
            opacity: 0.6;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom right, rgba(233,79,27,0.7) 0%, rgba(196,61,16,0.8) 100%);
            z-index: 1;
        }

        .brand-content {
            position: relative;
            z-index: 2;
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-wrapper {
            width: 80px;
            height: 80px;
            background: #ffffff;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            transition: var(--transition);
        }

        .logo-wrapper:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 32px rgba(0,0,0,0.2);
        }

        .brand-logo {
            width: 55px;
            height: 55px;
        }

        .brand-title {
            font-size: 46px;
            font-weight: 800;
            color: white;
            line-height: 1.1;
            margin-bottom: 20px;
            letter-spacing: -0.02em;
        }

        .brand-subtitle {
            font-size: 16px;
            color: rgba(255,255,255,0.9);
            line-height: 1.7;
            max-width: 450px;
            margin: 0 auto;
        }

        .brand-features {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: row;
            justify-content: center;
            gap: 20px;
        }

        .feature-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-lg);
            padding: 24px 20px;
            color: white;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
            width: 160px;
            backdrop-filter: blur(4px);
            transition: var(--transition);
        }

        .feature-item:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 32px;
            color: white;
            margin-bottom: 8px;
        }

        .decoration-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.03);
            z-index: 0;
        }
        .decoration-circle:nth-child(1) { width: 400px; height: 400px; top: -150px; right: -100px; }
        .decoration-circle:nth-child(2) { width: 250px; height: 250px; bottom: -80px; left: -80px; }
        .decoration-circle:nth-child(3) { width: 150px; height: 150px; top: 40%; right: 10%; }

        /* ─── RIGHT PANEL (form) ─── */
        .form-panel {
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 40px 20px;
        }

        .form-wrapper {
            width: 100%;
            max-width: 560px;
        }

        /* Mobile logo */
        .mobile-logo { text-align: center; margin-bottom: 28px; }
        .mobile-logo img { width: 52px; height: 52px; margin-bottom: 8px; }
        .mobile-logo-text {
            font-size: 20px; font-weight: 700;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }

        /* Form card */
        .form-card {
            background: var(--bg-primary);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border);
            overflow: hidden;
            transition: var(--transition);
            animation: fadeUp 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .form-card:hover { box-shadow: var(--shadow-xl); }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-header { padding: 32px 36px 0; }
        .form-header h1 {
            font-size: 24px; font-weight: 700; color: var(--text-primary);
            margin-bottom: 6px; letter-spacing: -0.01em;
        }
        .form-header p { font-size: 14px; color: var(--text-secondary); }

        .form-body { padding: 24px 36px 36px; }

        /* ─── Form elements ─── */
        .form-row { display: flex; gap: 16px; }
        .form-row .input-group { flex: 1; }

        .input-group { margin-bottom: 18px; }
        .input-group label {
            display: block; font-size: 13px; font-weight: 600;
            color: var(--text-primary); margin-bottom: 7px; letter-spacing: 0.01em;
        }

        .input-wrapper { position: relative; }

        .input-wrapper i.field-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: var(--text-muted); font-size: 14px; transition: var(--transition); pointer-events: none;
        }

        .input-field {
            width: 100%; padding: 12px 14px 12px 40px;
            border: 1.5px solid var(--border); border-radius: var(--radius-md);
            font-size: 14px; font-family: 'Inter', sans-serif;
            background: var(--bg-input); color: var(--text-primary);
            transition: var(--transition); outline: none;
        }
        .input-field::placeholder { color: var(--text-muted); }
        .input-field:focus {
            border-color: var(--primary); background: var(--bg-primary);
            box-shadow: 0 0 0 4px var(--primary-glow);
        }
        .input-field:focus ~ i.field-icon { color: var(--primary); }

        .password-toggle-btn {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: var(--text-muted);
            cursor: pointer; padding: 4px; font-size: 15px; transition: var(--transition);
        }
        .password-toggle-btn:hover { color: var(--primary); }

        .error-msg { color: var(--error); font-size: 12px; margin-top: 4px; display: block; }

        /* Photo upload */
        .photo-upload-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 24px;
            border: 2px dashed var(--border);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: var(--transition);
            background: var(--bg-input);
            text-align: center;
        }
        .photo-upload-area:hover {
            border-color: var(--primary);
            background: rgba(233, 79, 27, 0.03);
        }

        .photo-preview-circle {
            width: 52px; height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-glow), rgba(255,140,66,0.15));
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; flex-shrink: 0;
            border: 2px solid var(--border);
        }
        .photo-preview-circle i { font-size: 20px; color: var(--primary); }
        .photo-preview-circle img {
            width: 100%; height: 100%; object-fit: cover; display: none;
        }

        .photo-upload-text span { display: block; font-size: 14px; font-weight: 600; color: var(--primary); margin-bottom: 2px; }
        .photo-upload-text small { font-size: 12px; color: var(--text-secondary); }

        .photo-upload-area input[type="file"] {
            position: absolute; width: 0; height: 0; opacity: 0;
        }

        /* Submit button */
        .btn-submit {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; padding: 14px; border: none; border-radius: var(--radius-md);
            font-size: 15px; font-weight: 600; font-family: 'Inter', sans-serif;
            cursor: pointer; transition: var(--transition);
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white; box-shadow: 0 4px 12px rgba(233, 79, 27, 0.3);
            position: relative; overflow: hidden; margin-top: 4px;
        }
        .btn-submit::after {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.1) 100%);
            opacity: 0; transition: opacity 0.3s;
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(233, 79, 27, 0.35); }
        .btn-submit:hover::after { opacity: 1; }
        .btn-submit:active { transform: translateY(0); }

        /* Divider */
        .divider { display: flex; align-items: center; gap: 16px; margin: 22px 0; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }
        .divider span {
            font-size: 12px; color: var(--text-muted); font-weight: 500;
            text-transform: uppercase; letter-spacing: 0.05em;
        }

        /* Google button */
        .btn-google {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; padding: 13px; border: 1.5px solid var(--border);
            border-radius: var(--radius-md); font-size: 14px; font-weight: 500;
            font-family: 'Inter', sans-serif; cursor: pointer; transition: var(--transition);
            background: var(--bg-primary); color: var(--text-primary); text-decoration: none;
        }
        .btn-google:hover { background: var(--bg-secondary); border-color: #d1d5db; box-shadow: var(--shadow-md); }
        .btn-google svg { width: 18px; height: 18px; }

        /* Footer */
        .form-footer {
            text-align: center; margin-top: 24px;
            font-size: 14px; color: var(--text-secondary);
        }
        .form-footer a {
            color: var(--primary); text-decoration: none; font-weight: 600; transition: var(--transition);
        }
        .form-footer a:hover { color: var(--primary-dark); text-decoration: underline; }

        /* Password strength indicator */
        .password-strength { margin-top: 8px; display: flex; gap: 4px; }
        .strength-bar { flex: 1; height: 3px; background: var(--border); border-radius: 2px; transition: var(--transition); }
        .strength-bar.weak { background: var(--error); }
        .strength-bar.medium { background: #f59e0b; }
        .strength-bar.strong { background: var(--success); }

        /* SweetAlert customization */
        .swal2-popup { font-family: 'Inter', sans-serif !important; border-radius: 16px !important; }
        .swal2-confirm { border-radius: 10px !important; padding: 10px 24px !important; font-weight: 600 !important; }

        /* ─── Responsive ─── */
        @media (min-width: 1024px) {
            .brand-panel { display: flex; }
            .form-panel { width: 50%; margin-left: 50%; padding: 40px 60px; }
            .mobile-logo { display: none; }
        }

        @media (max-width: 600px) {
            .form-body { padding: 20px 18px 28px; }
            .form-header { padding: 24px 18px 0; }
            .form-header h1 { font-size: 20px; }
            .form-row { flex-direction: column; gap: 0; }
            .form-wrapper { max-width: 100%; }
        }
    </style>
</head>

<body>
    <!-- Brand Panel -->
    <div class="brand-panel">
        <video class="brand-video" autoplay muted loop playsinline>
            <source src="{{ asset('assets/images/VideoCar225.mp4') }}" type="video/mp4">
        </video>
        <div class="decoration-circle"></div>
        <div class="decoration-circle"></div>
        <div class="decoration-circle"></div>

        <div class="brand-content">
            <a href="{{ route('home') }}" style="text-decoration: none; display: inline-block;">
                <div class="logo-wrapper">
                    <img src="{{ asset('assetsPoster/assets/images/logo_car225.png') }}" alt="Car225" class="brand-logo">
                </div>
            </a>
            <h2 class="brand-title">Créez votre<br>compte en<br>quelques clics.</h2>
            <p class="brand-subtitle" style="color: #fff;">Rejoignez la première plateforme de réservation de transport en Côte d'Ivoire. Simple, rapide et sécurisé.</p>
        </div>

        <div class="brand-features">
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                <span>Réservation en moins de 2 minutes</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-wallet"></i></div>
                <span>Portefeuille numérique intégré</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-headset"></i></div>
                <span>Support client réactif 7j/7</span>
            </div>
        </div>
    </div>

    <!-- Form Panel -->
    <div class="form-panel">
        <div class="form-wrapper">
            <a href="{{ route('home') }}" style="text-decoration: none; display: block;">
                <div class="mobile-logo">
                    <img src="{{ asset('assetsPoster/assets/images/logo_car225.png') }}" alt="Car225">
                    <div class="mobile-logo-text">Car225</div>
                </div>
            </a>

            <div class="form-card">
                <div class="form-header">
                    <h1>Créer un compte ✨</h1>
                    <p>Rejoignez la communauté Car225</p>
                </div>

                <div class="form-body">
                    <form method="POST" action="{{ route('user.handleRegister') }}" enctype="multipart/form-data" id="registerForm">
                        @csrf

                        <!-- Nom / Prénom -->
                        <div class="form-row">
                            <div class="input-group">
                                <label for="name">Nom</label>
                                <div class="input-wrapper">
                                    <input type="text" id="name" name="name" class="input-field"
                                        value="{{ old('name') }}" required placeholder="Votre nom">
                                    <i class="fas fa-user field-icon"></i>
                                </div>
                                @error('name') <span class="error-msg">{{ $message }}</span> @enderror
                            </div>

                            <div class="input-group">
                                <label for="prenom">Prénom</label>
                                <div class="input-wrapper">
                                    <input type="text" id="prenom" name="prenom" class="input-field"
                                        value="{{ old('prenom') }}" required placeholder="Votre prénom">
                                    <i class="fas fa-user field-icon"></i>
                                </div>
                                @error('prenom') <span class="error-msg">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Email / Contact -->
                        <div class="form-row">
                            <div class="input-group">
                                <label for="email">Adresse email <span style="color: var(--text-muted); font-weight: 400;">(optionnel)</span></label>
                                <div class="input-wrapper">
                                    <input type="email" id="email" name="email" class="input-field"
                                        value="{{ old('email') }}" placeholder="votre@email.com">
                                    <i class="fas fa-envelope field-icon"></i>
                                </div>
                                @error('email') <span class="error-msg">{{ $message }}</span> @enderror
                            </div>

                            <div class="input-group">
                                <label for="contact">Téléphone</label>
                                <div class="input-wrapper">
                                    <input type="text" id="contact" name="contact" class="input-field"
                                        value="{{ old('contact') }}" required placeholder="07 XX XX XX XX" maxlength="10" minlength="10">
                                    <i class="fas fa-phone field-icon"></i>
                                </div>
                                @error('contact') <span class="error-msg">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Passwords -->
                        <div class="form-row">
                            <div class="input-group">
                                <label for="password">Mot de passe</label>
                                <div class="input-wrapper">
                                    <input type="password" id="password" name="password" class="input-field" required
                                        placeholder="Min. 8 caractères" oninput="checkStrength(this.value)">
                                    <i class="fas fa-lock field-icon"></i>
                                    <button type="button" class="password-toggle-btn" onclick="togglePassword(this)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="password-strength">
                                    <div class="strength-bar" id="str1"></div>
                                    <div class="strength-bar" id="str2"></div>
                                    <div class="strength-bar" id="str3"></div>
                                    <div class="strength-bar" id="str4"></div>
                                </div>
                                @error('password') <span class="error-msg">{{ $message }}</span> @enderror
                            </div>

                            <div class="input-group">
                                <label for="password_confirmation">Confirmation</label>
                                <div class="input-wrapper">
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        class="input-field" required placeholder="Répéter le mot de passe">
                                    <i class="fas fa-lock field-icon"></i>
                                    <button type="button" class="password-toggle-btn" onclick="togglePassword(this)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password_confirmation') <span class="error-msg">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Photo Profile -->
                        <div class="input-group">
                            <label>Photo de profil <span style="color: var(--text-muted); font-weight: 400;">(optionnel)</span></label>
                            <label class="photo-upload-area" for="photo_profile">
                                <div class="photo-preview-circle" id="previewCircle">
                                    <i class="fas fa-camera" id="cameraIcon"></i>
                                    <img id="preview" alt="Preview">
                                </div>
                                <div class="photo-upload-text">
                                    <span id="uploadLabel">Choisir une photo</span>
                                    <small>JPG, PNG, JPEG — Max 2 Mo</small>
                                </div>
                                <input type="file" id="photo_profile" name="photo_profile" accept="image/*" onchange="previewImage(this)">
                            </label>
                            @error('photo_profile') <span class="error-msg">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="btn-submit">
                            <i class="fas fa-arrow-right"></i> Créer mon compte
                        </button>
                    </form>

                    <div class="divider"><span>ou</span></div>

                    <a href="{{ route('auth.google') }}" class="btn-google">
                        <svg viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                        S'inscrire avec Google
                    </a>
                </div>
            </div>

            <div class="form-footer">
                <p>Vous avez déjà un compte ? <a href="{{ route('login') }}">Se connecter</a></p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(btn) {
            const input = btn.parentElement.querySelector('.input-field');
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({ icon: 'error', text: 'La photo ne doit pas dépasser 2 Mo.', confirmButtonColor: '#e94f1b' });
                    input.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('preview');
                    img.src = e.target.result;
                    img.style.display = 'block';
                    document.getElementById('cameraIcon').style.display = 'none';
                    document.getElementById('uploadLabel').textContent = file.name;
                };
                reader.readAsDataURL(file);
            }
        }

        function checkStrength(pwd) {
            const bars = [document.getElementById('str1'), document.getElementById('str2'), document.getElementById('str3'), document.getElementById('str4')];
            bars.forEach(b => b.className = 'strength-bar');
            let score = 0;
            if (pwd.length >= 4) score++;
            if (pwd.length >= 8) score++;
            if (/[A-Z]/.test(pwd) && /[a-z]/.test(pwd)) score++;
            if (/[0-9]/.test(pwd) || /[^A-Za-z0-9]/.test(pwd)) score++;
            const levels = ['weak', 'weak', 'medium', 'strong', 'strong'];
            for (let i = 0; i < score; i++) {
                bars[i].classList.add(levels[score]);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Succès !',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#e94f1b',
                    confirmButtonText: 'OK',
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    html: `
                        <ul style="text-align: left; padding-left: 20px; list-style: none;">
                            @foreach ($errors->all() as $error)
                                <li style="margin-bottom: 4px;">• {{ $error }}</li>
                            @endforeach
                        </ul>
                    `,
                    confirmButtonColor: '#e94f1b',
                    confirmButtonText: 'Compris'
                });
            @endif

            // Form validation before submit
            document.getElementById('registerForm').addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('password_confirmation').value;

                if (password !== confirmPassword) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Les mots de passe ne correspondent pas',
                        confirmButtonColor: '#e94f1b',
                        confirmButtonText: 'Corriger'
                    });
                }
            });
        });
    </script>
</body>
</html>
