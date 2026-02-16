<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe - Chauffeur</title>
    <link rel="shortcut icon" href="{{ asset('assetsPoster/assets/images/logo_car225.png') }}" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body class="bg-gradient-to-br from-orange-400 via-orange-500 to-orange-600 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-[#e94e1a] to-[#d33d0f] p-8 text-center">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-[#e94e1a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Réinitialisation</h1>
                <p class="text-orange-100">Saisissez le code reçu et votre nouveau mot de passe</p>
            </div>

            <!-- Body -->
            <div class="p-8">
                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                        <p class="text-red-700 text-sm">{{ session('error') }}</p>
                    </div>
                @endif

                <form action="{{ route('chauffeur.password.update') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">

                    <!-- Code OTP -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Code de vérification (OTP)</label>
                        <div class="relative">
                            <input type="text" name="otp" required maxlength="6"
                                class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all tracking-widest text-center text-xl font-bold"
                                placeholder="000000">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-2.107-7.705L6.157 11c1.133-.96 2.132-2.122 2.918-3.457m0 2.636a21.19 21.19 0 003.496-1.132m.893 12.038a3.123 3.123 0 00.491 2.274m0-5.093c0-1.326.39-2.586 1.054-3.61m0 5.093c.664-1.024 1.054-2.284 1.054-3.61m0 3.61c.666-1.025 1.056-2.285 1.056-3.61m0 3.61c.668-1.026 1.058-2.286 1.058-3.61m0 3.61V11"/>
                                </svg>
                            </div>
                        </div>
                        @error('otp')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nouveau mot de passe (min 8 car.)</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required minlength="8"
                                class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="••••••••">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Confirmer le nouveau mot de passe</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8"
                                class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="••••••••">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-[#e94e1a] text-white font-bold py-4 rounded-xl hover:bg-[#d33d0f] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                        Réinitialiser le mot de passe
                    </button>
                    
                    <div class="text-center mt-6">
                        <a href="{{ route('chauffeur.login') }}" class="text-sm text-gray-500 hover:text-[#e94e1a] transition-colors">
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
