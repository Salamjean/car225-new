@extends('compagnie.layouts.template')

@section('title', 'Créer une ligne de transport')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    :root {
        --primary: #e94e1a;
        --primary-dark: #d33d0f;
        --secondary: #f97316;
        --aller-color: #10b981;
        --aller-dark: #059669;
        --retour-color: #3b82f6;
        --retour-dark: #2563eb;
    }

    /* Tom Select Modern Styling */
    .ts-control {
        border-radius: 0.75rem !important;
        padding: 0.875rem 1rem !important;
        background-color: #f9fafb !important;
        border: 2px solid #e5e7eb !important;
        font-size: 1rem !important;
        transition: all 0.3s ease !important;
    }
    .ts-wrapper.focus .ts-control {
        box-shadow: 0 0 0 4px rgba(233, 78, 26, 0.1) !important;
        border-color: var(--primary) !important;
        background-color: white !important;
    }
    .ts-dropdown {
        border-radius: 0.75rem !important;
        margin-top: 0.5rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        border: 1px solid #e5e7eb !important;
    }
    .ts-dropdown .active {
        background-color: rgba(233, 78, 26, 0.1) !important;
        color: var(--primary) !important;
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
        padding: 0.875rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
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

    select.input-modern {
        display: none !important;
    }

    /* Dual Card Layout */
    .dual-card-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-top: 2rem;
    }

    @media (max-width: 768px) {
        .dual-card-container {
            grid-template-columns: 1fr;
        }
    }

    .programme-card {
        background: white;
        border-radius: 1.25rem;
        overflow: hidden;
        box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.1);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 2px solid transparent;
    }

    .programme-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 50px -15px rgba(0, 0, 0, 0.15);
    }

    .programme-card.aller {
        border-color: rgba(16, 185, 129, 0.3);
    }

    .programme-card.aller:hover {
        border-color: var(--aller-color);
    }

    .programme-card.retour {
        border-color: rgba(59, 130, 246, 0.3);
    }

    .programme-card.retour:hover {
        border-color: var(--retour-color);
    }

    .card-header {
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .card-header.aller {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .card-header.retour {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    .card-icon {
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
    }

    .card-title {
        color: white;
    }

    .card-title h3 {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0;
    }

    .card-title .route {
        font-size: 0.875rem;
        opacity: 0.9;
        margin-top: 0.25rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Schedule Items */
    .schedule-container {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .schedule-item {
        background: white;
        border: 1px solid #eef2f6;
        border-radius: 1rem;
        padding: 1rem;
        position: relative;
        animation: slideIn 0.3s ease-out;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .schedule-item:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
        border-color: #e2e8f0;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .schedule-item .badge {
        position: absolute;
        top: -10px;
        left: 0.75rem;
        background: #6366f1;
        color: white;
        font-size: 0.65rem;
        font-weight: 800;
        padding: 0.2rem 0.6rem;
        border-radius: 6px;
        text-transform: uppercase;
        z-index: 10;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .schedule-item.aller .badge {
        background: var(--aller-color);
    }

    .schedule-item.retour .badge {
        background: var(--retour-color);
    }

    .schedule-times {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-top: 0.25rem;
    }

    .time-input-group {
        flex: 1;
    }

    .time-input-group label {
        display: block;
        font-size: 0.65rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        margin-bottom: 0.25rem;
    }

    .time-input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1.5px solid #f1f5f9;
        border-radius: 0.6rem;
        font-size: 1.1rem;
        font-weight: 700;
        text-align: center;
        transition: all 0.2s ease;
        background: #f8fafc;
        color: #334155;
    }

    .time-input:focus {
        outline: none;
        border-color: var(--primary);
        background: white;
        box-shadow: 0 0 0 3px rgba(233, 78, 26, 0.1);
    }

    .time-input.arrival {
        background: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
        border-style: dashed;
    }

    .time-arrow {
        color: #cbd5e1;
        margin-top: 1rem;
    }

    .remove-schedule {
        position: absolute;
        top: -8px;
        right: -8px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: white;
        border: 1px solid #fee2e2;
        color: #ef4444;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        z-index: 20;
    }

    .remove-schedule:hover {
        background: #ef4444;
        color: white;
        transform: scale(1.1) rotate(90deg);
        border-color: #ef4444;
    }

    /* Add Schedule Button */
    .add-schedule-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.75rem;
        border: 2px dashed #e2e8f0;
        border-radius: 1rem;
        background: #f8fafc;
        color: #64748b;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 1rem;
    }

    .add-schedule-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: #fff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .add-schedule-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: rgba(233, 78, 26, 0.05);
    }

    .add-schedule-btn.aller:hover {
        border-color: var(--aller-color);
        color: var(--aller-color);
        background: rgba(16, 185, 129, 0.05);
    }

    .add-schedule-btn.retour:hover {
        border-color: var(--retour-color);
        color: var(--retour-color);
        background: rgba(59, 130, 246, 0.05);
    }

    /* Summary Section */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    @media (max-width: 768px) {
        .summary-grid {
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        }
    }

    .feature-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        padding: 1.25rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .feature-card:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
    }

    .feature-card .icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .feature-card .value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--primary);
    }

    .feature-card .label {
        font-size: 0.75rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Hidden State */
    .hidden {
        display: none !important;
    }

    /* Tarif Section */
    .tarif-section {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border: 2px solid #fbbf24;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-top: 1.5rem;
    }

    .tarif-input-wrapper {
        position: relative;
        max-width: 280px;
        margin: 0 auto;
    }

    .tarif-input-wrapper .icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.5rem;
    }

    .tarif-input-wrapper .currency {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        font-weight: 600;
        color: #92400e;
    }

    .tarif-input {
        width: 100%;
        padding: 1rem 5rem 1rem 3.5rem;
        border: 2px solid #f59e0b;
        border-radius: 0.75rem;
        font-size: 1.5rem;
        font-weight: 800;
        text-align: center;
        background: white;
        transition: all 0.3s ease;
    }

    .tarif-input:focus {
        outline: none;
        border-color: #d97706;
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.2);
    }

    /* Toggle Switch */
    .switch-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
        padding: 1.25rem 1.5rem;
        border-radius: 1.25rem;
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
        transition: all 0.3s ease;
    }

    .switch-wrapper:hover {
        border-color: var(--retour-color);
        background: white;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .switch-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .switch-icon {
        width: 40px;
        height: 40px;
        background: rgba(59, 130, 246, 0.1);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--retour-color);
        font-size: 1.1rem;
    }

    .switch-text h4 {
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .switch-text p {
        font-size: 0.75rem;
        color: #64748b;
        margin: 0;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 54px;
        height: 28px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: .4s;
        border-radius: 34px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    input:checked + .toggle-slider {
        background-color: var(--retour-color);
    }

    input:checked + .toggle-slider:before {
        transform: translateX(26px);
    }

    #retour-section-container {
        transition: all 0.4s ease;
    }

    #retour-section-container.collapsed {
        opacity: 0;
        transform: scale(0.95);
        pointer-events: none;
        display: none;
    }

    .dual-card-container.single {
        grid-template-columns: 1fr;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Header -->
    <div class="hero-gradient text-white py-10 px-6">
        <div class="max-w-5xl mx-auto">
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
                    <p class="text-white/70 mt-1">Configurez les horaires Aller et Retour en un seul formulaire</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-6 -mt-6">
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
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-5">
                        <span class="section-badge bg-blue-100 text-blue-700">
                            <i class="fas fa-map-marked-alt"></i>
                            Étape 1
                        </span>
                        <h2 class="text-lg font-bold text-gray-800">Choisir l'itinéraire et les gares</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Itinéraire</label>
                            <select name="itineraire_id" id="itineraire_id" class="input-modern" required>
                                <option value="">-- Sélectionner un itinéraire --</option>
                                @foreach($itineraires as $itineraire)
                                    <option value="{{ $itineraire->id }}" 
                                            data-depart="{{ $itineraire->point_depart }}"
                                            data-arrive="{{ $itineraire->point_arrive }}"
                                            data-duree="{{ $itineraire->durer_parcours }}"
                                            {{ old('itineraire_id', $preselectedItineraireId) == $itineraire->id ? 'selected' : '' }}>
                                        {{ $itineraire->point_depart }} → {{ $itineraire->point_arrive }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Gare de départ</label>
                            <select name="gare_depart_id" id="gare_depart_id" class="input-modern" required>
                                <option value="">-- Sélectionner --</option>
                                @foreach($gares as $gare)
                                    <option value="{{ $gare->id }}">{{ $gare->nom_gare }} ({{ $gare->ville }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Gare d'arrivée</label>
                            <select name="gare_arrivee_id" id="gare_arrivee_id" class="input-modern" required>
                                <option value="">-- Sélectionner --</option>
                                @foreach($gares as $gare)
                                    <option value="{{ $gare->id }}">{{ $gare->nom_gare }} ({{ $gare->ville }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Dual Card Container - Hidden until itinerary selected -->
                <div id="cards-container" class="hidden">
                    <div class="flex items-center gap-3 mb-5">
                        <span class="section-badge bg-purple-100 text-purple-700">
                            <i class="fas fa-clock"></i>
                            Étape 2
                        </span>
                        <h2 class="text-lg font-bold text-gray-800">Configurer les horaires</h2>
                    </div>

                    <!-- Toggle Retour -->
                    <div class="switch-wrapper">
                        <div class="switch-info">
                            <div class="switch-icon">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                            <div class="switch-text">
                                <h4>Créer le trajet retour ?</h4>
                                <p>Activez cette option pour configurer le voyage dans le sens inverse</p>
                            </div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="with_retour" id="toggle-retour">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="dual-card-container" id="dual-cards">
                        <!-- Card ALLER -->
                        <div class="programme-card aller">
                            <div class="card-header aller">
                                <div class="card-icon">
                                    <i class="fas fa-bus-alt"></i>
                                </div>
                                <div class="card-title">
                                    <h3>Aller</h3>
                                    <div class="route" id="aller-route">-- → --</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="schedule-container" id="aller-schedules">
                                    <!-- Initial schedule will be added by JS -->
                                </div>
                                <button type="button" class="add-schedule-btn aller" onclick="addSchedule('aller')">
                                    <i class="fas fa-plus-circle"></i>
                                    Ajouter un horaire
                                </button>
                            </div>
                        </div>

                        <!-- Card RETOUR -->
                        <div id="retour-section-container">
                            <div class="programme-card retour">
                                <div class="card-header retour">
                                    <div class="card-icon">
                                        <i class="fas fa-bus"></i>
                                    </div>
                                    <div class="card-title">
                                        <h3>Retour</h3>
                                        <div class="route" id="retour-route">-- → --</div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="schedule-container" id="retour-schedules">
                                        <!-- Initial schedule will be added by JS -->
                                    </div>
                                    <button type="button" class="add-schedule-btn retour" onclick="addSchedule('retour')">
                                        <i class="fas fa-plus-circle"></i>
                                        Ajouter un horaire
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Capacité et Tarification -->
                    <div class="tarif-section">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                            <!-- Capacité -->
                            <div>
                                <div class="text-center mb-4">
                                    <span class="section-badge bg-blue-200 text-blue-800">
                                        <i class="fas fa-users"></i>
                                        Étape 3
                                    </span>
                                    <h2 class="text-lg font-bold text-gray-800 mt-2">Capacité du car</h2>
                                    <p class="text-sm text-blue-700">Nombre de places par défaut</p>
                                </div>
                                <div class="tarif-input-wrapper">
                                    <span class="icon">💺</span>
                                    <input type="number" name="capacity" id="capacity" 
                                           class="tarif-input"
                                           min="1" max="100" value="{{ old('capacity', 64) }}" placeholder="64" required>
                                    <span class="currency">Places</span>
                                </div>
                            </div>

                            <!-- Prix -->
                            <div>
                                <div class="text-center mb-4">
                                    <span class="section-badge bg-amber-200 text-amber-800">
                                        <i class="fas fa-coins"></i>
                                        Étape 4
                                    </span>
                                    <h2 class="text-lg font-bold text-gray-800 mt-2">Tarif du billet</h2>
                                    <p class="text-sm text-amber-700">Prix identique Aller/Retour</p>
                                </div>
                                <div class="tarif-input-wrapper">
                                    <span class="icon">💰</span>
                                    <input type="number" name="montant_billet" id="montant_billet" 
                                           class="tarif-input"
                                           min="0" step="100" value="{{ old('montant_billet', $existingMontantBillet) }}" placeholder="0" required>
                                    <span class="currency">FCFA</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Résumé -->
            <div class="glass-card p-6 mb-6" id="summary-section" style="display: none;">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-check-circle text-green-500"></i>
                    Récapitulatif des programmes à créer
                </h3>
                
                <div class="summary-grid">
                    <div class="feature-card">
                        <div class="icon">🚌</div>
                        <p class="value" id="summary-aller">1</p>
                        <p class="label">Programme(s) Aller</p>
                    </div>
                    <div class="feature-card" id="summary-retour-card">
                        <div class="icon">🔄</div>
                        <p class="value" id="summary-retour">1</p>
                        <p class="label">Programme(s) Retour</p>
                    </div>
                    <div class="feature-card">
                        <div class="icon">📋</div>
                        <p class="value" id="summary-total">2</p>
                        <p class="label">Total Programmes</p>
                    </div>
                    <div class="feature-card">
                        <div class="icon">💵</div>
                        <p class="value" id="summary-price">0</p>
                        <p class="label">FCFA / Billet</p>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-center pb-10" id="submit-section" style="display: none;">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-rocket text-xl"></i>
                    <span>Créer les programmes</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const itineraireSelect = document.getElementById('itineraire_id');
    const cardsContainer = document.getElementById('cards-container');
    const summarySection = document.getElementById('summary-section');
    const submitSection = document.getElementById('submit-section');
    const montantBillet = document.getElementById('montant_billet');
    
    let currentDurationMinutes = 90;
    let allerCount = 1;
    let retourCount = 1;

    const toggleRetour = document.getElementById('toggle-retour');
    const retourSection = document.getElementById('retour-section-container');
    const dualCards = document.getElementById('dual-cards');
    const summaryRetourCard = document.getElementById('summary-retour-card');

    // Initialisation Tom Select
    const tsSettings = {
        create: false,
        placeholder: "-- Choisir --",
        maxOptions: 50,
        allowEmptyOption: true
    };

    const tsItineraire = new TomSelect("#itineraire_id", tsSettings);
    const tsGareDepart = new TomSelect("#gare_depart_id", tsSettings);
    const tsGareArrivee = new TomSelect("#gare_arrivee_id", tsSettings);

    function handleRetourToggle() {
        const isWithRetour = toggleRetour.checked;
        
        if (isWithRetour) {
            retourSection.classList.remove('collapsed');
            dualCards.classList.remove('single');
            summaryRetourCard.style.display = 'block';
            retourSection.querySelectorAll('input, select').forEach(el => el.disabled = false);
        } else {
            retourSection.classList.add('collapsed');
            dualCards.classList.add('single');
            summaryRetourCard.style.display = 'none';
            retourSection.querySelectorAll('input, select').forEach(el => el.disabled = true);
        }
        updateSummary();
    }

    toggleRetour.addEventListener('change', handleRetourToggle);

    function parseDurationToMinutes(durationStr) {
        if (!durationStr) return 90;
        let hours = 0, minutes = 0;
        const hourMatch = durationStr.match(/(\d+)\s*heure/i);
        if (hourMatch) hours = parseInt(hourMatch[1]);
        const minMatch = durationStr.match(/(\d+)\s*min/i);
        if (minMatch) minutes = parseInt(minMatch[1]);
        return (hours * 60) + minutes;
    }

    window.calculateArrivalTime = function(departureInput) {
        if (!departureInput || !departureInput.value) return;
        const [hours, mins] = departureInput.value.split(':').map(Number);
        if (isNaN(hours) || isNaN(mins)) return;
        const departDate = new Date(2026, 0, 1, hours, mins);
        departDate.setMinutes(departDate.getMinutes() + currentDurationMinutes);
        const arriveHours = departDate.getHours().toString().padStart(2, '0');
        const arriveMins = departDate.getMinutes().toString().padStart(2, '0');
        const scheduleItem = departureInput.closest('.schedule-item');
        if (scheduleItem) {
            const arrivalInput = scheduleItem.querySelector('.time-input.arrival');
            if (arrivalInput) arrivalInput.value = `${arriveHours}:${arriveMins}`;
        }
    };

    function updateAllArrivalTimes() {
        document.querySelectorAll('.departure-time').forEach(input => calculateArrivalTime(input));
    }

    function updateSummary() {
        const allerSchedules = document.querySelectorAll('#aller-schedules .schedule-item').length;
        const retourSchedules = toggleRetour.checked ? document.querySelectorAll('#retour-schedules .schedule-item').length : 0;
        document.getElementById('summary-aller').textContent = allerSchedules;
        document.getElementById('summary-retour').textContent = retourSchedules;
        document.getElementById('summary-total').textContent = allerSchedules + retourSchedules;
        document.getElementById('summary-price').textContent = montantBillet.value;
    }

    itineraireSelect.addEventListener('change', function() {
        const selectedValue = this.value;
        const option = this.options[this.selectedIndex];
        
        if (selectedValue) {
            cardsContainer.classList.remove('hidden');
            summarySection.style.display = 'block';
            submitSection.style.display = 'flex';
            
            const depart = option.dataset.depart;
            const arrive = option.dataset.arrive;
            document.getElementById('aller-route').textContent = `${depart} → ${arrive}`;
            document.getElementById('retour-route').textContent = `${arrive} → ${depart}`;
            
            currentDurationMinutes = parseDurationToMinutes(option.dataset.duree);
            updateAllArrivalTimes();
            updateSummary();
        } else {
            cardsContainer.classList.add('hidden');
            summarySection.style.display = 'none';
            submitSection.style.display = 'none';
        }
    });

    const vehicules = @json($vehicules ?? []);
    const chauffeurs = @json($chauffeurs ?? []);

    function generateOptions(items, valueField, labelField, includeSame = false) {
        let html = '<option value="">-- Choisir --</option>';
        if (includeSame) html += '<option value="same">Identique au précédent</option>';
        items.forEach(item => {
            const label = item[labelField] + (item.prenom ? ' ' + item.prenom : '');
            html += `<option value="${item[valueField]}">${label}</option>`;
        });
        return html;
    }

    window.addSchedule = function(type, initialTime = null, initialArrivalTime = null) {
        const container = document.getElementById(`${type}-schedules`);
        const items = container.querySelectorAll('.schedule-item');
        const newIndex = items.length;
        let nextTime = type === 'aller' ? '06:00' : '14:00';
        if (initialTime) nextTime = initialTime;
        else if (items.length > 0) {
            const lastItem = items[items.length - 1];
            const lastTimeInput = lastItem.querySelector('.departure-time');
            if (lastTimeInput && lastTimeInput.value) {
                const [hours, mins] = lastTimeInput.value.split(':').map(Number);
                let nextHour = (hours + 1) % 24;
                nextTime = `${nextHour.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}`;
            }
        }
        
        const scheduleHtml = `
            <div class="schedule-item ${type}" data-index="${newIndex}">
                <span class="badge">Horaire ${newIndex + 1}</span>
                ${newIndex > 0 ? `<button type="button" class="remove-schedule" onclick="window.removeSchedule('${type}', ${newIndex})"><i class="fas fa-times"></i></button>` : ''}
                <div class="schedule-times">
                    <div class="time-input-group">
                        <label>Départ</label>
                        <input type="time" name="${type}_horaires[${newIndex}][heure_depart]" 
                               class="time-input ${type} departure-time" 
                               value="${nextTime}" required
                               data-type="${type}" data-index="${newIndex}">
                    </div>
                    <div class="time-arrow"><i class="fas fa-long-arrow-alt-right"></i></div>
                    <div class="time-input-group">
                        <label>Arrivée</label>
                        <input type="time" name="${type}_horaires[${newIndex}][heure_arrive]" 
                               class="time-input arrival ${type}" 
                               value="${initialArrivalTime || ''}" readonly>
                    </div>
                </div>
            </div>`;
        container.insertAdjacentHTML('beforeend', scheduleHtml);
        const newRow = container.querySelector(`.schedule-item[data-index="${newIndex}"]`);
        calculateArrivalTime(newRow.querySelector('.departure-time'));
        updateSummary();
    };

    window.removeSchedule = function(type, index) {
        const container = document.getElementById(`${type}-schedules`);
        const items = container.querySelectorAll('.schedule-item');
        if (items.length <= 1) { alert('Vous devez conserver au moins un horaire'); return; }
        const item = container.querySelector(`.schedule-item[data-index="${index}"]`);
        if (item) { item.remove(); reindexSchedules(type); updateSummary(); }
    };

    function reindexSchedules(type) {
        const container = document.getElementById(`${type}-schedules`);
        const items = container.querySelectorAll('.schedule-item');
        items.forEach((item, idx) => {
            item.dataset.index = idx;
            item.querySelector('.badge').textContent = `Horaire ${idx + 1}`;
            const departInput = item.querySelector('input[name*="heure_depart"]');
            const arriveInput = item.querySelector('input[name*="heure_arrive"]');
            departInput.name = `${type}_horaires[${idx}][heure_depart]`;
            departInput.dataset.index = idx;
            arriveInput.name = `${type}_horaires[${idx}][heure_arrive]`;
            const removeBtn = item.querySelector('.remove-schedule');
            if (removeBtn) removeBtn.setAttribute('onclick', `window.removeSchedule('${type}', ${idx})`);
        });
    }

    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('departure-time')) calculateArrivalTime(e.target);
    });

    document.getElementById('programmeForm').addEventListener('submit', function(e) {
        if (document.getElementById('gare_depart_id').value === document.getElementById('gare_arrivee_id').value) {
            e.preventDefault();
            alert('La gare de départ et la gare d\'arrivée doivent être différentes.');
        }
    });

    const existingAller = @json($existingAller ?? []);
    const existingRetour = @json($existingRetour ?? []);
    const preselectedId = "{{ $preselectedItineraireId ?? '' }}";
    const oldId = "{{ old('itineraire_id') }}";
    const initialId = preselectedId || oldId || itineraireSelect.value;

    if (initialId) {
        tsItineraire.setValue(initialId);
        setTimeout(() => {
            const allerContainer = document.getElementById('aller-schedules');
            const retourContainer = document.getElementById('retour-schedules');
            if (existingAller.length > 0) {
                allerContainer.innerHTML = '';
                existingAller.forEach(h => window.addSchedule('aller', h.heure_depart, h.heure_arrive));
            } else if (allerContainer.children.length === 0) window.addSchedule('aller');
            if (existingRetour.length > 0) {
                retourContainer.innerHTML = '';
                existingRetour.forEach(h => window.addSchedule('retour', h.heure_depart, h.heure_arrive));
            } else if (retourContainer.children.length === 0) window.addSchedule('retour');
        }, 300);
    } else {
        document.getElementById('aller-schedules').innerHTML = '';
        document.getElementById('retour-schedules').innerHTML = '';
        window.addSchedule('aller');
        window.addSchedule('retour');
    }

    montantBillet.addEventListener('input', updateSummary);
    handleRetourToggle();
});
</script>
@endsection