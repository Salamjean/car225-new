@extends('admin.layouts.template')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto">
        
        <!-- Header Section -->
        <div class="mb-8 bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                    <i class="fas fa-fire-extinguisher"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-gray-900 tracking-tight">Nouveau Sapeur-Pompier</h1>
                    <p class="text-sm text-gray-500 font-medium mt-1">Ajouter une nouvelle caserne au système de secours interactif</p>
                </div>
            </div>
            
            <a href="{{ route('sapeur-pompier.index') }}" class="group flex items-center justify-center px-5 py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 rounded-xl font-bold transition-all duration-300 hover:shadow-md">
                <i class="fas fa-arrow-left mr-2 text-gray-400 group-hover:text-gray-600 transition-colors"></i>
                Retour à la liste
            </a>
        </div>

        <!-- Main Form Container -->
        <form action="{{ route('sapeur-pompier.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column (General Info) -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                            <i class="fas fa-info-circle text-red-500"></i>
                            <h2 class="text-lg font-bold text-gray-800">Informations Générales</h2>
                        </div>
                        
                        <div class="p-6 space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Nom -->
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-sm font-bold text-gray-700 mb-1.5" for="name">Nom / Caserne <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-building text-gray-400"></i>
                                        </div>
                                        <input id="name" class="block w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 focus:bg-white transition-all font-medium placeholder-gray-400" type="text" name="name" value="{{ old('name') }}" required placeholder="Ex: GSPM Indénié" />
                                    </div>
                                    @error('name') <span class="text-red-500 text-xs font-medium mt-1 inline-block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-sm font-bold text-gray-700 mb-1.5" for="email">Email Officiel <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-envelope text-gray-400"></i>
                                        </div>
                                        <input id="email" class="block w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 focus:bg-white transition-all font-medium placeholder-gray-400" type="email" name="email" value="{{ old('email') }}" required placeholder="contact@gspm.ci" />
                                    </div>
                                    @error('email') <span class="text-red-500 text-xs font-medium mt-1 inline-block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Contact -->
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-sm font-bold text-gray-700 mb-1.5" for="contact">Téléphone <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-phone-alt text-gray-400"></i>
                                        </div>
                                        <input id="contact" class="block w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 focus:bg-white transition-all font-medium placeholder-gray-400" type="text" name="contact" value="{{ old('contact') }}" required placeholder="Ex: 180 ou +225..." />
                                    </div>
                                    @error('contact') <span class="text-red-500 text-xs font-medium mt-1 inline-block">{{ $message }}</span> @enderror
                                </div>
                                
                                <!-- Logo -->
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-sm font-bold text-gray-700 mb-1.5" for="path_logo">Logo de la caserne</label>
                                    <div class="relative">
                                        <input id="path_logo" class="block w-full py-2.5 px-3 bg-gray-50 border border-gray-200 text-gray-600 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all font-medium file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-600 hover:file:bg-red-100" type="file" name="path_logo" accept="image/*" />
                                    </div>
                                    @error('path_logo') <span class="text-red-500 text-xs font-medium mt-1 inline-block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Location and Action) -->
                <div class="space-y-6">
                    <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 overflow-hidden relative group">
                        <!-- Decorative gradient line -->
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-500 to-orange-500"></div>
                        
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-map-marked-alt text-red-500"></i>
                                <h2 class="text-lg font-bold text-gray-800">Géolocalisation</h2>
                            </div>
                        </div>

                        <div class="p-6 space-y-5">
                            <!-- Commune -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1.5" for="commune">Commune <span class="text-red-500">*</span></label>
                                <input id="commune" class="block w-full px-4 py-3 bg-gray-50 border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 focus:bg-white transition-all font-medium placeholder-gray-400" type="text" name="commune" value="{{ old('commune') }}" required placeholder="Ex: Adjamé" />
                            </div>

                            <!-- Adresse -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1.5" for="adresse">Adresse de la caserne <span class="text-red-500">*</span></label>
                                <input id="adresse" class="block w-full px-4 py-3 bg-gray-50 border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 focus:bg-white transition-all font-medium placeholder-gray-400" type="text" name="adresse" value="{{ old('adresse') }}" required placeholder="Ex: Face à la corniche" />
                            </div>

                            <button type="button" onclick="geocodeAddress()" class="w-full flex items-center justify-center gap-2 py-3 px-4 bg-gray-900 hover:bg-gray-800 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg active:scale-[0.98]">
                                <i class="fas fa-crosshairs text-red-400"></i> Auto-localiser (GPS)
                            </button>
                            
                            <!-- GPS Coordinates Output -->
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mt-4 space-y-3">
                                <div class="flex items-center gap-2 text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                    <i class="fas fa-satellite text-gray-400"></i> Coordonnées GPS
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1" for="latitude">Latitude</label>
                                        <input id="latitude" class="block w-full px-3 py-2 bg-gray-100 border-transparent text-gray-600 rounded-lg text-sm font-mono focus:ring-0 cursor-text" type="text" name="latitude" value="{{ old('latitude') }}" />
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1" for="longitude">Longitude</label>
                                        <input id="longitude" class="block w-full px-3 py-2 bg-gray-100 border-transparent text-gray-600 rounded-lg text-sm font-mono focus:ring-0 cursor-text" type="text" name="longitude" value="{{ old('longitude') }}" />
                                    </div>
                                </div>
                                <p class="text-[10px] text-gray-400 leading-tight mt-2"><i class="fas fa-info-circle"></i> Ces coordonnées sont cruciales pour calculer la distance avec les accidents.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="w-full relative group overflow-hidden flex items-center justify-center gap-3 py-4 bg-red-600 border border-red-700 text-white rounded-[20px] font-black text-lg transition-all shadow-[0_8px_20px_rgba(220,38,38,0.3)] hover:shadow-[0_12px_25px_rgba(220,38,38,0.4)] hover:-translate-y-1 active:translate-y-0">
                        <div class="absolute inset-0 bg-gradient-to-r from-red-600 via-red-500 to-orange-500 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <i class="fas fa-save relative z-10 text-xl"></i>
                        <span class="relative z-10">Créer la Caserne</span>
                    </button>
                </div>

            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function geocodeAddress() {
            if ("geolocation" in navigator) {
                Swal.fire({
                    title: 'Récupération du GPS...',
                    text: 'Veuillez patienter pendant que nous obtenons votre position...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                navigator.geolocation.getCurrentPosition(async function(position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;

                    // Mettre à jour les champs cachés / affichés
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lon;

                    try {
                        // Reverse Geocoding avec OpenStreetMap (Nominatim)
                        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`);
                        const data = await response.json();

                        if (data && data.address) {
                            // Extraire la commune (city, town, village, suburb)
                            const commune = data.address.city || data.address.town || data.address.village || data.address.suburb || data.address.county || '';
                            
                            if (commune) {
                                document.getElementById('commune').value = commune;
                            }
                            
                            // Extraire l'adresse détaillée (rue + infos)
                            if (data.display_name) {
                                // On garde les 2 premiers éléments de l'adresse pour que ça ne soit pas trop long (ex: "Rue Paul Langevin, Marcory")
                                const shortAddress = data.display_name.split(',').slice(0, 2).join(', ').trim();
                                document.getElementById('adresse').value = shortAddress;
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Position trouvée !',
                                text: 'Vos coordonnées et adresse ont été remplies automatiquement.',
                                confirmButtonColor: '#10b981',
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Position GPS récupérée',
                                text: 'Les coordonnées GPS sont remplies, mais l\'adresse exacte n\'a pas pu être devinée. Veuillez la saisir manuellement.',
                                confirmButtonColor: '#f59e0b',
                            });
                        }
                    } catch (error) {
                        console.error('Erreur Reverse Geocoding:', error);
                        Swal.fire({
                            icon: 'success',
                            title: 'Position GPS récupérée',
                            text: 'Coordonnées récupérées avec succès. (Réseau indisponible pour traduire l\'adresse).',
                            confirmButtonColor: '#10b981',
                        });
                    }

                }, function(error) {
                    console.error("Erreur Geolocation: ", error.message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Accès GPS refusé',
                        text: 'Impossible d\'obtenir votre position. Veuillez vérifier que votre appareil ou navigateur autorise la géolocalisation, ou saisissez manuellement.',
                        confirmButtonColor: '#d33',
                    });
                    
                    document.getElementById('latitude').readOnly = false;
                    document.getElementById('longitude').readOnly = false;
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            } else {
                Swal.fire('Erreur', 'La géolocalisation n\'est pas supportée par ce navigateur.', 'error');
            }
        }
    </script>
@endsection