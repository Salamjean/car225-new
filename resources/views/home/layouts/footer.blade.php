<footer id="footer" class="footer position-relative dark-background">
    <div class="container footer-top">
        <div class="row gy-4">
            <div class="col-lg-4 col-md-6 footer-about">
                <a href="{{route('home')}}" class="d-flex align-items-center">
                    <img src="{{asset('assetsPoster/assets/images/logo_car225.png')}}" alt="Logo Car225" class="footer-logo" style="height: 50px; background-color: white; padding: 5px; border-radius: 5px;">
                    <span class="sitename ms-2">Car225</span>
                </a>
                <p class="mt-3">Votre plateforme de référence pour la réservation de billets de car en Côte d'Ivoire. Voyagez facilement, en toute sécurité et aux meilleurs prix.</p>
                <div class="footer-contact pt-3">
                    <p><i class="bi bi-geo-alt me-2"></i>Plateau, Abidjan - Côte d'Ivoire</p>
                    <p class="mt-2"><strong><i class="bi bi-telephone me-2"></i>Téléphone:</strong> <span>+225 27 22 44 55 66</span></p>
                    <p><strong><i class="bi bi-whatsapp me-2"></i>WhatsApp:</strong> <span>+225 07 77 88 99 00</span></p>
                    <p><strong><i class="bi bi-envelope me-2"></i>Email:</strong> <span>contact@car225.ci</span></p>
                </div>
            </div>

            <div class="col-lg-2 col-md-3 footer-links">
                <h4>Navigation</h4>
                <ul>
                    <li><i class="bi bi-chevron-right"></i> <a href="{{route('home')}}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Accueil</a></li>
                    <li><i class="bi bi-chevron-right"></i> <a href="{{route('home.about')}}" class="{{ request()->routeIs('home.about') ? 'active' : '' }}">À propos</a></li>
                    <li><i class="bi bi-chevron-right"></i> <a href="{{route('home.destination')}}" class="{{ request()->routeIs('home.destination') ? 'active' : '' }}">Destinations</a></li>
                    <li><i class="bi bi-chevron-right"></i> <a href="{{route('home.compagny')}}" class="{{ request()->routeIs('home.compagny') ? 'active' : '' }}">Compagnies</a></li>
                    <li><i class="bi bi-chevron-right"></i> <a href="{{route('home.contact')}}" class="{{ request()->routeIs('home.contact') ? 'active' : '' }}">Contact</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-3 footer-links">
                <h4>Services</h4>
                <ul>
                    <li><i class="bi bi-ticket-perforated"></i> <a href="#">Réservation en ligne</a></li>
                    <li><i class="bi bi-people"></i> <a href="#">Réservation groupe</a></li>
                    <li><i class="bi bi-arrow-clockwise"></i> <a href="#">Modification billet</a></li>
                    <li><i class="bi bi-cash-coin"></i> <a href="#">Remboursement</a></li>
                    <li><i class="bi bi-shield-check"></i> <a href="#">Assurance voyage</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 footer-links">
                <h4>Infos Utiles</h4>
                <ul>
                    <li><i class="bi bi-question-circle"></i> <a href="#">FAQ - Questions fréquentes</a></li>
                    <li><i class="bi bi-file-text"></i> <a href="#">Conditions générales</a></li>
                    <li><i class="bi bi-shield-lock"></i> <a href="#">Politique de confidentialité</a></li>
                    <li><i class="bi bi-truck"></i> <a href="#">Infos bagages</a></li>
                    <li><i class="bi bi-calendar-check"></i> <a href="#">Horaires & Gares</a></li>
                </ul>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-8">
                <div class="payment-methods">
                    <h5 class="mb-3">Moyens de paiement acceptés</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="payment-badge"><i class="bi bi-phone"></i> Mobile Money</span>
                        <span class="payment-badge"><i class="bi bi-credit-card"></i> Carte Bancaire</span>
                        <span class="payment-badge"><i class="bi bi-cash"></i> Espèces (en agence)</span>
                        <span class="payment-badge"><i class="bi bi-wallet2"></i> Orange Money</span>
                        <span class="payment-badge"><i class="bi bi-wallet2"></i> MTN Mobile Money</span>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <h5>Suivez-nous</h5>
                <p>Restez connecté pour les dernières offres et actualités</p>
                <div class="social-links d-flex mt-3">
                    <a href="https://facebook.com/car225" class="facebook"><i class="bi bi-facebook"></i></a>
                    <a href="https://twitter.com/car225" class="twitter"><i class="bi bi-twitter-x"></i></a>
                    <a href="https://instagram.com/car225" class="instagram"><i class="bi bi-instagram"></i></a>
                    <a href="https://wa.me/2250777889900" class="whatsapp"><i class="bi bi-whatsapp"></i></a>
                    <a href="https://t.me/car225" class="telegram"><i class="bi bi-telegram"></i></a>
                </div>
                
                <div class="app-download mt-4">
                    <p class="small mb-2">Téléchargez notre application</p>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn-app btn-sm">
                            <i class="bi bi-apple"></i> App Store
                        </a>
                        <a href="#" class="btn-app btn-sm">
                            <i class="bi bi-google-play"></i> Play Store
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container copyright text-center mt-4 pt-4 border-top">
        <p>© <span>{{ date('Y') }}</span> <strong class="px-1 sitename">KKS - Technologies</strong> <span>Tous droits réservés</span></p>
        <div class="credits">
            Développé avec <i class="bi bi-heart text-danger"></i> pour les voyageurs ivoiriens
        </div>
    </div>
</footer>