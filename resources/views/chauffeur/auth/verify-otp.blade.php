<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification OTP - Chauffeur</title>
    <link rel="shortcut icon" href="{{ asset('assetsPoster/assets/images/logo_car225.png') }}" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-orange-400 via-orange-500 to-orange-600 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-[#e94e1a] to-[#d33d0f] p-8 text-center">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-[#e94e1a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Vérification OTP</h1>
                <p class="text-orange-100">Entrez le code reçu par email</p>
            </div>

            <!-- Body -->
            <div class="p-8">
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
                        <p class="text-green-700 text-sm">{{ session('success') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                        <p class="text-red-700 text-sm">{{ session('error') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                        @foreach ($errors->all() as $error)
                            <p class="text-red-700 text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="mb-6 p-4 bg-orange-50 border-l-4 border-orange-500 rounded-lg">
                    <p class="text-orange-700 text-sm">
                        <strong>Email:</strong> {{ $email }}
                    </p>
                    <p class="text-orange-600 text-xs mt-2">
                        Un code à 6 chiffres a été envoyé à cette adresse.
                    </p>
                </div>

                <form action="{{ route('chauffeur.verify-otp.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">

                    <!-- OTP Code -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Code OTP</label>
                        <div class="relative">
                            <input type="text" name="otp" id="otp" required maxlength="6" pattern="[0-9]{6}"
                                class="w-full px-4 py-3 text-center text-2xl font-bold tracking-widest border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all"
                                placeholder="000000"
                                autocomplete="off">
                        </div>
                        <p class="mt-2 text-xs text-gray-500 text-center"></p>
                    </div>

                    <!-- Password Fields -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nouveau mot de passe</label>
                            <div class="relative">
                                <input type="password" name="password" required minlength="8"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all"
                                    placeholder="Minimum 8 caractères">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Confirmer le mot de passe</label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" required minlength="8"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all"
                                    placeholder="Répétez le mot de passe">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-[#e94e1a] text-white font-bold py-4 rounded-xl hover:bg-[#d33d0f] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                        Vérifier mon compte
                    </button>
                </form>

                <!-- Resend OTP -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600 mb-2">Vous n'avez pas reçu de code ?</p>
                    <form action="{{ route('chauffeur.verify-otp.resend') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        <button type="submit" class="text-[#e94e1a] hover:text-[#d33d0f] font-semibold text-sm hover:underline">
                            Renvoyer le code
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <a href="{{ route('chauffeur.login') }}" class="text-white hover:underline text-sm">
                ← Retour à la connexion
            </a>
        </div>
    </div>

    <script>
        // Auto-focus on OTP input
        document.getElementById('otp').focus();

        // Only allow numbers
        document.getElementById('otp').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>
