@extends('home.layouts.template')

@section('content')

<!-- Hero -->
<section class="bg-slate-50 pt-28 pb-8">
    <div class="container mx-auto px-4 max-w-[900px]">
        <div class="text-center mb-10">
            <h1 class="text-[2.5rem] md:text-5xl font-black text-slate-900 mb-4 tracking-tight">
                Mes <span class="text-[#0e743a]">Réservations</span>
            </h1>
            <p class="text-slate-700 text-[15px] font-medium">
                Gérez et suivez tous vos billets réservés
            </p>
        </div>

        <!-- Search Bar -->
        <form id="searchForm" action="{{ route('home.reservations') }}" method="GET" class="max-w-[800px] mx-auto mb-6">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative shadow-sm rounded-xl overflow-hidden bg-white border border-slate-200">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#94a3b8" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.44 1.16a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z"/></svg>
                    </div>
                    <input type="text" id="searchInput" name="reference" value="{{ $searchRef ?? '' }}" class="bg-white text-slate-900 text-[15px] rounded-xl block w-full pl-11 pr-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-[#e94e1a] border-0" placeholder="Entrez la référence de votre billet (ex: TX-WAL-FPFXCVCBPE)">
                </div>
                <!-- Search Button -->
                <button type="submit" class="bg-[#e94e1a] text-white px-8 py-3.5 rounded-xl font-bold hover:bg-[#d14316] shadow-sm hover:shadow-md transition-all duration-300 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.44 1.16a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z"/></svg>
                    <span>Rechercher</span>
                </button>
            </div>
        </form>
    </div>
</section>

<!-- Reservations List -->
<section class="bg-slate-50 pb-20">
    <div class="container mx-auto px-4 max-w-[900px]">

        @if(!$searched)
            <!-- État initial -->
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-sm">
                <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#0e743a" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 3.5V14a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V3.5z"/><path d="M4 5.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8m0 2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5"/></svg>
                </div>
                <h3 class="text-xl font-extrabold text-slate-800 mb-3">Retrouvez vos billets</h3>
                <p class="text-slate-500 text-[15px] max-w-md mx-auto">Entrez la référence de votre billet dans la barre de recherche ci-dessus pour retrouver vos réservations.</p>
            </div>
        @elseif($grouped->isEmpty())
            <!-- Aucun résultat -->
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-sm">
                <div class="w-20 h-20 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#f15a24" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.44 1.16a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z"/></svg>
                </div>
                <h3 class="text-xl font-extrabold text-slate-800 mb-3">Aucune réservation trouvée</h3>
                <p class="text-slate-500 text-[15px] max-w-md mx-auto">Aucune réservation ne correspond à la référence "<strong>{{ $searchRef }}</strong>". Vérifiez votre référence et réessayez.</p>
            </div>
        @else
            <!-- Résultats groupés -->
            <div class="space-y-5">
                @foreach($grouped as $baseRef => $group)
                    {{-- === CARTE ALLER === --}}
                    @if($group->aller->isNotEmpty())
                        @php
                            $reservation = $group->first;
                            $programme = $reservation->programme;
                            $compagnie = $programme->compagnie;
                            $gareDepart = $programme->gareDepart;
                            $gareArrivee = $programme->gareArrivee;
                            // Fallback via itinéraire si gareDepart/gareArrivee sont nulles
                            $nomGareDepart = $gareDepart->nom ?? ($programme->itineraire->point_depart ?? $programme->point_depart ?? 'N/A');
                            $nomGareArrivee = $gareArrivee->nom ?? ($programme->itineraire->point_arrive ?? $programme->point_arrive ?? 'N/A');
                            $isActive = $reservation->statut === 'confirmee';
                            $statutLabel = match($reservation->statut) {
                                'confirmee' => ['label' => 'Confirmé', 'class' => 'bg-emerald-100 text-emerald-700 border-emerald-200'],
                                'terminee' => ['label' => 'Passé', 'class' => 'bg-slate-100 text-slate-600 border-slate-200'],
                                'annulee' => ['label' => 'Annulé', 'class' => 'bg-red-100 text-red-600 border-red-200'],
                                default => ['label' => ucfirst($reservation->statut), 'class' => 'bg-slate-100 text-slate-600 border-slate-200'],
                            };
                            $allSeats = $group->all_seats_aller;
                        @endphp
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                            <!-- Header -->
                            <div class="px-6 pt-5 pb-2 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-5">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <h3 class="text-lg font-extrabold text-[#111]">
                                        {{ $programme->point_depart ?? 'N/A' }} → {{ $programme->point_arrive ?? 'N/A' }}
                                    </h3>
                                    <span class="text-[10px] font-black tracking-wider px-2.5 py-1 rounded-full border {{ $statutLabel['class'] }}">
                                        {{ $statutLabel['label'] }}
                                    </span>
                                </div>
                                <div class="text-left sm:text-right mt-2 sm:mt-0 w-full sm:w-auto">
                                    <div class="text-xl font-black text-[#f15a24]">{{ number_format((float)$reservation->montant, 0, ',', ' ') }} F</div>
                                    <div class="text-[12px] font-semibold text-slate-500">{{ $compagnie->name ?? 'N/A' }}</div>
                                </div>
                            </div>

                            <!-- Gares -->
                            <div class="px-6 pb-2">
                                <div class="text-[12px] text-slate-500 font-medium flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#f15a24" viewBox="0 0 16 16"><path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/></svg>
                                    Gare: {{ $nomGareDepart }} → {{ $nomGareArrivee }}
                                </div>
                            </div>

                            <!-- Reference -->
                            <div class="px-6 pb-2">
                                <div class="text-[12px] text-slate-400 font-semibold">
                                    Billet # {{ $reservation->reference }}
                                    @if($group->aller->count() > 1)
                                        <span class="text-slate-300">→</span> {{ $group->aller->last()->reference }}
                                    @endif
                                </div>
                            </div>

                            <!-- Details Grid & Actions -->
                            <div class="px-6 pb-6 grid grid-cols-2 sm:grid-cols-4 gap-y-6 sm:gap-y-4 gap-x-4">
                                <div>
                                    <div class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Date</div>
                                    <div class="text-[14px] font-bold text-slate-800 flex items-center gap-1.5 mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="#f15a24" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 3.5V14a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V3.5z"/></svg>
                                        {{ $reservation->date_voyage ? \Carbon\Carbon::parse($reservation->date_voyage)->translatedFormat('d F Y') : 'N/A' }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Heure de départ</div>
                                    <div class="text-[14px] font-bold text-slate-800 flex items-center gap-1.5 mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="#f15a24" viewBox="0 0 16 16"><path d="M8 3.5a.5.5 0 0 0-1 0V8a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 7.71z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/></svg>
                                        {{ $reservation->heure_depart ? \Carbon\Carbon::parse($reservation->heure_depart)->format('H:i') : ($programme->heure_depart ? \Carbon\Carbon::parse($programme->heure_depart)->format('H:i') : 'N/A') }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Sièges</div>
                                    <div class="text-[14px] font-bold text-slate-800 flex items-center gap-1.5 mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="#f15a24" viewBox="0 0 16 16"><path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/><path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/></svg>
                                        {{ $allSeats ?: 'N/A' }}
                                    </div>
                                </div>
                                <div class="row-span-2 flex flex-col justify-start">
                                    <div class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Passager</div>
                                    <div class="text-[14px] font-bold text-slate-800 mt-1">
                                        {{ $reservation->passager_nom_complet }}
                                    </div>
                                    @if($compagnie && $compagnie->path_logo)
                                    <div class="mt-auto pt-4">
                                        <img src="{{ asset('storage/' . $compagnie->path_logo) }}" alt="{{ $compagnie->name }}" class="h-10 md:h-12 w-auto object-contain max-w-[200px] rounded-xl shadow-sm border border-slate-100 p-1.5 bg-white bg-opacity-50">
                                    </div>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="col-span-2 sm:col-span-3 flex items-end mt-2 sm:mt-0">
                                    <div class="flex flex-wrap items-center gap-3 w-full">
                                    @if($isActive)
                                    @if($group->aller->count() > 1)
                                        @foreach($group->aller as $ticket)
                                        <a href="{{ route('home.reservations.download', $ticket->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-[#0e743a] text-[#0e743a] hover:bg-emerald-50 text-[13px] font-bold rounded-lg transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/></svg>
                                            Place {{ $ticket->seat_number }}
                                        </a>
                                        @endforeach
                                    @else
                                    <a href="{{ route('home.reservations.download', $reservation->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-[#0e743a] text-[#0e743a] hover:bg-emerald-50 text-[13px] font-bold rounded-lg transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/></svg>
                                        Télécharger
                                    </a>
                                    @endif
                                    <a href="https://wa.me/?text=Bonjour, j'ai besoin d'aide avec ma réservation {{ $reservation->reference }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-slate-200 text-slate-700 hover:bg-slate-50 text-[13px] font-bold rounded-lg transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="#25D366" viewBox="0 0 16 16"><path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.326-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/></svg>
                                        Support WhatsApp
                                    </a>
                                @else
                                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 border-2 border-slate-200 text-slate-400 text-[13px] font-bold rounded-lg cursor-not-allowed">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/></svg>
                                        Télécharger
                                    </span>
                                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 border-2 border-slate-200 text-slate-400 text-[13px] font-bold rounded-lg cursor-not-allowed">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.326-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/></svg>
                                        Support WhatsApp
                                    </span>
                                @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- === CARTE RETOUR === --}}
                    @if($group->retour->isNotEmpty())
                        @php
                            $retourFirst = $group->retour->first();
                            $retProgramme = $retourFirst->programme;
                            $retCompagnie = $retProgramme->compagnie;
                            $retGareDepart = $retProgramme->gareDepart;
                            $retGareArrivee = $retProgramme->gareArrivee;
                            $retNomGareDepart = $retGareDepart->nom ?? ($retProgramme->itineraire->point_depart ?? $retProgramme->point_depart ?? 'N/A');
                            $retNomGareArrivee = $retGareArrivee->nom ?? ($retProgramme->itineraire->point_arrive ?? $retProgramme->point_arrive ?? 'N/A');
                            $retIsActive = $retourFirst->statut === 'confirmee';
                            $retStatutLabel = match($retourFirst->statut) {
                                'confirmee' => ['label' => 'Confirmé', 'class' => 'bg-emerald-100 text-emerald-700 border-emerald-200'],
                                'terminee' => ['label' => 'Passé', 'class' => 'bg-slate-100 text-slate-600 border-slate-200'],
                                'annulee' => ['label' => 'Annulé', 'class' => 'bg-red-100 text-red-600 border-red-200'],
                                default => ['label' => ucfirst($retourFirst->statut), 'class' => 'bg-slate-100 text-slate-600 border-slate-200'],
                            };
                            $retAllSeats = $group->all_seats_retour;
                        @endphp
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow overflow-hidden {{ $group->aller->isNotEmpty() ? 'border-t-4 border-t-amber-400' : '' }}">
                            @if($group->aller->isNotEmpty())
                            <div class="px-6 pt-3 pb-0">
                                <span class="text-[11px] font-black uppercase tracking-widest text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">🔁 Retour</span>
                            </div>
                            @endif
                            <!-- Header -->
                            <div class="px-6 pt-4 pb-2 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-5">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <h3 class="text-lg font-extrabold text-[#111]">
                                        {{ $retProgramme->point_depart ?? 'N/A' }} → {{ $retProgramme->point_arrive ?? 'N/A' }}
                                    </h3>
                                    <span class="text-[10px] font-black tracking-wider px-2.5 py-1 rounded-full border {{ $retStatutLabel['class'] }}">
                                        {{ $retStatutLabel['label'] }}
                                    </span>
                                </div>
                                <div class="text-left sm:text-right mt-2 sm:mt-0 w-full sm:w-auto">
                                    <div class="text-xl font-black text-[#f15a24]">{{ number_format((float)$retourFirst->montant, 0, ',', ' ') }} F</div>
                                    <div class="text-[12px] font-semibold text-slate-500">{{ $retCompagnie->name ?? 'N/A' }}</div>
                                </div>
                            </div>

                            <!-- Gares -->
                            <div class="px-6 pb-2">
                                <div class="text-[12px] text-slate-500 font-medium flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#f15a24" viewBox="0 0 16 16"><path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/></svg>
                                    Gare: {{ $retNomGareDepart }} → {{ $retNomGareArrivee }}
                                </div>
                            </div>

                            <!-- Reference -->
                            <div class="px-6 pb-2">
                                <div class="text-[12px] text-slate-400 font-semibold">
                                    Billet # {{ $retourFirst->reference }}
                                    @if($group->retour->count() > 1)
                                        <span class="text-slate-300">→</span> {{ $group->retour->last()->reference }}
                                    @endif
                                </div>
                            </div>

                            <!-- Details Grid & Actions -->
                            <div class="px-6 pb-6 grid grid-cols-2 sm:grid-cols-4 gap-y-6 sm:gap-y-4 gap-x-4">
                                <div>
                                    <div class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Date</div>
                                    <div class="text-[14px] font-bold text-slate-800 flex items-center gap-1.5 mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="#f15a24" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 3.5V14a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V3.5z"/></svg>
                                        {{ $retourFirst->date_voyage ? \Carbon\Carbon::parse($retourFirst->date_voyage)->translatedFormat('d F Y') : 'N/A' }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Heure de départ</div>
                                    <div class="text-[14px] font-bold text-slate-800 flex items-center gap-1.5 mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="#f15a24" viewBox="0 0 16 16"><path d="M8 3.5a.5.5 0 0 0-1 0V8a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 7.71z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/></svg>
                                        {{ $retourFirst->heure_depart ? \Carbon\Carbon::parse($retourFirst->heure_depart)->format('H:i') : ($retProgramme->heure_depart ? \Carbon\Carbon::parse($retProgramme->heure_depart)->format('H:i') : 'N/A') }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Sièges</div>
                                    <div class="text-[14px] font-bold text-slate-800 flex items-center gap-1.5 mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="#f15a24" viewBox="0 0 16 16"><path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/><path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/></svg>
                                        {{ $retAllSeats ?: 'N/A' }}
                                    </div>
                                </div>
                                <div class="row-span-2 flex flex-col justify-start">
                                    <div class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Passager</div>
                                    <div class="text-[14px] font-bold text-slate-800 mt-1">
                                        {{ $retourFirst->passager_nom_complet }}
                                    </div>
                                    @if($retCompagnie && $retCompagnie->path_logo)
                                    <div class="mt-auto pt-4">
                                        <img src="{{ asset('storage/' . $retCompagnie->path_logo) }}" alt="{{ $retCompagnie->name }}" class="h-10 md:h-12 w-auto object-contain max-w-[200px] rounded-xl shadow-sm border border-slate-100 p-1.5 bg-white bg-opacity-50">
                                    </div>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="col-span-2 sm:col-span-3 flex items-end mt-2 sm:mt-0">
                                    <div class="flex flex-wrap items-center gap-3 w-full">
                                    @if($retIsActive)
                                    @if($group->retour->count() > 1)
                                        @foreach($group->retour as $ticket)
                                        <a href="{{ route('home.reservations.download', $ticket->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-[#0e743a] text-[#0e743a] hover:bg-emerald-50 text-[13px] font-bold rounded-lg transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/></svg>
                                            Place {{ $ticket->seat_number }}
                                        </a>
                                        @endforeach
                                    @else
                                    <a href="{{ route('home.reservations.download', $retourFirst->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-[#0e743a] text-[#0e743a] hover:bg-emerald-50 text-[13px] font-bold rounded-lg transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/></svg>
                                        Télécharger
                                    </a>
                                    @endif
                                    <a href="https://wa.me/?text=Bonjour, j'ai besoin d'aide avec ma réservation {{ $retourFirst->reference }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-slate-200 text-slate-700 hover:bg-slate-50 text-[13px] font-bold rounded-lg transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="#25D366" viewBox="0 0 16 16"><path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.326-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/></svg>
                                        Support WhatsApp
                                    </a>
                                @else
                                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 border-2 border-slate-200 text-slate-400 text-[13px] font-bold rounded-lg cursor-not-allowed">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/></svg>
                                        Télécharger
                                    </span>
                                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 border-2 border-slate-200 text-slate-400 text-[13px] font-bold rounded-lg cursor-not-allowed">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.326-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/></svg>
                                        Support WhatsApp
                                    </span>
                                @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</section>

<!-- Comment utiliser votre billet -->
<section class="bg-white py-16">
    <div class="container mx-auto px-4 max-w-[900px]">
        <div class="bg-[#fafafa] border border-slate-200 rounded-2xl p-8 md:p-10">
            <h3 class="text-xl font-extrabold text-slate-900 text-center mb-10">Comment utiliser votre billet ?</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-12 h-12 bg-[#f15a24] text-white rounded-full flex items-center justify-center mx-auto mb-4 text-lg font-black shadow-lg shadow-orange-200">1</div>
                    <h4 class="text-[15px] font-extrabold text-slate-800 mb-2">Téléchargez votre billet</h4>
                    <p class="text-[13px] text-slate-500">Cliquez sur "Télécharger" pour obtenir votre billet en PDF</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-[#f15a24] text-white rounded-full flex items-center justify-center mx-auto mb-4 text-lg font-black shadow-lg shadow-orange-200">2</div>
                    <h4 class="text-[15px] font-extrabold text-slate-800 mb-2">Présentez votre QR code</h4>
                    <p class="text-[13px] text-slate-500">Montrez le code QR au chauffeur jour du trajet</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-[#f15a24] text-white rounded-full flex items-center justify-center mx-auto mb-4 text-lg font-black shadow-lg shadow-orange-200">3</div>
                    <h4 class="text-[15px] font-extrabold text-slate-800 mb-2">Montez à bord</h4>
                    <p class="text-[13px] text-slate-500">Installez-vous et bon voyage ! Contactez nous si besoin</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Le formulaire se soumettra naturellement via le bouton de type submit
});
</script>

@endsection

@push('styles')
<style>
    body { padding-top: 0 !important; }
    ::placeholder { color: #9ca3af !important; font-weight: 500; }
</style>
@endpush

