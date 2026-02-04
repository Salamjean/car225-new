@extends('compagnie.layouts.template')

@section('title', 'Créer une ligne de transport')

@section('content')
<style>
    :root {
        --primary: #e94e1a;
        --primary-dark: #d33d0f;
        --secondary: #f97316;
    }

    .hero-gradient {
        background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%);
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.98);
        border-radius: 1.5rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .input-modern {
        width: 100%;
        padding: 1rem 1.25rem;
        border: 2px solid #e5e7eb;
        border-radius: 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f9fafb;
    }

    .input-modern:focus {
        outline: none;
        border-color: var(--primary);
        background: white;
        box-shadow: 0 0 0 4px rgba(233, 78, 26, 0.1);
    }

    .section-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        padding: 1.25rem 2.5rem;
        border-radius: 1rem;
        font-weight: 700;
        font-size: 1.1rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 10px 40px -10px rgba(233, 78, 26, 0.5);
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 50px -10px rgba(233, 78, 26, 0.6);
    }

    .route-display {
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        border: 2px solid #86efac;
        border-radius: 1rem;
        padding: 1.5rem;
    }

    .feature-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        padding: 1rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .feature-card:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
    }

    select.input-modern {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1.25rem;
        padding-right: 3rem;
    }
</style>

<div class="min-h-screen bg-gray-50">
    <!-- Hero Header -->
    <div class="hero-gradient text-white py-10 px-6">
        <div class="max-w-3xl mx-auto">
            <a href="{{ route('programme.index') }}" class="inline-flex items-center gap-2 text-white/70 hover:text-white transition mb-4">
                <i class="fas fa-arrow-left"></i>
                <span>Retour aux lignes</span>
            </a>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-orange-400 to-red-500 rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-route text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Créer une ligne de transport</h1>
                    <p class="text-white/70 mt-1">Service continu 24h/24 - Les clients choisissent leur heure</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-6 -mt-6">
        <!-- Info Banner -->
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl p-4 mb-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas fa-infinity text-xl"></i>
            </div>
            <div>
                <p class="font-bold">Service continu 24h/24</p>
                <p class="text-sm text-white/80">Les départs se font en continu. L'utilisateur choisit sa date et son heure lors de la réservation.</p>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('programme.store') }}" method="POST" id="programmeForm">
            @csrf

            <div class="glass-card p-8 mb-6">
                <!-- Section 1: Itinéraire -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-5">
                        <span class="section-badge bg-blue-100 text-blue-700">
                            <i class="fas fa-map-marked-alt"></i>
                            Étape 1
                        </span>
                        <h2 class="text-lg font-bold text-gray-800">Choisir l'itinéraire</h2>
                    </div>

                    <select name="itineraire_id" id="itineraire_id" class="input-modern" required>
                        <option value="">-- Sélectionner un itinéraire --</option>
                        @foreach($itineraires as $itineraire)
                            <option value="{{ $itineraire->id }}" 
                                    data-depart="{{ $itineraire->point_depart }}"
                                    data-arrive="{{ $itineraire->point_arrive }}"
                                    data-duree="{{ $itineraire->durer_parcours }}"
                                    {{ old('itineraire_id') == $itineraire->id ? 'selected' : '' }}>
                                {{ $itineraire->point_depart }} → {{ $itineraire->point_arrive }} ({{ $itineraire->durer_parcours }})
                            </option>
                        @endforeach
                    </select>

                    <!-- Route Preview -->
                    <div id="route_preview" class="route-display hidden mt-4">
                        <div class="flex items-center justify-between">
                            <div class="text-center">
                                <div class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center mx-auto mb-2">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <p class="font-bold text-gray-800" id="preview_depart">--</p>
                                <p class="text-xs text-gray-500">Départ</p>
                            </div>
                            <div class="flex-1 px-4">
                                <div class="h-1 bg-gradient-to-r from-green-500 to-red-500 rounded-full relative">
                                    <i class="fas fa-bus text-gray-600 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-lg"></i>
                                </div>
                                <p class="text-center text-sm text-gray-500 mt-2" id="preview_duree">--</p>
                            </div>
                            <div class="text-center">
                                <div class="w-10 h-10 bg-red-500 text-white rounded-full flex items-center justify-center mx-auto mb-2">
                                    <i class="fas fa-flag-checkered"></i>
                                </div>
                                <p class="font-bold text-gray-800" id="preview_arrive">--</p>
                                <p class="text-xs text-gray-500">Arrivée</p>
                            </div>
                        </div>
                        
                        <!-- Aller-Retour indication -->
                        <div class="mt-4 pt-4 border-t border-green-200">
                            <p class="text-sm text-green-700 font-medium text-center">
                                <i class="fas fa-exchange-alt mr-2"></i>
                                2 lignes créées automatiquement : Aller + Retour
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Période -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-5">
                        <span class="section-badge bg-purple-100 text-purple-700">
                            <i class="fas fa-calendar-alt"></i>
                            Étape 2
                        </span>
                        <h2 class="text-lg font-bold text-gray-800">Période d'activité</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date de début</label>
                            <input type="date" name="date_debut" id="date_debut" class="input-modern" 
                                   min="{{ date('Y-m-d') }}" value="{{ old('date_debut', date('Y-m-d')) }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date de fin</label>
                            <input type="date" name="date_fin" id="date_fin" class="input-modern"
                                   value="{{ old('date_fin', date('Y-12-31')) }}" required>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Horaires -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-5">
                        <span class="section-badge bg-amber-100 text-amber-700">
                            <i class="fas fa-clock"></i>
                            Étape 3
                        </span>
                        <h2 class="text-lg font-bold text-gray-800">Horaires de départ</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-play text-green-500 mr-1"></i>Heure de départ
                            </label>
                            <input type="time" name="heure_depart" id="heure_depart" class="input-modern text-xl font-bold" 
                                   value="{{ old('heure_depart', '06:00') }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-flag-checkered text-red-500 mr-1"></i>Heure d'arrivée (calculée)
                            </label>
                            <input type="time" name="heure_arrive" id="heure_arrive" class="input-modern text-xl font-bold bg-gray-100" 
                                   value="{{ old('heure_arrive', '07:30') }}" readonly>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>Calculée automatiquement + durée parcours
                            </p>
                        </div>
                    </div>

                    <!-- Preview horaires -->
                    <div id="time_preview" class="hidden mt-4 bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-xl p-4">
                        <div class="flex items-center justify-center gap-6">
                            <div class="text-center">
                                <p class="text-3xl font-bold text-green-600" id="preview_heure_depart">06:00</p>
                                <p class="text-xs text-gray-500">Départ</p>
                            </div>
                            <div class="flex items-center gap-2 text-gray-400">
                                <i class="fas fa-arrow-right"></i>
                                <span class="text-sm font-medium" id="preview_time_duree">+1h30</span>
                                <i class="fas fa-arrow-right"></i>
                            </div>
                            <div class="text-center">
                                <p class="text-3xl font-bold text-red-500" id="preview_heure_arrive">07:30</p>
                                <p class="text-xs text-gray-500">Arrivée</p>
                            </div>
                        </div>
                    </div>

                    <p class="mt-3 text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1 text-blue-500"></i>
                        La ligne sera active pendant toute la période définie.
                    </p>
                </div>

                <!-- Section 4: Tarification -->
                <div>
                    <div class="flex items-center gap-3 mb-5">
                        <span class="section-badge bg-green-100 text-green-700">
                            <i class="fas fa-coins"></i>
                            Étape 4
                        </span>
                        <h2 class="text-lg font-bold text-gray-800">Tarif du billet</h2>
                    </div>

                    <div class="relative max-w-sm">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-2xl">💰</span>
                        <input type="number" name="montant_billet" id="montant_billet" 
                               class="input-modern !pl-14 !pr-20 text-xl font-bold"
                               min="0" step="100" value="{{ old('montant_billet', 5000) }}" required>
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">FCFA</span>
                    </div>
                </div>
            </div>

            <!-- Résumé -->
            <div class="glass-card p-6 mb-6">
                <h3 class="font-bold text-gray-800 mb-4">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    Ce qui sera créé
                </h3>
                
                <div class="grid grid-cols-3 gap-4">
                    <div class="feature-card">
                        <div class="text-3xl mb-2">🚌</div>
                        <p class="text-2xl font-bold text-primary">2</p>
                        <p class="text-sm text-gray-500">Lignes (A↔R)</p>
                    </div>
                    <div class="feature-card">
                        <div class="text-3xl mb-2">🕐</div>
                        <p class="text-lg font-bold text-gray-800">24h/24</p>
                        <p class="text-sm text-gray-500">Disponible</p>
                    </div>
                    <div class="feature-card">
                        <div class="text-3xl mb-2">📅</div>
                        <p class="text-lg font-bold text-gray-800" id="summary_days">--</p>
                        <p class="text-sm text-gray-500">Jours actifs</p>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-center pb-10">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-plus-circle text-xl"></i>
                    <span>Créer la ligne de transport</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const itineraireSelect = document.getElementById('itineraire_id');
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    const heureDepart = document.getElementById('heure_depart');
    const heureArrive = document.getElementById('heure_arrive');
    
    // Store duration in minutes for the selected itinerary
    let currentDurationMinutes = 90; // Default 1h30
    
    // Parse duration string like "1 heure 29 min" to minutes
    function parseDurationToMinutes(durationStr) {
        if (!durationStr) return 90;
        
        let hours = 0, minutes = 0;
        
        // Match "1 heure" or "2 heures"
        const hourMatch = durationStr.match(/(\d+)\s*heure/i);
        if (hourMatch) hours = parseInt(hourMatch[1]);
        
        // Match "29 min" or "45 minutes"
        const minMatch = durationStr.match(/(\d+)\s*min/i);
        if (minMatch) minutes = parseInt(minMatch[1]);
        
        return (hours * 60) + minutes;
    }
    
    // Calculate arrival time from departure + duration
    function calculateArrivalTime() {
        if (!heureDepart.value) return;
        
        const [hours, mins] = heureDepart.value.split(':').map(Number);
        const departDate = new Date(2026, 0, 1, hours, mins);
        departDate.setMinutes(departDate.getMinutes() + currentDurationMinutes);
        
        const arriveHours = departDate.getHours().toString().padStart(2, '0');
        const arriveMins = departDate.getMinutes().toString().padStart(2, '0');
        heureArrive.value = `${arriveHours}:${arriveMins}`;
        
        // Update preview
        const timePreview = document.getElementById('time_preview');
        if (timePreview) {
            timePreview.classList.remove('hidden');
            document.getElementById('preview_heure_depart').textContent = heureDepart.value;
            document.getElementById('preview_heure_arrive').textContent = heureArrive.value;
            
            const h = Math.floor(currentDurationMinutes / 60);
            const m = currentDurationMinutes % 60;
            const durationDisplay = m > 0 ? `+${h}h${m.toString().padStart(2, '0')}` : `+${h}h`;
            document.getElementById('preview_time_duree').textContent = durationDisplay;
        }
    }
    
    function updatePreview() {
        const selected = itineraireSelect.selectedOptions[0];
        const routePreview = document.getElementById('route_preview');
        
        if (selected && selected.value) {
            routePreview.classList.remove('hidden');
            document.getElementById('preview_depart').textContent = selected.dataset.depart;
            document.getElementById('preview_arrive').textContent = selected.dataset.arrive;
            document.getElementById('preview_duree').textContent = selected.dataset.duree;
            
            // Update duration and recalculate arrival
            currentDurationMinutes = parseDurationToMinutes(selected.dataset.duree);
            calculateArrivalTime();
        } else {
            routePreview.classList.add('hidden');
        }
        
        // Calculate days
        if (dateDebut.value && dateFin.value) {
            const start = new Date(dateDebut.value);
            const end = new Date(dateFin.value);
            const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
            document.getElementById('summary_days').textContent = days > 0 ? days : 0;
        }
    }
    
    itineraireSelect.addEventListener('change', updatePreview);
    dateDebut.addEventListener('change', updatePreview);
    dateFin.addEventListener('change', updatePreview);
    heureDepart.addEventListener('change', calculateArrivalTime);
    heureDepart.addEventListener('input', calculateArrivalTime);
    
    updatePreview();
});
</script>
@endsection