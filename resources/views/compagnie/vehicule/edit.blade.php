@extends('compagnie.layouts.template')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#e94f1b] rounded-2xl shadow-lg mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Modifier Véhicule</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Modifiez les informations du véhicule {{ $vehicule->immatriculation }}
            </p>
        </div>

        <!-- Carte du formulaire -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="{{ route('vehicule.update', $vehicule->id) }}" method="POST" class="p-8">
                @csrf
                @method('PUT')

                <!-- Section 0: Affectation à une Gare -->
                @if(isset($gares) && $gares->count() > 0)
                <div class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-blue-500 rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Affectation à une Gare</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Gare</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <select name="gare_id" required
                                    class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white">
                                <option value="" disabled>-- Sélectionnez une gare --</option>
                                @foreach($gares as $gare)
                                    <option value="{{ $gare->id }}" {{ old('gare_id', $vehicule->gare_id) == $gare->id ? 'selected' : '' }}>
                                        {{ $gare->nom_gare }} — {{ $gare->ville }}
                                    </option>
                                @endforeach
                            </select>
                            @error('gare_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                @endif

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
                            @error('immatriculation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Numéro de série -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Numéro de série</label>
                            <input type="text" name="numero_serie" value="{{ old('numero_serie', $vehicule->numero_serie) }}"
                                class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                placeholder="Numéro de série du véhicule">
                            @error('numero_serie') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Type de rangée</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <select name="type_range" id="type_range" required
                                class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white appearance-none">
                                <option value="2x2" {{ old('type_range', $vehicule->type_range) == '2x2' ? 'selected' : '' }}>2x2 (2 places par côté)</option>
                                <option value="2x3" {{ old('type_range', $vehicule->type_range) == '2x3' ? 'selected' : '' }}>2x3 (2 à gauche, 3 à droite)</option>
                                <option value="2x4" {{ old('type_range', $vehicule->type_range) == '2x4' ? 'selected' : '' }}>2x4 (2 à gauche, 4 à droite)</option>
                            </select>
                            @error('type_range') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <span>Nombre total de places</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="number" name="nombre_place" id="nombre_place" value="{{ old('nombre_place', $vehicule->nombre_place) }}" required
                                class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                placeholder="Ex: 16">
                            @error('nombre_place') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center pt-8 border-t border-gray-200">
                    <a href="{{ route('vehicule.index') }}" class="px-8 py-4 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all">Retour</a>
                    <button type="submit" class="px-12 py-4 bg-[#e94f1b] text-white font-bold rounded-xl hover:bg-[#e89116] shadow-lg transition-all">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
