@extends('compagnie.layouts.template')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full shadow-lg mb-4">
                <i class="fas fa-shield-alt text-3xl text-[#e94f1b]"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900">Vérification Email</h2>
            <p class="mt-2 text-sm text-gray-600">
                Un code de vérification a été envoyé à 
                <span class="font-medium text-gray-900">{{ $pendingData['email'] }}</span>
            </p>
        </div>

        <div class="bg-white py-8 px-4 shadow-xl rounded-2xl sm:px-10 border border-gray-100">
            <form class="space-y-6" action="{{ route('gare.verify-email-update.store', $gare->id) }}" method="POST">
                @csrf
                
                <div>
                    <label for="otp" class="block text-sm font-medium text-gray-700 text-center mb-4">
                        Entrez le code à 6 chiffres
                    </label>
                    <div class="mt-1">
                        <input id="otp" name="otp" type="text" maxlength="6" required
                            class="appearance-none block w-full px-3 py-4 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#e94f1b] focus:border-[#e94f1b] sm:text-lg text-center tracking-[1em] font-bold"
                            placeholder="000000">
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-[#e94f1b] hover:bg-[#d84617] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#e94f1b] transition-all transform hover:-translate-y-0.5">
                        Valider la modification
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">
                            Ou
                        </span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-3">
                    <a href="{{ route('gare.edit', $gare->id) }}"
                        class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        Annuler la modification
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
