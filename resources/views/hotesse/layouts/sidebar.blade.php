<aside class="mdc-drawer mdc-drawer--dismissible mdc-drawer--open" style="background-color: #ffeaca">
    <div class="mdc-drawer__header">
        <a href="{{ route('hotesse.dashboard') }}" class="brand-logo">
            @auth('hotesse')
                @if (Auth::guard('hotesse')->user()->profile_picture)
                    <img src="{{ asset('storage/' . Auth::guard('hotesse')->user()->profile_picture) }}"
                        style="width: 50%; margin-left: 50px; border-radius: 50%; object-fit: cover;" alt="Photo de profil">
                @else
                    <div style="width: 50%; margin-left: 50px; display: flex; align-items: center; justify-content: center;">
                        <div
                            style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(to right, #e94e1a, #d33d0f); display: flex; align-items: center; justify-content: center; color: white; font-size: 40px; font-weight: bold;">
                            {{ strtoupper(substr(Auth::guard('hotesse')->user()->prenom, 0, 1)) }}{{ strtoupper(substr(Auth::guard('hotesse')->user()->name, 0, 1)) }}
                        </div>
                    </div>
                @endif
            @else
                <img src="{{ asset('assetsPoster/assets/images/logo_car225.png') }}" style="width: 50%; margin-left: 50px" alt="logo">
            @endauth
        </a>
    </div>
    <div class="mdc-drawer__content">
        <div class="user-info">
            <p class="name text-center text-black">{{ Auth::guard('hotesse')->user()->name }}
                {{ Auth::guard('hotesse')->user()->prenom }}</p>
            <p class="email text-center text-black">{{ Auth::guard('hotesse')->user()->email }}</p>
        </div>
        <div class="mdc-list-group">
            <nav class="mdc-list mdc-drawer-menu">
                <!-- Tableau de bord -->
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="{{ route('hotesse.dashboard') }}">
                        <i class="fas fa-home mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                        Tableau de bord
                    </a>
                </div>

                <!-- Vendre Ticket -->
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="{{ route('hotesse.vendre-ticket') }}">
                        <i class="fas fa-ticket-alt mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                        Vendre Ticket
                    </a>
                </div>

                <!-- Historique des Ventes -->
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="{{ route('hotesse.ventes') }}">
                        <i class="fas fa-history mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                        Historique
                    </a>
                </div>

                <!-- Mon Profile -->
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="{{ route('hotesse.profile') }}">
                        <i class="fas fa-user mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                        Mon Profil
                    </a>
                </div>
            </nav>
        </div>
    </div>
</aside>
