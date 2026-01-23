<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Car 225</title>
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
            width: 40%;
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
            margin-bottom: 20px;
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

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236c757d' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 16px;
            padding-right: 40px;
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

        .btn-success {
            background: var(--secondary);
            color: var(--white);
        }

        .btn-success:hover {
            background: #27ae60;
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

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
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

        .input-icon {
            position: absolute;
            right: 16px;
            top: 40px;
            color: var(--gray);
            font-size: 16px;
            pointer-events: none;
        }

        @media (max-width: 480px) {
            .auth-body {
                padding: 30px 20px;
            }

            .auth-header {
                padding: 25px 20px;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .auth-container {
                max-width: 100%;
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

        /* Ajoutez ce CSS dans votre section <style> */
        .form-control[type="file"] {
            padding: 12px;
            cursor: pointer;
        }

        .form-control[type="file"]::-webkit-file-upload-button {
            background: var(--light-gray);
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            margin-right: 10px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            transition: var(--transition);
        }

        .form-control[type="file"]::-webkit-file-upload-button:hover {
            background: #dde1e6;
        }

        .text-muted {
            color: var(--gray) !important;
            font-size: 13px;
            margin-top: 5px;
            display: block;
        }

        .error-message {
            color: #e74c3c;
            font-size: 13px;
            margin-top: 5px;
            display: block;
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h1>Inscription</h1>
                </div>
                <p>Rejoignez notre communauté</p>
            </div>
            <div class="auth-body">
                <form method="POST" action="{{ route('user.handleRegister') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-row">
                        <div class="form-group">
                            <label for="name"><i class="fas fa-user"></i> Nom</label>
                            <input type="text" id="name" name="name" class="form-control"
                                value="{{ old('name') }}" required placeholder="Votre nom">
                            @error('name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="prenom"><i class="fas fa-user"></i> Prénom</label>
                            <input type="text" id="prenom" name="prenom" class="form-control"
                                value="{{ old('prenom') }}" required placeholder="Votre prénom">
                            @error('prenom')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Adresse Email</label>
                            <input type="email" id="email" name="email" class="form-control"
                                value="{{ old('email') }}" required placeholder="votre@email.com">
                            <div class="input-icon">
                                <i class="fas fa-at"></i>
                            </div>
                            @error('email')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="contact"><i class="fas fa-phone"></i> Contact</label>
                            <input type="text" id="contact" name="contact" class="form-control"
                                value="{{ old('contact') }}" required placeholder="Votre numéro de téléphone">
                            <div class="input-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            @error('contact')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>


                    <div class="form-row">
                        <div class="form-group">
                            <label for="password"><i class="fas fa-lock"></i> Mot de passe</label>
                            <input type="password" id="password" name="password" class="form-control" required
                                placeholder="Minimum 8 caractères">
                            <span class="password-toggle">
                                <i class="fas fa-eye"></i>
                            </span>
                            @error('password')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation"><i class="fas fa-lock"></i> Confirmer le mot de
                                passe</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-control" required placeholder="Répétez votre mot de passe">
                            <span class="password-toggle">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        @error('password_confirmation')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="adresse"><i class="fas fa-home"></i> Adresse</label>
                            <input type="text" id="adresse" name="adresse" class="form-control"
                                value="{{ old('adresse') }}" required placeholder="Votre adresse complète">
                            <div class="input-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            @error('adresse')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="photo_profile"><i class="fas fa-camera"></i> Photo de profil
                                (optionnel)</label>
                            <input type="file" id="photo_profile" name="photo_profile" class="form-control"
                                accept="image/*" onchange="previewImage(this)">
                            <small class="text-muted">Formats acceptés : JPG, PNG, JPEG. Max : 2MB</small>
                            <div id="image-preview" style="margin-top: 10px; display: none;">
                                <img id="preview"
                                    style="max-width: 150px; max-height: 150px; border-radius: 10px; border: 2px solid var(--light-gray);">
                            </div>
                            @error('photo_profile')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-user-plus"></i> Créer mon compte
                    </button>
                </form>
            </div>
        </div>

        <div class="auth-footer">
            <p>Vous avez déjà un compte ? <a href="{{ route('login') }}">Se connecter</a></p>
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
                                <li style="text-align:center">{{ $error }}</li>
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
