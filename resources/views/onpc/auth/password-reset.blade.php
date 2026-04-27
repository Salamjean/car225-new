<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation ONPC — CAR 225</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --primary: #1e3a8a; --secondary: #1e40af; --light: #f8f9fa; --white: #ffffff; --gray: #6c757d; --light-gray: #e9ecef; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; background: linear-gradient(rgba(255,255,255,0.05), rgba(30,58,138,0.95)); padding: 20px; }
        .reset-container { width: 100%; max-width: 500px; }
        .reset-card { background: rgba(255,255,255,0.97); border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden; }
        .reset-header { background: linear-gradient(to right, #0f172a, #1e3a8a); color: var(--white); padding: 30px; text-align: center; }
        .steps-progress { display: flex; justify-content: space-between; padding: 25px 30px 15px; position: relative; }
        .steps-progress::before { content: ''; position: absolute; top: 47px; left: 45px; right: 45px; height: 2px; background: var(--light-gray); z-index: 0; }
        .step-item { display: flex; flex-direction: column; align-items: center; position: relative; z-index: 1; flex: 1; }
        .step-number { width: 40px; height: 40px; border-radius: 50%; background: var(--light-gray); color: var(--gray); display: flex; align-items: center; justify-content: center; font-weight: 600; margin-bottom: 8px; }
        .step-item.active .step-number { background: var(--primary); color: var(--white); }
        .step-item.completed .step-number { background: #10b981; color: var(--white); }
        .step-label { font-size: 11px; color: var(--gray); font-weight: 500; text-transform: uppercase; }
        .reset-body { padding: 10px 40px 40px; }
        .step-content { display: none; }
        .step-content.active { display: block; animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .form-group { margin-bottom: 20px; position: relative; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #495057; font-size: 13px; text-transform: uppercase; }
        .form-control { width: 100%; padding: 14px 16px; border: 2px solid #e9ecef; border-radius: 12px; font-size: 15px; background: #f8f9fa; }
        .otp-inputs { display: flex; gap: 10px; justify-content: center; margin: 25px 0; }
        .otp-input { width: 45px; height: 55px; text-align: center; font-size: 22px; font-weight: 700; border: 2px solid #e9ecef; border-radius: 12px; background: #f8f9fa; }
        .btn { display: block; width: 100%; padding: 16px; border: none; border-radius: 12px; font-size: 15px; font-weight: 600; cursor: pointer; text-transform: uppercase; }
        .btn-primary { background: linear-gradient(to right, var(--primary), var(--secondary)); color: var(--white); }
        .btn-back { background: #e9ecef; color: #495057; margin-top: 12px; }
        .back-to-login { text-align: center; margin-top: 25px; padding-top: 20px; border-top: 1px dotted #dee2e6; }
        .back-to-login a { color: var(--primary); text-decoration: none; font-size: 14px; font-weight: 600; }
        .password-toggle { position: absolute; right: 16px; top: 40px; cursor: pointer; color: #adb5bd; }
    </style>
</head>

<body>
    <div class="reset-container">
        <div class="reset-card">
            <div class="reset-header">
                <h1><i class="fas fa-shield-alt"></i> Espace ONPC</h1>
                <p>Réinitialisation du mot de passe</p>
            </div>
            <div class="steps-progress">
                <div class="step-item active" id="step-indicator-1"><div class="step-number">1</div><div class="step-label">Email</div></div>
                <div class="step-item" id="step-indicator-2"><div class="step-number">2</div><div class="step-label">OTP</div></div>
                <div class="step-item" id="step-indicator-3"><div class="step-number">3</div><div class="step-label">Mot de passe</div></div>
            </div>
            <div class="reset-body">
                <div class="step-content active" id="step-1">
                    <form id="emailForm">
                        <div class="form-group">
                            <label>Email ONPC</label>
                            <input type="email" id="email" class="form-control" placeholder="agent@onpc.ci" required>
                        </div>
                        <button type="submit" class="btn btn-primary" id="sendOtpBtn">Recevoir le code</button>
                    </form>
                    <div class="back-to-login"><a href="{{ route('onpc.login') }}"><i class="fas fa-arrow-left"></i> Retour à la connexion</a></div>
                </div>
                <div class="step-content" id="step-2">
                    <form id="otpForm">
                        <div class="otp-inputs">
                            @for($i = 1; $i <= 6; $i++)
                                <input type="text" maxlength="1" class="otp-input" id="otp{{ $i }}" required autocomplete="off">
                            @endfor
                        </div>
                        <button type="submit" class="btn btn-primary" id="verifyOtpBtn">Vérifier</button>
                        <button type="button" class="btn btn-back" onclick="goToStep(1)">Retour</button>
                    </form>
                </div>
                <div class="step-content" id="step-3">
                    <form id="passwordForm">
                        <div class="form-group">
                            <label>Nouveau mot de passe</label>
                            <input type="password" id="password" class="form-control" placeholder="8+ caractères" required minlength="8">
                            <span class="password-toggle" onclick="togglePassword('password')"><i class="fas fa-eye"></i></span>
                        </div>
                        <div class="form-group">
                            <label>Confirmation</label>
                            <input type="password" id="password_confirmation" class="form-control" required minlength="8">
                            <span class="password-toggle" onclick="togglePassword('password_confirmation')"><i class="fas fa-eye"></i></span>
                        </div>
                        <button type="submit" class="btn btn-primary" id="resetPasswordBtn">Réinitialiser</button>
                        <button type="button" class="btn btn-back" onclick="goToStep(2)">Retour</button>
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
            e.preventDefault();
            const email = document.getElementById('email').value;
            const btn = document.getElementById('sendOtpBtn'); btn.disabled = true; btn.innerHTML = 'Envoi…';
            try {
                const resp = await fetch('{{ route("onpc.password.sendOtp") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ email })
                });
                const data = await resp.json();
                if (data.success) { userEmail = email; goToStep(2); }
                else { Swal.fire({ icon: 'error', text: data.message }); }
            } catch (e) { Swal.fire({ icon: 'error', text: 'Erreur technique.' }); }
            btn.disabled = false; btn.innerHTML = 'Recevoir le code';
        });
        document.getElementById('otpForm').addEventListener('submit', async function(e) {
            e.preventDefault(); otpCode = '';
            for (let i = 1; i <= 6; i++) otpCode += document.getElementById('otp' + i).value;
            const btn = document.getElementById('verifyOtpBtn'); btn.disabled = true;
            try {
                const resp = await fetch('{{ route("onpc.password.verifyOtp") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ email: userEmail, otp: otpCode })
                });
                const data = await resp.json();
                if (data.success) { goToStep(3); } else { Swal.fire({ icon: 'error', text: data.message }); }
            } catch (e) { Swal.fire({ icon: 'error', text: 'Erreur technique.' }); }
            btn.disabled = false;
        });
        document.getElementById('passwordForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const password = document.getElementById('password').value;
            const password_confirmation = document.getElementById('password_confirmation').value;
            if (password !== password_confirmation) { Swal.fire({ icon: 'error', text: 'Les mots de passe ne correspondent pas.' }); return; }
            const btn = document.getElementById('resetPasswordBtn'); btn.disabled = true;
            try {
                const resp = await fetch('{{ route("onpc.password.reset") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ email: userEmail, otp: otpCode, password, password_confirmation })
                });
                const data = await resp.json();
                if (data.success) {
                    Swal.fire({ icon: 'success', text: data.message }).then(() => { window.location.href = '{{ route("onpc.login") }}'; });
                } else { Swal.fire({ icon: 'error', text: data.message }); }
            } catch (e) { Swal.fire({ icon: 'error', text: 'Échec de la réinitialisation.' }); }
            btn.disabled = false;
        });
        const inputs = document.querySelectorAll('.otp-input');
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => { if (e.target.value && index < 5) inputs[index + 1].focus(); });
            input.addEventListener('keydown', (e) => { if (e.key === 'Backspace' && !e.target.value && index > 0) inputs[index - 1].focus(); });
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const text = e.clipboardData.getData('text').slice(0, 6);
                if (/^\d+$/.test(text)) for (let i = 0; i < text.length; i++) {
                    if (inputs[index + i]) { inputs[index + i].value = text[i]; if (index + i < 5) inputs[index + i + 1].focus(); }
                }
            });
        });
        function togglePassword(id) {
            const input = document.getElementById(id); const icon = input.nextElementSibling.querySelector('i');
            if (input.type === 'password') { input.type = 'text'; icon.className = 'fas fa-eye-slash'; }
            else { input.type = 'password'; icon.className = 'fas fa-eye'; }
        }
    </script>
</body>
</html>
