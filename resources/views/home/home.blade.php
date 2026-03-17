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
    <!-- SECTION COMPTEUR DE CONFIANCE -->
    <!-- ============================================ -->
    <section id="trust-counter" class="trust-counter-section">
        <div class="container">
            <div class="trust-content text-center" data-aos="fade-up">
                <h2 class="trust-title text-[#1a1a1a] font-black md:text-3xl text-2xl mx-auto mb-8" style="line-height: 1.4; max-width: 900px;">
                    Avec plus de <span class="counter-highlight text-[#008000] text-4xl inline-block mx-1" data-target="{{ $usersCount ?? 0 }}">0</span>utilisateurs, <br class="hidden md:block">
                    <span class="text-[#e94e1a]">Car225</span> est le partenaire de confiance pour vos trajets en Côte d’Ivoire.
                </h2>
                

                
                <!-- DESTINATION BANNER (Orange Theme) -->
                <div class="destination-promo mt-8 w-full relative rounded-2xl overflow-hidden flex flex-col md:flex-row items-center border border-[#ffedd5]" style="background: linear-gradient(135deg, #fff6f0 0%, #ffedd5 100%); min-height: 360px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
                    <!-- Content -->
                    <div class="p-10 md:p-16 z-20 md:w-1/2 text-left">
                        <h3 class="text-[#2d3748] text-2xl md:text-3xl font-black mb-3 leading-tight opacity-90">Quelle sera votre prochaine destination ?</h3>
                        <p class="text-[#4a5568] mb-6 text-sm md:text-base leading-relaxed max-w-sm font-medium">Découvrez notre carte du réseau avec de nombreuses destinations à travers la Côte d'Ivoire.</p>
                        <a href="{{ route('programmes.all') }}" class="inline-flex cursor-pointer text-sm items-center justify-center bg-[#e94e1a] hover:bg-[#d14316] text-white font-bold py-3 px-6 rounded-lg shadow-lg transition-all duration-300">
                            <i class="fas fa-map mr-2"></i> Voir les itinéraires
                        </a>
                    </div>
                    
                    <!-- Decorative Graphic Background -->
                    <div class="absolute inset-0 z-10 w-full h-full pointer-events-none opacity-80">
                        <!-- City Skyline SVG -->
                        <svg viewBox="0 0 1000 300" preserveAspectRatio="xMaxYMax slice" class="absolute right-0 bottom-0 h-[100%] w-[150%] md:w-[100%] mix-blend-multiply opacity-20">
                            <path fill="#fdb391" d="M300,300 L300,220 L330,220 L330,150 L360,150 L360,200 L400,200 L400,100 L430,100 L430,180 L490,180 L490,120 L550,120 L550,230 L590,230 L590,160 L650,160 L650,250 L690,250 L690,190 L740,190 L740,240 L800,240 L800,130 L850,130 L850,220 L900,220 L900,100 L950,100 L950,300 Z"></path>
                            <path fill="#fa986a" d="M350,300 L350,250 L380,250 L380,180 L420,180 L420,270 L470,270 L470,210 L510,210 L510,140 L560,140 L560,240 L620,240 L620,170 L680,170 L680,260 L720,260 L720,200 L770,200 L770,280 L820,280 L820,160 L880,160 L880,250 L930,250 L930,180 L1000,180 L1000,300 Z"></path>
                            <path fill="#f5763b" d="M400,300 L400,280 L440,280 L440,220 L480,220 L480,290 L530,290 L530,240 L590,240 L590,190 L640,190 L640,270 L670,270 L670,220 L720,220 L720,300 L780,300 L780,260 L840,260 L840,210 L890,210 L890,280 L960,280 L960,230 L1000,230 L1000,300 Z"></path>
                        </svg>
                        
                        <!-- Floating Location Tags -->
                        <div class="hidden md:flex absolute right-[25%] top-[15%] bg-white/90 backdrop-blur-sm rounded-lg p-2 shadow-sm items-center gap-2 transform">
                            <div class="bg-gray-800 text-[#e94e1a] rounded px-1.5 py-1 text-[10px]"><i class="fas fa-ticket-alt"></i></div>
                            <div>
                                <div class="text-[11px] font-bold text-gray-700 leading-none mb-0.5">Yamoussoukro</div>
                                <div class="text-[9px] text-gray-500 leading-none">2h30m · Trajet direct</div>
                            </div>
                        </div>

                        <div class="hidden md:flex absolute right-[12%] top-[35%] bg-white/90 backdrop-blur-sm rounded-lg p-2 shadow-sm items-center gap-2 transform -rotate-1">
                            <div class="bg-gray-800 text-[#e94e1a] rounded px-1.5 py-1 text-[10px]"><i class="fas fa-ticket-alt"></i></div>
                            <div>
                                <div class="text-[11px] font-bold text-gray-700 leading-none mb-0.5">Bouaké</div>
                                <div class="text-[9px] text-gray-500 leading-none">4h45m · Trajet direct</div>
                            </div>
                        </div>

                        <div class="hidden md:flex absolute right-[35%] top-[55%] bg-white/90 backdrop-blur-sm rounded-lg p-2 shadow-sm items-center gap-2 transform rotate-2">
                            <div class="bg-gray-800 text-[#e94e1a] rounded px-1.5 py-1 text-[10px]"><i class="fas fa-ticket-alt"></i></div>
                            <div>
                                <div class="text-[11px] font-bold text-gray-700 leading-none mb-0.5">Abidjan</div>
                                <div class="text-[9px] text-gray-500 leading-none">Départs fréquents</div>
                            </div>
                        </div>
                        
                        <!-- Car225 Logo -->
                        <div class="absolute right-[5%] bottom-4 text-[#e94e1a] drop-shadow-md flex items-center gap-2">
                            <img src="{{ asset('assetsPoster/assets/images/Car225_favicon.png') }}" class="h-10 w-10 md:h-12 md:w-12 object-contain" alt="Car225 Logo">
                            <span class="font-black text-xl italic mt-1 opacity-90" style="letter-spacing: -1px;">car<span class="text-[#2d3748]">225</span></span>
                        </div>
                    </div>
                </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- SECTION NOS PARTENAIRES -->
    <!-- ============================================ -->
    <section id="nos-partenaires" class="py-16 bg-white">
        <div class="container mx-auto px-4 text-center" data-aos="fade-up">
            <h2 class="trajets-section-title !mb-12">Nos Partenaires</h2>
            
            @if(isset($compagnies) && $compagnies->count() > 0)
            <div class="flex flex-wrap justify-center items-center gap-12 md:gap-24 opacity-50 hover:opacity-100 transition-opacity duration-500">
                @foreach($compagnies->take(6) as $comp)
                    @if($comp->path_logo)
                        <img src="{{ asset('storage/' . $comp->path_logo) }}" alt="{{ $comp->nom_compagnie ?? 'Compagnie' }}" class="h-10 md:h-14 object-contain grayscale hover:grayscale-0 transition-all duration-300">
                    @else
                        <span class="text-xl font-black text-gray-300 uppercase tracking-widest hover:text-gray-800 transition-colors duration-300">{{ $comp->nom_compagnie ?? 'CAR225' }}</span>
                    @endif
                @endforeach
            </div>
            @endif
        </div>
    </section>

    <!-- ============================================ -->
    <!-- SECTION VIDEO (Carte Orange Étirée Compacte) -->
    <!-- ============================================ -->
    <section id="video-presentation" class="py-4 bg-white">
        <div class="container mx-auto px-4" data-aos="fade-up">
            <!-- Carte orange étirée : le fond fusionne avec la vidéo -->
            <div class="w-full relative rounded-3xl overflow-hidden border-[6px] border-white shadow-2xl flex items-center justify-center py-6" 
                 style="background-color: #D36A1C;">
                
                <!-- Vidéo incrustée : scale augmenté à 1.05 pour masquer totalement les lignes de compression aux bords du fichier -->
                <div class="w-full max-w-[512px] overflow-hidden rounded-2xl">
                    <video autoplay loop muted playsinline class="w-full h-auto block scale-[1.05] transform-gpu origin-center" style="border: none; outline: none; box-shadow: none;">
                        <source src="{{ asset('assets/images/VideoCar225.mp4') }}" type="video/mp4">
                        Votre navigateur ne supporte pas la vidéo.
                    </video>
                </div>
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
    padding: 40px 20px 80px !important;
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

/* ============================================
   TRUST COUNTER SECTION
   ============================================ */
.trust-counter-section {
    padding: 100px 0 30px;
    background-color: #ffffff;
    border-top: 1px solid #f2f2f2;
}

.counter-highlight {
    font-family: 'Inter', 'Poppins', sans-serif;
    position: relative;
    top: 4px; /* Slight visual adjustment */
}
</style>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const counters = document.querySelectorAll('.counter-highlight');
        const speed = 100;

        const animateCounters = (counter) => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-target');
                const rawCount = counter.innerText.replace(/,/g, '');
                const count = +rawCount;
                
                const inc = target / speed;

                if (count < target) {
                    counter.innerText = Math.ceil(count + inc).toLocaleString();
                    setTimeout(updateCount, 15);
                } else {
                    counter.innerText = target.toLocaleString();
                }
            };
            updateCount();
        };

        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if(entry.isIntersecting) {
                    animateCounters(entry.target);
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => {
            observer.observe(counter);
        });
    });
</script>
@endsection
