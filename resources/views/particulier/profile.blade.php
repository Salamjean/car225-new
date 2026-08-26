@extends('particulier.layouts.template')

@section('page-title', 'Mon Profil & Véhicule')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Left Profile Card -->
            <div class="md:col-span-1 bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm text-center">
                <div class="w-32 h-32 rounded-full overflow-hidden mx-auto mb-4 border-4 border-white shadow-md relative bg-gray-50">
                    @if($particulier->photo_proprietaire)
                        <img src="{{ $particulier->photo_proprietaire_url }}" alt="avatar" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center font-bold text-gray-400 bg-gray-200 text-3xl">
                            {{ substr($particulier->name, 0, 1) }}
                        </div>
                    @endif
                </div>

                <h3 class="text-lg font-black text-gray-800">{{ $particulier->full_name }}</h3>
                <p class="text-xs font-bold text-[#e94f1b] mt-0.5">{{ $particulier->code_id }}</p>
                
                <div class="mt-4 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase border border-emerald-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Compte Agréé
                </div>

                <div class="text-left border-t border-gray-100 pt-6 mt-6 space-y-4">
                    <div>
                        <span class="text-[10px] font-black uppercase text-gray-400 block">Adresse Email</span>
                        <span class="text-xs font-semibold text-gray-700 break-all">{{ $particulier->email }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase text-gray-400 block">Numéro de téléphone</span>
                        <span class="text-xs font-bold text-gray-700">{{ $particulier->contact }}</span>
                    </div>
                </div>
            </div>

            <!-- Right Vehicle & Docs Details -->
            <div class="md:col-span-2 space-y-6">
                <!-- Vehicle Specs Card -->
                <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm space-y-6">
                    <h4 class="font-black text-gray-800 text-base border-b border-gray-100 pb-3">
                        <i class="fas fa-bus text-[#e94f1b] me-2"></i> Caractéristiques du Véhicule
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <span class="text-[10px] font-black uppercase text-gray-400 block">Numéro d'immatriculation</span>
                                <span class="inline-flex px-2 py-1 mt-1 rounded bg-gray-100 text-gray-800 font-mono text-xs font-bold border border-gray-200">
                                    {{ $particulier->immatriculation }}
                                </span>
                            </div>
                            <div>
                                <span class="text-[10px] font-black uppercase text-gray-400 block">Nombre de places</span>
                                <p class="text-sm font-bold text-gray-800 mt-1">{{ $particulier->nombre_place_car }} places</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <span class="text-[10px] font-black uppercase text-gray-400 block">Mise en service</span>
                                <p class="text-sm font-bold text-gray-800 mt-1">{{ $particulier->date_mise_service->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Photos Card -->
                <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm space-y-4">
                    <h4 class="font-black text-gray-800 text-base border-b border-gray-100 pb-3">
                        <i class="fas fa-images text-[#e94f1b] me-2"></i> Photos du Véhicule
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Photo Complète -->
                        <div class="rounded-2xl overflow-hidden border border-gray-200/60 shadow-sm">
                            <div class="bg-gray-50 text-[9px] font-black text-gray-500 p-2 text-center border-b uppercase">Vue Complète</div>
                            <div class="h-32 bg-gray-100">
                                @if($particulier->photo_complete_car)
                                    <a href="{{ $particulier->photo_complete_car_url }}" target="_blank">
                                        <img src="{{ $particulier->photo_complete_car_url }}" alt="Vue complète" class="w-full h-full object-cover hover:scale-105 transition-all">
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Photo Avant -->
                        <div class="rounded-2xl overflow-hidden border border-gray-200/60 shadow-sm">
                            <div class="bg-gray-50 text-[9px] font-black text-gray-500 p-2 text-center border-b uppercase">Vue Avant</div>
                            <div class="h-32 bg-gray-100">
                                @if($particulier->photo_avant_car)
                                    <a href="{{ $particulier->photo_avant_car_url }}" target="_blank">
                                        <img src="{{ $particulier->photo_avant_car_url }}" alt="Vue avant" class="w-full h-full object-cover hover:scale-105 transition-all">
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Photo Arrière -->
                        <div class="rounded-2xl overflow-hidden border border-gray-200/60 shadow-sm">
                            <div class="bg-gray-50 text-[9px] font-black text-gray-500 p-2 text-center border-b uppercase">Vue Arrière</div>
                            <div class="h-32 bg-gray-100">
                                @if($particulier->photo_arriere_car)
                                    <a href="{{ $particulier->photo_arriere_car_url }}" target="_blank">
                                        <img src="{{ $particulier->photo_arriere_car_url }}" alt="Vue arrière" class="w-full h-full object-cover hover:scale-105 transition-all">
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents Verification Card -->
                <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm space-y-4">
                    <h4 class="font-black text-gray-800 text-base border-b border-gray-100 pb-3">
                        <i class="fas fa-file-contract text-[#e94f1b] me-2"></i> Pièces Administratives
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Carte Grise -->
                        <div class="p-4 bg-gray-50 border border-gray-100 rounded-2xl flex flex-col justify-between">
                            <div>
                                <h5 class="font-bold text-gray-800 text-sm mb-1">Carte Grise</h5>
                                <p class="text-xs text-gray-400 font-semibold mb-3">Document d'immatriculation officiel</p>
                            </div>
                            
                            <a href="{{ $particulier->carte_grise_url }}" target="_blank"
                                class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 text-xs font-bold transition-all shadow-sm">
                                <i class="fas fa-external-link-alt"></i> Ouvrir la pièce jointe
                            </a>
                        </div>

                        <!-- Visite Technique -->
                        <div class="p-4 bg-gray-50 border border-gray-100 rounded-2xl flex flex-col justify-between">
                            <div>
                                <h5 class="font-bold text-gray-800 text-sm mb-1">Visite Technique</h5>
                                <p class="text-xs text-gray-400 font-semibold mb-3">Attestation de conformité routière</p>
                            </div>
                            
                            <a href="{{ $particulier->visite_technique_url }}" target="_blank"
                                class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 text-xs font-bold transition-all shadow-sm">
                                <i class="fas fa-external-link-alt"></i> Ouvrir la pièce jointe
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
