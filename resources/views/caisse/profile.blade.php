@extends('caisse.layouts.template')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Mon Profil</h1>
            <p class="text-lg text-gray-600">Gérez vos informations personnelles</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Photo de profil -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl shadow-xl p-8">
                    <div class="text-center">
                        @if($caisse->profile_picture)
                            <img src="{{ asset('storage/' . $caisse->profile_picture) }}" 
                                 class="w-40 h-40 rounded-full object-cover mx-auto mb-4 border-4 border-[#e94e1a]" 
                                 alt="Photo de profil">
                        @else
                            <div class="w-40 h-40 rounded-full mx-auto mb-4 border-4 border-[#e94e1a] bg-gradient-to-r from-[#e94e1a] to-[#d33d0f] flex items-center justify-center">
                                <span class="text-6xl font-bold text-white">
                                    {{ strtoupper(substr($caisse->prenom, 0, 1)) }}{{ strtoupper(substr($caisse->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        
                        <h3 class="text-2xl font-bold text-gray-900">{{ $caisse->prenom }} {{ $caisse->name }}</h3>
                        <p class="text-gray-600">{{ $caisse->email }}</p>
                        
                        <div class="mt-6 space-y-3">
                            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                                <span class="text-sm text-gray-600">Tickets</span>
                                <span class="font-bold text-[#e94e1a]">{{ $caisse->tickets }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                <span class="text-sm text-gray-600">Statut</span>
                                <span class="font-bold text-green-600">{{ $caisse->isArchived() ? 'Archivé' : 'Actif' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaires -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informations personnelles -->
                <div class="bg-white rounded-3xl shadow-xl p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-[#e94e1a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Informations Personnelles
                    </h2>

                    <form action="{{ route('caisse.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nom *</label>
                                <input type="text" name="name" value="{{ old('name', $caisse->name) }}" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Prénom *</label>
                                <input type="text" name="prenom" value="{{ old('prenom', $caisse->prenom) }}" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                                @error('prenom')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Contact *</label>
                                <input type="text" name="contact" value="{{ old('contact', $caisse->contact) }}" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                                @error('contact')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Cas d'urgence</label>
                                <input type="text" name="cas_urgence" value="{{ old('cas_urgence', $caisse->cas_urgence) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                                @error('cas_urgence')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Commune</label>
                                <input type="text" name="commune" value="{{ old('commune', $caisse->commune) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                                @error('commune')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Photo de profil</label>
                                <input type="file" name="profile_picture" accept="image/*"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                                <p class="text-xs text-gray-500 mt-1">JPG, PNG (Max: 2MB)</p>
                                @error('profile_picture')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" 
                                class="w-full md:w-auto px-8 py-3 bg-[#e94e1a] text-white font-bold rounded-xl hover:bg-[#d33d0f] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Changer le mot de passe -->
                <div class="bg-white rounded-3xl shadow-xl p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-[#e94e1a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Changer le Mot de Passe
                    </h2>

                    <form action="{{ route('caisse.profile.password') }}" method="POST">
                        @csrf
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Mot de passe actuel *</label>
                                <input type="password" name="current_password" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                                @error('current_password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nouveau mot de passe *</label>
                                <input type="password" name="new_password" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                                @error('new_password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Confirmer le mot de passe *</label>
                                <input type="password" name="new_password_confirmation" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" 
                                class="w-full md:w-auto px-8 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                                Changer le mot de passe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Succès!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#e94e1a',
            timer: 3000,
            timerProgressBar: true
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Erreur!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d33'
        });
    @endif
</script>
@endsection
