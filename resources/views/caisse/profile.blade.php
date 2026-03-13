@extends('caisse.layouts.template')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50/30 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <!-- En-tête -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Mon Profil Caissière</h1>
            <p class="mt-2 text-lg text-gray-600">Gérez vos informations personnelles et votre compte</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Colonne de gauche : Aperçu -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 sticky top-24">
                    <div class="h-32 bg-gradient-to-r from-orange-500 to-red-600"></div>
                    <div class="px-6 pb-8">
                        <div class="relative -mt-16 mb-6 text-center">
                            @if($caisse->profile_picture)
                                <img src="{{ asset('storage/' . $caisse->profile_picture) }}" 
                                     class="w-32 h-32 rounded-3xl object-cover mx-auto border-4 border-white shadow-lg" 
                                     alt="Photo de profil">
                            @else
                                <div class="w-32 h-32 rounded-3xl mx-auto border-4 border-white bg-gradient-to-br from-orange-100 to-orange-200 flex items-center justify-center shadow-lg">
                                    <span class="text-4xl font-bold text-orange-500">
                                        {{ strtoupper(substr($caisse->prenom, 0, 1)) }}{{ strtoupper(substr($caisse->name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                            <div class="absolute bottom-1 right-1/2 translate-x-12 translate-y-1 bg-green-500 text-white p-1.5 rounded-lg border-2 border-white shadow-sm">
                                <i class="fas fa-check text-[10px]"></i>
                            </div>
                        </div>

                        <div class="text-center mb-8">
                            <h3 class="text-xl font-bold text-gray-900">{{ $caisse->prenom }} {{ $caisse->name }}</h3>
                            <p class="text-sm font-medium text-orange-600">{{ $caisse->email }}</p>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">Code ID</span>
                                </div>
                                <span class="font-bold text-gray-900">{{ $caisse->code_id ?? 'N/A' }}</span>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">Compagnie</span>
                                </div>
                                <span class="font-bold text-gray-900">{{ $caisse->compagnie->name ?? 'N/A' }}</span>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                                        <i class="fas fa-warehouse"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">Gare</span>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900 leading-tight">{{ $caisse->gare->nom_gare ?? 'N/A' }}</p>
                                    <p class="text-[10px] text-gray-400 font-medium">{{ $caisse->gare->ville ?? '' }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-orange-50 rounded-2xl border border-orange-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-orange-200 text-orange-700 flex items-center justify-center">
                                        <i class="fas fa-ticket-alt"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Stock Tickets</span>
                                </div>
                                <span class="font-bold text-orange-700">{{ $caisse->tickets ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne de droite : Formulaires -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Informations personnelles -->
                <div class="bg-white rounded-3xl shadow-xl p-8 transition-all hover:shadow-2xl">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center">
                            <i class="fas fa-user-edit text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Modifier mes informations</h2>
                            <p class="text-sm text-gray-500">Mettez à jour vos coordonnées personnelles</p>
                        </div>
                    </div>

                    <form action="{{ route('caisse.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Nom *</label>
                                <input type="text" name="name" value="{{ old('name', $caisse->name) }}" required
                                    class="w-full px-4 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-orange-500 focus:bg-white transition-all outline-none">
                                @error('name') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Prénom *</label>
                                <input type="text" name="prenom" value="{{ old('prenom', $caisse->prenom) }}" required
                                    class="w-full px-4 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-orange-500 focus:bg-white transition-all outline-none">
                                @error('prenom') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Contact *</label>
                                <input type="text" name="contact" value="{{ old('contact', $caisse->contact) }}" required
                                    class="w-full px-4 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-orange-500 focus:bg-white transition-all outline-none">
                                @error('contact') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Cas d'urgence</label>
                                <input type="text" name="cas_urgence" value="{{ old('cas_urgence', $caisse->cas_urgence) }}"
                                    class="w-full px-4 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-orange-500 focus:bg-white transition-all outline-none">
                                @error('cas_urgence') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2 space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Commune de résidence</label>
                                <input type="text" name="commune" value="{{ old('commune', $caisse->commune) }}"
                                    class="w-full px-4 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-orange-500 focus:bg-white transition-all outline-none">
                                @error('commune') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2 space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Photo de profil</label>
                                <input type="file" name="profile_picture" accept="image/*"
                                    class="w-full px-4 py-3 bg-gray-50 border-2 border-dashed border-gray-300 rounded-2xl hover:border-orange-500 transition-all cursor-pointer">
                                <p class="text-[10px] text-gray-400">Format : JPG, PNG. Poids max : 2 Mo.</p>
                                @error('profile_picture') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" 
                                class="w-full py-4 bg-orange-600 text-white font-bold rounded-2xl hover:bg-orange-700 shadow-lg hover:shadow-orange-500/30 transition-all transform hover:-translate-y-0.5">
                                Sauvegarder les modifications
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Sécurité -->
                <div class="bg-white rounded-3xl shadow-xl p-8 transition-all hover:shadow-2xl border-l-8 border-blue-600">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                            <i class="fas fa-shield-alt text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Sécurité du compte</h2>
                            <p class="text-sm text-gray-500">Mettez à jour votre mot de passe</p>
                        </div>
                    </div>

                    <form action="{{ route('caisse.profile.password') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 ml-1">Mot de passe actuel *</label>
                            <input type="password" name="current_password" required
                                class="w-full px-4 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-600 focus:bg-white transition-all outline-none">
                            @error('current_password') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Nouveau mot de passe *</label>
                                <input type="password" name="new_password" required
                                    class="w-full px-4 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-600 focus:bg-white transition-all outline-none">
                                @error('new_password') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Confirmer le mot de passe *</label>
                                <input type="password" name="new_password_confirmation" required
                                    class="w-full px-4 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-600 focus:bg-white transition-all outline-none">
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" 
                                class="w-full py-4 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 shadow-lg hover:shadow-blue-500/30 transition-all transform hover:-translate-y-0.5">
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
            title: 'Succès!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#ea580c',
            timer: 3500,
            timerProgressBar: true,
            customClass: {
                popup: 'rounded-3xl'
            }
        });
    @endif

    @if($errors->any() && !session('success'))
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: "{{ $errors->first() }}",
            confirmButtonColor: '#d33',
            customClass: {
                popup: 'rounded-3xl'
            }
        });
    @endif
</script>
@endsection
