<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="shortcut icon" href="{{ asset('assetsPoster/assets/images/logo_car225.png') }}" />
    <title>Gare - Vérification OTP</title>

<style>
  :root {
    --primary-color: #e94f1b;
    --light-color: #ffffff;
    --dark-color: #212529;
    --error-color: #dc3545;
    --success-color: #28a745;
    --transition-speed: 0.3s;
  }

  * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

  body {
    display: flex; align-items: center; justify-content: center;
    min-height: 100vh; margin: 0;
    background: linear-gradient(rgba(255, 255, 255, 0.1), rgba(233,79,27, 0.9));
    padding: 20px;
  }

  .otp-container {
    background-color: rgba(255, 255, 255, 0.95);
    padding: 40px; width: 100%; max-width: 500px;
    border-radius: 16px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    text-align: center;
  }

  .otp-header { margin-bottom: 30px; }
  .otp-header img { height: 80px; margin-bottom: 1rem; }
  .otp-header h2 { color: var(--dark-color); font-size: 1.5rem; margin-bottom: 0.5rem; }
  .otp-header p { color: #6c757d; font-size: 0.9rem; }
  .otp-header .email-highlight { color: var(--primary-color); font-weight: 600; }

  .otp-input-group {
    display: flex; gap: 10px; justify-content: center; margin: 30px 0;
  }

  .otp-digit {
    width: 50px; height: 60px; text-align: center;
    font-size: 1.5rem; font-weight: 700;
    border: 2px solid #e9ecef; border-radius: 12px;
    outline: none; transition: all var(--transition-speed) ease;
  }

  .otp-digit:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(233, 79, 27, 0.2);
  }

  .submit-btn {
    width: 100%; height: 55px;
    background: linear-gradient(to right, var(--primary-color), var(--primary-color));
    border: none; color: white; font-size: 1.1rem; font-weight: 600;
    border-radius: 12px; cursor: pointer;
    transition: all var(--transition-speed) ease;
    margin-top: 20px;
  }

  .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15); }

  .error-message { color: var(--error-color); font-size: 0.85rem; margin-top: 10px; }
  .success-message { color: var(--success-color); margin-bottom: 15px; font-weight: 500; }

  .floating { animation: floating 3s ease-in-out infinite; }
  @keyframes floating {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
    100% { transform: translateY(0px); }
  }

  @media (max-width: 576px) {
    .otp-container { padding: 30px 20px; }
    .otp-digit { width: 42px; height: 50px; font-size: 1.2rem; }
  }
</style>
</head>
<body>
<div class="otp-container animate__animated animate__fadeIn">
    <div class="otp-header">
        <img src="{{ asset('assetsPoster/assets/images/logo_car225.png') }}" class="floating" alt="Logo">
        <h2>Vérification OTP</h2>
        <p>Un code de vérification a été envoyé à<br>
            <span class="email-highlight">{{ $email }}</span>
        </p>
    </div>

    @if (Session::get('error'))
        <div class="error-message animate__animated animate__shakeX">
            <i class="fas fa-exclamation-circle"></i> {{ Session::get('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('gare-espace.handleVerifyOtp') }}">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <input type="hidden" name="otp" id="otp-hidden">

        <div class="otp-input-group">
            <input type="text" class="otp-digit" maxlength="1" data-index="0" autofocus>
            <input type="text" class="otp-digit" maxlength="1" data-index="1">
            <input type="text" class="otp-digit" maxlength="1" data-index="2">
            <input type="text" class="otp-digit" maxlength="1" data-index="3">
            <input type="text" class="otp-digit" maxlength="1" data-index="4">
            <input type="text" class="otp-digit" maxlength="1" data-index="5">
        </div>

        @error('otp')
            <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
        @enderror

        <button type="submit" class="submit-btn">
            <i class="fas fa-check-circle"></i> Vérifier le code
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const digits = document.querySelectorAll('.otp-digit');
    const hiddenInput = document.getElementById('otp-hidden');

    function updateHiddenInput() {
        let otp = '';
        digits.forEach(d => otp += d.value);
        hiddenInput.value = otp;
    }

    digits.forEach((digit, index) => {
        digit.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1);
            updateHiddenInput();
            if (this.value && index < digits.length - 1) {
                digits[index + 1].focus();
            }
        });

        digit.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && index > 0) {
                digits[index - 1].focus();
            }
        });

        digit.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
            pastedData.split('').forEach((char, i) => {
                if (digits[i]) digits[i].value = char;
            });
            updateHiddenInput();
            if (digits[pastedData.length - 1]) digits[pastedData.length - 1].focus();
        });
    });

    @if(Session::has('error'))
        Swal.fire({ icon: 'error', title: 'Erreur', text: '{{ Session::get('error') }}', confirmButtonColor: '#e94f1b' });
    @endif
});
</script>
</body>
</html>
