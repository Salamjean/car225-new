@extends('compagnie.layouts.template')
@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
        <div class="mx-auto" style="width: 90%">
            <!-- En-tête -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-[#e94f1b] rounded-2xl shadow-lg mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-3">Nouveau Programme</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Planifiez un nouveau trajet pour votre compagnie
                </p>
            </div>

            <!-- Carte du formulaire -->
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
                <form action="{{ route('programme.store') }}" method="POST" class="p-8">
                    @csrf

                    <!-- Section 1: Informations de base -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-2 h-8 bg-[#e94f1b] rounded-full mr-4"></div>
                            <h2 class="text-2xl font-bold text-gray-900">Informations du trajet</h2>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                            <!-- Itinéraire -->
                            <div class="space-y-2">
                                <label class="flex items-center text-sm font-semibold text-gray-700">
                                    Itinéraire
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="relative">
                                    <select name="itineraire_id" id="itineraire_id" required
                                        class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white appearance-none">
                                        <option value="">Sélectionnez un itinéraire</option>
                                        @foreach ($itineraires as $itineraire)
                                            <option value="{{ $itineraire->id }}"
                                                data-point-depart="{{ $itineraire->point_depart }}"
                                                data-point-arrive="{{ $itineraire->point_arrive }}"
                                                data-durer="{{ $itineraire->durer_parcours }}">
                                                {{ $itineraire->point_depart }} → {{ $itineraire->point_arrive }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                @error('itineraire_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Durée du parcours (auto-rempli) -->
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-700">Durée du parcours</label>
                                <div class="relative">
                                    <input type="text" id="durer_parcours" name="durer_parcours" readonly
                                        class="w-full px-4 py-4 border border-gray-200 rounded-xl bg-gray-100 text-gray-600">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Point de départ (auto-rempli) -->
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-700">Point de départ</label>
                                <div class="relative">
                                    <input type="text" id="point_depart" name="point_depart" readonly
                                        class="w-full px-4 py-4 border border-gray-200 rounded-xl bg-gray-100 text-gray-600">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                </div>
                                @error('point_depart')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Point d'arrivée (auto-rempli) -->
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-700">Point d'arrivée</label>
                                <div class="relative">
                                    <input type="text" id="point_arrive" name="point_arrive" readonly
                                        class="w-full px-4 py-4 border border-gray-200 rounded-xl bg-gray-100 text-gray-600">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                </div>
                                @error('point_arrive')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Véhicule et Personnel -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-2 h-8 bg-green-500 rounded-full mr-4"></div>
                            <h2 class="text-2xl font-bold text-gray-900">Véhicule et Équipage</h2>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                            <!-- Véhicule -->
                            <div class="space-y-2">
                                <label class="flex items-center text-sm font-semibold text-gray-700">
                                    <span>Véhicule</span>
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="relative">
                                    <select name="vehicule_id" id="vehicule_id" required
                                        class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white appearance-none">
                                        <option value="">Sélectionnez un véhicule</option>
                                        @foreach ($vehicules as $vehicule)
                                            <option value="{{ $vehicule->id }}">
                                                {{ $vehicule->marque }} {{ $vehicule->modele }} -
                                                {{ $vehicule->immatriculation }} ({{ $vehicule->nombre_place }} places)
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                @error('vehicule_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Chauffeur -->
                            <div class="space-y-2">
                                <label class="flex items-center text-sm font-semibold text-gray-700">
                                    <span>Chauffeur</span>
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="relative">
                                    <select name="personnel_id" id="personnel_id" required
                                        class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white appearance-none">
                                        <option value="">Sélectionnez un chauffeur</option>
                                        @foreach ($chauffeurs as $chauffeur)
                                            <option value="{{ $chauffeur->id }}">
                                                {{ $chauffeur->prenom }} {{ $chauffeur->name }} -
                                                {{ $chauffeur->contact }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                </div>
                                @error('personnel_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Convoyeur (optionnel) -->
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-700">Convoyeur (optionnel)</label>
                                <div class="relative">
                                    <select name="convoyeur_id" id="convoyeur_id"
                                        class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white appearance-none">
                                        <option value="">Sélectionnez un convoyeur (optionnel)</option>
                                        @foreach ($convoyeurs as $convoyeur)
                                            <option value="{{ $convoyeur->id }}">
                                                {{ $convoyeur->prenom }} {{ $convoyeur->name }} -
                                                {{ $convoyeur->contact }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                        </svg>
                                    </div>
                                </div>
                                @error('convoyeur_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Montant du billet -->
                            <div class="space-y-2">
                                <label class="flex items-center text-sm font-semibold text-gray-700">
                                    <span>Montant du billet (FCFA)</span>
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" name="montant_billet" id="montant_billet"
                                        value="{{ old('montant_billet') }}" required min="0" step="100"
                                        placeholder="Ex: 5000"
                                        class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                </div>
                                @error('montant_billet')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">Prix par passager en francs CFA</p>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Date et Heure -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-2 h-8 bg-purple-500 rounded-full mr-4"></div>
                            <h2 class="text-2xl font-bold text-gray-900">Horaires</h2>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Date de départ -->
                            <div class="space-y-2">
                                <label class="flex items-center text-sm font-semibold text-gray-700">
                                    <span>Date de départ</span>
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="relative">
                                    <input type="date" name="date_depart" id="date_depart"
                                        value="{{ old('date_depart') }}" required min="{{ date('Y-m-d') }}"
                                        class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white">
                                </div>
                                @error('date_depart')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Heure de départ -->
                            <div class="space-y-2">
                                <label class="flex items-center text-sm font-semibold text-gray-700">
                                    <span>Heure de départ</span>
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="relative">
                                    <input type="time" name="heure_depart" id="heure_depart"
                                        value="{{ old('heure_depart') }}" required
                                        class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white">

                                </div>
                                @error('heure_depart')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Heure d'arrivée (calculée automatiquement) -->
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-700">Heure d'arrivée (calculée)</label>
                                <div class="relative">
                                    <input type="time" name="heure_arrive" id="heure_arrive" readonly
                                        class="w-full px-4 py-4 border border-gray-200 rounded-xl bg-gray-100 text-gray-600">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                @error('heure_arrive')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Type de programmation -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-2 h-8 bg-blue-500 rounded-full mr-4"></div>
                            <h2 class="text-2xl font-bold text-gray-900">Type de programmation</h2>
                        </div>

                        <div class="space-y-6">
                            <!-- Sélection du type -->
                            <div class="space-y-2">
                                <label class="flex items-center text-sm font-semibold text-gray-700">
                                    <span>Type de programmation</span>
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Option Ponctuel -->
                                    <div class="relative">
                                        <input type="radio" name="type_programmation" id="type_ponctuel"
                                            value="ponctuel" checked class="sr-only peer">
                                        <label for="type_ponctuel"
                                            class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#e94f1b] peer-checked:border-[#e94f1b] peer-checked:bg-orange-50 transition-all duration-200">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-6 h-6 border-2 border-gray-300 rounded-full mr-3 peer-checked:border-[#e94f1b] peer-checked:bg-[#e94f1b] flex items-center justify-center">
                                                    <div class="w-3 h-3 bg-white rounded-full peer-checked:block hidden">
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="font-semibold text-gray-900">Programme ponctuel</span>
                                                    <p class="text-sm text-gray-600">Un seul trajet à la date spécifiée</p>
                                                </div>
                                            </div>
                                            <svg class="w-6 h-6 text-[#e94f1b] hidden peer-checked:block" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </label>
                                    </div>

                                    <!-- Option Récurrent -->
                                    <div class="relative">
                                        <input type="radio" name="type_programmation" id="type_recurrent"
                                            value="recurrent" class="sr-only peer">
                                        <label for="type_recurrent"
                                            class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#e94f1b] peer-checked:border-[#e94f1b] peer-checked:bg-orange-50 transition-all duration-200">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-6 h-6 border-2 border-gray-300 rounded-full mr-3 peer-checked:border-[#e94f1b] peer-checked:bg-[#e94f1b] flex items-center justify-center">
                                                    <div class="w-3 h-3 bg-white rounded-full peer-checked:block hidden">
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="font-semibold text-gray-900">Programme récurrent</span>
                                                    <p class="text-sm text-gray-600">Trajet répété sur une période</p>
                                                </div>
                                            </div>
                                            <svg class="w-6 h-6 text-[#e94f1b] hidden peer-checked:block" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Option Aller-Retour -->
                            <div class="space-y-4">
                                <label class="flex items-center text-sm font-semibold text-gray-700">
                                    <span>Options additionnelles</span>
                                </label>
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-exchange-alt text-[#e94f1b]"></i>
                                            </div>
                                            <div>
                                                <span class="font-semibold text-gray-900">Aller-Retour</span>
                                                <p class="text-xs text-gray-500">Ce programme est un voyage aller-retour</p>
                                            </div>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="is_aller_retour" value="1" class="sr-only peer">
                                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-[#e94f1b]"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Configuration Retour (cachée par défaut) -->
                            <div id="retour_config_fields" class="hidden space-y-4 mt-4">
                                <div class="bg-green-50 p-6 rounded-xl border-2 border-green-200">
                                    <div class="flex items-center mb-4">
                                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-undo text-white"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-gray-900">Configuration du Programme Retour</h3>
                                            <p class="text-sm text-gray-600">Itinéraire inversé: <span id="retour_itineraire_display" class="font-semibold text-green-700"></span></p>
                                        </div>
                                    </div>

                                    <!-- Section Retour PONCTUEL -->
                                    <div id="retour_ponctuel_section" class="space-y-4">
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                            <!-- Date de retour (Cachée car identique à l'aller) -->
                                            <div class="hidden">
                                                <input type="date" name="retour_date" id="retour_date" class="w-full">
                                            </div>
                                            
                                            <!-- Message informatif -->
                                            <div class="col-span-2 bg-blue-50 border border-blue-200 rounded-xl p-3 flex items-center gap-3">
                                                <i class="fas fa-info-circle text-blue-500"></i>
                                                <p class="text-sm text-blue-700">
                                                    Pour un aller-retour ponctuel, le retour se fait le même jour que le départ (<span id="ponctuel_retour_date_display" class="font-bold">--</span>).
                                                </p>
                                            </div>

                                            <!-- Heure de départ retour -->
                                            <div class="space-y-2">
                                                <label class="flex items-center text-sm font-semibold text-gray-700">
                                                    <span>Heure départ retour</span>
                                                    <span class="text-red-500 ml-1">*</span>
                                                </label>
                                                <input type="time" name="retour_heure_depart" id="retour_heure_depart"
                                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 bg-white">
                                            </div>

                                            <!-- Heure d'arrivée retour (calculée) -->
                                            <div class="space-y-2">
                                                <label class="text-sm font-semibold text-gray-700">Heure arrivée retour (calculée)</label>
                                                <input type="time" name="retour_heure_arrive" id="retour_heure_arrive" readonly
                                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-100 text-gray-600">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Section Retour RÉCURRENT -->
                                    <div id="retour_recurrent_section" class="hidden space-y-4">
                                        
                                        <!-- CHAMP CACHÉ pour la date de début calculée automatiquement -->
                                        <input type="hidden" name="retour_date_debut_recurrent" id="retour_date_debut_recurrent">

                                        <div class="space-y-2">
                                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                                <span>Jours de retour</span>
                                                <span class="text-red-500 ml-1">*</span>
                                            </label>
                                            <p class="text-xs text-gray-500 mb-2">
                                                Les dates sont calculées automatiquement en fonction de votre date de départ (<span id="ref_date_display" class="font-bold text-gray-800">--</span>).
                                                Décochez les jours non souhaités :
                                            </p>
                                            
                                            <!-- Conteneur pour les checkboxes dynamiques -->
                                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3" id="jours_retour_container">
                                                <span class="text-sm text-gray-400 italic" id="aucun_jour_retour_msg">
                                                    Sélectionnez d'abord les jours dans "Jours de la semaine" ci-dessous
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Heure unique pour tous les retours -->
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
                                            <div class="space-y-2">
                                                <label class="flex items-center text-sm font-semibold text-gray-700">
                                                    <span>Heure de départ retour</span>
                                                    <span class="text-red-500 ml-1">*</span>
                                                </label>
                                                <input type="time" name="retour_heure_depart_recurrent" id="retour_heure_depart_recurrent"
                                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 bg-white">
                                            </div>
                                            <div class="space-y-2">
                                                <label class="text-sm font-semibold text-gray-700">Heure d'arrivée retour (calculée)</label>
                                                <input type="time" name="retour_heure_arrive_recurrent" id="retour_heure_arrive_recurrent" readonly
                                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-100 text-gray-600">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Info récapitulative -->
                                    <div class="bg-green-100 border border-green-300 rounded-lg p-3 mt-4">
                                        <p class="text-sm text-green-800">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            <strong>Un programme retour sera automatiquement créé</strong> avec l'itinéraire inversé et les horaires spécifiés ci-dessus.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Section pour les programmations récurrentes (cachée par défaut) -->
                            <div id="recurrent_fields" class="hidden space-y-6">
                                <!-- Date de fin -->
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="flex items-center text-sm font-semibold text-gray-700">
                                            <span>Date de fin de programmation</span>
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="date" name="date_fin_programmation"
                                                id="date_fin_programmation" min="{{ date('Y-m-d') }}"
                                                class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white">
                                        </div>
                                    </div>

                                    <!-- Jours de la semaine -->
                                    <div class="space-y-2">
                                        <label class="flex items-center text-sm font-semibold text-gray-700">
                                            <span>Jours de la semaine</span>
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <div class="flex flex-wrap gap-2">
                                            <div class="relative">
                                                <input type="checkbox" name="jours_recurrence[]" id="jour_lundi"
                                                    value="lundi" class="sr-only peer">
                                                <label for="jour_lundi"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg cursor-pointer hover:border-[#e94f1b] peer-checked:border-[#e94f1b] peer-checked:bg-[#e94f1b] peer-checked:text-white transition-all duration-200">
                                                    <span class="text-sm font-medium">Lun</span>
                                                </label>
                                            </div>
                                            <div class="relative">
                                                <input type="checkbox" name="jours_recurrence[]" id="jour_mardi"
                                                    value="mardi" class="sr-only peer">
                                                <label for="jour_mardi"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg cursor-pointer hover:border-[#e94f1b] peer-checked:border-[#e94f1b] peer-checked:bg-[#e94f1b] peer-checked:text-white transition-all duration-200">
                                                    <span class="text-sm font-medium">Mar</span>
                                                </label>
                                            </div>
                                            <div class="relative">
                                                <input type="checkbox" name="jours_recurrence[]" id="jour_mercredi"
                                                    value="mercredi" class="sr-only peer">
                                                <label for="jour_mercredi"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg cursor-pointer hover:border-[#e94f1b] peer-checked:border-[#e94f1b] peer-checked:bg-[#e94f1b] peer-checked:text-white transition-all duration-200">
                                                    <span class="text-sm font-medium">Mer</span>
                                                </label>
                                            </div>
                                            <div class="relative">
                                                <input type="checkbox" name="jours_recurrence[]" id="jour_jeudi"
                                                    value="jeudi" class="sr-only peer">
                                                <label for="jour_jeudi"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg cursor-pointer hover:border-[#e94f1b] peer-checked:border-[#e94f1b] peer-checked:bg-[#e94f1b] peer-checked:text-white transition-all duration-200">
                                                    <span class="text-sm font-medium">Jeu</span>
                                                </label>
                                            </div>
                                            <div class="relative">
                                                <input type="checkbox" name="jours_recurrence[]" id="jour_vendredi"
                                                    value="vendredi" class="sr-only peer">
                                                <label for="jour_vendredi"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg cursor-pointer hover:border-[#e94f1b] peer-checked:border-[#e94f1b] peer-checked:bg-[#e94f1b] peer-checked:text-white transition-all duration-200">
                                                    <span class="text-sm font-medium">Ven</span>
                                                </label>
                                            </div>
                                            <div class="relative">
                                                <input type="checkbox" name="jours_recurrence[]" id="jour_samedi"
                                                    value="samedi" class="sr-only peer">
                                                <label for="jour_samedi"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg cursor-pointer hover:border-[#e94f1b] peer-checked:border-[#e94f1b] peer-checked:bg-[#e94f1b] peer-checked:text-white transition-all duration-200">
                                                    <span class="text-sm font-medium">Sam</span>
                                                </label>
                                            </div>
                                            <div class="relative">
                                                <input type="checkbox" name="jours_recurrence[]" id="jour_dimanche"
                                                    value="dimanche" class="sr-only peer">
                                                <label for="jour_dimanche"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg cursor-pointer hover:border-[#e94f1b] peer-checked:border-[#e94f1b] peer-checked:bg-[#e94f1b] peer-checked:text-white transition-all duration-200">
                                                    <span class="text-sm font-medium">Dim</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informations sur la récurrence -->
                                <div id="info_recurrent" class="hidden bg-blue-50 border border-blue-200 rounded-xl p-4">
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div>
                                            <p class="text-sm text-blue-800">
                                                <strong>Programmation récurrente :</strong> Ce programme sera disponible
                                                tous les jours sélectionnés,
                                                à la même heure, du <span id="date_debut_text"></span> au <span
                                                    id="date_fin_text"></span>.
                                                Les passagers pourront réserver pour n'importe quelle date dans cet
                                                intervalle.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div
                        class="flex flex-col sm:flex-row gap-4 justify-between items-center pt-8 border-t border-gray-200">
                        <a href="{{ route('programme.index') }}"
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
                                Créer le programme
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- 1. Gestion Itinéraire & Durée ---
            const itineraireSelect = document.getElementById('itineraire_id');
            const pointDepartInput = document.getElementById('point_depart');
            const pointArriveInput = document.getElementById('point_arrive');
            const durerParcoursInput = document.getElementById('durer_parcours');
            const retourItineraireDisplay = document.getElementById('retour_itineraire_display');

            itineraireSelect.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                if (option.value) {
                    pointDepartInput.value = option.dataset.pointDepart;
                    pointArriveInput.value = option.dataset.pointArrive;
                    durerParcoursInput.value = option.dataset.durer;
                    if(retourItineraireDisplay) {
                         retourItineraireDisplay.textContent = option.dataset.pointArrive + ' → ' + option.dataset.pointDepart;
                    }
                    calculateArrivalTimes();
                } else {
                    pointDepartInput.value = '';
                    pointArriveInput.value = '';
                    durerParcoursInput.value = '';
                }
            });

            // --- 2. Calcul des Heures d'arrivée ---
            const heureDepartInput = document.getElementById('heure_depart');
            
            function calculateArrivalTimes() {
                const duree = durerParcoursInput.value;
                if(!duree) return;

                // Calcul Aller
                if(heureDepartInput.value) {
                    document.getElementById('heure_arrive').value = addDurationToTime(heureDepartInput.value, duree);
                }

                // Calcul Retour Ponctuel
                const retourHeurePonctuel = document.getElementById('retour_heure_depart');
                if(retourHeurePonctuel && retourHeurePonctuel.value) {
                    document.getElementById('retour_heure_arrive').value = addDurationToTime(retourHeurePonctuel.value, duree);
                }

                // Calcul Retour Récurrent
                const retourHeureRecurrent = document.getElementById('retour_heure_depart_recurrent');
                if(retourHeureRecurrent && retourHeureRecurrent.value) {
                    document.getElementById('retour_heure_arrive_recurrent').value = addDurationToTime(retourHeureRecurrent.value, duree);
                }
            }

            function addDurationToTime(startTime, durationStr) {
                // Parsing duration "X heures Y minutes" or "Xh Ymin"
                let hours = 0, minutes = 0;
                const hMatch = durationStr.match(/(\d+)\s*h/i) || durationStr.match(/(\d+)\s*heure/i);
                const mMatch = durationStr.match(/(\d+)\s*m/i) || durationStr.match(/(\d+)\s*minute/i);
                
                if(hMatch) hours = parseInt(hMatch[1]);
                if(mMatch) minutes = parseInt(mMatch[1]);

                const [startH, startM] = startTime.split(':').map(Number);
                const date = new Date();
                date.setHours(startH, startM);
                date.setHours(date.getHours() + hours);
                date.setMinutes(date.getMinutes() + minutes);

                return date.toTimeString().slice(0, 5);
            }

            // Listeners pour calcul heure
            heureDepartInput.addEventListener('change', calculateArrivalTimes);
            const retourHeureDepart = document.getElementById('retour_heure_depart');
            if(retourHeureDepart) retourHeureDepart.addEventListener('change', calculateArrivalTimes);
            const retourHeureDepartRecurrent = document.getElementById('retour_heure_depart_recurrent');
            if(retourHeureDepartRecurrent) retourHeureDepartRecurrent.addEventListener('change', calculateArrivalTimes);


            // --- 3. Gestion Types Programmation & Aller-Retour ---
            const radioPonctuel = document.getElementById('type_ponctuel');
            const radioRecurrent = document.getElementById('type_recurrent');
            const checkboxAR = document.querySelector('input[name="is_aller_retour"]');
            
            const sectionRecurrent = document.getElementById('recurrent_fields');
            const infoRecurrent = document.getElementById('info_recurrent');
            const sectionRetourConfig = document.getElementById('retour_config_fields');
            const sectionRetourPonctuel = document.getElementById('retour_ponctuel_section');
            const sectionRetourRecurrent = document.getElementById('retour_recurrent_section');
            const dateFinProgrammation = document.getElementById('date_fin_programmation');

            function updateUI() {
                // Affichage champs récurrents
                if(radioRecurrent.checked) {
                    sectionRecurrent.classList.remove('hidden');
                    if(infoRecurrent) infoRecurrent.classList.remove('hidden');
                    dateFinProgrammation.required = true;
                } else {
                    sectionRecurrent.classList.add('hidden');
                    if(infoRecurrent) infoRecurrent.classList.add('hidden');
                    dateFinProgrammation.required = false;
                }

                // Affichage config Retour
                if(checkboxAR.checked) {
                    sectionRetourConfig.classList.remove('hidden');
                    
                    if(radioPonctuel.checked) {
                        sectionRetourPonctuel.classList.remove('hidden');
                        sectionRetourRecurrent.classList.add('hidden');
                        
                        // Set required
                        document.getElementById('retour_heure_depart').required = true;
                        if(document.getElementById('retour_heure_depart_recurrent'))
                            document.getElementById('retour_heure_depart_recurrent').required = false;
                        
                        // Update display date retour (même jour)
                        const dateDep = document.getElementById('date_depart').value;
                        if(dateDep) {
                            const d = new Date(dateDep);
                            const displayEl = document.getElementById('ponctuel_retour_date_display');
                            if(displayEl) displayEl.textContent = d.toLocaleDateString('fr-FR');
                            
                            // Set hidden input value
                            const hiddenDate = document.getElementById('retour_date');
                            if(hiddenDate) hiddenDate.value = dateDep;
                        }

                    } else {
                        sectionRetourPonctuel.classList.add('hidden');
                        sectionRetourRecurrent.classList.remove('hidden');

                        // Set required
                        document.getElementById('retour_heure_depart').required = false;
                        if(document.getElementById('retour_heure_depart_recurrent'))
                            document.getElementById('retour_heure_depart_recurrent').required = true;

                        // Générer les checkboxes de retour
                        generateRetourCheckboxes();
                    }
                } else {
                    sectionRetourConfig.classList.add('hidden');
                    // Remove required
                    document.getElementById('retour_heure_depart').required = false;
                    if(document.getElementById('retour_heure_depart_recurrent'))
                        document.getElementById('retour_heure_depart_recurrent').required = false;
                }
            }

            // Listeners UI
            radioPonctuel.addEventListener('change', updateUI);
            radioRecurrent.addEventListener('change', updateUI);
            checkboxAR.addEventListener('change', updateUI);
            document.getElementById('date_depart').addEventListener('change', updateUI);

            // --- 4. Logique Jours Retour (Auto-calcul date) ---
            const mapJours = { 'lundi':1, 'mardi':2, 'mercredi':3, 'jeudi':4, 'vendredi':5, 'samedi':6, 'dimanche':0 };

            function generateRetourCheckboxes() {
                const container = document.getElementById('jours_retour_container');
                const checkboxesAller = document.querySelectorAll('input[name="jours_recurrence[]"]:checked');
                const dateDepartStr = document.getElementById('date_depart').value;

                container.innerHTML = '';
                
                if(!dateDepartStr || checkboxesAller.length === 0) {
                    container.innerHTML = '<span class="text-sm text-gray-500 italic col-span-full">Veuillez sélectionner une date de départ et des jours de récurrence ci-dessus.</span>';
                    return;
                }

                // Date de référence (Date de début du programme aller)
                const dateRef = new Date(dateDepartStr);
                const dayRef = dateRef.getDay(); // 0-6

                // Calcul date min pour le champ hidden
                let minDateRetour = null;

                checkboxesAller.forEach(cb => {
                    const jourNom = cb.value; // 'lundi', etc.
                    const jourIndex = mapJours[jourNom];
                    
                    // Calcul du décalage pour trouver la prochaine occurrence de ce jour
                    let diff = jourIndex - dayRef;
                    if(diff < 0) diff += 7; // Prochaine occurrence
                    // Si diff == 0 (même jour), on ajoute 7 jours pour que le retour soit la semaine suivante ?
                    // NON, généralement un retour peut être le même jour. Laissons diff = 0.
                    
                    // On ajoute ce nombre de jours à la date de départ
                    const dateRetour = new Date(dateRef);
                    dateRetour.setDate(dateRef.getDate() + diff);
                    
                    const dateRetourStr = dateRetour.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long' });
                    const dateRetourISO = dateRetour.toISOString().split('T')[0];

                    // Mise à jour date min
                    if(!minDateRetour || dateRetourISO < minDateRetour) {
                        minDateRetour = dateRetourISO;
                    }

                    // Création HTML
                    const div = document.createElement('div');
                    div.className = 'relative';
                    div.innerHTML = `
                        <input type="checkbox" name="jours_retour[]" id="ret_${jourNom}" value="${jourNom}" checked class="peer sr-only" onchange="window.recalcMinDate()">
                        <label for="ret_${jourNom}" class="flex flex-col items-center justify-center p-3 bg-white border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 peer-checked:border-green-500 peer-checked:bg-green-600 peer-checked:text-white transition-all">
                            <span class="font-bold text-gray-900 capitalize" style="color: inherit;">${jourNom}</span>
                            <span class="text-xs text-green-600 mt-1" style="color: inherit;">${dateRetourStr}</span>
                            <input type="hidden" class="date-val" value="${dateRetourISO}">
                        </label>
                    `;
                    container.appendChild(div);
                });

                // Set initial min date
                const dateDebutRetourInput = document.getElementById('retour_date_debut_recurrent');
                if(dateDebutRetourInput) dateDebutRetourInput.value = minDateRetour;
                
                // Mettre à jour la date de référence affichée
                const refDateDisplay = document.getElementById('ref_date_display');
                if(refDateDisplay) refDateDisplay.textContent = new Date(dateDepartStr).toLocaleDateString('fr-FR');
            }

            // Exposer la fonction pour l'onclick
            window.recalcMinDate = function() {
                const checked = document.querySelectorAll('input[name="jours_retour[]"]:checked');
                let min = null;
                checked.forEach(cb => {
                    const val = cb.parentElement.querySelector('.date-val').value;
                    if(!min || val < min) min = val;
                });
                const dateDebutRetourInput = document.getElementById('retour_date_debut_recurrent');
                if(dateDebutRetourInput) dateDebutRetourInput.value = min;
            };

            // Listeners pour regénérer les cases retour quand l'aller change
            document.querySelectorAll('input[name="jours_recurrence[]"]').forEach(cb => {
                cb.addEventListener('change', function() {
                    if(checkboxAR.checked && radioRecurrent.checked) generateRetourCheckboxes();
                    // ✅ Validation nouvelle : vérifier la cohérence date/jours
                    validateDateWithRecurrenceDays();
                });
            });

            // ✅ NOUVELLE FONCTION DE VALIDATION CLIENT
            function validateDateWithRecurrenceDays() {
                // Seulement pour programmes récurrents
                if (!radioRecurrent.checked) return;

                const dateDepartInput = document.getElementById('date_depart');
                const dateDepart = dateDepartInput.value;
                const joursChecked = document.querySelectorAll('input[name="jours_recurrence[]"]:checked');
                
                // Supprimer l'ancien message d'erreur s'il existe
                const existingError = dateDepartInput.parentElement.parentElement.querySelector('.validation-error-date');
                if (existingError) existingError.remove();
                
                if (!dateDepart || joursChecked.length === 0) return;
                
                // Mapper les jours en français aux jours de la semaine
                const joursFrancais = {
                    'monday': 'lundi',
                    'tuesday': 'mardi',
                    'wednesday': 'mercredi',
                    'thursday': 'jeudi',
                    'friday': 'vendredi',
                    'saturday': 'samedi',
                    'sunday': 'dimanche'
                };
                
                // Obtenir le jour de la semaine de la date sélectionnée
                const date = new Date(dateDepart + 'T00:00:00');
                const jourAnglais = date.toLocaleString('en-US', { weekday: 'long' }).toLowerCase();
                const jourFrancais = joursFrancais[jourAnglais];
                
                // Vérifier si ce jour est dans les jours sélectionnés
                const joursSelectionnes = Array.from(joursChecked).map(cb => cb.value);
                const isValid = joursSelectionnes.includes(jourFrancais);
                
                if (!isValid) {
                    // Afficher un message d'erreur
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'validation-error-date bg-red-50 border border-red-200 rounded-lg p-3 mt-2';
                    errorDiv.innerHTML = `
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm text-red-700">
                                <strong>Incohérence détectée !</strong> La date sélectionnée (${date.toLocaleDateString('fr-FR')}) tombe un <strong>${jourFrancais}</strong>, 
                                mais ce jour n'est pas dans vos jours de récurrence sélectionnés : <strong>${joursSelectionnes.join(', ')}</strong>.
                                <br><span class="text-xs">Veuillez choisir une date qui correspond à un de vos jours de récurrence.</span>
                            </p>
                        </div>
                    `;
                    dateDepartInput.parentElement.parentElement.appendChild(errorDiv);
                    dateDepartInput.classList.add('border-red-500');
                } else {
                    dateDepartInput.classList.remove('border-red-500');
                }
            }

            // ✅ VALIDATION AU SUBMIT DU FORMULAIRE
            document.querySelector('form').addEventListener('submit', function(e) {
                if (radioRecurrent.checked) {
                    const dateDepartInput = document.getElementById('date_depart');
                    const joursChecked = document.querySelectorAll('input[name="jours_recurrence[]"]:checked');
                    
                    if (dateDepartInput.value && joursChecked.length > 0) {
                        const joursFrancais = {
                            'monday': 'lundi', 'tuesday': 'mardi', 'wednesday': 'mercredi',
                            'thursday': 'jeudi', 'friday': 'vendredi', 'saturday': 'samedi', 'sunday': 'dimanche'
                        };
                        
                        const date = new Date(dateDepartInput.value + 'T00:00:00');
               const jourAnglais = date.toLocaleString('en-US', { weekday: 'long' }).toLowerCase();
                        const jourFrancais = joursFrancais[jourAnglais];
                        const joursSelectionnes = Array.from(joursChecked).map(cb => cb.value);
                        
                        if (!joursSelectionnes.includes(jourFrancais)) {
                            e.preventDefault();
                            alert(`❌ La date de départ tombe un ${jourFrancais}, mais ce jour n'est pas dans vos jours de récurrence sélectionnés (${joursSelectionnes.join(', ')}). Veuillez corriger avant de soumettre.`);
                            dateDepartInput.focus();
                            return false;
                        }
                    }
                }
            });

            // ✅ Ajouter listeners pour validation en temps réel
            const elementDateDepart = document.getElementById('date_depart');
            if (elementDateDepart) {
                elementDateDepart.addEventListener('change', validateDateWithRecurrenceDays);
            }
            if (radioRecurrent) {
                radioRecurrent.addEventListener('change', validateDateWithRecurrenceDays);
            }

            // Init
            updateUI();
        });
    </script>
    <style>
        input:focus,
        select:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(233, 78, 26, 0.15);
        }

        /* Style pour les champs en lecture seule */
        input[readonly] {
            background-color: #f9fafb;
            color: #6b7280;
            cursor: not-allowed;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .grid.grid-cols-1 {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection