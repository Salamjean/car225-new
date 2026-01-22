@extends('home.layouts.template')
@section('content')

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
                </div>
            </div>
            <div class="col-lg-6" data-aos="zoom-in">
                <div class="hero-image-stack">
                    <div class="image-box box-main">
                        <img src="{{ asset('assets/img/travel/destination-10.webp') }}" alt="Car225 Voyage" class="img-fluid rounded-custom shadow-2xl border-white-thin">
                    </div>
                    <div class="image-box box-floating shadow-xl animate-float">
                        <img src="{{ asset('assets/img/travel/destination-18.webp') }}" alt="Gare Moderne" class="img-fluid rounded-custom border-green-thick">
                        <div class="floating-badge shadow-lg">
                            <i class="bi bi-shield-check-fill text-success fs-3"></i>
                            <div>
                                <span class="d-block fw-bold text-dark">Sécurisé</span>
                                <small class="text-muted">Certifié 2026</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Timeline Station Section -->
<section class="py-5 overflow-hidden" id="notre-histoire" style="background-color: #f8fafb;">
    <div class="container py-lg-5">
        <div class="text-center mb-5" data-aos="fade-up">
            <h6 class="text-success fw-bold text-uppercase ls-2">Notre Parcours</h6>
            <h2 class="display-5 fw-bold text-dark">L'épopée <span class="text-orange">Car225</span></h2>
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
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Call to Experience -->
<section class="py-5 bg-white">
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