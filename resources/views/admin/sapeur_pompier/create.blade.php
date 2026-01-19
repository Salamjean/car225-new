@extends('admin.layouts.template')

@section('content')
    <div class="px-4sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Page header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Ajouter un Sapeur-Pompier</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('sapeur-pompier.index') }}"
                    class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600 hover:text-gray-800">
                    <svg class="w-4 h-4 fill-current text-gray-500 shrink-0 mr-2" viewBox="0 0 16 16">
                        <path
                            d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                    </svg>
                    <span class="hidden xs:block ml-2">Retour</span>
                </a>
            </div>
        </div>

        <div class="bg-white p-6 shadow-lg rounded-2xl border border-gray-100">
            <form action="{{ route('sapeur-pompier.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom du groupe -->
                    <div>
                        <label class="block text-sm font-medium mb-1" for="name">Nom du Groupe / Caserne <span
                                class="text-red-500">*</span></label>
                        <input id="name"
                            class="form-input w-full rounded-lg border-gray-300 hover:border-red-500 focus:border-red-500"
                            type="text" name="name" value="{{ old('name') }}" required placeholder="Ex: GSPM Indenié" />
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium mb-1" for="email">Email Officiel <span
                                class="text-red-500">*</span></label>
                        <input id="email"
                            class="form-input w-full rounded-lg border-gray-300 hover:border-red-500 focus:border-red-500"
                            type="email" name="email" value="{{ old('email') }}" required placeholder="contact@gspm.ci" />
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Contact -->
                    <div>
                        <label class="block text-sm font-medium mb-1" for="contact">Contact Téléphonique <span
                                class="text-red-500">*</span></label>
                        <input id="contact"
                            class="form-input w-full rounded-lg border-gray-300 hover:border-red-500 focus:border-red-500"
                            type="text" name="contact" value="{{ old('contact') }}" required
                            placeholder="+225 01 02 03 04" />
                        @error('contact') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Logo -->
                    <div>
                        <label class="block text-sm font-medium mb-1" for="path_logo">Logo du groupe</label>
                        <input id="path_logo" class="form-input w-full rounded-lg border-gray-300" type="file"
                            name="path_logo" accept="image/*" />
                        @error('path_logo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="my-6 border-t border-gray-100 pt-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-map-marked-alt text-red-500 mr-2"></i> Localisation
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                        <!-- Commune -->
                        <div>
                            <label class="block text-sm font-medium mb-1" for="commune">Commune <span
                                    class="text-red-500">*</span></label>
                            <input id="commune" class="form-input w-full rounded-lg border-gray-300" type="text"
                                name="commune" value="{{ old('commune') }}" required placeholder="Ex: Adjamé" />
                        </div>

                        <!-- Adresse -->
                        <div>
                            <label class="block text-sm font-medium mb-1" for="adresse">Adresse Géographique <span
                                    class="text-red-500">*</span></label>
                            <div class="flex gap-2">
                                <input id="adresse" class="form-input w-full rounded-lg border-gray-300" type="text"
                                    name="adresse" value="{{ old('adresse') }}" required
                                    placeholder="Ex: Boulevard de la Paix" />
                                <button type="button" onclick="geocodeAddress()"
                                    class="btn bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-lg shadow whitespace-nowrap">
                                    <i class="fas fa-search-location mr-2"></i> Localiser
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Cliquez sur "Localiser" pour remplir automatiquement les
                                coordonnées.</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2 text-sm text-gray-500 mb-2 italic">
                            Ces coordonnées seront utilisées pour calculer la distance avec les accidents.
                        </div>
                        <!-- Latitude -->
                        <div>
                            <label class="block text-sm font-medium mb-1" for="latitude">Latitude</label>
                            <input id="latitude"
                                class="form-input w-full bg-gray-100 rounded-lg border-gray-300 text-gray-500 cursor-not-allowed"
                                type="text" name="latitude" value="{{ old('latitude') }}" readonly />
                        </div>

                        <!-- Longitude -->
                        <div>
                            <label class="block text-sm font-medium mb-1" for="longitude">Longitude</label>
                            <input id="longitude"
                                class="form-input w-full bg-gray-100 rounded-lg border-gray-300 text-gray-500 cursor-not-allowed"
                                type="text" name="longitude" value="{{ old('longitude') }}" readonly />
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end mt-8">
                    <button type="submit"
                        class="btn bg-red-600 hover:bg-red-700 text-white w-full md:w-auto px-8 py-3 rounded-lg shadow-lg font-bold text-lg">
                        <i class="fas fa-save mr-2"></i> Enregistrer le Sapeur-Pompier
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        async function geocodeAddress() {
            const name = document.getElementById('name').value;
            const adresse = document.getElementById('adresse').value;
            const commune = document.getElementById('commune').value;

            if (!adresse || !commune) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Champs manquants',
                    text: 'Veuillez remplir la commune et l\'adresse avant de lancer la localisation.',
                    confirmButtonColor: '#d33',
                });
                return;
            }

            // Construction de la requête avec le nom de la caserne pour plus de précision
            // On essaie d'abord: Caserne, Adresse, Commune...
            let query = `${adresse}, ${commune}, Abidjan, Côte d'Ivoire`;

            // Si le nom est renseigné, on l'ajoute pour affiner (ex: caserne l'indenié, adjamé...)
            if (name) {
                query = `${name}, ${adresse}, ${commune}, Côte d'Ivoire`;
            }

            Swal.fire({
                title: 'Recherche des coordonnées...',
                text: 'Interrogation de Google Maps (via OSM)...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                // Utilisation de Nominatim (OpenStreetMap) qui est gratuit et ne nécessite pas de clé API
                // C'est une excellente alternative à Google Maps Geocoding API pour ce cas d'usage
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`);
                const data = await response.json();

                if (data && data.length > 0) {
                    const location = data[0];
                    document.getElementById('latitude').value = location.lat;
                    document.getElementById('longitude').value = location.lon;

                    Swal.fire({
                        icon: 'success',
                        title: 'Localisation trouvée !',
                        text: `Coordonnées récupérées : ${location.lat}, ${location.lon}`,
                        confirmButtonColor: '#10b981',
                    });
                } else {
                    throw new Error('Adresse non trouvée');
                }
            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Impossible de trouver les coordonnées pour cette adresse. Veuillez vérifier l\'orthographe ou entrer les coordonnées manuellement si possible.',
                    footer: '<a href="https://www.google.com/maps" target="_blank" class="text-blue-500 underline">Ouvrir Google Maps</a>',
                    confirmButtonColor: '#d33',
                });
                // Rendre les champs éditables en cas d'échec pour saisie manuelle
                document.getElementById('latitude').readOnly = false;
                document.getElementById('longitude').readOnly = false;
                document.getElementById('latitude').classList.remove('bg-gray-100', 'cursor-not-allowed');
                document.getElementById('longitude').classList.remove('bg-gray-100', 'cursor-not-allowed');
            }
        }
    </script>
@endsection