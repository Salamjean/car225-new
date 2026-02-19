@extends('compagnie.layouts.template')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    .ts-control {
        border-radius: 0.75rem !important;
        padding: 0.75rem 1rem !important;
        background-color: #f9fafb !important;
        border: 1px solid #e5e7eb !important;
    }
    .ts-wrapper.focus .ts-control {
        box-shadow: 0 0 0 2px #e94f1b !important;
        border-color: transparent !important;
    }
    .ts-dropdown {
        border-radius: 0.75rem !important;
        margin-top: 0.5rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    }
    input:focus, select:focus {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(254, 162, 25, 0.15);
    }
    #default-avatar, #image-preview {
        transition: all 0.3s ease;
    }
    #default-avatar:hover, #image-preview:hover {
        transform: scale(1.05);
    }
    select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#e94f1b] rounded-2xl shadow-lg mb-4">
                <i class="fas fa-plus text-3xl text-white"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Nouvelle Gare</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Ajoutez un nouveau point d'embarquement avec son responsable
            </p>
        </div>

        <!-- Formulaire -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="{{ route('gare.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf

                <!-- Section 1: Informations de la gare -->
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-[#e94f1b] rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Informations de la gare</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nom de la gare -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Nom de la gare</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" name="nom_gare" value="{{ old('nom_gare') }}" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                    placeholder="Ex: Gare de Bassam">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <i class="fas fa-warehouse text-gray-400"></i>
                                </div>
                            </div>
                            @error('nom_gare')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Ville -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Ville</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <select id="select-ville" name="ville" required class="w-full">
                                <option value="">Sélectionnez une ville</option>
                                @php
                                    $villes = [
                                        'Abidjan','Alepé', 'Abengourou', 'Adzopé', 'Agboville', 'Anyama', 'Bondoukou', 'Bongouanou', 'Bouaflé', 'Bouaké', 
                                        'Boundiali', 'Bouna', 'Dabou', 'Daloa', 'Divo', 'Duékoué', 'Ferkessédougou', 'Gagnoa', 
                                        'Grand-Bassam', 'Guiglo', 'Issia', 'Katiola', 'Korhogo', 'Man', 'Odienné', 'Oumé', 
                                        'San-Pédro', 'Séguéla', 'Sinfra', 'Soubré', 'Tanda', 'Touba', 'Toumodi', 'Vavoua', 
                                        'Yamoussoukro', 'Zénoula'
                                    ];
                                    sort($villes);
                                @endphp
                                @foreach($villes as $ville)
                                    <option value="{{ $ville }}" {{ old('ville') == $ville ? 'selected' : '' }}>
                                        {{ $ville }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ville')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Adresse -->
                        <div class="space-y-2 md:col-span-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Adresse / Situation Géographique</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" name="adresse" value="{{ old('adresse') }}" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                    placeholder="Ex: Rue 12, Face au marché">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <i class="fas fa-map-marker-alt text-gray-400"></i>
                                </div>
                            </div>
                            @error('adresse')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Responsable de la gare -->
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-blue-500 rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Responsable de la gare</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Nom -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Nom</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" name="responsable_nom" value="{{ old('responsable_nom') }}" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                    placeholder="Entrez le nom">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                            @error('responsable_nom')
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
                                <input type="text" name="responsable_prenom" value="{{ old('responsable_prenom') }}" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                    placeholder="Entrez le prénom">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                            @error('responsable_prenom')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 3: Informations de contact -->
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-green-500 rounded-full mr-4"></div>
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
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                    placeholder="email@exemple.com">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-blue-500">Un code OTP sera envoyé à cet email</p>
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
                            <div class="flex gap-3">
                                <div class="w-32">
                                    <select name="country_code" required
                                        class="w-full px-3 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white appearance-none">
                                        <option value="+225" selected>🇨🇮 +225</option>
                                        <option value="+33">🇫🇷 +33</option>
                                        <option value="+1">🇺🇸 +1</option>
                                        <option value="+44">🇬🇧 +44</option>
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <div class="relative">
                                        <input type="text" name="contact" value="{{ old('contact') }}" required
                                            maxlength="10"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)"
                                            class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                            placeholder="Ex: 0700000000">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <i class="fas fa-phone text-gray-400"></i>
                                        </div>
                                    </div>
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
                            <div class="flex gap-3">
                                <div class="w-32">
                                    <select name="country_code_urgence"
                                        class="w-full px-3 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white appearance-none">
                                        <option value="+225" selected>🇨🇮 +225</option>
                                        <option value="+33">🇫🇷 +33</option>
                                        <option value="+1">🇺🇸 +1</option>
                                        <option value="+44">🇬🇧 +44</option>
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <div class="relative">
                                        <input type="text" name="contact_urgence" value="{{ old('contact_urgence') }}"
                                            maxlength="10"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)"
                                            class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                            placeholder="Ex: 0100000000">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <i class="fas fa-exclamation-triangle text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">Personne à contacter en cas d'urgence</p>
                            @error('contact_urgence')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Commune -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Commune</span>
                            </label>
                            <div class="relative">
                                <input type="text" name="commune" value="{{ old('commune') }}"
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                    placeholder="Ex: Cocody, Yopougon...">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <i class="fas fa-map-pin text-gray-400"></i>
                                </div>
                            </div>
                            @error('commune')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 4: Photo de profil -->
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-purple-500 rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Photo de profil</h2>
                    </div>

                    <div class="flex justify-center">
                        <div class="max-w-md w-full">
                            <div class="text-center space-y-4">
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
                    <a href="{{ route('gare.index') }}" 
                       class="flex items-center px-8 py-4 text-gray-700 font-semibold rounded-xl border border-gray-300 hover:bg-gray-50 transition-all duration-200 group">
                        <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour à la liste
                    </a>
                    
                    <button type="submit"
                            class="flex items-center px-8 py-4 bg-[#e94f1b] text-white font-bold rounded-xl hover:bg-[#e89116] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Enregistrer la gare
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    new TomSelect("#select-ville",{
        create: false,
        sortField: { field: "text", direction: "asc" },
        placeholder: "Rechercher une ville...",
        maxOptions: 50
    });

    // Image upload preview
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
            defaultAvatar.classList.remove('hidden');
            imagePreview.classList.add('hidden');
            profileImageInput.value = '';
        }
    });

    // SweetAlert messages
    @if(session('success'))
        Swal.fire({
            icon: 'success', title: 'Succès!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#e94f1b', timer: 5000, showConfirmButton: true
        });
    @endif

    @if(session('warning'))
        Swal.fire({
            icon: 'warning', title: 'Attention',
            text: "{{ session('warning') }}",
            confirmButtonColor: '#e94f1b'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error', title: 'Erreur',
            text: "{{ session('error') }}",
            confirmButtonColor: '#e94f1b'
        });
    @endif
</script>
@endsection
