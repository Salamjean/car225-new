@extends('admin.layouts.template')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-10">
    <div class=" mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#e94f1b] rounded-2xl shadow-lg mb-4">
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
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden mb-12">
            <form action="{{ route('compagnie.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf

                @if($errors->any())
                    <div class="mb-8 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                            <span class="font-bold text-red-800">Veuillez corriger les erreurs suivantes :</span>
                        </div>
                        <ul class="list-disc list-inside text-sm text-red-700">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Section 1: Informations de base -->
                <div class="mb-12">
                    <div class="flex items-center mb-8">
                        <div class="w-2 h-8 bg-[#e94f1b] rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900 uppercase tracking-wide">Informations de base</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Nom -->
                        <div class="space-y-3">
                            <label class="text-sm font-bold text-gray-700 flex items-center">
                                Nom de la compagnie <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative group">
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    class="w-full px-5 py-4 border-2 border-gray-100 rounded-2xl focus:ring-0 focus:border-[#e94f1b] transition-all duration-300 bg-gray-50 focus:bg-white @error('name') border-red-300 @enderror"
                                    placeholder="Ex: Transport Express">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 group-focus-within:text-[#e94f1b]">
                                    <i class="fas fa-building text-lg"></i>
                                </div>
                            </div>
                            @error('name') <p class="text-xs text-red-500 font-medium italic">{{ $message }}</p> @enderror
                        </div>

                        <!-- Sigle -->
                        <div class="space-y-3">
                            <label class="text-sm font-bold text-gray-700 flex items-center">
                                Sigle / Abréviation <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative group">
                                <input type="text" name="sigle" value="{{ old('sigle') }}" required
                                    class="w-full px-5 py-4 border-2 border-gray-100 rounded-2xl focus:ring-0 focus:border-[#e94f1b] transition-all duration-300 bg-gray-50 focus:bg-white @error('sigle') border-red-300 @enderror"
                                    placeholder="Ex: TE">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 group-focus-within:text-[#e94f1b]">
                                    <i class="fas fa-tag text-lg"></i>
                                </div>
                            </div>
                            @error('sigle') <p class="text-xs text-red-500 font-medium italic">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Contact et Localisation -->
                <div class="mb-12">
                    <div class="flex items-center mb-8">
                        <div class="w-2 h-8 bg-green-500 rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900 uppercase tracking-wide">Contact & Localisation</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                        <!-- Email -->
                        <div class="space-y-3">
                            <label class="text-sm font-bold text-gray-700 flex items-center">
                                Login / Email Professionnel <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative group">
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    class="w-full px-5 py-4 border-2 border-gray-100 rounded-2xl focus:ring-0 focus:border-[#e94f1b] transition-all duration-300 bg-gray-50 focus:bg-white @error('email') border-red-300 @enderror"
                                    placeholder="contact@compagnie.ci">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 group-focus-within:text-[#e94f1b]">
                                    <i class="fas fa-envelope text-lg"></i>
                                </div>
                            </div>
                            @error('email') <p class="text-xs text-red-500 font-medium italic">{{ $message }}</p> @enderror
                        </div>

                        <!-- Contact -->
                        <div class="space-y-3">
                            <label class="text-sm font-bold text-gray-700 flex items-center">
                                Numéro de Contact <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="flex gap-3">
                                <div class="w-32">
                                    <select name="prefix" required
                                        class="w-full px-3 py-4 border-2 border-gray-100 rounded-2xl bg-gray-50 focus:border-[#e94f1b] transition-all duration-300">
                                        <option value="+225" {{ old('prefix') == '+225' ? 'selected' : '' }}>🇨🇮 +225</option>
                                        <option value="+221" {{ old('prefix') == '+221' ? 'selected' : '' }}>🇸🇳 +221</option>
                                        <option value="+226" {{ old('prefix') == '+226' ? 'selected' : '' }}>🇧🇫 +226</option>
                                        <option value="+223" {{ old('prefix') == '+223' ? 'selected' : '' }}>🇲🇱 +223</option>
                                        <option value="+229" {{ old('prefix') == '+229' ? 'selected' : '' }}>🇧🇯 +229</option>
                                    </select>
                                </div>
                                <div class="flex-1 relative group">
                                    <input type="text" name="contact" value="{{ old('contact') }}" required
                                        class="w-full px-5 py-4 border-2 border-gray-100 rounded-2xl focus:ring-0 focus:border-[#e94f1b] transition-all duration-300 bg-gray-50 focus:bg-white @error('contact') border-red-300 @enderror"
                                        placeholder="07 00 00 00 00">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 group-focus-within:text-[#e94f1b]">
                                        <i class="fas fa-phone text-lg"></i>
                                    </div>
                                </div>
                            </div>
                            @error('contact') <p class="text-xs text-red-500 font-medium italic">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Commune -->
                        <div class="space-y-3 text-sm">
                            <label class="text-sm font-bold text-gray-700 flex items-center">
                                Commune du Siège <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative group ">
                                <select name="commune" required
                                    class="w-full px-5 py-4 border-2 border-gray-100 rounded-2xl focus:ring-0 focus:border-[#e94f1b] transition-all duration-300 bg-gray-50 focus:bg-white appearance-none @error('commune') border-red-300 @enderror">
                                    <option value="">Choisir une commune...</option>
                                    @php
                                        $communes = ['Abobo', 'Adjamé', 'Attécoubé', 'Cocody', 'Koumassi', 'Marcory', 'Plateau', 'Port-Bouët', 'Treichville', 'Yopougon', 'Songon', 'Bingerville', 'Anyama'];
                                    @endphp
                                    @foreach($communes as $c)
                                        <option value="{{ $c }}" {{ old('commune') == $c ? 'selected' : '' }}>{{ $c }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 group-focus-within:text-[#e94f1b]">
                                    <i class="fas fa-chevron-down text-lg"></i>
                                </div>
                            </div>
                            @error('commune') <p class="text-xs text-red-500 font-medium italic">{{ $message }}</p> @enderror
                        </div>

                        <!-- Adresse -->
                        <div class="space-y-3">
                            <label class="text-sm font-bold text-gray-700 flex items-center">
                                Adresse Géographique <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative group">
                                <input type="text" name="adresse" value="{{ old('adresse') }}" required
                                    class="w-full px-5 py-4 border-2 border-gray-100 rounded-2xl focus:ring-0 focus:border-[#e94f1b] transition-all duration-300 bg-gray-50 focus:bg-white @error('adresse') border-red-300 @enderror"
                                    placeholder="Ex: Rue 12, Face à la pharmacie...">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 group-focus-within:text-[#e94f1b]">
                                    <i class="fas fa-map-marker-alt text-lg"></i>
                                </div>
                            </div>
                            @error('adresse') <p class="text-xs text-red-500 font-medium italic">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section Logo -->
                <div class="mb-12">
                    <div class="flex items-center mb-8">
                        <div class="w-2 h-8 bg-blue-500 rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900 uppercase tracking-wide">Identité Visuelle (Logo)</h2>
                    </div>

                    <div class="flex flex-col items-center justify-center">
                        <div id="upload-area" 
                            class="w-full max-w-2xl border-4 border-dashed border-gray-200 rounded-[2rem] p-12 text-center cursor-pointer transition-all duration-500 hover:border-[#e94f1b] hover:bg-orange-50 group @error('path_logo') border-red-200 bg-red-50 @enderror">
                            <div id="upload-content">
                                <div class="mx-auto w-20 h-20 bg-orange-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-[#e94f1b] transition-all duration-300 text-[#e94f1b] group-hover:text-white">
                                    <i class="fas fa-cloud-upload-alt text-3xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Uploader le logo</h3>
                                <p class="text-gray-500 mb-6">Glissez votre image ici ou cliquez pour parcourir</p>
                                <button type="button" class="px-8 py-3 bg-white border-2 border-gray-200 text-gray-700 rounded-xl font-bold hover:bg-[#e94f1b] hover:text-white hover:border-[#e94f1b] transition-all duration-300 shadow-sm">
                                    Parcourir les fichiers
                                </button>
                                <p class="text-xs text-gray-400 mt-4 uppercase tracking-widest font-bold">Format: PNG, JPG, JPEG (Max 2MB)</p>
                            </div>
                            <input type="file" id="path_logo" name="path_logo" class="hidden" accept="image/*">
                            
                            <!-- Aperçu -->
                            <div id="image-preview" class="hidden mt-6 animate-fadeIn">
                                <div class="relative inline-block mt-4">
                                    <img id="preview" class="mx-auto max-h-48 rounded-2xl shadow-2xl border-4 border-white" src="" alt="Aperçu du logo">
                                    <button type="button" id="remove-image" class="absolute -top-4 -right-4 w-10 h-10 bg-red-500 text-white rounded-full shadow-lg hover:bg-red-600 transition-all duration-200 flex items-center justify-center border-4 border-white">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <p class="mt-4 text-green-600 font-bold"><i class="fas fa-check-circle mr-1"></i> Fichier sélectionné avec succès</p>
                            </div>
                        </div>
                        @error('path_logo') <p class="text-sm text-red-500 mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-6 justify-between items-center pt-10 border-t-2 border-gray-50">
                    <a href="{{ route('compagnie.index') }}" 
                       class="flex items-center px-10 py-5 text-gray-500 font-bold rounded-2xl border-2 border-gray-100 hover:bg-gray-100 transition-all duration-200 group">
                        <i class="fas fa-arrow-left mr-3 group-hover:-translate-x-2 transition-transform duration-300"></i>
                        ANNULER
                    </a>
                    
                    <button type="submit"
                        class="w-full sm:w-auto flex items-center justify-center px-16 py-5 bg-[#e94f1b] text-white font-black rounded-2xl hover:bg-[#d84416] transform hover:-translate-y-2 transition-all duration-300 shadow-[0_10px_30px_rgba(233,79,27,0.3)] hover:shadow-[0_15px_40px_rgba(233,79,27,0.4)] uppercase tracking-widest">
                        ENREGISTRER LA COMPAGNIE
                    </button>
                </div>
            </form>
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
        uploadArea.classList.add('border-[#e94f1b]', 'bg-green-50');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('border-[#e94f1b]', 'bg-green-50');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('border-[#e94f1b]', 'bg-green-50');
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
    const prefixSelect = document.querySelector('select[name="prefix"]');
    const phoneInput = document.querySelector('input[name="contact"]');

    // Format automatique du numéro pour la Côte d'Ivoire (+225)
    phoneInput.addEventListener('input', function(e) {
        if (prefixSelect.value === '+225') {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 10) value = value.substring(0, 10);
            
            // Format: 07 01 02 03 04
            if (value.length > 0) {
                const parts = value.match(/.{1,2}/g);
                if (parts) e.target.value = parts.join(' ');
            } else {
                e.target.value = value;
            }
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
    box-shadow: 0 8px 25px rgba(254, 162, 25, 0.15);
}
</style>
@endsection