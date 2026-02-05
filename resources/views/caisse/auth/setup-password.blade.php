<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Définir mot de passe - Caissière</title>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Définir votre mot de passe</h1>
                <p class="text-orange-100">Créez un mot de passe sécurisé</p>
            </div>

            <!-- Body -->
            <div class="p-8">
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                        @foreach ($errors->all() as $error)
                            <p class="text-red-700 text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('caisse.auth.setup-password.submit') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nouveau mot de passe</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all"
                                placeholder="Minimum 8 caractères">
                            <button type="button" onclick="togglePassword('password')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Au moins 8 caractères</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Confirmer le mot de passe</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all"
                                placeholder="Retapez votre mot de passe">
                            <button type="button" onclick="togglePassword('password_confirmation')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Password Strength Indicator -->
                    <div id="password-strength" class="hidden">
                        <div class="flex items-center space-x-2 mb-2">
                            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div id="strength-bar" class="h-full transition-all duration-300"></div>
                            </div>
                            <span id="strength-text" class="text-xs font-medium"></span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-[#e94e1a] text-white font-bold py-4 rounded-xl hover:bg-[#d33d0f] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                        Définir et continuer
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-500" style="color: #ffffffff;">&copy; {{ date('Y') }} CAR225. Tous droits réservés.</p>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            field.type = field.type === 'password' ? 'text' : 'password';
        }

        // Password strength checker
        const passwordInput = document.getElementById('password');
        const strengthIndicator = document.getElementById('password-strength');
        const strengthBar = document.getElementById('strength-bar');
        const strengthText = document.getElementById('strength-text');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            if (password.length === 0) {
                strengthIndicator.classList.add('hidden');
                return;
            }
            
            strengthIndicator.classList.remove('hidden');
            
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]+/)) strength++;
            if (password.match(/[A-Z]+/)) strength++;
            if (password.match(/[0-9]+/)) strength++;
            if (password.match(/[$@#&!]+/)) strength++;

            switch(strength) {
                case 0:
                case 1:
                    strengthBar.style.width = '20%';
                    strengthBar.style.backgroundColor = '#ef4444';
                    strengthText.textContent = 'Faible';
                    strengthText.style.color = '#ef4444';
                    break;
                case 2:
                    strengthBar.style.width = '40%';
                    strengthBar.style.backgroundColor = '#f59e0b';
                    strengthText.textContent = 'Moyen';
                    strengthText.style.color = '#f59e0b';
                    break;
                case 3:
                    strengthBar.style.width = '60%';
                    strengthBar.style.backgroundColor = '#3b82f6';
                    strengthText.textContent = 'Bon';
                    strengthText.style.color = '#3b82f6';
                    break;
                case 4:
                case 5:
                    strengthBar.style.width = '100%';
                    strengthBar.style.backgroundColor = '#10b981';
                    strengthText.textContent = 'Excellent';
                    strengthText.style.color = '#10b981';
                    break;
            }
        });

        // Match validation
        const confirmInput = document.getElementById('password_confirmation');
        confirmInput.addEventListener('input', function() {
            if (this.value && this.value !== passwordInput.value) {
                this.classList.add('border-red-500');
            } else {
                this.classList.remove('border-red-500');
            }
        });
    </script>
</body>
</html>
