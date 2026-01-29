<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Car 225</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <link rel="shortcut icon" href="{{asset('assetsPoster/assets/images/logo_car225.png')}}" />
    <style>
        :root {
            --primary: #e94f1b;
            --primary-dark: #e94f1b;
            --secondary: #2ecc71;
            --light: #f8f9fa;
            --dark: #343a40;
            --white: #ffffff;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --border-radius: 12px;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: var(--dark);
        }

        .auth-container {
            width: 100%;
            max-width: 450px;
            margin: 0 auto;
        }

        .auth-card {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
        }

        .auth-card:hover {
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        }

        .auth-header {
            background: linear-gradient(to right, var(--primary), #ffb74d);
            color: var(--white);
            padding: 30px;
            text-align: center;
        }

        .auth-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .auth-header p {
            font-size: 15px;
            opacity: 0.9;
        }

        .auth-body {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
            font-size: 14px;
        }

        .form-group label i {
            margin-right: 8px;
            color: var(--primary);
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--light-gray);
            border-radius: 10px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            transition: var(--transition);
            background: var(--white);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(254, 162, 25, 0.15);
        }

        .btn {
            display: block;
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .auth-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--light-gray);
            color: var(--gray);
            font-size: 14px;
        }

        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .auth-footer a:hover {
            text-decoration: underline;
            color: var(--primary-dark);
        }

        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: rgba(46, 204, 113, 0.1);
            color: #27ae60;
            border: 1px solid rgba(46, 204, 113, 0.2);
        }

        .alert-error {
            background: rgba(231, 76, 60, 0.1);
            color: #c0392b;
            border: 1px solid rgba(231, 76, 60, 0.2);
        }

        .alert i {
            font-size: 18px;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 40px;
            cursor: pointer;
            color: var(--gray);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .logo-icon {
            background: var(--white);
            color: var(--primary);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }

        .forgot-password a {
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .forgot-password i {
            margin-right: 6px;
        }

        @media (max-width: 480px) {
            .auth-body {
                padding: 30px 20px;
            }

            .auth-header {
                padding: 25px 20px;
            }
        }

        /* Ajoutez ceci dans votre section <style> */
        .swal2-popup {
            font-family: 'Poppins', sans-serif;
            border-radius: var(--border-radius) !important;
        }

        .swal2-confirm {
            border-radius: 10px !important;
            padding: 10px 24px !important;
            font-weight: 600 !important;
        }

        .swal2-success {
            border-color: var(--secondary) !important;
            color: var(--secondary) !important;
        }

        .swal2-error {
            border-color: #e74c3c !important;
            color: #e74c3c !important;
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <h1>Connexion</h1>
                </div>
                <p>Accédez à votre espace personnel</p>
            </div>
            <div class="auth-body">
                <form method="POST" action="{{ route('user.handleLogin') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Adresse Email</label>
                        <input type="email" id="email" name="email" class="form-control"
                            value="{{ old('email') }}" required autofocus placeholder="votre@email.com">
                    </div>

                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Mot de passe</label>
                        <input type="password" id="password" name="password" class="form-control" required
                            placeholder="Votre mot de passe">
                        <span class="password-toggle">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Se souvenir de moi</label>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </button>
                </form>

                <div class="forgot-password">
                    <a href="{{ route('password.request') }}">
                        <i class="fas fa-key"></i> Mot de passe oublié ?
                    </a>
                </div>
            </div>
        </div>

        <div class="auth-footer">
            <p>Vous n'avez pas de compte ? <a href="{{ route('user.register') }}">Créer un compte</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButtons = document.querySelectorAll('.password-toggle');

            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.previousElementSibling;
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    this.innerHTML = type === 'password' ?
                        '<i class="fas fa-eye"></i>' :
                        '<i class="fas fa-eye-slash"></i>';
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButtons = document.querySelectorAll('.password-toggle');

            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.previousElementSibling;
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    this.innerHTML = type === 'password' ?
                        '<i class="fas fa-eye"></i>' :
                        '<i class="fas fa-eye-slash"></i>';
                });
            });

            // Gestion des messages SweetAlert2
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Succès !',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#2ecc71',
                    confirmButtonText: 'OK',
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    html: `
                        <ul style="text-align: left; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    `,
                    confirmButtonColor: '#e74c3c',
                    confirmButtonText: 'Compris'
                });
            @endif

            // Optionnel: Validation du formulaire avant soumission
            document.getElementById('registerForm').addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('password_confirmation').value;

                if (password !== confirmPassword) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Les mots de passe ne correspondent pas',
                        confirmButtonColor: '#e74c3c',
                        confirmButtonText: 'Corriger'
                    });
                }
            });
        });
    </script>
</body>
</body>

</html>
