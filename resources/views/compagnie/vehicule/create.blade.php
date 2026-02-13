@extends('compagnie.layouts.template')
@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
        <div class=" mx-auto" style="width: 90%">
            <!-- En-tête -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-[#e94f1b] rounded-2xl shadow-lg mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-3">Nouveau Véhicule</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Ajoutez un nouveau véhicule à votre flotte
                </p>
            </div>

            <!-- Carte du formulaire -->
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
                <form action="{{ route('vehicule.store') }}" method="POST" class="p-8">
                    @csrf

                    <!-- Section 1: Informations générales -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-2 h-8 bg-[#e94f1b] rounded-full mr-4"></div>
                            <h2 class="text-2xl font-bold text-gray-900">Informations générales</h2>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Immatriculation -->
                            <div class="space-y-2">
                                <label class="flex items-center text-sm font-semibold text-gray-700">
                                    <span>Immatriculation</span>
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input type="text" name="immatriculation" value="{{ old('immatriculation') }}" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white font-mono"
                                    placeholder="Entrer l'immatriculation">
                                @error('immatriculation')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Numéro de série -->
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-700">Numéro de série</label>
                                <input type="text" name="numero_serie" value="{{ old('numero_serie') }}"
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                    placeholder="Numéro de série du véhicule">
                                @error('numero_serie')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Configuration des places -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-2 h-8 bg-green-500 rounded-full mr-4"></div>
                            <h2 class="text-2xl font-bold text-gray-900">Configuration des places</h2>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Type de rangée -->
                            <div class="space-y-2">
                                <label class="flex items-center text-sm font-semibold text-gray-700">
                                    <span>Type de rangée</span>
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <select name="type_range" id="type_range" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white appearance-none">
                                    <option value="">Sélectionnez le type</option>
                                    <option value="2x2" {{ old('type_range') == '2x2' ? 'selected' : '' }}>2x2 (2 places
                                        par rangée)</option>
                                    <option value="2x3" {{ old('type_range') == '2x3' ? 'selected' : '' }}>2x3 (3 places
                                        par rangée)</option>
                                    <option value="2x4" {{ old('type_range') == '2x4' ? 'selected' : '' }}>2x4 (4 places
                                        par rangée)</option>
                                </select>
                                @error('type_range')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nombre total de places -->
                            <div class="space-y-2">
                                <label class="flex items-center text-sm font-semibold text-gray-700">
                                    <span>Nombre total de places</span>
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input type="number" name="nombre_place" id="nombre_place"
                                    value="{{ old('nombre_place') }}" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                    placeholder="Ex: 16">
                                @error('nombre_place')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Visualisation de la configuration -->
                        <div id="configuration_visuelle"
                            class="mt-6 p-8 bg-gray-50 rounded-2xl border border-gray-200 hidden">
                            <h3 class="text-xl font-semibold text-gray-900 mb-6">Visualisation de la configuration</h3>

                            <!-- Configuration calculée -->
                            <div class="mb-6 p-6 bg-white rounded-xl border border-gray-200">
                                <div class="grid grid-cols-3 gap-6 text-center text-base">
                                    <div>
                                        <div class="font-semibold text-gray-700">Type</div>
                                        <div id="config_type" class="text-xl font-bold text-[#e94f1b]">-</div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-700">Rangées</div>
                                        <div id="config_ranger" class="text-xl font-bold text-green-600">-</div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-700">Places/rangée</div>
                                        <div id="config_places_ranger" class="text-xl font-bold text-blue-600">-</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Visualisation VERTICALE des places -->
                            <div class="space-y-6">
                                <!-- Conteneur des places - disposition verticale -->
                                <div id="rangees_container" class="flex flex-col items-center space-y-0 w-full">
                                    <!-- Les places seront générées dynamiquement ici en colonne -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div
                        class="flex flex-col sm:flex-row gap-4 justify-between items-center pt-8 border-t border-gray-200">
                        <a href="{{ route('vehicule.index') }}"
                            class="flex items-center px-8 py-4 text-gray-700 font-semibold rounded-xl border border-gray-300 hover:bg-gray-50 transition-all duration-200 group">
                            <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform duration-200"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Retour à la liste
                        </a>

                        <div class="flex gap-4">
                            <!-- Bouton Réinitialiser -->
                            <button type="reset"
                                class="flex items-center px-6 py-4 text-gray-700 font-semibold rounded-xl border border-gray-300 hover:bg-gray-50 transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Réinitialiser
                            </button>

                            <!-- Bouton Créer -->
                            <button type="submit"
                                class="flex items-center px-8 py-4 bg-[#e94f1b] text-white font-bold rounded-xl hover:bg-[#e89116] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Créer le véhicule
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeRangeSelect = document.getElementById('type_range');
            const configurationVisuelle = document.getElementById('configuration_visuelle');
            const rangeesContainer = document.getElementById('rangees_container');
            const configType = document.getElementById('config_type');
            const configRanger = document.getElementById('config_ranger');
            const configPlacesRanger = document.getElementById('config_places_ranger');
            const nombrePlaceInput = document.getElementById('nombre_place');

            // Configuration des types de rangées
            const typeRangeConfig = {
                '2x2': {
                    placesGauche: 2,
                    placesDroite: 2,
                    description: "2 places par côté"
                },
                '2x3': {
                    placesGauche: 2,
                    placesDroite: 3,
                    description: "2 places à gauche, 3 à droite"
                },
                '2x4': {
                    placesGauche: 2,
                    placesDroite: 4,
                    description: "2 places à gauche, 4 à droite"
                }
            };

            // Gestion du changement de type de rangée et nombre de places
            typeRangeSelect.addEventListener('change', updateVisualisation);
            nombrePlaceInput.addEventListener('input', updateVisualisation);

            function updateVisualisation() {
                const selectedType = typeRangeSelect.value;
                const totalPlaces = parseInt(nombrePlaceInput.value) || 0;

                if (!selectedType || !totalPlaces || !typeRangeConfig[selectedType]) {
                    configurationVisuelle.classList.add('hidden');
                    return;
                }

                const config = typeRangeConfig[selectedType];
                const placesGauche = config.placesGauche;
                const placesDroite = config.placesDroite;
                const placesParRanger = placesGauche + placesDroite;

                // Calcul du nombre de rangées
                const nombreRanger = Math.ceil(totalPlaces / placesParRanger);

                // Mettre à jour la configuration
                configType.textContent = selectedType;
                configRanger.textContent = nombreRanger + ' rangées';
                configPlacesRanger.textContent = placesParRanger + ' places/rangée';

                // Afficher la configuration visuelle
                configurationVisuelle.classList.remove('hidden');

                // Générer TOUTES les rangées dans un seul cadre
                rangeesContainer.innerHTML = '';
                let numeroPlace = 1;

                let toutesRangeesHTML = '';

                for (let ranger = 1; ranger <= nombreRanger; ranger++) {
                    // Calculer le nombre de places pour cette rangée
                    const placesRestantes = totalPlaces - (numeroPlace - 1);
                    const placesCetteRanger = Math.min(placesParRanger, placesRestantes);
                    
                    // Répartir les places entre gauche et droite selon la configuration
                    const placesGaucheCetteRanger = Math.min(placesGauche, placesCetteRanger);
                    const placesDroiteCetteRanger = Math.min(placesDroite, placesCetteRanger - placesGaucheCetteRanger);

                    const rangeeHTML = `
                        <div class="flex justify-between items-center w-full py-4 ${ranger < nombreRanger ? 'border-b border-gray-200' : ''}">
                            <!-- Numéro de rangée -->
                            <div class="w-24 text-center text-lg font-semibold text-gray-700">Rangée ${ranger}</div>
                            
                            <!-- Côté gauche -->
                            <div class="flex gap-4 flex-1 justify-center">
                                ${Array.from({length: placesGaucheCetteRanger}, (_, i) => `
                                    <div class="w-14 h-14 bg-[#e94f1b] rounded-lg flex items-center justify-center text-white font-bold shadow-lg" 
                                         title="Place ${numeroPlace + i}">
                                        ${numeroPlace + i}
                                    </div>
                                `).join('')}
                            </div>
                            
                            <!-- Allée -->
                            <div class="w-4 h-16 bg-gray-400 rounded-full mx-12"></div>
                            
                            <!-- Côté droit -->
                            <div class="flex gap-4 flex-1 justify-center">
                                ${Array.from({length: placesDroiteCetteRanger}, (_, i) => `
                                    <div class="w-14 h-14 bg-green-500 rounded-lg flex items-center justify-center text-white font-bold shadow-lg" 
                                         title="Place ${numeroPlace + placesGaucheCetteRanger + i}">
                                        ${numeroPlace + placesGaucheCetteRanger + i}
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;
                    
                    toutesRangeesHTML += rangeeHTML;
                    numeroPlace += placesCetteRanger;
                }

                // Cadre unique avec toutes les rangées
                const cadreHTML = `
                    <div class="bg-white p-8 rounded-xl border border-gray-200 w-full">
                        <!-- En-tête -->
                        <div class="flex justify-between items-center w-full pb-4 mb-4 border-b border-gray-300">
                            <div class="w-24 text-center text-sm font-semibold text-gray-700">Rangée</div>
                            <div class="flex-1 text-center text-sm font-semibold text-gray-700">Côté gauche</div>
                            <div class="w-16 text-center text-sm font-semibold text-gray-700">Allée</div>
                            <div class="flex-1 text-center text-sm font-semibold text-gray-700">Côté droit</div>
                        </div>
                        
                        <!-- Toutes les rangées -->
                        <div class="space-y-0 w-full">
                            ${toutesRangeesHTML}
                        </div>
                    </div>
                `;

                rangeesContainer.innerHTML = cadreHTML;
            }

            // Formatage automatique de l'immatriculation
            // const immatriculationInput = document.querySelector('input[name="immatriculation"]');
            // immatriculationInput.addEventListener('input', function(e) {
            //     let value = e.target.value.toUpperCase().replace(/[^A-Z0-9\s]/g, '');

            //     // Format CI: XX 123 XX
            //     if (value.length > 2 && value[2] !== ' ') {
            //         value = value.substring(0, 2) + ' ' + value.substring(2);
            //     }
            //     if (value.length > 6 && value[6] !== ' ') {
            //         value = value.substring(0, 6) + ' ' + value.substring(6);
            //     }

            //     e.target.value = value;
            // });

            // // Initialisation si des valeurs existent déjà (en cas d'erreur de validation)
            // if (typeRangeSelect.value) {
            //     updateVisualisation();
            // }
        });

        // SweetAlert pour les messages de session
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: '{{ session('success') }}',
                confirmButtonColor: '#e94f1b'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33'
            });
        @endif
    </script>

    <style>
        input:focus,
        select:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(254, 162, 25, 0.15);
        }

        /* Style pour la visualisation des places */
        .rangee-place {
            transition: all 0.3s ease;
        }

        .rangee-place:hover {
            transform: scale(1.1);
        }

        /* Style pour les rangées */
        #rangees_container > div {
            transition: all 0.3s ease;
        }

        #rangees_container > div:hover {
            background-color: #f9fafb;
        }

        /* Pour s'assurer que le conteneur prend toute la largeur */
        #configuration_visuelle {
            width: 100%;
        }
    </style>
@endsection