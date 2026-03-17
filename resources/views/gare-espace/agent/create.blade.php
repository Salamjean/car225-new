@extends('gare-espace.layouts.template')
@section('title', 'Nouvel Agent')
@section('content')

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="max-width: 860px">

        {{-- En-tête --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#e94f1b] rounded-2xl shadow-lg mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Nouvel Agent</h1>
            <p class="text-gray-500 text-base">Créez un profil complet pour votre futur collaborateur de gare</p>
        </div>

        {{-- Carte principale --}}
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="{{ route('gare-espace.agents.store') }}" method="POST"
                  enctype="multipart/form-data" id="agentForm">
                @csrf

                {{-- Photo de profil --}}
                <div class="p-8 border-b border-gray-100">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-[#e94f1b] rounded-full mr-4"></div>
                        <h2 class="text-xl font-bold text-gray-900">Photo d'identité</h2>
                        <span class="ml-3 text-sm text-gray-400 font-medium">(optionnel)</span>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-6">
                        {{-- Aperçu --}}
                        <div class="relative flex-shrink-0">
                            <div id="avatarWrapper" class="w-28 h-28 rounded-2xl overflow-hidden bg-gradient-to-br from-[#e94f1b] to-orange-400 flex items-center justify-center shadow-md">
                                <div id="avatarDefault">
                                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <img id="avatarPreview" src="" alt="Aperçu" class="w-full h-full object-cover hidden">
                            </div>
                            <label for="profile_picture"
                                class="absolute -bottom-2 -right-2 w-9 h-9 bg-white border-2 border-[#e94f1b] rounded-full flex items-center justify-center cursor-pointer shadow-md hover:bg-[#e94f1b] hover:text-white transition-all group">
                                <svg class="w-4 h-4 text-[#e94f1b] group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <input type="file" id="profile_picture" name="profile_picture" class="hidden" accept="image/*">
                            </label>
                        </div>

                        {{-- Infos upload --}}
                        <div class="text-center sm:text-left">
                            <p class="font-semibold text-gray-700 mb-1">Choisir une photo</p>
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
                        <div class="w-2 h-8 bg-[#e94f1b] rounded-full mr-4"></div>
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
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    placeholder="Ex : Bakayoko"
                                    class="w-full pl-10 pr-4 py-3.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-200 text-sm font-medium outline-none @error('name') border-red-400 bg-red-50 @enderror">
                            </div>
                            @error('name')
                                <p class="text-red-500 text-xs flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Prénom --}}
                        <div class="space-y-1.5">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                Prénom(s) <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <input type="text" name="prenom" value="{{ old('prenom') }}" required
                                    placeholder="Ex : Jean-Marc"
                                    class="w-full pl-10 pr-4 py-3.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-200 text-sm font-medium outline-none @error('prenom') border-red-400 bg-red-50 @enderror">
                            </div>
                            @error('prenom')
                                <p class="text-red-500 text-xs flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="space-y-1.5">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                Adresse email <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    placeholder="agent@compagnie.com"
                                    class="w-full pl-10 pr-4 py-3.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-200 text-sm font-medium outline-none @error('email') border-red-400 bg-red-50 @enderror">
                            </div>
                            @error('email')
                                <p class="text-red-500 text-xs flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Commune --}}
                        <div class="space-y-1.5">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                Commune de résidence <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <input type="text" name="commune" value="{{ old('commune') }}" required
                                    placeholder="Ex : Cocody, Yopougon..."
                                    class="w-full pl-10 pr-4 py-3.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-200 text-sm font-medium outline-none @error('commune') border-red-400 bg-red-50 @enderror">
                            </div>
                            @error('commune')
                                <p class="text-red-500 text-xs flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Téléphone --}}
                        <div class="space-y-1.5">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                Numéro de téléphone <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <input type="text" name="contact" value="{{ old('contact') }}" required
                                    placeholder="07 XX XX XX XX" maxlength="10" minlength="10"
                                    pattern="[0-9]{10}" inputmode="numeric"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                    class="w-full pl-10 pr-4 py-3.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-200 text-sm font-medium outline-none @error('contact') border-red-400 bg-red-50 @enderror">
                            </div>
                            <p class="text-xs text-gray-400">10 chiffres sans espaces ni tirets</p>
                            @error('contact')
                                <p class="text-red-500 text-xs flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
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
                                        Nom et prénom de la personne à contacter <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <i class="fas fa-user-shield text-gray-400"></i>
                                        </div>
                                        <input type="text" name="nom_urgence" value="{{ old('nom_urgence') }}" required
                                            placeholder="Ex : Awa Sanogo"
                                            class="w-full pl-10 pr-4 py-3.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-200 text-sm font-medium outline-none @error('nom_urgence') border-red-400 bg-red-50 @enderror">
                                    </div>
                                    @error('nom_urgence')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-1.5">
                                    <label class="flex items-center text-sm font-semibold text-gray-700">
                                        Lien de parenté <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <i class="fas fa-users text-gray-400"></i>
                                        </div>
                                        <select name="lien_parente_urgence" required
                                            class="w-full pl-10 pr-4 py-3.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-200 text-sm font-medium outline-none appearance-none @error('lien_parente_urgence') border-red-400 bg-red-50 @enderror">
                                            <option value="">Sélectionner</option>
                                            <option value="Père" {{ old('lien_parente_urgence') == 'Père' ? 'selected' : '' }}>Père</option>
                                            <option value="Mère" {{ old('lien_parente_urgence') == 'Mère' ? 'selected' : '' }}>Mère</option>
                                            <option value="Frère" {{ old('lien_parente_urgence') == 'Frère' ? 'selected' : '' }}>Frère</option>
                                            <option value="Sœur" {{ old('lien_parente_urgence') == 'Sœur' ? 'selected' : '' }}>Sœur</option>
                                            <option value="Oncle" {{ old('lien_parente_urgence') == 'Oncle' ? 'selected' : '' }}>Oncle</option>
                                            <option value="Tante" {{ old('lien_parente_urgence') == 'Tante' ? 'selected' : '' }}>Tante</option>
                                            <option value="Conjoint(e)" {{ old('lien_parente_urgence') == 'Conjoint(e)' ? 'selected' : '' }}>Conjoint(e)</option>
                                            <option value="Autre" {{ old('lien_parente_urgence') == 'Autre' ? 'selected' : '' }}>Autre</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                        </div>
                                    </div>
                                    @error('lien_parente_urgence')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-1.5">
                                    <label class="flex items-center text-sm font-semibold text-gray-700">
                                        Contact d'urgence <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <i class="fas fa-phone-alt text-gray-400"></i>
                                        </div>
                                        <input type="text" name="cas_urgence" value="{{ old('cas_urgence') }}" required
                                            placeholder="05 XX XX XX XX" maxlength="10" minlength="10"
                                            pattern="[0-9]{10}" inputmode="numeric"
                                            oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                            class="w-full pl-10 pr-4 py-3.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-200 text-sm font-medium outline-none @error('cas_urgence') border-red-400 bg-red-50 @enderror">
                                    </div>
                                    <p class="text-[10px] text-gray-400 italic">Doit être différent du contact principal.</p>
                                    @error('cas_urgence')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="p-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <a href="{{ route('gare-espace.agents.index') }}"
                        class="flex items-center gap-2 px-6 py-3.5 text-gray-700 font-semibold rounded-xl border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 text-sm group">
                        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour à la liste
                    </a>

                    <div class="flex items-center gap-3">
                        <button type="reset" id="resetBtn"
                            class="flex items-center gap-2 px-5 py-3.5 text-gray-500 font-semibold rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 transition-all duration-200 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Réinitialiser
                        </button>

                        <button type="submit" id="submitBtn"
                            class="flex items-center gap-2 px-7 py-3.5 bg-[#e94f1b] text-white font-bold rounded-xl hover:bg-[#d33d0f] transform hover:-translate-y-0.5 transition-all duration-200 shadow-lg hover:shadow-xl text-sm">
                            <svg id="submitIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            <svg id="submitSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Créer le profil agent
                        </button>
                    </div>
                </div>

            </form>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Photo preview ── */
    const photoInput   = document.getElementById('profile_picture');
    const preview      = document.getElementById('avatarPreview');
    const defaultIcon  = document.getElementById('avatarDefault');

    photoInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({ icon: 'warning', title: 'Fichier trop volumineux', text: 'Max 2 Mo autorisé.', confirmButtonColor: '#e94f1b' });
            this.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            defaultIcon.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    });

    /* ── Reset photo ── */
    document.getElementById('agentForm').addEventListener('reset', () => {
        setTimeout(() => {
            preview.src = '';
            preview.classList.add('hidden');
            defaultIcon.classList.remove('hidden');
        }, 10);
    });

    /* ── Submit spinner ── */
    document.getElementById('agentForm').addEventListener('submit', function (e) {
        document.getElementById('submitIcon').classList.add('hidden');
        document.getElementById('submitSpinner').classList.remove('hidden');
    });

    /* ── Alerts ── */
    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Agent créé !', text: '{{ session("success") }}', timer: 3000, showConfirmButton: false, toast: true, position: 'top-end' });
    @endif
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Erreur de saisie',
            html: `<ul style="text-align:left;padding-left:1rem;list-style:none;font-size:0.9rem">
                @foreach($errors->all() as $err)
                    <li style="margin-bottom:4px">• {{ $err }}</li>
                @endforeach
            </ul>`,
            confirmButtonColor: '#e94f1b',
            confirmButtonText: 'Corriger'
        });
    @endif
});
</script>

@endsection
