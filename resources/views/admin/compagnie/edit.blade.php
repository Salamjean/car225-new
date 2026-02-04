@extends('admin.layouts.template')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
    <div class=" mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#e94f1b] rounded-2xl shadow-lg mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Modifier la Compagnie</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Modifiez les informations de {{ $compagnie->name }}
            </p>
        </div>

        <!-- Carte du formulaire -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="{{ route('compagnie.update', $compagnie) }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf
                @method('PUT')

                <!-- Section 1: Informations de base -->
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-[#e94f1b] rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Informations de base</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                        <!-- Nom -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Nom de la compagnie</label>
                            <span class="text-red-500 ml-1">*</span>
                            <div class="relative">
                                <input type="text" name="name" value="{{ old('name', $compagnie->name) }}" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                    placeholder="Entrez le nom complet">
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                         <!-- Username -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Nom d'utilisateur</label>
                            <input type="text" name="username" value="{{ old('username', $compagnie->username) }}"
                                class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                placeholder="Nom d'utilisateur (optionnel)">
                            @error('username')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Sigle -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Sigle</label>
                            <input type="text" name="sigle" value="{{ old('sigle', $compagnie->sigle) }}"
                                class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                placeholder="Sigle (optionnel)">
                            @error('sigle')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slogan -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Slogan</label>
                            <input type="text" name="slogan" value="{{ old('slogan', $compagnie->slogan) }}"
                                class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                placeholder="Slogan de la compagnie">
                            @error('slogan')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
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
                                <input type="email" name="email" value="{{ old('email', $compagnie->email) }}" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                    placeholder="email@compagnie.com">
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
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
                                    @php
                                        $currentPrefix = old('prefix', $compagnie->prefix);
                                        $currentContact = old('contact', $compagnie->contact);
                                        // Extraire le numéro du contact complet
                                        if (strpos($currentContact, $currentPrefix) === 0) {
                                            $phoneNumber = trim(str_replace($currentPrefix, '', $currentContact));
                                        } else {
                                            $phoneNumber = $currentContact;
                                        }
                                    @endphp
                                    <select name="prefix" required
                                        class="w-full px-3 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white appearance-none">
                                        <option value="+225" {{ $currentPrefix == '+225' ? 'selected' : '' }}>🇨🇮 +225</option>
                                        <option value="+33" {{ $currentPrefix == '+33' ? 'selected' : '' }}>🇫🇷 +33</option>
                                        <option value="+1" {{ $currentPrefix == '+1' ? 'selected' : '' }}>🇺🇸 +1</option>
                                        <option value="+44" {{ $currentPrefix == '+44' ? 'selected' : '' }}>🇬🇧 +44</option>
                                        <option value="+49" {{ $currentPrefix == '+49' ? 'selected' : '' }}>🇩🇪 +49</option>
                                        <option value="+32" {{ $currentPrefix == '+32' ? 'selected' : '' }}>🇧🇪 +32</option>
                                        <option value="+221" {{ $currentPrefix == '+221' ? 'selected' : '' }}>🇸🇳 +221</option>
                                        <option value="+223" {{ $currentPrefix == '+223' ? 'selected' : '' }}>🇲🇱 +223</option>
                                        <option value="+226" {{ $currentPrefix == '+226' ? 'selected' : '' }}>🇧🇫 +226</option>
                                        <option value="+229" {{ $currentPrefix == '+229' ? 'selected' : '' }}>🇧🇯 +229</option>
                                    </select>
                                </div>
                                <!-- Numéro de téléphone -->
                                <div class="flex-1">
                                    <input type="text" name="contact" value="{{ $phoneNumber }}" required
                                        class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                        placeholder="07 00 00 00 00">
                                    @error('contact')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
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
                                class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white appearance-none">
                                <option value="">Sélectionnez une commune</option>
                                <option value="Abobo" {{ old('commune', $compagnie->commune) == 'Abobo' ? 'selected' : '' }}>Abobo</option>
                                <option value="Adjamé" {{ old('commune', $compagnie->commune) == 'Adjamé' ? 'selected' : '' }}>Adjamé</option>
                                <option value="Attécoubé" {{ old('commune', $compagnie->commune) == 'Attécoubé' ? 'selected' : '' }}>Attécoubé</option>
                                <option value="Cocody" {{ old('commune', $compagnie->commune) == 'Cocody' ? 'selected' : '' }}>Cocody</option>
                                <option value="Koumassi" {{ old('commune', $compagnie->commune) == 'Koumassi' ? 'selected' : '' }}>Koumassi</option>
                                <option value="Marcory" {{ old('commune', $compagnie->commune) == 'Marcory' ? 'selected' : '' }}>Marcory</option>
                                <option value="Plateau" {{ old('commune', $compagnie->commune) == 'Plateau' ? 'selected' : '' }}>Plateau</option>
                                <option value="Port-Bouët" {{ old('commune', $compagnie->commune) == 'Port-Bouët' ? 'selected' : '' }}>Port-Bouët</option>
                                <option value="Treichville" {{ old('commune', $compagnie->commune) == 'Treichville' ? 'selected' : '' }}>Treichville</option>
                                <option value="Yopougon" {{ old('commune', $compagnie->commune) == 'Yopougon' ? 'selected' : '' }}>Yopougon</option>
                                <option value="Songon" {{ old('commune', $compagnie->commune) == 'Songon' ? 'selected' : '' }}>Songon</option>
                                <option value="Bingerville" {{ old('commune', $compagnie->commune) == 'Bingerville' ? 'selected' : '' }}>Bingerville</option>
                                <option value="Anyama" {{ old('commune', $compagnie->commune) == 'Anyama' ? 'selected' : '' }}>Anyama</option>
                            </select>
                            @error('commune')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Adresse -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Adresse</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="text" name="adresse" value="{{ old('adresse', $compagnie->adresse) }}" required
                                class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                placeholder="Adresse complète dans la commune">
                            @error('adresse')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    </div>
                </div>

                <!-- Section 3: Gestion des Tickets -->
                <div class="mb-12">
                     <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-blue-500 rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Gestion des Tickets</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 bg-blue-50 p-6 rounded-2xl border border-blue-100">
                        <div class="flex flex-col justify-center">
                            <h3 class="text-lg font-bold text-gray-800 mb-2">Solde Actuel</h3>
                            <div class="flex items-center gap-3">
                                <span class="text-4xl font-black text-blue-600">{{ $compagnie->tickets }}</span>
                                <span class="text-sm font-semibold text-blue-400 uppercase tracking-wider">Tickets disponibles</span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label class="block text-sm font-semibold text-gray-700">Ajouter des tickets (Rechargement)</label>
                            <div class="flex gap-4">
                                <input type="number" name="add_tickets" min="0" placeholder="Quantité à ajouter"
                                    class="flex-1 px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-white">
                                <input type="number" name="montant_paye" min="0" placeholder="Montant payé (FCFA)"
                                    class="flex-1 px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 bg-white">
                            </div>
                            <p class="text-xs text-gray-500 italic">Laissez vide si vous ne souhaitez pas ajouter de tickets.</p>
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
                    
                    <div class="flex gap-4">
                        <!-- Bouton Réinitialiser -->
                        <button type="reset"
                                class="flex items-center px-6 py-4 text-gray-700 font-semibold rounded-xl border border-gray-300 hover:bg-gray-50 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Réinitialiser
                        </button>

                        <!-- Bouton Mettre à jour -->
                        <button type="submit"
                                class="flex items-center px-8 py-4 bg-[#e94f1b] text-white font-bold rounded-xl hover:bg-[#e89116] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Mettre à jour
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Inclure SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
});

// Confirmation de suppression du logo
function confirmDeleteLogo() {
    Swal.fire({
        title: 'Supprimer le logo ?',
        text: "Vous êtes sur le point de supprimer le logo actuel de la compagnie.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Oui, supprimer !',
        cancelButtonText: 'Annuler',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-3xl'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Rediriger vers une route de suppression du logo
            window.location.href = "#";
        }
    });
}

// Format automatique du numéro pour la Côte d'Ivoire
const phoneInput = document.querySelector('input[name="contact"]');
const countryCodeSelect = document.querySelector('select[name="prefix"]');

phoneInput.addEventListener('input', function(e) {
    if (countryCodeSelect.value === '+225') {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 0) {
            value = value.match(/.{1,2}/g).join(' ');
        }
        e.target.value = value;
    }
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