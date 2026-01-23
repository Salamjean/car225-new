@extends('home.layouts.template')
@section('content')

    <!-- Hero Company - Immersive Background -->
    <section class="company-hero-section"
        style="background: linear-gradient(rgba(5, 30, 35, 0.8), rgba(5, 30, 35, 0.8)), url('{{ asset('assets/img/travel/destination-10.webp') }}') center/cover no-repeat;">
        <div class="container">
            <div class="row align-items-center py-5">
                <div class="col-lg-8 offset-lg-2 text-center" data-aos="fade-up">
                    <div class="hero-badge mb-3">Partenaires de Confiance</div>
                    <h1 class="hero-title text-white mb-3">Nos <span style="color: #e94f1b;">Compagnies</span> Partenaires
                    </h1>
                    <p class="hero-subtitle text-white-50 mb-4 mx-auto" style="max-width: 600px;">
                        Découvrez les transporteurs qui assurent vos trajets en toute sécurité à travers le pays.
                    </p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center bg-transparent p-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50">Accueil</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Compagnies</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <!-- Company Stats Bar -->
    <section class="py-4">
        <div class="container">
            <div class="stats-card-wrapper bg-white shadow-lg rounded-4 p-4 position-relative"
                style="margin-top: 15px; z-index: 10; " >
                <div class="row g-4 text-center">
                    <div class="col-md-4 border-end">
                        <div class="stat-content">
                            <h2 class="fw-bold text-dark mb-0">{{ $compagnies->count() }}</h2>
                            <p class="text-muted text-uppercase small mb-0">Partenaires Actifs</p>
                        </div>
                    </div>
                    <div class="col-md-4 border-end">
                        <div class="stat-content">
                            <h2 class="fw-bold text-dark mb-0">100%</h2>
                            <p class="text-muted text-uppercase small mb-0">Sécurité Garantie</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-content">
                            <h2 class="fw-bold text-dark mb-0">24/7</h2>
                            <p class="text-muted text-uppercase small mb-0">Disponibilité</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Companies Grid - Modern Card Style with Logo in Header -->
    <section class="py-5 bg-light-soft">
        <div class="container">
            <div class="section-header text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Choisissez votre <span style="color: #e94f1b;">Transporteur</span></h2>
                <p class="text-muted">Des compagnies certifiées pour un voyage en toute sérénité</p>
            </div>

            <div class="row g-4">
                @forelse($compagnies as $compagnie)
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}">
                        <div class="company-card-modern">
                            <div class="card-logo-header bg-white d-flex align-items-center justify-content-center p-4">
                                @if($compagnie->path_logo)
                                    <img src="{{ asset('storage/' . $compagnie->path_logo) }}" alt="{{ $compagnie->name }}"
                                        class="main-company-logo">
                                @else
                                    <div class="placeholder-logo-large">{{ substr($compagnie->name, 0, 2) }}</div>
                                @endif
                                <div class="vehicle-icon-badge">
                                    <i class="bi bi-bus-front-fill"></i>
                                </div>
                            </div>
                            <div class="card-body-modern p-4 text-center border-top">
                                <h4 class="company-name fw-bold text-dark mb-2">{{ $compagnie->name }}</h4>
                                @if($compagnie->slogan)
                                    <p class="company-slogan italic text-muted small mb-3">"{{ $compagnie->slogan }}"</p>
                                @endif

                                <div class="company-info-tags d-flex justify-content-center gap-2 mb-4">
                                    <span class="info-tag"><i
                                            class="bi bi-geo-alt me-1"></i>{{ $compagnie->commune ?? 'Côte d\'Ivoire' }}</span>
                                    <span class="info-tag"><i class="bi bi-star-fill text-warning me-1"></i>4.9</span>
                                </div>

                                <div class="d-grid">
                                    <a href="{{ route('home.destination', ['compagnie_id' => $compagnie->id]) }}"
                                        class="btn btn-company-action">
                                        Voir les Itinéraires <i class="bi bi-chevron-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="empty-state p-5 bg-white rounded-4 shadow-sm">
                            <i class="bi bi-building text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                            <h4 class="mt-3">Bientôt de nouvelles compagnies</h4>
                            <p class="text-muted">Revenez bientôt pour découvrir nos nouveaux partenaires.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <style>
        .bg-light-soft {
            background-color: #f8fafb;
        }

        /* Hero */
        .company-hero-section {
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

        /* Company Card Modern */
        .company-card-modern {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.4s ease;
            height: 100%;
            border: 1px solid rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
        }

        .company-card-modern:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .card-logo-header {
            height: 180px;
            position: relative;
            background: radial-gradient(circle, #ffffff 0%, #f8f9fa 100%);
        }

        .main-company-logo {
            max-width: 80%;
            max-height: 120px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .company-card-modern:hover .main-company-logo {
            transform: scale(1.05);
        }

        .placeholder-logo-large {
            width: 120px;
            height: 120px;
            background: #e94f1b;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 3rem;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(254, 162, 25, 0.2);
        }

        .vehicle-icon-badge {
            position: absolute;
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 50px;
            background: #e94f1b;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 10px rgba(254, 162, 25, 0.3);
            z-index: 5;
            border: 4px solid white;
        }

        .card-body-modern {
            padding-top: 30px !important;
            flex-grow: 1;
        }

        .info-tag {
            font-size: 0.75rem;
            background: #f1f3f5;
            padding: 5px 12px;
            border-radius: 50px;
            color: #495057;
        }

        .btn-company-action {
            background: #051e23;
            color: white;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-company-action:hover {
            background: #e94f1b;
            color: white;
            box-shadow: 0 5px 15px rgba(254, 162, 25, 0.3);
        }

        @media (max-width: 768px) {
            .stats-card-wrapper .border-end {
                border-end: none !important;
                border-bottom: 1px solid #eee;
                padding-bottom: 15px;
            }
        }
    </style>

@endsection