@extends('compagnie.layouts.template')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8 px-4">
    <div class="max-w-3xl mx-auto">
        {{-- En-tête --}}
        <div class="mb-8">
            <a href="{{ route('programme.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-2 inline-flex items-center gap-1">
                <i class="fas fa-arrow-left"></i> Retour aux programmes
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Modifier le programme</h1>
        </div>

        {{-- Infos route (lecture seule) --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-route text-xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">
                        {{ $programme->point_depart }} <i class="fas fa-arrow-right mx-1 text-orange-500"></i> {{ $programme->point_arrive }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        @if($programme->gareDepart)
                            <i class="fas fa-map-marker-alt text-green-500 mr-1"></i>{{ $programme->gareDepart->nom_gare }}
                            <i class="fas fa-arrow-right mx-1 text-gray-300"></i>
                            <i class="fas fa-map-marker-alt text-red-500 mr-1"></i>{{ $programme->gareArrivee->nom_gare ?? 'N/A' }}
                        @endif
                        @if($programme->durer_parcours)
                            <span class="ml-3 text-gray-400">|</span>
                            <i class="fas fa-clock ml-2 mr-1 text-blue-400"></i>{{ $programme->durer_parcours }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Formulaire --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-800 to-gray-900 text-white">
                <h3 class="font-bold flex items-center gap-2">
                    <i class="fas fa-edit"></i> Paramètres du programme
                </h3>
            </div>

            <form action="{{ route('programme.update', $programme->id) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                {{-- Erreurs --}}
                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- Heure de départ --}}
                    <div>
                        <label for="heure_depart" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-clock text-green-500 mr-1"></i> Heure de départ
                        </label>
                        <input type="time" name="heure_depart" id="heure_depart" 
                            value="{{ old('heure_depart', \Carbon\Carbon::parse($programme->heure_depart)->format('H:i')) }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent text-lg font-bold">
                    </div>

                    {{-- Heure d'arrivée --}}
                    <div>
                        <label for="heure_arrive" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-flag-checkered text-red-500 mr-1"></i> Heure d'arrivée
                        </label>
                        <input type="time" name="heure_arrive" id="heure_arrive" 
                            value="{{ old('heure_arrive', \Carbon\Carbon::parse($programme->heure_arrive)->format('H:i')) }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent text-lg font-bold">
                    </div>

                    {{-- Montant du billet --}}
                    <div>
                        <label for="montant_billet" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-money-bill-wave text-yellow-500 mr-1"></i> Montant du billet (FCFA)
                        </label>
                        <input type="number" name="montant_billet" id="montant_billet" 
                            value="{{ old('montant_billet', intval($programme->montant_billet)) }}" required min="0" step="100"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent text-lg font-bold">
                    </div>

                    {{-- Nombre de places --}}
                    <div>
                        <label for="capacity" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-chair text-blue-500 mr-1"></i> Nombre de places
                        </label>
                        <input type="number" name="capacity" id="capacity" 
                            value="{{ old('capacity', $programme->capacity ?? 50) }}" required min="1"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent text-lg font-bold">
                        <p class="text-xs text-gray-400 mt-1">Capacité maximale du véhicule pour ce programme</p>
                    </div>

                    {{-- Statut --}}
                    <div class="md:col-span-2">
                        <label for="statut" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-toggle-on text-purple-500 mr-1"></i> Statut
                        </label>
                        <div class="flex gap-4">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="statut" value="actif" class="hidden peer"
                                    {{ old('statut', $programme->statut) == 'actif' ? 'checked' : '' }}>
                                <div class="peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 border-2 border-gray-200 rounded-xl p-4 text-center transition-all hover:border-green-300">
                                    <i class="fas fa-check-circle text-2xl mb-1"></i>
                                    <p class="font-bold">Actif</p>
                                    <p class="text-xs opacity-70">Programme visible et réservable</p>
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="statut" value="annule" class="hidden peer"
                                    {{ old('statut', $programme->statut) == 'annule' ? 'checked' : '' }}>
                                <div class="peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 border-2 border-gray-200 rounded-xl p-4 text-center transition-all hover:border-red-300">
                                    <i class="fas fa-times-circle text-2xl mb-1"></i>
                                    <p class="font-bold">Annulé</p>
                                    <p class="text-xs opacity-70">Programme désactivé</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('programme.index') }}" 
                        class="px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        <i class="fas fa-times mr-1"></i> Annuler
                    </a>
                    <button type="submit" 
                        class="px-8 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white font-bold rounded-xl hover:from-orange-600 hover:to-red-600 transform hover:-translate-y-0.5 transition-all duration-200 shadow-lg">
                        <i class="fas fa-save mr-2"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Parser la durée du parcours
    function parseDuration(str) {
        if (!str) return 90;
        let h = 0, m = 0;
        const hM = str.match(/(\d+)\s*h/i);
        if (hM) h = parseInt(hM[1]);
        const mM = str.match(/(\d+)\s*m/i);
        if (mM) m = parseInt(mM[1]);
        return (h * 60) + m || 90;
    }

    const dureeMinutes = parseDuration(@json($programme->durer_parcours));
    const departInput = document.getElementById('heure_depart');
    const arriveeInput = document.getElementById('heure_arrive');

    departInput.addEventListener('change', function() {
        if (this.value) {
            const [h, m] = this.value.split(':').map(Number);
            const total = h * 60 + m + dureeMinutes;
            const arrH = Math.floor(total / 60) % 24;
            const arrM = total % 60;
            arriveeInput.value = `${String(arrH).padStart(2, '0')}:${String(arrM).padStart(2, '0')}`;
        }
    });
</script>
@endsection