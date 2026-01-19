@extends('home.layouts.template')
@section('content')

<!-- Hero About -->
<section class="about-hero-section" style="background: linear-gradient(135deg, #fea219 0%, #ff8c00 100%);">
    <div class="container">
        <div class="row align-items-center min-vh-80">
            <div class="col-lg-8" data-aos="fade-right">
                <h1 class="hero-title text-white mb-3">À propos de <span class="text-dark">Car225</span></h1>
                <p class="hero-subtitle text-white mb-4">Votre partenaire de confiance pour les voyages en car en Côte d'Ivoire</p>
                <div class="d-flex gap-3">
                    <a href="#notre-mission" class="btn btn-light" style="color: #fea219;">
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
                <div class="section-tag" style="background-color: #fea219; color: white;">Notre histoire</div>
                <h2 class="section-title mb-4">De l'idée à la <span style="color: #fea219;">réalité</span></h2>
                <p class="lead mb-4">
                    Fondée en 2020, Car225 est née d'un constat simple : la réservation de billets de car en Côte d'Ivoire devait être simplifiée et digitalisée.
                </p>
                <p class="mb-4">
                    Face aux longues files d'attente dans les gares routières et à la complexité des réservations, nous avons décidé de créer une plateforme qui révolutionne l'expérience du voyageur.
                </p>
                <div class="timeline-highlights">
                    <div class="timeline-item">
                        <div class="timeline-year" style="background-color: #fea219;">2020</div>
                        <h5>Lancement</h5>
                        <p>Première version avec 5 compagnies partenaires</p>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-year" style="background-color: #28a745;">2022</div>
                        <h5>Expansion</h5>
                        <p>25+ compagnies et 30+ villes desservies</p>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-year" style="background-color: #fea219;">2024</div>
                        <h5>Innovation</h5>
                        <p>Application mobile et paiement Mobile Money</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="about-img-wrapper">
                    <img src="{{ asset('assets/img/travel/destination-11.webp') }}" alt="Notre histoire" class="img-fluid rounded shadow">
                    <div class="experience-badge">
                        <h3>4+</h3>
                        <p>Ans d'expérience</p>
                    </div>
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
            <h2 class="section-title">Transformer l'expérience du <span style="color: #fea219;">voyageur</span></h2>
            <p class="section-subtitle">Nous croyons que voyager devrait être simple, sûr et accessible à tous</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4" data-aos="fade-up">
                <div class="mission-card">
                    <div class="mission-icon" style="background-color: #fea219;">
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
                    <div class="mission-icon" style="background-color: #fea219;">
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
                        <div class="value-icon" style="color: #fea219;">
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
                        <div class="value-icon" style="color: #fea219;">
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
                <div class="stats-card p-4 rounded" style="background: linear-gradient(135deg, #fea219 0%, #ff8c00 100%);">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Notre Équipe -->
<section class="py-5 bg-white" id="notre-equipe">
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <div class="section-tag" style="background-color: #fea219; color: white; display: inline-block;">Notre équipe</div>
            <h2 class="section-title">Rencontrez notre <span style="color: #fea219;">équipe</span></h2>
            <p class="section-subtitle">Des passionnés qui travaillent chaque jour pour améliorer votre expérience de voyage</p>
        </div>
        
        <div class="row g-4">
            @foreach([
                ['name' => 'Koffi Traoré', 'role' => 'Fondateur & CEO', 'image' => 'person-f-7.webp'],
                ['name' => 'Aminata Koné', 'role' => 'Responsable Commerciale', 'image' => 'person-f-5.webp'],
                ['name' => 'Jean Aké', 'role' => 'Développeur Principal', 'image' => 'person-f-13.webp'],
                ['name' => 'Fatou Diabaté', 'role' => 'Service Client', 'image' => 'person-f-10.webp']
            ] as $member)
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="team-card"> 
                    <div class="team-img">
                        <img src="{{ asset('assets/img/person/' . $member['image']) }}" alt="{{ $member['name'] }}">
                        <div class="team-social">
                            <a href="#"><i class="bi bi-linkedin"></i></a>
                            <a href="#"><i class="bi bi-twitter-x"></i></a>
                            <a href="#"><i class="bi bi-facebook"></i></a>
                        </div>
                    </div>
                    <div class="team-info">
                        <h5>{{ $member['name'] }}</h5>
                        <p class="team-role" style="color: #fea219;">{{ $member['role'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA Join Us -->
<section class="py-5" style="background-color: #28a745;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8" data-aos="fade-right">
                <h2 class="text-white mb-3">Vous êtes une compagnie de transport ?</h2>
                <p class="text-white mb-4">Rejoignez notre réseau et augmentez votre visibilité en ligne</p>
            </div>
            <div class="col-lg-4" data-aos="fade-left">
                <a href="#" class="btn btn-light btn-lg w-100" style="color: #28a745;">
                    <i class="bi bi-building me-2"></i>Devenir partenaire
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials About -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Ils nous <span style="color: #fea219;">font confiance</span></h2>
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
                                <i class="bi bi-star-fill" style="color: #fea219;"></i>
                                <i class="bi bi-star-fill" style="color: #fea219;"></i>
                                <i class="bi bi-star-fill" style="color: #fea219;"></i>
                                <i class="bi bi-star-fill" style="color: #fea219;"></i>
                                <i class="bi bi-star-fill" style="color: #fea219;"></i>
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
                                <i class="bi bi-star-fill" style="color: #fea219;"></i>
                                <i class="bi bi-star-fill" style="color: #fea219;"></i>
                                <i class="bi bi-star-fill" style="color: #fea219;"></i>
                                <i class="bi bi-star-fill" style="color: #fea219;"></i>
                                <i class="bi bi-star-half" style="color: #fea219;"></i>
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
                                <i class="bi bi-star-fill" style="color: #fea219;"></i>
                                <i class="bi bi-star-fill" style="color: #fea219;"></i>
                                <i class="bi bi-star-fill" style="color: #fea219;"></i>
                                <i class="bi bi-star-fill" style="color: #fea219;"></i>
                                <i class="bi bi-star-fill" style="color: #fea219;"></i>
                            </div>
                        </div>
                    </div>
                    <p>"Enfin une solution qui simplifie vraiment la vie des voyageurs. Félicitations pour cette belle initiative !"</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Hero Section */
    .about-hero-section {
        padding: 100px 0;
        position: relative;
        overflow: hidden;
    }
    
    .about-hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 40%;
        height: 100%;
        background: rgba(255, 255, 255, 0.1);
        clip-path: polygon(100% 0, 100% 100%, 0 0);
    }
    
    .about-hero-img img {
        transform: rotate(-5deg);
        filter: drop-shadow(0 10px 20px rgba(0,0,0,0.2));
    }
    
    /* Section Tag */
    .section-tag {
        display: inline-block;
        padding: 6px 20px;
        border-radius: 30px;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 20px;
    }
    
    /* Timeline */
    .timeline-highlights {
        margin-top: 30px;
    }
    
    .timeline-item {
        position: relative;
        padding-left: 70px;
        margin-bottom: 30px;
    }
    
    .timeline-year {
        position: absolute;
        left: 0;
        top: 0;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 18px;
    }
    
    /* Mission Cards */
    .mission-card {
        text-align: center;
        padding: 30px 20px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
        height: 100%;
    }
    
    .mission-card:hover {
        transform: translateY(-10px);
    }
    
    .mission-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 32px;
    }
    
    /* Values */
    .value-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 30px;
    }
    
    .value-icon {
        font-size: 28px;
        margin-right: 20px;
        min-width: 50px;
    }
    
    /* Team */
    .team-card {
        text-align: center;
        overflow: hidden;
        border-radius: 15px;
        background: white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
    }
    
    .team-card:hover {
        transform: translateY(-10px);
    }
    
    .team-img {
        position: relative;
        overflow: hidden;
        height: 250px;
    }
    
    .team-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .team-social {
        position: absolute;
        bottom: -50px;
        left: 0;
        right: 0;
        background: rgba(254, 162, 25, 0.9);
        padding: 15px;
        display: flex;
        justify-content: center;
        gap: 15px;
        transition: bottom 0.3s ease;
    }
    
    .team-card:hover .team-social {
        bottom: 0;
    }
    
    .team-social a {
        color: white;
        font-size: 18px;
        transition: transform 0.3s ease;
    }
    
    .team-social a:hover {
        transform: scale(1.2);
    }
    
    .team-info {
        padding: 20px;
    }
    
    .team-role {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 0;
    }
    
    /* Testimonials */
    .testimonial-card {
        border: 2px solid #f0f0f0;
        height: 100%;
        transition: border-color 0.3s ease;
    }
    
    .testimonial-card:hover {
        border-color: #fea219;
    }
    
    .partner-logo {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        object-fit: cover;
    }
    
    /* Stats Card */
    .stats-card {
        box-shadow: 0 10px 30px rgba(254, 162, 25, 0.3);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .about-hero-section {
            padding: 60px 0;
        }
        
        .timeline-item {
            padding-left: 60px;
        }
        
        .timeline-year {
            width: 50px;
            height: 50px;
            font-size: 16px;
        }
        
        .team-img {
            height: 200px;
        }
    }
</style>

@endsection