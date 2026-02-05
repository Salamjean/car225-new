<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification OTP - Caissière</title>
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
                <h1 class="text-2xl font-bold text-white mb-2">Vérification OTP</h1>
                <p class="text-orange-100">Entrez le code reçu par email</p>
            </div>

            <!-- Body -->
            <div class="p-8">
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
                        <p class="text-green-700 text-sm">{{ session('success') }}</p>
                    </div>
                @endif

                @if (session('info'))
                    <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
                        <p class="text-blue-700 text-sm">{{ session('info') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                        @foreach ($errors->all() as $error)
                            <p class="text-red-700 text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('caisse.auth.verify-otp.submit') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all"
                            placeholder="votre@email.com">
                    </div>

                    <!-- Code OTP -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Code OTP (6 chiffres)</label>
                        <input type="text" name="otp_code" maxlength="6" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all text-center text-2xl font-bold tracking-widest"
                            placeholder="000000"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-[#e94e1a] text-white font-bold py-4 rounded-xl hover:bg-[#d33d0f] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                        Vérifier
                    </button>
                </form>

                <!-- Resend OTP Form -->
                <form action="{{ route('caisse.auth.resend-otp') }}" method="POST" id="resend-form" class="hidden">
                    @csrf
                    <input type="hidden" name="email" id="resend-email">
                </form>

                <!-- Resend Link -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Vous n'avez pas reçu le code ?
                        <button type="button" onclick="resendOtp()" class="text-[#e94e1a] font-semibold hover:underline">
                            Renvoyer le code
                        </button>
                    </p>
                </div>

                <!-- Login Link -->
                <div class="mt-4 text-center border-t border-gray-200 pt-6">
                    <p class="text-sm text-gray-600">
                        Vous avez déjà configuré votre compte ?
                        <a href="{{ route('caisse.auth.login') }}" class="text-[#e94e1a] font-semibold hover:underline">
                            Se connecter
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-500" style="color: #ffffffff;">&copy; {{ date('Y') }} CAR225. Tous droits réservés.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function resendOtp() {
            const email = document.querySelector('input[name="email"]').value;
            
            if (!email) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Veuillez entrer votre adresse email',
                    confirmButtonColor: '#e94e1a'
                });
                return;
            }

            document.getElementById('resend-email').value = email;
            
            Swal.fire({
                title: 'Renvoyer le code ?',
                text: `Un nouveau code sera envoyé à ${email}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Oui, renvoyer',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#e94e1a',
                cancelButtonColor: '#6b7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('resend-form').submit();
                }
            });
        }
    </script>
</body>
</html>
