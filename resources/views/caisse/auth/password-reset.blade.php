<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe Caissier - Car 225</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="shortcut icon" href="{{asset('assetsPoster/assets/images/logo_car225.png')}}" />
    <style>
        :root {
            --primary: #e94f1b;
            --primary-dark: #cc3f12;
            --secondary: #28a745;
            --light: #f8f9fa;
            --dark: #28a745;
            --white: #ffffff;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --border-radius: 16px;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0;
            background: linear-gradient(rgba(255, 255, 255, 0.1), rgba(40, 167, 69, 0.8)), url('{{ asset('assets/assets/img/arrierep.jpg') }}');
            background-size: cover; background-position: center; padding: 20px;
        }
        .reset-container { width: 100%; max-width: 500px; }
        .reset-card { background: rgba(255, 255, 255, 0.95); border-radius: var(--border-radius); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); backdrop-filter: blur(10px); overflow: hidden; }
        .reset-header { background: linear-gradient(to right, #28a745, #1e7e34); color: var(--white); padding: 30px; text-align: center; }
        .steps-progress { display: flex; justify-content: space-between; padding: 25px 30px 15px; position: relative; }
        .steps-progress::before { content: ''; position: absolute; top: 47px; left: 45px; right: 45px; height: 2px; background: var(--light-gray); z-index: 0; }
        .step-item { display: flex; flex-direction: column; align-items: center; position: relative; z-index: 1; flex: 1; }
        .step-number { width: 40px; height: 40px; border-radius: 50%; background: var(--light-gray); color: var(--gray); display: flex; align-items: center; justify-content: center; font-weight: 600; margin-bottom: 8px; }
        .step-item.active .step-number { background: #28a745; color: var(--white); }
        .step-item.completed .step-number { background: var(--secondary); color: var(--white); }
        .step-label { font-size: 11px; color: var(--gray); font-weight: 500; text-align: center; text-transform: uppercase; }
        .reset-body { padding: 10px 40px 40px; }
        .step-content { display: none; }
        .step-content.active { display: block; animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .form-group { margin-bottom: 20px; position: relative; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #495057; font-size: 13px; text-transform: uppercase; }
        .form-control { width: 100%; padding: 14px 16px; border: 2px solid #e9ecef; border-radius: 12px; font-size: 15px; background: #f8f9fa; }
        .otp-inputs { display: flex; gap: 10px; justify-content: center; margin: 25px 0; }
        .otp-input { width: 45px; height: 55px; text-align: center; font-size: 22px; font-weight: 700; border: 2px solid #e9ecef; border-radius: 12px; background: #f8f9fa; }
        .btn { display: block; width: 100%; padding: 16px; border: none; border-radius: 12px; font-size: 15px; font-weight: 600; cursor: pointer; text-align: center; text-decoration: none; text-transform: uppercase; }
        .btn-primary { background: linear-gradient(to right, #28a745, #1e7e34); color: var(--white); }
        .btn-back { background: #e9ecef; color: #495057; margin-top: 12px; }
        .back-to-login { text-align: center; margin-top: 25px; padding-top: 20px; border-top: 1px dotted #dee2e6; }
        .back-to-login a { color: #28a745; text-decoration: none; font-size: 14px; font-weight: 600; }
        .password-toggle { position: absolute; right: 16px; top: 40px; cursor: pointer; color: #adb5bd; }
    </style>
</head>

<body>
    <div class="reset-container">
        <div class="reset-card">
            <div class="reset-header">
                <h1><i class="fas fa-cash-register"></i> Accès Caissier</h1>
                <p>Réinitialisation du compte caisse</p>
            </div>
            <div class="steps-progress">
                <div class="step-item active" id="step-indicator-1">
                    <div class="step-number">1</div>
                    <div class="step-label">Email</div>
                </div>
                <div class="step-item" id="step-indicator-2">
                    <div class="step-number">2</div>
                    <div class="step-label">OTP</div>
                </div>
                <div class="step-item" id="step-indicator-3">
                    <div class="step-number">3</div>
                    <div class="step-label">Password</div>
                </div>
            </div>
            <div class="reset-body">
                <div class="step-content active" id="step-1">
                    <form id="emailForm">
                        <div class="form-group">
                            <label for="email">Email Caissier</label>
                            <input type="email" id="email" class="form-control" placeholder="Entrez votre email" required>
                        </div>
                        <button type="submit" class="btn btn-primary" id="sendOtpBtn">Recevoir le code</button>
                    </form>
                    <div class="back-to-login"><a href="{{ route('caisse.auth.login') }}"><i class="fas fa-arrow-left"></i> Retour</a></div>
                </div>
                <div class="step-content" id="step-2">
                    <form id="otpForm">
                        <div class="otp-inputs">
                            <input type="text" maxlength="1" class="otp-input" id="otp1" required autocomplete="off">
                            <input type="text" maxlength="1" class="otp-input" id="otp2" required autocomplete="off">
                            <input type="text" maxlength="1" class="otp-input" id="otp3" required autocomplete="off">
                            <input type="text" maxlength="1" class="otp-input" id="otp4" required autocomplete="off">
                            <input type="text" maxlength="1" class="otp-input" id="otp5" required autocomplete="off">
                            <input type="text" maxlength="1" class="otp-input" id="otp6" required autocomplete="off">
                        </div>
                        <button type="submit" class="btn btn-primary" id="verifyOtpBtn">Vérifier</button>
                        <button type="button" class="btn btn-back" onclick="goToStep(1)">RETOUR</button>
                    </form>
                </div>
                <div class="step-content" id="step-3">
                    <form id="passwordForm">
                        <div class="form-group">
                            <label for="password">Nouveau mot de passe</label>
                            <input type="password" id="password" class="form-control" placeholder="8+ caractères" required>
                            <span class="password-toggle" onclick="togglePassword('password')"><i class="fas fa-eye"></i></span>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirmation</label>
                            <input type="password" id="password_confirmation" class="form-control" placeholder="Confirmer" required>
                            <span class="password-toggle" onclick="togglePassword('password_confirmation')"><i class="fas fa-eye"></i></span>
                        </div>
                        <button type="submit" class="btn btn-primary" id="resetPasswordBtn">Réinitialiser</button>
                        <button type="button" class="btn btn-back" onclick="goToStep(2)">RETOUR</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        let userEmail = ''; let otpCode = '';
        function goToStep(step) {
            document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.step-item').forEach(el => el.classList.remove('active', 'completed'));
            document.getElementById('step-' + step).classList.add('active');
            document.getElementById('step-indicator-' + step).classList.add('active');
            for (let i = 1; i < step; i++) {
                document.getElementById('step-indicator-' + i).classList.add('completed');
                document.getElementById('step-indicator-' + i).querySelector('.step-number').innerHTML = '<i class="fas fa-check"></i>';
            }
        }
        document.getElementById('emailForm').addEventListener('submit', async function(e) {
            e.preventDefault(); const email = document.getElementById('email').value;
            const btn = document.getElementById('sendOtpBtn'); btn.disabled = true; btn.innerHTML = '...';
            try {
                const response = await fetch('{{ route("caisse.password.sendOtp") }}', {
                    method: 'POST', headers: { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                    },
                    body: JSON.stringify({ email })
                });
                const data = await response.json();
                if (data.success) { userEmail = email; goToStep(2); } 
                else { Swal.fire({ icon: 'error', text: data.message }); }
            } catch (e) { Swal.fire({ icon: 'error', text: 'Erreur technique.' }); }
            btn.disabled = false; btn.innerHTML = 'Recevoir le code';
        });
        document.getElementById('otpForm').addEventListener('submit', async function(e) {
            e.preventDefault(); otpCode = ''; for (let i = 1; i <= 6; i++) otpCode += document.getElementById('otp' + i).value;
            const btn = document.getElementById('verifyOtpBtn'); btn.disabled = true;
            try {
                const response = await fetch('{{ route("caisse.password.verifyOtp") }}', {
                    method: 'POST', headers: { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                    },
                    body: JSON.stringify({ email: userEmail, otp: otpCode })
                });
                const data = await response.json();
                if (data.success) { goToStep(3); } else { Swal.fire({ icon: 'error', text: data.message }); }
            } catch (e) { Swal.fire({ icon: 'error', text: 'Erreur.' }); }
            btn.disabled = false;
        });
        document.getElementById('passwordForm').addEventListener('submit', async function(e) {
            e.preventDefault(); const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            if (password !== passwordConfirmation) { Swal.fire({ icon: 'error', text: 'Erreur.' }); return; }
            const btn = document.getElementById('resetPasswordBtn'); btn.disabled = true;
            try {
                const response = await fetch('{{ route("caisse.password.reset") }}', {
                    method: 'POST', headers: { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                    },
                    body: JSON.stringify({ email: userEmail, otp: otpCode, password, password_confirmation: passwordConfirmation })
                });
                const data = await response.json();
                if (data.success) { Swal.fire({ icon: 'success', text: data.message }).then(() => { window.location.href = '{{ route("caisse.auth.login") }}'; }); }
                else { Swal.fire({ icon: 'error', text: data.message }); }
            } catch (e) { Swal.fire({ icon: 'error', text: 'Échec.' }); }
            btn.disabled = false;
        });
        const inputs = document.querySelectorAll('.otp-input');
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => { if (e.target.value && index < 5) inputs[index + 1].focus(); });
            input.addEventListener('keydown', (e) => { if (e.key === 'Backspace' && !e.target.value && index > 0) inputs[index - 1].focus(); });
        });
        function togglePassword(id) {
            const input = document.getElementById(id); const icon = input.nextElementSibling.querySelector('i');
            if (input.type === 'password') { input.type = 'text'; icon.className = 'fas fa-eye-slash'; }
            else { input.type = 'password'; icon.className = 'fas fa-eye'; }
        }
    </script>
</body> </html>
