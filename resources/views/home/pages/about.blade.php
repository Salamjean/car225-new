@extends('home.layouts.template')
@section('content')

<<<<<<< HEAD
<!-- Editorial Header - Deep Green & Orange -->
<section class="about-header-modern overflow-hidden position-relative" style="background-color: #062a22; padding-top: 160px; padding-bottom: 100px;">
    <!-- Abstract blurred shapes for background depth -->
    <div class="abstract-shape shape-1"></div>
    <div class="abstract-shape shape-2"></div>
    
    <div class="container position-relative" style="z-index: 5;">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0" data-aos="fade-right">
                <span class="badge rounded-pill bg-white-transparent text-white px-3 py-2 mb-3">
                    <i class="bi bi-stars me-2 text-orange"></i>Notre Vision 2026
                </span>
                <h1 class="display-3 fw-bold text-white mb-4">Redéfinir le <br><span class="text-orange-gradient">Voyage en Afrique</span></h1>
                <p class="lead text-white-50 mb-5 pe-lg-5">
                    Bienvenue chez Car225. Nous transformons chaque kilomètre en une expérience fluide, sécurisée et connectée grâce à notre écosystème intelligent.
                </p>
                <div class="d-flex gap-3">
                    <a href="#notre-histoire" class="btn btn-warning rounded-pill px-4 py-3 fw-bold shadow-orange text-white" style="background-color: #fea219; border: none;">Découvrir l'histoire</a>
                    <a href="{{ route('home.contact') }}" class="btn btn-outline-light rounded-pill px-4 py-3 fw-bold">Nous rejoindre</a>
=======
<!-- Hero About -->
<section class="about-hero-section" style="background: linear-gradient(135deg, #e94e1a 0%, #ff8c00 100%);">
    <div class="container">
        <div class="row align-items-center min-vh-80">
            <div class="col-lg-8" data-aos="fade-right">
                <h1 class="hero-title text-white mb-3">À propos de <span class="text-dark">Car225</span></h1>
                <p class="hero-subtitle text-white mb-4">Votre partenaire de confiance pour les voyages en car en Côte d'Ivoire</p>
                <div class="d-flex gap-3">
                    <a href="#notre-mission" class="btn btn-light" style="color: #e94e1a;">
                        <i class="bi bi-compass me-2"></i>Notre mission
                    </a>
                    <a href="#notre-equipe" class="btn btn-outline-light">
                        <i class="bi bi-people me-2"></i>Notre équipe
                    </a>
                </div>
            </div>
            <div class="col-lg-4" data-aos="fade-left">
                <div class="about-hero-img">
                    <img src="{{ asset('assets/img/travel/destination-1.webp') }}" alt="À propos" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Notre Histoire -->
<section class="py-5 bg-white" id="notre-histoire">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="section-tag" style="background-color: #e94e1a; color: white;">Notre histoire</div>
                <h2 class="section-title mb-4">De l'idée à la <span style="color: #e94e1a;">réalité</span></h2>
                <p class="lead mb-4">
                    Fondée en 2020, Car225 est née d'un constat simple : la réservation de billets de car en Côte d'Ivoire devait être simplifiée et digitalisée.
                </p>
                <p class="mb-4">
                    Face aux longues files d'attente dans les gares routières et à la complexité des réservations, nous avons décidé de créer une plateforme qui révolutionne l'expérience du voyageur.
                </p>
                <div class="timeline-highlights">
                    <div class="timeline-item">
                        <div class="timeline-year" style="background-color: #e94e1a;">2020</div>
                        <h5>Lancement</h5>
                        <p>Première version avec 5 compagnies partenaires</p>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-year" style="background-color: #28a745;">2022</div>
                        <h5>Expansion</h5>
                        <p>25+ compagnies et 30+ villes desservies</p>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-year" style="background-color: #e94e1a;">2024</div>
                        <h5>Innovation</h5>
                        <p>Application mobile et paiement Mobile Money</p>
                    </div>
>>>>>>> origin/Car225m
                </div>
            </div>
            <div class="col-lg-6" data-aos="zoom-in">
                <div class="hero-image-stack">
                    <div class="image-box box-main">
                        <img src="{{ asset('assets/img/travel/destination-10.webp') }}" alt="Car225 Voyage" class="img-fluid rounded-custom shadow-2xl border-white-thin">
                    </div>
<<<<<<< HEAD
                    <div class="image-box box-floating shadow-xl animate-float">
                        <img src="{{ asset('assets/img/travel/destination-18.webp') }}" alt="Gare Moderne" class="img-fluid rounded-custom border-green-thick">
                        <div class="floating-badge shadow-lg">
                            <i class="bi bi-shield-check-fill text-success fs-3"></i>
                            <div>
                                <span class="d-block fw-bold text-dark">Sécurisé</span>
                                <small class="text-muted">Certifié 2026</small>
=======
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Notre Mission -->
<section class="py-5" style="background-color: #f8f9fa;" id="notre-mission">
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <div class="section-tag" style="background-color: #28a745; color: white; display: inline-block;">Notre mission</div>
            <h2 class="section-title">Transformer l'expérience du <span style="color: #e94e1a;">voyageur</span></h2>
            <p class="section-subtitle">Nous croyons que voyager devrait être simple, sûr et accessible à tous</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4" data-aos="fade-up">
                <div class="mission-card">
                    <div class="mission-icon" style="background-color: #e94e1a;">
                        <i class="bi bi-lightning-charge text-white"></i>
                    </div>
                    <h4>Simplifier</h4>
                    <p>Rendre la réservation intuitive et rapide grâce à une plateforme moderne</p>
                </div>
            </div>
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="mission-card">
                    <div class="mission-icon" style="background-color: #28a745;">
                        <i class="bi bi-shield-check text-white"></i>
                    </div>
                    <h4>Sécuriser</h4>
                    <p>Garantir des transactions sûres et des partenaires de confiance</p>
                </div>
            </div>
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="mission-card">
                    <div class="mission-icon" style="background-color: #e94e1a;">
                        <i class="bi bi-people text-white"></i>
                    </div>
                    <h4>Connecter</h4>
                    <p>Relier les voyageurs aux meilleures compagnies de transport</p>
                </div>
            </div>
        </div>
        
        <div class="row mt-5 align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="values-list">
                    <h3 class="mb-4">Nos valeurs</h3>
                    <div class="value-item">
                        <div class="value-icon" style="color: #e94e1a;">
                            <i class="bi bi-heart"></i>
                        </div>
                        <div class="value-content">
                            <h5>Passion</h5>
                            <p>Nous aimons ce que nous faisons et ça se voit dans notre service</p>
                        </div>
                    </div>
                    <div class="value-item">
                        <div class="value-icon" style="color: #28a745;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="value-content">
                            <h5>Intégrité</h5>
                            <p>Transparence et honnêteté dans toutes nos relations</p>
                        </div>
                    </div>
                    <div class="value-item">
                        <div class="value-icon" style="color: #e94e1a;">
                            <i class="bi bi-rocket"></i>
                        </div>
                        <div class="value-content">
                            <h5>Innovation</h5>
                            <p>Nous améliorons constamment notre plateforme</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="stats-card p-4 rounded" style="background: linear-gradient(135deg, #e94e1a 0%, #ff8c00 100%);">
                    <h3 class="text-white mb-4">Car225 en chiffres</h3>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stat-item text-center text-white">
                                <h2 class="stat-number">50+</h2>
                                <p class="stat-label">Destinations</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item text-center text-white">
                                <h2 class="stat-number">150K+</h2>
                                <p class="stat-label">Voyageurs</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item text-center text-white">
                                <h2 class="stat-number">30+</h2>
                                <p class="stat-label">Compagnies</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item text-center text-white">
                                <h2 class="stat-number">98%</h2>
                                <p class="stat-label">Satisfaction</p>
>>>>>>> origin/Car225m
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<<<<<<< HEAD
<!-- Timeline Station Section -->
<section class="py-5 overflow-hidden" id="notre-histoire" style="background-color: #f8fafb;">
    <div class="container py-lg-5">
        <div class="text-center mb-5" data-aos="fade-up">
            <h6 class="text-success fw-bold text-uppercase ls-2">Notre Parcours</h6>
            <h2 class="display-5 fw-bold text-dark">L'épopée <span class="text-orange">Car225</span></h2>
=======
<!-- Notre Équipe -->
<section class="py-5 bg-white" id="notre-equipe">
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <div class="section-tag" style="background-color: #e94e1a; color: white; display: inline-block;">Notre équipe</div>
            <h2 class="section-title">Rencontrez notre <span style="color: #e94e1a;">équipe</span></h2>
            <p class="section-subtitle">Des passionnés qui travaillent chaque jour pour améliorer votre expérience de voyage</p>
>>>>>>> origin/Car225m
        </div>
        
        <div class="timeline-station-wrapper">
            <div class="station-line"></div>
            <div class="row g-4 justify-content-between">
                <!-- Station 1: 2021 -->
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="station-item">
                        <div class="station-marker">
                            <i class="bi bi-rocket-takeoff"></i>
                        </div>
                        <div class="station-card shadow-sm p-4 mt-4 bg-white border-light-custom">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="year-label">2021</span>
                                <span class="badge bg-light text-muted small px-2">Lancement</span>
                            </div>
                            <h5 class="fw-bold text-dark">La Genèse</h5>
                            <p class="text-muted small mb-0">Identification de la fracture numérique dans le transport et début du développement.</p>
                        </div>
                    </div>
                </div>
                <!-- Station 2: 2023 -->
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="station-item mt-lg-5">
                        <div class="station-marker">
                            <i class="bi bi-hub"></i>
                        </div>
                        <div class="station-card shadow-sm p-4 mt-4 bg-white border-orange-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="year-label text-orange fw-bold">2023</span>
                                <span class="badge bg-orange-soft text-orange small px-2">Expansion</span>
                            </div>
                            <h5 class="fw-bold text-dark">Le Hub Digital</h5>
                            <p class="text-muted small mb-0">Lancement de la plateforme de gestion centralisée pour les gares et compagnies.</p>
                        </div>
                    </div>
                </div>
                <!-- Station 3: 2025 -->
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="station-item">
                        <div class="station-marker">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div class="station-card shadow-sm p-4 mt-4 bg-white border-success-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="year-label text-success fw-bold">2025</span>
                                <span class="badge bg-success-soft text-success small px-2">National</span>
                            </div>
                            <h5 class="fw-bold text-dark">Maillage Total</h5>
                            <p class="text-muted small mb-0">Couverture de 95% du territoire et digitalisation du suivi de colis.</p>
                        </div>
                    </div>
                </div>
                <!-- Station 4: 2026 -->
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
                    <div class="station-item mt-lg-5">
                        <div class="station-marker active">
                            <i class="bi bi-stars"></i>
                        </div>
                        <div class="station-card shadow-lg p-4 mt-4 border-success-bold bg-white overflow-hidden position-relative">
                            <div class="glow-effect"></div>
                            <div class="d-flex justify-content-between align-items-center mb-2 position-relative">
                                <span class="year-label bg-success text-white px-2 py-1 rounded-pill">2026</span>
                                <span class="badge bg-warning text-white small px-2 animate-pulse">Vision</span>
                            </div>
                            <h5 class="fw-bold text-dark position-relative">Leader Régional</h5>
                            <p class="text-muted small mb-0 position-relative">L'IA au service de la route. Référence de la mobilité en Afrique de l'Ouest.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="py-5" style="background-color: #ffffff;">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-5" data-aos="fade-right">
                <h2 class="display-6 fw-bold text-dark mb-4">Ce qui nous <span class="text-success">Anime</span> au quotidien</h2>
                <p class="text-muted mb-5">Nous transportons des émotions, des souvenirs et des opportunités business à travers toute la région.</p>
                <div class="checklist-modern">
                    <div class="check-item d-flex align-items-start mb-4">
                        <div class="check-box bg-light shadow-sm me-3 border-success-light">
                            <i class="bi bi-check2 text-success"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Qualité premium</h6>
                            <p class="small text-muted mb-0">Exigence constante sur le confort des voyageurs.</p>
                        </div>
                    </div>
                    <div class="check-item d-flex align-items-start mb-4">
                        <div class="check-box bg-light shadow-sm me-3 border-orange-light">
                            <i class="bi bi-check2 text-orange"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Transparence</h6>
                            <p class="small text-muted mb-0">Aucun frais caché, information en temps réel.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7" data-aos="fade-left">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="feature-card-v2 bg-white p-5 rounded-custom shadow-sm h-100 border-success-soft" style="border-top: 5px solid #198754;">
                            <div class="icon-v2 bg-success-soft text-success mb-4">
                                <i class="bi bi-heart-fill"></i>
                            </div>
                            <h5 class="fw-bold text-dark">Passion Clients</h5>
                            <p class="text-muted small">Écouter chaque voyageur pour s'améliorer sans cesse.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card-v2 p-5 rounded-custom shadow-lg h-100 text-white mt-md-4" style="background-color: #062a22; border-top: 5px solid #fea219;">
                            <div class="icon-v2 bg-white-transparent text-white mb-4">
                                <i class="bi bi-cpu"></i>
                            </div>
                            <h5 class="fw-bold text-orange">Innovation Tech</h5>
                            <p class="text-white-50 small">Le meilleur de la tech pour simplifier vos trajets.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="py-5" style="background-color: #f0f7f4;">
    <div class="container py-lg-5">
        <div class="row justify-content-center mb-5" data-aos="fade-up">
            <div class="col-lg-6 text-center">
                <h6 class="text-success fw-bold text-uppercase ls-2">L'Humain</h6>
                <h2 class="display-5 fw-bold text-dark">Les Visages de la <span class="text-orange">Confiance</span></h2>
            </div>
        </div>
        <div class="row g-4">
            @foreach([
                ['name' => 'Koffi Traoré', 'role' => 'Founder & Visionary', 'img' => 'person-f-7.webp', 'color' => '#fea219'],
                ['name' => 'Sara Kouamé', 'role' => 'Head of Experience', 'img' => 'person-f-5.webp', 'color' => '#198754'],
                ['name' => 'Ariel Zadi', 'role' => 'CTO / Architect', 'img' => 'person-f-13.webp', 'color' => '#fea219'],
                ['name' => 'Nina Gnaly', 'role' => 'Marketing Director', 'img' => 'person-f-9.webp', 'color' => '#198754']
            ] as $member)
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="team-card-editorial overflow-hidden rounded-custom position-relative shadow-sm">
                    <img src="{{ asset('assets/img/person/' . $member['img']) }}" alt="{{ $member['name'] }}" class="img-fluid w-100">
                    <div class="member-overlay p-4 d-flex flex-column justify-content-end" style="background: linear-gradient(to top, {{ $member['color'] }}ee 0%, transparent 70%);">
                        <h5 class="text-white fw-bold mb-0">{{ $member['name'] }}</h5>
                        <p class="text-white-50 small mb-2">{{ $member['role'] }}</p>
                        <div class="social-mini d-flex gap-2">
                            <a href="#"><i class="bi bi-linkedin text-white small"></i></a>
                        </div>
                    </div>
<<<<<<< HEAD
=======
                    <div class="team-info">
                        <h5>{{ $member['name'] }}</h5>
                        <p class="team-role" style="color: #e94e1a;">{{ $member['role'] }}</p>
                    </div>
>>>>>>> origin/Car225m
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Call to Experience -->
<section class="py-5 bg-white">
<<<<<<< HEAD
    <div class="container" data-aos="zoom-in">
        <div class="cta-banner-modern p-5 rounded-custom overflow-hidden position-relative border-0 shadow-lg" style="background-color: #062a22;">
            <div class="cta-bg-layer" style="background: url('{{ asset('assets/img/travel/destination-20.webp') }}') center/cover no-repeat; opacity: 0.3;"></div>
            <div class="position-relative z-index-10 py-4 text-center text-lg-start">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <h2 class="display-5 fw-bold text-white mb-3">Rejoignez le mouvement</h2>
                        <p class="text-white-50 fs-5 mb-lg-0">Découvrez une nouvelle façon de voyager avec Car225.</p>
                    </div>
                    <div class="col-lg-5 text-lg-end">
                        <a href="{{ route('login') }}" class="btn btn-warning btn-lg rounded-pill px-5 py-3 fw-bold text-white" style="background-color: #fea219; border: none; box-shadow: 0 10px 20px rgba(254,162,25,0.3);">Se Connecter</a>
                    </div>
=======
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Ils nous <span style="color: #e94e1a;">font confiance</span></h2>
            <p class="section-subtitle">Ce que disent nos partenaires</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4" data-aos="fade-up">
                <div class="testimonial-card p-4 rounded">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('assets/img/gallery/gallery-1.webp') }}" alt="UTB" class="partner-logo me-3">
                        <div>
                            <h5>Union des Transports de Bouaké</h5>
                            <div class="rating">
                                <i class="bi bi-star-fill" style="color: #e94e1a;"></i>
                                <i class="bi bi-star-fill" style="color: #e94e1a;"></i>
                                <i class="bi bi-star-fill" style="color: #e94e1a;"></i>
                                <i class="bi bi-star-fill" style="color: #e94e1a;"></i>
                                <i class="bi bi-star-fill" style="color: #e94e1a;"></i>
                            </div>
                        </div>
                    </div>
                    <p>"Car225 a transformé notre façon de vendre des billets. Les réservations en ligne ont augmenté nos ventes de 40%."</p>
                </div>
            </div>
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="testimonial-card p-4 rounded">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('assets/img/gallery/gallery-5.webp') }}" alt="STC" class="partner-logo me-3">
                        <div>
                            <h5>Société des Transports de Cocody</h5>
                            <div class="rating">
                                <i class="bi bi-star-fill" style="color: #e94e1a;"></i>
                                <i class="bi bi-star-fill" style="color: #e94e1a;"></i>
                                <i class="bi bi-star-fill" style="color: #e94e1a;"></i>
                                <i class="bi bi-star-fill" style="color: #e94e1a;"></i>
                                <i class="bi bi-star-half" style="color: #e94e1a;"></i>
                            </div>
                        </div>
                    </div>
                    <p>"La plateforme est intuitive pour nos clients et l'équipe de Car225 est toujours réactive pour nous aider."</p>
                </div>
            </div>
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="testimonial-card p-4 rounded">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('assets/img/gallery/gallery-8.webp') }}" alt="Voyageurs" class="partner-logo me-3">
                        <div>
                            <h5>Association des Voyageurs</h5>
                            <div class="rating">
                                <i class="bi bi-star-fill" style="color: #e94e1a;"></i>
                                <i class="bi bi-star-fill" style="color: #e94e1a;"></i>
                                <i class="bi bi-star-fill" style="color: #e94e1a;"></i>
                                <i class="bi bi-star-fill" style="color: #e94e1a;"></i>
                                <i class="bi bi-star-fill" style="color: #e94e1a;"></i>
                            </div>
                        </div>
                    </div>
                    <p>"Enfin une solution qui simplifie vraiment la vie des voyageurs. Félicitations pour cette belle initiative !"</p>
>>>>>>> origin/Car225m
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    :root {
        --primary-green: #062a22;
        --accent-orange: #fea219;
        --success-green: #198754;
    }

    .rounded-custom { border-radius: 2rem; }
    .ls-2 { letter-spacing: 2px; }
    .text-orange { color: #fea219; }
    .text-orange-gradient {
        background: linear-gradient(to right, #fea219, #f9b13d);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .bg-white-transparent { background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); }
    .bg-success-soft { background: rgba(25, 135, 84, 0.1); }
    .bg-orange-soft { background: rgba(254, 162, 25, 0.1); }
    .border-success-soft { border: 1px solid rgba(25, 135, 84, 0.1); }
    .border-orange-soft { border: 1px solid rgba(254, 162, 25, 0.1); }
    .border-white-thin { border: 5px solid white; }
    .border-green-thick { border: 8px solid white; }
    .border-success-bold { border: 2px solid #198754; }
    .border-light-custom { border: 1px solid #f0f0f0; }

    /* Abstract Shapes */
    .abstract-shape {
        position: absolute;
        width: 400px;
        height: 400px;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.15;
        z-index: 1;
    }
    .shape-1 { background: #fea219; top: -100px; left: -100px; }
    .shape-2 { background: #198754; bottom: -100px; right: -100px; }

    /* Hero Image Stack */
    .hero-image-stack { position: relative; padding: 40px; }
    .image-box.box-main { width: 100%; transform: rotate(-3deg); }
    .image-box.box-floating {
        position: absolute; width: 65%; bottom: -30px; right: -20px;
        transform: rotate(5deg); z-index: 10; border-radius: 2rem;
    }
    .floating-badge {
        position: absolute; top: -30px; left: -30px; background: white;
        padding: 15px 25px; border-radius: 1.5rem; display: flex; align-items: center; gap: 15px;
    }

    /* Animations */
    .animate-float { animation: float 6s ease-in-out infinite; }
    @keyframes float {
        0%, 100% { transform: rotate(5deg) translateY(0); }
        50% { transform: rotate(5deg) translateY(-20px); }
    }
    .animate-pulse { animation: pulse-orange 2s infinite; }
    @keyframes pulse-orange {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(254, 162, 25, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(254, 162, 25, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(254, 162, 25, 0); }
    }

    /* Timeline Station */
    .timeline-station-wrapper { position: relative; padding: 60px 0; }
    .station-line { position: absolute; top: 85px; left: 0; width: 100%; height: 4px; background: #e0e7e5; z-index: 1; }
    .station-marker {
        width: 60px; height: 60px; background: white; border: 4px solid #f0f0f0;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; color: #d0d7d5; position: relative; z-index: 5; margin-bottom: 20px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .station-marker.active {
        border-color: #198754; background: #198754; color: white;
        box-shadow: 0 0 25px rgba(25,135,84,0.4); transform: scale(1.1);
    }
    .station-item:hover .station-marker { transform: translateY(-5px) scale(1.1); color: #fea219; border-color: #fea219; }
    .station-item:hover .station-marker.active { color: white; border-color: #198754; }

    .glow-effect {
        position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
        background: radial-gradient(circle, rgba(254, 162, 25, 0.1) 0%, transparent 70%);
        opacity: 0; transition: opacity 0.5s ease;
    }
    .station-card:hover .glow-effect { opacity: 1; }

    .station-card { border-radius: 1.5rem; transition: all 0.4s ease; position: relative; }
    .station-card:hover { transform: translateY(-12px); box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important; }

    /* Other styles */
    .icon-v2 { width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .team-card-editorial { height: 400px; }
    .team-card-editorial img { height: 100%; object-fit: cover; transition: all 0.5s ease; }
    .team-card-editorial:hover img { transform: scale(1.1); }
    .check-box { width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }

    @media (max-width: 991px) {
        .station-line { display: none; }
        .station-item { text-align: center; margin-bottom: 30px; }
        .station-marker { margin: 0 auto 20px; }
    }
</style>

@endsection