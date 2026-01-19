<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Définir le mot de passe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-lg bg-white rounded-2xl shadow-xl p-8 border-t-4 border-red-600">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Finaliser votre inscription</h2>
            <p class="text-gray-600 mt-2">Veuillez entrer le code reçu par email et définir votre mot de passe pour le
                compte <strong>{{ $email }}</strong>.</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('sapeur-pompier.submit-define-access') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Code de vérification</label>
                <input type="text" id="code" name="code" required
                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 font-mono text-lg tracking-widest text-center"
                    placeholder="XXXX">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                    <input type="password" id="password" name="password" required
                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                        placeholder="••••••••">
                </div>
                <div>
                    <label for="confirme_password"
                        class="block text-sm font-medium text-gray-700 mb-1">Confirmer</label>
                    <input type="password" id="confirme_password" name="confirme_password" required
                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                        placeholder="••••••••">
                </div>
            </div>

            <button type="submit"
                class="w-full justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-red-600 hover:bg-red-700 transition-colors">
                Activer le compte
            </button>
        </form>
    </div>

</body>

</html>