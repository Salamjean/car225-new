<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portail Interne - Car 225</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="shortcut icon" href="{{asset('assetsPoster/assets/images/logo_car225.png')}}" />
    <style>
        :root {
            --brand-primary: #e94f1b;
            --brand-gradient: linear-gradient(135deg, #e94f1b 0%, #ff6b3d 100%);
            --corporate-dark: #0f172a;
            --corporate-gray: #1e293b;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --bg-body: #f1f5f9;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --input-bg: #f8fafc;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Changement de l'arrière plan pour un dégradé orange */
            background: linear-gradient(135deg, #e94f1b 0%, #ff8c42 100%);
            color: var(--text-main);
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 10;
        }

        .back-btn {
            position: absolute;
            top: -60px;
            left: 0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: var(--transition);
        }

        .back-btn:hover {
            color: #fff;
            transform: translateX(-4px);
        }

        .login-card {
            background: var(--bg-card);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            position: relative;
        }

        .login-header {
            padding: 40px 40px 20px;
            text-align: center;
            background: var(--bg-card);
            position: relative;
        }

        .logo-container {
            width: 72px;
            height: 72px;
            margin: 0 auto 24px;
            background: var(--bg-body);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 16px rgba(0,0,0,0.05);
            border: 1px solid var(--border-color);
        }

        .logo-container img {
            width: 45px;
            height: 45px;
            object-fit: contain;
        }

        .title {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .subtitle {
            font-size: 14px;
            color: var(--text-muted);
            font-weight: 500;
            line-height: 1.5;
        }

        .access-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: rgba(233, 79, 27, 0.1);
            color: var(--brand-primary);
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .login-body {
            padding: 0 40px 40px;
        }

        .input-group {
            margin-bottom: 24px;
        }

        .input-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 16px;
            transition: var(--transition);
            pointer-events: none;
        }

        .input-field {
            width: 100%;
            padding: 14px 16px 14px 46px;
            background: var(--input-bg);
            border: 2px solid transparent;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 500;
            color: var(--text-main);
            transition: var(--transition);
            font-family: inherit;
        }

        .input-field::placeholder { color: #94a3b8; font-weight: 400; }

        .input-field:focus {
            background: var(--bg-card);
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 4px var(--primary-glow);
            outline: none;
        }

        .input-field:focus ~ .input-icon {
            color: var(--brand-primary);
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            font-size: 16px;
            transition: var(--transition);
        }

        .password-toggle:hover { color: var(--brand-primary); }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: var(--brand-gradient);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
            box-shadow: 0 4px 12px rgba(233, 79, 27, 0.3);
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #ff6b3d 0%, #e94f1b 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(233, 79, 27, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .corporate-footer {
            margin-top: 32px;
            text-align: center;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
        }

        /* Decoration Elements */
        .card-decoration {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--brand-gradient);
        }

        /* SweetAlert */
        .swal2-popup { font-family: 'Plus Jakarta Sans', sans-serif !important; border-radius: 16px !important; }
        .swal2-confirm { border-radius: 10px !important; padding: 12px 24px !important; font-weight: 700 !important; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-wrapper { animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1); }

        @media (max-width: 480px) {
            .login-header { padding: 32px 24px 20px; }
            .login-body { padding: 0 24px 32px; }
            .back-btn { top: -45px; }
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <a href="{{ url('/') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Retour au site
        </a>

        <div class="login-card">
            <div class="card-decoration"></div>
            
            <div class="login-header">
                <div class="access-badge">
                    <i class="fas fa-lock"></i> Accès Restreint
                </div>
                <div class="logo-container">
                    <img src="{{ asset('assetsPoster/assets/images/logo_car225.png') }}" alt="Logo Car225">
                </div>
                <h1 class="title">Portail Interne</h1>
                <p class="subtitle">Espace de gestion réservé aux compagnies et partenaires autorisés.</p>
            </div>

            <div class="login-body">
                <form method="POST" action="{{ route('portail.login.submit') }}" novalidate>
                    @csrf

                    <div class="input-group">
                        <label for="identifiant">Email professionnel ou Code ID</label>
                        <div class="input-wrapper">
                            <input type="text" id="identifiant" name="identifiant" class="input-field" 
                                value="{{ old('identifiant') }}" required autofocus placeholder="Ex: agence@car225.ci">
                            <i class="fas fa-user input-icon" id="identifiant-icon"></i>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="password">Mot de passe d'accès</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" class="input-field" required placeholder="••••••••">
                            <i class="fas fa-key input-icon"></i>
                            <button type="button" class="password-toggle" onclick="togglePassword(this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">
                        Sécuriser la connexion <i class="fas fa-shield-alt"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="corporate-footer">
            &copy; {{ date('Y') }} Car 225. Système d'Information Administratif.
        </div>
    </div>

    <script>
        function togglePassword(btn) {
            const input = btn.previousElementSibling.previousElementSibling;
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Dynamic icon change based on input
            const identifiantField = document.getElementById('identifiant');
            const identifiantIcon = document.getElementById('identifiant-icon');

            if (identifiantField && identifiantIcon) {
                identifiantField.addEventListener('input', function(e) {
                    const value = e.target.value;
                    if (value.includes('@')) {
                        identifiantIcon.className = 'fas fa-envelope input-icon';
                    } else if (value.trim().length > 0) {
                        identifiantIcon.className = 'fas fa-id-badge input-icon';
                    } else {
                        identifiantIcon.className = 'fas fa-user input-icon';
                    }
                });
            }

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Vérification Réussie',
                    text: '{{ session('success') }}',
                    confirmButtonColor: 'var(--brand-primary)',
                    customClass: { popup: 'swal2-popup', confirmButton: 'swal2-confirm' }
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Accès Refusé',
                    text: '{{ session('error') }}',
                    confirmButtonColor: 'var(--corporate-dark)',
                    customClass: { popup: 'swal2-popup', confirmButton: 'swal2-confirm' }
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'warning',
                    title: 'Données Invalides',
                    html: `
                        <ul style="text-align: left; padding-left: 20px; list-style: none; color: #1e293b; font-size: 14px;">
                            @foreach ($errors->all() as $error)
                                <li style="margin-bottom: 6px;"><i class="fas fa-exclamation-triangle" style="color: #e94f1b; margin-right: 6px;"></i> {{ $error }}</li>
                            @endforeach
                        </ul>
                    `,
                    confirmButtonColor: 'var(--corporate-dark)',
                    customClass: { popup: 'swal2-popup', confirmButton: 'swal2-confirm' }
                });
            @endif
        });
    </script>
</body>
</html>

