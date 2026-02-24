@extends('home.layouts.template')
@section('content')
<!-- Travel Hero Section -->
    @include('home.layouts.carrousel')
    <!-- /Travel Hero Section -->

    <!-- ============================================ -->
    <!-- SECTION TRAJETS POPULAIRES -->
    <!-- ============================================ -->
    <section id="trajets-populaires" class="trajets-populaires-section">
        <div class="container">
            <h2 class="trajets-section-title" data-aos="fade-up">Trajets Populaires</h2>

            <div class="trajets-grid" data-aos="fade-up" data-aos-delay="100">
                @forelse($trajetsPopulaires as $programme)
                <div class="trajet-card" onclick="void(0)">
                    <div class="trajet-card-header">
                        <div class="trajet-route">
                            <div class="trajet-point">
                                <span class="trajet-city">{{ $programme->point_depart }}</span>
                                <div class="text-[10px] text-blue-600 font-bold uppercase mb-1">{{ $programme->gareDepart->nom_gare ?? 'Gare' }}</div>
                                <span class="trajet-label">Départ</span>
                                <div class="bg-blue-50 text-blue-700 text-[10px] font-black px-2 py-0.5 rounded mt-1 flex items-center justify-center gap-1">
                                    <i class="bi bi-clock-fill"></i> {{ substr($programme->heure_depart, 0, 5) }}
                                </div>
                            </div>
                            <div class="trajet-arrow">
                                <i class="bi bi-chevron-right"></i>
                            </div>
                            <div class="trajet-point">
                                <span class="trajet-city">{{ $programme->point_arrive }}</span>
                                <div class="text-[10px] text-green-600 font-bold uppercase mb-1">{{ $programme->gareArrivee->nom_gare ?? 'Gare' }}</div>
                                <span class="trajet-label">Arrivée</span>
                            </div>
                        </div>
                    </div>
                    <div class="trajet-card-body">
                        <div class="trajet-price">
                            <span class="trajet-amount">{{ number_format($programme->montant_billet, 0, ',', ',') }} F</span>
                            <span class="trajet-badge">{{ $programme->places_disponibles }} Places disponible</span>
                        </div>
                        <div class="trajet-duration">
                            <i class="bi bi-clock"></i>
                            <span>{{ $programme->durer_parcours ?? '—' }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <!-- Fallback cards si aucun programme -->
                @for($i = 0; $i < 4; $i++)
                <div class="trajet-card" onclick="void(0)">
                    <div class="trajet-card-header">
                        <div class="trajet-route">
                            <div class="trajet-point">
                                <span class="trajet-city">Abidjan</span>
                                <span class="trajet-label">Départ</span>
                                <div class="bg-blue-50 text-blue-700 text-[10px] font-black px-2 py-0.5 rounded mt-1 flex items-center justify-center gap-1">
                                    <i class="bi bi-clock-fill"></i> 08:30
                                </div>
                            </div>
                            <div class="trajet-arrow">
                                <i class="bi bi-chevron-right"></i>
                            </div>
                            <div class="trajet-point">
                                <span class="trajet-city">Yamoussoukro</span>
                                <span class="trajet-label">Arrivée</span>
                            </div>
                        </div>
                    </div>
                    <div class="trajet-card-body">
                        <div class="trajet-price">
                            <span class="trajet-amount">4,500 F</span>
                            <span class="trajet-badge">8 trajets</span>
                        </div>
                        <div class="trajet-duration">
                            <i class="bi bi-clock"></i>
                            <span>2h 30min</span>
                        </div>
                    </div>
                </div>
                @endfor
                @endforelse
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- SECTION PRÊT À VOYAGER (CTA) -->
    <!-- ============================================ -->
    <section id="pret-a-voyager" class="pret-a-voyager-section">
        <div class="container">
            <div class="cta-content" data-aos="fade-up">
                <h2 class="cta-title">Prêt à voyager ?</h2>
                <p class="cta-subtitle">Réservez votre billet maintenant et économisez jusqu'à 50 % sur vos trajets</p>
                <a href="{{ route('programmes.all') }}" class="cta-btn">Réserver Maintenant</a>
            </div>
        </div>
    </section>

<!-- ============================================ -->
<!-- STYLES INLINE FOR TRAJETS + CTA -->
<!-- ============================================ -->
<style>
/* ============================================
   TRAJETS POPULAIRES SECTION
   ============================================ */
.trajets-populaires-section {
    padding: 100px 0 60px;
    background-color: #ffffff;
}

.trajets-section-title {
    text-align: center;
    font-size: 28px;
    font-weight: 750;
    color: #000000;
    margin-bottom: 50px;
    font-family: 'Poppins', sans-serif;
}

.trajets-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
}

.trajet-card {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: default;
}

.trajet-card:hover {
    border-color: #e94f1b;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    transform: translateY(-5px);
}

.trajet-card-header {
    padding: 24px 20px 16px;
    border-bottom: 1px solid #f2f2f2;
}

.trajet-route {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.trajet-point {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.trajet-city {
    font-size: 15px;
    font-weight: 600;
    color: #1a1a1a;
}

.trajet-label {
    font-size: 11px;
    color: #888;
    text-transform: capitalize;
}

.trajet-arrow {
    display: flex;
    align-items: center;
    justify-content: center;
    color: #333;
    font-size: 20px;
}

.trajet-card-body {
    padding: 20px;
}

.trajet-price {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}

.trajet-amount {
    font-size: 20px;
    font-weight: 700;
    color: #e94f1b;
}

.trajet-badge {
    font-size: 11px;
    font-weight: 600;
    color: #e94f1b;
    background: #fff5f2;
    padding: 4px 12px;
    border-radius: 6px;
    border: 1px solid #ffe8e0;
}

.trajet-duration {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #666;
}

.trajet-duration i {
    font-size: 14px;
    color: #333;
}

/* Responsive - Trajets */
@media (max-width: 1199px) {
    .trajets-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 991px) {
    .trajets-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 575px) {
    .trajets-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
}

/* ============================================
   PRÊT À VOYAGER CTA SECTION
   ============================================ */
.pret-a-voyager-section {
    padding: 0 !important;
    margin: 0 !important;
    background-color: #004a29 !important;
    border: none !important;
}

.pret-a-voyager-section .cta-content {
    background-color: #004a29 !important;
    padding: 80px 20px !important;
    text-align: center;
    border: none !important;
}

/* Ensure the main container doesn't have padding at the bottom */
.main {
    padding-bottom: 0 !important;
}
.footer {
    margin-top: 0 !important;
}

/* Make it full width like in the image */
@media (min-width: 1200px) {
    .pret-a-voyager-section .container {
        max-width: 100%;
        padding: 0;
    }
}

.pret-a-voyager-section .cta-title {
    font-size: 48px;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 20px;
}

.pret-a-voyager-section .cta-subtitle {
    font-size: 18px;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 40px;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

.pret-a-voyager-section .cta-btn {
    display: inline-block;
    padding: 18px 45px;
    background-color: #ffffff;
    color: #004a29;
    font-size: 16px;
    font-weight: 700;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.pret-a-voyager-section .cta-btn:hover {
    background-color: #f0f0f0;
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .pret-a-voyager-section .cta-title {
        font-size: 32px;
    }
    .pret-a-voyager-section .cta-subtitle {
        font-size: 16px;
    }
}
</style>
@endsection
