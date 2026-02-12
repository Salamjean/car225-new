<header id="header" class="header d-flex align-items-center fixed-top !p-0 !bg-[#e94e1a] shadow-lg">
  <div class="container-fluid container-xl d-flex align-items-center justify-content-between h-[70px]">

    <a href="{{route('home')}}" class="logo d-flex align-items-center gap-2">
      <img src="{{asset('assetsPoster/assets/images/Car225_favicon.png')}}" alt="Car225" class="bg-white rounded-full p-1" style="height: 40px; width: 40px;">
      <span class="text-white font-black text-2xl tracking-tighter uppercase d-none d-sm-block">Car225</span>
    </a>

    <nav id="navmenu" class="navmenu">
      <ul>
        <li><a href="{{route('home')}}" class="active !text-white font-bold uppercase text-xs tracking-wider hover:!text-white/80">Accueil</a></li>
        <li><a href="{{route('home.about')}}" class="!text-white font-bold uppercase text-xs tracking-wider hover:!text-white/80">A propos</a></li>
        <li><a href="{{route('home.destination')}}" class="!text-white font-bold uppercase text-xs tracking-wider hover:!text-white/80">Destinations</a></li>
        <li><a href="{{route('home.compagny')}}" class="!text-white font-bold uppercase text-xs tracking-wider hover:!text-white/80">Compagnies</a></li>
        <li><a href="{{route('home.services')}}" class="!text-white font-bold uppercase text-xs tracking-wider hover:!text-white/80">Services</a></li>
        <li><a href="{{route('home.contact')}}" class="!text-white font-bold uppercase text-xs tracking-wider hover:!text-white/80">Contactez-nous</a></li>
      </ul>
      <i class="mobile-nav-toggle d-xl-none bi bi-list text-white"></i>
    </nav>

    <div class="d-flex align-items-center gap-4">
        <a class="btn-getstarted !bg-white !text-[#e94e1a] !m-0 !font-black !px-6 !py-2 hover:!bg-gray-100 transition-all text-xs uppercase tracking-widest" href="{{ route('login')}}">Se connecter</a>
    </div>

  </div>
</header>

<style>
    /* Remove original template container styles to go full width and solid bg */
    .header .header-container {
        background: transparent !important;
        border-radius: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
    }
    .header .navmenu a, .header .navmenu a:focus {
        padding: 10px 15px !important;
    }
    .navmenu ul {
        background-color: transparent !important;
    }
</style>