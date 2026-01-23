@extends('home.layouts.template')
@section('content')

    <!-- Hero Contact -->
    <section class="contact-hero-section"
        style="background: linear-gradient(rgba(5, 30, 35, 0.8), rgba(5, 30, 35, 0.8)), url('{{ asset('assets/img/travel/destination-18.webp') }}') center/cover no-repeat;">
        <div class="container">
            <div class="row align-items-center py-5">
                <div class="col-lg-8 offset-lg-2 text-center" data-aos="fade-up">
                    <div class="hero-badge mb-3">Support & Assistance</div>
                    <h1 class="hero-title text-white mb-3">Contactez <span style="color: #e94f1b;">Notre Équipe</span></h1>
                    <p class="hero-subtitle text-white-50 mb-4 mx-auto" style="max-width: 600px;">
                        Une question ? Un problème technique ? Notre support est disponible 24h/24 et 7j/7 pour vous
                        accompagner.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="py-5 bg-light-soft">
        <div class="container">
            <div class="row g-5">
                <!-- Contact Info -->
                <div class="col-lg-4" data-aos="fade-right">
                    <div class="contact-info-wrapper h-100">
                        <h3 class="fw-bold text-dark mb-4">Informations de <span style="color: #e94f1b;">Contact</span></h3>
                        <p class="text-muted mb-5">N'hésitez pas à nous contacter directement ou à remplir le formulaire
                            ci-contre. Nous vous répondrons dans les plus brefs délais.</p>

                        <div class="contact-info-item d-flex align-items-center mb-4 p-3 bg-white rounded-4 shadow-sm">
                            <div class="info-icon-box bg-primary-soft text-primary">
                                <i class="bi bi-geo-alt fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 fw-bold">Notre Siège</h6>
                                <p class="mb-0 text-muted small">Abidjan, Cocody Riviera</p>
                            </div>
                        </div>

                        <div class="contact-info-item d-flex align-items-center mb-4 p-3 bg-white rounded-4 shadow-sm">
                            <div class="info-icon-box bg-success-soft text-success">
                                <i class="bi bi-telephone fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 fw-bold">Téléphone</h6>
                                <p class="mb-0 text-muted small">+225 07 00 00 00 00</p>
                            </div>
                        </div>

                        <div class="contact-info-item d-flex align-items-center mb-4 p-3 bg-white rounded-4 shadow-sm">
                            <div class="info-icon-box bg-warning-soft text-warning">
                                <i class="bi bi-envelope fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 fw-bold">Email</h6>
                                <p class="mb-0 text-muted small">salamjeanlouis3@gmail.com</p>
                            </div>
                        </div>

                        <div class="social-links mt-5">
                            <h6 class="mb-3 fw-bold">Suivez-nous</h6>
                            <div class="d-flex gap-3">
                                <a href="#" class="social-btn"><i class="bi bi-facebook"></i></a>
                                <a href="#" class="social-btn"><i class="bi bi-twitter-x"></i></a>
                                <a href="#" class="social-btn"><i class="bi bi-instagram"></i></a>
                                <a href="#" class="social-btn"><i class="bi bi-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="col-lg-8" data-aos="fade-left">
                    <div class="contact-form-card bg-white p-4 p-lg-5 rounded-4 shadow-lg">
                        <h3 class="fw-bold text-dark mb-4">Envoyez-nous un <span style="color: #e94f1b;">Message</span></h3>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 mb-4" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('home.contact.store') }}" method="POST" class="row g-4">
                            @csrf
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Nom Complet</label>
                                <input type="text" name="name"
                                    class="form-control rounded-3 py-3 px-4 bg-light border-0 shadow-none"
                                    placeholder="Ex: Jean Louis" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Adresse Email</label>
                                <input type="email" name="email"
                                    class="form-control rounded-3 py-3 px-4 bg-light border-0 shadow-none"
                                    placeholder="Ex: jean@mail.com" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Sujet du Message</label>
                                <input type="text" name="subject"
                                    class="form-control rounded-3 py-3 px-4 bg-light border-0 shadow-none"
                                    placeholder="Ex: Problème de réservation" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Votre Message</label>
                                <textarea name="message" rows="5"
                                    class="form-control rounded-3 py-3 px-4 bg-light border-0 shadow-none"
                                    placeholder="Comment pouvons-nous vous aider ?" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 py-3 w-100 fw-bold">
                                    <i class="bi bi-send me-2"></i> Envoyer le message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .bg-light-soft {
            background-color: #f8fafb;
        }

        /* Hero */
        .contact-hero-section {
            padding: 120px 0 60px;
            position: relative;
        }

        .hero-badge {
            display: inline-block;
            padding: 5px 15px;
            background: rgba(254, 162, 25, 0.1);
            color: #e94f1b;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Info Icons */
        .info-icon-box {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-primary-soft {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .bg-success-soft {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .bg-warning-soft {
            background-color: rgba(254, 162, 25, 0.1);
        }

        /* Social Buttons */
        .social-btn {
            width: 45px;
            height: 45px;
            background: white;
            color: #051e23;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .social-btn:hover {
            background: #e94f1b;
            color: white;
            transform: translateY(-5px);
        }

        /* Form Styles */
        .btn-primary {
            background-color: #e94f1b;
            border-color: #e94f1b;
        }

        .btn-primary:hover {
            background-color: #e69116;
            border-color: #e69116;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(254, 162, 25, 0.2);
        }

        .form-control:focus {
            background-color: #fff !important;
            border: 1px solid #e94f1b !important;
        }

        @media (max-width: 991px) {
            .contact-hero-section {
                padding: 100px 0 40px;
            }

            .contact-form-card {
                margin-top: 30px;
            }
        }
    </style>

@endsection