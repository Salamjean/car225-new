@extends('home.layouts.template')
@section('content')

    <!-- Hero Deletion -->
    <section class="deletion-hero-section"
        style="background: linear-gradient(rgba(5, 30, 35, 0.8), rgba(5, 30, 35, 0.8)), url('{{ asset('assets/img/travel/destination-18.webp') }}') center/cover no-repeat; padding: 120px 0 60px;">
        <div class="container">
            <div class="row align-items-center py-5">
                <div class="col-lg-8 offset-lg-2 text-center" data-aos="fade-up">
                    <div class="hero-badge mb-3" style="display: inline-block; padding: 5px 15px; background: rgba(220, 53, 69, 0.1); color: #dc3545; border-radius: 50px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Gestion de Compte</div>
                    <h1 class="hero-title text-white mb-3">Suppression de <span style="color: #dc3545;">Compte</span></h1>
                    <p class="hero-subtitle text-white-50 mb-4 mx-auto" style="max-width: 600px;">
                        Gérez vos données personnelles. Vous disposez du droit de révoquer à tout moment votre inscription et la suppression de votre compte Car225.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Deletion Content -->
    <section class="py-5 bg-light-soft" style="background-color: #f8fafb;">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 bg-white p-4 p-md-5 rounded-4 shadow-sm" data-aos="fade-up">
                    <h3 class="fw-bold mb-4">Demande de suppression de compte</h3>
                    <p class="text-muted">Conformément aux exigences légales et à notre politique de confidentialité, vous pouvez demander la clôture définitive de votre compte utilisateur Car225 et la suppression des données qui y sont attachées.</p>
                    
                    <div class="alert alert-warning border-0 rounded-3 mt-4 mb-4" style="background-color: rgba(255, 193, 7, 0.1); color: #856404;">
                        <h5 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Données sujettes à la suppression :</h5>
                        <ul class="mb-0 mt-2">
                            <li>Votre profil utilisateur (Nom, Prénom, Photo, numéro de téléphone, mot de passe).</li>
                            <li>Les informations relatives à vos contacts d'urgence.</li>
                            <li>Vos préférences et adresses enregistrées.</li>
                        </ul>
                    </div>

                    <div class="alert alert-info border-0 rounded-3 mb-5" style="background-color: rgba(13, 110, 253, 0.1); color: #004085;">
                        <h5 class="alert-heading fw-bold"><i class="bi bi-info-circle-fill me-2"></i> Données conservées (Obligations légales) :</h5>
                        <p class="mb-0 mt-2">Nous pourrions conserver l'historique des réservations effectuées, ou les traces des rechargements électroniques et paiements validés par nos partenaires pour des durées légales établies. Ces données servent uniquement à des fins de comptabilité ou d'audit en cas de litige.</p>
                    </div>

                    <h4 class="fw-bold mb-3">Comment initier la procédure ?</h4>
                    <p class="text-muted mb-4">Si vous ne pouvez plus accéder à votre compte via l'application ou procéder par vous-même depuis l'interface client, veuillez nous adresser un courriel depuis votre adresse liée au compte ou en précisant vos coordonnées d'inscription. Nous traiterons votre demande dans un délai n'excédant pas 7 jours ouvrés.</p>

                    <div class="text-center mt-5 mb-3">
                        <a href="mailto:contact@car225.com?subject=Demande de suppression de compte - Car225&body=Bonjour, je souhaite procéder à la suppression définitive de mon compte Car225.%0D%0A%0D%0AVeuillez trouver ci-dessous l'identifiant (numéro de téléphone ou e-mail) lié à ce compte : [INSÉRER ICI]." 
                           class="btn btn-danger btn-lg rounded-pill px-5 fw-bold shadow">
                            <i class="bi bi-envelope-x me-2"></i> Faire la demande par Email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
