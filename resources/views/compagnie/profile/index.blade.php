@extends('compagnie.layouts.template')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Profil Compagnie</h1>
            <p class="text-lg text-gray-600">Gérez les informations de votre structure</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Logo de la compagnie -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl shadow-xl p-8">
                    <div class="text-center">
                        <div class="relative w-48 h-48 mx-auto mb-6 group cursor-pointer border-4 border-[#e94e1a] rounded-2xl overflow-hidden shadow-inner bg-gray-50 flex items-center justify-center" onclick="document.getElementById('logoInput').click()">
                            @if($compagnie->path_logo)
                                <img src="{{ asset('storage/' . $compagnie->path_logo) }}" 
                                     class="w-full h-full object-contain transition-all duration-300 group-hover:opacity-75" 
                                     id="logoPreview"
                                     alt="Logo {{ $compagnie->name }}">
                            @else
                                <div id="logoPlaceholder" class="w-full h-full flex items-center justify-center bg-[#e94f1b] text-white text-5xl font-bold">
                                    {{ substr($compagnie->name, 0, 2) }}
                                </div>
                                <img src="" id="logoPreview" class="w-full h-full object-contain hidden" alt="Logo">
                            @endif
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-black/20">
                                <i class="fas fa-camera text-3xl text-white"></i>
                            </div>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-gray-900">{{ $compagnie->name }}</h3>
                        <p class="text-gray-500 font-medium">{{ $compagnie->sigle ?? 'Pas de sigle' }}</p>
                        
                        <div class="mt-8 space-y-4">
                            <div class="flex items-center justify-between p-4 bg-orange-50 rounded-2xl border border-orange-100">
                                <div class="flex items-center">
                                    <i class="fas fa-ticket-alt text-[#e94e1a] mr-3"></i>
                                    <span class="text-sm text-gray-600">Solde Tickets</span>
                                </div>
                                <span class="font-bold text-xl text-[#e94e1a]">{{ number_format($compagnie->tickets, 0, ',', ' ') }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-2xl border border-blue-100">
                                <div class="flex items-center">
                                    <i class="fas fa-id-badge text-blue-600 mr-3"></i>
                                    <span class="text-sm text-gray-600">Identifiant</span>
                                </div>
                                <span class="font-bold text-gray-800">{{ $compagnie->username }}</span>
                            </div>
                        </div>

                        <p class="mt-6 text-sm text-gray-400 italic">"{{ $compagnie->slogan ?? 'Votre slogan ici' }}"</p>
                    </div>
                </div>
            </div>

            <!-- Formulaires -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Informations générales -->
                <div class="bg-white rounded-3xl shadow-xl p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8 flex items-center">
                        <div class="w-2 h-8 bg-[#e94e1a] rounded-full mr-4"></div>
                        Informations de la Compagnie
                    </h2>

                    <form action="{{ route('compagnie.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" id="logoInput" name="path_logo" accept="image/*" class="hidden" onchange="previewImage(this)">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Nom complet *</label>
                                <input type="text" name="name" value="{{ old('name', $compagnie->name) }}" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all bg-gray-50 focus:bg-white">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>



                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Email professionnel *</label>
                                <input type="email" name="email" value="{{ old('email', $compagnie->email) }}" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all bg-gray-50 focus:bg-white">
                                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Contact / Téléphone *</label>
                                <input type="text" name="contact" value="{{ old('contact', $compagnie->contact) }}" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all bg-gray-50 focus:bg-white">
                                @error('contact') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Sigle (ex: UTB)</label>
                                <input type="text" name="sigle" value="{{ old('sigle', $compagnie->sigle) }}"
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all bg-gray-50 focus:bg-white">
                                @error('sigle') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Slogan</label>
                                <input type="text" name="slogan" value="{{ old('slogan', $compagnie->slogan) }}"
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all bg-gray-50 focus:bg-white">
                                @error('slogan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Commune *</label>
                                <input type="text" name="commune" value="{{ old('commune', $compagnie->commune) }}" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all bg-gray-50 focus:bg-white">
                                @error('commune') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Adresse Géographique *</label>
                                <input type="text" name="adresse" value="{{ old('adresse', $compagnie->adresse) }}" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all bg-gray-50 focus:bg-white">
                                @error('adresse') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="mt-8">
                            <button type="submit" 
                                class="w-full md:w-auto px-10 py-4 bg-[#e94e1a] text-white font-bold rounded-xl hover:bg-[#d33d0f] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center">
                                <i class="fas fa-save mr-2"></i>
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Sécurité -->
                <div class="bg-white rounded-3xl shadow-xl p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8 flex items-center">
                        <div class="w-2 h-8 bg-blue-500 rounded-full mr-4"></div>
                        Sécurité & Mot de passe
                    </h2>

                    <form action="{{ route('compagnie.profile.password') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Mot de passe actuel</label>
                                <input type="password" name="current_password" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-gray-50 focus:bg-white">
                                @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Nouveau mot de passe</label>
                                <input type="password" name="password" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-gray-50 focus:bg-white">
                                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Confirmation</label>
                                <input type="password" name="password_confirmation" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-gray-50 focus:bg-white">
                            </div>
                        </div>

                        <div class="mt-8">
                            <button type="submit" 
                                class="w-full md:w-auto px-10 py-4 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center">
                                <i class="fas fa-lock mr-2"></i>
                                Mettre à jour le mot de passe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('logoPreview');
                const placeholder = document.getElementById('logoPlaceholder');
                
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Succès!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#e94e1a',
            timer: 3000
        });
    @endif
</script>
@endsection
