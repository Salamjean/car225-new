@extends('gare-espace.layouts.template')

@section('title', 'Mon Profil Gare')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50/30 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <!-- En-tête -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Gestion du Profil Gare</h1>
            <p class="mt-2 text-lg text-gray-600">Consultez et modifiez les informations de votre établissement</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Colonne de gauche : Aperçu -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 sticky top-24">
                    <div class="h-32 bg-gradient-to-r from-orange-500 to-red-600"></div>
                    <div class="px-6 pb-8">
                        <div class="relative -mt-16 mb-6 text-center">
                            @if($gare->profile_image)
                                <img src="{{ asset('storage/' . $gare->profile_image) }}" 
                                     class="w-32 h-32 rounded-2xl object-cover mx-auto border-4 border-white shadow-lg" 
                                     alt="Logo Gare">
                            @else
                                <div class="w-32 h-32 rounded-2xl mx-auto border-4 border-white bg-gradient-to-br from-orange-100 to-orange-200 flex items-center justify-center shadow-lg">
                                    <i class="fas fa-warehouse text-4xl text-orange-500"></i>
                                </div>
                            @endif
                            <div class="absolute bottom-1 right-1/2 translate-x-12 translate-y-1 bg-green-500 text-white p-1.5 rounded-lg border-2 border-white shadow-sm">
                                <i class="fas fa-check text-[10px]"></i>
                            </div>
                        </div>

                        <div class="text-center mb-8">
                            <h3 class="text-xl font-bold text-gray-900">{{ $gare->nom_gare }}</h3>
                            <p class="text-sm font-medium text-orange-600">{{ $gare->ville }}</p>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-2xl">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Compagnie</p>
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $gare->compagnie->name ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-2xl">
                                <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center shrink-0">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Responsable</p>
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $gare->responsable_prenom }} {{ $gare->responsable_nom }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne de droite : Formulaires -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Informations Générales -->
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center">
                                <i class="fas fa-edit text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Informations de la Gare</h2>
                                <p class="text-sm text-gray-500">Mettez à jour les détails de votre point de vente</p>
                            </div>
                        </div>

                        <form action="{{ route('gare-espace.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Nom de la Gare *</label>
                                    <input type="text" name="nom_gare" value="{{ old('nom_gare', $gare->nom_gare) }}" required
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-orange-500 focus:bg-white transition-all outline-none">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Ville *</label>
                                    <input type="text" name="ville" value="{{ old('ville', $gare->ville) }}" readonly
                                        class="w-full px-4 py-3 bg-gray-100 border-2 border-transparent rounded-2xl text-gray-500 cursor-not-allowed">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Nom du Responsable *</label>
                                    <input type="text" name="responsable_nom" value="{{ old('responsable_nom', $gare->responsable_nom) }}" required
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-orange-500 focus:bg-white transition-all outline-none">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Prénom du Responsable *</label>
                                    <input type="text" name="responsable_prenom" value="{{ old('responsable_prenom', $gare->responsable_prenom) }}" required
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-orange-500 focus:bg-white transition-all outline-none">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Contact Gare *</label>
                                    <input type="text" name="contact" value="{{ old('contact', $gare->contact) }}" required
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-orange-500 focus:bg-white transition-all outline-none">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Contact Urgence</label>
                                    <input type="text" name="contact_urgence" value="{{ old('contact_urgence', $gare->contact_urgence) }}"
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-orange-500 focus:bg-white transition-all outline-none">
                                </div>

                                <div class="md:col-span-2 space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Adresse complète</label>
                                    <textarea name="adresse" rows="3"
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-orange-500 focus:bg-white transition-all outline-none">{{ old('adresse', $gare->adresse) }}</textarea>
                                </div>

                                <div class="md:col-span-2 space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Logo / Image de la Gare</label>
                                    <input type="file" name="profile_image" accept="image/*"
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-dashed border-gray-300 rounded-2xl hover:border-orange-500 transition-all cursor-pointer">
                                </div>
                            </div>

                            <div class="pt-4">
                                <button type="submit" 
                                    class="w-full py-4 bg-orange-600 text-white font-bold rounded-2xl hover:bg-orange-700 shadow-lg hover:shadow-orange-500/30 transition-all transform hover:-translate-y-0.5">
                                    Sauvegarder les modifications
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Section Localisation GPS -->
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="p-8">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center">
                                <i class="fas fa-map-pin text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h2 class="text-xl font-bold text-gray-900">Localisation GPS de la Gare</h2>
                                <p class="text-sm text-gray-500">Nécessaire pour le tracé de route sur la carte de suivi</p>
                            </div>
                            @if(isset($locationRequests) && $locationRequests->count() > 0)
                            <button type="button" onclick="openGPSHistoryModal()"
                                class="flex items-center gap-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-800 rounded-xl transition-all text-xs font-bold"
                                title="Voir l'historique des demandes GPS">
                                <i class="fas fa-history"></i>
                                <span class="hidden sm:inline">Historique</span>
                                <span class="bg-gray-400 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $locationRequests->count() }}</span>
                            </button>
                            @endif
                        </div>

                        <!-- Statut demande en attente -->
                        @if(isset($pendingRequest) && $pendingRequest)
                        <div class="flex items-start gap-4 p-4 bg-blue-50 border border-blue-200 rounded-2xl mb-6">
                            <i class="fas fa-clock text-blue-500 text-xl flex-shrink-0 mt-0.5"></i>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-blue-800">Demande en attente d'approbation</p>
                                <p class="text-xs text-blue-600 mt-0.5 font-mono">
                                    Lat : {{ number_format($pendingRequest->latitude, 6) }} | Lng : {{ number_format($pendingRequest->longitude, 6) }}
                                </p>
                                <p class="text-xs text-blue-500 mt-1">Envoyée le {{ $pendingRequest->created_at->format('d/m/Y à H:i') }} — en attente de validation par votre compagnie.</p>
                            </div>
                        </div>
                        @elseif($gare->latitude && $gare->longitude)
                        <div class="flex items-center gap-4 p-4 bg-green-50 border border-green-200 rounded-2xl mb-6">
                            <i class="fas fa-check-circle text-green-500 text-xl flex-shrink-0"></i>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-green-800">Position enregistrée</p>
                                <p class="text-xs text-green-600 font-mono mt-0.5">
                                    Lat : {{ number_format($gare->latitude, 6) }} &nbsp;|&nbsp; Lng : {{ number_format($gare->longitude, 6) }}
                                </p>
                            </div>
                        </div>
                        @else
                        <div class="flex items-center gap-4 p-4 bg-amber-50 border border-amber-200 rounded-2xl mb-6">
                            <i class="fas fa-exclamation-triangle text-amber-500 text-xl flex-shrink-0"></i>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-amber-800">Aucune position enregistrée</p>
                                <p class="text-xs text-amber-600 mt-0.5">Cliquez sur le bouton ci-dessous pour soumettre une demande à votre compagnie.</p>
                            </div>
                        </div>
                        @endif

                        <p class="text-xs text-gray-400 mb-4">
                            <i class="fas fa-info-circle mr-1"></i>
                            La mise à jour nécessite votre mot de passe et sera soumise à l'approbation de votre compagnie.
                        </p>

                        <button type="button" onclick="openGPSPasswordModal()" id="gps-profile-btn"
                            class="w-full py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-2xl shadow-lg hover:shadow-green-500/30 transition-all flex items-center justify-center gap-3 {{ (isset($pendingRequest) && $pendingRequest) ? 'opacity-60 cursor-not-allowed' : '' }}"
                            {{ (isset($pendingRequest) && $pendingRequest) ? 'disabled' : '' }}>
                            <i class="fas fa-crosshairs text-lg"></i>
                            <span>{{ (isset($pendingRequest) && $pendingRequest) ? 'Demande en cours...' : 'Mettre à jour la position GPS' }}</span>
                        </button>
                    </div>
                </div>

                <!-- Section Sécurité -->
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                                <i class="fas fa-lock text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Mot de passe</h2>
                                <p class="text-sm text-gray-500">Sécurisez votre compte gare</p>
                            </div>
                        </div>

                        <form action="{{ route('gare-espace.profile.password') }}" method="POST" class="space-y-6">
                            @csrf
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Mot de passe actuel *</label>
                                <input type="password" name="current_password" required
                                    class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-500 focus:bg-white transition-all outline-none">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Nouveau mot de passe *</label>
                                    <input type="password" name="new_password" required
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-500 focus:bg-white transition-all outline-none">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Confirmer le nouveau mot de passe *</label>
                                    <input type="password" name="new_password_confirmation" required
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-500 focus:bg-white transition-all outline-none">
                                </div>
                            </div>

                            <div class="pt-4">
                                <button type="submit" 
                                    class="w-full py-4 bg-gray-900 text-white font-bold rounded-2xl hover:bg-gray-800 shadow-lg transition-all transform hover:-translate-y-0.5">
                                    Mettre à jour le mot de passe
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const GPS_REQUEST_URL  = "{{ route('gare-espace.profile.requestLocation') }}";
const CSRF_TOKEN_PROF  = "{{ csrf_token() }}";

function openGPSPasswordModal() {
    if (!navigator.geolocation) {
        Swal.fire({ icon: 'error', title: 'Non supporté', text: 'Votre navigateur ne prend pas en charge la géolocalisation.', confirmButtonColor: '#16a34a' });
        return;
    }

    Swal.fire({
        title: 'Confirmation requise',
        html: `
            <p class="text-sm text-gray-600 mb-4">Pour des raisons de sécurité, entrez votre mot de passe pour soumettre la demande de mise à jour GPS à votre compagnie.</p>
            <input id="swal-pwd" type="password" class="swal2-input" placeholder="Votre mot de passe">
        `,
        icon: 'lock',
        confirmButtonText: 'Continuer',
        confirmButtonColor: '#16a34a',
        showCancelButton: true,
        cancelButtonText: 'Annuler',
        focusConfirm: false,
        preConfirm: () => {
            const pwd = document.getElementById('swal-pwd').value;
            if (!pwd) {
                Swal.showValidationMessage('Veuillez entrer votre mot de passe.');
                return false;
            }
            return pwd;
        }
    }).then(result => {
        if (!result.isConfirmed) return;
        const password = result.value;

        // Step 2: capture GPS
        Swal.fire({
            title: 'Localisation en cours...',
            text: 'Veuillez patienter pendant la capture de votre position GPS.',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        navigator.geolocation.getCurrentPosition(
            pos => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;

                fetch(GPS_REQUEST_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN_PROF },
                    body: JSON.stringify({ password, latitude: lat, longitude: lng })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Demande envoyée !',
                            html: `<p class="text-sm text-gray-600">${data.message}</p>
                                   <p class="font-mono text-xs text-gray-400 mt-2">Lat : ${lat.toFixed(6)} | Lng : ${lng.toFixed(6)}</p>`,
                            confirmButtonColor: '#16a34a',
                        }).then(() => window.location.reload());
                    } else {
                        Swal.fire({ icon: 'error', title: 'Erreur', text: data.message, confirmButtonColor: '#d33' });
                    }
                })
                .catch(() => {
                    Swal.fire({ icon: 'error', title: 'Erreur réseau', text: 'Impossible d\'envoyer la demande. Veuillez réessayer.', confirmButtonColor: '#d33' });
                });
            },
            () => {
                Swal.fire({
                    icon: 'warning', title: 'Accès refusé',
                    text: 'Veuillez autoriser la géolocalisation dans votre navigateur, puis réessayez.',
                    confirmButtonColor: '#16a34a'
                });
            },
            { enableHighAccuracy: true, timeout: 12000 }
        );
    });
}
</script>

<!-- Modal Historique GPS -->
@if(isset($locationRequests) && $locationRequests->count() > 0)
<div id="gps-history-modal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeGPSHistoryModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[80vh] flex flex-col">
            <!-- Header -->
            <div class="flex items-center gap-4 p-6 border-b border-gray-100">
                <div class="w-10 h-10 rounded-xl bg-gray-100 text-gray-500 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-history"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900">Historique des demandes GPS</h3>
                    <p class="text-xs text-gray-500">{{ $locationRequests->count() }} demande(s) au total</p>
                </div>
                <button onclick="closeGPSHistoryModal()" class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 transition-all">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <!-- Body -->
            <div class="overflow-y-auto p-6 space-y-3">
                @foreach($locationRequests as $req)
                <div class="flex items-start gap-3 p-4 rounded-2xl border
                    {{ $req->statut === 'approved' ? 'bg-green-50 border-green-200' : ($req->statut === 'rejected' ? 'bg-red-50 border-red-200' : 'bg-blue-50 border-blue-200') }}">
                    <div class="flex-shrink-0 mt-0.5">
                        @if($req->statut === 'approved')
                            <i class="fas fa-check-circle text-green-500"></i>
                        @elseif($req->statut === 'rejected')
                            <i class="fas fa-times-circle text-red-500"></i>
                        @else
                            <i class="fas fa-clock text-blue-500"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-mono text-gray-600">{{ number_format($req->latitude, 5) }}, {{ number_format($req->longitude, 5) }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                {{ $req->statut === 'approved' ? 'bg-green-200 text-green-800' : ($req->statut === 'rejected' ? 'bg-red-200 text-red-800' : 'bg-blue-200 text-blue-800') }}">
                                {{ $req->statut === 'approved' ? 'Approuvée' : ($req->statut === 'rejected' ? 'Rejetée' : 'En attente') }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $req->created_at->format('d/m/Y à H:i') }}</p>
                        @if($req->statut === 'rejected' && $req->rejected_reason)
                            <p class="text-xs text-red-500 mt-0.5 italic">{{ $req->rejected_reason }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<script>
function openGPSHistoryModal() {
    document.getElementById('gps-history-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeGPSHistoryModal() {
    document.getElementById('gps-history-modal').classList.add('hidden');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeGPSHistoryModal();
});
</script>
@endif

@if(session('success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Succès!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#ea580c',
            timer: 3500,
            timerProgressBar: true,
            customClass: {
                popup: 'rounded-3xl'
            }
        });
    </script>
@endif

@if($errors->any())
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: "{{ $errors->first() }}",
            confirmButtonColor: '#d33',
            customClass: {
                popup: 'rounded-3xl'
            }
        });
    </script>
@endif
@endsection
