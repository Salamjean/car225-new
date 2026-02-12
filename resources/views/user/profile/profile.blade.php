@extends('user.layouts.template')

@section('title', 'Mon Profil')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Mon Profil</h1>
            <p class="text-lg text-gray-600">Gérez vos informations personnelles</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Photo de profil -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl shadow-xl p-8">
                    <div class="text-center">
                        <div class="relative w-40 h-40 mx-auto mb-4 group cursor-pointer" onclick="document.getElementById('photoInput').click()">
                            <img src="{{ $user->photo_profile_path ? asset('storage/' . $user->photo_profile_path) : asset('assets/images/default-avatar.png') }}" 
                                 class="w-full h-full rounded-full object-cover border-4 border-[#e94e1a] transition-all duration-300 group-hover:opacity-75" 
                                 id="profilePhotoPreview"
                                 alt="Photo de profil">
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <i class="fas fa-camera text-2xl text-gray-800"></i>
                            </div>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-gray-900">{{ $user->name }} {{ $user->prenom }}</h3>
                        <p class="text-gray-600">{{ $user->email }}</p>
                        
                        <div class="mt-6 space-y-3">
                            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                                <span class="text-sm text-gray-600">Solde</span>
                                <span class="font-bold text-[#e94e1a]">{{ number_format($user->solde, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                <span class="text-sm text-gray-600">Contact</span>
                                <span class="font-bold text-gray-800">{{ $user->contact ?? 'N/A' }}</span>
                            </div>
                            <!-- Formulaire caché pour upload photo -->
                            <form id="photoForm" enctype="multipart/form-data">
                                @csrf
                                <input type="file" id="photoInput" name="photo" accept="image/*" style="display: none;" onchange="uploadPhoto()">
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaires -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informations personnelles -->
                <div class="bg-white rounded-3xl shadow-xl p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-[#e94e1a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Informations Personnelles
                    </h2>

                    <form id="profileForm">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nom *</label>
                                <input type="text" id="name" name="name" value="{{ $user->name }}" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                                <div class="invalid-feedback text-red-500 text-sm mt-1"></div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Prénom</label>
                                <input type="text" id="prenom" name="prenom" value="{{ $user->prenom }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                                <div class="invalid-feedback text-red-500 text-sm mt-1"></div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                                <input type="email" id="email" name="email" value="{{ $user->email }}" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                                <div class="invalid-feedback text-red-500 text-sm mt-1"></div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Contact</label>
                                <input type="text" id="contact" name="contact" value="{{ $user->contact }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                                <div class="invalid-feedback text-red-500 text-sm mt-1"></div>
                            </div>

                            <div class="md:col-span-2 mt-4 border-t pt-4">
                                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-life-ring mr-2 text-red-500"></i>
                                    Personne à contacter d'urgence
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nom personne à contacter</label>
                                        <input type="text" id="nom_urgence" name="nom_urgence" value="{{ $user->nom_urgence }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                                        <div class="invalid-feedback text-red-500 text-sm mt-1"></div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Prénom personne à contacter</label>
                                        <input type="text" id="prenom_urgence" name="prenom_urgence" value="{{ $user->prenom_urgence }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                                        <div class="invalid-feedback text-red-500 text-sm mt-1"></div>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Contact d'urgence</label>
                                        <input type="text" id="contact_urgence" name="contact_urgence" value="{{ $user->contact_urgence }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                                        <p class="text-[10px] text-gray-500 mt-1 italic">Doit être différent de votre contact principal.</p>
                                        <div class="invalid-feedback text-red-500 text-sm mt-1"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" 
                                class="w-full md:w-auto px-8 py-3 bg-[#e94e1a] text-white font-bold rounded-xl hover:bg-[#d33d0f] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Changer le mot de passe -->
                <div class="bg-white rounded-3xl shadow-xl p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-[#e94e1a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Changer le Mot de Passe
                    </h2>

                    <form id="passwordForm">
                        @csrf
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Mot de passe actuel *</label>
                                <input type="password" id="current_password" name="current_password" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                                <div class="invalid-feedback text-red-500 text-sm mt-1"></div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nouveau mot de passe *</label>
                                <input type="password" id="new_password" name="new_password" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                                <div class="invalid-feedback text-red-500 text-sm mt-1"></div>
                                <p class="text-xs text-gray-500 mt-1">Minimum 8 caractères</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Confirmer le mot de passe *</label>
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94e1a] focus:border-transparent">
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" 
                                class="w-full md:w-auto px-8 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl">
                                Changer le mot de passe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Helper to clear errors
    function clearErrors(form) {
        form.find('input').removeClass('border-red-500 focus:ring-red-500').addClass('border-gray-300 focus:ring-[#e94e1a]');
        form.find('.invalid-feedback').text('');
    }

    // Helper to show errors
    function showErrors(errors) {
        for (let field in errors) {
            let input = $(`#${field}`);
            input.removeClass('border-gray-300 focus:ring-[#e94e1a]').addClass('border-red-500 focus:ring-red-500');
            input.next('.invalid-feedback').text(errors[field][0]);
        }
    }

    // Mise à jour du profil
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        clearErrors(form);
        
        const formData = new FormData(this);
        
        // Validation additionnelle côté client
        const contact = $('#contact').val();
        const contactUrgence = $('#contact_urgence').val();
        
        if (contact && contactUrgence && contact === contactUrgence) {
            $('#contact_urgence').addClass('border-red-500 focus:ring-red-500').removeClass('border-gray-300');
            $('#contact_urgence').next('.invalid-feedback').text('Le contact d\'urgence doit être différent de votre contact principal.');
            return;
        }
        
        $.ajax({
            url: '{{ route("user.profile.update") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Succès!',
                    text: response.message,
                    confirmButtonColor: '#e94e1a',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showErrors(xhr.responseJSON.errors);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur!',
                        text: xhr.responseJSON?.message || 'Une erreur est survenue',
                        confirmButtonColor: '#e94e1a'
                    });
                }
            }
        });
    });

    // Changement de mot de passe
    $('#passwordForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        clearErrors(form);
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("user.profile.password") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Succès!',
                    text: response.message,
                    confirmButtonColor: '#e94e1a',
                    timer: 2000,
                    showConfirmButton: false
                });
                form[0].reset();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showErrors(xhr.responseJSON.errors);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur!',
                        text: xhr.responseJSON?.message || 'Une erreur est survenue',
                        confirmButtonColor: '#e94e1a'
                    });
                }
            }
        });
    });

    // Upload de la photo
    function uploadPhoto() {
        const formData = new FormData($('#photoForm')[0]);
        
        $.ajax({
            url: '{{ route("user.profile.photo") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#profilePhotoPreview').attr('src', response.photo_url);
                Swal.fire({
                    icon: 'success',
                    title: 'Succès!',
                    text: response.message,
                    confirmButtonColor: '#e94e1a',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur!',
                    text: xhr.responseJSON?.message || 'Erreur lors du téléchargement',
                    confirmButtonColor: '#e94e1a'
                });
            }
        });
    }
</script>
@endpush
