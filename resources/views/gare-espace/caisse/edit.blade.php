@extends('gare-espace.layouts.template')
@section('title', 'Modifier Caissière')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-2xl shadow-lg mb-4 text-white">
                <i class="fas fa-cash-register text-2xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Modifier Caissière</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Mise à jour du profil de {{ $caisse->name }} {{ $caisse->prenom }}
            </p>
        </div>

        <!-- Carte du formulaire -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="{{ route('gare-espace.caisse.update', $caisse) }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf
                @method('PUT')

                <!-- Section 1: Informations personnelles -->
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-blue-600 rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Informations personnelles</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Nom <span class="text-red-500 ml-1">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $caisse->name) }}" required
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all bg-gray-50 text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Prénom <span class="text-red-500 ml-1">*</span></label>
                            <input type="text" name="prenom" value="{{ old('prenom', $caisse->prenom) }}" required
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all bg-gray-50 text-sm">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Contact -->
                <div class="mb-12">
                   <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-blue-600 rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Informations de contact</h2>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Email <span class="text-red-500 ml-1">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $caisse->email) }}" required
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl text-sm bg-gray-50">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Contact principal <span class="text-red-500 ml-1">*</span></label>
                            <input type="text" name="contact" value="{{ old('contact', $caisse->contact) }}" required
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl text-sm bg-gray-50">
                        </div>
                         <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Commune</label>
                            <input type="text" name="commune" value="{{ old('commune', $caisse->commune) }}" 
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl text-sm bg-gray-50">
                        </div>
                    </div>
                    
                    <div class="mt-8 pt-8 border-t border-gray-100 italic font-semibold text-gray-800 flex items-center mb-4">
                        <i class="fas fa-life-ring mr-2 text-red-500"></i>
                         L'urgence
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                         <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Nom urgence <span class="text-red-500 ml-1">*</span></label>
                            <input type="text" name="nom_urgence" value="{{ old('nom_urgence', $caisse->nom_urgence) }}" required
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl text-sm bg-gray-50">
                        </div>
                         <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Lien parenté <span class="text-red-500 ml-1">*</span></label>
                            <input type="text" name="lien_parente_urgence" value="{{ old('lien_parente_urgence', $caisse->lien_parente_urgence) }}" required
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl text-sm bg-gray-50">
                        </div>
                         <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Contact urgence <span class="text-red-500 ml-1">*</span></label>
                            <input type="text" name="cas_urgence" value="{{ old('cas_urgence', $caisse->cas_urgence) }}" required
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
                            <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-blue-50">
                                @if($caisse->profile_picture)
                                    <img id="preview" src="{{ asset('storage/' . $caisse->profile_picture) }}" class="w-full h-full object-cover">
                                @else
                                    <div id="previewFallback" class="w-full h-full bg-blue-100 flex items-center justify-center text-blue-400">
                                        <i class="fas fa-user text-4xl"></i>
                                    </div>
                                    <img id="preview" src="" class="w-full h-full object-cover hidden">
                                @endif
                            </div>
                            <label for="profile_picture" class="absolute bottom-0 right-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center cursor-pointer shadow-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-camera"></i>
                                <input type="file" id="profile_picture" name="profile_picture" class="hidden" accept="image/*">
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center pt-8 border-t border-gray-100">
                    <a href="{{ route('gare-espace.caisse.index') }}" class="px-8 py-4 text-gray-600 font-bold hover:text-gray-900 transition-colors">
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
document.getElementById('profile_picture').addEventListener('change', function(e) {
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
