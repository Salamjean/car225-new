<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe - Car 225</title>
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

        .reset-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }

        .reset-card {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .reset-header {
            background: linear-gradient(to right, var(--primary), #ffb74d);
            color: var(--white);
            padding: 30px;
            text-align: center;
        }

        .reset-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .reset-header p {
            font-size: 15px;
            opacity: 0.9;
        }

        /* Steps Progress */
        .steps-progress {
            display: flex;
            justify-content: space-between;
            padding: 30px 40px 20px;
            position: relative;
        }

        .steps-progress::before {
            content: '';
            position: absolute;
            top: 52px;
            left: 40px;
            right: 40px;
            height: 3px;
            background: var(--light-gray);
            z-index: 0;
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
            flex: 1;
        }

        .step-number {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--light-gray);
            color: var(--gray);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 8px;
            transition: var(--transition);
        }

        .step-item.active .step-number {
            background: var(--primary);
            color: var(--white);
            box-shadow: 0 0 0 4px rgba(254, 162, 25, 0.2);
        }

        .step-item.completed .step-number {
            background: var(--secondary);
            color: var(--white);
        }

        .step-item.completed .step-number i {
            display: block;
        }

        .step-item .step-number span {
            display: block;
        }

        .step-item.completed .step-number span {
            display: none;
        }

        .step-label {
            font-size: 13px;
            color: var(--gray);
            font-weight: 500;
            text-align: center;
        }

        .step-item.active .step-label {
            color: var(--primary);
        }

        /* Form Container */
        .reset-body {
            padding: 20px 40px 40px;
        }

        .step-content {
            display: none;
        }

        .step-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group {
            margin-bottom: 25px;
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
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(254, 162, 25, 0.15);
        }

        .otp-inputs {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0;
        }

        .otp-input {
            width: 50px;
            height: 55px;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            border: 2px solid var(--light-gray);
            border-radius: 10px;
            transition: var(--transition);
        }

        .otp-input:focus {
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
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover:not(:disabled) {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-back {
            background: var(--light-gray);
            color: var(--dark);
            margin-top: 10px;
        }

        .btn-back:hover {
            background: #d0d4d9;
        }

        .back-to-login {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--light-gray);
        }

        .back-to-login a {
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
        }

        .back-to-login a:hover {
            text-decoration: underline;
        }

        .info-text {
            text-align: center;
            color: var(--gray);
            font-size: 14px;
            margin-bottom: 20px;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 40px;
            cursor: pointer;
            color: var(--gray);
        }

        @media (max-width: 480px) {
            .reset-body {
                padding: 20px;
            }

            .steps-progress {
                padding: 20px;
            }

            .step-label {
                font-size: 11px;
            }

            .otp-input {
                width: 45px;
                height: 50px;
                font-size: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="reset-container">
        <div class="reset-card">
            <div class="reset-header">
                <h1><i class="fas fa-key"></i> Réinitialisation</h1>
                <p>Récupérez l'accès à votre compte</p>
            </div>

            <!-- Steps Progress -->
            <div class="steps-progress">
                <div class="step-item active" id="step-indicator-1">
                    <div class="step-number">
                        <span>1</span>
                        <i class="fas fa-check" style="display: none;"></i>
                    </div>
                    <div class="step-label">Email</div>
                </div>
                <div class="step-item" id="step-indicator-2">
                    <div class="step-number">
                        <span>2</span>
                        <i class="fas fa-check" style="display: none;"></i>
                    </div>
                    <div class="step-label">Code OTP</div>
                </div>
                <div class="step-item" id="step-indicator-3">
                    <div class="step-number">
                        <span>3</span>
                        <i class="fas fa-check" style="display: none;"></i>
                    </div>
                    <div class="step-label">Nouveau mot de passe</div>
                </div>
            </div>

            <div class="reset-body">
                <!-- Step 1: Email -->
                <div class="step-content active" id="step-1">
                    <div class="info-text">
                        <p>Entrez votre adresse email pour recevoir un code de vérification.</p>
                    </div>
                    <form id="emailForm">
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Adresse Email</label>
                            <input type="email" id="email" class="form-control" placeholder="votre@email.com" required>
                        </div>
                        <button type="submit" class="btn btn-primary" id="sendOtpBtn">
                            <i class="fas fa-paper-plane"></i> Envoyer le code
                        </button>
                    </form>
                    <div class="back-to-login">
                        <a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> Retour à la connexion</a>
                    </div>
                </div>

                <!-- Step 2: OTP Verification -->
                <div class="step-content" id="step-2">
                    <div class="info-text">
                        <p>Un code à 6 chiffres a été envoyé à votre email.</p>
                    </div>
                    <form id="otpForm">
                        <div class="otp-inputs">
                            <input type="text" maxlength="1" class="otp-input" id="otp1" required>
                            <input type="text" maxlength="1" class="otp-input" id="otp2" required>
                            <input type="text" maxlength="1" class="otp-input" id="otp3" required>
                            <input type="text" maxlength="1" class="otp-input" id="otp4" required>
                            <input type="text" maxlength="1" class="otp-input" id="otp5" required>
                            <input type="text" maxlength="1" class="otp-input" id="otp6" required>
                        </div>
                        <button type="submit" class="btn btn-primary" id="verifyOtpBtn">
                            <i class="fas fa-check-circle"></i> Vérifier le code
                        </button>
                        <button type="button" class="btn btn-back" onclick="goToStep(1)">
                            <i class="fas fa-arrow-left"></i> Retour
                        </button>
                    </form>
                </div>

                <!-- Step 3: New Password -->
                <div class="step-content" id="step-3">
                    <div class="info-text">
                        <p>Choisissez un nouveau mot de passe sécurisé.</p>
                    </div>
                    <form id="passwordForm">
                        <div class="form-group">
                            <label for="password"><i class="fas fa-lock"></i> Nouveau mot de passe</label>
                            <input type="password" id="password" class="form-control" placeholder="Minimum 8 caractères" required>
                            <span class="password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation"><i class="fas fa-lock"></i> Confirmer le mot de passe</label>
                            <input type="password" id="password_confirmation" class="form-control" placeholder="Répétez le mot de passe" required>
                            <span class="password-toggle" onclick="togglePassword('password_confirmation')">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <button type="submit" class="btn btn-primary" id="resetPasswordBtn">
                            <i class="fas fa-check"></i> Réinitialiser le mot de passe
                        </button>
                        <button type="button" class="btn btn-back" onclick="goToStep(2)">
                            <i class="fas fa-arrow-left"></i> Retour
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let userEmail = '';
        let otpCode = '';

        // Step 1: Send OTP
        document.getElementById('emailForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const btn = document.getElementById('sendOtpBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';

            try {
                const response = await fetch('{{ route("password.sendOtp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ email })
                });

                const data = await response.json();

                if (data.success) {
                    userEmail = email;
                    Swal.fire({
                        icon: 'success',
                        title: 'Code envoyé !',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    goToStep(2);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: data.message
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Une erreur est survenue. Veuillez réessayer.'
                });
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane"></i> Envoyer le code';
            }
        });

        // Step 2: Verify OTP
        document.getElementById('otpForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            otpCode = '';
            for (let i = 1; i <= 6; i++) {
                otpCode += document.getElementById('otp' + i).value;
            }

            if (otpCode.length !== 6) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Veuillez entrer les 6 chiffres du code.'
                });
                return;
            }

            const btn = document.getElementById('verifyOtpBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Vérification...';

            try {
                const response = await fetch('{{ route("password.verifyOtp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ email: userEmail, otp: otpCode })
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Vérifié !',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    goToStep(3);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: data.message
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Une erreur est survenue. Veuillez réessayer.'
                });
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle"></i> Vérifier le code';
            }
        });

        // Step 3: Reset Password
        document.getElementById('passwordForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;

            if (password !== passwordConfirmation) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Les mots de passe ne correspondent pas.'
                });
                return;
            }

            if (password.length < 8) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Le mot de passe doit contenir au moins 8 caractères.'
                });
                return;
            }

            const btn = document.getElementById('resetPasswordBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Réinitialisation...';

            try {
                const response = await fetch('{{ route("password.reset") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ 
                        email: userEmail, 
                        otp: otpCode,
                        password: password,
                        password_confirmation: passwordConfirmation
                    })
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès !',
                        text: data.message,
                        confirmButtonText: 'Se connecter'
                    }).then(() => {
                        window.location.href = '{{ route("login") }}';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: data.message
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Une erreur est survenue. Veuillez réessayer.'
                });
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check"></i> Réinitialiser le mot de passe';
            }
        });

        // Navigation entre steps
        function goToStep(step) {
            // Hide all steps
            document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.step-item').forEach(el => {
                el.classList.remove('active');
                el.classList.remove('completed');
            });

            // Show current step
            document.getElementById('step-' + step).classList.add('active');
            document.getElementById('step-indicator-' + step).classList.add('active');

            // Mark previous steps as completed
            for (let i = 1; i < step; i++) {
                document.getElementById('step-indicator-' + i).classList.add('completed');
            }
        }

        // OTP Input Navigation
        const otpInputs = document.querySelectorAll('.otp-input');
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                if (this.value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value === '' && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });

            // Only allow numbers
            input.addEventListener('keypress', function(e) {
                if (!/[0-9]/.test(e.key)) {
                    e.preventDefault();
                }
            });
        });

        // Password toggle
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html>
