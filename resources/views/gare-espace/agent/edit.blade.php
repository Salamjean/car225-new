@extends('gare-espace.layouts.template')
@section('title', "Modifier l'Agent")
@section('content')

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="max-width: 860px">

        {{-- En-tête --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#3b82f6] rounded-2xl shadow-lg mb-4">
                <i class="fas fa-user-edit text-white text-2xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Modifier l'Agent</h1>
            <p class="text-gray-500 text-base">Mettez à jour les informations de {{ $agent->name }} {{ $agent->prenom }}</p>
        </div>

        {{-- Carte principale --}}
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="{{ route('gare-espace.agents.update', $agent) }}" method="POST"
                  enctype="multipart/form-data" id="agentForm">
                @csrf
                @method('PUT')

                {{-- Photo de profil --}}
                <div class="p-8 border-b border-gray-100">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-[#3b82f6] rounded-full mr-4"></div>
                        <h2 class="text-xl font-bold text-gray-900">Photo d'identité</h2>
                        <span class="ml-3 text-sm text-gray-400 font-medium">(optionnel)</span>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-6">
                        {{-- Aperçu --}}
                        <div class="relative flex-shrink-0">
                            <div id="avatarWrapper" class="w-28 h-28 rounded-2xl overflow-hidden bg-gradient-to-br from-[#3b82f6] to-blue-400 flex items-center justify-center shadow-md">
                                @if($agent->profile_picture)
                                    <img id="avatarPreview" src="{{ asset('storage/' . $agent->profile_picture) }}" alt="Aperçu" class="w-full h-full object-cover">
                                    <div id="avatarDefault" class="hidden">
                                        <i class="fas fa-user text-white text-4xl"></i>
                                    </div>
                                @else
                                    <div id="avatarDefault">
                                        <i class="fas fa-user text-white text-4xl"></i>
                                    </div>
                                    <img id="avatarPreview" src="" alt="Aperçu" class="w-full h-full object-cover hidden">
                                @endif
                            </div>
                            <label for="profile_picture"
                                class="absolute -bottom-2 -right-2 w-9 h-9 bg-white border-2 border-[#3b82f6] rounded-full flex items-center justify-center cursor-pointer shadow-md hover:bg-[#3b82f6] hover:text-white transition-all group">
                                <i class="fas fa-camera text-[#3b82f6] group-hover:text-white"></i>
                                <input type="file" id="profile_picture" name="profile_picture" class="hidden" accept="image/*">
                            </label>
                        </div>

                        {{-- Infos upload --}}
                        <div class="text-center sm:text-left">
                            <p class="font-semibold text-gray-700 mb-1">Modifier la photo</p>
                            <p class="text-sm text-gray-400 mb-3">Formats acceptés : JPG, PNG · Max 2 Mo</p>
                        </div>
                    </div>

                    @error('profile_picture')
                        <p class="text-red-500 text-xs mt-3">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Informations personnelles --}}
                <div class="p-8 border-b border-gray-100">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-[#3b82f6] rounded-full mr-4"></div>
                        <h2 class="text-xl font-bold text-gray-900">Informations personnelles</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Nom --}}
                        <div class="space-y-1.5">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                Nom de famille <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input type="text" name="name" value="{{ old('name', $agent->name) }}" required
                                    class="w-full pl-10 pr-4 py-3.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#3b82f6] focus:border-transparent transition-all duration-200 text-sm font-medium outline-none @error('name') border-red-400 bg-red-50 @enderror">
                            </div>
                        </div>

                        {{-- Prénom --}}
                        <div class="space-y-1.5">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                Prénom(s) <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input type="text" name="prenom" value="{{ old('prenom', $agent->prenom) }}" required
                                    class="w-full pl-10 pr-4 py-3.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#3b82f6] focus:border-transparent transition-all duration-200 text-sm font-medium outline-none @error('prenom') border-red-400 bg-red-50 @enderror">
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="space-y-1.5">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                Adresse email <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input type="email" name="email" value="{{ old('email', $agent->email) }}" required
                                    class="w-full pl-10 pr-4 py-3.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#3b82f6] focus:border-transparent transition-all duration-200 text-sm font-medium outline-none @error('email') border-red-400 bg-red-50 @enderror">
                            </div>
                        </div>

                        {{-- Commune --}}
                        <div class="space-y-1.5">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                Commune de résidence <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="fas fa-map-marker-alt text-gray-400"></i>
                                </div>
                                <input type="text" name="commune" value="{{ old('commune', $agent->commune) }}" required
                                    class="w-full pl-10 pr-4 py-3.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#3b82f6] focus:border-transparent transition-all duration-200 text-sm font-medium outline-none @error('commune') border-red-400 bg-red-50 @enderror">
                            </div>
                        </div>

                        {{-- Téléphone --}}
                        <div class="space-y-1.5">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                Numéro de téléphone <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="fas fa-phone text-gray-400"></i>
                                </div>
                                <input type="text" name="contact" value="{{ old('contact', $agent->contact) }}" required
                                    class="w-full pl-10 pr-4 py-3.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#3b82f6] focus:border-transparent transition-all duration-200 text-sm font-medium outline-none @error('contact') border-red-400 bg-red-50 @enderror">
                            </div>
                        </div>

                        {{-- Contact d'urgence --}}
                        <div class="md:col-span-2 pt-4 border-t border-gray-100 mt-2">
                            <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-life-ring mr-2 text-red-500"></i>
                                Personne à contacter en cas d'urgence
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="md:col-span-2 space-y-1.5">
                                    <label class="flex items-center text-sm font-semibold text-gray-700">
                                        Nom complet d'urgence <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <input type="text" name="nom_urgence" value="{{ old('nom_urgence', $agent->nom_urgence) }}" required
                                        class="w-full px-4 py-3.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#3b82f6] outline-none text-sm">
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-sm font-semibold text-gray-700">Lien parenté</label>
                                    <select name="lien_parente_urgence" required class="w-full px-4 py-3.5 border border-gray-200 rounded-xl bg-gray-50 text-sm">
                                        @foreach(['Père', 'Mère', 'Frère', 'Sœur', 'Oncle', 'Tante', 'Conjoint(e)', 'Autre'] as $lien)
                                            <option value="{{ $lien }}" {{ old('lien_parente_urgence', $agent->lien_parente_urgence) == $lien ? 'selected' : '' }}>{{ $lien }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-sm font-semibold text-gray-700">Contact d'urgence</label>
                                    <input type="text" name="cas_urgence" value="{{ old('cas_urgence', $agent->cas_urgence) }}" required
                                        class="w-full px-4 py-3.5 border border-gray-200 rounded-xl bg-gray-50 text-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="p-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <a href="{{ route('gare-espace.agents.index') }}"
                        class="flex items-center gap-2 px-6 py-3.5 text-gray-700 font-semibold rounded-xl border border-gray-200 hover:bg-gray-50 transition-all duration-200 text-sm">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>

                    <button type="submit" class="flex items-center gap-2 px-10 py-3.5 bg-[#3b82f6] text-white font-bold rounded-xl hover:bg-blue-700 transform hover:-translate-y-0.5 transition-all duration-200 shadow-lg text-sm">
                        <i class="fas fa-save mr-2"></i> Enregistrer les modifications
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const photoInput = document.getElementById('profile_picture');
    const preview = document.getElementById('avatarPreview');
    const defaultIcon = document.getElementById('avatarDefault');

    photoInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if(defaultIcon) defaultIcon.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endsection
