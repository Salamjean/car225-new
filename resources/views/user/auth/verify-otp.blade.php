<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification OTP - Car 225</title>
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
            --warning: #f59e0b;
            --info: #3b82f6;
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
            position: relative;
            overflow: hidden;
            padding: 60px;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand-video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(145deg, rgba(233,79,27,0.85) 0%, rgba(196,61,16,0.88) 50%, rgba(26,26,46,0.92) 100%);
            z-index: 1;
        }

        .brand-content { position: relative; z-index: 2; }

        .logo-wrapper {
            width: 72px; height: 72px;
            background: rgba(255,255,255,0.95);
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 36px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            backdrop-filter: blur(10px);
            transition: var(--transition);
        }

        .logo-wrapper:hover {
            transform: scale(1.05);
            background: #ffffff;
            box-shadow: 0 12px 32px rgba(0,0,0,0.2);
        }

        .brand-logo { width: 50px; height: 50px; }

        .brand-title {
            font-size: 36px; font-weight: 800; color: white;
            line-height: 1.15; margin-bottom: 16px; letter-spacing: -0.02em;
        }

        .brand-subtitle {
            font-size: 15px; color: rgba(255,255,255,0.7);
            line-height: 1.7; max-width: 360px;
        }

        .brand-features {
            position: relative; z-index: 2;
            display: flex; flex-direction: column; gap: 18px;
        }

        .feature-item {
            display: flex; align-items: center; gap: 16px;
            color: rgba(255,255,255,0.85); font-size: 14px; font-weight: 500;
        }

        .feature-icon {
            width: 42px; height: 42px;
            background: rgba(255,255,255,0.12);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 17px; color: white; flex-shrink: 0;
            backdrop-filter: blur(10px);
        }

        .decoration-circle {
            position: absolute; border-radius: 50%;
            background: rgba(255,255,255,0.05); z-index: 0;
        }
        .decoration-circle:nth-child(1) { width: 300px; height: 300px; top: -100px; right: -80px; }
        .decoration-circle:nth-child(2) { width: 200px; height: 200px; bottom: -50px; left: -60px; }
        .decoration-circle:nth-child(3) { width: 120px; height: 120px; top: 50%; right: 20%; }

        /* ─── RIGHT PANEL ─── */
        .form-panel {
            width: 100%; min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 40px 20px;
        }

        .form-wrapper { width: 100%; max-width: 460px; }

        .mobile-logo { text-align: center; margin-bottom: 28px; }
        .mobile-logo img { width: 52px; height: 52px; margin-bottom: 8px; }
        .mobile-logo-text {
            font-size: 20px; font-weight: 700;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }

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

        .form-header { padding: 36px 36px 0; text-align: center; }
        .form-header h1 {
            font-size: 24px; font-weight: 700; color: var(--text-primary);
            margin-bottom: 8px; letter-spacing: -0.01em;
        }
        .form-header p { font-size: 14px; color: var(--text-secondary); line-height: 1.6; }

        .form-body { padding: 28px 36px 36px; }

        /* OTP Icon */
        .otp-icon-wrapper {
            width: 80px; height: 80px; margin: 0 auto 20px;
            background: linear-gradient(135deg, var(--primary-glow), rgba(255,140,66,0.15));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            animation: pulse 2s ease-in-out infinite;
        }

        .otp-icon-wrapper i {
            font-size: 32px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(233, 79, 27, 0.2); }
            50% { transform: scale(1.05); box-shadow: 0 0 0 15px rgba(233, 79, 27, 0); }
        }

        .phone-display {
            display: inline-block;
            background: var(--bg-input);
            padding: 6px 14px;
            border-radius: var(--radius-sm);
            font-weight: 600;
            color: var(--primary);
            font-size: 15px;
            margin-top: 8px;
            letter-spacing: 0.05em;
        }

        /* OTP Input */
        .otp-inputs {
            display: flex; gap: 10px; justify-content: center; margin-bottom: 24px;
        }

        .otp-input {
            width: 52px; height: 60px;
            text-align: center;
            font-size: 24px; font-weight: 700;
            font-family: 'Inter', sans-serif;
            border: 2px solid var(--border);
            border-radius: var(--radius-md);
            background: var(--bg-input);
            color: var(--text-primary);
            transition: var(--transition);
            outline: none;
        }

        .otp-input:focus {
            border-color: var(--primary);
            background: var(--bg-primary);
            box-shadow: 0 0 0 4px var(--primary-glow);
            transform: translateY(-2px);
        }

        .otp-input.filled {
            border-color: var(--success);
            background: rgba(16, 185, 129, 0.05);
        }

        .otp-input.error {
            border-color: var(--error);
            background: rgba(239, 68, 68, 0.05);
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .error-msg {
            color: var(--error); font-size: 13px;
            text-align: center; margin-bottom: 16px; display: block;
        }

        /* Hidden real input */
        .otp-hidden { position: absolute; opacity: 0; width: 0; height: 0; }

        /* Submit button */
        .btn-submit {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; padding: 14px; border: none; border-radius: var(--radius-md);
            font-size: 15px; font-weight: 600; font-family: 'Inter', sans-serif;
            cursor: pointer; transition: var(--transition);
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white; box-shadow: 0 4px 12px rgba(233, 79, 27, 0.3);
            position: relative; overflow: hidden;
        }
        .btn-submit::after {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.1) 100%);
            opacity: 0; transition: opacity 0.3s;
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(233, 79, 27, 0.35); }
        .btn-submit:hover::after { opacity: 1; }
        .btn-submit:active { transform: translateY(0); }
        .btn-submit:disabled {
            opacity: 0.6; cursor: not-allowed; transform: none;
            box-shadow: 0 2px 6px rgba(233, 79, 27, 0.15);
        }

        /* Resend section */
        .resend-section {
            text-align: center; margin-top: 24px;
            padding-top: 20px; border-top: 1px solid var(--border);
        }

        .resend-text {
            font-size: 14px; color: var(--text-secondary); margin-bottom: 8px;
        }

        .resend-btn {
            display: inline-flex; align-items: center; gap: 6px;
            background: none; border: none;
            color: var(--primary); font-size: 14px; font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer; transition: var(--transition);
            padding: 8px 16px; border-radius: var(--radius-sm);
        }

        .resend-btn:hover:not(:disabled) {
            background: var(--primary-glow); color: var(--primary-dark);
        }

        .resend-btn:disabled {
            color: var(--text-muted); cursor: not-allowed;
        }

        .countdown {
            font-size: 13px; color: var(--text-muted); font-weight: 500;
        }

        /* Timer circle */
        .timer-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: var(--bg-input); padding: 6px 14px;
            border-radius: 20px; font-size: 13px; color: var(--text-secondary);
            margin-top: 8px;
        }

        .timer-badge i { color: var(--warning); }

        /* Footer */
        .form-footer {
            text-align: center; margin-top: 24px;
            font-size: 14px; color: var(--text-secondary);
        }
        .form-footer a {
            color: var(--primary); text-decoration: none; font-weight: 600;
            transition: var(--transition);
        }
        .form-footer a:hover { color: var(--primary-dark); text-decoration: underline; }

        /* SweetAlert */
        .swal2-popup { font-family: 'Inter', sans-serif !important; border-radius: 16px !important; }
        .swal2-confirm { border-radius: 10px !important; padding: 10px 24px !important; font-weight: 600 !important; }

        /* Responsive */
        @media (min-width: 1024px) {
            .brand-panel { display: flex; }
            .form-panel { width: 50%; padding: 40px 60px; }
            .mobile-logo { display: none; }
        }

        @media (max-width: 480px) {
            .form-body { padding: 24px 20px 28px; }
            .form-header { padding: 28px 20px 0; }
            .form-header h1 { font-size: 20px; }
            .otp-input { width: 44px; height: 52px; font-size: 20px; }
            .otp-inputs { gap: 8px; }
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
            <h2 class="brand-title">Vérification<br>de votre<br>numéro.</h2>
            <p class="brand-subtitle">Pour votre sécurité, nous vérifions votre numéro de téléphone par SMS. Saisissez le code reçu pour activer votre compte.</p>
        </div>

        <div class="brand-features">
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                <span>Sécurité renforcée de votre compte</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-sms"></i></div>
                <span>Code envoyé par SMS instantanément</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                <span>Vérification rapide en 10 secondes</span>
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
                    <div class="otp-icon-wrapper">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h1>Vérification OTP 🔐</h1>
                    <p>Un code de vérification à 6 chiffres a été envoyé à</p>
                    <div class="phone-display">
                        <i class="fas fa-phone" style="font-size: 12px;"></i>
                        {{ $maskedPhone }}
                    </div>
                </div>

                <div class="form-body">
                    <form method="POST" action="{{ route('user.verify-otp.submit') }}" id="otpForm">
                        @csrf
                        <input type="hidden" name="otp" id="otpHidden">

                        <!-- OTP Inputs visuels -->
                        <div class="otp-inputs" id="otpInputs">
                            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="one-time-code" data-index="0">
                            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="1">
                            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="2">
                            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="3">
                            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="4">
                            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="5">
                        </div>

                        @error('otp')
                            <span class="error-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                        @enderror

                        <div class="timer-badge" id="timerBadge" style="display: flex; justify-content: center;">
                            <i class="fas fa-clock"></i>
                            <span id="timerText">Code valide pendant 10:00</span>
                        </div>

                        <button type="submit" class="btn-submit" id="verifyBtn" disabled style="margin-top: 20px;">
                            <i class="fas fa-check-circle"></i> Vérifier le code
                        </button>
                    </form>

                    <div class="resend-section">
                        <p class="resend-text">Vous n'avez pas reçu le code ?</p>
                        <form method="POST" action="{{ route('user.resend-otp') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="resend-btn" id="resendBtn" disabled>
                                <i class="fas fa-redo"></i>
                                <span id="resendText">Renvoyer dans <span id="resendCountdown">60</span>s</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <p><a href="{{ route('user.register') }}"><i class="fas fa-arrow-left"></i> Retour à l'inscription</a></p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.otp-input');
            const hiddenInput = document.getElementById('otpHidden');
            const verifyBtn = document.getElementById('verifyBtn');
            const resendBtn = document.getElementById('resendBtn');
            const resendText = document.getElementById('resendText');
            const resendCountdown = document.getElementById('resendCountdown');
            const timerText = document.getElementById('timerText');

            // OTP Input Logic
            inputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    const val = e.target.value.replace(/[^0-9]/g, '');
                    e.target.value = val;

                    if (val) {
                        e.target.classList.add('filled');
                        e.target.classList.remove('error');
                        if (index < inputs.length - 1) {
                            inputs[index + 1].focus();
                        }
                    } else {
                        e.target.classList.remove('filled');
                    }

                    updateOtpValue();
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        inputs[index - 1].focus();
                        inputs[index - 1].value = '';
                        inputs[index - 1].classList.remove('filled');
                        updateOtpValue();
                    }
                });

                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pasted = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
                    pasted.split('').forEach((char, i) => {
                        if (inputs[i]) {
                            inputs[i].value = char;
                            inputs[i].classList.add('filled');
                        }
                    });
                    if (pasted.length > 0) {
                        const focusIndex = Math.min(pasted.length, inputs.length - 1);
                        inputs[focusIndex].focus();
                    }
                    updateOtpValue();
                });

                input.addEventListener('focus', function() {
                    this.select();
                });
            });

            // Focus first input
            inputs[0].focus();

            function updateOtpValue() {
                let otp = '';
                inputs.forEach(input => otp += input.value);
                hiddenInput.value = otp;
                verifyBtn.disabled = otp.length !== 6;
            }

            // Resend countdown (60s)
            let resendSeconds = 60;
            const resendTimer = setInterval(() => {
                resendSeconds--;
                resendCountdown.textContent = resendSeconds;
                if (resendSeconds <= 0) {
                    clearInterval(resendTimer);
                    resendBtn.disabled = false;
                    resendText.innerHTML = '<i class="fas fa-redo"></i> Renvoyer le code';
                }
            }, 1000);

            // OTP expiry timer (10min)
            let expirySeconds = 600;
            const expiryTimer = setInterval(() => {
                expirySeconds--;
                const minutes = Math.floor(expirySeconds / 60);
                const seconds = expirySeconds % 60;
                timerText.textContent = `Code valide pendant ${minutes}:${seconds.toString().padStart(2, '0')}`;
                if (expirySeconds <= 0) {
                    clearInterval(expiryTimer);
                    timerText.textContent = 'Code expiré — renvoyez un nouveau code';
                    timerText.style.color = 'var(--error)';
                    verifyBtn.disabled = true;
                }
            }, 1000);

            // SweetAlert messages
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Succès !',
                    text: {!! json_encode(session('success')) !!},
                    confirmButtonColor: '#e94f1b',
                    confirmButtonText: 'OK',
                });
            @endif

            @if (session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Attention',
                    text: {!! json_encode(session('warning')) !!},
                    confirmButtonColor: '#e94f1b',
                    confirmButtonText: 'OK',
                });
            @endif

            @if (session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Information',
                    text: {!! json_encode(session('info')) !!},
                    confirmButtonColor: '#e94f1b',
                    confirmButtonText: 'OK',
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: {!! json_encode(session('error')) !!},
                    confirmButtonColor: '#e94f1b',
                    confirmButtonText: 'Compris',
                });
            @endif

            @if ($errors->any())
                inputs.forEach(input => input.classList.add('error'));
                Swal.fire({
                    icon: 'error',
                    title: 'Code invalide',
                    html: `
                        <ul style="text-align: left; padding-left: 20px; list-style: none;">
                            @foreach ($errors->all() as $error)
                                <li style="margin-bottom: 4px;">• {{ $error }}</li>
                            @endforeach
                        </ul>
                    `,
                    confirmButtonColor: '#e94f1b',
                    confirmButtonText: 'Réessayer'
                });
            @endif
        });
    </script>
</body>
</html>
