<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portail Interne - Car 225</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="shortcut icon" href="{{asset('assetsPoster/assets/images/Car225_favicon.png')}}" />
    <style>
        :root {
            --primary-orange: #e94f1b;
            --slate-blue: #324766;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            /* Arrière-plan Image + Overlay Teinté */
            background: linear-gradient(135deg, rgba(50, 71, 102, 0.85) 0%, rgba(233, 79, 27, 0.75) 100%), 
                        url('{{ asset('assets/images/login_bg.png') }}') center/cover no-repeat fixed;
            color: var(--slate-blue);
            padding: 20px;
            overflow: hidden;
        }

        .login-wrapper {
            width: 100%;
            max-width: 460px;
            position: relative;
            z-index: 10;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: white;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            margin-bottom: 24px;
            transition: var(--transition);
            opacity: 0.9;
        }
        .back-btn:hover { transform: translateX(-5px); opacity: 1; color: #ffffff; text-shadow: 0 0 10px rgba(233,79,27,0.5); }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 36px;
            padding: 50px 40px;
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .glass-card::before {
            content: '';
            position: absolute; inset: 0;
            border-radius: 36px;
            padding: 2px;
            background: linear-gradient(135deg, rgba(255,255,255,1), rgba(255,255,255,0.1));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask-composite: exclude;
            pointer-events: none;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-box {
            width: 84px; height: 84px;
            margin: 0 auto 24px;
            background: #ffffff;
            border-radius: 22px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            animation: float 5s ease-in-out infinite;
        }
        .logo-box img { width: 52px; object-fit: contain; }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }

        .title {
            font-size: 30px;
            font-weight: 950;
            color: var(--slate-blue);
            letter-spacing: -1px;
            margin-bottom: 8px;
        }

        .subtitle {
            font-size: 15px;
            color: var(--slate-blue);
            opacity: 0.7;
            font-weight: 600;
            line-height: 1.5;
        }

        .input-group {
            margin-bottom: 24px;
        }

        .input-group label {
            display: block;
            font-size: 12px;
            font-weight: 900;
            color: var(--slate-blue);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
        }

        .input-field {
            position: relative;
        }

        .input-field i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--slate-blue);
            opacity: 0.4;
            font-size: 18px;
            transition: var(--transition);
            pointer-events: none;
        }

        .input-field input {
            width: 100%;
            padding: 18px 64px 18px 58px;
            background: #ffffff;
            border: 2px solid transparent;
            border-radius: 20px;
            font-size: 15px;
            font-weight: 700;
            color: var(--slate-blue);
            transition: var(--transition);
            font-family: inherit;
            box-shadow: 0 8px 25px rgba(0,0,0,0.03);
        }

        .input-field input::placeholder { color: #CBD5E1; font-weight: 500; }

        .input-field input:focus {
            background: #ffffff;
            border-color: var(--primary-orange);
            box-shadow: 0 15px 35px rgba(233, 79, 27, 0.15);
            outline: none;
        }

        .input-field input:focus + i {
            color: var(--primary-orange);
            opacity: 1;
        }

        .pass-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 44px; height: 44px;
            background: transparent;
            border: none;
            color: #94A3B8; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            transition: var(--transition);
            z-index: 50;
        }
        .pass-toggle:hover { 
            color: var(--primary-orange);
            background: rgba(233, 79, 27, 0.05);
            border-radius: 12px;
        }

        .forgot-pass-link {
            font-size: 13px;
            font-weight: 700;
            color: var(--slate-blue);
            text-decoration: none;
            opacity: 0.7;
            transition: var(--transition);
        }
        .forgot-pass-link:hover {
            opacity: 1;
            color: var(--primary-orange);
            transform: translateX(-2px);
            display: inline-block;
        }

        .submit-btn {
            width: 100%;
            padding: 18px;
            background: var(--slate-blue);
            color: white;
            border: none;
            border-radius: 20px;
            font-size: 16px;
            font-weight: 900;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center; justify-content: center; gap: 12px;
            margin-top: 10px;
            box-shadow: 0 12px 30px rgba(50, 71, 102, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .submit-btn:hover {
            background: #25344d;
            transform: translateY(-4px);
            box-shadow: 0 20px 45px rgba(50, 71, 102, 0.4);
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 13px;
            color: white;
            font-weight: 700;
            opacity: 0.9;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        /* ── ANIMATIONS ── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .stagger { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) both; }

        @media (max-width: 480px) {
            .glass-card { padding: 40px 24px; }
            .title { font-size: 26px; }
        }

        /* SweetAlert Glass */
        .swal2-glass {
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(20px) !important;
            border-radius: 36px !important;
            border: 1px solid rgba(255,255,255,0.5) !important;
        }
        .swal2-title { color: var(--slate-blue) !important; font-weight: 950 !important; }
        .swal2-confirm { border-radius: 18px !important; background: var(--slate-blue) !important; padding: 12px 30px !important; }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <a href="{{ url('/') }}" class="back-btn stagger" style="animation-delay: 0.1s">
            <i class="fas fa-arrow-left"></i> Retour au site
        </a>

        <div class="glass-card stagger" style="animation-delay: 0.2s">
            <div class="header">
                <div class="logo-box">
                    <img src="{{ asset('assetsPoster/assets/images/logo_car225.png') }}" alt="Logo Car225">
                </div>
                <h1 class="title">Connexion Interne</h1>
                <p class="subtitle">Espace de gestion sécurisé pour vos trajets.</p>
            </div>

            <form method="POST" action="{{ route('portail.login.submit') }}" novalidate>
                @csrf

                <div class="input-group">
                    <label>Identifiant ou Email</label>
                    <div class="input-field">
                        <i class="fas fa-id-card" id="id-icon"></i>
                        <input type="text" name="identifiant" id="identifiant" 
                               value="{{ old('identifiant') }}" placeholder="Ex: email@test.com ou CHF-000000" required autofocus>
                    </div>
                </div>

                <div class="input-group">
                    <label>Mot de passe</label>
                    <div class="input-field">
                        <i class="fas fa-key"></i>
                        <input type="password" name="password" id="pro-password-field" 
                               placeholder="••••••••" required>
                        <button type="button" class="pass-toggle" id="pro-password-toggle" onclick="togglePasswordPro()">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div style="text-align: right; margin-top: 10px;">
                        <a href="{{ route('portail.password.request') }}" class="forgot-pass-link">Mot de passe oublié ?</a>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    Se connecter <i class="fas fa-shield-alt"></i>
                </button>
            </form>
        </div>

        
    </div>
    <script>
        function togglePasswordPro() {
            const field = document.getElementById('pro-password-field');
            const btn = document.getElementById('pro-password-toggle');
            if (field && btn) {
                const isPassword = field.type === 'password';
                field.type = isPassword ? 'text' : 'password';
                const icon = btn.querySelector('i');
                if (isPassword) {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const identifiantField = document.getElementById('identifiant');
            const idIcon = document.getElementById('id-icon');

            if (identifiantField && idIcon) {
                identifiantField.addEventListener('input', function(e) {
                    const value = e.target.value;
                    if (value.includes('@')) {
                        idIcon.className = 'fas fa-envelope';
                    } else if (value.trim().length > 0) {
                        idIcon.className = 'fas fa-id-badge';
                    } else {
                        idIcon.className = 'fas fa-id-card';
                    }
                });
            }

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Vérification Réussie',
                    text: '{{ session('success') }}',
                    customClass: { popup: 'swal2-glass', confirmButton: 'swal2-confirm' }
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Accès Refusé',
                    text: '{{ session('error') }}',
                    customClass: { popup: 'swal2-glass', confirmButton: 'swal2-confirm' }
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'warning',
                    title: 'Données Non Valides',
                    html: `
                        <ul style="text-align: left; padding: 0 15px; list-style: none; font-size: 14px; color: var(--slate-blue);">
                            @foreach ($errors->all() as $error)
                                <li style="margin-bottom: 8px;"><i class="fas fa-exclamation-triangle" style="color:var(--primary-orange); margin-right: 8px;"></i> {{ $error }}</li>
                            @endforeach
                        </ul>
                    `,
                    customClass: { popup: 'swal2-glass', confirmButton: 'swal2-confirm' }
                });
            @endif
        });
    </script>
</body>
</html>