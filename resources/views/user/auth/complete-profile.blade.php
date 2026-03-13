<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finaliser votre inscription - Car 225</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="shortcut icon" href="{{asset('assetsPoster/assets/images/Car225_favicon.png')}}" />
    <style>
        :root {
            --primary: #e94f1b;
            --primary-dark: #c43d10;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --bg-secondary: #f8fafc;
            --border: #e2e8f0;
            --radius-lg: 16px;
            --shadow-lg: 0 20px 40px -12px rgba(0,0,0,0.12);
            --transition: all 0.25s ease;
        }
        *, *::before, *::after {
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-secondary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            max-width: 450px;
            width: 100%;
            text-align: center;
        }
        .logo { width: 100px; margin-bottom: 20px; }
        h1 { font-size: 24px; color: var(--text-primary); margin-bottom: 10px; }
        p { color: var(--text-secondary); margin-bottom: 30px; line-height: 1.5; }
        .input-group { text-align: left; margin-bottom: 20px; }
        label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; }
        .input-wrapper { position: relative; }
        .input-field {
            width: 100%;
            padding: 12px 40px;
            border: 2px solid var(--border);
            border-radius: 12px;
            font-size: 16px;
            transition: var(--transition);
        }
        .input-field:focus { border-color: var(--primary); outline: none; }
        .icon { position: absolute; left: 15px; top: 15px; color: var(--text-secondary); }
        .submit-btn {
            width: 100%;
            background: var(--primary);
            color: white;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
        }
        .submit-btn:hover { background: var(--primary-dark); transform: translateY(-2px); }
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .alert-info { background: #e0f2fe; color: #0369a1; }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{asset('assetsPoster/assets/images/Car225_favicon.png')}}" alt="Logo" class="logo">
        <h1>Presque terminé !</h1>
        <p>Pour des raisons de sécurité et pour faciliter vos réservations, merci de renseigner votre numéro de téléphone.</p>

        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        <form action="{{ route('user.update-contact') }}" method="POST">
            @csrf
            <div class="input-group">
                <label for="contact">Numéro de téléphone</label>
                <div class="input-wrapper">
                    <i class="fas fa-phone icon"></i>
                    <input type="text" name="contact" id="contact" class="input-field" placeholder="Ex: 0707070707" required value="{{ old('contact') }}">
                </div>
                @error('contact')
                    <span style="color: red; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="submit-btn">Terminer l'inscription</button>
        </form>

        <div style="margin-top: 20px;">
            <a href="{{ route('user.logout') }}" style="color: var(--text-secondary); text-decoration: none; font-size: 14px;">Se déconnecter</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Attention',
                    text: '{{ session('warning') }}',
                    confirmButtonColor: '#e94f1b'
                });
            @endif
        });
    </script>
</body>
</html>
