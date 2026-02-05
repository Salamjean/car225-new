@extends('compagnie.layouts.template')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#e94e1a] rounded-2xl shadow-lg mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Nouvelle Caissière</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Ajoutez une nouvelle caissière à votre équipe
            </p>
        </div>

        <!-- Carte du formulaire -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="{{ route('compagnie.caisse.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf

                <!-- Section 1: Informations personnelles -->
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-[#e94e1a] rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Informations personnelles</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Nom -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Nom</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       name="name" 
                                       value="{{ old('name') }}"
                                       required
                                       class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                       placeholder="Entrez le nom">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Prénom -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Prénom</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       name="prenom" 
                                       value="{{ old('prenom') }}"
                                       required
                                       class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                       placeholder="Entrez le prénom">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                            @error('prenom')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Informations de contact -->
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-blue-500 rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Informations de contact</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Email -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Email</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="email" 
                                       name="email" 
                                       value="{{ old('email') }}"
                                       required
                                       class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                       placeholder="email@exemple.com">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-blue-600">Un code OTP sera envoyé à cet email</p>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact principal -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Contact principal</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       name="contact" 
                                       value="{{ old('contact') }}"
                                       required
                                       class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                       placeholder="Ex: +225 07 00 00 00 00">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                            </div>
                            @error('contact')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact d'urgence -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Contact d'urgence</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       name="cas_urgence" 
                                       value="{{ old('cas_urgence') }}"
                                       class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                       placeholder="Numéro en cas d'urgence">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">Personne à contacter en cas d'urgence</p>
                            @error('cas_urgence')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Commune -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Commune</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       name="commune" 
                                       value="{{ old('commune') }}"
                                       class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                       placeholder="Ex: Cocody, Yopougon...">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                            </div>
                            @error('commune')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 3: Photo de profil -->
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-purple-500 rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Photo de profil</h2>
                    </div>

                    <div class="flex justify-center">
                        <div class="max-w-md w-full">
                            <div class="text-center space-y-4">
                                <!-- Aperçu de l'image -->
                                <div class="flex justify-center">
                                    <div class="relative">
                                        <div id="image-preview" class="w-32 h-32 bg-gray-200 rounded-full flex items-center justify-center border-2 border-dashed border-gray-300 hidden overflow-hidden">
                                            <img id="preview" class="w-full h-full rounded-full object-cover" src="" alt="Aperçu">
                                        </div>
                                        <div id="default-avatar" class="w-32 h-32 bg-[#e94e1a] rounded-full flex items-center justify-center text-white font-bold text-lg">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Bouton d'upload centré -->
                                <div class="space-y-2">
                                    <label for="profile_picture" class="cursor-pointer inline-block">
                                        <div class="px-6 py-4 border-2 border-dashed border-gray-300 rounded-xl hover:border-[#e94e1a] transition-all duration-200 text-center bg-gray-50 hover:bg-white">
                                            <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-600">Cliquez pour uploader une photo</span>
                                            <p class="text-xs text-gray-400 mt-1">PNG, JPG, JPEG (max. 2MB)</p>
                                        </div>
                                    </label>
                                    <input type="file" id="profile_picture" name="profile_picture" class="hidden" accept="image/*">
                                    @error('profile_picture')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 justify-between items-center pt-8 border-t border-gray-200">
                    <a href="{{ route('compagnie.caisse.index') }}" 
                       class="flex items-center px-8 py-4 text-gray-700 font-semibold rounded-xl border border-gray-300 hover:bg-gray-50 transition-all duration-200 group">
                        <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour à la liste
                    </a>
                    
                    <button type="submit"
                            class="flex items-center px-8 py-4 bg-[#e94e1a] text-white font-bold rounded-xl hover:bg-[#d33d0f] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Créer la caissière
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de l'upload d'image
    const profilePictureInput = document.getElementById('profile_picture');
    const imagePreview = document.getElementById('image-preview');
    const defaultAvatar = document.getElementById('default-avatar');
    const preview = document.getElementById('preview');

    profilePictureInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                defaultAvatar.classList.add('hidden');
                imagePreview.classList.remove('hidden');
            };
            
            reader.readAsDataURL(file);
        } else {
            defaultAvatar.classList.remove('hidden');
            imagePreview.classList.add('hidden');
            profilePictureInput.value = '';
        }
    });

    // Validation en temps réel de l'email
    const emailInput = document.querySelector('input[name="email"]');
    emailInput.addEventListener('blur', function(e) {
        const email = e.target.value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email && !emailRegex.test(email)) {
            e.target.classList.add('border-red-300');
        } else {
            e.target.classList.remove('border-red-300');
        }
    });
});
</script>

<style>
input:focus, select:focus {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(233, 78, 26, 0.15);
}

#default-avatar, #image-preview {
    transition: all 0.3s ease;
}

#default-avatar:hover, #image-preview:hover {
    transform: scale(1.05);
}
</style>
@endsection
