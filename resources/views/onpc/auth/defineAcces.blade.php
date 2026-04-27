<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Définir mon mot de passe ONPC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Outfit', sans-serif; } </style>
</head>

<body class="bg-slate-900 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-key text-3xl text-blue-700"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900">Définir mon mot de passe</h2>
            <p class="text-gray-500 text-sm mt-1">Saisissez le code reçu par email pour finaliser la création de votre compte.</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                @foreach($errors->all() as $err)
                    <p class="text-sm text-red-700">{{ $err }}</p>
                @endforeach
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('onpc.submit-define-access') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                <input type="email" value="{{ $email }}" disabled
                    class="block w-full px-3 py-2.5 bg-gray-100 border-gray-200 rounded-lg text-gray-600">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Code de validation</label>
                <input type="text" name="code" required placeholder="Code reçu par email"
                    class="block w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-600">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nouveau mot de passe</label>
                <input type="password" name="password" required minlength="8"
                    class="block w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-600">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Confirmer le mot de passe</label>
                <input type="password" name="confirme_password" required minlength="8"
                    class="block w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-600">
            </div>

            <button type="submit"
                class="w-full py-3 rounded-lg font-bold text-white bg-blue-700 hover:bg-blue-800 shadow-lg">
                Définir mon mot de passe
            </button>
        </form>
        <div class="text-center mt-4">
            <a href="{{ route('onpc.login') }}" class="text-sm text-gray-500 hover:text-blue-700">
                <i class="fas fa-arrow-left"></i> Retour à la connexion
            </a>
        </div>
    </div>
</body>

</html>
