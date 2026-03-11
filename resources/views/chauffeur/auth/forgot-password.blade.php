<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - Chauffeur</title>
    <link rel="shortcut icon" href="{{ asset('assetsPoster/assets/images/logo_car225.png') }}" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body class="bg-gradient-to-br from-orange-400 via-orange-500 to-orange-600 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden text-center">
            <!-- Header -->
            <div class="bg-gradient-to-r from-[#e94e1a] to-[#d33d0f] p-8">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-[#e94e1a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Mot de passe oublié</h1>
                <p class="text-orange-100">Entrez votre identifiant (Contact ou Code ID) pour recevoir un code de réinitialisation</p>
            </div>

            <!-- Body -->
            <div class="p-8">
                @if (session('status'))
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg text-left">
                        <p class="text-green-700 text-sm">{{ session('status') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg text-left">
                        <p class="text-red-700 text-sm">{{ session('error') }}</p>
                    </div>
                @endif

                <form action="{{ route('chauffeur.password.email') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Email/Identity -->
                    <div class="text-left">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Contact ou Code ID</label>
                        <div class="relative">
                            <input type="text" name="email" value="{{ old('email') }}" required
                                class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="Contact ou Code ID">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-[#e94e1a] text-white font-bold py-4 rounded-xl hover:bg-[#d33d0f] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                        Envoyer le code
                    </button>
                    
                    <div class="mt-6">
                        <a href="{{ route('chauffeur.login') }}" class="text-sm text-[#e94e1a] hover:underline font-semibold">
                            Retour à la connexion
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-sm text-white">&copy; {{ date('Y') }} CAR225. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
