@extends('sapeur_pompier.layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-900">Mon Profil</h2>
    <p class="text-gray-500 mt-1">Gérez les informations de votre caserne et votre mot de passe</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
        <p class="font-bold">Succès</p>
        <p>{{ session('success') }}</p>
    </div>
@endif

<div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden max-w-4xl">
    <div class="p-6 border-b border-gray-100 bg-gray-50 flex items-center gap-3">
        <i class="fas fa-user-shield text-red-600 text-xl"></i>
        <h3 class="text-lg font-bold text-gray-800">Paramètres du compte</h3>
    </div>

    <form action="{{ route('sapeur-pompier.profile.update') }}" method="POST" class="p-6 md:p-8 space-y-8">
        @csrf
        @method('PUT')

        <!-- Infos Générales -->
        <div>
            <h4 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Informations Générales</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2" for="name">Nom / Caserne</label>
                    <input id="name" class="w-full px-4 py-3 bg-gray-50 border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all font-medium" type="text" name="name" value="{{ old('name', $user->name) }}" required />
                    @error('name') <span class="text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2" for="email">Email Officiel (Non modifiable)</label>
                    <input id="email" class="w-full px-4 py-3 bg-gray-100 border-transparent text-gray-500 rounded-xl cursor-not-allowed font-medium" type="email" value="{{ $user->email }}" disabled />
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2" for="contact">Contact Téléphonique</label>
                    <input id="contact" class="w-full px-4 py-3 bg-gray-50 border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all font-medium" type="text" name="contact" value="{{ old('contact', $user->contact) }}" required />
                    @error('contact') <span class="text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2" for="commune">Commune <span class="text-red-500">*</span></label>
                    <input id="commune" class="block w-full px-4 py-3 bg-gray-50 border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 focus:bg-white transition-all font-medium placeholder-gray-400" type="text" name="commune" value="{{ old('commune', $user->commune) }}" required placeholder="Ex: Adjamé" />
                </div>

                <!-- Adresse -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2" for="adresse">Adresse de la caserne <span class="text-red-500">*</span></label>
                    <input id="adresse" class="block w-full px-4 py-3 bg-gray-50 border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 focus:bg-white transition-all font-medium placeholder-gray-400" type="text" name="adresse" value="{{ old('adresse', $user->adresse) }}" required placeholder="Ex: Face à la corniche" />
                </div>
            </div>

            <!-- Bouton Localisation -->
            <div class="mt-4">
                <button type="button" onclick="geocodeAddress()" class="w-full flex items-center justify-center gap-2 py-3 px-4 bg-gray-900 hover:bg-gray-800 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg active:scale-[0.98]">
                    <i class="fas fa-crosshairs text-red-400"></i> Auto-localiser (GPS)
                </button>
            </div>

            <!-- Coordonnées GPS Output -->
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mt-4 space-y-3">
                <div class="flex items-center gap-2 text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                    <i class="fas fa-satellite text-gray-400"></i> Coordonnées GPS
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1" for="latitude">Latitude</label>
                        <input id="latitude" class="block w-full px-3 py-2 bg-gray-100 border-transparent text-gray-600 rounded-lg text-sm font-mono focus:ring-0 cursor-text" type="text" name="latitude" value="{{ old('latitude', $user->latitude) }}" readonly />
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1" for="longitude">Longitude</label>
                        <input id="longitude" class="block w-full px-3 py-2 bg-gray-100 border-transparent text-gray-600 rounded-lg text-sm font-mono focus:ring-0 cursor-text" type="text" name="longitude" value="{{ old('longitude', $user->longitude) }}" readonly />
                    </div>
                </div>
                <p class="text-[10px] text-gray-400 leading-tight mt-2"><i class="fas fa-info-circle"></i> Ces coordonnées permettent de calculer la distance en cas d'accident.</p>
            </div>
        </div>

        <hr class="border-gray-100">

        <!-- Sécurité -->
        <div>
            <h4 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Sécurité (Nouveau mot de passe)</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2" for="password">Nouveau mot de passe</label>
                    <input id="password" class="w-full px-4 py-3 bg-gray-50 border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all font-medium" type="password" name="password" placeholder="Laissez vide pour ne pas modifier" />
                    @error('password') <span class="text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2" for="password_confirmation">Confirmer le mot de passe</label>
                    <input id="password_confirmation" class="w-full px-4 py-3 bg-gray-50 border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all font-medium" type="password" name="password_confirmation" placeholder="Confirmez le nouveau mot de passe" />
                </div>
            </div>
        </div>

        <div class="pt-4 flex justify-end">
            <button type="submit" class="px-8 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center gap-2">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
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

                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lon;

                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`);
                    const data = await response.json();

                    if (data && data.address) {
                        const commune = data.address.city || data.address.town || data.address.village || data.address.suburb || data.address.county || '';
                        
                        if (commune) {
                            document.getElementById('commune').value = commune;
                        }
                        
                        if (data.display_name) {
                            const shortAddress = data.display_name.split(',').slice(0, 2).join(', ').trim();
                            document.getElementById('adresse').value = shortAddress;
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Position mise à jour !',
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
                    text: 'Impossible d\'obtenir votre position. Veuillez vérifier que votre appareil ou navigateur autorise la géolocalisation.',
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
