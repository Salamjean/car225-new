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
                        <form id="mark-treated-form"
                            action="{{ route('sapeur-pompier.signalement.mark-as-treated', $signalement->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="nombre_morts" id="input-nombre-morts">
                            <input type="hidden" name="nombre_blesses" id="input-nombre-blesses">
                            <input type="hidden" name="details_intervention" id="input-details-intervention">

                            <button type="button" onclick="confirmTreatment()"
                                class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-6 py-2 rounded-lg font-medium shadow-sm transition-colors flex items-center gap-2">
                                <i class="fas fa-check"></i> Marquer comme traité
                            </button>
                        </form>
                    @else
                        <div class="flex flex-col items-end mr-4">
                            <button disabled
                                class="bg-green-100 text-green-700 px-6 py-2 rounded-lg font-medium cursor-default flex items-center gap-2 mb-1">
                                <i class="fas fa-check-circle"></i> Déjà traité
                            </button>
                            <div class="text-xs text-gray-500 text-right">
                                @if($signalement->nombre_morts > 0) <span
                                class="text-red-600 font-bold">{{ $signalement->nombre_morts }} Mort(s)</span> • @endif
                                @if($signalement->nombre_blesses > 0) <span
                                class="text-orange-600 font-bold">{{ $signalement->nombre_blesses }} Blessé(s)</span> @endif
                            </div>
                        </div>
                    @endif

                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    <script>
                        function confirmTreatment() {
                            Swal.fire({
                                title: 'Bilan de l\'intervention',
                                html: `
                                                <div class="text-left">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de mort(s)</label>
                                                    <input type="number" id="swal-morts" class="swal2-input m-0 mb-4 w-full" placeholder="0" min="0" value="0">

                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de blessé(s)</label>
                                                    <input type="number" id="swal-blesses" class="swal2-input m-0 mb-4 w-full" placeholder="0" min="0" value="0">

                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Autres informations / Rapport</label>
                                                    <textarea id="swal-details" class="swal2-textarea m-0 w-full" placeholder="Détails sur l'intervention..."></textarea>
                                                </div>
                                            `,
                                showCancelButton: true,
                                confirmButtonText: 'Valider et Clôturer',
                                cancelButtonText: 'Annuler',
                                confirmButtonColor: '#10b981',
                                cancelButtonColor: '#6b7280',
                                preConfirm: () => {
                                    const morts = document.getElementById('swal-morts').value;
                                    const blesses = document.getElementById('swal-blesses').value;
                                    const details = document.getElementById('swal-details').value;

                                    if (!morts || !blesses) {
                                        Swal.showValidationMessage('Veuillez renseigner les chiffres (mettez 0 si aucun)');
                                        return false;
                                    }

                                    return { morts: morts, blesses: blesses, details: details };
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    document.getElementById('input-nombre-morts').value = result.value.morts;
                                    document.getElementById('input-nombre-blesses').value = result.value.blesses;
                                    document.getElementById('input-details-intervention').value = result.value.details;

                                    document.getElementById('mark-treated-form').submit();
                                }
                            });
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