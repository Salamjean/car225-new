@extends('gare-espace.layouts.template')
@section('title', 'Modifier Personnel')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-2xl shadow-lg mb-4 text-white">
                <i class="fas fa-user-edit text-2xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Modifier Personnel</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Mise à jour du profil de {{ $personnel->name }} {{ $personnel->prenom }}
            </p>
        </div>

        <!-- Carte du formulaire -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="{{ route('gare-espace.personnel.update', $personnel) }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf
                @method('PUT')

                <!-- Section 1: Informations personnelles -->
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-blue-600 rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Informations personnelles</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Nom -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">Nom <span class="text-red-500 ml-1">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $personnel->name) }}" required
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all bg-gray-50 focus:bg-white text-sm font-medium">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Prénom -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">Prénom <span class="text-red-500 ml-1">*</span></label>
                            <input type="text" name="prenom" value="{{ old('prenom', $personnel->prenom) }}" required
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all bg-gray-50 focus:bg-white text-sm font-medium">
                            @error('prenom') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Type -->
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">Type <span class="text-red-500 ml-1">*</span></label>
                            <select name="type_personnel" required class="w-full px-4 py-4 border border-gray-200 rounded-xl bg-gray-50 text-sm font-medium">
                                <option value="Chauffeur" {{ old('type_personnel', $personnel->type_personnel) == 'Chauffeur' ? 'selected' : '' }}>Chauffeur</option>
                                <option value="Convoyeur" {{ old('type_personnel', $personnel->type_personnel) == 'Convoyeur' ? 'selected' : '' }}>Convoyeur</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Contacts -->
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-blue-600 rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Informations de contact</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Email <span class="text-red-500 ml-1">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $personnel->email) }}" required
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl text-sm bg-gray-50">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Contact <span class="text-red-500 ml-1">*</span></label>
                            <input type="text" name="contact" value="{{ old('contact', $personnel->contact) }}" required
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl text-sm bg-gray-50">
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-100 italic font-semibold text-gray-800 flex items-center mb-4">
                        <i class="fas fa-life-ring mr-2 text-red-500"></i>
                        Contact d'urgence
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-1 space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Nom d'urgence <span class="text-red-500 ml-1">*</span></label>
                            <input type="text" name="nom_urgence" value="{{ old('nom_urgence', $personnel->nom_urgence) }}" required
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl text-sm bg-gray-50">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Lien parenté <span class="text-red-500 ml-1">*</span></label>
                            <input type="text" name="lien_parente_urgence" value="{{ old('lien_parente_urgence', $personnel->lien_parente_urgence) }}" required
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl text-sm bg-gray-50">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Téléphone d'urgence <span class="text-red-500 ml-1">*</span></label>
                            <input type="text" name="contact_urgence" value="{{ old('contact_urgence', $personnel->contact_urgence) }}" required
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl text-sm bg-gray-50">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Photo -->
                <div class="mb-12">
                   <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-blue-600 rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Photo de profil</h2>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="relative">
                            <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-blue-50 shadow-md">
                                @if($personnel->profile_image)
                                    <img id="preview" src="{{ asset('storage/' . $personnel->profile_image) }}" class="w-full h-full object-cover">
                                @else
                                    <div id="previewFallback" class="w-full h-full bg-blue-100 flex items-center justify-center text-blue-400">
                                        <i class="fas fa-user text-4xl"></i>
                                    </div>
                                    <img id="preview" src="" class="w-full h-full object-cover hidden">
                                @endif
                            </div>
                            <label for="profile_image" class="absolute bottom-0 right-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center cursor-pointer shadow-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-camera"></i>
                                <input type="file" id="profile_image" name="profile_image" class="hidden" accept="image/*">
                            </label>
                        </div>
                        <div class="text-sm text-gray-500">
                            Format accepté: JPG, PNG, GIF. Max: 2 Mo.
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center pt-8 border-t border-gray-100">
                    <a href="{{ route('gare-espace.personnel.index') }}" class="px-8 py-4 text-gray-600 font-bold hover:text-gray-900 transition-colors">
                        Annuler
                    </a>
                    <button type="submit" class="px-10 py-4 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1">
                        Sauvegarder les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('profile_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('preview');
            const fallback = document.getElementById('previewFallback');
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (fallback) fallback.classList.add('hidden');
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
