@extends('home.layouts.template')
@section('content')

    <!-- Hero Destination - Modern & Minimalist -->
    <section class="destination-hero-section"
        style="background: linear-gradient(rgba(5, 30, 35, 0.8), rgba(5, 30, 35, 0.8)), url('{{ asset('assets/img/travel/destination-15.webp') }}') center/cover no-repeat;">
        <div class="container">
            <div class="row align-items-center py-4">
                <div class="col-lg-8 offset-lg-2 text-center" data-aos="fade-up">
                    <div class="hero-badge mb-3">Réseau National</div>
                    <h1 class="hero-title text-white mb-3">
                        @if(request('compagnie_id') && $itineraires->isNotEmpty())
                            Itinéraires de <span style="color: #e94f1b;">{{ $itineraires->first()->compagnie->name }}</span>
                        @else
                            Toutes nos <span style="color: #e94f1b;">Destinations</span>
                        @endif
                    </h1>
                    <p class="hero-subtitle text-white-50 mb-4 mx-auto" style="max-width: 600px;">
                        @if(request('compagnie_id'))
                            Découvrez les trajets spécifiques proposés par cette compagnie.
                        @else
                            Explorez les liaisons interurbaines proposées par les meilleures compagnies de transport en Côte
                            d'Ivoire.
                        @endif
                    </p>
                    @if(request('compagnie_id'))
                        <div class="mb-4">
                            <a href="{{ route('home.destination') }}" class="btn btn-outline-light btn-sm rounded-pill px-4">
                                <i class="bi bi-x-circle me-2"></i>Afficher tous les itinéraires
                            </a>
                        </div>
                    @endif
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center bg-transparent p-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50">Accueil</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Destinations</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <!-- Search & Statistics Bar -->
    <section class="search-stats-section py-4">
        <div class="container">
            <div class="stats-card-wrapper bg-white shadow-lg rounded-4 p-3 mb-5 mt-n5 position-relative"
                style="margin-top: 70px;">
                <div class="row align-items-center">
                    <div class="col-md-7 border-end">
                        <div class="search-input-group d-flex align-items-center px-3">
                            <i class="bi bi-search text-muted fs-4"></i>
                            <input type="text" id="destinationSearch"
                                class="form-control border-0 shadow-none fs-5 py-2 ms-2"
                                placeholder="Où souhaitez-vous aller ? (ex: Bouaké, San Pedro...)">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="d-flex justify-content-around text-center">
                            <div class="stat-item">
                                <span class="d-block fw-bold fs-4 text-dark">{{ $itineraires->count() }}</span>
                                <small class="text-muted text-uppercase"
                                    style="font-size: 0.7rem; letter-spacing: 1px;">Itinéraires</small>
                            </div>
                            <div class="stat-item border-start ps-4">
                                <span
                                    class="d-block fw-bold fs-4 text-dark">{{ $itineraires->unique('compagnie_id')->count() }}</span>
                                <small class="text-muted text-uppercase"
                                    style="font-size: 0.7rem; letter-spacing: 1px;">Compagnies</small>
                            </div>
                            <div class="stat-item border-start ps-4">
                                <span class="d-block fw-bold fs-4 text-dark">24/7</span>
                                <small class="text-muted text-uppercase"
                                    style="font-size: 0.7rem; letter-spacing: 1px;">Service</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Destinations Feed -->
    <section class="py-5 bg-light-soft">
        <div class="container">
            <div class="row g-4" id="itinerairesContainer">
                @forelse($itineraires as $itineraire)
                    <div class="col-lg-4 col-md-6 itineraire-card-wrapper" data-aos="fade-up"
                        data-aos-delay="{{ ($loop->index % 3) * 100 }}">
                        <div class="itinerary-ticket-card">
                            <!-- Top: Company Info -->
                            <div class="ticket-header d-flex align-items-center p-4 border-bottom">
                                <div class="company-logo-container bg-light rounded-circle d-flex align-items-center justify-content-center p-2"
                                    style="width: 50px; height: 50px;">
                                    @if($itineraire->compagnie && $itineraire->compagnie->path_logo)
                                        <img src="{{ asset('storage/' . $itineraire->compagnie->path_logo) }}"
                                            alt="{{ $itineraire->compagnie->name }}" class="img-fluid rounded-circle">
                                    @else
                                        <i class="bi bi-building fs-4 text-primary"></i>
                                    @endif
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0 fw-bold text-dark">{{ $itineraire->compagnie->name ?? 'Compagnie Express' }}
                                    </h6>
                                    @if($itineraire->compagnie && $itineraire->compagnie->slogan)
                                        <p class="mb-0 text-muted italic" style="font-size: 0.7rem;">
                                            "{{ $itineraire->compagnie->slogan }}"</p>
                                    @endif
                                    <span class="badge rounded-pill bg-light text-primary border mt-1"
                                        style="font-size: 0.65rem;">Officiel</span>
                                </div>
                                <div class="ms-auto">
                                    <span class="text-muted small"><i
                                            class="bi bi-shield-check text-success me-1"></i>Vérifié</span>
                                </div>
                            </div>

                            <!-- Mid: Route Info -->
                            <div class="ticket-body p-4">
                                <div
                                    class="route-display position-relative d-flex justify-content-between align-items-center mb-4">
                                    <div class="city-box text-start">
                                        <span class="text-muted text-uppercase small" style="font-size: 0.65rem;">Départ</span>
                                        <h4 class="mb-0 fw-bold text-dark">{{ $itineraire->point_depart }}</h4>
                                    </div>

                                    <div class="route-visual flex-grow-1 px-3 text-center">
                                        <div class="route-line-modern">
                                            <i class="bi bi-bus-front text-primary fs-5"></i>
                                        </div>
                                    </div>

                                    <div class="city-box text-end">
                                        <span class="text-muted text-uppercase small" style="font-size: 0.65rem;">Arrivée</span>
                                        <h4 class="mb-0 fw-bold text-dark">{{ $itineraire->point_arrive }}</h4>
                                    </div>
                                </div>

                                <div class="additional-details d-flex gap-3 mb-4">
                                    <div class="detail-tag">
                                        <i class="bi bi-clock"></i> {{ $itineraire->durer_parcours }}
                                    </div>
                                    <div class="detail-tag">
                                        <i class="bi bi-geo-alt"></i> {{ $itineraire->compagnie->commune ?? 'Côte d\'Ivoire' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom: Action -->
                            <div class="ticket-footer p-4 pt-0">
                                <a href="{{ route('login') }}" class="btn btn-modern-action w-100 py-3 rounded-3">
                                    <span class="me-2">Réserver ce trajet</span>
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="empty-state p-5 bg-white rounded-4 shadow-sm">
                            <i class="bi bi-geo-alt-fill text-muted" style="font-size: 4rem; opacity: 0.2;"></i>
                            <h4 class="mt-3 text-dark">Aucun itinéraire disponible</h4>
                            <p class="text-muted">Nous n'avons trouvé aucun trajet correspondant à votre recherche.</p>
                            <a href="{{ route('home') }}" class="btn btn-link text-primary mt-2">Retour à l'accueil</a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <style>
        /* Global Section Styles */
        .bg-light-soft {
            background-color: #f8fafb;
        }

        /* Hero Section */
        .destination-hero-section {
            padding: 120px 0 40px;
            position: relative;
            overflow: hidden;
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

        /* Stats Card Overlay */
        .mt-n5 {
            margin-top: -5rem;
        }

        .stats-card-wrapper {
            z-index: 100;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Itinerary Ticket Card Design */
        .itinerary-ticket-card {
            background: white;
            border-radius: 24px;
            border: 1px solid rgba(0, 0, 0, 0.03);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            transition: all 0.4s ease;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .itinerary-ticket-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            border-color: rgba(254, 162, 25, 0.2);
        }

        /* Route Visual Styles */
        .route-line-modern {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .route-line-modern::before {
            content: "";
            position: absolute;
            top: 50%;
            left: -10px;
            right: -10px;
            height: 2px;
            background: #e9ecef;
            z-index: 0;
        }

        .route-line-modern i {
            background: white;
            padding: 0 10px;
            position: relative;
            z-index: 1;
        }

        /* Details Tags */
        .detail-tag {
            font-size: 0.75rem;
            color: #6c757d;
            background: #f8f9fa;
            padding: 6px 12px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .detail-tag i {
            color: #e94f1b;
        }

        /* Modern CTA Button */
        .btn-modern-action {
            background-color: #051e23;
            color: white;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-modern-action:hover {
            background-color: #e94f1b;
            color: white;
            transform: scale(1.02);
        }

        /* Breadcrumb Customization */
        .breadcrumb-item+.breadcrumb-item::before {
            color: rgba(255, 255, 255, 0.3);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .mt-n5 {
                margin-top: -3rem;
            }

            .stats-card-wrapper .border-end {
                border-end: none !important;
                border-bottom: 1px solid #eee;
                margin-bottom: 15px;
                padding-bottom: 15px;
            }

            .route-visual {
                padding: 0 5x;
            }

            .city-box h4 {
                font-size: 1.1rem;
            }
        }
    </style>

    <script>
        // Real-time Filtering
        document.getElementById('destinationSearch').addEventListener('input', function (e) {
            const query = e.target.value.toLowerCase().trim();
            const cards = document.querySelectorAll('.itineraire-card-wrapper');
            let count = 0;

            cards.forEach(card => {
                const depart = card.querySelector('.city-box:first-child h4').textContent.toLowerCase();
                const arrive = card.querySelector('.city-box:last-child h4').textContent.toLowerCase();
                const company = card.querySelector('.ticket-header h6').textContent.toLowerCase();

                if (depart.includes(query) || arrive.includes(query) || company.includes(query)) {
                    card.style.display = 'block';
                    setTimeout(() => card.style.opacity = '1', 10);
                    count++;
                } else {
                    card.style.opacity = '0';
                    setTimeout(() => card.style.display = 'none', 300);
                }
            });

            // Toggle Empty State (Optional: could add a dynamic empty state if count === 0)
        });
    </script>

@endsection