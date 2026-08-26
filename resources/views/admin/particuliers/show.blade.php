@extends('admin.layouts.template')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto animate-fade-in">

        <!-- Breadcrumb / Header -->
        <div class="mb-8">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-800">Accueil</a>
                <span><i class="fas fa-chevron-right text-xs"></i></span>
                <a href="{{ route('admin.particulier.index') }}" class="hover:text-gray-800">Particuliers</a>
                <span><i class="fas fa-chevron-right text-xs"></i></span>
                <span class="text-gray-800 font-semibold">Fiche Détails</span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl text-gray-800 font-extrabold tracking-tight">Détails du Transporteur Particulier</h1>
                    <p class="text-sm text-gray-500 mt-1">Étude et validation des pièces justificatives.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.particulier.demandes') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold text-xs shadow-sm transition-colors">
                        <i class="fas fa-arrow-left"></i> Demandes en attente
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
                <p class="font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
                <p class="font-semibold">{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left column: Owner info & Actions -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Owner Info Card -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 text-center">
                    <div class="w-32 h-32 rounded-full overflow-hidden mx-auto mb-4 border-4 border-white shadow-md relative bg-gray-100">
                        @if($particulier->photo_proprietaire)
                            <img src="{{ $particulier->photo_proprietaire_url }}" alt="Propriétaire" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center font-bold text-gray-400 bg-gray-200 text-3xl">
                                {{ substr($particulier->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-800 mb-1">{{ $particulier->full_name }}</h3>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Candidat Particulier</p>

                    <div class="text-left border-t border-gray-100 pt-4 space-y-3">
                        <div>
                            <span class="text-[10px] font-black uppercase text-gray-400 block">Adresse Email</span>
                            <span class="text-sm font-semibold text-gray-700 break-all">{{ $particulier->email }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase text-gray-400 block">Numéro Téléphone</span>
                            <span class="text-sm font-bold text-gray-700">{{ $particulier->contact }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase text-gray-400 block">Statut Actuel</span>
                            <div class="mt-1">
                                @if($particulier->statut === 'en_attente')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-bold uppercase border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> En Attente
                                    </span>
                                @elseif($particulier->statut === 'valide')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold uppercase border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Agréé
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-50 text-red-700 text-xs font-bold uppercase border border-red-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Rejeté
                                    </span>
                                    @if($particulier->motif_rejet)
                                        <div class="mt-2.5 p-3 rounded-xl bg-red-50/50 border border-red-100 text-[11px] font-semibold text-red-700 leading-normal">
                                            <span class="font-bold block uppercase text-[9px] text-red-500 mb-0.5"><i class="fas fa-exclamation-triangle"></i> Motif du rejet</span>
                                            {{ $particulier->motif_rejet }}
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        @if($particulier->code_id)
                            <div>
                                <span class="text-[10px] font-black uppercase text-gray-400 block">Code Unique (ID Portail)</span>
                                <span class="inline-flex mt-1 px-2.5 py-1 rounded bg-[#fff7ed] text-[#ea580c] font-mono text-xs font-bold border border-[#fed7aa]">
                                    {{ $particulier->code_id }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Admin Action Card -->
                @if($particulier->statut === 'en_attente')
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
                        <h4 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-2">Décision administrative</h4>
                        
                        <form action="{{ route('admin.particulier.valider', $particulier) }}" method="POST" onsubmit="return confirm('Valider ce transporteur particulier générera ses codes de connexion et lui enverra un SMS/email. Continuer ?');">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm transition-all shadow-sm">
                                <i class="fas fa-check-circle text-base"></i> Valider & Générer Accès
                            </button>
                        </form>

                        <button type="button" onclick="openRefuseModal()" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 font-bold text-sm transition-all border border-red-200 shadow-sm cursor-pointer">
                            <i class="fas fa-times-circle text-base"></i> Refuser l'inscription
                        </button>
                    </div>
                @endif
            </div>

            <!-- Right column: Vehicle specs & Docs -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Vehicle specifications -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                    <h4 class="font-bold text-gray-800 text-base mb-4 border-b border-gray-100 pb-3">
                        <i class="fas fa-bus text-orange me-2"></i> Caractéristiques du Car
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div>
                                <span class="text-xs text-gray-400 font-semibold uppercase">Plaque d'immatriculation</span>
                                <p class="text-base font-bold text-gray-800 font-mono mt-0.5">{{ $particulier->immatriculation }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400 font-semibold uppercase">Capacité totale du véhicule</span>
                                <p class="text-base font-bold text-gray-800 mt-0.5">{{ $particulier->nombre_place_car }} places</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <span class="text-xs text-gray-400 font-semibold uppercase">Date de mise en service</span>
                                <p class="text-base font-bold text-gray-800 mt-0.5">{{ \Carbon\Carbon::parse($particulier->date_mise_service)->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400 font-semibold uppercase">Âge du véhicule</span>
                                <div class="mt-0.5">
                                    <span class="inline-flex px-2.5 py-1 rounded-lg bg-blue-50 text-blue-800 text-xs font-bold border border-blue-100">
                                        <i class="fas fa-history me-1"></i> {{ $ageString }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Photos gallery -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                    <h4 class="font-bold text-gray-800 text-base mb-4 border-b border-gray-100 pb-3">
                        <i class="fas fa-images text-orange me-2"></i> Galerie Photos du Car
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Photo Complète -->
                        <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                            <div class="bg-gray-50 text-[10px] font-bold text-gray-500 p-2 text-center border-b uppercase">Vue Complète</div>
                            <div class="h-44 bg-gray-100">
                                @if($particulier->photo_complete_car)
                                    <a href="{{ $particulier->photo_complete_car_url }}" target="_blank">
                                        <img src="{{ $particulier->photo_complete_car_url }}" alt="Vue complète" class="w-full h-full object-cover hover:scale-105 transition-all">
                                    </a>
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-image text-2xl"></i></div>
                                @endif
                            </div>
                        </div>

                        <!-- Photo Avant -->
                        <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                            <div class="bg-gray-50 text-[10px] font-bold text-gray-500 p-2 text-center border-b uppercase">Vue Avant</div>
                            <div class="h-44 bg-gray-100">
                                @if($particulier->photo_avant_car)
                                    <a href="{{ $particulier->photo_avant_car_url }}" target="_blank">
                                        <img src="{{ $particulier->photo_avant_car_url }}" alt="Vue avant" class="w-full h-full object-cover hover:scale-105 transition-all">
                                    </a>
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-image text-2xl"></i></div>
                                @endif
                            </div>
                        </div>

                        <!-- Photo Arrière -->
                        <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                            <div class="bg-gray-50 text-[10px] font-bold text-gray-500 p-2 text-center border-b uppercase">Vue Arrière</div>
                            <div class="h-44 bg-gray-100">
                                @if($particulier->photo_arriere_car)
                                    <a href="{{ $particulier->photo_arriere_car_url }}" target="_blank">
                                        <img src="{{ $particulier->photo_arriere_car_url }}" alt="Vue arrière" class="w-full h-full object-cover hover:scale-105 transition-all">
                                    </a>
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-image text-2xl"></i></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents verification -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                    <h4 class="font-bold text-gray-800 text-base mb-4 border-b border-gray-100 pb-3">
                        <i class="fas fa-file-signature text-orange me-2"></i> Pièces Justificatives (Documents officiels)
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Carte Grise -->
                        <div class="p-4 rounded-xl border border-gray-100 bg-gray-50 flex flex-col justify-between">
                            <div>
                                <h5 class="font-bold text-gray-800 text-sm mb-1">Carte Grise</h5>
                                <p class="text-xs text-gray-400 font-semibold mb-3">Attestation de propriété du car</p>
                            </div>
                            
                            @if($particulier->carte_grise)
                                @php $isPdfGrise = \Illuminate\Support\Str::endsWith(strtolower($particulier->carte_grise), '.pdf'); @endphp
                                @if($isPdfGrise)
                                    <div class="text-center py-4">
                                        <i class="fas fa-file-pdf text-red-500 text-4xl mb-2"></i>
                                        <p class="text-xs font-semibold text-gray-500">Document PDF</p>
                                    </div>
                                @else
                                    <div class="h-28 rounded-lg overflow-hidden border mb-3 bg-white">
                                        <img src="{{ $particulier->carte_grise_url }}" alt="Carte Grise" class="w-full h-full object-cover">
                                    </div>
                                @endif
                                <a href="{{ $particulier->carte_grise_url }}" target="_blank"
                                    class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 text-xs font-bold transition-all shadow-sm">
                                    <i class="fas fa-external-link-alt"></i> Visualiser le document
                                </a>
                            @else
                                <div class="text-center py-4 text-gray-400 font-semibold text-xs">Aucun fichier</div>
                            @endif
                        </div>

                        <!-- Visite Technique -->
                        <div class="p-4 rounded-xl border border-gray-100 bg-gray-50 flex flex-col justify-between">
                            <div>
                                <h5 class="font-bold text-gray-800 text-sm mb-1">Visite Technique</h5>
                                <p class="text-xs text-gray-400 font-semibold mb-3">Contrôle technique en règle</p>
                            </div>
                            
                            @if($particulier->visite_technique)
                                @php $isPdfVisite = \Illuminate\Support\Str::endsWith(strtolower($particulier->visite_technique), '.pdf'); @endphp
                                @if($isPdfVisite)
                                    <div class="text-center py-4">
                                        <i class="fas fa-file-pdf text-red-500 text-4xl mb-2"></i>
                                        <p class="text-xs font-semibold text-gray-500">Document PDF</p>
                                    </div>
                                @else
                                    <div class="h-28 rounded-lg overflow-hidden border mb-3 bg-white">
                                        <img src="{{ $particulier->visite_technique_url }}" alt="Visite Technique" class="w-full h-full object-cover">
                                    </div>
                                @endif
                                <a href="{{ $particulier->visite_technique_url }}" target="_blank"
                                    class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 text-xs font-bold transition-all shadow-sm">
                                    <i class="fas fa-external-link-alt"></i> Visualiser le document
                                </a>
                            @else
                                <div class="text-center py-4 text-gray-400 font-semibold text-xs">Aucun fichier</div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <style>
        .animate-fade-in {
            animation: fadeIn 0.4s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .text-orange {
            color: #e94f1b;
        }
    </style>

    <!-- Modal Refuser Inscription (Custom Tailwind Modal) -->
    <div id="refuseRequestModal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 hidden" style="background-color: rgba(0, 0, 0, 0.65);">
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden animate-fade-in" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-gray-100 p-4 bg-gray-50/70">
                <h5 class="font-bold text-gray-800 text-base flex items-center gap-2 mb-0">
                    <span class="w-7 h-7 rounded-lg bg-red-100 text-red-600 flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-ban"></i>
                    </span>
                    Motif de rejet de la demande
                </h5>
                <button type="button" onclick="closeRefuseModal()" class="w-8 h-8 rounded-full bg-white hover:bg-gray-100 text-gray-400 hover:text-gray-600 flex items-center justify-center text-sm border border-gray-200 transition-colors focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.particulier.refuser', $particulier) }}" method="POST">
                @csrf
                <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto">
                    <p class="text-xs text-gray-500 font-semibold leading-relaxed mb-0">
                        Sélectionnez les motifs de rejet ou saisissez une explication personnalisée. Ces informations seront envoyées par SMS au demandeur.
                    </p>
                    
                    <!-- Checkboxes for common documents -->
                    <div class="space-y-2.5 border-b border-gray-100 pb-4">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Pièces & Informations non conformes :</span>
                        
                        <label class="flex items-start gap-3 p-2.5 rounded-xl border border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors mb-0">
                            <input type="checkbox" name="rejected_items[]" value="Photos du véhicule non conformes" class="mt-0.5 w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500 accent-red-600">
                            <span class="text-xs font-semibold text-gray-700">Photos du véhicule (incomplètes, floues ou non conformes)</span>
                        </label>
                        
                        <label class="flex items-start gap-3 p-2.5 rounded-xl border border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors mb-0">
                            <input type="checkbox" name="rejected_items[]" value="Carte grise expirée ou non valide" class="mt-0.5 w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500 accent-red-600">
                            <span class="text-xs font-semibold text-gray-700">Carte grise (inexistante, expirée ou non valide)</span>
                        </label>
                        
                        <label class="flex items-start gap-3 p-2.5 rounded-xl border border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors mb-0">
                            <input type="checkbox" name="rejected_items[]" value="Visite technique non valide" class="mt-0.5 w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500 accent-red-600">
                            <span class="text-xs font-semibold text-gray-700">Visite technique (expirée ou non valide)</span>
                        </label>
                        
                        <label class="flex items-start gap-3 p-2.5 rounded-xl border border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors mb-0">
                            <input type="checkbox" name="rejected_items[]" value="Capacité ou état du car non conforme" class="mt-0.5 w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500 accent-red-600">
                            <span class="text-xs font-semibold text-gray-700">Véhicule non éligible (moins de 10 places ou trop ancien)</span>
                        </label>
                    </div>

                    <!-- Textarea for custom reason -->
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider block mb-1.5">Observations personnalisées :</label>
                        <textarea name="custom_motif" placeholder="Précisez la raison détaillée du refus..." rows="3"
                            class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:bg-white outline-none font-semibold text-xs text-gray-800 transition-all"></textarea>
                    </div>
                </div>
                <div class="border-t border-gray-100 p-4 bg-gray-50 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeRefuseModal()" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2.5 rounded-xl text-xs font-extrabold bg-red-600 hover:bg-red-700 text-white shadow-lg shadow-red-600/30 transition-all">
                        <i class="fas fa-paper-plane me-1"></i> Refuser & Notifier
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRefuseModal() {
            const modal = document.getElementById('refuseRequestModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeRefuseModal() {
            const modal = document.getElementById('refuseRequestModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        // Close on clicking backdrop
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('refuseRequestModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeRefuseModal();
                    }
                });
            }
        });
    </script>
@endsection
