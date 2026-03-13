@extends('gare-espace.layouts.template')

@section('title', 'Mon Profil Gare')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50/30 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <!-- En-tête -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Gestion du Profil Gare</h1>
            <p class="mt-2 text-lg text-gray-600">Consultez et modifiez les informations de votre établissement</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Colonne de gauche : Aperçu -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 sticky top-24">
                    <div class="h-32 bg-gradient-to-r from-orange-500 to-red-600"></div>
                    <div class="px-6 pb-8">
                        <div class="relative -mt-16 mb-6 text-center">
                            @if($gare->profile_image)
                                <img src="{{ asset('storage/' . $gare->profile_image) }}" 
                                     class="w-32 h-32 rounded-2xl object-cover mx-auto border-4 border-white shadow-lg" 
                                     alt="Logo Gare">
                            @else
                                <div class="w-32 h-32 rounded-2xl mx-auto border-4 border-white bg-gradient-to-br from-orange-100 to-orange-200 flex items-center justify-center shadow-lg">
                                    <i class="fas fa-warehouse text-4xl text-orange-500"></i>
                                </div>
                            @endif
                            <div class="absolute bottom-1 right-1/2 translate-x-12 translate-y-1 bg-green-500 text-white p-1.5 rounded-lg border-2 border-white shadow-sm">
                                <i class="fas fa-check text-[10px]"></i>
                            </div>
                        </div>

                        <div class="text-center mb-8">
                            <h3 class="text-xl font-bold text-gray-900">{{ $gare->nom_gare }}</h3>
                            <p class="text-sm font-medium text-orange-600">{{ $gare->ville }}</p>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-2xl">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Compagnie</p>
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $gare->compagnie->name ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-2xl">
                                <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center shrink-0">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Responsable</p>
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $gare->responsable_prenom }} {{ $gare->responsable_nom }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne de droite : Formulaires -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Informations Générales -->
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center">
                                <i class="fas fa-edit text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Informations de la Gare</h2>
                                <p class="text-sm text-gray-500">Mettez à jour les détails de votre point de vente</p>
                            </div>
                        </div>

                        <form action="{{ route('gare-espace.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Nom de la Gare *</label>
                                    <input type="text" name="nom_gare" value="{{ old('nom_gare', $gare->nom_gare) }}" required
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-orange-500 focus:bg-white transition-all outline-none">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Ville *</label>
                                    <input type="text" name="ville" value="{{ old('ville', $gare->ville) }}" readonly
                                        class="w-full px-4 py-3 bg-gray-100 border-2 border-transparent rounded-2xl text-gray-500 cursor-not-allowed">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Nom du Responsable *</label>
                                    <input type="text" name="responsable_nom" value="{{ old('responsable_nom', $gare->responsable_nom) }}" required
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-orange-500 focus:bg-white transition-all outline-none">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Prénom du Responsable *</label>
                                    <input type="text" name="responsable_prenom" value="{{ old('responsable_prenom', $gare->responsable_prenom) }}" required
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-orange-500 focus:bg-white transition-all outline-none">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Contact Gare *</label>
                                    <input type="text" name="contact" value="{{ old('contact', $gare->contact) }}" required
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-orange-500 focus:bg-white transition-all outline-none">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Contact Urgence</label>
                                    <input type="text" name="contact_urgence" value="{{ old('contact_urgence', $gare->contact_urgence) }}"
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-orange-500 focus:bg-white transition-all outline-none">
                                </div>

                                <div class="md:col-span-2 space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Adresse complète</label>
                                    <textarea name="adresse" rows="3"
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-orange-500 focus:bg-white transition-all outline-none">{{ old('adresse', $gare->adresse) }}</textarea>
                                </div>

                                <div class="md:col-span-2 space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Logo / Image de la Gare</label>
                                    <input type="file" name="profile_image" accept="image/*"
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-dashed border-gray-300 rounded-2xl hover:border-orange-500 transition-all cursor-pointer">
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
                </div>

                <!-- Section Sécurité -->
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                                <i class="fas fa-lock text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Mot de passe</h2>
                                <p class="text-sm text-gray-500">Sécurisez votre compte gare</p>
                            </div>
                        </div>

                        <form action="{{ route('gare-espace.profile.password') }}" method="POST" class="space-y-6">
                            @csrf
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Mot de passe actuel *</label>
                                <input type="password" name="current_password" required
                                    class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-500 focus:bg-white transition-all outline-none">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Nouveau mot de passe *</label>
                                    <input type="password" name="new_password" required
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-500 focus:bg-white transition-all outline-none">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Confirmer le nouveau mot de passe *</label>
                                    <input type="password" name="new_password_confirmation" required
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-500 focus:bg-white transition-all outline-none">
                                </div>
                            </div>

                            <div class="pt-4">
                                <button type="submit" 
                                    class="w-full py-4 bg-gray-900 text-white font-bold rounded-2xl hover:bg-gray-800 shadow-lg transition-all transform hover:-translate-y-0.5">
                                    Mettre à jour le mot de passe
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
    </script>
@endif

@if($errors->any())
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: "{{ $errors->first() }}",
            confirmButtonColor: '#d33',
            customClass: {
                popup: 'rounded-3xl'
            }
        });
    </script>
@endif
@endsection
