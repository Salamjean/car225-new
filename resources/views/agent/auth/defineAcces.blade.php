<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <link rel="shortcut icon" href="{{asset('assetsPoster/assets/images/logo_car225.png')}}" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Agent - Login</title>
</head>

<style>
  :root {
    --primary-color: #fea219;
    --primary-light: #fea219;
    --primary-dark: #28a745;
    --light-color: #ffffff;
    --dark-color: #212529;
    --gray-color: #28a745;
    --error-color: #dc3545;
    --success-color: #28a745;
    --transition-speed: 0.3s;
  }

  .auth-container {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    background: 
      linear-gradient(rgba(255,255,255, 0.1), rgba(255,136,0, 0.9));
      /* url('{{ asset('assets/assets/img/arrierep.jpg') }}'); */
    background-size: cover;
    background-position: center;
    padding: 2rem;
  }

  .auth-card {
    width: 100%;
    max-width: 500px;
    background-color: var(--light-color);
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    transform: translateY(0);
    transition: transform var(--transition-speed) ease, box-shadow var(--transition-speed) ease;
  }

  .auth-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
  }

  .auth-header {
    background-color: var(--primary-color);
    color: var(--light-color);
    padding: 2rem;
    text-align: center;
  }

  .auth-logo {
    height: 80px;
    margin-bottom: 1rem;
  }

  .auth-title {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
  }

  .auth-subtitle {
    font-size: 0.9rem;
    opacity: 0.9;
  }

  .auth-body {
    padding: 2rem;
  }

  .input-group {
    position: relative;
    margin-bottom: 1.5rem;
  }

  .input-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-color);
    transition: all var(--transition-speed) ease;
  }

  .input-field {
    width: 100%;
    height: 50px;
    padding: 0.5rem 1rem 0.5rem 3rem;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    font-size: 1rem;
    transition: all var(--transition-speed) ease;
    background-color: transparent;
  }

  .input-field:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(0, 51, 196, 0.1);
  }

  .input-field:focus ~ .input-icon {
    color: var(--primary-color);
  }

  .password-toggle {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: var(--gray-color);
    transition: all var(--transition-speed) ease;
  }

  .password-toggle:hover {
    color: var(--primary-color);
  }

  .auth-btn {
    width: 100%;
    height: 50px;
    background-color: #fea219;
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-speed) ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
  }

  .auth-btn:hover {
    background-color: #fc9700;
    transform: translateY(-2px);
  }

  .auth-btn:active {
    transform: translateY(0);
  }

  .error-message {
    color: var(--error-color);
    font-size: 0.85rem;
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .success-message {
    color: var(--success-color);
    text-align: center;
    margin-bottom: 1.5rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
  }

  @media (max-width: 576px) {
    .auth-container {
      padding: 1rem;
    }
    
    .auth-card {
      border-radius: 12px;
    }
    
    .auth-header, .auth-body {
      padding: 1.5rem;
    }
  }

  /* Animations */
  @keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
    100% { transform: translateY(0px); }
  }

  .floating {
    animation: float 3s ease-in-out infinite;
  }
</style>
<body>
<div class="auth-container">
  <div class="auth-card animate__animated animate__fadeIn">
    <div class="auth-header" >
      <img src="{{ asset('assetsPoster/assets/images/logo_car225.png') }}" class="auth-logo floating" alt="Logo" style="background-color: white">
      <h1 class="auth-title">Finalisation de l'inscription</h1>
      <p class="auth-subtitle">Complétez les informations pour finaliser votre compte</p>
    </div>

    <div class="auth-body">
      <form method="POST" action="{{ route('agent.validate', $email) }}">
        @csrf
        @method('post')

        @if (Session::get('success'))
          <div class="success-message animate__animated animate__bounceIn">
            <i class="fas fa-check-circle"></i> {{ Session::get('success') }}
          </div>
        @endif

        <!-- Email Field -->
        <div class="input-group">
          <i class="fas fa-envelope input-icon"></i>
          <input class="input-field" type="email" name="email" value="{{ $email }}" readonly>
          @error('email')
            <div class="error-message">
              <i class="fas fa-exclamation-circle"></i> {{ $message }}
            </div>
          @enderror
        </div>

        <!-- Code Field -->
        <div class="input-group">
          <i class="fas fa-lock input-icon"></i>
          <input class="input-field" type="text" name="code" placeholder="Code de vérification" value="{{ old('code') }}" required>
          @error('code')
            <div class="error-message">
              <i class="fas fa-exclamation-circle"></i> {{ $message }}
            </div>
          @enderror
        </div>

        <!-- Password Field -->
        <div class="input-group">
          <i class="fas fa-key input-icon"></i>
          <input class="input-field" type="password" name="password" id="password" placeholder="Mot de passe" required>
          <i class="fas fa-eye password-toggle" id="togglePassword"></i>
          @error('password')
            <div class="error-message">
              <i class="fas fa-exclamation-circle"></i> {{ $message }}
            </div>
          @enderror
        </div>

        <!-- Confirm Password Field -->
        <div class="input-group">
          <i class="fas fa-key input-icon"></i>
          <input class="input-field" type="password" name="confirme_password" id="confirmPassword" placeholder="Confirmer le mot de passe" required>
          <i class="fas fa-eye password-toggle" id="toggleConfirmPassword"></i>
          @error('confirme_password')
            <div class="error-message">
              <i class="fas fa-exclamation-circle"></i> {{ $message }}
            </div>
          @enderror
        </div>

        <button type="submit" class="auth-btn animate__animated animate__pulse animate__infinite animate__slower">
          <i class="fas fa-user-plus"></i> Valider l'inscription
        </button>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Gestion des messages flash avec SweetAlert
    @if(Session::has('success1'))
      Swal.fire({
        icon: 'success',
        title: 'Succès',
        text: '{{ Session::get('success1') }}',
        confirmButtonColor: '#0033c4',
        background: 'white'
      });
    @endif

    @if(Session::has('success'))
      Swal.fire({
        icon: 'success',
        title: 'Succès',
        text: '{{ Session::get('success') }}',
        confirmButtonColor: '#0033c4',
        background: 'white'
      });
    @endif

    @if(Session::has('error'))
      Swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: '{{ Session::get('error') }}',
        confirmButtonColor: '#0033c4',
        background: 'white'
      });
    @endif

    // Password toggle functionality
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    
    const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
    const confirmPassword = document.querySelector('#confirmPassword');

    togglePassword.addEventListener('click', function() {
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);
      this.classList.toggle('fa-eye-slash');
    });

    toggleConfirmPassword.addEventListener('click', function() {
      const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
      confirmPassword.setAttribute('type', type);
      this.classList.toggle('fa-eye-slash');
    });

    // Animation au survol de la carte
    const authCard = document.querySelector('.auth-card');
    authCard.addEventListener('mouseenter', () => {
      authCard.classList.add('animate__animated', 'animate__pulse');
    });
    
    authCard.addEventListener('animationend', () => {
      authCard.classList.remove('animate__animated', 'animate__pulse');
    });
  });
</script>
</body>
</html>