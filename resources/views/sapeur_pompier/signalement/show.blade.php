@extends('sapeur_pompier.layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('sapeur-pompier.dashboard') }}"
                class="flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Retour au tableau de bord
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Signalement #{{ $signalement->id }}</h1>
        </div>

        <div
            class="bg-white rounded-2xl shadow-xl overflow-hidden {{ $signalement->statut == 'traite' ? 'border-2 border-green-500' : '' }}">
            <div
                class="p-6 {{ $signalement->type == 'accident' ? 'bg-red-50' : 'bg-gray-50' }} border-b border-gray-100 flex justify-between items-start">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span
                            class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase {{ $signalement->type == 'accident' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white' }}">
                            {{ $signalement->type }}
                        </span>
                        @if($signalement->statut == 'traite')
                            <span
                                class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase bg-green-500 text-white">
                                <i class="fas fa-check mr-1"></i> Traité
                            </span>
                        @endif
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">
                        Signalé le {{ $signalement->created_at->format('d/m/Y à H:i') }}
                    </h2>
                </div>
                <div class="text-right">
                    @php
                        $isChauffeur = $signalement->personnel_id && !$signalement->user_id;
                        $isCompagnie = $signalement->compagnie_id && !$signalement->user_id && !$signalement->personnel_id;
                    @endphp
                    <p class="text-xs text-gray-500 mb-1">Signalé par</p>
                    @if($isChauffeur && $signalement->personnel)
                        <p class="font-bold text-gray-900">{{ $signalement->personnel->name }} {{ $signalement->personnel->prenom ?? '' }}</p>
                        <p class="text-xs text-gray-400">{{ $signalement->personnel->contact ?? '' }}</p>
                        <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-full uppercase">
                            <i class="fas fa-id-badge text-[8px]"></i> Chauffeur
                        </span>
                    @elseif($signalement->compagnie)
                        <p class="font-bold text-gray-900">{{ $signalement->compagnie->name ?? 'Compagnie' }}</p>
                        <p class="text-xs text-gray-400">{{ $signalement->compagnie->email ?? '' }}</p>
                        <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-orange-100 text-orange-700 text-[10px] font-bold rounded-full uppercase">
                            <i class="fas fa-building text-[8px]"></i> Compagnie
                        </span>
                    @elseif($signalement->user)
                        <p class="font-bold text-gray-900">{{ $signalement->user->name ?? 'Utilisateur Inconnu' }}</p>
                        <p class="text-xs text-gray-400">{{ $signalement->user->telephone ?? $signalement->user->contact ?? '' }}</p>
                        <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-bold rounded-full uppercase">
                            <i class="fas fa-user text-[8px]"></i> Passager
                        </span>
                    @else
                        <p class="font-bold text-gray-900">Source inconnue</p>
                        <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-gray-100 text-gray-700 text-[10px] font-bold rounded-full uppercase">
                            <i class="fas fa-question text-[8px]"></i> Inconnu
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-8 space-y-8">
                <!-- Description -->
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Description</h3>
                    <p class="text-gray-700 text-lg leading-relaxed bg-gray-50 p-4 rounded-lg border border-gray-100">
                        {{ $signalement->description }}
                    </p>
                </div>

                <!-- Photo du Signalement -->
                @if($signalement->photo_path)
                    <div class="mb-6">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Photo du site</h3>
                        <div class="rounded-lg overflow-hidden shadow-lg border border-gray-200">
                            <img src="{{ asset($signalement->photo_path) }}" alt="Photo du signalement"
                                class="w-full h-auto max-h-[500px] object-cover"
                                onerror="this.onerror=null; this.src='{{ $signalement->photo_path }}';">
                        </div>
                    </div>
                @endif

                <!-- Localisation -->
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Localisation</h3>
                    @if($signalement->latitude && $signalement->longitude)
                        <div class="flex items-center gap-4">
                            <div class="flex-1 bg-gray-100 p-3 rounded-lg flex items-center gap-3">
                                <i class="fas fa-map-pin text-red-500 text-xl"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Lieu (Estimation)</p>
                                    <p class="font-bold text-gray-800" id="address-display">
                                        <i class="fas fa-spinner fa-spin text-blue-500"></i> Recherche de l'adresse...
                                    </p>
                                    <p class="font-mono text-xs text-gray-400 mt-1">{{ $signalement->latitude }},
                                        {{ $signalement->longitude }}
                                    </p>
                                </div>
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat={{ $signalement->latitude }}&lon={{ $signalement->longitude }}')
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data && data.display_name) {
                                                document.getElementById('address-display').innerHTML = '<i class="fas fa-map-marker-alt text-red-500 mr-2"></i>' + data.display_name;
                                            } else {
                                                document.getElementById('address-display').innerText = "Adresse introuvable";
                                            }
                                        })
                                        .catch(err => {
                                            console.error(err);
                                            document.getElementById('address-display').innerText = "Erreur chargement adresse";
                                        });
                                });
                            </script>
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $signalement->latitude }},{{ $signalement->longitude }}"
                                target="_blank"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg transition-all transform hover:-translate-y-1 flex items-center gap-2">
                                <i class="fas fa-location-arrow"></i> Ouvrir GPS
                            </a>
                        </div>
                    @else
                        <p class="text-gray-400 italic">Aucune donnée de géolocalisation disponible.</p>
                    @endif
                </div>

                <!-- Voyage Info (Si disponible) -->
                @if($signalement->programme)
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Détails du Voyage</h3>
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                            <div class="flex flex-col md:flex-row gap-4">
                                <div>
                                    <p class="text-xs text-blue-400 uppercase">Compagnie</p>
                                    <p class="font-bold text-blue-900">
                                        {{ $signalement->programme->compagnie->name ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-blue-400 uppercase">Trajet</p>
                                    <p class="font-bold text-blue-900">{{ $signalement->programme->point_depart }} <i
                                            class="fas fa-arrow-right mx-1 text-xs"></i>
                                        {{ $signalement->programme->point_arrive }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Info Véhicule Impliqué -->
                @if($signalement->programme && ($signalement->programme->vehicule || $signalement->vehicule_id))
                    @php
                        // Priorité au véhicule spécifique du signalement, sinon celui du programme
                        $vehicule = \App\Models\Vehicule::find($signalement->vehicule_id) ?? $signalement->programme->vehicule;
                    @endphp
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Véhicule Impliqué</h3>
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100 flex items-start gap-4">
                            <div class="bg-yellow-100 p-3 rounded-full">
                                <i class="fas fa-bus text-yellow-600 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-lg">{{ $vehicule->immatriculation ?? 'Inconnue' }}</h4>
                                <p class="text-sm text-gray-600">{{ $vehicule->marque ?? '' }} {{ $vehicule->modele ?? '' }}</p>
                                <p class="text-xs text-gray-400 mt-1">Compagnie:
                                    {{ $signalement->programme->compagnie->name ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des Passagers (Manifeste) -->
                    @php
                        $passengersList = collect();
                        $reservations = collect();

                        // Priorité au voyage_id pour une précision maximale (recommandé par l'utilisateur)
                        if ($signalement->voyage_id) {
                            $reservations = \App\Models\Reservation::where('voyage_id', $signalement->voyage_id)
                                ->whereIn('statut', ['confirmee', 'terminee'])
                                ->with('user')
                                ->get();
                        } elseif ($signalement->programme) {
                            // Fallback par programme et date d'accident
                            $dateVoyage = $signalement->created_at->format('Y-m-d');
                            $reservations = \App\Models\Reservation::where('programme_id', $signalement->programme->id)
                                ->whereDate('date_voyage', $dateVoyage)
                                ->whereIn('statut', ['confirmee', 'terminee'])
                                ->with('user')
                                ->get();
                        }

                        foreach ($reservations as $res) {
                            $name = trim(($res->passager_nom ?? '') . ' ' . ($res->passager_prenom ?? ''));
                            $contact = $res->passager_telephone ?? '-';
                            $seat = $res->seat_number ?? '?';
                            $urgence = $res->passager_urgence ?? $res->ice_contact ?? null; // Prise en compte de ice_contact s'il existe

                            $passengersList->push([
                                'name' => $name ?: ($res->user->name ?? 'Inconnu'),
                                'contact' => $contact,
                                'type' => 'Passager',
                                'seat' => $seat,
                                'ice_contact' => $urgence,
                                'ice_name' => 'Contact Urgence'
                            ]);
                        }
                    @endphp

                    <div class="mt-6 flex justify-between items-center bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div>
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">
                                <i class="fas fa-users text-gray-400 mr-2"></i> Passagers ({{ $passengersList->count() }})
                            </h3>
                            <p class="text-xs text-gray-500 mt-1">Cliquez pour voir la liste détaillée et les contacts
                                d'urgence.</p>
                        </div>
                        <button type="button" onclick="showPassengersList()"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-transform transform hover:-translate-y-0.5 flex items-center gap-2">
                            <i class="fas fa-list-alt"></i> Voir le Manifeste
                        </button>
                    </div>

                    <script>
                        function showPassengersList() {
                            const passengers = @json($passengersList);

                            if (passengers.length === 0) {
                                Swal.fire('Info', 'Aucun passager trouvé pour ce voyage.', 'info');
                                return;
                            }

                            let htmlContent = `
                                                        <div class="overflow-x-auto text-left">
                                                            <table class="w-full text-sm border-collapse">
                                                                <thead>
                                                                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs">
                                                                        <th class="p-2 border-b">Passager</th>
                                                                        <th class="p-2 border-b">Siège</th>
                                                                        <th class="p-2 border-b">Contact Urgence (ICE)</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                    `;

                            passengers.forEach(p => {
                                // Logique simplifiée : on affiche directement le contact d'urgence s'il existe
                                let iceInfo = '<span class="text-gray-400 italic">Non renseigné</span>';
                                if (p.ice_contact) {
                                    // p.ice_contact contient le nom et/ou le numéro
                                    iceInfo = `<div class="font-bold text-red-600">${p.ice_contact}</div><div class="text-xs text-gray-500">Contact Urgence</div>`;
                                }

                                htmlContent += `
                                                            <tr class="border-b hover:bg-gray-50">
                                                                <td class="p-2">
                                                                    <div class="font-bold text-gray-800">${p.name}</div>
                                                                    <div class="text-xs text-gray-500">${p.contact}</div>
                                                                </td>
                                                                <td class="p-2 font-mono font-bold text-center bg-gray-50">
                                                                    ${p.seat ? p.seat : '-'}
                                                                </td>
                                                                <td class="p-2">
                                                                    ${iceInfo}
                                                                </td>
                                                            </tr>
                                                        `;
                            });

                            htmlContent += `
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    `;

                            Swal.fire({
                                title: 'Manifeste des Passagers',
                                html: htmlContent,
                                width: '800px', // Plus large pour le tableau
                                showCloseButton: true,
                                showConfirmButton: false,
                                footer: '<p class="text-xs text-gray-400"><i class="fas fa-lock"></i> Données confidentielles - Usage strict sapeurs pompiers</p>'
                            });
                        }
                    </script>
                @endif

                <!-- Footer Actions -->
                <div class="bg-gray-50 p-6 border-t border-gray-100 flex justify-end gap-3">

                    @if($signalement->statut != 'traite')
                        <a href="{{ route('sapeur-pompier.signalement.bilan', $signalement->id) }}"
                           class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-6 py-2 rounded-lg font-medium shadow-sm transition-colors flex items-center gap-2">
                            <i class="fas fa-check"></i> Procéder au traitement
                        </a>
                    @else
                        <div class="flex flex-col items-end mr-4">
                            <button disabled
                                class="bg-green-100 text-green-700 px-6 py-2 rounded-lg font-medium cursor-default flex items-center gap-2 mb-1">
                                <i class="fas fa-check-circle"></i> Déjà traité
                            </button>
                            <div class="text-xs text-gray-500 text-right">
                                @if($signalement->nombre_morts > 0) <span class="text-red-600 font-bold">{{ $signalement->nombre_morts }} Mort(s)</span> • @endif
                                @if($signalement->nombre_blesses > 0) <span class="text-orange-600 font-bold">{{ $signalement->nombre_blesses }} Blessé(s)</span> • @endif
                                @if($signalement->bilan_passagers)
                                    @php
                                        $evacues = collect($signalement->bilan_passagers)->where('statut', 'evacue')->count();
                                        $indemnesCount = collect($signalement->bilan_passagers)->where('statut', 'indemne')->count();
                                    @endphp
                                    <span class="text-blue-600 font-bold">{{ $evacues }} Évacué(s)</span> •
                                    <span class="text-green-600 font-bold">{{ $indemnesCount }} Indemne(s)</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    <script>
                        const reservationsRaw = @json($reservations ?? collect());

                        function confirmTreatment() {
                            // Construction des lignes passagers
                            let passengerRows = '';
                            if (reservationsRaw.length > 0) {
                                reservationsRaw.forEach((res) => {
                                    const name = ((res.passager_nom || '') + ' ' + (res.passager_prenom || '')).trim() || (res.user ? res.user.name : 'Inconnu');
                                    const seat = res.seat_number || '?';
                                    const resId = res.id;

                                    passengerRows += `
                                    <div class="passenger-row" data-res-id="${resId}" data-statut="indemne"
                                        style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:14px 16px;margin-bottom:10px;transition:all .25s;">
                                        <div style="display:flex;align-items:center;gap:12px;">
                                            <div style="width:40px;height:40px;background:linear-gradient(135deg,#e0e7ff,#c7d2fe);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                                <span style="font-weight:800;font-size:13px;color:#4338ca;">${seat}</span>
                                            </div>
                                            <div style="flex:1;min-width:0;">
                                                <div style="font-weight:700;font-size:14px;color:#1e293b;">${name}</div>
                                                <div style="font-size:11px;color:#94a3b8;">Place n°${seat}</div>
                                            </div>
                                            <div style="display:flex;gap:6px;flex-shrink:0;">
                                                <button type="button" onclick="setPassengerStatus(${resId}, 'indemne', this)"
                                                    class="btn-indemne-${resId}"
                                                    style="padding:7px 14px;border-radius:10px;font-size:12px;font-weight:700;cursor:pointer;border:2px solid #22c55e;background:#f0fdf4;color:#16a34a;transition:all .2s;display:flex;align-items:center;gap:5px;">
                                                    <i class="fas fa-heart" style="font-size:10px;"></i> Indemne
                                                </button>
                                                <button type="button" onclick="setPassengerStatus(${resId}, 'evacue', this)"
                                                    class="btn-evacue-${resId}"
                                                    style="padding:7px 14px;border-radius:10px;font-size:12px;font-weight:700;cursor:pointer;border:2px solid #e2e8f0;background:#fff;color:#94a3b8;transition:all .2s;display:flex;align-items:center;gap:5px;">
                                                    <i class="fas fa-ambulance" style="font-size:10px;"></i> Évacué
                                                </button>
                                            </div>
                                        </div>
                                        <div id="hopital-fields-${resId}" style="display:none;margin-top:12px;padding-top:12px;border-top:1px dashed #fca5a5;">
                                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                                                <div>
                                                    <label style="display:block;font-size:10px;font-weight:700;color:#991b1b;text-transform:uppercase;margin-bottom:4px;letter-spacing:0.5px;">
                                                        <i class="fas fa-hospital" style="margin-right:3px;"></i> Hôpital d'évacuation
                                                    </label>
                                                    <input type="text" id="hopital-nom-${resId}" placeholder="Ex: CHU de Cocody"
                                                        style="width:100%;padding:8px 12px;border:1px solid #fca5a5;border-radius:10px;font-size:13px;background:#fff;">
                                                </div>
                                                <div>
                                                    <label style="display:block;font-size:10px;font-weight:700;color:#991b1b;text-transform:uppercase;margin-bottom:4px;letter-spacing:0.5px;">
                                                        <i class="fas fa-map-marker-alt" style="margin-right:3px;"></i> Adresse / Localisation
                                                    </label>
                                                    <input type="text" id="hopital-adresse-${resId}" placeholder="Ex: Cocody, Abidjan"
                                                        style="width:100%;padding:8px 12px;border:1px solid #fca5a5;border-radius:10px;font-size:13px;background:#fff;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                                });
                            }

                            const hasPassengers = reservationsRaw.length > 0;

                            Swal.fire({
                                title: '',
                                html: `
                                <div style="text-align:left;">
                                    {{-- En-tête --}}
                                    <div style="text-align:center;margin-bottom:20px;">
                                        <div style="width:56px;height:56px;background:linear-gradient(135deg,#fef2f2,#fee2e2);border-radius:16px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:10px;">
                                            <i class="fas fa-file-medical-alt" style="font-size:24px;color:#dc2626;"></i>
                                        </div>
                                        <h2 style="font-size:20px;font-weight:800;color:#1e293b;margin:0;">Bilan de l'intervention</h2>
                                        <p style="font-size:12px;color:#94a3b8;margin-top:4px;">Remplissez le bilan puis catégorisez chaque passager</p>
                                    </div>

                                    {{-- Section Bilan chiffré --}}
                                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:16px;margin-bottom:20px;">
                                        <div style="font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:1px;margin-bottom:12px;display:flex;align-items:center;gap:6px;">
                                            <i class="fas fa-chart-bar" style="color:#6366f1;"></i> Bilan chiffré
                                        </div>
                                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
                                            <div>
                                                <label style="display:block;font-size:10px;font-weight:700;color:#ef4444;text-transform:uppercase;margin-bottom:4px;">
                                                    <i class="fas fa-skull-crossbones" style="margin-right:3px;"></i> Mort(s)
                                                </label>
                                                <input type="number" id="swal-morts" value="0" min="0"
                                                    style="width:100%;padding:10px 14px;border:1px solid #e2e8f0;border-radius:10px;font-size:16px;font-weight:700;text-align:center;background:#fff;">
                                            </div>
                                            <div>
                                                <label style="display:block;font-size:10px;font-weight:700;color:#f59e0b;text-transform:uppercase;margin-bottom:4px;">
                                                    <i class="fas fa-user-injured" style="margin-right:3px;"></i> Blessé(s)
                                                </label>
                                                <input type="number" id="swal-blesses" value="0" min="0"
                                                    style="width:100%;padding:10px 14px;border:1px solid #e2e8f0;border-radius:10px;font-size:16px;font-weight:700;text-align:center;background:#fff;">
                                            </div>
                                        </div>
                                        <div>
                                            <label style="display:block;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:4px;">
                                                <i class="fas fa-pen" style="margin-right:3px;"></i> Rapport d'intervention
                                            </label>
                                            <textarea id="swal-details" rows="2" placeholder="Détails complémentaires..."
                                                style="width:100%;padding:10px 14px;border:1px solid #e2e8f0;border-radius:10px;font-size:13px;resize:vertical;background:#fff;"></textarea>
                                        </div>
                                    </div>

                                    ${hasPassengers ? `
                                    {{-- Section Passagers --}}
                                    <div style="margin-bottom:8px;">
                                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                                            <div style="font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:1px;display:flex;align-items:center;gap:6px;">
                                                <i class="fas fa-users" style="color:#3b82f6;"></i> État des passagers (${reservationsRaw.length})
                                            </div>
                                            <div style="display:flex;gap:12px;font-size:12px;font-weight:700;">
                                                <span id="count-indemnes" style="color:#16a34a;background:#f0fdf4;padding:3px 10px;border-radius:8px;">
                                                    <i class="fas fa-heart" style="font-size:9px;margin-right:3px;"></i> ${reservationsRaw.length} indemne(s)
                                                </span>
                                                <span id="count-evacues" style="color:#dc2626;background:#fef2f2;padding:3px 10px;border-radius:8px;">
                                                    <i class="fas fa-ambulance" style="font-size:9px;margin-right:3px;"></i> 0 évacué(s)
                                                </span>
                                            </div>
                                        </div>
                                        <div style="max-height:320px;overflow-y:auto;padding-right:4px;">
                                            ${passengerRows}
                                        </div>
                                    </div>
                                    ` : '<div style="text-align:center;padding:16px;background:#f8fafc;border-radius:12px;border:1px dashed #e2e8f0;color:#94a3b8;font-size:13px;margin-bottom:8px;"><i class="fas fa-info-circle" style="margin-right:6px;"></i>Aucun passager trouvé pour ce voyage</div>'}
                                </div>`,
                                width: '720px',
                                showCancelButton: true,
                                confirmButtonText: '<i class="fas fa-check-circle" style="margin-right:6px;"></i> Valider et Clôturer',
                                cancelButtonText: 'Annuler',
                                confirmButtonColor: '#10b981',
                                cancelButtonColor: '#94a3b8',
                                customClass: {
                                    popup: 'rounded-2xl',
                                    confirmButton: 'rounded-xl font-bold px-6 py-3',
                                    cancelButton: 'rounded-xl font-bold px-6 py-3',
                                },
                                preConfirm: () => {
                                    const morts = document.getElementById('swal-morts').value;
                                    const blesses = document.getElementById('swal-blesses').value;
                                    const details = document.getElementById('swal-details').value;

                                    if (morts === '' || blesses === '') {
                                        Swal.showValidationMessage('Renseignez le nombre de morts et de blessés (0 si aucun)');
                                        return false;
                                    }

                                    // Collecter les données passagers
                                    const passagers = [];
                                    let hasError = false;

                                    document.querySelectorAll('.passenger-row').forEach(row => {
                                        const resId = row.dataset.resId;
                                        const statut = row.dataset.statut || 'indemne';
                                        const entry = { reservation_id: resId, statut: statut };

                                        if (statut === 'evacue') {
                                            const hopNom = document.getElementById(`hopital-nom-${resId}`).value.trim();
                                            const hopAdresse = document.getElementById(`hopital-adresse-${resId}`).value.trim();
                                            if (!hopNom) {
                                                hasError = true;
                                                document.getElementById(`hopital-nom-${resId}`).style.borderColor = '#ef4444';
                                                document.getElementById(`hopital-nom-${resId}`).style.background = '#fff5f5';
                                            }
                                            entry.hopital_nom = hopNom;
                                            entry.hopital_adresse = hopAdresse;
                                        }
                                        passagers.push(entry);
                                    });

                                    if (hasError) {
                                        Swal.showValidationMessage('Précisez l\'hôpital pour chaque passager évacué.');
                                        return false;
                                    }

                                    return {
                                        morts, blesses, details, passagers
                                    };
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    const { morts, blesses, details, passagers } = result.value;
                                    submitBilan({ morts, blesses, details }, passagers);
                                }
                            });
                        }

                        function setPassengerStatus(resId, statut, btn) {
                            const row = btn.closest('.passenger-row');
                            row.dataset.statut = statut;

                            const btnIndemne = document.querySelector(`.btn-indemne-${resId}`);
                            const btnEvacue = document.querySelector(`.btn-evacue-${resId}`);
                            const hopitalFields = document.getElementById(`hopital-fields-${resId}`);

                            if (statut === 'evacue') {
                                btnEvacue.style.cssText = 'padding:7px 14px;border-radius:10px;font-size:12px;font-weight:700;cursor:pointer;border:2px solid #ef4444;background:#fef2f2;color:#dc2626;transition:all .2s;display:flex;align-items:center;gap:5px;';
                                btnIndemne.style.cssText = 'padding:7px 14px;border-radius:10px;font-size:12px;font-weight:700;cursor:pointer;border:2px solid #e2e8f0;background:#fff;color:#94a3b8;transition:all .2s;display:flex;align-items:center;gap:5px;';
                                hopitalFields.style.display = 'block';
                                row.style.border = '1px solid #fca5a5';
                                row.style.background = '#fffbfb';
                            } else {
                                btnIndemne.style.cssText = 'padding:7px 14px;border-radius:10px;font-size:12px;font-weight:700;cursor:pointer;border:2px solid #22c55e;background:#f0fdf4;color:#16a34a;transition:all .2s;display:flex;align-items:center;gap:5px;';
                                btnEvacue.style.cssText = 'padding:7px 14px;border-radius:10px;font-size:12px;font-weight:700;cursor:pointer;border:2px solid #e2e8f0;background:#fff;color:#94a3b8;transition:all .2s;display:flex;align-items:center;gap:5px;';
                                hopitalFields.style.display = 'none';
                                row.style.border = '1px solid #e2e8f0';
                                row.style.background = '#fff';
                            }

                            updateCounts();
                        }

                        function updateCounts() {
                            const allRows = document.querySelectorAll('.passenger-row');
                            let evacues = 0, indemnes = 0;
                            allRows.forEach(r => {
                                if (r.dataset.statut === 'evacue') evacues++;
                                else indemnes++;
                            });
                            const elIndemnes = document.getElementById('count-indemnes');
                            const elEvacues = document.getElementById('count-evacues');
                            if (elIndemnes) elIndemnes.innerHTML = `<i class="fas fa-heart" style="font-size:9px;margin-right:3px;"></i> ${indemnes} indemne(s)`;
                            if (elEvacues) elEvacues.innerHTML = `<i class="fas fa-ambulance" style="font-size:9px;margin-right:3px;"></i> ${evacues} évacué(s)`;
                        }

                        function submitBilan(bilanGeneral, passagers) {
                            document.getElementById('input-nombre-morts').value = bilanGeneral.morts;
                            document.getElementById('input-nombre-blesses').value = bilanGeneral.blesses;
                            document.getElementById('input-details-intervention').value = bilanGeneral.details;

                            const container = document.getElementById('dynamic-hidden-inputs');
                            container.innerHTML = '';

                            passagers.forEach((p) => {
                                const addField = (key, val) => {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = `passagers[${p.reservation_id}][${key}]`;
                                    input.value = val || '';
                                    container.appendChild(input);
                                };
                                addField('statut', p.statut);
                                if (p.statut === 'evacue') {
                                    addField('hopital_nom', p.hopital_nom);
                                    addField('hopital_adresse', p.hopital_adresse);
                                }
                            });

                            document.getElementById('mark-treated-form').submit();
                        }
                    </script>

                    <a href="tel:180"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-bold shadow transition-colors flex items-center gap-2">
                        <i class="fas fa-phone"></i> Appeler Renforts
                    </a>
                </div>
            </div>
        </div>
@endsection