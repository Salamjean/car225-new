@extends('home.layouts.template')
@section('content')

    <!-- Hero Services - Immersive Background -->
    <section class="services-hero-section"
        style="background: linear-gradient(rgba(5, 30, 35, 0.8), rgba(5, 30, 35, 0.8)), url('{{ asset('assets/img/travel/destination-20.webp') }}') center/cover no-repeat;">
        <div class="container">
            <div class="row align-items-center py-5">
                <div class="col-lg-8 offset-lg-2 text-center" data-aos="fade-up">
                    <div class="hero-badge mb-3">Nos Solutions</div>
                    <h1 class="hero-title text-white mb-3">Des Services Pensés pour <span
                            style="color: #fea219;">Vous</span></h1>
                    <p class="hero-subtitle text-white-50 mb-4 mx-auto" style="max-width: 600px;">
                        Car225 vous accompagne au-delà du simple trajet. Découvrez nos services innovants pour une
                        expérience de voyage exceptionnelle.
                    </p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center bg-transparent p-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50">Accueil</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Services</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Services Grid -->
    <section class="py-5 bg-light-soft">
        <div class="container">
            <div class="row g-4">
                <!-- Service 1: Réservation de billets -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-card-modern">
                        <div class="service-icon-wrapper">
                            <i class="bi bi-ticket-perforated"></i>
                        </div>
                        <div class="service-content p-4 text-center">
                            <h4 class="fw-bold text-dark mb-3">Réservation en Ligne</h4>
                            <p class="text-muted mb-4">Réservez vos places en quelques clics auprès de vos compagnies
                                préférées, sans vous déplacer en gare.</p>
                            <ul class="service-features-list text-start mb-4">
                                <li><i class="bi bi-check2-circle me-2 text-success"></i> Choix de la place</li>
                                <li><i class="bi bi-check2-circle me-2 text-success"></i> Paiement Mobile Money</li>
                                <li><i class="bi bi-check2-circle me-2 text-success"></i> E-ticket instantané</li>
                            </ul>
                            <a href="{{ route('home.destination') }}" class="btn btn-outline-primary rounded-pill px-4">En
                                savoir plus</a>
                        </div>
                    </div>
                </div>

                <!-- Service 2: Livraison de Colis -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="service-card-modern featured">
                        <div class="service-icon-wrapper">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div class="service-content p-4 text-center">
                            <h4 class="fw-bold text-dark mb-3">Envoi & Suivi de Colis</h4>
                            <p class="text-muted mb-4">Expédiez vos colis en toute sécurité entre les villes et suivez leur
                                acheminement en temps réel.</p>
                            <ul class="service-features-list text-start mb-4">
                                <li><i class="bi bi-check2-circle me-2 text-success"></i> Tarification transparente</li>
                                <li><i class="bi bi-check2-circle me-2 text-success"></i> QR Code de suivi</li>
                                <li><i class="bi bi-check2-circle me-2 text-success"></i> Notification à l'arrivée</li>
                            </ul>
                            <a href="#" class="btn btn-primary rounded-pill px-4">Expédier un colis</a>
                        </div>
                    </div>
                </div>

                <!-- Service 3: Location de Véhicules -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="service-card-modern">
                        <div class="service-icon-wrapper">
                            <i class="bi bi-truck-flatbed"></i>
                        </div>
                        <div class="service-content p-4 text-center">
                            <h4 class="fw-bold text-dark mb-3">Location de Cars</h4>
                            <p class="text-muted mb-4">Besoin d'un car pour un événement ? Louez un véhicule avec chauffeur
                                via nos partenaires certifiés.</p>
                            <ul class="service-features-list text-start mb-4">
                                <li><i class="bi bi-check2-circle me-2 text-success"></i> Voyages de groupe</li>
                                <li><i class="bi bi-check2-circle me-2 text-success"></i> Confort sur mesure</li>
                                <li><i class="bi bi-check2-circle me-2 text-success"></i> Devis gratuit</li>
                            </ul>
                            <a href="{{ route('home.contact') }}" class="btn btn-outline-primary rounded-pill px-4">Demander
                                un devis</a>
                        </div>
                    </div>
                </div>

                <!-- Service 4: Assistance 24/7 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="service-card-modern">
                        <div class="service-icon-wrapper">
                            <i class="bi bi-headset"></i>
                        </div>
                        <div class="service-content p-4 text-center">
                            <h4 class="fw-bold text-dark mb-3">Assistance Voyageur</h4>
                            <p class="text-muted mb-4">Une équipe dédiée pour vous accompagner avant, pendant et après votre
                                voyage.</p>
                            <ul class="service-features-list text-start mb-4">
                                <li><i class="bi bi-check2-circle me-2 text-success"></i> Support multicanal</li>
                                <li><i class="bi bi-check2-circle me-2 text-success"></i> Gestion des réclamations</li>
                                <li><i class="bi bi-check2-circle me-2 text-success"></i> Aide à la réservation</li>
                            </ul>
                            <a href="{{ route('home.contact') }}" class="btn btn-outline-primary rounded-pill px-4">Nous
                                contacter</a>
                        </div>
                    </div>
                </div>

                <!-- Service 5: Programmes de Fidélité -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                    <div class="service-card-modern">
                        <div class="service-icon-wrapper">
                            <i class="bi bi-gift"></i>
                        </div>
                        <div class="service-content p-4 text-center">
                            <h4 class="fw-bold text-dark mb-3">Programme Fidélité</h4>
                            <p class="text-muted mb-4">Cumulez des points à chaque voyage et profitez de réductions
                                exclusives chez nos partenaires.</p>
                            <ul class="service-features-list text-start mb-4">
                                <li><i class="bi bi-check2-circle me-2 text-success"></i> Points par kilomètre</li>
                                <li><i class="bi bi-check2-circle me-2 text-success"></i> Statut VIP</li>
                                <li><i class="bi bi-check2-circle me-2 text-success"></i> Cadeaux & Réductions</li>
                            </ul>
                            <a href="{{ route('user.register') }}"
                                class="btn btn-outline-primary rounded-pill px-4">Rejoindre</a>
                        </div>
                    </div>
                </div>

                <!-- Service 6: API pour Partenaires -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                    <div class="service-card-modern">
                        <div class="service-icon-wrapper">
                            <i class="bi bi-code-slash"></i>
                        </div>
                        <div class="service-content p-4 text-center">
                            <h4 class="fw-bold text-dark mb-3">Solutions Entreprises</h4>
                            <p class="text-muted mb-4">Intégrez nos solutions de billetterie directement dans vos systèmes
                                via notre API dédiée.</p>
                            <ul class="service-features-list text-start mb-4">
                                <li><i class="bi bi-check2-circle me-2 text-success"></i> Intégration facile</li>
                                <li><i class="bi bi-check2-circle me-2 text-success"></i> Rapports détaillés</li>
                                <li><i class="bi bi-check2-circle me-2 text-success"></i> Support technique</li>
                            </ul>
                            <a href="#" class="btn btn-outline-primary rounded-pill px-4">Voir l'API</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="py-5" style="background-color: #051e23;">
        <div class="container text-center py-4" data-aos="zoom-in">
            <h2 class="text-white mb-4">Prêt à vivre une nouvelle expérience de voyage ?</h2>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('home.destination') }}" class="btn btn-primary btn-lg rounded-pill px-5">Voir les
                    destinations</a>
                <a href="{{ route('user.register') }}" class="btn btn-outline-light btn-lg rounded-pill px-5">Créer un
                    compte</a>
            </div>
        </div>
    </section>

    <style>
        .bg-light-soft {
            background-color: #f8fafb;
        }

        /* Hero */
        .services-hero-section {
            padding: 120px 0 60px;
            position: relative;
        }

        .hero-badge {
            display: inline-block;
            padding: 5px 15px;
            background: rgba(254, 162, 25, 0.1);
            color: #fea219;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Service Card Modern */
        .service-card-modern {
            background: white;
            border-radius: 20px;
            padding: 20px;
            height: 100%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.4s ease;
            border: 1px solid rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .service-card-modern:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border-color: rgba(254, 162, 25, 0.2);
        }

        .service-card-modern.featured {
            background: linear-gradient(135deg, #ffffff 0%, #fffbf2 100%);
            border: 2px solid rgba(254, 162, 25, 0.3);
        }

        .service-icon-wrapper {
            width: 80px;
            height: 80px;
            background: rgba(254, 162, 25, 0.1);
            color: #fea219;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .service-card-modern:hover .service-icon-wrapper {
            background: #fea219;
            color: white;
            transform: rotate(-5deg) scale(1.1);
        }

        .service-features-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .service-features-list li {
            margin-bottom: 10px;
            font-size: 0.9rem;
            color: #495057;
        }

        .btn-outline-primary {
            border-color: #fea219;
            color: #fea219;
        }

        .btn-outline-primary:hover {
            background-color: #fea219;
            border-color: #fea219;
            color: white;
        }

        .btn-primary {
            background-color: #fea219;
            border-color: #fea219;
        }

        .btn-primary:hover {
            background-color: #e69116;
            border-color: #e69116;
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .services-hero-section {
                padding: 100px 0 40px;
            }
        }
    </style>

@endsection