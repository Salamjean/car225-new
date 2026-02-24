<footer id="footer" class="footer" style="background-color: #000000; color: #ffffff; padding: 60px 0 20px; font-family: 'Poppins', sans-serif;">

    <div class="container footer-top">
        <div class="row gy-4">
            <!-- Brand Column -->
            <div class="col-lg-4 col-md-12 footer-about">
                <a href="{{ route('home') }}" class="logo d-flex align-items-center gap-2 mb-4">
                    <img src="{{asset('assetsPoster/assets/images/Car225_favicon.png')}}" alt="Car225" class="bg-white rounded-full p-1" style="height: 40px; width: 40px;">
                    <span class="text-white font-black text-2xl tracking-tighter uppercase">Car225</span>
                </a>
                <p style="color: #ffffff; font-size: 14px; line-height: 1.6; max-width: 320px; margin-top: 10px;">
                    La meilleur plateforme de reservation de billets de car en Côte d'Ivoire.
                </p>
            </div>

            <!-- Navigation Column -->
            <div class="col-lg-2 col-6 footer-links">
                <h4 style="color: #ffffff; font-size: 16px; font-weight: 700; margin-bottom: 20px; text-transform: none;">Navigation</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 12px;"><a href="{{ route('home') }}" style="color: #ffffff; text-decoration: none; font-size: 14px;">Accueil</a></li>
                    <li style="margin-bottom: 12px;"><a href="{{ route('home.compagny') }}" style="color: #ffffff; text-decoration: none; font-size: 14px;">Nos compagnies</a></li>
                    <li style="margin-bottom: 12px;"><a href="{{ route('reservation.index') }}" style="color: #ffffff; text-decoration: none; font-size: 14px;">Mes billets</a></li>
                </ul>
            </div>

            <!-- Support Column -->
            <div class="col-lg-3 col-6 footer-links">
                <h4 style="color: #ffffff; font-size: 16px; font-weight: 700; margin-bottom: 20px; text-transform: none;">Support</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 12px;"><a href="{{ route('home.contact') }}" style="color: #ffffff; text-decoration: none; font-size: 14px;">Contact Téléphone</a></li>
                    <li style="margin-bottom: 12px;"><a href="{{ route('home.contact') }}" style="color: #ffffff; text-decoration: none; font-size: 14px;">Chat WhatsApp</a></li>
                    <li style="margin-bottom: 12px;"><a href="{{ route('home.infos') }}" style="color: #ffffff; text-decoration: none; font-size: 14px;">FAQ</a></li>
                </ul>
            </div>

            <!-- Contact Column -->
            <div class="col-lg-3 col-md-12 footer-contact">
                <h4 style="color: #ffffff; font-size: 16px; font-weight: 700; margin-bottom: 20px; text-transform: none;">Nous contacter</h4>
                <div style="font-size: 14px; line-height: 2;">
                    <p style="margin: 0;">+225 01 02 03 04</p>
                    <p style="margin: 0;">WhatsApp</p>
                    <p style="margin: 0;">support@car225.ci</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Copyright Area -->
    <div class="container mt-5 pt-3">
        <hr style="border-top: 1px solid rgba(255, 255, 255, 0.1); margin-bottom: 20px;">
        <div class="copyright text-center" style="color: #ffffff; font-size: 13px; opacity: 0.8;">
            <p>© 2025 Car 225 Tous droits réservés</p>
        </div>
    </div>

</footer>

<style>
.footer .footer-links a:hover {
    color: #e94f1b !important;
    text-decoration: underline !important;
}
</style>