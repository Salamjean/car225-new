<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Réinitialisation de mot de passe - Car 225</title>
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
            background: linear-gradient(135deg, rgba(50, 71, 102, 0.85) 0%, rgba(233, 79, 27, 0.75) 100%), 
                        url('{{ asset('assets/images/login_bg.png') }}') center/cover no-repeat fixed;
            color: var(--slate-blue);
            padding: 20px;
            overflow: hidden;
        }

        .reset-wrapper {
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
        }

        .header {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo-box {
            width: 70px; height: 70px;
            margin: 0 auto 20px;
            background: #ffffff;
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .logo-box img { width: 45px; }

        .title {
            font-size: 24px;
            font-weight: 950;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .subtitle {
            font-size: 14px;
            opacity: 0.7;
            font-weight: 600;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
        }
        .step-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #CBD5E1;
            transition: var(--transition);
        }
        .step-dot.active {
            background: var(--primary-orange);
            width: 24px;
            border-radius: 4px;
        }

        .input-group { margin-bottom: 20px; }
        .input-group label {
            display: block; font-size: 11px; font-weight: 900;
            text-transform: uppercase; letter-spacing: 1px;
            margin-bottom: 8px; opacity: 0.8;
        }
        .input-field { position: relative; }
        .input-field i {
            position: absolute; left: 18px; top: 50%; transform: translateY(-50%);
            color: var(--slate-blue); opacity: 0.4; font-size: 16px;
        }
        .input-field input {
            width: 100%; padding: 16px 20px 16px 50px;
            background: #ffffff; border: 2px solid transparent;
            border-radius: 18px; font-size: 15px; font-weight: 700;
            color: var(--slate-blue); transition: var(--transition);
        }
        .input-field input:focus {
            border-color: var(--primary-orange);
            outline: none;
            box-shadow: 0 10px 25px rgba(233, 79, 27, 0.1);
        }

        .submit-btn {
            width: 100%; padding: 16px;
            background: var(--slate-blue); color: white;
            border: none; border-radius: 18px;
            font-size: 15px; font-weight: 900;
            cursor: pointer; transition: var(--transition);
            display: flex; align-items: center; justify-content: center; gap: 10px;
            text-transform: uppercase;
        }
        .submit-btn:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(50, 71, 102, 0.3); }

        .loader {
            display: none;
            width: 20px; height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top: 3px solid #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .stagger { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .swal2-glass {
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(20px) !important;
            border-radius: 36px !important;
        }
    </style>
</head>
<body>

    <div class="reset-wrapper">
        <a href="{{ route('portail.login') }}" class="back-btn stagger" style="animation-delay: 0.1s">
            <i class="fas fa-arrow-left"></i> Retour à la connexion
        </a>

        <div class="glass-card stagger" style="animation-delay: 0.2s">
            <div class="header">
                <div class="logo-box">
                    <img src="{{ asset('assetsPoster/assets/images/logo_car225.png') }}" alt="Logo">
                </div>
                <h1 class="title" id="page-title">Réinitialisation</h1>
                <p class="subtitle" id="page-subtitle">Suivez les étapes pour retrouver l'accès.</p>
            </div>

            <div class="step-indicator">
                <div class="step-dot active" id="dot-1"></div>
                <div class="step-dot" id="dot-2"></div>
                <div class="step-dot" id="dot-3"></div>
            </div>

            <!-- Étape 1 : Identification -->
            <div id="step-1" class="stagger">
                <div class="input-group">
                    <label>Identifiant ou Email</label>
                    <div class="input-field">
                        <i class="fas fa-id-card"></i>
                        <input type="text" id="identity" placeholder="Ex: email@test.com ou HTS-000000">
                    </div>
                </div>
                <button type="button" class="submit-btn" id="btn-1" onclick="handleSendOtp()">
                    <span class="btn-text">Envoyer le code</span>
                    <div class="loader"></div>
                </button>
            </div>

            <!-- Étape 2 : OTP -->
            <div id="step-2" style="display: none;" class="stagger">
                <div class="input-group">
                    <label>Code de vérification (6 chiffres)</label>
                    <div class="input-field">
                        <i class="fas fa-shield-alt"></i>
                        <input type="text" id="otp" maxlength="6" placeholder="Saisir le code">
                    </div>
                </div>
                <button type="button" class="submit-btn" id="btn-2" onclick="handleVerifyOtp()">
                    <span class="btn-text">Vérifier le code</span>
                    <div class="loader"></div>
                </button>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="javascript:void(0)" onclick="goToStep(1)" style="font-size: 13px; color: var(--slate-blue); font-weight: 700; text-decoration: none; opacity: 0.6;">Modifier l'identifiant</a>
                </div>
            </div>

            <!-- Étape 3 : Nouveau mot de passe -->
            <div id="step-3" style="display: none;" class="stagger">
                <div class="input-group">
                    <label>Nouveau mot de passe</label>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" placeholder="••••••••">
                    </div>
                </div>
                <div class="input-group">
                    <label>Confirmer le mot de passe</label>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password_confirmation" placeholder="••••••••">
                    </div>
                </div>
                <button type="button" class="submit-btn" id="btn-3" onclick="handleReset()">
                    <span class="btn-text">Réinitialiser</span>
                    <div class="loader"></div>
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentUserIdentifier = '';
        let currentOtp = '';

        function goToStep(step) {
            document.getElementById('step-1').style.display = step === 1 ? 'block' : 'none';
            document.getElementById('step-2').style.display = step === 2 ? 'block' : 'none';
            document.getElementById('step-3').style.display = step === 3 ? 'block' : 'none';

            document.getElementById('dot-1').classList.toggle('active', step === 1);
            document.getElementById('dot-2').classList.toggle('active', step === 2);
            document.getElementById('dot-3').classList.toggle('active', step === 3);

            if(step === 1) {
                document.getElementById('page-subtitle').innerText = "Suivez les étapes pour retrouver l'accès.";
            } else if(step === 2) {
                document.getElementById('page-subtitle').innerText = "Entrez le code reçu par Email ou SMS.";
            } else if(step === 3) {
                document.getElementById('page-subtitle').innerText = "Définissez votre nouveau mot de passe.";
            }
        }

        async function postData(url, data) {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });
            return response.json();
        }

        function toggleLoading(btnId, isLoading) {
            const btn = document.getElementById(btnId);
            const text = btn.querySelector('.btn-text');
            const loader = btn.querySelector('.loader');
            btn.disabled = isLoading;
            text.style.display = isLoading ? 'none' : 'inline';
            loader.style.display = isLoading ? 'block' : 'none';
        }

        async function handleSendOtp() {
            const identity = document.getElementById('identity').value;
            if(!identity) return Swal.fire({ icon: 'warning', title: 'Attention', text: 'Veuillez saisir votre identifiant.' });

            toggleLoading('btn-1', true);
            try {
                const res = await postData("{{ route('portail.password.sendOtp') }}", { identity });
                if(res.success) {
                    currentUserIdentifier = res.identifier;
                    Swal.fire({ icon: 'success', title: 'Code envoyé', text: res.message });
                    goToStep(2);
                } else {
                    Swal.fire({ icon: 'error', title: 'Erreur', text: res.message });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Erreur', text: 'Une erreur technique est survenue.' });
            } finally {
                toggleLoading('btn-1', false);
            }
        }

        async function handleVerifyOtp() {
            const otp = document.getElementById('otp').value;
            if(otp.length !== 6) return Swal.fire({ icon: 'warning', title: 'Code incomplet', text: 'Le code doit contenir 6 chiffres.' });

            toggleLoading('btn-2', true);
            try {
                const res = await postData("{{ route('portail.password.verifyOtp') }}", { 
                    identifier: currentUserIdentifier,
                    otp: otp 
                });
                if(res.success) {
                    currentOtp = otp;
                    goToStep(3);
                } else {
                    Swal.fire({ icon: 'error', title: 'Code incorrect', text: res.message });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Erreur', text: 'Une erreur technique est survenue.' });
            } finally {
                toggleLoading('btn-2', false);
            }
        }

        async function handleReset() {
            const password = document.getElementById('password').value;
            const password_confirmation = document.getElementById('password_confirmation').value;

            if(password.length < 8) return Swal.fire({ icon: 'warning', title: 'Mot de passe court', text: '8 caractères minimum.' });
            if(password !== password_confirmation) return Swal.fire({ icon: 'warning', title: 'Mismatch', text: 'Les mots de passe ne correspondent pas.' });

            toggleLoading('btn-3', true);
            try {
                const res = await postData("{{ route('portail.password.reset') }}", { 
                    identifier: currentUserIdentifier,
                    otp: currentOtp,
                    password: password,
                    password_confirmation: password_confirmation
                });
                if(res.success) {
                    await Swal.fire({ icon: 'success', title: 'Succès', text: res.message });
                    window.location.href = "{{ route('portail.login') }}";
                } else {
                    Swal.fire({ icon: 'error', title: 'Erreur', text: res.message });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Erreur', text: 'Une erreur technique est survenue.' });
            } finally {
                toggleLoading('btn-3', false);
            }
        }
    </script>
</body>
</html>
