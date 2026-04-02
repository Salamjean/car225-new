@extends('home.layouts.template')
@section('content')

    <!-- Hero Privacy -->
    <section class="privacy-hero-section"
        style="background: linear-gradient(rgba(5, 30, 35, 0.8), rgba(5, 30, 35, 0.8)), url('{{ asset('assets/img/travel/destination-18.webp') }}') center/cover no-repeat; padding: 120px 0 60px;">
        <div class="container">
            <div class="row align-items-center py-5">
                <div class="col-lg-8 offset-lg-2 text-center" data-aos="fade-up">
                    <div class="hero-badge mb-3" style="display: inline-block; padding: 5px 15px; background: rgba(254, 162, 25, 0.1); color: #e94f1b; border-radius: 50px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Légal & Confidentialité</div>
                    <h1 class="hero-title text-white mb-3">Politique de <span style="color: #e94f1b;">Confidentialité</span></h1>
                    <p class="hero-subtitle text-white-50 mb-4 mx-auto" style="max-width: 600px;">
                        Car225 s'engage à protéger la vie privée de ses utilisateurs. Découvrez comment nous gérons vos données en toute transparence.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Privacy Content -->
    <section class="py-5 bg-light-soft" style="background-color: #f8fafb;">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1 bg-white p-4 p-md-5 rounded-4 shadow-sm" data-aos="fade-up">
                    <h2 class="mb-4">1. Introduction</h2>
                    <p class="text-muted">L'application mobile et la plateforme web "Car225" s'engagent à protéger la vie privée de leurs utilisateurs (passagers, chauffeurs, et partenaires). Cette politique détaille les données que nous collectons dans le cadre de nos services de réservation de billets de transport et de gestion de voyages, et explique la manière dont elles sont utilisées.</p>

                    <h2 class="mt-5 mb-4">2. Données collectées</h2>
                    <ul class="text-muted list-group list-group-flush mb-4">
                        <li class="list-group-item bg-transparent"><strong>Informations de compte :</strong> Nom, prénom, email, numéro de téléphone, et contact d'urgence, utilisés pour la création de profil, les réservations et l'authentification.</li>
                        <li class="list-group-item bg-transparent"><strong>Données de paiement :</strong> Historique de rechargement de portefeuille (Wallet), historique de transactions via nos partenaires (Wave, CinetPay, etc.) pour l'achat ou l'annulation de vos billets.</li>
                        <li class="list-group-item bg-transparent"><strong>Localisation (GPS) :</strong> Utilisée principalement pour le suivi de la flotte (chauffeurs) en temps réel par les gares. Pour les usagers (passagers), la localisation n'est collectée que si autorisée (pour trouver les gares à proximité par exemple).</li>
                        <li class="list-group-item bg-transparent"><strong>Interactions et Support :</strong> Tout message envoyé à notre support client, signalement d'incident ou de voyage, ou retour d'expérience.</li>
                    </ul>

                    <h2 class="mt-5 mb-4">3. Utilisation des données</h2>
                    <p class="text-muted mb-2">Vos données sont strictement utilisées pour :</p>
                    <ul class="text-muted">
                        <li>Gérer votre compte utilisateur, votre portefeuille électronique, et faciliter l'achat de vos titres de transport et vos réservations en ligne.</li>
                        <li>Vous notifier (par SMS ou email) de l'état de votre réservation, de changements de programme ou de correspondances.</li>
                        <li>Alerter les forces de l'ordre (Sapeurs pompiers) et les contacts d'urgence fournis en cas d'incident, retard majeur ou de sinistre lors d'un voyage.</li>
                        <li>Assurer la gestion, la sécurité et le bon suivi des voyages pour les compagnies de transport.</li>
                    </ul>

                    <h2 class="mt-5 mb-4">4. Services Tiers</h2>
                    <p class="text-muted">Pour vous offrir la meilleure expérience, nous collaborons avec des services tiers de confiance pour le fonctionnement de la plateforme :</p>
                    <ul class="text-muted">
                        <li>Partenaires de paiement (Wave, etc.) pour le traitement sécurisé des dépôts et des achats de billets.</li>
                        <li>Services d'expédition de SMS et Notifications (Firebase Cloud Messaging) pour les alertes de voyage et codes OTP.</li>
                        <li>Cartographie (Google Maps) pour la localisation des gares et le suivi des itinéraires.</li>
                    </ul>

                    <h2 class="mt-5 mb-4">5. Suppression des données</h2>
                    <p class="text-muted">Conformément aux réglementations sur la protection des données, vous avez le droit d'exiger la suppression de votre compte et de vos données personnelles. Vous pouvez initier cette démarche directement depuis l'application mobile ou en consultant notre <a href="{{ route('home.deletion') }}" style="color: #e94f1b; text-decoration: none; font-weight: bold;">Page de suppression de compte</a>.</p>

                    <h2 class="mt-5 mb-4">6. Contact</h2>
                    <p class="text-muted mb-0">Pour toute demande d'assistance ou question relative à notre politique de confidentialité, n'hésitez pas à contacter notre équipe à l'adresse suivante : <strong>contact@car225.com</strong></p>
                </div>
            </div>
        </div>
    </section>

@endsection
