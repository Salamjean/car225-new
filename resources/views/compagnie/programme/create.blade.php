@extends('compagnie.layouts.template')
@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
        <div class="mx-auto" style="width: 90%">
            <!-- En-tête -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-[#fea219] rounded-2xl shadow-lg mb-4">
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
                            <div class="w-2 h-8 bg-[#fea219] rounded-full mr-4"></div>
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
                                        class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#fea219] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white appearance-none">
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
                                        class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#fea219] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white appearance-none">
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
                                        class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#fea219] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white appearance-none">
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
                                        class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#fea219] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white appearance-none">
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
                                        class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#fea219] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white">
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
                                        class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#fea219] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white">
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
                                        class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#fea219] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white">

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
                                            class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#fea219] peer-checked:border-[#fea219] peer-checked:bg-orange-50 transition-all duration-200">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-6 h-6 border-2 border-gray-300 rounded-full mr-3 peer-checked:border-[#fea219] peer-checked:bg-[#fea219] flex items-center justify-center">
                                                    <div class="w-3 h-3 bg-white rounded-full peer-checked:block hidden">
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="font-semibold text-gray-900">Programme ponctuel</span>
                                                    <p class="text-sm text-gray-600">Un seul trajet à la date spécifiée</p>
                                                </div>
                                            </div>
                                            <svg class="w-6 h-6 text-[#fea219] hidden peer-checked:block" fill="none"
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
                                            class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#fea219] peer-checked:border-[#fea219] peer-checked:bg-orange-50 transition-all duration-200">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-6 h-6 border-2 border-gray-300 rounded-full mr-3 peer-checked:border-[#fea219] peer-checked:bg-[#fea219] flex items-center justify-center">
                                                    <div class="w-3 h-3 bg-white rounded-full peer-checked:block hidden">
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="font-semibold text-gray-900">Programme récurrent</span>
                                                    <p class="text-sm text-gray-600">Trajet répété sur une période</p>
                                                </div>
                                            </div>
                                            <svg class="w-6 h-6 text-[#fea219] hidden peer-checked:block" fill="none"
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
                                                <i class="fas fa-exchange-alt text-[#fea219]"></i>
                                            </div>
                                            <div>
                                                <span class="font-semibold text-gray-900">Aller-Retour</span>
                                                <p class="text-xs text-gray-500">Ce programme est un voyage aller-retour</p>
                                            </div>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="is_aller_retour" value="1" class="sr-only peer">
                                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-[#fea219]"></div>
                                        </label>
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
                                                class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#fea219] focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white">
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
                                                    class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg cursor-pointer hover:border-[#fea219] peer-checked:border-[#fea219] peer-checked:bg-[#fea219] peer-checked:text-white transition-all duration-200">
                                                    <span class="text-sm font-medium">Lun</span>
                                                </label>
                                            </div>
                                            <div class="relative">
                                                <input type="checkbox" name="jours_recurrence[]" id="jour_mardi"
                                                    value="mardi" class="sr-only peer">
                                                <label for="jour_mardi"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg cursor-pointer hover:border-[#fea219] peer-checked:border-[#fea219] peer-checked:bg-[#fea219] peer-checked:text-white transition-all duration-200">
                                                    <span class="text-sm font-medium">Mar</span>
                                                </label>
                                            </div>
                                            <div class="relative">
                                                <input type="checkbox" name="jours_recurrence[]" id="jour_mercredi"
                                                    value="mercredi" class="sr-only peer">
                                                <label for="jour_mercredi"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg cursor-pointer hover:border-[#fea219] peer-checked:border-[#fea219] peer-checked:bg-[#fea219] peer-checked:text-white transition-all duration-200">
                                                    <span class="text-sm font-medium">Mer</span>
                                                </label>
                                            </div>
                                            <div class="relative">
                                                <input type="checkbox" name="jours_recurrence[]" id="jour_jeudi"
                                                    value="jeudi" class="sr-only peer">
                                                <label for="jour_jeudi"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg cursor-pointer hover:border-[#fea219] peer-checked:border-[#fea219] peer-checked:bg-[#fea219] peer-checked:text-white transition-all duration-200">
                                                    <span class="text-sm font-medium">Jeu</span>
                                                </label>
                                            </div>
                                            <div class="relative">
                                                <input type="checkbox" name="jours_recurrence[]" id="jour_vendredi"
                                                    value="vendredi" class="sr-only peer">
                                                <label for="jour_vendredi"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg cursor-pointer hover:border-[#fea219] peer-checked:border-[#fea219] peer-checked:bg-[#fea219] peer-checked:text-white transition-all duration-200">
                                                    <span class="text-sm font-medium">Ven</span>
                                                </label>
                                            </div>
                                            <div class="relative">
                                                <input type="checkbox" name="jours_recurrence[]" id="jour_samedi"
                                                    value="samedi" class="sr-only peer">
                                                <label for="jour_samedi"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg cursor-pointer hover:border-[#fea219] peer-checked:border-[#fea219] peer-checked:bg-[#fea219] peer-checked:text-white transition-all duration-200">
                                                    <span class="text-sm font-medium">Sam</span>
                                                </label>
                                            </div>
                                            <div class="relative">
                                                <input type="checkbox" name="jours_recurrence[]" id="jour_dimanche"
                                                    value="dimanche" class="sr-only peer">
                                                <label for="jour_dimanche"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg cursor-pointer hover:border-[#fea219] peer-checked:border-[#fea219] peer-checked:bg-[#fea219] peer-checked:text-white transition-all duration-200">
                                                    <span class="text-sm font-medium">Dim</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informations sur la récurrence -->
                                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
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
                                class="flex items-center px-8 py-4 bg-[#fea219] text-white font-bold rounded-xl hover:bg-[#e89116] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
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
            const itineraireSelect = document.getElementById('itineraire_id');
            const pointDepartInput = document.getElementById('point_depart');
            const pointArriveInput = document.getElementById('point_arrive');
            const durerParcoursInput = document.getElementById('durer_parcours');
            const heureDepartInput = document.getElementById('heure_depart');
            const heureArriveInput = document.getElementById('heure_arrive');
            const nbreSiegeInput = document.getElementById('nbre_siege_occupe');
            const vehiculeSelect = document.getElementById('vehicule_id');

            // Auto-remplissage des champs lors de la sélection d'un itinéraire
            itineraireSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];

                if (selectedOption.value !== '') {
                    pointDepartInput.value = selectedOption.getAttribute('data-point-depart');
                    pointArriveInput.value = selectedOption.getAttribute('data-point-arrive');
                    durerParcoursInput.value = selectedOption.getAttribute('data-durer');
                } else {
                    pointDepartInput.value = '';
                    pointArriveInput.value = '';
                    durerParcoursInput.value = '';
                }
            });

            // Calcul automatique de l'heure d'arrivée
            function calculerHeureArrivee() {
                const heureDepart = heureDepartInput.value;
                const duree = durerParcoursInput.value;

                if (heureDepart && duree) {
                    // Convertir la durée en minutes
                    const dureeMinutes = convertirDureeEnMinutes(duree);

                    // Calculer l'heure d'arrivée
                    const [heures, minutes] = heureDepart.split(':').map(Number);
                    const dateDepart = new Date();
                    dateDepart.setHours(heures, minutes, 0, 0);

                    const dateArrivee = new Date(dateDepart.getTime() + dureeMinutes * 60000);

                    // Formater l'heure d'arrivée
                    const heuresArrivee = dateArrivee.getHours().toString().padStart(2, '0');
                    const minutesArrivee = dateArrivee.getMinutes().toString().padStart(2, '0');

                    heureArriveInput.value = `${heuresArrivee}:${minutesArrivee}`;
                } else {
                    heureArriveInput.value = '';
                }
            }

            // Fonction pour convertir la durée en minutes
            function convertirDureeEnMinutes(duree) {
                // Supposer que la durée est au format "Xh Ymin" ou "X heures Y minutes"
                const heuresMatch = duree.match(/(\d+)\s*h/);
                const minutesMatch = duree.match(/(\d+)\s*min/);

                let totalMinutes = 0;

                if (heuresMatch) {
                    totalMinutes += parseInt(heuresMatch[1]) * 60;
                }

                if (minutesMatch) {
                    totalMinutes += parseInt(minutesMatch[1]);
                }

                return totalMinutes;
            }

            // Écouter les changements d'heure de départ et de durée
            heureDepartInput.addEventListener('change', calculerHeureArrivee);
            itineraireSelect.addEventListener('change', calculerHeureArrivee);

            // Limiter le nombre de sièges occupés selon la capacité du véhicule
            function mettreAJourLimiteSieges() {
                const selectedVehicule = vehiculeSelect.options[vehiculeSelect.selectedIndex];
                if (selectedVehicule.value !== '') {
                    // Extraire le nombre de places du texte de l'option
                    const texte = selectedVehicule.text;
                    const match = texte.match(/\((\d+)\s+places\)/);
                    if (match) {
                        const nombrePlaces = parseInt(match[1]);
                        nbreSiegeInput.max = nombrePlaces;
                        nbreSiegeInput.placeholder = `Max: ${nombrePlaces} places`;
                    }
                }
            }

            vehiculeSelect.addEventListener('change', mettreAJourLimiteSieges);

            // Validation de la date (ne pas permettre les dates passées)
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('date_depart').min = today;
        });

        // Gestion du type de programmation
        const typePonctuel = document.getElementById('type_ponctuel');
        const typeRecurrent = document.getElementById('type_recurrent');
        const recurrentFields = document.getElementById('recurrent_fields');
        const dateFinProgrammation = document.getElementById('date_fin_programmation');
        const dateDepartInput = document.getElementById('date_depart');
        const dateDebutText = document.getElementById('date_debut_text');
        const dateFinText = document.getElementById('date_fin_text');

        // Afficher/masquer les champs de récurrence
        function toggleRecurrentFields() {
            if (typeRecurrent.checked) {
                recurrentFields.classList.remove('hidden');
                dateFinProgrammation.required = true;
                // Mettre à jour les textes d'information
                updateDateTexts();
            } else {
                recurrentFields.classList.add('hidden');
                dateFinProgrammation.required = false;
            }
        }

        // Mettre à jour les textes de dates
        function updateDateTexts() {
            if (dateDepartInput.value) {
                const dateDebut = new Date(dateDepartInput.value);
                dateDebutText.textContent = dateDebut.toLocaleDateString('fr-FR');
            }

            if (dateFinProgrammation.value) {
                const dateFin = new Date(dateFinProgrammation.value);
                dateFinText.textContent = dateFin.toLocaleDateString('fr-FR');
            }
        }

        // Écouter les changements
        typePonctuel.addEventListener('change', toggleRecurrentFields);
        typeRecurrent.addEventListener('change', toggleRecurrentFields);
        dateDepartInput.addEventListener('change', updateDateTexts);
        dateFinProgrammation.addEventListener('change', updateDateTexts);

        // Initialiser
        toggleRecurrentFields();

        // Validation des dates pour les programmations récurrentes
        dateFinProgrammation.addEventListener('change', function() {
            const dateDebut = new Date(dateDepartInput.value);
            const dateFin = new Date(this.value);

            if (dateFin <= dateDebut) {
                alert('La date de fin doit être postérieure à la date de début');
                this.value = '';
            }
        });

        // Valider qu'au moins un jour est sélectionné pour les récurrentes
        document.querySelector('form').addEventListener('submit', function(e) {
            if (typeRecurrent.checked) {
                const joursSelectionnes = document.querySelectorAll('input[name="jours_recurrence[]"]:checked');

                if (joursSelectionnes.length === 0) {
                    e.preventDefault();
                    alert('Veuillez sélectionner au moins un jour de la semaine pour la programmation récurrente');
                    return false;
                }

                if (!dateFinProgrammation.value) {
                    e.preventDefault();
                    alert('Veuillez spécifier une date de fin pour la programmation récurrente');
                    return false;
                }
            }
        });
    </script>

    <style>
        input:focus,
        select:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(254, 162, 25, 0.15);
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
