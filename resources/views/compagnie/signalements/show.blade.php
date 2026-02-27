@extends('compagnie.layouts.template')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-red-50 py-8 px-4">
        <div class="max-w-5xl mx-auto">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-2xl p-4 flex items-center gap-3 animate-fade-in">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center text-green-600 flex-shrink-0">
                        <i class="fas fa-check-circle text-lg"></i>
                    </div>
                    <p class="text-green-800 font-semibold text-sm">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4 flex items-center gap-3 animate-fade-in">
                    <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center text-red-600 flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-lg"></i>
                    </div>
                    <p class="text-red-800 font-semibold text-sm">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Navigation & Actions -->
            <div class="mb-6 flex justify-between items-center">
                <a href="{{ route('compagnie.signalements.index') }}"
                    class="group flex items-center text-gray-500 hover:text-red-600 transition-colors font-medium">
                    <i class="fas fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform"></i>
                    Retour à la liste
                </a>
                <div class="flex gap-2">
                    @if($signalement->statut !== 'traite')
                        <form action="{{ route('compagnie.signalements.mark-traite', $signalement->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white rounded-xl shadow-lg hover:bg-green-700 transition-all font-bold text-sm flex items-center gap-2">
                                <i class="fas fa-check-circle"></i> Marquer comme traité
                            </button>
                        </form>
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
                            @if($signalement->statut === 'traite')
                                <span class="px-3 py-1 bg-green-500 text-white rounded-full text-xs font-bold uppercase tracking-wider">
                                    <i class="fas fa-check mr-1"></i> Traité
                                </span>
                            @endif
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
                                        <img src="{{ asset($signalement->photo_path) }}" alt="Photo de l'incident"
                                            class="w-full h-auto object-cover max-h-[500px] transition-transform duration-500 group-hover:scale-105"
                                            onerror="this.onerror=null; this.src='{{ $signalement->photo_path }}';">
                                        <div
                                            class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <a href="{{ asset($signalement->photo_path) }}" target="_blank"
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
                            <!-- Reporter Card -->
                            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                                <h4 class="text-xs font-bold text-gray-400 uppercase mb-4">Signalé par</h4>
                                @php
                                    $isChauffeur = $signalement->personnel_id && !$signalement->user_id;
                                    if ($isChauffeur && $signalement->personnel) {
                                        $rName = $signalement->personnel->name . ' ' . ($signalement->personnel->prenom ?? '');
                                        $rContact = $signalement->personnel->contact ?? 'Sans numéro';
                                        $rInitial = strtoupper(substr($signalement->personnel->name, 0, 1));
                                        $rType = 'Chauffeur';
                                        $rColor = 'blue';
                                        $rIcon = 'fa-id-badge';
                                    } elseif ($signalement->user) {
                                        $rName = $signalement->user->name . ' ' . ($signalement->user->prenom ?? '');
                                        $rContact = $signalement->user->contact ?? $signalement->user->telephone ?? 'Sans numéro';
                                        $rInitial = strtoupper(substr($signalement->user->name, 0, 1));
                                        $rType = 'Passager';
                                        $rColor = 'purple';
                                        $rIcon = 'fa-user';
                                    } else {
                                        $rName = 'Inconnu';
                                        $rContact = 'Sans numéro';
                                        $rInitial = '?';
                                        $rType = 'Inconnu';
                                        $rColor = 'gray';
                                        $rIcon = 'fa-question';
                                    }
                                @endphp
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-12 h-12 bg-{{ $rColor }}-100 rounded-full flex items-center justify-center text-{{ $rColor }}-600 font-bold text-xl border-2 border-{{ $rColor }}-200">
                                        {{ $rInitial }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ trim($rName) }}</p>
                                        <p class="text-xs text-gray-500">{{ $rContact }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-{{ $rColor }}-50 text-{{ $rColor }}-700 text-[10px] font-black rounded-lg uppercase tracking-widest border border-{{ $rColor }}-200">
                                    <i class="fas {{ $rIcon }} text-[9px]"></i> {{ $rType }}
                                </span>
                            </div>

                            <!-- Vehicle Card -->
                            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                                <h4 class="text-xs font-bold text-gray-400 uppercase mb-4">Véhicule & Trajet</h4>
                                <div class="space-y-4">
                                    <div class="flex items-start gap-3">
                                        <i class="fas fa-bus text-gray-400 mt-1"></i>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">
                                                {{ $signalement->vehicule?->immatriculation ?? $signalement->programme?->vehicule?->immatriculation ?? 'Non assigné' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $signalement->vehicule?->marque ?? $signalement->programme?->vehicule?->marque ?? '' }}
                                                {{ $signalement->vehicule?->modele ?? $signalement->programme?->vehicule?->modele ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <i class="fas fa-route text-gray-400 mt-1"></i>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">
                                                {{ $signalement->programme?->point_depart ?? '?' }} →
                                                {{ $signalement->programme?->point_arrive ?? '?' }}
                                            </p>
                                            <p class="text-xs text-gray-500">Trajet via programme
                                                #{{ $signalement->programme_id }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sapeur Pompier Card (si accident et pompier assigné) --}}
                            @if($signalement->type === 'accident' && $signalement->sapeurPompier)
                            <div class="bg-red-50 rounded-2xl p-6 border border-red-100">
                                <h4 class="text-xs font-bold text-red-400 uppercase mb-4 flex items-center gap-2">
                                    <i class="fas fa-fire-extinguisher text-red-500"></i> Sapeur Pompier assigné
                                </h4>
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center border-2 border-red-200">
                                        <i class="fas fa-fire-alt text-red-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 text-sm">{{ $signalement->sapeurPompier->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $signalement->sapeurPompier->commune }}</p>
                                    </div>
                                </div>
                                @if($signalement->sapeurPompier->contact)
                                    <div class="flex items-center gap-2 mt-2 text-xs text-gray-500">
                                        <i class="fas fa-phone text-[10px]"></i> {{ $signalement->sapeurPompier->contact }}
                                    </div>
                                @endif
                                @if($signalement->sapeurPompier->email)
                                    <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                                        <i class="fas fa-envelope text-[10px]"></i> {{ $signalement->sapeurPompier->email }}
                                    </div>
                                @endif
                            </div>
                            @endif

                            <!-- Status Badge -->
                            <div class="bg-gray-900 rounded-2xl p-6 text-center">
                                <h4 class="text-xs font-bold text-gray-500 uppercase mb-3">Statut Actuel</h4>
                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold {{ $signalement->statut === 'traite' ? 'bg-green-500 text-white' : 'bg-red-500 text-white animate-pulse' }} shadow-lg">
                                    {{ ucfirst($signalement->statut) }}
                                </span>
                            </div>

                            {{-- Gare de départ --}}
                            @if(isset($gareDepart) && $gareDepart)
                            <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100">
                                <h4 class="text-xs font-bold text-blue-400 uppercase mb-4 flex items-center gap-2">
                                    <i class="fas fa-warehouse text-blue-500"></i> Gare de départ
                                </h4>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center border-2 border-blue-200">
                                        <i class="fas fa-warehouse text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 text-sm">{{ $gareDepart->nom_gare }}</p>
                                        <p class="text-xs text-gray-500">{{ $gareDepart->ville ?? $gareDepart->commune ?? '' }}</p>
                                    </div>
                                </div>
                                @if($gareDepart->contact)
                                    <div class="flex items-center gap-2 mt-3 text-xs text-gray-500">
                                        <i class="fas fa-phone text-[10px]"></i> {{ $gareDepart->contact }}
                                    </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- ============== ACTION BUTTONS SECTION (AFTER content) ============== --}}
                    @if($signalement->statut !== 'traite' && in_array($signalement->type, ['accident', 'panne', 'retard']))
                    <div class="mt-10 bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl border border-amber-200 p-6">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-bolt text-amber-600 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">Actions d'urgence</h3>
                                <p class="text-xs text-gray-500">Alertez les parties concernées rapidement</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-{{ $signalement->type === 'accident' ? '2' : '1' }} gap-4">
                            {{-- ALERTER LA GARE --}}
                            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-warehouse text-blue-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-sm">Alerter la Gare</h4>
                                        @if(isset($gareDepart) && $gareDepart)
                                            <p class="text-[10px] text-gray-400">{{ $gareDepart->nom_gare }} — {{ $gareDepart->email ?? 'pas d\'email' }}</p>
                                        @else
                                            <p class="text-[10px] text-red-400">Aucune gare de départ trouvée</p>
                                        @endif
                                    </div>
                                </div>

                                @if(isset($gareDepart) && $gareDepart)
                                    <form action="{{ route('compagnie.signalements.alert-gare', $signalement->id) }}" method="POST">
                                        @csrf
                                        <textarea name="message" rows="2" placeholder="Message personnalisé à la gare (optionnel)..."
                                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all resize-none mb-3"></textarea>
                                        <button type="submit"
                                            class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-blue-200 hover:bg-blue-700 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                                            <i class="fas fa-paper-plane"></i> Envoyer l'alerte à la gare
                                        </button>
                                    </form>
                                @else
                                    <div class="py-4 px-4 bg-gray-50 rounded-xl text-center text-gray-400 text-xs font-medium">
                                        <i class="fas fa-info-circle mr-1"></i> Aucune gare de départ assignée à ce programme
                                    </div>
                                @endif
                            </div>

                            {{-- CONTACTER SAPEUR POMPIER (seulement pour les accidents) --}}
                            @if($signalement->type === 'accident')
                            <div class="bg-white rounded-2xl border border-red-100 p-5 shadow-sm">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-fire-extinguisher text-red-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-sm">Contacter Sapeur Pompier</h4>
                                        @if(isset($sapeurPompier) && $sapeurPompier)
                                            <p class="text-[10px] text-gray-400">
                                                {{ $sapeurPompier->name }} — {{ $sapeurPompier->commune }}
                                                @if($signalement->sapeur_pompier_id)
                                                    <span class="text-green-500 font-bold">(Déjà assigné)</span>
                                                @else
                                                    <span class="text-blue-500">(Le plus proche)</span>
                                                @endif
                                            </p>
                                        @else
                                            <p class="text-[10px] text-red-400">Aucun pompier disponible à proximité</p>
                                        @endif
                                    </div>
                                </div>

                                @if(isset($sapeurPompier) && $sapeurPompier)
                                    <form action="{{ route('compagnie.signalements.alert-pompier', $signalement->id) }}" method="POST">
                                        @csrf
                                        <textarea name="message" rows="2" placeholder="Instructions supplémentaires (optionnel)..."
                                            class="w-full px-4 py-2.5 bg-gray-50 border border-red-50 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:bg-white outline-none transition-all resize-none mb-3"></textarea>
                                        <button type="submit"
                                            class="w-full py-3 bg-red-600 text-white rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-red-200 hover:bg-red-700 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                                            <i class="fas fa-fire-extinguisher"></i> Contacter le Sapeur Pompier
                                        </button>
                                    </form>
                                @elseif(!$signalement->latitude || !$signalement->longitude)
                                    <div class="py-4 px-4 bg-red-50 rounded-xl text-center text-red-400 text-xs font-medium">
                                        <i class="fas fa-map-marker-alt mr-1"></i> Coordonnées GPS manquantes pour localiser un pompier
                                    </div>
                                @else
                                    <div class="py-4 px-4 bg-gray-50 rounded-xl text-center text-gray-400 text-xs font-medium">
                                        <i class="fas fa-info-circle mr-1"></i> Aucun sapeur pompier actif trouvé à proximité
                                    </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                </div>
            </div>

            <!-- Footer Info -->
            <p class="text-center text-gray-400 text-xs mt-8">
                Signalement traité via le système de sécurité CAR225. &copy; {{ date('Y') }}
            </p>
        </div>
    </div>
@endsection