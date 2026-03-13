@extends('agent.layouts.template')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Mon Profil Agent</h1>
            <p class="text-lg text-gray-600">Gérez vos informations personnelles et votre compte</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Photo de profil -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl shadow-xl p-8 sticky top-24">
                    <div class="text-center">
                        <div class="relative inline-block mb-6">
                            @if($agent->profile_picture)
                                <img src="{{ asset('storage/' . $agent->profile_picture) }}" 
                                     class="w-40 h-40 rounded-3xl object-cover mx-auto border-4 border-[#e94e1a] shadow-lg" 
                                     alt="Photo de profil">
                            @else
                                <div class="w-40 h-40 rounded-3xl mx-auto border-4 border-[#e94e1a] bg-gradient-to-br from-[#e94e1a] to-[#d33d0f] flex items-center justify-center shadow-lg">
                                    <span class="text-6xl font-bold text-white">
                                        {{ strtoupper(substr($agent->prenom, 0, 1)) }}{{ strtoupper(substr($agent->name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                            <div class="absolute -bottom-2 -right-2 bg-green-500 text-white p-2 rounded-xl shadow-md">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $agent->prenom }} {{ $agent->name }}</h3>
                        <p class="text-[#e94e1a] font-semibold mb-4">{{ $agent->email }}</p>
                        
                        <div class="space-y-3 mt-8">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-orange-100 text-[#e94e1a] flex items-center justify-center">
                                        <i class="fas fa-id-badge"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">Code ID</span>
                                </div>
                                <span class="font-bold text-gray-900">{{ $agent->code_id ?? 'N/A' }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">Compagnie</span>
                                </div>
                                <span class="font-bold text-gray-900">{{ $agent->compagnie->name ?? 'N/A' }}</span>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">Gare</span>
                                </div>
                                <span class="font-bold text-gray-900 text-right">{{ $agent->gare->nom_gare ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaires -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Informations personnelles -->
                <div class="bg-white rounded-3xl shadow-xl p-8 transition-all hover:shadow-2xl">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                            <div class="w-10 h-10 rounded-xl bg-orange-100 text-[#e94e1a] flex items-center justify-center mr-4">
                                <i class="fas fa-user-edit"></i>
                            </div>
                            Modifier mes informations
                        </h2>
                    </div>

                    <form action="{{ route('agent.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Nom *</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" name="name" value="{{ old('name', $agent->name) }}" required
                                        class="w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-[#e94e1a] focus:bg-white transition-all outline-none"
                                        placeholder="Votre nom">
                                </div>
                                @error('name') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Prénom *</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" name="prenom" value="{{ old('prenom', $agent->prenom) }}" required
                                        class="w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-[#e94e1a] focus:bg-white transition-all outline-none"
                                        placeholder="Votre prénom">
                                </div>
                                @error('prenom') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Contact *</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="text" name="contact" value="{{ old('contact', $agent->contact) }}" required
                                        class="w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-[#e94e1a] focus:bg-white transition-all outline-none"
                                        placeholder="Votre numéro de téléphone">
                                </div>
                                @error('contact') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Cas d'urgence</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                        <i class="fas fa-ambulance"></i>
                                    </span>
                                    <input type="text" name="cas_urgence" value="{{ old('cas_urgence', $agent->cas_urgence) }}"
                                        class="w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-[#e94e1a] focus:bg-white transition-all outline-none"
                                        placeholder="Contact d'urgence">
                                </div>
                                @error('cas_urgence') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2 space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Commune / Adresse</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <input type="text" name="commune" value="{{ old('commune', $agent->commune) }}"
                                        class="w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-[#e94e1a] focus:bg-white transition-all outline-none"
                                        placeholder="Votre lieu de résidence">
                                </div>
                                @error('commune') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2 space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Changer la photo de profil</label>
                                <div class="flex items-center justify-center w-full">
                                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                                            <p class="text-sm text-gray-500">Cliquez pour téléverser une image</p>
                                        </div>
                                        <input type="file" name="profile_picture" class="hidden" accept="image/*" />
                                    </label>
                                </div>
                                <p class="text-[10px] text-gray-400 text-center">Recommandé : JPG ou PNG. Max 2 Mo.</p>
                                @error('profile_picture') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" 
                                class="w-full py-4 bg-gradient-to-r from-[#e94e1a] to-[#d33d0f] text-white font-bold rounded-2xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
                                Sauvegarder les modifications
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Changer le mot de passe -->
                <div class="bg-white rounded-3xl shadow-xl p-8 transition-all hover:shadow-2xl border-l-8 border-blue-500">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8 flex items-center">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mr-4">
                            <i class="fas fa-lock"></i>
                        </div>
                        Sécurité du compte
                    </h2>

                    <form action="{{ route('agent.profile.password') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Mot de passe actuel *</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                        <i class="fas fa-shield-alt"></i>
                                    </span>
                                    <input type="password" name="current_password" required
                                        class="w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-500 focus:bg-white transition-all outline-none"
                                        placeholder="Mot de passe actuel">
                                </div>
                                @error('current_password') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Nouveau mot de passe *</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                            <i class="fas fa-key"></i>
                                        </span>
                                        <input type="password" name="new_password" required
                                            class="w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-500 focus:bg-white transition-all outline-none"
                                            placeholder="Nouveau mot de passe">
                                    </div>
                                    @error('new_password') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Confirmer le mot de passe *</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                            <i class="fas fa-check-double"></i>
                                        </span>
                                        <input type="password" name="new_password_confirmation" required
                                            class="w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-500 focus:bg-white transition-all outline-none"
                                            placeholder="Confirmer">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" 
                                class="w-full py-4 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
                                Mettre à jour le mot de passe
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
            title: 'Parfait!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#e94e1a',
            timer: 3500,
            timerProgressBar: true,
            background: '#fff',
            customClass: {
                popup: 'rounded-3xl'
            }
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Oups...',
            text: '{{ $errors->first() }}',
            confirmButtonColor: '#d33',
            background: '#fff',
            customClass: {
                popup: 'rounded-3xl'
            }
        });
    @endif
</script>
@endsection
