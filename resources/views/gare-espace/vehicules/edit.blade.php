@extends('gare-espace.layouts.template')
@section('title', 'Modifier le Véhicule')
@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
        <div class=" mx-auto" style="width: 90%">
            <!-- En-tête -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-[#e94f1b] rounded-2xl shadow-lg mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-3">Modifier le Véhicule</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Mise à jour des informations du véhicule {{ $vehicule->immatriculation }}
                </p>
            </div>

            <!-- Carte du formulaire -->
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
                <form action="{{ route('gare-espace.vehicules.update', $vehicule) }}" method="POST" class="p-8">
                    @csrf
                    @method('PUT')

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
                                <input type="text" name="immatriculation" value="{{ old('immatriculation', $vehicule->immatriculation) }}" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white font-mono"
                                    placeholder="Entrer l'immatriculation">
                                @error('immatriculation')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Numéro de série -->
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-700">Numéro de série</label>
                                <input type="text" name="numero_serie" value="{{ old('numero_serie', $vehicule->numero_serie) }}"
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
                                    <option value="2x2" {{ old('type_range', $vehicule->type_range) == '2x2' ? 'selected' : '' }}>2x2 (2 places par rangée)</option>
                                    <option value="2x3" {{ old('type_range', $vehicule->type_range) == '2x3' ? 'selected' : '' }}>2x3 (3 places par rangée)</option>
                                    <option value="2x4" {{ old('type_range', $vehicule->type_range) == '2x4' ? 'selected' : '' }}>2x4 (4 places par rangée)</option>
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
                                    value="{{ old('nombre_place', $vehicule->nombre_place) }}" required
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

                            <div class="space-y-6">
                                <div id="rangees_container" class="flex flex-col items-center space-y-0 w-full">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-between items-center pt-8 border-t border-gray-200">
                        <a href="{{ route('gare-espace.vehicules.index') }}"
                            class="flex items-center px-8 py-4 text-gray-700 font-semibold rounded-xl border border-gray-300 hover:bg-gray-50 transition-all duration-200 group">
                            <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform duration-200"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Retour à la liste
                        </a>

                        <div class="flex gap-4">
                            <button type="submit"
                                class="flex items-center px-8 py-4 bg-[#e94f1b] text-white font-bold rounded-xl hover:bg-[#d33d0f] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Enregistrer les modifications
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

            const typeRangeConfig = {
                '2x2': { placesGauche: 2, placesDroite: 2, description: "2 places par côté" },
                '2x3': { placesGauche: 2, placesDroite: 3, description: "2 places à gauche, 3 à droite" },
                '2x4': { placesGauche: 2, placesDroite: 4, description: "2 places à gauche, 4 à droite" }
            };

            typeRangeSelect.addEventListener('change', updateVisualisation);
            nombrePlaceInput.addEventListener('input', updateVisualisation);

            // Initial call
            updateVisualisation();

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

                const nombreRanger = Math.ceil(totalPlaces / placesParRanger);

                configType.textContent = selectedType;
                configRanger.textContent = nombreRanger + ' rangées';
                configPlacesRanger.textContent = placesParRanger + ' places/rangée';

                configurationVisuelle.classList.remove('hidden');

                rangeesContainer.innerHTML = '';
                let numeroPlace = 1;
                let toutesRangeesHTML = '';

                for (let ranger = 1; ranger <= nombreRanger; ranger++) {
                    const placesRestantes = totalPlaces - (numeroPlace - 1);
                    const placesCetteRanger = Math.min(placesParRanger, placesRestantes);
                    const placesGaucheCetteRanger = Math.min(placesGauche, placesCetteRanger);
                    const placesDroiteCetteRanger = Math.min(placesDroite, placesCetteRanger - placesGaucheCetteRanger);

                    const rangeeHTML = `
                        <div class="flex justify-between items-center w-full py-4 ${ranger < nombreRanger ? 'border-b border-gray-200' : ''}">
                            <div class="w-24 text-center text-lg font-semibold text-gray-700">Rangée ${ranger}</div>
                            <div class="flex gap-4 flex-1 justify-center">
                                ${Array.from({length: placesGaucheCetteRanger}, (_, i) => `
                                    <div class="w-14 h-14 bg-[#e94f1b] rounded-lg flex items-center justify-center text-white font-bold shadow-lg" title="Place ${numeroPlace + i}">
                                        ${numeroPlace + i}
                                    </div>
                                `).join('')}
                            </div>
                            <div class="w-4 h-16 bg-gray-400 rounded-full mx-12"></div>
                            <div class="flex gap-4 flex-1 justify-center">
                                ${Array.from({length: placesDroiteCetteRanger}, (_, i) => `
                                    <div class="w-14 h-14 bg-green-500 rounded-lg flex items-center justify-center text-white font-bold shadow-lg" title="Place ${numeroPlace + placesGaucheCetteRanger + i}">
                                        ${numeroPlace + placesGaucheCetteRanger + i}
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;
                    
                    toutesRangeesHTML += rangeeHTML;
                    numeroPlace += placesCetteRanger;
                }

                const cadreHTML = `
                    <div class="bg-white p-8 rounded-xl border border-gray-200 w-full">
                        <div class="flex justify-between items-center w-full pb-4 mb-4 border-b border-gray-300">
                            <div class="w-24 text-center text-sm font-semibold text-gray-700">Rangée</div>
                            <div class="flex-1 text-center text-sm font-semibold text-gray-700">Côté gauche</div>
                            <div class="w-16 text-center text-sm font-semibold text-gray-700">Allée</div>
                            <div class="flex-1 text-center text-sm font-semibold text-gray-700">Côté droit</div>
                        </div>
                        <div class="space-y-0 w-full">
                            ${toutesRangeesHTML}
                        </div>
                    </div>
                `;

                rangeesContainer.innerHTML = cadreHTML;
            }
        });

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
        input:focus, select:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(254, 162, 25, 0.15);
        }

        .rangee-place { transition: all 0.3s ease; }
        .rangee-place:hover { transform: scale(1.1); }

        #rangees_container > div { transition: all 0.3s ease; }
        #rangees_container > div:hover { background-color: #f9fafb; }

        #configuration_visuelle { width: 100%; }

        select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }
    </style>
@endsection
