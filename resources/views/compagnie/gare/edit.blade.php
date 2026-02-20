@extends('compagnie.layouts.template')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
    <div class="mx-auto" style="width: 80%">
        <!-- En-tête -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#e94f1b] rounded-2xl shadow-lg mb-4">
                <i class="fas fa-edit text-3xl text-white"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Modifier la Gare</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Mettre à jour les informations de {{ $gare->nom_gare }}
            </p>
        </div>

        <!-- Formulaire -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="{{ route('gare.update', $gare->id) }}" method="POST" class="p-8">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Nom de la gare -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Nom de la gare <span class="text-red-500">*</span></label>
                        <input type="text" name="nom_gare" value="{{ old('nom_gare', $gare->nom_gare) }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent outline-none transition-all bg-gray-50 focus:bg-white"
                            placeholder="Ex: Gare de Bassam">
                        @error('nom_gare')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ville -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Ville <span class="text-red-500">*</span></label>
                        <select name="ville" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent outline-none transition-all bg-gray-50 focus:bg-white">
                            <option value="">Sélectionnez une ville</option>
                            @php
                                $villes = [
                                    'Abidjan', 'Abengourou', 'Adzopé', 'Agboville', 'Anyama', 'Bondoukou', 'Bongouanou', 'Bouaflé', 'Bouaké', 
                                    'Boundiali', 'Bouna', 'Dabou', 'Daloa', 'Divo', 'Duékoué', 'Ferkessédougou', 'Gagnoa', 
                                    'Grand-Bassam', 'Guiglo', 'Issia', 'Katiola', 'Korhogo', 'Man', 'Odienné', 'Oumé', 
                                    'San-Pédro', 'Séguéla', 'Sinfra', 'Soubré', 'Tanda', 'Touba', 'Toumodi', 'Vavoua', 
                                    'Yamoussoukro', 'Zénoula'
                                ];
                                sort($villes);
                            @endphp
                            @foreach($villes as $ville)
                                <option value="{{ $ville }}" {{ old('ville', $gare->ville) == $ville ? 'selected' : '' }}>
                                    {{ $ville }}
                                </option>
                            @endforeach
                        </select>
                        @error('ville')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Adresse -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Adresse / Situation Géographique <span class="text-red-500">*</span></label>
                        <input type="text" name="adresse" value="{{ old('adresse', $gare->adresse) }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent outline-none transition-all bg-gray-50 focus:bg-white"
                            placeholder="Ex: Rue 12, Face au marché">
                        @error('adresse')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Commune -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Commune</label>
                        <input type="text" name="commune" value="{{ old('commune', $gare->commune) }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent outline-none transition-all bg-gray-50 focus:bg-white"
                            placeholder="Ex: Cocody">
                        @error('commune')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-full border-t border-gray-100 my-4"></div>

                    <!-- Responsable Nom -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Nom du Responsable <span class="text-red-500">*</span></label>
                        <input type="text" name="responsable_nom" value="{{ old('responsable_nom', $gare->responsable_nom) }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent outline-none transition-all bg-gray-50 focus:bg-white"
                            placeholder="Nom du responsable">
                        @error('responsable_nom')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Responsable Prénom -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Prénom du Responsable <span class="text-red-500">*</span></label>
                        <input type="text" name="responsable_prenom" value="{{ old('responsable_prenom', $gare->responsable_prenom) }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent outline-none transition-all bg-gray-50 focus:bg-white"
                            placeholder="Prénom du responsable">
                        @error('responsable_prenom')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $gare->email) }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent outline-none transition-all bg-gray-50 focus:bg-white"
                            placeholder="email@exemple.com">
                        <p class="text-xs text-orange-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            La modification de l'email nécessitera une vérification par code OTP.
                        </p>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contact -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Contact <span class="text-red-500">*</span></label>
                        <input type="text" name="contact" value="{{ old('contact', $gare->contact) }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent outline-none transition-all bg-gray-50 focus:bg-white"
                            placeholder="Contact principal">
                        @error('contact')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contact Urgence -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Contact d'Urgence</label>
                        <input type="text" name="contact_urgence" value="{{ old('contact_urgence', $gare->contact_urgence) }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent outline-none transition-all bg-gray-50 focus:bg-white"
                            placeholder="Contact d'urgence">
                        @error('contact_urgence')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Photo de profil (Optionnel) -->
                     <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Photo de profil</label>
                        <input type="file" name="profile_image" accept="image/*"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent outline-none transition-all bg-gray-50 focus:bg-white">
                        @if($gare->profile_image)
                            <div class="mt-2">
                                <span class="text-xs text-gray-500">Image actuelle:</span>
                                <img src="{{ asset('storage/' . $gare->profile_image) }}" alt="Profile" class="h-10 w-10 rounded-full object-cover mt-1">
                            </div>
                        @endif
                        @error('profile_image')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 justify-between items-center pt-8 border-t border-gray-100">
                    <a href="{{ route('gare.index') }}"
                        class="flex items-center px-8 py-3 text-gray-700 font-semibold rounded-xl border border-gray-300 hover:bg-gray-50 transition-all">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Retour
                    </a>

                    <button type="submit"
                        class="flex items-center px-8 py-3 bg-[#e94f1b] text-white font-bold rounded-xl hover:bg-[#e89116] transform hover:-translate-y-1 transition-all shadow-lg">
                        <i class="fas fa-check mr-2"></i>
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
