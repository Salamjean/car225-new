@extends('home.layouts.template')
@section('content')
<!-- Travel Hero Section -->
    @include('home.layouts.carrousel')
    <!-- /Travel Hero Section -->
    <section id="why-us" class="why-us section">

        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <!-- Pourquoi Nous Choisir -->
            <div class="why-choose-section">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center mb-5" data-aos="fade-up" data-aos-delay="100">
                        <h3>Pourquoi Nous Choisir pour Votre Prochain Voyage</h3>
                        <p>Profitez d'une expérience de réservation simplifiée et sécurisée pour tous vos déplacements. Des
                            billets aux meilleurs prix, une assistance disponible 24h/24 et 7j/7.</p>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-airplane-engines-fill"></i>
                            </div>
                            <h4>Réservation Rapide</h4>
                            <p>Obtenez vos billets en quelques clics. Notre plateforme intuitive vous permet de réserver vos
                                vols, trains et bus en moins de 5 minutes.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="250">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <h4>Paiement Sécurisé</h4>
                            <p>Transactions cryptées et protégées. Vos données bancaires sont sécurisées avec les dernières
                                technologies de protection.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-tags-fill"></i>
                            </div>
                            <h4>Meilleurs Prix Garantis</h4>
                            <p>Nous comparons les prix de toutes les compagnies pour vous offrir les tarifs les plus
                                compétitifs du marché.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="350">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-headset"></i>
                            </div>
                            <h4>Assistance 24/7</h4>
                            <p>Notre équipe est disponible à tout moment pour vous aider avec vos réservations,
                                modifications ou annulations.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-globe-americas"></i>
                            </div>
                            <h4>Destinations Mondiales</h4>
                            <p>Accédez à des milliers de destinations à travers le monde. Des vols internationaux aux
                                trajets locaux.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="450">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-phone-fill"></i>
                            </div>
                            <h4>Application Mobile</h4>
                            <p>Gérez vos réservations où que vous soyez grâce à notre application mobile disponible sur iOS
                                et Android.</p>
                        </div>
                    </div>

                    <!-- Optionnel : Ajout de deux nouvelles fonctionnalités spécifiques -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-arrow-clockwise"></i>
                            </div>
                            <h4>Modifications Flexibles</h4>
                            <p>Changez vos dates ou destinations facilement avec nos options de modification flexibles et
                                peu coûteuses.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="550">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-bell-fill"></i>
                            </div>
                            <h4>Alertes Prix</h4>
                            <p>Recevez des notifications lorsque les prix baissent sur vos trajets favoris et économisez
                                jusqu'à 40%.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-wallet-fill"></i>
                            </div>
                            <h4>Programme de Fidélité</h4>
                            <p>Gagnez des points à chaque réservation et bénéficiez de réductions exclusives sur vos
                                prochains voyages.</p>
                        </div>
                    </div>
                </div><!-- Fin Grille des Fonctionnalités -->
            </div><!-- Fin Pourquoi Nous Choisir -->

            <!-- Section Destinations Phares -->
            <section id="featured-destinations" class="featured-destinations section">

                <!-- Titre de la Section -->
                <div class="container section-title" data-aos="fade-up">
                    <h2>Destinations Populaires</h2>
                    <div><span>Découvrez Nos</span> <span class="description-title">Destinations les Plus Réservées</span>
                    </div>
                </div><!-- Fin Titre de la Section -->

                <div class="container" data-aos="fade-up" data-aos-delay="100">

                    <div class="row">

                        <div class="col-lg-6" data-aos="zoom-in" data-aos-delay="200">
                            <div class="featured-destination">
                                <div class="destination-overlay">
                                    <img src="assets/img/travel/destination-3.webp" alt="Paris, France" class="img-fluid">
                                    <div class="destination-info">
                                        <span class="destination-tag">Tendance</span>
                                        <h3>Paris, France</h3>
                                        <p class="location"><i class="bi bi-geo-alt-fill"></i> Aéroport Charles de Gaulle
                                        </p>
                                        <p class="description">La ville lumière vous attend ! Réservez vos billets pour
                                            Paris et profitez des meilleures offres sur les vols directs depuis votre ville.
                                        </p>
                                        <div class="destination-meta">
                                            <div class="tours-count">
                                                <i class="bi bi-airplane"></i>
                                                <span>12 Compagnies</span>
                                            </div>
                                            <div class="rating">
                                                <i class="bi bi-star-fill"></i>
                                                <span>4.8 (856 avis)</span>
                                            </div>
                                        </div>
                                        <div class="price-info">
                                            <span class="starting-from">À partir de</span>
                                            <span class="amount">289€</span>
                                            <span class="price-note">aller simple</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="row g-3">

                                <div class="col-12" data-aos="fade-left" data-aos-delay="300">
                                    <div class="compact-destination">
                                        <div class="destination-image">
                                            <img src="assets/img/travel/destination-7.webp" alt="New York, USA"
                                                class="img-fluid">
                                            <div class="badge-offer">Promo</div>
                                        </div>
                                        <div class="destination-details">
                                            <h4>New York, USA</h4>
                                            <p class="location"><i class="bi bi-geo-alt"></i> Aéroport JFK</p>
                                            <p class="brief">Big Apple à portée de main ! Profitez de nos tarifs spéciaux
                                                sur les vols transatlantiques vers New York.</p>
                                            <div class="stats-row">
                                                <span class="tour-count"><i class="bi bi-clock-history"></i> Vols
                                                    quotidiens</span>
                                                <span class="rating"><i class="bi bi-star-fill"></i> 4.7</span>
                                                <span class="price">à partir de 499€</span>
                                            </div>
                                            <a href="destination-details.html" class="quick-link">Voir les disponibilités
                                                <i class="bi bi-chevron-right"></i></a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12" data-aos="fade-left" data-aos-delay="400">
                                    <div class="compact-destination">
                                        <div class="destination-image">
                                            <img src="assets/img/travel/destination-11.webp" alt="Tokyo, Japon"
                                                class="img-fluid">
                                        </div>
                                        <div class="destination-details">
                                            <h4>Tokyo, Japon</h4>
                                            <p class="location"><i class="bi bi-geo-alt"></i> Aéroport Narita</p>
                                            <p class="brief">Découvrez le Japon avec nos offres spéciales sur les vols
                                                long-courriers vers Tokyo. Service premium inclus.</p>
                                            <div class="stats-row">
                                                <span class="tour-count"><i class="bi bi-calendar-check"></i> Vols
                                                    directs</span>
                                                <span class="rating"><i class="bi bi-star-fill"></i> 4.9</span>
                                                <span class="price">à partir de 789€</span>
                                            </div>
                                            <a href="destination-details.html" class="quick-link">Voir les disponibilités
                                                <i class="bi bi-chevron-right"></i></a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12" data-aos="fade-left" data-aos-delay="500">
                                    <div class="compact-destination">
                                        <div class="destination-image">
                                            <img src="assets/img/travel/destination-16.webp"
                                                alt="Dubaï, Émirats Arabes Unis" class="img-fluid">
                                            <div class="badge-offer limited">Dernières places</div>
                                        </div>
                                        <div class="destination-details">
                                            <h4>Dubaï, Émirats</h4>
                                            <p class="location"><i class="bi bi-geo-alt"></i> Aéroport International</p>
                                            <p class="brief">Profitez de notre promotion spéciale sur les vols vers Dubaï.
                                                Classe économique et affaires disponibles.</p>
                                            <div class="stats-row">
                                                <span class="tour-count"><i class="bi bi-suitcase"></i> Bagage
                                                    inclus</span>
                                                <span class="rating"><i class="bi bi-star-fill"></i> 4.8</span>
                                                <span class="price">à partir de 429€</span>
                                            </div>
                                            <a href="destination-details.html" class="quick-link">Voir les disponibilités
                                                <i class="bi bi-chevron-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- Section des offres spéciales -->
                <div class="row mt-5" data-aos="fade-up" data-aos-delay="700">
                    <div class="col-12 text-center">
                        <div class="promo-banner">
                            <div class="promo-content">
                                <i class="bi bi-lightning-charge-fill promo-icon"></i>
                                <div class="promo-text">
                                    <h4>Offre Flash : Économisez jusqu'à 40%</h4>
                                    <p>Réservez dans les 24h et bénéficiez de réductions exceptionnelles sur plus de 100
                                        destinations</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

        </div>

    </section><!-- /Section Destinations Phares -->

    <!-- Section Témoignages -->
    <section id="testimonials-home" class="testimonials-home section">

        <!-- Titre de la Section -->
        <div class="container section-title" data-aos="fade-up">
            <h2>Témoignages</h2>
            <div><span>Ce Que Nos Voyageurs</span> <span class="description-title">Disent de Nous</span></div>
        </div><!-- Fin Titre de la Section -->

        <div class="container" data-aos="fade-up" data-aos-delay="100">

            <div class="swiper init-swiper">
                <script type="application/json" class="swiper-config">
                {
                    "loop": true,
                    "speed": 600,
                    "autoplay": {
                        "delay": 5000
                    },
                    "slidesPerView": "auto",
                    "pagination": {
                        "el": ".swiper-pagination",
                        "type": "bullets",
                        "clickable": true
                    },
                    "breakpoints": {
                        "320": {
                            "slidesPerView": 1,
                            "spaceBetween": 40
                        },
                        "1200": {
                            "slidesPerView": 3,
                            "spaceBetween": 1
                        }
                    }
                }
            </script>
                <div class="swiper-wrapper">

                    <div class="swiper-slide">
                        <div class="testimonial-item">
                            <p>
                                <i class="bi bi-quote quote-icon-left"></i>
                                <span>Réserver mon billet de car Abidjan-Yamoussoukro n'a jamais été aussi facile !
                                    Plateforme intuitive, confirmation immédiate et excellent service client. Je recommande
                                    à 100% !</span>
                                <i class="bi bi-quote quote-icon-right"></i>
                            </p>
                            <img src="assets/img/person/person-m-9.webp" class="testimonial-img" alt="">
                            <h3>Koffi Traoré</h3>
                            <h4>Étudiant à Abidjan</h4>
                            <div class="rating">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                        </div>
                    </div><!-- Fin témoignage -->

                    <div class="swiper-slide">
                        <div class="testimonial-item">
                            <p>
                                <i class="bi bi-quote quote-icon-left"></i>
                                <span>Je voyage régulièrement entre Bouaké et Abidjan pour mon commerce. Grâce à cette
                                    plateforme, je réserve mes places à l'avance aux meilleurs prix. Les cars sont toujours
                                    conformes à la réservation !</span>
                                <i class="bi bi-quote quote-icon-right"></i>
                            </p>
                            <img src="assets/img/person/person-f-5.webp" class="testimonial-img" alt="">
                            <h3>Aminata Koné</h3>
                            <h4>Commerçante à Bouaké</h4>
                            <div class="rating">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-half"></i>
                            </div>
                        </div>
                    </div><!-- Fin témoignage -->

                    <div class="swiper-slide">
                        <div class="testimonial-item">
                            <p>
                                <i class="bi bi-quote quote-icon-left"></i>
                                <span>Service exceptionnel pour les trajets San Pedro-Abidjan ! J'ai pu choisir mon siège à
                                    l'avance et recevoir mon e-billet directement sur mon téléphone. Plus besoin de faire la
                                    queue à la gare routière.</span>
                                <i class="bi bi-quote quote-icon-right"></i>
                            </p>
                            <img src="assets/img/person/person-f-12.webp" class="testimonial-img" alt="">
                            <h3>Fatou Diabaté</h3>
                            <h4>Enseignante à San Pedro</h4>
                            <div class="rating">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                        </div>
                    </div><!-- Fin témoignage -->

                    <div class="swiper-slide">
                        <div class="testimonial-item">
                            <p>
                                <i class="bi bi-quote quote-icon-left"></i>
                                <span>Voyager en famille de Korhogo à Abidjan est maintenant un plaisir. La possibilité de
                                    réserver plusieurs places côte à côte et les réductions famille nous font économiser
                                    beaucoup. Excellent service !</span>
                                <i class="bi bi-quote quote-icon-right"></i>
                            </p>
                            <img src="assets/img/person/person-m-12.webp" class="testimonial-img" alt="">
                            <h3>Sékou Coulibaly</h3>
                            <h4>Fonctionnaire à Korhogo</h4>
                            <div class="rating">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                        </div>
                    </div><!-- Fin témoignage -->

                    <div class="swiper-slide">
                        <div class="testimonial-item">
                            <p>
                                <i class="bi bi-quote quote-icon-left"></i>
                                <span>En tant que commerçant, je fais souvent Abidjan-Daloa. La plateforme me permet de
                                    gérer facilement mes réservations récurrentes. Les notifications SMS avant le départ
                                    sont très pratiques !</span>
                                <i class="bi bi-quote quote-icon-right"></i>
                            </p>
                            <img src="assets/img/person/person-m-13.webp" class="testimonial-img" alt="">
                            <h3>Jean Aké</h3>
                            <h4>Commerçant à Daloa</h4>
                            <div class="rating">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star"></i>
                            </div>
                        </div>
                    </div><!-- Fin témoignage -->

                    <div class="swiper-slide">
                        <div class="testimonial-item">
                            <p>
                                <i class="bi bi-quote quote-icon-left"></i>
                                <span>Les voyages Man-Abidjan sont maintenant plus sûrs avec la réservation en ligne. Plus
                                    besoin de transporter de l'argent pour acheter le billet sur place. Paiement mobile
                                    sécurisé et e-ticket reçu instantanément !</span>
                                <i class="bi bi-quote quote-icon-right"></i>
                            </p>
                            <img src="assets/img/person/person-f-8.webp" class="testimonial-img" alt="">
                            <h3>Marie Touré</h3>
                            <h4>Infirmière à Man</h4>
                            <div class="rating">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                        </div>
                    </div><!-- Fin témoignage -->

                    <div class="swiper-slide">
                        <div class="testimonial-item">
                            <p>
                                <i class="bi bi-quote quote-icon-left"></i>
                                <span>Service client disponible 7j/7 ! J'ai dû annuler mon trajet Abengourou-Abidjan et le
                                    remboursement a été traité en 48h. Transparence et efficacité, je ne réserve plus
                                    ailleurs !</span>
                                <i class="bi bi-quote quote-icon-right"></i>
                            </p>
                            <img src="assets/img/person/person-m-7.webp" class="testimonial-img" alt="">
                            <h3>Yao N'Guessan</h3>
                            <h4>Agriculteur à Abengourou</h4>
                            <div class="rating">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-half"></i>
                            </div>
                        </div>
                    </div><!-- Fin témoignage -->

                    <div class="swiper-slide">
                        <div class="testimonial-item">
                            <p>
                                <i class="bi bi-quote quote-icon-left"></i>
                                <span>Idéal pour les voyages étudiants ! Les réductions sur les trajets réguliers et la
                                    facilité de changement de billet m'ont beaucoup aidé pendant mes études à l'Université
                                    Félix Houphouët-Boigny.</span>
                                <i class="bi bi-quote quote-icon-right"></i>
                            </p>
                            <img src="assets/img/person/person-f-9.webp" class="testimonial-img" alt="">
                            <h3>Chantal Bamba</h3>
                            <h4>Étudiante à Cocody</h4>
                            <div class="rating">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                        </div>
                    </div><!-- Fin témoignage -->

                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>

    </section><!-- /Section Témoignages -->

    <!-- Section Newsletter & Avantages -->
<section id="call-to-action" class="call-to-action section light-background">

    <!-- Section Compteur Utilisateurs -->
    <div class="container mb-5" data-aos="fade-up">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white p-5 rounded-xl shadow-lg border border-orange-100 text-center relative overflow-hidden group hover:shadow-xl transition-all duration-300">
                    <!-- Background Elements -->
                    <div class="absolute top-0 left-0 w-24 h-24 bg-orange-50 rounded-br-full opacity-50"></div>
                    <div class="absolute bottom-0 right-0 w-32 h-32 bg-orange-50 rounded-tl-full opacity-50"></div>
                    
                    <div class="relative z-10">
                        <div class="inline-flex items-center justify-center p-3 bg-orange-100 rounded-full mb-4 group-hover:scale-110 transition-transform duration-300">
                            <i class="bi bi-people-fill text-3xl text-[#e94f1b]"></i>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Ils nous font confiance</h3>
                        
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <span data-purecounter-start="0" 
                                  data-purecounter-end="{{ $usersCount ?? 0 }}" 
                                  data-purecounter-duration="2" 
                                  class="purecounter text-5xl font-extrabold text-[#e94f1b]">
                                0
                            </span>
                            <span class="text-3xl font-bold text-[#e94f1b]">+</span>
                        </div>
                        
                        <p class="text-gray-600 font-medium">Utilisateurs inscrits sur la plateforme</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="newsletter-section" data-aos="fade-up" data-aos-delay="300">
            <div class="newsletter-card">
                <div class="newsletter-content">
                    <div class="newsletter-icon">
                        <i class="bi bi-bus-front"></i>
                    </div>
                    <div class="newsletter-text">
                        <h3>Restez Informé</h3>
                        <p>Recevez nos meilleures offres de billets et les nouveautés sur les trajets en Côte d'Ivoire</p>
                    </div>
                </div>

                <form class="php-email-form newsletter-form" action="forms/newsletter.php" method="post">
                    <div class="form-wrapper">
                        <input type="email" name="email" class="email-input" placeholder="Votre adresse email"
                            required="">
                        <button type="submit" class="subscribe-btn">
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>

                    <div class="loading">Chargement</div>
                    <div class="error-message"></div>
                    <div class="sent-message">Inscription réussie ! Consultez votre email pour nos offres spéciales.</div>

                    <div class="trust-indicators">
                        <i class="bi bi-lock"></i>
                        <span>Vos données sont protégées. Désinscription à tout moment.</span>
                    </div>
                </form>
            </div>
        </div>

        <div class="benefits-showcase" data-aos="fade-up" data-aos-delay="400">
            <div class="benefits-header">
                <h3>Pourquoi Voyager avec Nous</h3>
                <p>Découvrez les avantages de réserver vos billets de car en ligne</p>
            </div>

            <div class="benefits-grid">
                <div class="benefit-card" data-aos="flip-left" data-aos-delay="450">
                    <div class="benefit-visual">
                        <div class="benefit-icon-wrap">
                            <i class="bi bi-phone"></i>
                        </div>
                        <div class="benefit-pattern"></div>
                    </div>
                    <div class="benefit-content">
                        <h4>Réservation Mobile</h4>
                        <p>Réservez vos billets en quelques clics depuis votre smartphone, 24h/24 et 7j/7</p>
                    </div>
                </div>

                <div class="benefit-card" data-aos="flip-left" data-aos-delay="500">
                    <div class="benefit-visual">
                        <div class="benefit-icon-wrap">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="benefit-pattern"></div>
                    </div>
                    <div class="benefit-content">
                        <h4>Paiement Sécurisé</h4>
                        <p>Transactions protégées avec options de paiement adaptées (Mobile Money, Carte bancaire)</p>
                    </div>
                </div>

                <div class="benefit-card" data-aos="flip-left" data-aos-delay="550">
                    <div class="benefit-visual">
                        <div class="benefit-icon-wrap">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="benefit-pattern"></div>
                    </div>
                    <div class="benefit-content">
                        <h4>Modification Facile</h4>
                        <p>Changez votre date ou heure de voyage facilement depuis votre compte en ligne</p>
                    </div>
                </div>
            </div>

            <div class="benefits-grid mt-4">
                <div class="benefit-card" data-aos="flip-left" data-aos-delay="600">
                    <div class="benefit-visual">
                        <div class="benefit-icon-wrap">
                            <i class="bi bi-tags"></i>
                        </div>
                        <div class="benefit-pattern"></div>
                    </div>
                    <div class="benefit-content">
                        <h4>Meilleurs Prix</h4>
                        <p>Comparaison automatique des prix des différentes compagnies de transport</p>
                    </div>
                </div>

                <div class="benefit-card" data-aos="flip-left" data-aos-delay="650">
                    <div class="benefit-visual">
                        <div class="benefit-icon-wrap">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div class="benefit-pattern"></div>
                    </div>
                    <div class="benefit-content">
                        <h4>Réseau National</h4>
                        <p>Accès à toutes les gares routières de Côte d'Ivoire et à leurs horaires en temps réel</p>
                    </div>
                </div>

                <div class="benefit-card" data-aos="flip-left" data-aos-delay="700">
                    <div class="benefit-visual">
                        <div class="benefit-icon-wrap">
                            <i class="bi bi-headset"></i>
                        </div>
                        <div class="benefit-pattern"></div>
                    </div>
                    <div class="benefit-content">
                        <h4>Support Client</h4>
                        <p>Équipe disponible pour vous aider avant, pendant et après votre voyage</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Téléchargement App Mobile -->
        {{-- <div class="app-download-section mt-5" data-aos="fade-up" data-aos-delay="800">
            <div class="app-card">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="app-content">
                            <h3>Téléchargez Notre Application</h3>
                            <p>Gérez vos réservations, recevez des notifications et accédez à vos billets même hors ligne</p>
                            
                            <div class="app-features">
                                <div class="feature-item">
                                    <i class="bi bi-qr-code"></i>
                                    <span>E-billet QR Code</span>
                                </div>
                                <div class="feature-item">
                                    <i class="bi bi-bell"></i>
                                    <span>Alertes trafic</span>
                                </div>
                                <div class="feature-item">
                                    <i class="bi bi-wallet2"></i>
                                    <span>Paiement mobile</span>
                                </div>
                            </div>
                            
                            <div class="download-buttons mt-4">
                                <a href="#" class="btn-app-store">
                                    <i class="bi bi-apple"></i>
                                    <div>
                                        <small>Télécharger sur</small>
                                        <span>App Store</span>
                                    </div>
                                </a>
                                <a href="#" class="btn-play-store">
                                    <i class="bi bi-google-play"></i>
                                    <div>
                                        <small>Disponible sur</small>
                                        <span>Google Play</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-center">
                        <div class="app-preview">
                            <img src="assets/img/app-mockup.png" alt="Application mobile" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

    </div>
</section>
@endsection
