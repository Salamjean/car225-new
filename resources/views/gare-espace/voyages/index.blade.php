@extends('gare-espace.layouts.template')

@section('title', 'Programmer les Voyages')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    .ts-wrapper .ts-control {
        border-radius: 0.75rem !important;
        padding: 0.75rem 1rem !important;
        border-color: #e5e7eb !important;
        background-color: #f9fafb !important;
        font-size: 0.875rem !important;
    }
    .ts-wrapper.focus .ts-control {
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.5) !important;
        border-color: transparent !important;
    }
    .ts-dropdown {
        border-radius: 0.75rem !important;
        margin-top: 5px !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Programmer les Voyages</h2>
            <p class="text-gray-500 text-lg">Assignez des chauffeurs et véhicules aux programmes de votre compagnie</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                    <p class="text-red-700 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Date Selector -->
        <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 mb-8">
            <form action="{{ route('gare-espace.voyages.index') }}" method="GET" class="flex items-end gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Date du voyage</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar text-gray-400"></i>
                        </div>
                        <input type="date" name="date" value="{{ $date }}" min="{{ date('Y-m-d') }}"
                            onchange="this.form.submit()"
                            class="block w-full pl-10 px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-gray-50 focus:bg-white">
                    </div>
                </div>
                <button type="submit" class="bg-blue-600 text-white p-3.5 rounded-xl hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg">
                    <i class="fas fa-search text-lg"></i>
                </button>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium uppercase">Programmes disponibles</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalProgrammesCount }}</p>
                    </div>
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-route text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium uppercase">Chauffeurs disponibles</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $chauffeurs->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-check text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium uppercase">Véhicules actifs</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $vehicules->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-bus text-orange-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Programmes List -->
        <div class="space-y-6">
            @forelse($programmes as $programme)
                @php
                    $voyageAssigne = $programme->voyages->first();
                @endphp
                
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                    <!-- Programme Header -->
                    <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center text-blue-600 font-bold text-xl border border-gray-100">
                                    {{ \Carbon\Carbon::parse($programme->heure_depart)->format('H:i') }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Programme #{{ $programme->id }}</p>
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="font-bold text-gray-900 text-lg">{{ $programme->gareDepart?->nom_gare ?? 'Gare inconnue' }}</span>
                                        <i class="fas fa-arrow-right text-blue-500"></i>
                                        <span class="font-bold text-gray-900 text-lg">{{ $programme->gareArrivee?->nom_gare ?? 'Gare inconnue' }}</span>
                                    </div>
                                    
                                    <div class="flex items-center gap-4 text-sm">
                                        <div class="flex items-center gap-2 bg-blue-50 px-3 py-1 rounded-lg">
                                            <i class="fas fa-map-marker-alt text-blue-600"></i>
                                            <span class="text-gray-700">
                                                <span class="font-semibold">{{ $programme->point_depart }}</span>
                                                <i class="fas fa-long-arrow-alt-right text-blue-500 mx-1"></i>
                                                <span class="font-semibold">{{ $programme->point_arrive }}</span>
                                            </span>
                                        </div>
                                        
                                        @php
                                            $depart = \Carbon\Carbon::parse($programme->heure_depart);
                                            $arrivee = \Carbon\Carbon::parse($programme->heure_arrive);
                                            $duree = $depart->diff($arrivee);
                                            $heures = $duree->h;
                                            $minutes = $duree->i;
                                        @endphp
                                        
                                        <div class="flex items-center gap-2 bg-purple-50 px-3 py-1 rounded-lg">
                                            <i class="fas fa-clock text-purple-600"></i>
                                            <span class="text-gray-700">
                                                <span class="font-semibold">Durée:</span> 
                                                @if($heures > 0)
                                                    {{ $heures }}h 
                                                @endif
                                                {{ $minutes }}min
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-center gap-2 bg-blue-100 px-3 py-1 rounded-lg">
                                            <i class="fas fa-users text-blue-700"></i>
                                            <span class="text-gray-700">
                                                <span class="font-semibold">Capacité requise:</span> {{ $programme->getTotalSeats() }} places
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($voyageAssigne)
                                <span class="px-4 py-2 bg-green-100 text-green-700 rounded-xl font-semibold text-sm flex items-center gap-2">
                                    <i class="fas fa-check-circle"></i>
                                    Assigné
                                </span>
                            @else
                                <span class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-xl font-semibold text-sm flex items-center gap-2">
                                    <i class="fas fa-clock"></i>
                                    En attente
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Assignment Form or Details -->
                    <div class="p-6">
                        @if($voyageAssigne)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="bg-gray-50 p-4 rounded-xl">
                                    <p class="text-xs font-bold text-gray-500 uppercase mb-2">Chauffeur assigné</p>
                                    <p class="font-bold text-gray-900">{{ $voyageAssigne->chauffeur?->prenom ?? 'N/A' }} {{ $voyageAssigne->chauffeur?->name ?? '' }}</p>
                                    <p class="text-sm text-gray-500">{{ $voyageAssigne->chauffeur?->contact ?? '' }}</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-xl">
                                    <p class="text-xs font-bold text-gray-500 uppercase mb-2">Véhicule assigné</p>
                                    <p class="font-bold text-gray-900">{{ $voyageAssigne->vehicule?->immatriculation ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-500">{{ $voyageAssigne->vehicule?->marque ?? '' }} {{ $voyageAssigne->vehicule?->modele ?? '' }}</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-xl">
                                    <p class="text-xs font-bold text-gray-500 uppercase mb-2">Statut du voyage</p>
                                    @if($voyageAssigne->statut === 'en_attente')
                                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg font-semibold text-sm">
                                            <i class="fas fa-hourglass-half"></i> En attente
                                        </span>
                                    @elseif($voyageAssigne->statut === 'confirmé')
                                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-blue-100 text-blue-700 rounded-lg font-semibold text-sm">
                                            <i class="fas fa-check"></i> Confirmé
                                        </span>
                                    @elseif($voyageAssigne->statut === 'en_cours')
                                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-purple-100 text-purple-700 rounded-lg font-semibold text-sm">
                                            <i class="fas fa-spinner fa-spin"></i> En cours
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-green-100 text-green-700 rounded-lg font-semibold text-sm">
                                            <i class="fas fa-check-circle"></i> Terminé
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($voyageAssigne->statut !== 'terminé')
                                <form action="{{ route('gare-espace.voyages.destroy', $voyageAssigne->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler {{ $voyageAssigne->statut === 'en_cours' ? 'ce voyage en cours' : 'cette assignation' }} ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full bg-red-500 text-white py-3 rounded-xl font-bold hover:bg-red-600 transition-colors flex items-center justify-center gap-2">
                                        <i class="fas fa-times-circle"></i>
                                        {{ $voyageAssigne->statut === 'en_cours' ? 'Annuler le voyage' : 'Annuler l\'assignation' }}
                                    </button>
                                </form>
                            @endif
                        @else
                            <form action="{{ route('gare-espace.voyages.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="programme_id" value="{{ $programme->id }}">
                                <input type="hidden" name="date_voyage" value="{{ $date }}">
                                <input type="hidden" class="required-capacity" value="{{ $programme->getTotalSeats() }}">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 uppercase mb-2">
                                            <i class="fas fa-user-tie mr-1"></i> Chauffeur
                                        </label>
                                        <select name="personnel_id" required class="tom-select block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 focus:bg-white">
                                            <option value="">-- Sélectionner un chauffeur --</option>
                                            @foreach($chauffeurs as $chauffeur)
                                                <option value="{{ $chauffeur->id }}">
                                                    {{ $chauffeur->prenom }} {{ $chauffeur->name }} - {{ $chauffeur->contact }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 uppercase mb-2">
                                            <i class="fas fa-bus mr-1"></i> Véhicule
                                        </label>
                                        <select name="vehicule_id" required class="tom-select vehicule-select block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 focus:bg-white">
                                            <option value="">-- Sélectionner un véhicule --</option>
                                            @foreach($vehicules as $vehicule)
                                                <option value="{{ $vehicule->id }}" data-capacity="{{ $vehicule->nombre_place }}">
                                                    {{ $vehicule->immatriculation }} - {{ $vehicule->marque }} {{ $vehicule->modele }} ({{ $vehicule->nombre_place }} places)
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3.5 rounded-xl font-bold flex items-center justify-center gap-2 hover:from-blue-700 hover:to-indigo-700 transition-all shadow-md hover:shadow-lg">
                                    <i class="fas fa-check-circle"></i>
                                    Assigner ce voyage
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-gray-200">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-inbox text-gray-300 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun programme disponible</h3>
                    <p class="text-gray-500 max-w-md mx-auto">Il n'y a pas de programmes actifs pour cette date.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $programmes->links() }}
        </div>
    </div>
</div>
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.tom-select').forEach((el) => {
            new TomSelect(el, {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: el.options[0].text,
                allowEmptyOption: true,
            });
        });

        // Validation de la capacité lors de la soumission
        const forms = document.querySelectorAll('form[action="{{ route("gare-espace.voyages.store") }}"]');
        forms.forEach(form => {
            const vehiculeSelect = form.querySelector('.vehicule-select');
            const requiredCapacity = parseInt(form.querySelector('.required-capacity').value || 64);

            // Validation lors du changement de sélection (facultatif mais proactif)
            if (vehiculeSelect.tomselect) {
                vehiculeSelect.tomselect.on('change', function(value) {
                    const option = vehiculeSelect.options[vehiculeSelect.selectedIndex];
                    const control = vehiculeSelect.tomselect.control;
                    
                    if (option && option.dataset.capacity) {
                        const vehicleCapacity = parseInt(option.dataset.capacity);
                        if (vehicleCapacity !== requiredCapacity) {
                            control.style.borderColor = '#ef4444'; // Red border
                            control.style.backgroundColor = '#fef2f2';
                        } else {
                            control.style.borderColor = '#10b981'; // Green border
                            control.style.backgroundColor = '#f0fdf4';
                        }
                    } else {
                        control.style.borderColor = '';
                        control.style.backgroundColor = '';
                    }
                });
            }

            // Validation lors de la soumission
            form.addEventListener('submit', function(e) {
                const selectedOption = vehiculeSelect.options[vehiculeSelect.selectedIndex];
                
                if (selectedOption && selectedOption.dataset.capacity) {
                    const vehicleCapacity = parseInt(selectedOption.dataset.capacity);
                    
                    if (vehicleCapacity !== requiredCapacity) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Capacité non correspondante',
                            html: `
                                <div class="text-left py-2">
                                    <p class="mb-3">Ce programme requiert un véhicule de <strong>${requiredCapacity} places</strong>.</p>
                                    <p class="text-red-600 font-bold">Le véhicule sélectionné possède ${vehicleCapacity} places.</p>
                                    <p class="mt-4 text-sm text-gray-600 italic">Veuillez choisir un véhicule dont la capacité correspond exactement à celle du programme.</p>
                                </div>
                            `,
                            confirmButtonColor: '#3b82f6',
                            confirmButtonText: 'Compris'
                        });
                    }
                }
            });
        });
    });
</script>
@endsection
@endsection
