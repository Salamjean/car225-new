<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Télécharger Car 225</title>
  
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="{{asset('assetsPoster/assets/images/Car225_favicon.png')}}" rel="icon">
  
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Vendor CSS -->
  <link href="{{asset('assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
  <link href="{{asset('assets/vendor/aos/aos.css')}}" rel="stylesheet">
  <link href="{{asset('assets/css/main.css')}}" rel="stylesheet">
</head>

<body class="bg-gray-50">

<main class="min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8 overflow-hidden relative">
    
    <!-- Éléments de fond -->
    <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-[#e94e1a]/10 to-transparent pointer-events-none"></div>
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#004a29]/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-[#e94e1a]/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-4xl w-full space-y-12 z-10">
        <!-- Logo & Titre -->
        <div class="text-center" data-aos="fade-down">
            <a href="/">
                <img class="mx-auto h-24 w-auto drop-shadow-lg mb-6" src="{{ asset('assetsPoster/assets/images/Car225_favicon.png') }}" alt="Car225">
            </a>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">
                L'expérience <span class="text-[#e94e1a]">Car225</span> dans votre poche
            </h1>
            <p class="mt-4 text-xl text-gray-600 max-w-2xl mx-auto font-medium">
                Réservez vos trajets, suivez vos convois en temps réel et gérez vos réservations en toute simplicité.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <!-- Mockup App -->
            <div class="relative" data-aos="fade-right">
                <div class="bg-gray-800 rounded-[3rem] p-4 shadow-2xl border-8 border-gray-100 max-w-[280px] mx-auto relative overflow-hidden transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                    <div class="bg-white rounded-[2.5rem] h-[500px] w-full overflow-hidden relative">
                        <!-- Écran du téléphone avec l'image réelle -->
                        <img src="{{ asset('assets/images/unnamed.png') }}" alt="App Screenshot" class="w-full h-full object-cover">
                    </div>
                </div>
                <div class="absolute -bottom-6 -right-6 md:right-12 bg-white p-4 rounded-2xl shadow-xl flex items-center gap-3 border border-gray-100 animate-bounce" style="animation-duration: 3s">
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Disponibilité</p>
                        <p class="text-sm font-bold text-gray-800">100% Gratuit</p>
                    </div>
                </div>
            </div>

            <!-- Boutons Téléchargement -->
            <div class="space-y-8" data-aos="fade-left">
                <div class="space-y-4">
                    <h2 class="text-2xl font-bold text-gray-800">Téléchargez maintenant</h2>
                    <p class="text-gray-600 font-medium">Choisissez votre plateforme pour commencer votre voyage.</p>
                </div>

                <div class="flex flex-col space-y-4">
                    <a href="https://play.google.com/store/apps/details?id=com.Robinson.car225" target="_blank" class="flex items-center gap-4 bg-gray-900 text-white px-8 py-4 rounded-2xl hover:bg-black transition-all duration-300 transform hover:-translate-y-1 shadow-lg group no-underline">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Google Play" class="h-10 w-auto">
                        <div class="text-left">
                            <p class="text-[10px] uppercase font-medium opacity-70 m-0">Disponible sur</p>
                            <p class="text-xl font-bold leading-tight m-0">Google Play</p>
                        </div>
                        <i class="fas fa-arrow-right ml-auto opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    </a>

                    <a href="#" target="_blank" class="flex items-center gap-4 bg-white text-gray-900 border-2 border-gray-900 px-8 py-4 rounded-2xl hover:bg-gray-50 transition-all duration-300 transform hover:-translate-y-1 shadow-lg group no-underline">
                        <i class="fab fa-apple text-4xl"></i>
                        <div class="text-left">
                            <p class="text-[10px] uppercase font-medium opacity-70 m-0">Bientôt sur</p>
                            <p class="text-xl font-bold leading-tight m-0">App Store</p>
                        </div>
                        <i class="fas fa-arrow-right ml-auto opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    </a>
                </div>

                <ul class="list-none p-0 space-y-3">
                    <li class="flex items-center gap-3 text-gray-600 font-medium">
                        <i class="fas fa-check-circle text-green-500"></i>
                        Réservations en 2 clics
                    </li>
                    <li class="flex items-center gap-3 text-gray-600 font-medium">
                        <i class="fas fa-check-circle text-green-500"></i>
                        Suivi GPS en temps réel
                    </li>
                </ul>
                
                <div class="pt-6 text-center md:text-left">
                    <a href="/" class="text-gray-500 hover:text-[#e94e1a] font-medium transition-colors inline-flex items-center gap-2 no-underline">
                        <i class="fas fa-chevron-left text-xs"></i>
                        Retour au site web
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="{{asset('assets/vendor/aos/aos.js')}}"></script>
<script>
    AOS.init({
        duration: 1000,
        easing: 'ease-in-out',
        once: true,
        mirror: false
    });
</script>

</body>
</html>
