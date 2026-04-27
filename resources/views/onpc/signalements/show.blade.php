@extends('onpc.layouts.app')

@section('title', 'Signalement #' . $signalement->id)

@section('content')
    <a href="{{ route('onpc.signalements.index') }}" class="text-sm text-blue-700 hover:underline mb-4 inline-flex items-center gap-1">
        <i class="fas fa-arrow-left"></i> Tous les signalements
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900 capitalize flex items-center gap-3">
                    <span class="w-12 h-12 rounded-full {{ $signalement->type === 'accident' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700' }} flex items-center justify-center">
                        <i class="fas {{ $signalement->type === 'accident' ? 'fa-car-crash' : 'fa-exclamation-triangle' }}"></i>
                    </span>
                    {{ $signalement->type }} — #{{ $signalement->id }}
                </h1>
                <p class="text-sm text-gray-500 mt-2">Créé le {{ $signalement->created_at->format('d/m/Y à H:i') }}</p>
            </div>
            <div>
                <span class="px-3 py-1.5 rounded-full text-sm font-bold {{ $signalement->statut === 'traite' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ ucfirst($signalement->statut) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="font-bold text-gray-900 mb-2"><i class="fas fa-fire-extinguisher text-red-500 mr-1"></i> Caserne</h3>
                @if($signalement->sapeurPompier)
                    <a href="{{ route('onpc.sapeurs.show', $signalement->sapeurPompier->id) }}" class="block bg-gray-50 rounded-xl p-4 hover:bg-gray-100">
                        <div class="font-bold text-gray-900">{{ $signalement->sapeurPompier->name }}</div>
                        <div class="text-sm text-gray-500">{{ $signalement->sapeurPompier->commune }} — {{ $signalement->sapeurPompier->adresse }}</div>
                        <div class="text-xs text-gray-500 mt-1"><i class="fas fa-phone"></i> {{ $signalement->sapeurPompier->contact }}</div>
                    </a>
                @else <p class="text-gray-500">Aucune caserne assignée.</p> @endif
            </div>

            <div>
                <h3 class="font-bold text-gray-900 mb-2"><i class="fas fa-building text-blue-700 mr-1"></i> Compagnie</h3>
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="font-bold text-gray-900">{{ optional($signalement->compagnie)->name ?? '—' }}</div>
                </div>
            </div>

            <div>
                <h3 class="font-bold text-gray-900 mb-2"><i class="fas fa-route text-purple-700 mr-1"></i> Trajet</h3>
                <div class="bg-gray-50 rounded-xl p-4 text-sm">
                    @if($signalement->voyage && $signalement->voyage->programme)
                        <div>{{ $signalement->voyage->programme->point_depart }} → {{ $signalement->voyage->programme->point_arrive }}</div>
                        <div class="text-xs text-gray-500">Voyage #{{ $signalement->voyage_id }} — {{ $signalement->voyage->date_voyage ?? '' }}</div>
                    @elseif($signalement->convoi && $signalement->convoi->itineraire)
                        <div>{{ $signalement->convoi->itineraire->point_depart }} → {{ $signalement->convoi->itineraire->point_arrive }}</div>
                        <div class="text-xs text-gray-500">Convoi {{ $signalement->convoi->reference ?? '#' . $signalement->convoi_id }}</div>
                    @else — @endif
                </div>
            </div>

            <div>
                <h3 class="font-bold text-gray-900 mb-2"><i class="fas fa-id-card text-emerald-700 mr-1"></i> Reportage</h3>
                <div class="bg-gray-50 rounded-xl p-4 text-sm space-y-1">
                    @if($signalement->personnel)
                        <div><span class="text-gray-500">Chauffeur :</span> <strong>{{ $signalement->personnel->prenom }} {{ $signalement->personnel->name }}</strong></div>
                    @endif
                    @if($signalement->vehicule)
                        <div><span class="text-gray-500">Véhicule :</span> {{ $signalement->vehicule->immatriculation }}</div>
                    @endif
                    @if($signalement->latitude && $signalement->longitude)
                        <div class="text-xs text-gray-500">
                            GPS : {{ number_format($signalement->latitude, 6) }}, {{ number_format($signalement->longitude, 6) }}
                            <a target="_blank" href="https://www.google.com/maps?q={{ $signalement->latitude }},{{ $signalement->longitude }}"
                                class="text-blue-700 hover:underline ml-1"><i class="fas fa-external-link-alt"></i> Voir sur la carte</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6">
            <h3 class="font-bold text-gray-900 mb-2"><i class="fas fa-align-left text-gray-700 mr-1"></i> Description</h3>
            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded text-gray-700 italic whitespace-pre-line">{{ $signalement->description }}</div>
        </div>

        @if($signalement->photo_path)
            <div class="mt-6">
                <h3 class="font-bold text-gray-900 mb-2"><i class="fas fa-camera text-rose-500 mr-1"></i> Photo</h3>
                <img src="{{ asset($signalement->photo_path) }}" alt="Photo signalement" class="rounded-xl max-h-96 border border-gray-200">
            </div>
        @endif

        @if($signalement->details_intervention)
            <div class="mt-6">
                <h3 class="font-bold text-gray-900 mb-2"><i class="fas fa-clipboard-check text-emerald-700 mr-1"></i> Compte-rendu d'intervention</h3>
                <div class="bg-emerald-50 border-l-4 border-emerald-400 p-4 rounded text-gray-700 whitespace-pre-line">{{ $signalement->details_intervention }}</div>
            </div>
        @endif

        @if($signalement->statut === 'traite')
            <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-rose-50 rounded-xl p-4 text-center">
                    <div class="text-2xl font-black text-rose-700">{{ $signalement->nombre_morts ?? 0 }}</div>
                    <div class="text-xs uppercase text-gray-500 font-semibold mt-1">Morts</div>
                </div>
                <div class="bg-orange-50 rounded-xl p-4 text-center">
                    <div class="text-2xl font-black text-orange-700">{{ $signalement->nombre_blesses ?? 0 }}</div>
                    <div class="text-xs uppercase text-gray-500 font-semibold mt-1">Blessés</div>
                </div>
                @php
                    $bil = $bilanDetailed ?? [];
                    $evac = collect($bil)->where('statut', 'evacue')->count();
                    $indemnes = collect($bil)->where('statut', 'indemne')->count();
                @endphp
                <div class="bg-teal-50 rounded-xl p-4 text-center">
                    <div class="text-2xl font-black text-teal-700">{{ $evac }}</div>
                    <div class="text-xs uppercase text-gray-500 font-semibold mt-1">Évacués</div>
                </div>
                <div class="bg-emerald-50 rounded-xl p-4 text-center">
                    <div class="text-2xl font-black text-emerald-700">{{ $indemnes }}</div>
                    <div class="text-xs uppercase text-gray-500 font-semibold mt-1">Indemnes</div>
                </div>
            </div>

            @if(!empty($bilanDetailed))
                <div class="mt-6">
                    <h3 class="font-bold text-gray-900 mb-3"><i class="fas fa-users text-blue-700 mr-1"></i> Bilan passagers</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($bilanDetailed as $row)
                            @php
                                $colorMap = ['indemne' => 'emerald', 'evacue' => 'teal', 'blesse' => 'orange', 'mort' => 'rose'];
                                $col = $colorMap[$row['statut'] ?? ''] ?? 'gray';
                                $statutIcon = [
                                    'indemne' => 'fa-shield-alt',
                                    'evacue'  => 'fa-ambulance',
                                    'blesse'  => 'fa-user-injured',
                                    'mort'    => 'fa-skull-crossbones',
                                ][$row['statut'] ?? ''] ?? 'fa-user';
                            @endphp
                            <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
                                <div class="flex items-start gap-3">
                                    {{-- Avatar passager --}}
                                    @if(!empty($row['photo_url']))
                                        <img src="{{ $row['photo_url'] }}" alt="{{ $row['nom_passager'] }}"
                                            class="w-16 h-16 rounded-full object-cover ring-2 ring-{{ $col }}-100 shrink-0">
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center font-black text-lg shrink-0">
                                            {{ $row['photo_initials'] ?? '?' }}
                                        </div>
                                    @endif

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2 mb-1">
                                            <h4 class="font-bold text-gray-900 truncate">{{ $row['nom_passager'] ?? '—' }}</h4>
                                            <span class="shrink-0 inline-flex items-center gap-1 px-2 py-1 rounded-full text-[11px] font-bold bg-{{ $col }}-100 text-{{ $col }}-700">
                                                <i class="fas {{ $statutIcon }}"></i>
                                                {{ ucfirst($row['statut'] ?? '—') }}
                                            </span>
                                        </div>

                                        {{-- Sous-titre infos identité --}}
                                        <div class="flex flex-wrap gap-x-3 gap-y-0.5 text-xs text-gray-600 mb-2">
                                            @if(!empty($row['age']))
                                                <span><i class="fas fa-birthday-cake text-pink-500 mr-1"></i>{{ $row['age'] }} ans</span>
                                            @endif
                                            @if(!empty($row['genre']))
                                                <span class="capitalize"><i class="fas {{ $row['genre'] === 'femme' ? 'fa-venus' : ($row['genre'] === 'homme' ? 'fa-mars' : 'fa-genderless') }} mr-1"></i>{{ $row['genre'] }}</span>
                                            @endif
                                            @if(!empty($row['has_account']))
                                                <span class="text-blue-700"><i class="fas fa-id-badge mr-1"></i>Compte CAR225</span>
                                            @endif
                                        </div>

                                        {{-- Détails dépliables --}}
                                        <dl class="text-xs space-y-1">
                                            @if(!empty($row['date_naissance']))
                                                <div class="flex"><dt class="w-32 text-gray-500">Date de naissance</dt><dd class="text-gray-900 font-medium">{{ \Carbon\Carbon::parse($row['date_naissance'])->format('d/m/Y') }}</dd></div>
                                            @endif
                                            @if(!empty($row['piece_identite']))
                                                <div class="flex"><dt class="w-32 text-gray-500">Pièce d'identité</dt><dd class="text-gray-900 font-medium">{{ $row['piece_identite'] }}</dd></div>
                                            @endif
                                            @if(!empty($row['email']))
                                                <div class="flex"><dt class="w-32 text-gray-500">Email</dt><dd class="text-gray-900 font-medium truncate">{{ $row['email'] }}</dd></div>
                                            @endif
                                            @if(!empty($row['contact']))
                                                <div class="flex"><dt class="w-32 text-gray-500">Téléphone</dt><dd class="text-gray-900 font-medium">{{ $row['contact'] }}</dd></div>
                                            @endif
                                            @if(!empty($row['contact_urgence']))
                                                <div class="flex"><dt class="w-32 text-gray-500">Contact urgence</dt><dd class="text-rose-700 font-bold">{{ $row['contact_urgence'] }}@if(!empty($row['nom_urgence']))<span class="text-gray-500 font-normal"> — {{ $row['nom_urgence'] }}</span>@endif</dd></div>
                                            @endif
                                            @if(!empty($row['seat_number']))
                                                <div class="flex"><dt class="w-32 text-gray-500">Place</dt><dd class="text-gray-900 font-medium">N°{{ $row['seat_number'] }}</dd></div>
                                            @endif
                                            @if(!empty($row['reference']))
                                                <div class="flex"><dt class="w-32 text-gray-500">Réservation</dt><dd class="text-gray-900 font-mono text-[11px]">{{ $row['reference'] }}</dd></div>
                                            @endif
                                        </dl>

                                        {{-- Encart hôpital pour évacués --}}
                                        @if(($row['statut'] ?? null) === 'evacue')
                                            <div class="mt-3 bg-teal-50 border-l-4 border-teal-500 rounded p-2 text-xs">
                                                <div class="font-bold text-teal-800"><i class="fas fa-hospital-symbol mr-1"></i>{{ $row['hopital_nom'] ?? '—' }}</div>
                                                @if(!empty($row['hopital_adresse']))
                                                    <div class="text-teal-700 mt-0.5">{{ $row['hopital_adresse'] }}</div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>
@endsection
