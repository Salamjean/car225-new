<header id="header" class="header d-flex align-items-center fixed-top">
  <div
    class="header-container container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

    <a href="{{route('home')}}" class="logo d-flex align-items-center me-auto me-xl-0">
      <!-- Uncomment the line below if you also wish to use an image logo -->
      <img src="{{asset('assetsPoster/assets/images/logo_car225.png')}}" alt="" style="background-color: white; border-radius: 100px;">
      {{-- <h1 class="sitename">Tour</h1> --}}
    </a>

    <nav id="navmenu" class="navmenu">
      <ul>
        <li><a href="{{route('home')}}" class="active">Accueil</a></li>
        <li><a href="{{route('home.about')}}">A propos</a></li>
        <li><a href="{{route('home.destination')}}">Destinations</a></li>
        <li><a href="{{route('home.compagny')}}">Compagnies</a></li>
        <li><a href="{{route('home.services')}}">Services</a></li>
        <li><a href="{{route('home.contact')}}">Contactez-nous</a></li>
      </ul>
      <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
    </nav>

    <a class="btn-getstarted" href="{{ route('login')}}">Se connecter</a>

  </div>
</header>