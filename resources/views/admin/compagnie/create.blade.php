@extends('admin.layouts.template')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-10">
    <div class=" mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#e94e1a] rounded-2xl shadow-lg mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Nouvelle Compagnie</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Créez un nouveau profil de compagnie avec toutes les informations nécessaires
            </p>
        </div>

        <!-- Carte du formulaire -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="{{ route('compagnie.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf

                <!-- Section 1: Informations de base -->
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-[#e94e1a] rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Informations de base</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                        <!-- Nom -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Nom de la compagnie</label>
                             <span class="text-red-500 ml-1">*</span>
                            <div class="relative">
                                <input type="text" name="name" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                    placeholder="Entrez le nom complet">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                         <!-- Username -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Nom d'utilisateur</label>
                            <input type="text" name="username"
                                class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                placeholder="Nom d'utilisateur (optionnel)">
                        </div>

                        <!-- Sigle -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Sigle</label>
                            <input type="text" name="sigle"
                                class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                placeholder="Sigle (optionnel)">
                        </div>

                        <!-- Slogan -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Slogan</label>
                            <input type="text" name="slogan"
                                class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                placeholder="Slogan de la compagnie">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Contact et Localisation -->
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-green-500 rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Contact & Localisation</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                        <!-- Email -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Email</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="email" name="email" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                    placeholder="email@compagnie.com">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Contact avec Code Pays -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Contact</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="flex gap-3">
                                <!-- Code Pays -->
                                <div class="w-32">
                                    <select name="prefix" required
                                        class="w-full px-3 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white appearance-none">
                                        <option value="+225">🇨🇮 +225</option>
                                        <option value="+33">🇫🇷 +33</option>
                                        <option value="+1">🇺🇸 +1</option>
                                        <option value="+44">🇬🇧 +44</option>
                                        <option value="+49">🇩🇪 +49</option>
                                        <option value="+32">🇧🇪 +32</option>
                                        <option value="+221">🇸🇳 +221</option>
                                        <option value="+223">🇲🇱 +223</option>
                                        <option value="+226">🇧🇫 +226</option>
                                        <option value="+229">🇧🇯 +229</option>
                                    </select>
                                </div>
                                <!-- Numéro de téléphone -->
                                <div class="flex-1">
                                    <input type="text" name="contact" required
                                        class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                        placeholder="07 00 00 00 00">
                                </div>
                            </div>
                        </div>

                        <!-- Commune d'Abidjan -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Commune</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <select name="commune" required
                                class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white appearance-none">
                                <option value="">Sélectionnez une commune</option>
                                <option value="Abobo">Abobo</option>
                                <option value="Adjamé">Adjamé</option>
                                <option value="Attécoubé">Attécoubé</option>
                                <option value="Cocody">Cocody</option>
                                <option value="Koumassi">Koumassi</option>
                                <option value="Marcory">Marcory</option>
                                <option value="Plateau">Plateau</option>
                                <option value="Port-Bouët">Port-Bouët</option>
                                <option value="Treichville">Treichville</option>
                                <option value="Yopougon">Yopougon</option>
                                <option value="Songon">Songon</option>
                                <option value="Bingerville">Bingerville</option>
                                <option value="Anyama">Anyama</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Adresse -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Adresse</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="text" name="adresse" required
                                class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                placeholder="Adresse complète dans la commune">
                        </div>
                    </div>
                </div>

                <!-- Section 4: Logo -->
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-green-500 rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Logo de la compagnie</h2>
                    </div>

                    <div class="flex flex-col items-center justify-center">
                        <div id="upload-area" 
                            class="w-full max-w-2xl border-3 border-dashed border-gray-300 rounded-3xl p-12 text-center cursor-pointer transition-all duration-300 hover:border-[#e94e1a] hover:bg-green-50 group">
                            <div id="upload-content">
                                <div class="mx-auto w-16 h-16 bg-[#e94e1a] rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">Uploader le logo</h3>
                                <p class="text-gray-500 mb-4">Glissez-déposez votre fichier ou cliquez pour parcourir</p>
                                <button type="button" class="px-6 py-3 bg-[#e94e1a] text-white rounded-xl font-semibold hover:bg-[#e89116] transition-colors duration-200">
                                    Choisir un fichier
                                </button>
                                <p class="text-sm text-gray-400 mt-3">PNG, JPG, JPEG jusqu'à 2MB</p>
                            </div>
                            <input type="file" id="path_logo" name="path_logo" class="hidden" accept="image/*">
                            
                            <!-- Aperçu -->
                            <div id="image-preview" class="hidden mt-6">
                                <img id="preview" class="mx-auto max-h-32 rounded-2xl shadow-lg" src="" alt="Aperçu du logo">
                                <button type="button" id="remove-image" class="mt-4 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-200">
                                    Supprimer l'image
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 justify-between items-center pt-8 border-t border-gray-200">
                    <a href="{{ route('compagnie.index') }}" 
                       class="flex items-center px-8 py-4 text-gray-700 font-semibold rounded-xl border border-gray-300 hover:bg-gray-50 transition-all duration-200 group">
                        <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour à la liste
                    </a>
                    
                    <button type="submit"
                        class="flex items-center px-12 py-4 bg-[#e94e1a] text-white font-bold rounded-xl hover:bg-[#e89116] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Créer la compagnie
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Gestion de l'upload d'image
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('upload-area');
    const fileInput = document.getElementById('path_logo');
    const uploadContent = document.getElementById('upload-content');
    const imagePreview = document.getElementById('image-preview');
    const preview = document.getElementById('preview');
    const removeBtn = document.getElementById('remove-image');

    // Click sur la zone d'upload
    uploadArea.addEventListener('click', () => fileInput.click());

    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('border-[#e94e1a]', 'bg-green-50');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('border-[#e94e1a]', 'bg-green-50');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('border-[#e94e1a]', 'bg-green-50');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            handleFileSelect(e.dataTransfer.files[0]);
        }
    });

    // Changement de fichier
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length) {
            handleFileSelect(e.target.files[0]);
        }
    });

    // Suppression d'image
    removeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        fileInput.value = '';
        uploadContent.classList.remove('hidden');
        imagePreview.classList.add('hidden');
    });

    function handleFileSelect(file) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                uploadContent.classList.add('hidden');
                imagePreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    // Gestion du code pays et du numéro
    const countryCodeSelect = document.querySelector('select[name="country_code"]');
    const phoneInput = document.querySelector('input[name="contact"]');

    // Format automatique du numéro pour la Côte d'Ivoire
    phoneInput.addEventListener('input', function(e) {
        if (countryCodeSelect.value === '+225') {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                value = value.match(/.{1,2}/g).join(' ');
            }
            e.target.value = value;
        }
    });
});
</script>

<style>
.border-3 {
    border-width: 3px;
}

input:focus, select:focus {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(233, 78, 26, 0.15);
}
</style>
@endsection