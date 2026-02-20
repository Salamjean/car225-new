@extends('compagnie.layouts.template')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#e94e1a] rounded-2xl shadow-lg mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Modifier Caissière</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Modifiez les informations de {{ $caisse->prenom }} {{ $caisse->name }}
            </p>
        </div>

        <!-- Carte du formulaire -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="{{ route('compagnie.caisse.update', $caisse->id) }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf
                @method('PUT')

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
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white">
                                <option value="" disabled>-- Sélectionnez une gare --</option>
                                @foreach($gares as $gare)
                                    <option value="{{ $gare->id }}" {{ old('gare_id', $caisse->gare_id) == $gare->id ? 'selected' : '' }}>
                                        {{ $gare->nom_gare }} — {{ $gare->ville }}
                                    </option>
                                @endforeach
                            </select>
                            @error('gare_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                @endif

                <!-- Section 1: Informations personnelles -->
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-[#e94e1a] rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Informations personnelles</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">Nom</label>
                            <input type="text" name="name" value="{{ old('name', $caisse->name) }}" required
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">Prénom</label>
                            <input type="text" name="prenom" value="{{ old('prenom', $caisse->prenom) }}" required
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white">
                            @error('prenom') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">Email</label>
                            <input type="email" name="email" value="{{ old('email', $caisse->email) }}" required
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">Contact</label>
                            <input type="text" name="contact" value="{{ old('contact', $caisse->contact) }}" required
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white">
                            @error('contact') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">Commune</label>
                            <input type="text" name="commune" value="{{ old('commune', $caisse->commune) }}"
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">Contact d'urgence</label>
                            <input type="text" name="cas_urgence" value="{{ old('cas_urgence', $caisse->cas_urgence) }}"
                                   class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white">
                        </div>
                    </div>
                </div>

                <!-- Photo -->
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-purple-500 rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Photo de profil</h2>
                    </div>

                    <div class="flex flex-col items-center">
                        <div class="mb-4">
                            @if($caisse->profile_picture)
                                <img id="preview" src="{{ asset('storage/' . $caisse->profile_picture) }}" class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg">
                            @else
                                <div id="default-avatar" class="w-32 h-32 bg-[#e94e1a] rounded-full flex items-center justify-center text-white font-bold">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <img id="preview" class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg hidden">
                            @endif
                        </div>
                        <label for="profile_picture" class="cursor-pointer bg-gray-100 px-6 py-2 rounded-lg border-2 border-dashed border-gray-300 hover:border-[#e94e1a] transition-all">
                            <span>Changer la photo</span>
                            <input type="file" id="profile_picture" name="profile_picture" class="hidden" accept="image/*">
                        </label>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-8 border-t border-gray-200">
                    <a href="{{ route('compagnie.caisse.index') }}" class="px-8 py-4 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all">Retour</a>
                    <button type="submit" class="px-12 py-4 bg-[#e94e1a] text-white font-bold rounded-xl hover:bg-[#d33d0f] shadow-lg transition-all">Enregistrer les modifications</button>
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
            const defaultAvatar = document.getElementById('default-avatar');
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if(defaultAvatar) defaultAvatar.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
