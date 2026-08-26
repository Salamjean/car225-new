<header id="header" class="header d-flex align-items-center fixed-top !p-0 !bg-[#e94e1a] shadow-lg">
  <div class="container-fluid container-xl d-flex align-items-center justify-content-between h-[70px]">

    <a href="{{route('home')}}" class="logo d-flex align-items-center gap-2">
      <img src="{{asset('assetsPoster/assets/images/Car225_favicon.png')}}" alt="Car225" class="bg-white rounded-full p-1" style="height: 40px; width: 40px;">
      <span class="text-white font-black text-2xl tracking-tighter uppercase d-none d-sm-block">Car225</span>
    </a>

    <nav id="navmenu" class="navmenu">
      <ul>
        <li><a href="{{route('home')}}" class="active !text-white font-bold uppercase text-xs tracking-wider hover:!text-white/80">Accueil</a></li>
        {{-- <li><a href="{{route('home.compagny')}}" class="!text-white font-bold uppercase text-xs tracking-wider hover:!text-white/80">Compagnies</a></li> --}}
        <li><a href="{{route('home.reservations')}}" class="!text-white font-bold uppercase text-xs tracking-wider hover:!text-white/80">Mes réservations</a></li>
        <li><a href="{{route('home.convoi')}}" class="!text-white font-bold uppercase text-xs tracking-wider hover:!text-white/80">Convoi</a></li>
        <!-- <li><a href="{{route('home.signaler')}}" class="!text-white font-bold uppercase text-xs tracking-wider hover:!text-white/80">Signalez un probleme</a></li> -->
        <li><a href="{{route('home.contact')}}" class="!text-white font-bold uppercase text-xs tracking-wider hover:!text-white/80">Contactez-nous</a></li>
      </ul>
      <i class="mobile-nav-toggle d-xl-none bi bi-list text-white"></i>
    </nav>

    <div class="d-flex align-items-center gap-4">
        @if(Auth::guard('web')->check())
            @php
                $user = Auth::guard('web')->user();
                $initials = strtoupper(substr($user->name ?? '', 0, 1) . substr($user->prenom ?? '', 0, 1));
            @endphp
            <div class="dropdown">
                <button class="dropdown-toggle flex items-center justify-center p-0 border-0 bg-transparent focus:outline-none" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    @if($user->photo_profile_path)
                        <img src="{{ asset('storage/' . $user->photo_profile_path) }}" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-md hover:scale-105 transition-transform">
                    @else
                        <div class="w-10 h-10 rounded-full bg-white text-[#e94e1a] flex items-center justify-center font-black border-2 border-white shadow-md hover:bg-gray-100 hover:scale-105 transition-all text-xs uppercase">
                            {{ $initials ?: 'U' }}
                        </div>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end mt-2 !border-0 !rounded-xl shadow-xl !py-2 animate-in fade-in slide-in-from-top-2 duration-150" aria-labelledby="userDropdown" style="background-color: #ffffff; min-width: 200px;">
                    <li class="px-4 py-2 border-b border-gray-100">
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-0">Utilisateur</p>
                        <p class="text-xs font-black text-gray-800 truncate mb-0">{{ $user->name }} {{ $user->prenom }}</p>
                    </li>
                    <li>
                        <a class="dropdown-item !text-xs !font-bold !text-gray-700 hover:!bg-[#e94e1a]/10 hover:!text-[#e94e1a] !px-4 !py-2.5 transition-colors flex items-center gap-2" href="{{ route('reservation.create') }}">
                            <i class="fas fa-ticket-alt text-[#e94e1a]/80 text-[10px]"></i> Accéder à ma page
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item !text-xs !font-bold !text-red-600 hover:!bg-red-50 !px-4 !py-2.5 transition-colors flex items-center gap-2" href="{{ route('user.logout') }}">
                            <i class="fas fa-sign-out-alt text-red-600/80 text-[10px]"></i> Se déconnecter
                        </a>
                    </li>
                </ul>
            </div>
        @else
            <a class="btn-getstarted !bg-white !text-[#e94e1a] !m-0 !font-black !px-6 !py-2 hover:!bg-gray-100 transition-all text-xs uppercase tracking-widest" href="{{ route('login')}}">Se connecter</a>
        @endif
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
    /* Hide caret icon from dropdown toggle button */
    .dropdown-toggle::after {
        display: none !important;
    }
</style>