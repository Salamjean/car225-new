<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion ONPC — CAR 225</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('assetsPoster/assets/images/logo_car225.png') }}" />
    <style> body { font-family: 'Outfit', sans-serif; } </style>
</head>

<body class="bg-slate-900 flex items-center justify-center min-h-screen relative overflow-hidden">

    <div class="absolute inset-0 opacity-20 pointer-events-none">
        <div class="absolute top-[-10%] right-[-5%] w-[500px] h-[500px] bg-blue-700 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] left-[-5%] w-[500px] h-[500px] bg-indigo-600 rounded-full blur-[120px]"></div>
    </div>

    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8 relative z-10">
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shield-alt text-4xl text-blue-700"></i>
            </div>
            <h2 class="text-2xl font-black text-gray-900">Espace ONPC</h2>
            <p class="text-gray-500 mt-2 text-sm">Office National de la Protection Civile</p>
        </div>

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        @endif
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        <form action="{{ route('onpc.login') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input id="email" type="email" name="email" required value="{{ old('email') }}"
                        class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                        placeholder="agent@onpc.ci">
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Mot de passe</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input id="password" type="password" name="password" required
                        class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                        placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="inline-flex items-center text-gray-600">
                    <input type="checkbox" name="remember" class="mr-2 rounded">
                    Se souvenir de moi
                </label>
                <a href="{{ route('onpc.password.request') }}" class="text-blue-700 font-semibold hover:underline">
                    Mot de passe oublié ?
                </a>
            </div>

            <button type="submit"
                class="w-full py-3 px-4 rounded-lg font-bold text-white bg-gradient-to-r from-blue-700 to-indigo-700 hover:from-blue-800 hover:to-indigo-800 shadow-lg transition-all">
                SE CONNECTER
            </button>
        </form>
    </div>

</body>

</html>
