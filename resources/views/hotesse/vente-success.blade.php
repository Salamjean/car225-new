@extends('hotesse.layouts.template')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- Success Header -->
        <div class="text-center mb-8">
            <div class="mx-auto w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Vente Réussie !</h1>
            <p class="text-lg text-gray-600">{{ count($reservations) }} ticket(s) vendu(s) avec succès</p>
        </div>

        <!-- Tickets Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            @foreach($reservations as $reservation)
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border-l-4 border-[#e94e1a]">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Ticket N° {{ $loop->iteration }}</h3>
                            <p class="text-sm text-gray-500">{{ $reservation->reference }}</p>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                            Confirmée
                        </span>
                    </div>

                    <!-- Passenger Info -->
                    <div class="mb-4 pb-4 border-b border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Passager</h4>
                        <p class="text-lg font-bold text-gray-900">{{ $reservation->passager_prenom }} {{ $reservation->passager_nom }}</p>
                        <p class="text-sm text-gray-600">{{ $reservation->passager_telephone }}</p>
                        @if($reservation->passager_email)
                        <p class="text-sm text-gray-600">{{ $reservation->passager_email }}</p>
                        @endif
                    </div>

                    <!-- Trip Info -->
                    <div class="mb-4 pb-4 border-b border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Trajet</h4>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-lg font-bold text-gray-900">{{ $reservation->programme->point_depart }}</span>
                            <svg class="w-5 h-5 text-[#e94e1a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                            <span class="text-lg font-bold text-gray-900">{{ $reservation->programme->point_arrive }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <span class="text-gray-600">Date:</span>
                                <span class="font-semibold">{{ \Carbon\Carbon::parse($reservation->date_voyage)->format('d/m/Y') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Départ:</span>
                                <span class="font-semibold">{{ \Carbon\Carbon::parse($reservation->heure_depart)->format('H:i') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Siège:</span>
                                <span class="font-semibold">N° {{ $reservation->seat_number }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Véhicule:</span>
                                <span class="font-semibold">{{ $reservation->programme->vehicule->immatriculation }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-700 font-semibold">Montant</span>
                        <span class="text-2xl font-bold text-[#e94e1a]">{{ number_format($reservation->montant, 0, ',', ' ') }} FCFA</span>
                    </div>

                    <!-- QR Code -->
                    @if($reservation->qr_code_path)
                    <div class="text-center mb-4">
                        <img src="{{ asset('storage/' . $reservation->qr_code_path) }}" alt="QR Code" class="mx-auto w-32 h-32">
                        <p class="text-xs text-gray-500 mt-1">Code de vérification</p>
                    </div>
                    @endif

                    <!-- Print Button -->
                    <a href="{{ route('hotesse.ticket.imprimer', $reservation->id) }}" target="_blank"
                        class="block w-full px-6 py-3 bg-[#e94e1a] text-white font-bold rounded-xl hover:bg-[#d33d0f] text-center transition-all duration-200">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Imprimer ce ticket
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4 justify-center">
            <a href="{{ route('hotesse.vendre-ticket') }}" 
                class="px-8 py-4 bg-[#e94e1a] text-white font-bold rounded-xl hover:bg-[#d33d0f] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                Nouvelle Vente
            </a>
            <a href="{{ route('hotesse.ventes') }}" 
                class="px-8 py-4 bg-gray-700 text-white font-bold rounded-xl hover:bg-gray-800 transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                Voir l'historique
            </a>
            <a href="{{ route('hotesse.dashboard') }}" 
                class="px-8 py-4 bg-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-300 transition-all duration-200">
                Retour au tableau de bord
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Succès!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#e94e1a',
        timer: 3000,
        timerProgressBar: true
    });
</script>
@endif
@endsection
