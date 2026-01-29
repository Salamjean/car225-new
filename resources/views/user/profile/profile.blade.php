@extends('user.layouts.template')

@section('title', 'Mon Profil')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Mon Profil</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Profil</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Photo de profil -->
        <div class="col-xl-4">
            <div class="card overflow-hidden">
                <div class="bg-primary bg-soft">
                    <div class="row">
                        <div class="col-12">
                            <div class="text-primary p-3">
                                <h5 class="text-primary">Bienvenue!</h5>
                                <p>Gérez votre profil</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="avatar-xl profile-user-wid mb-4 mx-auto">
                                <div class="position-relative">
                                    <img src="{{ $user->photo_profile_path ? asset('storage/' . $user->photo_profile_path) : asset('assets/images/default-avatar.png') }}" 
                                         alt="" 
                                         class="img-thumbnail rounded-circle avatar-xl" 
                                         id="profilePhotoPreview">
                                    <span class="avatar-title rounded-circle bg-light text-primary font-size-16" 
                                          style="position: absolute; bottom: 0; right: 0; cursor: pointer;"
                                          onclick="document.getElementById('photoInput').click()">
                                        <i class="bx bx-camera"></i>
                                    </span>
                                </div>
                            </div>
                            <h5 class="font-size-15 text-truncate text-center">{{ $user->name }}</h5>
                            <p class="text-muted mb-0 text-truncate text-center">{{ $user->email }}</p>
                        </div>
                    </div>

                    <!-- Formulaire caché pour upload photo -->
                    <form id="photoForm" enctype="multipart/form-data">
                        @csrf
                        <input type="file" id="photoInput" name="photo" accept="image/*" style="display: none;" onchange="uploadPhoto()">
                    </form>
                </div>
            </div>

            <!-- Informations supplémentaires -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Informations</h4>
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row">Nom complet :</th>
                                    <td>{{ $user->name }} {{ $user->prenom }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Téléphone :</th>
                                    <td>{{ $user->contact ?? 'Non renseigné' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Email :</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Adresse :</th>
                                    <td>{{ $user->adresse ?? 'Non renseignée' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Solde :</th>
                                    <td><span class="badge badge-soft-success font-size-12">{{ number_format($user->solde, 0, ',', ' ') }} FCFA</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modification du profil -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Modifier mes informations</h4>

                    <form id="profileForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="prenom" class="form-label">Prénom</label>
                                    <input type="text" class="form-control" id="prenom" name="prenom" value="{{ $user->prenom }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact" class="form-label">Téléphone</label>
                                    <input type="text" class="form-control" id="contact" name="contact" value="{{ $user->contact }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="adresse" class="form-label">Adresse</label>
                                    <input type="text" class="form-control" id="adresse" name="adresse" value="{{ $user->adresse }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="commune" class="form-label">Commune</label>
                                    <input type="text" class="form-control" id="commune" name="commune" value="{{ $user->commune }}">
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save font-size-16 align-middle me-2"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Changer le mot de passe -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Changer le mot de passe</h4>

                    <form id="passwordForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Mot de passe actuel <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Nouveau mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    <div class="invalid-feedback"></div>
                                    <small class="text-muted">Minimum 8 caractères</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-lock font-size-16 align-middle me-2"></i> Changer le mot de passe
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
    // Mise à jour du profil
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        
        // Retirer les anciennes erreurs
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        const formData = new FormData(this);
        
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
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        $(`#${field}`).addClass('is-invalid');
                        $(`#${field}`).next('.invalid-feedback').text(errors[field][0]);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur!',
                        text: xhr.responseJSON?.message || 'Une erreur est survenue'
                    });
                }
            }
        });
    });

    // Changement de mot de passe
    $('#passwordForm').on('submit', function(e) {
        e.preventDefault();
        
        // Retirer les anciennes erreurs
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
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
                    timer: 2000,
                    showConfirmButton: false
                });
                $('#passwordForm')[0].reset();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        $(`#${field}`).addClass('is-invalid');
                        $(`#${field}`).next('.invalid-feedback').text(errors[field][0]);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur!',
                        text: xhr.responseJSON?.message || 'Une erreur est survenue'
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
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur!',
                    text: xhr.responseJSON?.message || 'Erreur lors du téléchargement'
                });
            }
        });
    }
</script>
@endpush
