@extends('compagnie.layouts.template')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
    <div class="mx-auto" style="width: 80%">
        <!-- En-tête -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#e94f1b] rounded-2xl shadow-lg mb-4">
                <i class="fas fa-edit text-3xl text-white"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Modifier la Gare</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Mettre à jour les informations de {{ $gare->nom_gare }}
            </p>
        </div>

        <!-- Formulaire -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="{{ route('gare.update', $gare->id) }}" method="POST" class="p-8">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Nom de la gare -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Nom de la gare <span class="text-red-500">*</span></label>
                        <input type="text" name="nom_gare" value="{{ old('nom_gare', $gare->nom_gare) }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent outline-none transition-all bg-gray-50 focus:bg-white"
                            placeholder="Ex: Gare de Bassam">
                        @error('nom_gare')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ville -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Ville <span class="text-red-500">*</span></label>
                        <input type="text" name="ville" value="{{ old('ville', $gare->ville) }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent outline-none transition-all bg-gray-50 focus:bg-white"
                            placeholder="Ex: Abidjan">
                        @error('ville')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Adresse -->
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700">Adresse / Situation Géographique <span class="text-red-500">*</span></label>
                        <input type="text" name="adresse" value="{{ old('adresse', $gare->adresse) }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent outline-none transition-all bg-gray-50 focus:bg-white"
                            placeholder="Ex: Rue 12, Face au marché">
                        @error('adresse')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 justify-between items-center pt-8 border-t border-gray-100">
                    <a href="{{ route('gare.index') }}"
                        class="flex items-center px-8 py-3 text-gray-700 font-semibold rounded-xl border border-gray-300 hover:bg-gray-50 transition-all">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Retour
                    </a>

                    <button type="submit"
                        class="flex items-center px-8 py-3 bg-[#e94f1b] text-white font-bold rounded-xl hover:bg-[#e89116] transform hover:-translate-y-1 transition-all shadow-lg">
                        <i class="fas fa-check mr-2"></i>
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
