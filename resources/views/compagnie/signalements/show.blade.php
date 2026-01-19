@extends('compagnie.layouts.template')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-red-50 py-8 px-4">
        <div class="max-w-4xl mx-auto">

            <!-- Navigation & Actions -->
            <div class="mb-6 flex justify-between items-center">
                <a href="{{ route('compagnie.signalements.index') }}"
                    class="group flex items-center text-gray-500 hover:text-red-600 transition-colors font-medium">
                    <i class="fas fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform"></i>
                    Retour à la liste
                </a>
                <div class="flex gap-2">
                    @if($signalement->statut !== 'traite')
                        <button
                            class="px-4 py-2 bg-green-600 text-white rounded-xl shadow-lg hover:bg-green-700 transition-all font-bold text-sm flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Marquer comme traité
                        </button>
                    @endif
                </div>
            </div>

            <!-- Main Card -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
                <!-- Card Header with Gradient -->
                <div class="px-8 py-10 bg-gradient-to-r from-red-600 to-red-500 text-white relative">
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <span
                                class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider">
                                ID #{{ $signalement->id }}
                            </span>
                            <span
                                class="px-3 py-1 bg-white text-red-600 rounded-full text-xs font-bold uppercase tracking-wider">
                                {{ $signalement->type }}
                            </span>
                        </div>
                        <h1 class="text-3xl font-extrabold mb-2">Détails du Signalement</h1>
                        <p class="text-red-100 flex items-center gap-2">
                            <i class="far fa-calendar-alt"></i>
                            Signalé le {{ $signalement->created_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>
                    <!-- Decorative Icon -->
                    <i
                        class="fas fa-exclamation-circle absolute right-8 top-1/2 -translate-y-1/2 text-8xl text-white/10"></i>
                </div>

                <!-- Content Area -->
                <div class="p-8 sm:p-10">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                        <!-- Left Column: Incident Info -->
                        <div class="md:col-span-2 space-y-8">
                            <!-- Description Section -->
                            <section>
                                <h3
                                    class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <i class="fas fa-align-left text-red-500"></i> Description du problème
                                </h3>
                                <div
                                    class="bg-gray-50 rounded-2xl p-6 border border-gray-100 italic text-gray-700 leading-relaxed shadow-inner">
                                    "{{ $signalement->description }}"
                                </div>
                            </section>

                            <!-- Visual Evidence -->
                            @if($signalement->photo_path)
                                <section>
                                    <h3
                                        class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                        <i class="fas fa-camera text-red-500"></i> Preuve Visuelle
                                    </h3>
                                    <div class="rounded-2xl overflow-hidden border-4 border-white shadow-xl group relative">
                                        <img src="{{ Storage::url($signalement->photo_path) }}" alt="Photo de l'incident"
                                            class="w-full h-auto object-cover max-h-[500px] transition-transform duration-500 group-hover:scale-105">
                                        <div
                                            class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <a href="{{ Storage::url($signalement->photo_path) }}" target="_blank"
                                                class="bg-white text-gray-900 px-6 py-3 rounded-xl font-bold flex items-center gap-2 transform translate-y-4 group-hover:translate-y-0 transition-transform">
                                                <i class="fas fa-expand"></i> Voir en plein écran
                                            </a>
                                        </div>
                                    </div>
                                </section>
                            @endif

                            <!-- Location Section -->
                        @if($signalement->latitude && $signalement->longitude)
                        <section>
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-red-500"></i> Lieu de l'incident
                            </h3>
                            <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100 flex items-center justify-between">
                                <div class="flex items-center gap-4 flex-1">
                                    <div class="w-12 h-12 bg-white rounded-full flex-shrink-0 flex items-center justify-center shadow-sm">
                                        <i class="fas fa-location-arrow text-blue-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-blue-500 font-bold uppercase">Adresse approximative</p>
                                        <p class="font-bold text-gray-900 text-lg leading-snug" id="address-display">
                                            <i class="fas fa-spinner fa-spin mr-2"></i>Chargement de l'adresse...
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1 font-mono">
                                            GPS: {{ $signalement->latitude }}, {{ $signalement->longitude }}
                                        </p>
                                    </div>
                                </div>
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $signalement->latitude }},{{ $signalement->longitude }}" 
                                   target="_blank"
                                   class="bg-white text-blue-600 px-4 py-2 rounded-lg shadow-sm font-bold hover:shadow-md transition-all text-sm flex items-center gap-2 flex-shrink-0 ml-4">
                                    <i class="fas fa-external-link-alt"></i> GPS
                                </a>
                            </div>
                        </section>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const lat = {{ $signalement->latitude }};
                                const lon = {{ $signalement->longitude }};
                                const display = document.getElementById('address-display');

                                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data && data.display_name) {
                                            display.innerText = data.display_name;
                                        } else {
                                            display.innerText = "Adresse introuvable";
                                        }
                                    })
                                    .catch(err => {
                                        console.error(err);
                                        display.innerText = "Erreur lors de la récupération de l'adresse";
                                    });
                            });
                        </script>
                        @endif
                        </div>

                        <!-- Right Column: Sidebar Stats -->
                        <div class="space-y-6">
                            <!-- User Card -->
                            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                                <h4 class="text-xs font-bold text-gray-400 uppercase mb-4">Signalé par</h4>
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center text-red-600 font-bold text-xl">
                                        {{ substr($signalement->user->name ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $signalement->user->name.' '.$signalement->user->prenom ?? 'Inconnu' }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $signalement->user->contact ?? 'Sans numéro' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Vehicle Card -->
                            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                                <h4 class="text-xs font-bold text-gray-400 uppercase mb-4">Véhicule & Trajet</h4>
                                <div class="space-y-4">
                                    <div class="flex items-start gap-3">
                                        <i class="fas fa-bus text-gray-400 mt-1"></i>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">
                                                {{ $signalement->programme->vehicule->immatriculation ?? 'Non assigné' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $signalement->programme->vehicule->marque ?? '' }}
                                                {{ $signalement->programme->vehicule->modele ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <i class="fas fa-route text-gray-400 mt-1"></i>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">
                                                {{ $signalement->programme->point_depart ?? '?' }} →
                                                {{ $signalement->programme->point_arrive ?? '?' }}
                                            </p>
                                            <p class="text-xs text-gray-500">Trajet via programme
                                                #{{ $signalement->programme_id }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div class="bg-gray-900 rounded-2xl p-6 text-center">
                                <h4 class="text-xs font-bold text-gray-500 uppercase mb-3">Statut Actuel</h4>
                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold {{ $signalement->statut === 'traite' ? 'bg-green-500 text-white' : 'bg-red-500 text-white animate-pulse' }} shadow-lg">
                                    {{ ucfirst($signalement->statut) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Info -->
            <p class="text-center text-gray-400 text-xs mt-8">
                Signalement traité via le système de sécurité CAR225. &copy; {{ date('Y') }}
            </p>
        </div>
    </div>
@endsection