@extends('user.layouts.template')

@section('title', 'Détails de la Réservation - ' . $reservation->reference)

@section('content')
<div class="space-y-8">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4">
                <a href="{{ route('reservation.index') }}" class="hover:text-[#e94f1b] transition-colors">Mes Réservations</a>
                <i class="fas fa-chevron-right text-[8px]"></i>
                <span class="text-gray-900">{{ $reservation->reference }}</span>
            </nav>
            <h1 class="text-3xl font-black text-[#1A1D1F] tracking-tight flex items-center gap-3 uppercase">
                <i class="fas fa-receipt text-[#e94f1b]"></i>
                Détails du <span class="text-[#e94f1b]">Billet</span>
            </h1>
            <p class="text-gray-500 font-medium">Référence : {{ $reservation->reference }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if($reservation->statut == 'confirmee')
                <a href="{{ route('reservations.download', $reservation) }}" 
                   class="px-6 py-3.5 bg-gray-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-gray-900/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-2">
                    <i class="fas fa-file-pdf"></i> Télécharger le Billet
                </a>
            @endif
            <a href="{{ route('reservation.index') }}" class="px-6 py-3.5 bg-white border border-gray-100 text-gray-600 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-50 transition-all flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Journey Card -->
            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-route text-[#e94f1b]"></i>
                        Itinéraire du Voyage
                    </h3>
                    <span class="px-3 py-1 bg-white border border-gray-100 rounded-lg text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        #{{ $reservation->programme->id }}
                    </span>
                </div>
                <div class="p-8">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-8 mb-12">
                        <div class="text-center md:text-left">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Départ</p>
                            <h4 class="text-2xl font-black text-[#e94f1b]">{{ $reservation->programme->point_depart }}</h4>
                            <p class="text-sm font-bold text-[#e94f1b]">{{ \Carbon\Carbon::parse($reservation->heure_depart ?? $reservation->programme->heure_depart)->format('H:i') }}</p>
                        </div>
                        
                        <div class="flex-1 flex flex-col items-center gap-2 max-w-[200px]">
                            <div class="w-full h-px bg-dashed bg-gray-200 relative">
                                <i class="fas fa-bus absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-2 text-[#e94f1b]"></i>
                            </div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $reservation->programme->durer_parcours }} MIN</span>
                        </div>

                        <div class="text-center md:text-right">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Arrivée</p>
                            <h4 class="text-2xl font-black text-[#e94f1b]">{{ $reservation->programme->point_arrive }}</h4>
                            <p class="text-sm font-bold text-[#e94f1b]">{{ \Carbon\Carbon::parse($reservation->heure_arrive ?? $reservation->programme->heure_arrive)->format('H:i') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6 pt-8 border-t border-gray-50">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Date</p>
                            <p class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($reservation->date_voyage)->format('d/m/Y') }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Siège</p>
                            <p class="text-sm font-bold text-gray-900">Place N° {{ $reservation->seat_number }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Type</p>
                            <p class="text-sm font-bold text-gray-900 uppercase">
                                @if(str_contains($reservation->reference, '-RET-'))
                                    Billet Retour
                                @elseif($reservation->is_aller_retour)
                                    Billet Aller
                                @else
                                    Aller Simple
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Passenger Details -->
            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/50">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-user text-[#e94f1b]"></i>
                        Informations Passager
                    </h3>
                </div>
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="flex items-center gap-4 bg-gray-50 p-6 rounded-2xl">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-[#e94f1b]">
                            <i class="fas fa-id-card text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Nom du Voyageur</p>
                            <p class="text-lg font-black text-gray-900 uppercase leading-none">{{ $reservation->passager_nom }} {{ $reservation->passager_prenom }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 bg-gray-50 p-6 rounded-2xl">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-[#e94f1b]">
                            <i class="fas fa-phone text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Contact</p>
                            <p class="text-lg font-black text-gray-900 leading-none">{{ $reservation->passager_telephone }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-8">
            <!-- Status Card -->
            <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm text-center">
                @if($reservation->statut == 'confirmee')
                    <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center text-green-600 mx-auto mb-6">
                        <i class="fas fa-check-circle text-4xl"></i>
                    </div>
                    <h4 class="text-xl font-black text-gray-900 uppercase tracking-tight mb-2">Réservation Confirmée</h4>
                    <p class="text-gray-500 font-medium text-sm mb-6">Votre billet est prêt pour le voyage.</p>
                @elseif($reservation->statut == 'en_attente')
                    <div class="w-20 h-20 bg-yellow-50 rounded-full flex items-center justify-center text-yellow-600 mx-auto mb-6">
                        <i class="fas fa-clock text-4xl"></i>
                    </div>
                    <h4 class="text-xl font-black text-gray-900 uppercase tracking-tight mb-2">En Attente</h4>
                    <p class="text-gray-500 font-medium text-sm mb-6">Le paiement est en cours de validation.</p>
                @else
                    <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center text-red-600 mx-auto mb-6">
                        <i class="fas fa-times-circle text-4xl"></i>
                    </div>
                    <h4 class="text-xl font-black text-gray-900 uppercase tracking-tight mb-2">Réservation Annulée</h4>
                    <p class="text-gray-500 font-medium text-sm mb-6">Ce billet n'est plus valide.</p>
                @endif

                <div class="pt-6 border-t border-gray-50 space-y-4">
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-black text-gray-400 uppercase tracking-widest">Montant Payé</span>
                        <span class="font-black text-gray-900">{{ number_format($reservation->montant, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-black text-gray-400 uppercase tracking-widest">Méthode</span>
                        <span class="font-black text-gray-900 uppercase">{{ $reservation->payment_method ?? 'CinetPay' }}</span>
                    </div>
                </div>
            </div>

            <!-- Transport Company -->
            <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4 overflow-hidden p-2">
                    @if($reservation->programme->compagnie->logo)
                        <img src="{{ asset('storage/' . $reservation->programme->compagnie->logo) }}" class="w-full h-full object-contain">
                    @else
                        <i class="fas fa-bus text-2xl text-[#e94f1b]"></i>
                    @endif
                </div>
                <h5 class="text-lg font-black text-gray-900 uppercase tracking-tight">{{ $reservation->programme->compagnie->name }}</h5>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Transport Routier</p>
                
                <div class="space-y-3 text-left bg-gray-50 p-4 rounded-2xl">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-phone text-[#e94f1b] text-xs"></i>
                        <span class="text-xs font-bold text-gray-700">{{ $reservation->programme->compagnie->contact }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fas fa-envelope text-[#e94f1b] text-xs"></i>
                        <span class="text-xs font-bold text-gray-700">{{ $reservation->programme->compagnie->email }}</span>
                    </div>
                </div>
            </div>

            <!-- QR Code (Placeholder for now as in original) -->
            @if($reservation->qr_code_path)
            <div class="bg-[#1A1D1F] rounded-[32px] p-8 text-center text-white">
                <p class="text-[10px] font-black uppercase tracking-widest text-[#e94f1b] mb-4">Code d'Embarquement</p>
                <div class="bg-white p-4 rounded-2xl mb-6 inline-block mx-auto">
                    <img src="{{ asset('storage/' . $reservation->qr_code_path) }}" class="w-32 h-32">
                </div>
                <p class="text-xs font-medium text-gray-400 mb-6">Présentez ce code au contrôleur lors de votre montée dans le car.</p>
                <a href="{{ route('reservations.download', $reservation) }}" class="w-full py-3 bg-white/10 hover:bg-white/20 rounded-xl text-xs font-black uppercase tracking-widest transition-all inline-block">
                    Sauvegarder
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection