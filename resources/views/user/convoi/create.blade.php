@extends('user.layouts.template')

@section('title', 'Demande de Convoi')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Navigation Pills -->
        <div class="inline-flex bg-white border border-gray-100 rounded-2xl p-1 shadow-sm">
            <a href="{{ route('user.convoi.create') }}" class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider bg-[#e94f1b] text-white shadow-sm">
                <i class="fas fa-plus-circle me-1"></i> Nouveau convoi
            </a>
            <a href="{{ route('user.convoi.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider text-gray-600 hover:text-gray-900 transition-colors">
                <i class="fas fa-list-ul me-1"></i> Mes convois
            </a>
        </div>

        <!-- Card Container -->
        <div class="bg-white rounded-[28px] border border-gray-100 shadow-xl p-8 sm:p-10">
            <!-- Header -->
            <div class="mb-8">
                <h3 class="text-[#e94f1b] text-xl font-bold mb-1">Formuler une Demande de Convoi</h3>
                <p class="text-gray-500 text-sm">
                    Renseignez les informations de voyage. Nous transmettrons directement votre demande au prestataire choisi.
                </p>
            </div>

            @if (session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-700 font-semibold text-sm mb-6 flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-500 text-lg flex-shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700 font-semibold text-sm mb-6 flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-red-500 text-lg flex-shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700 text-sm mb-6">
                    <p class="font-black mb-2 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-red-500"></i> Veuillez corriger les erreurs ci-dessous :
                    </p>
                    <ul class="list-disc list-inside space-y-1 text-xs font-semibold">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('user.convoi.store') }}" method="POST" class="space-y-6" id="convoiForm">
                @csrf

                <!-- Type de Prestataire -->
                <div>
                    <label class="form-label-custom">TYPE DE PRESTATAIRE <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Compagnie Radio Card -->
                        <div class="provider-radio-card active" id="card-prov-compagnie" onclick="selectProviderType('compagnie')">
                            <input type="radio" name="type_transporteur" id="prov-compagnie" value="compagnie" checked class="hidden">
                            <div class="flex items-center gap-4">
                                <div class="radio-icon flex-shrink-0">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div>
                                    <p class="title-radio">Compagnie Officielle</p>
                                    <p class="desc-radio">Bus et gares certifiés</p>
                                </div>
                            </div>
                        </div>

                        <!-- Particulier Radio Card -->
                        <div class="provider-radio-card" id="card-prov-particulier" onclick="selectProviderType('particulier')">
                            <input type="radio" name="type_transporteur" id="prov-particulier" value="particulier" class="hidden">
                            <div class="flex items-center gap-4">
                                <div class="radio-icon flex-shrink-0">
                                    <i class="fas fa-user-friends"></i>
                                </div>
                                <div>
                                    <p class="title-radio">Transporteur Particulier</p>
                                    <p class="desc-radio">Particulier agréé CAR225</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Compagnie Select Container -->
                <div id="compagnie-select-group" class="space-y-5">
                    <div>
                        <label class="form-label-custom">COMPAGNIE <span class="text-red-500">*</span></label>
                        
                        <!-- Trigger Button for Modal -->
                        <button type="button" id="btnShowCompagnies" onclick="openCompagnieModal()"
                            class="w-full py-4 px-4 rounded-xl border border-gray-300 hover:border-[#e94f1b] bg-white text-gray-700 font-bold text-sm flex items-center justify-center gap-2 transition-all">
                            <i class="fas fa-building text-[#e94f1b] mr-1"></i> Choisir une compagnie officielle
                        </button>
                        
                        <!-- Selected Badge for Compagnie (Initially Hidden) -->
                        <div id="selectedCompagnieBadge" class="hidden mt-2 p-3.5 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-between">
                            <div class="flex items-center gap-3 text-left">
                                <div class="logo-circle overflow-hidden rounded-full bg-white flex items-center justify-center border border-gray-200 flex-shrink-0" style="width: 44px; height: 44px;">
                                    <img id="selectedCompPhoto" src="" alt="Compagnie" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 text-sm mb-0" id="selectedCompName">Nom Compagnie</p>
                                    <p class="text-xs text-gray-400 font-semibold mb-0" id="selectedCompSigle">Sigle</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="openCompagnieModal()" class="px-3 py-1.5 rounded-lg text-xs font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 transition-all">
                                    Changer
                                </button>
                                <button type="button" class="text-xs font-bold text-red-500 hover:text-red-700 px-2 py-1 transition-all" onclick="clearCompagnieSelection()">
                                    Désélectionner
                                </button>
                            </div>
                        </div>

                        <input type="hidden" name="compagnie_id" id="compagnieSelect" value="{{ old('compagnie_id') }}">
                        @error('compagnie_id')
                            <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Gare Selection -->
                    <div>
                        <label class="form-label-custom">GARE LA PLUS PROCHE <span class="text-red-500">*</span></label>
                        <select id="gareSelect" name="gare_id" class="w-full form-input-premium cursor-pointer" disabled required>
                            <option value="">Choisir d'abord une compagnie</option>
                        </select>
                        <p class="text-xs text-gray-400 font-semibold mt-1.5 flex items-center gap-1">
                            <i class="fas fa-info-circle text-[#e94f1b]"></i> Cette gare recevra et traitera votre demande de convoi.
                        </p>
                        @error('gare_id')
                            <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Itineraire Selection -->
                    <div>
                        <label class="form-label-custom">ITINÉRAIRE <span class="text-gray-400 normal-case font-semibold">(optionnel — pré-remplit les lieux)</span></label>
                        <select id="itineraireSelect" name="itineraire_id" class="w-full form-input-premium cursor-pointer" disabled>
                            <option value="">Choisir d'abord une compagnie</option>
                        </select>
                        @error('itineraire_id')
                            <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Particulier Select Container -->
                <div id="particulier-select-group" class="hidden space-y-3">
                    <div>
                        <label class="form-label-custom">TRANSPORTEUR PARTICULIER (OPTIONNEL)</label>
                        
                        <!-- Trigger Button -->
                        <button type="button" id="btnShowParticuliers" onclick="openParticulierModal()"
                            class="w-full py-4 px-4 rounded-xl border border-gray-300 hover:border-[#e94f1b] bg-white text-gray-700 font-bold text-sm flex items-center justify-center gap-2 transition-all">
                            <i class="fas fa-user-plus text-[#e94f1b] mr-1"></i> Choisir un transporteur particulier
                        </button>
                        
                        <!-- Selected Badge (Initially Hidden) -->
                        <div id="selectedParticulierBadge" class="hidden mt-2 p-3.5 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-between">
                            <div class="flex items-center gap-3 text-left">
                                <div class="logo-circle overflow-hidden rounded-full bg-white flex items-center justify-center border border-gray-200 flex-shrink-0" style="width: 44px; height: 44px;">
                                    <img id="selectedPartPhoto" src="" alt="Chauffeur" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 text-sm mb-0" id="selectedPartName">Nom</p>
                                    <p class="text-xs text-gray-400 font-semibold mb-0" id="selectedPartCarInfo">Capacité & Plaque</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" class="px-3 py-1.5 rounded-lg text-xs font-bold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 transition-all" id="btnDetailsSelectedPart">
                                    <i class="fas fa-eye text-[#e94f1b] mr-1"></i> Détails
                                </button>
                                <button type="button" class="text-xs font-bold text-red-500 hover:text-red-700 px-2 py-1 transition-all" onclick="clearParticulierSelection()">
                                    Désélectionner
                                </button>
                            </div>
                        </div>

                        <p class="text-xs text-gray-400 font-semibold mt-1.5 flex items-center gap-1">
                            <i class="fas fa-info-circle text-[#e94f1b]"></i> Si aucun transporteur n'est sélectionné, votre demande sera transmise à l'ensemble des transporteurs agréés.
                        </p>

                        <input type="hidden" name="particulier_id" id="particulierSelect" value="{{ old('particulier_id') }}">
                        @error('particulier_id')
                            <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Lieux de Départ & Arrivée -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label-custom">LIEU DE DÉPART <span class="text-red-500">*</span></label>
                        <input type="text" id="lieuDepart" name="lieu_depart"
                            value="{{ old('lieu_depart') }}"
                            placeholder="Ex: Abidjan Cocody"
                            required
                            class="w-full form-input-premium">
                        @error('lieu_depart')
                            <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label-custom">LIEU D'ARRIVÉE <span class="text-red-500">*</span></label>
                        <input type="text" id="lieuArrivee" name="lieu_retour"
                            value="{{ old('lieu_retour') }}"
                            placeholder="Ex: Yamoussoukro Fondation"
                            required
                            class="w-full form-input-premium">
                        @error('lieu_retour')
                            <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Durée estimée du trajet Google Maps -->
                <div id="durationSection" class="hidden">
                    <div class="flex items-center gap-3 px-5 py-3.5 bg-blue-50 border border-blue-100 rounded-2xl">
                        <div class="w-8 h-8 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0 text-blue-600">
                            <i class="fas fa-clock text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-wider text-blue-500">Durée estimée du trajet</p>
                            <p id="durationText" class="text-sm font-black text-blue-800">—</p>
                        </div>
                        <div class="ml-auto flex items-center gap-1.5 text-blue-600 font-bold text-xs">
                            <i class="fas fa-road text-blue-400"></i>
                            <span id="distanceText">—</span>
                        </div>
                    </div>
                </div>

                <!-- Dates et Heures (4 colonnes en ligne) -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="form-label-custom">DATE ALLER <span class="text-red-500">*</span></label>
                        <input type="date" name="date_depart" id="date_depart"
                            value="{{ old('date_depart') }}"
                            min="{{ date('Y-m-d') }}"
                            required
                            class="w-full form-input-premium">
                        @error('date_depart')
                            <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label-custom">HEURE ALLER <span class="text-red-500">*</span></label>
                        <input type="time" name="heure_depart" id="heure_depart"
                            value="{{ old('heure_depart') }}"
                            required
                            class="w-full form-input-premium">
                        @error('heure_depart')
                            <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label-custom">DATE RETOUR (OPTIONNEL)</label>
                        <input type="date" name="date_retour" id="date_retour"
                            value="{{ old('date_retour') }}"
                            min="{{ date('Y-m-d') }}"
                            class="w-full form-input-premium">
                        @error('date_retour')
                            <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label-custom">HEURE RETOUR (OPTIONNEL)</label>
                        <input type="time" name="heure_retour" id="heure_retour"
                            value="{{ old('heure_retour') }}"
                            class="w-full form-input-premium">
                        @error('heure_retour')
                            <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Nombre de places attendues -->
                <div>
                    <label class="form-label-custom">NOMBRE DE PLACES ATTENDUES <span class="text-red-500">*</span></label>
                    <input type="number" name="nombre_personnes" id="nombre_personnes" min="10" max="1000"
                        value="{{ old('nombre_personnes', 10) }}"
                        required
                        class="w-full form-input-premium">
                    <p class="text-xs text-gray-500 font-semibold mt-2 flex items-center gap-1.5">
                        <i class="fas fa-info-circle text-gray-400"></i> Le minimum requis pour un convoi est de 10 personnes.
                    </p>
                    @error('nombre_personnes')
                        <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-gray-100">
                    <button type="submit"
                        class="btn-orange-premium w-full py-5 rounded-xl text-sm font-black uppercase tracking-widest text-white shadow-lg transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane text-xs"></i>
                        Soumettre ma demande de convoi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════════════ -->
    <!-- MODAL 1 : Sélection Compagnie -->
    <!-- ══════════════════════════════════════════════════════════════════════ -->
    <div id="compagnieSelectModal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeCompagnieModal()"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-orange-50 flex items-center justify-center text-[#e94f1b]">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900">Choisir une compagnie officielle</h3>
                        <p class="text-xs text-gray-500 font-medium">Sélectionnez la compagnie pour lui soumettre votre demande.</p>
                    </div>
                </div>
                <button type="button" onclick="closeCompagnieModal()" class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition-all">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <!-- Modal Search Input -->
            <div class="p-4 border-b border-gray-50 bg-gray-50/50">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" id="searchCompagnieInput" placeholder="Rechercher une compagnie..." oninput="filterCompagnies(this.value)"
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#e94f1b]/30 focus:border-[#e94f1b]">
                </div>
            </div>

            <!-- Modal Body (Cards Grid) -->
            <div class="p-6 overflow-y-auto flex-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="compagnieCardsGroup">
                    @foreach ($compagnies as $compagnie)
                        @php
                            $compLogo = $compagnie->path_logo ? asset('storage/' . $compagnie->path_logo) : asset('assetsPoster/assets/images/logo_car225.png');
                        @endphp
                        <div class="provider-select-card p-4 rounded-2xl border text-center transition-all"
                            id="card-compagnie-{{ $compagnie->id }}"
                            onclick="selectCompagnieCard({{ $compagnie->id }})"
                            data-name="{{ $compagnie->name }}"
                            data-logo="{{ $compLogo }}"
                            data-sigle="{{ $compagnie->sigle ?: 'Compagnie' }}">
                            <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center overflow-hidden">
                                <img src="{{ $compLogo }}" alt="{{ $compagnie->name }}" class="w-full h-full object-cover">
                            </div>
                            <h4 class="font-bold text-gray-900 text-sm truncate mb-0.5">{{ $compagnie->name }}</h4>
                            <p class="text-xs text-gray-400 font-semibold mb-3">{{ $compagnie->sigle ?: 'Compagnie' }}</p>
                            <button type="button" class="btn-orange-select w-full py-2 rounded-xl text-xs font-black uppercase tracking-wider">
                                Sélectionner
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                <button type="button" onclick="closeCompagnieModal()" class="px-5 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-700 text-xs font-bold hover:bg-gray-100 transition-all">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════════════ -->
    <!-- MODAL 2 : Sélection Particulier -->
    <!-- ══════════════════════════════════════════════════════════════════════ -->
    <div id="particulierSelectModal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeParticulierModal()"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-orange-50 flex items-center justify-center text-[#e94f1b]">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900">Choisir un transporteur particulier</h3>
                        <p class="text-xs text-gray-500 font-medium">Particuliers agréés avec cars certifiés CAR225.</p>
                    </div>
                </div>
                <button type="button" onclick="closeParticulierModal()" class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition-all">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <!-- Modal Search Input -->
            <div class="p-4 border-b border-gray-50 bg-gray-50/50">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" id="searchParticulierInput" placeholder="Rechercher un transporteur..." oninput="filterParticuliers(this.value)"
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#e94f1b]/30 focus:border-[#e94f1b]">
                </div>
            </div>

            <!-- Modal Body (Cards Grid) -->
            <div class="p-6 overflow-y-auto flex-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="particulierCardsGroup">
                    @forelse($particuliers as $particulier)
                        @php
                            $partJson = json_encode([
                                'nom'                 => $particulier->full_name,
                                'contact'             => $particulier->contact,
                                'email'               => $particulier->email,
                                'places'              => $particulier->nombre_place_car,
                                'immatriculation'     => $particulier->immatriculation,
                                'date_service'        => $particulier->date_mise_service ? \Carbon\Carbon::parse($particulier->date_mise_service)->format('d/m/Y') : 'N/A',
                                'photo_proprietaire'  => $particulier->photo_proprietaire_url,
                                'photo_complete'      => $particulier->photo_complete_car_url,
                                'photo_avant'         => $particulier->photo_avant_car_url,
                                'photo_arriere'       => $particulier->photo_arriere_car_url,
                            ]);
                        @endphp
                        <div class="provider-select-card p-4 rounded-2xl border text-center transition-all"
                            id="card-particulier-{{ $particulier->id }}"
                            onclick="selectParticulierCard({{ $particulier->id }})"
                            data-name="{{ $particulier->full_name }}"
                            data-photo="{{ $particulier->photo_proprietaire_url }}"
                            data-places="{{ $particulier->nombre_place_car }}"
                            data-immat="{{ $particulier->immatriculation }}"
                            data-json="{{ $partJson }}">
                            <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center overflow-hidden">
                                <img src="{{ $particulier->photo_proprietaire_url }}" alt="{{ $particulier->full_name }}" class="w-full h-full object-cover">
                            </div>
                            <h4 class="font-bold text-gray-900 text-sm truncate mb-0.5">{{ $particulier->full_name }}</h4>
                            <p class="text-xs text-gray-400 font-semibold mb-3">{{ $particulier->nombre_place_car }} places · {{ $particulier->immatriculation }}</p>
                            
                            <div class="flex items-center gap-2">
                                <button type="button" class="btn-orange-select flex-1 py-2 rounded-xl text-xs font-black uppercase tracking-wider">
                                    Sélectionner
                                </button>
                                <button type="button" onclick="event.stopPropagation(); showParticulierDetails({{ $particulier->id }})"
                                    class="w-9 h-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-500 hover:text-gray-900 flex items-center justify-center text-xs transition-all"
                                    title="Voir les photos du car">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-8 text-gray-400 font-medium text-xs">
                            <i class="fas fa-user-slash text-3xl text-gray-300 mb-2"></i>
                            <p>Aucun transporteur particulier agréé disponible pour le moment.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                <button type="button" onclick="closeParticulierModal()" class="px-5 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-700 text-xs font-bold hover:bg-gray-100 transition-all">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════════════ -->
    <!-- MODAL 3 : Détails Particulier & Photos Véhicule -->
    <!-- ══════════════════════════════════════════════════════════════════════ -->
    <div id="particulierDetailsModal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeDetailsModal()"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-gray-900 text-white flex items-center justify-between">
                <h3 class="text-sm font-black tracking-wide flex items-center gap-2">
                    <i class="fas fa-user-circle text-[#e94f1b]"></i> Informations du Transporteur Particulier
                </h3>
                <button type="button" onclick="closeDetailsModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 overflow-y-auto flex-1 bg-[#f8fafc]">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Column 1: Chauffeur Card -->
                    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center">
                        <div class="w-28 h-28 rounded-full overflow-hidden mx-auto mb-3 border-2 border-orange-100 bg-gray-50 flex items-center justify-center shadow-inner">
                            <img id="modalPartPhotoProprietaire" src="" alt="Chauffeur" class="w-full h-full object-cover">
                        </div>
                        <h4 class="font-black text-gray-900 text-base mb-1" id="modalPartName">Nom</h4>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-wider">
                            <i class="fas fa-check-circle"></i> Agréé CAR225
                        </span>
                    </div>

                    <!-- Column 2: Specs & Photos -->
                    <div class="md:col-span-2 space-y-4">
                        <!-- Caractéristiques -->
                        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                            <h5 class="text-[11px] font-black uppercase tracking-wider text-gray-500 mb-3 border-b border-gray-100 pb-2">
                                <i class="fas fa-info-circle text-[#e94f1b] mr-1"></i> Caractéristiques du Véhicule
                            </h5>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                                <div>
                                    <span class="text-gray-400 font-semibold block text-[10px] uppercase">Immatriculation</span>
                                    <strong class="text-gray-900 font-mono text-xs" id="modalPartImmatriculation">--</strong>
                                </div>
                                <div>
                                    <span class="text-gray-400 font-semibold block text-[10px] uppercase">Nombre de places</span>
                                    <strong class="text-gray-900 text-xs" id="modalPartPlaces">--</strong>
                                </div>
                                <div>
                                    <span class="text-gray-400 font-semibold block text-[10px] uppercase">Mise en service</span>
                                    <strong class="text-gray-900 text-xs" id="modalPartDateService">--</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Photos du car -->
                        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                            <h5 class="text-[11px] font-black uppercase tracking-wider text-gray-500 mb-3 border-b border-gray-100 pb-2">
                                <i class="fas fa-images text-[#e94f1b] mr-1"></i> Photos du Véhicule
                            </h5>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <div class="border border-gray-100 rounded-xl overflow-hidden bg-gray-50 h-24 cursor-pointer hover:opacity-90 transition-opacity" onclick="window.open(document.getElementById('modalPartPhotoComplete').src)">
                                        <img id="modalPartPhotoComplete" src="" alt="Vue complète" class="w-full h-full object-cover">
                                    </div>
                                    <span class="text-center text-[10px] font-bold text-gray-400 block mt-1">Vue complète</span>
                                </div>
                                <div>
                                    <div class="border border-gray-100 rounded-xl overflow-hidden bg-gray-50 h-24 cursor-pointer hover:opacity-90 transition-opacity" onclick="window.open(document.getElementById('modalPartPhotoAvant').src)">
                                        <img id="modalPartPhotoAvant" src="" alt="Vue avant" class="w-full h-full object-cover">
                                    </div>
                                    <span class="text-center text-[10px] font-bold text-gray-400 block mt-1">Vue avant</span>
                                </div>
                                <div>
                                    <div class="border border-gray-100 rounded-xl overflow-hidden bg-gray-50 h-24 cursor-pointer hover:opacity-90 transition-opacity" onclick="window.open(document.getElementById('modalPartPhotoArriere').src)">
                                        <img id="modalPartPhotoArriere" src="" alt="Vue arrière" class="w-full h-full object-cover">
                                    </div>
                                    <span class="text-center text-[10px] font-bold text-gray-400 block mt-1">Vue arrière</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-100 bg-white flex justify-end">
                <button type="button" onclick="closeDetailsModal()" class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-700 text-xs font-bold hover:bg-gray-200 transition-all">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .form-label-custom {
            font-size: 11px;
            font-weight: 800;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
            display: block;
        }
        .form-input-premium {
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            padding: 14px 16px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            outline: none;
            transition: all 0.2s ease;
        }
        .form-input-premium:focus {
            border-color: #e94f1b;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(233, 79, 27, 0.15);
        }
        .provider-radio-card {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 20px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .provider-radio-card:hover {
            border-color: rgba(233, 79, 27, 0.3);
            background: #fffdfb;
        }
        .provider-radio-card.active {
            border-color: #e94f1b;
            background: #fffaf7;
            box-shadow: 0 4px 15px rgba(233, 79, 27, 0.05);
        }
        .provider-radio-card.active .radio-icon {
            background: #e94f1b;
            color: #ffffff;
        }
        .radio-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #475569;
            transition: all 0.2s ease;
        }
        .title-radio {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 2px;
        }
        .desc-radio {
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
        }
        .btn-orange-premium {
            background: linear-gradient(135deg, #f97316, #e94f1b);
            color: #ffffff;
            box-shadow: 0 6px 20px rgba(233, 79, 27, 0.25);
        }
        .btn-orange-premium:hover {
            background: linear-gradient(135deg, #ea580c, #d44518);
            box-shadow: 0 8px 25px rgba(233, 79, 27, 0.35);
        }
        .provider-select-card {
            background: #ffffff;
            border: 2px solid #f1f5f9;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .provider-select-card:hover {
            border-color: rgba(233, 79, 27, 0.3);
            background: #fffdfb;
            transform: translateY(-2px);
        }
        .provider-select-card.active {
            border-color: #e94f1b;
            background: #fffaf7;
            box-shadow: 0 8px 25px rgba(233, 79, 27, 0.08);
        }
        .provider-select-card.active .btn-orange-select {
            background: #e94f1b !important;
            color: #ffffff !important;
        }
        .btn-orange-select {
            background: #f1f5f9;
            color: #475569;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-orange-select:hover {
            background: #e94f1b;
            color: #ffffff;
        }
    </style>
    @endpush

    {{-- Google Maps Autocomplete API --}}
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&loading=async&callback=initConvoiMap" async defer></script>

    @push('scripts')
    <script>
        // ── State variables ──────────────────────────────────────────────────
        let autocompleteDepart = null;
        let autocompleteArrive = null;
        let directionsService  = null;
        let mapsReady          = false;
        let itineraireMode     = false;

        const lieuDepart       = document.getElementById('lieuDepart');
        const lieuArrivee      = document.getElementById('lieuArrivee');
        const durationSection  = document.getElementById('durationSection');
        const durationText     = document.getElementById('durationText');
        const distanceText     = document.getElementById('distanceText');
        const compagnieSelect  = document.getElementById('compagnieSelect');
        const gareSelect       = document.getElementById('gareSelect');
        const itineraireSelect = document.getElementById('itineraireSelect');

        // ── Provider type switcher (Compagnie vs Particulier) ────────────────
        function selectProviderType(type) {
            const isComp = (type === 'compagnie');

            document.getElementById('prov-compagnie').checked = isComp;
            document.getElementById('prov-particulier').checked = !isComp;

            document.getElementById('card-prov-compagnie').classList.toggle('active', isComp);
            document.getElementById('card-prov-particulier').classList.toggle('active', !isComp);

            const compGroup = document.getElementById('compagnie-select-group');
            const partGroup = document.getElementById('particulier-select-group');

            if (isComp) {
                compGroup.classList.remove('hidden');
                partGroup.classList.add('hidden');
                gareSelect.required = true;
                compagnieSelect.required = true;
            } else {
                compGroup.classList.add('hidden');
                partGroup.classList.remove('hidden');
                gareSelect.required = false;
                compagnieSelect.required = false;
            }
        }

        // ── Compagnie Modal & Selection ──────────────────────────────────────
        function openCompagnieModal() {
            const m = document.getElementById('compagnieSelectModal');
            if (m) { m.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
        }

        function closeCompagnieModal() {
            const m = document.getElementById('compagnieSelectModal');
            if (m) { m.classList.add('hidden'); document.body.style.overflow = ''; }
        }

        function filterCompagnies(query) {
            const q = (query || '').toLowerCase().trim();
            document.querySelectorAll('#compagnieCardsGroup .provider-select-card').forEach(card => {
                const name = (card.getAttribute('data-name') || '').toLowerCase();
                const sigle = (card.getAttribute('data-sigle') || '').toLowerCase();
                const visible = name.includes(q) || sigle.includes(q);
                card.style.display = visible ? '' : 'none';
            });
        }

        // Select compagnie
        function selectCompagnieCard(id) {
            document.querySelectorAll('#compagnieCardsGroup .provider-select-card').forEach(c => c.classList.remove('active'));

            const card = document.getElementById('card-compagnie-' + id);
            if (card) {
                card.classList.add('active');
                const name  = card.getAttribute('data-name');
                const logo  = card.getAttribute('data-logo');
                const sigle = card.getAttribute('data-sigle');

                document.getElementById('selectedCompPhoto').src = logo;
                document.getElementById('selectedCompName').textContent = name;
                document.getElementById('selectedCompSigle').textContent = sigle;

                document.getElementById('selectedCompagnieBadge').classList.remove('hidden');
                document.getElementById('btnShowCompagnies').classList.add('hidden');
            }

            if (compagnieSelect) {
                compagnieSelect.value = id;
                loadGaresAndItineraires(id);
            }

            closeCompagnieModal();
        }

        function clearCompagnieSelection() {
            document.querySelectorAll('#compagnieCardsGroup .provider-select-card').forEach(c => c.classList.remove('active'));

            if (compagnieSelect) {
                compagnieSelect.value = '';
            }

            document.getElementById('selectedCompagnieBadge').classList.add('hidden');
            document.getElementById('btnShowCompagnies').classList.remove('hidden');

            gareSelect.innerHTML = '<option value="">Choisir d\'abord une compagnie</option>';
            gareSelect.disabled = true;

            itineraireSelect.innerHTML = '<option value="">Choisir d\'abord une compagnie</option>';
            itineraireSelect.disabled = true;

            setManualMode();
        }

        // ── Particulier Modal & Selection ────────────────────────────────────
        function openParticulierModal() {
            const m = document.getElementById('particulierSelectModal');
            if (m) { m.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
        }

        function closeParticulierModal() {
            const m = document.getElementById('particulierSelectModal');
            if (m) { m.classList.add('hidden'); document.body.style.overflow = ''; }
        }

        function filterParticuliers(query) {
            const q = (query || '').toLowerCase().trim();
            document.querySelectorAll('#particulierCardsGroup .provider-select-card').forEach(card => {
                const name  = (card.getAttribute('data-name') || '').toLowerCase();
                const immat = (card.getAttribute('data-immat') || '').toLowerCase();
                const visible = name.includes(q) || immat.includes(q);
                card.style.display = visible ? '' : 'none';
            });
        }

        function selectParticulierCard(id) {
            document.querySelectorAll('#particulierCardsGroup .provider-select-card').forEach(c => c.classList.remove('active'));

            const card = document.getElementById('card-particulier-' + id);
            if (card) {
                card.classList.add('active');
                const name   = card.getAttribute('data-name');
                const photo  = card.getAttribute('data-photo');
                const places = card.getAttribute('data-places');
                const immat  = card.getAttribute('data-immat');

                document.getElementById('selectedPartPhoto').src = photo;
                document.getElementById('selectedPartName').textContent = name;
                document.getElementById('selectedPartCarInfo').textContent = places + ' places · ' + immat;

                document.getElementById('btnDetailsSelectedPart').onclick = function() {
                    showParticulierDetails(id);
                };

                document.getElementById('selectedParticulierBadge').classList.remove('hidden');
                document.getElementById('btnShowParticuliers').classList.add('hidden');
            }

            const partSelect = document.getElementById('particulierSelect');
            if (partSelect) partSelect.value = id;

            closeParticulierModal();
        }

        function clearParticulierSelection() {
            document.querySelectorAll('#particulierCardsGroup .provider-select-card').forEach(c => c.classList.remove('active'));

            const partSelect = document.getElementById('particulierSelect');
            if (partSelect) partSelect.value = '';

            document.getElementById('selectedParticulierBadge').classList.add('hidden');
            document.getElementById('btnShowParticuliers').classList.remove('hidden');
        }

        // ── Particulier Details Modal ────────────────────────────────────────
        function showParticulierDetails(id) {
            const card = document.getElementById('card-particulier-' + id);
            if (!card) return;

            const data = JSON.parse(card.getAttribute('data-json') || '{}');
            document.getElementById('modalPartPhotoProprietaire').src = data.photo_proprietaire || '';
            document.getElementById('modalPartName').textContent       = data.nom || 'N/A';
            document.getElementById('modalPartImmatriculation').textContent = data.immatriculation || 'N/A';
            document.getElementById('modalPartPlaces').textContent     = (data.places || '--') + ' places';
            document.getElementById('modalPartDateService').textContent = data.date_service || 'N/A';
            document.getElementById('modalPartPhotoComplete').src     = data.photo_complete || '';
            document.getElementById('modalPartPhotoAvant').src        = data.photo_avant || '';
            document.getElementById('modalPartPhotoArriere').src      = data.photo_arriere || '';

            const m = document.getElementById('particulierDetailsModal');
            if (m) { m.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
        }

        function closeDetailsModal() {
            const m = document.getElementById('particulierDetailsModal');
            if (m) { m.classList.add('hidden'); document.body.style.overflow = ''; }
        }

        // ── AJAX Loaders for Compagnie ───────────────────────────────────────
        async function loadGaresAndItineraires(compagnieId) {
            if (!compagnieId) return;

            gareSelect.innerHTML = '<option value="">Chargement des gares...</option>';
            gareSelect.disabled = true;

            itineraireSelect.innerHTML = '<option value="">Chargement des itinéraires...</option>';
            itineraireSelect.disabled = true;

            try {
                // Gares
                const resGares = await fetch(`/user/convoi/compagnie/${compagnieId}/gares`);
                const dataGares = await resGares.json();
                let optG = '<option value="">Choisir une gare...</option>';
                (dataGares.gares || []).forEach(g => {
                    optG += `<option value="${g.id}">${g.nom_gare} (${g.ville || ''})</option>`;
                });
                gareSelect.innerHTML = optG;
                gareSelect.disabled = false;

                // Itinéraires
                const resItin = await fetch(`/user/convoi/compagnie/${compagnieId}/itineraires`);
                const dataItin = await resItin.json();
                let optI = '<option value="">— Saisir manuellement ou choisir un itinéraire —</option>';
                (dataItin.itineraires || []).forEach(it => {
                    optI += `<option value="${it.id}" data-depart="${it.point_depart}" data-arrive="${it.point_arrive}" data-duration="${it.durer_parcours || ''}">`
                          + `${it.point_depart} → ${it.point_arrive}</option>`;
                });
                itineraireSelect.innerHTML = optI;
                itineraireSelect.disabled = false;

            } catch (err) {
                console.error("Erreur AJAX:", err);
                gareSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                itineraireSelect.innerHTML = '<option value="">Erreur de chargement</option>';
            }
        }

        // Handle Itinerary Change
        if (itineraireSelect) {
            itineraireSelect.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                const dep = opt.getAttribute('data-depart');
                const arr = opt.getAttribute('data-arrive');
                const dur = opt.getAttribute('data-duration');

                if (dep && arr) {
                    setReadonlyMode(dep, arr, dur);
                } else {
                    setManualMode();
                }
            });
        }

        function setReadonlyMode(depart, arrive, duration) {
            itineraireMode = true;
            lieuDepart.value    = depart;
            lieuArrivee.value   = arrive;
            lieuDepart.readOnly  = true;
            lieuArrivee.readOnly = true;
            lieuDepart.classList.add('bg-gray-100', 'cursor-not-allowed');
            lieuArrivee.classList.add('bg-gray-100', 'cursor-not-allowed');

            if (duration) {
                durationText.textContent = duration;
                distanceText.textContent = '';
                durationSection.classList.remove('hidden');
            } else {
                tryCalculateDuration();
            }
        }

        function setManualMode() {
            itineraireMode = false;
            lieuDepart.readOnly  = false;
            lieuArrivee.readOnly = false;
            lieuDepart.classList.remove('bg-gray-100', 'cursor-not-allowed');
            lieuArrivee.classList.remove('bg-gray-100', 'cursor-not-allowed');
            durationSection.classList.add('hidden');
        }

        // ── Google Maps Places Autocomplete & Duration ──────────────────────
        function initConvoiMap() {
            mapsReady = true;
            directionsService = new google.maps.DirectionsService();
            const opts = { componentRestrictions: { country: 'ci' }, fields: ['formatted_address', 'geometry', 'name'] };
            autocompleteDepart = new google.maps.places.Autocomplete(lieuDepart, opts);
            autocompleteArrive = new google.maps.places.Autocomplete(lieuArrivee, opts);
            autocompleteDepart.addListener('place_changed', tryCalculateDuration);
            autocompleteArrive.addListener('place_changed', tryCalculateDuration);
        }

        function tryCalculateDuration() {
            if (!mapsReady || !directionsService) return;
            const origin      = lieuDepart.value.trim();
            const destination = lieuArrivee.value.trim();
            if (!origin || !destination) return;

            directionsService.route({
                origin, destination, travelMode: google.maps.TravelMode.DRIVING
            }).then(response => {
                const leg = response.routes[0].legs[0];
                durationText.textContent = leg.duration.text;
                distanceText.textContent = leg.distance.text;
                durationSection.classList.remove('hidden');
            }).catch(() => {
                if (!itineraireMode) durationSection.classList.add('hidden');
            });
        }

        // ── Escape Key closes modals ─────────────────────────────────────────
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCompagnieModal();
                closeParticulierModal();
                closeDetailsModal();
            }
        });

        // ── Init on DOM Load ─────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const urlPartId = urlParams.get('particulier_id');

            if (urlPartId) {
                selectProviderType('particulier');
                selectParticulierCard(urlPartId);
            } else {
                const oldType = "{{ old('type_transporteur', 'compagnie') }}";
                selectProviderType(oldType);

                const oldCompId = "{{ old('compagnie_id') }}";
                if (oldCompId) selectCompagnieCard(oldCompId);

                const oldPartId = "{{ old('particulier_id') }}";
                if (oldPartId) selectParticulierCard(oldPartId);
            }

            lieuDepart.addEventListener('blur',  () => { if (!itineraireMode) tryCalculateDuration(); });
            lieuArrivee.addEventListener('blur', () => { if (!itineraireMode) tryCalculateDuration(); });
        });
    </script>
    @endpush
@endsection
