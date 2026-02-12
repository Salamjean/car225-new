@extends('caisse.layouts.template')

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

        <!-- Tickets Cards (Compact Version) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($reservations as $reservation)
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-[#e94e1a] hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h3 class="font-bold text-gray-800 text-sm">Ticket N° {{ $loop->iteration }}</h3>
                        <p class="text-[10px] text-gray-400">{{ $reservation->reference }}</p>
                    </div>
                    <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-[10px] font-bold uppercase">
                        Confirmée
                    </span>
                </div>
                
                <div class="mb-3">
                    <p class="font-bold text-gray-800 text-base truncate" title="{{ $reservation->passager_prenom }} {{ $reservation->passager_nom }}">
                        {{ $reservation->passager_prenom }} {{ $reservation->passager_nom }}
                    </p>
                    <div class="flex items-center text-xs text-gray-500 mt-1">
                        <svg class="w-3 h-3 mr-1 text-[#e94e1a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        <span class="truncate">{{ $reservation->programme->point_depart }} <span class="mx-1">&rarr;</span> {{ $reservation->programme->point_arrive }}</span>
                    </div>
                </div>

                <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-100">
                    <span class="font-bold text-lg text-[#e94e1a]">{{ number_format($reservation->montant, 0, ',', ' ') }} F</span>
                    <div class="flex gap-2">
                        <button onclick="showTicketDetails({{ json_encode($reservation) }}, {{ json_encode($reservation->programme->point_depart) }}, {{ json_encode($reservation->programme->point_arrive) }}, {{ json_encode($reservation->programme->vehicule->immatriculation ?? 'Bus') }})" 
                            class="p-1.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-[#e94e1a] hover:text-white transition-all duration-200 shadow-sm hover:shadow-md" title="Voir détails">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                        <a href="{{ route('caisse.ticket.imprimer', $reservation->id) }}" target="_blank" 
                            class="p-1.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-800 hover:text-white transition-all duration-200 shadow-sm hover:shadow-md" title="Imprimer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <script>
            function showTicketDetails(reservation, depart, arrive, vehicule) {
                const dateVoyage = new Date(reservation.date_voyage).toLocaleDateString('fr-FR');
                const heureDepart = reservation.heure_depart ? reservation.heure_depart.substring(0, 5) : '--:--';
                const montant = new Intl.NumberFormat('fr-FR').format(reservation.montant);

                let qrCodeHtml = '';
                if(reservation.qr_code_path) {
                    qrCodeHtml = `
                        <div class="text-center my-4 p-4 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                            <img src="/storage/${reservation.qr_code_path}" alt="QR Code" class="mx-auto w-32 h-32 rounded-lg shadow-sm">
                            <p class="text-xs text-gray-500 mt-2 font-mono">${reservation.reference}</p>
                        </div>
                    `;
                }

                Swal.fire({
                    title: `<h3 class="text-2xl font-bold text-gray-800">Détails du Ticket</h3>`,
                    html: `
                        <div class="text-left space-y-4">
                            <!-- Passager -->
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Passager</p>
                                <p class="text-lg font-bold text-gray-900">${reservation.passager_prenom} ${reservation.passager_nom}</p>
                                <p class="text-sm text-gray-600 flex items-center gap-2 mt-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    ${reservation.passager_telephone}
                                </p>
                            </div>

                            <!-- Trajet -->
                            <div class="flex items-center justify-between p-4 bg-orange-50 rounded-xl border border-orange-100">
                                <div>
                                    <p class="text-xs font-bold text-gray-500 uppercase">Départ</p>
                                    <p class="font-bold text-gray-900">${depart}</p>
                                </div>
                                <svg class="w-6 h-6 text-[#e94e1a]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                <div class="text-right">
                                    <p class="text-xs font-bold text-gray-500 uppercase">Arrivée</p>
                                    <p class="font-bold text-gray-900">${arrive}</p>
                                </div>
                            </div>

                            <!-- Infos Techniques -->
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="bg-white border rounded-lg p-3">
                                    <span class="block text-xs text-gray-500 mb-1">Date & Heure</span>
                                    <span class="font-bold text-gray-800">${dateVoyage} à ${heureDepart}</span>
                                </div>
                                <div class="bg-white border rounded-lg p-3">
                                    <span class="block text-xs text-gray-500 mb-1">Siège</span>
                                    <span class="font-bold text-[#e94e1a] text-lg">N° ${reservation.seat_number}</span>
                                </div>
                                <div class="bg-white border rounded-lg p-3">
                                    <span class="block text-xs text-gray-500 mb-1">Véhicule</span>
                                    <span class="font-bold text-gray-800">${vehicule}</span>
                                </div>
                                <div class="bg-white border rounded-lg p-3">
                                    <span class="block text-xs text-gray-500 mb-1">Prix</span>
                                    <span class="font-bold text-gray-800">${montant} FCFA</span>
                                </div>
                            </div>

                            ${qrCodeHtml}
                        </div>
                    `,
                    showCloseButton: true,
                    showConfirmButton: false, // Pas besoin de bouton OK, la croix suffit ou cliquer dehors
                    width: '450px',
                    padding: '1.5rem',
                    customClass: {
                        popup: 'rounded-3xl'
                    }
                });
            }
        </script>

        <!-- Action Buttons -->
        <div class="flex gap-4 justify-center">
            <a href="{{ route('caisse.vendre-ticket') }}" 
                class="px-8 py-4 bg-[#e94e1a] text-white font-bold rounded-xl hover:bg-[#d33d0f] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                Nouvelle Vente
            </a>
            <a href="{{ route('caisse.ventes') }}" 
                class="px-8 py-4 bg-gray-700 text-white font-bold rounded-xl hover:bg-gray-800 transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                Voir l'historique
            </a>
            <a href="{{ route('caisse.dashboard') }}" 
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
