@extends('caisse.layouts.template')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Vendre des Tickets</h1>
            <p class="text-lg text-gray-600">Gérez vos ventes de tickets</p>
        </div>


        <!-- Formulaire de vente -->
        <div class="bg-white rounded-3xl shadow-xl p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-[#e94e1a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
                Nouvelle Vente
            </h2>

            <form action="{{ route('caisse.vendre-ticket.submit') }}" method="POST" id="ticket-form">
                @csrf

                <!-- Sélection du programme -->
<div class="mb-6">
    <label class="block text-sm font-semibold text-gray-700 mb-2">Programme (Départs d'aujourd'hui) *</label>
    <select name="programme_id" id="programme_id" required
        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
        <option value="">-- Sélectionnez un départ --</option>
        @foreach($programmes as $programme)
            <option value="{{ $programme->id }}">
                {{-- 1. L'heure (c'est le plus important pour un départ imminent) --}}
                {{ \Carbon\Carbon::parse($programme->heure_depart)->format('H:i') }} 
                
                {{-- 2. Le trajet --}}
                | {{ $programme->point_depart }} → {{ $programme->point_arrive }} 
                
                {{-- 3. La date (On affiche explicitement la date d'aujourd'hui) --}}
                | {{ \Carbon\Carbon::now()->format('d/m/Y') }} 
                
                {{-- 4. Infos véhicule et prix --}}
                | {{ $programme->vehicule->immatriculation ?? 'Bus' }}
                | {{ number_format($programme->montant_billet, 0, ',', ' ') }} FCFA
            </option>
        @endforeach
    </select>
    @error('programme_id')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

                <!-- Nombre de tickets -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre de tickets *</label>
                    <div class="flex items-center">
                        <button type="button" onclick="decrementTickets()" 
                            class="px-4 py-3 bg-gray-200 text-gray-700 font-bold rounded-l-xl hover:bg-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </button>
                        <input type="number" name="nombre_tickets" id="nombre_tickets" value="1" min="1" required
                            class="w-32 px-4 py-3 border-y border-gray-300 text-center font-bold text-lg focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                        <button type="button" onclick="incrementTickets()" 
                            class="px-4 py-3 bg-gray-200 text-gray-700 font-bold rounded-r-xl hover:bg-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </div>
                    @error('nombre_tickets')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Informations des passagers -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-4">Informations des Passagers</label>
                    <div id="passengers-container" class="space-y-4">
                        <!-- Les formulaires passagers seront ajoutés ici dynamiquement -->
                    </div>
                </div>

                <!-- Bouton de soumission -->
                <div class="flex gap-4">
                    <button type="submit" 
                        class="px-8 py-4 bg-[#e94e1a] text-white font-bold rounded-xl hover:bg-[#d33d0f] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                        Confirmer la vente
                    </button>
                    <button type="reset" 
                        class="px-8 py-4 bg-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-300 transition-all duration-200">
                        Réinitialiser
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    
    function incrementTickets() {
        const input = document.getElementById('nombre_tickets');
        input.value = parseInt(input.value) + 1;
        updatePassengerForms();
    }

    function decrementTickets() {
        const input = document.getElementById('nombre_tickets');
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
            updatePassengerForms();
        }
    }

    document.getElementById('nombre_tickets').addEventListener('input', updatePassengerForms);

    function updatePassengerForms() {
        const count = parseInt(document.getElementById('nombre_tickets').value) || 1;
        const container = document.getElementById('passengers-container');
        container.innerHTML = '';

        for (let i = 1; i <= count; i++) {
            const passengerForm = `
                <div class="p-4 border border-gray-300 rounded-xl bg-gray-50">
                    <h4 class="font-bold text-gray-900 mb-3">Passager ${i}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                            <input type="text" name="passenger_details[${i-1}][nom]" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                            <input type="text" name="passenger_details[${i-1}][prenom]" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                            <input type="tel" name="passenger_details[${i-1}][telephone]" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="passenger_details[${i-1}][email]"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                        </div>
                    </div>
                </div>
            `;
            container.innerHTML += passengerForm;
        }
    }

    // Initialiser les formulaires au chargement
    document.addEventListener('DOMContentLoaded', updatePassengerForms);

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Succès!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#e94e1a',
            timer: 3000,
            timerProgressBar: true
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Erreur!',
            html: '@foreach($errors->all() as $error) {{ $error }}<br> @endforeach',
            confirmButtonColor: '#d33'
        });
    @endif
</script>
@endsection
