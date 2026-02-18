@extends('compagnie.layouts.template')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#e94f1b] rounded-2xl shadow-lg mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Nouveau Personnel</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Ajoutez un nouveau membre à votre équipe
            </p>
        </div>

        <!-- Carte du formulaire -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="{{ route('personnel.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf

                <!-- Section 0: Affectation à une Gare -->
                @if(isset($gares) && $gares->count() > 0)
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-blue-500 rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Affectation à une Gare</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Gare</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <select name="gare_id" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white">
                                <option value="" disabled {{ old('gare_id') ? '' : 'selected' }}>-- Sélectionnez une gare --</option>
                                @foreach($gares as $gare)
                                    <option value="{{ $gare->id }}" {{ old('gare_id') == $gare->id ? 'selected' : '' }}>
                                        {{ $gare->nom_gare }} — {{ $gare->ville }}
                                    </option>
                                @endforeach
                            </select>
                            @error('gare_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-400 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Sélectionnez la gare à laquelle ce personnel sera affecté.
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Section 1: Informations personnelles -->
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-[#e94f1b] rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Informations personnelles</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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
                                       class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
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
                                       class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
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

                        <!-- Type de personnel (modifié en select) -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Type de personnel</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <select name="type_personnel" required
                                        class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white appearance-none">
                                    <option value="">Sélectionnez un type</option>
                                    <option value="Chauffeur" {{ old('type_personnel') == 'Chauffeur' ? 'selected' : '' }}>Chauffeur</option>
                                    <option value="Convoyeur" {{ old('type_personnel') == 'Convoyeur' ? 'selected' : '' }}>Convoyeur</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                </div>
                            </div>
                            @error('type_personnel')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Placeholder pour l'alignement -->
                        <div></div>
                    </div>
                </div>

                <!-- Section 2: Informations de contact -->
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-green-500 rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Informations de contact</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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
                                       class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                       placeholder="email@exemple.com">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Contact personnel</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="flex gap-3">
                                <!-- Code Pays -->
                                <div class="w-32">
                                    <select name="country_code" required
                                        class="w-full px-3 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white appearance-none">
                                        <option value="+225" selected>🇨🇮 +225</option>
                                        <option value="+33">🇫🇷 +33</option>
                                        <option value="+1">🇺🇸 +1</option>
                                        <option value="+44">🇬🇧 +44</option>
                                    </select>
                                </div>
                                <!-- Numéro de téléphone -->
                                <div class="flex-1">
                                    <input type="text" 
                                           name="contact" 
                                           value="{{ old('contact') }}"
                                           required
                                           maxlength="10"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)"
                                           class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                           placeholder="Ex: 0700000000">
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
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="flex gap-3">
                                <!-- Code Pays -->
                                <div class="w-32">
                                    <select name="country_code_urgence" required
                                        class="w-full px-3 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white appearance-none">
                                        <option value="+225" selected>🇨🇮 +225</option>
                                        <option value="+33">🇫🇷 +33</option>
                                        <option value="+1">🇺🇸 +1</option>
                                        <option value="+44">🇬🇧 +44</option>
                                    </select>
                                </div>
                                <!-- Numéro de téléphone -->
                                <div class="flex-1">
                                    <input type="text" 
                                           name="contact_urgence" 
                                           value="{{ old('contact_urgence') }}"
                                           required
                                           maxlength="10"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)"
                                           class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                           placeholder="Ex: 0100000000">
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">Personne à contacter en cas d'urgence</p>
                            <p id="error-contact-same" class="text-red-500 text-xs mt-1 hidden">Le contact d'urgence doit être différent du contact personnel.</p>
                            @error('contact_urgence')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>


                <!-- Section 4: Photo de profil (centrée en dernier) -->
                <div class="on mb-12">
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
                                        <div id="default-avatar" class="w-32 h-32 bg-[#e94f1b] rounded-full flex items-center justify-center text-white font-bold text-lg">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Bouton d'upload centré -->
                                <div class="space-y-2">
                                    <label for="profile_image" class="cursor-pointer inline-block">
                                        <div class="px-6 py-4 border-2 border-dashed border-gray-300 rounded-xl hover:border-[#e94f1b] transition-all duration-200 text-center bg-gray-50 hover:bg-white">
                                            <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-600">Cliquez pour uploader une photo</span>
                                            <p class="text-xs text-gray-400 mt-1">PNG, JPG, JPEG (max. 2MB)</p>
                                        </div>
                                    </label>
                                    <input type="file" id="profile_image" name="profile_image" class="hidden" accept="image/*">
                                    @error('profile_image')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 justify-between items-center pt-8 border-t border-gray-200">
                    <a href="{{ route('personnel.index') }}" 
                       class="flex items-center px-8 py-4 text-gray-700 font-semibold rounded-xl border border-gray-300 hover:bg-gray-50 transition-all duration-200 group">
                        <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour à la liste
                    </a>
                    
                    <div class="flex gap-4">
                        <!-- Bouton Créer -->
                        <button type="submit"
                                class="flex items-center px-8 py-4 bg-[#e94f1b] text-white font-bold rounded-xl hover:bg-[#e89116] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Créer le personnel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des boutons radio personnalisés pour le statut uniquement
    const statutOptions = document.querySelectorAll('.statut-option');

    // Fonction pour gérer la sélection des options de statut
    function handleStatutSelection(options) {
        options.forEach(option => {
            const radio = option.querySelector('input[type="radio"]');
            const checkmark = option.querySelector('.w-3.h-3');

            option.addEventListener('click', function() {
                // Désélectionner toutes les options du groupe
                options.forEach(opt => {
                    opt.classList.remove('border-[#e94f1b]', 'bg-orange-50');
                    opt.querySelector('.w-3.h-3').classList.add('hidden');
                });

                // Sélectionner l'option cliquée
                radio.checked = true;
                option.classList.add('border-[#e94f1b]', 'bg-orange-50');
                checkmark.classList.remove('hidden');
            });

            // Initialiser l'état des options sélectionnées
            if (radio.checked) {
                option.classList.add('border-[#e94f1b]', 'bg-orange-50');
                checkmark.classList.remove('hidden');
            }
        });
    }

    // Appliquer la gestion des sélections pour le statut
    handleStatutSelection(statutOptions);

    // Gestion de l'upload d'image
    const profileImageInput = document.getElementById('profile_image');
    const imagePreview = document.getElementById('image-preview');
    const defaultAvatar = document.getElementById('default-avatar');
    const preview = document.getElementById('preview');

    profileImageInput.addEventListener('change', function(e) {
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
            // Réinitialiser si le fichier n'est pas une image
            defaultAvatar.classList.remove('hidden');
            imagePreview.classList.add('hidden');
            profileImageInput.value = '';
        }
    });

    // Formatage automatique des numéros de téléphone (géré par oninput inline)
    const contactInput = document.querySelector('input[name="contact"]');
    const contactUrgenceInput = document.querySelector('input[name="contact_urgence"]');


    // Validation pour s'assurer que le contact personnel et d'urgence sont différents
    const errorSameContact = document.getElementById('error-contact-same');
    const form = document.querySelector('form');

    function validateContacts() {
        if (contactInput.value && contactUrgenceInput.value && contactInput.value === contactUrgenceInput.value) {
            errorSameContact.classList.remove('hidden');
            contactUrgenceInput.classList.add('border-red-500');
            return false;
        } else {
            errorSameContact.classList.add('hidden');
            contactUrgenceInput.classList.remove('border-red-500');
            return true;
        }
    }

    contactInput.addEventListener('input', validateContacts);
    contactUrgenceInput.addEventListener('input', validateContacts);

    form.addEventListener('submit', function(e) {
        if (!validateContacts()) {
            e.preventDefault();
            contactUrgenceInput.focus();
            
            // Notification SweetAlert2 si disponible (optionnel mais recommandé pour l'UX)
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur de validation',
                    text: 'Le contact d\'urgence doit être différent du contact personnel.',
                    confirmButtonColor: '#e94f1b'
                });
            } else {
                alert('Le contact personnel et le contact d\'urgence ne peuvent pas être identiques.');
            }
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

    // Animation au focus des champs
    const inputs = document.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('ring-2', 'ring-[#e94f1b]');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('ring-2', 'ring-[#e94f1b]');
        });
    });
});
</script>

<style>
input:focus, select:focus {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(254, 162, 25, 0.15);
}

/* Styles pour les options de statut sélectionnées */
.statut-option input[type="radio"]:checked + div .w-3.h-3 {
    display: flex !important;
}

.statut-option.border-\[\#e94f1b\] {
    border-color: #e94f1b;
    background-color: #fef6e6;
}

/* Animation pour l'upload d'image */
#default-avatar, #image-preview {
    transition: all 0.3s ease;
}

#default-avatar:hover, #image-preview:hover {
    transform: scale(1.05);
}

/* Style pour les indicateurs de statut */
.w-3.h-3.bg-green-500 {
    animation: pulse 2s infinite;
}

.w-3.h-3.bg-red-500 {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

/* Style pour le select */
select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

/* Responsive */
@media (max-width: 768px) {
    .grid.grid-cols-2 {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Success message
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Succès!',
        text: "{{ session('success') }}",
        confirmButtonColor: '#e94f1b',
        timer: 5000,
        showConfirmButton: true
    });
@endif

// Warning message
@if(session('warning'))
    Swal.fire({
        icon: 'warning',
        title: 'Attention',
        text: "{{ session('warning') }}",
        confirmButtonColor: '#e94f1b'
    });
@endif

// Error message
@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: "{{ session('error') }}",
        confirmButtonColor: '#e94f1b'
    });
@endif
</script>
@endsection
